<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ZYA CBT
* Achmad Lutfi
* achmdlutfi@gmail.com
* achmadlutfi.wordpress.com
*/
class Usermenu extends Member_Controller {
    function __construct(){
		parent:: __construct();
	}
    
    public function index($page=null, $id=null){
        $data['kode_menu']='user_menu';
        $this->load->model('usermenu_model');
        if($page=="add"){
            parent::cek_akses_crud($data['kode_menu'], 0);
            $this->load->helper('form');
            
            $query = $this->usermenu_model->get_menu_by_tipe(0);
            $parent = '';
            if($query->num_rows()>0){
                $parent = $parent.'<select name="parent" id="parent" class="form-control input-sm">';
                $menu = $query->result();
                foreach($menu as $temp){
                    $parent = $parent.'
                        <option value="'.$temp->kode_menu.'">'.$temp->nama_menu.'</option>
                    ';
                }
                $parent = $parent.'</select>';
            }else{
                $parent = '<input type="text" class="form-control input-sm" id="parent" name="parent" readonly>';
            }
            $data['parent'] = $parent;
            $this->template->display_admin('pengaturan/usermenu/add_view', 'Tambah Menu User', $data);
        }else if($page=="edit"){
            parent::cek_akses_crud($data['kode_menu'], 1);
            $this->load->helper('form');
            if(empty($id)){
                redirect('manager/usermenu');
            }else{
                $query = $this->usermenu_model->get_menu_by_id($id);
                if($query->num_rows()>0){
                    $temp = $query->row();
                    $data['id']=$temp->id;
                    if($temp->tipe==0){
                        $data['tipe']='Parent';
                    }else{
                        $data['tipe']='Child';
                    }
                    $data['parent']=$temp->parent;
                    $data['kode']=$temp->kode_menu;
                    $data['nama_menu']=$temp->nama_menu;
                    $data['url']=$temp->url;
                    $data['icon']=$temp->icon;
                    $data['urutan']=$temp->urutan;
                }else{
                    redirect('manager/usermenu');
                }
            }
            
            $this->template->display_admin('pengaturan/usermenu/edit_view', 'Edit Menu User', $data);
        }else{
            parent::cek_akses($data['kode_menu']);
            $this->template->display_admin('pengaturan/usermenu/list_view', 'Daftar Menu User', $data);
        }
    }
    
    function edit(){
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('id', 'ID Menu','required|strip_tags');
        $this->form_validation->set_rules('kode', 'Kode Menu','required|strip_tags');
        $this->form_validation->set_rules('nama', 'Nama Menu','required|strip_tags');
        $this->form_validation->set_rules('url', 'URL Menu','required|strip_tags');
        $this->form_validation->set_rules('icon', 'Icon','required|strip_tags');
        $this->form_validation->set_rules('urutan', 'Urutan','required|numeric|strip_tags');
        
        if($this->form_validation->run() == TRUE){
            $this->load->model('usermenu_model');
            $aksi = $this->input->post('aksi', TRUE);
            if($aksi==0){//hapus
                $id = $this->input->post('id', TRUE);
                $this->usermenu_model->delete($id);
                $status['status'] = 1;
                $status['pesan'] = 'Menu berhasil dihapus';
            }else if($aksi==1){//simpan
                $id = $this->input->post('id', TRUE);
                $data['nama_menu'] = $this->input->post('nama', TRUE);
                $data['url'] = $this->input->post('url', TRUE);
                $data['icon'] = $this->input->post('icon', TRUE);
                $data['urutan'] = $this->input->post('urutan', TRUE);
                    
                $this->usermenu_model->update($data, $id);
                $status['status'] = 1;
                $status['pesan'] = 'Menu berhasil di Simpan';
            }
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }
    
    function add(){
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('tipe', 'tipe','required|strip_tags');
        $this->form_validation->set_rules('kode', 'Kode Menu','required|strip_tags');
        $this->form_validation->set_rules('nama', 'Nama Menu','required|strip_tags');
        $this->form_validation->set_rules('url', 'URL Menu','required|strip_tags');
        $this->form_validation->set_rules('icon', 'Icon','required|strip_tags');
        $this->form_validation->set_rules('urutan', 'Urutan','required|numeric|strip_tags');
        
        if($this->form_validation->run() == TRUE){
            $this->load->model('usermenu_model');
            $data['kode_menu']=$this->input->post('kode', TRUE);
            if($this->usermenu_model->cek_kode_menu($data['kode_menu'])->row()->hasil>0){
                $status['status'] = 0;
                $status['pesan'] = 'Kode Menu sudah dipakai';
            }else{
                $data['tipe']=$this->input->post('tipe', TRUE);
                $data['parent']=$this->input->post('parent', TRUE);
                $data['nama_menu']=$this->input->post('nama', TRUE);
                $data['url']=$this->input->post('url', TRUE);
                $data['icon']=$this->input->post('icon', TRUE);
                $data['urutan']=$this->input->post('urutan', TRUE);
                
                if($data['tipe']==0){
                    $data['parent'] = '';
                    $this->usermenu_model->save($data);
                    $status['status'] = 1;
                    $status['pesan'] = 'Menu berhasil disimpan';
                }else{
                    if(empty($data['parent'])){
                        $status['status'] = 0;
                        $status['pesan'] = 'Parent Harus diisi';
                    }else{
                        $this->usermenu_model->save($data);
                        $status['status'] = 1;
                        $status['pesan'] = 'Menu berhasil disimpan';
                    }
                }
            }
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }
    
    function get_all_menu(){
        $is_edit = $this->access->cek_akses_crud('user_menu', 1);
        $this->load->model('usermenu_model');
        
		// variable initialization
		$search = "";
		$start = 0;
		$rows = 10;

		// get search value (if any)
		if (isset($_GET['sSearch']) && $_GET['sSearch'] != "" ) {
			$search = $_GET['sSearch'];
		}

		// limit
		$start = $this->get_start();
		$rows = $this->get_rows();

		// run query to get user listing
		$query = $this->usermenu_model->get_all_menu($start, $rows, $search);
		$iFilteredTotal = $query->num_rows();
		
		$iTotal=$this->usermenu_model->get_all_menu_count($search)->row()->hasil;
	    
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
	        "iTotalRecords" => $iTotal,
	        "iTotalDisplayRecords" => $iTotal,
	        "aaData" => array()
	    );

	    // get result after running query and put it in array
		$i=$start;
		$menu = $query->result();
	    foreach ($menu as $temp) {			
			$record = array();
            
			$record[] = ++$i;
            if($temp->tipe==0){
                $record[] = 'Parent';
            }else{
                $record[] = 'Child';
            }
            $record[] = $temp->parent;
			$record[] = $temp->kode_menu;
            $record[] = $temp->nama_menu;
            $record[] = $temp->url;
            $record[] = $temp->icon;
            if($is_edit){
                $record[] = '<a href="'.site_url('manager/usermenu/index/edit/'.$temp->id).'" class="btn btn-default btn-xs">Edit</a>';
            }else{
                $record[] = '';
            }

			$output['aaData'][] = $record;
		}
		// format it to JSON, this output will be displayed in datatable
		echo json_encode($output);
	}
	
	
	/**
	* funsi tambahan 
	* 
	* 
*/
	
	function get_start() {
		$start = 0;
		if (isset($_GET['iDisplayStart'])) {
			$start = intval($_GET['iDisplayStart']);

			if ($start < 0)
				$start = 0;
		}

		return $start;
	}

	function get_rows() {
		$rows = 10;
		if (isset($_GET['iDisplayLength'])) {
			$rows = intval($_GET['iDisplayLength']);
			if ($rows < 5 || $rows > 500) {
				$rows = 10;
			}
		}

		return $rows;
	}

	function get_sort_dir() {
		$sort_dir = "ASC";
		$sdir = strip_tags($_GET['sSortDir_0']);
		if (isset($sdir)) {
			if ($sdir != "asc" ) {
				$sort_dir = "DESC";
			}
		}

		return $sort_dir;
	}
}