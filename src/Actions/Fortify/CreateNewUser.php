<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Team;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Events\TeamMemberAdded;
use Laravel\Jetstream\Events\AddingTeamMember;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Truefrontier\TeamInvites\Models\Invitation;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array  $input
     * @return mixed
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'invite' => ['sometimes', 'nullable'],
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(
	            User::create([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'password' => Hash::make($input['password']),
                ]),
                function (User $user) use ($input) {
                    if (isset($input['invite'])) {
                        if ($invitation = Invitation::where('code', $input['invite'])->first()) {
                            if ($team = $invitation->team) {
                                AddingTeamMember::dispatch($team, $user);

                                $team->users()->attach($user, ['role' => $invitation->role]);

                                $user->current_team_id = $team->id;
                                $user->save();

                                TeamMemberAdded::dispatch($team, $user);

                                $invitation->delete();

                                // Force immediate login
                                Auth::login($user);
                            }
                        }

	                    if (config('invitee_creates_personal_team')) {
		                    $this->createTeam($user);
	                    }
                    } elseif (config('team_owner_creates_personal_team')) {
                        $this->createTeam($user);
                    }
                },
            );
        });
    }

    /**
     * Create a personal team for the user.
     *
     * @param  User $user
     * @return void
     */
    protected function createTeam(User $user)
    {
        $user->ownedTeams()->save(
	        Team::forceCreate([
                'user_id' => $user->id,
                'name' => explode(' ', $user->name, 2)[0] . "'s Team",
                'personal_team' => true,
            ]),
        );
    }
}
