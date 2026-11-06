<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Service\SalesReportService;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
   private SalesReportService $reports;

    public function __construct(SalesReportService $reports){
        $this->reports = $reports;
    }
    public function index(){
        $allReports = $this->reports->getAllSalesReport();
        return view('Sales.Report.sales-report-index',['allReports' => $allReports]);
    }

    public function filterReport(Request $request){
        $salesReport = $this->reports->getSalesReportByDate($request);
        return view('Sales.Report.sales-report-index', ['allReports' => $salesReport]);
    }

    public function genaratePdfReport(Request $request){
        $pdf = $this->reports->generatePDF($request);
        return $pdf;
    }

    public function genarateExcelReport(Request $request){
        $excel = $this->reports->generateExcel($request);
        return $excel;
    }


}
