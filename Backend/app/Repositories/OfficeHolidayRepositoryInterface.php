<?php

namespace App\Repositories;

interface OfficeHolidayRepositoryInterface
{
    public function update($id, array $data);
    public function delete($id);
    public function create(array $data);
    public function findByDate(string $holidayDate);
    public function getAll();
    public function getByType(string $type);
    public function getByTypeAndGroup(string $type, int $groupId);
    public function getByTypeAndGroupForMonth(string $type, int $groupId, int $year, int $month);
    public function findByDateTypeAndGroup(string $holidayDate, string $type, ?int $groupId = null);
}