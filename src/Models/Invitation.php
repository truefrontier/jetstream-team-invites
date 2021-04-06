<?php

namespace Truefrontier\JetstreamTeamInvites\Models;

use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invitation extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['user_id', 'team_id', 'role', 'email', 'code'];

    protected static function booted()
    {
        static::creating(function ($invitation) {
            $invitation->code = $invitation->code ?: Str::random(40);
            return $invitation;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
