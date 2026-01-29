<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container d-flex justify-content-between">
            <a href="<?= base_url('/') ?>">Attendees</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5 text-center">
                
                <h3>Welcome to the login page</h3> <br><br>

                <h2>LOGIN</h2>

                <form id="loginForm" class="text-start">

                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Password:</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter Your Password" required>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5">Submit</button><br><br>
                    </div>

                    <div class="text-center">
                        <p class="small">Don't have an account? <a href="<?= base_url('customer/register') ?>" class="text-decoration-none">Register here</a></p>
                    </div>
                    
                </form>

            </div>
        </div>
    </div>

    <footer class="container mt-5 py-4 text-center border-top">
        <p class="text-secondary"> &copy; 2026, Attendance Management System</p>
    </footer>

    <script>
        const loginForm = document.getElementById('loginForm');

        loginForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            fetch('<?= base_url('api/customer/login') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert("Success: " + result.message);
                    // Redirect to a dashboard or home after login
                    window.location.href = "<?= base_url('/customer/dashboard') ?>"; 
                } else {
                    alert("Login Failed: " + (result.message || "Invalid credentials"));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An error occurred during login.");
            });
        }); 
    </script>

</body>
</html>

