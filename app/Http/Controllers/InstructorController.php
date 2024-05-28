<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Intervention\Image\Facades\Image;


class InstructorController extends Controller
{
    // It returns the instructor.index view, which is a Blade template located at resources/views/instrctor/index.blade.php.
    public function InstructorDashboard(){
        return view('instructor.index');
    }


//Logs out the current session to clear all session data. and Redirects the user to the home page. This ensures a secure and clean logout process.
    public function InstructorLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }


//Finds the user record his/her ID. and This function ensures that the correct User profile data is retrieved and displayed on the profile view page.

    public function InstructorProfile(){

        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('instructor.instructor_profile_view',compact('profileData'));
    }


//  function updates an authenticated instructor's profile, including personal details and an optional profile photo. It ensures the old photo is deleted before storing the new one, then saves all updates to the database and provides a success notification to the user.

    public function  InstructorProfileStore(Request $request)
    {
        $id = Auth::user()->id;
        $data = User::find($id);


        $data->username = $request->username;
        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if($request->file('photo')){
            $file = $request->file('photo');
            @unlink(public_path('upload/instructor_images/').$data->photo);
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/instructor_images/'),$filename);
            $data['photo']= $filename;
        }
            $data->save();
            $notification = array(
                'message' => ' Profile Updated Successfully',
                'alert-type' => 'success',

            );
            return redirect()->back()->with($notification);


    }// End Method 



// get info instructor  and View Change password Page with user Infomations Data

    public function InstructorChangePassword(){

        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('instructor.instructor_change_password',compact('profileData'));

    }// End Method



// The InstructorPasswordUpdate method processes the password update and It checks if the old password matches the current password.
    //If the old password is correct, it updates the password and returns a success message; otherwise, it returns an error message.




    public function InstructorPasswordUpdate(Request $request){

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

