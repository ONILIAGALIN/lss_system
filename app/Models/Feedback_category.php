<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback_category extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function questions(){
    return $this->hasMany(Feedback_Question::class, 'category_id');
    }

}
