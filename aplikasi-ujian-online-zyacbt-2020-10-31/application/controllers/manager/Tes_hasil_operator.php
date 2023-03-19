<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ZYA CBT
* Achmad Lutfi
* achmdlutfi@gmail.com
* achmadlutfi.wordpress.com
*/
class Tes_hasil_operator extends Member_Controller {
	private $kode_menu = 'tes-hasil-operator';
	private $kelompok = 'tes';
	private $url = 'manager/tes_hasil_operator';
	
    function __construct(){
		parent:: __construct();
		$this->load->model('users_model');
		$this->load->model('cbt_user_model');
		$this->load->model('cbt_user_grup_model');
		$this->load->model('cbt_tes_model');
		$this->load->model('cbt_tes_token_model');
		$this->load->model('cbt_tes_topik_set_model');
		$this->load->model('cbt_tes_user_model');
		$this->load->model('cbt_tesgrup_model');
		$this->load->model('cbt_soal_model');
		$this->load->model('cbt_jawaban_model');
		$this->load->model('cbt_tes_soal_model');
		$this->load->model('cbt_tes_soal_jawaban_model');

		parent::cek_akses($this->kode_menu);
	}
	
    public function index($page=null, $id=null){
        $data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;
		
        $username = $this->access->get_username();
        $user_id = $this->users_model->get_login_info($username)->id;

		// mendapatkan token hari ini yang di generate user
		$query_token = $this->cbt_tes_token_model->get_by_user_now($user_id);
		$token = '';
		if($query_token->num_rows()>0){
			$query_token = $query_token->row();
			$token = $query_token->token;
		}

		$data['token'] = $token;
        
        $this->template->display_admin($this->kelompok.'/tes_hasil_operator_view', 'Hasil Tes', $data);
    }
    
    function get_datatable(){
		// variable initialization
		$token = $this->input->get('token');

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
		$query = $this->cbt_tes_user_model->get_datatable_operator($start, $rows, $token);
		$iFilteredTotal = $query->num_rows();
		
		$iTotal= $this->cbt_tes_user_model->get_datatable_operator_count($token)->row()->hasil;
	    
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
            $record[] = $temp->tesuser_creation_time;
            $record[] = $temp->tes_duration_time.' menit';
            $record[] = $temp->tes_nama;
            $record[] = $temp->grup_nama;
            $record[] = $temp->user_firstname;
            $record[] = $temp->nilai;
            if($temp->tesuser_status==1){
            	$tanggal = new DateTime();
                // Cek apakah tes sudah melebihi batas waktu
                $tanggal_tes = new DateTime($temp->tesuser_creation_time);
                $tanggal_tes->modify('+'.$temp->tes_duration_time.' minutes');
                if($tanggal>$tanggal_tes){
                	$record[] = 'Selesai';
                }else{
                	$tanggal = $tanggal_tes->diff($tanggal);
                	$menit_sisa = ($tanggal->h*60)+($tanggal->i);
                	$record[] = 'Berjalan (-'.$menit_sisa.' menit)';
                }
            }else{
            	$record[] = 'Selesai';
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