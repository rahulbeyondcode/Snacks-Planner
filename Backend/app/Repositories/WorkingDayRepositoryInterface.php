<?php
namespace App\Repositories;

interface WorkingDayRepositoryInterface
{
    public function getCurrent();
    public function update(array $days, $userId);
}
