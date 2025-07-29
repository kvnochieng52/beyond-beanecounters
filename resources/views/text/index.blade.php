@extends('adminlte::page')

@section('title', 'SMS Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Manage SMS</h4>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped" id="smsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Contacts</th>
                    <th>Date</th>
                    <th>Agent</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Contacts Modal -->
<div class="modal fade" id="contactsModal" tabindex="-1" aria-labelledby="contactsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactsModalLabel">Contact List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contactsModalBody">
                <!-- Contacts will be dynamically loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Decline Reason Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="declineModalLabel">Decline SMS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="declineForm">
                    <div class="form-group">
                        <label for="declineReason">Reason for declining (required):</label>
                        <textarea class="form-control" id="declineReason" name="decline_reason" rows="3" required
                            placeholder="Please provide a reason for declining this SMS..."></textarea>
                    </div>
                    <input type="hidden" id="declineTextId" name="text_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDecline">Decline SMS</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<style>
    #smsTable {
        table-layout: fixed;
        width: 100%;
    }
    .badge {
    font-size: 0.65rem;
    font-weight: 400;
    padding: 0.2rem 0.35rem;
    vertical-align: middle;
    }

    .wrap-text {
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
    }

    #smsTable th:nth-child(2),
    #smsTable td:nth-child(2) {
        width: 15%;
    }

    #smsTable th:nth-child(1),
    #smsTable td:nth-child(1) {
        width: 2%;
    }

    #smsTable th:nth-child(3),
    #smsTable td:nth-child(3) {
        width: 25%;
    }

    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.5;
        border-radius: 0.2rem;
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
        let table = $('#smsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('text.index') }}',
            autoWidth: false,
            columnDefs: [
                {targets: 0, width: "2%", className: "wrap-text" },
                { targets: 1, width: "15%", className: "wrap-text" },
                { targets: 2, width: "25%", className: "wrap-text" }
            ],
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
               {
            data: 'text_title',
            name: 'text_title',
            render: function(data, type, row) {
            // Add scheduled label if scheduled == 1
            const scheduledLabel = row.scheduled == 1
            
            ? `<br/><span class="badge badge-info ml-2">Scheduled ${row.schedule_date} </span>`
            : '';
            
            return `
            <strong>
                <a href="/text/${row.id}" class="text-primary">${data}</a>
                ${scheduledLabel}
            </strong>`;
            }
            },
                { data: 'message', name: 'message' },
                {
                    data: 'recepient_contacts',
                    name: 'recepient_contacts',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (row.contact_type === 'csv') {
                            return `<a href="${row.csv_file_path}" class="btn btn-secondary btn-sm" target="_blank">view</a>`;
                        }
                        else if (row.contact_type === 'manual') {
                            return `<button class="btn btn-secondary btn-sm view-manual-contacts"
                                        data-contacts="${row.recepient_contacts}">
                                        view
                                    </button>`;
                        }
                        else if (row.contact_type === 'saved') {
                            return `<button class="btn btn-secondary btn-sm view-saved-contacts"
                                data-contact-list='${row.contact_list}' data-text-id="${row.id}">
                                view
                            </button>`;
                        }
                        return '';
                    }
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        return data ? moment(data).format('DD-MM-YYYY hh:mm A') : '';
                    },
                    name: 'created_at'
                },
                {
                    data: 'created_by_name',
                    name: 'created_by_name',
                    searchable: false,
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<a href="/text/${row.id}" class="text-primary">${data}</a>`;
                    }
                },
                {
                    data: 'text_status_id',
                    name: 'text_status_id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let buttons = '';

                        @if(auth()->user()->roles->first()->name == 'Admin')
                        if (row.text_status_id == 6) {
                            buttons += `
                                <button type="button" class="btn btn-success btn-xs approve-btn" data-id="${row.id}" title="Approve SMS">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button type="button" class="btn btn-danger btn-xs decline-btn" data-id="${row.id}" title="Decline SMS">
                                    <i class="fas fa-times"></i> Decline
                                </button>
                            `;
                         }
                        @endif
                        return buttons;
                    }
                }
            ],
            columnDefs: [
                { targets: 0, className: 'text-center' }
            ]
        });

        // Handle View Manual Contacts button
        $(document).on('click', '.view-manual-contacts', function() {
            let contacts = $(this).data('contacts');
            if (contacts) {
                let contactList = '<ul>';
                contacts.split(',').forEach(contact => {
                    contactList += `<li>${contact.trim()}</li>`;
                });
                contactList += '</ul>';
                $('#contactsModalBody').html(contactList);
            } else {
                $('#contactsModalBody').html('<p>No contacts available.</p>');
            }
            $('#contactsModal').modal('show');
        });

        // Handle View Saved Contacts button
        $(document).on('click', '.view-saved-contacts', function() {
            let contactList = $(this).data('contact-list');
            let textId = $(this).data('text-id');

            if (contactList.length === 0) {
                $('#contactsModalBody').html('<p>No saved contacts available.</p>');
                $('#contactsModal').modal('show');
                return;
            }

            $.ajax({
                url: `/get-contacts`,
                method: 'POST',
                data: {
                    contact_ids: contactList,
                    text_id: textId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        let contacts = response.contacts;
                        let modalContent = `
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>List Title</th>
                                        <th>Contacts count</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>`;

                        contacts.forEach(contact => {
                            modalContent += `
                                <tr>
                                    <td>${contact.title}</td>
                                    <td>${contact.contact_list_count}</td>
                                    <td>
                                        <a href="/contacts/${contact.id}" class="btn btn-primary btn-sm" target="_blank">View Contacts</a>
                                    </td>
                                </tr>`;
                        });

                        modalContent += `</tbody></table>`;
                        $('#contactsModalBody').html(modalContent);
                    } else {
                        $('#contactsModalBody').html('<p>Error retrieving contacts.</p>');
                    }
                    $('#contactsModal').modal('show');
                },
                error: function() {
                    $('#contactsModalBody').html('<p>Failed to load contacts.</p>');
                    $('#contactsModal').modal('show');
                }
            });
        });

        // Handle Approve button click
        $(document).on('click', '.approve-btn', function(e) {
            e.preventDefault();
            let textId = $(this).data('id');

            if (!confirm('Are you sure you want to approve this SMS?')) {
                return;
            }

            $.ajax({
                url: '{{ route("text.approveDecline") }}',
                method: 'POST',
                data: {
                    action: 'approve',
                    text_id: textId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert('SMS approved successfully!');
                        $('#smsTable').DataTable().ajax.reload();
                    } else {
                        alert(response.message || 'Failed to approve SMS.');
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Error occurred while approving SMS.');
                }
            });
        });

        // Handle Decline button click
        $(document).on('click', '.decline-btn', function(e) {
            e.preventDefault();
            let textId = $(this).data('id');
            $('#declineTextId').val(textId);
            $('#declineReason').val('');
            $('#declineModal').modal('show');
        });

        // Handle Confirm Decline button click
        $(document).on('click', '#confirmDecline', function(e) {
            e.preventDefault();
            
            let textId = $('#declineTextId').val();
            let declineReason = $('#declineReason').val().trim();
            
            if (!declineReason) {
                alert('Please provide a reason for declining this SMS.');
                $('#declineReason').focus();
                return;
            }
            
            if (declineReason.length < 2) {
                alert('Please provide a more detailed reason (at least 2 characters).');
                $('#declineReason').focus();
                return;
            }

            $.ajax({
                url: '{{ route("text.approveDecline") }}',
                method: 'POST',
                data: {
                    action: 'decline',
                    text_id: textId,
                    decline_message: declineReason
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#declineModal').modal('hide');
                        alert('SMS declined successfully!');
                        $('#smsTable').DataTable().ajax.reload();
                    } else {
                        alert(response.message || 'Failed to decline SMS.');
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Error occurred while declining SMS.');
                }
            });
        });
    });
</script>
@stop