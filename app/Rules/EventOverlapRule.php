<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

use App\Models\EventSchedules;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class EventOverlapRule implements Rule
{

    protected $possibleSchedules;

    public function __construct($possibleSchedules) {
        $this->possibleSchedules = $possibleSchedules;
    }

    public function passes($attribute, $value)
    {
        // Compare the event schedules that will be spawned to existing event schedules.
        // compare start time / start time + duration while start time is not yet end time
        // Nothing should fall between event_schedule.start_date_time and event_schedule.end_time
        foreach ($this->possibleSchedules as $schedule) {
            $startDateTime = $schedule['start_date_time'];
            $endDateTime = $schedule['end_date_time'];
            Log::info("S: $startDateTime, E: $endDateTime");


            $overlappingEventsExist = EventSchedules::where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where('start_date_time', '>=', $startDateTime)
                      ->where('end_date_time', '<=', $endDateTime);
            })->exists();

            if ($overlappingEventsExist) {
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return 'An event already exists with that schedule.';
    }
}
