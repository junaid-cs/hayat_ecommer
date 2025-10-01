<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request){
        $search = $request->search ?? "";
        $request->flash();
        if($search != ''){
          $show = Permission::where('name', 'LIKE', '%'.$search.'%')->orderBy('id','DESC')->paginate(10);
        }else{
        $show = Permission::orderBy('id', 'DESC')->paginate(10);
        }
        return view('Admin/user/roles/permission',compact('show'));
    }

    public function storepermissions(Request $req){
        $store = new Permission;
        $store->name = $req->name;
        $store->save();
        return redirect()->back()->with('status', 'Permission Add Successfully');
    }

    public function editpermissions($id){
        $result = Permission::find($id);
        return response()->json(['show' => $result]);

    }

    public function updatepermissions(Request $req){
    
        $store = Permission::find($req->id);
        $store->name = $req->name;
        $store->update();
        return redirect()->back()->with('status', 'Permission Updated Successfully');
    }

    public function deletepermissions($id){
        $result = Permission::find($id)->delete();
        return redirect()->back()->with('status', 'Permission Delected Successfully');


    }
}
