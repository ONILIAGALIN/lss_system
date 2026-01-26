<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback_response;
use Illuminate\Support\Facades\Validator;
class FeedbackResponseController extends Controller
{
    public function store (Request $request){
        $validator = Validator::make($request->all(), [
            "user_id" => "required|exists:users,id",
            "question_id" => "required|exists:questions,id",
            "choice_id" => "required|exists:choices,id"
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Request didn't pass the validation.",
                "errors" => $validator->errors()
            ],400);
        }
    }
}