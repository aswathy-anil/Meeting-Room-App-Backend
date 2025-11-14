<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    // Admin-only: List all rooms
    public function index()
    {
        $rooms = Room::all();
        return response()->json($rooms);
    }

    // Admin-only: Create room with image
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'capacity' => 'required|integer',
            'location' => 'required|string',
            'status' => 'required|in:active,maintenance,inactive',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('rooms', 'public');
            $data['image'] = $path;
        }

        $room = Room::create($data);
        return response()->json($room, 201);
    }

    // Admin-only: Update room
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'sometimes|required|string',
            'capacity' => 'sometimes|required|integer',
            'location' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:active,maintenance,inactive',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $path = $request->file('image')->store('rooms', 'public');
            $room->image = $path;
        }

        $room->update($request->only(['name','capacity','location','status','description','image']));
        return response()->json($room);
    }

    // Admin-only: Delete room
    public function destroy(Room $room)
    {
        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }
        $room->delete();
        return response()->json(['message' => 'Room deleted']);
    }

     // Show a single room
     public function show($id)
     {
         $room = Room::find($id);
 
         if (!$room) {
             return response()->json([
                 'message' => 'Room not found.'
             ], 404);
         }
 
         return response()->json($room);
     }
}
