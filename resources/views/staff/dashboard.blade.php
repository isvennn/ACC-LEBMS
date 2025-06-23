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
@section('content')
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>0<sup style="font-size: 20px"></sup></h3>
                    <p>No. Category</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tags"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>0<sup style="font-size: 20px"></sup></h3>
                    <p>No. Items</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>0<sup style="font-size: 20px"></sup></h3>
                    <p>No. Borrowers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>0<sup style="font-size: 20px"></sup></h3>
                    <p>No. Transaction</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <canvas id="conditionChart"></canvas>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {

        });
    </script>
@endsection
