<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $items = Item::with('category.laboratory');

        // Apply laboratory filter if provided
        if ($request->laboratory) {
            $items->whereHas('category', function ($query) use ($request) {
                $query->where('laboratory_id', $request->laboratory);
            });
        }

        if (in_array($user->user_role, ['Laboratory Head', 'Laboratory In-charge'])) {
            $items->whereHas('category', function ($query) use ($user) {
                $query->where('laboratory_id', $user->laboratory_id);
            });
        }

        $response = $items->get()->map(function ($item, $index) {
            $actionUpdate = '<button onclick="update(' . "'" . $item->id . "'" . ')" type="button" title="Update" class="btn btn-secondary"><i class="fas fa-edit"></i></button>';
            $actionDelete = '<button onclick="trash(' . "'" . $item->id . "'" . ')" type="button" title="Delete" class="btn btn-danger"><i class="fas fa-trash"></i></button>';
            $action = $actionUpdate;

            return [
                'count' => $index + 1,
                'item_name' => ucwords(strtolower($item->item_name)),
                'item_description' => $item->item_description,
                'item_price' => number_format($item->item_price, 2),
                'category' => $item->category ? $item->category->category_name : 'N/A',
                'laboratory' => $item->category && $item->category->laboratory ? $item->category->laboratory->name : 'N/A',
                'beginning_qty' => $item->beginning_qty,
                'current_qty' => $item->current_qty,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                'action' => $action,
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

        try {
            $validated = $request->validate([
                'item_name' => 'required|string|max:50|unique:items,item_name',
                'item_description' => 'required|string',
                'item_price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'beginning_qty' => 'required|integer|min:0',
                'current_qty' => 'required|integer|min:0',
            ], [
                'item_name.required' => 'Item name is required.',
                'item_name.string' => 'Item name must be a string.',
                'item_name.max' => 'Item name cannot exceed 50 characters.',
                'item_name.unique' => 'This item name is already taken.',
                'item_description.required' => 'Item description is required.',
                'item_description.string' => 'Item description must be a string.',
                'item_price.required' => 'Item price is required.',
                'item_price.numeric' => 'Item price must be a number.',
                'item_price.min' => 'Item price cannot be negative.',
                'category_id.required' => 'Category is required.',
                'category_id.exists' => 'Selected category does not exist.',
                'beginning_qty.required' => 'Beginning quantity is required.',
                'beginning_qty.integer' => 'Beginning quantity must be an integer.',
                'beginning_qty.min' => 'Beginning quantity cannot be negative.',
                'current_qty.required' => 'Current quantity is required.',
                'current_qty.integer' => 'Current quantity must be an integer.',
                'current_qty.min' => 'Current quantity cannot be negative.',
            ]);

            $item = Item::create($validated);

            // Prepare QR code content
            $qrCodeContent = json_encode([
                'item_id' => $item->id,
                'item_name' => $item->item_name,
            ]);

            // Generate QR code using Endroid Builder and GD renderer (PNG)
            $result = Builder::create()
                ->writer(new PngWriter()) // Use GD-based PNG writer
                ->data($qrCodeContent)
                ->size(200)
                ->margin(10)
                ->build();

            // Define temp path
            $tempPath = 'qrcodes/item_' . $item->id . '.png';

            // Store QR code image to disk
            Storage::disk('public')->put($tempPath, $result->getString());

            // Add to media library
            $item->addMedia(Storage::disk('public')->path($tempPath))
                ->toMediaCollection('qrcode');

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Item successfully stored.',
                'item' => $item,
            ], 201);
        } catch (ValidationException $e) {
            DB::rollback();

            return response()->json([
                'valid' => false,
                'msg' => '',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to store item: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to store item. Please try again later.',
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
            $item = Item::findOrFail($id);

            DB::commit();

            return response()->json($item, 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to retrieve item: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to retrieve item. Please try again later.',
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
            $validated = $request->validate([
                'item_name' => 'required|string|max:50|unique:items,item_name,' . $id,
                'item_description' => 'required|string',
                'item_price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'beginning_qty' => 'required|integer|min:0',
                'current_qty' => 'required|integer|min:0',
            ], [
                'item_name.required' => 'Item name is required.',
                'item_name.string' => 'Item name must be a string.',
                'item_name.max' => 'Item name cannot exceed 50 characters.',
                'item_name.unique' => 'This item name is already taken.',
                'item_description.required' => 'Item description is required.',
                'item_description.string' => 'Item description must be a string.',
                'item_price.required' => 'Item price is required.',
                'item_price.numeric' => 'Item price must be a number.',
                'item_price.min' => 'Item price cannot be negative.',
                'category_id.required' => 'Category is required.',
                'category_id.exists' => 'Selected category does not exist.',
                'beginning_qty.required' => 'Beginning quantity is required.',
                'beginning_qty.integer' => 'Beginning quantity must be an integer.',
                'beginning_qty.min' => 'Beginning quantity cannot be negative.',
                'current_qty.required' => 'Current quantity is required.',
                'current_qty.integer' => 'Current quantity must be an integer.',
                'current_qty.min' => 'Current quantity cannot be negative.',
            ]);

            $item = Item::findOrFail($id);
            $item->update($validated);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Item successfully updated.',
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
            Log::error('Failed to update item: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update item. Please try again later.',
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
            $item = Item::findOrFail($id);

            // Delete associated media (e.g., QR code)
            $item->clearMediaCollection('qrcode');

            // Delete the item
            $item->delete();

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Item successfully deleted.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete item: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to delete item. Please try again later.',
            ], 500);
        }
    }
}
