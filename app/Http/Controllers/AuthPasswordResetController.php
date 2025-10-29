<?php

namespace App\Http\Controllers;

use App\Domains\AuthPasswordReset\Parameters\AuthPasswordResetParameter;
use App\Domains\AuthPasswordReset\Services\AuthPasswordResetService;
use App\Http\Requests\AuthPasswordResetRequest;
use App\Http\Resources\Common\ErrorResource;
use App\Http\Resources\Common\LoginInfoResource;
use Throwable;

class AuthPasswordResetController extends Controller
{
    public function __construct(private AuthPasswordResetService $service) {}

    public function index(AuthPasswordResetRequest $request): LoginInfoResource|ErrorResource
    {
        try {
            $params = AuthPasswordResetParameter::makeParams($request->validated());
            $loginInfoEntity = $this->service->passwordReset($params, $request);

            return new LoginInfoResource($loginInfoEntity);
        } catch (Throwable $error) {
            // debugError($error);
            return new ErrorResource($error);
        }
    }
}
