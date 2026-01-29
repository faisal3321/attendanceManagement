<?= view('layouts/header') ?>

<h3 class="mb-3">Mark Attendance</h3>

<div id="attendanceList">
    <p>Loading attendance records...</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAttendance();
});

async function loadAttendance() {
    try {
        // Note: You need to create an API endpoint to get customer's attendance
        // For now, this is a placeholder
        document.getElementById('attendanceList').innerHTML = `
            <div class="alert alert-info">
                API endpoint needed to fetch customer's attendance records.
                Create in AttendanceController: getCustomerAttendance(customerId)
            </div>
        `;
        
        // The endpoint should return attendance records where customer can mark present/absent
        // Customer can only update customer_side_attendance field
        
    } catch (error) {
        console.error('Error:', error);
    }
}

// Function to update attendance (will be called when user clicks present/absent)
async function updateAttendance(id, status) {
    try {
        const res = await fetch('/api/attendance/customer/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: id,
                customer_side_attendance: status // 1 for present, 0 for absent
            })
        });
        
        const data = await res.json();
        
        if (data.success) {
            alert('Attendance updated!');
            loadAttendance();
        } else {
            alert(data.message || 'Update failed');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}
</script>

<?= view('layouts/footer') ?>