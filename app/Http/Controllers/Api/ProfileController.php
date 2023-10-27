<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\UserInterface;
use App\Service\Facades\Api;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public $interface;

    public function __construct(UserInterface $interface)
    {
        $this->interface = $interface;
    }

    public function profile(){
        try {
            return $this->interface->profile();
        }catch (\Exception $exception){
            return Api::server_error($exception);
        }
    }

    public function changePassword(Request $request){
        try {
            return $this->interface->changePassword($request);
        }catch (\Exception $exception){
            return Api::server_error($exception);
        }
    }

    public function uploadLogo(Request $request){
        try {
           return $this->interface->uploadImage($request);
        }catch (\Exception $exception){
            return Api::server_error($exception);
        }
    }
}
