<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\VehicleModel;
use App\Models\SecurityGuardModel;

class CarBookingAPI extends ResourceController
{
    protected $format = 'json';
    protected $vehicleModel;
    protected $guardModel;

    public function __construct()
    {
        $this->vehicleModel = new VehicleModel();
        $this->guardModel = new SecurityGuardModel();
	 // CORS headers
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, Authorization');
    header('Access-Control-Allow-Credentials: true');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Max-Age: 86400');
        http_response_code(200);
        exit();
    }
    }

    /**
     * Get all cars (optional filters: make_id, model_id, color_id)
     */
    public function get_cars()
    {
        $make_id = $this->request->getGet('make_id');
        $model_id = $this->request->getGet('model_id');
        $color_id = $this->request->getGet('color_id');

        $builder = $this->vehicleModel->builder()
            ->select('tbl_vehicle_price.id, tbl_vehicle_price.make_id, tbl_vehicle_price.model_id, tbl_vehicle_price.color_id, tbl_vehicle_price.price_per_day,tbl_vehicle_price.quantity,
                      tbl_vehicle_make.name as make_name,
                      tbl_vehicle_model.name as model_name,
                      tbl_vehicle_color.color_name as color_name')
            ->join('tbl_vehicle_make', 'tbl_vehicle_make.id = tbl_vehicle_price.make_id')
            ->join('tbl_vehicle_model', 'tbl_vehicle_model.id = tbl_vehicle_price.model_id')
            ->join('tbl_vehicle_color', 'tbl_vehicle_color.id = tbl_vehicle_price.color_id')
            ->where('tbl_vehicle_price.status', 1);

        if ($make_id) $builder->where('tbl_vehicle_price.make_id', $make_id);
        if ($model_id) $builder->where('tbl_vehicle_price.model_id', $model_id);
        if ($color_id) $builder->where('tbl_vehicle_price.color_id', $color_id);

        $cars = $builder->get()->getResultArray();

        return $this->respond(['status' => true, 'data' => $cars]);
    }

    /**
     * Get models by make_id
     */
    public function get_models($make_id = null)
    {
        if (!$make_id) {
            return $this->respond(['status' => false, 'message' => 'Make ID is required']);
        }

        $models = $this->vehicleModel
            ->select('tbl_vehicle_model.id, tbl_vehicle_model.name')
            ->join('tbl_vehicle_model', 'tbl_vehicle_model.id = tbl_vehicle_price.model_id')
            ->where('tbl_vehicle_price.make_id', $make_id)
            ->where('tbl_vehicle_price.status', 1)
            ->groupBy('tbl_vehicle_model.id')
            ->findAll();

        return $this->respond(['status' => true, 'data' => $models]);
    }

    /**
     * Get colors by make + model
     */
    public function get_colors()
    {
        $make_id = $this->request->getGet('make_id');
        $model_id = $this->request->getGet('model_id');

        if (!$make_id || !$model_id) {
            return $this->respond(['status' => false, 'message' => 'Make and Model are required']);
        }

        $colors = $this->vehicleModel
            ->select('tbl_vehicle_price.id as price_id, 
                      tbl_vehicle_color.id as color_id, 
                      tbl_vehicle_color.color_name,
                      tbl_vehicle_price.quantity,
		      tbl_vehicle_price.price_per_day')
    ->join('tbl_vehicle_color', 'tbl_vehicle_color.id = tbl_vehicle_price.color_id')
    ->where('tbl_vehicle_price.make_id', $make_id)
    ->where('tbl_vehicle_price.model_id', $model_id)
    ->where('tbl_vehicle_price.status', 1)
    ->groupBy('tbl_vehicle_price.id')
    ->findAll();
        return $this->respond(['status' => true, 'data' => $colors]);
    }

    /**
     * Get single car by make + model + color
     */
    public function get_car()
    {
        $make_id = $this->request->getGet('make_id');
        $model_id = $this->request->getGet('model_id');
        $color_id = $this->request->getGet('color_id');

        if (!$make_id || !$model_id || !$color_id) {
            return $this->respond(['status' => false, 'message' => 'Make, Model, and Color are required']);
        }

        $car = $this->vehicleModel
            ->where('make_id', $make_id)
            ->where('model_id', $model_id)
            ->where('color_id', $color_id)
            ->where('status', 1)
            ->first();

        return $this->respond(['status' => $car ? true : false, 'data' => $car]);
    }

public function get_guard_price()
{
    $guard = $this->guardModel->first(); // $this->guardModel use karo
    if($guard){
        return $this->respond([
            'status' => true,
            'price_per_day' => (float)$guard['price']
        ]);
    } else {
        return $this->respond([
            'status' => false,
            'message' => 'No guard found',
            'price_per_day' => 0
        ]);
    }
}

    /**
     * Calculate total price for selected vehicle + security guards
     */


// assume CodeIgniter Controller
public function calculate_total_proxy()
{
    $data = $this->request->getJSON(true);

    if (!$data) {
        return $this->respond([
            'status' => false,
            'message' => 'No JSON received',
            'raw' => $this->request->getBody()
        ]);
    }

    $cars       = $data['cars'] ?? [];
    $start_date = $data['start_date'] ?? null;
    $end_date   = $data['end_date'] ?? null;
    $num_guards = isset($data['num_guards']) ? (int)$data['num_guards'] : 0;

    if (!$start_date || !$end_date || empty($cars)) {
        return $this->respond([
            'status' => false,
            'message' => 'Missing required fields',
            'debug' => $data
        ]);
    }

    $total_vehicle_price = 0;

    // calculate number of days
    $days = (strtotime($end_date) - strtotime($start_date)) / 86400 + 1;
    if ($days < 1) $days = 1;

    foreach ($cars as $car) {
        $vehicle_id   = $car['vehicle_id'] ?? null;
        $num_vehicles = isset($car['num_vehicles']) ? (int)$car['num_vehicles'] : 1;

        if (!$vehicle_id) {
            return $this->respond([
                'status' => false,
                'message' => 'Vehicle ID missing in one of the cars',
                'debug' => $car
            ]);
        }

        $vehicle = $this->vehicleModel->find($vehicle_id);
        if (!$vehicle) {
            return $this->respond([
                'status' => false,
                'message' => 'Vehicle not found',
                'debug_id' => $vehicle_id
            ]);
        }

        $price_per_day = (float) $vehicle['price_per_day'];
        $total_vehicle_price += $price_per_day * $num_vehicles * $days;
    }

    // Guard price
    $guard_price = 0;
    if ($num_guards > 0) {
        $guard = $this->guardModel->first();
        $guard_price = $guard ? (float)$guard['price'] : 0;
    }

    $total_guard_price = $guard_price * $num_guards * $days;
    $grand_total = $total_vehicle_price + $total_guard_price;

    return $this->respond([
        'status' => true,
        'data' => [
            'days' => $days,
            'total_vehicle_price' => $total_vehicle_price,
            'total_guard_price' => $total_guard_price,
            'grand_total' => $grand_total
        ]
    ]);
}


}
