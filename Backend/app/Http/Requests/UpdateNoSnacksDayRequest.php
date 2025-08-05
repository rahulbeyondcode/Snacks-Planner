<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\OfficeHoliday;
use Illuminate\Support\Facades\Auth;

class UpdateNoSnacksDayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
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
                $user = Auth::user();
                if (!$user) {
                    $validator->errors()->add('user', 'User not authenticated.');
                    return;
                }

                // Get user's group (assuming snack_manager belongs to a group)
                $groupMember = $user->groupMembers()->where('role_id', \App\Models\Role::SNACK_MANAGER)->first();
                if (!$groupMember) {
                    $validator->errors()->add('group', 'User is not a snack manager in any group.');
                    return;
                }

                $holidayId = $this->route('id');
                $date = \Carbon\Carbon::createFromFormat('d-M-Y', $this->holiday_date)->format('Y-m-d');

                $exists = OfficeHoliday::where('holiday_date', $date)
                    ->where('type', OfficeHoliday::TYPE_NO_SNACKS_DAY)
                    ->where('group_id', $groupMember->group_id)
                    ->where('holiday_id', '!=', $holidayId)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('holiday_date', 'This no snacks day already exists for your group.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'holiday_date.required' => 'The date is required.',
            'holiday_date.date_format' => 'The date must be in d-M-Y format.'
        ];
    }
}