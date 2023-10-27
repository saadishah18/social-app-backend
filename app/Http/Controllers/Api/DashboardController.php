<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\DashboardInterface;
use App\Service\Facades\Api;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public $interface;

    public function __construct(DashboardInterface $interface)
    {
        $this->interface = $interface;
    }

    public function adminDashboard(){
        try {
            return $this->interface->webDashboard();
        }catch (\Exception $exception){
            return Api::server_error($exception);
        }
    }

}
