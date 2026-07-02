<?php
namespace App\Models;
use CodeIgniter\Model;

class ColorModel extends Model
{
    protected $table = 'tbl_vehicle_color';
    protected $primaryKey = 'id';
    protected $allowedFields = ['color_name','status'];
}
