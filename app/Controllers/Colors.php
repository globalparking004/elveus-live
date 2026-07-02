<?php
namespace App\Controllers;

use App\Models\ColorModel;
use CodeIgniter\API\ResponseTrait;

class Colors extends BaseController
{
    use ResponseTrait;

    protected $Color;

    public function __construct()
    {
        $this->Color = new ColorModel();
    }

    // Company listing view
    public function index()
    {
        $data = [
            "page_title" => "Colors",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('vehicles'), "title" => "Colors", "status" => "", "link" => false]
            ]
        ];
        return view('colors/view', $data);
    }

    // Single company record (Edit)
    public function get_record()
    {
        $id = id_de($this->request->getVar('id'));
        $data = $this->Color->where('id', $id)->first();

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
                'color_name' => 'required'
            ]);
            if(!$validate){
                return $this->response->setJSON(['status'=>false,'errors'=>$this->validation->getErrors()]);
            }
            $exists = $this->Color
                ->where('color_name', $this->request->getPost('color_name'))
                ->first();
            if ($exists) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'This color already exists in system.'
                ]);
            }

        $data = [
            'color_name' => $this->request->getPost('color_name'),
            'status'       => $this->request->getPost('status')
        ];

        try {
            $this->Color->insert($data);
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Color added successfully'
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
            'color_name' => 'required'
        ]);

        if (!$validate) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validation->getErrors()
            ]);
        }
        $exists = $this->Color
                ->where('color_name', $this->request->getPost('color_name'))
                ->where('id !=', $id)
                ->first();
            if ($exists) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'This color already exists in system.'
                ]);
            }

        $data = [
            'color_name' => $this->request->getPost('color_name'),
            'status'       => $this->request->getPost('status')
        ];

        $res = $this->Color->update($id, $data);

        if ($res) {
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Color updated successfully'
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
        $data = $this->Color->find($id);

        if (!$data) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Record not found'
            ]);
        }
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_vehicle_price');
        $builder->where('color_id', $id);
        $count = $builder->countAllResults();

        if ($count > 0) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'You cannnot delete color. This color is linked to a car'
            ]);
        }
        if ($this->Color->delete($id)) {
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Color deleted successfully'
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
            1 => 'color_name',
            2 => 'status'
        ];

        $builder = $this->db->table('tbl_vehicle_color')
            ->select('id, color_name, status, created_at');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('color_name', $search)
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
                esc($row->color_name),
                $statusBadge,
                $action
            ];
        }

        $totalRecords = $this->db->table('tbl_vehicle_color')->countAllResults();

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
        ]);
    }
}
