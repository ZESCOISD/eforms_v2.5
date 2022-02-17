<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>eZesco | Subsistence</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('dashboard/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('dashboard/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dashboard/dist/css/adminlte.min.css')}}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <style>
        [data-title]:hover:after {
            opacity: 1;
            transition: all 0.1s ease 0.5s;
            visibility: visible;
        }
        [data-title]:after {
            content: attr(data-title);
            position: absolute;
            bottom: -1.6em;
            left: 100%;
            padding: 4px 4px 4px 8px;
            color: #666;
            white-space: nowrap;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            border-radius: 5px;
            -moz-box-shadow: 0px 0px 4px #666;
            -webkit-box-shadow: 0px 0px 4px #666;
            box-shadow: 0px 0px 4px #666;
            background-image: -moz-linear-gradient(top, #f0eded, #bfbdbd);
            background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #f0eded), color-stop(1, #bfbdbd));
            background-image: -webkit-linear-gradient(top, #f0eded, #bfbdbd);
            background-image: -moz-linear-gradient(top, #f0eded, #bfbdbd);
            background-image: -ms-linear-gradient(top, #f0eded, #bfbdbd);
            background-image: -o-linear-gradient(top, #f0eded, #bfbdbd);
            opacity: 0;
            z-index: 99999;
            visibility: hidden;
        }
        [data-title] {
            position: relative;
        }
    </style>

    @stack('custom-styles')

</head>
<body class="hold-transition sidebar-dark-secondary sidebar-mini sidebar-collapse layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

    <!-- Navbar -->
@include('layouts.eforms.subsistence.navbar')
<!-- /.navbar -->

    <!-- Main Sidebar Container -->
@include('layouts.eforms.subsistence.sidebar')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">


        <!-- Main content -->
    @yield('content')
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    @include('layouts.eforms.subsistence.footer')

</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="{{ asset('dashboard/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap -->
<script src="{{ asset('dashboard/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('dashboard/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dashboard/dist/js/adminlte.js')}}"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="{{ asset('dashboard/dist/js/demo.js')}}"></script>

<!-- PAGE PLUGINS -->
<!-- jQuery Mapael -->
<script src="{{ asset('dashboard/plugins/jquery-mousewheel/jquery.mousewheel.js')}}"></script>
<script src="{{ asset('dashboard/plugins/raphael/raphael.min.js')}}"></script>
<script src="{{ asset('dashboard/plugins/jquery-mapael/jquery.mapael.min.js')}}"></script>
<script src="{{ asset('dashboard/plugins/jquery-mapael/maps/usa_states.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{ asset('dashboard/plugins/chart.js/Chart.min.js')}}"></script>

<!-- PAGE SCRIPTS -->
<script src="{{ asset('dashboard/dist/js/pages/dashboard2.js')}}"></script>

@stack('custom-scripts')
</body>
</html>
