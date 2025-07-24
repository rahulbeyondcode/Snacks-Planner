<?php

namespace App\Repositories;

interface OfficeHolidayRepositoryInterface
{
    public function update($id, array $data);
    public function delete($id);
    public function create(array $data);
    public function findByDate(string $holidayDate);
    public function getAll();
}
