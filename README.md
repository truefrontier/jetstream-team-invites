# Custom Laravel Jetstream Team Invites

## Prerequisites

This package assumes you have already installed Jetstream and your `Team` and `User` Models are in the `app/Models` 
directory and namespace.

The PHP code is tested with Laravel 8 to 12 (Jetstream 2 to 5) on PHP 7.4 to 8.5, and CI runs every Laravel version on each pull request.
The register page this package ships (`--tag=vue`) is written for Jetstream 2's Inertia stack. On Jetstream 3 or later, see [Register page](#register-page).

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

Publish the config, the three Jetstream/Fortify actions and the invite email. `--force` replaces the actions Jetstream installed in `app/Actions`:
```bash
php artisan vendor:publish --provider="Truefrontier\JetstreamTeamInvites\JetstreamTeamInvitesServiceProvider" --tag=config --tag=actions --tag=views --force
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

## Register page

The invite email links to `/register?email=...&invite=...`. The register form has to send `invite` along, so the new user joins the team.

**Jetstream 2, Inertia stack:** publish the package's page.
```bash
php artisan vendor:publish --provider="Truefrontier\JetstreamTeamInvites\JetstreamTeamInvitesServiceProvider" --tag=vue --force
```

**Jetstream 3 or later, Inertia stack:** don't publish `--tag=vue`. That page imports Jetstream 2 components (`@/Jetstream/...`) that no longer exist. Instead, change the form in your own `resources/js/Pages/Auth/Register.vue`:
```js
const params = new URLSearchParams(typeof window === 'undefined' ? '' : window.location.search);

const form = useForm({
    name: '',
    email: params.get('email') ?? '',
    password: '',
    password_confirmation: '',
    terms: false,
    invite: params.get('invite'),
});
```

**Livewire stack:** in `resources/views/auth/register.blade.php`, add `<input type="hidden" name="invite" value="{{ request('invite') }}">` inside the form, and change the email input's `:value="old('email')"` to `:value="old('email', request('email'))"`.

## Running the tests

```bash
composer install
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
