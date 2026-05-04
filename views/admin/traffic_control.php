<?php include __DIR__ . '/../layouts/header.php'; ?>

<?php
require_once __DIR__ . '/../../config/Database.php';
$db2 = (new Database())->getConnection();
$trafficStmt = $db2->query("SELECT setting_value FROM app_settings WHERE setting_key='traffic_level'");
$trafficRow = $trafficStmt->fetch(PDO::FETCH_ASSOC);
$currentTraffic = $trafficRow ? $trafficRow['setting_value'] : 'low';
?>

<div class="mb-4">
    <h2 class="fw-bold fs-3 mb-1">Traffic Control</h2>
    <p class="text-muted small fw-500">Set current venue traffic level to activate conditional SOPs</p>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Current Level: 
            <span class="badge <?php
                echo $currentTraffic === 'high' ? 'bg-danger' : ($currentTraffic === 'medium' ? 'bg-warning text-dark' : 'bg-success');
            ?> px-3 py-2 rounded-pill fs-6">
                <?php echo ucfirst($currentTraffic); ?>
            </span>
        </h5>
        
        <form method="POST" action="index.php?action=set_traffic">
            <div class="row g-3">
                <div class="col-4">
                    <button type="submit" name="level" value="low" class="btn btn-success w-100 py-4 fw-bold <?php echo $currentTraffic === 'low' ? 'opacity-100 shadow' : 'opacity-50'; ?>" style="font-size: 1.1rem;">
                        <i class="fas fa-circle-notch mb-2 d-block fs-2"></i>
                        LOW
                    </button>
                </div>
                <div class="col-4">
                    <button type="submit" name="level" value="medium" class="btn btn-warning w-100 py-4 fw-bold <?php echo $currentTraffic === 'medium' ? 'opacity-100 shadow' : 'opacity-50'; ?>" style="font-size: 1.1rem;">
                        <i class="fas fa-signal mb-2 d-block fs-2"></i>
                        MED
                    </button>
                </div>
                <div class="col-4">
                    <button type="submit" name="level" value="high" class="btn btn-danger w-100 py-4 fw-bold <?php echo $currentTraffic === 'high' ? 'opacity-100 shadow' : 'opacity-50'; ?>" style="font-size: 1.1rem;">
                        <i class="fas fa-fire mb-2 d-block fs-2"></i>
                        HIGH
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Conditional SOPs</h5>
        <p class="text-muted small mb-3">SOPs assigned with a traffic condition will only appear on staff dashboards when the traffic level matches.</p>
        <?php
        $condStmt = $db2->query("SELECT s.title, a.condition_traffic, a.assigned_to_role FROM sop_assignments a JOIN sops s ON a.sop_id = s.id WHERE a.trigger_type = 'conditional' ORDER BY a.condition_traffic");
        $conditionalSops = $condStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php if (empty($conditionalSops)): ?>
        <p class="text-muted small fw-500">No conditional SOPs configured yet. Edit an SOP assignment and set trigger type to "Conditional".</p>
        <?php else: ?>
        <?php foreach ($conditionalSops as $c): ?>
        <div class="d-flex align-items-center py-2 border-bottom border-light">
            <span class="badge <?php echo $c['condition_traffic'] === 'high' ? 'bg-danger' : ($c['condition_traffic'] === 'medium' ? 'bg-warning text-dark' : 'bg-success'); ?> me-3 px-2">
                <?php echo ucfirst($c['condition_traffic']); ?>
            </span>
            <span class="fw-500"><?php echo htmlspecialchars($c['title']); ?></span>
            <span class="ms-auto text-muted small"><?php echo str_replace('staff_', '', $c['assigned_to_role']); ?></span>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
