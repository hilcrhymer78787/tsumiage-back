<?php

declare(strict_types=1);

namespace App\Domains\WorkCreate\Services;

use App\Domains\Shared\LoginInfo\Services\LoginInfoService;
use App\Domains\WorkCreate\Parameters\WorkCreateParameter;
use App\Domains\WorkCreate\Queries\WorkCreateQuery;
use App\Http\Exceptions\AppHttpException;
use App\Http\Requests\WorkCreateRequest;

class WorkCreateService
{
    public function __construct(
        private readonly LoginInfoService $loginInfoService,
        private readonly WorkCreateQuery $query,
    ) {}

    public function updateOrCreateWork(WorkCreateParameter $params, WorkCreateRequest $request): string
    {
        $userId = $this->loginInfoService->getLoginInfo($request)->id;

        $isExistMyTask = $this->query->getIsExistMyTask($params->taskId, $userId);
        if (! $isExistMyTask) {
            throw new AppHttpException(404, '更新するタスクが存在しません');
        }

        $this->query->updateOrCreateWork($params, $userId);

        return '活動情報を更新しました';
    }
}
