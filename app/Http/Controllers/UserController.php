<?php

namespace App\Http\Controllers;
use App\Complaint;
use function GuzzleHttp\Psr7\str;
use http\Env\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;
use App\Account;
use App\Role;
use App\SendMail;
use App\Beneficiary;
use App\buffer_transaction;
use App\otp;
use App\transactions;
use App\block_status;
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
        $acc_no = mt_rand(100000, 999999);
        $account = ['email' => $request->email, 'account_no' => $acc_no  , 'balance' => 0];
        $block_status = ['account_no' => $acc_no, 'status' => false];
        $user = new User();
        $user->create($request->only($user->getFillable()));
        $user1 = User::whereEmail($request->email)->firstorfail();
        $role = Role::whereName('user')->firstorfail();
        $user1->roles()->attach($role->id);
        Account::create($account);
        block_status::create($block_status);
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

        if(Auth::user()->email == $request->ben_email)
        {
            return response()->json(["success" => false, "error" =>"Can't add yourself as beneficiary."],400);
        }

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
            'email' => 'required|exists:users,email',
            'type' => 'required|in:Feedback,Complaint',
            'message' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(["success" => false, "error" =>$validator->errors()->first()],400);
        }

        $complaint = ['email' => Auth::user()->email,'type' => $request->type, 'message' => $request->message, 'resolved' => false];

       $res =  Complaint::whereEmailAndTypeAndMessage($request->email,$request->type,$request->message)->first();
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

    public function getallblocked()
    {
        return response()->json(["success" => true, "Accounts" => block_status::whereStatus(true)->get()],200);
    }

    public function blockaccount(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'account_no' => 'required|exists:accounts,account_no'
        ]);

        if ($validator->fails()) {

            return response()->json(["success" => false, "error" =>$validator->errors()->first()],400);
        }

        block_status::whereAccount_no($request->account_no)->update(['status' => true]);
        return response()->json(["success" => true,"message" => "Account Blocked"],200);

    }

    public function unblockaccount(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'account_no' => 'required|exists:accounts,account_no'
        ]);

        if ($validator->fails()) {

            return response()->json(["success" => false, "error" =>$validator->errors()->first()],400);
        }

        block_status::whereAccount_no($request->account_no)->update(['status' => false]);
        return response()->json(["success" => true,"message" => "Account Unblocked"],200);

    }

    public function starttransfer(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'account_no' => 'required|exists:accounts,account_no',
            'amount' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(["success" => false, "error" =>$validator->errors()->first()],400);
        }

        $account = Account::whereEmail(Auth::user()->email)->first();
        $status = block_status::whereAccount_no($account->account_no)->first();

        if($status->status == true)
        {
            return response()->json(["success" => false, "error" =>"Account is Blocked. Get it unblocked first"],400);
        }

        if($account->balance < $request->amount)
        {
            return response()->json(["success" => false, "error" =>"Insufficient Balance"],400);
        }

        $otp_prev = otp::whereEmail(Auth::user()->email)->first();

        if($otp_prev)
        {
            return response()->json(["success" => false, "error" =>"Please complete your pending transaction"],400);
        }

        $buffer_trans = ['from_account_no' => $account->account_no, 'to_account_no' => $request->account_no, 'amount' => $request->amount];
        $otp = mt_rand(1000, 9999);
        $buf_return = buffer_transaction::create($buffer_trans);

        $otp_detail = ['email' => Auth::user()->email, 'otp' => $otp, 'tid' => $buf_return->id];
        otp::create($otp_detail);
        $email_status = SendMail::send(Auth::user()->email,"OTP for transaction","Your otp for transaction worth " .  (string)$request->amount . ' is ' . $otp);

        if($email_status == 1)
        {
            return response()->json(["success" => true,"message" => "OTP Sent"],200);
        }
        else
        {
            return response()->json(["success" => false,"message" => "OTP could not be sent. try again"],400);
        }

    }

    public function completetransfer(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'otp' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(["success" => false, "error" =>$validator->errors()->first()],400);
        }

        $otp_obj = otp::whereEmail(Auth::user()->email)->first();

        if($otp_obj->otp == $request->otp)
        {
            $buf_transaction = buffer_transaction::whereId($otp_obj->tid)->first();
            Account::whereAccount_no($buf_transaction->from_account_no)->decrement('balance',$buf_transaction->amount);
            Account::whereAccount_no($buf_transaction->to_account_no)->increment('balance',$buf_transaction->amount);
            $transaction = new transactions();
            $transaction->create($buf_transaction->only($transaction->getFillable()));
            $buf_transaction->delete();
            return response()->json(["success" => true, "message" => "Transfer Complete"],200);
        }
        else
        {
            return response()->json(["success" => false, "error" => "Incorrect OTP"],400);
        }
    }








//    public function bulkmail()
//    {
//
//    }

}
