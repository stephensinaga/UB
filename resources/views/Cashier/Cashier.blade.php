@extends('layouts.app')

@section('contents')
<div class="container card-body">
    <section class="section dashboard container-fluid">
        <div class="row">
            <!-- Product section -->
            <div class="col-lg-7">
                <!-- Adjust to col-lg-7 -->
                <form method="GET" action="{{ route('CashierView') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari berdasarkan Nama Produk atau Kode" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="category" class="form-control">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->category }}">{{ $category->category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="row" style="max-height: 650px; overflow-y: auto;">
                    @foreach ($product as $item)
                    <div class="col-xxl-4 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title text-truncate" style="font-size: 1rem;">{{ $item->product_name }}
                                </h4>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon d-flex align-items-center justify-content-center"
                                        style="width: 150px; height: 150px;">
                                        @if ($item->product_images)
                                        <img src="{{ asset('storage/' . $item->product_images) }}" alt="Product Image"
                                            class="img-fluid" style="object-fit: cover;">
                                        @else
                                        <i class="bi bi-cart" style="font-size: 3rem;"></i>
                                        @endif
                                    </div>
                                    <div class="ps-3 flex-grow-1">
                                        <h6 class="product-price" style="font-size: 1rem;">Rp{{
                                            number_format($item->product_price) }}</h6>
                                        <p class="text-muted small product-code">{{ $item->product_code }}</p>
                                        <form method="post" class="OrderProduct" data-id="{{ $item->id }}">
                                            @csrf
                                            <button type="submit" class="btn btn-primary mt-2"><i
                                                    class="bi bi-plus"></i> Order</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Checkout section -->
            <div class="col-lg-5">
                <!-- Reduced from col-lg-5 to make room -->
                <div class="container-md mt-5">
                    <div class="card shadow-sm p-2">
                        <h2 class="mb-4">Checkout List</h2>
                        <div class="table-responsive">
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
                                    @foreach ($order as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ number_format($item->product_price) }}</td>
                                        <td>
                                            {{ $item->qty }}
                                            <form method="post" class="MinOrderItem d-inline-block"
                                                data-id="{{ $item->id }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-danger">-</button>
                                            </form>
                                        </td>
                                        <td>{{ number_format($item->qty * $item->product_price) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td colspan="3" class="text-right">Total Price:</td>
                                        <td>{{ number_format($order->sum(fn($item) => $item->qty *
                                            $item->product_price)) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog"
                            aria-labelledby="invoiceModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="invoiceModalLabel">Invoice</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="invoice-header">
                                            <h6>No. Invoice: <span id="invoiceId"></span></h6>
                                            <p>Tanggal: <span id="invoiceDate"></span></p>
                                        </div>
                                        <div class="invoice-details mt-3">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>Kasir:</th>
                                                    <td id="cashierName"></td>
                                                </tr>
                                                <tr>
                                                    <th>Pelanggan:</th>
                                                    <td id="customerNames"></td>
                                                </tr>
                                                <tr>
                                                    <th>Total Harga:</th>
                                                    <td id="grandTotal"></td>
                                                </tr>
                                                <tr>
                                                    <th>Metode Pembayaran:</th>
                                                    <td id="payments"></td>
                                                </tr>
                                                <tr id="cashRow" style="display: none;">
                                                    <th>Uang Dibayar:</th>
                                                    <td id="cashs"></td>
                                                </tr>
                                                <tr id="changesRow" style="display: none;">
                                                    <th>Kembalian:</th>
                                                    <td id="changes"></td>
                                                </tr>
                                                <tr id="transferProofRow" style="display: none;">
                                                    <th>Bukti Transfer:</th>
                                                    <td id="transferProofs"></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Kembali</button>
                                        <button type="button" class="btn btn-primary" id="PrintInvoice">Print</button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- Closing div for modal -->
                        
                        <form method="POST" id="CheckOutTable" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="customerSelect">Pelanggan</label>
                                <select class="form-control" id="customerSelect" name="customer_select">
                                    <option value="">Pilih Pelanggan</option>
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->customer }}">{{ $customer->customer }}</option>
                                    @endforeach
                                    <option value="other">Lainnya (Isi Manual)</option>
                                </select>
                                <div id="manualEntry" class="manual-entry mt-2" style="display: none;">
                                    <label for="customerName">Masukan nama Pelanggan</label>
                                    <input type="text" class="form-control" id="customerName" name="customer">
                                </div>

                                <br>
                                <label for="paymentType">Tipe Pembayaran</label>
                                <select class="form-control" id="paymentType" name="payment_type">
                                    <option value="">Pilih Tipe Pembayaran</option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                </select>

                                <div class="cash-section mt-3">
                                    <label for="cashGiven">Uang Dibayar</label>
                                    <input type="number" class="form-control" id="cashGiven" name="cash"
                                        placeholder="Masukkan jumlah uang">
                                </div>

                                <div class="transfer-section mt-3" style="display: none;">
                                    <label for="transferProof">Unggah Bukti Transfer</label>
                                    <input type="file" class="form-control-file" id="transferProof"
                                        name="transfer_proof">
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary mt-3" name="checkout_type"
                                        value="checkout">Checkout</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
            // Hide sections initially
            $('#manualEntry').hide();
            $('.cash-section').hide();
            $('.transfer-section').hide();

            // Show manual entry based on customer selection
            $('#customerSelect').on('change', function() {
                $('#manualEntry').toggle($(this).val() === 'other');
            });

            // Show cash input or transfer section based on payment type selection
            $('#paymentType').on('change', function() {
                const paymentType = $(this).val();
                $('.cash-section').toggle(paymentType === 'cash');
                $('.transfer-section').toggle(paymentType === 'transfer');
            });

            function calculateTotalPrice() {
                let totalPrice = 0;

                $('tbody tr').each(function() {
                    let price = $(this).find('td:nth-child(2)').text().trim();
                    price = parseFloat(price.replace(/[^0-9.-]+/g, ''));

                    let qty = $(this).find('td:nth-child(3)').text().trim();
                    qty = parseInt(qty);

                    if (!isNaN(price) && !isNaN(qty)) {
                        let totalItemPrice = price * qty;
                        totalPrice += totalItemPrice;
                    }
                });

                $('tfoot tr td:last-child').text(totalPrice.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }

            calculateTotalPrice();

            // Ordering a product
            $('.OrderProduct').on('submit', function(event) {
                event.preventDefault();
                let id = $(this).data('id');
                let url = `/cashier/order/selected/product/${id}`;

                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $(this).serialize(),
                    success: function(result) {
                        // Jalankan kode lainnya di sini, misalnya reload halaman tanpa alert
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert(
                            'Gagal Memesan Product.'
                        ); // Kamu bisa menghilangkan alert ini juga jika tidak diperlukan
                    }
                });
            });

            // Checkout Process
            $('#CheckOutTable').on('submit', function(event) {
                event.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: "/cashier/checkout/pending/product",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(result) {
                        displayInvoice(result.invoice);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('Pesanan gagal di Checkout');
                    }
                });
            });

            // Function to display invoice
            function displayInvoice(invoice) {
                $('#invoiceId').text(invoice.id);
                $('#invoiceDate').text(new Date(invoice.created_at).toLocaleDateString());
                $('#cashierName').text(invoice.cashier);
                $('#customerNames').text(invoice.customer);
                $('#grandTotal').text('Rp ' + parseFloat(invoice.grandtotal).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                $('#payments').text(invoice.payment.charAt(0).toUpperCase() + invoice.payment.slice(1));

                if (invoice.payment === 'cash') {
                    $('#cashRow').show();
                    $('#changesRow').show();
                    $('#cashs').text('Rp ' + parseFloat(invoice.cash).toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $('#changes').text('Rp ' + parseFloat(invoice.changes).toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $('#transferProofRow').hide();
                } else if (invoice.payment === 'transfer') {
                    $('#cashRow').hide();
                    $('#changesRow').hide();
                    $('#transferProofRow').show();
                    $('#transferProofs').html('<a href="/storage/' + invoice.transfer_image +
                        '" target="_blank">Lihat Bukti Transfer</a>');
                }
                $('#PrintInvoice').attr('data-id', invoice.id);
                $('#invoiceModal').modal('show');
                $('.modal-backdrop').remove();
            }


            // Reducing item quantity
            $('tbody').on('submit', '.MinOrderItem', function(event) {
                event.preventDefault();
                let id = $(this).data('id');
                let url = `/cashier/min/pending/order/${id}`;

                $.ajax({
                    url: url,
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        alert('Pengurangan Berhasil');
                        location.reload();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('Pengurangan Gagal 🤦‍♂️');
                    }
                });
            });

            $('#PrintInvoice').click(function() {
                // Ambil ID invoice dari elemen modal
                var invoiceId = $('#invoiceId').text();

                // Lakukan permintaan AJAX untuk mencetak invoice
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/cashier/print/invoice/' + invoiceId, // Sesuaikan dengan route Anda
                    type: 'GET',
                    success: function(response) {
                        alert(response.success);
                        location.reload();
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr
                            .responseJSON.error : 'An error occurred.';
                        alert('Error printing invoice: ' + errorMessage);
                    }
                });
            });
        });
</script>
