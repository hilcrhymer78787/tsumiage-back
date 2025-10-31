<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\Work;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PurgeOldDeleted extends Command
{
    protected $signature = 'purge:old-deleted';

    protected $description = '論理削除から24時間以上経過したデータを物理削除';

    public function handle()
    {
        $threshold = Carbon::now()->subDay(); // 24時間前

        // works の物理削除
        $worksDeleted = Work::onlyTrashed()
            ->where('deleted_at', '<=', $threshold)
            ->forceDelete();

        // tasks の物理削除
        $tasksDeleted = Task::onlyTrashed()
            ->where('deleted_at', '<=', $threshold)
            ->forceDelete();

        $this->info("Works deleted: {$worksDeleted}");
        $this->info("Tasks deleted: {$tasksDeleted}");
    }
}
