<?php


namespace App\Notifications\Drivers;


use Illuminate\Support\Facades\Log;

class FCM
{
    public function send($notifiable, $notification)
    {
        try {
            $allowed = true;
            if ($notifiable->device_token && $allowed) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: key=' . config('services.firebase.server_key'),
                    'Content-Type: application/json',
                ]);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    "registration_ids" => [$notifiable->device_token],
                    "notification" => [
                        "title" => $notification->title,
                        "body" => $notification->body,
                    ],
                    "data" => [
                        'type' => $notification->type,
                        'data' => $notification->data,
                    ]
                ]));
                return curl_exec($ch);
            }
        } catch (\Exception $e) {
            Log::channel('fcm')->error($e->getMessage());
        }
    }
}