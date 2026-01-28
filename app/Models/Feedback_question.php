<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback_question extends Model
{
    
    protected $fillable = [
        'category_id',
        'question',
        'is_active',
    ];

    public function category(){
    return $this->belongsTo(Feedback_Category::class, 'category_id');
    }

    public function choices(){
        return $this->hasMany(Feedback_Choice::class, 'question_id');
    }

    public function responses(){
        return $this->hasMany(Feedback_Response::class, 'question_id');
    }

}
