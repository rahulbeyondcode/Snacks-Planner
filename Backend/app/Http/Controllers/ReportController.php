<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportServiceInterface;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DownloadReportRequest;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportServiceInterface $reportService)
    {
        $this->reportService = $reportService;
    }

    // Only account_manager can download reports
    public function download(DownloadReportRequest $request)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $report = $this->reportService->generateReport($request->validated());
        if (!$report) {
            return response()->json(['message' => 'No data found for report'], 404);
        }

        $filename = $report['filename'];
        $content = $report['content'];
        $mime = $request->input('format') === 'xls' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/pdf';

        return response($content, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
