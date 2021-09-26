@component('mail::message')
{{ $invite->user->name }} wants you to join their team: {{ $invite->team->name }}

@component('mail::button', ['url' => route('register', ['email' => $invite->email, 'invite' => $invite->code])])
Create Your Account
@endcomponent

<br><br>

Already have a login?

@component('mail::button', ['url' => route('team-invitations.accept', ['invitation' => $invite])])
Login To Join
@endcomponent

Looking forward to having you on the team!
@endcomponent
