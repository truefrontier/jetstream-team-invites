<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function addTeamMember(User $user, Team $team)
    {
        return $user->ownsTeam($team);
    }

    public function removeTeamMember(User $user, Team $team)
    {
        return $user->ownsTeam($team);
    }
}
