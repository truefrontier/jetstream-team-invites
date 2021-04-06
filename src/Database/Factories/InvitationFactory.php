<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Str;
use Truefrontier\JetstreamTeamInvites\Models\Invitation;
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
            $user_with_team = User::factory()->create();
            $team = Team::factory()->create([
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
