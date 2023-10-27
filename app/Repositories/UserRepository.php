<?php

namespace App\Repositories;

use App\Http\Resources\UserResource;
use App\Models\Plans;
use App\Models\User;
use App\Repositories\Interfaces\UserInterface;
use App\Service\Facades\Api;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules;

class UserRepository implements UserInterface
{
    public function profile(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        return Api::response(new UserResource($user));
    }

    public function user($id): \Illuminate\Http\JsonResponse
    {
        $user = User::find($id);
        return Api::response(new UserResource($user));
    }

    public function getAllUsers($request)
    {
//        echo "<pre>";
//       $result = print_r($request->all());
//        return $result;
        $users = User::whereNotIn('role', ['sadmin', 'Employee']);
        if (isset($request['role']) && $request['role'] != '') {
            $users->where('role', $request['role']);
        }
        if (isset($request['status']) && $request['status'] != '') {
            $users->where('is_active', $request['status']);
        }
        if (isset($request['plan_id']) && $request['plan_id'] != '') {
            $users->where('plan_id', $request['plan_id']);
        }
        $users->orderBy('created_at','desc');
        return $users->paginate(10);
    }

    public function getAllUsersForMobile($request)
    {
        $users = User::where('created_by',auth()->id());
        if(isset($request['search']) && $request['search'] != ''){
            $users->where('user_name','like','%'.$request['search'].'%')
                ->orWhere('email','=', $request['email']);
        }
        $users->orderBy('created_at','desc');
        return $users->paginate(10);
    }

    public function store($request): \Illuminate\Http\JsonResponse
    {
        $data = $request->all();
        if(auth()->user()->role  === 'sadmin'){
            $data['role'] = 'Admin';
        }else{
            $data['role'] = 'Employee';
        }
        $data['is_approved'] = 1;
        $data['created_by'] = auth()->id();
        $data['is_active'] = 1;
        $data['password'] = Hash::make($request['password']);
        $data['plan_id'] = Plans::where('name', 'paid')->first()->id;

        $user = User::create($data);
//        dd($user->dealerAdmin);
        if($user){
            $logo = $user->dealerAdmin->logo;
            $user->logo  = $logo;
            $user->update();
        }
        return Api::response(new UserResource($user), 'Employee created successfully');

    }

    public function update($model)
    {
        $request = request();

        // todo @saad User is unique on the basis of Name & DOB
        if ($request->has('user_name')) $model->user_name = $request->user_name;
        if ($request->has('first_name')) $model->first_name = $request->first_name;
        if ($request->has('last_name')) $model->last_name = $request->last_name;
        if ($request->has('country_code')) $model->country_code = $request->country_code;
        if ($request->has('country_name')) $model->country_name = $request->country_name;
        if ($request->has('phone')) $model->phone = $request->phone;
        if ($request->has('email')) $model->email = $request->email;
        // todo @saad verify phone is unique before updating
        if ($request->has('role')) $model->role = $request->role;
        if ($request->has('password')) $model->password = Hash::make($request->password);

        if (isset($request->profile_image) &&  $request->has('profile_image')) {
            $image_validation = $this->image_validation($request->file('profile_image'));
            if ($image_validation['error'] == false) {
                if ($model->profile_image && file_exists(public_path('storage/files/profile' . $model->profile_image))) {
                    unlink(public_path('/files/profile' . $model->profile_image));
                }
                $path = $request->file('profile_image')->store('/files/profile', 'public');
                $model->profile_image = $path;
            }
        }

        $model->update();
        $message = trans('auth.profile_updated');
        $return_array = [
            'user' => $model,
            'message' => $message
        ];
        return $return_array;
    }


    public function image_validation($image)
    {
        if($image != null || $image != ''){
            $fileExtension = substr(strrchr($image->getClientOriginalName(), '.'), 1);
            if ($fileExtension != 'jpg' && $fileExtension != 'jpeg' && $fileExtension != 'png' && $fileExtension != 'gif') {
                return Api::error('Image extension should be jpeg,jpg,png,and gif');
            }
            $filesize = \File::size($image);
            if ($filesize >= 1024 * 1024 * 20) {
                return Api::error('Image size should less than 20 mb');
            }
            $return_array['message'] = 'Image Fine';
            $return_array['error'] = false;
            return $return_array;
        }
    }

    public function changeStatus($request)
    {
        $user = User::find($request->id);
        $user->is_active = ($request->status == 1) ? 0 : 1;
        if ($user->update()) {
            return Api::response($user->refresh(), 'Status Changed Successfully');
        }
    }

    public function changePassword($request)
    {
        if (!Api::validate([
            'old_password' => 'required',
            'password' => ['required', 'min:8', 'confirmed', Rules\Password::defaults()],
            'password_confirmation' => ['required', 'min:8'],

        ])) {
            return Api::validation_errors();
        }

        $user = auth()->user();
        if (!Hash::check($request->old_password, $user->password)) {
            return Api::error('Old password is not correct');
        }
        $user->password = Hash::make($request->password);
        $user->update();
        return Api::response('Password updated successfully');
    }

    public function uploadImage($request)
    {
        if (!Api::validate([
            'logo' => 'required|image|mimes:in:jpeg,png,jpg,gif,svg|max:10240',
        ])) {
            return Api::validation_errors();
        }
        $user = auth()->user();
        if ($user->plan_id == 1) {

            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/images/logo', $filename);
               $user->logo = $filename;
            }
            $user->update();

            $check = $user->employees()->update(['logo'=> $filename]);
            return Api::response(new UserResource($user), 'Logo uploaded successfully');
        } else {
            return Api::error('You are  not allowed for this action');
        }
    }

}
