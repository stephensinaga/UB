@extends('layouts.app')

@section('contents')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header border-0 bg-light">
            <h3 class="text-center font-weight-bold text-dark">Edit Pre Order</h3>
        </div>
        <div class="card-body">
            <!-- Form for Pre Order Details -->
            <form action="{{ route('UpdatePo', ['id' => $PoCustomer->id]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <!-- Customer Details -->
                <div class="form-group">
                    <label for="customer">Customer</label>
                    <input type="text" id="customer" name="customer" class="form-control"
                        value="{{ $PoCustomer->customer }}" required>
                </div>

                <div class="form-group">
                    <label for="customer_contact">Customer Contact</label>
                    <input type="text" id="customer_contact" name="customer_contact" class="form-control"
                        value="{{ $PoCustomer->customer_contact }}" required>
                </div>

                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea id="keterangan" name="keterangan"
                        class="form-control">{{ $PoCustomer->keterangan }}</textarea>
                </div>

                <div class="form-group">
                    <label for="total_price">GrandTotal (Rp)</label>
                    <input type="text" id="total_price" name="total_price" class="form-control"
                        value="{{ number_format($PoCustomer->total_price, 0, ',', '.') }}" readonly>
                </div>
                @if ($PoCustomer->payment ==='cash')
                <div class="form-group">
                    <label for="cashgiven">Cash Given (Rp)</label>
                    <input type="text" id="cash" name="cash" class="form-control"
                        value="{{ number_format($PoCustomer->cash, 0, ',', '.') }}" readonly>
                </div>
                @else
                <img src="{{ asset('storage/' . $PoCustomer->transfer_img) }}" alt="Product Image" width="100">
                @endif

                <div class="form-group">
                    <label for="progress">Progress</label>
                    <select id="progress" name="progress" class="form-control">
                        <option value="pending" {{ $PoCustomer->progress == 'pending' ? 'selected' : '' }}>Pending
                        </option>
                        <option value="onProgress" {{ $PoCustomer->progress == 'onProgress' ? 'selected' : '' }}>In
                            Progress</option>
                        <option value="done" {{ $PoCustomer->progress == 'done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>

                <!-- Table for Pre Order Items -->
                <h4 class="mt-4">Pre Order Items</h4>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Product</th>
                            <th class="text-center">Unit</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Price (Rp)</th>
                            <th class="text-center">Grandtotal (Rp)</th>
                            <th class="text-center">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($PoDetail as $item)
                        <tr>
                            <td class="text-center">
                                <input type="text" name="PoDetail[{{ $item->id }}][product]" class="form-control"
                                    value="{{ $item->product }}" readonly>
                            </td>
                            <td class="text-center">
                                <input type="text" name="PoDetail[{{ $item->id }}][unit]" class="form-control"
                                    value="{{ $item->unit }}" readonly>
                            </td>
                            <td class="text-center">
                                <input type="number" name="PoDetail[{{ $item->id }}][qty]" class="form-control"
                                    value="{{ $item->qty }}" readonly>
                            </td>
                            <td class="text-center">
                                <input type="text" name="PoDetail[{{ $item->id }}][price]" class="form-control"
                                    value="{{ number_format($item->price, 0, ',', '.') }}" readonly>
                            </td>
                            <td class="text-center">
                                <input type="text" name="PoDetail[{{ $item->id }}][grandtotal]" class="form-control"
                                    value="{{ number_format($item->grandtotal, 0, ',', '.') }}" readonly>
                            </td>
                            <td class="text-center">
                                <input type="text" name="PoDetail[{{ $item->id }}][keteranganOrder]"
                                    class="form-control" value="{{ $item->keterangan }}">
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Update Pre Order</button>
                </div>
            </form>
        </div>
    </div>
</div>


@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('PoList') }}";
            }
        });

        // Auto-close alert after timeout (optional)
        setTimeout(() => {
            window.location.href = "{{ route('PoList') }}";
        }, 3000); // 3000 ms = 3 seconds
    });
</script>
@endif
@endsection