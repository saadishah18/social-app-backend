<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Interfaces\SettingsInterface;
use App\Service\Facades\Api;

class SettingsRepository implements SettingsInterface
{

    public function termsConditions()
    {
        $terms = Setting::where('name', 'terms_conditions')->first();
        return Api::response(['terms_conditions' => $terms->value], 'Terms and condition fetched');
    }

    public function privacyPolicy()
    {
        $terms = Setting::where('name', 'privacy_policy')->first();
        return Api::response(['privacy_policy' => $terms->value], 'Terms and condition fetched');
    }

    public function aboutUs()
    {
        $terms = Setting::where('name', 'about_us')->first();
        return Api::response(['about_us' => $terms->value], 'Terms and condition fetched');
    }

    public function updatePolicyAndTerms($request)
    {
        if (isset($request['terms'])) {
            $terms = Setting::updateOrCreate(
                ['name' => 'terms_conditions'], // Attributes to match the record
                [
                    'name' => 'terms_conditions', // Values to update or create the record
                    'value' => $request['terms']
                ]
            );
            return Api::response($terms, 'Terms updated successfully');
        }
        if (isset($request['policy'])) {
            $terms = Setting::updateOrCreate(
                ['name' => 'privacy_policy'], // Attributes to match the record
                [
                    'name' => 'privacy_policy', // Values to update or create the record
                    'value' => $request['policy']
                ]
            );
            return Api::response($terms, 'Policy updated successfully');
        }
    }

}
