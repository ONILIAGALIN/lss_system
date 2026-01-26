<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback_response extends Model
{
    protected $fillable = [
        'user_id',
        'question_id',
        'choice_id',
    ];

    public function user(){
    return $this->belongsTo(User::class);
    }

    public function question(){
    return $this->belongsTo(Feedback_Question::class);
    }

    public function choice(){
    return $this->belongsTo(Feedback_Choice::class);
    }
}
