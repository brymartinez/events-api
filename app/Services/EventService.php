<?php

namespace App\Services;
use App\Models\Events;
use App\Models\EventSchedules;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class EventService
{

    public function generatePossibleSchedules($request)  {
        $schedules = [];

        $currentStartDateTime = Carbon::parse($request->startDateTime);
        $currentEndDateTime = Carbon::parse($request->startDateTime)->copy()->addMinutes($request->duration);

        do {
            // Create an array for the current iteration
            $schedule = [
                'start_date_time' => $currentStartDateTime,
                'end_date_time' => $currentEndDateTime,
            ];

            $schedules[] = $schedule;

            if ($request->frequency === 'Weekly') {
                $currentStartDateTime = $currentStartDateTime->copy()->addWeeks(1);
            } elseif ($request->frequency === 'Monthly') {
                $currentStartDateTime = $currentStartDateTime->copy()->addMonths(1);
            }

            $currentEndDateTime = $currentStartDateTime->copy()->addMinutes($request->duration);
        } while ($currentEndDateTime->lte(($request->endDateTime ?: $request->startDateTime->copy()->addYear())) && $request->frequency !== 'Once-Off');

        return $schedules;
    }

    public function createSchedules($events, $schedules) {
        // Create event schedule entries
        $currentStartDateTime = Carbon::parse($events->start_date_time);
        $currentEndDateTime = Carbon::parse($events->start_date_time)->copy()->addMInutes($events->duration);

        $batchData = [];

        foreach ($schedules as $schedule) {
            $batchData[] = [
                'event_id' => $events->id,
                'start_date_time' => Carbon::parse($schedule['start_date_time']),
                'end_date_time' => Carbon::parse($schedule['end_date_time']),
            ];
        }

        EventSchedules::insert($batchData);
    }
}
