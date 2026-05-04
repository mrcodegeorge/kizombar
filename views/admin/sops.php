<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold fs-3 m-0">SOP Management</h2>
    <a href="index.php?action=sop_edit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
        <i class="fas fa-plus me-1"></i> New
    </a>
</div>

<div class="row g-3">
    <?php foreach ($sops as $sop): ?>
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-light text-brand border fw-600 px-3 py-2 rounded-pill">
                        <?php echo htmlspecialchars($sop['category']); ?>
                    </span>
                    <div class="dropdown">
                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown" style="min-height: auto;">
                            <i class="fas fa-ellipsis-v p-2"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                            <li><a class="dropdown-item fw-500" href="index.php?action=sop_edit&id=<?php echo $sop['id']; ?>"><i class="fas fa-pen me-2 text-muted"></i> Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item fw-500 text-danger" href="index.php?action=sop_delete&id=<?php echo $sop['id']; ?>" onclick="return confirm('Are you sure?')"><i class="fas fa-trash me-2"></i> Delete</a></li>
                        </ul>
                    </div>
                </div>
                
                <h4 class="card-title fw-bold text-dark fs-5 mb-2"><?php echo htmlspecialchars($sop['title']); ?></h4>
                <p class="card-text text-muted small mb-3 lh-sm"><?php echo htmlspecialchars(substr($sop['description'], 0, 80)) . '...'; ?></p>
                
                <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-auto">
                    <small class="text-muted fw-500"><i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($sop['creator_name']); ?></small>
                    <a href="index.php?action=sop_edit&id=<?php echo $sop['id']; ?>" class="text-brand fw-600 small text-decoration-none">Manage Steps <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($sops)): ?>
<div class="text-center py-5 mt-4 bg-white rounded-4 border shadow-sm">
    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
        <i class="fas fa-tasks fa-2x text-muted"></i>
    </div>
    <h4 class="fw-bold text-dark">No SOPs Found</h4>
    <p class="text-muted px-4 mb-4">You haven't created any Standard Operating Procedures yet.</p>
    <a href="index.php?action=sop_edit" class="btn btn-primary rounded-pill px-4 py-2 fw-600">Create First SOP</a>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
