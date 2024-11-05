@extends('layouts.app')
@section('contents')
    <!-- Card for Weekly Receipts Form -->
    <div class="card">
        <div class="card-header mb-3">
            <h4 class="text-center">Weekly Receipts</h4>
        </div>
        <div class="card-body mt-3">
            <!-- Form for Weekly Receipts -->
            <form action="javascript:void(0)" method="POST" id="InReceipts">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="id_material">Material</label>
                        <select name="id_material" id="id_material" class="form-control" required>
                            <option value="" selected disabled>Select Material</option>
                            @foreach ($material as $materials)
                                <option value="{{ $materials->id }}">{{ $materials->material }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="qty">Qty</label>
                        <input type="number" class="form-control" id="qty" name="qty"
                            placeholder="Enter quantity" required>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="id_unit">Unit</label>
                        <select name="id_unit" id="id_unit" class="form-control" required>
                            <option value="" selected disabled>Select Unit</option>
                            @foreach ($unit as $units)
                                <option value="{{ $units->id }}">{{ $units->unit }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="price">Price</label>
                        <input type="text" class="form-control" name="price" id="price" placeholder="Enter price"
                            required>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="information">Information</label>
                        <textarea name="information" id="information" class="form-control" cols="30" rows="3"
                            placeholder="Additional information"></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fa fa-paper-plane"></i> Submit
                </button>
            </form>
        </div>
    </div>

    <!-- Download Report Card -->
    <div class="card mt-4">
        <div class="card-header text-center">
            <h5>Download Report</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('ExportWeeklyReceipts') }}" method="GET" id="filterForm">
                @csrf

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="filter_date">Filter by Date:</label>
                        <input type="date" name="filter_date" id="filter_date" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="filter_month">Filter by Month:</label>
                        <input type="month" name="filter_month" id="filter_month" class="form-control">
                    </div>
                </div>

                <div class="btn-group mt-3 d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary">Download Report</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table for Weekly Receipts -->
    <div class="card mt-4">
        <div class="card-header">
            <h4 class="text-center">Weekly Receipts</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover table-responsive-md text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>Material</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Information</th>
                        <th>Purchase Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pending as $item)
                        <tr>
                            <form action="javascript:void(0)" method="POST" id="UpdatePending-{{ $item->id }}">
                                @csrf
                                @method('put')
                                <input type="hidden" name="id" value="{{ $item->id }}">
                                <td>{{ $item->material->material }}</td>
                                <td><input type="number" class="form-control" name="qty" value="{{ $item->qty }}">
                                </td>
                                <td>{{ $item->unit->unit }}</td>
                                <td><input type="text" name="price" id="price-{{ $item->id }}"
                                        value="Rp {{ number_format($item->price, 2, ',', '.') }}"></td>
                                <td>Rp {{ number_format($item->total, 2, ',', '.') }}</td>
                                <td>{{ $item->information ?? '-' }}</td>
                                <td>{{ $item->purchase_date }}</td>
                                <td>
                                    <button type="submit" class="btn btn-sm btn-success"
                                        onclick="submitForm({{ $item->id }})">Update</button>
                                    <button type="button" class="btn btn-sm btn-danger DeletePending"
                                        data-id="{{ $item->id }}">Delete</button>
                                </td>
                            </form>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Button Save -->
            <div class="text-right mt-3">
                <form action="javascript:void(0)" method="post" id="SaveWeeklyReceipts">
                    @csrf
                    @method('POST')
                    @foreach ($pending as $item)
                        <input type="hidden" name="ids[]" value="{{ $item->id }}">
                    @endforeach
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Save Receipts
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src=" https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function submitForm(id) {
            let form = $('#UpdatePending-' + id);
            let priceInput = $('#price-' + id);
            priceInput.val(priceInput.val().replace(/[^0-9]/g, '')); // Remove formatting

            var formData = form.serialize();

            $.ajax({
                url: "{{ route('UpdatePending', ['id' => 'id']) }}".replace('id', id),
                type: 'PUT',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseText
                    });
                }
            });
        }

        $(document).ready(function() {
            // Price formatting for input
            $(document).on('input', '[id^=price]', function(e) {
                let value = this.value.replace(/[^,\d]/g, ''); // Only digits
                if (value) {
                    value = parseInt(value, 10).toLocaleString('id-ID', {
                        minimumFractionDigits: 0
                    });
                    this.value = 'Rp. ' + value;
                }
            });

            // Remove Rp. format on form submit
            $('#InReceipts').on('submit', function(event) {
                event.preventDefault();
                let priceInput = $('#price');
                priceInput.val(priceInput.val().replace(/[^0-9]/g, '')); // Only digits

                var formData = new FormData(this); // Use FormData for file uploads

                $.ajax({
                    url: "{{ route('InReceipts') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseText
                        });
                    }
                });
            });

            $('.DeletePending').on('click', function() {
                let id = $(this).data('id'); // Get the ID from data-id attribute
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
                            url: "{{ route('DeletePending', ['id' => 'id']) }}".replace(
                                'id', id), // Replace id in URL
                            type: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseText
                                });
                            }
                        });
                    }
                });
            });

            $('#SaveWeeklyReceipts').on('submit', function(event) {
                event.preventDefault();

                // Display SweetAlert confirmation dialog
                Swal.fire({
                    title: 'Save as PO or Stock?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'PO',
                    cancelButtonText: 'Stock',
                    reverseButtons: true
                }).then((result) => {
                    // Determine the selected type based on user's choice
                    let type = result.isConfirmed ? 'PO' : 'Stock';

                    // Add the type parameter to the form data
                    let formData = $(this).serialize() + '&type=' + type;

                    // Submit the form data with the selected type
                    $.ajax({
                        url: "{{ route('SaveWeeklyReceipts') }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseText
                            });
                        }
                    });
                });
            });

        });
    </script>
@endsection
