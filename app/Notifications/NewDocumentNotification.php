<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewDocumentNotification extends Notification
{
    use Queueable;

    protected $document;

    public function __construct($document)
    {
        $this->document = $document;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Dokumen Baru: ' . $this->document->title)
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Dokumen baru telah diunggah ke akun Sinergi PAS Anda.')
            ->line('Judul Dokumen: ' . $this->document->title)
            ->line('Kategori: ' . $this->document->category->name)
            ->action('Lihat Dokumen', url('/documents'))
            ->line('Terima kasih telah menggunakan layanan kami!');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Dokumen Baru Tersedia',
            'message' => 'Dokumen "' . $this->document->title . '" telah diunggah ke akun Anda.',
            'document_id' => $this->document->id,
            'category' => $this->document->category->name,
        ];
    }
}
