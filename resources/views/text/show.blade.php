@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
{{-- <h1>Dashboard</h1> --}}
@stop

@section('content')



<div class="card">
    <div class="card-header">
        <h4>{{$text->text_title}}</h4>
    </div>

    <div class="card-body">

        <h3>{{$text->contacts_count}} <small class="text-muted">Contacts</small></h3>
        <div class="progress">
            <div class="progress-bar bg-success" role="progressbar" style="width: {{$percentage}}%;">
                <strong>{{$percentage}}% Overall Progress</strong>
            </div>
        </div>

        <div class="row text-center mt-4">

            <div class="col-md-3">
                <div class="card bg-success text-white p-3">
                    <h3>{{$sentMessagesCount}}</h3>
                    <p>SMS DELIVERED</p>
                    <a href="/queue/export?delivered=on&text_id={{$text->id}}" class="btn btn-primary btn-sm">View
                        Delivered SMS <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-danger text-white p-3">
                    <h3>{{$undeliveredMessagesCount}}</h3>
                    <p>SMS UNDELIVERED</p>
                    <a href="/queue/export?undelivered=on&text_id={{$text->id}}" class="btn btn-primary btn-sm">View
                        Undelivered SMS <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark text-white p-3">
                    <h3>{{$blackListedMessagesCount}}</h3>
                    <p>SMS BLACKLISTED</p>
                    <a href="/queue/export?blocked=on&text_id={{$text->id}}" class="btn btn-primary btn-sm">View
                        Blacklisted SMS <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-warning text-dark p-3">
                    <h3>{{$queuedMessagesCount}}</h3>
                    <p>SMS IN QUEUE</p>
                    {{-- <button class="btn btn-light btn-sm">View SMS in Queue <i
                            class="fas fa-arrow-right"></i></button> --}}
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{-- <h5><strong>EXPORT REPORT:</strong></h5>
            <p>Leave the options blank and click Export Report for a full SMS import. Check any options to filter.</p>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox">
                <label class="form-check-label">Delivered SMS</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox">
                <label class="form-check-label">Undelivered SMS</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox">
                <label class="form-check-label">Cancelled SMS</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox">
                <label class="form-check-label">Blacklisted SMS</label>
            </div>
            <button class="btn btn-secondary ml-3">EXPORT REPORT</button> --}}

            <form action="{{ route('queue.export') }}" method="GET">
                <h5><strong>EXPORT REPORT:</strong></h5>
                <p>Leave the options blank and click Export Report for a full SMS import. Check any options to filter.
                </p>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="delivered" name="delivered">
                    <label class="form-check-label" for="delivered">Delivered SMS</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="undelivered" name="undelivered">
                    <label class="form-check-label" for="undelivered">Undelivered SMS</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="cancelled" name="cancelled">
                    <label class="form-check-label" for="cancelled">Cancelled SMS</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="blacklisted" name="blacklisted">
                    <label class="form-check-label" for="blacklisted">Blacklisted SMS</label>
                </div>


                <input type="hidden" name="text_id" value="{{$text->id}}">

                <button type="submit" class="btn btn-secondary mt-3">EXPORT REPORT</button>
            </form>
        </div>
    </div>
</div>

@stop

@section('css')
{{-- Add here extra stylesheets --}}
{{--
<link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
<script>
    console.log("Hi, I'm using the Laravel-AdminLTE package!"); 
</script>
@stop