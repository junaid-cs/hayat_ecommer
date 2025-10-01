<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products\ProductModel;
use App\Models\Category\CategoryModel;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Traits\FileTraits;


class ProductController extends Controller
{

    use FileTraits;

    public function index(Request $request)
    {
        $search = $request->search ?? "";
        $request->flash();
        if ($search != '') {
            $show = ProductModel::where('title', 'LIKE', '%' . $search . '%')
                ->orWhere('sub_title', 'LIKE', '%' . $search . '%')
                ->orWhereHas('category', function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                })
                ->orWhere('price', 'LIKE', '%' . $search . '%')
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
            $show = ProductModel::orderBy('id', 'DESC')->paginate(10);
        }
        $categories = CategoryModel::orderBy('id', 'DESC')->get();
        return view('Admin/products/index', compact('show', 'categories'));
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
        $store->discount_price = $request->discount_price;
        $store->affiliate_url = $request->affiliate_url;
        $store->amazon = $request->amazon;
        $store->e_bay = $request->e_bay;
        $store->etsy = $request->etsy;
        $store->walmart = $request->walmart;
        $store->description = $request->description;
        $store->save();
        return redirect()->route('Products')->with('success', 'Product Added successfully.');
    }


    // bulk upload
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'bulk_file' => 'required|file|mimes:csv,xls,xlsx|max:5120',
        ]);

        Excel::import(new ProductsImport, $request->file('bulk_file'));

        return redirect()->back()->with('success', 'Products uploaded successfully!');
    }   /*
    ---------------------------------------------------
    |  Product Edit
    ---------------------------------------------------
    */
    public function edit($id)
    {
        try {
            $categories = CategoryModel::orderBy('id', 'DESC')->get();
            $product = ProductModel::find($id);
            return response()->json([
                'html' => view('admin.products.edit', compact('product', 'categories'))->render(),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /*
    ---------------------------------------------------
    |  Product update
    ---------------------------------------------------
    */
    public function update(Request $request)
    {
        try {
            $store = ProductModel::findOrFail($request->id);
            $store->title = $request->title;
            $store->category_id = $request->category_id;
            if ($request->hasFile('image')) {
                if ($store->image && file_exists(public_path('images/' . $store->image))) {
                    unlink(public_path('images/' . $store->image));
                }
                $store->image = $this->uploadSingleImage($request->file('image'), 'images');
            }
            $store->price = $request->price;
            $store->discount_price = $request->discount_price;
            $store->affiliate_url = $request->affiliate_url;
            $store->amazon = $request->amazon;
            $store->e_bay = $request->e_bay;
            $store->etsy = $request->etsy;
            $store->walmart = $request->walmart;
            $store->description = $request->description;
            $store->update();
            return redirect()->back()->with('success', 'Product Updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    // delete product
    public function delete($id)
    {
        try {
            $product = ProductModel::findOrFail($id);
            if ($product->image && file_exists(public_path('images/' . $product->image))) {
                unlink(public_path('images/' . $product->image));
            }
            $product->delete();
            return redirect()->back()->with('success', 'Product Deleted Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }


    // change status

    public function change_status($id)
    {
        try {
            $product = ProductModel::findOrFail($id);
            if ($product->status == 1) {
                $product->status = 0;
                $product->update();
                return redirect()->back()->with('success', 'Product Deactivated Successfully');
            } else {
                $product->status = 1;
                $product->update();
                return redirect()->back()->with('success', 'Product Activated Successfully');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }
}
