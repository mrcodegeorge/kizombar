<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold fs-3 m-0">Incident Reports</h2>
    <span class="badge bg-danger px-3 py-2 rounded-pill"><?php echo count($incidents); ?> Total</span>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <?php foreach ($incidents as $inc): 
            $sevColor = match($inc['severity']) {
                'critical' => 'danger',
                'high' => 'danger',
                'medium' => 'warning',
                default => 'success'
            };
            $typeLabel = match($inc['type']) {
                'equipment_failure' => '🔧 Equipment',
                'customer_issue' => '👤 Customer',
                'safety_hazard' => '⚠️ Safety',
                default => '📋 Other'
            };
        ?>
        <div class="p-3 border-bottom border-light">
            <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-1">
                        <span class="fw-bold text-dark me-2"><?php echo $typeLabel; ?></span>
                        <span class="badge bg-<?php echo $sevColor; ?> bg-opacity-<?php echo $inc['severity'] === 'critical' ? '100' : '15'; ?> text-<?php echo $sevColor; ?> border border-<?php echo $sevColor; ?> border-opacity-25 rounded-pill px-2">
                            <?php echo ucfirst($inc['severity']); ?>
                        </span>
                        <?php if ($inc['zoho_synced']): ?>
                            <span class="badge bg-light text-muted border ms-2 px-2 py-1 rounded-pill" style="font-size: 0.65rem;">CRM ✓</span>
                        <?php endif; ?>
                    </div>
                    <p class="mb-1 text-dark small" style="max-width: 300px;"><?php echo htmlspecialchars(substr($inc['description'], 0, 100)) . (strlen($inc['description']) > 100 ? '...' : ''); ?></p>
                    <p class="mb-0 text-muted" style="font-size: 0.75rem;">
                        <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($inc['reporter_name']); ?>
                        <span class="ms-2"><i class="far fa-clock me-1"></i> <?php echo date('M j, H:i', strtotime($inc['created_at'])); ?></span>
                    </p>
                </div>
                <?php if ($inc['image_path']): ?>
                <a href="<?php echo htmlspecialchars($inc['image_path']); ?>" target="_blank" class="ms-2 flex-shrink-0">
                    <img src="<?php echo htmlspecialchars($inc['image_path']); ?>" alt="Incident photo" class="rounded" style="width: 56px; height: 56px; object-fit: cover;">
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($incidents)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shield-alt text-success" style="font-size: 3rem;"></i>
            <h5 class="fw-bold mt-3">No incidents reported</h5>
            <p class="text-muted mb-0 fw-500">All clear!</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
