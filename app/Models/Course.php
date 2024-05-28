<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



//this all the relations between tables
class Course extends Model
{
    use HasFactory;
    protected $guarded = [];



// this all the relations between Course and category >> category_id

    public function category(){
        return $this->belongsTo(Category::class, 'category_id' ,'id');
    }


    // this all the relations between Course and subcategory >> subcategory_id


    public function subcategory(){
        return $this->belongsTo(SubCategory::class, 'subcategory_id' ,'id');
    }
    
        // this all the relations between Course and user >> instructor_id

    public function user(){
        return $this->belongsTo(User::class, 'instructor_id' ,'id');
    }
    
}
