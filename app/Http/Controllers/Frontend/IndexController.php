<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\User;
use App\Models\Course_goal;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon;

class IndexController extends Controller
{

    // It fetches the course, its goals, related instructor courses, categories, and related courses based on the category ID. These details are then passed to a view called 'course_details' in the frontend.
    public function CourseDetails($id,$slug){

        $course = Course::find($id);
        $goals = Course_goal::where('course_id',$id)->orderBy('id','DESC')->get();

        $ins_id = $course->instructor_id; 
        $instructorCourses = Course::where('instructor_id',$ins_id)->orderBy('id','DESC')->get();

        $categories = Category::latest()->get();

        $cat_id = $course->category_id; 
        $relatedCourses = Course::where('category_id',$cat_id)->where('id','!=',$id)->orderBy('id','DESC')->limit(3)->get();

        return view('frontend.course.course_details',compact('course','goals','instructorCourses','categories','relatedCourses'));

    } // End Method 


/* 
It queries the database to fetch courses that belong to the category with the given $id.
Filters the courses by checking that their status is set to '1'.
Retrieves the details of the category based on its ID.
Fetches a list of all categories, ordered by the latest.
*/
    public function CategoryCourse($id, $slug){

        $courses = Course::where('category_id',$id)->where('status','1')->get();
        $category = Category::where('id',$id)->first();
        $categories = Category::latest()->get();
        return view('frontend.category.category_all',compact('courses','category','categories')); 
    }// End Method 



/* 
It retrieves courses from the database where the subcategory_id matches the provided $id and the status is set to '1'.
It fetches the subcategory details based on the provided $id.
It retrieves all categories from the database, ordered by the latest.
*/

    public function SubCategoryCourse($id, $slug){

        $courses = Course::where('subcategory_id',$id)->where('status','1')->get();
        $subcategory = SubCategory::where('id',$id)->first();
        $categories = Category::latest()->get();
        return view('frontend.category.subcategory_all',compact('courses','subcategory','categories')); 
    }// End Method 



//this function serves to gather information about an instructor and their courses for display in a frontend view.
    public function InstructorDetails($id){
        $instructor = User::find($id);
        $courses = Course::where('instructor_id',$id)->get();
        return view('frontend.instructor.instructor_details',compact('instructor','courses'));

    }// End Method 
    
    

} 