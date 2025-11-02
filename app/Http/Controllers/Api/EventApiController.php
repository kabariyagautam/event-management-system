<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventApiController extends Controller
{
    // Fetch all categorized events
    public function index()
    {
        $today = Carbon::today();

        return response()->json([
            'today' => Event::whereDate('date', $today)->orderBy('time')->get(),
            'future' => Event::whereDate('date', '>', $today)->orderBy('date')->get(),
            'past' => Event::whereDate('date', '<', $today)->orderByDesc('date')->get(),
        ]);
    }

    // Store new event
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'time' => 'nullable',
            'location' => 'nullable|string',
        ]);

        $event = Event::create($validated);
        return response()->json(['message' => 'Event created successfully', 'event' => $event], 201);
    }

    // Show specific event
    public function show($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }
        return response()->json($event);
    }
    // Update event
    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $event->update([
            'title' => $request->title,
            'date' => $request->date,
            'time' => $request->time,
            'location' => $request->location,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Event updated successfully!']);
    }


    // Delete event
    public function destroy($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $event->delete();
        return response()->json(['message' => 'Event deleted successfully']);
    }

}
