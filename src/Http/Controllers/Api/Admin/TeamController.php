<?php

namespace BrilliantPortal\Framework\Http\Controllers\Api\Admin;

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use BrilliantPortal\Framework\Http\Controllers\Api\Controller;
use BrilliantPortal\Framework\Http\Resources\DataWrapCollection;
use BrilliantPortal\Framework\Http\Resources\JsonResource;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Laravel\Jetstream\Contracts\InvitesTeamMembers;
use Laravel\Jetstream\Contracts\RemovesTeamMembers;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Jetstream;
use Vyuldashev\LaravelOpenApi\Annotations as OpenApi;

/**
 * Team controller.
 *
 * @since 0.1.0
 *
 * @OpenApi\PathItem()
 */
class TeamController extends Controller
{
    /**
     * Model name.
     *
     * @since 0.1.0
     *
     * @var string
     */
    protected $model = Team::class;

    /**
     * Ability.
     *
     * @since 0.1.0
     *
     * @var string
     */
    protected $ability = 'team';

    /**
     * Display a listing of all teams.
     *
     * @OpenApi\Operation(tags="Admin: Team")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\Admin\TeamsListResponse", statusCode=200)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new DataWrapCollection(Team::all());
    }

    /**
     * Create a new team.
     *
     * @OpenApi\Operation(tags="Admin: Team")
     * @OpenApi\RequestBody(factory="\BrilliantPortal\Framework\OpenApi\RequestBodies\Admin\TeamCreateRequestBody")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\Admin\TeamCreateResponse", statusCode=201)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorValidationResponse", statusCode=422)
     *
     * @param  \Illuminate\Http\Request  $request Team data.
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:'.User::class.',id',
            'name' => 'required|string|max:255',
            'personal_team' => 'nullable|boolean',
        ]);

        if (! Arr::has($validated, 'personal_team')) {
            $validated['personal_team'] = false;
        }

        $team = new Team($validated);
        $team->save();

        return response()->json(new JsonResource($team), 201);
    }

    /**
     * Display the specified team.
     *
     * @OpenApi\Operation(tags="Admin: Team")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\Admin\TeamShowResponse", statusCode=200)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorNotFoundResponse", statusCode=404)
     *
     * @param  \App\Models\Team  $team Team ID.
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        return response()->json(new JsonResource($team));
    }

    /**
     * Update the specified team.
     *
     * Update a team by supplying the changed data. Any data not in the request will remain unchanged.
     *
     * @OpenApi\Operation(tags="Admin: Team", method="PATCH")
     * @OpenApi\RequestBody(factory="\BrilliantPortal\Framework\OpenApi\RequestBodies\Admin\TeamCreateRequestBody")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\Admin\TeamShowResponse", statusCode=200)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorNotFoundResponse", statusCode=404)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorValidationResponse", statusCode=422)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team Team ID.
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'user_id' => 'integer|exists:'.User::class.',id',
            'name' => 'filled|string|max:255',
            'personal_team' => 'filled|boolean',
        ]);

        $team->fill($validated);
        $team->save();

        return response()->json(new JsonResource($team));
    }

    /**
     * Delete the specified team.
     *
     * @OpenApi\Operation(tags="Admin: Team")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\Admin\TeamDeleteResponse", statusCode=200)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorNotFoundResponse", statusCode=404)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\Admin\TeamFailedDeleteResponse", statusCode=422)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\InternalServerErrorResponse", statusCode=500)
     *
     * @param  \App\Models\Team  $team Team ID.
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        try {
            $team->delete();
        } catch (QueryException $e) {
            if (Str::contains($e->getMessage(), 'a foreign key constraint fails')) {
                $response = [
                    'message' => 'This team contains child objects that cannot be deleted in order to prevent unintended consequences. Please delete those before deleting the team.',
                ];
                $code = 422;
            } else {
                $response = [
                    'message' => $e->getMessage(),
                ];
                $code = 500;
            }

            return response()->json($response, $code);
        }

        return response()->json(new JsonResource($team));
    }

    /**
     * Invite or directly add user to the specified team.
     *
     * @since 0.2.0
     *
     * @param \Illuminate\Http\Request $request
     * @param int $teamId
     * @return \Illuminate\Http\Response
     */
    public function inviteTeamMember(Request $request, $teamId)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'role' => [
                'required',
                'string',
                Rule::in(collect(Jetstream::$roles)->pluck('key')),
            ],
        ]);

        $model = Jetstream::teamModel();
        $team = $model::whereKey($teamId)->first();

        $this->authorize('addTeamMember', $team);

        if (Features::sendsTeamInvitations()) {
            app(InvitesTeamMembers::class)->invite(Auth::user(), $team, $validated['email'], $validated['role']);
            $message = 'Invited '.$validated['email'].' to '.$team->name.' with role '.$validated['role'];
        } else {
            app(AddsTeamMembers::class)->add(Auth::user(), $team, $validated['email'], $validated['role']);
            $message = 'Added '.$validated['email'].' to '.$team->name.' with role '.$validated['role'];
        }

        $invitation = TeamInvitation::query()
            ->where('team_id', $team->id)
            ->where('email', $validated['email'])
            ->limit(1)
            ->latest()
            ->first('id');

        return response()->json([
            'invitation_id' => $invitation->id,
            'message' => $message,
        ], 201);
    }

    /**
     * Cancel invitation for user to the specified team.
     *
     * @since 0.2.0
     *
     * @param \Illuminate\Http\Request $request
     * @param int $teamId
     * @param int $invitationId
     * @return \Illuminate\Http\Response
     */
    public function cancelTeamMemberInvitation(Request $request, $teamId, $invitationId)
    {
        $teamModel = Jetstream::teamModel();
        $team = $teamModel::whereKey($teamId)->first();

        $this->authorize('removeTeamMember', $team);

        $invitationModel = Jetstream::teamInvitationModel();
        $invitationModel::whereKey($invitationId)->delete();

        return response()->json(['message' => 'Removed invitation']);
    }

    /**
     * Remove user from the specified team.
     *
     * @since 0.2.0
     *
     * @param \Illuminate\Http\Request $request
     * @param int $team
     * @param int $user
     * @param \Laravel\Jetstream\Contracts\RemovesTeamMembers $remover
     * @return \Illuminate\Http\Response
     */
    public function removeUser(Request $request, $teamId, $userId, RemovesTeamMembers $remover)
    {
        $teamModel = Jetstream::teamModel();
        $team = $teamModel::whereKey($teamId)->first();

        $this->authorize('removeTeamMember', $team);

        $userModel = Jetstream::userModel();
        $user = $userModel::whereKey($userId)->first();

        $remover->remove(Auth::user(), $team, $user);

        return response()->json(['message' => 'Removed user']);
    }
}
