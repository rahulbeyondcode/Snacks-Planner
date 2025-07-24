<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DownloadReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|in:monthly_expense,snack_summary,total_contributions',
            'month' => 'required_if:type,monthly_expense,snack_summary|date_format:Y-m',
            'format' => 'required|in:xls,pdf',
        ];
    }
}
