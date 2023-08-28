<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InviteUserNotif extends Notification implements ShouldQueue
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
        $data['nama'] = $notifiable->nama;
        $data['kode'] = $notifiable->token;
        // $data['kode'] = $this->data->code;
        $data['url'] = config('app.url_fe') . '/auth/verify?token=' . $notifiable->token;

        return (new MailMessage)
            ->subject('Verifikasi Akun | ' . config('app.name'))
            ->markdown('mail.verify-akun', ['data' => (object) $data]);
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
