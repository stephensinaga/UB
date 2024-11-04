@extends('layouts.app')

@section('contents')
    <div class="card p-4">
        <h3 class="mb-4 text-center">Input Product</h3>
        <form action="javascript:void(0)" method="POST" id="AddOrder">
            @csrf
            <div class="row mb-3">
                <div class="col-md-3 mb-3">
                    <label for="productName" class="form-label">Product Name</label>
                    <input type="text" class="form-control" name="product" placeholder="Ex. Nasi Kotak" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="unit" class="form-label">Unit</label>
                    <input type="text" class="form-control" name="unit" placeholder="Ex. Box / Kotak / Bungkus"
                        required>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="qty" class="form-label">Qty</label>
                    <input type="number" class="form-control" name="qty" placeholder="Ex. 100" required>
                </div>
                <div class="col-md-4">
                    <label for="price" class="form-label">Price</label>
                    <input type="text" class="form-control" name="price" id="price" placeholder="Ex. Rp. 12.500"
                        required>
                </div>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Additional Information</label>
                <textarea class="form-control" name="keterangan" rows="3" placeholder="Ex. Without 'vegetables'"></textarea>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Add Order</button>
            </div>
        </form>
    </div>

    <div class="card mt-4 p-4">
        <h4 class="mb-3 text-center">Order List</h4>
        <table class="table table-bordered text-center">
            <thead class="text-center">
                <tr>
                    <th>Product</th>
                    <th>Unit</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Grand Total</th>
                    <th>Information</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="orderTableBody">
                @foreach ($order as $item)
                    <tr>
                        <td class="text-center">{{ $item->product }}</td>
                        <td class="text-center">{{ $item->unit }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-center">Rp. {{ number_format($item->price, 2, ',', '.') }}</td>
                        <td class="text-center">Rp. {{ number_format($item->grandtotal, 2, ',', '.') }}</td>
                        <td class="text-center">{{ $item->keterangan ?? ' --- ' }}</td>
                        <td>
                            <form action="{{ route('DeletePoOrder', ['id' => $item->id]) }}" method="POST"
                                class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger delete-button">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-end mt-4">
            <button type="button" class="btn btn-success" id="proccessOrder" data-bs-toggle="modal"
                data-bs-target="#processOrderModal">Process All Orders</button>
        </div>
    </div>

    <!-- Modal for Processing Orders -->
    <div class="modal fade" id="processOrderModal" tabindex="-1" aria-labelledby="processOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="processOrderModalLabel">Process Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="javascript:void(0)" method="POST" id="ProcessOrderForm" enctype="multipart/form-data">
                    @method('post')
                    @csrf

                    @foreach ($order as $item)
                        <input type="hidden" name="ids[]" value="{{ $item->id }}">
                    @endforeach
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="customer" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" name="customer" required>
                        </div>
                        <div class="mb-3">
                            <label for="customer_contact" class="form-label">Customer Contact</label>
                            <input type="text" class="form-control" name="customer_contact" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Additional Information</label>
                            <textarea class="form-control" name="keterangan" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="payment" class="form-label">Payment Type</label>
                            <select class="form-select" name="payment" id="payment" required>
                                <option value="">Select Payment Type</option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                        <div class="mb-3" id="cashInput" style="display: none;">
                            <label for="cash" class="form-label">Cash Amount</label>
                            <input type="text" class="form-control" name="cash" id="modalPrice"
                                placeholder="Rp. 0">
                        </div>
                        <div class="mb-3" id="transferImgInput" style="display: none;">
                            <label for="transfer_img" class="form-label">Transfer Proof</label>
                            <input type="file" class="form-control" name="transfer_img"">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            // Show/Hide fields based on payment type selection
            $('#payment').on('change', function() {
                if (this.value === 'cash') {
                    $('#cashInput').show();
                    $('#transferImgInput').hide();
                } else if (this.value === 'transfer') {
                    $('#cashInput').hide();
                    $('#transferImgInput').show();
                } else {
                    $('#cashInput, #transferImgInput').hide();
                }
            });

            function formatPriceInput(input) {
                input.addEventListener('input', function() {
                    let value = this.value.replace(/[^,\d]/g, '');
                    if (value) {
                        value = parseInt(value, 10).toLocaleString('id-ID', {
                            minimumFractionDigits: 0
                        });
                        this.value = 'Rp. ' + value;
                    }
                });
            }

            // Attach event listeners to both price inputs
            const priceInput = document.getElementById('price');
            const modalPriceInput = document.getElementById('modalPrice');

            if (priceInput) {
                formatPriceInput(priceInput);
            }

            if (modalPriceInput) {
                formatPriceInput(modalPriceInput);
            }


            // Remove "Rp." format when submitting the form
            document.getElementById('AddOrder').addEventListener('submit', function() {
                const priceInput = document.getElementById('price');
                if (priceInput) {
                    priceInput.value = priceInput.value.replace(/[^0-9]/g, '');
                }
            });

            document.getElementById('ProcessOrderForm').addEventListener('submit', function() {
                const modalPriceInput = document.getElementById('modalPrice');
                if (modalPriceInput) {
                    modalPriceInput.value = modalPriceInput.value.replace(/[^0-9]/g, '');
                }
            });


            // Add order with AJAX
            $('#AddOrder').on('submit', function(event) {
                event.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('AddPoOrder') }}",
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
                            timerProgressBar: true
                        });
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Add Data',
                            text: xhr.responseText,
                            showConfirmButton: true
                        });
                    }
                });
            });

            // Delete confirmation
            $('.delete-button').on('click', function() {
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: form.attr('action'),
                            type: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    'Your data has been deleted.',
                                    'success'
                                );
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to Delete Data',
                                    text: xhr.responseText,
                                    showConfirmButton: true
                                });
                            }
                        });
                    }
                });
            });

            $('#ProcessOrderForm').on('submit', function(event) {
                event.preventDefault();
                let formData = new FormData(this); // Ambil FormData dari form

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data akan di upload!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, upload data!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('ProcessPoPendingOrder') }}",
                            type: 'POST',
                            data: formData, // Gunakan FormData
                            contentType: false, // Penting untuk upload file
                            processData: false, // Penting untuk upload file
                            success: function(response) {
                                Swal.fire(
                                    'Berhasil!',
                                    'Data Anda telah diupload.',
                                    'success'
                                );
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Mengupload Data',
                                    text: xhr.responseText,
                                    showConfirmButton: true
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
