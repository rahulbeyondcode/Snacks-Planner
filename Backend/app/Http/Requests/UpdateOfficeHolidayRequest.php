<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfficeHolidayRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'holiday_date' => 'sometimes|date',
            'description' => 'nullable|string|max:255',
        ];
    }
}
