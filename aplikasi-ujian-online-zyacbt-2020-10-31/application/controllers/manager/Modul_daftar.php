<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modul_daftar extends Member_Controller {
	private $kode_menu = 'modul-daftar';
	private $kelompok = 'modul';
	private $url = 'manager/modul_daftar';
	
    function __construct(){
		parent:: __construct();
		$this->load->model('cbt_modul_model');
		$this->load->model('cbt_topik_model');
		$this->load->model('cbt_jawaban_model');
		$this->load->model('cbt_soal_model');

		parent::cek_akses($this->kode_menu);
	}
	
    public function index(){
        $data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;

        $query_user = $this->users_model->get_user_by_username($this->access->get_username());
        $select = '';
        $counter = 0;
        if($query_user->num_rows()>0){
            $query_user = $query_user->row();

            // Mengecek apakah user dibatasi hanya mengentry beberapa topik
            if(!empty($query_user->opsi1)){
                $user_topik = explode(',', $query_user->opsi1);
                foreach ($user_topik as $topik_id) {
                    $query_topik = $this->cbt_topik_model->get_by_kolom_join_modul('topik_id', $topik_id);
                    if($query_topik->num_rows()>0){
                        $topik = $query_topik->row();
                        
                        $jml_soal = $this->cbt_soal_model->count_by_kolom('soal_topik_id', $topik->topik_id)->row()->hasil;
                        $counter++;
                        $select = $select.'<option value="'.$topik->topik_id.'">'.$topik->modul_nama.' - '.$topik->topik_nama.' ['.$jml_soal.']</option>';
                    }
                }
            }else{
                // Jika user tidak dibatasi mengedit soal sesuai topik
                $query_modul = $this->cbt_modul_model->get_modul();
                if($query_modul->num_rows()>0){
                    $select = '';
                    $query_modul = $query_modul->result();
                    foreach ($query_modul as $temp) {
                        $query_topik = $this->cbt_topik_model->get_by_kolom_join_modul('topik_modul_id', $temp->modul_id);
                        if($query_topik->num_rows()){
                            $select = $select.'<optgroup label="Modul '.$temp->modul_nama.'">';

                            $query_topik = $query_topik->result();
                            foreach ($query_topik as $topik) {
                            	$jml_soal = $this->cbt_soal_model->count_by_kolom('soal_topik_id', $topik->topik_id)->row()->hasil;
                                $counter++;
                                $select = $select.'<option value="'.$topik->topik_id.'">'.$topik->modul_nama.' - '.$topik->topik_nama.' ['.$jml_soal.']</option>';
                            }

                            $select = $select.'</optgroup>';
                        }
                    }
                }
            }
        }

        if($counter==0){
        	$select = '<option value="kosong">Tidak Ada Data Topik</option>';
        }
        
        $data['select_topik'] = $select;
        
        $this->template->display_admin($this->kelompok.'/modul_daftar_view', 'Daftar Soal', $data);
	}
	
	function cetak_soal($topik_id=null){
		if(!empty($topik_id)){
			$query_soal = $this->cbt_soal_model->get_by_kolom('soal_topik_id', $topik_id);
			if($query_soal->num_rows()>0){
				$query_soal = $query_soal->result();

				$query_topik = $this->cbt_topik_model->get_by_kolom_limit('topik_id', $topik_id, 1)->row();

				$soal_table = '
					<H3>Daftar Soal</h3>
					<b>Topik = '.$query_topik->topik_nama.'</b>
					<hr />
					<table class="table" border="0">';

				$a = 1;

				foreach($query_soal as $temp){
					$posisi = $this->config->item('upload_path').'/topik_'.$temp->soal_topik_id;

					$soal = $temp->soal_detail;
					$soal = str_replace("[base_url]", base_url(), $soal);

					if($temp->soal_tipe==1){
						$tipe_soal = 'Pilihan Ganda';
					}else if($temp->soal_tipe==2){
						$tipe_soal = 'Essay';
					}

					if(!empty($temp->soal_audio)){
						$posisi = $this->config->item('upload_path').'/topik_'.$temp->soal_topik_id;
						$soal = $soal.'<br />
							<audio controls>
							<source src="'.base_url().$posisi.'/'.$temp->soal_audio.'" type="audio/mpeg">
							Your browser does not support the audio element.
							</audio>
						';
					}

					$soal_table = $soal_table.'
							<tr>
							<td>'.$a++.'</td>
							<td colspan="2">'.$soal.'</td>
							<td width="15%"></td>
							</tr>
					';

					$query_jawaban = $this->cbt_jawaban_model->get_by_soal($temp->soal_id);
					if($query_jawaban->num_rows()>0){
						$query_jawaban = $query_jawaban->result();
						foreach ($query_jawaban as $jawaban) {
							$temp_jawaban = $jawaban->jawaban_detail;
							$temp_jawaban = str_replace("[base_url]", base_url(), $temp_jawaban);

							$temp_benar = '';
							if($jawaban->jawaban_benar==1){
								$temp_benar = '<b>Benar</b>';
							}

							$soal_table = $soal_table.'
								<tr>
									<td width="5%"> </td>
									<td width="5%">'.$temp_benar.'</td>
									<td width="75%">'.$temp_jawaban.'</td>
									<td width="15%"></td>
								</tr>
							';
						}
					}

					$soal_table = $soal_table.'
								<tr>
									<td colspan="4"> ---------------------------------------------------------------------------- </td>
								</tr>
							';
				}

				$soal_table = $soal_table.'</table>
				
					<script lang="javascript">
    				window.print();
					</script>

				';

				echo $soal_table;
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
				$data['select'] = '<select name="topik" id="topik" class="form-control input-sm" onchange="refresh_table()">';
				foreach ($query as $temp) {
					$data['select'] = $data['select'].'<option value="'.$temp->topik_id.'">'.$temp->topik_nama.'</option>';	
				}
				$data['select'] = $data['select'].'</select>';
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
                      <td colspan="3">'.$soal.'</td>
                      <td width="15%"><a href="'.site_url().'/manager/modul_soal/index/'.$temp->soal_id.'" title="Edit Soal" style="cursor: pointer;"><span class="glyphicon glyphicon-edit"></span>Edit Soal</a></td>
                    </tr>
            ';


            if($temp->soal_tipe==1){
            	$query_jawaban = $this->cbt_jawaban_model->get_by_soal($temp->soal_id);
	            if($query_jawaban->num_rows()>0){
	            	$query_jawaban = $query_jawaban->result();
	            	$a = 0;
	            	foreach ($query_jawaban as $jawaban) {
	            		$temp_jawaban = $jawaban->jawaban_detail;
						$temp_jawaban = str_replace("[base_url]", base_url(), $temp_jawaban);

						$temp_benar = 'Salah';
						if($jawaban->jawaban_benar==1){
							$temp_benar = '<b>Benar</b>';
						}

	            		$jawaban_table = $jawaban_table.'
	            			<tr>
		                      	<td width="5%">'.++$a.'.</td>
		                      	<td width="5%">'.$temp_benar.'</td>
		                      	<td width="75%">'.$temp_jawaban.'</td>
		                      	<td width="15%"><a href="'.site_url().'/manager/modul_jawaban/index/'.$temp->soal_id.'/'.$jawaban->jawaban_id.'" title="Edit Jawaban" style="cursor: pointer;"><span class="glyphicon glyphicon-edit"></span>Edit Jawaban</a></td>
		                    </tr>
	            		';
	            	}
	            }
            }else if($temp->soal_tipe==3){
            	$jawaban_table = $jawaban_table.'
	            			<tr>
		                      	<td width="20%"><b>Kunci Jawaban</b></td>
		                      	<td colspan="3">'.$temp->soal_kunci.'</td>
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