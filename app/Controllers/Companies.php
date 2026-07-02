<?php
namespace App\Controllers;

use App\Models\CompanyModel;
use CodeIgniter\API\ResponseTrait;

class Companies extends BaseController
{
    use ResponseTrait;

    protected $Company;

    public function __construct()
    {
        $this->Company = new CompanyModel();
    }

    // Company listing view
    public function index()
    {
        $data = [
            "page_title" => "Companies",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('company'), "title" => "Companies", "status" => "", "link" => false]
            ]
        ];
        return view('companies/view', $data);
    }

    // Single company record (Edit)
    public function get_record()
    {
        $id = id_de($this->request->getVar('id'));
        $data = $this->Company->where('id', $id)->first();

        if ($data) {
            return $this->response->setJSON([
                'status' => true,
                'data'   => $data
            ]);
        }

        return $this->response->setJSON([
            'status'  => false,
            'message' => 'Record not found'
        ]);
    }

    // Save company
    public function save()
    {
        $validate = $this->validate([
                'name' => 'required'
            ]);
            if(!$validate){
                return $this->response->setJSON(['status'=>false,'errors'=>$this->validation->getErrors()]);
            }
            $exists = $this->Company
                ->where('name', $this->request->getPost('name'))
                ->first();
            if ($exists) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'This Company already exists in system.'
                ]);
            }
        $data = [
            'name' => $this->request->getPost('name'),
            'status'       => $this->request->getPost('status') ?? 1
        ];

        try {
            $this->Company->insert($data);
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Company added successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Error occurred while saving'
            ]);
        }
    }

    // Update company
    public function update()
    {
        $id = id_de($this->request->getPost('id'));

        $validate = $this->validate([
            'name' => 'required'
        ]);

        if (!$validate) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validation->getErrors()
            ]);
        }
        $exists = $this->Company
                ->where('name', $this->request->getPost('name'))
                ->where('id !=', $id)
                ->first();
            if ($exists) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'This Company already exists in system.'
                ]);
            }
        $data = [
            'name' => $this->request->getPost('name'),
            'status'       => $this->request->getPost('status') ?? 1
        ];

        $res = $this->Company->update($id, $data);

        if ($res) {
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Company updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'status'  => false,
            'message' => 'Error updating record'
        ]);
    }

    // Delete company
    public function delete_record()
    {
        $id = id_de($this->request->getVar('id'));
        $data = $this->Company->find($id);

        if (!$data) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Record not found'
            ]);
        }
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_vehicle_model');
        $builder->where('make_id', $id);
        $count = $builder->countAllResults();

        if ($count > 0) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'You cannnot delete company. This company is linked to a model'
            ]);
        }
        if ($this->Company->delete($id)) {
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Company deleted successfully'
            ]);
        }

        return $this->response->setJSON([
            'status'  => false,
            'message' => 'Error deleting record'
        ]);
    }

    // DataTable data
    public function get()
    {
        $request = \Config\Services::request();

        $draw   = intval($request->getGet('draw') ?? 1);
        $start  = intval($request->getGet('start') ?? 0);
        $length = intval($request->getGet('length') ?? 10);
        $search = $request->getGet('search')['value'] ?? '';

        $orderColumnIndex = $request->getGet('order')[0]['column'] ?? 0;
        $orderDir         = $request->getGet('order')[0]['dir'] ?? 'asc';

        $columns = [
            0 => 'created_at',
            1 => 'name',
            2 => 'status'
        ];

        $builder = $this->db->table('tbl_vehicle_make')
            ->select('id, name, status, created_at');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('status', $search)
                ->groupEnd();
        }

        $recordsFiltered = $builder->countAllResults(false);

        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';

        $builder->orderBy($orderColumn, $orderDir)
            ->limit($length, $start);

        $query = $builder->get();

        $data = [];
        foreach ($query->getResult() as $row) {

            $statusBadge = $row->status == 1
                ? "<span class='badge badge-glow bg-success'>Active</span>"
                : "<span class='badge badge-glow bg-danger'>Inactive</span>";

            $id = id_en($row->id);

            $action = "
            <div class='btn-group'>
                <button class='btn btn-outline-primary btn-sm dropdown-toggle' data-bs-toggle='dropdown'>Actions</button>
                <div class='dropdown-menu'>
                    <a class='dropdown-item' href='javascript:void(0);' onclick='edit_data(\"$id\")'>
                        <i data-feather='edit'></i> Edit
                    </a>
                    <a class='dropdown-item' href='javascript:void(0);' onclick='delete_data(\"$id\")'>
                        <i data-feather='trash'></i> Delete
                    </a>
                </div>
            </div>";

            $data[] = [
                date('d-m-Y', strtotime($row->created_at)),
                esc($row->name),
                $statusBadge,
                $action
            ];
        }

        $totalRecords = $this->db->table('tbl_vehicle_make')->countAllResults();

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
        ]);
    }
}
