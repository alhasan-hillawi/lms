<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $guarded = [];


    // this all the relations between Review and User >> User_id

    public function user(){
        return $this->belongsTo(User::class, 'user_id' ,'id');
    }
    

        // this all the relations between Review and Course >> course_id

    public function course(){
        return $this->belongsTo(Course::class, 'course_id' ,'id');
    }


}
