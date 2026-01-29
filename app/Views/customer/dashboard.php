<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container d-flex justify-content-between">
            <a class="navbar-brand text-dark text-decoration-none fw-bold" href="<?= base_url('/') ?>">Attendees</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center text-center">
            <div >
                <h1 >Customer Dashboard</h1>
                <p >Welcome back! What would you like to do today?</p>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-4 border rounded shadow-sm bg-white h-100">
                            <h4 class="mb-3">Booking</h4>
                            <p class="text-secondary small">Find and book available workers for your requirements.</p>
                            <a href="<?= base_url('customer/bookWorker') ?>" >Book Worker</a>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-4 border rounded shadow-sm bg-white h-100">
                            <h4 class="mb-3">Attendance</h4>
                            <p class="text-secondary small">Check and manage the attendance records of your workers.</p>
                            <a href="<?= base_url('customer/attendance') ?>" >Manage Attendance</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <footer class="container mt-5 py-4 text-center border-top">
        <p class="text-secondary small"> &copy; 2026, Attendance Management System</p>
    </footer>

</body>
</html>