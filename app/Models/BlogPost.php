<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;
    protected $guarded = [];

    // this all the relations between BlogPost and BlogCategory >> blogcat_id

    public function blog(){
        return $this->belongsTo(BlogCategory::class, 'blogcat_id' ,'id');
    }

    
}
