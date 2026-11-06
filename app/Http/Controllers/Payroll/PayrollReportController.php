<?php

namespace App\Http\Controllers\Payroll;

use App\Exports\AttendanceReportExport;
use App\Exports\CommissionReportExport;
use App\Exports\SalaryReportExport;
use App\Exports\PayrollReportExport;
use App\Exports\IncentiveReportExport;
use App\Http\Controllers\Controller;
use App\Service\PayrollReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PayrollReportController extends Controller
{
    private PayrollReportService $payrollReportService;

    public function __construct(PayrollReportService $PayrollReportService)
    {
        $this->payrollReportService = $PayrollReportService;
    }

    public function salary_index()
    {
        $employees_dropdown = \Facades\App\Models\Employee::employeesDropdown();

        return view('payroll.report.salary_report', compact('employees_dropdown'));
    }
    public function commission_index()
    {
        $employees_dropdown = \Facades\App\Models\Employee::employeesDropdown();

        return view('payroll.report.commission_report', compact('employees_dropdown'));
    }

    public function attendance_index()
    {
        $employees_dropdown = \Facades\App\Models\Employee::employeesDropdown();

        return view('payroll.report.attendance_report', compact('employees_dropdown'));
    }
    public function payroll_index()
    {
        $employees_dropdown = \Facades\App\Models\Employee::employeesDropdown();

        return view('payroll.report.payroll_report', compact('employees_dropdown'));
    }
    public function incentive_index()
    {
        $employees_dropdown = \Facades\App\Models\Employee::employeesDropdown();

        return view('payroll.report.incentive_report', compact('employees_dropdown'));
    }



    public function salary_vue_list(Request $request)
    {
        return $this->payrollReportService->salaryReportList($request);
    }

    public function commission_vue_list(Request $request)
    {
        return $this->payrollReportService->commissionReportList($request);
    }

    public function attendance_vue_list(Request $request)
    {
        return $this->payrollReportService->attendanceReportList($request);
    }

    public function payroll_vue_list(Request $request)
    {
        return $this->payrollReportService->payrollReportList($request);
    }

    public function incentive_vue_list(Request $request)
    {
        return $this->payrollReportService->incentiveReportList($request);
    }


    public function SalaryReportExportToExcel(Request $request)
    {
        $file_name = 'Salary_report_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new SalaryReportExport(
            $request->employee_name,
            $request->start_date,
            $request->end_date,
            $request->emp_id,
            $request->balance_amount,
        ), $file_name);
    }
    public function CommissionReportExportToExcel(Request $request)
    {
        $file_name = 'Commission_report_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new CommissionReportExport(
            $request->employee_name,
            $request->start_date,
            $request->end_date,
            $request->emp_id,
        ), $file_name);
    }
    public function AttendanceReportExportToExcel(Request $request)
    {
        $file_name = 'Attendance_report_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new AttendanceReportExport(
            $request->employee_name,
            $request->start_date,
            $request->end_date,
            $request->attendanceType,
            $request->emp_id,
        ), $file_name);
    }
    public function PayrollReportExportToExcel(Request $request)
    {
        $file_name = 'Payroll_report_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new PayrollReportExport(
            $request->employee_name,
            $request->start_date,
            $request->end_date,
            $request->payment_mode,
            $request->emp_id,
        ), $file_name);
    }
    public function IncentiveReportExportToExcel(Request $request)
    {
        $file_name = 'Incentive_report_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new IncentiveReportExport(
            $request->employee_name,
            $request->start_date,
            $request->end_date,
            $request->emp_id,
        ), $file_name);
    }






    public function salary_report_exportToPDF(Request $request)
    {
        // dd($request->all());
        $salary_list = $this->payrollReportService->salaryexportToPDF($request);

        $result = [
            'records' => $salary_list['records'],
            'employee_name' => $request->employee_name,


        ];

        $file_name = 'salary_report_' . date('Y_m_d_H_i_s') . '.pdf';

        $pdf = Pdf::loadView('payroll.report.salary_report-pdf-view', $result);
        return $pdf->download($file_name);
    }

    public function salary_report_print(Request $request)
    {
        $salary_list = $this->payrollReportService->salaryexportToPDF($request);
        $records = $salary_list['records'];

        $employeeLabel = __('All');
        if (! empty($request->emp_id)) {
            $employee = \App\Models\Employee::find($request->emp_id);
            $employeeLabel = $employee?->name ?? $employeeLabel;
        } elseif (! empty($request->employee_name)) {
            $employeeLabel = $request->employee_name;
        }

        $salaryFilterLabel = __('All Employees');
        if (! empty($request->balance_amount) && (string) $request->balance_amount === '1') {
            $salaryFilterLabel = __('Pending Salary');
        }

        $totalNetSalary = 0;
        $totalAdvance = 0;
        $totalPaid = 0;
        $totalPending = 0;

        foreach ($records as $record) {
            $totalNetSalary += (float) ($record->payroll->net_salary ?? 0);
            $totalAdvance += (float) ($record->payroll->advance ?? 0);
            $totalPaid += (float) ($record->payment_amount ?? 0);
            $totalPending += (float) ($record->balance_amount ?? 0);
        }

        return view('payroll.report.salary_report-print', [
            'records' => $records,
            'employee_label' => $employeeLabel,
            'salary_filter_label' => $salaryFilterLabel,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_net_salary' => $totalNetSalary,
            'total_advance' => $totalAdvance,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
        ]);
    }

    public function commission_report_exportToPDF(Request $request)
    {
        $commission_list = $this->payrollReportService->commissionexportToPDF($request);

        $result = [
            'records' => $commission_list['records'],
            'employee_name' => $request->employee_name,
        ];

        $file_name = 'commission_report_' . date('Y_m_d_H_i_s') . '.pdf';

        $pdf = PDF::loadView('payroll.report.commission_report-pdf-view', $result);
        return $pdf->download($file_name);
    }

    public function commission_report_print(Request $request)
    {
        $commission_list = $this->payrollReportService->commissionexportToPDF($request);
        $records = $commission_list['records'];

        $employeeLabel = __('All');
        if (! empty($request->emp_id)) {
            $employee = \App\Models\Employee::find($request->emp_id);
            $employeeLabel = $employee?->name ?? $employeeLabel;
        } elseif (! empty($request->employee_name)) {
            $employeeLabel = $request->employee_name;
        }

        $totalCommission = $records->sum(function ($record) {
            return (float) $record->commission_amount;
        });

        return view('payroll.report.commission_report-print', [
            'records' => $records,
            'employee_label' => $employeeLabel,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_commission' => $totalCommission,
        ]);
    }

    public function attendance_report_exportToPDF(Request $request)
    {
        $attendance_list = $this->payrollReportService->attendanceexportToPDF($request);

        $result = [
            'records' => $attendance_list['records'],
            'employee_name' => $request->employee_name,
        ];

        $file_name = 'attendance_report_' . date('Y_m_d_H_i_s') . '.pdf';

        $pdf = Pdf::loadView('payroll.report.attendance_report-pdf-view', $result);
        return $pdf->download($file_name);
    }

    public function attendance_report_print(Request $request)
    {
        $records = $this->payrollReportService->attendanceReportFormattedRows($request);

        $employeeLabel = __('All');
        if (! empty($request->emp_id)) {
            $employee = \App\Models\Employee::find($request->emp_id);
            $employeeLabel = $employee?->name ?? $employeeLabel;
        } elseif (! empty($request->employee_name)) {
            $employeeLabel = $request->employee_name;
        }

        $attendanceTypeLabels = [
            'present' => __('Present'),
            'absent' => __('Absent / Leave'),
            'late' => __('Late'),
            'over_time' => __('Over Time'),
        ];
        $attendanceTypeLabel = $attendanceTypeLabels[$request->attendanceType ?? ''] ?? __('All');

        return view('payroll.report.attendance_report-print', [
            'records' => $records,
            'employee_label' => $employeeLabel,
            'attendance_type_label' => $attendanceTypeLabel,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
    }

    public function payroll_report_exportToPDF(Request $request)
    {
        $payroll_list = $this->payrollReportService->payrollexportToPDF($request);

        $result = [
            'records' => $payroll_list['records'],
            'employee_name' => $request->employee_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,


        ];

        $file_name = 'payroll_report_' . date('Y_m_d_H_i_s') . '.pdf';

        $pdf = Pdf::loadView('payroll.report.payroll_report-pdf-view', $result);
        return $pdf->download($file_name);
    }

    public function payroll_report_print(Request $request)
    {
        $payroll_list = $this->payrollReportService->payrollexportToPDF($request);
        $records = $payroll_list['records'];

        $employeeLabel = __('All');
        if (! empty($request->emp_id)) {
            $employee = \App\Models\Employee::find($request->emp_id);
            $employeeLabel = $employee?->name ?? $employeeLabel;
        } elseif (! empty($request->employee_name)) {
            $employeeLabel = $request->employee_name;
        }

        $totals = [
            'salary' => 0,
            'advance' => 0,
            'leave' => 0,
            'deduction' => 0,
            'payable' => 0,
        ];

        foreach ($records as $record) {
            $totals['salary'] += (float) ($record->basic_salary ?? 0);
            $totals['advance'] += (float) ($record->advance ?? 0);
            $totals['leave'] += (float) ($record->approve_leave ?? 0);
            $totals['deduction'] += (float) ($record->deduction ?? 0);
            $totals['payable'] += (float) ($record->payable_amount ?? 0);
        }

        return view('payroll.report.payroll_report-print', [
            'records' => $records,
            'employee_label' => $employeeLabel,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'totals' => $totals,
        ]);
    }

    public function incentive_report_exportToPDF(Request $request)
    {
        $incentive_list = $this->payrollReportService->incentiveexportToPDF($request);

        $result = [
            'records' => $incentive_list['records'],
            'employee_name' => $request->employee_name,
        ];

        $file_name = 'incentive_report_' . date('Y_m_d_H_i_s') . '.pdf';

        $pdf = Pdf::loadView('payroll.report.incentive_report-pdf-view', $result);
        return $pdf->download($file_name);
    }

    public function incentive_report_print(Request $request)
    {
        $incentive_list = $this->payrollReportService->incentiveexportToPDF($request);
        $records = $incentive_list['records'];

        $employeeLabel = __('All');
        if (! empty($request->emp_id)) {
            $employee = \App\Models\Employee::find($request->emp_id);
            $employeeLabel = $employee?->name ?? $employeeLabel;
        } elseif (! empty($request->employee_name)) {
            $employeeLabel = $request->employee_name;
        }

        $totalTarget = 0;
        $totalAmount = 0;
        $totalIncentive = 0;

        foreach ($records as $record) {
            $totalTarget += (float) ($record->target_amount ?? 0);
            $totalAmount += (float) ($record->total_amount ?? 0);
            $totalIncentive += (float) ($record->incentive_amount ?? 0);
        }

        return view('payroll.report.incentive_report-print', [
            'records' => $records,
            'employee_label' => $employeeLabel,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_target' => $totalTarget,
            'total_amount' => $totalAmount,
            'total_incentive' => $totalIncentive,
        ]);
    }

    public function working_hours_index()
    {
        $beauticians_dropdown = \Facades\App\Models\Beautician::beauticiansDropdown();
        return view('payroll.report.working_hours', compact('beauticians_dropdown'));
    }
    public function working_hours_vue_list(Request $request)
    {
        return $this->payrollReportService->workinHoursReportList($request);
    }
    public function working_hours_summary_vue_list(Request $request)
    {
        return $this->payrollReportService->workingHoursSummaryReportList($request);
    }

    public function working_hours_report_print(Request $request)
    {
        $tab = $request->get('tab', 'daily');

        $employeeLabel = __('All');
        if (! empty($request->beautician_id)) {
            $user = \App\Models\User::find($request->beautician_id);
            $employeeLabel = $user?->name ?? $employeeLabel;
        }

        if ($tab === 'summary') {
            $report = $this->payrollReportService->buildWorkingHoursSummaryReport($request);

            return view('payroll.report.working_hours-print', [
                'tab' => 'summary',
                'records' => $report['data'],
                'totals' => $report['totals'],
                'employee_label' => $employeeLabel,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
        }

        $records = $this->payrollReportService->workingHoursDailyRows($request);

        return view('payroll.report.working_hours-print', [
            'tab' => 'daily',
            'records' => $records,
            'employee_label' => $employeeLabel,
            'filter_date' => $request->start_date,
        ]);
    }
}
