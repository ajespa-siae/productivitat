<?php

namespace App\Mail;

use App\Models\Notificacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notificacion;

    public function __construct(Notificacion $notificacion)
    {
        $this->notificacion = $notificacion;
    }

    public function build()
    {
        return $this->view('emails.notificacion')
                    ->subject('Nova notificació - SIAE Avaluació');
    }
}
