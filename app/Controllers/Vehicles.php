<?php
    namespace App\Controllers;
    use App\Models\VehicleModel;
    use App\Models\VehicleMakeModel;
    use App\Models\VehicleColorModel;
    use CodeIgniter\API\ResponseTrait;
    class Vehicles extends BaseController
    {
        use ResponseTrait;
        protected $Vehicle;
        protected $Make;
        protected $Color;
        public function __construct()
        {
            $this->Vehicle = new VehicleModel();
            $this->Make = new VehicleMakeModel();
            $this->Color = new VehicleColorModel();
        }
        public function index()
        {
            $data = [
                "page_title" => "Vehicles",
                "breadcrumb" => [
                    ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                    ["href" => base_url('vehicles'), "title" => "Vehicles", "status" => "", "link" => false]
                ],
                "makes" => $this->Make->where('status',1)->findAll(),
                "colors" => $this->Color->where('status',1)->findAll()
            ];
            return view('vehicles/view', $data);
        }
        public function get_models()
        {
            $make_id = $this->request->getVar('make_id');
            $models = $this->Vehicle->getModelsByMake($make_id);
            return $this->response->setJSON($models);
        }
        public function get_record()
        {
            $id = $this->request->getVar('id');
            $id = id_de($id);
            $data = $this->Vehicle->where('id', $id)->first();
            if($data){
                return $this->response->setJSON(['status'=>true,'data'=>$data]);
            }else{
                return $this->response->setJSON(['status'=>false,'message'=>'Record not found']);
            }
        }
        public function save()
        {
            $validate = $this->validate([
                'make_id' => 'required',
                'model_id' => 'required',
                'color_id' => 'required',
                'price_per_day' => 'required|decimal',
                'quantity' => 'required|numeric'
            ]);
            if(!$validate){
                return $this->response->setJSON(['status'=>false,'errors'=>$this->validation->getErrors()]);
            }
            // Duplicate Check
            $exists = $this->Vehicle
                ->where('make_id', $this->request->getPost('make_id'))
                ->where('model_id', $this->request->getPost('model_id'))
                ->where('color_id', $this->request->getPost('color_id'))
                ->first();
            if ($exists) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'This vehicle already exists in system.'
                ]);
            }
            $data = [
                'make_id' => $this->request->getPost('make_id'),
                'model_id' => $this->request->getPost('model_id'),
                'color_id' => $this->request->getPost('color_id'),
                'price_per_day' => $this->request->getPost('price_per_day'),
                'quantity' => $this->request->getPost('quantity'),
                'status' => $this->request->getPost('status')
            ];
            try {
                $this->Vehicle->insert($data);
                return $this->response->setJSON(['status'=>true,'message'=>'Vehicle added successfully']);
            } catch (\Exception $e){
                return $this->response->setJSON(['status'=>false,'message'=>'Duplicate or error occurred']);
            }
        }
        public function update()
        {
            $id = id_de($this->request->getPost('id'));
            $validate = $this->validate([
                'make_id' => 'required',
                'model_id' => 'required',
                'color_id' => 'required',
                'price_per_day' => 'required|decimal',
                'quantity' => 'required|numeric'
            ]);
            if(!$validate)
            {
                return $this->response->setJSON(['status'=>false,'errors'=>$this->validation->getErrors()]);
            }
            $exists = $this->Vehicle
                ->where('make_id', $this->request->getPost('make_id'))
                ->where('model_id', $this->request->getPost('model_id'))
                ->where('color_id', $this->request->getPost('color_id'))
                 ->where('id !=', $id) // current record ignore karo
                ->first();
            if ($exists) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'This vehicle already exists in system.'
                ]);
            }
            $data = [
                'make_id' => $this->request->getPost('make_id'),
                'model_id' => $this->request->getPost('model_id'),
                'color_id' => $this->request->getPost('color_id'),
                'price_per_day' => $this->request->getPost('price_per_day'),
                'quantity' => $this->request->getPost('quantity'),
                'status'       => $this->request->getPost('status')
            ];
            $res = $this->Vehicle->update($id, $data);
            if($res)
            {
                return $this->response->setJSON(['status'=>true,'message'=>'Vehicle updated successfully']);
            }else
            {
                return $this->response->setJSON(['status'=>false,'message'=>'Error on update']);
            }
        }
        public function delete_record()
        {
            $id = $this->request->getVar('id');
            $id = id_de($id); // Operator ke jaisa ID decryption
            $data = $this->Vehicle->where('id', $id)->first();
            if (sizeof($data) > 0) {
                $response = $this->Vehicle->delete($id);
                if ($response) {
                    $result = ['status' => true, "message" => "Vehicle successfully deleted"];
                } else {
                    $result = ['status' => false, "message" => "Unexpected error on delete record"];
                }
            } else {
                $result = ['status' => false, "message" => "Requested vehicle not found in system"];
            }
            return $this->response->setJSON($result);
        }
        public function get()
        {
            $request = \Config\Services::request();
            $draw = intval($request->getGet('draw') ?? 1);
            $start = intval($request->getGet('start') ?? 0);
            $length = intval($request->getGet('length') ?? 10);
            $search = $request->getGet('search')['value'] ?? '';
            $orderColumnIndex = $request->getGet('order')[0]['column'] ?? 0;
            $orderDir = $request->getGet('order')[0]['dir'] ?? 'asc';
            // Column mapping for ordering
            $columns = [
                0 => 'vp.created_at',
                1 => 'vmk.name',
                2 => 'vmd.name',
                3 => 'vcl.color_name',
                4 => 'vp.price_per_day',
                // 5 => 'vp.quantity',
                6 => 'vp.status'
            ];
            $builder = $this->db->table('tbl_vehicle_price vp')->select('vp.id, vp.created_at, vp.price_per_day, vp.quantity, vp.status, vmk.name AS make_name, vmd.name AS model_name, vcl.color_name')->join('tbl_vehicle_make vmk', 'vmk.id = vp.make_id', 'left')->join('tbl_vehicle_model vmd', 'vmd.id = vp.model_id', 'left')->join('tbl_vehicle_color vcl', 'vcl.id = vp.color_id', 'left');
                // Search
            if(!empty($search))
            {
                $builder->groupStart()->like('vmk.name', $search)->orLike('vmd.name', $search)->orLike('vcl.color_name', $search)->orLike('vp.price_per_day', $search)->orLike('vp.quantity', $search)->orLike('vp.status', $search)->groupEnd();
            }
            // Total records after filtering
            $recordsFiltered = $builder->countAllResults(false);
            // Order & Limit
            $orderColumn = $columns[$orderColumnIndex] ?? 'vp.created_at';
            $builder->orderBy($orderColumn, $orderDir)
                    ->limit($length, $start);
            $query = $builder->get();
            $data = [];
            foreach($query->getResult() as $row)
            {
                $statusBadge = $row->status == 1 ? 
                    "<span class='badge badge-glow bg-success'>Active</span>" : 
                    "<span class='badge badge-glow bg-danger'>Inactive</span>";
                $id = id_en($row->id);
                $action = "<div class='btn-group'>
                              <a href='javascript:void(0);' class='btn btn-outline-primary btn-sm dropdown-toggle' data-bs-toggle='dropdown'>Actions</a>
                              <div class='dropdown-menu'>
                                  <a class='dropdown-item' href='javascript:void(0);' onclick='edit_data(`$id`)'>
                                      <i data-feather='edit'></i> Edit
                                  </a>
                                  <a class='dropdown-item' href='javascript:void(0);' onclick='delete_data(`$id`)'>
                                      <i data-feather='trash'></i> Delete
                                  </a>
                              </div>
                           </div>";
                $data[] = [
                    date("d-m-Y", strtotime($row->created_at)),
                    $row->make_name,
                    $row->model_name,
                    $row->color_name,
                    "Rs. ".$row->price_per_day,
                    $row->quantity,
                    $statusBadge,
                    $action
                ];
            }
            // Total records in table (without filtering)
            $totalRecords = $this->db->table('tbl_vehicle_price')->countAllResults();
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
        }
    }
?>