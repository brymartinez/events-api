<?php

namespace App\Services;
use App\Models\Events;
use App\Models\EventSchedules;
use Carbon\Carbon;

class EventService
{
    public function createSchedules($events) {
        // Create event schedule entries
        $currentStartDateTime = $events->start_date_time;
        $currentEndDateTime = $events->start_date_time->copy()->addMInutes($events->duratio);

        $batchData = [];


        while ($currentEndDateTime->lte(($events->end_date_time))) {
            // TODO - default to 1 year
            $batchData[] = [
                'event_id' => $events->id,
                'start_date_time' => $currentStartDateTime,
                'end_date_time' => $currentEndDateTime,
            ];

            $currentStartDateTime = Carbon::parse($currentStartDateTime)->addWeeks(1); // Only for weekly. Use frequency as basis
            $currentEndDateTime = Carbon::parse($currentStartDateTime)->addMinutes($events->duration);
        }

        EventSchedules::insert($batchData);
    }
}
