@component('mail::message')
{{ $invite->user->name }} wants you to join their team: {{ $invite->team->name }}

@component('mail::button', ['url' => route('register', ['email' => $invite->email, 'invite' => $invite->code])])
Create Your Account
@endcomponent

Looking forward to having you on the team!
@endcomponent
