<?php

namespace Truefrontier\JetstreamTeamInvites\Tests;

use App\Actions\Jetstream\AddTeamMember;
use App\Models\Team;
use App\Models\User;
use App\Policies\TeamPolicy;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Laravel\Jetstream\Jetstream;
use Orchestra\Testbench\TestCase as Orchestra;
use Truefrontier\JetstreamTeamInvites\JetstreamTeamInvitesServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [JetstreamTeamInvitesServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('fortify.home', '/dashboard');
        // The invite email is published to resources/views/emails in an app.
        $app['config']->set('view.paths', array_merge(
            $app['config']->get('view.paths'),
            [realpath(__DIR__ . '/../src/resources')]
        ));
    }

    protected function setUp(): void
    {
        parent::setUp();

        // The tables a Jetstream app with teams already has.
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('current_team_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('name');
            $table->boolean('personal_team');
            $table->timestamps();
        });
        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');
            $table->foreignId('user_id');
            $table->string('role')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'user_id']);
        });
        Schema::create('team_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');
            $table->string('email');
            $table->string('role')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'email']);
        });

        // The package's own migration, as `php artisan migrate` runs it.
        Artisan::call('migrate', [
            '--path' => realpath(__DIR__ . '/../src/Database/Migrations'),
            '--realpath' => true,
        ]);

        // What the app's JetstreamServiceProvider and AuthServiceProvider set up.
        Gate::policy(Team::class, TeamPolicy::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        if (! RedirectResponse::hasMacro('banner')) {
            RedirectResponse::macro('banner', function ($message) {
                return $this->with('flash', ['bannerStyle' => 'success', 'banner' => $message]);
            });
        }
        // Fortify's register route, which the invite email links to.
        Route::get('/register', function () {
            return 'register';
        })->name('register');
        Route::getRoutes()->refreshNameLookups();
    }

    protected function ownerWithTeam(): User
    {
        $owner = User::create([
            'name' => 'Ada Owner',
            'email' => 'ada@example.com',
            'password' => 'secret',
        ]);
        $team = Team::forceCreate([
            'user_id' => $owner->id,
            'name' => "Ada's Team",
            'personal_team' => true,
        ]);
        $owner->forceFill(['current_team_id' => $team->id])->save();

        return $owner->fresh();
    }

    protected function user(string $email): User
    {
        return User::create(['name' => 'Someone', 'email' => $email, 'password' => 'secret']);
    }
}
