<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity_log;
use Illuminate\Support\Facades\Validator;
class ActivityLogController extends Controller
{
    public function store (Request $request){
        $validator = Validator::make($request->all(), [
            "user_id" => "required|exists:users,id",
            "action" => "required|string",
            "description" => "nullable|min:1",
            "ip_address" => "nullable|string",
            "user_agent" => "nullable|min:1",
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Logs didn't pass the validations",
                "errors" => $validator->errors()
            ]);
        }

        $validated = $validator->validated();
        $logs = Activity_log::create([
            "user_id" => $validated ["user_id"],
            "action" => $validated ["action"],
            "description" => $validated ["description"] ?? null,
            "ip_address" => $validated ["ip_address"] ?? null,
            "user_agent" => $validated ["user_agent"] ?? null,
        ]);

        return response()->json([
            "ok" => true,
            "message" => "Activity logs success!",
            "data" => $logs
        ],201);
    }

    public function index (){
        return response()->json([
            "ok" => true,
            "message" => "Retrieved Activity logs success.",
            "data" => Activity_log::paginate(10)
        ]);
    }

    public function show (Request $request, Activity_log $logs){
        return response()->json([
            "ok" => true,
            "message" => "Filter Success",
            "data" => $logs
        ],200);
    }

    public function update (Request $request, Activity_log $logs){
        $validator = Validator::make($request->all(), [
            "user_id" => "sometimes|exists:users,id",
            "action" => "sometimes|string",
            "description" => "sometimes|min:1",
            "ip_address" => "sometimes|string",
            "user_agent" => "sometimes|min:1",
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Requeest to update logs didn't pass the validation",
                "errors" => $validator->errors()
            ],400);
        }

        $validated = $validator->validated();
        $logs->update([
            "user_id" => $validated ["user_id"],
            "action" => $validated ["action"],
            "description" => $validated ["description"],
            "ip_address" => $validated ["ip_address"],
            "user_agent" => $validated ["suer_agent"]
        ]);

        return response()->json([
            "ok" => true,
            "message" => "Update Successful",
            "data" => $logs
        ]);
    }

    public function destroy (Request $request, Activity_log $logs){
        $logs->delete();
        return response()->json([
            "ok" => true,
            "message" => "Logs Successfully Deleted",
        ],205);
    }
}
