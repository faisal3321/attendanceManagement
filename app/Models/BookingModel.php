<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table            = 'bookings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    protected $allowedFields    = [
        'booking_id', 
        'customer_id', 
        'worker_id', 
        'duration_months'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    
    // Get bookings by joining worker and customer data
    public function getFullDetails($bookingId = null)
    {
        $builder = $this->select('bookings.*, workers.name as worker_name, customers.name as customer_name');
        $builder->join('workers', 'workers.worker_id = bookings.worker_id');
        $builder->join('customers', 'customers.customer_id = bookings.customer_id');

        if ($bookingId) {
            return $builder->where('bookings.booking_id', $bookingId)->first();
        }

        return $builder->findAll();
    }
  
}
