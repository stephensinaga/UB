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
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class CashierController extends Controller
{
    public function CashierView(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $productQuery = Product::query();  // Mulai dari query builder

        if ($search) {
            $productQuery->where(function ($query) use ($search) {
                $query->where('product_name', 'like', '%' . $search . '%')
                    ->orWhere('product_code', 'like', '%' . $search . '%');
            });
        }

        if ($category) {
            $productQuery->where('product_category', $category);  // Tambahkan ke query builder
        }

        $product = $productQuery->get();  // Panggil get() hanya setelah semua query selesai
        $categories = Category::all();

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

        // Ambil produk sesuai dengan filter yang ada
        $product = $productQuery->get();

        // Ambil semua kategori produk
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
        $today = Carbon::now()->format('d-m-y');
        $lastInvoice = MainOrder::whereDate('created_at', $today)->orderBy('no_invoice', 'desc')->first();
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

        $Checkout = new MainOrder();
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
            $order->save();
        }

        $invoice = MainOrder::where('id', $Checkout->id)->first();

        // $pdf = FacadePdf::loadView('struk.invoice_template', compact('invoice', 'orders', 'customer'))
        //     ->setPaper([0, 0, 226.77, 841.89]);

        return response()->json([
            'message' => 'Checkout berhasil',
            'invoice' => $invoice,
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

    private function printToThermalPrinter($mainOrder)
    {
        $connector = new WindowsPrintConnector("POS-801");

        try {
            $printer = new Printer($connector);

            // Mencetak Header
            $printer->setEmphasis(true);
            $printer->text("INVOICE\n");
            $printer->setEmphasis(false);
            $printer->text("-----------------------------\n");

            // Mencetak Detail Order
            $printer->text("Invoice No    : {$mainOrder->id}\n");
            $printer->text("Date          : {$mainOrder->created_at->format('d/m/Y')}\n");
            $printer->text("Cashier       : {$mainOrder->cashier}\n");
            $printer->text("Customer      : {$mainOrder->customer}\n");
            $printer->text("Payment Method: " . ucfirst($mainOrder->payment) . "\n");

            if ($mainOrder->payment === 'cash') {
                $printer->text("Paid          : Rp" . number_format($mainOrder->cash, 0, ',', '.') . "\n");
            } else {
                $printer->text("Transfer Proof: See Attached\n");
            }

            // Mencetak Detail Produk tanpa border
            $printer->text("-----------------------------\n");

            foreach ($mainOrder->orders as $order) {
                $productName = str_pad($order->product_name, 22); // Padding untuk nama produk
                $productPrice = "Rp" . number_format($order->product_price, 0, ',', '.');
                $quantity = $order->qty;
                $printer->text("{$productName}    : {$productPrice} * {$quantity}\n");
            }

            // Mencetak Total
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

            // Tutup koneksi printer
            $printer->close();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
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
