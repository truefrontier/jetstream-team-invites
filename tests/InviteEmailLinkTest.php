<?php

namespace Truefrontier\JetstreamTeamInvites\Tests;

use Truefrontier\JetstreamTeamInvites\Mail\InviteTeamMember as InviteMail;
use Truefrontier\JetstreamTeamInvites\Models\Invitation;

class InviteEmailLinkTest extends TestCase
{
    public function test_login_to_join_link_in_the_email_adds_an_existing_user_to_the_team()
    {
        $owner = $this->ownerWithTeam();
        $invitee = $this->user('new@example.com');
        $invitation = Invitation::create([
            'user_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'email' => 'new@example.com',
            'role' => 'editor',
        ]);

        $html = (new InviteMail($invitation))->render();
        preg_match_all('/href="([^"]*team-invitations[^"]*)"/', $html, $links);
        $this->assertCount(1, array_unique($links[1]));

        $this->get(html_entity_decode($links[1][0]))->assertRedirect('/dashboard');

        $team = $owner->currentTeam->fresh();
        $this->assertSame('editor', $team->users()->find($invitee->id)->membership->role);
        $this->assertSame(0, Invitation::count());
    }
}
