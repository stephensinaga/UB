@extends('layouts.app')

@section('contents')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    table,
    th,
    td {
        border: 1px solid black;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    .card {
        margin: 20px 0;
    }

    .create-button,
    .receipts-button {
        position: relative;
        display: inline-block;
        margin-right: 10px;
    }

    .table-container {
        position: relative;
    }

    .button-container {
        text-align: right;
        margin-bottom: 10px;
    }
</style>

<body>
    <div class="container mt-4">
        <!-- Filter and Table in a Card -->
        <div class="card">
            <div class="card-header">
                <h4>Material List</h4>
            </div>

            <div class="card-body">
                <!-- Filter Section -->
                <div class="filter-section bg-light p-4 mb-4 rounded shadow-sm">
                    <form method="GET" action="{{ route('FilterMaterial') }}" class="mb-4">
                        <div class="row">
                            <!-- Date Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" class="form-control" id="date" name="date"
                                        value="{{ request('date') }}">
                                </div>
                            </div>

                            <!-- Date Range Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Date Range</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            value="{{ request('start_date') }}">
                                        <span class="input-group-text">s/d</span>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                            value="{{ request('end_date') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Button -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-sm form-control">Filter</button>
                                </div>
                            </div>

                            <!-- Reset Button -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <a href="{{ route('FilterMaterial') }}"
                                        class="btn btn-secondary btn-sm form-control">Reset Filter</a>
                                </div>
                            </div>
                            <!-- Download Report-->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <a href="{{ route('ExportLaporanStock', request()->all()) }}"
                                        class="btn btn-primary btn-sm form-control">Download Report</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tombol Create dan Weekly Receipts -->
                <div class="button-container">
                    <button type="button" class="btn btn-primary create-button" data-toggle="modal"
                        data-target="#AddStockModal">Update Stock</button>
                    <button type="button" class="btn btn-primary create-button" data-toggle="modal"
                        data-target="#AddUnitModal">Add Unit</button>
                    <button type="button" class="btn btn-primary create-button" data-toggle="modal"
                        data-target="#AddMaterialModal">Add Material</button>
                </div>

                @if (session('success'))
                <div class="message">
                    {{ session('success') }}
                </div>
                @endif

                <!-- Table Section with Create Button -->
                <div class="table-container">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Grand Total</th>
                                <th>Information</th>
                                <th>Date</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stock as $stocks)
                            <tr>
                                <td>{{ $stocks->material->material ?? '-' }}</td> <!-- Akses relasi material -->
                                <td>{{ $stocks->qty }}</td>
                                <td>{{ $stocks->unit->unit ?? '-' }}</td> <!-- Akses relasi unit -->
                                <td>Rp {{ number_format($stocks->price, 2, ',', '.') }}</td>
                                <td>Rp {{ number_format($stocks->total, 2, ',', '.') }}</td>
                                <td>{{ $stocks->information ?? '-' }}</td>
                                <td>{{ $stocks->created_at }}</td>
                                <td>
                                    <a href="{{ route('UpdateView', ['id' => $stocks->id]) }}"
                                        class="btn btn-sm btn-warning">Update</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal for Add Stock -->
        <div class="modal fade" id="AddStockModal" tabindex="-1" role="dialog" aria-labelledby="AddStockModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="AddStockModalLabel">Add Stock</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="javascript:void(0)" id="AddStock" method="POST">
                            @csrf
                            @method('post')

                            <div class="form-group">
                                <label for="id_material">Material ID</label>
                                <select name="id_material" id="id_material" class="form-control">
                                    <option value="" selected disabled>Select Material</option>
                                    @foreach ($material as $materials)
                                    <option value="{{ $materials->id }}">{{ $materials->material }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="qty">QTY</label>
                                <input type="number" class="form-control" name="qty" placeholder="QTY">
                            </div>

                            <div class="form-group">
                                <label for="unit">Unit</label>
                                <select name="unit" id="unit" class="form-control">
                                    <option value="" selected disabled>Select Unit</option>
                                    @foreach ($unit as $units)
                                    <option value="{{ $units->id }}">{{ $units->unit }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="text" class="form-control" id="price" name="price" placeholder="Price">
                            </div>

                            <div class="form-group">
                                <label for="information">Information</label>
                                <textarea name="information" class="form-control" cols="30" rows="5"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Create New</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Add Unit -->
        <div class="modal fade" id="AddUnitModal" tabindex="-1" role="dialog" aria-labelledby="AddUnitModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="AddUnitModalLabel">Add Unit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="javascript:void(0)" id="AddUnit" method="POST">
                            @csrf
                            @method('post')

                            <div class="form-group">
                                <label for="unit_name">Unit Name</label>
                                <input type="text" class="form-control" name="unit" placeholder="Unit Name" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Create Unit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Add Material -->
        <div class="modal fade" id="AddMaterialModal" tabindex="-1" role="dialog"
            aria-labelledby="AddMaterialModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="AddMaterialModalLabel">Add Material</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="javascript:void(0)" id="AddMaterial" method="POST">
                            @csrf
                            @method('post')

                            <div class="form-group">
                                <label for="material_name">Material Name</label>
                                <input type="text" class="form-control" name="material" placeholder="Material Name"
                                    required>
                            </div>

                            <button type="submit" class="btn btn-primary">Create Material</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script type="text/javascript">
        // Setup CSRF Token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {

            const priceInput = document.getElementById('price');

            priceInput.addEventListener('input', function(e) {
                let value = this.value.replace(/[^,\d]/g, ''); // Hanya angka
                if (value) {
                    value = parseInt(value, 10).toLocaleString('id-ID', {
                        minimumFractionDigits: 0
                    });
                    this.value = 'Rp. ' + value;
                }
            });

            // Menghilangkan format Rp. saat submit form
            document.getElementById('AddStock').addEventListener('submit', function() {
                priceInput.value = priceInput.value.replace(/[^0-9]/g, ''); // Hanya mengirimkan angka
            });

            $('#AddStock').on('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('AddStock') }}"
                    , type: 'POST'
                    , data: formData
                    , contentType: false
                    , processData: false, // Inside your AJAX success and error functions
                    success: function(response) {
                        if (response.success) {
                            $('#AddStockModal').modal('hide');
                            Swal.fire({
                                icon: 'success'
                                , title: 'Success'
                                , text: response.message
                            , }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error'
                                , title: 'Error'
                                , text: response.message
                            , });
                        }
                    }
                    , error: function(xhr) {
                        Swal.fire({
                            icon: 'error'
                            , title: 'Error'
                            , text: xhr.responseText
                        , });
                    }
                });
            });

            $('#AddMaterial').on('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('CreateMaterial') }}"
                    , type: 'POST'
                    , data: formData
                    , contentType: false
                    , processData: false
                    , success: function(response) {
                        if (response.success) {
                            $('#AddStockModal').modal('hide');
                            Swal.fire({
                                icon: 'success'
                                , title: 'Success'
                                , text: response.message
                            , }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error'
                                , title: 'Error'
                                , text: response.message
                            , });
                        }
                    }
                    , error: function(xhr) {
                        Swal.fire({
                            icon: 'error'
                            , title: 'Error'
                            , text: xhr.responseText
                        , });
                    }
                });
            });

            $('#AddUnit').on('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('CreateUnit') }}"
                    , type: 'POST'
                    , data: formData
                    , contentType: false
                    , processData: false
                    ,  success: function(response) {
                        if (response.success) {
                            $('#AddStockModal').modal('hide');
                            Swal.fire({
                                icon: 'success'
                                , title: 'Success'
                                , text: response.message
                            , }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error'
                                , title: 'Error'
                                , text: response.message
                            , });
                        }
                    }
                    , error: function(xhr) {
                        Swal.fire({
                            icon: 'error'
                            , title: 'Error'
                            , text: xhr.responseText
                        , });
                    }
                });
            });
        });

    </script>
</body>
@endsection
