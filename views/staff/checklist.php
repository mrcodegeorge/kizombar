<?php include __DIR__ . '/../layouts/header.php'; ?>

<?php
// Inventory Low-Stock Alert for this SOP category
require_once __DIR__ . '/../../src/Models/Inventory.php';
$inv = new Inventory($db ?? (new Database())->getConnection());
$lowStockItems = $inv->getLowStockByCategory($sop['category'] ?? '');
?>

<div class="mb-3">
    <a href="index.php?action=dashboard" class="text-brand text-decoration-none fw-600 mb-2 d-inline-block">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
    <h2 class="fw-bold mt-2 mb-1 fs-3"><?php echo htmlspecialchars($sop['title']); ?></h2>
    <p class="text-muted small fw-500"><i class="far fa-clock me-1"></i> <?php echo ucfirst($log['shift']); ?> Shift</p>
</div>

<?php if (!empty($lowStockItems)): ?>
<div class="alert alert-danger border-0 rounded-3 mb-3 py-2 px-3" role="alert">
    <p class="fw-bold mb-1"><i class="fas fa-boxes me-1"></i> Low Stock Alert</p>
    <?php foreach ($lowStockItems as $ls): ?>
    <p class="mb-0 small">⚠️ <strong><?php echo htmlspecialchars($ls['name']); ?></strong>: <?php echo $ls['current_stock']; ?> <?php echo $ls['unit']; ?> remaining (min: <?php echo $ls['min_threshold']; ?>)</p>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Progress Bar -->
<?php 
$total = count($steps);
$completed_count = 0;
foreach ($steps as $s) if ($s['completed']) $completed_count++;
$percent = ($total > 0) ? ($completed_count / $total) * 100 : 0;
?>
<div class="mb-4">
    <div class="d-flex justify-content-between mb-1">
        <span class="text-muted fw-600 small text-uppercase">Progress</span>
        <span class="text-brand fw-bold small" id="progress-text"><?php echo $completed_count; ?> / <?php echo $total; ?></span>
    </div>
    <div class="progress" style="height: 12px; border-radius: 6px; background-color: #E5E7EB;">
        <div id="progress-bar" class="progress-bar bg-success" role="progressbar" style="width: <?php echo $percent; ?>%; transition: width 0.3s ease;"></div>
    </div>
</div>

<div class="checklist-container mb-5 pb-5">
    <?php foreach ($steps as $step): ?>
    <div class="checklist-item-wrapper mb-3">
        <div class="checklist-row <?php echo $step['completed'] ? 'completed' : ''; ?> <?php echo ($step['requires_photo'] && !$step['image_path'] && !$step['completed']) ? 'requires-photo-pending' : ''; ?>" data-step-id="<?php echo $step['step_id']; ?>">
            <input type="checkbox" class="step-checkbox d-none" id="step-<?php echo $step['step_id']; ?>" <?php echo $step['completed'] ? 'checked' : ''; ?> <?php echo ($step['requires_photo'] && !$step['image_path'] && !$step['completed']) ? 'disabled' : ''; ?>>
            <div class="custom-checkbox">
                <i class="fas fa-check" style="display: none;"></i>
            </div>
            <div class="step-text fw-500 fs-5">
                <?php echo htmlspecialchars($step['step_text']); ?>
            </div>
        </div>
        
        <?php if ($step['requires_photo']): ?>
            <div class="photo-upload-section bg-white border border-top-0 rounded-bottom p-3 shadow-sm <?php echo $step['completed'] ? 'd-none' : ''; ?>">
                <?php if ($step['image_path']): ?>
                    <div class="d-flex align-items-center text-success fw-500">
                        <i class="fas fa-image me-2 fs-4"></i> Photo uploaded!
                    </div>
                <?php else: ?>
                    <label class="btn btn-outline-brand w-100 py-2 d-block fw-600 border-dashed m-0">
                        <i class="fas fa-camera me-2"></i> Upload Photo Proof
                        <input type="file" class="d-none photo-upload-input" accept="image/*" capture="environment" data-step-id="<?php echo $step['step_id']; ?>">
                    </label>
                    <div class="upload-progress d-none mt-2">
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-brand" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<div class="sticky-bottom-action">
    <?php if (!empty($sop['requires_signoff']) && $log['signoff_status'] === null): ?>
        <button id="submitSopBtn" class="btn btn-warning w-100 py-3 fs-5 shadow fw-bold" <?php echo ($completed_count === $total && $total > 0) ? '' : 'disabled'; ?>>
            <i class="fas fa-signature me-2"></i> Submit for Approval
        </button>
    <?php elseif (!empty($sop['requires_signoff']) && $log['signoff_status'] === 'pending'): ?>
        <button class="btn btn-secondary w-100 py-3 fs-5 shadow fw-bold" disabled>
            <i class="fas fa-clock me-2"></i> Awaiting Sign-off
        </button>
    <?php elseif (!empty($sop['requires_signoff']) && $log['signoff_status'] === 'rejected'): ?>
        <button class="btn btn-danger w-100 py-3 fs-5 shadow fw-bold" disabled>
            <i class="fas fa-times me-2"></i> Sign-off Rejected
        </button>
    <?php else: ?>
        <button id="submitSopBtn" class="btn btn-primary w-100 py-3 fs-5 shadow" <?php echo ($completed_count === $total && $total > 0) ? '' : 'disabled'; ?>>
            Submit SOP
        </button>
    <?php endif; ?>
</div>

<script>
    window.sopLogId = <?php echo $log['id']; ?>;
    window.sopTotalSteps = <?php echo $total; ?>;
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
