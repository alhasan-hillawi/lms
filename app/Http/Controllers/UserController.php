<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{

    // It returns the Index.index view, which is a Blade template located at resources/views/frontend/index.blade.php.

    public function Index(){
        return view('frontend.index');
    } // End Method 


//Logs out the current session to clear all session data. and Redirects the user to the home page. This ensures a secure and clean logout process.

    public function UserLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }


    // It returns the user Dashboard After Login to his/her account index view, which is a Blade template located at resources/views/frontend/index.blade.php.

    public function UserDashboard(){
        return view('frontend.dashboard.index');
    }




//Finds the user record his/her ID. and This function ensures that the correct User profile data is retrieved and displayed on the profile view page.
    public function UserProfile(){

        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('frontend.dashboard.edit_profile',compact('profileData')); 
    } // End Method 


//  function updates an authenticated user's profile, including personal details and an optional profile photo. It ensures the old photo is deleted before storing the new one, then saves all updates to the database and provides a success notification to the user.

    
    public function UserProfileUpdate(Request $request){

        $id = Auth::user()->id;
        $data = User::find($id);
        $data->name = $request->name;
        $data->username = $request->username;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if ($request->file('photo')) {
           $file = $request->file('photo');
           @unlink(public_path('upload/user_images/'.$data->photo));
           $filename = date('YmdHi').$file->getClientOriginalName();
           $file->move(public_path('upload/user_images'),$filename);
           $data['photo'] = $filename; 
        }

        $data->save();

        $notification = array(
            'message' => 'User Profile Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

    }// End Method 





    public function UserChangePassword(){
        return view('frontend.dashboard.change_password');
    }// End Method 



// The UserPasswordUpdate method processes the password update and It checks if the old password matches the current password.
    //If the old password is correct, it updates the password and returns a success message; otherwise, it returns an error message.


    public function UserPasswordUpdate(Request $request){

        /// Validation 
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        if (!Hash::check($request->old_password, auth::user()->password)) {
            
            $notification = array(
                'message' => 'Old Password Does not Match!',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }

        /// Update The new Password 
        User::whereId(auth::user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $notification = array(
            'message' => 'Password Change Successfully',
            'alert-type' => 'success'
        );
        return back()->with($notification); 

    }// End Method
    
    


}
