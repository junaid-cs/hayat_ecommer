<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Category\CategoryModel;
use App\Models\Products\ProductModel;

use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function index()
    {
        $categories = CategoryModel::where('status', 1)->get();
        $productsdsc = ProductModel::orderBy('id', 'DESC')->get();
        $productsrated = ProductModel::withCount('ratings')
            ->orderBy('ratings_count', 'DESC')
            ->take(10)
            ->get();

        return view('website.index', compact('categories', 'productsdsc', 'productsrated'));
    }

    public function products_list()
    {
        $categories = CategoryModel::where('status', 1)->get();
        $products = ProductModel::orderBy('id', 'DESC')->get();
        return view('website.products', compact('categories', 'products'));
    }

    // Product Detail
    public function product_detail($id)
    {
        $categories = CategoryModel::where('status', 1)->get();
        $product = ProductModel::with('category','ratings.user')->find($id);
        return view('website.product_detail', compact('categories', 'product'));
    }

    // product search base on category
    public function product_search($category)
    {
        $categories = CategoryModel::where('status', 1)->get();
        $products = ProductModel::where('category_id', $category)->orderBy('id', 'DESC')->get();
        return view('website.products', compact('categories', 'products'));
    }


    // User Dashboard

    public function user_dashboard()
    {
        $categories = CategoryModel::where('status', 1)->get();
        $products = ProductModel::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();
        return view('website.user.dashboard', compact('categories', 'products'));
    }
}
