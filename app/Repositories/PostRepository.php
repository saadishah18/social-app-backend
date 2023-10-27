<?php

namespace App\Repositories;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Repositories\Interfaces\PostInterface;
use App\Service\Facades\Api;
use Illuminate\Support\Facades\Storage;

class PostRepository implements PostInterface
{
    public function index($request){

        $user = auth()->user();

        $posts = Post::when($user->role == 'Admin', function ($query) use ($user) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('created_by', $user->id)->orWhere('id',$user->id);
            });
        })->when($user->role == 'Employee', function ($query) use ($user) {
            $query->where('user_id',$user->id);
        })->when($user->role == 'sadmin', function ($query) use ($user) {
            $query->whereHas('user');
        })->when($user->role == 'User', function ($query) use ($user,$request) {
            $query->where('user_id',$user->id);
        })->where('status', $request->status)->orderBy('created_at','DESC')->paginate();

        return $posts;
    }

    public function store($request){
        if (!Api::validate([
            'client_signature' => 'nullable|mimes:in:jpeg,png,jpg,gif,svg|max:10240',
            'post_image' => 'required|image|mimes:in:jpeg,png,jpg,gif,svg|max:10240',
        ])) {
            return Api::validation_errors();
        }
        $user = auth()->user();
        if($user->plan_id == 2 && $user->posts()->count() == 4){
            return Api::error('You have reached your maximum limit');
        }
        $data = $request->all();
        if ($request->hasFile('client_signature')) {
            $image = $request->file('client_signature');
            $signature_image_name = rand().'-'. time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images', $signature_image_name);
            $data['client_signature'] = $signature_image_name;
        }
        if ($request->hasFile('post_image')) {
            $image = $request->file('post_image');
            $filename = rand().'-'.time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images', $filename);
            $data['post_image'] = $filename;
        }

        $data['user_id'] = auth()->id();
        if(auth()->user()->role == 'Admin' || auth()->user()->role == 'User'){
            $data['status'] = 'Approved';
        }else{
            $data['status'] = 'Pending';
        }

        $post = Post::create($data);
        return Api::response(new PostResource($post),'Post Created Successfully');
    }

    public function detail($id){

    }

    public function delete($id){

    }

    public function update($request){
        $post = Post::find($request['post_id']);
        if($post){
            if($request['status'] == 'approved'){
                $post->status = 'Approved';
                $post->update();
                return Api::response(new PostResource($post->refresh()),'Status updated successfully',200);
            }elseif($request['status'] == 'rejected'){
                $post->status = 'Approved';
                $post->delete();
                return Api::response([],'Post rejected successfully',200);
            }

        }else{
            return Api::error('Post not found',404);
        }
    }
}
