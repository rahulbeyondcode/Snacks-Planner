<?php

namespace App\Services;

interface OfficeHolidayServiceInterface
{
    public function updateHoliday($id, array $data);
    public function deleteHoliday($id);
    public function getAllHolidays();
    public function setHoliday(array $data);
    public function isHolidaySet(string $holidayDate);
}
