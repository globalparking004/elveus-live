<?php
namespace App\Models;
use CodeIgniter\Model;

class SecurityGuardModel extends Model
{
    protected $table = 'tbl_security_guards';
    protected $primaryKey = 'id';
    protected $allowedFields = ['price','status'];
}
