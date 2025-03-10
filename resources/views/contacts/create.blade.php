@extends('adminlte::page')

@section('title', 'Create Contact')

@section('content_header')
{{-- <h1>Create Contact</h1> --}}
@stop

@section('content')

<div class="card">
    <div class="card-body">

        <div class="row">

            <div class="col-md-10">
                <form action="{{ route('contacts.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="is_active">Status</label>
                                <select name="is_active" class="form-control custom-select" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <h4>Contact List</h4>
                    <div id="contact-lists">
                        <div class="form-row mb-3">
                            <div class="col-md-6">
                                <input type="text" name="contact_lists[0][name]" placeholder="Name" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="contact_lists[0][telephone]" placeholder="Telephone"
                                    class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-contact-list" class="btn btn-secondary">Add Contact</button>
                    <br><br>
                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
    let index = 1;
    document.getElementById('add-contact-list').addEventListener('click', function() {
        const div = document.createElement('div');
        div.classList.add('form-row', 'mb-3');
        div.innerHTML = `
            <div class="col-md-6">
                <input type="text" name="contact_lists[${index}][name]" placeholder="Name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="contact_lists[${index}][telephone]" placeholder="Telephone" class="form-control" required>
            </div>
        `;
        document.getElementById('contact-lists').appendChild(div);
        index++;
    });
</script>
@stop