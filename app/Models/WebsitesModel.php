<?php
namespace App\Models;
use CodeIgniter\Model;

class WebsitesModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tbl_websites';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'short_code', 'web_name', 'airport_name', 'domain', 'legal_name', 'email', 'created_at', 'type', 'code', 'title', 'logo','reviews', 'introduction', 'customer_service', 'address', 'company_id', 'footer', 'terms_conditions', 'privacy_policy', 'contact_us' ,'header_color','footer_color','google_analytics_id','google_adwords_id','google_conversion_event_id','secret_key','publisher_key','cur','why_choose', 'terminals','payment_redirection','status'];
    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
