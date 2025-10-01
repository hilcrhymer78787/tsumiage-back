<?php

namespace App\Http\Controllers;

use App\Domains\WorkDelete\Parameters\WorkDeleteParameter;
use App\Http\Resources\Common\ErrorResource;
use App\Domains\WorkDelete\Services\WorkDeleteService;
use App\Http\Requests\WorkDeleteRequest;
use App\Http\Resources\Common\SuccessResource;
use Throwable;

class WorkDeleteController extends Controller
{
    public function __construct(private WorkDeleteService $service) {}

    public function index(WorkDeleteRequest $request): SuccessResource | ErrorResource
    {
        try {
            $params = WorkDeleteParameter::makeParams($request->validated());
            $message = $this->service->deleteWork($params, $request);
            return new SuccessResource($message);
        } catch (Throwable $error) {
            debugError($error);
            return new ErrorResource($error);
        }
    }
}
