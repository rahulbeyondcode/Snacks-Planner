<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SnackRotationService;

class SnackGroupRotateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snack:rotate-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rotate snack planning groups and update user roles according to the monthly/weekly schedule.';

    protected $snackRotationService;

    public function __construct(SnackRotationService $snackRotationService)
    {
        parent::__construct();
        $this->snackRotationService = $snackRotationService;
    }

    public function handle()
    {
        $this->info('Starting snack group and role rotation...');
        $result = $this->snackRotationService->rotateGroupsAndRoles();
        if ($result) {
            $this->info('Snack group and role rotation completed successfully.');
        } else {
            $this->error('Snack group and role rotation failed. Check logs for details.');
        }
        return $result ? 0 : 1;
    }
}
