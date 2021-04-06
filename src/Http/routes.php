<?php

use Illuminate\Support\Facades\Route;
use Truefrontier\TeamInvites\Http\Controllers\TeamInvitesController;

Route::get('/team-invitations/{invitation}', [TeamInvitesController::class, 'accept'])
	->middleware(['signed'])
	->name('team-invitations.accept');

Route::delete('/team-invitations/{invitation}', [TeamInvitesController::class, 'destroy'])->name('team-invitations.destroy');