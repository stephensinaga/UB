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

        $pdf = FacadePdf::loadView('struk.invoice_template', compact('invoice', 'orders', 'customer'))
        ->setPaper([0, 0, 226.77, 841.89]); // Ukuran 80mm (80mm x panjang)

        return response()->json([
            'message' => 'Checkout berhasil',
            'invoice' => $invoice,
            'pdf_url' => route('download.invoice', $Checkout->id) // URL download (opsional)
        ], 200);
    }
}