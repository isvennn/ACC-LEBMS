<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $categories = Category::with('laboratory');

        if (in_array($user->user_role, ['Laboratory Head', 'Laboratory In-charge'])) {
            $categories->where('laboratory_id', $user->laboratory_id);
        }

        $response = $categories->get()->map(function ($category, $index) {
            $actionUpdate = '<button onclick="update(' . "'" . $category->id . "'" . ')" type="button" title="Update" class="btn btn-secondary"><i class="fas fa-edit"></i></button>';
            $actionDelete = '<button onclick="trash(' . "'" . $category->id . "'" . ')" type="button" title="Delete" class="btn btn-danger"><i class="fas fa-trash"></i></button>';
            $action = $actionUpdate;

            return [
                'count' => $index + 1,
                'category_name' => ucwords(strtolower($category->category_name)),
                'category_type' => $category->category_type,
                'laboratory' => $category->laboratory ? $category->laboratory->name : 'N/A',
                'created_at' => $category->created_at->format('Y-m-d H:i:s'),
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
                'category_name' => 'required|string|max:100|unique:categories,category_name',
                'category_type' => 'required|in:Tools,Equipment,Apparatus',
                'laboratory_id' => 'required|exists:laboratories,id',
            ], [
                'category_name.required' => 'Category name is required.',
                'category_name.string' => 'Category name must be a string.',
                'category_name.max' => 'Category name cannot exceed 100 characters.',
                'category_name.unique' => 'This category name is already taken.',
                'category_type.required' => 'Category type is required.',
                'category_type.in' => 'Invalid category type.',
                'laboratory_id.required' => 'Laboratory is required.',
                'laboratory_id.exists' => 'Selected laboratory does not exist.',
            ]);

            $category = Category::create($validated);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Category successfully stored.',
                'category' => $category,
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
            Log::error('Failed to store category: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to store category. Please try again later.',
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
            $category = Category::findOrFail($id);

            DB::commit();

            return response()->json($category, 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to retrieve category: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to retrieve category. Please try again later.',
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
                'category_name' => 'required|string|max:100|unique:categories,category_name,' . $id,
                'category_type' => 'required|in:Tools,Equipment,Apparatus',
                'laboratory_id' => 'required|exists:laboratories,id',
            ], [
                'category_name.required' => 'Category name is required.',
                'category_name.string' => 'Category name must be a string.',
                'category_name.max' => 'Category name cannot exceed 100 characters.',
                'category_name.unique' => 'This category name is already taken.',
                'category_type.required' => 'Category type is required.',
                'category_type.in' => 'Invalid category type.',
                'laboratory_id.required' => 'Laboratory is required.',
                'laboratory_id.exists' => 'Selected laboratory does not exist.',
            ]);

            $category = Category::findOrFail($id);
            $category->update($validated);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Category successfully updated.',
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
            Log::error('Failed to update category: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update category. Please try again later.',
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
            $category = Category::findOrFail($id);
            $category->delete();

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Category successfully deleted.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete category: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to delete category. Please try again later.',
            ], 500);
        }
    }
}
