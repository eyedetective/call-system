<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=@yield('viewport_scale',1)">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-token" content="{{ auth()->user() ? auth()->user()->api_token : '' }}">

    <title>{{ config('app.name', 'Laravel') }} | @yield('title', 'Admin Panel')</title>
    @yield('head_first')
    <link href="{{('/admin/dist/img/favicon.jpg')}}" rel="icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{('/admin/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{('/admin/dist/css/adminlte.min.css')}}">
    <style>
        .ui--overlay {
            position: fixed;
            z-index: 99999;
            background: rgba(17, 17, 17, 0.42);
            width: 100%;
            height: 100%;
            color: rgba(255, 255, 255, 0.8);
        }
        .ui--control-overlay{
            background-color: rgba(43, 43, 43, 0.397);bottom: 0;
            left: 0;
            position: fixed;
            right: 0;
            top: 0;
            z-index: 1070;
        }
    </style>
    @yield('head_last')
</head>

<body class="hold-transition sidebar-mini @yield('collapse_menu')">
    <div class="ui--loader" style="display:none">
        <div class="ui--overlay d-flex justify-content-center align-items-center">
            <i class="fas fa-3x fa-spinner fa-spin"></i>
        </div>
    </div>
    <div class="wrapper">
        @include('layouts.header')
        @include('layouts.sidebar')
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <span class="h3 m-0">@yield('show_page_name',__('page.'.Route::currentRouteName()))</span>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            @yield('breadcrumbs')
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="@yield('container_class','container-fluid')">
                    @yield('content')
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            @yield('footer')
        </footer>
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-light w-25 h-100" style="top:0;z-index:1072;overflow-y:auto;">
            <div class="p-3 control-sidebar-content h-100">
                @yield('control-sidebar')
            </div>
        </aside>
        <div class="ui--control-overlay" style="display: none"></div>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="{{('/admin/plugins/jquery/jquery.min.js')}}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{('/admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{('/admin/dist/js/adminlte.min.js')}}"></script>
</body>
@yield('afterbody')
</html>

