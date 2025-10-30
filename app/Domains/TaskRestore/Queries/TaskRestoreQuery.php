<?php
namespace App\Domains\TaskRestore\Queries;

use App\Models\Task;
use App\Models\Work;

class TaskRestoreQuery
{
    /**
     * 論理削除済みのタスクを復元
     */
    public function restoreTask(int $taskId, int $userId): int
    {
        return Task::withTrashed()
            ->where('task_id', $taskId)
            ->where('task_user_id', $userId)
            ->restore();
    }

    /**
     * 論理削除済みの作業を復元
     */
    public function restoreWork(int $taskId, int $userId): int
    {
        return Work::withTrashed()
            ->where('work_task_id', $taskId)
            ->where('work_user_id', $userId)
            ->restore();
    }
}
