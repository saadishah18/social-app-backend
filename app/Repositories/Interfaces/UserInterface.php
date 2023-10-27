<?php

namespace App\Repositories\Interfaces;

interface UserInterface
{
    public function profile(): \Illuminate\Http\JsonResponse;

    public function user($id): \Illuminate\Http\JsonResponse;

    public function getAllUsers($request);

    public function getAllUsersForMobile($request);

    public function store($array): \Illuminate\Http\JsonResponse;

//    public function update($model);

//    public function remove($id): \Illuminate\Http\JsonResponse;

    public function changeStatus($array);

    public function changePassword($array);

    public function uploadImage($array);
}
