<?php

namespace App\Services;

interface SubGroupServiceInterface
{
    public function listSubGroups(array $filters = []);

    public function getSubGroup(int $id);

    public function createSubGroup(array $data);

    public function updateSubGroup(int $id, array $data);

    public function deleteSubGroup(int $id);
}
