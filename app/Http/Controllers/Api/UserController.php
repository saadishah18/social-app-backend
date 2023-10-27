<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Interfaces\UserInterface;
use App\Service\Facades\Api;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    protected $user_repository;

    public function __construct(UserInterface $interface)
    {
        $this->user_repository = $interface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $users = $this->user_repository->getAllUsers($request);
            return Api::response([
                'users' => UserResource::collection($users),
                'pagination' => new PaginationResource($users),
            ], 'User Fetched Successfully');
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

    public function getAllUsersForMobile(Request $request)
    {
        try {
            $users = $this->user_repository->getAllUsersForMobile($request);
            return Api::response([
                'users' => UserResource::collection($users),
                'pagination' => new PaginationResource($users),
            ], 'User Fetched Successfully');
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

    /**
     *
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        try {
            return $this->user_repository->store($request);
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        try {
            if (!Api::validate([
                'profile_image' => 'nullable|image|mimes:in:jpeg,png,jpg,gif,svg|max:10240',
//                'phone' => 'nullable|' . Rule::unique('users')->ignore(auth()->id()),
                'email' => 'nullable|email|' . Rule::unique('users')->ignore(auth()->id()),
                'user_name' => 'nullable|' . Rule::unique('users')->ignore(auth()->id()),
            ])) {
                return Api::validation_errors();
            }
            $user = auth()->user();
            $response = $this->user_repository->update($user);
            return Api::response(new UserResource($response['user']), 'Profile Updated Successfully');
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function changeStatus(Request $request)
    {
        try {
            return $this->user_repository->changeStatus($request);
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }


    public function adminUsers(Request $request)
    {
        $users = User::where('created_by', $request->id)->paginate(10);
        return Api::response([
            'users' => UserResource::collection($users),
            'pagination' => new PaginationResource($users),
        ], 'Admin Users Fetched Successfully');
    }

    public function deleteAccount()
    {
        $user = auth()->user();
        $user->softDeleteWithPosts();
        return Api::response('', 'Account deleted Successfully');
    }
}
