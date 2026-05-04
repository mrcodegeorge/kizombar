<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="index.php?action=dashboard" class="text-brand text-decoration-none fw-600 mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-2"></i> Dashboard
        </a>
        <h2 class="fw-bold fs-3 mt-2 mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Missed SOPs</h2>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <?php foreach ($missed_sops as $log): ?>
        <div class="d-flex align-items-center p-3 border-bottom border-light">
            <div class="me-3">
                <div class="avatar-circle bg-danger bg-opacity-10 text-danger" style="width: 45px; height: 45px; font-size: 1.2rem;">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($log['title']); ?></h6>
                <p class="mb-0 text-muted small">
                    <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($log['staff_name']); ?>
                    <span class="ms-2"><i class="far fa-calendar-alt me-1"></i> <?php echo date('M j, Y', strtotime($log['date'])); ?> (<?php echo ucfirst($log['shift']); ?>)</span>
                </p>
            </div>
            <div class="text-end ms-2">
                <span class="badge bg-danger text-white border px-2 py-1 rounded-pill">
                    <?php echo htmlspecialchars($log['category']); ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($missed_sops)): ?>
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
            </div>
            <h5 class="fw-bold">All clear!</h5>
            <p class="text-muted mb-0 fw-500">No missed SOPs found.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
