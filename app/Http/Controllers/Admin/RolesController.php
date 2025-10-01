<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ?? "";
        $request->flash();
        if ($search != '') {
            $show = Role::where('name', 'LIKE', '%' . $search . '%')->orderBy('id', 'DESC')->paginate(10)->appends(['search' => $search]);
        } else {
            $show = Role::orderBy('id', 'DESC')->paginate(10);
        }
        return view('Admin/user/roles/roles', compact('show'));
    }

    public function storeroles(Request $req)
    {
        $store       = new Role;
        $store->name = $req->name;
        $store->save();
        return redirect()->back()->with('status', 'Role Add Successfully');
    }

    public function editroles($id)
    {
        $result = Role::find($id);
        return response()->json(['show' => $result]);

    }

    /*
    --------------------------------------------------------------------
    | Assign Permission
    --------------------------------------------------------------------
    */

    public function accesscontrols($id)
    {
        $role       = Role::find($id);
        $permission = Permission::get();

        return view('Admin/user/roles/assignroles', compact('role', 'permission'));

    }

    public function rolehavepermissions(Request $req)
    {
        $assign      = [];
        $permissions = $req->input('permission');
        $roleid      = $req->roleid;

        foreach ($permissions as $key => $value) {
            $assign[] = $value;
        }
        $role = Role::find($roleid);
        foreach ($assign as $key => $permission) {
            if ($role->hasPermissionTo($permission)) {

            } else {

                $role->givePermissionTo($permission);

            }

        }
        return redirect()->back()->with('status', 'Permission Assign');
    }

    /*
    --------------------------------------------------------------------
    | Delete Assign Permission
    --------------------------------------------------------------------
    */

    public function deleteassigns($permission_id, $role_id)
    {
        try {
            $role = Role::find($role_id);

            if ($role->hasPermissionTo($permission_id)) {
                $role->revokePermissionTo($permission_id);
                return redirect()->back()->with('status', 'Permission Deleted');
    
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', 'Permission Not Deleted');
        }
       

    }

    /*
    --------------------------------------------------------------------
    | Update Roles
    --------------------------------------------------------------------
    */

    public function updateroles(Request $req)
    {
        try {
            $store       = Role::find($req->id);
            $store->name = $req->name;
            $store->update();
            return redirect()->back()->with('status', 'Role Updated Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', 'Role Not Updated');

        }
    }

    /*
    --------------------------------------------------------------------
    | Delete Roles
    --------------------------------------------------------------------
    */

    public function deleteroles($id)
    {
        try {
            $role = Role::find($id);
            $role->delete();
            return redirect()->back()->with('status', 'Role Deleted Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', 'Role Not Deleted');
        }

    }
}
