<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Events;
use App\Models\EventSchedules;
use App\Models\User;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index()
    {
        $events = Events::all();
        return response()->json($events);
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'eventName' => ['required'],
            'frequency' => ['required', Rule::in(['Once-Off', 'Weekly', 'Monthly'])],
            'startDateTime' => ['required', 'date_format:Y-m-d H:i'],
            'endDateTime' => ['nullable', 'date_format:Y-m-d H:i'],
            'duration' => ['required', 'integer', 'numeric'],
            'invitees' => ['required', 'array', function ($attribute, $value, $fail) {
                foreach ($value as $item) {
                    if (!is_int($item)) {
                        $fail($attribute.' must contain only integers.');
                    }
                }
            }],
        ]);

        $events = new Events;
        $events->name = $request->eventName;
        $events->frequency = $request->frequency;
        $events->start_date_time = Carbon::parse($request->startDateTime);
        $events->end_date_time = Carbon::parse($request->endDateTime);
        $events->duration = $request->duration;
        $events->invitees = $request->invitees;
        $events->save();

        return response()->json(["message" => "Event added" ], 201);
    }
}
