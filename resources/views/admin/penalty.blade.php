@extends('layout.master')
@section('title')
    Penalty List
@endsection
@section('app-title')
    Penalty Management
@endsection
@section('active-penalties')
    active
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <table id="penaltyTable" class="table table-bordered table-striped mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Borrower Name</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Remarks</th>
                    <th>Laboratory</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#penaltyTable').DataTable({
            ajax: {
                url: '{{ route('penalties.data') }}',
                dataSrc: ''
            },
            columns: [
                { data: 'count' },
                { data: 'borrower_name' },
                { data: 'item_name' },
                { data: 'quantity' },
                { data: 'status' },
                { data: 'amount' },
                { data: 'remarks' },
                { data: 'laboratory' },
            ],
            responsive: true
        });
    });
</script>
@endsection
