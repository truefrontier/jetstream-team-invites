<?php

namespace Truefrontier\TeamInvites\Models;

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
        return $this->belongsTo(config('team_invites.user_modal'));
    }

    public function team()
    {
        return $this->belongsTo(config('team_invites.team_modal'));
    }
}
