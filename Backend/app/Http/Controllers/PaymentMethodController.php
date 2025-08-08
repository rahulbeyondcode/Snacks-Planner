<?php

namespace App\Http\Controllers;

use App\Repositories\PaymentMethodRepository;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
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
        try {
            $paymentMethods = $this->repo->all();
            return apiResponse(
                true,
                'Payment methods retrieved successfully',
                $paymentMethods,
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to retrieve payment methods: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    public function store(StorePaymentMethodRequest $request)
    {
        try {
            $method = $this->repo->create($request->validated());
            $all = $this->repo->all();
            return apiResponse(
                true,
                'Payment method added successfully',
                $all,
                201
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to create payment method: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    public function update(UpdatePaymentMethodRequest $request, $id)
    {
        try {
            $method = $this->repo->update($id, $request->validated());
            if (!$method) {
                return apiResponse(
                    false,
                    'Payment method not found',
                    [],
                    404
                );
            }
            $all = $this->repo->all();
            return apiResponse(
                true,
                'Payment method updated successfully',
                $all,
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to update payment method: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->repo->delete($id);
            if (!$deleted) {
                return apiResponse(
                    false,
                    'Payment method not found',
                    [],
                    404
                );
            }
            $all = $this->repo->all();
            return apiResponse(
                true,
                'Payment method deleted successfully',
                $all,
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to delete payment method: ' . $e->getMessage(),
                [],
                500
            );
        }
    }
}
