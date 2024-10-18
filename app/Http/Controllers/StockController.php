<?php

namespace App\Http\Controllers;

use App\Models\BelanjaMingguan;
use App\Models\Material;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\WeeklyReceipts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StockController extends Controller
{
    public function StorageView()
    {
        $stock = Stock::select('id', 'id_material', 'qty', 'id_unit', 'price', 'total', 'information', 'created_at')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('stocks')
                    ->groupBy('id_material');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        $unit = Unit::all();
        $material = Material::all();
        return view('Admin.Stock.Storage', compact('stock', 'unit', 'material'));
    }

    public function AddStock(Request $request)
    {
        $qty = $request->qty;
        $price = $request->price;
        $grandtotal = $qty * $price;

        // Simpan data material
        $material = new Stock();
        $material->id_material = $request->id_material;
        $material->qty = $qty;
        $material->id_unit = $request->unit;
        $material->price = $price;
        $material->total = $grandtotal;
        $material->information = $request->information;
        $material->save();

        return response()->json([
            'success' => true,
            'message' => 'Stock add successfully.'
        ]);
    }


    public function UpdateView($id)
    {
        $stock = Stock::where('id', $id)->first();
        $material = Material::all();
        $unit = Unit::all();
        return view('Admin.Stock.UpdateStock', compact('stock', 'material', 'unit'));
    }

    public function UpdateProcess(Request $request, $id)
    {
        // Validasi data jika perlu
        $stock = Stock::where('id',  $id)->first();
        $qty = $request->qty;
        $price = $request->price;
        $total = $qty * $price;
        if ($stock) {
            $stocks = new Stock();
            $stocks->id_material = $request->id_material;
            $stocks->qty = $qty;
            $stocks->id_unit = $request->unit;
            $stocks->price = $price;
            $stocks->total = $total;
            $stocks->information = $request->information;
            $stocks->save();

            return response()->json(['success' => true, 'message' => 'Update Success']);
        }

        return response()->json(['success' => false, 'message' => 'Stock not found'], 404);
    }

    public function FilterMaterial(Request $request)
    {
        // Mulai query untuk Material
        $stockQuery = Stock::query();

        $filtersApplied = false;

        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            // Tambahkan filter untuk mengambil data yang paling baru pada tanggal yang dipilih
            $stockQuery->whereDate('created_at', $request->date)
                ->whereIn('id', function ($query) use ($request) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('stocks')
                        ->whereDate('created_at', $request->date)
                        ->groupBy('id_material');
                });
            $filtersApplied = true;
        }

        // Filter berdasarkan rentang tanggal jika ada
        if ($request->filled('start_date') && $request->filled('end_date')) {
            // Ambil data terbaru di setiap hari dalam rentang tanggal
            $stockQuery->whereBetween('created_at', [$request->start_date, $request->end_date])
                ->whereIn('id', function ($query) use ($request) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('stocks')
                        ->whereBetween('created_at', [$request->start_date, $request->end_date])
                        ->groupBy(DB::raw('DATE(created_at)'));
                });
            $filtersApplied = true;
        }

        // Jika filter diisi, ambil data, jika tidak kosongkan data
        if ($filtersApplied) {
            $stock = $stockQuery->get();
        } else {
            $stock = collect(); // Data kosong jika tidak ada filter
        }

        // Kembalikan view dengan data yang difilter
        $unit = Unit::all();
        $material = Material::all();

        return view('Admin.Stock.Storage', compact('stock', 'unit', 'material'));
    }

    public function CreateUnit(Request $request)
    {
        $unit = new Unit();
        $unit->unit = $request->unit;
        $unit->save();
        return response()->json([
            'success' => true,
            'message' => 'Unit created successfully.'
        ]);
    }
    public function CreateMaterial(Request $request)
    {
        $mataerial = new Material();
        $mataerial->material = $request->material;
        $mataerial->save();
        return response()->json([
            'success' => true,
            'message' => 'Material created successfully.'
        ]);
    }

    public function ExportLaporanStock(Request $request)
    {
        // Buat Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Tanggal hari ini
        $today = Carbon::now()->format('d M Y');

        // Judul laporan
        $title = 'Report Material Stock - ' . $today;

        // Tambahkan header dengan tanggal hari ini
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:E1'); // Gabungkan sel untuk header, sesuaikan kolom hingga E
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header kolom di Excel
        $sheet->setCellValue('A2', 'No');
        $sheet->setCellValue('B2', 'Nama Material');
        $sheet->setCellValue('C2', 'Stock');
        $sheet->setCellValue('D2', 'Unit');
        $sheet->setCellValue('E2', 'Price');
        $sheet->setCellValue('F2', 'Grand Total');
        $sheet->setCellValue('G2', 'Information');
        $sheet->setCellValue('H2', 'Created At');

        // Mulai query untuk materials
        $stockQuery = Stock::query();
        if ($request->has('date')) {
            $stockQuery->whereDate('created_at', $request->date);
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $stockQuery->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Ambil data sesuai filter
        $stocks = $stockQuery->orderBy('created_at', 'asc')->get();

        // Inisialisasi variabel untuk grouping by date
        $currentDate = null;
        $row = 3; // Mulai dari baris ketiga setelah header
        $no = 1;  // Nomor urut

        foreach ($stocks as $stock) {
            // Cek apakah tanggal berubah
            $stockDate = $stock->created_at->format('d M Y');
            if ($currentDate !== $stockDate) {
                // Jika tanggal berubah, tambahkan tanggal sebagai header
                $sheet->setCellValue('A' . $row, 'Date: ' . $stockDate);
                $sheet->mergeCells('A' . $row . ':H' . $row); // Gabungkan sel hingga E
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++; // Increment baris setelah header tanggal
                $currentDate = $stockDate;
            }

            // Isi data material
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row,  $stock->material->material);
            $sheet->setCellValue('C' . $row, $stock->qty);
            $sheet->setCellValue('D' . $row, $stock->unit->unit);
            $sheet->setCellValue('E' . $row, $stock->price);
            $sheet->setCellValue('F' . $row, $stock->total);
            $sheet->setCellValue('G' . $row, $stock->information ?? '-');
            $sheet->setCellValue('H' . $row, $stock->created_at->format('d M Y H:i'));

            // Styling untuk setiap baris data
            $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $row . ':H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('Rp #,##0');
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('Rp #,##0');

            $row++;
            $no++; // Increment nomor urut
        }

        // Auto-size kolom
        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Simpan Excel dengan nama file yang mengandung tanggal hari ini
        $fileName = 'Laporan_Stock_Material_' . Carbon::now()->format('d_m_Y') . '.xlsx';
        $filePath = storage_path('app/' . $fileName);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Kirim file ke browser untuk diunduh
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    public function WeeklyReceiptsView()
    {
        $material = Material::all();
        $unit = Unit::all();
        $pending = WeeklyReceipts::where('type', 'stock')->where('status', 'pending')->get();
        return view('Admin.Stock.WeeklyReceipts', compact('material', 'unit', 'pending'));
    }

    public function InReceipts(Request $request)
    {
        // Menambahkan validasi untuk input
        $request->validate([
            'id_material' => 'required|exists:materials,id', // Pastikan id_material ada di tabel materials
            'qty' => 'required|integer|min:1', // Harus berupa integer dan lebih dari 0
            'id_unit' => 'required|exists:units,id', // Pastikan id_unit ada di tabel units
            'price' => 'required|numeric|min:0', // Harus berupa angka dan tidak negatif
            'information' => 'nullable|string', // Informasi bersifat opsional
        ]);

        $qty = $request->qty;
        $price = $request->price;
        $total = $qty * $price;

        $item = new WeeklyReceipts();
        $item->admin = Auth::user()->name;
        $item->type = 'stock';
        $item->id_material = $request->id_material; // Menggunakan id_material yang valid
        $item->qty = $qty;
        $item->id_unit = $request->id_unit; // Menggunakan id_unit yang valid
        $item->price = $price;
        $item->total = $total;
        $item->information = $request->information;
        $item->purchase_date = Carbon::now()->format('Y-m-d'); // Format standar untuk penyimpanan di database
        $item->status = 'pending';
        $item->save();

        return response()->json(['success' => true, 'message' => 'Material added.']);
    }

    public function UpdatePending(Request $request, $id)
    {
        $item = WeeklyReceipts::where('id', $id)->where('type', 'stock')->where('status', 'pending')->where('admin', Auth::user()->name)->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found']);
        }

        $qty = $request->qty;
        $price = $item->price; // Assuming price remains the same
        $total = $qty * $price;

        $item->qty = $qty;
        $item->price = $price;
        $item->total = $total;
        $item->save();

        return response()->json(['success' => true, 'message' => 'Data updated successfully.']);
    }

    public function DeletePending($id)
    {
        $item = WeeklyReceipts::where('id', $id)->where('type', 'stock')->where('status', 'pending')->first();
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Delete data successfully.']);
    }

    public function UpdateStockFromReceipts($item)
    {
        $material = new Stock();
        $material->id_material = $item->id_material;
        $material->qty = $item->qty;
        $material->id_unit = $item->id_unit;
        $material->price = $item->price;
        $material->total = $item->total;
        $material->information = $item->information;
        $material->save();
    }

    public function SaveWeeklyReceipts($id)
    {
        // Ambil semua items yang sesuai dengan filter
        $items = WeeklyReceipts::where('id', $id)
            ->where('type', 'stock')
            ->where('status', 'pending')
            ->where('admin', Auth::user()->name)
            ->get();  // Menggunakan get() untuk mengembalikan koleksi

        // Validasi jika tidak ada item yang ditemukan
        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No pending receipts found for this user.'
            ]);
        }

        // Loop melalui semua items yang ditemukan
        foreach ($items as $item) {
            // Update status item menjadi 'purchased'
            $item->status = 'purchased';
            $item->save();

            // Panggil fungsi untuk update stock berdasarkan item
            $this->UpdateStockFromReceipts($item);
        }

        return response()->json([
            'success' => true,
            'message' => 'Receipts have been saved and stock updated successfully.'
        ]);
    }
}
