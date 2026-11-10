<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\Team as JetstreamTeam;
use Truefrontier\JetstreamTeamInvites\Traits\HasJetstreamTeamInvites;

class Team extends JetstreamTeam
{
    use HasFactory;
    use HasJetstreamTeamInvites;

    protected $guarded = [];

    protected $casts = ['personal_team' => 'boolean'];
}
