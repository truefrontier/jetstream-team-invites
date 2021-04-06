<?php

namespace Truefrontier\JetstreamTeamInvites;

use Illuminate\Support\Facades\Facade;

/**
 * Class JetstreamTeamInvitesFacade
 * @package Truefrontier\JetstreamTeamInvites
 */
class JetstreamTeamInvitesFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'jetstreamTeamInvites';
    }
}
