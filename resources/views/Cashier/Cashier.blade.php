<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cashier</title>
    <style>
        .manual-entry {
            display: none;
        }

        .cash-section,
        .transfer-section {
            display: none;
        }
    </style>
</head>

<body>
    <!-- Product list and order form -->
    <h1>Cashier</h1>
    <div class="card mt-5 p-3">
        <h2>Product List</h2>
        <table class="table table-bordered table-striped mt-3">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Product Code</th>
                    <th>Product Category</th>
                    <th>Product Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($product as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if ($item->product_images)
                                <img src="{{ asset('storage/' . $item->product_images) }}" alt="Product Image"
                                    width="100">
                            @else
                                No Image
                            @endif
                        </td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->product_category }}</td>
                        <td>{{ number_format($item->product_price, 2) }}</td>
                        <td>
                            <form method="post" class="OrderProduct" data-id="{{ $item->id }}">
                                @csrf
                                <button type="submit">Order</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary mt-3">Checkout</button>
                </div>
            </form>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Function to calculate the total price
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


            $('#paymentType').on('change', function() {
                let paymentType = $(this).val();
                if (paymentType === 'cash') {
                    $('.cash-section').show();
                    $('.transfer-section').hide();
                } else if (paymentType === 'transfer') {
                    $('.cash-section').hide();
                    $('.transfer-section').show();
                } else {
                    $('.cash-section').hide();
                    $('.transfer-section').hide();
                }
            });

            // Ordering a product
            $('tbody').on('submit', '.OrderProduct', function(event) {
                event.preventDefault();
                var item = $(this);
                var id = item.data('id');
                var url = "/cashier/order/selected/product/" + id;

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: item.serialize(),
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

            // Handling customer select input
            $('#customerSelect').on('change', function() {
                if ($(this).val() == 'other') {
                    $('#manualEntry').show();
                } else {
                    $('#manualEntry').hide();
                }
            });

            // Checkout Process (Submit checkout table)
            $('#CheckOutTable').on('submit', function(event) {
                event.preventDefault();

                var form = $(this)[0]; // Mengambil elemen form
                var formData = new FormData(form); // Membuat FormData untuk meng-handle file upload

                $.ajax({
                    url: "/cashier/checkout/pending/product",
                    type: 'POST',
                    data: formData,
                    contentType: false, // Pastikan konten tidak di-encode
                    processData: false, // Jangan memproses data
                    success: function(result) {
                        alert('Pesanan berhasil di Checkout');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        alert('Pesanan gagal di Checkout');
                    }
                });
            });


            // Reducing item quantity (MinOrderItem form)
            $('tbody').on('submit', '.MinOrderItem', function(event) {
                event.preventDefault();
                var item = $(this);
                var id = item.data('id');
                var url = "/cashier/min/pending/order/" + id;

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
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        alert('Pengurangan Gagal 🤦‍♂️');
                    }
                });
            });
        });
    </script>

</body>

</html>
