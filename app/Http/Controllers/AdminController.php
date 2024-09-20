<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MainOrder;
use App\Models\Order;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class AdminController extends Controller
{

    public function Dashboard()
    {
        return view('dashboard');
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
            $imagePath = $image->storeAs('product_images', $imageName, 'public');
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
        $data = Product::where('id', $id)->first();

        if ($request->hasFile('product_images')) {
            $image = $request->file('product_images');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('product_images', $imageName, 'public');

            $data->product_images = $imagePath;
        }

        $data->product_name = $request->product_name;
        $data->product_code = $request->product_code;
        $data->product_category = $request->product_category;
        $data->product_price = $request->product_price;
        $data->update();

        return redirect(route('CreateProductView'));
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

    public function LaporanView()
    {
        $user = Auth::user()->name;
        $mainOrders = MainOrder::with('orders')->where('cashier', $user)->get();

        return view('Admin.LaporanView', compact('mainOrders', 'user'));
    }

    public function DetailLaporan($id)
    {
        $orders = Order::where('main_id', $id)->get();

        return response()->json($orders);
    }

    public function printInvoice($id)
    {
        // Find the main order by id and load related orders
        $mainOrder = MainOrder::with('orders')->find($id);

        if (!$mainOrder) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }

        $pdf = Pdf::loadView('admin.invoice', compact('mainOrders'));

        return $pdf->download('invoice.pdf');
    }

}

