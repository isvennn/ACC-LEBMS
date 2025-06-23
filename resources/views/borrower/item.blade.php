@extends('layout.master')
@section('title')
    Item List
@endsection
@section('app-title')
    Item Management
@endsection
@section('active-items')
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
                        <th>Category</th>
                        <th>Laboratory</th>
                        <th>Current Qty</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
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
                    data: function(d) {
                        d.laboratory = $('#laboratoryFilter').val();
                    }
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
                        data: 'category'
                    },
                    {
                        data: 'laboratory'
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
                ],
                dom: '<"d-flex justify-content-between align-items-center"<"search-box"f><"custom-action"B>>rtip',
                buttons: [{
                    text: '<select id="laboratoryFilter" class="form-control form-control-sm" style="width: 200px; display: inline-block;"><option value="">All Laboratories</option>@foreach (\App\Models\Laboratory::all() as $laboratory)<option value="{{ $laboratory->id }}">{{ $laboratory->name }}</option>@endforeach</select>',
                    className: 'laboratory-filter',
                    action: function(e, dt, node, config) {
                        // No action needed here; change event is handled below
                    }
                }],
                initComplete: function() {
                    // Attach change event to laboratory filter after DataTable initialization
                    $('#laboratoryFilter').on('change', function() {
                        table1.ajax.reload();
                    });
                }
            });
        });
    </script>
@endsection
