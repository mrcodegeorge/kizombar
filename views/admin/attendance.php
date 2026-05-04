<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-4">
    <h2 class="fw-bold fs-3 mb-1">Attendance & Liveness</h2>
    <p class="text-muted small fw-500">Review staff clock-in verifications</p>
</div>

<!-- Branch Toggle -->
<div class="mb-4 overflow-auto pb-1" style="white-space: nowrap;">
    <div class="d-inline-flex gap-2 p-1 bg-white rounded-pill shadow-sm border">
        <a href="index.php?action=set_branch&branch=all" class="btn rounded-pill px-3 py-1 fw-600 <?php echo (!isset($_SESSION['active_branch']) || $_SESSION['active_branch'] === 'all') ? 'btn-primary' : 'btn-light text-muted'; ?>" style="font-size: 0.85rem;">All Branches</a>
        <a href="index.php?action=set_branch&branch=kizobar" class="btn rounded-pill px-3 py-1 fw-600 <?php echo (isset($_SESSION['active_branch']) && $_SESSION['active_branch'] === 'kizobar') ? 'btn-primary' : 'btn-light text-muted'; ?>" style="font-size: 0.85rem;">Kizobar</a>
        <a href="index.php?action=set_branch&branch=kizo_cafe" class="btn rounded-pill px-3 py-1 fw-600 <?php echo (isset($_SESSION['active_branch']) && $_SESSION['active_branch'] === 'kizo_cafe') ? 'btn-primary' : 'btn-light text-muted'; ?>" style="font-size: 0.85rem;">Kizo Café</a>
    </div>
</div>

<div class="row g-3">
    <?php foreach ($logs as $log): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-circle me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                            <?php echo strtoupper(substr($log['staff_name'], 0, 1)); ?>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($log['staff_name']); ?></h6>
                            <p class="text-muted small mb-0">
                                <?php echo ucfirst(str_replace('_', ' ', $log['type'])); ?> • 
                                <?php echo date('M d, H:i', strtotime($log['timestamp'])); ?>
                            </p>
                        </div>
                        <div id="statusBadge-<?php echo $log['id']; ?>">
                            <?php if ($log['verification_status'] === 'pending'): ?>
                                <span class="badge bg-warning rounded-pill px-3">Pending Review</span>
                            <?php elseif ($log['verification_status'] === 'approved'): ?>
                                <span class="badge bg-success rounded-pill px-3">Approved</span>
                            <?php else: ?>
                                <span class="badge bg-danger rounded-pill px-3">Rejected</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($log['type'] === 'clock_in' && $log['video_path']): ?>
                        <div class="bg-light rounded-3 p-3 mb-3">
                            <h6 class="small fw-bold text-muted mb-2 text-uppercase" style="letter-spacing: 0.5px;">Verification Proof</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <video src="<?php echo $log['video_path']; ?>" class="w-100 rounded-2" controls style="max-height: 150px; background: #000;"></video>
                                    <p class="text-center small text-muted mt-1 mb-0">Video Proof</p>
                                </div>
                                <div class="col-6">
                                    <?php if ($log['audio_path']): ?>
                                        <audio src="<?php echo $log['audio_path']; ?>" controls class="w-100 mt-4"></audio>
                                        <p class="text-center small text-muted mt-1 mb-0">Voice Proof</p>
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-secondary bg-opacity-10 rounded-2 text-muted small">No Audio</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($log['verification_status'] === 'pending'): ?>
                            <div class="d-flex gap-2" id="actions-<?php echo $log['id']; ?>">
                                <button onclick="updateStatus(<?php echo $log['id']; ?>, 'approved')" class="btn btn-success btn-sm flex-grow-1 fw-600 rounded-pill">Approve</button>
                                <button onclick="updateStatus(<?php echo $log['id']; ?>, 'rejected')" class="btn btn-outline-danger btn-sm flex-grow-1 fw-600 rounded-pill">Reject</button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($logs)): ?>
        <div class="col-12 text-center py-5">
            <i class="fas fa-calendar-alt text-muted fs-1 mb-3"></i>
            <p class="text-muted fw-500">No attendance logs found.</p>
        </div>
    <?php endif; ?>
</div>

<script>
function updateStatus(id, status) {
    const fd = new FormData();
    fd.append('id', id);
    fd.append('status', status);

    fetch('index.php?action=attendance_update_status', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const badge = document.getElementById('statusBadge-' + id);
            const actions = document.getElementById('actions-' + id);
            
            if (status === 'approved') {
                badge.innerHTML = '<span class="badge bg-success rounded-pill px-3">Approved</span>';
            } else {
                badge.innerHTML = '<span class="badge bg-danger rounded-pill px-3">Rejected</span>';
            }
            if (actions) actions.remove();
            showToast('Verification updated to ' + status);
        }
    });
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
