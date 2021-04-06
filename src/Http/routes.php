<?php

use Illuminate\Support\Facades\Route;
use Truefrontier\JetstreamTeamInvites\Http\Controllers\JetstreamTeamInvitesController;

Route::get('/team-invitations/{invitation}', [JetstreamTeamInvitesController::class, 'accept'])
	->middleware(['signed'])
	->name('team-invitations.accept');

Route::delete('/team-invitations/{invitation}', [JetstreamTeamInvitesController::class, 'destroy'])->name('team-invitations.destroy');