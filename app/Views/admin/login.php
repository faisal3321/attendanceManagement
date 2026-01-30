<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; height: 100vh; display: flex; align-items: center; }
        .login-card { width: 100%; max-width: 400px; padding: 15px; margin: auto; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="text-center mb-4">Admin Login</h3>
            
            <div id="loginAlert" class="alert alert-danger d-none" role="alert"></div>

            <form id="loginForm">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" id="loginBtn">
                    <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span> Login
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const alertBox = document.getElementById('loginAlert');
        const btn = document.getElementById('loginBtn');
        const spinner = document.getElementById('btnSpinner');

        // Reset UI
        alertBox.classList.add('d-none');
        btn.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const response = await fetch('<?= base_url('api/login') ?>', {
                method: 'POST',
                headers: 
                { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = '<?= base_url('admin/dashboard') ?>';
            } else {
                alertBox.textContent = result.messages?.error || result.message || 'Invalid credentials';
                alertBox.classList.remove('d-none');
            }
        } catch (error) {
            alertBox.textContent = "Connection error. Please try again.";
            alertBox.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            spinner.classList.add('d-none');
        }
    });
    
</script>

</body>
</html>