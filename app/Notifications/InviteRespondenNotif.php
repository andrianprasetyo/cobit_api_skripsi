<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InviteRespondenNotif extends Notification
{
    use Queueable;

    private $data=null;
    private $subject = 'Undangan Kuesioner Responden';

    /**
     * Create a new notification instance.
     */
    public function __construct($data=null,$subject='')
    {
        $this->data=$data;
        if($subject !='')
        {
            $this->subject=$subject;
        }
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
        $data['email'] = $notifiable->email;
        $data['organisasi'] = $this->data->nama;
        $data['kode']= $notifiable->code;
        // $data['organisasi']=json_encode($this->data);
        $data['url'] = config('app.url_fe') . '/kuesioner/responden?code='. $notifiable->code;
        return (new MailMessage)
            ->subject($this->subject.' | ' . config('app.name'))
            ->markdown('mail.invited-responden', ['data' => (object) $data]);
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
