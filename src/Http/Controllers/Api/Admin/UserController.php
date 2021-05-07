<?php

namespace BrilliantPortal\Framework\Http\Controllers\Api\Admin;

use BrilliantPortal\Framework\Http\Controllers\Api\Controller;
use BrilliantPortal\Framework\Http\Resources\DataWrapCollection;
use BrilliantPortal\Framework\Http\Resources\JsonResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Vyuldashev\LaravelOpenApi\Annotations as OpenApi;

/**
 * User controller.
 *
 * @since 0.1.0
 *
 * @OpenApi\PathItem()
 */
class UserController extends Controller
{
    /**
     * Model name.
     *
     * @since 0.1.0
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Ability.
     *
     * @since 0.1.0
     *
     * @var string
     */
    protected $ability = 'user';

    /**
     * Display a listing of all users.
     *
     * @OpenApi\Operation(tags="Admin: User")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\Admin\UsersListResponse", statusCode=200)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new DataWrapCollection(User::all());
    }

    /**
     * Create a new user.
     *
     * @OpenApi\Operation(tags="Admin: User")
     * @OpenApi\RequestBody(factory="\BrilliantPortal\Framework\OpenApi\RequestBodies\Admin\UserCreateRequestBody")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\Admin\UserCreateResponse", statusCode=201)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorValidationResponse", statusCode=422)
     *
     * @param  \Illuminate\Http\Request  $request User data.
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:'.User::class.',email',
            'external_id' => 'nullable|string|max:255',
        ]);

        $user = new User($validated);
        $user->password = Hash::make(Str::random(80));
        $user->save();

        return response()->json(new JsonResource($user), 201);
    }

    /**
     * Display the specified user.
     *
     * @OpenApi\Operation(tags="Admin: User")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\Admin\UserShowResponse", statusCode=200)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorNotFoundResponse", statusCode=404)
     *
     * @param  \App\Models\User  $user User ID.
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json(new JsonResource($user));
    }

    /**
     * Update the specified user.
     *
     * Update a user by supplying the changed data. Any data not in the request will remain unchanged.
     *
     * @OpenApi\Operation(tags="Admin: User", method="PATCH")
     * @OpenApi\RequestBody(factory="\BrilliantPortal\Framework\OpenApi\RequestBodies\Admin\UserCreateRequestBody")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\Admin\UserShowResponse", statusCode=200)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorNotFoundResponse", statusCode=404)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorValidationResponse", statusCode=422)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user User ID.
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'filled|string',
            'email' => 'filled|email|unique:'.User::class.',email',
            'external_id' => 'nullable|string|max:255',
        ]);

        $user->fill($validated);
        $user->save();

        return response()->json(new JsonResource($user));
    }

    /**
     * Delete the specified user.
     *
     * @OpenApi\Operation(tags="Admin: User")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\Admin\UserDeleteResponse", statusCode=200)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorNotFoundResponse", statusCode=404)
     *
     * @param  \App\Models\User  $user User ID.
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(new JsonResource($user));
    }
}
