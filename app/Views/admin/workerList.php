<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Worker Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .table > tbody > tr > td {
            vertical-align: middle;
            padding-top: 25px; 
            padding-bottom: 25px;
            color: #444;
            /* Prevents the text from jumping to the next line */
            white-space: nowrap; 
        }
        .worker-id-tag {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            background: #f0f2f5;
            padding: 4px 12px; 
            border-radius: 4px;
            /* Ensures ID stays in one line */
            white-space: nowrap;
            display: inline-block;
            letter-spacing: 0.5px;
        }
        /* Custom class to handle address truncation properly */
        .address-cell {
            max-width: 250px; 
            overflow: hidden; 
            text-overflow: ellipsis; 
            white-space: nowrap;
        }
        .btn-manage { border-radius: 20px; padding: 5px 15px; font-size: 0.85rem; }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid py-5 px-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2 text-primary"></i> Worker Directory</h5>
            <a href="<?= base_url('admin/addWorker') ?>" class="btn btn-primary btn-sm">+ Add Worker</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="workerTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="workerTableBody">
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    async function fetchWorkers() {
        try {
            const response = await fetch('<?= base_url('api/workers') ?>');
            const result = await response.json();
            const tbody = document.getElementById('workerTableBody');
            tbody.innerHTML = '';

            if (result.success && result.data.length > 0) {
                result.data.forEach(worker => {
                    tbody.innerHTML += `
                        <tr>
                            <td class="ps-4">
                                <span class="worker-id-tag">${worker.id}</span>
                            </td>
                            <td class="fw-bold">${worker.name}</td>
                            <td>${worker.age} Yrs</td>
                            <td>${worker.phone}</td>
                            <td class="text-muted address-cell" title="${worker.address || ''}">
                                ${worker.address || 'N/A'}
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?= base_url('admin/attendance') ?>?worker_id=${worker.id}" class="btn btn-primary btn-manage me-2">
                                    Manage Attendance
                                </a>
                                <a href="<?= base_url('admin/workers/edit/') ?>${worker.id}" class="text-warning me-3" title="Edit">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </a>
                                <a href="javascript:void(0)" onclick="deleteWorker(${worker.id})" class="text-danger" title="Delete">
                                    <i class="bi bi-trash3-fill fs-5"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">No workers found.</td></tr>';
            }
        } catch (error) {
            console.error("Error fetching workers:", error);
        }
    }

    async function deleteWorker(id) {
        if (confirm('Are you sure you want to delete this worker? This action cannot be undone.')) {
            try {
                const response = await fetch(`<?= base_url('api/workers/') ?>${id}`, {
                    method: 'DELETE',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const result = await response.json();
                if (result.success) {
                    alert(result.message);
                    fetchWorkers(); 
                } else {
                    alert(result.message || 'Failed to delete worker');
                }
            } catch (error) {
                console.error("Error deleting worker:", error);
                alert("An error occurred while deleting the worker.");
            }
        }
    }

    fetchWorkers();
</script>

</body>
</html>