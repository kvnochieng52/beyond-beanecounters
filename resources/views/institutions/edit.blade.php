@extends('adminlte::page')
@section('title', 'Edit Institution')
@section('content_header')
<h1>Edit Institution</h1>
@stop
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route('institutions.update', $institution) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label for="institution_name">Name</label>
                <input type="text" name="institution_name" class="form-control"
                    value="{{ $institution->institution_name }}" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" class="form-control">{{ $institution->address }}</textarea>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $institution->email }}">
            </div>
            <div class="form-group">
                <label for="website">Website</label>
                <input type="text" name="website" class="form-control" value="{{ $institution->website }}">
            </div>
            <div class="form-group">
                <label for="telephone">Telephone</label>
                <input type="text" name="telephone" class="form-control" value="{{ $institution->telephone }}">
            </div>
            <div class="form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" class="form-control"
                    value="{{ $institution->contact_person }}">
            </div>

            <div class="form-group">
                <label for="client_contract_type_id">Client Contract Type</label>
                <select name="client_contract_type_id" class="form-control">
                    <option value="">Select Contract Type</option>
                    @foreach($clientContractTypes as $type)
                        <option value="{{ $type->id }}" {{ $institution->client_contract_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="contact_person">How to Pay Instructions</label>
                <textarea name="how_to_pay_instructions" class="form-control">{{$institution->how_to_pay_instructions}}</textarea>
            </div>


            <div class="form-group">
                <label for="is_active">Status</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ $institution->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$institution->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
</div>
@stop