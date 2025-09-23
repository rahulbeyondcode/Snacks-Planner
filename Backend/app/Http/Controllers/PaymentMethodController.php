<?php

namespace App\Http\Controllers;

use App\Repositories\PaymentMethodRepository;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use Illuminate\Http\Request;

class PaymentMethodController extends BaseController
{
    protected $repo;
    public function __construct(PaymentMethodRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $paymentMethods = $this->repo->all();
        return $this->resourceCollectionResponse(PaymentMethodResource::collection($paymentMethods));
    }

    public function store(StorePaymentMethodRequest $request)
    {
        $paymentMethod = $this->repo->create($request->validated());
        return $this->createdResponse(new PaymentMethodResource($paymentMethod), 'Payment method created successfully');
    }

    public function update(UpdatePaymentMethodRequest $request, $id)
    {
        $paymentMethod = $this->repo->update($id, $request->validated());
        if (!$paymentMethod) {
            return $this->notFoundResponse('Payment method not found');
        }
        return $this->updatedResponse(new PaymentMethodResource($paymentMethod), 'Payment method updated successfully');
    }

    public function destroy($id)
    {
        $paymentMethod = $this->repo->find($id);
        if (!$paymentMethod) {
            return $this->notFoundResponse('Payment method not found');
        }
        $this->repo->delete($id);
        return $this->deletedResponse('Payment method deleted successfully');
    }
}
