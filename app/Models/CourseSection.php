<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    use HasFactory;
    protected $guarded = [];

    // this all the relations between lectures and CourseSection >> section_id

    public function lectures(){
        return $this->hasMany(CourseLecture::class, 'section_id');
    }
}
