<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Worker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 600px;">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Edit Worker Details</h5>
        </div>
        <div class="card-body p-4">
            <form id="editForm">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" id="name" class="form-control" value="<?= $worker['name'] ?>" required>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Age</label>
                        <input type="number" id="age" class="form-control" value="<?= $worker['age'] ?>" required>
                    </div>
                    <div class="col">
                        <label class="form-label">Phone</label>
                        <input type="text" id="phone" class="form-control" value="<?= $worker['phone'] ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea id="address" class="form-control" rows="3"><?= $worker['address'] ?></textarea>
                </div>
                <div class="d-flex justify-content-between pt-3">
                    <a href="<?= base_url('admin/workerList') ?>" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4" id="saveBtn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('editForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;

        const data = {
            name: document.getElementById('name').value,
            age: document.getElementById('age').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value
        };

        try {
            const response = await fetch('<?= base_url('api/workers/update/'.$worker['id']) ?>', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            if (result.success) {
                alert(result.message);
                window.location.href = '<?= base_url('admin/workerList') ?>';
            } else {
                alert('Update failed');
                saveBtn.disabled = false;
            }
        } catch (error) {
            console.error(error);
            saveBtn.disabled = false;
        }
    });
</script>
</body>
</html>