<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0">SOP Assignments</h3>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignModal">
        <i class="fas fa-plus me-2"></i>New Assignment
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover m-0">
                <thead>
                    <tr class="text-muted small border-secondary">
                        <th class="ps-4">SOP Title</th>
                        <th>Assigned To</th>
                        <th>Frequency</th>
                        <th>Shift</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignments as $a): ?>
                    <tr class="border-secondary align-middle">
                        <td class="ps-4 fw-bold"><?php echo htmlspecialchars($a['sop_title']); ?></td>
                        <td>
                            <?php if ($a['assigned_to_user_id']): ?>
                                <span class="badge bg-info bg-opacity-25 text-info"><i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($a['user_name']); ?></span>
                            <?php else: ?>
                                <span class="badge bg-warning bg-opacity-25 text-warning"><i class="fas fa-users me-1"></i> <?php echo ucfirst(str_replace('staff_', '', $a['assigned_to_role'])); ?> Staff</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo ucfirst($a['frequency']); ?></td>
                        <td><span class="badge bg-secondary bg-opacity-25"><?php echo ucfirst($a['shift']); ?></span></td>
                        <td class="pe-4 text-end">
                            <a href="index.php?action=assignment_delete&id=<?php echo $a['id']; ?>" class="btn btn-link text-danger p-0" onclick="return confirm('Remove this assignment?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark border-secondary text-white">
      <div class="modal-header border-secondary">
        <h5 class="modal-title">Assign SOP</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="index.php?action=assignment_save" method="POST">
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label small text-muted">Select SOP</label>
                <select name="sop_id" class="form-select bg-dark border-secondary text-white" required>
                    <?php foreach ($sops as $s): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-muted">Assign to Role</label>
                    <select name="role" class="form-select bg-dark border-secondary text-white">
                        <option value="">- Specific User -</option>
                        <option value="staff_bar">Bar Staff</option>
                        <option value="staff_kitchen">Kitchen Staff</option>
                        <option value="staff_cafe">Café Staff</option>
                        <option value="staff_cleaning">Cleaning Staff</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-muted">OR Specific User</label>
                    <select name="user_id" class="form-select bg-dark border-secondary text-white">
                        <option value="">- Role Based -</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?> (<?php echo ucfirst(str_replace('staff_', '', $u['role'])); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-muted">Frequency</label>
                    <select name="frequency" class="form-select bg-dark border-secondary text-white">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-muted">Shift</label>
                    <select name="shift" class="form-select bg-dark border-secondary text-white">
                        <option value="morning">Morning</option>
                        <option value="evening">Evening</option>
                        <option value="night">Night</option>
                        <option value="all">All Day</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer border-secondary">
            <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Create Assignment</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
