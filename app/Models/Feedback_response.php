<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback_response extends Model
{
    protected $fillable = [
        'user_id',
        'question_id',
        'choice_id',
        'comment'
    ];

    public function user(){
    return $this->belongsTo(User::class, 'user_id');
    }

    public function question(){
    return $this->belongsTo(Feedback_Question::class, 'question_id');
    }

    public function choice(){
    return $this->belongsTo(Feedback_Choice::class, 'choice_id');
    }
}
