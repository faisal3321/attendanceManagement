<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('customer/dashboard') ?>">Attendees</a>
            <div class="ms-auto">
                <a href="<?= base_url('customer/dashboard') ?>" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            <!-- <button class="btn btn-success btn-sm" onclick="syncAttendance()">Sync Today's Attendance</button> -->
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Worker Attendance Logs</h3>
                    <button class="btn btn-primary btn-sm" onclick="fetchAttendance()">Refresh List</button>
                </div>

                <div class="card shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Worker Name</th>
                                    <th>Punch In/Out</th>
                                    <th>Worker_Attendance</th>
                                    <th>Customer_Attendance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTable">
                                <tr>
                                    <td colspan="6" class="text-center py-4">Loading attendance records...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const customerId = "<?= session()->get('customer_id') ?>";

        async function fetchAttendance() {
            try {
                const response = await fetch(`<?= base_url('api/attendance/customer/')?>${customerId}`);
                const result = await response.json();
                
                const tbody = document.getElementById('attendanceTable');
                tbody.innerHTML = '';

                if (result.success && result.data.length > 0) {
                    result.data.forEach(row => {
                        const isDiscrepancy = row.discrepancy == 1;
                        tbody.innerHTML += `
                            <tr class="${isDiscrepancy ? 'table-warning' : ''}">
                                <td>${row.attendance_date}</td>
                                <td><strong>${row.worker_name}</strong></td>
                                <td><small>${row.punch_in} - ${row.punch_out}</small></td>
                                <td>
                                    <span class="badge ${row.worker_attendance == 1 ? 'bg-success' : 'bg-danger'}">
                                        ${row.worker_attendance == 1 ? 'Present' : 'Absent'}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge ${row.customer_side_attendance == 1 ? 'bg-info' : 'bg-secondary'}">
                                        ${row.customer_side_attendance == 1 ? 'Confirmed Present' : 'Marked Absent'}
                                    </span>
                                    ${isDiscrepancy ? '<br><small class="text-danger fw-bold">Conflict Detected</small>' : ''}
                                </td>
                                <td>
                                    <select class="form-select form-select-sm w-auto" onchange="updateStatus(${row.id}, this.value)">
                                        <option value="1" ${row.customer_side_attendance == 1 ? 'selected' : ''}>Present</option>
                                        <option value="0" ${row.customer_side_attendance == 0 ? 'selected' : ''}>Absent</option>
                                    </select>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No attendance logs found.</td></tr>';
                }
            } catch (error) {
                console.error("Error:", error);
            }
        }


       async function updateStatus(attendanceId, status) {
            try {
                const response = await fetch(`<?= base_url('api/attendance/customer/update') ?>`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: attendanceId,
                        customer_side_attendance: parseInt(status) 
                    })
                });
                
                const result = await response.json();
                
                if(result.success) {
                    console.log(result.message); 
                    fetchAttendance(); 
                } else {
                    alert(result.messages.error || "Update failed");
                }
            } catch (error) {
                alert("Network error. Please try again.");
            }
        }


        // async function syncAttendance() {
        //     try {
        //         const response = await fetch(`<?= base_url('api/attendance/sync') ?>`, { method: 'POST' });
        //         const result = await response.json();
        //         alert(result.message);
        //         fetchAttendance(); // Reload the table
        //     } catch (error) {
        //         console.error("Sync failed", error);
        //     }
        // }
        
        window.onload = fetchAttendance;
    </script>
    
</body>
</html>