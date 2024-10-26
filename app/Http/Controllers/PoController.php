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
        return view('Admin.PO.PreOrder', compact('product','item'));
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

    public function DeleteItem($id){
        $item = PreOrderItem::where('id', $id)->first();
        $item->delete();
        return back();
    }

    public function MakePreOrder(Request $request){
        
    }

}
