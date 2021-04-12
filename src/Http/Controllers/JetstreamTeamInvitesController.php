<?php

namespace Truefrontier\JetstreamTeamInvites\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Truefrontier\JetstreamTeamInvites\Models\Invitation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class JetstreamTeamInvitesController extends BaseController
{
	use AuthorizesRequests;
	use DispatchesJobs;
	use ValidatesRequests;

	/**
	 * Accept a team invitation.
	 *
	 * @param Request $request
	 * @param Invitation $invitation
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function accept(Request $request, Invitation $invitation)
	{
		app(AddsTeamMembers::class)->add(
			$invitation->team->owner,
			$invitation->team,
			$invitation->email,
			$invitation->role,
        );

		$invitation->delete();

		return redirect(config('fortify.home'))->banner(
			__('Great! You have accepted the invitation to join the :team team.', [
				'team' => $invitation->team->name,
			]),
        );
	}

	/**
	 * Cancel the given team invitation.
	 *
	 * @param Request $request
	 * @param Invitation $invitation
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy(Request $request, Invitation $invitation)
	{
		if (!Gate::forUser($request->user())->check('removeTeamMember', $invitation->team)) {
			throw new AuthorizationException();
		}

		$invitation->delete();

		return back(303);
	}
}