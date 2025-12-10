<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    // Mapped from: get_history.php
    public function index()
    {
        // Fetch ALL trips (since we have no login system right now)
        // Or filter by NULL user_id if you want only guest trips
        $trips = Trip::orderBy('created_at', 'desc')->get();

        return view('history', compact('trips'));
    }

    // Mapped from: save_trip.php
    public function store(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'start_point' => 'nullable|string|max:255', // Make sure this matches your DB
            'start_date'  => 'required|date',
            'end_date'    => 'required|date',
            'notes'       => 'nullable|string',
            'distance_km' => 'nullable|numeric',
            'duration_minutes' => 'nullable|integer',
            'route_type' => 'nullable|string'
        ]);

        // 2. Create Trip (Allowed for Guests)
        // We use the Trip model directly instead of $request->user()
        Trip::create($validated);

        // 3. Return JSON (because your map calls this via fetch)
        return response()->json(['status' => 'success', 'message' => 'Trip saved!']);
    }

    // Mapped from: delete_trip.php
    public function destroy(Trip $trip)
    {
        $trip->delete();
        return back()->with('success', 'Trip deleted successfully.');
    }
}