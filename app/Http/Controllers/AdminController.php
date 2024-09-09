<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function Dashboard(){
        return view('dashboard');
    }

    public function CreateProductView()
    {
        $items = Product::all();
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
}
