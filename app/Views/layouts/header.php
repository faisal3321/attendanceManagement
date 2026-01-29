<!DOCTYPE html>
<html>
<head>
    <title>Worker Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container">
        <a class="navbar-brand" href="/">WorkerSync</a>
        
        <div class="navbar-nav">
            <?php if(session()->get('customer_id')): ?>
                <a class="nav-link" href="/customer/dashboard">Dashboard</a>
                <a class="nav-link" href="/customer/logout">Logout</a>
            <?php elseif(session()->get('admin_id')): ?>
                <a class="nav-link" href="/admin/dashboard">Admin Panel</a>
                <a class="nav-link" href="/admin/logout">Logout</a>
            <?php else: ?>
                <a class="nav-link" href="/customer/register">Register</a>
                <a class="nav-link" href="/customer/login">Login</a>
                <a class="nav-link" href="/admin/login">Admin</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container">