<?php

namespace App\Http\Controllers;

use App\Domains\TaskRestore\Parameters\TaskRestoreParameter;
use App\Domains\TaskRestore\Services\TaskRestoreService;
use App\Http\Requests\TaskRestoreRequest;
use App\Http\Resources\Common\ErrorResource;
use App\Http\Resources\Common\SuccessResource;
use Throwable;

class TaskRestoreController extends Controller
{
    public function __construct(private TaskRestoreService $service) {}

    public function index(TaskRestoreRequest $request): SuccessResource|ErrorResource
    {
        try {
            $params = TaskRestoreParameter::makeParams($request->validated());
            $message = $this->service->restoreTask($params, $request);

            return new SuccessResource($message);
        } catch (Throwable $error) {
            // debugError($error);
            return new ErrorResource($error);
        }
    }
}
