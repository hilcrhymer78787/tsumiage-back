<?php

namespace App\Http\Controllers;

use App\Domains\AuthEmailVerify\Services\AuthEmailVerifyService;
use App\Http\Resources\Common\ErrorResource;
use App\Http\Resources\Common\SuccessResource;
use Illuminate\Http\Request;
use Throwable;

class AuthEmailVerifyController extends Controller
{
    public function __construct(private AuthEmailVerifyService $service) {}

    public function index(Request $request): SuccessResource|ErrorResource
    {
        try {
            $message = $this->service->authEmailVerify($request);

            return new SuccessResource($message);
        } catch (Throwable $error) {

            return new ErrorResource($error);
        }
    }
}
