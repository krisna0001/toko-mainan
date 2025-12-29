<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ShippingMethodController extends Controller
{
    public function index()
    {
        try {
            $methods = ShippingMethod::orderBy('name')->get();
            return response()->json(['success' => true, 'data' => $methods]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch shipping methods', 'error' => $e->getMessage()], 500);
        }
    }

    public function getActive()
    {
        try {
            $methods = ShippingMethod::where('is_active', true)->orderBy('name')->get();
            return response()->json(['success' => true, 'data' => $methods]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch active shipping methods', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'base_cost' => 'required|numeric|min:0',
                'estimated_days' => 'nullable|string|max:50',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();
            $method = ShippingMethod::create($request->all());
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Shipping method created successfully', 'data' => $method], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create shipping method', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $method = ShippingMethod::findOrFail($id);
            return response()->json(['success' => true, 'data' => $method]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Shipping method not found', 'error' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $method = ShippingMethod::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'base_cost' => 'required|numeric|min:0',
                'estimated_days' => 'nullable|string|max:50',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();
            $method->update($request->all());
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Shipping method updated successfully', 'data' => $method]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update shipping method', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $method = ShippingMethod::findOrFail($id);
            $method->delete();
            return response()->json(['success' => true, 'message' => 'Shipping method deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete shipping method', 'error' => $e->getMessage()], 500);
        }
    }
}
