<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LaboratoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $laboratories = Laboratory::all();

        $response = $laboratories->map(function ($laboratory, $index) {
            $actionUpdate = '<button onclick="update(' . "'" . $laboratory->id . "'" . ')" type="button" title="Update" class="btn btn-secondary"><i class="fas fa-edit"></i></button>';
            $actionDelete = '<button onclick="trash(' . "'" . $laboratory->id . "'" . ')" type="button" title="Delete" class="btn btn-danger"><i class="fas fa-trash"></i></button>';
            $action = $actionUpdate . '&nbsp;' . $actionDelete;

            return [
                'count' => $index + 1,
                'name' => ucwords(strtolower($laboratory->name)),
                'description' => $laboratory->description,
                'created_at' => $laboratory->created_at->format('Y-m-d H:i:s'),
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
                'name' => 'required|string|max:30|unique:laboratories,name',
                'description' => 'required|string',
            ], [
                'name.required' => 'Laboratory name is required.',
                'name.string' => 'Laboratory name must be a string.',
                'name.max' => 'Laboratory name cannot exceed 30 characters.',
                'name.unique' => 'This laboratory name is already taken.',
                'description.required' => 'Laboratory description is required.',
                'description.string' => 'Laboratory description must be a string.',
            ]);

            $laboratory = Laboratory::create($validated);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Laboratory successfully stored.',
                'laboratory' => $laboratory,
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
            Log::error('Failed to store laboratory: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to store laboratory. Please try again later.',
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
            $laboratory = Laboratory::findOrFail($id);

            DB::commit();

            return response()->json($laboratory, 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to retrieve laboratory: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to retrieve laboratory. Please try again later.',
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
                'name' => 'required|string|max:30|unique:laboratories,name,' . $id,
                'description' => 'required|string',
            ], [
                'name.required' => 'Laboratory name is required.',
                'name.string' => 'Laboratory name must be a string.',
                'name.max' => 'Laboratory name cannot exceed 30 characters.',
                'name.unique' => 'This laboratory name is already taken.',
                'description.required' => 'Laboratory description is required.',
                'description.string' => 'Laboratory description must be a string.',
            ]);

            $laboratory = Laboratory::findOrFail($id);
            $laboratory->update($validated);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Laboratory successfully updated.',
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
            Log::error('Failed to update laboratory: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update laboratory. Please try again later.',
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
            $laboratory = Laboratory::findOrFail($id);
            $laboratory->delete();

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Laboratory successfully deleted.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete laboratory: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to delete laboratory. Please try again later.',
            ], 500);
        }
    }
}
