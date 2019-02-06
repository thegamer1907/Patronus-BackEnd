<?php

namespace App\Http\Controllers;
use http\Env\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;
use App\Role;
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
}
