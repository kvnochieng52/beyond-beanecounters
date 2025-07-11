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

        <div class="row">
            <div class="col-md-12">

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="activityTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Agent</th>
                                <th>Ticket No</th>
                                <th>Activity Title</th>
                                <th>Title/Name</th>
                                <th>Insitution</th>
                                <th>Amount</th>
                                <th>Balance</th>
                                <th>PTP(Amount)</th>
                                <th>PTP(Retire Date)</th>
                                <th>Description</th>
                                <th>Call Desposition</th>
                                <th>Due</th>
                                <th>Status</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    /* Improved table layout */
    #activityTable {
        width: 100% !important;
        table-layout: auto;
        /* Changed from fixed to auto */
    }

    /* Better responsive handling */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Ensure proper text wrapping */
    .wrap-text {
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        max-width: 200px;
        /* Set max width for long text columns */
    }

    /* Specific column width adjustments */
    #activityTable th:nth-child(1),
    #activityTable td:nth-child(1) {
        width: 40px;
        /* # column */
        text-align: center;
    }

    #activityTable th:nth-child(2),
    #activityTable td:nth-child(2) {
        width: 100px;
        /* Date column */
    }

    #activityTable th:nth-child(3),
    #activityTable td:nth-child(3) {
        width: 120px;
        /* Agent column */
    }

    #activityTable th:nth-child(4),
    #activityTable td:nth-child(4) {
        width: 80px;
        /* Ticket No column */
    }

    #activityTable th:nth-child(5),
    #activityTable td:nth-child(5) {
        width: 150px;
        /* Activity Title column */
    }

    #activityTable th:nth-child(6),
    #activityTable td:nth-child(6) {
        width: 120px;
        /* Title/Name column */
    }

    #activityTable th:nth-child(7),
    #activityTable td:nth-child(7) {
        width: 120px;
        /* Institution column */
    }

    #activityTable th:nth-child(8),
    #activityTable td:nth-child(8) {
        width: 100px;
        /* Amount column */
        text-align: right;
    }

    #activityTable th:nth-child(9),
    #activityTable td:nth-child(9) {
        width: 100px;
        /* Balance column */
        text-align: right;
    }

    #activityTable th:nth-child(10),
    #activityTable td:nth-child(10) {
        width: 100px;
        /* PTP Amount column */
        text-align: right;
    }

    #activityTable th:nth-child(11),
    #activityTable td:nth-child(11) {
        width: 100px;
        /* PTP Date column */
    }

    #activityTable th:nth-child(12),
    #activityTable td:nth-child(12) {
        width: 200px;
        /* Description column */
    }

    #activityTable th:nth-child(13),
    #activityTable td:nth-child(13) {
        width: 120px;
        /* Call Disposition column */
    }

    #activityTable th:nth-child(14),
    #activityTable td:nth-child(14) {
        width: 100px;
        /* Due column */
    }

    #activityTable th:nth-child(15),
    #activityTable td:nth-child(15) {
        width: 100px;
        /* Status column */
    }

    #activityTable th:nth-child(16),
    #activityTable td:nth-child(16) {
        width: 120px;
        /* Action column */
        text-align: center;
    }

    /* Better badge styling */
    .badge {
        font-size: 0.75em;
        white-space: nowrap;
    }

    /* Improve button spacing */
    .btn-xs {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
        margin: 0 1px;
    }

    /* DataTables wrapper adjustments */
    .dataTables_wrapper {
        width: 100%;
    }

    .dataTables_scroll {
        overflow: auto;
    }

    /* Ensure proper header alignment */
    .dataTables_scrollHead {
        overflow: hidden;
    }

    .dataTables_scrollBody {
        overflow: auto;
    }

    /* Fix for small screens */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .btn-xs {
            padding: 0.125rem 0.25rem;
            font-size: 0.6875rem;
        }

        .badge {
            font-size: 0.625em;
        }
    }
</style>

@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
    $(document).ready(function() {
        

    //     let table = $('#activityTable').DataTable({
    //     processing: true,
    //     serverSide: true,
    //     ajax: '{{ route('all-activity') }}',
    //     autoWidth: false,
    //     scrollX: true,
    //     columns: [
    //         {
    //             data: 'DT_RowIndex',
    //             name: 'DT_RowIndex',
    //             orderable: false,
    //             searchable: false,
    //             className: 'text-center'
    //         },
    //         {
    //             data: 'created_date',
    //             name: 'created_date',
    //             render: function(data, type, row) {
    //                 return moment(data).format('DD-MM-YYYY');
    //             }
    //         },
    //         {
    //             data: 'created_by_name',
    //             name: 'CREATED_BY_JOIN.name',
    //             render: function(data, type, row) {
    //                 return data || 'N/A';
    //             }
    //         },
    //         {
    //             data: 'lead_id',
    //             name: 'activities.lead_id',
    //             render: function(data, type, row) {
    //                 return data ? `#${data}` : 'N/A';
    //             }
    //         },
    //         {
    //             data: 'activity_type_title',
    //             name: 'activity_types.activity_type_title',
    //             render: function(data, type, row) {
    //                 let icon = row.activity_type_icon ? `<i class="${row.activity_type_icon}"></i> ` : '';
    //                 return `${icon}${data || 'N/A'}`;
    //             }
    //         },
    //         {
    //             data: 'lead_title',
    //             name: 'leads.title',
    //             render: function(data, type, row) {
    //                 return data || 'N/A';
    //             }
    //         },
    //         {
    //             data: 'institution_name',
    //             name: 'institutions.institution_name',
    //             render: function(data, type, row) {
    //                 return data || 'N/A';
    //             }
    //         },
    //         {
    //             data: 'lead_amount',
    //             name: 'leads.amount',
    //             render: function(data, type, row) {
    //                 return data ? `KES ${parseFloat(data).toLocaleString()}` : 'N/A';
    //             },
    //             className: 'text-right'
    //         },
    //         {
    //             data: 'lead_balance',
    //             name: 'leads.balance',
    //             render: function(data, type, row) {
    //                 return data ? `KES ${parseFloat(data).toLocaleString()}` : 'N/A';
    //             },
    //             className: 'text-right'
    //         },
    //         {
    //             data: 'ptp_amount',
    //             name: 'ptps.ptp_amount',
    //             render: function(data, type, row) {
    //                 return data ? `KES ${parseFloat(data).toLocaleString()}` : 'N/A';
    //             },
    //             className: 'text-right'
    //         },
    //         {
    //             data: 'ptp_expiry_date',
    //             name: 'ptps.ptp_expiry_date',
    //             render: function(data, type, row) {
    //                 return data ? moment(data).format('DD-MM-YYYY') : 'N/A';
    //             }
    //         },
    //         {
    //             data: 'description',
    //             name: 'activities.description',
    //             render: function(data, type, row) {
    //                 if (!data) return 'N/A';
    //                 // Truncate long descriptions
    //                 if (data.length > 100) {
    //                     return `<span title="${data}">${data.substring(0, 100)}...</span>`;
    //                 }
    //                 return data;
    //             },
    //             className: 'wrap-text'
    //         },
    //         {
    //             data: 'call_disposition_name',
    //             name: 'call_dispositions.call_disposition_name',
    //             render: function(data, type, row) {
    //                 return data || 'N/A';
    //             }
    //         },
    //         {
    //             data: 'due_date',
    //             name: 'activities.due_date',
    //             render: function(data, type, row) {
    //                 if (!data) return 'N/A';
                    
    //                 let dueDate = moment(data);
    //                 let now = moment();
    //                 let diff = dueDate.diff(now, 'days');
                    
    //                 if (diff < 0) {
    //                     return `<span class="badge bg-danger" title="Overdue by ${Math.abs(diff)} days">${dueDate.format('DD-MM-YYYY')}</span>`;
    //                 } else if (diff === 0) {
    //                     return `<span class="badge bg-warning" title="Due today">${dueDate.format('DD-MM-YYYY')}</span>`;
    //                 } else if (diff <= 3) {
    //                     return `<span class="badge bg-info" title="Due in ${diff} days">${dueDate.format('DD-MM-YYYY')}</span>`;
    //                 } else {
    //                     return `<span class="badge bg-success" title="Due in ${diff} days">${dueDate.format('DD-MM-YYYY')}</span>`;
    //                 }
    //             }
    //         },
    //         {
    //             data: 'activity_status_name',
    //             name: 'activity_statuses.activity_status_name',
    //             render: function(data, type, row) {
    //                 let colorCode = row.activity_status_color_code || 'secondary';
    //                 return `<span class="badge bg-${colorCode}">${data || 'N/A'}</span>`;
    //             }
    //         },
    //         {
    //             data: 'action',
    //             name: 'action',
    //             orderable: false,
    //             searchable: false,
    //             render: function(data, type, row) {
    //                 const canEdit = @json(auth()->user()->hasAnyRole(['Admin', 'Supervisor', 'Manager']));
    //                 const canDelete = @json(auth()->user()->hasAnyRole(['Admin', 'Supervisor']));
                    
    //                 let buttons = '';
                    
    //                 // View button
    //                 buttons += `
    //                     <a href="/activities/${row.id}" class="btn btn-info btn-xs" title="View">
    //                         <i class="fas fa-eye"></i>
    //                     </a> `;
                    
    //                 // Edit button
    //                 if (canEdit) {
    //                     buttons += `
    //                         <button type="button" class="btn btn-primary btn-xs edit-btn" 
    //                                 data-id="${row.id}" 
    //                                 data-title="${row.activity_type_title || ''}" 
    //                                 data-description="${row.description || ''}" 
    //                                 data-toggle="modal" 
    //                                 data-target="#edit_activity_modal" 
    //                                 title="Edit">
    //                             <i class="fas fa-edit"></i>
    //                         </button> `;
    //                 }
                    
    //                 // Delete button
    //                 if (canDelete) {
    //                     buttons += `
    //                         <form action="/activities/${row.id}" method="POST" class="d-inline">
    //                             @csrf
    //                             @method('DELETE')
    //                             <button type="submit" class="btn btn-danger btn-xs" 
    //                                     onclick="return confirm('Are you sure you want to delete this activity?')" 
    //                                     title="Delete">
    //                                 <i class="fas fa-trash"></i>
    //                             </button>
    //                         </form>`;
    //                 }
                    
    //                 return buttons;
    //             }
    //         }
    //     ],
    //     columnDefs: [
    //         {
    //             targets: [7, 8, 9], // Amount columns
    //             className: 'text-right'
    //         },
    //         {
    //             targets: [11], // Description column
    //             className: 'wrap-text'
    //         }
    //     ],
    //     order: [[1, 'desc']], // Order by date descending
    //     pageLength: 25,
    //     responsive: true,
    //     dom: 'Bfrtip',
    //     buttons: [
    //         'copy', 'csv', 'excel', 'pdf', 'print'
    //     ]
    // });



    let table = $('#activityTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/activity/', // Update with your route
                autoWidth: false,
                scrollX: true,
                scrollCollapse: true,
                fixedHeader: true,
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        width: '40px'
                    },
                    {
                        data: 'created_date',
                        name: 'created_date',
                        render: function(data, type, row) {
                            return data ? moment(data).format('DD-MM-YYYY') : 'N/A';
                        },
                        width: '100px'
                    },
                    {
                        data: 'created_by_name',
                        name: 'CREATED_BY_JOIN.name',
                        render: function(data, type, row) {
                            return data || 'N/A';
                        },
                        width: '120px'
                    },
                    {
                        data: 'lead_id',
                        name: 'activities.lead_id',
                        render: function(data, type, row) {
                            return data ? `#${data}` : 'N/A';
                        },
                        width: '80px'
                    },
                    {
                        data: 'activity_title',
                        name: 'activity_title',
                      
                        width: '150px'
                    },
                    {
                        data: 'lead_title',
                        name: 'leads.title',
                        render: function(data, type, row) {
                            return data || 'N/A';
                        },
                        width: '120px'
                    },
                    {
                        data: 'institution_name',
                        name: 'institutions.institution_name',
                        render: function(data, type, row) {
                            return data || 'N/A';
                        },
                        width: '120px'
                    },
                    {
                        data: 'lead_amount',
                        name: 'leads.amount',
                        render: function(data, type, row) {
                            return data ? `KES ${parseFloat(data).toLocaleString()}` : 'N/A';
                        },
                        className: 'text-right',
                        width: '100px'
                    },
                    {
                        data: 'lead_balance',
                        name: 'leads.balance',
                        render: function(data, type, row) {
                            return data ? `KES ${parseFloat(data).toLocaleString()}` : 'N/A';
                        },
                        className: 'text-right',
                        width: '100px'
                    },
                    {
                        data: 'ptp_amount',
                        name: 'ptps.ptp_amount',
                        render: function(data, type, row) {
                            return data ? `KES ${parseFloat(data).toLocaleString()}` : 'N/A';
                        },
                        className: 'text-right',
                        width: '100px'
                    },
                    {
                        data: 'ptp_expiry_date',
                        name: 'ptps.ptp_expiry_date',
                        render: function(data, type, row) {
                            return data ? moment(data).format('DD-MM-YYYY') : 'N/A';
                        },
                        width: '100px'
                    },
                    {
                        data: 'description',
                        name: 'activities.description',
                        render: function(data, type, row) {
                            if (!data) return 'N/A';
                            // Truncate long descriptions
                            if (data.length > 50) {
                                return `<span title="${data.replace(/"/g, '&quot;')}">${data.substring(0, 50)}...</span>`;
                            }
                            return data;
                        },
                        className: 'wrap-text',
                        width: '200px'
                    },
                    {
                        data: 'call_disposition_name',
                        name: 'call_dispositions.call_disposition_name',
                        render: function(data, type, row) {
                            return data || 'N/A';
                        },
                        width: '120px'
                    },
                    {
                        data: 'due_date',
                        name: 'activities.due_date',
                        render: function(data, type, row) {
                            if (!data) return 'N/A';
                            
                            let dueDate = moment(data);
                            let now = moment();
                            let diff = dueDate.diff(now, 'days');
                            
                            if (diff < 0) {
                                return `<span class="badge badge-danger" title="Overdue by ${Math.abs(diff)} days">${dueDate.format('DD-MM-YYYY')}</span>`;
                            } else if (diff === 0) {
                                return `<span class="badge badge-warning" title="Due today">${dueDate.format('DD-MM-YYYY')}</span>`;
                            } else if (diff <= 3) {
                                return `<span class="badge badge-info" title="Due in ${diff} days">${dueDate.format('DD-MM-YYYY')}</span>`;
                            } else {
                                return `<span class="badge badge-success" title="Due in ${diff} days">${dueDate.format('DD-MM-YYYY')}</span>`;
                            }
                        },
                        width: '100px'
                    },
                    {
                        data: 'activity_status_name',
                        name: 'activity_statuses.activity_status_name',
                        render: function(data, type, row) {
                            let colorCode = row.activity_status_color_code || 'secondary';
                            return `<span class="badge badge-${colorCode}">${data || 'N/A'}</span>`;
                        },
                        width: '100px'
                    },
                    // {
                    //     data: 'action',
                    //     name: 'action',
                    //     orderable: false,
                    //     searchable: false,
                    //     render: function(data, type, row) {
                    //         let buttons = '';
                            
                    //         // View button
                    //         buttons += `<a href="/activities/${row.id}" class="btn btn-info btn-xs" title="View"><i class="fas fa-eye"></i></a> `;
                            
                    //         // Edit button
                    //         buttons += `<button type="button" class="btn btn-primary btn-xs edit-btn" data-id="${row.id}" title="Edit"><i class="fas fa-edit"></i></button> `;
                            
                    //         // Delete button
                    //         buttons += `<button type="button" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?')" title="Delete"><i class="fas fa-trash"></i></button>`;
                            
                    //         return buttons;
                    //     },
                    //     className: 'text-center',
                    //     width: '120px'
                    // }
                ],
                order: [[1, 'desc']], // Order by date descending
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6">>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-secondary btn-sm'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-secondary btn-sm'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-secondary btn-sm'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-secondary btn-sm'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-secondary btn-sm'
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