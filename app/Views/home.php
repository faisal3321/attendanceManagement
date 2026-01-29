<?= view('layouts/header') ?>

<h1 class="mb-4">Worker - Attendance System</h1>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-body">
                <h4>Customer</h4>
                <a href="/customer/register" class="btn btn-primary mb-2">Register</a>
                <a href="/customer/login" class="btn btn-outline-primary mb-2">Login</a>
                <p class="mt-3">Book workers and mark daily attendance</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-body">
                <h4>Admin</h4>
                <a href="/admin/login" class="btn btn-success">Admin Login</a>
                <p class="mt-3">Manage workers, customers, and attendance</p>
            </div>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>