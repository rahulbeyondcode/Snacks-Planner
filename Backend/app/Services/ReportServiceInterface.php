<?php

namespace App\Services;

interface ReportServiceInterface
{
    /**
     * @param array $params ['type' => ..., 'month' => ..., 'format' => ...]
     * @return array|null ['filename' => ..., 'content' => ...]
     */
    public function generateReport(array $params);
}
