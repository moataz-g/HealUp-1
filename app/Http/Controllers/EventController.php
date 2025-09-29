<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Show participants for a given event (professor view).
     */
    public function participants($eventId)
    {
        $event = Event::with('users')->findOrFail($eventId);
        $participants = $event->users;
        return view('events.participants', compact('event', 'participants'));
    }
    /**
     * Register the authenticated user for an event.
     */
    public function register($eventId)
    {
        $user = auth()->user();
        $event = Event::findOrFail($eventId);

        // Check if already registered
        if ($event->users()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'You are already registered for this event.');
        }

        // Register user
        $event->users()->attach($user->id, ['registered_at' => now()]);

        // Optionally increment current_participants
        $event->increment('current_participants');

        return redirect()->back()->with('success', 'You have registered for the event!');
    }
    /**
     * Display the front office events list for students and professors.
     */
    public function frontoffice()
    {
        $events = Event::where('is_active', true)
            ->whereDate('date', '>=', now())
            ->orderBy('date', 'asc')
            ->with('category')
            ->get();
        return view('events.frontoffice', compact('events'));
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $events = Event::orderBy('date', 'asc')->paginate(10);
    return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('events.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_participants' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
        ]);

        $validated['current_participants'] = 0;
        $validated['is_active'] = true;

        Event::create($validated);
        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
    return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('events.edit', compact('event', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_participants' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
        ]);

        $event->update($validated);
        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
    $event->delete();
    return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
