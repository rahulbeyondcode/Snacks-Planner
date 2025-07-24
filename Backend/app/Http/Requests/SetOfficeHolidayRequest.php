<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetOfficeHolidayRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'holiday_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ];
    }
}
