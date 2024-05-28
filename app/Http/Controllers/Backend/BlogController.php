<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class BlogController extends Controller
{

//to fetches the latest blog categories from the database and passes these categories to a specific view (blog_category)
   public function AllBlogCategory(){

    $category = BlogCategory::latest()->get();
    return view('admin.backend.blogcategory.blog_category',compact('category'));

   }// End Method 



//The StoreBlogCategory method to Handles the request to create a new blog category.

   public function StoreBlogCategory(Request $request){

    BlogCategory::insert([
        'category_name' => $request->category_name,
        'category_slug' => strtolower(str_replace(' ','-',$request->category_name)),
    ]);

    $notification = array(
        'message' => 'BlogCategory Inserted Successfully',
        'alert-type' => 'success'
    );
    return redirect()->back()->with($notification);


   }// End Method 




// EditBlogCategory functions Retrieves the corresponding blog category from the database using the ID and returns the category data as a JSON response, 
   public function EditBlogCategory($id){

    $categories = BlogCategory::find($id);
    return response()->json($categories);

   }// End Method 



// Retrieves the category by its ID from the request . Updates the category's name and slug in the database. Sets up a success notification message. Redirects the user back to the previous page with the success notification.

   public function UpdateBlogCategory(Request $request){
    $cat_id = $request->cat_id;

    BlogCategory::find($cat_id)->update([
        'category_name' => $request->category_name,
        'category_slug' => strtolower(str_replace(' ','-',$request->category_name)),
    ]);

    $notification = array(
        'message' => 'BlogCategory Updated Successfully',
        'alert-type' => 'success'
    );
    return redirect()->back()->with($notification);


   }// End Method 




// to delete the DeleteBlogCategory by its ID from the request

   public function DeleteBlogCategory($id){
   
    BlogCategory::find($id)->delete();

    $notification = array(
        'message' => 'BlogCategory Deleted Successfully',
        'alert-type' => 'success'
    );
    return redirect()->back()->with($notification);


   }// End Method 




   //////////// All Blog Post Method .//
// Get all posts

   public function BlogPost(){
    $post = BlogPost::latest()->get();
    return view('admin.backend.post.all_post',compact('post'));
   }// End Method 


// and blog post  
   public function AddBlogPost(){

    $blogcat = BlogCategory::latest()->get();
    return view('admin.backend.post.add_post',compact('blogcat'));

   }// End Method 

// 


//Inserts the blog post details into the BlogPost database table, including the image URL, and generates a slug for the post title.

   public function StoreBlogPost(Request $request){

    $image = $request->file('post_image');  
    $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
    Image::make($image)->resize(370,247)->save('upload/post/'.$name_gen);
    $save_url = 'upload/post/'.$name_gen;

    BlogPost::insert([
        'blogcat_id' => $request->blogcat_id,
        'post_title' => $request->post_title,
        'post_slug' => strtolower(str_replace(' ','-',$request->post_title)),
        'long_descp' => $request->long_descp,
        'post_tags' => $request->post_tags,
        'post_image' => $save_url,  
        'created_at' => Carbon::now(),      

    ]);

    $notification = array(
        'message' => 'Blog Post Inserted Successfully',
        'alert-type' => 'success'
    );
    return redirect()->route('blog.post')->with($notification);  

   }// End Method 



//Fetches the latest blog categories. and Finds the blog post by its ID. returns a view for editing the blog post, passing the post and blog categories data to the view.


   public function EditBlogPost($id){

    $blogcat = BlogCategory::latest()->get();
    $post = BlogPost::find($id);
    return view('admin.backend.post.edit_post',compact('post','blogcat'));

   }// End Method 


// Updates the blog post in the database with the new image URL and other details. and Redirects the user to the blog post list page with the notification message.

   public function UpdateBlogPost(Request $request){
        
    $post_id = $request->id;

    if ($request->file('post_image')) {

        $image = $request->file('post_image');  
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        Image::make($image)->resize(370,247)->save('upload/post/'.$name_gen);
        $save_url = 'upload/post/'.$name_gen;
    
        BlogPost::find($post_id)->update([
            'blogcat_id' => $request->blogcat_id,
            'post_title' => $request->post_title,
            'post_slug' => strtolower(str_replace(' ','-',$request->post_title)),
            'long_descp' => $request->long_descp,
            'post_tags' => $request->post_tags,
            'post_image' => $save_url,  
            'created_at' => Carbon::now(),      
    
        ]);
    
        $notification = array(
            'message' => 'Blog Post Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('blog.post')->with($notification);  
    
    } else {

        BlogPost::find($post_id)->update([
            'blogcat_id' => $request->blogcat_id,
            'post_title' => $request->post_title,
            'post_slug' => strtolower(str_replace(' ','-',$request->post_title)),
            'long_descp' => $request->long_descp,
            'post_tags' => $request->post_tags, 
            'created_at' => Carbon::now(),      
    
        ]);
    
        $notification = array(
            'message' => 'Blog Post Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('blog.post')->with($notification);  

    } // end else 

}// End Method 


// to delete the DeleteBlogPost by its ID from the request

public function DeleteBlogPost($id){

    $item = BlogPost::find($id);
    $img = $item->post_image;
    unlink($img);

    BlogPost::find($id)->delete();

        $notification = array(
            'message' => 'Blog Post Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

}// End Method 


/* Retrieves the blog post using the slug.
Extracts and splits the tags associated with the blog post into an array.
Fetches the latest blog categories.
Fetches the latest three blog posts.
Passes the retrieved data to the frontend.blog.blog_details view for rendering. */
public function BlogDetails($slug){

    $blog = BlogPost::where('post_slug',$slug)->first();
    $tags = $blog->post_tags;
    $tags_all = explode(',',$tags);
    $bcategory = BlogCategory::latest()->get();
    $post = BlogPost::latest()->limit(3)->get();
    return view('frontend.blog.blog_details',compact('blog','tags_all','bcategory','post'));

}// End Method 


//Fetches all blog posts that belong to a specified category ($id). Gets the latest blog categories. Retrieves the latest three blog posts.

public function BlogCatList($id){

    $blog = BlogPost::where('blogcat_id',$id)->get();
    $breadcat = BlogCategory::where('id',$id)->first();
    $bcategory = BlogCategory::latest()->get();
    $post = BlogPost::latest()->limit(3)->get();
    return view('frontend.blog.blog_cat_list',compact('blog','breadcat','bcategory','post'));

}// End Method 

//The BlogList function retrieves and prepares data for the blog list page. and  Fetches all blog posts, ordered by the latest. Gets the latest blog categories  and Retrieves the latest three blog posts.

public function BlogList(){

    $blog = BlogPost::latest()->get();
    $bcategory = BlogCategory::latest()->get();
    $post = BlogPost::latest()->limit(3)->get();
    return view('frontend.blog.blog_list',compact('blog','bcategory','post'));


}// End Method 


} 