<?php

declare(strict_types=1);

namespace App\Domains\AuthBearer\Services;

use App\Domains\Shared\LoginInfo\Entities\LoginInfoEntity;
use App\Domains\Shared\LoginInfo\Services\LoginInfoService;
use Illuminate\Foundation\Http\FormRequest;

class AuthBearerService
{
    public function __construct(
        private readonly LoginInfoService $loginInfoService,
    ) {}

    public function bearerAuth(FormRequest $request): LoginInfoEntity
    {
        $loginInfoModel = $this->loginInfoService->getLoginInfo($request);

        return new LoginInfoEntity(
            id: $loginInfoModel->id,
            email: $loginInfoModel->email,
            name: $loginInfoModel->name,
            userImg: $loginInfoModel->user_img,
            emailVerifiedAt: $loginInfoModel->email_verified_at,
        );
    }
}
