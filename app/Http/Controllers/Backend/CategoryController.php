<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{


        ////////// All SubCategory Methods //////////////

// to get all Category table form database 
    public function AllCategory(){

        $category = Category::latest()->get();
        return view('admin.backend.category.all_category',compact('category'));

    }// End Method 




// This method handles the storage of a new category.

    public function AddCategory(){
        return view('admin.backend.category.add_category');
    }// End Method 

    public function StoreCategory(Request $request){

        $image = $request->file('image');  
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        Image::make($image)->resize(370,246)->save('upload/category/'.$name_gen);
        $save_url = 'upload/category/'.$name_gen;

        Category::insert([
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ','-',$request->category_name)),
            'image' => $save_url,        

        ]);

        $notification = array(
            'message' => 'Category Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.category')->with($notification);  

    }// End Method 




// This method is responsible for retrieving the details of a specific category for editing 

    public function EditCategory($id){

        $category = Category::find($id);
        return view('admin.backend.category.edit_category',compact('category'));
    }// End Method 


/* 
This PHP function is designed to update a category
It accepts a request object and retrieves the category ID from it. 
If an image file is uploaded along with the request, it processes the image, saves it to a specified directory, 
If no image is uploaded, it updates only the textual details of the category without modifying the image. 
Finally, it redirects the user to a specified route with a success message notifying whether the update was successful with or without an image.
*/
    public function UpdateCategory(Request $request){
        
        $cat_id = $request->id;

        if ($request->file('image')) {

            $image = $request->file('image');  
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            Image::make($image)->resize(370,246)->save('upload/category/'.$name_gen);
            $save_url = 'upload/category/'.$name_gen;
    
            Category::find($cat_id)->update([
                'category_name' => $request->category_name,
                'category_slug' => strtolower(str_replace(' ','-',$request->category_name)),
                'image' => $save_url,        
    
            ]);
    
            $notification = array(
                'message' => 'Category Updated with image Successfully',
                'alert-type' => 'success'
            );
            return redirect()->route('all.category')->with($notification);  
        
        } else {

            Category::find($cat_id)->update([
                'category_name' => $request->category_name,
                'category_slug' => strtolower(str_replace(' ','-',$request->category_name)),  
    
            ]);
    
            $notification = array(
                'message' => 'Category Updated without image Successfully',
                'alert-type' => 'success'
            );
            return redirect()->route('all.category')->with($notification);

        } // end else 

    }// End Method 




// to delete the DeleteCategory by its ID from the request


    public function DeleteCategory($id){

        $item = Category::find($id);
        $img = $item->image;
        unlink($img);

        Category::find($id)->delete();

            $notification = array(
                'message' => 'Category Deleted Successfully',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);

    }// End Method 




    ////////// All SubCategory Methods //////////////
//retrieves all subcategories from the database,
    public function AllSubCategory(){

        $subcategory = SubCategory::latest()->get();
        return view('admin.backend.subcategory.all_subcategory',compact('subcategory'));

    }// End Method 



// Add New SubCategory

    public function AddSubCategory(){

        $category = Category::latest()->get();
        return view('admin.backend.subcategory.add_subcategory',compact('category'));

    }// End Method 





//This function encapsulates the process of storing a new subcategory .
    public function StoreSubCategory(Request $request){ 

        SubCategory::insert([
            'category_id' => $request->category_id,
            'subcategory_name' => $request->subcategory_name,
            'subcategory_slug' => strtolower(str_replace(' ','-',$request->subcategory_name)), 

        ]);

        $notification = array(
            'message' => 'SubCategory Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.subcategory')->with($notification);  

    }// End Method 






//This PHP method EditSubCategory retrieves data necessary for editing a subcategory with a given ID
    public function EditSubCategory($id){

        $category = Category::latest()->get();
        $subcategory = SubCategory::find($id);
        return view('admin.backend.subcategory.edit_subcategory',compact('category','subcategory'));

    }// End Method




//UpdateSubCategory takes a request object as input, presumably from a form submission. It updates a SubCategory model instance in the database based on the provided request data.
    public function UpdateSubCategory(Request $request){ 

        $subcat_id = $request->id;

        SubCategory::find($subcat_id)->update([
            'category_id' => $request->category_id,
            'subcategory_name' => $request->subcategory_name,
            'subcategory_slug' => strtolower(str_replace(' ','-',$request->subcategory_name)), 

        ]);

        $notification = array(
            'message' => 'SubCategory Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.subcategory')->with($notification);  

    }// End Method 



// to delete the DeleteSubCategory by its ID from the request
    public function DeleteSubCategory($id){

        SubCategory::find($id)->delete();

        $notification = array(
            'message' => 'SubCategory Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

    }// End Method 




} 