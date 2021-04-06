<?php

namespace Truefrontier\TeamInvites\Traits;

use Truefrontier\TeamInvites\Models\Invitation;

/**
 * Trait HasTeamInvites
 * @package Truefrontier\TeamInvites\Traits
 */
trait HasTeamInvites {

	/**
	 * @return mixed
	 */
	public function invitations()
	{
		return $this->hasMany(Invitation::class);
	}
}