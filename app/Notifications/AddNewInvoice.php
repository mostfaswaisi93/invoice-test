<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class AddNewInvoice extends Notification
{
    use Queueable;
    private $invoices;

    public function __construct(invoices $invoices)
    {
        $this->invoices = $invoices;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            //'data' => $this->details['body']
            'id' => $this->invoices->id,
            'title' => 'تم إضافة فاتورة جديد بواسطة :',
            'user' => Auth::user()->name,
        ];
    }
}
