@extends('layouts.app')

@section('contents')
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('Dashboard')}}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-8">
                <div class="row">

                    <!-- Sales Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card sales-card">

                            <div class="card-body">
                                <h5 class="card-title"><a href="{{route('HistoryPenjualanCashier')}}" style="text-decoration: none;">Sales <span>| Today</span></a></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-cart"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{$penjualan}}</h6>
                                        <span class="text-success small pt-1 fw-bold">{{$subPenjualan}}</span> <span class="text-muted small pt-2 ps-1">Product Terjual</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><!-- End Sales Card -->

                    <!-- Customer Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card customer-card">

                            <div class="card-body">
                                <h5 class="card-title">Customers <span>| Today</span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{$customer}}</h6>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><!-- End Customer Card -->

                </div>
            </div>

    </section>
@endsection
