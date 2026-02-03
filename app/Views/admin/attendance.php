<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Attendance Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .table-card, .filter-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .status-select {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            border: 1px solid #ddd;
            cursor: pointer;
            width: 100%;
        }
        .select-present { background:#e6f4ea; color:#1e7e34; }
        .select-absent  { background:#fce8e6; color:#d93025; }
        .select-half    { background:#fff4e5; color:#b05e00; }
        .select-na      { background:#f1f3f4; color:#5f6368; }
        .empty-msg {
            padding: 40px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>

<body>

<div class="container py-5">
    <h2 class="fw-bold mb-4">Worker Attendance Control</h2>

    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold text-uppercase text-muted mb-3">Date Range</h6>

            <div class="row g-3 align-items-end">

                <input type="hidden" id="filterWorkerId">

                <div class="col-md-3">
                    <label class="form-label fw-bold small">From Date</label>
                    <input type="date" id="startDate" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small">To Date</label>
                    <input type="date" id="endDate" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small">Admin ID</label>
                    <input type="text" id="adminId" class="form-control"
                           value="<?= session()->get('admin_id') ?>">
                </div>

                <div class="col-md-3">
                    <button onclick="generateAndFilter()"
                            class="btn btn-primary w-100 fw-bold">
                        Apply Filter
                    </button>
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
                        <th>Worker</th>
                        <th>Admin</th>
                        <th>Customer</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody id="logs">
                    <tr>
                        <td colspan="6" class="empty-msg">Loading attendance…</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const API_ATTENDANCE = "<?= base_url('api/attendance') ?>";
const API_CALENDAR   = "<?= base_url('api/calendar/generate') ?>";

let currentFilter = { start:'', end:'', workerId:'' };
let autoDateSet = false;


function getStatusClass(v) {
    v = parseInt(v);
    if (v === 0) return 'select-na';
    if (v === 1) return 'select-present';
    if (v === 2) return 'select-absent';
    if (v === 3) return 'select-half';
    
}

function getQueryParam(p) {
    return new URLSearchParams(window.location.search).get(p);
}

// main logic is here

async function generateAndFilter() {
    const start    = document.getElementById('startDate').value;
    const end      = document.getElementById('endDate').value;
    const workerId = document.getElementById('filterWorkerId').value;
    const adminId  = document.getElementById('adminId').value;

    if (!workerId) return alert("No worker selected");

    currentFilter = { start, end, workerId: workerId.trim() };

    // selection for refresh
    sessionStorage.setItem('attendance_start', start);
    sessionStorage.setItem('attendance_end', end);

    document.getElementById('logs').innerHTML =
        `<tr><td colspan="6" class="empty-msg">Fetching data…</td></tr>`;

    if (start && end) {
        await fetch(API_CALENDAR, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-ADMIN-ID': adminId
            },
            body: JSON.stringify({ start_date: start, end_date: end })
        });
    }

    loadLogs();
}

async function loadLogs() {
    const res  = await fetch(API_ATTENDANCE);
    const json = await res.json();
    const tbody = document.getElementById('logs');

    if (!json.data?.length) {
        tbody.innerHTML =
            `<tr><td colspan="6" class="empty-msg">No attendance found</td></tr>`;
        return;
    }

    // 🔹 auto-fill oldest date ONLY ONCE
    if (!autoDateSet && currentFilter.workerId) {
        const rows = json.data.filter(
            r => r.worker_id.toString() === currentFilter.workerId
        );

        if (rows.length) {
            const oldest = rows.map(r => r.actual_date).sort()[0];
            currentFilter.start = sessionStorage.getItem('attendance_start') || oldest;
            currentFilter.end   = sessionStorage.getItem('attendance_end') || '';

            document.getElementById('startDate').value = currentFilter.start;
            document.getElementById('endDate').value   = currentFilter.end;

            autoDateSet = true;
        }
    }

    const filtered = json.data.filter(r => {
        if (r.worker_id.toString() !== currentFilter.workerId) return false;
        if (currentFilter.start && currentFilter.end)
            return r.actual_date >= currentFilter.start &&
                   r.actual_date <= currentFilter.end;
        return true;
    });

    if (!filtered.length) {
        tbody.innerHTML =
            `<tr><td colspan="6" class="empty-msg">No records found</td></tr>`;
        return;
    }

    tbody.innerHTML = filtered.map(r => {
        const key = r.id + "_" + r.actual_date.replace(/-/g,'');
        return `
        <tr>
            <td class="ps-4 small">${r.id}</td>
            <td><strong>${r.actual_date}</strong></td>
            <td>${r.worker_name}<br><small>${r.worker_id}</small></td>
            <td>
                <select class="status-select ${getStatusClass(r.worker_attendance)}">
                    <option value="1" ${r.worker_attendance==1?'selected':''}>Present</option>
                    <option value="2" ${r.worker_attendance==2?'selected':''}>Absent</option>
                    <option value="3" ${r.worker_attendance==3?'selected':''}>Half</option>
                    <option value="0" ${r.worker_attendance==0?'selected':''}>N/A</option>
                </select>
            </td>
            <td>
                <select class="status-select ${getStatusClass(r.customer_side_attendance)}">
                    <option value="0" ${r.customer_side_attendance==0?'selected':''}>N/A</option>
                    <option value="1" ${r.customer_side_attendance==1?'selected':''}>Present</option>
                    <option value="2" ${r.customer_side_attendance==2?'selected':''}>Absent</option>
                    <option value="3" ${r.customer_side_attendance==3?'selected':''}>Half</option>
                </select>
            </td>
            <td class="text-end pe-4">
                <button class="btn btn-sm btn-success">Update</button>
            </td>
        </tr>`;
    }).join('');
}


window.addEventListener('DOMContentLoaded', () => {
    const workerId = getQueryParam('worker_id');
    if (workerId) {
        document.getElementById('filterWorkerId').value = workerId;
        currentFilter.workerId = workerId.trim();
        loadLogs();
    }
});
</script>

</body>
</html>
