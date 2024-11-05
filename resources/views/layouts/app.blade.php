<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dapur Negeriku</title>

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">


    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/img/lgobbn.png')}}" type="image/png">
    <style>
        body {
            padding-top: 70px;
            margin: 0;
        }

        .container-fluid {
            display: flex;
            flex-wrap: nowrap;
        }

        .sidebar {
            width: 220px;
            /* Lebar sidebar dikurangi */
            height: 100vh;
            background-color: #f8f9fa;
            position: fixed;
            top: 70px;
            /* Sesuaikan dengan tinggi navbar */
            left: 0;
            overflow-y: auto;
            padding: 20px;
            z-index: 1;
        }

        .content-wrapper {
            margin-left: 220px;
            /* Sesuaikan dengan lebar sidebar */
            padding: 20px;
            width: calc(100% - 220px);
            /* Hitung ulang lebar konten setelah sidebar */
            background-color: #fff;
            min-height: calc(100vh - 70px);
            /* Pastikan konten menutupi seluruh viewport */
        }

        /* Responsive untuk layar kecil */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                /* Sidebar lebih kecil pada layar kecil */
            }

            .content-wrapper {
                margin-left: 80px;
                /* Margin konten disesuaikan */
                width: calc(100% - 80px);
                /* Lebar konten disesuaikan */
            }
        }
    </style>
</head>

<body>

    <!-- ======= Header ======= -->
    @include('layouts.navbar')
    <!-- End Header -->

    <div class="container-fluid">
        <!-- ======= Sidebar ======= -->
        @include('layouts.sidebar')
        <!-- End Sidebar -->
        <div class="container">
             @yield('contents')
        </div>
    </div>

    <!-- ======= Footer ======= -->
    @include('layouts.footer')
    <!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>
