<?php
namespace App\Controllers;
use App\Models\SecurityGuardModel;
use CodeIgniter\API\ResponseTrait;

class Security extends BaseController
{
    use ResponseTrait;
    protected $Guard;

    public function __construct()
    {
        $this->Guard = new SecurityGuardModel();
    }

    // Admin panel main guards ka view
    public function index()
    {
        $data = [
            "page_title" => "Security Guards",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('security'), "title" => "Security Guards", "status" => "", "link" => false]
            ]
        ];
        return view('security/view', $data);
    }

    // Single guard ka record get karna (edit ke liye)
    public function get_record()
    {
        $id = $this->request->getVar('id');
        $id = id_de($id);
        $data = $this->Guard->where('id', $id)->first();
        if($data){
            return $this->response->setJSON(['status'=>true,'data'=>$data]);
        } else {
            return $this->response->setJSON(['status'=>false,'message'=>'Record not found']);
        }
    }

    // Guard add karna
    public function save()
    {
        $validate = $this->validate([
            'price' => 'required|decimal'
        ]);
        if(!$validate){
            return $this->response->setJSON(['status'=>false,'errors'=>$this->validation->getErrors()]);
        }

        $data = [
            'price' => $this->request->getPost('price'),
            'status' => $this->request->getPost('status') ?? 1
        ];

        try {
            $this->Guard->insert($data);
            return $this->response->setJSON(['status'=>true,'message'=>'Security guard added successfully']);
        } catch (\Exception $e){
            return $this->response->setJSON(['status'=>false,'message'=>'Error occurred']);
        }
    }

    // Guard update karna
    public function update()
    {
        $id = id_de($this->request->getPost('id'));
        $validate = $this->validate([
            'price' => 'required|decimal'
        ]);
        if(!$validate){
            return $this->response->setJSON(['status'=>false,'errors'=>$this->validation->getErrors()]);
        }

        $data = [
            'price' => $this->request->getPost('price'),
            'status' => $this->request->getPost('status') ?? 1
        ];

        $res = $this->Guard->update($id, $data);
        if($res){
            return $this->response->setJSON(['status'=>true,'message'=>'Security guard updated successfully']);
        } else {
            return $this->response->setJSON(['status'=>false,'message'=>'Error on update']);
        }
    }

    // Guard delete
    public function delete_record()
    {
        $id = $this->request->getVar('id');
        $id = id_de($id);

        $data = $this->Guard->where('id', $id)->first();
        if($data){
            $res = $this->Guard->delete($id);
            if($res){
                return $this->response->setJSON(['status'=>true,'message'=>'Security Guard successfully deleted']);
            } else {
                return $this->response->setJSON(['status'=>false,'message'=>'Error deleting record']);
            }
        } else {
            return $this->response->setJSON(['status'=>false,'message'=>'Record not found']);
        }
    }

    // DataTable ke liye sab guards fetch karna
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
            0 => 'created_at',
            1 => 'price',
            2 => 'status'
        ];

        $builder = $this->db->table('tbl_security_guards')->select('id, price, status, created_at');

        if(!empty($search)){
            $builder->groupStart()
                    ->like('price', $search)
                    ->orLike('status', $search)
                    ->groupEnd();
        }

        $recordsFiltered = $builder->countAllResults(false);

        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $builder->orderBy($orderColumn, $orderDir)
                ->limit($length, $start);

        $query = $builder->get();
        $data = [];

        foreach($query->getResult() as $row){
            $statusBadge = $row->status == 1 ? 
                "<span class='badge badge-glow bg-success'>Active</span>" : 
                "<span class='badge badge-glow bg-danger'>Inactive</span>";
            $id = id_en($row->id);
            $action = "<div class='btn-group'>
                          <button class='btn btn-outline-primary btn-sm dropdown-toggle' data-bs-toggle='dropdown'>Actions</button>
                          <div class='dropdown-menu'>
                              <a class='dropdown-item' href='javascript:void(0);' onclick='edit_data(\"$id\")'>
                                  <i data-feather='edit'></i> Edit
                              </a>
                              <a hidden class='dropdown-item' href='javascript:void(0);' onclick='delete_data(\"$id\")'>
                                  <i data-feather='trash'></i> Delete
                              </a>
                          </div>
                       </div>";
            $data[] = [
                date("d-m-Y", strtotime($row->created_at)),
                "Rs. ".$row->price,
                $statusBadge,
                $action
            ];
        }

        $totalRecords = $this->db->table('tbl_security_guards')->countAllResults();

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }
}
?>
