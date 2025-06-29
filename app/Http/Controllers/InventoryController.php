<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    /**
     * Display a listing of the inventories.
     */
    // public function index(): JsonResponse
    // {
    //     try {
    //         $user = auth()->user();
    //         $laboratoryId = null;

    //         if (!$user) {
    //             return response()->json(['valid' => false, 'msg' => 'User profile not found.'], 404);
    //         }

    //         // Determine laboratory for non-Admin users
    //         if ($user->role !== 'Admin') {
    //             $laboratoryId = $user->laboratory_id;
    //         }

    //         // Fetch grouped inventories
    //         $inventoriesQuery = Inventory::select(
    //             'inventory_number',
    //             DB::raw('MIN(starting_period) as starting_period'),
    //             DB::raw('MAX(ending_period) as ending_period')
    //         )
    //             ->when($laboratoryId, function ($query) use ($laboratoryId) {
    //                 return $query->where('laboratory_id', $laboratoryId);
    //             })
    //             ->groupBy('inventory_number');

    //         $inventories = $inventoriesQuery->get();

    //         $response = $inventories->map(function ($inventory, $index) {
    //             return [
    //                 'count' => $index + 1,
    //                 'inventory_number' => $inventory->inventory_number,
    //                 'starting_period' => date('F j, Y', strtotime($inventory->starting_period)),
    //                 'ending_period' => date('F j, Y', strtotime($inventory->ending_period)),
    //                 'action' => '<button class="btn btn-primary btn-md" onclick="view(\'' . $inventory->inventory_number . '\')" title="View Inventories"><i class="fas fa-eye"></i></button>',
    //             ];
    //         });

    //         return response()->json(['valid' => true, 'msg' => 'Inventories fetched successfully', 'data' => $response]);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to retrieve inventories: ' . $e->getMessage());
    //         return response()->json(['valid' => false, 'msg' => 'Failed to retrieve inventories'], 500);
    //     }
    // }

    public function index(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $laboratoryId = null;

            if (!$user) {
                return response()->json(['valid' => false, 'msg' => 'User profile not found.'], 404);
            }

            if ($user->role !== 'Admin') {
                $laboratoryId = $user->laboratory_id;
            }

            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $inventoriesQuery = Inventory::select(
                'inventory_number',
                DB::raw('MIN(starting_period) as starting_period'),
                DB::raw('MAX(ending_period) as ending_period')
            )
                ->when($laboratoryId, function ($query) use ($laboratoryId) {
                    return $query->where('laboratory_id', $laboratoryId);
                })
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('starting_period', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('ending_period', '<=', $endDate);
                })
                ->groupBy('inventory_number');

            $inventories = $inventoriesQuery->get();

            $response = $inventories->map(function ($inventory, $index) {
                return [
                    'count' => $index + 1,
                    'inventory_number' => $inventory->inventory_number,
                    'starting_period' => date('F j, Y', strtotime($inventory->starting_period)),
                    'ending_period' => date('F j, Y', strtotime($inventory->ending_period)),
                    'action' => '<button class="btn btn-primary btn-md" onclick="view(\'' . $inventory->inventory_number . '\')" title="View Inventories"><i class="fas fa-eye"></i></button>',
                ];
            });

            return response()->json(['valid' => true, 'msg' => 'Inventories fetched successfully', 'data' => $response]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve inventories: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to retrieve inventories'], 500);
        }
    }

    /**
     * Store a newly created inventory in storage.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Validate request data
            $validated = $request->validate([
                'starting_period' => 'required|date',
                'ending_period' => 'required|date|after_or_equal:starting_period',
            ], [
                'starting_period.required' => 'The starting period is required.',
                'starting_period.date' => 'The starting period must be a valid date.',
                'ending_period.required' => 'The ending period is required.',
                'ending_period.date' => 'The ending period must be a valid date.',
                'ending_period.after_or_equal' => 'The ending period must be on or after the starting period.',
            ]);

            $startingPeriod = $validated['starting_period'];
            $endingPeriod = $validated['ending_period'];
            $user = auth()->user();

            if (!$user) {
                return response()->json(['valid' => false, 'msg' => 'User not authenticated.'], 401);
            }

            // Determine laboratory for non-Admin users
            $laboratoryId = $user->user_role === 'Admin' ? null : $user->laboratory_id;

            if ($user->user_role !== 'Admin' && !$laboratoryId) {
                return response()->json(['valid' => false, 'msg' => 'No laboratory assigned to this user.'], 403);
            }

            // Generate inventory number
            $year = date('Y', strtotime($startingPeriod));
            $prefix = "INV-$year-";
            $latestInventory = Inventory::where('inventory_number', 'like', "$prefix%")
                ->orderBy('inventory_number', 'desc')
                ->first();

            $inventoryNumber = $latestInventory
                ? $prefix . str_pad((int)substr($latestInventory->inventory_number, -4) + 1, 4, '0', STR_PAD_LEFT)
                : $prefix . '0001';

            // Fetch items for the laboratory
            $items = Item::join('categories', 'items.category_id', '=', 'categories.id')
                ->when($laboratoryId, function ($query) use ($laboratoryId) {
                    return $query->where('categories.laboratory_id', $laboratoryId);
                })
                ->select('items.*')
                ->get();

            if ($items->isEmpty()) {
                return response()->json(['valid' => false, 'msg' => 'No items found for the specified laboratory.'], 404);
            }

            // Fetch transaction statuses within the period
            $transactionStatuses = TransactionStatus::whereIn('item_id', $items->pluck('id'))
                ->whereHas('transaction', function ($query) use ($startingPeriod, $endingPeriod) {
                    $query->whereBetween('created_at', [$startingPeriod . ' 00:00:00', $endingPeriod . ' 23:59:59']);
                })
                ->select('item_id', 'status', DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('item_id', 'status')
                ->get()
                ->groupBy('item_id');

            // Calculate total borrowed from transactions
            $totalBorrowed = Transaction::whereIn('item_id', $items->pluck('id'))
                ->whereBetween('created_at', [$startingPeriod . ' 00:00:00', $endingPeriod . ' 23:59:59'])
                ->where('status', 'Released')
                ->select('item_id', DB::raw('SUM(approve_quantity) as total_borrowed'))
                ->groupBy('item_id')
                ->get()
                ->keyBy('item_id');

            $inventoryData = $items->map(function ($item) use ($transactionStatuses, $totalBorrowed, $inventoryNumber, $startingPeriod, $endingPeriod, $laboratoryId) {
                $statuses = $transactionStatuses->get($item->id, collect());

                $quantities = [
                    'Lost' => 0,
                    'Damaged' => 0,
                    'For Repair' => 0,
                    'For Disposal' => 0,
                ];

                foreach ($statuses as $status) {
                    if (array_key_exists($status->status, $quantities)) {
                        $quantities[$status->status] = (int)$status->total_quantity;
                    }
                }

                $lostQuantity = $quantities['Lost'];
                $damagedQuantity = $quantities['Damaged'];
                $repairedQuantity = $quantities['For Repair'];
                $disposedQuantity = $quantities['For Disposal'];

                $totalBorrowedQty = $totalBorrowed->has($item->id) ? (int)$totalBorrowed[$item->id]->total_borrowed : 0;
                $usableQuantity = max($item->current_qty - ($lostQuantity + $damagedQuantity + $repairedQuantity + $disposedQuantity), 0);

                return [
                    'inventory_number' => $inventoryNumber,
                    'item_id' => $item->id,
                    'beginning_inventory' => $item->beginning_qty,
                    'ending_inventory' => $item->current_qty,
                    'starting_period' => $startingPeriod,
                    'ending_period' => $endingPeriod,
                    'total_borrowed' => $totalBorrowedQty,
                    'usable_quantity' => $usableQuantity,
                    'damaged_quantity' => $damagedQuantity,
                    'lost_quantity' => $lostQuantity,
                    'repaired_qty' => $repairedQuantity,
                    'disposed_quantity' => $disposedQuantity,
                    'laboratory_id' => $laboratoryId ?: $item->category->laboratory_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            // Bulk insert or update inventory data
            foreach ($inventoryData as $data) {
                Inventory::updateOrCreate(
                    [
                        'inventory_number' => $data['inventory_number'],
                        'item_id' => $data['item_id'],
                        'starting_period' => $data['starting_period'],
                        'ending_period' => $data['ending_period'],
                    ],
                    $data
                );
            }

            DB::commit();

            return response()->json(['valid' => true, 'msg' => 'Inventory created successfully'], 201);
        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json(['valid' => false, 'msg' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to store inventory: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to store inventory: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified inventory.
     */
    public function show($inventory_number): JsonResponse
    {
        try {
            $user = auth()->user();
            $laboratoryId = null;

            if (!$user) {
                return response()->json(['valid' => false, 'msg' => 'User profile not found.'], 404);
            }

            // Determine laboratory for non-Admin users
            if ($user->role !== 'Admin') {
                $laboratoryId = $user->laboratory_id;
            }

            // Fetch inventory records
            $inventories = Inventory::where('inventory_number', $inventory_number)
                ->with(['item.category'])
                ->when($laboratoryId, function ($query) use ($laboratoryId) {
                    return $query->where('laboratory_id', $laboratoryId);
                })
                ->get();

            if ($inventories->isEmpty()) {
                return response()->json(['valid' => false, 'msg' => 'Inventory not found.'], 404);
            }

            $flattenedData = $inventories->flatMap(function ($record) {
                $itemData = [
                    'item_id' => $record->item_id,
                    'item_name' => $record->item->item_name ?? 'N/A',
                    'beginning_inventory' => $record->beginning_inventory,
                    'ending_inventory' => $record->ending_inventory,
                    'category_name' => $record->item->category->category_name ?? 'N/A',
                    'image' => $record->item->image ? asset($record->item->image) : asset('dist/img/default.jpg'),
                ];

                $statuses = [
                    'Okay' => $record->usable_quantity,
                    'Damaged' => $record->damaged_quantity,
                    'Lost' => $record->lost_quantity,
                    'For Repair' => $record->repaired_qty,
                    'For Disposal' => $record->disposed_quantity,
                ];

                return collect($statuses)
                    ->filter(fn($quantity) => $quantity > 0)
                    ->map(fn($quantity, $status) => array_merge($itemData, [
                        'status' => $status,
                        'quantity' => $quantity,
                    ]))
                    ->values();
            });

            return response()->json([
                'valid' => true,
                'data' => $flattenedData,
                'inventoryNumber' => $inventory_number,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve inventory data: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to retrieve inventory data.'], 500);
        }
    }
}
