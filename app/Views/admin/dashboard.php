<?= view('layouts/header') ?>

<h3 class="mb-3">Admin Dashboard</h3>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Add Worker</h5>
                <a href="/admin/add-worker" class="btn btn-primary">Add Worker</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Manage Attendance</h5>
                <a href="/admin/manage-attendance" class="btn btn-success">Attendance</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Manage Customers</h5>
                <a href="/admin/manage-customers" class="btn btn-warning">Customers</a>
            </div>
        </div>
    </div>
</div>

<?php if(session()->get('is_super')): ?>
<div class="card mt-3">
    <div class="card-body">
        <h5>Super Admin Options</h5>
        <p>As super admin, you can create other admin accounts.</p>
        <!-- Add create admin form here if needed -->
    </div>
</div>
<?php endif; ?>

<?= view('layouts/footer') ?>