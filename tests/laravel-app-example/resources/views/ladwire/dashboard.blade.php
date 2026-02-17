@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <flux:prose>
        <livewire:laravel-ladwire-dashboard::dashboard />
    </flux:prose>
</div>
@endsection
