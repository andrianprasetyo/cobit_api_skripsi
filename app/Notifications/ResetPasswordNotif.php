<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotif extends Notification
{
    use Queueable;

    private $data=null;
    /**
     * Create a new notification instance.
     */
    public function __construct($data=null)
    {
        $this->data=$data;
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
        $data['nama'] = $notifiable->email;
        $data['kode'] = $notifiable->otp->kode;
        // $data['kode']= $this->data->kode;
        $data['url'] = config('app.url_fe').'/auth/forgot-password/verify?token='. $notifiable->otp->token;
        return (new MailMessage)
                    ->subject('Reset Password | '.config('app.name'))
                    ->markdown('mail.reset-password', ['data' => (object)$data]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
