<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\User;
use App\Models\Wishlist; 

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon;

class WishListController extends Controller
{

// If the course already exists in the wishlist, it returns an error message. If the user is not authenticated, it returns an error prompting the user to log in.

    public function AddToWishList(Request $request, $course_id){

        if (Auth::check()) {
           $exists = Wishlist::where('user_id',Auth::id())->where('course_id',$course_id)->first();

           if (!$exists) {
            Wishlist::insert([
                'user_id' => Auth::id(),
                'course_id' => $course_id,
                'created_at' => Carbon::now(),
            ]);
            return response()->json(['success' => 'Successfully Added on your Wishlist']);
           }else {
            return response()->json(['error' => 'This Product Has Already on your withlist']);
           }
  
        }else{
            return response()->json(['error' => 'At First Login Your Account']);
        } 

    } // End Method 




// It likely displays a page showing all items in a user's wishlist. 

    public function AllWishlist(){

        return view('frontend.wishlist.all_wishlist');

    }// End Method 


// retrieves a user's wishlist of courses.retrieves them along with their corresponding course details. It also counts the total number of items in the wishlist. 


    public function GetWishlistCourse(){

        $wishlist = Wishlist::with('course')->where('user_id',Auth::id())->latest()->get();

        $wishQty = Wishlist::count();

        return response()->json(['wishlist' => $wishlist, 'wishQty' => $wishQty]);

    }// End Method 


// to Remove Whislist 
    public function RemoveWishlist($id){

        Wishlist::where('user_id',Auth::id())->where('id',$id)->delete();
        return response()->json(['success' => 'Successfully Course Remove']);

    }// End Method 




}