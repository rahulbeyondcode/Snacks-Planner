<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkingDaysRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            'working_days' => 'required|array|min:1|max:7',
            'working_days.*' => 'required|string|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
        ];
    }
}
