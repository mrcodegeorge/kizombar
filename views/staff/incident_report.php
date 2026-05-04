<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-4">
    <a href="index.php?action=dashboard" class="text-brand text-decoration-none fw-600 mb-2 d-inline-block">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
    <h2 class="fw-bold fs-3 mt-2 mb-0">Report Incident</h2>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
    <div class="alert alert-success py-2 rounded-3 text-center fw-500 mb-4" role="alert">
        <i class="fas fa-check-circle me-1"></i> Incident reported successfully!
    </div>
<?php endif; ?>

<?php if (isset($formError) && is_array($formError) && !$formError['success']): ?>
    <div class="alert alert-danger py-2 rounded-3 text-center fw-500 mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($formError['message']); ?>
    </div>
<?php endif; ?>

<form action="index.php?action=incident_report" method="POST" enctype="multipart/form-data">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                
                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">Incident Type</label>
                    <select name="type" class="form-select" required>
                        <option value="" disabled selected>Select type...</option>
                        <option value="equipment_failure">🔧 Equipment Failure</option>
                        <option value="customer_issue">👤 Customer Issue</option>
                        <option value="safety_hazard">⚠️ Safety Hazard</option>
                        <option value="other">📋 Other</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">Severity</label>
                    <div class="row g-2">
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="severity" id="sev_low" value="low" checked>
                            <label class="btn btn-outline-success w-100 fw-600" for="sev_low">Low</label>
                        </div>
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="severity" id="sev_medium" value="medium">
                            <label class="btn btn-outline-warning w-100 fw-600" for="sev_medium">Med</label>
                        </div>
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="severity" id="sev_high" value="high">
                            <label class="btn btn-outline-danger w-100 fw-600" for="sev_high">High</label>
                        </div>
                        <div class="col-3">
                            <input type="radio" class="btn-check" name="severity" id="sev_critical" value="critical">
                            <label class="btn btn-outline-dark w-100 fw-600" for="sev_critical">🚨</label>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">Description</label>
                    <textarea name="description" class="form-control" rows="4" required placeholder="Describe what happened in detail..."></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">Photo (Optional)</label>
                    <label class="btn btn-outline-secondary w-100 py-3 d-block fw-600 m-0">
                        <i class="fas fa-camera me-2"></i> Take / Upload Photo
                        <input type="file" name="photo" class="d-none" accept="image/*" capture="environment">
                    </label>
                    <div id="photoPreview" class="mt-2 text-muted small text-center d-none">
                        <i class="fas fa-check text-success me-1"></i> Photo selected
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="sticky-bottom-action">
        <button type="submit" class="btn btn-danger w-100 py-3 fs-5 shadow fw-bold">
            <i class="fas fa-exclamation-triangle me-2"></i> Submit Report
        </button>
    </div>
</form>

<script>
document.querySelector('input[name="photo"]').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        document.getElementById('photoPreview').classList.remove('d-none');
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
