@extends('layout.master')
@section('title')
    Report Filters
@endsection
@section('app-title')
    Generate Reports
@endsection
@section('active-reports')
    active
@endsection
@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Report Filters</h3>
        </div>
        <div class="card-body">
            <form id="reportFilterForm">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label for="report_type">Report Type</label>
                        <select id="report_type" name="report_type" class="form-control chosen-select" required>
                            <option value="">Select Report</option>
                            <option value="stock_summary">Item Stock Summary</option>
                            <option value="transaction_history">Transaction History</option>
                            <option value="penalty_summary">Penalty Summary</option>
                            <option value="overdue_transactions">Overdue Transactions</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="laboratory_id">Laboratory</label>
                        <select id="laboratory_id" name="laboratory_id" class="form-control chosen-select">
                            <option value="">All Laboratories</option>
                            @foreach ($laboratories as $laboratory)
                                <option value="{{ $laboratory->id }}">{{ $laboratory->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="user_id">User</label>
                        <select id="user_id" name="user_id" class="form-control chosen-select">
                            <option value="">All Users</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="status">Status (Transaction Only)</label>
                        <select id="status" name="status" class="form-control chosen-select">
                            <option value="">All Statuses</option>
                            <option value="Pending">Pending</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Released">Released</option>
                            <option value="Returned">Returned</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="category_type">Category Type (Stock Only)</label>
                        <select id="category_type" name="category_type" class="form-control chosen-select">
                            <option value="">All Types</option>
                            <option value="Tools">Tools</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Apparatus">Apparatus</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i>
                            Generate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Report Results</h3>
        </div>
        <div class="card-body">
            <table id="reportTable" class="table table-bordered table-striped">
                <thead>
                    <tr id="tableHeaders"></tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var table;
        $(document).ready(function() {
            $('.chosen-select').chosen({
                width: '100%',
                allow_single_deselect: true,
                placeholder_text_single: 'Select an option'
            });

            // let table = $('#reportTable').DataTable({
            //     "paging": true,
            //     "lengthChange": false,
            //     "searching": true,
            //     "ordering": true,
            //     "info": true,
            //     "autoWidth": true,
            //     "responsive": true,
            // });

            $('#reportFilterForm').on('submit', function(e) {
                e.preventDefault();
                let reportType = $('#report_type').val();
                if (!reportType) {
                    showErrorMessage('Please select a report type.');
                    return;
                }

                $.ajax({
                    url: `/reports/${reportType}`,
                    method: 'GET',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        let data = response.data;
                        let columns = [];

                        if (reportType === 'stock_summary') {
                            columns = [{
                                    data: 'laboratory_name',
                                    title: 'Laboratory'
                                },
                                {
                                    data: 'category_name',
                                    title: 'Category'
                                },
                                {
                                    data: 'item_name',
                                    title: 'Item Name'
                                },
                                {
                                    data: 'item_description',
                                    title: 'Description'
                                },
                                {
                                    data: 'beginning_qty',
                                    title: 'Beginning Qty'
                                },
                                {
                                    data: 'current_qty',
                                    title: 'Current Qty'
                                },
                                {
                                    data: 'item_price',
                                    title: 'Price'
                                },
                            ];
                            if (Array.isArray(data)) {
                                data = data.reduce((acc, item) => {
                                    acc[item.laboratory_name] = acc[item
                                        .laboratory_name] || [];
                                    acc[item.laboratory_name].push(item);
                                    return acc;
                                }, {});
                            }
                            let flatData = [];
                            for (let lab in data) {
                                data[lab].forEach(item => flatData.push({
                                    ...item,
                                    laboratory_name: lab
                                }));
                            }
                            data = flatData;
                        } else if (reportType === 'transaction_history') {
                            columns = [{
                                    data: 'transaction_no',
                                    title: 'Transaction No'
                                },
                                {
                                    data: 'item_name',
                                    title: 'Item'
                                },
                                {
                                    data: null,
                                    title: 'User',
                                    render: function(data) {
                                        return data.user ? data.user.full_name : 'N/A';
                                    }
                                },
                                {
                                    data: 'reserve_quantity',
                                    title: 'Reserve Qty'
                                },
                                {
                                    data: 'approve_quantity',
                                    title: 'Approve Qty'
                                },
                                {
                                    data: 'date_of_usage',
                                    title: 'Date of Usage'
                                },
                                {
                                    data: 'date_of_return',
                                    title: 'Date of Return'
                                },
                                {
                                    data: 'time_of_return',
                                    title: 'Time of Return'
                                },
                                {
                                    data: 'status',
                                    title: 'Status'
                                },
                            ];
                        } else if (reportType === 'penalty_summary') {
                            columns = [{
                                    data: 'transaction_no',
                                    title: 'Transaction No'
                                },
                                {
                                    data: 'item_name',
                                    title: 'Item'
                                },
                                {
                                    data: null,
                                    title: 'User',
                                    render: function(data) {
                                        return data.user ? data.user.full_name : 'N/A';
                                    }
                                },
                                {
                                    data: 'quantity',
                                    title: 'Quantity'
                                },
                                {
                                    data: 'amount',
                                    title: 'Amount'
                                },
                                {
                                    data: 'status',
                                    title: 'Status'
                                },
                                {
                                    data: 'remarks',
                                    title: 'Remarks'
                                },
                            ];
                        } else if (reportType === 'overdue_transactions') {
                            columns = [{
                                    data: 'transaction_no',
                                    title: 'Transaction No'
                                },
                                {
                                    data: 'item_name',
                                    title: 'Item'
                                },
                                {
                                    data: null,
                                    title: 'User',
                                    render: function(data) {
                                        return data.user ? data.user.full_name : 'N/A';
                                    }
                                },
                                {
                                    data: 'reserve_quantity',
                                    title: 'Reserve Qty'
                                },
                                {
                                    data: 'date_of_return',
                                    title: 'Date of Return'
                                },
                                {
                                    data: 'status',
                                    title: 'Status'
                                },
                            ];
                        }

                        if ($.fn.DataTable.isDataTable('#reportTable')) {
                            $('#reportTable').DataTable().clear().destroy();
                            $('#reportTable').empty();
                        }

                        table = $('#reportTable').DataTable({
                            data: data,
                            columns: columns,
                            paging: true,
                            searching: true,
                            ordering: true,
                            info: true,
                            autoWidth: true,
                            responsive: true,
                            // dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',

                            dom: '<"d-flex justify-content-between align-items-center"<"search-box"f><"custom-button"B>>rtip',
                            buttons: [{
                                extend: "print",
                                text: '<i class="fa fa-print"></i> Print',
                                className: "btn btn-info btn-md",
                                titleAttr: "Print data",
                                title: '',
                                customize: function(win) {
                                    $(win.document.body).prepend(`
                                            <div class="container">
                                                <div class="header">
                                                    <div class="logo">
                                                        <img src="{{ asset('dist/img/acclogo.png') }}" style="height: 150px; width: 150px">
                                                    </div>
                                                    <div class="title">
                                                        <h3>Abuyog Community College</h3>
                                                        <h3>Laboratory</h3>
                                                        <h3>Equipment Borrowing and Management System</h3>
                                                        <h3>Report List</h3>
                                                    </div>
                                                    <div class="logo">
                                                        <img src="{{ asset('dist/img/acclogo.png') }}" style="height: 150px; width: 150px">
                                                    </div>
                                                </div>
                                            </div>
                                        `);
                                    $(win.document.head).append(`
                                            <style type="text/css" media="print">
                                                @page { size: auto; margin: 25px; }
                                            </style>
                                            <style type="text/css" media="all">
                                                .container { display: flex; flex-direction: column; align-items: center; }
                                                .header { display: flex; justify-content: space-between; width: 100%; padding: 10px; }
                                                .title { text-align: center; }
                                                .content { display: flex; justify-content: space-between; padding: 20px; }
                                                table { width: 100%; margin-top: 20px; border-collapse: collapse; }
                                                table th, table td { padding: 8px; text-align: center; color: black; vertical-align: middle; box-sizing: border-box; border: 1px solid black; }
                                            </style>
                                        `);
                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');
                                }
                            }]
                        });

                        $('#exportExcel').prop('disabled', false);
                    },
                    error: function(jqXHR) {
                        showErrorMessage(jqXHR.responseJSON?.message ||
                            'Failed to generate report.');
                    }
                });
            });

            $('#exportExcel').on('click', function() {
                let reportType = $('#report_type').val();
                if (!reportType) {
                    showErrorMessage('Please select a report type.');
                    return;
                }
                let queryString = $('#reportFilterForm').serialize() + '&export=excel';
                window.location.href = `/reports/${reportType}?${queryString}`;
            });
        });
    </script>
@endsection
