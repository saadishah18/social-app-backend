<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgotPasswordEmail extends Notification implements ShouldQueue
{
    use Queueable;
    public $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct($digts)
    {
        $this->otp = $digts;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Forgot Password Email')
            ->line('Hi '.$notifiable->user_name.',There was a request to change your password!')
            ->line('If you did not make this request then please ignore this email.')
            ->line('Otherwise, please use the following otp code to reset password')
            ->line($this->otp)
//            ->action('')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
