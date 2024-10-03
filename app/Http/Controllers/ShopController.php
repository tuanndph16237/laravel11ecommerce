<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
{
    $size = $request->query('size')?$request->query('size'):12;
    $o_column = "";
    $o_order = "";
    $order = $request->query('order') ? $request->query('order') : -1;
    switch($order)
    {
        case 1:
            $o_column='created_at';
            $o_order='DESC';
            break;
        case 2:
            $o_column='created_at';
            $o_order='ASC';
            break;
        case 3:
            $o_column='regular_price';
            $o_order='ASC';
            break;
        case 4:
            $o_column='regular_price';
            $o_order='DESC';
            break;
        default:
            $o_column='id';
            $o_order='DESC';
    }
    $products = Product::orderBy("created_at","DESC")->paginate($size);
    $categories = Category::orderBy("name","ASC")->get();
    $brands = Brand::orderBy("name","ASC")->get();
    return view('shop',compact("products","size"));
}

public function product_details($product_slug)
{
    $product = Product::where("slug",$product_slug)->first();
    $rproducts = Product::where("slug","<>",$product_slug)->get()->take(8);
    return view('details',compact("product","rproducts"));
}

}
