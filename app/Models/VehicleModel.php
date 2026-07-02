<?php
namespace App\Models;
use CodeIgniter\Model;

class VehicleModel extends Model
{
    protected $table = 'tbl_vehicle_price';
    protected $primaryKey = 'id';
    protected $allowedFields = ['make_id','model_id','color_id','price_per_day','quantity','status'];
    
    // Make ke hisaab se models get karne ka function
    public function getModelsByMake($make_id)
    {
        return $this->db->table('tbl_vehicle_model')
                        ->where('make_id', $make_id)
                        ->where('status', 1)
                        ->get()
                        ->getResultArray();
    }
}
