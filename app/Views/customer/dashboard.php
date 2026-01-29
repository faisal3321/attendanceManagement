<?= view('layouts/header') ?>

<h3 class="mb-3">Customer Dashboard</h3>
<p>Welcome, <?= $customer_name ?></p>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h5>Book Worker</h5>
                <a href="/customer/book-worker" class="btn btn-primary">Book Now</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h5>Mark Attendance</h5>
                <a href="/customer/mark-attendance" class="btn btn-success">Mark Attendance</a>
            </div>
        </div>
    </div>
</div>

<h4>My Bookings</h4>
<div id="bookingsList" class="mt-3">
    <p>Loading bookings...</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerId = "<?= $customer_id ?>";
    loadBookings(customerId);
});

async function loadBookings(customerId) {
    try {
        const res = await fetch(`/api/mybookings/${customerId}`);
        const data = await res.json();
        
        const bookingsList = document.getElementById('bookingsList');
        
        if (data.data && data.data.length > 0) {
            let html = '<div class="list-group">';
            data.data.forEach(booking => {
                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>${booking.booking_id}</strong><br>
                                Worker: ${booking.worker_name}<br>
                                Duration: ${booking.duration_months} months
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            bookingsList.innerHTML = html;
        } else {
            bookingsList.innerHTML = '<p>No active bookings found.</p>';
        }
    } catch (error) {
        document.getElementById('bookingsList').innerHTML = '<p class="text-danger">Error loading bookings.</p>';
    }
}
</script>

<?= view('layouts/footer') ?>