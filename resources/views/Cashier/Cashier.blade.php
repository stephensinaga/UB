@extends('layouts.app')

@section('contents')
<main id="main" class="main">
    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    <div class="row mt-5">
                        <form method="GET" action="{{ route('CashierView') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan Nama Produk atau Kode" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-4">
                                    <select name="category" class="form-control">
                                        <option value="">Semua Kategori</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->category }}">
                                            {{ $category->category }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                        </form>
                        <div class="row" style="max-height: 650px; overflow-y: auto;"> <!-- Tinggi maks sudah ditingkatkan -->
                            @foreach($product as $index => $item)
                            <div class="col-xxl-4 col-md-6 mb-4"> <!-- Grid system Bootstrap -->
                                <div class="card">
                                    <div class="card-body">
                                        <!-- Pindahkan nama produk ke bagian paling atas -->
                                        <h4 class="card-title text-truncate" style="font-size: 1rem;">{{ $item->product_name }}</h4> <!-- Text truncation untuk mencegah overflow -->
                                        <div class="d-flex align-items-center">
                                            <div class="card-icon d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                                                @if($item->product_images)
                                                <img src="{{ asset('storage/' . $item->product_images) }}" alt="Product Image" style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                <i class="bi bi-cart" style="font-size: 3rem;"></i>
                                                @endif
                                            </div>
                                            <div class="ps-3" style="flex-grow: 1;">
                                                <h6 class="product-price" style="font-size: 1rem;">Rp{{ number_format($item->product_price, 2) }}</h6> <!-- Ukuran font responsif -->
                                                <p class="text-muted small product-code">{{ $item->product_code }}</p> <!-- CSS responsif -->

                                                <form method="post" class="OrderProduct" data-id="{{ $item->id }}"
                                                    action="{{ url('/cashier/order/selected/product/' . $item->id) }}">
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
                        </div> <!-- Closing div for row with products -->
                    </div> <!-- Closing div for mt-5 row -->
                </div> <!-- Closing div for inner row -->
            </div> <!-- Closing div for col-lg-8 -->
            <div class="col-lg-4">
                <!-- Checkout List -->
                <div class="container mt-5">
                    <div class="card shadow-sm p-4">
                        <h2 class="mb-4">Checkout List</h2>
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
                                    <td>{{ number_format($item->product_price, 2) }}</td>
                                    <td>
                                        {{ $item->qty }}
                                        <form method="post" class="MinOrderItem" data-id="{{ $item->id }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit">Reduce</button>
                                        </form>
                                    </td>
                                    <td>{{ number_format($item->qty * $item->product_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="3" class="text-right">Total Price:</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>

                        <!-- Checkout Form -->
                        <form method="POST" id="CheckOutTable" enctype="multipart/form-data">
                            @csrf
                            <!-- Customer selection, payment type, and cash/transfer inputs -->
                            <div class="form-group">
                                <label for="customerSelect">Pelanggan</label>
                                <select class="form-control" id="customerSelect" name="customer_select">
                                    <option value="">Pilih Pelanggan</option>
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->customer }}">{{ $customer->customer }}</option>
                                    @endforeach
                                    <option value="other">Lainnya (Isi Manual)</option>
                                </select>
                                <div id="manualEntry" class="manual-entry mt-2">
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
                                    <input type="number" class="form-control" id="cashGiven" name="cash">
                                </div>

                                <div class="transfer-section mt-3">
                                    <label for="transferProof">Unggah Bukti Transfer</label>
                                    <input type="file" class="form-control-file" id="transferProof" name="transfer_proof">
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary mt-3">Checkout</button>
                                </div>

                            </div>
                    </div>
                    </form> <!-- Closing div for Checkout Form -->

                    <!-- Bootstrap Modal for Invoice -->
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
                                    <button type="button" class="btn btn-primary"
                                        id="PrintInvoice">Print</button>
                                </div>
                            </div>
                        </div>
                    </div> <!-- Closing div for modal -->
                </div> <!-- Closing div for card -->
            </div> <!-- Closing div for container -->
    </section>
</main><!-- End #main -->
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

        // Function to calculate the total price
        function calculateTotalPrice() {
            let totalPrice = 0;

            $('tbody tr').each(function() {
                let price = parseFloat($(this).find('td:nth-child(2)').text().replace(/[^0-9.-]+/g, ''));
                let qty = parseInt($(this).find('td:nth-child(3)').text());

                if (!isNaN(price) && !isNaN(qty)) {
                    totalPrice += price * qty;
                }
            });

            return totalPrice; // Return the total price
        }

        // Ordering a product
        $('tbody').on('submit', '.OrderProduct', function(event) {
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
                    alert(result.message || 'Product Berhasil di Pesan');
                    location.reload();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Gagal Memesan Product.');
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
                $('#transferProofs').html('<a href="/storage/' + invoice.transfer_image + '" target="_blank">Lihat Bukti Transfer</a>');
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

        $('#PrintInvoice').on('click', function() {
            var invoiceId = $(this).data('id');
            $.ajax({
                url: '/print/invoice/' + invoiceId,
                type: 'GET',
                success: function(response) {
                    // Handle the successful print response here
                    window.open(response.url, '_blank'); // Assuming the response has a URL
                },
                error: function(xhr) {
                    alert('Failed to print invoice.');
                }
            });
        });
    });
</script>