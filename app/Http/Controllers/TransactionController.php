<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TransactionPenalty;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Generate a unique transaction number for the current year.
     *
     * @param  string $year
     * @return string
     */
    private function generateTransactionNumber($year)
    {
        return DB::transaction(function () use ($year) {
            // Lock the table or row to prevent concurrent reads
            $lastTransaction = Transaction::where('transaction_no', 'like', "{$year}-%")
                ->orderBy('transaction_no', 'desc')
                ->lockForUpdate() // Pessimistic locking
                ->first();

            // Get the last sequence number or start from 1
            $lastSequence = $lastTransaction ? (int)substr($lastTransaction->transaction_no, -4) : 0;

            // Increment and pad the sequence number
            $newSequence = sprintf('%04d', $lastSequence + 1);

            // Generate the new transaction number
            return "{$year}-{$newSequence}";
        });
    }

    public function checkUserLimit($userId): JsonResponse
    {
        $count = Transaction::where('user_id', $userId)
            ->whereIn('status', ['Pending', 'Confirmed', 'Released'])
            ->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $transactionsQuery = Transaction::with(['item.category.laboratory', 'user']);

        if (!in_array($user->user_role, ['Admin', 'Laboratory Head', 'Laboratory In-charge'])) {
            $transactionsQuery->where('user_id', $user->id);
        }

        if (in_array($user->user_role, ['Laboratory Head', 'Laboratory In-charge'])) {
            $transactionsQuery->whereHas('item', function ($query) use ($user) {
                $query->whereHas('category', function ($query) use ($user) {
                    $query->where('laboratory_id', $user->laboratory_id);
                });
            });
        }

        $transactions = $transactionsQuery->get();

        $response = $transactions->map(function ($transaction, $index) use ($user) {
            $actions = '';
            $currentDate = now()->toDateString();
            $dateOfUsage = $transaction->date_of_usage->toDateString();

            if (in_array($user->user_role, ['Employee', 'Borrower'])) {
                if ($transaction->status === 'Pending') {
                    $actions = '
                    <button onclick="update(\'' . $transaction->id . '\')" type="button" title="Edit" class="btn btn-secondary"><i class="fas fa-edit"></i></button>
                    <button onclick="cancelTransaction(\'' . $transaction->id . '\')" type="button" title="Cancel" class="btn btn-danger"><i class="fas fa-times"></i></button>
                ';
                }
            } else {
                if ($transaction->status === 'Pending') {
                    $actions = '
                    <button onclick="update(\'' . $transaction->id . '\')" type="button" title="Edit" class="btn btn-secondary"><i class="fas fa-edit"></i></button>
                    <button onclick="confirmTransaction(\'' . $transaction->id . '\')" type="button" title="Confirm" class="btn btn-success"><i class="fas fa-check"></i></button>
                    <button onclick="rejectTransaction(\'' . $transaction->id . '\')" type="button" title="Reject" class="btn btn-danger"><i class="fas fa-times"></i></button>
                ';
                } elseif ($transaction->status === 'Confirmed') {
                    if ($currentDate <= $dateOfUsage) {
                        $actions = '<button onclick="releaseTransaction(\'' . $transaction->id . '\')" type="button" title="Release" class="btn btn-primary"><i class="fas fa-share"></i></button>';
                    } else {
                        $actions = '<button type="button" title="Release" class="btn btn-primary" disabled>Item can\'t be released because the date of usage has not been met.</button>';
                    }
                } elseif ($transaction->status === 'Released') {
                    $returnDateTime = Carbon::parse(
                        $transaction->getRawOriginal('date_of_return') . ' ' . $transaction->time_of_return
                    );

                    // Add grace period (e.g., 2 days)
                    $gracePeriodDays = 2; // Adjust this value as needed
                    $fineStartDateTime = $returnDateTime->copy()->addDays($gracePeriodDays);

                    $now = now();
                    $fine = 0;

                    // Only calculate fine if current time is after the fine start date
                    if ($now->greaterThan($fineStartDateTime)) {
                        // Calculate hours late after grace period
                        $hoursLate = $fineStartDateTime->diffInHours($now); // Reverse order to ensure positive value
                        $fine = $hoursLate * 5; // 5 currency units per hour late
                    }

                    Log::info('Fine: ' . $fine . ', Now: ' . $now->toDateTimeString() . ', FineStartDateTime: ' . $fineStartDateTime->toDateTimeString());

                    $fineText = $fine > 0
                        ? '<span class="text-danger ml-2">Fine: ₱' . number_format($fine, 2) . '</span>'
                        : '';

                    $actions = '<button onclick="returnTransaction(' . "'" . $transaction->id . "','" . $fine . "'" . ')" type="button" title="Return" class="btn btn-warning"><i class="fas fa-undo"></i> ' . $fineText . '</button>';
                } elseif (in_array($transaction->status, ['Rejected', 'Cancelled'])) {
                    // $actions = '
                    //     <button type="button" title="Edit" class="btn btn-secondary" disabled><i class="fas fa-edit"></i></button>
                    //     <button type="button" title="Confirm" class="btn btn-success" disabled><i class="fas fa-check"></i></button>
                    //     <button type="button" title="Reject" class="btn btn-danger" disabled><i class="fas fa-times"></i></button>
                    //     <button type="button" title="Release" class="btn btn-primary" disabled><i class="fas fa-share"></i></button>
                    //     <button type="button" title="Return" class="btn btn-warning" disabled><i class="fas fa-undo"></i></button>
                    // ';
                    $actions = '';
                }
            }

            return [
                'count' => $index + 1,
                'transaction_no' => $transaction->transaction_no,
                'item_name' => $transaction->item ? ucwords(strtolower($transaction->item->item_name)) : 'N/A',
                'user_name' => $transaction->user ? $transaction->user->first_name . ' ' . $transaction->user->last_name : 'N/A',
                'reserve_quantity' => $transaction->reserve_quantity,
                'approve_quantity' => $transaction->approve_quantity,
                'date_of_usage' => $transaction->date_of_usage->format('Y-m-d'),
                'date_of_return' => $transaction->date_of_return->format('Y-m-d'),
                // 'time_of_return' => date('H:i A', strtotime($transaction->time_of_return)),
                // 'time_of_return' => date('h:i A', strtotime($transaction->time_of_return)),
                // 'time_of_return' => date('H:i A', strtotime($transaction->time_of_return)),
                'time_of_return' => date('h:i A', strtotime($transaction->time_of_return)),
                'status' => $transaction->status,
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                'action' => $actions,
            ];
        });

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        // dd($request->all());

        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.reserve_quantity' => 'required|integer|min:1',
                'items.*.date_of_usage' => 'required|date|after_or_equal:today',
                'items.*.date_of_return' => 'required|date',
                'items.*.time_of_return' => 'required|date_format:H:i', // <-- Fixed
            ], [
                'user_id.required' => 'The user field is required.',
                'user_id.exists' => 'The selected user is invalid.',
                'items.required' => 'You must add at least one item to reserve.',
                'items.array' => 'The items field must be an array.',
                'items.min' => 'You must add at least one item to reserve.',
                'items.*.item_id.required' => 'Each item must have a valid ID.',
                'items.*.item_id.exists' => 'One or more selected items are invalid.',
                'items.*.reserve_quantity.required' => 'Each item must have a reserve quantity.',
                'items.*.reserve_quantity.integer' => 'The reserve quantity for each item must be a valid number.',
                'items.*.reserve_quantity.min' => 'The reserve quantity for each item must be at least 1.',
                'items.*.date_of_usage.required' => 'The date of usage is required for each item.',
                'items.*.date_of_usage.date' => 'The date of usage must be a valid date.',
                'items.*.date_of_usage.after_or_equal' => 'The date of usage must be today or later.',
                'items.*.date_of_return.required' => 'The date of return is required for each item.',
                'items.*.date_of_return.date' => 'The date of return must be a valid date.',
                'items.*.time_of_return.required' => 'The time of return is required for each item.',
                'items.*.time_of_return.date_format' => 'The time of return must be in 24-hour HH:MM format (e.g. 14:30).',
            ]);

            $year = date('Y');

            foreach ($validated['items'] as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);

                if ($itemData['reserve_quantity'] > $item->current_qty) {
                    return response()->json([
                        'valid' => false,
                        'msg' => "Insufficient stock for item: {$item->item_name}.",
                    ], 422);
                }

                $transactionNo = $this->generateTransactionNumber($year);

                Transaction::create([
                    'transaction_no' => $transactionNo,
                    'item_id' => $itemData['item_id'],
                    'user_id' => $validated['user_id'],
                    'reserve_quantity' => $itemData['reserve_quantity'],
                    'approve_quantity' => 0,
                    'date_of_usage' => $itemData['date_of_usage'],
                    'date_of_return' => $itemData['date_of_return'],
                    'time_of_return' => $itemData['time_of_return'],
                    'status' => 'Pending',
                ]);

                $item->current_qty -= $itemData['reserve_quantity'];
                $item->save();
            }

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Transactions successfully stored.',
            ], 201);
        } catch (ValidationException $e) {
            DB::rollback();

            return response()->json([
                'valid' => false,
                'msg' => 'Please fix the following errors:',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to store transactions: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to store transactions. Please try again later.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::with('item')->findOrFail($id);

            DB::commit();

            return response()->json($transaction, 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to retrieve transaction: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to retrieve transaction. Please try again later.',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::findOrFail($id);
            $originalReserveQuantity = $transaction->reserve_quantity;
            $originalItemId = $transaction->item_id;

            $request->time_of_return = date('H:i', strtotime($request->time_of_return));

            $validated = $request->validate([
                'item_id' => 'required|exists:items,id',
                'user_id' => 'required|exists:users,id',
                'reserve_quantity' => 'required|integer|min:1',
                'date_of_usage' => 'required|date|after_or_equal:today',
                'date_of_return' => 'required|date',
                // 'time_of_return' => 'required|date_format:H:i',
            ], [
                'item_id.required' => 'Item is required.',
                'item_id.exists' => 'Selected item does not exist.',
                'user_id.required' => 'User is required.',
                'user_id.exists' => 'Selected user does not exist.',
                'reserve_quantity.required' => 'Reserve quantity is required.',
                'reserve_quantity.integer' => 'Reserve quantity must be an integer.',
                'reserve_quantity.min' => 'Reserve quantity must be at least 1.',
                'date_of_usage.required' => 'Date of usage is required.',
                'date_of_usage.date' => 'Invalid date of usage.',
                'date_of_usage.after_or_equal' => 'Date of usage must be today or later.',
                'date_of_return.required' => 'Date of return is required.',
                'date_of_return.date' => 'Invalid date of return.',
                // 'date_of_return.after' => 'Date of return must be after date of usage.',
                'time_of_return.required' => 'Time of return is required.',
                'time_of_return.date_format' => 'Time of return must be in HH:MM format.',
            ]);

            $newItem = Item::findOrFail($validated['item_id']);

            // Check if reserve_quantity is valid for the new item
            if ($validated['reserve_quantity'] > $newItem->current_qty + ($originalItemId == $validated['item_id'] ? $originalReserveQuantity : 0)) {
                throw ValidationException::withMessages([
                    'reserve_quantity' => 'Reserve quantity cannot exceed current available quantity.',
                ]);
            }

            // If item_id has changed, restore quantity to the original item
            if ($originalItemId != $validated['item_id']) {
                $originalItem = Item::findOrFail($originalItemId);
                $originalItem->current_qty += $originalReserveQuantity;
                $originalItem->save();

                // Deduct reserve_quantity from the new item
                $newItem->current_qty -= $validated['reserve_quantity'];
                $newItem->save();
            } else {
                // If item_id is the same, adjust quantity only if reserve_quantity changed
                if ($originalReserveQuantity != $validated['reserve_quantity']) {
                    $newItem->current_qty += $originalReserveQuantity;
                    $newItem->current_qty -= $validated['reserve_quantity'];
                    $newItem->save();
                }
            }

            // Update the transaction
            $transaction->update($validated);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Transaction successfully updated.',
            ], 200);
        } catch (ValidationException $e) {
            DB::rollback();

            return response()->json([
                'valid' => false,
                'msg' => '',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update transaction: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update transaction. Please try again later.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::findOrFail($id);
            $item = Item::findOrFail($transaction->item_id);

            if ($transaction->status === 'Pending' || $transaction->status === 'Confirmed') {
                $item->current_qty += $transaction->reserve_quantity;
                $item->save();
            }

            $transaction->delete();

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Transaction successfully deleted.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete transaction: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to delete transaction. Please try again later.',
            ], 500);
        }
    }

    /**
     * Confirm a transaction.
     */
    public function confirm($id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::findOrFail($id);
            if ($transaction->status !== 'Pending') {
                throw new \Exception('Only pending transactions can be confirmed.');
            }

            $transaction->update([
                'status' => 'Confirmed',
                'approve_quantity' => $transaction->reserve_quantity,
            ]);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Transaction successfully confirmed.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to confirm transaction: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to confirm transaction. Please try again later.',
            ], 500);
        }
    }

    /**
     * Reject a transaction.
     */
    public function cancel($id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::findOrFail($id);
            if ($transaction->status !== 'Pending') {
                throw new \Exception('Only pending transactions can be canceled.');
            }

            $item = Item::findOrFail($transaction->item_id);
            $item->current_qty += $transaction->reserve_quantity;
            $item->save();

            $transaction->update(['status' => 'Cancelled']);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Transaction successfully canceled.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to cancel transaction: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to cancel transaction. Please try again later.',
            ], 500);
        }
    }

    /**
     * Reject a transaction.
     */
    public function reject($id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::findOrFail($id);
            if ($transaction->status !== 'Pending') {
                throw new \Exception('Only pending transactions can be rejected.');
            }

            $item = Item::findOrFail($transaction->item_id);
            $item->current_qty += $transaction->reserve_quantity;
            $item->save();

            $transaction->update(['status' => 'Rejected']);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Transaction successfully rejected.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to reject transaction: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to reject transaction. Please try again later.',
            ], 500);
        }
    }

    /**
     * Release a transaction.
     */
    public function release($id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::findOrFail($id);
            if ($transaction->status !== 'Confirmed') {
                throw new \Exception('Only confirmed transactions can be released.');
            }

            if (now()->toDateString() > $transaction->date_of_usage->toDateString()) {
                throw new \Exception('Item cannot be released because the date of usage has not been met.');
            }

            $transaction->update(['status' => 'Released']);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Transaction successfully released.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to release transaction: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return a transaction.
     */
    // public function returnTransaction(Request $request, $id): JsonResponse
    // {
    //     DB::beginTransaction();

    //     try {
    //         $transaction = Transaction::findOrFail($id);
    //         if ($transaction->status !== 'Released') {
    //             throw new \Exception('Only released transactions can be returned.');
    //         }

    //         $item = Item::findOrFail($transaction->item_id);

    //         $validated = $request->validate([
    //             'returns' => 'required|array|min:1',
    //             'returns.*.return_status' => 'required|in:Good,Lost,Damaged,For Repair,For Disposal',
    //             'returns.*.quantity' => 'required|integer|min:1',
    //             'returns.*.penalty_remarks' => 'nullable|in:Replace,Pay',
    //         ]);

    //         // Validate total quantity
    //         $totalReturnQuantity = array_sum(array_column($validated['returns'], 'quantity'));
    //         if ($totalReturnQuantity !== $transaction->approve_quantity) {
    //             throw ValidationException::withMessages([
    //                 'returns' => "Total return quantity ($totalReturnQuantity) must equal approved quantity ({$transaction->approve_quantity}).",
    //             ]);
    //         }

    //         // Process each return status
    //         foreach ($validated['returns'] as $return) {
    //             if ($return['return_status'] === 'Good') {
    //                 $item->current_qty += $return['quantity'];
    //             }

    //             TransactionStatus::create([
    //                 'transaction_id' => $id,
    //                 'item_id' => $transaction->item_id,
    //                 'quantity' => $return['quantity'],
    //                 'status' => $return['return_status'],
    //             ]);

    //             if (in_array($return['return_status'], ['Lost', 'Damaged']) && !empty($return['penalty_remarks'])) {
    //                 TransactionPenalty::create([
    //                     'transaction_id' => $id,
    //                     'item_id' => $transaction->item_id,
    //                     'user_id' => $transaction->user_id,
    //                     'quantity' => $return['quantity'],
    //                     'amount' => $item->item_price * $return['quantity'],
    //                     'status' => $return['return_status'],
    //                     'remarks' => $return['penalty_remarks'],
    //                 ]);
    //             }
    //         }

    //         $item->save();
    //         $transaction->update(['status' => 'Returned']);

    //         DB::commit();

    //         return response()->json([
    //             'valid' => true,
    //             'msg' => 'Transaction successfully returned.',
    //         ], 200);
    //     } catch (ValidationException $e) {
    //         DB::rollback();

    //         return response()->json([
    //             'valid' => false,
    //             'msg' => '',
    //             'errors' => $e->errors(),
    //         ], 422);
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         Log::error('Failed to return transaction: ' . $e->getMessage());

    //         return response()->json([
    //             'valid' => false,
    //             'msg' => 'Failed to return transaction. Please try again later.',
    //         ], 500);
    //     }
    // }
    public function returnTransaction(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::findOrFail($id);
            if ($transaction->status !== 'Released') {
                throw new \Exception('Only released transactions can be returned.');
            }

            $item = Item::findOrFail($transaction->item_id);

            $validated = $request->validate([
                'returns' => 'required|array|min:1',
                'returns.*.return_status' => 'required|in:Good,Lost,Damaged,For Repair,For Disposal',
                'returns.*.quantity' => 'required|integer|min:1',
                'returns.*.penalty_remarks' => 'nullable|in:Replace,Pay',
            ]);

            // Validate total quantity
            $totalReturnQuantity = array_sum(array_column($validated['returns'], 'quantity'));
            if ($totalReturnQuantity !== $transaction->approve_quantity) {
                throw ValidationException::withMessages([
                    'returns' => "Total return quantity ($totalReturnQuantity) must equal approved quantity ({$transaction->approve_quantity}).",
                ]);
            }

            // Check for late return penalty
            $returnDateTime = Carbon::parse(
                $transaction->getRawOriginal('date_of_return') . ' ' . $transaction->time_of_return
            );
            $gracePeriodDays = 2;
            $fineStartDateTime = $returnDateTime->copy()->addDays($gracePeriodDays);
            $now = now();
            $fine = 0;

            if ($now->greaterThan($fineStartDateTime)) {
                $hoursLate = $fineStartDateTime->diffInHours($now);
                $fine = $hoursLate * 5; // ₱5 per hour late

                if ($fine > 0) {
                    TransactionPenalty::create([
                        'transaction_id' => $id,
                        'item_id' => $transaction->item_id,
                        'user_id' => $transaction->user_id,
                        'quantity' => $transaction->approve_quantity,
                        'amount' => $fine,
                        'status' => 'Late Return',
                        'remarks' => 'Pay',
                    ]);
                }
            }

            // Process each return status
            foreach ($validated['returns'] as $return) {
                if ($return['return_status'] === 'Good') {
                    $item->current_qty += $return['quantity'];
                }

                TransactionStatus::create([
                    'transaction_id' => $id,
                    'item_id' => $transaction->item_id,
                    'quantity' => $return['quantity'],
                    'status' => $return['return_status'],
                ]);

                if (in_array($return['return_status'], ['Lost', 'Damaged']) && !empty($return['penalty_remarks'])) {
                    TransactionPenalty::create([
                        'transaction_id' => $id,
                        'item_id' => $transaction->item_id,
                        'user_id' => $transaction->user_id,
                        'quantity' => $return['quantity'],
                        'amount' => $item->item_price * $return['quantity'],
                        'status' => $return['return_status'],
                        'remarks' => $return['penalty_remarks'],
                    ]);
                }
            }

            $item->save();
            $transaction->update(['status' => 'Returned']);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Transaction successfully returned.',
            ], 200);
        } catch (ValidationException $e) {
            DB::rollback();

            return response()->json([
                'valid' => false,
                'msg' => '',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to return transaction: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to return transaction. Please try again later.',
            ], 500);
        }
    }
}
