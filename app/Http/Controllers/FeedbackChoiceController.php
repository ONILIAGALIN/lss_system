<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback_choice;
use Illuminate\Support\Facades\Validator;
class FeedbackChoiceController extends Controller
{
    public function store (Request $request){
        $validator = Validator::make($request->all(), [
            "question_id" => "required|exists:feedback_questions,id",
            "label" => "required|in:A,B,C,D,E,N/A",
            "description" => "required|string|min:2",
            "value" => "required|integer|min:1|max:5",
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
        $choices = Feedback_choice::with(["question"])->get();
        return response()->json([
            "ok" => true,
            "message" => "Feedback question has been retrieved!",
            "data" => $choices
        ],200);
    }

    public function show (Request $request, Feedback_choice $choices){
        return response()->json([
            "ok" => true,
            "message" => "Specific Feedback question retrieved successfully",
            "data" => $choices
        ]);
    }

    public function update (Request $request, Feedback_choice $choices){
        $validator = Validator::make($request->all(), [
            "question_id" => "sometimes|exists:questions,id",
            "label" => "sometimes|in:A,B,C,D,E,N/A",
            "description" => "sometimes|string|min:2",
            "value" => "sometimes|integer",
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Update Feedback choices didn't pass the validation",
                "errors" => $validator->errors()
            ],400);
        }
        $validated = $validator->validated();
        $choices->update([
            "question_id" => $validated ["quetion_id"],
            "label" => $validated ["label"],
            "description" => $validated ["description"],
            "value" => $validated ["value"]
        ]);

        return response()->json([
            "ok" => true,
            "message" => "Update Feedback Choices Successfully applied",
            "data" => $choices
        ],200);
    }

    public function destroy (Request $request, Feedback_choice $choices){
        $choices->delete();
        return response()->json([
            "ok" => true,
            "message" => "Feedback choices successfully deleted."
        ],205);
    }
}
