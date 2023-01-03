<?php

namespace BrilliantPortal\Framework\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Show the form to create the first team.
     *
     * @since 1.0.0
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        return view('brilliant-portal-framework::teams.create-first');
    }

    /**
     * Store the user’s team.
     *
     * @since 1.0.0
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['personal_team'] = true;

        $team = new Team();
        $team->forceFill($validated);
        $team->save();

        return redirect(route('dashboard'));
    }

    /**
     * Show the already invited message.
     *
     * @since 1.0.0
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function alreadyInvited()
    {
        return view('brilliant-portal-framework::teams.already-invited');
    }
}
