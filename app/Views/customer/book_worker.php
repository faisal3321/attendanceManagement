<?= view('layouts/header') ?>

<h3 class="mb-3">Book a Worker</h3>

<form id="bookingForm">
    <div class="mb-3">
        <label>Select Worker</label>
        <select class="form-control" id="worker_id" name="worker_id" required>
            <option value="">Loading workers...</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label>Duration (Months)</label>
        <select class="form-control" id="duration_months" name="duration_months" required>
            <option value="1">1 Month</option>
            <option value="3">3 Months</option>
            <option value="6">6 Months</option>
            <option value="12">12 Months</option>
        </select>
    </div>
    
    <input type="hidden" id="customer_id" value="<?= session()->get('customer_id') ?>">
    
    <button class="btn btn-primary" type="submit">Book Worker</button>
    <a href="/customer/dashboard" class="btn btn-secondary">Back</a>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadWorkers();
});

async function loadWorkers() {
    try {
        // Note: You need to create a new API endpoint in WorkerController to get available workers
        // For now, we'll use a placeholder
        const select = document.getElementById('worker_id');
        select.innerHTML = '<option value="">Please create API endpoint for available workers</option>';
        
        // You should add this to WorkerController:
        /*
        public function getAvailableWorkers() {
            $model = new WorkerModel();
            $workers = $model->where('status', 'active')->findAll();
            return $this->respond(['success' => true, 'data' => $workers]);
        }
        */
        
        // Then uncomment this:
        /*
        const res = await fetch('/api/workers/available');
        const data = await res.json();
        
        if (data.success && data.data.length > 0) {
            select.innerHTML = '<option value="">Select Worker</option>';
            data.data.forEach(worker => {
                select.innerHTML += `<option value="${worker.worker_id}">${worker.name} (${worker.worker_id})</option>`;
            });
        }
        */
        
    } catch (error) {
        console.error('Error:', error);
    }
}

document.getElementById('bookingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        customer_id: document.getElementById('customer_id').value,
        worker_id: this.worker_id.value,
        duration_months: this.duration_months.value
    };
    
    try {
        const res = await fetch('/api/book/worker', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await res.json();
        
        if (data.success) {
            alert('Worker booked successfully!');
            window.location.href = '/customer/dashboard';
        } else {
            alert(data.message || 'Booking failed');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
});
</script>

<?= view('layouts/footer') ?>