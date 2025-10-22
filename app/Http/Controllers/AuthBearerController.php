<?php

namespace App\Http\Controllers;

use App\Domains\AuthBearer\Services\AuthBearerService;
use App\Http\Resources\Common\ErrorResource;
use App\Http\Resources\Common\LoginInfoResource;
use Illuminate\Foundation\Http\FormRequest;
use Throwable;

class AuthBearerController extends Controller
{
    public function __construct(private AuthBearerService $service) {}

    public function index(FormRequest $request): LoginInfoResource|ErrorResource
    {
        try {
            $loginInfoEntity = $this->service->bearerAuth($request);

            return new LoginInfoResource($loginInfoEntity);
        } catch (Throwable $error) {
            // debugError($error);
            return new ErrorResource($error);
        }
    }
}
