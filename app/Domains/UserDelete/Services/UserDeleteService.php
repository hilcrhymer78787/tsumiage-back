<?php

declare(strict_types=1);

namespace App\Domains\UserDelete\Services;

use App\Domains\Shared\CheckIsExistEmail\Services\CheckIsExistEmailService;
use App\Domains\Shared\Invitation\Queries\InvitationQuery;
use App\Domains\Shared\LoginInfo\Services\LoginInfoService;
use App\Domains\Shared\Task\Queries\TaskQuery;
use App\Domains\Shared\Work\Queries\WorkQuery;
use App\Domains\UserDelete\Queries\UserDeleteQuery;
use Illuminate\Foundation\Http\FormRequest;

class UserDeleteService
{
    public function __construct(
        private readonly LoginInfoService $loginInfoService,
        private readonly CheckIsExistEmailService $checkIsExistEmailService,
        private readonly UserDeleteQuery $query,
        private readonly TaskQuery $taskQuery,
        private readonly WorkQuery $workQuery,
        private readonly InvitationQuery $invitationQuery,
    ) {}

    public function deleteUser(FormRequest $request): string
    {
        $loginInfoModel = $this->loginInfoService->getLoginInfo($request);

        $userId = $loginInfoModel->id;
        $this->query->deleteUser($userId);
        $this->taskQuery->deleteTaskByUserId($userId);
        $this->workQuery->deleteWorkByUserId($userId);
        $this->invitationQuery->deleteInvitationByUserId($userId);

        return 'ユーザーを削除しました';
    }
}
