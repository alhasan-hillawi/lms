<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourseSection;
use App\Models\Question;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon;

class QuestionController extends Controller
{


//It inserts data into a database table called questions, 
    public function UserQuestion(Request $request){

        $course_id = $request->course_id;
        $instructor_id = $request->instructor_id;

        Question::insert([
            'course_id' => $course_id,
            'user_id' => Auth::user()->id,
            'instructor_id' => $instructor_id,
            'subject' => $request->subject,
            'question' => $request->question,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Message Send Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);  

    } // End Method 


// It view all Questions to Instuctor ,

    public function InstructorAllQuestion(){

        $id = Auth::user()->id;
        $question = Question::where('instructor_id',$id)->where('parent_id', null)->orderBy('id','desc')->get();
        return view('instructor.question.all_question',compact('question'));

    }// End Method 




//The QuestionDetails function retrieves details about a question and its replies based on the provided question ID. 
    public function QuestionDetails($id){

        $question = Question::find($id);
        $replay = Question::where('parent_id',$id)->orderBy('id','asc')->get();
        return view('instructor.question.question_details',compact('question','replay'));

    }// End Method 



// this function is responsible for saving a user's question to the database and then redirecting the user with a success message.
    public function InstructorReplay(Request $request){

        $que_id = $request->qid;
        $user_id = $request->user_id;
        $course_id = $request->course_id;
        $instructor_id = $request->instructor_id;

        Question::insert([
            'course_id' => $course_id,
            'user_id' => $user_id,
            'instructor_id' => $instructor_id,
            'parent_id' => $que_id,
            'question' => $request->question,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Message Send Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('instructor.all.question')->with($notification); 


    }// End Method 



}