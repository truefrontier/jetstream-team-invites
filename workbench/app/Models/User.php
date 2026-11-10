<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Jetstream\HasTeams;
use Truefrontier\JetstreamTeamInvites\Traits\HasJetstreamTeamInvites;

class User extends Authenticatable
{
    use HasJetstreamTeamInvites;
    use HasTeams;

    protected $guarded = [];
}
