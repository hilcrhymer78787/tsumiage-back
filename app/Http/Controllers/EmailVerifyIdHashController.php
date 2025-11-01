<?php

namespace App\Http\Controllers;

use App\Domains\EmailVerifyIdHash\Services\EmailVerifyIdHashService;
use App\Http\Resources\Common\ErrorResource;
use App\Http\Resources\Common\SuccessResource;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Throwable;

class EmailVerifyIdHashController extends Controller
{
    public function __construct(private EmailVerifyIdHashService $service) {}

    public function index(EmailVerificationRequest $request): SuccessResource|ErrorResource
    {
        try {
            $message = $this->service->emailVerify($request);

            return new SuccessResource($message);
        } catch (Throwable $error) {
            
            return new ErrorResource($error);
        }
    }
}
