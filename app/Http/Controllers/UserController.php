<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends Controller
{
   public function store (Request $request){
    
       if ($request->user()->role !== 'admin') {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthorized. only Admin can Create User.'
            ], 403);
        }
    
        $validator = Validator::make($request->all(),[
            "username" => "required|unique:users,username|min:8",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:8|max:64|confirmed",
            "first_name" => "required|string|min:2|max:64",
            "middle_name" => "nullable|string|min:2|max:64",
            "last_name" => "required|string|min:2|max:64",
            "birth_date" => "sometimes|date",
            "gender" => "required|in:Male,Female,N/A"
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Validation Failded",
                "errors" => $validator->errors()
            ],400);
        }
        $user_input = $validator->safe()->only(["username","email","password"]);
        $user_input["password"] = Hash::make($user_input["password"]);
        $user_input["role"] = "user";

        $profile_input = $validator->safe()->except(["username","email","password"]);

        $user = User::create($user_input);
        $user->profile()->create($profile_input);
        $user->load('profile');

        return response()->json([
            "ok" =>true,
            "message" => "User Created Successfully",
            "data" => $user
        ],201);
    }

    public function index(Request $request){
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthorized. only Admin can view User.'
            ], 403);
        } 
        return response()->json([
          "ok" => true,
          "message" => "User Data Retrieved Successfully",
            "data" => User::paginate(10),  
        ],200);
    }

    public function show (Request $request){
        $user = User::with("profile")->get();
        return response()->json([
            "ok" => true,
            "message" => "Specific data has been Retribreved Successfully",
            "data" => $user
        ],200);
    }

    public function update (Request $request, User $user){
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthorized. only Admin can delete User.'
            ], 403);
        }
        $validator = Validator::make($request->all(),[
            "username" => "sometimes|unique:users,username,".$user->id."|min:8",
            "email" => "sometimes|email|unique:users,email,".$user->id,
            "role" => "sometimes|in:admin,user",
            "password" => "sometimes|string|min:8|max:64|confirmed",
            "first_name" => "sometimes|string|min:2|max:64",
            "middle_name" => "sometimes|nullable|string|min:2|max:64",
            "last_name" => "sometimes|string|min:2|max:64",
            "birth_date" => "sometimes|date",
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Request to Update User Failed",
                "errors" => $validator->errors()
            ],400);
        }

        $user_input = $validator->safe()->only(["username","email","password","role"]);
        if(isset($user_input["password"]) && $user_input["password"]){
            $user_input["password"] = Hash::make($user_input["password"]);
        } else{
            unset($user_input["password"]);
        }
        $profile_input = $validator->safe()->except(["username","email","password","role"]);
        $user->update($user_input);
        if ($user->profile) {
        $user->profile()->update($profile_input);
        } else {
            $user->profile()->create($profile_input);
        }
        
        return response()->json([
            "ok" => true,
            "message" => "User Updated Successfully",
            "data" => $user->load("profile")
        ],200);
    }

    public function destroy (Request $request, User $user){
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthorized. only Admin can delete User.'
            ], 403);
        }
        $user->delete();
        return response()->json([
            "ok" => true,
            "message" => "User Deleted Successfully"
        ],200);
    }

    public function exportUsersWithProfiles(){
        // $users = User::leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
        //     ->select(
        //         'users.id','users.username','users.email','users.role',
        //         'profiles.first_name','profiles.last_name',
        //         'profiles.birth_date',
        //     )
        //     ->get();
        $users = User::with('profile')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Role');
        $sheet->setCellValue('E1', 'First Name');
        $sheet->setCellValue('F1', 'Middle Name');
        $sheet->setCellValue('F1', 'Last Name');
        $sheet->setCellValue('G1', 'Birth Date');

        $row = 2;
        foreach ($users as $user) {
      
            $sheet->setCellValue('A' . $row, $user->id);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->email);
            $sheet->setCellValue('D' . $row, $user->role);
            $sheet->setCellValue('E' . $row, $user->profile?->first_name ?$user->profile?->first_name : null);
            $sheet->setCellValue('F' . $row, $user->profile?->middle_name ?$user->profile?->middle_name : null);
            $sheet->setCellValue('F' . $row, $user->profile?->last_name);
            $sheet->setCellValue('G' . $row, $user->profile?->birth_date);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="users_profiles.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output'); // Send directly to browser/Postman
        exit;
    }
}