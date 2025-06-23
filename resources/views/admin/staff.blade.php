@extends('layout.master')
@section('title')
    User List
@endsection
@section('app-title')
    User Management
@endsection
@section('active-laboratory-open')
    menu-open
@endsection
@section('active-laboratory')
    active
@endsection
@section('active-laboratory-staff')
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
                        <th>Laboratory</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('modal.add_staff')
    @include('modal.update_staff')
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
                        exclude_roles: ['Admin', 'Borrower', 'Employee']
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
                        data: 'laboratory'
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

            $.ajax({
                method: 'POST',
                url: '/users',
                data: $('#addForm').serialize(),
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        $('#addForm').trigger('reset');
                        $("select").trigger("chosen:updated");
                        showSuccessMessage(response.msg);
                        $('#addModal').modal('hide');
                        table1.ajax.reload(null, false);
                    }
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
        });

        $('#updateForm').submit(function(event) {
            event.preventDefault();

            $.ajax({
                method: 'PUT',
                url: `/users/${userID}`,
                data: $('#updateForm').serialize(),
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        $('#updateForm').trigger('reset');
                        $("select").trigger("chosen:updated");
                        showSuccessMessage(response.msg);
                        $('#updateModal').modal('hide');
                        table1.ajax.reload(null, false);
                    }
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
        });
    </script>
@endsection
