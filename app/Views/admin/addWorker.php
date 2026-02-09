<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Worker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-3">
        <div class="container">
            <div class="ms-auto">
                <a href="<?= base_url('admin/workerList') ?>" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-semibold">
                    <span id="spinner" class="spinner-border spinner-border-sm d-none"></span>
                    <i class="bi bi-people-fill me-1"></i> Show Worker List
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add New Worker</h5>
                        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-sm btn-light">Back</a>
                    </div>
                    <div class="card-body p-4">
                        <div id="alertMsg" class="alert d-none" role="alert"></div>

                        <form id="addWorkerForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" placeholder="John Doe" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="age" class="form-label">Age</label>
                                    <input type="number" class="form-control" id="age" min="18" max="60" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" required>
                                        <option value="">Choose...</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" placeholder="ex: 9876543210" required>
                            </div>

                            <div class="mb-4">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" rows="3" placeholder="Enter full address" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100" id="submitBtn">
                                <span id="spinner" class="spinner-border spinner-border-sm d-none"></span> Save Worker
                            </button>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Get Admin ID from session 
        const adminId = "<?= session()->get('admin_id') ?>";

        document.getElementById('addWorkerForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const alertMsg = document.getElementById('alertMsg');

            // Reset UI
            alertMsg.classList.add('d-none');
            submitBtn.disabled = true;
            spinner.classList.remove('d-none');

            // Collect Data
            const workerData = {
                name: document.getElementById('name').value,
                age: document.getElementById('age').value,
                gender: document.getElementById('gender').value,
                phone: document.getElementById('phone').value,
                address: document.getElementById('address').value
            };

            try {
                const response = await fetch('<?= base_url('api/add/worker') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-ADMIN-ID': adminId // Required by your WorkerController
                    },
                    body: JSON.stringify(workerData)
                });

                const result = await response.json();

                if (result.success) {
                    alertMsg.className = "alert alert-success";
                    alertMsg.textContent = result.message + " Worker ID: " + result.data.id;
                    alertMsg.classList.remove('d-none');
                    document.getElementById('addWorkerForm').reset();
                } else {
                    alertMsg.className = "alert alert-danger";
                    alertMsg.textContent = result.messages?.error || result.message || "Failed to create worker";
                    alertMsg.classList.remove('d-none');
                }
            } catch (error) {
                alertMsg.className = "alert alert-danger";
                alertMsg.textContent = "Network error. Please try again.";
                alertMsg.classList.remove('d-none');
            } finally {
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
            }
        });
    </script>


</body>
</html>