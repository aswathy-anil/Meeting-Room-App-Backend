<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function stats(Request $request)
    {
        $userId = $request->user()->id;

        $total = Booking::where('user_id', $userId)->count();

        $confirmed = Booking::where('user_id', $userId)
                    ->where('status', 'confirmed')
                    ->where('from_datetime', '>', Carbon::now())
                    ->count();

        $upcoming = Booking::where('user_id', $userId)
                    ->whereIn('status', ['confirmed', 'booked'])
                    ->where('from_datetime', '>', Carbon::now())
                    ->count();


        $roomCount = Booking::where('user_id', $userId)
                    ->distinct('room_id')
                    ->count('room_id');

        $thisWeek = Booking::where('user_id', $userId)
                    ->with('room')
                    ->get();

        return response()->json([
            'total' => $total,
            'upcoming' => $upcoming,
            'confirmed_upcoming' => $confirmed,
            'rooms_used' => $roomCount,
            'weekly_bookings' => $thisWeek
        ]);
    }
}
