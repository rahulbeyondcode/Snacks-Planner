<?php

namespace App\Services;

interface OfficeHolidayServiceInterface
{
    public function updateHoliday($id, array $data);
    public function deleteHoliday($id);
    public function getAllHolidays();
    public function createHoliday(array $data);
    public function setHoliday(array $data);
    public function isHolidaySet(string $holidayDate);
    public function getOfficeHolidays();
    public function getNoSnacksDaysForGroup(int $groupId, ?int $year = null, ?int $month = null);
    public function isHolidaySetForTypeAndGroup(string $holidayDate, string $type, ?int $groupId = null);
}