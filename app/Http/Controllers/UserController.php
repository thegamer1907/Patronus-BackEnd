<?php

namespace App\Http\Controllers;
use http\Env\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\SendMail;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function check()
    {
        return response()->json(["Key" => "API works"],200);
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(),[
           'email' => 'required|unique:users',
            'password' => 'required|min:4',
            'name' => 'required',
            'govt_id' => 'required',
            'govt_id_type' => 'required|in:Driving License,Aadhar,Pan Card',
            'address' => 'required|max:180'
        ]);

        if ($validator->fails()) {

            return response()->json(["success" => false, "error" =>$validator->errors()->first()],400);
        }


        $user = new User();
        $user->create($request->only($user->getFillable()));
        $user1 = User::whereEmail($request->email)->firstorfail();
        $role = Role::whereName('user')->firstorfail();
        $user1->roles()->attach($role->id);
        return response()->json(["success" => true,"message" => "User created Successfully"],200);
    }

    public function changepassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required',
            'oldpassword' => 'required',
            'newpassword' => 'required|min:4'
        ]);

        if ($validator->fails()) {

            return response()->json(["success" => false, "error" =>$validator->errors()->first()],400);
        }

        $user = User::whereEmail($request->email)->wherePassword($request->oldpassword)->first();
        if(!$user)
            return response()->json(['error' => 'invalid_credentials'], 401);
        $done = User::whereEmail($request->email)->update(['password' => $request->newpassword]);
        return response()->json(["success" => true, "message" =>"Password Changed Successfully"],200);
    }

    public function forgot(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(["success" => false, "error" =>$validator->errors()->first()],400);
        }

        $random_password = str_random(8);

        $status = SendMail::send($request->email,"Password Change Request","Your new password is " . $random_password);
        if($status == 1)
        {
            $user = User::whereEmail($request->email)->update(['password' => $random_password]);
            return response()->json(["success" => true,"message" => "Password Reset Successful"],200);
        }
        else
        {
            return response()->json(["success" => false,"message" => "Mail not Sent"],417);
        }
    }

}
