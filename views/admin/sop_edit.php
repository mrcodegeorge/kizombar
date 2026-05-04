<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-4">
    <a href="index.php?action=sops" class="text-brand text-decoration-none fw-600 mb-2 d-inline-block">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
    <h2 class="fw-bold fs-3 mt-2 mb-0"><?php echo isset($sop['id']) ? 'Edit SOP' : 'Create SOP'; ?></h2>
</div>

<form action="index.php?action=sop_save" method="POST">
    <?php if (isset($sop['id'])): ?>
        <input type="hidden" name="id" value="<?php echo $sop['id']; ?>">
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">SOP Title</label>
                    <input type="text" name="title" class="form-control" 
                           value="<?php echo htmlspecialchars($sop['title'] ?? ''); ?>" required placeholder="e.g. Morning Bar Opening">
                </div>
                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="Bar" <?php echo (isset($sop['category']) && $sop['category'] == 'Bar') ? 'selected' : ''; ?>>Bar</option>
                        <option value="Kitchen" <?php echo (isset($sop['category']) && $sop['category'] == 'Kitchen') ? 'selected' : ''; ?>>Kitchen</option>
                        <option value="Café" <?php echo (isset($sop['category']) && $sop['category'] == 'Café') ? 'selected' : ''; ?>>Café</option>
                        <option value="Cleaning" <?php echo (isset($sop['category']) && $sop['category'] == 'Cleaning') ? 'selected' : ''; ?>>Cleaning</option>
                        <option value="Service" <?php echo (isset($sop['category']) && $sop['category'] == 'Service') ? 'selected' : ''; ?>>Service</option>
                        <option value="Inventory" <?php echo (isset($sop['category']) && $sop['category'] == 'Inventory') ? 'selected' : ''; ?>>Inventory</option>
                        <option value="Finance" <?php echo (isset($sop['category']) && $sop['category'] == 'Finance') ? 'selected' : ''; ?>>Finance</option>
                        <option value="Staff" <?php echo (isset($sop['category']) && $sop['category'] == 'Staff') ? 'selected' : ''; ?>>Staff</option>

                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">Branch</label>
                    <select name="branch" class="form-select" required>
                        <option value="all" <?php echo (!isset($sop['branch']) || $sop['branch'] == 'all') ? 'selected' : ''; ?>>All Branches</option>
                        <option value="kizobar" <?php echo (isset($sop['branch']) && $sop['branch'] == 'kizobar') ? 'selected' : ''; ?>>Kizobar</option>
                        <option value="kizo_cafe" <?php echo (isset($sop['branch']) && $sop['branch'] == 'kizo_cafe') ? 'selected' : ''; ?>>Kizo Café</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">Description</label>
                    <textarea name="description" class="form-control" rows="3" 
                              placeholder="Brief overview of this SOP..."><?php echo htmlspecialchars($sop['description'] ?? ''); ?></textarea>
                </div>
                <div class="col-12">
                    <div class="card border-0 bg-light rounded-3 p-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="requires_signoff" id="requires_signoff" value="1" 
                                   <?php echo !empty($sop['requires_signoff']) ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-600 text-dark" for="requires_signoff">
                                Requires Supervisor Sign-off
                                <small class="d-block text-muted fw-400">Staff must submit for admin approval when complete</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold fs-5 m-0">Checklist Steps</h4>
        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-600 px-3" id="add-step" style="min-height: auto;">
            <i class="fas fa-plus me-1"></i> Add Step
        </button>
    </div>

    <div id="steps-container" class="mb-4">
        <?php 
        $steps = $sop['steps'] ?? [['step_text' => '']];
        foreach ($steps as $index => $step): 
        ?>
        <div class="card border-0 shadow-sm mb-2 step-item">
            <div class="card-body p-2 px-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="me-2 text-muted drag-handle p-2"><i class="fas fa-grip-lines"></i></div>
                    <input type="text" name="steps[]" class="form-control border-0 shadow-none px-2" 
                           value="<?php echo htmlspecialchars($step['step_text']); ?>" placeholder="Step description..." required>
                    <button type="button" class="btn btn-link text-danger ms-1 remove-step p-2" style="min-height: auto;">
                        <i class="fas fa-times-circle fs-5"></i>
                    </button>
                </div>
                <div class="d-flex align-items-center ms-4 ps-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="requires_photo[<?php echo $index; ?>]" value="1" <?php echo !empty($step['requires_photo']) ? 'checked' : ''; ?>>
                        <label class="form-check-label text-muted small fw-500">Requires Photo Proof</label>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="sticky-bottom-action">
        <button type="submit" class="btn btn-primary w-100 py-3 fs-5 shadow">
            Save SOP
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('steps-container');
    const addButton = document.getElementById('add-step');

    addButton.addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'card border-0 shadow-sm mb-2 step-item';
        div.innerHTML = `
            <div class="card-body p-2 px-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="me-2 text-muted drag-handle p-2"><i class="fas fa-grip-lines"></i></div>
                    <input type="text" name="steps[]" class="form-control border-0 shadow-none px-2" placeholder="Step description..." required>
                    <button type="button" class="btn btn-link text-danger ms-1 remove-step p-2" style="min-height: auto;">
                        <i class="fas fa-times-circle fs-5"></i>
                    </button>
                </div>
                <div class="d-flex align-items-center ms-4 ps-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input new-step-photo-toggle" type="checkbox" value="1">
                        <label class="form-check-label text-muted small fw-500">Requires Photo Proof</label>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(div);
        updateStepIndexes();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-step')) {
            const items = document.querySelectorAll('.step-item');
            if (items.length > 1) {
                e.target.closest('.step-item').remove();
                updateStepIndexes();
            } else {
                showToast('At least one step is required.');
            }
        }
    });

    function updateStepIndexes() {
        const items = document.querySelectorAll('.step-item');
        items.forEach((item, index) => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.name = `requires_photo[${index}]`;
            }
        });
    }
    
    // Initial call to ensure everything is indexed properly
    updateStepIndexes();
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
