@extends('adminlte::page')

@section('title', 'Edit Contact')

@section('content_header')
{{-- <h1>Edit Contact</h1> --}}
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-10">
                <form action="{{ route('contacts.update', $contact) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" name="title" value="{{ $contact->title }}" class="form-control"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="is_active">Status</label>
                                <select name="is_active" class="form-control" required>
                                    <option value="1" {{ $contact->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$contact->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <h4>Contact List</h4>
                    <div id="contact-lists">
                        @foreach($contact->contactLists as $index => $list)
                        <div class="form-row mb-3 contact-item">
                            <div class="col-md-5">
                                <input type="text" name="contact_lists[{{ $index }}][name]" value="{{ $list->name }}"
                                    placeholder="Name" class="form-control" required>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="contact_lists[{{ $index }}][telephone]"
                                    value="{{ $list->telephone }}" placeholder="Telephone" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-contact">Remove</button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" id="add-contact-list" class="btn btn-secondary">Add Contact</button>
                    <br><br>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    let index = {{ $contact->contactLists->count() }};

    // Add new contact row
    document.getElementById('add-contact-list').addEventListener('click', function() {
        const div = document.createElement('div');
        div.classList.add('form-row', 'mb-3', 'contact-item');
        div.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="contact_lists[${index}][name]" placeholder="Name" class="form-control" required>
            </div>
            <div class="col-md-5">
                <input type="text" name="contact_lists[${index}][telephone]" placeholder="Telephone" class="form-control" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-contact">Remove</button>
            </div>
        `;
        document.getElementById('contact-lists').appendChild(div);
        index++;
    });

    // Remove contact row functionality
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-contact')) {
            e.target.closest('.contact-item').remove();
        }
    });
</script>
@stop