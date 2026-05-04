<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-4">
    <h2 class="fw-bold fs-3 mb-1">Admin Dashboard</h2>
    <p class="text-muted small fw-500">Daily Operations Overview</p>
</div>

<!-- Branch Filter Pills -->
<div class="mb-4 overflow-auto pb-1" style="white-space: nowrap; -webkit-overflow-scrolling: touch;">
    <div class="d-inline-flex gap-2 p-1 bg-white rounded-pill shadow-sm border">
        <a href="index.php?action=set_branch&branch=all" class="btn rounded-pill px-3 py-1 fw-600 <?php echo (!isset($data['active_branch']) || $data['active_branch'] === 'all') ? 'btn-primary' : 'btn-light text-muted'; ?>" style="font-size: 0.85rem;">
            All Branches
        </a>
        <a href="index.php?action=set_branch&branch=kizobar" class="btn rounded-pill px-3 py-1 fw-600 <?php echo (isset($data['active_branch']) && $data['active_branch'] === 'kizobar') ? 'btn-primary' : 'btn-light text-muted'; ?>" style="font-size: 0.85rem;">
            Kizobar
        </a>
        <a href="index.php?action=set_branch&branch=kizo_cafe" class="btn rounded-pill px-3 py-1 fw-600 <?php echo (isset($data['active_branch']) && $data['active_branch'] === 'kizo_cafe') ? 'btn-primary' : 'btn-light text-muted'; ?>" style="font-size: 0.85rem;">
            Kizo Café
        </a>
    </div>
</div>

<!-- Mobile Cards layout for analytics -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid var(--brand-primary) !important;">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                    <i class="fas fa-list-check text-brand fs-5"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1 fw-600">Total SOPs Today</h6>
                    <h3 class="fw-bold m-0 text-dark"><?php echo $data['stats']['total_today']; ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid var(--success) !important;">
            <div class="card-body p-3">
                <h6 class="text-muted small mb-2 fw-600">Completed</h6>
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle text-success me-2 fs-5"></i>
                    <h3 class="fw-bold m-0 text-dark"><?php echo $data['stats']['completed_today']; ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid var(--pending) !important;">
            <div class="card-body p-3">
                <h6 class="text-muted small mb-2 fw-600">Pending / Missed</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle text-danger me-2 fs-5"></i>
                        <h3 class="fw-bold m-0 text-dark"><?php echo $data['stats']['total_today'] - $data['stats']['completed_today']; ?></h3>
                    </div>
                    <a href="index.php?action=missed_sops" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill fw-bold" style="font-size: 0.75rem;">View</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 mt-4">
    <h4 class="fw-bold fs-5 m-0">Staff Compliance (7 Days)</h4>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <?php foreach ($data['compliance'] as $comp): 
            $total = $comp['total_assigned'];
            $completed = $comp['total_completed'];
            $score = $total > 0 ? round(($completed / $total) * 100) : 0;
            $colorClass = $score >= 80 ? 'bg-success' : ($score >= 50 ? 'bg-warning' : 'bg-danger');
        ?>
        <div class="d-flex align-items-center p-3 border-bottom border-light">
            <div class="avatar-circle me-3 flex-shrink-0" style="width: 45px; height: 45px; font-size: 1.2rem;">
                <?php echo strtoupper(substr($comp['name'], 0, 1)); ?>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between mb-1">
                    <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($comp['name']); ?></h6>
                    <span class="fw-bold text-dark"><?php echo $score; ?>%</span>
                </div>
                <div class="progress" style="height: 6px; border-radius: 3px; background-color: #E5E7EB;">
                    <div class="progress-bar <?php echo $colorClass; ?>" role="progressbar" style="width: <?php echo $score; ?>%"></div>
                </div>
                <p class="mb-0 text-muted small mt-1">
                    <?php echo $completed; ?> / <?php echo $total; ?> completed 
                    <?php if ($comp['total_missed'] > 0): ?>
                        <span class="text-danger fw-600 ms-2"><i class="fas fa-exclamation-triangle"></i> <?php echo $comp['total_missed']; ?> missed</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($data['compliance'])): ?>
        <div class="text-center py-4">
            <p class="text-muted mb-0 fw-500">No data available for the last 7 days.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 mt-5">
    <h4 class="fw-bold fs-5 m-0">Today's Activity</h4>
    <a href="#" class="text-brand fw-600 small text-decoration-none">View All</a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <?php foreach ($data['recent_logs'] as $log): ?>
        <div class="d-flex align-items-center p-3 border-bottom border-light">
            <div class="me-3">
                <?php if($log['status'] === 'completed'): ?>
                    <i class="fas fa-check-circle text-success fs-4"></i>
                <?php else: ?>
                    <i class="fas fa-clock text-danger fs-4"></i>
                <?php endif; ?>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($log['title']); ?></h6>
                <p class="mb-0 text-muted small">
                    <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($log['user_name']); ?>
                    <span class="ms-2 badge bg-light text-dark border"><?php echo ucfirst($log['shift']); ?></span>
                </p>
            </div>
            <div class="text-end">
                <?php if($log['status'] === 'completed'): ?>
                    <span class="d-block text-muted small fw-500"><?php echo date('H:i', strtotime($log['completed_at'])); ?></span>
                <?php else: ?>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2">Pending</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($data['recent_logs'])): ?>
        <div class="text-center py-5">
            <p class="text-muted mb-0 fw-500">No activity logged for today yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
