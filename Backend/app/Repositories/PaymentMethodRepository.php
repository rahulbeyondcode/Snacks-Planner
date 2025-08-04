<?php

namespace App\Repositories;

use App\Models\PaymentMethod;

class PaymentMethodRepository
{
    public function all()
    {
        return PaymentMethod::orderBy('id')->get();
    }

    public function find($id)
    {
        return PaymentMethod::find($id);
    }

    public function create(array $data)
    {
        return PaymentMethod::create($data);
    }

    public function update($id, array $data)
    {
        $method = PaymentMethod::find($id);
        if ($method) {
            $method->update($data);
        }
        return $method;
    }

    public function delete($id)
    {
        $method = PaymentMethod::find($id);
        if ($method) {
            $method->delete();
            return true;
        }
        return false;
    }
}
