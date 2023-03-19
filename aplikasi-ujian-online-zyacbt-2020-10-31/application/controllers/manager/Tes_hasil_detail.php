<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ZYA CBT
* Achmad Lutfi
* achmdlutfi@gmail.com
* achmadlutfi.wordpress.com
*/
class Tes_hasil_detail extends Member_Controller {
	private $kode_menu = 'tes-hasil';
	private $kelompok = 'tes';
	private $url = 'manager/tes_hasil_detail';
	
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
	
    public function index($tesuser_id=null){
        $data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;

        if(!empty($tesuser_id)){
        	$query_testuser = $this->cbt_tes_user_model->get_by_kolom_limit('tesuser_id', $tesuser_id, 1);
        	if($query_testuser->num_rows()>0){
        		$query_testuser = $query_testuser->row();

        		$query_test = $this->cbt_tes_model->get_by_kolom_limit('tes_id', $query_testuser->tesuser_tes_id, 1)->row();
        		$query_user = $this->cbt_user_model->get_by_kolom_limit('user_id', $query_testuser->tesuser_user_id, 1)->row();

        		$data['tes_user_id'] = $tesuser_id;
        		$data['tes_nama'] = $query_test->tes_nama;
        		$data['tes_mulai'] = $query_testuser->tesuser_creation_time;
        		$data['user_nama'] = $query_user->user_firstname;

        		$nilai = $this->cbt_tes_soal_model->get_nilai($tesuser_id)->row();
        		$data['nilai'] = $nilai->hasil.'  /  '.$query_test->tes_max_score.'  (nilai / nilai maksimal) ';

        		$data['benar'] = ($nilai->total_soal-$nilai->jawaban_salah).'  /  '.$nilai->total_soal.'  (jawaban benar / total soal)';

        		$this->template->display_admin($this->kelompok.'/tes_hasil_detail_view', 'Hasil Tes Detail', $data);
        	}else{
        		redirect('manager/tes_hasil');	
        	}
        }else{
        	redirect('manager/tes_hasil');
        }
    }
    
    function get_datatable(){
		// variable initialization
		$tesuser_id = $this->input->get('tes_user_id');

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
		$query = $this->cbt_tes_soal_model->get_datatable($start, $rows, 'soal_detail', $search, $tesuser_id);
		$iFilteredTotal = $query->num_rows();
		
		$iTotal= $this->cbt_tes_soal_model->get_datatable_count('soal_detail', $search, $tesuser_id)->row()->hasil;
	    
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

			if($temp->soal_tipe==1){
				$record[] = 'Pilihan Ganda';
			}else if($temp->soal_tipe==2){
				$record[] = 'Essay';
			}else if($temp->soal_tipe==3){
				$record[] = 'Jawaban Singkat';
			}

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

            $jawaban_table = '
            	<table class="table" border="0">
            		<tr>
                      <td colspan="4">'.$soal.'</td>
                    </tr>
            ';

            // cek tipe soal
            // Jika soal adalah jenis pilihan ganda
            if($temp->soal_tipe==1){
            	$query_jawaban = $this->cbt_tes_soal_jawaban_model->get_by_tessoal($temp->tessoal_id);
	            if($query_jawaban->num_rows()>0){
	            	$query_jawaban = $query_jawaban->result();
	            	$a = 0;
	            	$jawaban_table = $jawaban_table.'
	            			<tr>
		                      	<td width="5%"> </td>
		                      	<td width="5%">Kunci</td>
		                      	<td width="5%">Pilihan</td>
		                      	<td width="85%">Jawaban</td>
		                    </tr>
	            		';
	            	foreach ($query_jawaban as $jawaban) {
	            		$temp_jawaban = $jawaban->jawaban_detail;
						$temp_jawaban = str_replace("[base_url]", base_url(), $temp_jawaban);

						$temp_benar = '';
						if($jawaban->jawaban_benar==1){
							$temp_benar = '<b>1</b>';
						}
						$temp_pilihan = '';
						if($jawaban->soaljawaban_selected==1){
							$temp_pilihan = '<b>1</b>';
						}

	            		$jawaban_table = $jawaban_table.'
	            			<tr>
		                      	<td width="5%">'.++$a.'.</td>
		                      	<td width="5%">'.$temp_benar.'</td>
		                      	<td width="5%">'.$temp_pilihan.'</td>
		                      	<td width="85%">'.$temp_jawaban.'</td>
		                    </tr>
	            		';
	            	}
	            }
            }else if($temp->soal_tipe==2){
            	// Jika soal adalah soal essay
            	$jawaban_table = $jawaban_table.'
            		<tr>
		            	<td width="5%"></td>
		                <td width="5%">Skor</td>
		                <td width="90%" colspan="2">Jawaban</td>
		            </tr>
	            	<tr>
		            	<td width="5%"></td>
		                <td width="5%">'.$temp->tessoal_nilai.'</td>
		                <td width="90%" colspan="2"><div style="width:100%;"><pre style="white-space: pre-wrap;word-wrap: break-word;">'.$temp->tessoal_jawaban_text.'</pre></div></td>
		            </tr>
	            ';
            }else if($temp->soal_tipe==3){
            	// Jika soal adalah soal Jawaban Singkat
            	$jawaban_table = $jawaban_table.'
            		<tr>
		            	<td width="5%"></td>
		                <td width="5%">Skor</td>
		                <td width="90%" colspan="2">Jawaban Singkat</td>
		            </tr>
	            	<tr>
		            	<td width="5%"></td>
		                <td width="5%">'.$temp->tessoal_nilai.'</td>
		                <td width="90%" colspan="2"><div style="width:100%;">'.$temp->tessoal_jawaban_text.'</div></td>
		            </tr>
	            ';
            }
            $jawaban_table = $jawaban_table.'</table>';

            $record[] = $jawaban_table;

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