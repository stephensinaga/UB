<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MainOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class CashierController extends Controller
{
    public function CashierView(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        // Mulai dari query builder
        $productQuery = Product::query();

        // Filter berdasarkan search input
        if ($search) {
            $productQuery->where(function ($query) use ($search) {
                $query->where('product_name', 'like', '%' . $search . '%')
                    ->orWhere('product_code', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan kategori
        if ($category) {
            $productQuery->where('product_category', $category);
        }

        // Ambil data produk dengan pagination
        $product = $productQuery->get(); // Batasi 12 produk per halaman
        $categories = Category::all();

        // Data lainnya
        $order = Order::whereNull('main_id')->get();
        $customers = Customer::all();
        $invoice = MainOrder::latest()->first();

        return view('Cashier.Cashier', compact('product', 'order', 'customers', 'invoice', 'category', 'categories'));
    }

    public function GuestView(Request $request)
    {
        // Mengambil nomor meja dan nama customer dari session
        $tableNumber = session('table_number');
        $customerName = session('customer_name');

        $search = $request->input('search');
        $category = $request->input('category');

        $productQuery = Product::query(); // Mulai dari query builder

        // Filter produk berdasarkan pencarian
        if ($search) {
            $productQuery->where(function ($query) use ($search) {
                $query->where('product_name', 'like', '%' . $search . '%')
                    ->orWhere('product_code', 'like', '%' . $search . '%');
            });
        }

        // Filter produk berdasarkan kategori
        if ($category) {
            $productQuery->where('product_category', $category);
        }

        $product = $productQuery->get(); // Batasi 12 produk per halaman
        $categories = Category::all();

        // Mencari orders berdasarkan no_meja, customer_name, dan main_id yang null
        $order = Order::where('no_meja', $tableNumber)
            ->where('customer', $customerName) // Asumsi ada kolom customer_name di tabel orders
            ->whereNull('main_id')
            ->get();

        // Ambil semua customer
        $customers = Customer::all();

        return view('Guest.Cashier', compact('product', 'order', 'customers', 'category', 'categories', 'tableNumber', 'customerName'));
    }

    public function ListOrder()
    {
        $mainOrders = MainOrder::whereNull('cashier')->get();
        return view('Cashier.ListOrder', compact('mainOrders'));
    }


    public function SaveSession(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string',
            'customer_name' => 'required|string',
        ]);

        // Simpan data ke session
        session([
            'table_number' => $request->table_number,
            'customer_name' => $request->customer_name,
        ]);

        // Kembalikan respon sukses
        return response()->json(['success' => true]);
    }

    public function GuestOrder($id)
    {
        // Mengambil produk berdasarkan ID
        $product = Product::where('id', $id)->first();

        // Pastikan produk ditemukan
        if ($product) {
            // Mengambil nomor meja dari session
            $tableNumber = session('table_number');
            $customerName = session('customer_name');

            // Cek apakah produk sudah diorder sebelumnya
            $checkItem = Order::where('product_id', $product->id)
                ->where('no_meja', $tableNumber) // Cek berdasarkan no_meja juga
                ->whereNull('main_id')
                ->first();

            if ($checkItem) {
                // Jika sudah diorder, tambahkan jumlahnya
                $checkItem->qty += 1;
                $checkItem->save();
            } else {
                // Jika belum diorder, buat order baru
                $order = new Order();
                $order->customer = $customerName;
                $order->no_meja = $tableNumber;
                $order->product_id = $product->id;
                $order->product_name = $product->product_name;
                $order->product_code = $product->product_code;
                $order->product_category = $product->product_category;
                $order->product_price = $product->product_price;
                $order->qty = 1;

                $order->save();
            }
        }
        return back();
    }


    public function Order($id)
    {
        $product = Product::where('id', $id)->first();

        if ($product) {
            $checkItem = Order::where('product_id', $product->id)
                ->whereNull('main_id')
                ->first();

            if ($checkItem) {
                $checkItem->qty += 1;
                $checkItem->save();
            } else {
                $order = new Order();
                $order->product_id = $product->id;
                $order->product_name = $product->product_name;
                $order->product_code = $product->product_code;
                $order->product_category = $product->product_category;
                $order->product_price = $product->product_price;
                $order->qty = 1;

                $order->save();
            }
        }

        return back();
    }

    public function MinOrderItem($id)
    {
        $product = Order::where('id', $id)->first();

        if ($product) {
            $product->qty -= 1;

            if ($product->qty <= 0) {
                $product->delete();
            } else {
                $product->save();
            }
        }
        return redirect()->route('CashierView');
    }

    public function AddOrderItem($id)
    {
        $product = Order::where('id', $id)->first();

        if ($product) {
            $product->qty += 1;
            $product->save();
        }
        return redirect()->route('CashierView');
    }

    public function MinOrderItemGuest($id)
    {
        $product = Order::where('id', $id)->first();

        if ($product) {
            $product->qty -= 1;

            if ($product->qty <= 0) {
                $product->delete();
            } else {
                $product->save();
            }
        }
        return redirect()->route('GuestCashierView');
    }

    public function AddOrderItemGuest($id)
    {
        $product = Order::where('id', $id)->first();

        if ($product) {
            $product->qty += 1;
            $product->save();
        }
        return redirect()->route('GuestCashierView');
    }

    public function GuestCheckOut(Request $request)
    {
        // Ambil no_meja dari request atau session
        $tableNumber = $request->input('no_meja') ?? session('table_number');

        // Pastikan bahwa no_meja ada, jika tidak, kembalikan error
        if (!$tableNumber) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => ['no_meja' => ['The no meja field is required.']]
            ], 422);
        }

        $customerName = session('customer_name');

        // Generate no_invoice, incremented and reset every day
        $today = Carbon::today(); // Use Carbon::today() to compare correctly with whereDate
        $lastInvoice = MainOrder::whereDate('created_at', $today)->orderBy('no_invoice', 'desc')->first();

        // Increment invoice number
        $noInvoice = $lastInvoice ? $lastInvoice->no_invoice + 1 : 1;

        // Get pending orders for the table
        $orders = Order::where('no_meja', $tableNumber)->whereNull('main_id')->get();

        if ($orders->isEmpty()) {
            return response()->json(['error' => 'No orders found for this table.'], 404);
        }

        // Calculate grand total
        $grandtotal = $orders->sum(function ($order) {
            return $order->qty * $order->product_price;
        });

        // Create new main order for checkout
        $Checkout = new MainOrder();
        $Checkout->no_invoice = $noInvoice;
        $Checkout->no_meja = $tableNumber;
        $Checkout->customer = $customerName; // For guest
        $Checkout->grandtotal = $grandtotal;
        $Checkout->status = 'pending';
        $Checkout->save();

        // Update each order to link with main order
        $mainOrderId = $Checkout->id;

        foreach ($orders as $order) {
            $order->main_id = $mainOrderId;
            $order->save();
        }

        return response()->json([
            'message' => 'Guest checkout successful',
            'invoice' => $Checkout,
        ], 200);
    }

    public function CheckOut(Request $request)
    {
        $request->validate([
            'customer_select' => 'required',
            'payment_type' => 'required',
            'cash' => 'nullable|numeric|min:0',
            'transfer_proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $customer = Customer::firstOrCreate(
            ['customer' => $request->customer_select == 'other' ? $request->customer : $request->customer_select],
            ['customer' => $request->customer]
        );

        $orders = Order::whereNull('main_id')->get();
        $grandtotal = $orders->sum(function ($order) {
            return $order->qty * $order->product_price;
        });

        $cashGiven = $request->cash ?? 0;
        $changes = $cashGiven - $grandtotal;

        $transferImage = null;
        if ($request->hasFile('transfer_proof')) {
            $transfer = $request->file('transfer_proof');
            $transferImageName = time() . '_' . $transfer->getClientOriginalName();
            $transferImage = $transfer->storeAs('bukti_transfer', $transferImageName, 'public');
        }

        $cashier = Auth::user();

        // Generate invoice number
        $today = now()->format('Y-m-d');
        // Cari pesanan terakhir pada hari ini
        $lastOrder = MainOrder::whereDate('created_at', $today)->orderBy('id', 'desc')->first();
        // Jika belum ada pesanan hari ini, nomor invoice dimulai dari 1, jika ada, increment dari nomor terakhir
        $newInvoiceNumber = $lastOrder ? ($lastOrder->no_invoice + 1) : 1;

        $Checkout = new MainOrder();
        $Checkout->no_meja = $request->no_meja;
        $Checkout->no_invoice = $newInvoiceNumber; // Assign new invoice number
        $Checkout->cashier = $cashier->name;
        $Checkout->customer = $customer->customer;
        $Checkout->grandtotal = $grandtotal;
        $Checkout->payment = $request->payment_type;
        $Checkout->cash = $cashGiven;
        $Checkout->changes = max($changes, 0);
        $Checkout->transfer_image = $transferImage;
        $Checkout->status = 'checkout';
        $Checkout->save();

        $mainOrderId = $Checkout->id;

        foreach ($orders as $order) {
            $order->main_id = $mainOrderId;
            $order->customer = $customer->customer;
            $order->no_meja = $request->no_meja;
            $order->save();
        }

        $invoice = MainOrder::where('id', $Checkout->id)->first();

        return response()->json([
            'message' => 'Checkout berhasil',
            'invoice' => $invoice,
        ], 200);
    }

    public function ProcessPendingOrder($id, $payment_type, $cash = null, $img = null)
    {
        $data = MainOrder::findOrFail($id);
        $cashier = Auth::user(); // Pastikan user login

        // Jika cashier tidak ditemukan (user tidak login)
        if (!$cashier) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Hitung kembalian jika pembayaran tunai
        $cashGiven = (float)$cash;
        $changes = (float)$cashGiven - (float)$data->grandtotal;

        // Proses bukti transfer jika ada
        $transferImage = null;
        if ($img) {
            $transfer = request()->file('img');
            $transferImageName = time() . '_' . $transfer->getClientOriginalName();
            $transferImage = $transfer->storeAs('bukti_transfer', $transferImageName, 'public');
        } else {
            $transferImage = null;
        }


        // Update data order
        $data->cashier = $cashier->name;
        $data->payment = $payment_type;
        $data->cash = $cashGiven;
        $data->changes = $changes;
        $data->transfer_image = $transferImage;
        $data->status = 'checkout';
        $data->save();

        return response()->json([
            'message' => 'Order processed successfully',
            'invoice' => $data,
        ], 200);
    }


    public function printInvoice($id)
    {
        // Temukan main order berdasarkan id dan muat relasi orders
        $mainOrder = MainOrder::with('orders')->find($id);

        if (!$mainOrder) {
            return response()->json(['error' => 'Invoice not found.'], 404);
        }

        // Cetak invoice
        $result = $this->printToThermalPrinter($mainOrder);

        if ($result['success']) {
            return response()->json(['success' => 'Invoice printed successfully!']);
        } else {
            return response()->json(['error' => $result['error']], 500);
        }
    }

    private function resizeImage($imagePath, $newWidth, $newHeight)
    {
        list($width, $height) = getimagesize($imagePath);
        $source = imagecreatefrompng($imagePath);
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Membuat latar belakang transparan (untuk PNG)
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefill($newImage, 0, 0, $transparent);

        // Resize gambar
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Simpan gambar yang diubah ukurannya
        $resizedPath = public_path('assets/img/resized_logo.png');
        imagepng($newImage, $resizedPath);

        // Hapus memori gambar
        imagedestroy($source);
        imagedestroy($newImage);

        return $resizedPath;
    }

    private function printToThermalPrinter($mainOrder)
    {
        $connector = new WindowsPrintConnector("POS-801");
        $printer = null;

        try {
            $printer = new Printer($connector);

            // Memuat dan meresize gambar logo
            $logoPath = public_path('assets/img/dapur_negeri.png');
            $resizedLogoPath = $this->resizeImage($logoPath, 210, 175); // Resize ke 200x200 piksel (ukuran lebih besar)
            $logo = EscposImage::load($resizedLogoPath, false); // Memuat gambar yang sudah di-resize

            // Mengatur justifikasi menjadi tengah untuk gambar
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->bitImageColumnFormat($logo, Printer::IMG_DEFAULT);

            // Cetak detail order (tanpa tulisan INVOICE)
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text("-----------------------------\n");
            $printer->setEmphasis(false);

            // Cetak detail order
            $printer->text("Invoice No    : {$mainOrder->id}\n");
            $printer->text("Date          : {$mainOrder->created_at->format('d/m/Y')}\n");
            $printer->text("Cashier       : {$mainOrder->cashier}\n");
            $printer->text("Customer      : {$mainOrder->customer}\n");

            // Tambahkan nomor meja jika ada
            if (isset($mainOrder->table_number)) {
                $printer->text("Table No      : {$mainOrder->table_number}\n");
            }

            $printer->text("Payment Method: " . ucfirst($mainOrder->payment) . "\n");

            if ($mainOrder->payment === 'cash') {
                $printer->text("Paid          : Rp" . number_format($mainOrder->cash, 0, ',', '.') . "\n");
            } else {
                $printer->text("Transfer Proof: See Attached\n");
            }

            // Cetak detail produk
            $printer->text("-----------------------------\n");
            foreach ($mainOrder->orders as $order) {
                $productName = str_pad($order->product_name, 23);
                $productPrice = "Rp" . number_format($order->product_price, 0, ',', '.');
                $quantity = $order->qty;
                $printer->text("{$productName}    : {$productPrice} * {$quantity}\n");
            }

            // Cetak total
            $printer->text("-----------------------------\n");
            $printer->text("Total         : Rp" . number_format($mainOrder->grandtotal, 0, ',', '.') . "\n");

            if ($mainOrder->payment === 'cash') {
                $printer->text("Cash Paid     : Rp" . number_format($mainOrder->cash, 0, ',', '.') . "\n");
                $printer->text("Change        : Rp" . number_format($mainOrder->changes, 0, ',', '.') . "\n");
            }

            $printer->text("-----------------------------\n");
            $printer->text("Thank you for your purchase!\n");

            // Memotong kertas
            $printer->cut();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        } finally {
            if ($printer !== null) {
                $printer->close(); // Pastikan selalu menutup koneksi
            }
        }
    }


    public function testPrinterConnection()
    {
        // Ganti dengan nama printer Anda
        $printerName = "smb://rpl-07/POS-801"; // Sesuaikan dengan nama printer Anda
        $connector = new WindowsPrintConnector($printerName);

        try {
            $printer = new Printer($connector);

            // Mencetak teks uji
            $printer->text("Test Print - Printer Connection Successful\n");
            $printer->cut();
            $printer->close();

            return response()->json(['success' => 'Printer connection successful!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Printer connection failed: ' . $e->getMessage()], 500);
        }
    }
}
