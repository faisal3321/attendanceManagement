<?= view('layouts/header') ?>

<h3 class="mb-3">Customer Login</h3>

<form id="loginForm">
    <input class="form-control mb-2" name="identity" placeholder="Email or Phone" required>
    <input class="form-control mb-2" name="password" type="password" placeholder="Password" required>

    <button class="btn btn-primary">Login</button>
</form>

<p class="mt-3">Don't have account? <a href="/customer/register">Register here</a></p>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const payload = Object.fromEntries(formData.entries());

    const res = await fetch('/api/customer/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });

    const data = await res.json();

    if (data.success) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/index.php/set-session';
        
        const input1 = document.createElement('input');
        input1.type = 'hidden';
        input1.name = 'customer_id';
        input1.value = data.data.customer_id;
        
        const input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = 'customer_name';
        input2.value = data.data.name;
        
        form.appendChild(input1);
        form.appendChild(input2);
        document.body.appendChild(form);
        form.submit();
    } else {
        alert(data.message || 'Login failed');
    }
});
</script>

<?= view('layouts/footer') ?>