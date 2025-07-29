<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeHolidayRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'holiday_date' => 'required|date_format:d-M-Y|unique:office_holidays,holiday_date',
            'description' => 'nullable|string|max:255',
        ];
    }
}
