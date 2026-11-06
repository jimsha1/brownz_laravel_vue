<?php

namespace App\Service;

use App\Exports\SalesReportExport;
use App\Models\Beautician;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class SalesReportService
{

    public function getAllSalesReport()
    {
        $bookings = Booking::get();
        return $bookings;
    }

    public function getSalesReportByDate($data = null)
    {

        $records = Booking::with('beauticians.user');

        $startDate = $data->start_date;
        $endDate = $data->end_date;

        if ($startDate && $endDate) {
            $records->whereBetween('created_at', ["{$startDate} 00:00:00", "{$endDate} 23:59:59"]);
        } elseif ($startDate) {
            $records->where('created_at', '>=', "{$startDate} 00:00:00");
        } elseif ($endDate) {
            $records->where('created_at', '<=', "{$endDate} 23:59:59");
        }

        if (!empty($data->beautician_name)) {
            $records->whereHas('beauticians.user', function ($query) use ($data) {
                    $query->where('name', $data->beautician_name);
            });
        }

        return $records->orderBy('created_at', $data->sort_order ?? 'DESC')->get();
    }

    public function generatePDF()
    {
        $data = Booking::all(); // Your data
        $pdf = PDF::loadView('Sales.pdf.report', compact('data'));
        return $pdf->download('report.pdf');
    }
    public function generateExcel()
    {
        return Excel::download(new SalesReportExport, 'report.xlsx');
    }
}
