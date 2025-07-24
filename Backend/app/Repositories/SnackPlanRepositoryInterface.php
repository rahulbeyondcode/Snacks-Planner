<?php

namespace App\Repositories;

interface SnackPlanRepositoryInterface
{
    public function create(array $data);
    public function find(int $id);
    public function list(array $filters = []);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getMonthlyExpense(string $month);
    public function getSnackSummary(string $month);
}
