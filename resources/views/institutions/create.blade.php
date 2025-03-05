@extends('adminlte::page')
@section('title', 'Add Institution')
@section('content_header')
<h1>Add Institution</h1>
@stop
@section('content')

<div class="card">

    <div class="card-body">
        <form action="{{ route('institutions.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="institution_name">Name</label>
                <input type="text" name="institution_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label for="website">Website</label>
                <input type="text" name="website" class="form-control">
            </div>
            <div class="form-group">
                <label for="telephone">Telephone</label>
                <input type="text" name="telephone" class="form-control">
            </div>
            <div class="form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" class="form-control">
            </div>
            <div class="form-group">
                <label for="is_active">Status</label>
                <select name="is_active" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Save</button>
        </form>
    </div>
</div>
@stop