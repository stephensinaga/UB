<?php

namespace App\Http\Controllers;

use App\Models\PreOrder;
use App\Models\PreOrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class PoController extends Controller
{
    public function view()
    {
        $order = PreOrderItem::whereNull('pre_order_id')->get();
        return view('Admin.PO.PreOrder', compact('order'));
    }

    public function AddOrder(Request $request)
    {

        $qty = $request->qty;
        $price = $request->price;
        $grandtotal = $qty * $price;

        $item = new PreOrderItem();
        $item->product = $request->product;
        $item->unit = $request->unit;
        $item->qty = $qty;
        $item->price = $price;
        $item->grandtotal= $grandtotal;
        $item->keterangan = $request->keterangan;
        $item->save();

        return back();
    }

    public function Delete($id){
        $item = PreOrderItem::find($id);
        $item->delete();

        return back();
    }
}
