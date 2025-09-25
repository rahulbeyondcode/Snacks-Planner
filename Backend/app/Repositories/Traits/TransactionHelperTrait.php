<?php

namespace App\Repositories\Traits;

use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;

trait TransactionHelperTrait
{
    /**
     * Execute a callback within a database transaction
     */
    protected function executeInTransaction(callable $callback)
    {
        try {
            DB::beginTransaction();
            $result = $callback();
            DB::commit();
            return $result;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Execute a callback with transaction and custom error handling
     */
    protected function executeWithTransaction(callable $callback, ?callable $errorHandler = null)
    {
        try {
            DB::beginTransaction();
            $result = $callback();
            DB::commit();
            return $result;
        } catch (Throwable $e) {
            DB::rollBack();

            if ($errorHandler) {
                return $errorHandler($e);
            }

            throw $e;
        }
    }

    /**
     * Execute multiple operations in a single transaction
     */
    protected function executeMultipleInTransaction(array $operations): bool
    {
        try {
            DB::beginTransaction();

            foreach ($operations as $operation) {
                if (is_callable($operation)) {
                    $operation();
                }
            }

            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Execute with transaction and return success/failure
     */
    protected function executeSafelyInTransaction(callable $callback): array
    {
        try {
            DB::beginTransaction();
            $result = $callback();
            DB::commit();
            return ['success' => true, 'data' => $result];
        } catch (Throwable $e) {
            DB::rollBack();
            return ['success' => false, 'error' => $e->getMessage(), 'exception' => $e];
        }
    }

    /**
     * Execute with transaction and custom success/error handling
     */
    protected function executeWithCustomHandling(callable $callback, callable $onSuccess, callable $onError)
    {
        try {
            DB::beginTransaction();
            $result = $callback();
            DB::commit();
            return $onSuccess($result);
        } catch (Throwable $e) {
            DB::rollBack();
            return $onError($e);
        }
    }

    /**
     * Execute with validation and transaction
     */
    protected function executeWithValidation(callable $validator, callable $callback)
    {
        try {
            // Run validation first
            $validator();

            DB::beginTransaction();
            $result = $callback();
            DB::commit();
            return $result;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Batch insert with transaction support
     */
    protected function batchInsertWithTransaction(string $table, array $data): bool
    {
        try {
            DB::beginTransaction();

            if (!empty($data)) {
                DB::table($table)->insert($data);
            }

            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Batch update with transaction support
     */
    protected function batchUpdateWithTransaction(string $table, array $data, string $primaryKey = 'id'): bool
    {
        try {
            DB::beginTransaction();

            foreach ($data as $record) {
                DB::table($table)->where($primaryKey, $record[$primaryKey])->update($record);
            }

            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
