@extends('adminlte::page')

@section('title', 'View Contact')

{{-- @section('content_header')
<h1>View Contact</h1>
@stop --}}

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">View Contact</h4>
    </div>
    <div class="card-header">
        <strong>{{ $contact->title }}</strong>
        <a href="{{ route('contacts.index') }}" class="btn btn-secondary btn-sm float-right">Back</a>
    </div>
    <div class="card-body">
        <p><strong>Status:</strong> {{ $contact->is_active ? 'Active' : 'Inactive' }}</p>
        <h4>Contact Lists:</h4>
        <ul>
            @foreach($contact->contactLists as $list)
            <li>{{ $list->name }} - {{ $list->telephone }}</li>
            @endforeach
        </ul>
    </div>
</div>
@stop