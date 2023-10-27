<?php namespace App\Service\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\JsonResponse response($data = null, $message = '', $status = 200)
 * @method static \Illuminate\Http\JsonResponse not_found($message = '')
 * @method static boolean validate($rules, $messages = [])
 * @method static \Illuminate\Http\JsonResponse validation_errors()
 * @method static \Illuminate\Http\JsonResponse error($message = '', $status = 422)
 * @method static \Illuminate\Http\JsonResponse server_error(\Throwable $throwable)
 * @method static \Illuminate\Http\JsonResponse forbidden()
 * @method static \Illuminate\Http\JsonResponse unauthenticated()
 *
 * @see \App\Service\ApiService
 */
class Api extends Facade
{

    protected static function getFacadeAccessor()
    {
        return '\App\Service\ApiService';
    }

}
