# Custom Laravel Jetstream Team Invites

## Prerequisites

This package assumes you have already installed Jetstream and your `Team` and `User` Models are in the `app/Models` 
directory and namespace.

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
    "url": "https://github.com/peachysoftware/jetstream-team-invites"
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

Lastly, add the Invitation Trait to your Team and User Model:
```php
use Truefrontier\JetstreamTeamInvites\Traits\HasJetstreamTeamInvites;

class User
{
    use HasJetstreamTeamInvites;
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
