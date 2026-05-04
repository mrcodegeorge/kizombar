<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-4">
    <a href="index.php?action=inventory" class="text-brand text-decoration-none fw-600 mb-2 d-inline-block">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
    <h2 class="fw-bold fs-3 mt-2 mb-0">Add Inventory Item</h2>
</div>

<form action="index.php?action=inventory_add" method="POST">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">Item Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Vodka">
                </div>
                <div class="col-6">
                    <label class="form-label fw-600 text-dark mb-2">Unit</label>
                    <input type="text" name="unit" class="form-control" required placeholder="e.g. L, kg, units">
                </div>
                <div class="col-6">
                    <label class="form-label fw-600 text-dark mb-2">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="Bar">Bar</option>
                        <option value="Kitchen">Kitchen</option>
                        <option value="Café">Café</option>
                        <option value="Cleaning">Cleaning</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label fw-600 text-dark mb-2">Current Stock</label>
                    <input type="number" name="stock" class="form-control" required placeholder="0" step="0.5" min="0">
                </div>
                <div class="col-6">
                    <label class="form-label fw-600 text-dark mb-2">Min Threshold</label>
                    <input type="number" name="threshold" class="form-control" required placeholder="0" step="0.5" min="0">
                </div>
            </div>
        </div>
    </div>
    <div class="sticky-bottom-action">
        <button type="submit" class="btn btn-primary w-100 py-3 fs-5 shadow">Add Item</button>
    </div>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
