<?php

use App\Models\OTP;
use App\Models\PortalSetting;
use App\Notifications\OTPEmail;
use App\Notifications\OTPPhone;
use Illuminate\Support\Str;

if (!function_exists('generate_code')) {
    function generate_code($length): string
    {
        $code = array_merge(range(0, 9), range(0, 9));
        shuffle($code);
        return implode(array_slice($code, 0, $length));
    }
}

if (!function_exists('generate_filename')) {
    function generate_filename($file): string
    {
//        return Str::random() . '.' . time() . '.' . $file->getClientOriginalExtension();
        return $file->getClientOriginalName();
    }
}

if (!function_exists('send_otp')) {
    function send_otp($user, $type): string
    {
        $code_number = generate_code(4);


        if($type == 'email'){
            $check_user_otp = OTP::where('slug',$type)->where('value',$user->email)->first();
            if($check_user_otp){
                $check_user_otp->otp = $code_number;
                $check_user_otp->update();
            }else{
                $check_user_otp = OTP::create([
                    'slug' => $type,
                    'value' => $user->email,
                    'otp' => $code_number
                ]);
            }
            $user->notify(new OTPEmail($code_number));
        }elseif($type == 'phone'){
            $check_user_otp = OTP::where('slug',$type)->where('value',$user->phone)->first();
            if($check_user_otp){
                $check_user_otp->otp = $code_number;
                $check_user_otp->update();
            }else{
                $check_user_otp =  OTP::create([
                    'slug' => 'phone',
                    'value' => $user->phone,
                    'otp' => $code_number
                ]);
            }
            $user->notify(new OTPPhone($code_number));
        }
        return $code_number;
    }
}

if (!function_exists('imagePath')) {
    function imagePath($image,): string
    {
       return asset('storage/images/'.$image);
    }
}

if (!function_exists('rand_time')) {

    function rand_time($min_date, $max_date)
    {
        /* Gets 2 dates as string, earlier and later date.
           Returns date in between them.
        */

        $min_epoch = strtotime($min_date);
        $max_epoch = strtotime($max_date);

        $rand_epoch = rand($min_epoch, $max_epoch);

        return date('H:i:s', $rand_epoch);
    }

}

if (!function_exists('formatNumbers')) {
    function formatNumbers($number)
    {
        $result = number_format($number, 2);
        return (float) $result;
    }
}

if (!function_exists('requiredUsersEmail')) {
    function requiredUsersEmail($service_id)
    {
        $required_email = 0;
        if($service_id == 7 || $service_id == 11){
           $required_email = 1;
        }
        return $required_email;
    }
}

if (!function_exists('errorMessage')) {
    function errorMessage($message = null, $error = true, $status = 422)
    {
        $return_array['message'] = $message;
        $return_array['error'] = $error;
        $return_array['status'] = $status;
        return $return_array;
    }
}

if (!function_exists('successResponse')) {
    function successResponse($data = null, $message = null, $error = false, $status = 200)
    {
        $return_array['data'] = $data;
        $return_array['message'] = $message;
        $return_array['error'] = $error;
        $return_array['status'] = $status;
        return $return_array;
    }
}

if (!function_exists('fullName')) {
    function fullName($first_name, $last_name)
    {
        return ucfirst($first_name.' '.$last_name);
    }
}

if (!function_exists('checkIfUploadedFileHasSameName')) {
    function checkIfUploadedFileHasSameName($imagePath)
    {
        if(!is_null($imagePath)){
            return file_exists(public_path($imagePath));
        }
        return false;
    }
}

if (!function_exists('checkIfFileIsUploadedThenDelete')) {
    function checkIfFileIsUploadedThenDelete($imagePath)
    {
        if(!is_null($imagePath)){
            \Illuminate\Support\Facades\File::delete(public_path($imagePath));
            return true;
        }
        return false;
    }
}

if (!function_exists('checkIfDirectoryIsAvailable')) {
    function checkIfDirectoryIsAvailable($directory)
    {
        if (!\Illuminate\Support\Facades\File::exists(public_path($directory))) {
            \Illuminate\Support\Facades\File::makeDirectory(public_path($directory), 0777, true);
        }
    }
}



