<?php
    namespace App\Models;
    use CodeIgniter\Model;
    class CompanyModelsModel extends Model
    {
        protected $table = 'tbl_vehicle_model';
        protected $primaryKey = 'id';
        protected $allowedFields = ['make_id','name','status'];
    }
?>