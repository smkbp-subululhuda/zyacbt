<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Peserta_reset extends Member_Controller {
	private $kode_menu = 'peserta-reset';
	private $kelompok = 'peserta';
	private $url = 'manager/peserta_reset';
	
    function __construct(){
		parent:: __construct();
		$this->load->model('cbt_user_grup_model');
		$this->load->model('cbt_user_model');
		$this->load->model('cbt_sessions_model');

		parent::cek_akses($this->kode_menu);
	}
	
    public function index(){
        $data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;

        $query_group = $this->cbt_user_grup_model->get_group();

        if($query_group->num_rows()>0){
        	$select = '';
        	$query_group = $query_group->result();
        	foreach ($query_group as $temp) {
        		$select = $select.'<option value="'.$temp->grup_id.'">'.$temp->grup_nama.'</option>';
        	}

        }else{
        	$select = '<option value="100000">KOSONG</option>';
        }
        $data['select_group'] = $select;
        
		$query_konfigurasi = $this->cbt_konfigurasi_model->get_by_kolom_limit('konfigurasi_kode', 'proteksi_multilogin', 1);
		$view = '/peserta_reset_view';
		if($query_konfigurasi->num_rows()>0){
			if($query_konfigurasi->row()->konfigurasi_isi=='tidak'){
				$view = '/peserta_reset_tidakaktif_view';
			}
		}
        
		$this->template->display_admin($this->kelompok.$view, 'Reset Login', $data);
    }
	
    function reset_daftar_peserta(){
    	$this->load->library('form_validation');
        
		$this->form_validation->set_rules('edit-user-id[]', 'Peserta','required|strip_tags');
		if($this->form_validation->run() == TRUE){
			$user_id = $this->input->post('edit-user-id', TRUE);
			foreach( $user_id as $kunci => $isi ) {
				if($isi=="on"){
					$data['user_login'] = '0';

					$this->cbt_user_model->update('user_id', $kunci, $data);
					// Menghapus cbt_session berdasarkan user_id
					$driver = $this->config->item('sess_driver');
					if(!empty($driver) AND $driver=='database'){
						// Menghapus isi cbt_session
						$query_user = $this->cbt_user_model->get_by_kolom_limit('user_id', $kunci, 1);
						if($query_user->num_rows()>0){
							$query_user = $query_user->row();
							$this->cbt_sessions_model->delete_by_username($query_user->user_name);
						}
					}
            	}
            }
            $status['status'] = 1;
            $status['pesan'] = 'Daftar Peserta berhasil direset';
		}else{
			$status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }
    
    function get_datatable(){
		// variable initialization
		$group = $this->input->get('group');

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
		$query = $this->cbt_user_model->get_datatable_resetlogin($start, $rows, 'user_firstname', $search, $group);
		$iFilteredTotal = $query->num_rows();
		
		$iTotal= $this->cbt_user_model->get_datatable_resetlogin_count('user_firstname', $search, $group)->row()->hasil;
	    
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
            
			$record[] = ++$i;
            $record[] = $temp->user_name;
            $record[] = stripslashes($temp->user_firstname);

            $query_group = $this->cbt_user_grup_model->get_by_kolom_limit('grup_id', $temp->user_grup_id, 1)->row();

            $record[] = $query_group->grup_nama;
			$record[] = $temp->user_detail;
			
            $record[] = '<input type="checkbox" name="edit-user-id['.$temp->user_id.']" >';

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