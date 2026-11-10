<?php

namespace Truefrontier\JetstreamTeamInvites\Tests;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Truefrontier\JetstreamTeamInvites\Models\Invitation;

class RegisterWithInviteTest extends TestCase
{
    private function register(array $extra = []): User
    {
        return app(CreateNewUser::class)->create(array_merge([
            'name' => 'Grace New',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $extra));
    }

    public function test_registering_with_an_invite_code_joins_the_team_and_logs_in()
    {
        $owner = $this->ownerWithTeam();
        $invitation = Invitation::create([
            'user_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'email' => 'new@example.com',
            'role' => 'editor',
        ]);

        $user = $this->register(['invite' => $invitation->code]);

        $team = $owner->currentTeam->fresh();
        $this->assertSame('editor', $team->users()->find($user->id)->membership->role);
        $this->assertSame($team->id, (int) $user->fresh()->current_team_id);
        $this->assertSame(0, $user->ownedTeams()->count());
        $this->assertSame(0, Invitation::count());
        $this->assertSame($user->id, Auth::id());
    }

    public function test_invitee_gets_a_personal_team_too_when_configured()
    {
        config(['truefrontier_team_invites.invitee_creates_personal_team' => true]);
        $owner = $this->ownerWithTeam();
        $invitation = Invitation::create([
            'user_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'email' => 'new@example.com',
        ]);

        $user = $this->register(['invite' => $invitation->code]);

        $this->assertSame(["Grace's Team"], $user->ownedTeams()->pluck('name')->all());
    }

    public function test_registering_without_an_invite_creates_no_team_by_default()
    {
        $user = $this->register();

        $this->assertSame(0, $user->ownedTeams()->count());
        $this->assertNull($user->fresh()->current_team_id);
    }

    public function test_registering_without_an_invite_creates_a_personal_team_when_configured()
    {
        config(['truefrontier_team_invites.team_owner_creates_personal_team' => true]);

        $user = $this->register();

        $this->assertSame(["Grace's Team"], $user->ownedTeams()->pluck('name')->all());
    }

    public function test_an_unknown_invite_code_still_registers_the_user_without_a_team()
    {
        $user = $this->register(['invite' => 'no-such-code']);

        $this->assertTrue($user->exists);
        $this->assertSame(0, $user->teams()->count());
    }
}
