<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\User;
use App\Repositories\Interfaces\DashboardInterface;
use App\Service\Facades\Api;

class DashboardRepository implements DashboardInterface
{
    public function webDashboard(){
        $total_users = User::where('role','!=','sadmin')->count();

        $posts = Post::count();

        $dealers = User::where('role','admin')->count();

        return Api::response(['users' => $total_users, 'posts' => $posts, 'dealers' => $dealers],'Dashboard records');
    }
}
