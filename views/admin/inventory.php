<?php include __DIR__ . '/../layouts/header.php'; ?>

<?php
require_once __DIR__ . '/../../src/Models/Inventory.php';
$inv = new Inventory($db);
$items = $inv->getAll();
$grouped = [];
foreach ($items as $item) {
    $grouped[$item['category']][] = $item;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold fs-3 m-0">Inventory</h2>
    <a href="index.php?action=inventory_add" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
        <i class="fas fa-plus me-1"></i> Add Item
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success py-2 rounded-3 text-center fw-500 mb-4">
    <i class="fas fa-check-circle me-1"></i> Stock updated!
</div>
<?php endif; ?>

<?php foreach ($grouped as $category => $catItems): ?>
<div class="d-flex justify-content-between align-items-center mb-2 mt-4">
    <h5 class="fw-bold m-0"><?php echo htmlspecialchars($category); ?></h5>
</div>
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-0">
        <?php foreach ($catItems as $item): 
            $isLow = $item['current_stock'] <= $item['min_threshold'];
            $pct = $item['min_threshold'] > 0 ? min(100, ($item['current_stock'] / ($item['min_threshold'] * 2)) * 100) : 100;
        ?>
        <div class="d-flex align-items-center p-3 border-bottom border-light">
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="fw-bold mb-0 <?php echo $isLow ? 'text-danger' : 'text-dark'; ?>">
                        <?php if ($isLow): ?><i class="fas fa-exclamation-triangle me-1"></i><?php endif; ?>
                        <?php echo htmlspecialchars($item['name']); ?>
                    </h6>
                    <span class="fw-bold <?php echo $isLow ? 'text-danger' : 'text-dark'; ?>">
                        <?php echo $item['current_stock']; ?> <span class="fw-400 text-muted"><?php echo $item['unit']; ?></span>
                    </span>
                </div>
                <div class="progress" style="height: 5px; border-radius: 3px; background-color: #E5E7EB;">
                    <div class="progress-bar <?php echo $isLow ? 'bg-danger' : 'bg-success'; ?>" style="width: <?php echo $pct; ?>%"></div>
                </div>
                <p class="text-muted mb-0 mt-1" style="font-size: 0.72rem;">Min: <?php echo $item['min_threshold']; ?> <?php echo $item['unit']; ?></p>
            </div>
            <div class="ms-3">
                <form method="POST" action="index.php?action=inventory_update" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                    <input type="number" name="stock" value="<?php echo $item['current_stock']; ?>" class="form-control form-control-sm text-center" style="width: 70px; font-size: 0.85rem;" step="0.5" min="0">
                    <button type="submit" class="btn btn-sm btn-outline-brand py-1 px-2 rounded" style="min-height: auto; font-size: 0.75rem;">Save</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
