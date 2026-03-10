<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;

    public function __construct($title = '', $message = '')
    {
        $this->title = $title;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'created_at' => now()
        ];
    }

    public function toArray($notifiable)
{
    return [
        'title' => $this->title,
        'message' => $this->message,
    ];
}
}