<?= view('layouts/header') ?>

<h3 class="mb-3">Manage Attendance</h3>

<div id="attendanceList">
    <p>Loading attendance records...</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAttendance();
});

async function loadAttendance() {
    try {
        const res = await fetch('/api/attendance');
        const data = await res.json();
        
        const container = document.getElementById('attendanceList');
        
        if (data.success && data.data.length > 0) {
            let html = '<div class="table-responsive"><table class="table table-striped"><thead><tr>';
            html += '<th>Date</th><th>Worker</th><th>Customer</th><th>Admin Status</th><th>Customer Status</th><th>Discrepancy</th><th>Actions</th></tr></thead><tbody>';
            
            data.data.forEach(record => {
                html += `<tr>
                    <td>${record.attendance_date}</td>
                    <td>${record.worker_name}</td>
                    <td>${record.customer_name}</td>
                    <td>${record.worker_attendance ? 'Present' : 'Absent'}</td>
                    <td>${record.customer_side_attendance ? 'Present' : 'Absent'}</td>
                    <td>${record.discrepancy ? 'Yes' : 'No'}</td>
                    <td>
                        <button onclick="overrideAttendance(${record.id})" class="btn btn-sm btn-warning">Override</button>
                    </td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
            container.innerHTML = html;
        } else {
            container.innerHTML = '<p>No attendance records found.</p>';
        }
    } catch (error) {
        container.innerHTML = '<p class="text-danger">Error loading attendance.</p>';
    }
}

function overrideAttendance(id) {
    // This would open a modal or form to update the attendance
    alert('Override functionality would open here for record ID: ' + id);
}
</script>

<?= view('layouts/footer') ?>