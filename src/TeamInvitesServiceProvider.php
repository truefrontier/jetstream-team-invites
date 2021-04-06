<?php

namespace Truefrontier\TeamInvites;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Class TeamInvitesServiceProvider
 * @package Truefrontier\TeamInvites
 */
class TeamInvitesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerPublishing();
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
        });
    }

    /**
     * Get the Telescope route group configuration array.
     *
     * @return array
     */
    private function routeConfiguration()
    {
        return [
            'namespace' => 'Truefrontier\TeamInvites\Http\Controllers',
        ];
    }

    /**
     * Register the package's migrations.
     *
     * @return void
     */
    private function registerMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        }
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    private function registerPublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

	    $this->publishes([
		    __DIR__ . '/Database/Migrations' => database_path('migrations'),
	    ], 'migrations');

	    $this->publishes([
		    __DIR__ . '/Database/Factories' => database_path('factories'),
	    ], 'factories');

	    $this->publishes([
		    __DIR__.'/Actions/Fortify/CreateNewUser.php' => app_path('Actions/Fortify/CreateNewUser.php'),
		    __DIR__.'/Actions/Jetstream/AddTeamMember.php' => app_path('Actions/Jetstream/AddTeamMember'),
		    __DIR__.'/Actions/Jetstream/InviteTeamMember.php' => app_path('Actions/Jetstream/InviteTeamMember.php'),
	    ], 'actions');

	    $this->publishes([
		    __DIR__.'/config/team_invites.php' => config_path('team_invites.php'),
	    ], 'config');

	    $this->publishes([
		    __DIR__.'/resources/emails' => resource_path('views/vendor/truefrontier/team_invites'),
	    ], 'views');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
