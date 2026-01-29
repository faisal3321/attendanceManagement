<?= view('layouts/header') ?>

<h3 class="mb-3">Customer Register</h3>

<form id="registerForm">
    <input class="form-control mb-2" name="name" placeholder="Full Name" required>
    <input class="form-control mb-2" name="email" type="email" placeholder="Email" required>
    <input class="form-control mb-2" name="password" type="password" placeholder="Password" required>
    <input class="form-control mb-2" name="phone" placeholder="Phone" required>
    <textarea class="form-control mb-2" name="address" placeholder="Address" required></textarea>

    <button class="btn btn-success">Register</button>
</form>

<p class="mt-3">Already have account? <a href="/customer/login">Login here</a></p>

<script>
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const payload = Object.fromEntries(formData.entries());

    const res = await fetch('/api/customer/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });

    const data = await res.json();

    if (data.success) {
        alert('Registration successful. Please login.');
        window.location.href = '/customer/login';
    } else {
        alert(data.message || 'Registration failed');
    }
});
</script>

<?= view('layouts/footer') ?>