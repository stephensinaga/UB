<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('assets/img/dapur_negeri.jpeg') }}" type="image/png">

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
</head>

<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Informasi Pelanggan dan Nomor Meja</h5>
            </div>
            <div class="modal-body">
                <form id="SessionForm" action="javascript:void(0)">
                    @csrf
                    @method('POST')
                    <div class="form-group">
                        <label for="tableNumber">Nomor Meja</label>
                        <input type="text" class="form-control" id="tableNumber" name="table_number"
                            placeholder="Masukkan nomor meja" required>
                    </div>
                    <div class="form-group">
                        <label for="customerName">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="customerName" name="customer_name"
                            placeholder="Masukkan nama pelanggan" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container card-body" id="mainContent" style="display: none;">
    <section class="section dashboard container-fluid">
        <div class="row">
            <!-- Product section -->
            <div class="col-lg-7">
                <!-- Adjust to col-lg-7 -->
                <form method="GET" action="{{ route('GuestCashierView') }}" class="mb-4">
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
                                    <h4 class="card-title text-truncate" style="font-size: 1rem;">
                                        {{ $item->product_name }}
                                    </h4>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon d-flex align-items-center justify-content-center"
                                            style="width: 150px; height: 150px;">
                                            @if ($item->product_images)
                                                <img src="{{ asset('storage/' . $item->product_images) }}"
                                                    alt="Product Image" class="img-fluid" style="object-fit: cover;">
                                            @else
                                                <i class="bi bi-cart" style="font-size: 3rem;"></i>
                                            @endif
                                        </div>
                                        <div class="ps-3 flex-grow-1">
                                            <h6 class="product-price" style="font-size: 1rem;">
                                                Rp{{ number_format($item->product_price) }}</h6>
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
                                        <td>{{ number_format($order->sum(fn($item) => $item->qty * $item->product_price)) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <form method="POST" id="CheckOutTable" enctype="multipart/form-data">
                            @csrf
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary mt-3" name="checkout_type"
                                    value="checkout">Checkout</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Cek apakah session untuk nomor meja dan customer sudah ada
        var tableNumberSession = '{{ $tableNumber ?? '' }}';
        var customerNameSession = '{{ $customerName ?? '' }}';

        // Jika session ada, tampilkan konten utama dan sembunyikan modal
        if (tableNumberSession && customerNameSession) {
            $('#mainContent').show(); // Tampilkan konten utama
            $('#customerModal').modal('hide'); // Sembunyikan modal
        } else {
            // Jika session tidak ada, tampilkan modal untuk mengisi nomor meja dan nama pelanggan
            $('#customerModal').modal('show');
        }

        // Proses form modal ketika submit
        $('#SessionForm').on('submit', function(e) {
            e.preventDefault();

            // Ambil input dari modal
            let tableNumber = $('#tableNumber').val();
            let customerName = $('#customerName').val();

            // Lakukan validasi sederhana
            if (tableNumber && customerName) {
                // Kirim data ke server menggunakan AJAX
                $.ajax({
                    url: '{{ route('SaveSession') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        table_number: tableNumber,
                        customer_name: customerName
                    },
                    success: function(response) {
                        if (response.success) {
                            // Jika sukses, sembunyikan modal dan tampilkan konten utama
                            $('#customerModal').modal('hide');
                            $('#mainContent').show();

                            // Reload halaman atau lakukan apapun yang diinginkan setelah sukses
                            location.reload();
                        } else {
                            alert('Gagal menyimpan data.');
                        }
                    }
                });
            } else {
                alert('Harap isi nomor meja dan nama pelanggan.');
            }
        });

        // Mencegah modal ditutup jika session belum sukses disimpan
        $('#customerModal').modal({
            backdrop: 'static',
            keyboard: false
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
            let url = `/guest/order/selected/product/${id}`;

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $(this).serialize(),
                success: function(result) {
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
                url: "{{route('GuestCheckout')}}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(result) {
                    alert('Checkout berhasil!');
                    location.reload();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('Pesanan gagal di Checkout');
                }
            });
        });

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
    });
</script>
