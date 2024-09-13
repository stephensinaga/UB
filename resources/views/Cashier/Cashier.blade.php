@extends('layouts.app')

@section('contents')
<main id="main" class="main">
    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-8">
            <div class="row">

                {{-- card --}}
                <div class="row">
                    @foreach($product as $index => $item)
                    <div class="col-xxl-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="card-icon d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                                        @if($item->product_images)
                                        <img src="{{ asset('storage/' . $item->product_images) }}" alt="Product Image" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                        <i class="bi bi-cart" style="font-size: 3rem;"></i>
                                        @endif
                                    </div>
                                    <div class="ps-3" style="flex-grow: 1;">
                                        <h4 style="font-size:15px" class="card-title">{{ $item->product_name }}</h4>
                                        <h6 style="font-size: 20px;">Rp{{ number_format($item->product_price, 2) }}</h6>
                                        <span class="text-muted small pt-2 ps-1">{{ $item->product_code }}</span>

                                        <form action="javascript:void(0)" method="post" id="OrderProduct" data-id="{{ $item->id }}">
                                            @csrf
                                            <button type="submit" class="btn btn-primary mt-2">
                                                <i class="bi bi-plus"></i> Order
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            </div><!-- End Left side columns -->

            <!-- Right side columns -->
            <div class="col-lg-4">

            <!-- Recent Activity -->
                <div class="card">
                    <div class="card shadow-sm p-4">
                        <h2 class="mb-4">Checkout List</h2>
                        <form action="javascript:void(0)" method="POST" id="CheckOutTable">
                            @csrf
                            @method('POST')
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Product Price</th>
                                        <th>Product Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingProduct as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ number_format($item->product_price, 2) }}</td>
                                        <td>{{ $item->order_qty }}
                                            <form action="javascript:void(0)" method="delete" data-id="{{ $item->id }}"
                                                id="DeletePendingOrder">
                                                @csrf
                                                @method('delete')
                                                <button type="submit"><i class="fa-solid fa-minus"></i></button>
                                            </form>
                                        </td>
                                        <td>{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td colspan="3" class="text-right">Total Price:</td>
                                        <td>{{ number_format($total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="form-group">
                                <label for="customerSelect">Pelanggan</label>
                                <select class="form-control" id="customerSelect" name="customer_DD">
                                    <option value="">Pilih Pelanggan</option>
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->customer }}">{{ $customer->customer }}</option>
                                    @endforeach
                                    <option value="other">Lainnya (Isi Manual)</option>
                                </select>
                                <div id="manualEntry" class="manual-entry mt-2">
                                    <label for="customerName">Masukan nama Pelanggan</label>
                                    <input type="text" class="form-control" id="customerName" name="customer">
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary mt-3">Checkout</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- End Recent Activity -->


            </div><!-- End Right side columns -->

        </div>
    </section>

</main><!-- End #main -->
    <div class="container mt-5">

    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="text/javascript">
                    $(document).ready(function () {
                        $('tbody').on('submit', '#OrderProduct', function (event) {
                event.preventDefault();
                var item = $(this);
                var id = item.data('id');
                var url = "/cashier/order/selected/product/" + id;

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: item.serialize(),
                    success: function (result) {
                        alert(result.message || 'Product Berhasil di Pesan');
                        location.reload();
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        alert('Gagal Memesan Product.');
                    }
                });
            });

            $('#customerSelect').on('change', function () {
                if ($(this).val() == 'other') {
                    $('#manualEntry').show();
                } else {
                    $('#manualEntry').hide();
                }
            });

            $('#CheckOutTable').on('submit', function (event) {
                event.preventDefault();

                var form = $(this);
                var url = "/cashier/checkout/pending/product";
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    success: function (result) {
                        alert('Pesanan berhasil di Checkout');
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
                        alert('Pesanan gagal di Checkout');
                    }
                });
            });


        });
    </script>

