@extends('layouts.app')

@section('contents')

<!-- Basic Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-labelledby="basicModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="basicModalLabel">Add Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
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
                    placeholder="Enter New Category" style="display: none;">
            </div>
            <div class="form-group">
                <label for="product_price">Product Price:</label>
                <input type="text" name="product_price" class="form-control" required>
            </div>
      </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Create Product</button>
            </div>
     </form>
    </div>
  </div>
</div><!-- End Basic Modal-->

<div class="card mt-5 p-3">
    <div class="d-flex justify-content-between align-items-center">
        <form method="GET" action="{{ route('CreateProductView') }}" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search Name or Code" value="{{ request()->search }}">
            <select name="category" class="form-select me-2">
                <option value="">All</option>
                @foreach($category as $cat)
                    <option value="{{ $cat->category }}" {{ request()->category == $cat->category ? 'selected' : '' }}>{{ $cat->category }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal">
            Add Product
        </button>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped">
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
                        <img src="{{ asset($item->product_images) }}" alt="Product Image" width="100">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
                Swal.fire({
                    icon: 'success',
                    title: 'Data Will Be Added',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                        let timerInterval = setInterval(() => {
                            const timer = Swal.getHtmlContainer().querySelector('b');
                            if (timer) {
                                timer.textContent = Swal.getTimerLeft();
                            }
                        }, 100);
                    },
                    willClose: () => {
                        clearInterval(timerInterval);
                    }
                });
                location.reload();

            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Fail Added Data',
                    text: xhr.responseText,
                    showConfirmButton: true
                });
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Data Will Be Delete',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        item.closest('tr').remove(); // Remove the item row after the alert closes
                    });
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed Deleted Data',
                        text: xhr.responseText,
                        showConfirmButton: true
                    });
                }
            });
        });
    });
</script>

@endsection
