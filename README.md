# Laravel Jetstream - Custom Team Invites


## Installation

Install this package via Composer:

```composer
"require": {
  "truefrontier/team-invites": "dev-master",
}

"repositories": [
  {
    "type":"vcs",
    "url": "https://github.com/truefrontier/team-invites"
  }
]
```

Publish the config options:
```bash
php artisan vendor:publish --provider="Truefrontier\TeamInvites\TeamInvitesServiceProvider" --tag="config"
```

## Packages Used

- [Spatie Schemaless Attributes](https://github.com/spatie/laravel-schemaless-attributes)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
