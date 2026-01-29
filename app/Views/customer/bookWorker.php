<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Worker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container d-flex justify-content-between">
            <a class="navbar-brand text-dark text-decoration-none fw-bold" href="<?= base_url('/') ?>">Attendees</a>
            <div class="ms-auto">
                <a href="<?= base_url('customer/dashboard') ?>" class="text-decoration-none text-dark">Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Book a Worker</h2>
                
                <form id="bookingForm" class="border p-4 rounded bg-white shadow-sm">
                    
                    <div class="mb-3">
                        <label class="form-label">Your Customer ID:</label>
                        <input type="text" name="customer_id" class="form-control" placeholder="Enter your ID (e.g. CUST123)" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Worker:</label>
                        <select name="worker_id" id="workerSelect" class="form-select" required>
                            <option value="">Loading workers...</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Duration (Months):</label>
                        <select name="duration_months" class="form-select" required>
                            <option value="1">1 Month</option>
                            <option value="3">3 Months</option>
                            <option value="6">6 Months</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" id="submitBtn" class="btn btn-success" disabled>Confirm Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // 1. Fetch workers when the page loads
        window.addEventListener('DOMContentLoaded', () => {
            const workerSelect = document.getElementById('workerSelect');
            const submitBtn = document.getElementById('submitBtn');

            // Replace this URL with your actual API endpoint that lists workers
            fetch('<?= base_url('api/workers') ?>') 
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data.length > 0) {
                        workerSelect.innerHTML = '<option value="" selected disabled>Choose a worker...</option>';
                        
                        result.data.forEach(worker => {
                            const option = document.createElement('option');
                            option.value = worker.worker_id; // Value sent to database
                            option.textContent = worker.name; // Name shown to customer
                            workerSelect.appendChild(option);
                        });
                        
                        submitBtn.disabled = false; // Enable button once loaded
                    } else {
                        workerSelect.innerHTML = '<option>No workers available</option>';
                    }
                })
                .catch(err => {
                    console.error("Error loading workers:", err);
                    workerSelect.innerHTML = '<option>Error loading workers</option>';
                });
        });

        // 2. Handle Form Submission (Same as before)
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            fetch('<?= base_url('api/book-worker') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert("Success! " + result.message);
                    window.location.href = "<?= base_url('customer/dashboard') ?>";
                } else {
                    alert("Error: " + result.message);
                }
            });
        });
    </script>
</body>
</html>