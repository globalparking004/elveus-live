<?php
namespace App\Models;
use CodeIgniter\Model;

class ProductsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tbl_products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'product_code', 'name', 'name_ar','telephone', 'address', 'postcode', 'parent', 'airport' , 'latitude', 'longitude', 'logo','logo1', 'distance_miles',
        'transfer_time', 'customize1', 'customize2', 'is_none_amendable', 'meet_and_greet', 'on_airport', 'park_mark', 'product_type', 'notice_period', 'opening_time', 'closing_time',
        'commission', 'linked_product_code', 'linked_price', 'directions', 'introduction', 'information', 'security_measures',
        'arrival_procedures', 'departure_procedures', 'disabled_facilities', 'transfers', 'score_price', 'score_accessibility',
        'score_efficiency', 'score_security', 'capacity', 'adjust_prices_by_capacity', 'capacity_threshold_one', 'capacity_threshold_one_increase', 'capacity_threshold_two', 'capacity_threshold_two_increase','created_at', 'updated_at', 'deleted_at', 'operator_id','exclusive_to_website_id','useful_information', 'driver_contact', 'parking_facility_contact', 'what_to_do_when_you_arrive', 'what_to_do_when_you_return', 'security_information','get_limiter_time','operator_id_show','map_link','capacity_threshold_day','replace_product_code','min_days', 'max_days','directions_ar','introduction_ar','information_ar','security_measures_ar','arrival_procedures_ar','departure_procedures_ar','disabled_facilities_ar','transfers_ar'
    ];
    // Dates
    protected $useTimestamps = true;
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
