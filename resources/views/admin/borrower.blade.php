@extends('layout.master')
@section('title')
    Borrower List
@endsection
@section('app-title')
    Borrower Management
@endsection
@section('active-borrower')
    active
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <table id="table1" class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Contact No</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('modal.add_borrower')
    @include('modal.update_borrower')
    <div class="modal fade" id="viewStudentID" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Student ID</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="student-id-image" src="" alt="Student ID" class="img-fluid rounded shadow-sm"
                        style="max-height: 500px;" />
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        var userID, table1;

        $(document).ready(function() {
            table1 = $('#table1').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "responsive": true,
                "ajax": {
                    url: '{{ route('users.index') }}',
                    dataSrc: '',
                    data: {
                        exclude_roles: ['Admin', 'Employee', 'Laboratory Head', 'Laboratory In-charge']
                    }
                },
                "columns": [{
                        data: 'count'
                    },
                    {
                        data: 'full_name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'contact_no'
                    },
                    {
                        data: 'user_role'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: '<"d-flex justify-content-between align-items-center"<"search-box"f><"custom-button"B>>rtip',
                buttons: [{
                    text: '<i class="fa fa-plus-circle"></i> Add New',
                    className: 'btn btn-primary btn-md',
                    action: function(e, dt, node, config) {
                        $('#addModal').modal({
                            backdrop: 'static',
                            keyboard: false,
                            show: true
                        });
                    }
                }],
            });

            $('#table1 tbody').on('click', 'td', function() {
                var cellIndex = $(this).index(); // Get column index of clicked cell

                // Ignore click if it's the 7th column (Actions)
                if (cellIndex === 6) return;

                var data = table1.row($(this).closest('tr')).data();

                if (data && data.student_id) {
                    $('#student-id-image').attr('src', data.student_id);
                    $('#viewStudentID').modal('show');
                } else {
                    $('#student-id-image').attr('src', '');
                    $('#viewStudentID').modal('hide');
                    toastr.warning('No Student ID image available.');
                }
            });
        });

        function update(id) {
            $.ajax({
                method: 'GET',
                url: `/users/${id}`,
                dataType: 'json',
                cache: false,
                success: function(response) {
                    userID = response.id;
                    $('#updateForm').find('input[id=first_name]').val(response.first_name);
                    $('#updateForm').find('input[id=middle_name]').val(response.middle_name);
                    $('#updateForm').find('input[id=last_name]').val(response.last_name);
                    $('#updateForm').find('input[id=extension_name]').val(response.extension_name);
                    $('#updateForm').find('input[id=contact_no]').val(response.contact_no);
                    $('#updateForm').find('input[id=email]').val(response.email);
                    $('#updateForm').find('select[id=user_role]').val(response.user_role);
                    $('#updateForm').find('select[id=laboratory_id]').val(response.laboratory_id);
                    $("select").trigger("chosen:updated");
                    $('#updateModal').modal({
                        backdrop: 'static',
                        keyboard: false,
                        show: true
                    });
                },
                error: function(jqXHR) {
                    if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                        let errors = jqXHR.responseJSON.errors;
                        let errorMsg = `${jqXHR.responseJSON.msg}\n`;
                        for (const [field, messages] of Object.entries(errors)) {
                            errorMsg += `- ${messages.join(', ')}\n`;
                        }
                        showErrorMessage(errorMsg);
                    } else {
                        showErrorMessage("An unexpected error occurred. Please try again.");
                    }
                }
            });
        }

        function status(id) {
            Swal.fire({
                title: 'Are you sure you want to change this user\'s status?',
                text: "This will update the user’s active/inactive state.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it',
                cancelButtonText: 'Cancel',
                reverseButtons: false,
                allowOutsideClick: false,
                showClass: {
                    popup: 'animated fadeInDown'
                },
                hideClass: {
                    popup: 'animated fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: 'PUT',
                        url: `/users/status/${id}`,
                        dataType: 'JSON',
                        cache: false,
                        success: function(response) {
                            if (response.valid) {
                                table1.ajax.reload(null, false);
                                showSuccessMessage(response.msg);
                            } else {
                                showErrorMessage(response.msg);
                            }
                        },
                        error: function(jqXHR) {
                            showErrorMessage(
                                jqXHR.responseJSON && jqXHR.responseJSON.msg
                                    ? jqXHR.responseJSON.msg
                                    : "An unexpected error occurred. Please try again."
                            );
                        }
                    });
                }
            });
        }


        function trash(id) {
            $.ajax({
                method: 'DELETE',
                url: `/users/${id}`,
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        table1.ajax.reload(null, false);
                        showSuccessMessage(response.msg);
                    }
                },
                error: function(jqXHR) {
                    showErrorMessage(jqXHR.responseJSON && jqXHR.responseJSON.msg ? jqXHR.responseJSON.msg :
                        "An unexpected error occurred. Please try again.");
                }
            });
        }

       $('#addForm').submit(function(event) {
                event.preventDefault();
                $('#addForm').find('button[type=submit]').attr('disabled', true);

                if ($('#addForm').valid()) {
                    $('#addModal').modal('hide');
                    Swal.fire({
                        title: 'Do you want to add this borrower?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No',
                        reverseButtons: false,
                        allowOutsideClick: false,
                        showClass: {
                            popup: 'animated fadeInDown'
                        },
                        hideClass: {
                            popup: 'animated fadeOutUp'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Serialize form data and manually append the `user_role` field
                            let formData = $('#addForm').serializeArray();

                            $.ajax({
                                method: 'POST',
                                url: '/users',
                                data: formData, // Use modified formData
                                dataType: 'JSON',
                                cache: false,
                                success: function(response) {
                                    if (response.valid) {
                                        $('#addForm')[0].reset();
                                        showSuccessMessage(response.msg);
                                        table1.ajax.reload(null, false);
                                    } else {
                                        showErrorMessage(response.msg);
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    if (jqXHR.responseJSON && jqXHR.responseJSON
                                        .error) {
                                        var errors = jqXHR.responseJSON.error;
                                        var errorMsg = "Error submitting data: " +
                                            errors + ". ";
                                        showErrorMessage(errorMsg);
                                    } else {
                                        showErrorMessage(
                                            'Something went wrong! Please try again later.'
                                        );
                                    }
                                }
                            });
                        }
                    });
                }

                $('#addForm').find('button[type=submit]').removeAttr('disabled');
            });

            $('#updateForm').submit(function(event) {
                event.preventDefault();
                $('#updateForm').find('button[type=submit]').attr('disabled', true);
                if ($('#updateForm').valid()) {
                    $('#updateModal').modal('hide');
                    Swal.fire({
                        title: 'Do you want to save the updated data?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No',
                        reverseButtons: false,
                        allowOutsideClick: false,
                        showClass: {
                            popup: 'animated fadeInDown'
                        },
                        hideClass: {
                            popup: 'animated fadeOutUp'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                method: 'PUT',
                                url: `/users/${userID}`,
                                data: $('#updateForm').serialize(),
                                dataType: 'JSON',
                                cache: false,
                                success: function(response) {
                                    if (response.valid) {
                                        $('#updateForm')[0].reset();
                                        showSuccessMessage(response.msg);
                                        table1.ajax.reload(null, false);
                                    } else {
                                        showErrorMessage(response.msg);
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    if (jqXHR.responseJSON && jqXHR.responseJSON
                                        .error) {
                                        var errors = jqXHR.responseJSON.error;
                                        var errorMsg = "Error submitting data: " +
                                            errors + ". ";
                                        showErrorMessage(errorMsg);
                                    } else {
                                        showErrorMessage(
                                            'Something went wrong! Please try again later.'
                                        );
                                    }
                                }
                            });
                        }
                    });
                }
                $('#updateForm').find('button[type=submit]').removeAttr('disabled');
            });


    </script>
@endsection
