@extends('layouts.app')

@section('contents')
    <div class="container">
        <h1 class="my-4">Order List</h1>

        @if ($mainOrders->isEmpty())
            <div class="alert alert-info">
                No orders available.
            </div>
        @else
            <div class="row fw-bold mb-2">
                <div class="col-2">Invoice No</div>
                <div class="col-2">Table No</div>
                <div class="col-3">Customer</div>
                <div class="col-3">Grand Total</div>
                <div class="col-2">Actions</div>
            </div>
            @foreach ($mainOrders as $order)
                <div class="row mb-2 border p-2">
                    <div class="col-2">{{ $order->no_invoice }}</div>
                    <div class="col-2">{{ $order->no_meja }}</div>
                    <div class="col-3">{{ $order->customer }}</div>
                    <div class="col-3">Rp{{ number_format($order->grandtotal, 0, ',', '.') }}</div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#processModal-{{ $order->id }}">
                            Process
                        </button>
                    </div>
                </div>

                <div class="modal fade" id="processModal-{{ $order->id }}" tabindex="-1"
                    aria-labelledby="processModalLabel-{{ $order->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="processModalLabel-{{ $order->id }}">Order Details - Invoice
                                    {{ $order->no_invoice }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Invoice No:</strong> {{ $order->no_invoice }}</p>
                                <p><strong>Table No:</strong> {{ $order->no_meja }}</p>
                                <p><strong>Customer:</strong> {{ $order->customer }}</p>
                                <p><strong>Grand Total:</strong> Rp{{ number_format($order->grandtotal, 0, ',', '.') }}</p>

                                <hr>

                                <h5>Ordered Products</h5>
                                <ul>
                                    @foreach ($order->orders as $product)
                                        <li>{{ $product->product_name }}, Quantity: {{ $product->qty }}, Price:
                                            Rp{{ number_format($product->product_price, 0, ',', '.') }}</li>
                                    @endforeach
                                </ul>

                                <hr>

                                <form method="POST" action="javascript:void(0)" enctype="multipart/form-data"
                                    id="ProccessPendingOrder-{{ $order->id }}">
                                    @method('put')
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $order->id }}">
                                    <div class="mt-3 mb-3">
                                        <label for="paymentType-{{ $order->id }}">Payment Type</label>
                                        <select class="form-control" id="paymentType-{{ $order->id }}"
                                            name="payment_type">
                                            <option value="">Choose Payment Type</option>
                                            <option value="cash">Cash</option>
                                            <option value="transfer">Transfer</option>
                                        </select>
                                    </div>

                                    <div id="cashSection-{{ $order->id }}">
                                        <div class="mb-3">
                                            <label for="cash" class="form-label">Cash Amount</label>
                                            <input type="text" class="form-control" name="cash_formatted" id="cashFormatted-{{ $order->id }}" required>
                                            <input type="hidden" name="cash" id="cash-{{ $order->id }}">
                                        </div>
                                    </div>

                                    <div id="transferSection-{{ $order->id }}">
                                        <div class="mb-3">
                                            <input type="file" class="form-control" name="img"
                                                id="img-{{ $order->id }}" accept="image/*" required>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success">Process Order</button>
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
                            <h6>Invoice No: <span id="invoiceId"></span></h6>
                            <p>Date: <span id="invoiceDate"></span></p>
                            <!-- Add Table Number -->
                            <p>Table No: <span id="tableNumber"></span></p>
                        </div>
                        <div class="invoice-details mt-3">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Cashier:</th>
                                    <td id="cashierName"></td>
                                </tr>
                                <tr>
                                    <th>Customer:</th>
                                    <td id="customerNames"></td>
                                </tr>
                                <tr>
                                    <th>Total Price:</th>
                                    <td id="grandTotal"></td>
                                </tr>
                                <tr>
                                    <th>Payment Method:</th>
                                    <td id="payments"></td>
                                </tr>
                                <tr id="cashRow" style="display: none;">
                                    <th>Amount Paid:</th>
                                    <td id="cashs"></td>
                                </tr>
                                <tr id="changesRow" style="display: none;">
                                    <th>Change:</th>
                                    <td id="changes"></td>
                                </tr>
                                <tr id="transferProofRow" style="display: none;">
                                    <th>Transfer Proof:</th>
                                    <td id="transferProofs"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            id="btnKembali">Back</button>
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

            // Iterate over orders
            @foreach ($mainOrders as $order)
                // Hide sections by default on modal load
                $('#cashSection-{{ $order->id }}').hide();
                $('#transferSection-{{ $order->id }}').hide();

                // On modal show, reset the form
                $('#processModal-{{ $order->id }}').on('shown.bs.modal', function() {
                    // Reset fields when the modal is opened
                    $('#cashSection-{{ $order->id }} input').val('').prop('required', false);
                    $('#transferSection-{{ $order->id }} input').val('').prop('required', false);
                    $('#cashSection-{{ $order->id }}').hide();
                    $('#transferSection-{{ $order->id }}').hide();
                });

                // Toggle payment sections based on the selected type
                $('#paymentType-{{ $order->id }}').on('change', function() {
                    const paymentType = $(this).val();

                    if (paymentType === 'cash') {
                        $('#cashSection-{{ $order->id }}').show().find('input').prop('required', true);
                        $('#transferSection-{{ $order->id }}').hide().find('input').prop('required',
                            false).val('');
                    } else if (paymentType === 'transfer') {
                        $('#transferSection-{{ $order->id }}').show().find('input').prop('required',
                            true);
                        $('#cashSection-{{ $order->id }}').hide().find('input').prop('required', false)
                            .val('');
                    } else {
                        $('#cashSection-{{ $order->id }}').hide().find('input').prop('required', false);
                        $('#transferSection-{{ $order->id }}').hide().find('input').prop('required',
                            false);
                    }
                });

                // Handle the form submission
                $('#ProccessPendingOrder-{{ $order->id }}').on('submit', function(event) {
                    event.preventDefault();
                    let formData = new FormData(this);

                    let id = {{ $order->id }};
                    let paymentType = formData.get('payment_type');
                    let cash = formData.get('cash') || null;

                    if (paymentType === 'cash' && !cash) {
                        alert('Please enter the cash amount.');
                        return;
                    }

                    if (paymentType === 'transfer' && formData.get('img') === null) {
                        alert('Please upload the transfer proof.');
                        return;
                    }

                    $.ajax({
                        url: '{{ route('SavePendingOrder', ['id' => ':id']) }}'.replace(':id', id),
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            // Assuming 'response.invoice' contains invoice details
                            displayInvoice(response.invoice);
                            $('#processModal-{{ $order->id }}').modal('hide');
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            alert('Failed to process the order.');
                        }
                    });
                });

                $('#cashFormatted-{{ $order->id }}').on('input', function() {
                    let value = $(this).val().replace(/\D/g, ''); // Hanya angka
                    $(this).val(formatRupiah(value, 'Rp.')); // Format sebagai rupiah dengan prefix Rp.
                    $('#cash-{{ $order->id }}').val(value); // Simpan nilai asli tanpa format
                });

                // Fungsi untuk memformat angka sebagai Rupiah
                function formatRupiah(angka, prefix) {
                    let numberString = angka.replace(/[^,\d]/g, '').toString(),
                        split = numberString.split(','),
                        sisa = split[0].length % 3,
                        rupiah = split[0].substr(0, sisa),
                        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                    return prefix == undefined ? rupiah : (rupiah ? prefix + rupiah : '');
                }
            @endforeach

            $('#btnKembali').on('click', function() {
                $('#invoiceModal').modal('hide'); // Menutup modal ketika tombol "Kembali" ditekan
            });

            // Function to display the invoice
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
                    $('#transferProofs').html('<a href="/' + invoice.transfer_image + '" target="_blank">Lihat Bukti Transfer</a>');
                }
                $('#PrintInvoice').attr('data-id', invoice.id);
                $('#invoiceModal').modal('show');
                $('.modal-backdrop').remove();
            }
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
@endsection
