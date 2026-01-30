<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Worker Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .table > tbody > tr > td {
            vertical-align: middle;
            padding-top: 15px;
            padding-bottom: 15px;
        }
        .action-btn { margin-right: 5px; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('admin/dashboard') ?>">Admin Dashboard</a>
    </div>
</nav>

<div class="container">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary">Worker Directory</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Worker ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($workers)): ?>
                        <?php foreach($workers as $worker): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted"><?= $worker['worker_id'] ?></td>
                            <td>
                                <div class="fw-bold"><?= $worker['name'] ?></div>
                                <small class="text-muted"><?= $worker['gender'] ?> | Age: <?= $worker['age'] ?></small>
                            </td>
                            <td><?= $worker['phone'] ?></td>
                            <td><span class="badge bg-success-subtle text-success border border-success-subtle">Active</span></td>
                            <td class="text-end pe-4">
                                <a href="<?= base_url('admin/attendance/'.$worker['worker_id']) ?>" class="btn btn-sm btn-outline-primary action-btn">
                                    Manage Attendance
                                </a>
                                <a href="<?= base_url('admin/workers/edit/'.$worker['id']) ?>" class="btn btn-sm btn-light text-warning border action-btn">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button onclick="deleteWorker(<?= $worker['id'] ?>)" class="btn btn-sm btn-light text-danger border">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No workers found in the system.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function deleteWorker(id) {
        if(confirm('Are you sure you want to delete this worker?')) {
            // You can implement the AJAX delete here later
            console.log('Deleting worker with ID:', id);
        }
    }
</script>

</body>
</html>