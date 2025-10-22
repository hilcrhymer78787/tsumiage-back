<?php

namespace App\Http\Controllers;

use App\Domains\TaskRead\Parameters\TaskReadParameter;
use App\Domains\TaskRead\Services\TaskReadService;
use App\Http\Requests\TaskReadRequest;
use App\Http\Resources\Common\ErrorResource;
use App\Http\Resources\TaskReadResource;
use Throwable;

class TaskReadController extends Controller
{
    public function __construct(private TaskReadService $service) {}

    public function index(TaskReadRequest $request): TaskReadResource|ErrorResource
    {
        try {
            $params = TaskReadParameter::makeParams($request->validated());
            $taskReadEntity = $this->service->taskRead($params, $request);

            return new TaskReadResource($taskReadEntity);
        } catch (Throwable $error) {
            // debugError($error);
            return new ErrorResource($error);
        }
    }
}
