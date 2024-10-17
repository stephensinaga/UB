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
                            data-target="#createMaterialModal">Create Material</button>
                        <button type="button" class="btn btn-primary receipts-button" data-toggle="modal"
                            data-target="#weeklyReceiptsModal">Weekly Receipts</button>
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
                                    <th>ID</th>
                                    <th>Material</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Price</th>
                                    <th>Grand Total</th>
                                    <th>Information</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($materials as $material)
                                    <tr>
                                        <td>{{ $material->id_material }}</td>
                                        <td>{{ $material->material }}</td>
                                        <td>{{ $material->qty }}</td>
                                        <td>{{ $material->satuan }}</td>
                                        <td>{{ number_format($material->harga, 2, ',', '.') }}</td>
                                        <td>{{ number_format($material->total, 2, ',', '.') }}</td>
                                        <td>{{ $material->keterangan ?? '-' }}</td>
                                        <td>{{ $material->created_at }}</td>
                                        <td>
                                            <a href="{{ route('UpdateView', ['id' => $material->id]) }}"
                                                class="btn btn-sm btn-warning">Update</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal for Create Material -->
            <div class="modal fade" id="createMaterialModal" tabindex="-1" role="dialog"
                aria-labelledby="createMaterialModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createMaterialModalLabel">Create Material</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="javascript:void(0)" id="CreateMaterial" method="POST">
                                @csrf
                                @method('post')
                                <div class="form-group">
                                    <label for="id_material">Material ID</label>
                                    <input type="text" class="form-control" name="id_material" placeholder="Material ID">
                                </div>
                                <div class="form-group">
                                    <label for="material">Material Name</label>
                                    <input type="text" class="form-control" name="material" placeholder="Material Name">
                                </div>
                                <div class="form-group">
                                    <label for="qty">QTY</label>
                                    <input type="number" class="form-control" name="qty" placeholder="QTY">
                                </div>
                                <div class="form-group">
                                    <label for="unit">Unit</label>
                                    <input type="text" class="form-control" name="satuan" placeholder="Unit">
                                </div>
                                <div class="form-group">
                                    <label for="harga">Price</label>
                                    <input type="number" class="form-control" name="harga" placeholder="Price">
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea name="keterangan" class="form-control" cols="30" rows="5"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Create New</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for Weekly Receipts -->
            <div class="modal fade" id="weeklyReceiptsModal" tabindex="-1" role="dialog"
                aria-labelledby="weeklyReceiptsModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="weeklyReceiptsModalLabel">Weekly Receipts</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Content or Form for Weekly Receipts -->
                            <p>Weekly Receipts content goes here...</p>
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
                $('#CreateMaterial').on('submit', function(event) {
                    event.preventDefault();
                    var formData = new FormData(this);

                    $.ajax({
                        url: "{{ route('NewMaterial') }}",
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.success) {
                                $('#createMaterialModal').modal('hide');
                                console.log(response);
                                // location.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            alert('Error: ' + xhr.responseText);
                        }
                    });
                });
            });
        </script>
    </body>
@endsection
