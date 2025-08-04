<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentMethodRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'method' => 'required|string|max:255|unique:payment_methods,method,' . $this->route('id'),
        ];
    }
}
