<?php

namespace App\Domains\TaskDelete\Queries;

use App\Models\Task;
use App\Models\Work;

class TaskDeleteQuery
{
    public function deleteTask(int $taskId, int $userId, ?bool $isHardDelete = false): int
    {
        $query = Task::where('task_id', $taskId)
            ->where('task_user_id', $userId);
        if ($isHardDelete) {
            return $query->forceDelete();
        }

        return $query->delete();
    }

    public function deleteWork(int $taskId, int $userId, ?bool $isHardDelete = false): int
    {
        $query = Work::where('work_task_id', $taskId)
            ->where('work_user_id', $userId);
        if ($isHardDelete) {
            return $query->forceDelete();
        }

        return $query->delete();
    }
}
