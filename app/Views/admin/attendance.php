<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Attendance Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .table-card, .filter-card { border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .status-select { padding: 5px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; border: 1px solid #ddd; cursor: pointer; width: 100%; }
        .select-present { background:#e6f4ea; color:#1e7e34; }
        .select-absent  { background:#fce8e6; color:#d93025; }
        .select-half    { background:#fff4e5; color:#b05e00; }
        .select-na      { background:#f1f3f4; color:#5f6368; }
        .empty-msg { padding: 40px; text-align: center; color: #6c757d; font-style: italic; }
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
                <input type="hidden" id="adminId" value="<?= session()->get('admin_id') ?>">
                <div class="col-md-4"><label class="form-label fw-bold small">From Date</label><input type="date" id="startDate" class="form-control"></div>
                <div class="col-md-4"><label class="form-label fw-bold small">To Date</label><input type="date" id="endDate" class="form-control"></div>
                <div class="col-md-4"><button onclick="generateAndFilter()" class="btn btn-primary w-100 fw-bold mt-4">Apply Filter</button></div>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr><th class="ps-4">ID</th><th>Date</th><th>Worker</th><th>Admin</th><th>Customer</th><th class="text-end pe-4">Action</th></tr>
                </thead>
                <tbody id="logs"><tr><td colspan="6" class="empty-msg">Loading attendance…</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<script>
const API_ATTENDANCE = "<?= base_url('api/attendance') ?>";
const API_CALENDAR   = "<?= base_url('api/calendar/generate') ?>";
const API_UPDATE     = "<?= base_url('api/attendance/admin/override') ?>";

let currentFilter = { start:'', end:'', workerId:'' };
let oldestCalendarDate = '';
let today = '';

function getTodayDate() {
    return new Date().toISOString().split('T')[0];
}

async function initDates() {
    today = getTodayDate();
    
    // Get oldest calendar date from server
    try {
        const res = await fetch(API_ATTENDANCE);
        const json = await res.json();
        
        if (json.data?.length) {
            // Find oldest date from attendance data
            const dates = json.data.map(r => r.actual_date).sort();
            oldestCalendarDate = dates[0];
            
            // Set date inputs
            document.getElementById('endDate').value = today;
            document.getElementById('endDate').min = oldestCalendarDate;
            document.getElementById('endDate').max = today;
            
            document.getElementById('startDate').value = oldestCalendarDate;
            document.getElementById('startDate').min = oldestCalendarDate;
            document.getElementById('startDate').max = today;
            
            // Set current filter
            currentFilter.start = oldestCalendarDate;
            currentFilter.end = today;
            
            // Save to session storage
            sessionStorage.setItem('attendance_start', oldestCalendarDate);
            sessionStorage.setItem('attendance_end', today);
        }
    } catch (error) {
        // Fallback to defaults
        document.getElementById('endDate').value = today;
        document.getElementById('endDate').max = today;
        document.getElementById('startDate').value = today;
        document.getElementById('startDate').max = today;
        currentFilter.start = today;
        currentFilter.end = today;
    }
}

function getStatusClass(v) {
    v = parseInt(v);
    if (v === 0) return 'select-na';
    if (v === 1) return 'select-present';
    if (v === 2) return 'select-absent';
    if (v === 3) return 'select-half';
    return 'select-na';
}

function getQueryParam(p) {
    return new URLSearchParams(window.location.search).get(p);
}

async function generateAndFilter() {
    const start = document.getElementById('startDate').value;
    const end = document.getElementById('endDate').value;
    const workerId = document.getElementById('filterWorkerId').value;
    const adminId = document.getElementById('adminId').value;

    if (!workerId) return alert("No worker selected");
    if (!start) return alert("Please select start date");
    if (!end) return alert("Please select end date");
    if (start > end) return alert("Start date cannot be after end date");
    
    // Validate date ranges
    if (start < oldestCalendarDate) {
        alert(`Start date cannot be before ${oldestCalendarDate}`);
        document.getElementById('startDate').value = oldestCalendarDate;
        return;
    }
    
    if (end > today) {
        alert(`End date cannot be after ${today}`);
        document.getElementById('endDate').value = today;
        return;
    }

    currentFilter = { start, end, workerId: workerId.trim() };
    sessionStorage.setItem('attendance_start', start);
    sessionStorage.setItem('attendance_end', end);

    document.getElementById('logs').innerHTML = `<tr><td colspan="6" class="empty-msg">Fetching data…</td></tr>`;

    if (start && end) {
        try {
            await fetch(API_CALENDAR, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-ADMIN-ID': adminId },
                body: JSON.stringify({ start_date: start, end_date: end })
            });
        } catch (error) {
            alert('Failed to generate calendar');
        }
    }

    loadLogs();
}

async function loadLogs() {
    try {
        const res = await fetch(API_ATTENDANCE);
        const json = await res.json();
        const tbody = document.getElementById('logs');

        if (!json.data?.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="empty-msg">No attendance found</td></tr>`;
            return;
        }

        const filtered = json.data.filter(r => {
            if (r.worker_id.toString() !== currentFilter.workerId) return false;
            if (currentFilter.start && currentFilter.end) {
                return r.actual_date >= currentFilter.start && r.actual_date <= currentFilter.end;
            }
            return true;
        });

        if (!filtered.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="empty-msg">No records found</td></tr>`;
            return;
        }

        tbody.innerHTML = filtered.map(r => `
            <tr id="row-${r.id}">
                <td class="ps-4 small">${r.id}</td>
                <td><strong>${r.actual_date}</strong></td>
                <td>${r.worker_name}<br><small class="text-muted">${r.worker_id}</small></td>
                <td>
                    <select class="status-select ${getStatusClass(r.worker_attendance)}"
                            onchange="onStatusChange(${r.id}, 'worker', this.value)"
                            data-original="${r.worker_attendance}">
                        <option value="0" ${r.worker_attendance==0?'selected':''}>N/A</option>
                        <option value="1" ${r.worker_attendance==1?'selected':''}>Present</option>
                        <option value="2" ${r.worker_attendance==2?'selected':''}>Absent</option>
                        <option value="3" ${r.worker_attendance==3?'selected':''}>Half</option>
                    </select>
                </td>
                <td>
                    <select class="status-select ${getStatusClass(r.customer_side_attendance)}"
                            onchange="onStatusChange(${r.id}, 'customer', this.value)"
                            data-original="${r.customer_side_attendance}">
                        <option value="0" ${r.customer_side_attendance==0?'selected':''}>N/A</option>
                        <option value="1" ${r.customer_side_attendance==1?'selected':''}>Present</option>
                        <option value="2" ${r.customer_side_attendance==2?'selected':''}>Absent</option>
                        <option value="3" ${r.customer_side_attendance==3?'selected':''}>Half</option>
                    </select>
                </td>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-success" onclick="updateAttendance(${r.id})" id="btn-${r.id}" disabled>Update</button>
                </td>
            </tr>`).join('');
    } catch (error) {
        document.getElementById('logs').innerHTML = `<tr><td colspan="6" class="empty-msg">Error loading data</td></tr>`;
    }
}

function onStatusChange(attendanceId, type, newValue) {
    const row = document.getElementById(`row-${attendanceId}`);
    const select = document.querySelector(`#row-${attendanceId} select[onchange*="${type}"]`);
    const originalValue = select.getAttribute('data-original');
    
    select.className = `status-select ${getStatusClass(newValue)}`;
    
    if (parseInt(newValue) !== parseInt(originalValue)) {
        document.getElementById(`btn-${attendanceId}`).disabled = false;
        row.classList.add('border-warning');
    } else {
        document.getElementById(`btn-${attendanceId}`).disabled = true;
        row.classList.remove('border-warning');
    }
}

async function updateAttendance(attendanceId) {
    const row = document.getElementById(`row-${attendanceId}`);
    const workerSelect = row.querySelector('select[onchange*="worker"]');
    const customerSelect = row.querySelector('select[onchange*="customer"]');
    
    const updateData = {
        id: attendanceId,
        worker_attendance: parseInt(workerSelect.value),
        customer_side_attendance: parseInt(customerSelect.value)
    };
    
    try {
        const response = await fetch(API_UPDATE, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-ADMIN-ID': document.getElementById('adminId').value },
            body: JSON.stringify(updateData)
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            alert('Updated!');
            workerSelect.setAttribute('data-original', workerSelect.value);
            customerSelect.setAttribute('data-original', customerSelect.value);
            document.getElementById(`btn-${attendanceId}`).disabled = true;
            row.classList.remove('border-warning');
        } else {
            alert(`Update failed: ${result.message || 'Error'}`);
        }
    } catch (error) {
        alert('Failed to update');
    }
}

window.addEventListener('DOMContentLoaded', async () => {
    await initDates();
    const workerId = getQueryParam('worker_id');
    if (workerId) {
        document.getElementById('filterWorkerId').value = workerId;
        currentFilter.workerId = workerId.trim();
        loadLogs();
    }
    
    // Date validation
    document.getElementById('startDate').addEventListener('change', function() {
        const endDate = document.getElementById('endDate').value;
        if (this.value > endDate) {
            alert("Start date cannot be after end date");
            this.value = currentFilter.start;
        }
    });
    
    document.getElementById('endDate').addEventListener('change', function() {
        const startDate = document.getElementById('startDate').value;
        if (this.value < startDate) {
            alert("End date cannot be before start date");
            this.value = currentFilter.end;
        }
        if (this.value > today) {
            alert(`Cannot select future dates (max: ${today})`);
            this.value = today;
        }
    });
});
</script>
</body>
</html>