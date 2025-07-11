@extends('layout.master')
@section('title')
    Transaction List
@endsection
@section('app-title')
    Transaction Management
@endsection
@section('active-transactions')
    active
@endsection
@section('APP-CSS')
    <style>
        .modal-content {
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            border-bottom: none;
        }

        .modal-footer {
            border-top: none;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 5px;
        }

        .chosen-container {
            width: 100% !important;
        }

        @media (max-width: 768px) {
            .modal-dialog {
                margin: 0.5rem;
            }

            .table-responsive {
                font-size: 0.9rem;
            }
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12" id="transaction-table">
            <table id="table1" class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Transaction No</th>
                        <th>Item</th>
                        <th>Reserve Qty</th>
                        <th>Date of Usage</th>
                        <th>Date of Return</th>
                        <th>Time of Return</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="card card-outline card-lime" id="transaction-add" style="display: none;">
        <form id="addForm" class="card-content">
            <input type="hidden" class="form-control" id="user_id" name="user_id" value="{{ auth()->user()->id }}">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 text-left mb-3">
                        <p class="badge badge-success">Remaining Transaction: <span id="remainingTransaction"></span></p>
                    </div>
                    <div class="col-lg-6 text-right mb-3">
                        <button type="button" id="addItemRow" class="btn btn-outline-success btn-sm"><i
                                class="fas fa-plus"></i> Add Item</button>
                    </div>
                </div>
                <table class="table table-bordered table-striped" id="itemsTable">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 25%;">Item</th>
                            <th style="width: 20%;">Date of Usage</th>
                            <th style="width: 20%;">Date of Return</th>
                            <th style="width: 15%;">Time of Return</th>
                            <th style="width: 15%;">Quantity</th>
                            <th style="width: 5%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <tr class="item-row">
                            <td>
                                <select class="form-select chosen-select item-select" name="items[0][item_id]"
                                    data-placeholder="Select Item" required>
                                    <option value=""></option>
                                    @foreach (\App\Models\Item::all() as $item)
                                        <option value="{{ $item->id }}">{{ $item->item_name }}
                                            ({{ $item->current_qty }} available)
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="date" class="form-control" name="items[0][date_of_usage]" required
                                    min="{{ date('Y-m-d') }}">
                            </td>
                            <td>
                                <input type="date" class="form-control" name="items[0][date_of_return]" required>
                            </td>
                            <td>
                                <input type="time" class="form-control" name="items[0][time_of_return]" required>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="items[0][reserve_quantity]" min="1"
                                    required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i
                                        class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                <button type="button" class="btn btn-secondary" id="transaction-cancel"><i class="fas fa-times"></i>
                    Close</button>
            </div>
        </form>
    </div>
    @include('employee.update_transaction')
@endsection
@section('scripts')
    <script type="text/javascript">
        var transactionID, table1;
        let rowCount = 1;
        const maxRows = 5;

        $(document).ready(function() {
            // Initialize Chosen.js
            $('.chosen-select').chosen({
                width: '100%',
                allow_single_deselect: true,
                placeholder_text_single: 'Select an option'
            });

            table1 = $('#table1').DataTable({
                paging: true,
                lengthChange: false,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: true,
                responsive: true,
                ajax: {
                    url: '{{ route('transactions.index') }}',
                    dataSrc: '',
                },
                columns: [{
                        data: 'count'
                    },
                    {
                        data: 'transaction_no'
                    },
                    {
                        data: 'item_name'
                    },
                    {
                        data: 'reserve_quantity'
                    },
                    {
                        data: 'date_of_usage'
                    },
                    {
                        data: 'date_of_return'
                    },
                    {
                        data: 'time_of_return'
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
                        $('#transaction-table').hide();
                        $('#transaction-add').show();
                        $('#addForm').trigger('reset');
                        $('.chosen-select').trigger('chosen:updated');
                        $('#itemsTableBody').html(`
                            <tr class="item-row">
                                <td>
                                    <select class="form-select chosen-select item-select" name="items[0][item_id]" data-placeholder="Select Item" required>
                                        <option value=""></option>
                                        @foreach (\App\Models\Item::all() as $item)
                                            <option value="{{ $item->id }}">{{ $item->item_name }} ({{ $item->current_qty }} available)</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="date" class="form-control" name="items[0][date_of_usage]" required min="{{ date('Y-m-d') }}">
                                </td>
                                <td>
                                    <input type="date" class="form-control" name="items[0][date_of_return]" required>
                                </td>
                                <td>
                                    <input type="time" class="form-control" name="items[0][time_of_return]" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="items[0][reserve_quantity]" min="1" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        `);
                        rowCount = 1;
                        $('#remainingTransaction').text(maxRows - rowCount);
                        $('.chosen-select').chosen('destroy').chosen({
                            width: '100%',
                            allow_single_deselect: true,
                            placeholder_text_single: 'Select an option'
                        });
                    }
                }],
            });
        });

        function update(id) {
            $.ajax({
                method: 'GET',
                url: `/transactions/${id}`,
                dataType: 'json',
                cache: false,
                success: function(response) {
                    transactionID = response.id;
                    $('#updateForm').find('select[id=item_id]').val(response.item_id).trigger('chosen:updated');
                    $('#updateForm').find('select[id=user_id]').val(response.user_id).trigger('chosen:updated');
                    $('#updateForm').find('input[id=reserve_quantity]').val(response.reserve_quantity);
                    $('#updateForm').find('input[id=approve_quantity]').val(response.approve_quantity);
                    $('#updateForm').find('input[id="date_of_usage"]').val(response.date_of_usage.substring(0,
                        10));
                    $('#updateForm').find('input[id="date_of_return"]').val(response.date_of_return.substring(0,
                        10));
                    $('#updateForm').find('input[id=time_of_return]').val(response.time_of_return);
                    $('#updateForm').find('select[id=status]').val(response.status).trigger('chosen:updated');
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

       function cancelTransaction(id) {
            Swal.fire({
                title: 'Are you sure you want to cancel this transaction?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it',
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
                        method: 'POST',
                        url: `/transactions/${id}/cancel`,
                        dataType: 'json',
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


        $('#transaction-cancel').on('click', function() {
            $('#transaction-table').show();
            $('#transaction-add').hide();
        });

        // Add new item row
        $('#addItemRow').on('click', function() {
            if (rowCount >= maxRows) {
                showErrorMessage('Maximum of 5 items can be added.');
                return;
            }

            const selectedItems = [];
            $('.item-select').each(function() {
                if ($(this).val()) {
                    selectedItems.push($(this).val());
                }
            });

            const newRow = `
                <tr class="item-row">
                    <td>
                        <select class="form-select chosen-select item-select" name="items[${rowCount}][item_id]" data-placeholder="Select Item" required>
                            <option value=""></option>
                            @foreach (\App\Models\Item::all() as $item)
                                <option value="{{ $item->id }}" ${selectedItems.includes('{{ $item->id }}') ? 'disabled' : ''}>
                                    {{ $item->item_name }} ({{ $item->current_qty }} available)
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="date" class="form-control" name="items[${rowCount}][date_of_usage]" required min="{{ date('Y-m-d') }}">
                    </td>
                    <td>
                        <input type="date" class="form-control" name="items[${rowCount}][date_of_return]" required>
                    </td>
                    <td>
                        <input type="time" class="form-control" name="items[${rowCount}][time_of_return]" required>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="items[${rowCount}][reserve_quantity]" min="1" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;

            $('#itemsTableBody').append(newRow);
            rowCount++;
            $('#remainingTransaction').text(maxRows - rowCount);
            updateRemoveButtons();
            $('.chosen-select').chosen('destroy').chosen({
                width: '100%',
                allow_single_deselect: true,
                placeholder_text_single: 'Select an option'
            });
        });

        // Remove item row
        $(document).on('click', '.remove-row', function() {
            if (rowCount > 1) {
                $(this).closest('tr').remove();
                rowCount--;
                $('#remainingTransaction').text(maxRows - rowCount);
                updateRemoveButtons();
                updateItemSelects();
            }
        });

        // Update remove buttons state
        function updateRemoveButtons() {
            $('.remove-row').prop('disabled', rowCount === 1);
        }

        // Update item select options to disable selected items
        $(document).on('change', '.item-select', function() {
            updateItemSelects();
            $(this).trigger('chosen:updated');
        });

        function updateItemSelects() {
            const selectedItems = [];
            $('.item-select').each(function() {
                if ($(this).val()) {
                    selectedItems.push($(this).val());
                }
            });

            $('.item-select').each(function() {
                const currentSelect = $(this);
                const currentValue = currentSelect.val();
                currentSelect.find('option').each(function() {
                    const optionValue = $(this).val();
                    if (optionValue && optionValue !== currentValue) {
                        $(this).prop('disabled', selectedItems.includes(optionValue));
                    }
                });
                currentSelect.trigger('chosen:updated');
            });
        }

        // Validate date_of_return to be after date_of_usage
        $(document).on('change', 'input[name$="[date_of_usage]"]', function() {
            const row = $(this).closest('tr');
            const dateOfUsage = $(this).val();
            const dateOfReturnInput = row.find('input[name$="[date_of_return]"]');
            if (dateOfUsage) {
                dateOfReturnInput.attr('min', dateOfUsage);
                if (dateOfReturnInput.val() <= dateOfUsage) {
                    dateOfReturnInput.val('');
                }
            }
        });

        // Form submission
        $('#addForm').submit(function(event) {
            event.preventDefault();
            $('#addForm').find('button[type=submit]').attr('disabled', true);

            if ($('#addForm').valid()) {
                Swal.fire({
                    title: 'Do you want to submit this transaction?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit it',
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
                            method: 'POST',
                            url: '/transactions',
                            data: $('#addForm').serialize(),
                            dataType: 'JSON',
                            cache: false,
                            success: function(response) {
                                if (response.valid) {
                                    $('#addForm').trigger('reset');
                                    $('#itemsTableBody').html(`
                                        <tr class="item-row">
                                            <td>
                                                <select class="form-select chosen-select item-select" name="items[0][item_id]" data-placeholder="Select Item" required>
                                                    <option value=""></option>
                                                    @foreach (\App\Models\Item::all() as $item)
                                                        <option value="{{ $item->id }}">{{ $item->item_name }} ({{ $item->current_qty }} available)</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="date" class="form-control" name="items[0][date_of_usage]" required min="{{ date('Y-m-d') }}">
                                            </td>
                                            <td>
                                                <input type="date" class="form-control" name="items[0][date_of_return]" required>
                                            </td>
                                            <td>
                                                <input type="time" class="form-control" name="items[0][time_of_return]" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" name="items[0][reserve_quantity]" min="1" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    `);
                                    rowCount = 1;
                                    $('.chosen-select').chosen('destroy').chosen({
                                        width: '100%',
                                        allow_single_deselect: true,
                                        placeholder_text_single: 'Select an option'
                                    });
                                    showSuccessMessage(response.msg);
                                    $('#transaction-table').show();
                                    $('#transaction-add').hide();
                                    table1.ajax.reload(null, false);
                                } else {
                                    showErrorMessage(response.msg);
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
                                    showErrorMessage(jqXHR.responseJSON?.msg ||
                                        "An unexpected error occurred. Please try again.");
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
                Swal.fire({
                    title: 'Do you want to save the changes?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, save it',
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
                            url: `/transactions/${transactionID}`,
                            data: $('#updateForm').serialize(),
                            dataType: 'JSON',
                            cache: false,
                            success: function(response) {
                                if (response.valid) {
                                    $('#updateForm').trigger('reset');
                                    $('.chosen-select').trigger('chosen:updated');
                                    showSuccessMessage(response.msg);
                                    $('#updateModal').modal('hide');
                                    table1.ajax.reload(null, false);
                                } else {
                                    showErrorMessage(response.msg);
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
                    }
                });
            }

            $('#updateForm').find('button[type=submit]').removeAttr('disabled');
        });
    </script>
@endsection
