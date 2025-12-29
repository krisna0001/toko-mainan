<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PromoController extends Controller
{
    public function index()
    {
        try {
            $promos = Promo::orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'data' => $promos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch promos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|unique:promos,code',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:percentage,fixed',
                'value' => 'required|numeric|min:0',
                'min_purchase' => 'nullable|numeric|min:0',
                'max_discount' => 'nullable|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:1',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            $promo = Promo::create($request->all());
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Promo created successfully',
                'data' => $promo
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create promo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $promo = Promo::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $promo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Promo not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $promo = Promo::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'code' => 'sometimes|required|string|unique:promos,code,' . $id,
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'sometimes|required|in:percentage,fixed',
                'value' => 'sometimes|required|numeric|min:0',
                'min_purchase' => 'nullable|numeric|min:0',
                'max_discount' => 'nullable|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:1',
                'start_date' => 'sometimes|required|date',
                'end_date' => 'sometimes|required|date|after:start_date',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            $promo->update($request->all());
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Promo updated successfully',
                'data' => $promo
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update promo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $promo = Promo::findOrFail($id);
            $promo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Promo deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete promo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function validate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
                'total' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $promo = Promo::where('code', $request->code)
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            if (!$promo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired promo code'
                ], 404);
            }

            if ($promo->min_purchase > 0 && $request->total < $promo->min_purchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimum purchase of Rp ' . number_format($promo->min_purchase) . ' required'
                ], 400);
            }

            if ($promo->usage_limit && $promo->used_count >= $promo->usage_limit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Promo code usage limit reached'
                ], 400);
            }

            $discount = 0;
            if ($promo->type === 'percentage') {
                $discount = ($request->total * $promo->value) / 100;
                if ($promo->max_discount && $discount > $promo->max_discount) {
                    $discount = $promo->max_discount;
                }
            } else {
                $discount = $promo->value;
            }

            return response()->json([
                'success' => true,
                'message' => 'Promo code is valid',
                'data' => [
                    'promo' => $promo,
                    'discount' => $discount,
                    'final_total' => $request->total - $discount
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate promo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

