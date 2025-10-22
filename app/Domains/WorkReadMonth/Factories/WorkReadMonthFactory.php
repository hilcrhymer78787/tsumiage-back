<?php

namespace App\Domains\WorkReadMonth\Factories;

use App\Domains\Shared\Calendar\Factories\CalendarFactory;
use App\Domains\WorkReadMonth\Entities\WorkReadMonthEntity;
use Illuminate\Support\Collection;

class WorkReadMonthFactory
{
    public function __construct(
        private readonly CalendarFactory $calendarFactory,
    ) {}

    public function create(Collection $calendarModels): WorkReadMonthEntity
    {
        $calendarEntities = $calendarModels->map(function ($calendarModel) {
            return $this->calendarFactory->create($calendarModel);
        });

        return new WorkReadMonthEntity($calendarEntities);
    }
}
