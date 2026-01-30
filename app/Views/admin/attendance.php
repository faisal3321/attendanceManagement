<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Attendance Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .table-card, .filter-card { border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .status-select {
            padding: 5px 10px; border-radius: 6px; font-size: 0.85rem;
            font-weight: 600; border: 1px solid #ddd; cursor: pointer; width: 100%;
        }
        .select-present { background-color: #e6f4ea; color: #1e7e34; border-color: #c3e6cb; }
        .select-absent { background-color: #fce8e6; color: #d93025; border-color: #f5c6cb; }
        .select-half { background-color: #fff4e5; color: #b05e00; border-color: #ffeeba; }
        .select-na { background-color: #f1f3f4; color: #5f6368; border-color: #ddd; }
        .empty-msg { padding: 40px; text-align: center; color: #6c757d; font-style: italic; }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="fw-bold mb-4">Worker Attendance Control</h2>

    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold text-uppercase text-muted mb-3">Filter & Sync Date Range</h6>
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Worker ID</label>
                    <input type="text" id="filterWorkerId" class="form-control" placeholder="worker id">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">From</label>
                    <input type="date" id="startDate" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">To</label>
                    <input type="date" id="endDate" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Admin Header ID</label>
                    <input type="text" id="adminId" class="form-control" placeholder="X-ADMIN-ID">
                </div>
                <div class="col-md-2">
                    <button onclick="generateAndFilter()" class="btn btn-primary w-100 fw-bold">Apply Filter</button>
                </div>
                <div class="col-md-2">
                    <button onclick="clearFilter()" class="btn btn-outline-secondary w-100 fw-bold">Clear</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Date</th>
                        <th>Worker Name</th>
                        <th>Admin Status</th>
                        <th>Customer Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="logs">
                    <tr>
                        <td colspan="6" class="empty-msg">No data to show. Please enter filters to fetch records.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const API_ATTENDANCE = "<?= base_url('api/attendance') ?>";
    const API_CALENDAR = "<?= base_url('api/calendar/generate') ?>";

    let currentFilter = { start: '', end: '', workerId: '' };

    function getStatusClass(val) {
        val = parseInt(val);
        if (val === 1) return 'select-present';
        if (val === 2) return 'select-absent';
        if (val === 3) return 'select-half';
        return 'select-na';
    }

    function clearFilter() {
        document.getElementById('filterWorkerId').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        currentFilter = { start: '', end: '', workerId: '' };
        document.getElementById('logs').innerHTML = `<tr><td colspan="6" class="empty-msg">No data to show. Please enter filters to fetch records.</td></tr>`;
    }

    async function generateAndFilter() {
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;
        const workerId = document.getElementById('filterWorkerId').value;
        const adminId = document.getElementById('adminId').value;

        if(!start || !end || !workerId) {
            return alert("Worker ID, Start, and End dates are required.");
        }

        currentFilter = { start, end, workerId: workerId.trim() };
        document.getElementById('logs').innerHTML = `<tr><td colspan="6" class="empty-msg">Fetching data...</td></tr>`;

        try {
            await fetch(API_CALENDAR, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-ADMIN-ID': adminId },
                body: JSON.stringify({ start_date: start, end_date: end })
            });

            loadLogs();
        } catch (error) {
            alert("Connection error.");
        }
    }

    async function loadLogs() {
        if (!currentFilter.workerId) return;

        const res = await fetch(API_ATTENDANCE);
        const json = await res.json();
        const tbody = document.getElementById('logs');
        
        const filteredData = json.data.filter(row => {
            const dateMatch = row.actual_date >= currentFilter.start && row.actual_date <= currentFilter.end;
            const workerMatch = row.worker_id.toString() === currentFilter.workerId;
            return dateMatch && workerMatch;
        });

        if (filteredData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="empty-msg">No records found for this worker in the selected range.</td></tr>`;
            return;
        }

        tbody.innerHTML = filteredData.map(row => {
            // FIX: Create a unique key by combining ID and Date (removing dashes from date)
            const uniqueKey = row.id + "_" + row.actual_date.replace(/-/g, "");

            return `
                <tr>
                    <td class="ps-4 text-muted small">${row.id}</td>
                    <td><strong>${row.actual_date}</strong></td>
                    <td><span class="fw-bold">${row.worker_name}</span><br><small>${row.worker_id}</small></td>
                    <td>
                        <select class="status-select ${getStatusClass(row.worker_attendance)}" 
                                onchange="this.className='status-select '+getStatusClass(this.value)"
                                id="adm-${uniqueKey}">
                            <option value="1" ${row.worker_attendance == 1 ? 'selected' : ''}>Present</option>
                            <option value="2" ${row.worker_attendance == 2 ? 'selected' : ''}>Absent</option>
                            <option value="3" ${row.worker_attendance == 3 ? 'selected' : ''}>Half-Day</option>
                            <option value="0" ${row.worker_attendance == 0 ? 'selected' : ''}>N/A</option>
                        </select>
                    </td>
                    <td>
                        <select class="status-select ${getStatusClass(row.customer_side_attendance)}" 
                                onchange="this.className='status-select '+getStatusClass(this.value)"
                                id="cus-${uniqueKey}">
                            <option value="0" ${row.customer_side_attendance == 0 ? 'selected' : ''}>N/A</option>
                            <option value="1" ${row.customer_side_attendance == 1 ? 'selected' : ''}>Present</option>
                            <option value="2" ${row.customer_side_attendance == 2 ? 'selected' : ''}>Absent</option>
                            <option value="3" ${row.customer_side_attendance == 3 ? 'selected' : ''}>Half-Day</option>
                        </select>
                    </td>
                    <td class="text-end pe-4">
                        <button onclick="updateRow(${row.id}, '${uniqueKey}')" id="btn-${uniqueKey}" class="btn btn-sm btn-success px-3">Update</button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function updateRow(dbId, uniqueKey) {
        const adminVal = document.getElementById(`adm-${uniqueKey}`).value;
        const customerVal = document.getElementById(`cus-${uniqueKey}`).value;
        const btn = document.getElementById(`btn-${uniqueKey}`);

        btn.disabled = true;
        btn.innerText = "...";

        try {
            const res = await fetch(`${API_ATTENDANCE}/admin/override`, {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    id: dbId, // Real DB ID for backend
                    worker_attendance: parseInt(adminVal),
                    customer_side_attendance: parseInt(customerVal)
                })
            });

            const result = await res.json();
            if(result.success) {
                await loadLogs(); 
                alert("Data saved successfully!");
            } else {
                alert("Server error: " + result.message);
            }
        } catch (e) {
            alert("Failed to update.");
        } finally {
            btn.disabled = false;
            btn.innerText = "Update";
        }
    }
</script>
</body>
</html>