<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController as BaseController;
use App\Mail\AccountVerification;
use App\Mail\ForgotMail;
use App\Models\Customer\Profile\ProfileModel;
use App\Models\OtpModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Mail;
use Validator;

class AuthController extends BaseController
{
    public function register(Request $req)
    {
        $rules = [
            'email'    => 'unique:users|required',
            'name'     => 'required',
            'password' => 'required|min:8|confirmed',
        ];
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        } else {
            $store           = new User;
            $store->name     = $req->name;
            $store->email    = $req->email;
            $store->password = Hash::make($req->password);
            $store->save();
            $success['token']       = $store->createToken('MyApp')->accessToken;
            $success['Information'] = $store;
            $this->sendOTPEmail($req);
            return $this->sendResponse($success, 'Customer Registered Sucessfully.');
        }
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user                   = Auth::user();
            $success['token']       = $user->createToken('MyApp')->accessToken;
            $success['Information'] = $user;
            return $this->sendResponse($success, 'Customer login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function logout(Request $request)
    {

        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);

    }

    public function forget(Request $request)
    {
        try {
            $checkemail = User::where('email', $request->email)->first();
            if ($checkemail) {
                $code     = rand(10000, 1000000);
                $mailData = [
                    'title' => 'Forget Password',
                    'body'  => 'Your change code is',
                    'name'  => $checkemail->name,
                    'otp'   => $code,
                ];
                Mail::to($request->email)->send(new ForgotMail($mailData));
                $store        = new OtpModel;
                $store->email = $request->email;
                $store->type  = $request->type;
                $store->code  = $code;
                $store->save();
                return response()->json(["Status" => "Email Send Successfully"]);

            } else {
                return response()->json(["Status" => "Email not found"], 400);
            }
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage());

        }
    }

    public function verityotp(Request $request)
    {

        $record = OtpModel::where('email', $request->email)
            ->where('code', $request->code)
            ->where('type', $request->type)
            ->where('is_used', false)
            ->first();
        if ($record) {
            $codeCreatedTime = $record->created_at;
            $expirationTime  = now()->subMinutes(5);

            if ($codeCreatedTime >= $expirationTime) {
                $record->is_used = true;
                $record->update();
                return response()->json(["Status" => "Code Exists"]);
            } else {
                return response()->json(["Status" => "Code Expired"], 422);
            }
        } else {
            return response()->json(["Status" => "Code Not Found"], 400);
        }
    }

    public function changepassword(Request $request)
    {
        $rules = [
            'email'    => 'required',
            'password' => 'required|min:8|confirmed',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        } else {
            $check = User::where('email', $request->email)->first();
            if ($check) {
                $check->password = Hash::make($request->password);
                $check->update();

                return response()->json(["Status" => "Password Updated Successfully"]);
            } else {
                return response()->json(["Status" => "Email Not Found"], 400);

            }
        }

    }

    public function sendOTPEmail(Request $request)
    {
        try {
            $verification_code = rand(100000, 999999);
            $otp               = OtpModel::create([
                'email' => $request->email,
                'type'  => "email_verification",
                'code'  => $verification_code,
            ]);
            $mailData = [

                'name' => $request->name,
                'otp'  => $verification_code,

            ];
            Mail::to($request->email)->send(new AccountVerification($mailData));

            return response()->json(['message' => 'Account Verification OTP sent successfully']);
        } catch (\Throwable $th) {
            Log::error('Error sending OTP email: ' . $th->getMessage());
            return response()->json(['error' => 'An error occurred while sending OTP'], 500);
        }
    }

    public function accountverify(Request $request)
    {

        $record = OtpModel::where('email', $request->email)
            ->where('code', $request->code)
            ->where('type', $request->type)
            ->where('is_used', false)
            ->first();
        if ($record) {
            $codeCreatedTime = $record->created_at;
            $expirationTime  = now()->subMinutes(5);

            if ($codeCreatedTime >= $expirationTime) {
                $record->is_used = true;
                $record->update();
                // update user verification
                $user                    = User::where('email', $request->email)->first();
                $user->email_verified_at = now();
                $user->update();
                return response()->json(["Status" => "User Verified"]);
            } else {
                return response()->json(["Status" => "Code Expired"], 422);
            }
        } else {
            return response()->json(["Status" => "Code Not Found"], 400);
        }
    }

    // Customer Profile

    public function profile(Request $request)
    {
        try {
            $Auth   = Auth::user()->id;
            $record = ProfileModel::where('customer', $Auth)->first();

            if ($record) {
                return response()->json(["Status" => "Profile Information already Added"], 500);
            } else {
                $store                     = new ProfileModel;
                $store->customer           = $Auth;
                $store->gender             = $request->gender;
                $store->interests          = json_encode($request->interests);
                $store->visa_type          = $request->visa_type;
                $store->notification       = $request->notification;
                $store->language           = $request->language;
                $store->province           = $request->province;
                $store->other_user_connect = $request->other_user_connect;
                $store->premium_features   = $request->premium_features;
                $store->save();
                return response()->json(["Status" => "Profile Added Successfully"], 200);
            }

        } catch (\Throwable $th) {
            return response()->json(["status" => "Error", "Message" => $th->getMessage()], 500);
        }
    }

    public function get_profile_info()
    {
        try {
            $Auth   = Auth::user()->id;
            $record = User::with('Profile_Information')->find($Auth);
            if ($record) {
                return response()->json(["status" => "success", "data" => $record], 200);
            } else {
                return response()->json(["status" => "Error", "Message" => "Not Found"], 500);
            }
        } catch (\Throwable $th) {
            return response()->json(["status" => "Error", "Message" => $th->getMessage()], 500);
        }
    }

}
