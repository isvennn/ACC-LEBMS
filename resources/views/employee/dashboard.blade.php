@extends('layout.master')
@section('title')
    Employee Dashboard
@endsection
@section('app-title')
    Employee Dashboard
@endsection
@section('active-dashboard')
    active
@endsection
@section('content')
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $activeTransactionCount }}<sup style="font-size: 20px"></sup></h3>
                    <p>Active Transactions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <a href="{{ route('viewEmployeeTransaction') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingTransactionCount }}<sup style="font-size: 20px"></sup></h3>
                    <p>Pending Transactions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <a href="{{ route('viewEmployeeTransaction') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $returnedItemCount }}<sup style="font-size: 20px"></sup></h3>
                    <p>Items Returned</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('viewEmployeeTransaction') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $overdueItemCount }}<sup style="font-size: 20px"></sup></h3>
                    <p>Overdue Items</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('viewEmployeeTransaction') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $penaltyCount }}<sup style="font-size: 20px"></sup></h3>
                    <p>Penalties</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('viewEmployeeTransaction') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction Status Overview</h3>
                </div>
                <div class="card-body">
                    <canvas id="transactionStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            // Transaction status chart
            const ctx = document.getElementById('transactionStatusChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Pending', 'Confirmed', 'Released', 'Returned', 'Rejected', 'Cancelled'],
                    datasets: [{
                        label: 'Transaction Statuses',
                        data: [
                            {{ $statusCounts['Pending'] ?? 0 }},
                            {{ $statusCounts['Confirmed'] ?? 0 }},
                            {{ $statusCounts['Released'] ?? 0 }},
                            {{ $statusCounts['Returned'] ?? 0 }},
                            {{ $statusCounts['Rejected'] ?? 0 }},
                            {{ $statusCounts['Cancelled'] ?? 0 }}
                        ],
                        backgroundColor: [
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(153, 102, 255, 0.8)'
                        ],
                        borderColor: [
                            'rgba(255, 206, 86, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(40, 167, 69, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Transaction Status Distribution'
                        }
                    }
                }
            });
        });
    </script>
@endsection
