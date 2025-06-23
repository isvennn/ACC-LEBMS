@extends('layout.master')
@section('title')
    Item List
@endsection
@section('app-title')
    Item Management
@endsection
@section('active-items-open')
    menu-open
@endsection
@section('active-items')
    active
@endsection
@section('active-items-list')
    active
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <table id="table1" class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Laboratory</th>
                        <th>Beginning Qty</th>
                        <th>Current Qty</th>
                        <th>Date Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('modal.add_item')
    @include('modal.update_item')
@endsection
@section('scripts')
    <script type="text/javascript">
        var itemID, table1;

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
                    url: '{{ route('items.index') }}',
                    dataSrc: '',
                },
                "columns": [{
                        data: 'count'
                    },
                    {
                        data: 'item_name'
                    },
                    {
                        data: 'item_description'
                    },
                    {
                        data: 'item_price'
                    },
                    {
                        data: 'category'
                    },
                    {
                        data: 'laboratory'
                    },
                    {
                        data: 'beginning_qty'
                    },
                    {
                        data: 'current_qty'
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
                url: `/items/${id}`,
                dataType: 'json',
                cache: false,
                success: function(response) {
                    itemID = response.id;
                    $('#updateForm').find('input[id=item_name]').val(response.item_name);
                    $('#updateForm').find('textarea[id=item_description]').val(response.item_description);
                    $('#updateForm').find('input[id=item_price]').val(response.item_price);
                    $('#updateForm').find('select[id=category_id]').val(response.category_id);
                    $('#updateForm').find('input[id=beginning_qty]').val(response.beginning_qty);
                    $('#updateForm').find('input[id=current_qty]').val(response.current_qty);
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
                url: `/items/${id}`,
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
                url: '/items',
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
                url: `/items/${itemID}`,
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
