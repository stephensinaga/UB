@extends('layouts.app')

@section('contents')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header border-0" style="background-color: #f8f9fa;">
                <h3 class="text-center" style="font-weight: bold; color: #333;">Customer Order PO</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Customer Contact</th>
                            <th class="text-center">Information</th>
                            <th class="text-center">Grandtotal</th>
                            <th class="text-center">Payment</th>
                            <th class="text-center">Progress</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td class="text-center">{{ $order->customer }}</td>
                                <td class="text-center">{{ $order->customer_contact }}</td>
                                <td class="text-center">{{ $order->information ?? '---' }}</td>
                                <td class="text-center">Rp {{ number_format($order->grandtotal, 0, ',', '.') }}</td>
                                <td class="text-center">{{ ucfirst($order->payment) }}</td>
                                <td class="text-center">{{ ucfirst($order->progress) }}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm me-1">Edit</button>
                                    <form action="{{ route('DeletePO', ['id' => $order->id]) }}" method="post"
                                        class="delete-form d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="button"
                                            class="btn btn-danger btn-sm me-1 delete-button">Delete</button>
                                    </form>
                                    <button class="btn btn-warning btn-sm">Update Progress</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tambahkan SweetAlert2 dan script konfirmasi langsung di sini -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pilih semua tombol delete
            const deleteButtons = document.querySelectorAll('.delete-button');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('.delete-form');

                    // Tampilkan konfirmasi SweetAlert
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
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
