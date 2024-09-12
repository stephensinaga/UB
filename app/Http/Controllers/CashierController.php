<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MainOrder;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function CashierView()
    {
        $product = Product::all();

        $order = Order::whereNull('main_id')->get();

        $customers = Customer::all();

        return view('Cashier.Cashier', compact('product', 'order', 'customers'));
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
        // Validasi data dari form checkout
        $request->validate([
            'customer_select' => 'required',
            'payment_type' => 'required',
            'cash' => 'nullable|numeric|min:0',
            'transfer_proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        // Cari atau buat customer
        $customer = Customer::firstOrCreate(
            ['customer' => $request->customer_select == 'other' ? $request->customer : $request->customer_select],
            ['customer' => $request->customer]
        );

        // Hitung total harga dari order (asumsi sudah ada logic sebelumnya untuk menyimpan order ke tabel `orders`)
        $orders = Order::whereNull('main_id')->get(); // Sesuaikan query ini
        $grandtotal = $orders->sum(function ($order) {
            return $order->qty * $order->product_price;
        });

        // Hitung kembalian
        $cashGiven = $request->cash ?? 0;
        $changes = $cashGiven - $grandtotal;

        // Handle upload bukti transfer jika ada
        $transferImage = null;
        if ($request->hasFile('transfer_proof')) {
            $transfer = $request->file('transfer_proof');
            $transferImageName = time() . '_' . $transfer->getClientOriginalName();
            $transferImage = $transfer->store('bukti_transfer', $transferImageName, 'public');
        }


        // Simpan data ke tabel main_orders
        $Checkout = new MainOrder();
        $Checkout->customer = $customer->customer;
        $Checkout->grandtotal = $grandtotal;
        $Checkout->payment = $request->payment_type;
        $Checkout->cash = $cashGiven;
        $Checkout->changes = max($changes, 0); // Pastikan kembalian tidak negatif
        $Checkout->transfer_image = $transferImage;
        $Checkout->status = 'pending';
        $Checkout->save();

        // Dapatkan id dari main order yang baru saja disimpan
        $mainOrderId = $Checkout->id;

        // Ubah status pesanan dan tambahkan main_id
        foreach ($orders as $order) {
            $order->main_id = $mainOrderId; // Set main_id dengan id dari MainOrder yang baru saja dibuat
            $order->save();
        }

        return response()->json(['message' => 'Checkout berhasil'], 200);
    }
}
