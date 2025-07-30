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
            'holiday_date' => 'required|date_format:d-M-Y',
            'description' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('holiday_date')) {
                $date = \Carbon\Carbon::createFromFormat('d-M-Y', $this->holiday_date)->format('Y-m-d');
                $exists = \App\Models\OfficeHoliday::where('holiday_date', $date)->exists();
                if ($exists) {
                    $validator->errors()->add('holiday_date', 'This holiday date already exists.');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'holiday_date.required' => 'The holiday date is required.',
            'holiday_date.date_format' => 'The holiday date must be in d-M-Y format.'
        ];
    }
}
