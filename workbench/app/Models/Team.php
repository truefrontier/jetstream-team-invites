<?php

namespace App\Models;

use Laravel\Jetstream\Team as JetstreamTeam;
use Truefrontier\JetstreamTeamInvites\Traits\HasJetstreamTeamInvites;

class Team extends JetstreamTeam
{
    use HasJetstreamTeamInvites;

    protected $guarded = [];

    protected $casts = ['personal_team' => 'boolean'];
}
