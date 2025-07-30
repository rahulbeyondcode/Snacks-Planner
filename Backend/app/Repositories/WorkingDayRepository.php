<?php
namespace App\Repositories;

use App\Models\WorkingDay;

class WorkingDayRepository implements WorkingDayRepositoryInterface
{
    public function getCurrent()
    {
        return WorkingDay::latest()->first();
    }

    public function update(array $days, $userId)
    {
        $record = WorkingDay::latest()->first();
        if (!$record) {
            return WorkingDay::create([
                'working_days' => $days,
                'user_id' => $userId
            ]);
        }
        $record->working_days = $days;
        $record->user_id = $userId;
        $record->save();
        return $record;
    }
}
