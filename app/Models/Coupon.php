<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $guarded = [];

    // this all the relations between course and Coupon >> course_id

    public function course(){
        return $this->belongsTo(Course::class, 'course_id' ,'id');
    }

    
}
