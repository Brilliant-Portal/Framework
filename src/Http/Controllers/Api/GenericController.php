<?php

namespace BrilliantPortal\Framework\Http\Controllers\Api;

use BrilliantPortal\Framework\Http\Controllers\Api\Controller;
use BrilliantPortal\Framework\Http\Resources\DataWrapCollection;
use BrilliantPortal\Framework\Http\Resources\JsonResource;
use BrilliantPortal\Framework\OpenApi\Parameters as Parameters;
use BrilliantPortal\Framework\OpenApi\RequestBodies as RequestBodies;
use BrilliantPortal\Framework\OpenApi\Responses as Responses;
use BrilliantPortal\Framework\Rules\ClassExists;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

/**
 * Generic controller.
 *
 * @since 0.1.0
 */
#[OpenApi\PathItem()]
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

        $type = Arr::get($validated, 'type');
        if (Str::of($type)->startsWith('\\')) {
            $this->model = $type;
        } else {
            $this->model = '\App\Models\\' . $type;
        }

        $this->ability = Str::of($type)->lower()->__toString();

        parent::__construct();
    }

    /**
     * Display a listing of all objects.
     *
     * @return \Illuminate\Http\Response
     */
    #[OpenApi\Operation(tags: ['Generic Object'])]
    #[OpenApi\Parameters(factory: Parameters\GenericObjectTypeWithoutId::class)]
    #[OpenApi\Response(factory: Responses\GenericObjectsList::class, statusCode: 200)]
    #[OpenApi\Response(factory: Responses\Unauthenticated::class, statusCode: 401)]
    #[OpenApi\Response(factory: Responses\Forbidden::class, statusCode: 403)]
    public function index()
    {
        return new DataWrapCollection($this->model::all());
    }

    /**
     * Create a new object.
     *
     * @param  \Illuminate\Http\Request  $request User data.
     * @return \Illuminate\Http\Response
     */
    #[OpenApi\Operation(tags: ['Generic Object'])]
    #[OpenApi\Parameters(factory: Parameters\GenericObjectTypeWithoutId::class)]
    #[OpenApi\RequestBody(factory: RequestBodies\GenericObjectCreate::class)]
    #[OpenApi\Response(factory: Responses\GenericObjectCreate::class, statusCode: 201)]
    #[OpenApi\Response(factory: Responses\Unauthenticated::class, statusCode: 401)]
    #[OpenApi\Response(factory: Responses\Forbidden::class, statusCode: 403)]
    #[OpenApi\Response(factory: Responses\ErrorValidation::class, statusCode: 422)]
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
     * @param  \App\Models\Model  $model Model.
     * @return \Illuminate\Http\Response
     */
    #[OpenApi\Operation(tags: ['Generic Object'])]
    #[OpenApi\Parameters(factory: Parameters\GenericObjectType::class)]
    #[OpenApi\Response(factory: Responses\GenericObjectShow::class, statusCode: 200)]
    #[OpenApi\Response(factory: Responses\Unauthenticated::class, statusCode: 401)]
    #[OpenApi\Response(factory: Responses\Forbidden::class, statusCode: 403)]
    #[OpenApi\Response(factory: Responses\ErrorNotFound::class, statusCode: 404)]
    public function show($model)
    {
        return response()->json(new JsonResource($model));
    }

    /**
     * Update the specified object.
     *
     * Update a user by supplying the changed data. Any data not in the request will remain unchanged.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Model  $model Model.
     * @return \Illuminate\Http\Response
     */
    #[OpenApi\Operation(tags: ['Generic Object'], method: 'PATCH')]
    #[OpenApi\Parameters(factory: Parameters\GenericObjectType::class)]
    #[OpenApi\RequestBody(factory: RequestBodies\GenericObjectCreate::class)]
    #[OpenApi\Response(factory: Responses\GenericObjectShow::class, statusCode: 200)]
    #[OpenApi\Response(factory: Responses\Unauthenticated::class, statusCode: 401)]
    #[OpenApi\Response(factory: Responses\Forbidden::class, statusCode: 403)]
    #[OpenApi\Response(factory: Responses\ErrorNotFound::class, statusCode: 404)]
    #[OpenApi\Response(factory: Responses\ErrorValidation::class, statusCode: 422)]
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
     * @param  \App\Models\Model  $model Model.
     * @return \Illuminate\Http\Response
     */
    #[OpenApi\Operation(tags: ['Generic Object'])]
    #[OpenApi\Parameters(factory: Parameters\GenericObjectType::class)]
    #[OpenApi\Response(factory: Responses\GenericObjectDelete::class, statusCode: 200)]
    #[OpenApi\Response(factory: Responses\Unauthenticated::class, statusCode: 401)]
    #[OpenApi\Response(factory: Responses\Forbidden::class, statusCode: 403)]
    #[OpenApi\Response(factory: Responses\ErrorNotFound::class, statusCode: 404)]
    public function destroy($model)
    {
        $model->delete();

        return response()->json(new JsonResource($model));
    }
}
