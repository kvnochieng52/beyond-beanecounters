@extends('adminlte::page')

@section('title', 'Bulk Waiver/Discount Upload')

@section('content_header')
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Bulk Waiver/Discount Upload</h3>
    </div>
    <div class="card-body">
        @include('notices')

        <div class="row">
            <div class="col-md-8">
                <form action="{{ route('bulk-waiver-discount.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="csv_file">Select CSV File</label>
                        <input type="file" class="form-control-file" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                        <small class="form-text text-muted">
                            The CSV file should contain columns: <strong>ticket_id</strong> (or account_number) and <strong>waiver_discount</strong>
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">Upload and Process</button>
                </form>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>CSV Format Instructions</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Required Columns:</strong></p>
                        <ul>
                            <li><code>ticket_id</code> - Lead/Ticket ID</li>
                            <li><code>waiver_discount</code> - Discount amount</li>
                        </ul>
                        <p><strong>Alternative:</strong></p>
                        <ul>
                            <li><code>account_number</code> - Account/Reference number (instead of ticket_id)</li>
                            <li><code>waiver_discount</code> - Discount amount</li>
                        </ul>
                        <p><strong>Example CSV Content:</strong></p>
                        <pre>ticket_id,waiver_discount
1,500.00
2,250.00
3,1000.00</pre>
                        <a href="{{ asset('sample_csvs/sample_waiver_discount.csv') }}" class="btn btn-sm btn-outline-primary" download>
                            <i class="fa fa-download"></i> Download Sample CSV
                        </a>
                        <p class="text-warning"><small><strong>Note:</strong> The waiver/discount field is separate from balance and will not affect balance calculations.</small></p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('errors') && count(session('errors')) > 0)
        <div class="alert alert-warning mt-3">
            <h5>Upload Errors:</h5>
            <ul>
                @foreach(session('errors') as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
@stop

@section('css')
<style>
    pre {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 4px;
        font-size: 12px;
    }
</style>
@stop