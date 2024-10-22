<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class PoController extends Controller
{
    public function View(){
        $product = Product::all();
        return view('Admin.PO.PreOrder', compact('product'));
    }

}
