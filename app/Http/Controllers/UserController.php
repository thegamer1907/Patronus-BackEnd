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

            return response()->json(["success" => false, "error" =>$validator->messages() ],400);
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->govt_id = $request->input('govt_id');
        $user->govt_id_type = $request->input('govt_id_type');
        $user->address = $request->input('address');
        $user->save();
        $user1 = User::where('email', '=', $request->only("email"))->first();
        $role = Role::where('name', '=', 'user')->first();
        $user1->roles()->attach($role->id);
        return response()->json(["success" => true,"message" => "User created Successfully"],200);
    }
}
