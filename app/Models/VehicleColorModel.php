<?php
namespace App\Models;
use CodeIgniter\Model;

class VehicleColorModel extends Model
{
    protected $table = 'tbl_vehicle_color';
    protected $primaryKey = 'id';
    protected $allowedFields = ['color_name','status'];
}
