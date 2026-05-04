<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-4">
    <a href="index.php?action=dashboard" class="text-brand text-decoration-none fw-600 mb-2 d-inline-block">
        <i class="fas fa-arrow-left me-2"></i> Dashboard
    </a>
    <h2 class="fw-bold fs-3 mt-2 mb-0">Pending Sign-offs</h2>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <?php foreach ($pending_signoffs as $log): ?>
        <div class="p-3 border-bottom border-light">
            <div class="mb-2">
                <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($log['title']); ?></h6>
                <p class="mb-0 text-muted small">
                    <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($log['user_name']); ?>
                    <span class="ms-2"><i class="far fa-calendar-alt me-1"></i> <?php echo date('M j, Y', strtotime($log['date'])); ?></span>
                    <span class="ms-2 badge bg-light text-dark border"><?php echo ucfirst($log['shift']); ?></span>
                </p>
            </div>
            <div class="d-flex gap-2">
                <form method="POST" action="index.php?action=approve_sop" class="flex-grow-1">
                    <input type="hidden" name="log_id" value="<?php echo $log['id']; ?>">
                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                        <i class="fas fa-check me-1"></i> Approve
                    </button>
                </form>
                <form method="POST" action="index.php?action=reject_sop" class="flex-grow-1">
                    <input type="hidden" name="log_id" value="<?php echo $log['id']; ?>">
                    <input type="text" name="note" class="form-control form-control-sm mb-2" placeholder="Rejection reason (optional)">
                    <button type="submit" class="btn btn-outline-danger w-100 py-2 fw-bold">
                        <i class="fas fa-times me-1"></i> Reject
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($pending_signoffs)): ?>
        <div class="text-center py-5">
            <i class="fas fa-thumbs-up text-success" style="font-size: 3rem;"></i>
            <h5 class="fw-bold mt-3">All clear!</h5>
            <p class="text-muted mb-0 fw-500">No SOPs awaiting sign-off.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
