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

    public function getByType(string $type)
    {
        return OfficeHoliday::where('type', $type)->orderBy('holiday_date', 'asc')->get();
    }

    public function getByTypeAndGroup(string $type, int $groupId)
    {
        return OfficeHoliday::where('type', $type)
            ->where('group_id', $groupId)
            ->orderBy('holiday_date', 'asc')
            ->get();
    }

    public function getByTypeAndGroupForMonth(string $type, int $groupId, int $year, int $month)
    {
        return OfficeHoliday::where('type', $type)
            ->where('group_id', $groupId)
            ->whereYear('holiday_date', $year)
            ->whereMonth('holiday_date', $month)
            ->orderBy('holiday_date', 'asc')
            ->get();
    }

    public function findByDateTypeAndGroup(string $holidayDate, string $type, ?int $groupId = null)
    {
        $query = OfficeHoliday::where('holiday_date', $holidayDate)
            ->where('type', $type);

        if ($groupId !== null) {
            $query->where('group_id', $groupId);
        } else {
            $query->whereNull('group_id');
        }

        return $query->first();
    }
}