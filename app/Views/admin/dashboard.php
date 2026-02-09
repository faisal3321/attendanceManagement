<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-5 text-center">
        
        <h1 class="display-6">Welcome, <?= session()->get('admin_name') ?>...</h1><br><br>

        <h3> You Can Add Worker Here!</h3>
        <div class="mt-4">
            <a href="<?= base_url('admin/addWorker') ?>" class="btn btn-primary btn-lg">
                + Add New Worker
            </a>
            <a href="<?= base_url('admin/workerList') ?>" class="btn btn-warning btn-lg">
                Show Worker List
            </a>
        </div>
        
    </div>
</div>

</body>
</html>