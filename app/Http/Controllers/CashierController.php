<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function CashierView()
    {
        $product = Product::all();

        $order = Order::where('status', 'pending')->get();

        $productIds = $order->pluck('product_id')->toArray();

        $pendingProduct = Product::whereIn('id', $productIds)->get()->map(function ($product) use ($order) {
            $product->order_qty = $order->where('product_id', $product->id)->count();

            $product->total_price = $product->product_price * $product->order_qty;

            return $product;
        });

        $total = $pendingProduct->sum('total_price');

        $customers = Customer::all();

        return view('Cashier.Cashier', compact('product', 'order', 'total', 'customers', 'pendingProduct'));
    }

    public function Order(Request $request, $id)
    {
        $product = Product::where('id', $id)->first();

        if ($product) {
            $order = new Order();
            $order->product_id = $product->id;
            $order->status = 'pending';

            $order->save();
        }

        return back();
    }

    public function CheckOut(Request $request)
    {
        $customer = $request->input('customer');
        if (!$customer) {
            $customer = $request('customer_DD');
        }

        if (!$customer) {
            return back();
        }

        $customerRecord = Customer::firstOrCreate(['customer' => $customer]);
        $items = Order::where('status', 'pending')->get();

        foreach ($items as $checkout) {
            $checkout->status = 'ordered';
            $checkout->customer = $customerRecord->customer;
            $checkout->save();
        }

        return back();
    }

    public function DeletePendingOrder($id)
    {
        $item = Order::where('id', $id)->first();
        $item->delete();

        return back();
    }
}
