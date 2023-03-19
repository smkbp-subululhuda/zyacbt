<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ZYA CBT
* Achmad Lutfi
* achmdlutfi@gmail.com
* achmadlutfi.wordpress.com
*/
class Useratur extends Member_Controller {
    private $url = 'manager/useratur';

    function __construct(){
		parent:: __construct();

        $this->load->model('cbt_modul_model');
        $this->load->model('cbt_topik_model');
		$this->load->model('cbt_soal_model');
	}
    
    public function index($page=null, $id=null){
        $data['kode_menu'] = 'user_atur';
        $data['url'] = $this->url;

        $data['select_modul'] = $this->get_select_modul();
        
        if($page=="add"){
            parent::cek_akses_crud($data['kode_menu'], 0);
            $this->load->helper('form');
            
            $this->load->model('userlevel_model');
            
            $query = $this->userlevel_model->get_level();
            $level_opsi = '';
            if($query->num_rows()>0){
                $level_opsi = $level_opsi.'<select name="level" id="level" class="form-control input-sm">';
                $level = $query->result();
                foreach($level as $temp){
                    $level_opsi = $level_opsi.'
                        <option value="'.$temp->level.'">'.$temp->level.'</option>
                    ';
                }
                $level_opsi = $level_opsi.'</select>';
            }else{
                $level_opsi = '<input type="text" class="form-control input-sm" id="parent" name="parent" value="" readonly>';
            }
            
            $data['level_opsi'] = $level_opsi; 
            
            $this->template->display_admin('pengaturan/user/add_view', 'Tambah User', $data);
        }else if($page=="edit"){
            parent::cek_akses_crud($data['kode_menu'], 1);
            $this->load->helper('form');
            if(empty($id)){
                redirect('manager/usermenu');
            }else{
                $query_user = $this->users_model->get_user_by_id($id);
                if($query_user->num_rows()>0){
                    $this->load->model('userlevel_model');
                    
                    $temp = $query_user->row();
                    $data['username'] = $temp->username;
                    $data['nama_lengkap'] = $temp->nama;
                    $data['level_user'] = $temp->level;
                    $data['opsi1'] = $temp->opsi1;
                    $data['opsi2'] = $temp->opsi2;
                    $data['keterangan'] = $temp->keterangan;
                    
                    $query = $this->userlevel_model->get_level();
                    $level_opsi = '';
                    if($query->num_rows()>0){
                        $level_opsi = $level_opsi.'<select name="level" id="level" class="form-control input-sm">';
                        $level = $query->result();
                        foreach($level as $temp){
                            $selected = '';
                            if($temp->level==$data['level_user']){
                                $selected = 'selected';
                            }
                            $level_opsi = $level_opsi.'
                                <option value="'.$temp->level.'" '.$selected.'>'.$temp->level.'</option>
                            ';
                        }
                        $level_opsi = $level_opsi.'</select>';
                    }else{
                        $level_opsi = '<input type="text" class="form-control input-sm" id="parent" name="parent" value="" readonly>';
                    }
                    
                    $data['level_opsi'] = $level_opsi; 
                }else{
                    redirect('manager/useratur');
                }
            }
            
            $this->template->display_admin('pengaturan/user/edit_view', 'Edit User', $data);
        }else{
            parent::cek_akses($data['kode_menu']);
            $this->template->display_admin('pengaturan/user/list_view', 'Daftar User', $data);
        }
    }
    
    function edit(){
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('username', 'Username','required|strip_tags');
        $this->form_validation->set_rules('password', 'Password','required|strip_tags');
        $this->form_validation->set_rules('nama', 'nama','required|strip_tags');
        $this->form_validation->set_rules('level', 'Level','required|strip_tags');
        $this->form_validation->set_rules('keterangan', 'Keterangan','required|strip_tags');
        
        if($this->form_validation->run() == TRUE){
            $username = $this->input->post('username', true);
            $aksi = $this->input->post('aksi', TRUE);
            
            if($aksi==0){//hapus
                $this->users_model->delete($username);
                $status['status'] = 1;
                $status['pesan'] = 'User berhasil dihapus';
            }else if($aksi==1){//simpan
                $data_user['nama'] = $this->input->post('nama', true);
                $data_user['level'] = $this->input->post('level', true);
                $data_user['opsi1'] = strtoupper($this->input->post('opsi1', true));
                $data_user['opsi2'] = strtoupper($this->input->post('opsi2', true));
                $data_user['keterangan'] = $this->input->post('keterangan', true);
                
                $password = $this->input->post('password', true);
                if($password!='kosongkosong'){
                    $data_user['password'] = sha1($password);
                }
                
                $this->users_model->update($data_user, $username);
                
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
        
        $this->form_validation->set_rules('username', 'Username','required|strip_tags');
        $this->form_validation->set_rules('password', 'Password','required|strip_tags');
        $this->form_validation->set_rules('nama', 'nama','required|strip_tags');
        $this->form_validation->set_rules('level', 'Level','required|strip_tags');
        $this->form_validation->set_rules('keterangan', 'Keterangan','required|strip_tags');
        
        if($this->form_validation->run() == TRUE){
            $username = $this->input->post('username', true);
            if($this->users_model->get_login_info($username)){
                $status['status'] = 0;
                $status['pesan'] = 'Username sudah terdaftar';
            }else{
                $data_user['username'] = $username;
                $data_user['password'] = sha1($this->input->post('password', true));
                $data_user['nama'] = $this->input->post('nama', true);
                $data_user['level'] = $this->input->post('level', true);
                $data_user['opsi1'] = $this->input->post('opsi1', true);
                $data_user['opsi2'] = $this->input->post('opsi2', true);
                $data_user['keterangan'] = $this->input->post('keterangan', true);
                
                $this->users_model->save($data_user);
                
                $status['status'] = 1;
                $status['pesan'] = 'Username berhasil disimpan';
            }
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }
    
    
    function get_all_user(){
        $is_edit = $this->access->cek_akses_crud('user_atur', 1);
        $this->load->model('users_model');
        
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
		$query = $this->users_model->get_all_user($start, $rows, $search);
		$iFilteredTotal = $query->num_rows();
		
		$iTotal=$this->users_model->get_all_user_count($search)->row()->hasil;
	    
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
	        "iTotalRecords" => $iTotal,
	        "iTotalDisplayRecords" => $iTotal,
	        "aaData" => array()
	    );

	    // get result after running query and put it in array
		$i=$start;
		$user = $query->result();
	    foreach ($user as $temp) {			
			$record = array();
            
			$record[] = ++$i;
            $record[] = $temp->username;
			$record[] = $temp->nama;
            $record[] = $temp->level;
            $record[] = $temp->opsi1;
            $record[] = $temp->opsi2;
            $record[] = $temp->keterangan;
            if($is_edit){
                $record[] = '<a href="'.site_url('manager/useratur/index/edit/'.$temp->id).'" class="btn btn-default btn-xs">Edit</a>';
            }else{
                $record[] = '';
            }

			$output['aaData'][] = $record;
		}
		// format it to JSON, this output will be displayed in datatable
        
		echo json_encode($output);
	}

    function get_select_modul(){
        $query_modul = $this->cbt_modul_model->get_modul();
        $select = '';
        if($query_modul->num_rows()>0){
            $query_modul = $query_modul->result();
            foreach ($query_modul as $temp) {
                $select = $select.'<option value="'.$temp->modul_id.'">'.$temp->modul_nama.'</option>';
            }

        }else{
            $select = '<option value="10000000">KOSONG</option>';
        }

        return $select;
    }

    function get_datatable_topik(){
        // variable initialization
        $modul = $this->input->get('modul');

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
        $query = $this->cbt_topik_model->get_datatable($start, $rows, 'topik_nama', $search, $modul);
        $iFilteredTotal = $query->num_rows();
        
        $iTotal= $this->cbt_topik_model->get_datatable_count('topik_nama', $search, $modul)->row()->hasil;
        
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iTotal,
            "aaData" => array()
        );

        // get result after running query and put it in array
        $i=$start;
        $query = $query->result();
        foreach ($query as $temp) {         
            $record = array();
            
			$jml_soal = $this->cbt_soal_model->count_by_kolom('soal_topik_id', $temp->topik_id)->row()->hasil;
			
            $record[] = ++$i;
            $record[] = $temp->topik_nama.' ['.$jml_soal.']';
            $record[] = $temp->topik_detail;
            $record[] = '<a onclick="tambah_topik(\''.$temp->topik_id.'\', \''.addslashes($temp->topik_nama).'\')" style="cursor: pointer;" class="btn btn-default btn-xs">Tambah</a>';

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