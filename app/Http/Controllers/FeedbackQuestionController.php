<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback_question;
use Illuminate\Support\Facades\Validator;
class FeedbackQuestionController extends Controller

{
    public function store (Request $request){
        $validator = Validator::make($request->all(),[
            "category_id" => "required|exists:feedback_categories,id",
            "question" => "required|min:2",
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
            "question" => $validated ["question"],
            "is_active" => $validated ["is_active"]
        ]);
        return response()->json([
            "ok" => true,
            "message" => "Creating Feedback question Successfull.",
            "data" => $question
        ],201);
   }

    public function index (){
        $question = Feedback_question::with(["category"])->get();
        return response()->json([
            "ok" => true,
            "message" => "Retrieved feedback question successfull",
            "data" => $question
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
            "question" => "sometimes|min:2",
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
        $question->update([
            "category_id" => $validated ["category_id"],
            "question" => $validated ["question"],
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