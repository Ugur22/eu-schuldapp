<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Appointments extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public $appointment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.appointment')
        ->with([
            'subject' => 'Appointment with '. trim($this->appointment->client->firstname .' '. $this->appointment->client->lastname),
            'datetime' => \Carbon\Carbon::parse($this->appointment->event_date)->format('d-m-Y H:i'),
            'notes' => $this->appointment->notes,
            'place' => $this->appointment->location->name,
        ]);
    }
}
