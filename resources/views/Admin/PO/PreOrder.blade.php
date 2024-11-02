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

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Add Order</button>
            </div>
        </form>
    </div>

    <div class="card mt-4 p-4">
        <h4 class="mb-3 text-center">Order List</h4> <!-- Judul di tengah -->
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
                        <td><button type="submit" data-id="{{ $item->id }}" id="DeleteOrder">Delete</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            const priceInput = document.getElementById('price');

            priceInput.addEventListener('input', function(e) {
                let value = this.value.replace(/[^,\d]/g, ''); // Only digits
                if (value) {
                    value = parseInt(value, 10).toLocaleString('id-ID', {
                        minimumFractionDigits: 0
                    });
                    this.value = 'Rp. ' + value;
                }
            });

            // Remove "Rp." format when submitting the form
            document.getElementById('AddOrder').addEventListener('submit', function() {
                priceInput.value = priceInput.value.replace(/[^0-9]/g, ''); // Only digits
            });

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
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                                let timerInterval = setInterval(() => {
                                    const timer = Swal.getHtmlContainer()
                                        .querySelector('b');
                                    if (timer) {
                                        timer.textContent = Swal
                                            .getTimerLeft();
                                    }
                                }, 100);
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                            }
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
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                                let timerInterval = setInterval(() => {
                                    const timer = Swal.getHtmlContainer()
                                        .querySelector('b');
                                    if (timer) {
                                        timer.textContent = Swal
                                            .getTimerLeft();
                                    }
                                }, 100);
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                            }
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
        });
    </script>
@endsection
