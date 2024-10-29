<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MainOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Yajra\DataTables\Facades\DataTables;


class AdminController extends Controller
{

    public function Dashboard()
    {
        $penjualan = MainOrder::whereDate('created_at', now()->toDateString())->count();

        $subPenjualan = Order::whereDate('created_at', now()->toDateString())->sum('qty');

        $customer = MainOrder::whereDate('created_at', now()->toDateString())->distinct('customer')->count('customer');

        return view('dashboard', compact('penjualan', 'subPenjualan', 'customer'));
    }


    public function CreateProductView(Request $request)
    {
        $query = Product::query();

        // Filter by search (name or code)
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->search . '%')
                    ->orWhere('product_code', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('product_category', $request->category);
        }

        $items = $query->get();
        $category = Category::all();

        return view('Admin.createProduct', compact('items', 'category'));
    }


    public function CreateProduct(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255',
            'product_category' => 'required|string|max:255',
            'product_price' => 'required|numeric',
            'product_images' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $category = Category::firstOrCreate(
            ['category' => $request->product_category],
            ['category' => $request->product_category]
        );

        $imagePath = null;
        if ($request->hasFile('product_images')) {
            $image = $request->file('product_images');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->move(public_path('product_images'), $imageName);
            $imagePath = 'product_images/' . $imageName;
        }


        $product = new Product;
        $product->product_name = $request->product_name;
        $product->product_code = $request->product_code;
        $product->product_price = $request->product_price;
        $product->product_images = $imagePath;
        $product->product_category = $category->category;
        $product->save();

        return back()->with('success', 'Product created successfully.');
    }

    public function DeleteProduct($id)
    {
        $item = Product::where('id', $id)->first();
        $item->delete();
    }

    public function EditProductView($id)
    {
        $product = Product::findOrFail($id);
        return view('Admin.EditProduct', compact('product'));
    }

    public function EditProduct(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255',
            'product_category' => 'required|string|max:255',
            'product_price' => 'required|numeric',
            'product_images' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = Product::findOrFail($id);

        // Update or create the category
        $category = Category::firstOrCreate(
            ['category' => $request->product_category],
            ['category' => $request->product_category]
        );

        // Process image upload if a new image is uploaded
        if ($request->hasFile('product_images')) {
            $image = $request->file('product_images');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('product_images'), $imageName);
            $data->product_images = 'product_images/' . $imageName;
        }

        // Update product data
        $data->product_name = $request->product_name;
        $data->product_code = $request->product_code;
        $data->product_category = $category->category;
        $data->product_price = $request->product_price;
        $data->save();

        return redirect(route('CreateProductView'))->with('success', 'Product updated successfully.');
    }


    public function ExportLaporanPDF()
    {
        $mainOrders = MainOrder::with('orders')->get();

        $pdf = Pdf::loadView('admin.exportLaporanPDF', compact('mainOrders'));

        return $pdf->download('sales_report.pdf');
    }

    public function SalesReport()
    {
        $mainOrders = MainOrder::with('orders')->get();

        return view('Admin.exportLaporanPDF', compact('mainOrders'));
    }

    public function HistoryPenjualanCashier(Request $request)
    {
        $user = Auth::user()->name;

        $mainOrdersQuery = MainOrder::with('orders')->where('cashier', $user);

        if ($request->has('payment_method') && $request->payment_method != '') {
            $mainOrdersQuery->where('payment', $request->payment_method);
        }

        if ($request->has('customer') && $request->customer != '') {
            $mainOrdersQuery->where('customer', $request->customer);
        }

        $mainOrdersQuery->whereDate('created_at', now()->toDateString());

        $mainOrders = $mainOrdersQuery->get();

        $customers = MainOrder::distinct('customer')->pluck('customer');

        if ($request->ajax()) {
            return response()->json(['orders' => $mainOrders]);
        }

        return view('Cashier.laporanHarian', compact('mainOrders', 'user', 'customers'));
    }


    public function DownloadHistoryCashier()
    {
        $user = Auth::user()->name;
        $mainOrders = MainOrder::with('orders')->where('cashier', $user)->get();
    }

    public function DetailLaporan($id)
    {
        $orders = Order::where('main_id', $id)->get();

        return response()->json($orders);
    }

    public function LaporanPenjualan(Request $request)
    {
        // Ambil data cashier dari user
        $cashiers = User::pluck('name', 'id');

        // Mulai query untuk MainOrder, namun hanya jika ada filter
        $mainOrdersQuery = MainOrder::with('orders');

        $filtersApplied = false;

        // Filter berdasarkan metode pembayaran jika ada
        if ($request->filled('payment_method')) {
            $mainOrdersQuery->where('payment', $request->payment_method);
            $filtersApplied = true;
        }

        // Filter berdasarkan cashier jika ada
        if ($request->filled('cashier')) {
            $mainOrdersQuery->where('cashier', $request->cashier);
            $filtersApplied = true;
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            $mainOrdersQuery->whereDate('created_at', $request->date);
            $filtersApplied = true;
        }

        // Filter berdasarkan rentang tanggal jika ada
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $mainOrdersQuery->whereBetween('created_at', [$request->start_date, $request->end_date]);
            $filtersApplied = true;
        }

        // Jika filter diisi, ambil data, jika tidak kosongkan data
        if ($filtersApplied) {
            $mainOrders = $mainOrdersQuery->get();
        } else {
            $mainOrders = collect(); // Data kosong jika tidak ada filter
        }

        // Ambil daftar customer yang unik (opsional)
        $customers = MainOrder::distinct('customer')->pluck('customer');

        // Jika permintaan AJAX, kembalikan data dalam format JSON
        if ($request->ajax()) {
            return response()->json(['orders' => $mainOrders]);
        }

        // Tampilkan view dengan data yang diperlukan
        return view('Admin.laporanPenjualan', compact('mainOrders', 'cashiers', 'customers'));
    }
}
