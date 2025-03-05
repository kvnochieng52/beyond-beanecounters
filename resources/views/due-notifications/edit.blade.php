@extends('adminlte::page')
@section('title', 'Edit Notification')
@section('content_header')
<h1>Edit Notification</h1>
@stop
@section('content')

<div class="card">
    <div class="card-body">
        @include('notices')
        <form action="{{ route('due-notifications.update', $dueNotification->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="{{ $dueNotification->title }}" required>
            </div>

            <div class="form-group">
                <label>Available Columns</label>
                <select id="columnSelector" class="form-control select2">
                    <option value="">-- Select Column --</option>
                    @foreach($columns as $column)
                    <option value="{{ $column }}">{{ $column }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Message</label>
                <textarea name="message" id="messageBox" class="form-control"
                    required>{{ $dueNotification->message }}</textarea>
            </div>

            <div class="form-group">
                <label>Moment</label>
                <select name="moment" class="form-control" required>
                    <option value="before" {{ $dueNotification->moment == 'before' ? 'selected' : '' }}>Before</option>
                    <option value="after" {{ $dueNotification->moment == 'after' ? 'selected' : '' }}>After</option>
                </select>
            </div>

            <div class="form-group">
                <label>Days</label>
                <input type="number" name="days" class="form-control" value="{{ $dueNotification->days }}" required>
            </div>

            <div class="form-group">
                <label>Active</label>
                <select name="is_active" class="form-control" required>
                    <option value="1" {{ $dueNotification->is_active == 1 ? "selected" : "" }}>Yes</option>
                    <option value="0" {{ $dueNotification->is_active == 0 ? "selected" : "" }}>No</option>
                </select>
            </div>

            <div class="form-group">
                <label><input type="checkbox" name="send_sms" value="1" {{ $dueNotification->send_sms == 1 ? 'checked' :
                    '' }}> Send SMS</label>

            </div>

            <div class="form-group">
                <label><input type="checkbox" name="send_email" value="1" {{ $dueNotification->send_email == 1 ?
                    'checked' : ''
                    }}> Send Email</label>

            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@stop

@section('css')
{{--
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"> --}}
@stop

@section('js')
{{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script> --}}
<script>
    $(document).ready(function() {
        $('.select2').select2(); // Initialize Select2
        
        $('#columnSelector').change(function() {
            let column = $(this).val();
            if (column) {
                insertAtCursor($('#messageBox')[0], `{${column}} `);
                $(this).val(null).trigger('change'); // Reset dropdown after inserting
            }
        });

        function insertAtCursor(textarea, text) {
            if (document.selection) {
                // For IE
                textarea.focus();
                let sel = document.selection.createRange();
                sel.text = text;
            } else if (textarea.selectionStart || textarea.selectionStart === 0) {
                // For modern browsers
                let startPos = textarea.selectionStart;
                let endPos = textarea.selectionEnd;
                let value = textarea.value;
                textarea.value = value.substring(0, startPos) + text + value.substring(endPos, value.length);
                textarea.selectionStart = textarea.selectionEnd = startPos + text.length;
            } else {
                // Append at the end
                textarea.value += text;
            }
        }
    });
</script>
@stop