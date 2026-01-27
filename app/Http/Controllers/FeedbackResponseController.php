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
            "question_id" => "required|exists:feedback_questions,id",
            "choice_id" => "required|exists:feedback_choices,id"
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Request didn't pass the validation.",
                "errors" => $validator->errors()
            ],400);
        }

        $validated = $validator->validated();
        if(Feedback_response::where('user_id', $validated['user_id'])
            ->where('question_id', $validated['question_id'])
            ->exists()){
                return response()->json([
                    'ok' => false,
                    'message' => 'You have already answered this question.'
                ], 400);
        }
        $response = Feedback_response::create([
            "user_id" => $validated ["user_id"],
            "question_id" => $validated ["question_id"],
            "choice_id" => $validated ["choice_id"]
        ]);
        return response()->json([
            "ok" => true,
            "message" => "Feedback response created successfully",
            "data" => $response
        ],201);
    }

    public function index (Request $request){
        $perPage = $request->get('perPage', 10);
        $response = Feedback_response::with(["user","question","choice"])->paginate($perPage);
        return response()->json([
           "ok" => true,
           "message" => "Feedback response retrieved successfully",
           "data" => $response
        ],200);
    }

    public function show (Request $request, Feedback_response $response){
        return response()->json([
            "ok" => true,
            "message" => "Specific Feedback response retrieved successfully",
            "data" => $response
        ],200); 
    }

    public function update (Request $request, Feedback_response $response){
        $validator = Validator::make($request->all(),[
            "user_id" => "sometimes|exists:users,id",
            "question_id" => "sometimes|exists:questions,id",
            "choice_id" => "sometimes|exists:choices,id"
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Failed to update feedback response",
                "errors" => $validator->errors()
            ],400);
        }

        $validated = $validator->validated();
        $response->update([
            "user_id" => $validated ["user_id"],
            "question_id" => $validated ["question_id"],
            "choice_id" => $validated ["choice_id"]
        ]);
        return response()->json([
            "ok" => true,
            "message" => "Feedback response update successfully",
            "data" => $response
        ],200);
    }

    public function destroy (Request $request, Feedback_response $response){
        $response->delete();
        return response()->json([
            "ok" => true,
            "message" => "Feedback response deleted successfully"
        ],205);
    }
}