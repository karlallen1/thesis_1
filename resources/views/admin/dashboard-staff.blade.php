@extends('layouts.admin')

@section('content')
  <div class="p-6">
    <h1 class="text-2xl font-bold text-blue-700">Staff Dashboard</h1>
    <ul class="mt-4 space-y-2">
      <li><a href="/queue-status" class="text-blue-500 underline">Queue Status</a></li>
      <li><a href="/logout" class="text-red-500 underline">Logout</a></li>
    </ul>
  </div>
@endsection
