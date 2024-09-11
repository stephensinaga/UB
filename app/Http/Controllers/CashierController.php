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
        $customer = Customer::firstOrCreate(
            ['customer' => $request->customer_select],
            ['customer' => $request->customer_select]
        );

        $mainOrder = new MainOrder();
        $mainOrder->customer = $customer->customer;

        return back();
    }

}
