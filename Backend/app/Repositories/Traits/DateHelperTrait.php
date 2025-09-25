<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

trait DateHelperTrait
{
    /**
     * Apply date range filter to query
     */
    protected function applyDateRange(Builder $query, string $column, ?string $dateFrom = null, ?string $dateTo = null): void
    {
        if ($dateFrom) {
            $query->whereDate($column, '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate($column, '<=', $dateTo);
        }
    }

    /**
     * Apply month filter to query
     */
    protected function applyMonthFilter(Builder $query, string $column, ?string $month = null): void
    {
        if ($month) {
            $query->whereRaw('DATE_FORMAT(' . $column . ', "%Y-%m") = ?', [$month]);
        }
    }

    /**
     * Apply current month filter to query
     */
    protected function applyCurrentMonthFilter(Builder $query, string $column): void
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->toDateString();
        $monthEnd = $now->copy()->endOfMonth()->toDateString();

        $query->whereDate($column, '>=', $monthStart)
              ->whereDate($column, '<=', $monthEnd);
    }

    /**
     * Apply year-month filter to query
     */
    protected function applyYearMonthFilter(Builder $query, string $column, ?string $yearMonth = null): void
    {
        if ($yearMonth) {
            $query->whereRaw('DATE_FORMAT(' . $column . ', "%Y-%m") = ?', [$yearMonth]);
        }
    }

    /**
     * Get current month date range
     */
    protected function getCurrentMonthRange(): array
    {
        $now = now();
        return [
            'start' => $now->copy()->startOfMonth()->toDateString(),
            'end' => $now->copy()->endOfMonth()->toDateString(),
        ];
    }

    /**
     * Get month date range from month string (YYYY-MM)
     */
    protected function getMonthRange(string $month): array
    {
        $carbonDate = Carbon::createFromFormat('Y-m', $month);
        return [
            'start' => $carbonDate->copy()->startOfMonth()->toDateString(),
            'end' => $carbonDate->copy()->endOfMonth()->toDateString(),
        ];
    }

    /**
     * Apply date range filter with time consideration
     */
    protected function applyDateTimeRange(Builder $query, string $column, ?string $dateFrom = null, ?string $dateTo = null): void
    {
        if ($dateFrom) {
            $query->where($column, '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where($column, '<=', $dateTo);
        }
    }

    /**
     * Apply current month filter with time consideration
     */
    protected function applyCurrentMonthTimeFilter(Builder $query, string $column): void
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $query->whereBetween($column, [$monthStart, $monthEnd]);
    }

    /**
     * Apply search filter with date consideration
     */
    protected function applySearchWithDate(Builder $query, string $searchColumn, string $dateColumn, string $searchTerm, ?string $month = null): void
    {
        if ($month) {
            $this->applyMonthFilter($query, $dateColumn, $month);
        }

        if ($searchTerm) {
            $query->where($searchColumn, 'like', '%' . $searchTerm . '%');
        }
    }

    /**
     * Get date range for reporting (current month)
     */
    protected function getReportingDateRange(): array
    {
        $now = now();
        return [
            'month_start' => $now->copy()->startOfMonth()->toDateString(),
            'month_end' => $now->copy()->endOfMonth()->toDateString(),
            'year_month' => $now->format('Y-m'),
        ];
    }

    /**
     * Apply overlapping date range filter (for periods that overlap with given dates)
     */
    protected function applyOverlappingDateFilter(Builder $query, string $startColumn, string $endColumn, string $startDate, string $endDate): void
    {
        $query->where(function ($q) use ($startColumn, $endColumn, $startDate, $endDate) {
            $q->whereRaw('? BETWEEN ' . $startColumn . ' AND ' . $endColumn, [$startDate])
              ->orWhereRaw('? BETWEEN ' . $startColumn . ' AND ' . $endColumn, [$endDate])
              ->orWhereRaw($startColumn . ' <= ? AND ' . $endColumn . ' >= ?', [$startDate, $endDate]);
        });
    }
}
