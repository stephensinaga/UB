@extends('layouts.app')

@section('contents')
<div class="container mt-5">
    <!-- Card for Input -->
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-primary text-white py-4">
            <h5 class="card-title mb-0">Input Product</h5>
        </div>
        <div class="card-body mt-3">
            <form id="ItemForm" action="javascript:void(0)" method="post">
                @csrf
                <div class="row">
                    <!-- Product Name -->
                    <div class="col-md-6 mb-3">
                        <label for="product" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="product" name="product"
                            placeholder="Enter product name">
                    </div>
                    <!-- Quantity -->
                    <div class="col-md-3 mb-3">
                        <label for="qty" class="form-label">Quantity (Qty)</label>
                        <input type="number" class="form-control" id="qty" name="qty" placeholder="Enter quantity">
                    </div>
                    <!-- Price with Rupiah Format -->
                    <div class="col-md-3 mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="text" class="form-control" id="price" name="price" placeholder="Enter price">
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" rows="3" name="keterangan"
                            placeholder="Enter additional information"></textarea>
                    </div>
                </div>

                <!-- Button to Trigger Modal and Submit Button -->
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                        data-bs-target="#productListModal">
                        View Product List
                    </button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card mt-5 shadow-sm">
        <div class="card-header bg-gradient-dark text-white">
            <h5 class="card-title mb-0">Pre-Order Products</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Grand Total</th>
                        <th>Keterangan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item as $items)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $items->product }}</td>
                        <td>{{ $items->qty }}</td>
                        <td>Rp {{ number_format($items->price, 2) }}</td>
                        <td>Rp {{ number_format($items->grandtotal, 2) }}</td>
                        <td>{{ $items->keterangan }}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-button"
                                data-id="{{ $items->id }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#preOrderModal">
                    Process Pre-Order
                </button>
            </div>
        </div>
    </div>

    <!-- Modal for Product List -->
    <div class="modal fade" id="productListModal" tabindex="-1" aria-labelledby="productListModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white py-3">
                    <h5 class="modal-title" id="productListModalLabel">Product List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body mt-3">
                    <div class="row">
                        @foreach ($product as $item)
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 text-truncate">{{ $item->product_name }}</h6>
                                </div>
                                <div class="card-body d-flex align-items-center" style="height: 200px;">
                                    <div class="card-icon d-flex align-items-center justify-content-center"
                                        style="width: 150px; height: 150px;">
                                        @if ($item->product_images)
                                        <img src="{{ asset('storage/' . $item->product_images) }}" alt="Product Image"
                                            class="img-fluid" style="object-fit: cover;">
                                        @else
                                        <i class="bi bi-cart" style="font-size: 3rem;"></i>
                                        @endif
                                    </div>
                                    <div class="ps-3">
                                        <h6 class="product-price">Rp {{ number_format($item->product_price) }}</h6>
                                        <p class="text-muted small product-code">{{ $item->product_code }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Pre-Order Modal -->
    <div class="modal fade" id="preOrderModal" tabindex="-1" aria-labelledby="preOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header with Title -->
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title" id="preOrderModalLabel">Add Customer Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body with Form Inputs -->
                <div class="modal-body">
                    <form id="PreOrderForm" action="javascript:void(0)" method="post">
                        @csrf
                        <!-- Customer Input -->
                        <div class="mb-3">
                            <label for="customer" class="form-label">Customer</label>
                            <input type="text" class="form-control" id="customer" name="customer"
                                placeholder="Enter customer name">
                        </div>

                        <!-- Customer Contact Input -->
                        <div class="mb-3">
                            <label for="customer_contact" class="form-label">Customer Contact</label>
                            <input type="text" class="form-control" id="customer_contact" name="customer_contact"
                                placeholder="Enter customer contact">
                        </div>

                        <!-- Keterangan Textarea -->
                        <div class="mb-3">
                            <label for="keterangan_modal" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan_modal" rows="3" name="keterangan"
                                placeholder="Enter additional information"></textarea>
                        </div>

                        <!-- Payment Select Option -->
                        <div class="mb-3">
                            <label for="payment" class="form-label">Payment</label>
                            <select class="form-select" id="payment" name="payment">
                                <option value="" selected disabled>Select payment method</option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>

                        <!-- Conditional Input Fields Based on Payment Selection -->
                        <div id="cashField" class="mb-3 d-none">
                            <label for="cash" class="form-label">Cash Amount</label>
                            <input type="text" class="form-control" id="cash" name="cash"
                                placeholder="Enter cash amount">
                        </div>
                        <div id="transferField" class="mb-3 d-none">
                            <label for="transfer_proof" class="form-label">Transfer Proof</label>
                            <input type="file" class="form-control" id="transfer_proof" name="transfer_proof">
                        </div>
                    </form>
                </div>

                <!-- Modal Footer with Submit Button -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="PreOrderForm" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Show the correct modal on button click
$('#processPreOrderButton').on('click', function() {
    $('#preOrderModal').modal('show');
});

// Show or hide fields based on payment type selection
$('#payment').on('change', function() {
    const paymentType = $(this).val();
    if (paymentType === 'cash') {
        $('#cashField').removeClass('d-none');
        $('#transferField').addClass('d-none');
    } else if (paymentType === 'transfer') {
        $('#cashField').addClass('d-none');
        $('#transferField').removeClass('d-none');
    } else {
        $('#cashField').addClass('d-none');
        $('#transferField').addClass('d-none');
    }
});


    // Rupiah Format for Price Field
    const priceInput = document.getElementById('price');
    priceInput.addEventListener('keyup', function(e) {
        this.value = formatRupiah(this.value, 'Rp. ');
    });

    const cashInput = document.getElementById('cash');
    if (cashInput) {
        cashInput.addEventListener('keyup', function(e) {
            this.value = formatRupiah(this.value, 'Rp. ');
        });
    }

    // Format function for Rupiah
    function formatRupiah(angka, prefix) {
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }

    $(document).ready(function() {
        // Handle form submission for creating pre-orders
        $('#ItemForm').on('submit', function(event) {
            event.preventDefault();

            // Remove the Rupiah format before sending
            let priceValue = $('#price').val().replace(/[^\d]/g, '');
            $('#price').val(priceValue);

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('SavePOItem') }}",
                data: formData,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function(result) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data Will Be Added',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                    location.reload();
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fail Added Data',
                        text: xhr.responseText,
                        showConfirmButton: true
                    });
                }
            });
        });

        // Handle delete action with SweetAlert confirmation
        $('.delete-button').on('click', function() {
            const itemId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('DeletePOItem', ['id' => ':id']) }}".replace(':id', itemId),
                        type: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', 'Your data has been deleted.', 'success');
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Failed!', xhr.responseText, 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
