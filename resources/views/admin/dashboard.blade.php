@extends('layout.master')
@section('title')
    Dashboard
@endsection
@section('app-title')
    Admin Dashboard
@endsection
@section('active-dashboard')
    active
@endsection
@section('css')
    <style>
        .small-box {
            transition: transform 0.3s;
        }

        .small-box:hover {
            transform: translateY(-5px);
        }

        .card {
            border-radius: 0.5rem;
        }

        .chosen-container {
            width: 100% !important;
        }

        .chosen-container .chosen-single {
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            background: #fff;
        }

        .chosen-container .chosen-drop {
            border-color: #ced4da;
        }
    </style>
@endsection
@section('content')
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="form-group">
                <label for="laboratory_id">Filter by Laboratory</label>
                <select id="laboratory_id" name="laboratory_id" class="form-control chosen-select">
                    <option value="">All Laboratories</option>
                    @foreach ($laboratories as $laboratory)
                        <option value="{{ $laboratory->id }}">{{ $laboratory->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 class="count-laboratory">{{ $laboratoryCount }}</h3>
                    <p>No. Laboratories</p>
                </div>
                <div class="icon">
                    <i class="fas fa-flask"></i>
                </div>
                <a href="{{ route('viewAdminLaboratory') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 class="count-category">{{ $categoryCount }}</h3>
                    <p>No. Categories</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tags"></i>
                </div>
                <a href="{{ route('viewAdminCategory') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 class="count-item">{{ $itemCount }}</h3>
                    <p>No. Items</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
                <a href="{{ route('viewAdminItem') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 class="count-borrower">{{ $borrowerCount }}</h3>
                    <p>No. Borrowers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <a href="{{ route('viewAdminBorrower') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 class="count-employee">{{ $employeeCount }}</h3>
                    <p>No. Employees</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <a href="{{ route('viewAdminEmployee') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 class="count-transaction">{{ $transactionCount }}</h3>
                    <p>No. Transactions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <a href="{{ route('viewAdminTransaction') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-lg-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Transaction Statuses</h3>
                </div>
                <div class="card-body">
                    <canvas id="transactionStatusChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Stock Summary by Laboratory</h3>
                </div>
                <div class="card-body">
                    <canvas id="stockSummaryChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Category Type Distribution</h3>
                </div>
                <div class="card-body">
                    <canvas id="categoryTypeChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Borrower Activity (Top 5)</h3>
                </div>
                <div class="card-body">
                    <canvas id="borrowerActivityChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Penalty Trends</h3>
                </div>
                <div class="card-body">
                    <canvas id="penaltyTrendsChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Overdue Transactions by Laboratory</h3>
                </div>
                <div class="card-body">
                    <canvas id="overdueTransactionsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div> -->
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Chosen
            $('.chosen-select').chosen({
                width: '100%',
                allow_single_deselect: true,
                placeholder_text_single: 'Select a Laboratory'
            });

            // Transaction Statuses Chart
            let transactionStatusChart = new Chart(document.getElementById('transactionStatusChart'), {
                type: 'pie',
                data: {
                    labels: ['Pending', 'Confirmed', 'Released', 'Returned', 'Rejected', 'Cancelled'],
                    datasets: [{
                        data: [
                            {{ $transactionStatuses['Pending'] ?? 0 }},
                            {{ $transactionStatuses['Confirmed'] ?? 0 }},
                            {{ $transactionStatuses['Released'] ?? 0 }},
                            {{ $transactionStatuses['Returned'] ?? 0 }},
                            {{ $transactionStatuses['Rejected'] ?? 0 }},
                            {{ $transactionStatuses['Cancelled'] ?? 0 }}
                        ],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#F7464A',
                            '#D3D3D3'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                },
                                color: '#333'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Transaction Status Distribution',
                            font: {
                                size: 16
                            },
                            color: '#333'
                        }
                    }
                }
            });

            // Stock Summary Chart
            let stockSummaryChart = new Chart(document.getElementById('stockSummaryChart'), {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($stockSummary as $lab)
                            '{{ $lab->laboratory_name }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Current Quantity',
                        data: [
                            @foreach ($stockSummary as $lab)
                                {{ $lab->total_qty }},
                            @endforeach
                        ],
                        backgroundColor: '#36A2EB',
                        borderColor: '#2b8bce',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Item Stock by Laboratory',
                            font: {
                                size: 16
                            },
                            color: '#333'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Quantity',
                                font: {
                                    size: 14
                                }
                            },
                            ticks: {
                                color: '#333'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#333'
                            }
                        }
                    }
                }
            });

            // Category Type Chart
            let categoryTypeChart = new Chart(document.getElementById('categoryTypeChart'), {
                type: 'pie',
                data: {
                    labels: ['Tools', 'Equipment', 'Apparatus'],
                    datasets: [{
                        data: [
                            {{ $categoryTypes['Tools'] ?? 0 }},
                            {{ $categoryTypes['Equipment'] ?? 0 }},
                            {{ $categoryTypes['Apparatus'] ?? 0 }}
                        ],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                },
                                color: '#333'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Items by Category Type',
                            font: {
                                size: 16
                            },
                            color: '#333'
                        }
                    }
                }
            });

            // Borrower Activity Chart
            let borrowerActivityChart = new Chart(document.getElementById('borrowerActivityChart'), {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($borrowerActivity as $borrower)
                            '{{ $borrower['full_name'] }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Transactions',
                        data: [
                            @foreach ($borrowerActivity as $borrower)
                                {{ $borrower['count'] }},
                            @endforeach
                        ],
                        backgroundColor: '#4BC0C0',
                        borderColor: '#3aa8a8',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Top Borrowers by Transactions',
                            font: {
                                size: 16
                            },
                            color: '#333'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Transactions',
                                font: {
                                    size: 14
                                }
                            },
                            ticks: {
                                color: '#333'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#333',
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });

            // Penalty Trends Chart
            let penaltyTrendsChart = new Chart(document.getElementById('penaltyTrendsChart'), {
                type: 'line',
                data: {
                    labels: [<?php echo implode(
                        ',',
                        array_map(function ($key) {
                            return "'$key'";
                        }, array_keys($penaltyTrends)),
                    ); ?>],
                    datasets: [{
                        label: 'Penalty Amount',
                        data: [<?php echo implode(',', array_values($penaltyTrends)); ?>],
                        borderColor: '#FF6384',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                },
                                color: '#333'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Penalty Trends Over Time',
                            font: {
                                size: 16
                            },
                            color: '#333'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Amount',
                                font: {
                                    size: 14
                                }
                            },
                            ticks: {
                                color: '#333'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#333'
                            }
                        }
                    }
                }
            });

            // Overdue Transactions Chart
            let overdueTransactionsChart = new Chart(document.getElementById('overdueTransactionsChart'), {
                type: 'bar',
                data: {
                    labels: [<?php echo implode(
                        ',',
                        array_map(function ($key) {
                            return "'$key'";
                        }, array_keys($overdueTransactions)),
                    ); ?>],
                    datasets: [{
                        label: 'Overdue Transactions',
                        data: [<?php echo implode(',', array_values($overdueTransactions)); ?>],
                        backgroundColor: '#F7464A',
                        borderColor: '#d63c3f',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Overdue Transactions by Laboratory',
                            font: {
                                size: 16
                            },
                            color: '#333'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count',
                                font: {
                                    size: 14
                                }
                            },
                            ticks: {
                                color: '#333'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#333'
                            }
                        }
                    }
                }
            });

            // Laboratory Filter
            $('#laboratory_id').on('change', function() {
                let laboratoryId = $(this).val();
                $.ajax({
                    url: '{{ route('viewAdminDashboard') }}',
                    method: 'GET',
                    data: {
                        laboratory_id: laboratoryId
                    },
                    success: function(response) {
                        // Update Transaction Statuses
                        transactionStatusChart.data.datasets[0].data = [
                            response.transactionStatuses['Pending'] || 0,
                            response.transactionStatuses['Confirmed'] || 0,
                            response.transactionStatuses['Released'] || 0,
                            response.transactionStatuses['Returned'] || 0,
                            response.transactionStatuses['Rejected'] || 0,
                            response.transactionStatuses['Cancelled'] || 0
                        ];
                        transactionStatusChart.update();

                        // Update Stock Summary
                        stockSummaryChart.data.labels = response.stockSummary.map(item => item
                            .laboratory_name);
                        stockSummaryChart.data.datasets[0].data = response.stockSummary.map(
                            item => item.total_qty);
                        stockSummaryChart.update();

                        // Update Category Types
                        categoryTypeChart.data.datasets[0].data = [
                            response.categoryTypes['Tools'] || 0,
                            response.categoryTypes['Equipment'] || 0,
                            response.categoryTypes['Apparatus'] || 0
                        ];
                        categoryTypeChart.update();

                        // Update Borrower Activity
                        borrowerActivityChart.data.labels = response.borrowerActivity.map(
                            item => item.full_name);
                        borrowerActivityChart.data.datasets[0].data = response.borrowerActivity
                            .map(item => item.count);
                        borrowerActivityChart.update();

                        // Update Penalty Trends
                        penaltyTrendsChart.data.labels = Object.keys(response.penaltyTrends);
                        penaltyTrendsChart.data.datasets[0].data = Object.values(response
                            .penaltyTrends);
                        penaltyTrendsChart.update();

                        // Update Overdue Transactions
                        overdueTransactionsChart.data.labels = Object.keys(response
                            .overdueTransactions);
                        overdueTransactionsChart.data.datasets[0].data = Object.values(response
                            .overdueTransactions);
                        overdueTransactionsChart.update();

                        // Update Inventory Movement
                        inventoryMovementChart.data.labels = Object.keys(response.additions);
                        inventoryMovementChart.data.datasets[0].data = Object.values(response
                            .additions);
                        inventoryMovementChart.data.datasets[1].data = Object.values(response
                            .deductions);
                        inventoryMovementChart.update();

                        // Update Small-Box Counts
                        $('.count-laboratory').text(response.laboratoryCount);
                        $('.count-category').text(response.categoryCount);
                        $('.count-item').text(response.itemCount);
                        $('.count-borrower').text(response.borrowerCount);
                        $('.count-employee').text(response.employeeCount);
                        $('.count-transaction').text(response.transactionCount);
                    },
                    error: function() {
                        showErrorMessage('Failed to update dashboard.');
                    }
                });
            });
        });
    </script>
@endsection
