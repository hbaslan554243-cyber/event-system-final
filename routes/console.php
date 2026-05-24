<?php
use Illuminate\Support\Facades\Schedule;
use App\Models\Event;

Schedule::call(function () {
    // Mark events as ongoing
    Event::where('status', 'upcoming')
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->update(['status' => 'ongoing']);

    // Mark events as completed
    Event::where('status', 'ongoing')
        ->where('end_date', '<', now())
        ->update(['status' => 'completed']);

    // Also catch upcoming events that already passed
    Event::where('status', 'upcoming')
        ->where('end_date', '<', now())
        ->update(['status' => 'completed']);

})->everyMinute();