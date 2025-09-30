<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait BatchOperationTrait
{
    /**
     * Batch insert records
     */
    protected function batchInsert(array $data, ?string $table = null): bool
    {
        if (empty($data)) {
            return true;
        }

        $tableName = $table ?: $this->model->getTable();

        \DB::table($tableName)->insert($data);
        return true;
    }

    /**
     * Batch update records by IDs
     */
    protected function batchUpdate(array $data, array $ids, string $idColumn = 'id'): bool
    {
        if (empty($data) || empty($ids)) {
            return true;
        }

        return \DB::table($this->model->getTable())
            ->whereIn($idColumn, $ids)
            ->update($data) > 0;
    }

    /**
     * Batch update with different data for each record
     */
    protected function batchUpdateMultiple(array $records, string $idColumn = 'id'): bool
    {
        if (empty($records)) {
            return true;
        }

        foreach ($records as $record) {
            $id = $record[$idColumn];
            unset($record[$idColumn]);

            \DB::table($this->model->getTable())
                ->where($idColumn, $id)
                ->update($record);
        }

        return true;
    }

    /**
     * Batch delete records by IDs
     */
    protected function batchDelete(array $ids, string $idColumn = 'id'): bool
    {
        if (empty($ids)) {
            return true;
        }

        return \DB::table($this->model->getTable())
            ->whereIn($idColumn, $ids)
            ->delete() > 0;
    }

    /**
     * Batch soft delete records by IDs
     */
    protected function batchSoftDelete(array $ids, string $idColumn = 'id'): bool
    {
        if (empty($ids)) {
            return true;
        }

        return \DB::table($this->model->getTable())
            ->whereIn($idColumn, $ids)
            ->update(['deleted_at' => now()]) > 0;
    }

    /**
     * Batch create model instances
     */
    protected function batchCreate(array $data): Collection
    {
        if (empty($data)) {
            return collect();
        }

        $models = [];
        foreach ($data as $item) {
            $models[] = $this->model->create($item);
        }

        return collect($models);
    }

    /**
     * Batch update or create records
     */
    protected function batchUpsert(array $data, array $uniqueBy, ?array $update = null): bool
    {
        if (empty($data)) {
            return true;
        }

        \DB::table($this->model->getTable())->upsert($data, $uniqueBy, $update);
        return true;
    }

    /**
     * Batch assign relationships
     */
    protected function batchAssignRelationship(Model $parent, string $relationship, array $relatedIds): void
    {
        $parent->$relationship()->sync($relatedIds);
    }

    /**
     * Batch detach relationships
     */
    protected function batchDetachRelationship(Model $parent, string $relationship, array $relatedIds = []): void
    {
        if (empty($relatedIds)) {
            $parent->$relationship()->detach();
        } else {
            $parent->$relationship()->detach($relatedIds);
        }
    }

    /**
     * Batch sync relationships with additional data
     */
    protected function batchSyncWithPivot(Model $parent, string $relationship, array $data): void
    {
        $parent->$relationship()->sync($data);
    }

    /**
     * Prepare batch data with timestamps
     */
    protected function prepareBatchData(array $data, array $additionalFields = []): array
    {
        $now = now();
        $preparedData = [];

        foreach ($data as $item) {
            $preparedItem = array_merge($item, $additionalFields, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $preparedData[] = $preparedItem;
        }

        return $preparedData;
    }

    /**
     * Prepare batch update data
     */
    protected function prepareBatchUpdateData(array $data, string $idColumn = 'id'): array
    {
        $preparedData = [];

        foreach ($data as $item) {
            $id = $item[$idColumn];
            unset($item[$idColumn]);

            $preparedData[] = [
                'id' => $id,
                'data' => $item,
            ];
        }

        return $preparedData;
    }

    /**
     * Execute batch operation with transaction
     */
    protected function executeBatchOperation(callable $operation, array $data): bool
    {
        if (empty($data)) {
            return true;
        }

        try {
            \DB::beginTransaction();

            foreach ($data as $item) {
                $operation($item);
            }

            \DB::commit();
            return true;
        } catch (\Throwable $e) {
            \DB::rollBack();
            throw $e;
        }
    }
}
