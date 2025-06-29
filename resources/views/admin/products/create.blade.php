@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create Product</h1>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back to Products</a>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <!-- Nama Produk -->
            <div class="mb-3">
                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Deskripsi Produk -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Harga dan Stok -->
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">Price (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                               id="price" name="price" value="{{ old('price') }}" 
                               step="0.01" min="0" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                               id="stock" name="stock" value="{{ old('stock') }}" 
                               min="0" required>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Upload Gambar -->
            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                       id="image" name="image" accept="image/*">
                <div class="form-text">Upload gambar produk (JPG, PNG, maksimal 2MB)</div>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Status Aktif -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" 
                       name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    Active (Produk akan tampil di website)
                </label>
            </div>

            <!-- Tombol Submit -->
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary ms-2">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
        
        <!-- Preview Area -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Preview</h5>
                </div>
                <div class="card-body">
                    <div id="image-preview" class="mb-3 text-center">
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                             style="width: 100%; height: 200px; border: 2px dashed #ddd;">
                            <i class="fas fa-image text-muted fa-3x"></i>
                        </div>
                    </div>
                    <div class="product-info">
                        <h6 id="preview-name" class="text-muted">Product Name</h6>
                        <p id="preview-description" class="text-muted small">Product description will appear here...</p>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold text-success" id="preview-price">Rp 0</span>
                            <span class="text-muted small" id="preview-stock">Stock: 0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview gambar
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 200px;">`;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Preview nama produk
    const nameInput = document.getElementById('name');
    const previewName = document.getElementById('preview-name');
    nameInput.addEventListener('input', function() {
        previewName.textContent = this.value || 'Product Name';
    });
    
    // Preview deskripsi
    const descInput = document.getElementById('description');
    const previewDesc = document.getElementById('preview-description');
    descInput.addEventListener('input', function() {
        previewDesc.textContent = this.value || 'Product description will appear here...';
    });
    
    // Preview harga
    const priceInput = document.getElementById('price');
    const previewPrice = document.getElementById('preview-price');
    priceInput.addEventListener('input', function() {
        const price = parseFloat(this.value) || 0;
        previewPrice.textContent = 'Rp ' + price.toLocaleString('id-ID');
    });
    
    // Preview stok
    const stockInput = document.getElementById('stock');
    const previewStock = document.getElementById('preview-stock');
    stockInput.addEventListener('input', function() {
        previewStock.textContent = 'Stock: ' + (this.value || 0);
    });
});
</script>
@endsection