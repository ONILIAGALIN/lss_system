<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback_question;
use Illuminate\Support\Facades\Validator;
class FeedbackQuestionController extends Controller

{
    public function store (Request $request){
        $validator = Validator::make($request->all(),[
            "category_id" => "required|exist:categories,id",
            "text" => "required|min:2",
            "is_active" => "required|boolean",
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" =>false,
                "message" => "Request to create question didn't pass the validation.",
                "errors" => $validator->errors()
            ],400);
        }
        $validated = $validator->validated();
        $question = Feedback_question::create([
            "category_id" => $validated ["category_id"],
            "text" => $validated ["text"],
            "is_active" => $validated ["is_active"]
        ]);
        return response()->json([
            "ok" => true,
            "message" => "Creating Feedback question Successfull.",
            "data" => $question
        ],201);
   }

    public function index (){
        return response()->json([
            "ok" => true,
            "message" => "Retrieved feedback question successfull",
            "data" => Feedback_question::all()
        ],200);
   }

    public function show (Request $request, Feedback_question $question){
        return response()->json([
            "ok" => true,
            "message" => "Specific feedback has been retrieved",
            "data" => $question
        ],200);
   }

    public function update(Request $request, Feedback_question $question){
        $validator = Validator::make($request->all(), [
            "category_id" => "sometimes|exist:categories,id",
            "text" => "sometimes|min:2",
            "is_active" => "sometimes|boolean",
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Request to Update question didn't pass the validation.",
                "errors" => $validator->errors()
            ],400);
        }

        $validated = $validator->validated();
        $question = Feedback_question::update([
            "category_id" => $validated ["category_id"],
            "text" => $validated ["text"],
            "is_active" => $validated ["is_active"]
        ]);
        
        return response()->json([
            "ok" => true,
            "message" => "Update question successful",
            "data" => $question
        ],200);
    }

    public function destroy (Request $request, Feedback_question $question){
        $question->delete();
        return response()->json([
            "ok" => true,
            "message" => "Question successfully deleted!",
        ],200);
    }
}