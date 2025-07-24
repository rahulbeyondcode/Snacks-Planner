<?php

namespace App\Services;

interface ProfitLossServiceInterface
{
    /**
     * Return a summary of profit/loss for a given period (e.g., month).
     * @param array $filters
     * @return array
     */
    public function getProfitLossSummary(array $filters = []);
}
