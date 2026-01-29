<?= view('layouts/header') ?>

<h3 class="mb-3">Add New Worker</h3>

<form id="addWorkerForm">
    <input class="form-control mb-2" name="name" placeholder="Worker Name" required>
    <input class="form-control mb-2" name="age" type="number" placeholder="Age" required>
    
    <select class="form-control mb-2" name="gender" required>
        <option value="">Select Gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="others">Others</option>
    </select>
    
    <input class="form-control mb-2" name="phone" placeholder="Phone Number" required>
    <textarea class="form-control mb-2" name="address" placeholder="Address"></textarea>
    
    <input type="hidden" name="admin_id" value="<?= session()->get('admin_id') ?>">
    
    <button class="btn btn-primary" type="submit">Add Worker</button>
    <a href="/admin/dashboard" class="btn btn-secondary">Back</a>
</form>

<script>
document.getElementById('addWorkerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const payload = Object.fromEntries(formData.entries());

    // Get admin ID from session (you might need to pass it differently)
    const adminId = "<?= session()->get('admin_id') ?>";
    
    const res = await fetch('/api/add/worker', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-ADMIN-ID': adminId
        },
        body: JSON.stringify(payload)
    });

    const data = await res.json();

    if (data.success) {
        alert('Worker added successfully!');
        this.reset();
    } else {
        alert(data.message || 'Failed to add worker');
    }
});
</script>

<?= view('layouts/footer') ?>