<?php

namespace App\Http\Controllers;

use App\Exports\BeauticianReportExport;
use App\Exports\DailySalesReportExport;
use App\Exports\ClientReportExport;
use App\Exports\EditLogReportExport;
use App\Exports\VendorReportExport;
use App\Exports\SalesReportExport;
use App\Exports\ServiceReportExport;
use App\Exports\ServiceUsedProductReportExport;
use App\Exports\TaxReportExport;
use App\Models\Consultation;
use App\Models\HairAnalysis;
use App\Models\HairClientDesires;
use App\Models\HairHistory;
use App\Models\SkinAnalysis;
use App\Models\SkinLifestyle;
use App\Models\SkinHistory;
use App\Models\SkinConclusion;
use App\Models\User;
use App\Service\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Facades\App\Models\Beautician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private ReportService $reportService;

    public function __construct(ReportService $ReportService)
    {
        $this->reportService = $ReportService;
        $this->middleware('auth')->only([
            'consultation_index',
            'consultation_vue_list_api',
            'consultation_report_exportToPDF',
            'skin_consultation_print',
            'print1',
            'print2',
            'approveHairConsultation',
            'approveSkinConsultation',
        ]);
    }

    public function sales_index()
    {
        return view('report.sales_report');
    }

    public function daily_sales_index()
    {
        return view('report.daily_sales_report');
    }

    public function daily_sales_vue_list_api(Request $request)
    {
        return $this->reportService->dailySalesReport($request);
    }

    public function dailySalesReportExportToExcel(Request $request)
    {
        $file_name = 'daily_sales_report_' . date('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new DailySalesReportExport(
            $request->start_date,
            $request->end_date
        ), $file_name);
    }

    public function client_index()
    {
        return view('report.client_report');
    }

    public function vendor_index()
    {
        return view('report.vendor_report');
    }

    public function edit_log_index()
    {
        $editors = $this->reportService->editLogEditors();

        return view('report.edit_log_report', compact('editors'));
    }

    public function tax_index()
    {
        return view('report.tax_report');
    }

    public function service_index()
    {
        $beauticians_dropdown = Beautician::beauticiansDropdown();

        return view('report.service_report', compact('beauticians_dropdown'));
    }

    public function used_product_index()
    {
        $beauticians_dropdown = Beautician::beauticiansDropdown();
        $start_date = now()->startOfMonth()->toDateString();
        $end_date = now()->endOfMonth()->toDateString();

        return view('report.service_used_product_report', compact('beauticians_dropdown', 'start_date', 'end_date'));
    }

    public function beautician_index()
    {
        $beauticians_dropdown = Beautician::beauticiansDropdown();

        return view('report.beautician_report', compact('beauticians_dropdown'));
    }

    public function whatsapp_invoice_index()
    {
        return view('report.whatsapp_invoice');
    }

    public function sales_vue_list_api_admin_app(Request $request)
    {
        return $this->reportService->salesReportListAdminApi($request);
    }

    public function sales_vue_list_api(Request $request)
    {
        // return $request;
        return $this->reportService->salesReportListApi($request);
    }

    public function client_vue_list_api(Request $request)
    {
        return $this->reportService->clientReportListApi($request);
    }

    public function client_update(Request $request)
    {
        return $this->reportService->updateClientDetails($request);
    }

    public function client_vue_list_api_for_wiring(Request $request)
    {
        return $this->reportService->clientReportListApi($request, 'wiring');
    }

    public function vendor_vue_list_api(Request $request)
    {
        return $this->reportService->vendorReportListApi($request);
    }

    public function edit_log_vue_list_api(Request $request)
    {
        return $this->reportService->editLogReportListApi($request);
    }

    public function tax_vue_list_api(Request $request)
    {
        return $this->reportService->taxReportListApi($request);
    }

    public function service_vue_list_api(Request $request)
    {
        return $this->reportService->serviceReportListApi($request);
    }

    public function used_product_vue_list_api(Request $request)
    {
        return $this->reportService->usedProductReportListApi($request);
    }

    public function beautician_vue_list_api(Request $request)
    {
        return $this->reportService->beauticianReportListApi($request);
    }

    public function whatsapp_invoice_vue_list_api(Request $request)
    {
        return $this->reportService->whatsappReportListApi($request);
    }

    public function sales_report_exportToPDF(Request $request)
    {

        $sales_list = $this->reportService->salesexportToPDF($request);

        $sum_of_amount = $request->payment_mode
            ? $sales_list->sum(function ($record) use ($request) {
                $sign = $this->reportService->salesReportAmountSign($record, $request);
                $taken = abs((float) $record->bookingPayment
                    ->where('payment_mode', $request->payment_mode)
                    ->sum(function ($payment) {
                        return (float) $payment->amount_taken - (float) ($payment->amount_given ?? 0);
                    }));

                return $taken * $sign;
            })
            : $sales_list->sum(function ($record) {
                return $record->signed_payment_total
                    ?? ((float) ($record->payment_total ?? 0) * $this->reportService->salesReportAmountSign($record, request()));
            });

        $result = [
            'records' => $sales_list,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'client_name' => $request->client_name,
            'booking_code' => $request->booking_code,
            'payment_mode' => $request->payment_mode,
            'type' => $request->type ?: 'All',
            'line_type' => $request->line_type ?: '',
            'sum_of_amount' => $sum_of_amount,
        ];

        $file_name = 'sales_report_' . date('Y_m_d_H_i_s') . '.pdf';

        $pdf = Pdf::loadView('report.sales_report-pdf-view', $result);

        return $pdf->download($file_name);
    }

    public function client_report_exportToPDF(Request $request)
    {
        $client_list = $this->reportService->clientsexportToPDF($request);

        $result = [
            'records' => $client_list['records'],
            'client_name' => $request->client_name,
            'mobile' => $request->mobile,
            'status' => $request->status,
        ];

        $file_name = 'client_report_' . date('Y_m_d_H_i_s') . '.pdf';

        $pdf = Pdf::loadView('report.client_report-pdf-view', $result);

        return $pdf->download($file_name);
    }

    public function vendor_report_exportToPDF(Request $request)
    {
        $vendor_list = $this->reportService->vendorsexportToPDF($request);

        $result = [
            'records' => $vendor_list['records'],
            'totals' => $vendor_list['totals'] ?? ['total_purchase_amount' => 0, 'total_credit_amount' => 0],
            'vendor_name' => $request->vendor_name ?? '',
            'vendor_email' => $request->vendor_email ?? '',
            'vendor_phone' => $request->vendor_phone ?? '',
        ];

        $file_name = 'vendor_report_' . date('Y_m_d_H_i_s') . '.pdf';

        $pdf = PDF::loadView('report.vendor_report-pdf-view', $result);
        return $pdf->download($file_name);
    }

    public function tax_report_exportToPDF(Request $request)
    {
        $tax_list = $this->reportService->taxexportToPDF($request);

        $result = [
            'tax_type' => $tax_list['tax_type'] ?? 'output',
            'records' => $tax_list['records'] ?? collect(),
            'totals' => $tax_list['totals'] ?? [],
            'summary' => $tax_list['summary'] ?? [
                'output_tax' => 0,
                'input_tax' => 0,
                'net_tax' => 0,
            ],
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'booking_code' => $request->booking_code,
            'purchase_ref' => $request->purchase_ref,
            'vendor_name' => $request->vendor_name,
            'type' => $request->type ?: 'All',
        ];

        $file_name = 'tax_report_' . date('Y_m_d_H_i_s') . '.pdf';

        $pdf = Pdf::loadView('report.tax_report-pdf-view', $result);

        return $pdf->download($file_name);
    }

    public function service_report_exportToPDF(Request $request)
    {

        $service_list = $this->reportService->serviceexportToPDF($request);

        $beauticianName = '';
        if ($request->beautician_id) {
            $beautician = User::find($request->beautician_id);
            if ($beautician) {
                $beauticianName = $beautician->name;
            }
        }

        // $sum_of_amount = $service_list->sum(function ($record) {
        //     return ($record->service_booking_price ?? 0)
        //         + ($record->tax_amount ?? 0)
        //         - ($record->individual_discount_amt ?? 0);
        // });

        $sum_of_amount = $service_list->sum(function ($record) {
            return $record->line_total ?? 0;
        });

        $result = [
            'records' => $service_list,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'beautician_id' => $beauticianName,
            'service_name' => $request->service_name,
            'booking_code' => $request->booking_code,
            'sum_of_amount' => number_format($sum_of_amount, 2),
        ];

        $file_name = 'service_report_' . date('Y_m_d_H_i_s') . '.pdf';

        $pdf = Pdf::loadView('report.service_report-pdf-view', $result);

        return $pdf->download($file_name);
    }

    public function beautician_report_exportToPDF(Request $request)
    {

        $beautician_list = $this->reportService->beauticianexportToPDF($request);

        $beauticianName = '';
        if ($request->beautician_id) {
            $beautician = User::find($request->beautician_id);
            if ($beautician) {
                $beauticianName = $beautician->name;
            }
        }

        $sum_of_amount = $beautician_list->sum(function ($record) {
            return (float) ($record->price ?? 0);
        });

        $sum_of_commission = $beautician_list->sum(function ($record) {
            return (float) ($record->commission_amount ?? 0);
        });

        $result = [
            'records' => $beautician_list,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'booking_code' => $request->booking_code,
            'beautician_id' => $beauticianName,
            'line_type' => $request->line_type,
            'sum_of_amount' => number_format($sum_of_amount, 2, '.', ''),
            'sum_of_commission' => number_format($sum_of_commission, 2, '.', ''),
        ];

        $file_name = 'beautician_report_' . date('Y_m_d_H_i_s') . '.pdf';

        $pdf = Pdf::loadView('report.beautician_report-pdf-view', $result);

        return $pdf->download($file_name);
    }

    public function SalesReportExportToExcel(Request $request)
    {
        $file_name = 'sales_report_' . date('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new SalesReportExport(
            $request->start_date,
            $request->end_date,
            $request->client_name,
            $request->booking_code,
            $request->payment_mode,
            $request->type,
            $request->line_type,
        ), $file_name);
    }

    public function TaxReportExportToExcel(Request $request)
    {
        $file_name = 'tax_report_' . date('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new TaxReportExport(
            $request->start_date,
            $request->end_date,
            $request->booking_code,
            $request->tax_type ?? 'output',
            $request->purchase_ref,
            $request->vendor_name,
            $request->type,
        ), $file_name);
    }

    public function ClientReportExportToExcel(Request $request)
    {
        $file_name = 'client_report_' . date('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new ClientReportExport(
            (string) ($request->client_name ?? ''),
            (string) ($request->mobile ?? ''),
            (string) ($request->status ?? 'all'),
        ), $file_name);
    }

    public function VendorReportExportToExcel(Request $request)
    {
        $file_name = 'vendor_report_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new VendorReportExport(
            $request->vendor_name ?? '',
            $request->vendor_email ?? '',
            $request->vendor_phone ?? '',
        ), $file_name);
    }

    public function edit_log_report_exportToPDF(Request $request)
    {
        $editorName = '';
        if ($request->filled('edited_by')) {
            $editorName = (string) (User::query()->where('id', $request->edited_by)->value('name') ?? '');
        }

        $result = [
            'records' => $this->reportService->editLogRows($request),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'source_label' => $this->reportService->editLogSourceLabel($request->source_type ?: 'all'),
            'edited_by_name' => $editorName,
            'search' => $request->search,
        ];

        $file_name = 'edit_log_report_' . date('Y_m_d_H_i_s') . '.pdf';
        $pdf = Pdf::loadView('report.edit_log_report-pdf-view', $result)->setPaper('a4', 'landscape');

        return $pdf->download($file_name);
    }

    public function EditLogReportExportToExcel(Request $request)
    {
        $file_name = 'edit_log_report_' . date('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new EditLogReportExport(
            (string) ($request->start_date ?? ''),
            (string) ($request->end_date ?? ''),
            (string) ($request->source_type ?? 'all'),
            (string) ($request->edited_by ?? ''),
            (string) ($request->search ?? ''),
        ), $file_name);
    }

    public function used_product_report_exportToPDF(Request $request)
    {
        $records = $this->reportService->usedProductReportRows($request);
        $result = [
            'records' => $records,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => ucfirst((string) ($request->status ?: 'used')),
            'search' => $request->search,
            'total_cost' => number_format($records->sum(fn($row) => (float) $row['estimated_cost']), 2, '.', ''),
        ];

        $file_name = 'service_used_product_report_' . date('Y_m_d_H_i_s') . '.pdf';
        $pdf = Pdf::loadView('report.service_used_product_report-pdf-view', $result)->setPaper('a4', 'landscape');

        return $pdf->download($file_name);
    }

    public function UsedProductReportExportToExcel(Request $request)
    {
        $file_name = 'service_used_product_report_' . date('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new ServiceUsedProductReportExport(
            (string) ($request->start_date ?? ''),
            (string) ($request->end_date ?? ''),
            (string) ($request->status ?? 'used'),
            (string) ($request->beautician_id ?? ''),
            (string) ($request->search ?? ''),
        ), $file_name);
    }

    public function ServiceReportExportToExcel(Request $request)
    {
        $file_name = 'service_report_' . date('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new ServiceReportExport(
            $request->start_date,
            $request->end_date,
            $request->beautician_id,
            $request->service_name,
            $request->booking_code,
        ), $file_name);
    }

    public function BeauticianReportExportToExcel(Request $request)
    {
        $file_name = 'beautician_report_' . date('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new BeauticianReportExport(
            $request->start_date,
            $request->end_date,
            $request->booking_code,
            $request->beautician_id,
            $request->line_type,
        ), $file_name);
    }

    public function getBarGraphDataApi(Request $request)
    {
        try {
            return $this->reportService->getBarGraphData($request);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getSaleSummary(Request $request)
    {
        return $this->reportService->salesSummary($request);
    }

    public function sales_report_summary_exportToPDF(Request $request)
    {
        try {
            $summaryData = $this->reportService->salesSummary($request, true);

            $file_name = 'sales_summary_' . date('Y_m_d_H_i_s') . '.pdf';

            $pdf = Pdf::loadView('report.sales_summary_pdf', $summaryData);

            return $pdf->download($file_name);
        } catch (Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getServiceSummary(Request $request)
    {
        return $this->reportService->serviceSummary($request);
    }

    public function service_report_summary_exportToPDF(Request $request)
    {
        try {
            $summaryData = $this->reportService->serviceSummary($request, true);

            $beauticianName = '';
            if ($request->beautician_id) {
                $beautician = User::find($request->beautician_id);
                if ($beautician) {
                    $beauticianName = $beautician->name;
                }
            }

            $file_name = 'service_summary_' . date('Y_m_d_H_i_s') . '.pdf';

            $pdf = Pdf::loadView('report.service_summary_pdf', [
                'data' => $summaryData,
                'start_date' => $request->start_date ?? '',
                'end_date' => $request->end_date ?? '',
                'beautician_name' => $beauticianName ?? '',
                'service_name' => $request->service_name ?? '',
            ]);

            return $pdf->download($file_name);
        } catch (Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getBeauticianSummary(Request $request)
    {
        return $this->reportService->beauticianSummary($request);
    }

    public function beautician_report_summary_exportToPDF(Request $request)
    {
        try {
            $summaryData = $this->reportService->beauticianSummary($request, true);
            $beauticianName = '';
            if ($request->beautician_id) {
                $beautician = User::find($request->beautician_id);
                if ($beautician) {
                    $beauticianName = $beautician->name;
                }
            }

            $file_name = 'beautician_summary_' . date('Y_m_d_H_i_s') . '.pdf';
            $pdf = Pdf::loadView('report.beautician_summary_pdf', [
                'data' => $summaryData,
                'start_date' => $request->start_date ?? '',
                'end_date' => $request->end_date ?? '',
                'beautician_id' => $beauticianName ?? '',
            ]);

            return $pdf->download($file_name);
        } catch (Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function consultation_index()
    {
        return view('report.consultation_report');
    }

    public function consultation_vue_list_api(Request $request)
    {
        return $this->reportService->consultationReportList($request);
    }

    public function consultation_report_exportToPDF(Request $request)
    {
        try {
            $records = $this->reportService->consultationReportExport($request);

            $file_name = 'consultation_report_' . date('Y_m_d_H_i_s') . '.pdf';
            $pdf = Pdf::loadView('report.consultation_report-pdf-view', [
                'records' => $records,
                'start_date' => $request->start_date ?? '',
                'end_date' => $request->end_date ?? '',
                'client_name' => $request->client_name ?? '',
                'mobile' => $request->mobile ?? '',
                'type' => $request->type ?? '',
            ]);

            return $pdf->download($file_name);
        } catch (Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function skin_consultation_print(Request $request)
    {
        try {
            $request->merge(['type' => 'skin']);
            $records = $this->reportService->consultationReportExport($request);

            $file_name = 'skin_consultation_' . date('Y_m_d_H_i_s') . '.pdf';
            $pdf = Pdf::loadView('report.consultation_report-pdf-view', [
                'records' => $records,
                'start_date' => $request->start_date ?? '',
                'end_date' => $request->end_date ?? '',
                'client_name' => $request->client_name ?? '',
                'mobile' => $request->mobile ?? '',
                'type' => 'skin',
            ]);

            return $pdf->download($file_name);
        } catch (Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function print1($id)
    {
        $consultation = Consultation::with('skin_analyses', 'skin_lifestyles', 'skin_histories', 'skin_conclusions')->findOrFail($id);

        return view('report.skin_consultation_pdf', compact('consultation'));
    }

    public function print2($id)
    {
        $consultation = Consultation::with('hair_analyses', 'hair_histories', 'hair_client_desires')->findOrFail($id);

        return view('report.hair_consultation_pdf', compact('consultation'));
    }

    public function approveHairConsultation(Request $request, $id)
    {

        DB::beginTransaction();

        try {

            $consultation = Consultation::find($id);

            if (! $consultation) {

                return response()->json([
                    'success' => false,
                    'message' => 'Consultation not found',
                ]);
            }

            // --------------------------------
            // Update consultation table
            // --------------------------------
            $consultation->update([
                'customer_name' => $request->client_name,
                'customer_age' => $request->client_age,
                'customer_phone' => $request->client_phone,
                'customer_mobile' => $request->client_mobile,
                'date' => $request->date,
                'beautician_name' => $request->stylist_name,
                'beautician_designation' => $request->designation,
                'approve_status' => 'approved',

                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // --------------------------------
            // Update Hair Analyses
            // --------------------------------
            if ($request->primary_analysis) {

                foreach ($request->primary_analysis as $title => $values) {

                    HairAnalysis::updateOrCreate(
                        [
                            'consultation_id' => $consultation->id,
                            'hair_analyses_title' => $title,
                        ],
                        [
                            'hair_analyses_values' => json_encode($values),
                        ]
                    );
                }
            }

            // --------------------------------
            // Update Hair History
            // --------------------------------
            if ($request->hair_history) {

                foreach ($request->hair_history as $title => $items) {

                    $values = [];
                    $descriptions = [];

                    foreach ($items as $item) {

                        if (
                            isset($item['checked']) &&
                            ($item['checked'] === true || $item['checked'] === 'true')
                        ) {

                            $values[] = $item['type'] ?? null;

                            $descriptions[] = $item['description'] ?? null;
                        }
                    }

                    HairHistory::updateOrCreate(
                        [
                            'consultation_id' => $consultation->id,
                            'hair_history_title' => $title,
                        ],
                        [
                            'hair_history_values' => json_encode($values),
                            'hair_history_description' => json_encode($descriptions),
                        ]
                    );
                }
            }

            // --------------------------------
            // Update Client Desires
            // --------------------------------
            if ($request->client_desire) {

                foreach ($request->client_desire as $title => $desc) {

                    HairClientDesires::updateOrCreate(
                        [
                            'consultation_id' => $consultation->id,
                            'hair_client_desires_title' => $title,
                        ],
                        [
                            'hair_client_desires_description1' => $desc['description1'] ?? null,
                            'hair_client_desires_description2' => $desc['description2'] ?? null,
                        ]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Consultation approved successfully',
            ]);
        } catch (Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function approveSkinConsultation(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $consultation = Consultation::find($id);

            if (! $consultation) {

                return response()->json([
                    'success' => false,
                    'message' => 'Consultation not found',
                ]);
            }

            // --------------------------------
            // Update consultation table
            // --------------------------------
            $consultation->update([
                'customer_name' => $request->client_name,
                'customer_age' => $request->client_age,
                'customer_phone' => $request->client_phone,
                'customer_mobile' => $request->client_mobile,
                'date' => $request->date,
                'beautician_name' => $request->stylist_name,
                'beautician_designation' => $request->designation,

                'approve_status' => 'approved',

                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // --------------------------------
            // Update Skin Analysis
            // --------------------------------
            if ($request->skin_analysis) {

                foreach ($request->skin_analysis as $title => $values) {

                    SkinAnalysis::updateOrCreate(
                        [
                            'consultation_id' => $consultation->id,
                            'skin_analyses_title' => $title,
                        ],
                        [
                            'skin_analyses_values' => json_encode($values),
                        ]
                    );
                }
            }

            // --------------------------------
            // Update Life Style
            // --------------------------------
            if ($request->life_style) {

                foreach ($request->life_style as $title => $values) {

                    SkinLifestyle::updateOrCreate(
                        [
                            'consultation_id' => $consultation->id,
                            'skin_lifestyle_title' => $title,
                        ],
                        [
                            'skin_lifestyle_values' => json_encode($values),
                        ]
                    );
                }
            }

            // --------------------------------
            // Update Skin History
            // --------------------------------
            if ($request->history) {

                foreach ($request->history as $title => $values) {

                    SkinHistory::updateOrCreate(
                        [
                            'consultation_id' => $consultation->id,
                            'skin_history_title' => $title,
                        ],
                        [
                            'skin_history_values' => json_encode($values),
                        ]
                    );
                }
            }

            // --------------------------------
            // Update Conclusion
            // --------------------------------
            if ($request->conclusion) {

                foreach ($request->conclusion as $title => $item) {

                    SkinConclusion::updateOrCreate(
                        [
                            'consultation_id' => $consultation->id,
                            'skin_conclusion_title' => $title,
                        ],
                        [
                            'skin_conclusion_description' => $item['description'] ?? '',
                        ]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Skin consultation approved successfully',
            ]);
        } catch (Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
