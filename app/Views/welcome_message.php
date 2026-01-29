<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Attendance Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container">
            <a href="">Attendees</a>
            
            <div class="ms-auto">
                <a href="<?= base_url('customer/register') ?>" >REGISTER</a>
                <a href="<?= base_url('customer/login') ?>" >LOGIN</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="text-center">
                <h1 class="display-5 mb-4">WELCOME TO ATTENDANCE MANAGEMENT</h1>
                
                <div class="mb-4">
                    <p>We are going to manage the worker attendance.</p>
                    <p>To manage the worker attendance, please register or login first.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="container mt-5 py-4 text-center border-top">
        <p> &copy; 2026, Attendance Management System</p>
    </footer>

</body>
</html>