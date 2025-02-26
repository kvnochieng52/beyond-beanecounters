@extends('adminlte::page')

@section('title', 'Contacts')

@section('content_header')
<h1>Contacts</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('contacts.create') }}" class="btn btn-success mb-3">Create New Contact</a>

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif



                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contacts as $contact)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $contact->title }}</td>
                            <td>{{ $contact->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <a href="{{ route('contacts.show', $contact) }}" class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ route('contacts.destroy', $contact) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this contact?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $contacts->links() }}
            </div>
        </div>
    </div>
</div>
@stop