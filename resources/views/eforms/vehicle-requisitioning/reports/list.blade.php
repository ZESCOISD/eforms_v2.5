@extends('layouts.eforms.vehicle-requisitioning.master')


@push('custom-styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('dashboard/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('dashboard/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
@endpush


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Vehicle-Requisition: {{$category}}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('main-home')}}">Home</a></li>
                        <li class="breadcrumb-item active">Vehicle-Requisition: {{$category}}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


    <!-- Main page content -->
    <section class="content">


        @if(session()->has('message'))
            <div class="alert alert-success alert-dismissible">
                <p class="lead"> {{session()->get('message')}}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
    @endif

    <!-- Default box -->
        <div class="card">
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example3" class="table m-0">
                        <thead>
                        <tr>
                            <th>Serial</th>
                            <th>Claimant</th>
                            <th>Estimated Cost</th>
                            <th>Destination</th>
                            <th>Status</th>
                            <th>Period</th>
                            <th>View</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach( $list as $item )
                            <tr>
                                <td><a href="{{ route('logout') }}" class="dropdown-item"
                                       onclick="event.preventDefault();
                                           document.getElementById('show-form'+{{$item->id}}).submit();"> {{$item->code}}</a>
                                    <form id="show-form{{$item->id}}"
                                          action="{{ route('vehicle.requisitioning.show', $item->id) }}"
                                          method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </td>
                                <td>{{$item->staff_name}}</td>
                                <td>{{$item->amount}}</td>
                                <td>{{$item->destination}}</td>
                                <td><span
                                        class="badge badge-{{$item->status->html}}">{{$item->status->name}}</span>
                                </td>
                                <td>{{$item->created_at->diffForHumans()}}</td>
                                <td><a href="{{ route('logout') }}" class="btn btn-sm bg-orange"
                                       onclick="event.preventDefault();
                                           document.getElementById('show-form'+{{$item->id}}).submit();"> view</a>
                                    <form id="show-form{{$item->id}}"
                                          action="{{ route('vehicle.requisitioning.show', $item->id) }}"
                                          method="POST" class="d-none">
                                        @csrf
                                    </form></td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>

                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                @if( Auth::user()->profile_id ==  config('constants.user_profiles.EZESCO_002'))
                    @if($pending < 1)
                        <a href="{{route('vehicle.requisitioning.create')}}"
                           class="btn btn-sm bg-gradient-green float-left">New Vehicle Requisition</a>
                    @else
                        <a href="#" class="btn btn-sm btn-default float-left">New Vehicle Requisition</a>
                        <span class="text-danger m-3"> Sorry, You can not raise a new Vehicle Requisition because you already have an open Vehicle Requisition.</span>
                    @endif
                @endif
                    {!! $list->links() !!}
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->


@endsection


@push('custom-scripts')

    <!-- DataTables -->
    <script src="{{ asset('dashboard/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('dashboard/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('dashboard/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('dashboard/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>

    <!-- page script -->
    <script>
        $(function () {
            $("#example1").DataTable({
                "responsive": true,
                "autoWidth": false,
            });
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>


@endpush
