<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddInvoice extends Notification
{
    use Queueable;
    private $invoice_id;

    public function __construct($invoice_id)
    {
        $this->invoice_id = $invoice_id;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = 'http://invoice.test/invoices_details/' . $this->invoice_id;

        return (new MailMessage)
            ->subject('إضافة فاتورة جديدة')
            ->line('إضافة فاتورة جديدة')
            ->action('عرض الفاتورة', $url)
            ->line('شكرًا لاستخدامك برنامجنا لإدارة الفواتير');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
