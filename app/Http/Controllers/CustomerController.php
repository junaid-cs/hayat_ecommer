<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer\Profile\ProfileModel;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ?? "";
        $request->flash();
        if ($search != '') {
            $show = User::where('name', 'LIKE', '%' . $search . '%')->orwhere('email', 'LIKE', '%' . $search . '%')->where('role', '0')->orderBy('id', 'DESC')->paginate(10);
        } else {
            $show = User::where('role', '0')->orderBy('id', 'DESC')->paginate(10);
        }
        return view('Admin/user/customers', compact('show'));
    }

    /*
    --------------------------------------------------------------------
    | Update Customer
    --------------------------------------------------------------------  
     */

    public function update(Request $request)
    {
        try {
            $get        = User::find($request->id);
            $get->name  = $request->name;
            $get->email = $request->email;
            $get->update();
            return redirect()->back()->with('success', 'Customer Updated Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Updation Error');

        }

    }


    /*
    --------------------------------------------------------------------
    | View Customer
    --------------------------------------------------------------------  
     */
    public function view($id)
    {
        try {
            $customer = User::with('Profile_Information')->findorfail($id);
            return response()->json([
                'html' => view('Admin/user/customer_view', compact('customer'))->render(),
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Customer not found.');
        }
       
    }

    /*
    --------------------------------------------------------------------
    | Accept Customer
    --------------------------------------------------------------------  
     */ 
    public function accept($id)
    {
        try {
            $customer = ProfileModel::where('customer',$id)->first();
            if ($customer->status == 1) {
                return redirect()->back()->with('info', 'Customer Already Accepted');
            }
            else {
                $customer->status = 1;
                $customer->update();
                return redirect()->back()->with('success', 'Customer Accepted Successfully');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Customer not found.');
        }
    }

    /*
    --------------------------------------------------------------------
    | Reject Customer
    --------------------------------------------------------------------  
     */
    public function reject($id)
    {
        try {
            $customer = ProfileModel::where('customer',$id)->first();
            if ($customer->status == 2) {
                return redirect()->back()->with('info', 'Customer Already Rejected');
            }
            else {
                $customer->status = 2;
                $customer->update();
                return redirect()->back()->with('success', 'Customer Rejected Successfully');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Customer not found.');
        }
    }

    /*
    --------------------------------------------------------------------
    | Delete Customer
    --------------------------------------------------------------------  
     */

    public function DeleteUser($id)
    {
        try {
            $customer = User::find($id);
            if ($customer) {
                $profile = ProfileModel::where('customer', $id)->first();
                if ($profile) {
                    $profile->delete();
                }
                $customer->delete();
                return redirect()->back()->with('success', 'Customer Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Customer not found.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Customer not found.');
        }
    }
}
