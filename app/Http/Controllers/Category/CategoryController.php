<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category\CategoryModel;
use Illuminate\Http\Request;
use App\Traits\FileTraits;

class CategoryController extends Controller
{
    use FileTraits;

    public function index(Request $request)
    {
        $search = $request->search ?? "";
        $request->flash();
        if ($search != '') {
            $show = CategoryModel::where('name', 'LIKE', '%' . $search . '%')->orderBy('id', 'DESC')->paginate(10)->appends(['search' => $search]);
        } else {
            $show = CategoryModel::orderBy('id', 'DESC')->paginate(10);
        }
        return view('Admin/Category/category', compact('show'));
    }

    /*
    -------------------------------------------------
    | Store Category
    -------------------------------------------------
    */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);
            $category       = new CategoryModel();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $category->image = $this->uploadSingleImage($request->file('image'), 'images');
            }
            $category->save();
            return redirect()->back()->with('success', 'Category Created Successfully');
        } catch (\Throwable $th) {

            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    /*
    -------------------------------------------------
    | Category Status
    | 0 = Deactive
    | 1 = Active
    -------------------------------------------------
    */


    public function status($id)
    {
        try {

            $category = CategoryModel::findOrFail($id);
            if ($category->status == 1) {
                $category->status = 0;
                $category->update();
                return redirect()->back()->with('success', 'Category Deactivated Successfully');
            } else {
                $category->status = 1;
                $category->update();
                return redirect()->back()->with('success', 'Category Activated Successfully');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }


    /*
    -------------------------------------------------
    | Category Edit
    -------------------------------------------------
    */

    public function edit($id)
    {
        try {
            $category = CategoryModel::findOrFail($id);
            return response()->json($category);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    /*
    -------------------------------------------------
    | Category Update
    -------------------------------------------------
    */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);
            $category = CategoryModel::findOrFail($request->id);
            $category->name = $request->name;

            if ($request->hasFile('image')) {
                if ($category->image && file_exists(public_path('images/' . $category->image))) {
                    unlink(public_path('images/' . $category->image));
                }
                $category->image = $this->uploadSingleImage($request->file('image'), 'images/');
            }
            $category->update();
            return redirect()->back()->with('success', 'Category Updated Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    /*
    -------------------------------------------------
    | Category Delete
    
    -------------------------------------------------
    */
    public function delete($id)
    {
        try {
            $category = CategoryModel::findOrFail($id);
            if ($category->image && file_exists(public_path('images/' . $category->image))) {
                unlink(public_path('images/' . $category->image));
            }
            $category->delete();
            return redirect()->back()->with('success', 'Category Deleted Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }
}
