<?php

namespace App\Http\Controllers;

use App\Http\Resources\Common\ErrorResource;
use App\Domains\AuthLogout\Services\AuthLogoutService;
use App\Http\Resources\Common\SuccessResource;
use Illuminate\Foundation\Http\FormRequest;
use Throwable;

class AuthLogoutController extends Controller
{
    public function __construct(private readonly AuthLogoutService $service) {}

    public function index(FormRequest $request): SuccessResource | ErrorResource
    {
        try {
            $message = $this->service->logout($request);
            return new SuccessResource($message);
        } catch (Throwable $error) {
            // debugError($error);
            return new ErrorResource($error);
        }
    }
}
