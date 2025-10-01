<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products\ProductModel;
use App\Models\Category\CategoryModel;
use Illuminate\Support\Facades\Auth;
use App\Traits\FileTraits;
class ProductsController extends Controller
{
     use FileTraits;
        public function index(Request $request)
    {
        $search = $request->search ?? "";
        $request->flash();
        if ($search != '') {
            $show = ProductModel::where('user_id', Auth::user()->id)->where('title', 'LIKE', '%' . $search . '%')
                ->orWhere('sub_title', 'LIKE', '%' . $search . '%')
                ->orWhereHas('category', function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                })
                ->orWhere('price', 'LIKE', '%' . $search . '%')
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
            $show = ProductModel::where('user_id',Auth::user()->id)->orderBy('id', 'DESC')->paginate(10);
        }
        $category = CategoryModel::orderBy('id', 'DESC')->get();
        $categories = CategoryModel::where('status', 1)->get();

        return view('website.products.index', compact('show', 'categories','category'));
    }

        public function store(Request $request)
    {
        $user = Auth::user()->id;
        $store = new ProductModel;
        $store->title = $request->title;
        $store->user_id = $user;
        $store->category_id = $request->category_id;
        if ($request->hasFile('image')) {
            $store->image = $this->uploadSingleImage($request->file('image'), 'images');
        }
        $store->price = $request->price;
        $store->affiliate_url = $request->affiliate_url;
        $store->description = $request->description;
        $store->save();
        return redirect()->back()->with('success', 'Product Added successfully.');
    }

    // delete product
    public function delete($id)
    {
        $product = ProductModel::find($id);
        if ($product->image) {
            $this->deleteFile($product->image, 'images/');
        }
        if ($product) {
            $product->delete();
            return redirect()->back()->with('success', 'Product Deleted Successfully');
        } else {
            return redirect()->back()->with('error', 'Product Not Found');
        }
    }
}
