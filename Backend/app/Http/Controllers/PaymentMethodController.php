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
        return response()->json($this->repo->all());
    }

    public function store(StorePaymentMethodRequest $request)
    {
        $method = $this->repo->create($request->validated());
        $all = $this->repo->all();
        return response()->json(['message' => 'Payment method added successfully', 'data' => $all], 201);
    }

    public function update(UpdatePaymentMethodRequest $request, $id)
    {
        $method = $this->repo->update($id, $request->validated());
        if (!$method) {
            return response()->json(['message' => 'Payment method not found'], 404);
        }
        $all = $this->repo->all();
        return response()->json(['message' => 'Payment method updated successfully', 'data' => $all]);
    }

    public function destroy($id)
    {
        $deleted = $this->repo->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Payment method not found'], 404);
        }
        $all = $this->repo->all();
        return response()->json(['message' => 'Payment method deleted successfully', 'data' => $all]);
    }
}
