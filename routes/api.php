<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout']);
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/rooms',[RoomController::class,'index']);
    Route::get('/rooms/{room}',[RoomController::class,'show']);

    Route::post('/bookings',[BookingController::class,'store']);
    Route::get('/bookings',[BookingController::class,'index']);

    Route::get('/user/dashboard-stats', [UserDashboardController::class, 'stats']);

    // Admin-only room management
    Route::middleware('admin')->group(function(){
        Route::post('/rooms',[RoomController::class,'store']);
        Route::post('/rooms/{room}',[RoomController::class,'update']);
        Route::delete('/rooms/{room}',[RoomController::class,'destroy']);


        Route::put('/bookings/{booking}',[BookingController::class,'update']);

        // Booking Management
        Route::post('/bookings/{id}/confirm', [AdminController::class, 'confirmBooking']);
        Route::post('/bookings/{id}/cancel', [AdminController::class, 'cancelBooking']);

        // Dashboard Summary
        Route::get('/summary', [AdminController::class, 'dashboard']);
    });
});
