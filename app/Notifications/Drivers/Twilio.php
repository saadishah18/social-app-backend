<?php


namespace App\Notifications\Drivers;


use Twilio\Rest\Client;

class Twilio
{
    public function send($notifiable, $notification)
    {
        $client = new Client(config('services.twilio.sid'), config('services.twilio.auth_token'));
        $client->messages->create($notifiable->phone, ['from' => config('services.twilio.number'), 'body' => $notification->message]);
    }
}