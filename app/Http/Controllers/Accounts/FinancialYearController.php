<?php

namespace App\Http\Controllers\Accounts;

use App\Exports\FinancialYearDetailsExport;
use App\Exports\FinancialYearOpeningBalanceExport;
use App\Http\Controllers\Controller;
use App\Models\FinancialYear;
use App\Service\EmployeeService;
use App\Service\FinancialYearService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FinancialYearController extends Controller
{
    public function __construct(private FinancialYearService $financialYearService)
    {
        $this->middleware('auth');
        $this->middleware('permission:financial_year_view', ['only' => [
            'index',
            'list',
            'openingBalance',
            'openingBalancePrint',
            'openingBalanceExportToPDF',
            'openingBalanceExportToExcel',
            'details',
            'detailsPrint',
            'detailsExportToPDF',
            'detailsExportToExcel',
        ]]);
        $this->middleware('permission:financial_year_initialize', ['only' => ['initialize']]);
        $this->middleware('permission:financial_year_close', ['only' => ['previewClose', 'close']]);
        $this->middleware('permission:financial_year_open', ['only' => ['previewOpen', 'open']]);
    }

    public function index()
    {
        return view('accounts.financial-year.index', [
            'permissions' => [
                'initialize' => auth()->user()->can('financial_year_initialize'),
                'close' => auth()->user()->can('financial_year_close'),
                'open' => auth()->user()->can('financial_year_open'),
            ],
        ]);
    }

    public function list()
    {
        return response()->json([
            'data' => $this->financialYearService->listYears(),
            'permissions' => [
                'initialize' => auth()->user()->can('financial_year_initialize'),
                'close' => auth()->user()->can('financial_year_close'),
                'open' => auth()->user()->can('financial_year_open'),
            ],
        ]);
    }

    public function initialize()
    {
        try {
            $year = $this->financialYearService->initializeCurrentFinancialYear();

            return response()->json([
                'success' => true,
                'message' => "Financial year {$year->label} initialized successfully.",
                'data' => $this->financialYearService->listYears(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function previewClose(Request $request)
    {
        $request->validate([
            'financial_year_id' => 'required|exists:financial_years,id',
        ]);

        try {
            $year = FinancialYear::findOrFail($request->financial_year_id);

            return response()->json([
                'success' => true,
                'data' => $this->financialYearService->previewClose($year),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function close(Request $request, EmployeeService $employeeService)
    {
        $request->validate([
            'financial_year_id' => 'required|exists:financial_years,id',
            'auth_password' => 'required|string',
        ]);

        $passwordError = $employeeService->validateCurrentUserPassword($request->auth_password);
        if ($passwordError) {
            return response()->json([
                'success' => false,
                'message' => $passwordError,
                'errors' => ['auth_password' => [$passwordError]],
            ], 422);
        }

        try {
            $year = FinancialYear::findOrFail($request->financial_year_id);
            $group = $this->financialYearService->closeFinancialYear($year);

            return response()->json([
                'success' => true,
                'message' => "Financial year {$year->label} closed successfully.",
                'voucher_no' => $group->voucher_no ?? null,
                'data' => $this->financialYearService->listYears(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function previewOpen(Request $request)
    {
        try {
            $afterYear = null;
            if ($request->filled('financial_year_id')) {
                $afterYear = FinancialYear::findOrFail($request->financial_year_id);
            }

            return response()->json([
                'success' => true,
                'data' => $this->financialYearService->previewOpeningBalance($afterYear),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function open(Request $request)
    {
        try {
            $afterYear = null;
            if ($request->filled('financial_year_id')) {
                $afterYear = FinancialYear::findOrFail($request->financial_year_id);
            }

            $year = $this->financialYearService->openNextFinancialYear($afterYear);

            return response()->json([
                'success' => true,
                'message' => "Financial year {$year->label} opened successfully with opening balance audit voucher.",
                'opening_voucher_no' => $year->openingTransactionGroup?->voucher_no,
                'data' => $this->financialYearService->listYears(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function openingBalance(Request $request)
    {
        $request->validate([
            'financial_year_id' => 'required|exists:financial_years,id',
        ]);

        try {
            $year = FinancialYear::findOrFail($request->financial_year_id);

            return response()->json([
                'success' => true,
                'data' => $this->financialYearService->getOpeningBalanceDetails($year),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function openingBalancePrint(Request $request)
    {
        $report = $this->buildOpeningBalanceReport($request);

        return view('accounts.financial-year.opening-balance-print', $report);
    }

    public function openingBalanceExportToPDF(Request $request)
    {
        $report = $this->buildOpeningBalanceReport($request);
        $label = $report['financial_year']['label'] ?? 'financial_year';
        $fileName = 'opening_balance_audit_'.$label.'_'.date('Y_m_d_H_i_s').'.pdf';

        $pdf = Pdf::loadView('accounts.financial-year.opening-balance-pdf-view', $report);

        return $pdf->download($fileName);
    }

    public function openingBalanceExportToExcel(Request $request)
    {
        $report = $this->buildOpeningBalanceReport($request);
        $label = $report['financial_year']['label'] ?? 'financial_year';
        $fileName = 'opening_balance_audit_'.$label.'_'.date('Y_m_d_H_i_s').'.xlsx';

        return Excel::download(new FinancialYearOpeningBalanceExport($report), $fileName);
    }

    private function buildOpeningBalanceReport(Request $request): array
    {
        $request->validate([
            'financial_year_id' => 'required|exists:financial_years,id',
        ]);

        $year = FinancialYear::findOrFail($request->financial_year_id);

        return $this->financialYearService->getOpeningBalanceDetails($year);
    }

    public function details(Request $request)
    {
        $request->validate([
            'financial_year_id' => 'required|exists:financial_years,id',
        ]);

        try {
            $year = FinancialYear::findOrFail($request->financial_year_id);

            return response()->json([
                'success' => true,
                'data' => $this->financialYearService->getYearDetails($year),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function detailsPrint(Request $request)
    {
        $report = $this->buildYearDetailsReport($request);

        return view('accounts.financial-year.details-print', $report);
    }

    public function detailsExportToPDF(Request $request)
    {
        $report = $this->buildYearDetailsReport($request);
        $label = $report['financial_year']['label'] ?? 'financial_year';
        $fileName = 'financial_year_details_'.$label.'_'.date('Y_m_d_H_i_s').'.pdf';

        $pdf = Pdf::loadView('accounts.financial-year.details-pdf-view', $report);

        return $pdf->download($fileName);
    }

    public function detailsExportToExcel(Request $request)
    {
        $report = $this->buildYearDetailsReport($request);
        $label = $report['financial_year']['label'] ?? 'financial_year';
        $fileName = 'financial_year_details_'.$label.'_'.date('Y_m_d_H_i_s').'.xlsx';

        return Excel::download(new FinancialYearDetailsExport($report), $fileName);
    }

    private function buildYearDetailsReport(Request $request): array
    {
        $request->validate([
            'financial_year_id' => 'required|exists:financial_years,id',
        ]);

        $year = FinancialYear::findOrFail($request->financial_year_id);

        return $this->financialYearService->getYearDetails($year);
    }
}
