<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\Course_goal;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Coupon;
use Illuminate\Support\Facades\Session;
use App\Models\Payment;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Question;

class OrderController extends Controller
{


// This method fetches pending payment orders from a database using the Payment model, where the status is set as 'pending'. 

    public function AdminPendingOrder(){

        $payment = Payment::where('status','pending')->orderBy('id','DESC')->get();
        return view('admin.backend.orders.pending_orders',compact('payment'));

    } // End Method 
    


// The function assumes that there is a Payment model and an Order model with appropriate database tables.

    public function AdminOrderDetails($payment_id){

        $payment = Payment::where('id',$payment_id)->first();
        $orderItem = Order::where('payment_id',$payment_id)->orderBy('id','DESC')->get();

        return view('admin.backend.orders.admin_order_details',compact('payment','orderItem'));

    }// End Method 



/* It updates the status of the payment to 'confirm'.
It creates a notification message array indicating the success of the order confirmation. */

    public function PendingToConfirm($payment_id){
        Payment::find($payment_id)->update(['status' => 'confirm']);

        $notification = array(
            'message' => 'Order Confrim Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('admin.confirm.order')->with($notification);  


    }// End Method 



/* 
It queries the Payment model to retrieve records where the status is "confirm".
The retrieved payments are ordered by their IDs in descending order.
*/
    public function AdminConfirmOrder(){

        $payment = Payment::where('status','confirm')->orderBy('id','DESC')->get();
        return view('admin.backend.orders.confirm_orders',compact('payment'));

    } // End Method 



// The InstructorAllOrder function retrieves the latest order items associated with the currently authenticated instructor. It first retrieves the instructor's ID from the authenticated user
    public function InstructorAllOrder(){

        $id = Auth::user()->id;

        $latestOrderItem = Order::where('instructor_id',$id)->select('payment_id', \DB::raw('MAX(id) as max_id'))->groupBy('payment_id');

        $orderItem = Order::joinSub($latestOrderItem, 'latest_order', function($join) {
            $join->on('orders.id', '=', 'latest_order.max_id');
        })->orderBy('latest_order.max_id','DESC')->get();
        
        return view('instructor.orders.all_orders',compact('orderItem'));

    }// End Method 




//The InstructorOrderDetails function retrieves the details of a specific order identified by its payment ID.

    public function InstructorOrderDetails($payment_id){

        $payment = Payment::where('id',$payment_id)->first();
        $orderItem = Order::where('payment_id',$payment_id)->orderBy('id','DESC')->get();

        return view('instructor.orders.instructor_order_details',compact('payment','orderItem'));

    }// End Method 




/* 
It retrieves payment information based on the provided payment ID using the Payment model.
It fetches order items associated with the same payment ID from the Order model, sorting them by ID in descending order.
*/
    public function InstructorOrderInvoice($payment_id){

        $payment = Payment::where('id',$payment_id)->first();
        $orderItem = Order::where('payment_id',$payment_id)->orderBy('id','DESC')->get();

        $pdf = Pdf::loadView('instructor.orders.order_pdf',compact('payment','orderItem'))->setPaper('a4')->setOption([
            'tempDir' => public_path(),
            'chroot' => public_path(),
        ]);
        return $pdf->download('invoice.pdf');

    }// End Method 




// the method is used to fetch and display a user's latest orders for each course they've purchased.
    public function MyCourse(){
        $id = Auth::user()->id;

        $latestOrders = Order::where('user_id',$id)->select('course_id', \DB::raw('MAX(id) as max_id'))->groupBy('course_id');

        $mycourse = Order::joinSub($latestOrders, 'latest_order', function($join) {
            $join->on('orders.id', '=', 'latest_order.max_id');
        })->orderBy('latest_order.max_id','DESC')->get();
        
        return view('frontend.mycourse.my_all_course',compact('mycourse'));

    }// End Method 




// this function essentially prepares the necessary data for rendering a course view page, including course details, sections, and questions.
  
    public function CourseView($course_id){
        $id = Auth::user()->id;

        $course = Order::where('course_id',$course_id)->where('user_id',$id)->first();
        $section = CourseSection::where('course_id',$course_id)->orderBy('id','asc')->get();

        $allquestion = Question::latest()->get();

        return view('frontend.mycourse.course_view',compact('course','section','allquestion'));


    }// End Method 




} 