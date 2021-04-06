<?php

namespace Truefrontier\TeamInvites\Database\Factories;

use Illuminate\Support\Str;
use Truefrontier\TeamInvites\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvitationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invitation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition($user_with_team = null)
    {
        if (!$user_with_team) {
            $user_with_team = app(config('team_invites.user_modal'))::factory()->create();
            $team = app(config('team_invites.team_modal'))::factory()->create([
                'user_id' => $user_with_team->id,
            ]);

            $user_with_team->current_team_id = $team->id;
            $user_with_team->save();
            $user_with_team->refresh();
        }

        return [
            'user_id' => $user_with_team->id,
            'team_id' => $user_with_team->currentTeam->id,
            'role' => 'super_admin',
            'email' => $this->faker->email,
            'code' => Str::random(40),
        ];
    }
}
