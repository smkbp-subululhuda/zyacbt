<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Peserta_online extends Member_Controller {
	private $kode_menu = 'peserta-online';
	private $kelompok = 'peserta';
	private $url = 'manager/peserta_online';
	
    function __construct(){
		parent:: __construct();
		$this->load->model('cbt_user_grup_model');
		$this->load->model('cbt_user_model');
		$this->load->model('cbt_tes_user_model');
		$this->load->model('cbt_tes_model');

		parent::cek_akses($this->kode_menu);
	}
	
    public function index($id_soal=null){
        $data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;
		
		$query_group = $this->cbt_user_grup_model->get_group();
        if($query_group->num_rows()>0){
        	$select = '<option value="semua">Semua</option>';
        	$query_group = $query_group->result();
        	foreach ($query_group as $temp) {
        		$select = $select.'<option value="'.$temp->grup_id.'">'.$temp->grup_nama.'</option>';
        	}

        }else{
        	$select = '<option value="0">KOSONG</option>';
        }
        $data['select_group'] = $select;
		
		$tanggal_awal = date('Y-m-d H:i', strtotime('- 1 hours'));
        $tanggal_akhir = date('Y-m-d H:i');
		$data['rentang_waktu'] = $tanggal_awal.' - '.$tanggal_akhir;
        
        $this->template->display_admin($this->kelompok.'/peserta_online_view', 'Status Online', $data);
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

			if(!empty($temp->soal_audio)){
				$posisi = $this->config->item('upload_path').'/topik_'.$temp->soal_topik_id;
				$soal = $soal.'<br />
					<audio controls>
					  <source src="'.base_url().$posisi.'/'.$temp->soal_audio.'" type="audio/mpeg">
					Your browser does not support the audio element.
					</audio>
				';
			}

            $record[] = $soal;

            $query_jawaban = $this->cbt_jawaban_model->count_by_kolom('jawaban_soal_id', $temp->soal_id)->row();

            $record[] = '<div style="text-align: center;">'.$query_jawaban->hasil.'</div>';
            /**$record[] = '
            	<a onclick="jawaban(\''.$temp->soal_id.'\')" style="cursor: pointer;" class="btn btn-default btn-xs">Tambah Jawaban</a>
            	<a onclick="edit(\''.$temp->soal_id.'\')" style="cursor: pointer;" class="btn btn-default btn-xs">Edit Soal</a>
            	<a onclick="hapus(\''.$temp->soal_id.'\')" style="cursor: pointer;" class="btn btn-default btn-xs">Hapus Soal</a>
            ';*/

            if($temp->soal_tipe!=2 AND $temp->soal_tipe!=3){
            	$record[] = '<div style="text-align: center;">
	            	<a onclick="jawaban(\''.$temp->soal_id.'\')" title="Tambah Jawaban" style="cursor: pointer;"><span class="glyphicon glyphicon-question-sign"></span></a>
	            	<a onclick="edit(\''.$temp->soal_id.'\')" title="Edit Soal" style="cursor: pointer;"><span class="glyphicon glyphicon-edit"></span></a>
	            	<a onclick="hapus(\''.$temp->soal_id.'\')" title="Hapus Soal" style="cursor: pointer;"><span class="glyphicon glyphicon-remove"></span></a>
	            	</div>
	            ';
            }else{
            	$record[] = '<div style="text-align: center;">
	            	<a onclick="edit(\''.$temp->soal_id.'\')" title="Edit Soal" style="cursor: pointer;"><span class="glyphicon glyphicon-edit"></span></a>
	            	<a onclick="hapus(\''.$temp->soal_id.'\')" title="Hapus Soal" style="cursor: pointer;"><span class="glyphicon glyphicon-remove"></span></a>
	            	</div>
	            ';
            }

			$output['aaData'][] = $record;
		}
		// format it to JSON, this output will be displayed in datatable
        
		echo json_encode($output);
	}

	function get_datatable_image(){
		$topik = $this->input->get('topik');
		if(!empty($topik)){
			$posisi = $this->config->item('upload_path').'/topik_'.$topik;
		}else{
			$posisi = $this->config->item('upload_path');
		}

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
		if(!is_dir($posisi)){
			mkdir($posisi);
	    }
		$query = directory_map($posisi, 1);

	    // get result after running query and put it in array
	    $iTotal = 0;
		$i=$start;

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
	        "iTotalRecords" => $iTotal,
	        "iTotalDisplayRecords" => $iTotal,
	        "aaData" => array()
	    );
		
	    foreach ($query as $temp) {			
			$record = array();

			$temp = str_replace("\\","", $temp);
            
			$record[] = ++$i;
			$is_dir=0;
			$is_image=0;
			$info = pathinfo($temp);

			if(!is_dir($posisi.'/'.$temp)){
            	if($info['extension']=='jpg' or $info['extension']=='png' or $info['extension']=='jpeg'){
            		$file_info = get_file_info($posisi.'/'.$temp);

            		$record[] = '<a style="cursor:pointer;" onclick="image_preview(\''.$posisi.'\',\''.$temp.'\')">'.$posisi.'/'.$temp.'</a>';
            		$record[] = '<a style="cursor:pointer;" onclick="image_preview(\''.$posisi.'\',\''.$temp.'\')"><img src="'.base_url().$posisi.'/'.$temp.'" height="40" /></a>';
            		$record[] = date('Y-m-d H:i:s', $file_info['date']);
            		$record[] = '<a onclick="image_preview(\''.$posisi.'\',\''.$temp.'\')" style="cursor: pointer;" class="btn btn-default btn-xs">Pilih</a>';
            		$output['aaData'][] = $record;

					$iTotal++;
            	}
        	}
		}

		$output['iTotalRecords'] = $iTotal;
		$output['iTotalDisplayRecords'] = $iTotal;
        
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