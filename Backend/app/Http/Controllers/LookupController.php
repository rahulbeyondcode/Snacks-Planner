<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\OfficeHolidayResource;
use App\Http\Resources\PaymentMethodResource;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Repositories\CategoryRepository;
use App\Repositories\PaymentMethodRepository;
use App\Services\OfficeHolidayServiceInterface;
use App\Services\WorkingDayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LookupController extends Controller
{
    protected $categoryRepo;
    protected $paymentMethodRepo;
    protected $officeHolidayService;
    protected $workingDayService;

    public function __construct(
        CategoryRepository $categoryRepo,
        PaymentMethodRepository $paymentMethodRepo,
        OfficeHolidayServiceInterface $officeHolidayService,
        WorkingDayService $workingDayService
    ) {
        $this->categoryRepo = $categoryRepo;
        $this->paymentMethodRepo = $paymentMethodRepo;
        $this->officeHolidayService = $officeHolidayService;
        $this->workingDayService = $workingDayService;
    }

    /**
     * Get all lookup data for the application
     * Accessible to all authenticated users
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get permissions and transform to nested structure
        $permissionsData = $this->getPermissionsStructure();

        // Get working days
        $current = $this->workingDayService->getCurrent();
        $workingDays = $current ? $current->working_days : [];

        // Get office holidays
        if ($user && $user->role->name === 'account_manager') {
            $holidays = $this->officeHolidayService->getOfficeHolidays();
        } else {
            // For shared access, return all holidays for backward compatibility
            $holidays = $this->officeHolidayService->getAllHolidays();
        }

        // Get payment methods
        $paymentMethods = $this->paymentMethodRepo->all();

        // Get categories
        $categories = $this->categoryRepo->all();

        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $permissionsData,
                'working_days' => $workingDays,
                'holidays' => OfficeHolidayResource::collection($holidays),
                'payment_methods' => PaymentMethodResource::collection($paymentMethods),
                'categories' => CategoryResource::collection($categories)
            ]
        ]);
    }

    /**
     * Transform permissions into nested structure grouped by resource and module
     */
    private function getPermissionsStructure()
    {
        $permissions = Permission::all();

        $structure = [];

        foreach ($permissions as $permission) {
            $module = $permission->module;
            $resource = $permission->resource;
            $action = $permission->action;

            // Initialize resource if not exists
            if (!isset($structure[$resource])) {
                $structure[$resource] = [];
            }

            // Initialize module if not exists
            if (!isset($structure[$resource][$module])) {
                $structure[$resource][$module] = [];
            }

            // Add action if not already in array
            if (!in_array($action, $structure[$resource][$module])) {
                $structure[$resource][$module][] = $action;
            }
        }

        return $structure;
    }
}
