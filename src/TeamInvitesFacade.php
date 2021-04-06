<?php

namespace Truefrontier\TeamInvites;

use Illuminate\Support\Facades\Facade;

/**
 * Class TeamInvitesFacade
 * @package Truefrontier\TeamInvites
 */
class TeamInvitesFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'teamInvites';
    }
}
