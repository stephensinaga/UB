@extends('layouts.app')
@section('contents')

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Updating Storage</h1>
        <form action="javascript:void(0)" method="POST" id="UpdateStock">
            @csrf
            @method('POST')

            <div class="form-group">
                <label for="id_material">Material ID</label>
                <select name="id_material" id="id_material" class="form-control">
                    <option value="" selected disabled>Select Material</option>
                    @foreach ($material as $materials)
                    <option value="{{ $materials->id }}" {{ $materials->id == $stock->id_material ? 'selected' : '' }}>
                        {{ $materials->material }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="qty">QTY</label>
                <input type="number" class="form-control" name="qty" placeholder="QTY" value="{{ $stock->qty }}">
            </div>

            <div class="form-group">
                <label for="unit">Unit</label>
                <select name="unit" id="unit" class="form-control">
                    <option value="" selected disabled>Select Unit</option>
                    @foreach ($unit as $units)
                    <option value="{{ $units->id }}" {{ $units->id == $stock->id_unit ? 'selected' : '' }}>
                        {{ $units->unit }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="text" class="form-control" id="price" name="price" placeholder="Price"
                    value="{{ number_format($stock->price, 0, ',', '.') }}">
            </div>

            <div class="form-group">
                <label for="information">Information</label>
                <textarea name="information" class="form-control" cols="30"
                    rows="5">{{ $stock->information }}</textarea>
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function formatPrice(input) {
            let value = input.value.replace(/[^0-9]/g, ''); // Remove everything except numbers
            input.value = value === '' ? '' : 'Rp. ' + value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        $(document).ready(function() {
            // Ensure jQuery is loaded before this point
            $('#price').on('input', function() {
                formatPrice(this); // Format price on input
            });

            $('#UpdateStock').on('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);

                // Get formatted price as a plain number for submission
                const priceValue = $('#price').val().replace(/[^0-9]/g, '');
                formData.set('price', priceValue);

                $.ajax({
                    url: "{{ route('UpdateProcess', ['id' => $stock->id]) }}",
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
                                window.location.href = "{{ route('StorageView') }}"; // Redirect to StorageView
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
    </script>

    @endsection
