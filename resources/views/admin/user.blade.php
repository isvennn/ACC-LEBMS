@extends('layout.master')
@section('title')
User List
@endsection
@section('app-title')
Users Management
@endsection
@section('active-users')
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
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div id="changePasswordModal" class="modal fade">
    <div class="modal-dialog">
        <form id="changePasswordForm" class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Update Password</h3>
            </div>
            <div class="modal-body">
                <!-- Current Password -->
                <div class="form-group password-wrapper">
                    <label for="current_password">Current Password <span class="text-danger">*</span></label>
                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                    <span class="password-toggle" data-target="#current_password"></span>
                </div>

                <!-- New Password -->
                <div class="form-group password-wrapper">
                    <label for="new_password">New Password <span class="text-danger">*</span></label>
                    <input type="password" name="new_password" id="new_password" class="form-control" required>
                    <span class="password-toggle" data-target="#new_password"></span>
                </div>

                <!-- Confirm Password -->
                <div class="form-group password-wrapper">
                    <label for="new_password_confirmation">Confirm New Password <span class="text-danger">*</span></label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                    <span class="password-toggle" data-target="#new_password_confirmation"></span>
                </div>

            </div>
            <div class="modal-footer text-right">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    var userID, table1;

    $(document).ready(function() {
        // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize DataTable
        table1 = $('#table1').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "responsive": true,
            "ajax": {
                url: '{{ route('users.list') }}',
                dataSrc: '',
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
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            dom: '<"d-flex justify-content-between align-items-center"<"search-box"f>>rtip',
            buttons: []
        });

        // Initialize jQuery Validation for changePasswordForm
        $("#changePasswordForm").validate({
            rules: {
                current_password: {
                    required: true
                },
                new_password: {
                    required: true,
                    minlength: 8,
                    regex: /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+$/
                },
                new_password_confirmation: {
                    required: true,
                    equalTo: "#new_password"
                }
            },
            messages: {
                current_password: {
                    required: "Please provide your current password."
                },
                new_password: {
                    required: "Please provide a new password.",
                    minlength: "Password must be at least 8 characters long.",
                    regex: "Password must include at least one uppercase letter, one lowercase letter, and one number."
                },
                new_password_confirmation: {
                    required: "Please confirm your new password.",
                    equalTo: "Passwords do not match."
                }
            },
            errorElement: "span",
            errorPlacement: function(error, element) {
                error.addClass("invalid-feedback");
                element.closest(".form-group").append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass("is-invalid");
            }
        });

        // Custom regex method for password validation
        $.validator.addMethod("regex", function(value, element, regexp) {
            return this.optional(element) || new RegExp(regexp).test(value);
        }, "Please follow the password requirements.");

        // Handle form submission
        $('#changePasswordForm').submit(function(event) {
            event.preventDefault();

            if (!$(this).valid()) {
                return;
            }

            $('#changePasswordModal').modal('hide');

            // SweetAlert confirmation
            Swal.fire({
                icon: 'question',
                title: 'Do you want to update the password?',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with AJAX form submission
                    

            $.ajax({
                        method: 'POST',
                        url: '{{ route("changeUserPassword", ["id" => ":id"]) }}'.replace(':id', userID),
                        data: $(this).serialize(),
                        dataType: 'JSON',
                        cache: false,
                        success: function(response) {
                            if (response.valid) {
                                // Display success message
                                showSuccessMessage(response.msg);
                                table1.ajax.reload(null, false); // Use table1 instead of table
                            } else {
                                // Display error message
                                showErrorMessage(response.msg);
                            }
                        },
                        error: function(xhr, textStatus, error) {
                            // Handle unexpected errors
                            let errorMessage = "An error occurred. Please try again.";
                            if (xhr.responseJSON && xhr.responseJSON.msg) {
                                errorMessage = xhr.responseJSON.msg;
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            showErrorMessage(errorMessage);
                        }
                    });
                } else {
                    // Re-open modal if canceled
                    $('#changePasswordModal').modal('show');
                }
            });
        });

        // Password toggle functionality
        $('.password-toggle').click(function() {
            var target = $(this).data('target');
            var $input = $(target);
            var type = $input.attr('type') === 'password' ? 'text' : 'password';
            $input.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });
    });

    // Update function to open modal
    function update(id) {
        userID = id;
        // $('#changePasswordForm')[0].reset(); // Clear form fields
        $('#changePasswordModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

    // SweetAlert success and error functions (assuming they are defined elsewhere)
    function showSuccessMessage(msg) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: msg,
            timer: 3000,
            showConfirmButton: false
        });
    }

    function showErrorMessage(msg) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: msg,
            confirmButtonColor: '#d33'
        });
    }
</script>
@endsection