@extends('layouts.app')

@section('contents')
<div class="row">
    <div class="col-md-8 offset-md-2">
            <div class="card-header">
                <h5>Edit Product</h5>
            </div>
            <div class="card-body">
                <form id="EditProductForm" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="edit_product_id" id="edit_product_id" value="{{ $product->id }}">

                    <div class="form-group">
                        <label for="product_images">Change Image:</label>
                        <input type="file" name="product_images" class="form-control" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label for="edit_product_name">Product Name:</label>
                        <input type="text" name="product_name" id="edit_product_name" class="form-control"
                            value="{{ $product->product_name }}" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_product_code">Product Code:</label>
                        <input type="text" name="product_code" id="edit_product_code" class="form-control"
                            value="{{ $product->product_code }}" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_product_category">Product Category:</label>
                        <select name="product_category" id="edit_product_category" class="form-control">
                            <option value="makanan" {{ $product->product_category == 'makanan' ? 'selected' : '' }}>Makanan</option>
                            <option value="minuman" {{ $product->product_category == 'minuman' ? 'selected' : '' }}>Minuman</option>
                            <option value="cemilan" {{ $product->product_category == 'cemilan' ? 'selected' : '' }}>Cemilan</option>
                            <option value="other" {{ !in_array($product->product_category, ['makanan', 'minuman', 'cemilan']) ? 'selected' : '' }}>Lainnya</option>
                        </select>

                        <input type="text" name="new_product_category" id="edit_new_product_category"
                            class="form-control mt-2" placeholder="Masukkan kategori baru"
                            style="display: {{ !in_array($product->product_category, ['makanan', 'minuman', 'cemilan']) ? 'block' : 'none' }};"
                            value="{{ !in_array($product->product_category, ['makanan', 'minuman', 'cemilan']) ? $product->product_category : '' }}">
                    </div>

                    <div class="form-group">
                        <label for="edit_product_price">Product Price:</label>
                        <input type="text" name="product_price" id="edit_product_price" class="form-control"
                            value="{{ $product->product_price }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
    </div>
</div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#edit_product_category').on('change', function() {
                if ($(this).val() === 'other') {
                    $('#edit_new_product_category').show();
                } else {
                    $('#edit_new_product_category').hide();
                }
            });

            $('#EditProductForm').on('submit', function(event) {
                event.preventDefault();

                var id = $('#edit_product_id').val();
                var formData = new FormData(this);

                if ($('#edit_product_category').val() === 'other') {
                    let newCategory = $('#edit_new_product_category').val();
                    if (newCategory) {
                        formData.set('product_category', newCategory);
                    }
                }

                $.ajax({
                    url: "/admin/edit/product/" + id,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data berhasil diubah',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            // Redirect after success and after the alert closes
                            window.location.href = '{{ route('CreateProductView') }}';
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal mengubah data',
                            text: xhr.responseText,
                            showConfirmButton: true
                        });
                    }
                });
            });
        });
    </script>
