<?php

namespace App\Http\Controllers;

use App\Repositories\PaymentMethodRepository;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    protected $repo;
    public function __construct(PaymentMethodRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $paymentMethods = $this->repo->all();
        return response()->json([
            'success' => true,
            'data' => PaymentMethodResource::collection($paymentMethods)
        ]);
    }

    public function store(StorePaymentMethodRequest $request)
    {
        $paymentMethod = $this->repo->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Payment method created successfully',
            'data' => new PaymentMethodResource($paymentMethod)
        ], 201);
    }

    public function update(UpdatePaymentMethodRequest $request, $id)
    {
        $paymentMethod = $this->repo->update($id, $request->validated());
        if (!$paymentMethod) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method not found',
                'data' => []
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Payment method updated successfully',
            'data' => new PaymentMethodResource($paymentMethod)
        ]);
    }

    public function destroy($id)
    {
        $paymentMethod = $this->repo->find($id);
        if (!$paymentMethod) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method not found',
                'data' => []
            ], 404);
        }
        $this->repo->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Payment method deleted successfully',
            'data' => []
        ]);
    }
}
