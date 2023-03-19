<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ZYA CBT
* Achmad Lutfi
* achmdlutfi@gmail.com
* achmadlutfi.wordpress.com
*/
class Tes_evaluasi extends Member_Controller {
	private $kode_menu = 'tes-evaluasi';
	private $kelompok = 'tes';
	private $url = 'manager/tes_evaluasi';
	
    function __construct(){
		parent:: __construct();
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

        $query_tes = $this->cbt_tes_user_model->get_by_group();
        $select = '';
        if($query_tes->num_rows()>0){
        	$query_tes = $query_tes->result();
        	foreach ($query_tes as $temp) {
        		$select = $select.'<option value="'.$temp->tes_id.'">'.$temp->tes_nama.'</option>';
        	}
        }
        $data['select_tes'] = $select;
        
        $this->template->display_admin($this->kelompok.'/tes_evaluasi_view', 'Evaluasi Jawaban', $data);
    }

    function simpan_nilai(){
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('evaluasi-testlog-id', 'Soal','required|strip_tags');
        $this->form_validation->set_rules('evaluasi-nilai-min', 'Nilai Minimal','required|decimal|strip_tags');
        $this->form_validation->set_rules('evaluasi-nilai-max', 'Nilai Maximal','required|decimal|strip_tags');
        $this->form_validation->set_rules('evaluasi-nilai', '','required|numeric|strip_tags');
        
        if($this->form_validation->run() == TRUE){
            $nilai = $this->input->post('evaluasi-nilai', TRUE);
            $nilai_min = $this->input->post('evaluasi-nilai-min', TRUE);
            $nilai_max = $this->input->post('evaluasi-nilai-max', TRUE);
            $tessoal_id = $this->input->post('evaluasi-testlog-id', TRUE);

            if($nilai>=$nilai_min AND $nilai<=$nilai_max){
                $data['tessoal_nilai'] = $nilai;
                $data['tessoal_comment'] = 'Sudah di koreksi '.$this->access->get_username();

                $this->cbt_tes_soal_model->update('tessoal_id', $tessoal_id, $data);

                $status['status'] = 1;
                $status['pesan'] = 'Nilai berhasil disimpan ';
            }else{
                $status['status'] = 0;
                $status['pesan'] = 'Nilai tidak boleh dibawah Nilai Minimal dan di atas Nilai Maximal !';
            }
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }
    
    /**
     * Mendapatkan soal dan jawaban berdasarkan tessoal_id
     *
     * @param      <type>  $id     The identifier
     */
    function get_by_id($id=null){
    	$data['data'] = 0;
		if(!empty($id)){
			$query = $this->cbt_modul_model->get_by_kolom('modul_id', $id);
			if($query->num_rows()>0){
				$query = $query->row();
				$data['data'] = 1;
				$data['id'] = $query->modul_id;
				$data['modul'] = $query->modul_nama;
				$data['status'] = $query->modul_aktif;
			}
		}
		echo json_encode($data);
    }
    
    function get_datatable(){
		// variable initialization
		$tes_id = $this->input->get('tes');
		$urutkan = $this->input->get('urutkan');

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
		$query = $this->cbt_tes_user_model->get_datatable_evaluasi($start, $rows, $tes_id, $urutkan);
		$iFilteredTotal = $query->num_rows();
		
		$iTotal= $this->cbt_tes_user_model->get_datatable_evaluasi_count($tes_id, $urutkan)->row()->hasil;
	    
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

            $soal = $temp->soal_detail;
            $soal = str_replace("[base_url]", base_url(), $soal);
            
			$record[] = ++$i;
            $record[] = $soal;
			// $record[] = '<div style="width:600px;"><pre style="white-space: pre-wrap;word-wrap: break-word;">'.$temp->tessoal_jawaban_text.'</pre></div>';

			$jawaban = $temp->tessoal_jawaban_text;
			// Menambah tag br untuk baris baru
			$jawaban = str_replace("\r","<br />",$jawaban);
			$jawaban = str_replace("\n","<br />",$jawaban);
			
			$record[] = $jawaban;
			
            $record[] = '<a onclick="evaluasi(\''.$temp->tessoal_id.'\',\''.$temp->tes_score_wrong.'\',\''.$temp->tes_score_right.'\')" style="cursor: pointer;" class="btn btn-default btn-xs">Evaluasi</a>';
            

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