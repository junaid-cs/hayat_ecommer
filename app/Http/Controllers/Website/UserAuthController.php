<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Category\CategoryModel;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    // register user
    public function user_register()
    {
        $categories = CategoryModel::where('status', 1)->get();
        return view('website.auth.register', compact('categories'));
    }


    // User login page
    public function user_login()
    {
        $categories = CategoryModel::where('status', 1)->get();
        return view('website.auth.login', compact('categories'));
    }

    // Password Change
    public function password_change(Request $request)
    {
        $user = User::find($request->id);

        if (!$user || !$request->has(['old_password', 'password'])) {
            return redirect()->back()->with(['message' => 'User not found or required fields missing.'], 400);
        }

        // Check if old password matches
        if (!Hash::check($request->old_password, $user->password)) {
            return redirect()->back()->with(['message' => 'Old password is incorrect.'], 400);
        }

        // Update to new password
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->back()->with(['success' => 'Password Updated Successfully.'], 400);
    }
}
