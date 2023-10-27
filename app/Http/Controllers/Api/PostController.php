<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\PostResource;
use App\Mail\PostShared;
use App\Models\Post;
use App\Repositories\Interfaces\PostInterface;
use App\Service\Facades\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Exception;

class PostController extends Controller
{
    protected $post_repository;

    public function __construct(PostInterface $interface)
    {
        $this->post_repository = $interface;
    }

    public function index(Request $request){
        try {
            $posts = $this->post_repository->index($request);
            return Api::response(
              ['posts' => PostResource::collection($posts), 'pagination' => new PaginationResource($posts)]
            );
        }catch (\Exception $exception){
//            dd($exception->getLine(),$exception->getMessage(),$exception->getFile(),$exception->getTrace());
            return Api::server_error($exception);
        }
    }

    public function store(Request $request){
        try {
            return $this->post_repository->store($request);
        }catch (\Exception $exception){
            return Api::server_error($exception);
        }
    }

    public function update(Request $request){
        try {
            return $this->post_repository->update($request);
        }catch (\Exception $exception){
            return Api::server_error($exception);
        }
    }

    public function sharePost(Request $request){
        try {
            $post = Post::find($request->id);
            if($post != null && $post->client_email !=  null){
                Mail::to($post->client_email)->send(new PostShared($post));
                return Api::response([],'Email Sent');
            }
            return Api::response([],'No client email exists');
        }catch (\Exception $exception){
            return Api::server_error($exception);
        }
    }
}
