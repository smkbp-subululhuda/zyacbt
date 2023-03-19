<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ZYA CBT
* Achmad Lutfi
* achmdlutfi@gmail.com
* achmadlutfi.wordpress.com
*/
class Tes_token extends Member_Controller {
	private $kode_menu = 'tes-token';
	private $kelompok = 'tes';
	private $url = 'manager/tes_token';
	
    function __construct(){
		parent:: __construct();
		$this->load->model('cbt_modul_model');
		$this->load->model('cbt_tes_model');
		$this->load->model('cbt_tes_token_model');

		parent::cek_akses($this->kode_menu);
	}
	
    public function index($page=null, $id=null){
        $data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;

        $username = $this->access->get_username();
		$user_id = $this->users_model->get_login_info($username)->id;

        $this->cbt_tes_token_model->delete_by_user_date($user_id);
		
		$query_tes = $this->cbt_tes_model->get_by_now();
		$select = '<option value="0">Semua Tes</option>';
		if($query_tes->num_rows()>0){
			$query_tes = $query_tes->result();
			foreach ($query_tes as $temp) {
        		$select = $select.'<option value="'.$temp->tes_id.'">'.$temp->tes_nama.'</option>';
        	}
		}
		$data['select_tes'] = $select;
        
        $this->template->display_admin($this->kelompok.'/tes_token_view', 'Token', $data);
    }

    function token(){
        $token = substr(uniqid(), 7);

        $username = $this->access->get_username();
        $user_id = $this->users_model->get_login_info($username)->id;
        $aktif = $this->input->post('token-aktif', TRUE);
		$tes_id = $this->input->post('token-tes', TRUE);

        $i=1;
        while($i==1){
        	if($this->cbt_tes_token_model->count_by_kolom('token_isi', $token)->row()->hasil==0){
        		$data['token_isi'] = strtoupper($token);
                $data['token_user_id'] = $user_id;
                $data['token_aktif'] = $aktif;
				$data['token_tes_id'] = $tes_id;

        		$this->cbt_tes_token_model->save($data);
        		$i=0;
        	}

        	$token = substr(uniqid(), 7);
        }

        $status['status'] = 1;
        $status['token'] = $data['token_isi'];
		$status['pesan'] = 'Token berhasil di generate';

        echo json_encode($status);
    }
	
	function token_manual(){
		$this->load->library('form_validation');
        
        $this->form_validation->set_rules('manual-token', 'Token Tes','required|strip_tags');
        
        if($this->form_validation->run() == TRUE){
            $username = $this->access->get_username();
			$user_id = $this->users_model->get_login_info($username)->id;
			$aktif = $this->input->post('manual-aktif', TRUE);
			$token = $this->input->post('manual-token', TRUE);
			$tes_id = $this->input->post('manual-tes', TRUE);
			
			if($this->cbt_tes_token_model->count_by_kolom('token_isi', $token)->row()->hasil==0){
        		$data['token_isi'] = strtoupper($token);
                $data['token_user_id'] = $user_id;
                $data['token_aktif'] = $aktif;
				$data['token_tes_id'] = $tes_id;

        		$this->cbt_tes_token_model->save($data);
				
				$status['status'] = 1;
				$status['pesan'] = 'Token berhasil disimpan';
        	}else{
				$status['status'] = 0;
				$status['pesan'] = 'Token sudah digunakan pada hari ini';
			}
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }

    function get_datatable(){
		// variable initialization
		$search = "";
		$start = 0;
		$rows = 10;
		
		$username = $this->access->get_username();

		// get search value (if any)
		if (isset($_GET['sSearch']) && $_GET['sSearch'] != "" ) {
			$search = $_GET['sSearch'];
		}

		// limit
		$start = $this->get_start();
		$rows = $this->get_rows();

		// run query to get user listing
		$query = $this->cbt_tes_token_model->get_datatable($start, $rows, 'token_isi', $search, $username);
		$iFilteredTotal = $query->num_rows();
		
		$iTotal= $this->cbt_tes_token_model->get_datatable_count('token_isi', $search, $username)->row()->hasil;
	    
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
            $record[] = $temp->token_isi;
            $record[] = $temp->token_ts;
            if($temp->token_aktif==1){
                $record[] = '1 Hari';
            }else{
                $record[] = $temp->token_aktif.' Menit';
            }
			
			if($temp->token_tes_id==0){
                $record[] = 'Semua Tes';
            }else{
				$query_tes = $this->cbt_tes_model->get_by_kolom_limit('tes_id', $temp->token_tes_id, 1);
				if($query_tes->num_rows()>0){
					$query_tes = $query_tes->row();
					$record[] = $query_tes->tes_nama;
				}else{
					$record[] = 'Spesifik Tes';
				}
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