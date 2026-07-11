<?php

namespace App\Notifications;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Book $book
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->book->title,
            'book_id' => $this->book->id,
            'message' => 'Nuevo libro disponible',
        ];
    }
}