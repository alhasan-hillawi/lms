<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Intervention\Image\Facades\Image;
use App\Models\Course;


class AdminController extends Controller
{

// It returns the admin.index view, which is a Blade template located at resources/views/admin/index.blade.php.

    public function AdminDashboard(){
        return view('admin.index');
    }



//Logs out the current session to clear all session data. and Redirects the user to the home page. This ensures a secure and clean logout process.

    public function AdminLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }



//Finds the user record his/her ID. and This function ensures that the correct User profile data is retrieved and displayed on the profile view page.
    public function AdminProfile(){

        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin_profile_view',compact('profileData'));
    }

//  function updates an authenticated Admin's profile, including personal details and an optional profile photo. It ensures the old photo is deleted before storing the new one, then saves all updates to the database and provides a success notification to the user.
    public function  AdminProfileStore(Request $request)
    {
        $id = Auth::user()->id;
        $data = User::find($id);


        $data->username = $request->username;
        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if($request->file('photo')){
            $file = $request->file('photo');
            @unlink(public_path('upload/admin_images/').$data->photo);
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/admin_images/'),$filename);
            $data['photo']= $filename;
        }
            $data->save();
            $notification = array(
                'message' => ' Profile Updated Successfully',
                'alert-type' => 'success',

            );
            return redirect()->back()->with($notification);


    }// End Method 


// get info User and View Change password Page with user Infomations Data

    public function AdminChangePassword(){

        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin_change_password',compact('profileData'));

    }// End Method


// The AdminPasswordUpdate method processes the password update and It checks if the old password matches the current password.
    //If the old password is correct, it updates the password and returns a success message; otherwise, it returns an error message.

    public function AdminPasswordUpdate(Request $request){

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
    

// GET all Instructor Users form Database and Shows in this root  resources/views/admin/backend/instructor/all_instructor.blade.php.
    public function AllInstructor(){

        $allinstructor = User::where('role','instructor')->latest()->get();
        return view('admin.backend.instructor.all_instructor',compact('allinstructor'));
    }// End Method



//  this function to change User status 0 is Inactive and 1 is Active 

    public function UpdateUserStatus(Request $request){

        $userId = $request->input('user_id');
        $isChecked = $request->input('is_checked',0);

        $user = User::find($userId);
        if ($user) {
            $user->status = $isChecked;
            $user->save();
        }

        return response()->json(['message' => 'User Status Updated Successfully']);

    }// End Method




// View All Cousese Table in Admin Dashbaord with detalis 
    public function AdminAllCourse(){

        $course = Course::latest()->get();
        return view('admin.backend.courses.all_course',compact('course'));

    }// End Method

    

// to Change Couses Status 0 to 1 or 1 to 0  ,, Admin allow to change Course Status 
    public function UpdateCourseStatus(Request $request){

        $courseId = $request->input('course_id');
        $isChecked = $request->input('is_checked',0);

        $course = Course::find($courseId);
        if ($course) {
            $course->status = $isChecked;
            $course->save();
        }

        return response()->json(['message' => 'Course Status Updated Successfully']);

    }// End Method


// to get all course information and send the deatlis for spsific course to this root resources/views/admin/backend/courses course_details.blade.php.

    public function AdminCourseDetails($id){

        $course = Course::find($id);
        return view('admin.backend.courses.course_details',compact('course'));

    }// End Method




}
