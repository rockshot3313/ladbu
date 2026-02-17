@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <flux:prose>
        <livewire:laravel-ladwire-user-management::user-management />
    </flux:prose>
</div>
@endsection
