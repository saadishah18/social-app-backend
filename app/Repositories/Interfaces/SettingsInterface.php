<?php

namespace App\Repositories\Interfaces;

interface SettingsInterface
{
    public function termsConditions();

    public function privacyPolicy();

    public function updatePolicyAndTerms($array);
}
