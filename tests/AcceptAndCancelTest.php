<?php

namespace Truefrontier\JetstreamTeamInvites\Tests;

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Truefrontier\JetstreamTeamInvites\Models\Invitation;

class AcceptAndCancelTest extends TestCase
{
    private function invite(User $owner, string $email, ?string $role = 'editor'): Invitation
    {
        return Invitation::create([
            'user_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'email' => $email,
            'role' => $role,
        ]);
    }

    public function test_signed_accept_link_adds_the_invitee_with_their_role_and_uses_up_the_invitation()
    {
        $owner = $this->ownerWithTeam();
        $invitee = $this->user('new@example.com');
        $invitation = $this->invite($owner, 'new@example.com');

        $this->get(URL::signedRoute('team-invitations.accept', ['invitation' => $invitation->code]))
            ->assertRedirect('/dashboard')
            ->assertSessionHas('flash.banner', "Great! You have accepted the invitation to join the Ada's Team team.");

        $team = $owner->currentTeam->fresh();
        $this->assertSame('editor', $team->users()->find($invitee->id)->membership->role);
        $this->assertSame(0, Invitation::count());
    }

    public function test_accept_link_without_a_signature_is_refused()
    {
        $owner = $this->ownerWithTeam();
        $this->user('new@example.com');
        $invitation = $this->invite($owner, 'new@example.com');

        $this->get(route('team-invitations.accept', ['invitation' => $invitation->code]))->assertStatus(403);

        $this->assertFalse($owner->currentTeam->fresh()->hasUserWithEmail('new@example.com'));
        $this->assertSame(1, Invitation::count());
    }

    public function test_signed_accept_link_for_an_unknown_code_is_refused()
    {
        $this->ownerWithTeam();

        $this->get(URL::signedRoute('team-invitations.accept', ['invitation' => 'no-such-code']))->assertStatus(403);
    }

    public function test_team_owner_can_cancel_an_invitation()
    {
        $owner = $this->ownerWithTeam();
        $invitation = $this->invite($owner, 'new@example.com');

        $this->actingAs($owner)
            ->delete(route('team-invitations.destroy', ['invitation' => $invitation->code]))
            ->assertStatus(303);

        $this->assertSame(0, Invitation::count());
    }

    public function test_someone_who_does_not_own_the_team_cannot_cancel_an_invitation()
    {
        $owner = $this->ownerWithTeam();
        $invitation = $this->invite($owner, 'new@example.com');

        $this->actingAs($this->user('stranger@example.com'))
            ->delete(route('team-invitations.destroy', ['invitation' => $invitation->code]))
            ->assertStatus(403);

        $this->assertSame(1, Invitation::count());
    }
}
