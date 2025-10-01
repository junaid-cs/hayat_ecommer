<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Rating\RatingModel;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;


class RatingController extends Controller
{
    // store rating
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);
        $rating = new RatingModel();
        $rating->user_id = Auth::user()->id;
        $rating->product_id = $request->product_id;
        $rating->rating = $request->rating;
        $rating->review = $request->review;
        $rating->save();
        return redirect()->back()->with('success','Product Review Added Successfully');
    }
    
}
