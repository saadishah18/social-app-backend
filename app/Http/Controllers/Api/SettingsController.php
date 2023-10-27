<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Repositories\Interfaces\SettingsInterface;
use App\Service\Facades\Api;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    protected $repository;

    public function __construct(SettingsInterface $interface)
    {
        $this->repository = $interface;
    }

    public function termsAndConditions(Request $request)
    {
        try {
            return $this->repository->termsConditions();
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

    public function privacyPolicy()
    {
        try {
            return $this->repository->privacyPolicy();
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

     public function aboutUs()
    {
        try {
            return $this->repository->aboutUs();
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }

    public function updatePolicyAndTerms(Request $request)
    {
        try {
            return $this->repository->updatePolicyAndTerms($request);
        } catch (\Exception $exception) {
            return Api::server_error($exception);
        }
    }
}
