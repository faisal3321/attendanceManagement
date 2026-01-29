<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container d-flex justify-content-between">
            <a  href="<?= base_url('/') ?>">Attendees</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                
                <h3>Welcome to the register page</h3> <br><br>

                <h2 >REGISTER</h2>

                <form id="registerForm" class="text-start">

                    <div class="mb-3">
                        <label class="form-label">Name:</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter Your Name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password:</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter Your Password" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone:</label>
                        <input type="number" name="phone" class="form-control" placeholder="Enter Phone Number" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Address:</label>
                        <textarea name="address" class="form-control" placeholder="Address" rows="3" required></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5">Submit</button><br><br>
                    </div>

                    <div class="text-center">
                        <a href="<?= base_url('customer/login') ?>" >Please Login </a>
                    </div>

                    
                </form>

            </div>
        </div>
    </div>

    <footer class="container mt-5 py-4 text-center border-top">
        <p> &copy; 2026, Attendance Management System</p>
    </footer>

    <script>
        // make sure we select the form correctly
        const regForm = document.getElementById('registerForm');

        regForm.addEventListener('submit', function(e) {
            //  most important line - it stops the URL from changing
            e.preventDefault(); 
            console.log("Form submission intercepted!");

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            // added a console log to help me debug
            console.log("Sending data:", data);

            fetch('<?= base_url('api/customer/register') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' 
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    // If we get a 404 or 500, see what the server says
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(result => {
                console.log("Server response:", result);
                if(result.success) {
                    alert("Success: " + result.message);
                    window.location.href = "<?= base_url('/customer/dashboard') ?>";
                } else {
                    alert("Registration Failed: " + (result.message || "Unknown error"));
                }
            })
            .catch(error => {
                console.error('Full Error Object:', error);
                alert("Check console: " + (error.message || "Server Error"));
            });
        }); 
    </script>

</body>
</html>