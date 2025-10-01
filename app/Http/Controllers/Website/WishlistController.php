<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Products\ProductModel;
use App\Models\Wishlist\WishlistModel;
use App\Models\Category\CategoryModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class WishlistController extends Controller
{

        // wishlist page
    public function index()
    {
        $user = Auth::user()->id;
        $categories = CategoryModel::where('status', 1)->get();
        $wishlists = WishlistModel::where('user_id', $user)->with('product')->paginate(10);
        return view('website.wishlist', compact('wishlists','categories'));
    }

    public function wishlist($productid)
    {
        $user = Auth::user()->id;
        $wishlist = WishlistModel::where('user_id', $user)
            ->where('product_id', $productid)
            ->first();
        if ($wishlist) {
            $wishlist->delete();
            return redirect()->back()->with('success', 'Wishlist removed successfully');
        } else {
            $store = new WishlistModel;
            $store->user_id = $user;
            $store->product_id = $productid;
            $store->save();

            return redirect()->back()->with('success', 'Wishlist added successfully');
        }
    }

    /*
    // clear wishlist
    */
    public function wish_clear()
    {
        $user = Auth::user()->id;
        $wishlists = WishlistModel::where('user_id', $user)->delete();
        return redirect()->back()->with('success', 'Wishlist cleared successfully');
    }

        /*
    // Delete wishlist
    */
    public function wish_delete($id)
    {
        $wishlists = WishlistModel::find($id);
        $wishlists->delete();
        return redirect()->back()->with('success', 'Wishlist Delete successfully');
    }


}

