<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback_choice extends Model
{
     protected $fillable = [
        'question_id',
        'label',
        'description',
        'text',
        'score',
    ];

    public function question(){
    return $this->belongsTo(Feedback_Question::class);
    }

public function responses(){
    return $this->hasMany(Feedback_Response::class);
    }

}
