<?php

namespace App\Services;

use App\Repositories\SnackPlanRepositoryInterface;
use App\Repositories\ContributionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService implements ReportServiceInterface
{
    protected $snackPlanRepository;
    protected $contributionRepository;

    public function __construct(
        SnackPlanRepositoryInterface $snackPlanRepository,
        ContributionRepositoryInterface $contributionRepository
    ) {
        $this->snackPlanRepository = $snackPlanRepository;
        $this->contributionRepository = $contributionRepository;
    }

    public function generateReport(array $params)
    {
        $type = $params['type'];
        $format = $params['format'];
        $month = $params['month'] ?? null;

        switch ($type) {
            case 'monthly_expense':
                $data = $this->getMonthlyExpenseData($month);
                $filename = "monthly_expense_{$month}." . $format;
                $title = 'Monthly Expense Report';
                break;
            case 'snack_summary':
                $data = $this->getSnackSummaryData($month);
                $filename = "snack_summary_{$month}." . $format;
                $title = 'Snack Consumption Summary';
                break;
            case 'total_contributions':
                $data = $this->getTotalContributionsData();
                $filename = "total_contributions." . $format;
                $title = 'Total Contributions';
                break;
            default:
                return null;
        }

        if (empty($data)) {
            return null;
        }

        if ($format === 'xls') {
            $content = $this->exportXLS($data, $title);
        } else {
            $content = $this->exportPDF($data, $title);
        }
        return [
            'filename' => $filename,
            'content' => $content,
        ];
    }

    protected function getMonthlyExpenseData($month)
    {
        // Example: fetch all snack plans for the month and sum total_amount
        return $this->snackPlanRepository->getMonthlyExpense($month);
    }

    protected function getSnackSummaryData($month)
    {
        // Example: fetch snack consumption grouped by item for the month
        return $this->snackPlanRepository->getSnackSummary($month);
    }

    protected function getTotalContributionsData()
    {
        // Example: sum contributions grouped by user or overall
        return $this->contributionRepository->getTotalContributions();
    }

    protected function exportXLS($data, $title)
    {
        // Use a package like Maatwebsite/Laravel-Excel (Excel::raw) for XLS export
        // Here, return dummy XLS content for illustration
        return Excel::raw(new \App\Exports\GenericExport($data, $title), \Maatwebsite\Excel\Excel::XLSX);
    }

    protected function exportPDF($data, $title)
    {
        // Use a package like barryvdh/laravel-dompdf (Pdf::loadView) for PDF export
        // Here, return dummy PDF content for illustration
        $pdf = Pdf::loadView('reports.generic', ['data' => $data, 'title' => $title]);
        return $pdf->output();
    }
}
