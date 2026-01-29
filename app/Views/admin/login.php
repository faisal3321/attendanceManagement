<?= view('layouts/header') ?>

<h3 class="mb-3">Admin Login</h3>

<form id="loginForm">
    <input class="form-control mb-2" name="username" placeholder="Username" required>
    <input class="form-control mb-2" name="password" type="password" placeholder="Password" required>

    <button class="btn btn-success">Login as Admin</button>
</form>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const payload = Object.fromEntries(formData.entries());

    const res = await fetch('/api/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });

    const data = await res.json();

    if (data.success) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/set-session';
        
        const input1 = document.createElement('input');
        input1.type = 'hidden';
        input1.name = 'admin_id';
        input1.value = data.data.admin_id;
        
        const input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = 'is_super';
        input2.value = data.data.is_super;
        
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