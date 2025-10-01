<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ?? "";
        $request->flash();
        if ($search != '') {
            $show = User::where('name', 'LIKE', '%' . $search . '%')->orwhere('email', 'LIKE', '%' . $search . '%')->where('role', '<>', '0')->orderBy('id', 'DESC')->paginate(10);
        } else {
            $show = User::where('role', '<>', '0')->orderBy('id', 'DESC')->paginate(10);
        }
        $roles = Role::orderBy('id', 'DESC')->get();

        return view('Admin/user/admins', compact('show', 'roles'));
    }

    /*
    --------------------------------------------------------------------
    | Add Admin
    --------------------------------------------------------------------
     */

    public function store(Request $request)
    {
        try {
            $store           = new User;
            $store->name     = $request->name;
            $store->email    = $request->email;
            $store->password = Hash::make($request->password);
            $store->role     = 'Admin';
            $store->save();
            $store->assignRole($request->role);

            return redirect()->back()->with('success', 'Admin Added Successfully');

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Adding Error');

        }

    }

    /*
    --------------------------------------------------------------------
    | Edit Admin
    --------------------------------------------------------------------
     */

    public function edit($id)
    {
        $result = User::find($id);
        return response()->json(['show' => $result]);

    }
    public function update(Request $request)
    {
        try {
            $get        = User::find($request->id);
            $get->name  = $request->name;
            $get->email = $request->email;
            $get->update();
            return redirect()->back()->with('success', 'Admin Updated Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Updation Error');

        }

    }

    /*
    --------------------------------------------------------------------
    | Delete Admin
    --------------------------------------------------------------------
     */

    public function Delete($id)
    {
        try {
            $delete = User::find($id)->delete();
            return redirect()->back()->with('success', 'Admin Deleted Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Updation Error');

        }

    }
}
