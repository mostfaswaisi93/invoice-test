<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index()
    {
        $sections = Section::all();
        $products = Product::all();
        return view('products.products', compact('sections', 'products'));
    }

    public function store(Request $request)
    {
        Product::create([
            'product_name' => $request->product_name,
            'section_id' => $request->section_id,
            'description' => $request->description,
        ]);
        session()->flash('Add', 'تم إضافة المنتج بنجاح ');
        return redirect('/products');
    }

    public function update(Request $request)
    {
        $id = Section::where('section_name', $request->section_name)->first()->id;

        $Products = Product::findOrFail($request->pro_id);

        $Products->update([
            'product_name' => $request->product_name,
            'description' => $request->description,
            'section_id' => $id,
        ]);

        session()->flash('Edit', 'تم تعديل المنتج بنجاح');
        return back();
    }

    public function destroy(Request $request)
    {
        $products = Product::findOrFail($request->pro_id);
        $products->delete();
        session()->flash('delete', 'تم حذف المنتج بنجاح');
        return back();
    }
}
