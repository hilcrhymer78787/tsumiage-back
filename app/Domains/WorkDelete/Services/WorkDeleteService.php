<?php

declare(strict_types=1);

namespace App\Domains\WorkDelete\Services;

use App\Domains\WorkDelete\Parameters\WorkDeleteParameter;
use App\Domains\WorkDelete\Queries\WorkDeleteQuery;
use App\Domains\Shared\LoginInfo\Services\LoginInfoService;
use App\Http\Requests\WorkDeleteRequest;
use App\Http\Exceptions\AppHttpException;

class WorkDeleteService
{
    public function __construct(
        private readonly LoginInfoService $loginInfoService,
        private readonly WorkDeleteQuery $query,
    ) {}

    public function deleteWork(WorkDeleteParameter $params, WorkDeleteRequest $request): string
    {
        $userId = $this->loginInfoService->getLoginInfo($request)->id;
        $workId = $params->id;

        $num = $this->query->deleteWork($workId, $userId);
        if (!$num) throw new AppHttpException(404, '活動情報が存在しません');
        
        return "活動情報を削除しました";
    }
}
