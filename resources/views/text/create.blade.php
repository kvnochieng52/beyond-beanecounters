@extends('adminlte::page')

@section('title', 'Bulk SMS Campaign')

@section('content_header')
{{-- <h1>Create Bulk SMS Campaign</h1> --}}
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">New SMS</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('text.store') }}" method="POST" enctype="multipart/form-data" id="smsForm">
            @csrf
            <div class="form-group">
                <label for="title">Title of SMS / Campaign*</label>
                <input type="text" name="title" id="title" class="form-control"
                    placeholder="Enter the SMS/Campaing Title">
            </div>

            <div class="form-group">
                <label>Select Contact Source*</label>
                <div class="d-flex align-items-center flex-wrap">
                    <div class="form-check mr-3">
                        <input class="form-check-input" type="radio" name="contact_source" value="manual" id="manual"
                            checked>
                        <label class="form-check-label" for="manual">Enter Recipient Contacts</label>
                    </div>
                    <div class="form-check mr-3">
                        <input class="form-check-input" type="radio" name="contact_source" value="csv" id="csv">
                        <label class="form-check-label" for="csv">Import from a CSV File</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="contact_source" value="saved" id="saved">
                        <label class="form-check-label" for="saved">From a Saved Contact List</label>
                    </div>
                </div>
            </div>

            <div class="form-group" id="manual-input">
                <label for="contacts">Enter Contacts (comma-separated)</label>
                <textarea name="contacts" id="contacts" class="form-control" rows="2"
                    placeholder="Enter the contacts comma separated e.g. 0712345678,0714345678"></textarea>
            </div>

            <div class="form-group d-none" id="csv-upload">

                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#csvModal">UPLOAD CSV
                    FILE</button>
                <div class="csv_file_details mt-2"></div>

                <input type="hidden" name="csv_file_name" id="csv_file_name">
                <input type="hidden" name="csv_file_path" id="csv_file_path">
                <input type="hidden" name="csv_file_columns" id="csv_file_columns">
            </div>

            <div class="form-group d-none" id="saved-list">
                <label for="contact_list">Select Contact List</label>
                <select name="contact_list[]" id="contact_list" class="select2 form-control" multiple
                    style="width:100%">
                    <option value="">Select a list</option>
                    @foreach($contactLists as $key=>$list)
                    <option value="{{$key}}">{{$list}}</option>
                    @endforeach
                </select>
            </div>


            <div class="row sms_template_row pb-3 d-none" id="sms_template_row">
               
                <div class="col-md-12 sms_template-group">
                    <p class="text-warning" style="font-size: 12px">Ensure the CSV File has a Ticket No column. If you shall be using any of the templates</p>
                    {!! Html::label('Select SMS Template', 'sms_template') !!}
                    {!! Html::select('sms_template', $sms_templates)->class('form-control
                    select2')->id('sms_template')->placeholder('--Specify--')->style("width:100%") !!}
                </div>
            
            </div>


            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center">
                    <label for="message">Message*</label>
                    <div class="dropdown d-none" id="csvColumnsWrapper">
                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="csvColumnsDropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{ }</button>
                        <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown"
                            aria-labelledby="csvColumnsDropdown"></ul>
                    </div>
                </div>
                <textarea name="message" id="message" class="form-control" rows="4"
                    placeholder="Enter your text message"></textarea>
            </div>


               <div class="row sms_template_row" id="sms_template_row" style="display: none" >
                        
                            <div class="col-md-12 sms_template-group pt-3">
                                {!! Html::label('Select SMS Template', 'sms_template') !!}
                                {!! Html::select('sms_template', $sms_templates)->class('form-control
                                select2')->id('sms_template')->placeholder('--Specify--')->style("width:100%") !!}
                            </div>
                        
                    </div>

            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="schedule" name="schedule">
                    <label class="form-check-label" for="schedule">Schedule SMS</label>
                </div>
            </div>

            <div class="form-group d-none" id="schedule-fields">
                <label for="schedule_date">Date & Time</label>
                <input type="text" name="schedule_date" id="schedule_date" class="form-control-sm datepicker"
                    placeholder="DD/MM/YYYY" style="width: 200px !important">
                <input type="text" name="schedule_time" id="schedule_time" class="form-control-sm timepicker"
                    placeholder="HH:MM AM/PM" style="width: 150px !important">
            </div>


            <div id="smsErrors" class="alert alert-danger d-none"></div>
            <input type="hidden" name="sms_contacts_count" id="sms_contacts_count">
            <button type="submit" class="btn btn-primary mt-3">PREVIEW SMS</button>
<input type="hidden" name="selectedTemplate" id="selectedTemplate">

        </form>
    </div>
</div>

<!-- CSV Upload Modal -->
<div class="modal fade" id="csvModal" tabindex="-1" aria-labelledby="csvModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="csvUploadForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="csvModalLabel">Upload CSV File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div id="csvError" class="alert alert-danger d-none"></div>
                    <div class="progress d-none" id="uploadProgress">
                        <div class="progress-bar" role="progressbar" style="width: 0%;">0%</div>
                    </div>
                    <input type="file" name="csv_file" id="csv_file" class="form-control mt-2" accept=".csv" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="previewSMSModal" tabindex="-1" aria-labelledby="previewSMSModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewSMSModalLabel">SMS Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="loading_wait">
                    <p>Loading Preview... Please Wait</p>
                </div>
                {{-- <div class="alert alert-info">
                </div> --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="sendSMSButton" class="btn btn-primary">
                    <span class="button-text">Send SMS</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>





@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<link rel="stylesheet" href="/css/validator/bootstrapValidator.min.css" />
<style>
    .csv_file_details a {
        color: #007bff;
        text-decoration: underline;
        margin-right: 10px;
    }

    .remove-csv {
        cursor: pointer;
        color: red;
        font-weight: bold;
    }


    .scrollable-dropdown {
        max-height: 200px;
        /* Adjust height as needed */
        overflow-y: auto;
    }
</style>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script src="/js/validator/bootstrapValidator.min.js"></script>
<script>
    $(document).ready(function() {
    // Initialize datepicker and timepicker
    $('.datepicker').datepicker({ dateFormat: 'dd/mm/yy', minDate: 0 });
    $('.timepicker').timepicker({ timeFormat: 'hh:mm p', interval: 30 });

    $('.select2').select2();

    $('#sendSMSButton').on('click', function() {
     $('#smsForm').off('submit').submit(); // Prevent double binding, then submit
    });

    // Toggle contact source input sections
    $('input[name="contact_source"]').on('change', function() {

        $('#sms_template_row').addClass('d-none')
        $('#manual-input, #csv-upload, #saved-list').addClass('d-none');
        if ($(this).val() === 'manual') $('#manual-input').removeClass('d-none');
        if ($(this).val() === 'saved') $('#saved-list').removeClass('d-none');
        if ($(this).val() === 'csv'){ 
            $('#sms_template_row').removeClass('d-none');
            $('#csv-upload').removeClass('d-none');
          
        }
       
    });

    // Toggle schedule fields
    $('#schedule').on('change', function() {
        $('#schedule-fields').toggleClass('d-none', !this.checked);
    });

    // CSV upload form submission
    $('#csvUploadForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $('#csvError').addClass('d-none').text('');
        $('#uploadProgress').removeClass('d-none');
        $('#uploadProgress .progress-bar').css('width', '0%').text('0%');

        $.ajax({
            url: '/texts/upload_csv',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                let xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        $('#uploadProgress .progress-bar').css('width', percent + '%').text(percent + '%');
                    }
                });
                return xhr;
            },
            success: function(response) {
                $('#uploadProgress').addClass('d-none');
                $('#csvModal').modal('hide');

                // Display uploaded CSV details
                // const fileLink = `<a href="${response.csv_path}" target="_blank">${response.original_name}</a>`;


                console.log(response);

                const fileLink = `<a href="#" target="_blank"><strong>${response.original_name}</strong></a>`;
                const removeBtn = `<span class="remove-csv" title="Remove CSV">&times;</span>`;
                $('.csv_file_details').html(fileLink + removeBtn);


             

                $('#csv_file_name').val(response.original_name);
                $('#csv_file_path').val(response.path);
                $('#csv_file_columns').val(response.columns); 

                // Populate dropdown with CSV columns
                const columns = response.columns.map(col => `<li><a class="dropdown-item" href="#" data-placeholder="{${col}}">{${col}}</a></li>`).join('');
                $('#csvColumnsDropdown').siblings('.dropdown-menu').html(columns);
                $('#csvColumnsWrapper').removeClass('d-none');

                // Insert column placeholder into message textarea at cursor position
                $('.dropdown-item').click(function(e) {
                    e.preventDefault();
                    insertAtCursor($('#message')[0], $(this).data('placeholder'));
                });

                // Remove uploaded CSV file details
                $('.remove-csv').click(function() {
                    $('.csv_file_details').empty();
                    $('#csv_file').val('');

                    $('#csv_file_name').val('');
                    $('#csv_file_path').val('');
                    $('#csv_file_columns').val('');

                    $('#csvColumnsWrapper').addClass('d-none');
                });


              
            },
            error: function(xhr) {
                $('#uploadProgress').addClass('d-none');
                const errorMsg = xhr.responseJSON?.error || 'An unexpected error occurred. Please try again.';
                $('#csvError').removeClass('d-none').text(errorMsg);
            }
        });
    });

    // Function to insert text at cursor position
    function insertAtCursor(textarea, text) {
        if (document.selection) {
            textarea.focus();
            const sel = document.selection.createRange();
            sel.text = text;
        } else if (textarea.selectionStart || textarea.selectionStart === 0) {
            const startPos = textarea.selectionStart;
            const endPos = textarea.selectionEnd;
            const before = textarea.value.substring(0, startPos);
            const after = textarea.value.substring(endPos, textarea.value.length);
            textarea.value = before + text + after;
            textarea.selectionStart = textarea.selectionEnd = startPos + text.length;
            textarea.focus();
        } else {
            textarea.value += text;
            textarea.focus();
        }
    }


$('#smsForm').on('submit', function(e) {
    e.preventDefault(); // Prevent default form submission
    $('#smsErrors').empty(); // Clear previous errors

    let errors = [];

    // Gather form values
    const title = $('#title').val().trim();
    const recipientMethod = $('input[name="contact_source"]:checked').val();
    const contacts = $('#contacts').val().trim();
    const csvFilePath = $('#csv_file_path').val().trim();
    const contactList = $('#contact_list').val();
    const message = $('#message').val().trim();
    const isScheduled = $('#schedule').is(':checked');
    const scheduleDate = $('#schedule_date').val().trim();
    const scheduleTime = $('#schedule_time').val().trim(); 

    // --- Validation ---
    if (!title) errors.push('Title is required.');

    if (recipientMethod === 'manual' && !contacts) {
        errors.push('Please enter recipient contacts.');
    } else if (recipientMethod === 'csv' && !csvFilePath) {
        errors.push('Please select a CSV file.');
    } else if (recipientMethod === 'saved' && !contactList) {
        errors.push('Please select a contact list.');
    }

    if (!message) errors.push('Message field is required.');

    if (isScheduled) {
        if (!scheduleDate) errors.push('Please enter the date/time for scheduling SMS.');
        if (!scheduleTime) errors.push('Please enter the time for scheduling SMS.');
    }

    // --- Display errors or send AJAX request ---
    if (errors.length > 0) {
        const errorList = $('<ul></ul>').addClass('error-list');
        errors.forEach(error => errorList.append($('<li></li>').text(error)));
        $('#smsErrors').append(errorList).removeClass('d-none');

        // Smooth scroll to the form
        $('html, body').animate({
            scrollTop: $('#smsForm').offset().top
        }, 500);

    } else {
        // --- Prepare data payload ---
        const formData = {
            title,
            recipientMethod,
            contacts,
            csvFilePath,
            contactList,
            message,
            schedule: isScheduled,
            scheduleDate,
            scheduleTime
        };

        // --- AJAX POST request ---
        $.ajax({
            url: '/texts/preview-sms',
            type: 'POST',
            data: formData,
            dataType: 'json',
            headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                // Optional: show loader or disable button
            },
            success: function(response) {

                const alertDiv = $(`
                <div class="alert alert-info">
                    ${response.personalizedMessage || 'No personalized message available.'}
                </div>
                `);
                
                // Create table with the response data
                const table = $(`
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>Total Contacts</strong></td>
                            <td>${response.totalContacts}</td>
                        </tr>
                        <tr>
                            <td><strong>Valid Contacts</strong></td>
                            <td><span style="color: green; font-weight: bold;">${response.validContacts}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Invalid Contacts</strong></td>
                            <td><span style="color: red; font-weight: bold;">${response.invalidContacts}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Message Total Characters</strong></td>
                            <td>${response.messageTotalChars}</td>
                        </tr>
                    </tbody>
                </table>
                `);


                $('#sms_contacts_count').val(response.totalContacts);
                
                // Populate modal body
                $('#previewSMSModal .modal-body').html('').append(alertDiv, table);
                
                // Show the modal
                $('#previewSMSModal').modal('show');


                //console.log(response)
               // $('.modal-body').html(response || 'No preview available.');

                // Show the modal after successful request
               // $('#previewSMSModal').modal('show');


               
               
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                $('#smsErrors').html(`<ul class="error-list"><li>${errorMsg}</li></ul>`).removeClass('d-none');

                // Scroll to the error section
                $('html, body').animate({
                    scrollTop: $('#smsErrors').offset().top
                }, 500);
            },
            complete: function() {
                // Optional: hide loader or re-enable button
            }
        });
    }
});


 $(document).on('change', 'select[name="sms_template"]', function() {
       var selectedValue = $(this).val();
        var selectedText = $(this).find('option:selected').text();
$('#selectedTemplate').val(selectedValue);
    

        var sms_message='';

        if(selectedValue=='introduction'){
            sms_message="Dear {}, Your debt for {}, of {} has been forwarded to Beyond BeanCounters for recovery. Urgently pay via {}, account: {}, or reach out to us to discuss a repayment plan, 0116648476.";   
        }


        
        if(selectedValue=='no_anwser'){
            sms_message="Dear {}, we have tried calling you without success. Kindly but urgently get in touch with us to discuss your debt with {} of {}. The debt ought to be settled to avoid additional penalties and other charges. Pay through {}, account number {}. Notify us on 0116648476."; 
        }

        if(selectedValue=='ptp_reminder'){
            sms_message="Dear {}, remember to make payment for Your debt of {}, of {} today. {}, account: {}. Notify us on 0116648476"; 
        }


        if(selectedValue=='refusal_to_pay'){
            sms_message="Dear {}, Despite previous reminders, your {} debt of {}, remains uncleared. Be strongly advised that failure to do so will force us to recover the debt at your cost, using our Field Collectors. Pay through {}, account {}. Notify us on 0116648476."; 
        }


          if(selectedValue=='broken_ptp_follow_up'){
            sms_message="Greetings, we have not yet received your  {} payment. Urgently pay. {}, Acc: {}. Notify us on 0116648476"; 
        }


        $('#message').val(sms_message);

     
       if(selectedValue != 'other') {
            $('#message').prop('readonly', true).css('background-color', '#f5f5f5');
       }else{
            $('#message').prop('readonly', false).css('background-color', '#ffffff');
       }
        
    });

 
});
</script>
@stop