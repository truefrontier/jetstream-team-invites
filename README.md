# Laravel Jetstream - Custom Team Invites

## Installation

This package assumes you have already installed Jetstream and your `Team` and `User` Models are in the `app/Models` 
directory and namespace.

## Installation

Install this package via Composer by added the package and the repository link:

```composer
"require": {
  "truefrontier/team-invites": "dev-main",
}

"repositories": [
  {
    "type":"vcs",
    "url": "https://github.com/truefrontier/team-invites"
  }
]
```

Then run:
```bash
composer update
```

Publish the config options:
```bash
php artisan vendor:publish --provider="Truefrontier\TeamInvites\TeamInvitesServiceProvider" --force
```

Lastly, add the Invitation Trait to your Team and User Model:
```php
use Truefrontier\TeamInvites\Traits\HasTeamInvites;

class User
{
    use HasTeamInvites;
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
