<?php

namespace App\Http\Controllers;

use App\Domains\AuthTest\Services\AuthTestService;
use App\Http\Resources\Common\ErrorResource;
use App\Http\Resources\Common\LoginInfoResource;
use Illuminate\Foundation\Http\FormRequest;
use Throwable;

class AuthTestController extends Controller
{
    public function __construct(
        private readonly AuthTestService $service,
    ) {}

    public function index(FormRequest $request): LoginInfoResource|ErrorResource
    {
        try {
            $loginInfoEntity = $this->service->testAuth($request);

            return new LoginInfoResource($loginInfoEntity);
        } catch (Throwable $error) {

            return new ErrorResource($error);
        }
    }
}
