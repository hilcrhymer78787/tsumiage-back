<?php

namespace App\Domains\WorkReadMonth\Services;

use App\Domains\Shared\Work\Factories\WorkFactory;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WorkReadMonthBuilder
{
    public function __construct(
        private readonly WorkFactory $workFactory,
    ) {}

    public function build(int $paramsYear, int $paramsMonth, Collection $taskModels, Collection $workModels): Collection
    {
        $startOfMonth = Carbon::create($paramsYear, $paramsMonth, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Workを日付とタスクIDで二重にグループ化してO(1)検索を実現
        $workIndex = $workModels->groupBy('work_date')->map(fn ($works) => $works->keyBy('work_task_id'));
        // 例:
        // [
        //    '2025-11-01' => [
        //        1 => WorkModel(...), // task_id = 1
        //        2 => WorkModel(...), // task_id = 2
        //    ],
        //
        //    ...,
        //
        //    '2025-11-30' => [
        //        1 => WorkModel(...), // task_id = 1
        //    ],
        // ];
        // 1日〜末日までの日付コレクションを生成
        $dates = collect(
            range(0, $endOfMonth->day - 1)
        )->map(fn ($i) => $startOfMonth->copy()->addDays($i)); // 例: ['2025-11-01', ..., '2025-11-30']

        $calendars = $dates->map(function (Carbon $date) use ($taskModels, $workIndex) {
            $dateString = $date->toDateString(); // 例: '2025-11-01'
            $worksForDate = $workIndex->get($dateString, collect());
            // 例:
            // [
            //     1 => WorkModel(...), // task_id = 1
            //     2 => WorkModel(...), // task_id = 2
            // ],
            $dayTasks = $taskModels->map(function ($taskModel) use ($worksForDate) {
                $workModel = $worksForDate->get($taskModel->task_id); // 例: task_id の WorkModel または null

                return [
                    'taskModel' => $taskModel,
                    'workModel' => $workModel,
                ];
            });

            return [
                'date' => $dateString,
                'tasks' => $dayTasks,
            ];
        });

        return collect($calendars);
    }
}
