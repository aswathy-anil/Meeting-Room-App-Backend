<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\BookingConfirmed;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    // ✅ Confirm booking
    public function confirmBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'confirmed';
        $booking->save();

        // Send mail to user
        if ($booking->user && $booking->user->email) {
            Mail::to($booking->user->email)->send(new BookingConfirmed($booking));
        }

        return response()->json([
            'message' => 'Booking confirmed successfully',
            'booking' => $booking
        ]);
    }

    // ❌ Cancel booking
    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'rejected';
        $booking->save();

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'booking' => $booking
        ]);
    }

    // 📊 Dashboard Summary
    public function dashboard()
    {
        $totalRooms = Room::count();
        $activeRooms = Room::where('status', 'active')->count();
        $underMaintenance = Room::where('status', 'maintenance')->count();

        $now = Carbon::now();
        $startOfWeek = $now->startOfWeek();
        $endOfWeek = $now->endOfWeek();

        $currentWeekBookings = Booking::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $totalUsers = User::count();

        return response()->json([
            'total_rooms' => $totalRooms,
            'active_rooms' => $activeRooms,
            'rooms_under_maintenance' => $underMaintenance,
            'current_week_bookings' => $currentWeekBookings,
            'completed_bookings' => $completedBookings,
            'total_users' => $totalUsers,
        ]);
    }
}
