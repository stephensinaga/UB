<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('assets/img/lgobbn.png')}}" type="image/png">
    <title>Guest Order</title>

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

    <style>
        #checkoutSection {
            max-width: 100%;
            position: sticky;
            top: 20px;
            /* Adjust to your preference */
            z-index: 1000;
            /* Ensure it stays on top */
        }

        .card {
            width: 100%;
        }

        /* Responsive adjustments */
        @media (min-width: 768px) {
            .product-container {
                display: flex;
                justify-content: space-between;
                /* Space between cards */
            }

            .product-list {
                flex: 1;
                /* Takes available space */
                margin-right: 20px;
                /* Space between product list and checkout */
            }

            #checkoutSection {
                flex-basis: 300px;
                /* Fixed width for checkout on larger screens */
            }
        }

        @media (max-width: 767px) {
            .product-list {
                margin-bottom: 20px;
                /* Space below the product list on mobile */
            }

            #checkoutSection {
                margin-top: 20px;
                /* Space above checkout on mobile */
                position: static;
                /* Reset to normal flow on mobile */
            }
        }
    </style>
</head>

<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Nomor Meja dan Nama Anda</h5>
            </div>
            <div class="modal-body">
                <form id="SessionForm" action="javascript:void(0)">
                    @csrf
                    @method('POST')
                    <div class="form-group">
                        <label for="tableNumber">Nomor Meja</label>
                        <input type="text" class="form-control" id="tableNumber" name="table_number"
                            placeholder="Nomor Meja" required>
                    </div>
                    <div class="form-group">
                        <label for="customerName">Nama Anda</label>
                        <input type="text" class="form-control" id="customerName" name="customer_name"
                            placeholder="Nama Anda" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Lanjutkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container card-body" id="mainContent" style="display: none;">
    <section class="section dashboard container-fluid">
        <div class="row mt-3 product-container">
            <!-- Product list in a card on the left -->
            <div class="col-lg-8 col-md-12 product-list">
                <div class="card">
                    <div class="card-body">
                        <h3 class="mb-2">Pesanan Produk</h3>


                        <form id="filterForm" method="GET" action="{{ route('CashierView') }}" class="mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4 col-sm-12 mb-2">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Cari Produk Anda..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-4 col-sm-12 mb-2">
                                    <select name="category" id="categorySelect" class="form-control">
                                        <option value="" disabled selected>Cari Berdasarkan Kategori...</option>
                                        <option value="">All</option>
                                        @foreach ($categories as $category)
                                        <option value="{{ $category->category }}" {{ request('category') == $category->category ? 'selected' : '' }}>
                                            {{ $category->category }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-12 text-md-right text-center">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#checkoutModal">
                                        Lanjut Pesan
                                    </button>
                                </div>
                            </div>
                        </form>



                        <!-- Product list -->
                        <div class="row" id="productList" style="max-height: 650px; overflow-y: auto;">
                            @foreach ($product as $item)
                            <div class="col-xxl-4 col-md-6 mb-4 product-item" data-name="{{ $item->product_name }}"
                                data-code="{{ $item->product_code }}"
                                data-category="{{ strtolower($item->product_category) }}">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title text-truncate" style="font-size: 1rem;">{{
                                            $item->product_name }}</h4>
                                        <div class="d-flex align-items-center">
                                            <div class="card-icon d-flex align-items-center justify-content-center"
                                                style="width: 150px; height: 150px;">
                                                @if ($item->product_images)
                                                <img src="{{ asset($item->product_images) }}"
                                                    alt="Product Image" class="img-fluid" style="object-fit: cover;">
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
                                                    <button type="submit" class="btn btn-primary mt-2">Pilih</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="checkoutModalLabel">Daftar Checkout</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Harga Produk</th>
                                        <th>Kuantitas Produk</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order as $item)
                                    <tr data-product-id="{{ $item->id }}">  
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ number_format($item->product_price) }}</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm qty-input" data-product-id="{{ $item->id }}" value="{{ $item->qty }}" min="1">
                                        </td>
                                        <td>{{ number_format($item->qty * $item->product_price) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td colspan="3" class="text-right">Total Harga:</td>
                                        <td>{{ number_format($order->sum(fn($item) => $item->qty * $item->product_price)) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <form method="POST" id="CheckOutTable" enctype="multipart/form-data">
                            @csrf
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary mt-3 w-100" name="checkout_type"
                                    value="checkout">Checkout</button>
                            </div>
                        </form>
                    </div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

                    // Update total per produk
                    $(this).find('td:nth-child(4)').text(totalItemPrice.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                }
            });

            $('tfoot tr td:last-child').text(totalPrice.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
        }

        $('tbody').on('change', '.qty-input', function(event) {
        let productId = $(this).data('product-id');
        let newQty = $(this).val();

        console.log('Qty updated:', newQty); // Tambahkan ini untuk memeriksa nilai qty

        $.ajax({
            url: `/guest/update/qty/order/${productId}`,
            type: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { qty: newQty },
            success: function(result) {
                calculateTotalPrice(); // Update harga total setelah perubahan qty
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Failed to update quantity.');
            }
        });
    });


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
                    Swal.fire({
                        icon: 'success',
                        title: 'Tunggu Pesanan Anda Akan di Konfimasi!',
                        showConfirmButton: false,
                        timer: 30000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading();
                            let timerInterval = setInterval(() => {
                                const timer = Swal.getHtmlContainer().querySelector('b');
                                if (timer) {
                                    timer.textContent = Swal.getTimerLeft();
                                }
                            }, 100);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                        }
                    });
                        location.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Checkout Gagal',
                        text: xhr.responseText,
                        showConfirmButton: true
                    });
                }
            });
        });

        // Panggil untuk update total saat pertama kali
        calculateTotalPrice();

        $('#searchInput, #categorySelect').on('input change', function() {
            filterProducts(); // Panggil fungsi filter saat ada perubahan input
        });

        function filterProducts() {
            var search = $('#searchInput').val().toLowerCase();
            var category = $('#categorySelect').val();

            // Loop semua produk dan hide/show berdasarkan filter
            $('.product-item').each(function() {
                var productName = $(this).data('name').toLowerCase();
                var productCode = $(this).data('code').toLowerCase();
                var productCategory = $(this).data('category');

                // Cek apakah produk sesuai dengan search dan category
                var isVisible = true;

                if (search && !productName.includes(search) && !productCode.includes(search)) {
                    isVisible = false;
                }

                if (category && productCategory.toLowerCase() !== category.toLowerCase()) {
                    isVisible = false;
                }

                // Tampilkan atau sembunyikan produk
                $(this).toggle(isVisible);
            });
        }
    });
</script>

