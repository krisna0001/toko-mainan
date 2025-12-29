<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    public function index()
    {
        try {
            $methods = PaymentMethod::orderBy('order')->get();
            return response()->json(['success' => true, 'data' => $methods]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch payment methods', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|unique:payment_methods,code',
                'description' => 'nullable|string',
                'icon' => 'nullable|string',
                'fee' => 'nullable|numeric|min:0',
                'fee_type' => 'required|in:fixed,percentage',
                'is_active' => 'boolean',
                'order' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();
            $method = PaymentMethod::create($request->all());
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Payment method created successfully', 'data' => $method], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create payment method', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $method = PaymentMethod::findOrFail($id);
            return response()->json(['success' => true, 'data' => $method]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Payment method not found', 'error' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $method = PaymentMethod::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'code' => 'sometimes|required|string|unique:payment_methods,code,' . $id,
                'description' => 'nullable|string',
                'icon' => 'nullable|string',
                'fee' => 'nullable|numeric|min:0',
                'fee_type' => 'sometimes|required|in:fixed,percentage',
                'is_active' => 'boolean',
                'order' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();
            $method->update($request->all());
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Payment method updated successfully', 'data' => $method]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update payment method', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $method = PaymentMethod::findOrFail($id);
            $method->delete();
            return response()->json(['success' => true, 'message' => 'Payment method deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete payment method', 'error' => $e->getMessage()], 500);
        }
    }
}
