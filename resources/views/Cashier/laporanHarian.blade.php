@extends('layouts.app')

@section('contents')
    <!-- Main Content -->
        <h1 class="text-center mb-4">Sales Report /day</h1>

        <!-- Filter Form -->
        <div class="filter-section bg-light p-4 mb-4 rounded shadow-sm">
            <form method="GET" action="{{ route('HistoryPenjualanCashier') }}" class="mb-4">
                <div class="row">
                    <!-- Payment Method Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select class="form-control form-control-sm" id="payment_method" name="payment_method">
                                <option value="">All</option>
                                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash
                                </option>
                                <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>
                                    Transfer</option>
                            </select>
                        </div>
                    </div>

                    <!-- Customer Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer">Customer</label>
                            <select class="form-control form-control-sm" id="customer" name="customer">
                                <option value="">All</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer }}"
                                        {{ request('customer') == $customer ? 'selected' : '' }}>{{ $customer }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-sm form-control">Filter</button>
                        </div>
                    </div>

                    <!-- Download Button -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <a href="{{ route('ExportLaporanPenjualanHarian') }}"
                                class="btn btn-primary btn-sm form-control">Download Report</a>
                        </div>
                    </div>
                </div>
            </form>
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
                                    <a href="{{ asset($order->transfer_image) }}" target="_blank">View Image</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailModal"
                                    data-id="{{ $order->id }}">Detail</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Modal -->
            <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailModalLabel">Detail Product</h5>
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
        </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#detailModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var orderId = button.data('id');

                $('#order-details').empty();

                $.ajax({
                    url: '/cashier/detail/pembelian/customer/' + orderId,
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
@endsection
