<?php

namespace App\Http\Controllers;
use App\Complaint;
use http\Env\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;
use App\Account;
use App\Role;
use App\SendMail;
use App\Beneficiary;
use Illuminate\Support\Facades\Auth;
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

        $account = ['email' => $request->email, 'account_no' => mt_rand(100000, 999999) , 'balance' => 0];
        $user = new User();
        $user->create($request->only($user->getFillable()));
        $user1 = User::whereEmail($request->email)->firstorfail();
        $role = Role::whereName('user')->firstorfail();
        $user1->roles()->attach($role->id);
        Account::create($account);
        return response()->json(["success" => true,"message" => "User created Successfully"],200);
    }

    public function changepassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required',
            'oldpassword' => 'required',
            'newpassword' => 'required|min:4'
        ]);

//        $try = Auth::user();

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

    public function overview()
    {
        $account = Account::whereEmail(Auth::user()->email)->first();
        return response()->json(["success" => true,"details" => $account],200);
    }

    public function addbenificiary(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'ben_email' => 'required',
            'ben_account_no' => 'required',
            'name' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(["success" => false, "error" =>$validator->errors()->first()],400);
        }

        $ben = Account::whereEmailAndAccount_no($request->ben_email,$request->ben_account_no)->first();

        if(!$ben)
        {
            return response()->json(["success" => false, "error" =>"No such account exists"],400);
        }

        $already = Beneficiary::whereEmailAndBen_email(Auth::user()->email,$request->ben_email)->first();

        if($already)
        {
            return response()->json(["success" => false, "error" =>"This account is already present in your benificiary list."],400);
        }

        $benificary = ['email' => Auth::user()->email,
            'ben_email' => $request->ben_email, 'ben_account_no' => $request->ben_account_no, 'name' => $request->name];

        Beneficiary::create($benificary);
        return response()->json(["success" => true, "message" =>"Benificiary Added Successfully"],200);
    }

    public function viewbenificiary()
    {
        $ben = Beneficiary::whereEmail(Auth::user()->email)->get();
        return response()->json(["success" => true, "benificiaries" =>$ben],200);
    }

    public function deletebenificiary(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'ben_email' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(["success" => false, "error" =>$validator->errors()->first()],400);
        }

        $already = Beneficiary::whereEmailAndBen_email(Auth::user()->email,$request->ben_email)->first();

        if(!$already)
        {
            return response()->json(["success" => false, "error" =>"No such benificiary exists"],400);
        }
        else
        {
            $already->delete();
            return response()->json(["success" => true, "message" =>"Benificiary Deleted Successfully"],200);
        }
    }

    public function filecomplaint(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'type' => 'required|in:Feedback,Complaint',
            'message' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(["success" => false, "error" =>$validator->errors()->first()],400);
        }

        $complaint = ['email' => Auth::user()->email,'type' => $request->type, 'message' => $request->message, 'resolved' => false];

        Complaint::create($complaint);
        return response()->json(["success" => true, "message" =>"Submitted Successfully"],200);
    }

    public function resolvecomplaint(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'type' => 'required|in:Feedback,Complaint',
            'message' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(["success" => false, "error" =>$validator->errors()->first()],400);
        }

        $complaint = ['email' => Auth::user()->email,'type' => $request->type, 'message' => $request->message, 'resolved' => false];

       $res =  Complaint::whereEmailAndTypeAndMessage(Auth::user()->email,$request->type,$request->message)->first();
       if(!$res)
       {
           return response()->json(["success" => false, "error" =>"Complaint not found"],404);
       }
       else
       {
           $res->update(['resolved' => true]);
           return response()->json(["success" => true, "message" =>"Resolved Successfully"],200);
       }


    }

    public function viewcomplaint()
    {
        return response()->json(["success" => true, "complaints" => Complaint::all()],200);
    }

    public function deleteuser(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(["success" => false, "error" =>$validator->errors()->first()],400);
        }

        $del = User::whereEmail($request->email)->first();
        if(!$del)
        {
            return response()->json(["success" => false, "error" =>"No such account exists"],400);
        }
        else
        {
            $del->delete();
            return response()->json(["success" => true, "message" =>"Account Deleted Successfully"],200);
        }
    }

    public function getallroles()
    {
        return response()->json(["success" => true, "Roles" => Role::all()],200);
    }



//    public function bulkmail()
//    {
//
//    }

}
