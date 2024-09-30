<?php

namespace App\Http\Controllers;

use App\Models\MainOrder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function ExportLaporanPenjualanHarian()
    {
        // Buat Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Tanggal hari ini
        $today = Carbon::now()->format('d M Y');

        // Tambahkan header dengan tanggal hari ini
        $sheet->setCellValue('A1', 'Laporan Penjualan Harian - ' . $today);
        $sheet->mergeCells('A1:J1'); // Gabungkan sel untuk header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header kolom di Excel (mulai dari baris ke-2 karena header laporan di baris pertama)
        $sheet->setCellValue('A2', 'No');
        $sheet->setCellValue('B2', 'ID');
        $sheet->setCellValue('C2', 'Cashier');
        $sheet->setCellValue('D2', 'Customer');
        $sheet->setCellValue('E2', 'Grand Total');
        $sheet->setCellValue('F2', 'Payment Method');
        $sheet->setCellValue('G2', 'Cash');
        $sheet->setCellValue('H2', 'Changes');
        $sheet->setCellValue('I2', 'Status');
        $sheet->setCellValue('J2', 'Order Date');

        // Ambil data penjualan harian
        $orders = MainOrder::whereDate('created_at', today())->get();

        // Isi data ke dalam Excel
        $row = 3; // Mulai dari baris ketiga setelah header
        $no = 1;  // Nomor urut
        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $order->id);
            $sheet->setCellValue('C' . $row, $order->cashier);
            $sheet->setCellValue('D' . $row, $order->customer);
            $sheet->setCellValue('E' . $row, 'Rp ' . number_format($order->grandtotal, 0, ',', '.'));
            $sheet->setCellValue('F' . $row, ucfirst($order->payment));
            $sheet->setCellValue('G' . $row, 'Rp ' . number_format($order->cash, 0, ',', '.'));
            $sheet->setCellValue('H' . $row, 'Rp ' . number_format($order->changes, 0, ',', '.'));
            $sheet->setCellValue('I' . $row, ucfirst($order->status));
            $sheet->setCellValue('J' . $row, $order->created_at->format('d M Y'));

            // Styling untuk setiap baris data
            $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
            $no++; // Increment nomor urut
        }

        // Auto-size kolom
        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Simpan Excel dengan nama file yang mengandung tanggal hari ini
        $fileName = 'Laporan_Penjualan_Harian_' . Carbon::now()->format('d_m_Y') . '.xlsx';
        $filePath = storage_path($fileName);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Kirim file ke browser untuk diunduh
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    public function ExportLaporanPenjualan(Request $request)
    {
        // Buat Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Tanggal hari ini
        $today = Carbon::now()->format('d M Y');

        // Judul laporan
        $title = 'Laporan Penjualan - ' . $today;

        // Tambahkan header dengan tanggal hari ini
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:J1'); // Gabungkan sel untuk header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header kolom di Excel
        $sheet->setCellValue('A2', 'No');
        $sheet->setCellValue('B2', 'ID');
        $sheet->setCellValue('C2', 'Cashier');
        $sheet->setCellValue('D2', 'Customer');
        $sheet->setCellValue('E2', 'Grand Total');
        $sheet->setCellValue('F2', 'Payment Method');
        $sheet->setCellValue('G2', 'Cash');
        $sheet->setCellValue('H2', 'Changes');
        $sheet->setCellValue('I2', 'Status');
        $sheet->setCellValue('J2', 'Order Date');

        // Tambahkan filter berdasarkan permintaan
        $filterRow = 2; // Baris untuk menampilkan filter di kolom L dan M
        if ($request->filled('payment_method')) {
            $sheet->setCellValue('L' . $filterRow, 'Filter Metode Pembayaran:');
            $sheet->setCellValue('M' . $filterRow, ucfirst($request->payment_method));
            $filterRow++;
        }

        if ($request->filled('cashier')) {
            $sheet->setCellValue('L' . $filterRow, 'Filter Cashier:');
            $sheet->setCellValue('M' . $filterRow, $request->cashier);
            $filterRow++;
        }

        if ($request->filled('date')) {
            $sheet->setCellValue('L' . $filterRow, 'Filter Tanggal:');
            $sheet->setCellValue('M' . $filterRow, $request->date);
            $filterRow++;
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $sheet->setCellValue('L' . $filterRow, 'Filter Rentang Tanggal:');
            $sheet->setCellValue('M' . $filterRow, $request->start_date . ' s/d ' . $request->end_date);
            $filterRow++;
        }

        // Mulai query untuk MainOrder, dengan filter
        $mainOrdersQuery = MainOrder::query();
        if ($request->has('payment_method')) {
            $mainOrdersQuery->where('payment', $request->payment_method);
        }
        if ($request->has('cashier')) {
            $mainOrdersQuery->where('cashier', $request->cashier);
        }
        if ($request->has('date')) {
            $mainOrdersQuery->whereDate('created_at', $request->date);
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $mainOrdersQuery->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Ambil data penjualan sesuai filter
        $orders = $mainOrdersQuery->get();

        // Inisialisasi variabel untuk menghitung total
        $totalGrandTotal = 0;
        $totalKeuntungan = 0;
        $itemPenjualan = [];

        // Isi data ke dalam Excel
        $row = 3; // Mulai dari baris ketiga setelah header
        $no = 1;  // Nomor urut
        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $order->id);
            $sheet->setCellValue('C' . $row, $order->cashier);
            $sheet->setCellValue('D' . $row, $order->customer);
            $sheet->setCellValue('E' . $row, 'Rp ' . number_format($order->grandtotal, 0, ',', '.'));
            $sheet->setCellValue('F' . $row, ucfirst($order->payment));
            $sheet->setCellValue('G' . $row, 'Rp ' . number_format($order->cash, 0, ',', '.'));
            $sheet->setCellValue('H' . $row, 'Rp ' . number_format($order->changes, 0, ',', '.'));
            $sheet->setCellValue('I' . $row, ucfirst($order->status));
            $sheet->setCellValue('J' . $row, $order->created_at->format('d M Y'));

            // Tambahkan total grandtotal
            $totalGrandTotal += $order->grandtotal;

            // // Tambahkan perhitungan keuntungan (asumsi ada kolom profit di MainOrder)
            // $totalKeuntungan += $order->profit;


            // Styling untuk setiap baris data
            $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
            $no++; // Increment nomor urut
        }

        // Auto-size kolom
        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);

        // // Tambahkan keterangan total keuntungan dan total penjualan setelah data
        // $sheet->setCellValue('A' . $row, 'Total Keuntungan');
        // $sheet->mergeCells('A' . $row . ':D' . $row);
        // $sheet->setCellValue('E' . $row, 'Rp ' . number_format($totalKeuntungan, 0, ',', '.'));

        // Total Grand Total langsung di L3 dan M3
        $sheet->setCellValue('L3', 'Total Penjualan: ');
        $sheet->mergeCells('L3:M3'); // Gabungkan sel untuk label
        $sheet->setCellValue('O3', 'Rp ' . number_format($totalGrandTotal, 0, ',', '.'));


        $row += 2;
        $sheet->setCellValue('A' . $row, 'Detail Penjualan Setiap Item');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;

        // Simpan Excel dengan nama file yang mengandung tanggal hari ini
        $fileName = 'Laporan_Penjualans_' . Carbon::now()->format('d_m_Y') . '.xlsx';
        $filePath = storage_path($fileName);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Kirim file ke browser untuk diunduh
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
}
