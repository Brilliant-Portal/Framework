<?php

namespace BrilliantPortal\Framework\Http\Controllers\Api;

use BrilliantPortal\Framework\Http\Controllers\Api\Controller;
use BrilliantPortal\Framework\Http\Resources\DataWrapCollection;
use BrilliantPortal\Framework\Http\Resources\JsonResource;
use BrilliantPortal\Framework\Rules\ClassExists;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Vyuldashev\LaravelOpenApi\Annotations as OpenApi;

/**
 * Generic controller.
 *
 * @since 0.1.0
 *
 * @OpenApi\PathItem()
 */
class GenericController extends Controller
{
    /**
     * Model name.
     *
     * @since 0.1.0
     *
     * @var string
     */
    protected $model;

    /**
     * Ability.
     *
     * @since 0.1.0
     *
     * @var string
     */
    protected $ability;

    public function __construct(Request $request)
    {
        $validated = $request->validate([
            'type' => [
                'string',
                Rule::notIn([
                    'team',
                    'Team',
                    'user',
                    'User',
                ]),
            ],
        ]);

        $type = $validated['type'];
        if (Str::of($type)->startsWith('\\')) {
            $this->model = $type;
        } else {
            $this->model = '\App\Models\\'.$type;
        }

        $this->ability = Str::of($validated['type'])->lower()->__toString();

        parent::__construct();
    }

    /**
     * Display a listing of all objects.
     *
     * @OpenApi\Operation(tags="Generic Object")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\GenericObjectsListResponse", statusCode=200)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new DataWrapCollection($this->model::all());
    }

    /**
     * Create a new object.
     *
     * @OpenApi\Operation(tags="Generic Object")
     * @OpenApi\RequestBody(factory="\BrilliantPortal\Framework\OpenApi\RequestBodies\GenericObjectCreateRequestBody")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\GenericObjectCreateResponse", statusCode=201)
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
            'type' => ['required', 'string', new ClassExists],
            'data' => 'required',
        ]);

        $object = new $this->model($validated['data']);
        $object->save();

        return response()->json(new JsonResource($object), 201);
    }

    /**
     * Display the specified object.
     *
     * @OpenApi\Operation(tags="Generic Object")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\GenericObjectShowResponse", statusCode=200)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorNotFoundResponse", statusCode=404)
     *
     * @param  \App\Models\Model  $model Model.
     * @return \Illuminate\Http\Response
     */
    public function show($model)
    {
        return response()->json(new JsonResource($model));
    }

    /**
     * Update the specified object.
     *
     * Update a user by supplying the changed data. Any data not in the request will remain unchanged.
     *
     * @OpenApi\Operation(tags="Generic Object", method="PATCH")
     * @OpenApi\RequestBody(factory="\BrilliantPortal\Framework\OpenApi\RequestBodies\GenericObjectCreateRequestBody")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\GenericObjectShowResponse", statusCode=200)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorNotFoundResponse", statusCode=404)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorValidationResponse", statusCode=422)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Model  $model Model.
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $model)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', ClassExists::class],
            'data' => 'required',
        ]);

        $model->fill($validated['data']);
        $model->save();

        return response()->json(new JsonResource($model));
    }

    /**
     * Delete the specified object.
     *
     * @OpenApi\Operation(tags="Generic Object")
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\GenericObjectDeleteResponse", statusCode=200)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\UnauthenticatedResponse", statusCode=401)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ForbiddenResponse", statusCode=403)
     * @OpenApi\Response(factory="\BrilliantPortal\Framework\OpenApi\Responses\ErrorNotFoundResponse", statusCode=404)
     *
     * @param  \App\Models\Model  $model Model.
     * @return \Illuminate\Http\Response
     */
    public function destroy($model)
    {
        $model->delete();

        return response()->json(new JsonResource($model));
    }
}
