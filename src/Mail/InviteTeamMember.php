<?php

namespace Truefrontier\TeamInvites\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Truefrontier\TeamInvites\Models\Invitation;
use Illuminate\Notifications\Messages\MailMessage;

class InviteTeamMember extends Mailable
{
    use Queueable, SerializesModels;

    public $invite;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invitation $invite)
    {
        $this->invite = $invite;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.teams.invite')
            ->subject($this->invite->user->name . ' invites you to join ' . $this->invite->team->name)
            ->with(['invite' => $this->invite]);
    }
}
