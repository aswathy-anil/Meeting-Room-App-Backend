<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
   // public $queue = 'emails'; // optional, name of queue

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('New Room Booking Placed')
                    ->view('emails.booking_placed');
    }
}
