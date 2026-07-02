<?php
namespace App\Controllers;
use App\Models\CompanyModelsModel;
use App\Models\VehicleMakeModel;
use CodeIgniter\API\ResponseTrait;

class Companymodels extends BaseController
{
    use ResponseTrait;

    protected $Model;
    protected $Make;

    public function __construct()
    {
        $this->Model = new CompanyModelsModel(); // Correct model included
        $this->Make = new VehicleMakeModel();
    }

    // Load View
    public function index()
    {
        $data = [
            "page_title" => "Company Models",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('companymodels'), "title" => "Models", "status" => "", "link" => false]
            ],
            "makes" => $this->Make->where('status',1)->findAll()
        ];
        return view('companymodels/view', $data);
    }

    // Save New Model
    public function save()
    {
        $validate = $this->validate([
            'make_id' => 'required',
            'name' => 'required'
        ]);

        if (!$validate) {
            return $this->response->setJSON(['status'=>false,'errors'=>$this->validation->getErrors()]);
        }

        // Duplicate Check
        $exists = $this->Model
            ->where('make_id', $this->request->getPost('make_id'))
            ->where('name', $this->request->getPost('name'))
            ->first();

        if ($exists) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'This model already exists for selected make.'
            ]);
        }

        $data = [
            'make_id' => $this->request->getPost('make_id'),
            'name' => $this->request->getPost('name'),
            'status' => $this->request->getPost('status')
        ];

        try {
            $this->Model->insert($data);
            return $this->response->setJSON(['status'=>true,'message'=>'Model added successfully']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status'=>false,'message'=>'Error occurred while adding model']);
        }
    }

    // Get Single Record for Edit
    public function get_record()
    {
        $id = id_de($this->request->getVar('id'));
        $data = $this->Model->where('id', $id)->first();

        if ($data) {
            return $this->response->setJSON(['status'=>true,'data'=>$data]);
        } else {
            return $this->response->setJSON(['status'=>false,'message'=>'Record not found']);
        }
    }

    // Update Model
    public function update()
    {
        $id = id_de($this->request->getPost('id'));

        $validate = $this->validate([
            'make_id' => 'required',
            'name' => 'required'
        ]);

        if(!$validate){
            return $this->response->setJSON(['status'=>false,'errors'=>$this->validation->getErrors()]);
        }

        // Duplicate check ignoring current record
        $exists = $this->Model
            ->where('make_id', $this->request->getPost('make_id'))
            ->where('name', $this->request->getPost('name'))
            ->where('id !=', $id)
            ->first();

        if ($exists) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'This model already exists for selected make.'
            ]);
        }

        $data = [
            'make_id' => $this->request->getPost('make_id'),
            'name' => $this->request->getPost('name'),
            'status' => $this->request->getPost('status')
        ];

        $res = $this->Model->update($id, $data);

        if($res){
            return $this->response->setJSON(['status'=>true,'message'=>'Model updated successfully']);
        } else {
            return $this->response->setJSON(['status'=>false,'message'=>'Error on update']);
        }
    }

    // Delete Model
    public function delete_record()
    {
        $id = id_de($this->request->getVar('id'));
        $data = $this->Model->where('id', $id)->first();
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_vehicle_price');
        $builder->where('model_id', $id);
        $count = $builder->countAllResults();

        if ($count > 0) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'You cannnot delete model. This model is linked to vehicle price'
            ]);
        }

        if ($data) {
            $response = $this->Model->delete($id);
            if ($response) {
                return $this->response->setJSON(['status' => true, 'message' => 'Model successfully deleted']);
            } else {
                return $this->response->setJSON(['status' => false, 'message' => 'Error on deleting record']);
            }
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Requested model not found']);
        }
    }

    // Get all models for DataTable
    // Get all models for DataTable
// Get all models for DataTable
public function get()
{
    $request = \Config\Services::request();
    $draw = intval($request->getGet('draw') ?? 1);
    $start = intval($request->getGet('start') ?? 0);
    $length = intval($request->getGet('length') ?? 10);
    $search = $request->getGet('search')['value'] ?? '';
    $orderColumnIndex = $request->getGet('order')[0]['column'] ?? 0;
    $orderDir = $request->getGet('order')[0]['dir'] ?? 'asc';

    $columns = [
        0 => 'cm.created_at',
        1 => 'vm.name',      // Make column
        2 => 'cm.name',      // Model column
        3 => 'cm.status',
    ];

    $builder = $this->db->table('tbl_vehicle_model cm')
        ->select('cm.id, cm.make_id, cm.name as model_name, cm.status, cm.created_at, vm.name as make_name')
        ->join('tbl_vehicle_make vm', 'vm.id = cm.make_id', 'left');

    // Search
    if(!empty($search)){
        $builder->groupStart()
            ->like('vm.name', $search)
            ->orLike('cm.name', $search)
            ->groupEnd();
    }

    $recordsFiltered = $builder->countAllResults(false);

    // Order and limit
    $orderColumn = $columns[$orderColumnIndex] ?? 'cm.created_at';
    $builder->orderBy($orderColumn, $orderDir)
            ->limit($length, $start);

    $query = $builder->get();
    $data = [];
    foreach($query->getResult() as $row){
        $statusBadge = $row->status == 1 ? "<span class='badge badge-glow bg-success'>Active</span>" :
                                            "<span class='badge badge-glow bg-danger'>Inactive</span>";
        $id = id_en($row->id);
        $action = "<div class='btn-group'>
                      <a href='javascript:void(0);' class='btn btn-outline-primary btn-sm dropdown-toggle' data-bs-toggle='dropdown'>Actions</a>
                      <div class='dropdown-menu'>
                          <a class='dropdown-item' href='javascript:void(0);' onclick='edit_data(`$id`)'><i data-feather='edit'></i> Edit</a>
                          <a class='dropdown-item' href='javascript:void(0);' onclick='delete_data(`$id`)'><i data-feather='trash'></i> Delete</a>
                      </div>
                   </div>";
        $data[] = [
            date("d-m-Y", strtotime($row->created_at)), // Created At
            $row->make_name,   // Make
            $row->model_name,  // Model
            $statusBadge,
            $action
        ];
    }

    $totalRecords = $this->db->table('tbl_vehicle_model')->countAllResults();

    return $this->response->setJSON([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $recordsFiltered,
        'data' => $data
    ]);
}


}
?>
