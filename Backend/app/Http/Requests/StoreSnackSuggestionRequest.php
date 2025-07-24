<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSnackSuggestionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'snack_name' => 'required|string|max:255',
            'comment' => 'nullable|string|max:255',
        ];
    }
}
