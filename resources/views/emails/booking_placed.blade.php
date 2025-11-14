<h3>New Booking Placed</h3>
<p>User: {{ $booking->user->name }} ({{ $booking->user->email }})</p>
<p>Location: {{ $booking->room->location }}</p>
<p>Room: {{ $booking->room->name }}</p>
<p>From: {{ $booking->from_datetime }}</p>
<p>To: {{ $booking->to_datetime }}</p>
