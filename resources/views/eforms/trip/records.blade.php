@extends('layouts.eforms.petty-cash.master')


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
                    <h1 class="m-0 text-dark">Petty-Cash : {{$category}}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('main-home')}}">Home</a></li>
                        <li class="breadcrumb-item active">Petty-Cash  : {{$category}}</li>
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
                    <table id="example1" class="table m-0">
                        <thead>
                        <tr>
                            <th>Serial</th>
                            <th>Claimant</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Period</th>
                            <th>View</th>
                            <th>Mark Void</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach( $list as $item )
                            <tr>
                                <td><a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                                        document.getElementById('show-form'+{{$item->id}}).submit();"> {{$item->code}}</a>
                                    <form id="show-form{{$item->id}}" action="{{ route('petty-cash-show', $item->id) }}"
                                          method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </td>
                                <td>{{$item->claimant_name}}</td>
                                <td>{{$item->total_payment}}</td>
                                <td><span class="badge badge-{{$item->status->html ?? "default"}}">{{$item->status->name ?? "none"}}</span>
                                </td>
                                <td>{{$item->updated_at->diffForHumans()}}</td>
                                <td><a href="{{ route('logout') }}" class="btn btn-sm bg-orange" onclick="event.preventDefault();
                                        document.getElementById('show-form'+{{$item->id}}).submit();"> View </a>
                                    <form id="show-form{{$item->id}}" action="{{ route('petty-cash-show', $item->id) }}"
                                          method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </td>
                                <td>
                                <button class="btn btn-sm bg-gradient-gray float-left" style="margin: 1px"
                                        title="Mark as Void."
                                        data-toggle="modal"
                                        data-target="#modal-delete{{$item->id}}">
                                    <i class="fa fa-trash"></i>
                                </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">

            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->


    @foreach($list as $item)
        <!-- DELETE MODAL-->
        <div class="modal fade" id="modal-delete{{$item->id}}">
            <div class="modal-dialog modal-md">
                <div class="modal-content bg-defualt">
                    <div class="modal-header">
                        <h4 class="modal-title text-center">Mark Voucher as Void</h4>
                    </div>
                    <!-- form start -->
                    <form role="form" method="post"
                          action="{{route('petty-cash-void', ['id' => $item->id])}}">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <p class="text-center">Are you sure you want to mark this form as void? </p>
                                    <p class="text-center">Note that you can not undo this action. </p>
                                      </div>

                                <div class="col-2">
                                    <label>Reason</label>
                                </div>
                                <div class="col-10">
                                    <div class="input-group">
                                        <textarea class="form-control" rows="2" name="reason"
                                                  placeholder="Enter reason why" required>
                                        </textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Mark</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.DELETE modal -->
    @endforeach


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
