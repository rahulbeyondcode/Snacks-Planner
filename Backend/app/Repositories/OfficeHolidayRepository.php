<?php

namespace App\Repositories;

use App\Models\OfficeHoliday;

class OfficeHolidayRepository implements OfficeHolidayRepositoryInterface
{
    public function update($id, array $data)
    {
        $holiday = OfficeHoliday::find($id);
        if ($holiday) {
            $holiday->update($data);
        }
        return $holiday;
    }

    public function delete($id)
    {
        $holiday = OfficeHoliday::find($id);
        if ($holiday) {
            $holiday->delete();
            return true;
        }
        return false;
    }
    public function getAll()
    {
        return OfficeHoliday::orderBy('holiday_date', 'asc')->get();
    }
    public function create(array $data)
    {
        return OfficeHoliday::create($data);
    }

    public function findByDate(string $holidayDate)
    {
        return OfficeHoliday::where('holiday_date', $holidayDate)->first();
    }
}
