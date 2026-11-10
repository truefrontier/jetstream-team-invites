<?php

namespace Truefrontier\JetstreamTeamInvites\Tests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Truefrontier\JetstreamTeamInvites\Models\Invitation;

/**
 * Requests here carry a session cookie, as a browser does. actingAs() and
 * withSession() keep state in memory, so they pass whether or not a route
 * starts the session.
 */
class RealSessionTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $dir = sys_get_temp_dir() . '/jti-sessions-' . getmypid();
        @mkdir($dir);
        $app['config']->set('session.driver', 'file');
        $app['config']->set('session.files', $dir);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob(config('session.files') . '/*') ?: []);
        @rmdir(config('session.files'));
        parent::tearDown();
    }

    /** Save $data to a session on disk, forget everything in memory, return its id. */
    private function storedSession(array $data = []): string
    {
        $store = app('session')->driver();
        $store->setId(Str::random(40));
        foreach ($data as $key => $value) {
            $store->put($key, $value);
        }
        $store->save();
        $this->forgetMemory();

        return $store->getId();
    }

    private function forgetMemory(): void
    {
        Auth::forgetGuards();
        app('session')->forgetDrivers();
        app()->forgetInstance('session.store');
    }

    private function invite($owner): Invitation
    {
        return Invitation::create([
            'user_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'email' => 'new@example.com',
        ]);
    }

    public function test_logged_in_owner_can_cancel_an_invitation()
    {
        $owner = $this->ownerWithTeam();
        $invitation = $this->invite($owner);
        $session = $this->storedSession([Auth::guard()->getName() => $owner->id]);

        $this->withCookie(config('session.cookie'), $session)
            ->delete(route('team-invitations.destroy', ['invitation' => $invitation->code]))
            ->assertStatus(303);

        $this->assertSame(0, Invitation::count());
    }

    public function test_accept_banner_reaches_the_next_page()
    {
        Route::middleware('web')->get('/dashboard', function () {
            return session('flash.banner', 'no banner');
        });
        $owner = $this->ownerWithTeam();
        $this->user('new@example.com');
        $invitation = $this->invite($owner);

        $accepted = $this->get(URL::signedRoute('team-invitations.accept', ['invitation' => $invitation->code]));
        $accepted->assertRedirect('/dashboard');
        // Send the cookie back exactly as the browser got it (Laravel 8's getCookie() ignores the decrypt flag).
        $session = $accepted->getCookie(config('session.cookie'), false);
        $this->forgetMemory();

        $this->withUnencryptedCookie(config('session.cookie'), $session ? $session->getValue() : '')
            ->get('/dashboard')
            ->assertSee("Great! You have accepted the invitation to join the Ada's Team team.", false);
    }
}
