# Custom Laravel Jetstream Team Invites

## Prerequisites

This package assumes you have already installed Jetstream and your `Team` and `User` Models are in the `app/Models` 
directory and namespace.

Tested with Laravel 8 to 12 (Jetstream 2 to 5) on PHP 7.4 to 8.5. CI runs each Laravel version on every pull request.

## Installation

Install this package via Composer by adding the package and the repository link:

```composer
"require": {
  "truefrontier/jetstream-team-invites": "dev-main",
},

// ...

"repositories": [
  {
    "type":"vcs",
    "url": "https://github.com/truefrontier/jetstream-team-invites"
  }
],
```

Then run:
```bash
composer update
```

Publish the config options:
```bash
php artisan vendor:publish --provider="Truefrontier\JetstreamTeamInvites\JetstreamTeamInvitesServiceProvider" --force
```

To use `Invitation::factory()` in your own tests, also publish `--tag=factories`. It needs your app's `User` and `Team` factories, which Jetstream installs.

Run the DB migration:
```bash
php artisan migrate
```

Lastly, add the Invitation Trait to your Team and User Model:
```php
use Truefrontier\JetstreamTeamInvites\Traits\HasJetstreamTeamInvites;

class User
{
    use HasJetstreamTeamInvites;
```

____

Enabling this package disables automatic team creation for new users by default. To enable it, look in the `config/truefrontier_team_invites.php` config file.

## Running the tests

```bash
composer install
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
