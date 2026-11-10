<?php

namespace Truefrontier\JetstreamTeamInvites\Tests;

use App\Actions\Jetstream\InviteTeamMember;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Truefrontier\JetstreamTeamInvites\Mail\InviteTeamMember as InviteMail;
use Truefrontier\JetstreamTeamInvites\Models\Invitation;

class InviteTest extends TestCase
{
    public function test_owner_invite_stores_an_invitation_and_emails_the_invitee()
    {
        Mail::fake();
        $owner = $this->ownerWithTeam();

        app(InviteTeamMember::class)->invite($owner, $owner->currentTeam, 'new@example.com', 'editor');

        $invitation = Invitation::sole();
        $this->assertSame($owner->id, (int) $invitation->user_id);
        $this->assertSame($owner->currentTeam->id, (int) $invitation->team_id);
        $this->assertSame('new@example.com', $invitation->email);
        $this->assertSame('editor', $invitation->role);
        $this->assertSame(40, strlen($invitation->code));
        Mail::assertSent(InviteMail::class, function (InviteMail $mail) use ($invitation) {
            return $mail->hasTo('new@example.com') && $mail->invite->is($invitation);
        });
    }

    public function test_someone_who_does_not_own_the_team_cannot_invite()
    {
        Mail::fake();
        $owner = $this->ownerWithTeam();
        $stranger = $this->user('stranger@example.com');

        try {
            app(InviteTeamMember::class)->invite($stranger, $owner->currentTeam, 'new@example.com', null);
            $this->fail('Expected an AuthorizationException.');
        } catch (AuthorizationException $e) {
            $this->assertSame(0, Invitation::count());
            Mail::assertNothingSent();
        }
    }

    public function test_a_current_member_cannot_be_invited()
    {
        Mail::fake();
        $owner = $this->ownerWithTeam();

        try {
            app(InviteTeamMember::class)->invite($owner, $owner->currentTeam, 'ada@example.com', null);
            $this->fail('Expected a ValidationException.');
        } catch (ValidationException $e) {
            $this->assertSame(['This user already belongs to the team.'], $e->errors()['email']);
            $this->assertSame(0, Invitation::count());
            Mail::assertNothingSent();
        }
    }

    public function test_the_same_email_cannot_be_invited_to_the_same_team_twice()
    {
        Mail::fake();
        $owner = $this->ownerWithTeam();
        app(InviteTeamMember::class)->invite($owner, $owner->currentTeam, 'new@example.com', null);

        try {
            app(InviteTeamMember::class)->invite($owner, $owner->currentTeam, 'new@example.com', null);
            $this->fail('Expected a ValidationException.');
        } catch (ValidationException $e) {
            $this->assertSame(['This user has already been invited to the team.'], $e->errors()['email']);
            $this->assertSame(1, Invitation::count());
            Mail::assertSent(InviteMail::class, 1);
        }
    }

    public function test_the_same_email_can_be_invited_to_a_different_team()
    {
        Mail::fake();
        $owner = $this->ownerWithTeam();
        app(InviteTeamMember::class)->invite($owner, $owner->currentTeam, 'new@example.com', null);
        $other = $this->user('other-owner@example.com');
        $otherTeam = \App\Models\Team::forceCreate(['user_id' => $other->id, 'name' => 'Other', 'personal_team' => true]);

        app(InviteTeamMember::class)->invite($other, $otherTeam, 'new@example.com', null);

        $this->assertSame(2, Invitation::count());
    }

    public function test_invite_email_names_the_inviter_and_team_and_links_to_register_with_the_code()
    {
        $owner = $this->ownerWithTeam();
        $invitation = Invitation::create([
            'user_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'email' => 'new@example.com',
            'role' => 'editor',
        ]);

        $mail = new InviteMail($invitation);
        $html = $mail->render();

        $this->assertSame("Ada Owner invites you to join Ada's Team", $mail->subject);
        $this->assertStringContainsString('Ada Owner wants you to join their team:', $html);
        $this->assertStringContainsString(
            e(url('/register') . '?email=new%40example.com&invite=' . $invitation->code),
            $html
        );
    }

    public function test_invitation_keeps_a_code_it_was_given()
    {
        $owner = $this->ownerWithTeam();

        $invitation = Invitation::create([
            'user_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'email' => 'new@example.com',
            'code' => 'given-code',
        ]);

        $this->assertSame('given-code', $invitation->code);
    }
}
