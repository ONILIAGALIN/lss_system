<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback_choice;
use Illuminate\Support\Facades\Validator;
class FeedbackChoiceController extends Controller
{
    public function store (Request $request){
        $validator = Validator::make($request->all(), [
            "question_id" => "required|exists:questions,id",
            "label" => "required|in:A,B,C,D,E,N/A",
            "description" => "required|string|min:2",
            "value" => "required|integer",
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Request didn't pass the validation!",
                "errors" => $validator->errors()
            ],400);
        }

        $validated = $validator->validated();
        $choices = Feedback_choice::create([
            "question_id" => $validated ["question_id"],
            "label" => $validated ["label"],
            "description" => $validated ["description"],
            "value" => $validated ["value"]
       ]);

       return response()->json([
            "ok" => true,
            "message" => "Feedback choices has been Created!",
            "data" => $choices
       ]);
    }

    public function index (){
        return response()->json([
            "ok" => true,
            "message" => "Feedback question has been retrieved!",
            "data" => Feedback_choice::All()
        ],200);
    }
}
