<?php

namespace Truefrontier\JetstreamTeamInvites\Traits;

use Truefrontier\JetstreamTeamInvites\Models\Invitation;

/**
 * Trait HasJetstreamTeamInvites
 * @package Truefrontier\JetstreamTeamInvites\Traits
 */
trait HasJetstreamTeamInvites {

	/**
	 * @return mixed
	 */
	public function invitations()
	{
		return $this->hasMany(Invitation::class);
	}
}