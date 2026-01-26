<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback_category;
use Illuminate\Support\Facades\Validator;
class FeedbackCategoryController extends Controller
{
    public function store (Request $request){
        $validator = Validator::make($request->all(),[
            "name" => "required|string|min:6|max:255|unique:feedback_categories,name",
            "description" => "nullable|string|min:2|max:255"
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Request did not pass validation",
                "errors" => $validator->errors()
            ],400);
        }
        $validated = $validator->validated();
        $category = Feedback_category::create([
            "name" => $validated["name"],
            "description" => $validated["description"] ?? null
        ]);
        return response()->json([
            "ok" => true,
            "message" => "Feedback category created successfully",
            "data" => $category
        ],201);
    }

    public function index(){
        return response()->json([
            "ok" => true,
            "message" => "Feedback categories retrieved successfully",
            "data" => Feedback_category::all()
        ],200);
    }

    public function show ($id){
        $category = Feedback_category::find($id);
        return response()->json([
            "ok" => true,
            "message" => "Feedback category retrieved successfully",
            "data" => $category
        ],200);
    }

    public function update (Request $request, Feedback_category $category){
        $validator = Validator::make($request->all(),[
            "name" => "sometimes|string|min:6|max:255|unique:feedback_categories,name,".$category->id,
            "description" => "sometimes|string|min:2|max:255"
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Request did not pass validation",
                "errors" => $validator->errors()
            ],400);
        }
        $validated = $validator->validated();
        $category->update([
            "name" => $validated["name"],
            "description" => $validated["description"] ?? null
        ]);
        return response()->json([
            "ok" => true,
            "message" => "Feedback category updated successfully",
            "data" => $category
        ],200);
    }

    public function destroy (Feedback_category $category){
        $category->delete();
        return response()->json ([
            "ok" => true,
            "message" => "Feedback category deleted successfully"
        ],200);
    }
}