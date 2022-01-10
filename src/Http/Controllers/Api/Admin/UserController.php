<?php

namespace BrilliantPortal\Framework\Http\Controllers\Api\Admin;

use App\Models\User;
use BrilliantPortal\Framework\Http\Controllers\Api\Controller;
use BrilliantPortal\Framework\Http\Resources\DataWrapCollection;
use BrilliantPortal\Framework\Http\Resources\JsonResource;
use BrilliantPortal\Framework\OpenApi\RequestBodies\Admin as RequestBodies;
use BrilliantPortal\Framework\OpenApi\Responses as GeneralResponses;
use BrilliantPortal\Framework\OpenApi\Responses\Admin as AdminResponses;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

/**
 * User controller.
 *
 * @since 0.1.0
 */
#[OpenApi\PathItem()]
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
     * @return \Illuminate\Http\Response
     */
    #[OpenApi\Operation(tags: ['Admin: User'])]
    #[OpenApi\Response(factory: AdminResponses\UsersList::class, statusCode: 200)]
    #[OpenApi\Response(factory: GeneralResponses\Unauthenticated::class, statusCode: 401)]
    #[OpenApi\Response(factory: GeneralResponses\Forbidden::class, statusCode: 403)]
    public function index()
    {
        return new DataWrapCollection(User::all());
    }

    /**
     * Create a new user.
     *
     * @param  \Illuminate\Http\Request  $request User data.
     * @return \Illuminate\Http\Response
     */
    #[OpenApi\Operation(tags: ['Admin: User'])]
    #[OpenApi\RequestBody(factory: RequestBodies\UserCreate::class)]
    #[OpenApi\Response(factory: AdminResponses\UserCreate::class, statusCode: 201)]
    #[OpenApi\Response(factory: GeneralResponses\Unauthenticated::class, statusCode: 401)]
    #[OpenApi\Response(factory: GeneralResponses\Forbidden::class, statusCode: 403)]
    #[OpenApi\Response(factory: GeneralResponses\ErrorValidation::class, statusCode: 422)]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:' . User::class . ',email',
            'external_id' => 'nullable|max:255',
        ]);

        $user = new User($validated);
        $user->password = Hash::make(Str::random(80));
        $user->save();

        event(new Registered($user));

        return response()->json(new JsonResource($user), 201);
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user User ID.
     * @return \Illuminate\Http\Response
     */
    #[OpenApi\Operation(tags: ['Admin: User'])]
    #[OpenApi\Response(factory: AdminResponses\UserShow::class, statusCode: 200)]
    #[OpenApi\Response(factory: GeneralResponses\Unauthenticated::class, statusCode: 401)]
    #[OpenApi\Response(factory: GeneralResponses\Forbidden::class, statusCode: 403)]
    #[OpenApi\Response(factory: GeneralResponses\ErrorNotFound::class, statusCode: 404)]
    public function show(User $user)
    {
        return response()->json(new JsonResource($user));
    }

    /**
     * Update the specified user.
     *
     * Update a user by supplying the changed data. Any data not in the request will remain unchanged.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user User ID.
     * @return \Illuminate\Http\Response
     */
    #[OpenApi\Operation(tags: ['Admin: User'], method: 'PATCH')]
    #[OpenApi\RequestBody(factory: RequestBodies\UserCreate::class)]
    #[OpenApi\Response(factory: AdminResponses\UserShow::class, statusCode: 200)]
    #[OpenApi\Response(factory: GeneralResponses\Unauthenticated::class, statusCode: 401)]
    #[OpenApi\Response(factory: GeneralResponses\Forbidden::class, statusCode: 403)]
    #[OpenApi\Response(factory: GeneralResponses\ErrorNotFound::class, statusCode: 404)]
    #[OpenApi\Response(factory: GeneralResponses\ErrorValidation::class, statusCode: 422)]
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'filled|string',
            'email' => [
                'filled',
                'email',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'external_id' => 'nullable|max:255',
        ]);

        $user->fill($validated);
        $user->save();

        return response()->json(new JsonResource($user));
    }

    /**
     * Delete the specified user.
     *
     * @param  \App\Models\User  $user User ID.
     * @return \Illuminate\Http\Response
     */
    #[OpenApi\Operation(tags: ['Admin: User'])]
    #[OpenApi\Response(factory: AdminResponses\UserDelete::class, statusCode: 200)]
    #[OpenApi\Response(factory: GeneralResponses\Unauthenticated::class, statusCode: 401)]
    #[OpenApi\Response(factory: GeneralResponses\Forbidden::class, statusCode: 403)]
    #[OpenApi\Response(factory: GeneralResponses\ErrorNotFound::class, statusCode: 404)]
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(new JsonResource($user));
    }
}
