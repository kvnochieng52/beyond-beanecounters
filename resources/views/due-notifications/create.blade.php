@extends('adminlte::page')
@section('title', 'Create Notification')
@section('content_header')
<h1>Create Notification</h1>
@stop
@section('content')

<div class="card">
    <div class="card-body">
        @include('notices')
        <form action="{{ route('due-notifications.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
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
                <textarea id="messageBox" name="message" class="form-control" required rows="10"></textarea>
            </div>


            <div class="form-group">
                <label>Moment</label>
                <select name="moment" class="form-control" required>
                    <option value="before">Before</option>
                    <option value="after">After</option>
                </select>
            </div>
            <div class="form-group">
                <label>Days</label>
                <input type="number" name="days" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Send SMS</label>
                <input type="checkbox" name="send_sms" value="1">
            </div>

            <div class="form-group">
                <label>Send Email</label>
                <input type="checkbox" name="send_email" value="1">
            </div>
            <div class="form-group">
                <label>Active</label>
                <select name="is_active" class="form-control" required>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Save</button>
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