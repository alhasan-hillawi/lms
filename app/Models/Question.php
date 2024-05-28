<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $guarded = [];
    

        // this all the relations between Question and Course >> course_id

    public function course(){
        return $this->belongsTo(Course::class, 'course_id' ,'id');
    }
     // this all the relations between Question and User >> user_id

 
    public function user(){
        return $this->belongsTo(User::class, 'user_id' ,'id');
    }
    // this all the relations between Question and instructor >> instructor_id

    public function instructor(){
        return $this->belongsTo(User::class, 'instructor_id' ,'id');
    }


}