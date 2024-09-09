<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Create Product</title>
</head>

<body>
    <div class="container mt-5">
        <div class="card p-3">
            <h3 class="mb-4">Create Product</h3>
            <form action="javascript:void(0)" method="post" enctype="multipart/form-data" id="CreateProduct">
                @method('POST')
                @csrf
                <div class="form-group">
                    <label for="product_images">Product Image:</label>
                    <input type="file" name="product_images" class="form-control" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="product_name">Product Name:</label>
                    <input type="text" name="product_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="product_code">Product Code:</label>
                    <input type="text" name="product_code" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="product_category">Product Category:</label>
                    <select name="product_category" class="form-control" id="product_category">
                        <option value=""> --> Pilih Category <-- </option>
                                @foreach ($category as $item)
                        <option value="{{ $item->category }}">{{ $item->category }}</option>
                        @endforeach
                        <option value="other">Lainnya</option>
                    </select>
                    <input type="text" name="new_product_category" id="new_product_category" class="form-control mt-2"
                        placeholder="Masukkan kategori baru" style="display: none;">
                </div>
                <div class="form-group">
                    <label for="product_price">Product Price:</label>
                    <input type="text" name="product_price" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Create Product</button>
                </div>
            </form>
        </div>

        <div class="card mt-5 p-3">
            <h2>Product List</h2>
            <table class="table table-bordered table-striped mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Product Code</th>
                        <th>Product Category</th>
                        <th>Product Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($item->product_images)
                            <img src="{{ asset('storage/' . $item->product_images) }}" alt="Product Image" width="100">
                            @else
                            No Image
                            @endif
                        </td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->product_category }}</td>
                        <td>{{ number_format($item->product_price, 2) }}</td>
                        <td>
                            <form action="javascript:void(0)" method="delete" class="DeleteProduct"
                                data-id="{{ $item->id }}">
                                @csrf
                                @method('delete')
                                <button type="submit" style="border: none; background: none; cursor: pointer;">
                                    <i class="fa fa-solid fa-trash"></i>
                                </button>
                            </form>
                            <a href="{{ route('EditProductView', ['id' => $item->id]) }}">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>



    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#product_category').on('change', function () {
                if ($(this).val() === 'other') {
                    $('#new_product_category').show();
                } else {
                    $('#new_product_category').hide();
                }
            });

            $('#CreateProduct').on('submit', function (event) {
                event.preventDefault();

                let formData = new FormData(this);

                if ($('#product_category').val() === 'other') {
                    let newCategory = $('#new_product_category').val();
                    if (newCategory) {
                        formData.set('product_category', newCategory);
                    }
                }

                $.ajax({
                    url: "{{ route('CreateProductProcess') }}",
                    data: formData,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    success: function (result) {
                        alert('Data berhasil di tambahkan');
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
                        alert('Gagal menambahkan data');
                    }
                });
            });

            $('.DeleteProduct').on('submit', function (event) {
        event.preventDefault();

        var item = $(this);
        var id = item.data('id');
        var url = "/admin/delete/product/" + id;

        $.ajax({
            url: url,
            type: 'DELETE',
            data: item.serialize(),
            success: function (result) {
                alert('Data berhasil dihapus');
                item.closest('tr').remove();
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText);
                alert('Gagal menghapus data');
            }
        });
    });

        });
    </script>
</body>

</html>
