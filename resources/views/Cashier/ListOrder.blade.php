@extends('layouts.app')

@section('contents')
<div class="container">
    <h1 class="my-4">List Order</h1>

    @if ($mainOrders->isEmpty())
    <div class="alert alert-info">
        No orders available.
    </div>
    @else
    <div class="row fw-bold mb-2">
        <div class="col-2">No Invoice</div>
        <div class="col-2">No Meja</div>
        <div class="col-3">Customer</div>
        <div class="col-3">Grand Total</div>
        <div class="col-2">Actions</div>
    </div>
    @foreach($mainOrders as $order)
    <div class="row mb-2 border p-2">
        <div class="col-2">{{ $order->no_invoice }}</div>
        <div class="col-2">{{ $order->no_meja }}</div>
        <div class="col-3">{{ $order->customer }}</div>
        <div class="col-3">Rp{{ number_format($order->grandtotal, 0, ',', '.') }}</div>
        <div class="col-2">
            <!-- Button to open modal with unique ID -->
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#processModal-{{ $order->id }}">
                Process
            </button>
        </div>
    </div>

    <!-- Modal with unique ID -->
    <div class="modal fade" id="processModal-{{ $order->id }}" tabindex="-1"
        aria-labelledby="processModalLabel-{{ $order->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="processModalLabel-{{ $order->id }}">Order Details - Invoice {{
                        $order->no_invoice }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Order Details -->
                    <p><strong>No Invoice:</strong> {{ $order->no_invoice }}</p>
                    <p><strong>No Meja:</strong> {{ $order->no_meja }}</p>
                    <p><strong>Customer:</strong> {{ $order->customer }}</p>
                    <p><strong>Grand Total:</strong> Rp{{ number_format($order->grandtotal, 0, ',', '.') }}</p>

                    <hr>

                    <h5>Products Ordered</h5>
                    <ul>
                        @foreach ($order->orders as $product)
                        <li>{{ $product->product_name }}, Quantity : {{ $product->qty }}, Price: Rp{{
                            number_format($product->product_price, 0, ',', '.') }}</li>
                        @endforeach
                    </ul>

                    <hr>

                    <!-- Form with unique ID for each order -->
                    <form method="POST" action="javascript:void(0)" enctype="multipart/form-data"
                        id="ProccessPendingOrder-{{ $order->id }}">
                        @method('put')
                        @csrf
                        <div class="mt-3 mb-3">
                            <label for="paymentType-{{ $order->id }}">Payment Type</label>
                            <select class="form-control" id="paymentType-{{ $order->id }}" name="payment_type">
                                <option value="">Choose Payment Type</option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>

                        <div class="cash-section mt-3 mb-3" id="cashSection-{{ $order->id }}">
                            <label for="cashGiven-{{ $order->id }}">Money Paid</label>
                            <input type="number" class="form-control" id="cashGiven-{{ $order->id }}" name="cash"
                                placeholder="Masukkan jumlah uang">
                        </div>

                        <div class="transfer-section mt-3 mb-3" id="transferSection-{{ $order->id }}">
                            <label for="transferProof-{{ $order->id }}">Upload Proof of Transfer</label>
                            <input type="file" class="form-control-file" id="transferProof-{{ $order->id }}"
                                name="transfer_proof">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Process Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @endif

    <!-- Invoice Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel"
        aria-hidden="true">
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
                        <!-- Tambahkan No Meja -->
                        <p>No. Meja: <span id="tableNumber"></span></p>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnKembali">Back</button>
                    <button type="button" class="btn btn-primary" id="PrintInvoice">Print</button>
                </div>
            </div>
        </div>
    </div>


</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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

    // Tampilkan nomor meja
    $('#tableNumber').text(invoice.no_meja);

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

        @foreach($mainOrders as $order)
            $('#cashSection-{{ $order->id }}').hide();
            $('#transferSection-{{ $order->id }}').hide();

            // Toggle between cash and transfer sections
            $('#paymentType-{{ $order->id }}').on('change', function() {
                const paymentType = $(this).val();
                $('#cashSection-{{ $order->id }}').toggle(paymentType === 'cash');
                $('#transferSection-{{ $order->id }}').toggle(paymentType === 'transfer');
            });

            // Handle form submit via AJAX
            $('#ProccessPendingOrder-{{ $order->id }}').on('submit', function(event) {
                event.preventDefault();

                // Get form data
                let formData = new FormData(this);
                let id = "{{ $order->id }}"; // Corrected variable for order ID
                let cash = formData.get('cash');
                let transferProof = formData.get('transfer_proof');

                // If cash or transferProof is empty, set to null
                if (!cash) {
                    cash = null;
                }
                if (!transferProof) {
                    transferProof = null;
                }

                // Set the route dynamically
                let url = "{{ route('ProcessPendingOrder', ['id' => 'ID_PLACEHOLDER', 'type' => 'TYPE_PLACEHOLDER', 'cash' => 'CASH_PLACEHOLDER', 'img' => 'IMG_PLACEHOLDER']) }}"
                    .replace('ID_PLACEHOLDER', id)
                    .replace('TYPE_PLACEHOLDER', formData.get('payment_type'))
                    .replace('CASH_PLACEHOLDER', cash)
                    .replace('IMG_PLACEHOLDER', transferProof);

                $.ajax({
                    url: url,
                    type: 'PUT',
                    data: formData,
                    contentType: false,
                    processData: false,

                    success: function(result) {
                            alert('Order processed successfully!');
                            $('#processModal-{{ $order->id }}').modal('hide'); // Tutup modal proses
                            displayInvoice(result.invoice); // Tampilkan modal invoice dengan data invoice yang baru
                        },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        @endforeach

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
            var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'An error occurred.';
            alert('Error printing invoice: ' + errorMessage);
        }
    });
});



    });
</script>
@endsection
