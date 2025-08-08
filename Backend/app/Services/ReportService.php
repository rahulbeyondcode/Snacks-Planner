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

        // Format data for export based on format type
        if ($format === 'xls') {
            $formattedData = $this->formatDataForExcel($data, $type);
            $content = $this->exportXLS($formattedData, $title);
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

    protected function formatDataForExcel($data, $type)
    {
        switch ($type) {
            case 'monthly_expense':
                // Add header row
                $formattedData = [
                    ['Date', 'Total Amount']
                ];
                // Add data rows
                foreach ($data as $row) {
                    $formattedData[] = [
                        $row['snack_date'],
                        $row['total_amount']
                    ];
                }
                return $formattedData;

            case 'snack_summary':
                // Add header row
                $formattedData = [
                    ['Snack Item', 'Total Consumed']
                ];
                // Add data rows
                foreach ($data as $row) {
                    $formattedData[] = [
                        $row['snack'],
                        $row['total_consumed']
                    ];
                }
                return $formattedData;

            case 'total_contributions':
                // Add header row
                $formattedData = [
                    ['User ID', 'User Name', 'Total Records', 'Paid Contributions', 'Unpaid Records']
                ];
                // Add data rows
                if (isset($data['by_user']) && is_array($data['by_user'])) {
                    foreach ($data['by_user'] as $row) {
                        $formattedData[] = [
                            $row['user_id'],
                            $row['user_name'] ?? 'Unknown',
                            $row['total_records'],
                            $row['paid_contributions'],
                            $row['unpaid_records']
                        ];
                    }
                }
                // Add summary rows
                $formattedData[] = ['', '', '', '', '']; // Empty row
                $formattedData[] = ['Summary', '', '', '', ''];
                $formattedData[] = ['Total Paid Contributions', '', '', $data['total_paid'] ?? 0, ''];
                $formattedData[] = ['Total Unpaid Records', '', '', '', $data['total_unpaid'] ?? 0];
                $formattedData[] = ['Total All Records', '', $data['total_all'] ?? 0, '', ''];
                return $formattedData;

            default:
                return $data;
        }
    }

    protected function exportXLS($data, $title)
    {
        // Ensure data is properly formatted as array of arrays
        if (!is_array($data) || empty($data)) {
            throw new \Exception('Invalid data format for Excel export');
        }

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
