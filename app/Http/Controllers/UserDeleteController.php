<?php

namespace App\Http\Controllers;

use App\Http\Resources\Common\ErrorResource;
use App\Http\Resources\Common\LoginInfoResource;
use App\Domains\UserDelete\Services\UserDeleteService;
use App\Http\Resources\Common\SuccessResource;
use Illuminate\Foundation\Http\FormRequest;
use Throwable;

class UserDeleteController extends Controller
{
    public function __construct(private UserDeleteService $service) {}

    public function index(FormRequest $request): SuccessResource | ErrorResource
    {
        try {
            $message = $this->service->deleteUser($request);
            return new SuccessResource($message);
        } catch (Throwable $error) {
            // debugError($error);
            return new ErrorResource($error);
        }
    }
}
