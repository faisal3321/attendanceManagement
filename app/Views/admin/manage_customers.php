<?= view('layouts/header') ?>

<h3 class="mb-3">Manage Customers</h3>

<div id="customersList">
    <p>Loading customers...</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Note: You need to create an API endpoint to get all customers
    // Add this to CustomerController: public function getAllCustomers()
    document.getElementById('customersList').innerHTML = `
        <div class="alert alert-info">
            API endpoint needed to fetch all customers.
            Create in CustomerController: getAllCustomers()
        </div>
    `;
});
</script>

<?= view('layouts/footer') ?>