<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\BookingPlaced;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role == 'admin') {
            return Booking::with('user','room')->get();
        }
        return $user->bookings()->with('room')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id'=>'required|exists:rooms,id',
            'from_datetime'=>'required|date',
            'to_datetime'=>'required|date|after:from_datetime',
        ]);
        // Convert ISO date (e.g. 2025-11-14T03:30:00.000Z) to MySQL datetime
        $from = Carbon::parse($request->from_datetime)->format('Y-m-d H:i:s');
        $to = Carbon::parse($request->to_datetime)->format('Y-m-d H:i:s');

        // Check for overlap
        $overlap = Booking::where('room_id', $request->room_id)
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('from_datetime', [$from, $to])
                ->orWhereBetween('to_datetime', [$from, $to])
                ->orWhere(function($q2) use ($from, $to) {
                    $q2->where('from_datetime', '<=', $from)
                        ->where('to_datetime', '>=', $to);
                });
            })
            ->exists();

        if($overlap){
            return response()->json(['message'=>'Room already booked for this slot'], 422);
        }

        $booking = Booking::create([
            'user_id'=>$request->user()->id,
            'room_id'=>$request->room_id,
            'from_datetime'=>$from,
            'to_datetime'=>$to,
            'status'=>'booked',
        ]);

        // TODO: send email to admin/user
        Mail::to('bookmymeetingroom@gmail.com')->send(new BookingPlaced($booking));

        return response()->json($booking);
    }
    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $request->validate([
            'status' => 'nullable|in:pending,confirmed,cancelled,completed',
            'from_datetime' => 'nullable|date',
            'to_datetime' => 'nullable|date|after:from_datetime',
            'room_id' => 'nullable|exists:rooms,id'
        ]);
        if ($request->filled('from_datetime')) {
            $booking->from_datetime = Carbon::parse($request->from_datetime)->format('Y-m-d H:i:s');
        }
    
        if ($request->filled('to_datetime')) {
            $booking->to_datetime = Carbon::parse($request->to_datetime)->format('Y-m-d H:i:s');
        }
    
        if ($request->filled('status')) {
            $booking->status = $request->status;
        }
        // Update fields dynamically
        $booking->fill($request->only(['room_id']));
         // Check for overlap
         // Convert ISO date (e.g. 2025-11-14T03:30:00.000Z) to MySQL datetime
        $from = Carbon::parse($request->from_datetime)->format('Y-m-d H:i:s');
        $to = Carbon::parse($request->to_datetime)->format('Y-m-d H:i:s');

        $overlap = Booking::where('room_id', $request->room_id)
         ->where(function ($q) use ($from, $to) {
             $q->whereBetween('from_datetime', [$from, $to])
             ->orWhereBetween('to_datetime', [$from, $to])
             ->orWhere(function($q2) use ($from, $to) {
                 $q2->where('from_datetime', '<=', $from)
                     ->where('to_datetime', '>=', $to);
             });
         })
         ->exists();

        if($overlap){
            return response()->json(['message'=>'Room already booked for this slot'], 422);
        }
        $booking->save();

        return response()->json([
            'message' => 'Booking updated successfully',
            'data' => $booking
        ]);
    }

}
