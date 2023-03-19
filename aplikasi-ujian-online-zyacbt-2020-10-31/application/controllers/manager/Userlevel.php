<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ZYA CBT
* Achmad Lutfi
* achmdlutfi@gmail.com
* achmadlutfi.wordpress.com
*/
class Userlevel extends Member_Controller {
    function __construct(){
		parent:: __construct();
	}
    
    public function index($page=null, $id=null){
        $data['kode_menu'] = 'user_level';
        
        $this->load->model('usermenu_model');
        if($page=="add"){
            parent::cek_akses_crud($data['kode_menu'], 0);
            $this->load->helper('form');
            
            $menu = '';
            $parent = $this->usermenu_model->get_menu_by_tipe(0);
            if($parent->num_rows()>0){
                $parent = $parent->result();
                foreach($parent as $temp){
                    if($temp->url=="#"){
                        $status='disabled';
                    }else{
                        $status='';
                    }
                    
                    $menu = $menu.'<div class="row">
                                        <div class="col-md-12">
                                            <div class="col-sm-6">
                                                <div class="col-xs-6">
                                                    <div class="checkbox">
                                                        <label>
                                                          <input type="checkbox" name="kodemenu['.$temp->kode_menu.']" '.$status.'/>
                                                          '.$temp->nama_menu.'
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-xs-3">
                                                    <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="add['.$temp->kode_menu.']" '.$status.'/>
                                                        Add
                                                    </label>
                                                    </div>
                                                </div>
                                                <div class="col-xs-3">
                                                    <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="edit['.$temp->kode_menu.']" '.$status.'/>
                                                        Edit
                                                    </label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            ';
                                        
                    $child = $this->usermenu_model->get_menu_by_parent($temp->kode_menu);
                    if($child->num_rows()>0){
                        $menu = $menu.'<div class="form-group col-sm-6">';
                        $child = $child->result();
                        foreach($child as $temp2){
                            $menu = $menu.'
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="kodemenu['.$temp2->kode_menu.']" />
                                          '.$temp2->nama_menu.'
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="add['.$temp2->kode_menu.']" checked />
                                        Add
                                    </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="edit['.$temp2->kode_menu.']" checked />
                                        Edit
                                    </label>
                                    </div>
                                </div>
                            </div>
                            ';
                        }
                        $menu = $menu.'</div>';
                    }
                    
                    
                    $menu = $menu.'     </div>
                                   </div>';
                }
            }else{
                $menu = $menu='<div class="row">
                                        <div class="col-md-12">
                                            <div col-sm-12">
                                                Tidak Ada Menu
                                            </div>
                                        </div>
                                </div>';
            }
            
            $data['data_menu'] = $menu;
            
            $this->template->display_admin('pengaturan/userlevel/add_view', 'Tambah Level', $data);
            
            
            
        }else if($page=="edit"){
            parent::cek_akses_crud($data['kode_menu'], 1);
            $this->load->helper('form');
            if(empty($id)){
                redirect('manager/userlevel');
            }else{
                $this->load->model('userlevel_model');
                $this->load->model('userakses_model');
                
                $query = $this->userlevel_model->get_level_by_id($id);
                if($query->num_rows()>0){
                    $query = $query->row();
                    $data['level'] = $query->level;
                    $data['keterangan'] = $query->keterangan;
                    
                    $menu = '';
                    $parent = $this->usermenu_model->get_menu_by_tipe(0);
                    if($parent->num_rows()>0){
                        $parent = $parent->result();
                        foreach($parent as $temp){
                            if($temp->url=="#"){
                                $status='disabled';
                            }else{
                                $status='';
                            }
                            
                            $parent_checked = '';
                            $parent_edit_checked = '';
                            $parent_add_checked = '';
                            
                            $user_akses = $this->userakses_model->get_akses_by_level_kodemenu($data['level'], $temp->kode_menu);
                            if($user_akses->num_rows()>0){
                                $parent_checked = 'checked';
                                $user_akses = $user_akses->row();
                                if($user_akses->add==1){
                                    $parent_add_checked = 'checked';
                                }
                                
                                if($user_akses->edit==1){
                                    $parent_edit_checked = 'checked';
                                }
                            }
                            
                            $menu = $menu.'<div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-sm-6">
                                                        <div class="col-xs-6">
                                                            <div class="checkbox">
                                                                <label>
                                                                  <input type="checkbox" name="kodemenu['.$temp->kode_menu.']" '.$status.' '.$parent_checked.' />
                                                                  '.$temp->nama_menu.'
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3">
                                                            <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" name="add['.$temp->kode_menu.']" '.$status.' '.$parent_add_checked.' />
                                                                Add
                                                            </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3">
                                                            <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" name="edit['.$temp->kode_menu.']" '.$status.' '.$parent_edit_checked.' />
                                                                Edit
                                                            </label>
                                                            </div>
                                                        </div>
                                                    </div>';
                                                
                            $child = $this->usermenu_model->get_menu_by_parent($temp->kode_menu);
                            if($child->num_rows()>0){
                                $menu = $menu.'<div class="form-group col-sm-6">';
                                $child = $child->result();
                                foreach($child as $temp2){
                                    $child_checked = '';
                                    $child_edit_checked = '';
                                    $child_add_checked = '';
                                    
                                    $user_akses = $this->userakses_model->get_akses_by_level_kodemenu($data['level'], $temp2->kode_menu);
                                    if($user_akses->num_rows()>0){
                                        $child_checked = 'checked';
                                        $user_akses = $user_akses->row();
                                        if($user_akses->add==1){
                                            $child_add_checked = 'checked';
                                        }
                                        
                                        if($user_akses->edit==1){
                                            $child_edit_checked = 'checked';
                                        }
                                    }
                                    
                                    
                                    $menu = $menu.'
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="checkbox">
                                                <label>
                                                  <input type="checkbox" name="kodemenu['.$temp2->kode_menu.']" '.$child_checked.' />
                                                  '.$temp2->nama_menu.'
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="add['.$temp2->kode_menu.']" '.$child_add_checked.'  />
                                                Add
                                            </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="edit['.$temp2->kode_menu.']" '.$child_edit_checked.'  />
                                                Edit
                                            </label>
                                            </div>
                                        </div>
                                    </div>
                                    ';
                                }
                                $menu = $menu.'</div>';
                            }
                            
                            
                            $menu = $menu.'     </div>
                                           </div>';
                        }
                    }else{
                        $menu = $menu='<div class="row">
                                                <div class="col-md-12">
                                                    <div col-sm-12">
                                                        Tidak Ada Menu
                                                    </div>
                                                </div>
                                        </div>';
                    }
                    
                    $data['data_menu'] = $menu;
                }else{
                    redirect('manager/userlevel');
                }
            }
            
            $this->template->display_admin('pengaturan/userlevel/edit_view', 'Edit Level User', $data);
        }else{
            parent::cek_akses($data['kode_menu']);
            $this->template->display_admin('pengaturan/userlevel/list_view', 'Daftar Level User', $data);
        }
    }
    
    function edit(){
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('level', 'Level','required|strip_tags');
        $this->form_validation->set_rules('keterangan', 'Keterangan','required|strip_tags');
        $this->form_validation->set_rules('kodemenu[]', 'Kode Menu','required');
        
        if($this->form_validation->run() == TRUE){
            $this->load->model('usermenu_model');
            $this->load->model('userakses_model');
            $this->load->model('userlevel_model');
            $this->load->model('users_model');
            
            $aksi = $this->input->post('aksi', TRUE);
            
            if($aksi==0){//hapus
                $level = $this->input->post('level', TRUE);
                
                if($level=='admin'){
                    $status['status'] = 0;
                    $status['pesan'] = 'Level ADMIN tidak bisa dihapus';
                }else{
                    if($this->users_model->get_user_count_by_level($level)->row()->hasil>0){
                        $status['status'] = 0;
                        $status['pesan'] = 'Level Masih Digunakan oleh user';
                    }else{
                        $this->userlevel_model->delete($level);
                        $status['status'] = 1;
                        $status['pesan'] = 'Level berhasil dihapus';
                    }
                }
            }else if($aksi==1){//simpan
                $level = $this->input->post('level', TRUE);
                $kodemenu = $this->input->post('kodemenu', TRUE);
                $create = $this->input->post('add', TRUE);
                $edit = $this->input->post('edit', TRUE);
                
                if(empty($edit)){
                    $edit = array('kosongggg');
                }
                if(empty($create)){
                    $create = array('kosongggg');
                }
                
                $query = $this->usermenu_model->get_menu();
                if($query->num_rows()>0){
                    $query = $query->result();
                    
                    // hapus user_akses berdasarkan level
                    $this->userakses_model->delete_by_level($level);
                    
                    foreach($query as $temp){
                        if(array_key_exists($temp->kode_menu, $kodemenu)){
                            if($kodemenu[$temp->kode_menu]=="on"){
                                $user_akses['level'] = $level;
                                $user_akses['kode_menu'] = $temp->kode_menu;
                                if(array_key_exists($temp->kode_menu, $create)){
                                    $user_akses['add'] = 1;
                                }else{
                                    $user_akses['add'] = 0;
                                }
                                if(array_key_exists($temp->kode_menu, $edit)){
                                    $user_akses['edit'] = 1;
                                }else{
                                    $user_akses['edit'] = 0;
                                }
                                
                                $this->userakses_model->save($user_akses);
                            }
                        }
                    }
                    
                    $status['status'] = 1;
                    $status['pesan'] = 'Level berhasil disimpan';
                }
                
                $status['status'] = 1;
                $status['pesan'] = 'Level berhasil di Simpan';
            }
            
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }
    
    function add(){
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('level', 'Level','required|strip_tags');
        $this->form_validation->set_rules('keterangan', 'Keterangan','required|strip_tags');
        $this->form_validation->set_rules('kodemenu[]', 'Kode Menu','required');
        if($this->form_validation->run() == TRUE){
            $this->load->model('usermenu_model');
            $this->load->model('userakses_model');
            $this->load->model('userlevel_model');
            
            $kodemenu = $this->input->post('kodemenu', TRUE);
            $create = $this->input->post('add', TRUE);
            $edit = $this->input->post('edit', TRUE);
            $level = $this->input->post('level', TRUE);
            $keterangan = $this->input->post('keterangan', TRUE);
            
            if(empty($edit)){
                $edit = array('kosongggg');
            }
            if(empty($create)){
                $create = array('kosongggg');
            }
            
            $query_level = $this->userlevel_model->cek_level($level)->row()->hasil;
            if($query_level==0){
                $user_level['level']=$level;
                $user_level['keterangan']=$keterangan;
                $this->userlevel_model->save($user_level);
                
                
                $query = $this->usermenu_model->get_menu();
                if($query->num_rows()>0){
                    $query = $query->result();
                    
                    foreach($query as $temp){
                        if(array_key_exists($temp->kode_menu, $kodemenu)){
                            if($kodemenu[$temp->kode_menu]=="on"){
                                $user_akses['level'] = $level;
                                $user_akses['kode_menu'] = $temp->kode_menu;
                                if(array_key_exists($temp->kode_menu, $create)){
                                    $user_akses['add'] = 1;
                                }else{
                                    $user_akses['add'] = 0;
                                }
                                if(array_key_exists($temp->kode_menu, $edit)){
                                    $user_akses['edit'] = 1;
                                }else{
                                    $user_akses['edit'] = 0;
                                }
                                
                                $this->userakses_model->save($user_akses);
                            }
                        }
                    }
                    
                    $status['status'] = 1;
                    $status['pesan'] = 'Level berhasil disimpan';
                }
            }else{
                $status['status'] = 0;
                $status['pesan'] = 'Level sudah digunakan';
            }
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }
    
    function get_all_level(){
        $is_edit = $this->access->cek_akses_crud('user_level', 1);
        $this->load->model('userlevel_model');
        
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
		$query = $this->userlevel_model->get_all_level($start, $rows, $search);
		$iFilteredTotal = $query->num_rows();
		
		$iTotal=$this->userlevel_model->get_all_level_count($search)->row()->hasil;
	    
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
	        "iTotalRecords" => $iTotal,
	        "iTotalDisplayRecords" => $iTotal,
	        "aaData" => array()
	    );

	    // get result after running query and put it in array
		$i=$start;
		$level = $query->result();
	    foreach ($level as $temp) {			
			$record = array();
            
			$record[] = ++$i;
            $record[] = $temp->level;
			$record[] = $temp->keterangan;
            
            $hak_akses = '';
            $akses = $this->userlevel_model->get_menu_by_level($temp->level);
            if($akses->num_rows()>0){
                $akses = $akses->result();
                $hak_akses = $hak_akses.'<ol>';
                foreach($akses as $temp2){
                    $hak_akses = $hak_akses.'<li> Nama Menu : '.$temp2->nama_menu.'. Add : '.$temp2->add.'. Edit : '.$temp2->edit.'.</li>';
                }
                $hak_akses = $hak_akses.'</ol>';
            }else{
                $hak_akses = 'Tidak Memiliki Hak Akses';
            }
            $record[] = $hak_akses;
            
            if($is_edit){
                $record[] = '<a href="'.site_url('manager/userlevel/index/edit/'.$temp->id).'" class="btn btn-default btn-xs">Edit</a>';
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