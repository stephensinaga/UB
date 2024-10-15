<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StockController extends Controller
{

    public function NewMaterial(Request $request)
    {
        $material = new Material();
        $material->id_material = $request->id_material;
        $material->material = $request->material;
        $material->qty = $request->qty;
        $material->satuan = $request->satuan;
        $material->keterangan = $request->keterangan;
        $material->save();
        return back();
    }

    public function StorageView()
    {
        $materials = Material::select('id', 'id_material', 'material', 'qty', 'satuan', 'keterangan', 'created_at')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)')) // Mengambil id paling besar (yang terbaru)
                    ->from('materials')
                    ->groupBy('id_material'); // Mengelompokkan berdasarkan id_material
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Admin.Stock.Storage', compact('materials'));
    }


    public function UpdateView($id)
    {
        $item = Material::where('id', $id)->first();
        return view('Admin.Stock.UpdateMaterial', compact('item'));
    }

    public function UpdateProcess(Request $request)
    {
        // Validasi data jika perlu
        $material = Material::where('id', $request->id)->first(); // Mengupdate material yang sudah ada
        if ($material) {
            $materials = new Material();
            $materials->id_material = $request->id_material;
            $materials->material = $request->material;
            $materials->qty = $request->qty;
            $materials->satuan = $request->satuan;
            $materials->keterangan = $request->keterangan;
            $materials->save();

            return response()->json(['success' => true, 'message' => 'Update Success']);
        }

        return response()->json(['success' => false, 'message' => 'Material not found'], 404);
    }

    public function FilterMaterial(Request $request)
    {
        // Mulai query untuk Material
        $materialsQuery = Material::query();

        $filtersApplied = false;

        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            $materialsQuery->whereDate('created_at', $request->date);
            $filtersApplied = true;
        }

        // Filter berdasarkan rentang tanggal jika ada
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $materialsQuery->whereBetween('created_at', [$request->start_date, $request->end_date]);
            $filtersApplied = true;
        }

        // Jika filter diisi, ambil data, jika tidak kosongkan data
        if ($filtersApplied) {
            $materials = $materialsQuery->get();
        } else {
            $materials = collect(); // Data kosong jika tidak ada filter
        }

        // Jika permintaan AJAX, kembalikan data dalam format JSON
        if ($request->ajax()) {
            return response()->json(['materials' => $materials]);
        }

        // Tampilkan view dengan data yang diperlukan
        return view('Admin.Stock.Storage', compact('materials'));
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
        $sheet->setCellValue('B2', 'ID Material');
        $sheet->setCellValue('C2', 'Nama Material');
        $sheet->setCellValue('D2', 'Stock');
        $sheet->setCellValue('E2', 'Unit');
        $sheet->setCellValue('F2', 'Information');
        $sheet->setCellValue('G2', 'Created At');
    
        // Mulai query untuk materials
        $materialsQuery = Material::query();
        if ($request->has('date')) {
            $materialsQuery->whereDate('created_at', $request->date);
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $materialsQuery->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
    
        // Ambil data sesuai filter
        $materials = $materialsQuery->orderBy('created_at', 'asc')->get();
    
        // Inisialisasi variabel untuk grouping by date
        $currentDate = null;
        $row = 3; // Mulai dari baris ketiga setelah header
        $no = 1;  // Nomor urut
    
        foreach ($materials as $material) {
            // Cek apakah tanggal berubah
            $materialDate = $material->created_at->format('d M Y');
            if ($currentDate !== $materialDate) {
                // Jika tanggal berubah, tambahkan tanggal sebagai header
                $sheet->setCellValue('A' . $row, 'Date: ' . $materialDate);
                $sheet->mergeCells('A' . $row . ':E' . $row); // Gabungkan sel hingga E
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++; // Increment baris setelah header tanggal
                $currentDate = $materialDate;
            }
    
            // Isi data material
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $material->id_material);
            $sheet->setCellValue('C' . $row, $material->material);
            $sheet->setCellValue('D' . $row, $material->qty);
            $sheet->setCellValue('E' . $row, $material->satuan);
            $sheet->setCellValue('F' . $row, $material->keterangan ?? '-');
            $sheet->setCellValue('G' . $row, $material->created_at->format('d M Y H:i'));
    
            // Styling untuk setiap baris data
            $sheet->getStyle('A' . $row . ':G' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
            $row++;
            $no++; // Increment nomor urut
        }
    
        // Auto-size kolom
        foreach (range('A', 'G') as $columnID) {
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
    
}
