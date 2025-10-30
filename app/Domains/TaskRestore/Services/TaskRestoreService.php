<?php

declare(strict_types=1);

namespace App\Domains\TaskRestore\Services;

use App\Domains\Shared\LoginInfo\Services\LoginInfoService;
use App\Domains\Shared\Task\Queries\TaskQuery;
use App\Domains\TaskRestore\Parameters\TaskRestoreParameter;
use App\Domains\TaskRestore\Queries\TaskRestoreQuery;
use App\Http\Exceptions\AppHttpException;
use App\Http\Requests\TaskRestoreRequest;

class TaskRestoreService
{
    public function __construct(
        private readonly LoginInfoService $loginInfoService,
        private readonly TaskRestoreQuery $query,
        private readonly TaskQuery $taskQuery,
    ) {}

    public function restoreTask(TaskRestoreParameter $params, TaskRestoreRequest $request): string
    {
        $userId = $this->loginInfoService->getLoginInfo($request)->id;
        $taskId = $params->id;

        $taskModel = $this->taskQuery->getTaskById($taskId, true);
        if (! $taskModel) {
            throw new AppHttpException(404, 'タスクが存在しません');
        }

        $isMyTask = $userId === $taskModel->task_user_id;
        if (! $isMyTask) {
            throw new AppHttpException(403, '自分のタスク以外を復元することはできません');
        }

        $num = $this->query->restoreTask($taskId, $userId);
        if (! $num) {
            throw new AppHttpException(500, 'タスクを復元できませんでした');
        }

        $this->query->restoreWork($taskId, $userId);

        return 'タスクを復元しました';
    }
}
