<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modul_soal extends Member_Controller {
	private $kode_menu = 'modul-soal';
	private $kelompok = 'modul';
	private $url = 'manager/modul_soal';
	
    function __construct(){
		parent:: __construct();
		$this->load->model('cbt_modul_model');
		$this->load->model('cbt_topik_model');
		$this->load->model('cbt_jawaban_model');
		$this->load->model('cbt_soal_model');
		$this->load->helper('directory');
		$this->load->helper('file');

		parent::cek_akses($this->kode_menu);
	}
	
    public function index($page=null, $id=null){
        $data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;

        $query_modul = $this->cbt_modul_model->get_modul();

        if($query_modul->num_rows()>0){
        	$select = '';
        	$query_modul = $query_modul->result();
        	foreach ($query_modul as $temp) {
        		$select = $select.'<option value="'.$temp->modul_id.'">'.$temp->modul_nama.'</option>';
        	}

        }else{
        	$select = '<option value="100000">KOSONG</option>';
        }
        $data['select_modul'] = $select;
        
        $this->template->display_admin($this->kelompok.'/modul_soal_view', 'Mengelola Soal', $data);
    }

    function tambah(){
    	$this->load->library('form_validation');
        
        $this->form_validation->set_rules('tambah-topik-id', 'Topik','required');
        $this->form_validation->set_rules('tambah-soal', 'Soal','required');
        $this->form_validation->set_rules('tambah-tipe', 'Tipe Soal','required|strip_tags');
        $this->form_validation->set_rules('tambah-kesulitan', 'Tingkat Kesulitan','required|strip_tags');
        
        if($this->form_validation->run() == TRUE){
        	$id_topik = $this->input->post('tambah-topik-id', TRUE);
        	$soal = $this->input->post('tambah-soal', FALSE);
        	$soal = str_replace(base_url(),"[base_url]", $soal);
        	$tipe = $this->input->post('tambah-tipe', TRUE);
        	$kesulitan = $this->input->post('tambah-kesulitan', TRUE);
        	$audio = $_FILES['tambah-audio']['name'];

        	if($id_topik=='kosong'){
        		$status['status'] = 0;
            	$status['pesan'] = 'Topik belum tersedia';
        	}else{
        		$data['soal_topik_id'] = $id_topik;
	        	$data['soal_detail'] = $soal;
	        	$data['soal_tipe'] = $tipe;
	        	$data['soal_difficulty'] = $kesulitan;
	        	$data['soal_aktif'] = 1;

	        	if(!empty($audio)){
	        		$data['soal_audio_play'] = $this->input->post('tambah-putar', TRUE);;

	        		$posisi = $this->config->item('upload_path').'/topik_'.$id_topik.'';

	        		if(!is_dir($posisi)){
	        			mkdir($posisi);
	        		}

	        		$field_name = 'tambah-audio';

	        		$config['upload_path'] = $posisi;
				    $config['allowed_types'] = 'mp3';
				    $config['max_size']	= '0';
				    $config['overwrite'] = true;
				    $config['file_name'] = strtolower($_FILES[$field_name]['name']);

				    $this->load->library('upload', $config);
				    if (!$this->upload->do_upload($field_name)){
			        	$status['status_upload'] = 0;
			            $status['pesan_upload'] = $this->upload->display_errors();
			        }else{
			        	$upload_data = $this->upload->data();
						$status['status_upload'] = 1;
			            $status['pesan_upload'] = 'File '.$upload_data['file_name'].' BERHASIL di IMPORT';
			        }
			        $data['soal_audio'] = $upload_data['file_name'];
	        	}

	        	$this->cbt_soal_model->save($data);

	        	$status['status'] = 1;
	        	$status['pesan'] = 'Soal berhasil disimpan';
        	}
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }

    function test(){
    	$this->load->helper('directory');
    	$map = directory_map('./uploads', 1);
    	foreach ($map as $temp) {
    		$info = pathinfo($temp);
    		if(is_dir('./uploads/'.$temp)){
    			echo $temp .' adalah Direktory';
    			echo '<br />';
    		}else{
    			echo '<img src="'.base_url().'uploads/'.$temp.'" width="500" />';
    		}
    	}
    }
	
	function get_topik_by_modul($modul_id=null){
		$data['data'] = 0;
		if(!empty($modul_id)){
			$query = $this->cbt_topik_model->get_by_kolom('topik_modul_id', $modul_id);
			if($query->num_rows()>0){
				$query = $query->result();
				$data['data'] = 1;
				foreach ($query as $temp) {
					$topik['id'] = $temp->topik_id;
					$topik['topik'] = $temp->topik_nama;

					$data['topik'][] = $topik;
				}
			}
		}
		echo json_encode($data);
	}
    
    function get_by_id($id=null){
    	$data['data'] = 0;
		if(!empty($id)){
			$query = $this->cbt_topik_model->get_by_kolom('topik_id', $id);
			if($query->num_rows()>0){
				$query = $query->row();
				$data['data'] = 1;
				$data['id'] = $query->topik_id;
				$data['topik'] = $query->topik_nama;
				$data['deskripsi'] = $query->topik_detail;
				$data['status'] = $query->topik_aktif;
			}
		}
		echo json_encode($data);
    }
    
    function get_datatable(){
		// variable initialization
		$topik = $this->input->get('topik');

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
		$query = $this->cbt_soal_model->get_datatable($start, $rows, 'soal_detail', $search, $topik);
		$iFilteredTotal = $query->num_rows();
		
		$iTotal= $this->cbt_soal_model->get_datatable_count('soal_detail', $search, $topik)->row()->hasil;
	    
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
			$soal = $temp->soal_detail;
			$soal = str_replace("[base_url]", base_url(), $soal);

            $record[] = $soal;
            $record[] = 'jml';
            $record[] = '<a onclick="edit(\''.$temp->soal_id.'\')" style="cursor: pointer;" class="btn btn-default btn-xs">Edit</a>
            	<a onclick="hapus(\''.$temp->soal_id.'\')" style="cursor: pointer;" class="btn btn-default btn-xs">Del</a>
            ';

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