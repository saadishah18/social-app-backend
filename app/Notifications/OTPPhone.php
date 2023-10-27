<?php

namespace App\Notifications;

use App\Notifications\Drivers\Twilio;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OTPPhone extends Notification
{
    use Queueable;

    public $message;

    public function __construct($otp_code)
    {
        $this->message = "Your OTP code is " . $otp_code;
    }

    public function via($notifiable)
    {
        return [Twilio::class];
    }


    public function toArray($notifiable)
    {
        return ['message' => $this->message];
    }
}
