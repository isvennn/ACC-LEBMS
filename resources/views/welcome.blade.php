@extends('layout.master')
@section('title')
    User List
@endsection
@section('app-title')
    Users Management
@endsection
@section('active-users')
    active
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <table id="table1" class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th data="count">#</th>
                        <th data="fullname">Name</th>
                        <th data="username">Username</th>
                        <th data="role">Role</th>
                        <th data="action" style="width: 2%">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {

        });
    </script>
@endsection
