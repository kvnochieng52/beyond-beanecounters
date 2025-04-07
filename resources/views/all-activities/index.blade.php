@extends('adminlte::page')

@section('title', 'Activity')

@section('content_header')
@stop

@section('content')
<div class="card">

    <div class="card-header">
        <h4 class="card-title">All Activities</h4>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped" id="activityTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Agent</th>
                    <th>Due</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>

    </div>
</div>

@stop

@section('css')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">

<style>
    /* Make the table fixed layout */
    #activityTable {
        table-layout: fixed;
        width: 100%;
    }

    /* Ensure wrapping of text */
    .wrap-text {
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
    }

    /* Specific column adjustments */
    #activityTable th:nth-child(2),
    #activityTable td:nth-child(2) {
        /* Title column */
        width: 15%;
    }

    #activityTable th:nth-child(1),
    #activityTable td:nth-child(1) {
        /* Title column */
        width: 2%;
    }

    #activityTable th:nth-child(3),
    #activityTable td:nth-child(3) {
        /* Message column */
        width: 25%;
    }
</style>

@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/en-gb.min.js"></script>
<script>
    $(document).ready(function() {
        let table = $('#activityTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('all-activity') }}',
            autoWidth: false,
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'activity_title',
                    name: 'activity_title',
                    render: function(data, type, row) {
                        return `<strong>${data}</strong>`;
                    }
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        return moment(data).format('DD-MM-YYYY');
                    },
                    name: 'created_at'
                },
                {
                    data: 'created_by_name',
                    name: 'created_by_name'
                },
                {
                    data: 'due',
                    name: 'due',
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {
                   data: 'activity_status_name',
                   name: 'activity_status_name',
                   render: function (data, type, row) {
                     return `<span class="badge bg-${row.activity_status_color_code}">${data}</span>`;
                   }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Optional: Add any additional client-side interactions here

        // Example: Countdown timer for due dates
        setInterval(function() {
            $('.badge[data-duetime]').each(function() {
                let dueTime = $(this).data('duetime');
                let now = Math.floor(Date.now() / 1000);
                let diff = dueTime - now;

                if (diff > 0) {
                    let days = Math.floor(diff / (24 * 3600));
                    let hours = Math.floor((diff % (24 * 3600)) / 3600);
                    let minutes = Math.floor((diff % 3600) / 60);

                    $(this).text(`${days}d ${hours}h ${minutes}m`);
                } else {
                    $(this).text('Overdue').addClass('bg-danger');
                }
            });
        }, 60000); // Update every minute
    });

    $(document).on('click', '.edit-btn', function () {
    let activityId = $(this).data('id');
    let title = $(this).data('title');
    let description = $(this).data('description');

    $('#activity_id').val(activityId);  // Set the hidden input field value
    $('#editActivityForm').attr('action', '/edit-activity/' + activityId); // Update form action dynamically
    $('#edit_activity_modal input[name="title"]').val(title);
    $('#edit_activity_modal textarea[name="description"]').val(description);
});

    </script>


@stop