@extends('layout.master')
@section('title')
    Category List
@endsection
@section('app-title')
    Category Management
@endsection
@section('active-items-open')
    menu-open
@endsection
@section('active-items')
    active
@endsection
@section('active-items-category')
    active
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <table id="table1" class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Category Type</th>
                        <th>Laboratory</th>
                        <th>Date Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('modal.add_category')
    @include('modal.update_category')
@endsection
@section('scripts')
    <script type="text/javascript">
        var categoryID, table1;

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
                    url: '{{ route('categories.index') }}',
                    dataSrc: '',
                },
                "columns": [{
                        data: 'count'
                    },
                    {
                        data: 'category_name'
                    },
                    {
                        data: 'category_type'
                    },
                    {
                        data: 'laboratory'
                    },
                    {
                        data: 'created_at',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            });
                        }
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
                url: `/categories/${id}`,
                dataType: 'json',
                cache: false,
                success: function(response) {
                    categoryID = response.id;
                    $('#updateForm').find('input[id=category_name]').val(response.category_name);
                    $('#updateForm').find('select[id=category_type]').val(response.category_type);
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
                url: `/categories/${id}`,
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
                url: '/categories',
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
                url: `/categories/${categoryID}`,
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
