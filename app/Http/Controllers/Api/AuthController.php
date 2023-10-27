<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
//use App\Http\Resources\PartnerResource;
use App\Http\Resources\UserResource;
//use App\Models\OTP;
//use App\Http\Responses\APIResponse;
use App\Models\OTP;
use App\Models\Plans;
use App\Models\SocialUser;
use App\Models\User;
use App\Notifications\ForgotPasswordEmail;
use App\Notifications\OTPEmail;
//use App\Notifications\OTPPhone;
use App\Service\Facades\Api;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
class AuthController extends Controller
{
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!Api::validate(['email' => 'required|email', 'password' => 'required'])) {
                return Api::validation_errors();
            }

            $user = User::firstWhere('email', $request->email);
            if (!$user) {
                return Api::error(trans('auth.failed'));
            }
            if (!Hash::check($request->password, $user->password)) {
                return Api::error(trans('auth.password'));
            }
            return Api::response([
                'user' => new UserResource($user),
//                'partner_detail' => $this->userAsPartnerData != null ? new PartnerResource($user) : null,
                'access_token' => $user->createToken('AccessToken')->plainTextToken
            ],'Login Successfully');
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!Api::validate([
                'user_name' => 'nullable|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',

                ])) {
                return Api::validation_errors();
            }
            $user = User::create([
//                'user_name' => $request->user_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'country_code' => $request->country_code,
                'country_name' => $request->country_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'User',
                'plan_id' => 2,
                'is_active' => 1,
            ]);
            return Api::response([
                'user' => new UserResource($user),
                'access_token' => $user->createToken('AccessToken')->plainTextToken
            ],'Registered Successfully',200);
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

    public function social(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!Api::validate(['email' => 'required', 'provider_id' => 'required', 'provider' => 'required|in:facebook,google,apple'])) {
                return Api::validation_errors();
            }
            $social_user = SocialUser::firstWhere($request->only(['provider', 'provider_id']));
            if (!$social_user) {
                $email = $request->has('email') ? $request->email : $request->provider_id . '@' . strtolower($request->provider) . '.com';
                if (!Api::validate([
                    'email' => 'required|email|unique:users',

                ])) {
                    return Api::validation_errors();
                }
                $user = User::create([
                    'email' => $email,
                    'password' => Hash::make('@User123!'),
                    'role' => 'User',
                    'plan_id' => Plans::where('name','Freemium')->first()->id,
                    'is_active' => 1,
                ]);
                SocialUser::create([
                    'user_id' => $user->id,
                    'provider_id' => $request->provider_id,
                    'provider' => $request->provider,
                ]);
                return Api::response([
                    'user' => new UserResource($user),
                    'access_token' => $user->createToken('AccessToken')->plainTextToken
                ]);
            } else {
                if($social_user->user == null){
                    $email = $request->has('email') ? $request->email : $request->provider_id . '@' . strtolower($request->provider) . '.com';
                    $user = User::where('email',$email)->first();
                    if($user == null){
                        $user = User::create([
                            'email' => $email,
                            'role' => 'User',
                            'plan_id' => 2,
                            'is_active' => 1,
                        ]);
                    }
                    return Api::response([
                        'user' => new UserResource($user),
                        'access_token' => $user->createToken('AccessToken')->plainTextToken
                    ]);
                }
                return Api::response([
                    'user' => new UserResource($social_user->user),
                    'access_token' => $social_user->user->createToken('AccessToken')->plainTextToken
                ]);
            }
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

    public function sendForgotPasswordOTP(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!Api::validate(['email' => 'required'])) {
                return Api::validation_errors();
            }
            $user = User::firstWhere($request->only('email'));
            if (!$user) {
                return Api::error(trans('auth.failed'));
            }

            $code_number = generate_code(4);
            $check_user_otp = $this->checkOTPExits($code_number, 'email',$request->email);
            DB::table('password_reset_tokens')->where($request->only('email'))->delete();
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $code_number,
                'created_at' => Carbon::now()
            ]);
            $user->notify(new ForgotPasswordEmail($code_number));
            $this->storeEmailTimestamp($request->email);
//            dd($code_number);
            return Api::response(data: ['opt_code' => $code_number], message: trans( 'auth.otp_sent',['digit' => 4, 'medium' => 'email']));
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

    public function resendForgotPasswordOTP(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!Api::validate(['email' => 'required'])) {
                return Api::validation_errors();
            }
            // Retrieve the timestamp of the last email sent from cookie or session
            $emailTimestamp = $this->retrieveEmailTimestamp($request->email);
            // Check if enough time has passed since the last email was sent
            if ($emailTimestamp && $emailTimestamp->addMinutes(1)->isFuture()) {
                return Api::error( 'Please wait for 1 minutes before resending the email', 422);
            }
            $user = User::firstWhere($request->only('email'));
            if (!$user) {
                return Api::error(trans('auth.failed'));
            }

            $code_number = generate_code(4);
            $check_user_otp = $this->checkOTPExits($code_number, 'email',$request->email);
            DB::table('password_reset_tokens')->where($request->only('email'))->delete();
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $code_number,
                'created_at' => Carbon::now()
            ]);
            $user->notify(new ForgotPasswordEmail($code_number));
            $this->storeEmailTimestamp($request->email);
            return Api::response(data: ['opt_code' => $code_number], message: trans('auth.otp_resent', ['digit' => 4, 'medium' => 'email']));
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

    public function resetPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'otp' => 'required',
            'password' => 'required|confirmed'
        ]);
        if ($validation->fails()) {
            return Api::error($validation->errors()->first(), 422);
        }

        $token = $request->otp;
        $password = $request->password;
        $userEmail = DB::table('password_reset_tokens')
            ->select('email')
            ->where('token', '=', $token)
            ->first();

        if ($userEmail) {
            $affected = DB::table('users')
                ->where('email', $userEmail->email)
                ->update(['password' => bcrypt($password)]);
            if ($affected) {
                DB::table('password_reset_tokens')->where('email', '=', $userEmail->email)->delete();
                OTP::where('otp',$request->otp)->delete();
                return Api::response([], trans('auth.reset_success'));
            } else {
                return Api::error('Not updated', 404);
            }
        } else {
            return Api::error('Invalid OTP', 404);
        }
    }


    public function updatePassword(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!Api::validate(['email' => 'required', 'password' => 'required|confirmed|min:6'])) {
                return Api::validation_errors();
            }

            $user = User::firstWhere($request->only('email'));
            if (!$user) {
                return Api::error(trans('auth.failed'));
            }

            $user->update(['password' => bcrypt($request->password)]);

            return Api::response(message: trans('auth.password_updated'));
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

   /* public function sendPhoneOTP(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!Api::validate(['phone' => 'required'])) {
                return Api::validation_errors();
            }

            $user = User::firstWhere($request->only('phone'));
            if (!$user) {
                return Api::error(trans('response.no_record'));
            }
            $code_number = generate_code(4);
            $check_user_otp = $this->checkOTPExits($code_number, 'phone',$request->phone);

            $user->notify(new OTPPhone($code_number));
            return Api::response(data: ['otp_code' => $code_number],message: trans('auth.otp_sent', ['medium' => 'phone', 'digit' => 4]));
        } catch (\Exception $exception) {
            dd($exception->getMessage());
            return Api::server_error($exception->getMessage());
        }
    }*/

    public function verifyPhone(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!Api::validate(['phone' => 'required', 'otp' => 'required'])) {
                return Api::validation_errors();
            }

            $user = User::firstWhere($request->only('phone'));
            if (!$user) {
                return Api::error(trans('auth.failed'));
            }

            if (!OTP::where(['slug' => 'phone', 'value' => $user->phone, 'otp' => $request->otp])->count()) {
                return Api::error(trans('auth.otp_failed'));
            }

            $user->update(['phone_verified_at' => now()->toDateTimeString()]);
            return Api::response(message: trans('auth.phone_verified'));
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

    public function sendEmailOTP(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!Api::validate(['email' => 'required'])) {
                return Api::validation_errors();
            }

            $user = User::firstWhere($request->only('email'));

            if (!$user) {
                return Api::not_found();
            }
            $code_number = generate_code(4);
            $check_user_otp = $this->checkOTPExits($code_number, 'email',$request->email);

            $user->notify(new OTPEmail($code_number));
            return Api::response(data: ['opt_code' => $code_number],message: trans('auth.otp_sent', ['medium' => 'phone', 'digit' => 4]));
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

    public function verifyEmailOTP(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!Api::validate(['email' => 'required', 'otp' => 'required'])) {
                return Api::validation_errors();
            }

            $user = User::firstWhere($request->only('email'));
            if (!$user) {
                return Api::error(trans('auth.failed'));
            }

            if (!OTP::where(['slug' => 'email', 'value' => $user->email, 'otp' => $request->otp])->count()) {
                return Api::error(trans('auth.otp_failed'));
            }

            if($user->email_verified_at != null){
                $user->update(['email_verified_at' => now()->toDateTimeString()]);
            }
            return Api::response(message: trans('auth.email_verified'));
        } catch (\Exception $exception) {
//            dd($exception->getMessage());
            return Api::server_error($exception);
        }
    }

    public function updateDeviceToken(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!Api::validate(['token' => 'required'])) {
                return Api::validation_errors();
            }
            auth()->user()->update([
                'device_token' => $request->token
            ]);
            return Api::response(message: trans('auth.token_update'));
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

    public function checkOTPExits($code, $type, $value){
        $check_user_otp = OTP::where('slug',$type)->where('value',$value)->first();
        if($check_user_otp){
            $check_user_otp->otp = $code;
            $check_user_otp->update();
        }else{
            $check_user_otp = OTP::create([
                'slug' => $type,
                'value' => $value,
                'otp' => $code
            ]);
        }
        return $check_user_otp;
    }

    public function logout(){
        $user = auth()->user();
//        $user->device_token = null;
//        $user->update();
        auth()->user()->tokens()->delete();
        return Api::response(message: trans('auth.logout'));
    }


    private function storeEmailTimestamp($email)
    {
        $timestamp = Carbon::now()->addMinute(5);
//        Session::put('time-'.$email, $timestamp);
//        Session::put($email, $timestamp);
        session(['time-'.$email => $timestamp]);
    }

    private function retrieveEmailTimestamp($email)
    {
//        return Cookie::get('email_timestamp');
       return Session::get($email);
        // Alternatively, retrieve from session: Session::get('email_timestamp');
    }
}
