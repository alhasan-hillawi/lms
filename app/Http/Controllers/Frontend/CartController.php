<?php

namespace App\Http\Controllers\Frontend;

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
use Illuminate\Support\Facades\Mail;
use App\Mail\Orderconfirm;
use Stripe;


class CartController extends Controller
{
 /* 

It accepts a request object and an ID representing the course to be added.
It retrieves the course details from the database using the provided ID.
If a coupon is in the session, it removes it.
It checks if the course is already in the cart.
If the course is not in the cart, it adds it.
Depending on whether the course has a discount price or not, it adds the course with either the selling price or the discount price to the cart.
 */

    public function AddToCart(Request $request, $id){

        $course = Course::find($id);

        if (Session::has('coupon')) {
            Session::forget('coupon');
        }

        // Check if the course is already in the cart
        $cartItem = Cart::search(function ($cartItem, $rowId) use ($id) {
            return $cartItem->id === $id;
        });

        if ($cartItem->isNotEmpty()) {
            return response()->json(['error' => 'Course is already in your cart']);
        }

        if ($course->discount_price == NULL) {

            Cart::add([
                'id' => $id, 
                'name' => $request->course_name, 
                'qty' => 1, 
                'price' => $course->selling_price, 
                'weight' => 1, 
                'options' => [
                    'image' => $course->course_image,
                    'slug' => $request->course_name_slug,
                    'instructor' => $request->instructor,
                ],
            ]); 

        }else{

            Cart::add([
                'id' => $id, 
                'name' => $request->course_name, 
                'qty' => 1, 
                'price' => $course->discount_price, 
                'weight' => 1, 
                'options' => [
                    'image' => $course->course_image,
                    'slug' => $request->course_name_slug,
                    'instructor' => $request->instructor,
                ],
            ]);  
        }

        return response()->json(['success' => 'Successfully Added on Your Cart']); 

    }// End Method 




//This PHP function, CartData, retrieves cart data including items, total price, and quantity, then returns it as JSON format


    public function CartData(){

        $carts = Cart::content();
        $cartTotal = Cart::total();
        $cartQty = Cart::count();

        return response()->json(array(
            'carts' => $carts,
            'cartTotal' => $cartTotal,
            'cartQty' => $cartQty,
        ));

    }// End Method 



//This PHP function, CartData, retrieves cart data including items, total price, and quantity, then returns it as JSON format


    public function AddMiniCart(){

        $carts = Cart::content();
        $cartTotal = Cart::total();
        $cartQty = Cart::count();

        return response()->json(array(
            'carts' => $carts,
            'cartTotal' => $cartTotal,
            'cartQty' => $cartQty,
        ));

    }// End Method 




    public function RemoveMiniCart($rowId){

        Cart::remove($rowId);
        return response()->json(['success' => 'Course Remove From Cart']);

    }// End Method 


// to view my Cart Page
    public function MyCart(){

        return view('frontend.mycart.view_mycart');

    } // End Method 

//It retrieves information about the items in a shopping cart, such as the cart contents ($carts), the total price ($cartTotal), and the quantity of items ($cartQty).

    public function GetCartCourse(){

        $carts = Cart::content();
        $cartTotal = Cart::total();
        $cartQty = Cart::count();

        return response()->json(array(
            'carts' => $carts,
            'cartTotal' => $cartTotal,
            'cartQty' => $cartQty,
        ));

    }// End Method 



// function CartRemove($rowId) removes an item from the cart and updates the session if a coupon is applied.

    public function CartRemove($rowId){

        Cart::remove($rowId);

        if (Session::has('coupon')) {
           $coupon_name = Session::get('coupon')['coupon_name'];
           $coupon = Coupon::where('coupon_name',$coupon_name)->first();

           Session::put('coupon',[
            'coupon_name' => $coupon->coupon_name,
            'coupon_discount' => $coupon->coupon_discount,
            'discount_amount' => round(Cart::total() * $coupon->coupon_discount/100),
            'total_amount' => round(Cart::total() - Cart::total() * $coupon->coupon_discount/100 )
        ]);

        }
        return response()->json(['success' => 'Course Remove From Cart']);

    }// End Method 



//It retrieves a coupon from the database based on the coupon name provided in the request and checks if the coupon is valid

    public function CouponApply(Request $request){

        $coupon = Coupon::where('coupon_name',$request->coupon_name)->where('coupon_validity','>=',Carbon::now()->format('Y-m-d'))->first(); 

        if ($coupon) {
            Session::put('coupon',[
                'coupon_name' => $coupon->coupon_name,
                'coupon_discount' => $coupon->coupon_discount,
                'discount_amount' => round(Cart::total() * $coupon->coupon_discount/100),
                'total_amount' => round(Cart::total() - Cart::total() * $coupon->coupon_discount/100 )
            ]);

            return response()->json(array(
                'validity' => true,
                'success' => 'Coupon Applied Successfully'
            ));
            
        }else {
            return response()->json(['error' => 'Invaild Coupon']);
        }

    }// End Method 




//It retrieves the coupon details from the database based on the provided coupon name and ensures that the coupon is valid by checking if its validity date is greater than or equal to the current date.
    public function InsCouponApply(Request $request){

        $coupon = Coupon::where('coupon_name',$request->coupon_name)->where('coupon_validity','>=',Carbon::now()->format('Y-m-d'))->first(); 

        if ($coupon) {
            if ($coupon->course_id == $request->course_id && $coupon->instructor_id == $request->instructor_id) {

                Session::put('coupon',[
                    'coupon_name' => $coupon->coupon_name,
                    'coupon_discount' => $coupon->coupon_discount,
                    'discount_amount' => round(Cart::total() * $coupon->coupon_discount/100),
                    'total_amount' => round(Cart::total() - Cart::total() * $coupon->coupon_discount/100 )
                ]);
    
                return response()->json(array(
                    'validity' => true,
                    'success' => 'Coupon Applied Successfully'
                )); 
                 
            } else {
                return response()->json(['error' => 'Coupon Criteria Not Met for this course and instructor']);
            }
        } else {
            return response()->json(['error' => 'Invalid Coupon']);
        }

    }// End Method 




//If it finds one, it returns a JSON response containing details about the subtotal, coupon name, discount, and total amount after applying the coupon.

    public function CouponCalculation(){

        if (Session::has('coupon')) {
           return response()->json(array(
            'subtotal' => Cart::total(),
            'coupon_name' => session()->get('coupon')['coupon_name'],
            'coupon_discount' => session()->get('coupon')['coupon_discount'],
            'discount_amount' => session()->get('coupon')['discount_amount'],
            'total_amount' => session()->get('coupon')['total_amount'],
           ));
        } else{
            return response()->json(array(
                'total' => Cart::total(),
            ));
        }

    }// End Method 



// to remove Coupon 
    public function CouponRemove(){

        Session::forget('coupon');
        return response()->json(['success' => 'Coupon Remove Successfully']);

    }// End Method 


/*

method checks if a user is authenticated. 
If so, it verifies if there are items in the shopping cart. 
If there are, it retrieves cart information and renders a checkout view. 
If the cart is empty, it redirects the user to the homepage with an error message. 
If the user is not authenticated, it redirects them to the login page with an error message prompting them to log in first. 

*/
    public function CheckoutCreate(){

        if (Auth::check()) {
            
            if (Cart::total() > 0) {
                $carts = Cart::content();
                $cartTotal = Cart::total();
                $cartQty = Cart::count();

                return view('frontend.checkout.checkout_view',compact('carts','cartTotal','cartQty'));
            } else{

                $notification = array(
                    'message' => 'Add At list One Course',
                    'alert-type' => 'error'
                );
                return redirect()->to('/')->with($notification); 

            }

        }else{

            $notification = array(
                'message' => 'You Need to Login First',
                'alert-type' => 'error'
            );
            return redirect()->route('login')->with($notification); 

        }

    }// End Method 





/* 


It checks if a coupon is applied and adjusts the total amount accordingly.
It collects user information and course details from the request.
If Stripe payment is selected, it redirects to the Stripe payment view.
If cash on delivery is selected:
It creates a new payment record with details like name, email, phone, address, total amount, and invoice number.
It checks if the user has already enrolled in the selected course to avoid duplicate orders.
It creates order records for each selected course, associating them with the payment record.
It removes the cart items from the session.
It sends an email confirmation to the user with order details.
It redirects the user to the index page with a success message.
Overall, the function handles both payment methods, creates payment and order records, sends email confirmations, and provides appropriate feedback to the user.


*/


    public function Payment(Request $request){

        if (Session::has('coupon')) {
           $total_amount = Session::get('coupon')['total_amount'];
        }else {
            $total_amount = round(Cart::total());
        }

            $data = array(); 
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['phone'] = $request->phone;
            $data['address'] = $request->address;
            $data['course_title'] = $request->course_title;
            $cartTotal = Cart::total();
            $carts = Cart::content();
        

        if ($request->cash_delivery == 'stripe') {
            return view('frontend.payment.stripe',compact('data','cartTotal','carts'));
        }elseif($request->cash_delivery == 'handcash'){ 

        // Cerate a new Payment Record 

        $data = new Payment();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        $data->cash_delivery = $request->cash_delivery;
        $data->total_amount = $total_amount;
        $data->payment_type = 'Direct Payment';
        
        $data->invoice_no = 'EOS' . mt_rand(10000000, 99999999);
        $data->order_date = Carbon::now()->format('d F Y');
        $data->order_month = Carbon::now()->format('F');
        $data->order_year = Carbon::now()->format('Y');
        $data->status = 'pending';
        $data->created_at = Carbon::now(); 
        $data->save();


       foreach ($request->course_title as $key => $course_title) {
        
            $existingOrder = Order::where('user_id',Auth::user()->id)->where('course_id',$request->course_id[$key])->first();

            if ($existingOrder) {

                $notification = array(
                    'message' => 'You Have already enrolled in this course',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification); 
            } // end if 

            $order = new Order();
            $order->payment_id = $data->id;
            $order->user_id = Auth::user()->id;
            $order->course_id = $request->course_id[$key];
            $order->instructor_id = $request->instructor_id[$key];
            $order->course_title = $course_title;
            $order->price = $request->price[$key];
            $order->save();

           } // end foreach 

           $request->session()->forget('cart');

           $paymentId = $data->id;

           /// Start Send email to student ///
           $sendmail = Payment::find($paymentId);
           $data = [
                'invoice_no' => $sendmail->invoice_no,
                'amount' => $total_amount,
                'name' => $sendmail->name,
                'email' => $sendmail->email,
           ];

           Mail::to($request->email)->send(new Orderconfirm($data));


           /// End Send email to student /// 

           $notification = array(
            'message' => 'Cash Payment Submit Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('index')->with($notification); 

        } // End Elseif 
           
       
    }// End Method 



////////////////////////////////////////////////

// this function need to spicial Key for every User 

    public function StripeOrder(Request $request){
        if (Session::has('coupon')) {
            $total_amount = Session::get('coupon')['total_amount'];
         }else {
             $total_amount = round(Cart::total());
         }

         \Stripe\Stripe::setApiKey('sk_test_51IUTWzALc6pn5BvMjaRW9STAvY4pLiq1dNViHoh5KtqJc9Bx7d4WKlCcEdHOJdg3gCcC2F19cDxUmCBJekGSZXte00RN2Fc4vm');

         $token = $_POST['stripeToken'];

         $charge = \Stripe\Charge::create([
            'amount' => $total_amount*100, 
            'currency' => 'usd',
            'description' => 'Lms',
            'source' => $token,
            'metadata' => ['order_id' => '3434'],
         ]); 

         $order_id = Payment::insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'total_amount' => $total_amount,
            'payment_type' => 'Stripe',
            'invoice_no' => 'EOS' . mt_rand(10000000, 99999999),
            'order_date' => Carbon::now()->format('d F Y'),
            'order_month' => Carbon::now()->format('F'),
            'order_year' => Carbon::now()->format('Y'),
            'status' => 'pending',
            'created_at' => Carbon::now(), 

         ]);

         $carts = Cart::content();
         foreach ($carts as $cart) {
            Order::insert([
                'payment_id' => $order_id,
                'user_id' => Auth::user()->id,
                'course_id' => $cart->id,
                'instructor_id' => $cart->options->instructor,
                'course_title' => $cart->options->name,
                'price' => $cart->price,
            ]);
         }// end foreach 

         if (Session::has('coupon')) {
            Session::forget('coupon');
         }
         Cart::destroy();

         $notification = array(
            'message' => 'Stripe Payment Submit Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('index')->with($notification); 

    }// End Method 

/////////////////////////////////////////////

    // function to handle the addition of courses to a shopping cart with some conditional logic based on whether the course has a discount price or not

    public function BuyToCart(Request $request, $id){

        $course = Course::find($id); 
      
        // Check if the course is already in the cart
        $cartItem = Cart::search(function ($cartItem, $rowId) use ($id) {
            return $cartItem->id === $id;
        });

        if ($cartItem->isNotEmpty()) {
            return response()->json(['error' => 'Course is already in your cart']);
        }

        if ($course->discount_price == NULL) {

            Cart::add([
                'id' => $id, 
                'name' => $request->course_name, 
                'qty' => 1, 
                'price' => $course->selling_price, 
                'weight' => 1, 
                'options' => [
                    'image' => $course->course_image,
                    'slug' => $request->course_name_slug,
                    'instructor' => $request->instructor,
                ],
            ]); 

        }else{

            Cart::add([
                'id' => $id, 
                'name' => $request->course_name, 
                'qty' => 1, 
                'price' => $course->discount_price, 
                'weight' => 1, 
                'options' => [
                    'image' => $course->course_image,
                    'slug' => $request->course_name_slug,
                    'instructor' => $request->instructor,
                ],
            ]);  
        }

        return response()->json(['success' => 'Successfully Added on Your Cart']); 

    }// End Method 





}
 