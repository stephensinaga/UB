@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Laporan Penjualan</h1>

        <!-- Data Table -->
         
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cashier</th>
                    <th>Customer</th>
                    <th>Grand Total</th>
                    <th>Payment Method</th>
                    <th>Cash</th>
                    <th>Changes</th>
                    <th>Status</th>
                    <th>Transfer Image</th>
                    <th>Order Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mainOrders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->cashier }}</td>
                    <td>{{ $order->customer }}</td>
                    <td>{{ number_format($order->grandtotal, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($order->payment) }}</td>
                    <td>{{ number_format($order->cash, 0, ',', '.') }}</td>
                    <td>{{ number_format($order->changes, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>
                        @if ($order->transfer_image)
                        <a href="{{ asset('storage/' . $order->transfer_image) }}" target="_blank">View Image</a>
                        @else
                        N/A
                        @endif
                    </td>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                    <td>
                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailModal" data-id="{{ $order->id }}">Detail</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Barang yang Dibeli</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Product Code</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody id="order-details">
                            <!-- Data akan dimasukkan melalui JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#detailModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var orderId = button.data('id');

                $('#order-details').empty();

                $.ajax({
                    url: '/admin/detail/pembelian/customer/' + orderId,
                    type: 'GET',
                    success: function(data) {
                        data.forEach(function(item) {
                            $('#order-details').append(`
                                <tr>
                                    <td>${item.product_name}</td>
                                    <td>${item.product_code}</td>
                                    <td>${item.product_category}</td>
                                    <td>${item.qty}</td>
                                    <td>${item.product_price}</td>
                                </tr>
                            `);
                        });
                    }
                });
            });
        });
    </script>
</body>