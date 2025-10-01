<?php

namespace App\Repositories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;

class PaymentMethodRepository extends BaseRepository
{
    public function __construct(PaymentMethod $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all payment methods ordered by ID
     */
    public function all(array $columns = ['*'], array $relations = [], array $filters = []): Collection
    {
        return $this->model->orderBy('id')->get($columns);
    }
}
