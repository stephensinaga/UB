<?php

namespace App\Http\Controllers;

use App\Models\PreOrder;
use App\Models\PreOrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class PoController extends Controller
{
    public function View()
    {
        $product = Product::all();
        $item = PreOrderItem::whereNull('pre_order_id')->get();
        return view('Admin.PO.PreOrder', compact('product', 'item'));
    }

    public function SavePOItem(Request $request)
    {
        $qty = $request->qty;
        $price = $request->price;
        $grandtotal = $qty * $price;

        $item = new PreOrderItem();
        $item->product = $request->product;
        $item->qty = $qty;
        $item->price = $price;
        $item->grandtotal = $grandtotal;
        $item->keterangan = $request->keterangan;
        $item->save();
    }

    public function DeleteItem($id)
    {
        $item = PreOrderItem::where('id', $id)->first();
        $item->delete();
        return back();
    }

    public function MakePreOrder(Request $request)
    {
        $ids = $request->input('ids');

        // Check if there are IDs
        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No item found.'
            ]);
        }

        $items = PreOredrItem::whereNull('pre_order_id')->get();
        $totalPrice = $items->sum('grandtotal');

        $cashGiven = $request->cash ?? 0;
        $changes = $cashGiven - $totalPrice;

        $transferImage = null;
        if ($request->hasFile('transfer_proof')) {
            $transfer = $request->file('transfer_proof');
            $transferImageName = time() . '_' . $transfer->getClientOriginalName();
            $transferImage = $transfer->storeAs('bukti_transfer', $transferImageName, 'public');
        }


        $order = new PreOrder();
        $order->customer = $request->customer;
        $order->customer_contact = $request->customer_contact;
        $order->keterangan = $request->keterangan;
        $order->total_price = $totalPrice;
        $order->payment = $request->payment;
        $order->cash = $cashGiven;
        $order->transfer_img = $transferImage;
        $order->save();

        return back();
    }
}
