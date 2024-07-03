<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChangeMailNotif extends Notification
{
    use Queueable;

    private $params = null;

    /**
     * Create a new notification instance.
     */
    public function __construct($params = null)
    {
        $this->params = $params;
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
        $data['nama'] = $this->params->nama;
        $data['email'] = $this->params->email;
        $data['kode'] = $this->params->code;
        $data['url']= config('app.url_fe') . '/auth/login';

        return (new MailMessage)
            ->subject('Verifikasi Email | ' . config('app.name'))
            ->markdown('mail.verify-akun', ['data'=> (object)$data]);
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
