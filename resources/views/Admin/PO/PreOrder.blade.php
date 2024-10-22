@extends('layouts.app')

@section('contents')
<div class="container mt-5">
    <!-- Card for Input -->
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-primary text-white py-4">
            <h5 class="card-title mb-0">Input Product</h5>
        </div>
        <div class="card-body mt-3">
            <form>
                <div class="row">
                    <!-- Product Name -->
                    <div class="col-md-6 mb-3">
                        <label for="product" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="product" placeholder="Enter product name">
                    </div>
                    <!-- Quantity -->
                    <div class="col-md-3 mb-3">
                        <label for="qty" class="form-label">Quantity (Qty)</label>
                        <input type="number" class="form-control" id="qty" placeholder="Enter quantity">
                    </div>
                    <!-- Price -->
                    <div class="col-md-3 mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="text" class="form-control" id="price" placeholder="Enter price">
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" rows="3" placeholder="Enter additional information"></textarea>
                    </div>
                </div>

                <!-- Button to Trigger Modal and Submit Button -->
                <div class="d-flex justify-content-between">
                    <!-- Button to Trigger Modal -->
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#productListModal">
                        View Product List
                    </button>
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Create Pre Order</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Product List -->
    <div class="modal fade" id="productListModal" tabindex="-1" aria-labelledby="productListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header with Similar Style -->
                <div class="modal-header bg-gradient-primary text-white py-3">
                    <h5 class="modal-title" id="productListModalLabel">Product List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body mt-3">
                    <div class="row">
                        @foreach ($product as $item)
                        <div class="col-md-6 mb-4">
                            <!-- Product List Card Styled Similar to Input Product -->
                            <div class="card shadow-sm">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 text-truncate">{{ $item->product_name }}</h6>
                                </div>
                                <div class="card-body d-flex align-items-center" style="height: 200px;">
                                    <div class="card-icon d-flex align-items-center justify-content-center"
                                        style="width: 150px; height: 150px;">
                                        @if ($item->product_images)
                                        <img src="{{ asset('storage/' . $item->product_images) }}"
                                            alt="Product Image" class="img-fluid" style="object-fit: cover;">
                                        @else
                                        <i class="bi bi-cart" style="font-size: 3rem;"></i>
                                        @endif
                                    </div>
                                    <div class="ps-3">
                                        <h6 class="product-price">Rp {{ number_format($item->product_price) }}</h6>
                                        <p class="text-muted small product-code">{{ $item->product_code }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ensure to include Bootstrap JS if not already included in your layout -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection
