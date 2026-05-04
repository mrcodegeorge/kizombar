<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-4">
    <h1 class="fw-bold fs-2 mb-1">Good morning, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?></h1>
    <p class="text-muted mb-0 fw-500"><?php echo date('l, M j'); ?></p>
</div>

<h3 class="fs-5 fw-600 mb-3 text-muted text-uppercase" style="letter-spacing: 1px;">Today's Tasks</h3>

<div class="row g-3">
    <?php foreach ($tasks as $task): ?>
    <div class="col-12">
        <a href="index.php?action=checklist&sop_id=<?php echo $task['sop_id']; ?>&shift=<?php echo $task['shift']; ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-light text-dark border fw-600 px-3 py-2 rounded-pill">
                            <?php echo htmlspecialchars($task['category']); ?>
                        </span>
                        <span class="status-badge <?php echo $task['status']; ?>">
                            <?php echo ucfirst($task['status']); ?>
                        </span>
                    </div>
                    
                    <h4 class="fw-bold text-dark mb-1 fs-5"><?php echo htmlspecialchars($task['title']); ?></h4>
                    <p class="text-muted small mb-0"><i class="far fa-clock me-1"></i> <?php echo ucfirst($task['shift']); ?> Shift</p>
                    
                    <div class="mt-4 d-flex justify-content-end">
                        <span class="btn btn-outline-primary rounded-pill fw-600 px-4">
                            <?php echo $task['status'] === 'completed' ? 'Review' : 'Start Task'; ?> <i class="fas fa-arrow-right ms-2"></i>
                        </span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($tasks)): ?>
<div class="text-center py-5 mt-4 bg-white rounded-4 border">
    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
        <i class="fas fa-glass-cheers fa-2x text-muted"></i>
    </div>
    <h4 class="fw-bold text-dark">All Caught Up!</h4>
    <p class="text-muted px-4">You have no tasks assigned for today. Enjoy your shift!</p>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
