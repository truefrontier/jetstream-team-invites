<?php

namespace Truefrontier\JetstreamTeamInvites\Tests;

use Database\Factories\InvitationFactory;
use Truefrontier\JetstreamTeamInvites\Models\Invitation;

class InvitationFactoryTest extends TestCase
{
    public function test_invitation_factory_creates_an_invitation_from_a_new_owner_and_team()
    {
        $this->assertInstanceOf(InvitationFactory::class, Invitation::factory());

        $invitation = Invitation::factory()->create();

        $this->assertSame(1, Invitation::count());
        $this->assertTrue($invitation->user->ownsTeam($invitation->team));
        $this->assertSame(40, strlen($invitation->code));
    }
}
