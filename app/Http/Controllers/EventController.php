<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Events;
use App\Models\EventSchedules;
use App\Models\User;
use Carbon\Carbon;

use App\Services\EventService;

use App\Rules\EventOverlapRule;
use App\Rules\OnceOffEndDateTimeRule;
use App\Rules\ValidInviteesRule;


class EventController extends Controller
{
    public function index()
    {
        $events = Events::all();
        return response()->json($events);
    }

    public function store(Request $request)
    {

        $eventSvc = new EventService(); // TODO - maybe move this to constructor


        $validated = $request->validate([
            'eventName' => ['required'],
            'frequency' => ['required', Rule::in(['Once-Off', 'Weekly', 'Monthly'])]
        ]);

        $possibleSchedules = $eventSvc->generatePossibleSchedules($request);

        $validated = $request->validate([
            'startDateTime' => ['required', 'date_format:Y-m-d H:i', new EventOverlapRule($possibleSchedules)],
            'endDateTime' => ['nullable', 'date_format:Y-m-d H:i', new OnceOffEndDateTimeRule],
            'duration' => ['required', 'integer', 'numeric'],
            'invitees' => ['required', 'array', function ($attribute, $value, $fail) {
                foreach ($value as $item) {
                    if (!is_int($item)) {
                        $fail($attribute.' must contain only integers.');
                    }
                }
            }, new ValidInviteesRule],
        ]);

        $events = new Events;
        $events->name = $request->eventName;
        $events->frequency = $request->frequency;
        $events->start_date_time = Carbon::parse($request->startDateTime);
        $events->end_date_time = Carbon::parse($request->endDateTime);
        $events->duration = $request->duration;
        $events->invitees = $request->invitees;
        $events->save();

        // TODO - use $possibleSchedules above
        $eventSvc->createSchedules($events, $possibleSchedules);

        return response()->json([
            'id' => $events->id,
            'eventName' => $events->name,
            'frequency' => $events->frequency,
            'startDateTime' => $events->start_date_time->toDateTimeString(),
            'endDateTime' => $events->end_date_time ? $events->end_date_time->toDateTimeString() : null,
            'duration' => $events->duration,
            'invitees' => $events->invitees,
        ], 201);
    }
}
