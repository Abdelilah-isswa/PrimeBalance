<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompanyInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $role;
    public $inviterName;
    public $token;

    public function __construct($company, $role, $inviterName, $token)
    {
        $this->company = $company;
        $this->role = $role;
        $this->inviterName = $inviterName;
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Invitation to join ' . $this->company->name)
                    ->view('emails.company-invitation');
    }
}
