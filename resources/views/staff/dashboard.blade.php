@extends('layout.master')
@section('title')
    Dashboard
@endsection
@section('app-title')
    {{ auth()->user()->user_role }} Dashboard
@endsection
@section('active-dashboard')
    active
@endsection
@section('content')
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $categoryCount }}<sup style="font-size: 20px"></sup></h3>
                    <p>No. Categories</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tags"></i>
                </div>
                <a href="{{ route('categories.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $itemCount }}<sup style="font-size: 20px"></sup></h3>
                    <p>No. Items</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
                <a href="{{ route('items.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $borrowerCount }}<sup style="font-size: 20px"></sup></h3>
                    <p>No. Borrowers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <a href="{{ route('users.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $transactionCount }}<sup style="font-size: 20px"></sup></h3>
                    <p>No. Transactions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <a href="{{ route('transactions.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Item Condition Overview</h3>
                </div>
                <div class="card-body">
                    <canvas id="conditionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            // Item condition chart
            const ctx = document.getElementById('conditionChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Good', 'Lost', 'Damaged', 'For Repair', 'For Disposal'],
                    datasets: [{
                        label: 'Item Conditions',
                        data: [
                            {{ $conditionCounts['Good'] ?? 0 }},
                            {{ $conditionCounts['Lost'] ?? 0 }},
                            {{ $conditionCounts['Damaged'] ?? 0 }},
                            {{ $conditionCounts['For Repair'] ?? 0 }},
                            {{ $conditionCounts['For Disposal'] ?? 0 }}
                        ],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(153, 102, 255, 0.8)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(54, 162, 235, 1)',
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
                            text: 'Item Condition Distribution'
                        }
                    }
                }
            });
        });
    </script>
@endsection
