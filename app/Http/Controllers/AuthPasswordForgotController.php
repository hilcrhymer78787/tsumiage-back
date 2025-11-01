<?php

namespace App\Http\Controllers;

use App\Domains\AuthPasswordForgot\Parameters\AuthPasswordForgotParameter;
use App\Domains\AuthPasswordForgot\Services\AuthPasswordForgotService;
use App\Http\Requests\AuthPasswordForgotRequest;
use App\Http\Resources\Common\ErrorResource;
use App\Http\Resources\Common\SuccessResource;
use Throwable;

class AuthPasswordForgotController extends Controller
{
    public function __construct(private AuthPasswordForgotService $service) {}

    public function index(AuthPasswordForgotRequest $request): SuccessResource|ErrorResource
    {
        try {
            $params = AuthPasswordForgotParameter::makeParams($request->validated());
            $message = $this->service->passwordForgot($params, $request);

            return new SuccessResource($message);
        } catch (Throwable $error) {

            return new ErrorResource($error);
        }
    }
}
