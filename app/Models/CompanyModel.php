<?php
namespace App\Models;
use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table = 'tbl_vehicle_make';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name','status'];
}
