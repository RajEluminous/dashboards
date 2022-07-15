<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCustomerNamesMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $lastMonthYear = date("M Y", strtotime("first day of previous month"));
        $viewName = 'emails.new_customer';
        $this->from('admin@limitlessfactor.com', 'Limitless Factor');
        $this->subject("The Amazing You Customer List [$lastMonthYear]");

        return $this->view($viewName, ['user' => $this->data]);
    }
}
