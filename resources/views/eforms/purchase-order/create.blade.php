@extends('layouts.eforms.purchase-order.master')


@push('custom-styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
@endpush


@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">New Purchase Order Reinstatement Form</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('purchase.order.home')}}">Home</a></li>
                        <li class="breadcrumb-item active">New Purchase Order Reinstatement Form</li>
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
        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible">
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
            <form method="post" enctype="multipart/form-data" name="db1"
                  action="{{route('purchase.order.store')}}">
                @csrf
                <div class="card-body">

                    <table border="1" width="100%" cellspacing="0" cellpadding="0" align="Centre"
                           class="mt-2 mb-4">
                        <thead>
                        <tr style="border: 1px">
                            <th width="33%" class="text-center"><a href="#"><img
                                        src="{{ asset('dashboard/dist/img/zesco1.png')}}" title="ZESCO" alt="ZESCO"
                                        width="30%"></a></th>
                            <th colspan="4" class="text-center">New Purchase Order Reinstatement Form</th>
                            <th colspan="1">Doc Number:<br>CO.14900.FORM.0003<br>Version: 3</th>
                        </tr>
                        </thead>

                    </table>

                    <div class="row mt-2 mb-2">
                        <div class="col-3">
                            <div class="row">
                                <div class="col-10"><label>Employee Name:</label></div>
                                <div class="col-12"><input type="text" name="staff_name" value="{{Auth::user()->name}}"
                                                           readonly class="form-control"></div>
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="row">
                                <div class="col-6 "><label>Man No:</label></div>
                                <div class="col-12"><input type="text" name="staff_no" class="form-control"
                                                           value="{{Auth::user()->staff_no}}" readonly
                                                           required>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="row">
                                <div class="col-12"><label>Job Title:</label></div>
                                <div class="col-12"><input type="text" name="job_title" class="form-control"
                                                           value="{{Auth::user()->job_code}}" readonly
                                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="row">
                                <div class="col-12"><label>Cost Centre:</label></div>
                                <div class="col-12"><input type="text" name="cost_centre" class="form-control"
                                                           value="{{ $user->user_unit->user_unit_code}}" readonly
                                                           required>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row mt-2 mb-4">

                        <div class="col-3">
                            <div class="col-12"><label>Purchase Order No:</label></div>
                            <select id="po" class="form-control select2 " name="purchase_order_no"  >
                                <option value="" selected disabled >Select Purchase Order Number</option>
                                @foreach($po as $item)
                                    <option value="{{$item->document_no}}" >{{$item->document_no}}</option>
                                @endforeach
                            </select>

                            @error('po')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror

                        </div>


                    </div>


                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="table-responsive">
                            <div class="col-lg-12 ">
                                <table class="table bg-green">
                                    <thead>
                                    <tr>
                                        <th>REASON FOR REINSTATEMENT</th>
                                        <th>PURCHASE ORDER AMOUNT(ESTIMATE)</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-lg-12 ">
                                <TABLE class="table">
                                    <TR>
                                        <TD>
                                            <textarea rows="4" type="text" name="reason_for_reinstatement"
                                                      class="form-control amount"
                                                      placeholder="Enter Reason for Reinstatement" id="reason_for_reinstatement"
                                                      required></textarea>
                                        </TD>
                                        <TD><input type="number" id="amount1" name="amount" onchange="getvalues()"
                                                   class="form-control amount" placeholder="Amount [ZMW]">
                                        </TD>
                                    </TR>
                                </TABLE>
                            </div>

                            <div class="row mt-2 mb-4">
{{--                               <div class="col-3">--}}
{{--                                    <div class="row">--}}
{{--                                        <div class="col-12"><label>Purchase Order Amount:</label></div>--}}
{{--                                        <div class="col-12"><input type="number" id="estimated_cost" name="estimated_cost" class="form-control"--}}
{{--                                            value="" readonly>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                <div class="col-3">
                                    <div class="row">
                                        <div class="col-12"><label>PURCHASE ORDER AMOUNT(ACTUAL):</label></div>
                                        <div class="col-12"><input type="text" class="form-control text-bold" readonly id="total-payment"
                                                                   name="purchase_order_value" value="">
                                    </div>
                                </div>
                            </div>
{{--                                <div class="row">--}}
{{--                                    <div class="col-2 offset-4">--}}
{{--                                        <label class="form-control-label">Attach Quotation Files (optional)</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-6">--}}
{{--                                        <div class="input-group">--}}
{{--                                            <input type="file" class="form-control" multiple name="quotation[]"--}}
{{--                                                   id="receipt" title="Upload Quotation Files (Optional)">--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </div>

                            <div class="col-lg-12 mb-4">
                                <div class="row">
                                    <div class="col-2 offset-4">
                                        <label class="form-control-label">Attach Support Documents (Mandatory)</label>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <input type="file" class="form-control" multiple name="quotation[]"
                                                   id="receipt" title="Upload Quotation Files (Optional)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>

                      <div class="row mb-1 mt-4">
                            <div class="col-2"><label>Requested By (User Dept):</label> </div>
                            <div class="col-2"><input type="text" name="employee_staff_name" class="form-control"
                                       value="{{Auth::user()->name}}" readonly required></div>

                            <div class="col-2"><label>Job Title:</label></div>
                            <div class="col-2"><input type="text" name="employee_job_title"  class="form-control"
                                                        value="{{Auth::user()->job_code}}" readonly required></div>

                            <div class="col-1"><label>Signature:</label></div>
                            <div class="col-1"><input type="text" name="employee_staff_no" class="form-control"
                                                      value="{{Auth::user()->staff_no}}" readonly required></div>

                            <div class="col-1"><label>Date:</label></div>
                            <div class="col-1"><input type="Date" name="employee_claim_date" class="form-control"
                                                      value="{{date('Y-m-d')}}" readonly required>
                            </div>
                        </div>

                        <div class="row mb-1 mt-2">
                            <div class="col-2"><label>Checked By (User Dept):</label> </div>
                            <div class="col-2"><input type="text" name="checker_name" class="form-control"
                                                       readonly required></div>

                            <div class="col-2"><label>Job Title:</label></div>
                            <div class="col-2"><input type="text" name="checker_job_title"  class="form-control"
                                                       readonly required></div>

                            <div class="col-1"><label>Signature:</label></div>
                            <div class="col-1"><input type="text" name="checker_staff_no" class="form-control"
                                                       readonly required></div>

                            <div class="col-1"><label>Date:</label></div>
                            <div class="col-1"><input type="text" name="checker_date" class="form-control"
                                                      readonly >


                            </div>
                        </div>

                        <div class="row mb-1 mt-2">
                            <div class="col-2"><label>Approved By (User Dept):</label> </div>
                            <div class="col-2"><input type="text" name="approver_name" class="form-control"
                                                       readonly required></div>

                            <div class="col-2"><label>Job Title:</label></div>
                            <div class="col-2"><input type="text" name="approver_job_title"  class="form-control"
                                                      readonly required></div>

                            <div class="col-1"><label>Signature:</label></div>
                            <div class="col-1"><input type="text" name="approver_staff_no" class="form-control"
                                                       readonly required></div>

                            <div class="col-1"><label>Date:</label></div>
                            <div class="col-1"><input type="text" name="approver_date" class="form-control"
                                                       readonly >
                            </div>
                        </div>

                        <div class="row mb-1 mt-2">
                            <div class="col-2"><label>Reinstated By (Procurement):</label> </div>
                            <div class="col-2"><input type="text" name="reinstater_name" class="form-control"
                                                       readonly required></div>

                            <div class="col-2"><label>Job Title:</label></div>
                            <div class="col-2"><input type="text" name="reinstater_job_title"  class="form-control"
                                                       readonly required></div>

                            <div class="col-1"><label>Signature:</label></div>
                            <div class="col-1"><input type="text" name="reinstater_staff_no" class="form-control"
                                                     readonly required></div>

                            <div class="col-1"><label>Date:</label></div>
                            <div class="col-1"><input type="text" name="reinstater_date" class="form-control"
                                                       readonly>
                            </div>
                        </div>

                    </div>

                    <table border="0" width="100%" cellspacing="0" cellpadding="0" align="Centre">

                        <tr>
                            <td colspan="2"><p><b>Note:</b>Attach Scanned Copies and email to procurementmgrs@zesco.co.zm or<br>
                                    submit physical copies to the Manager reinstating the document.<br>
                                    </p></td>
                        </tr>

                    </table>


                </div>


                <div class="card-footer">
                    <div class="col-12 text-center">
                        <input class="btn btn-lg btn-success" type="submit"
                               value="submit"
                               name="submit_form" class="form-control"
                        >
                    </div>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection


@push('custom-scripts')
    <!--  -->
    <script type="text/javascript">


        function getvalues() {
            var inps = document.getElementById('amount1');

            var total = parseFloat(inps.value || 0);




            if (!isNaN(total)) {

                //check if petty cash is below 2000
                if (total > 2000) {
                    $('#submit_possible').hide();
                    $('#submit_not_possible').show();
                } else if (total == 0) {
                    $('#submit_not_possible').hide();
                    $('#submit_possible').hide();
                } else {
                    $('#submit_not_possible').hide();
                    $('#submit_possible').show();
                }
                //set value
                document.getElementById('total-payment').value = total;
                document.getElementById('estimated_cost').value = total;
            }
        }


        // Navigation Script Starts Here
        $(document).ready(function () {

            //first hide the buttons
            $('#submit_possible').hide();
            $('#submit_not_possible').hide();

        });

    </script>


@endpush
