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
		$this->load->model('users_model');
		$this->load->model('cbt_tes_soal_model');
		$this->load->model('cbt_tes_topik_set_model');
		$this->load->helper('directory');
		$this->load->helper('file');

		parent::cek_akses($this->kode_menu);
	}
	
    public function index($id_soal=null){
        $data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;

        $is_edit = 0;

        if($id_soal!=null){
        	$query_soal = $this->cbt_soal_model->get_by_kolom_limit('soal_id', $id_soal, 1);
        	if($query_soal->num_rows()>0){
        		$query_soal = $query_soal->row();
        		$data['data_id'] = $query_soal->soal_id;
        		$data['data_soal'] = $query_soal->soal_detail;
        		$data['data_audio'] = $query_soal->soal_audio;
        		$data['data_putar'] = $query_soal->soal_audio_play;
        		$data['data_tipe'] = $query_soal->soal_tipe;
        		$data['data_kesulitan'] = $query_soal->soal_difficulty;
        		$data['data_topik'] = $query_soal->soal_topik_id;

        		$is_edit = 1;
        	}
        }

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
	        			$counter++;

	        			if(!empty($data['data_topik'])){
        					if($data['data_topik']==$topik->topik_id){
        						$select = $select.'<option value="'.$topik->topik_id.'" selected>'.$topik->modul_nama.' - '.$topik->topik_nama.'</option>';
        					}else{
        						$select = $select.'<option value="'.$topik->topik_id.'">'.$topik->modul_nama.' - '.$topik->topik_nama.'</option>';	
        					}
        				}else{
        					$select = $select.'<option value="'.$topik->topik_id.'">'.$topik->modul_nama.' - '.$topik->topik_nama.'</option>';
        				}
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
		        				$counter++;
		        				if(!empty($data['data_topik'])){
		        					if($data['data_topik']==$topik->topik_id){
		        						$select = $select.'<option value="'.$topik->topik_id.'" selected>'.$topik->modul_nama.' - '.$topik->topik_nama.'</option>';
		        					}else{
		        						$select = $select.'<option value="'.$topik->topik_id.'">'.$topik->modul_nama.' - '.$topik->topik_nama.'</option>';	
		        					}
		        				}else{
		        					$select = $select.'<option value="'.$topik->topik_id.'">'.$topik->modul_nama.' - '.$topik->topik_nama.'</option>';
		        				}
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

        if($counter!=0 and $is_edit==1){
        	$data['data_soal'] = '
        		edit(\''.$id_soal.'\');
        	';
        }
        
        $data['select_topik'] = $select;
        
        $this->template->display_admin($this->kelompok.'/modul_soal_view', 'Mengelola Soal', $data);
    }

    function tambah(){
    	$this->load->library('form_validation');
        
        $this->form_validation->set_rules('tambah-topik-id', 'Topik','required');
        $this->form_validation->set_rules('tambah-soal', 'Soal','required');
        $this->form_validation->set_rules('tambah-tipe', 'Tipe Soal','required|strip_tags');
        $this->form_validation->set_rules('tambah-kesulitan', 'Tingkat Kesulitan','required|strip_tags');
        
        if($this->form_validation->run() == TRUE){
        	$id_soal = $this->input->post('tambah-soal-id', TRUE);
        	$id_topik = $this->input->post('tambah-topik-id', TRUE);
        	$soal = $this->input->post('tambah-soal', FALSE);
        	$tipe = $this->input->post('tambah-tipe', TRUE);
        	$kesulitan = $this->input->post('tambah-kesulitan', TRUE);
        	$audio = $_FILES['tambah-audio']['name'];

        	$posisi = $this->config->item('upload_path').'/topik_'.$id_topik.'';

        	$doc = new DOMDocument();
			$doc->loadHTML($soal);
			$tags = $doc->getElementsByTagName('img');
			foreach ($tags as $tag) {
				$soal_image = $tag->getAttribute('src');
				if (strpos($soal_image, 'data:image/') !== false) {
					$soal_image_array = preg_split("@[:;,]+@", $soal_image);
					$extensi = explode('/', $soal_image_array[1]);

					$file_name = $posisi.'/'.uniqid().'.'.$extensi[1];

					if(!is_dir($posisi)){
			        	mkdir($posisi);
			        }

					// menyimpan file dari base64
					file_put_contents($file_name, base64_decode($soal_image_array[3]));

					//echo $data[1].'<br />'; // tipe file
					//echo $data[3].'<br />'; // data base64
					
					$soal = str_replace($soal_image, base_url().$file_name, $soal);
				}
			}

        	$soal = str_replace(base_url(),"[base_url]", $soal);

        	// Mengecek apakah tipe soal adalah jawaban singkat
        	$kunci_jawaban_singkat = '';
        	$status_jawaban_singkat = 1;
        	if($tipe==3){
        		$kunci_jawaban_singkat = $this->input->post('tambah-kunci-jawaban-singkat', TRUE);
        		if(empty($kunci_jawaban_singkat)){
        			$status_jawaban_singkat = 0;
        		}
        	}

        	if($id_topik=='kosong'){
        		$status['status'] = 0;
            	$status['pesan'] = 'Topik belum tersedia';
            }else if($status_jawaban_singkat==0){
            	$status['status'] = 0;
            	$status['pesan'] = 'Kunci Jawaban untuk Soal Jawaban Singkat tidak boleh kosong !';
        	}else{
        		$data['soal_topik_id'] = $id_topik;
	        	$data['soal_detail'] = $soal;
	        	$data['soal_tipe'] = $tipe;
	        	$data['soal_kunci'] = $kunci_jawaban_singkat;
	        	$data['soal_difficulty'] = $kesulitan;
	        	$data['soal_aktif'] = 1;
	        	$data['soal_audio_play'] = $this->input->post('tambah-putar', TRUE);

	        	$upload = 0;
	        	if(!empty($audio)){
	        		$upload = 1;

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
			            $status['pesan_upload'] = $this->upload->display_errors().'Tipe file yang di upload adalah '.$_FILES[$field_name]['type'];
			        }else{
			        	$upload_data = $this->upload->data();
			        	$data['soal_audio'] = $upload_data['file_name'];
						$status['status_upload'] = 1;
			            $status['pesan_upload'] = 'File '.$upload_data['file_name'].' BERHASIL di IMPORT';
			        }
	        	}

	        	if(!empty($id_soal)){
	        		/**
	        		 * Jika soal update
	        		 */
	        		$this->cbt_soal_model->update('soal_id', $id_soal, $data);
	        	}else{
	        		/**
	        		 * Jika soal baru
	        		 */
	        		$this->cbt_soal_model->save($data);	
	        	}

	        	if($upload==0){
	        		$status['status'] = 1;
	        		$status['pesan'] = 'Soal berhasil disimpan';
	        	}else{
	        		if($status['status_upload']==1){
		        		$status['status'] = 1;
		        		$status['pesan'] = 'Soal berhasil disimpan';
		        	}else{
		        		$status['status'] = 1;
		        		$status['pesan'] = 'Soal berhasil disimpan, tetapi Audio tidak tersimpan dengan kesalahan<br />Pesan : '.$status['pesan_upload'];
		        	}
	        	}
        	}
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }

    function hapus_soal(){
    	$this->load->library('form_validation');
        
        $this->form_validation->set_rules('hapus-id', 'Soal','required');
        
        if($this->form_validation->run() == TRUE){
        	$id = $this->input->post('hapus-id', TRUE);

        	$query_soal = $this->cbt_soal_model->get_by_kolom_limit('soal_id', $id, 1);
        	if($query_soal->num_rows()>0){
        		$query_soal = $query_soal->row();

        		if($this->cbt_tes_topik_set_model->count_by_kolom('tset_topik_id', $query_soal->soal_topik_id)->row()->hasil>0){
	        		$status['status'] = 0;
	            	$status['pesan'] = 'Soal tidak bisa dihapus, Topik soal masih dipakai pada Tes.';
	        	}else{
	        		$this->cbt_soal_model->delete('soal_id', $id);
		        	$status['status'] = 1;
		        	$status['pesan'] = 'Soal berhasil dihapus';
	        	}
        	}else{
        		$status['status'] = 0;
            	$status['pesan'] = 'Terjadi kesalahan, silahkan cek terlebih dahulu data Soal.';
        	}
        }else{
			$status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }

    function get_by_id($id=null){
    	$data['data'] = 0;
		if(!empty($id)){
			$query = $this->cbt_soal_model->get_by_kolom('soal_id', $id);
			if($query->num_rows()>0){
				$query = $query->row();
				$data['data'] = 1;
				$data['id'] = $query->soal_id;
				$soal = $query->soal_detail;
				$soal = str_replace("[base_url]", base_url(), $soal);
				$data['soal'] = $soal;
				$data['tipe'] = $query->soal_tipe;
				$data['kunci'] = $query->soal_kunci;
				$data['kesulitan'] = $query->soal_difficulty;
				$data['audio'] = $query->soal_audio;
				$data['putar'] = $query->soal_audio_play;
				$data['id_topik'] = $query->soal_topik_id;
			}
		}
		echo json_encode($data);
    }

    function upload_file(){
    	$this->load->library('form_validation');
        
        $this->form_validation->set_rules('image-topik-id', 'Topik','required');

        if($this->form_validation->run() == TRUE){
        	$id_topik = $this->input->post('image-topik-id', true);
	    	$posisi = $this->config->item('upload_path').'/topik_'.$id_topik;

	    	if(!is_dir($posisi)){
	        	mkdir($posisi);
	        }

	    	$field_name = 'image-file';
	        if(!empty($_FILES[$field_name]['name'])){
		    	$config['upload_path'] = $posisi;
			    $config['allowed_types'] = 'jpg|png|jpeg|gif';
			    $config['max_size']	= '0';
			    $config['overwrite'] = true;
			    $config['file_name'] = strtolower($_FILES[$field_name]['name']);

			    if(file_exists($posisi.'/'.$config['file_name'])){
	        		$status['status'] = 0;
	            	$status['pesan'] = 'Nama file sudah terdapat pada direktori, silahkan ubah nama file yang akan di upload';
		    	}else{
			        $this->load->library('upload', $config);
		            if (!$this->upload->do_upload($field_name)){
		            	$status['status'] = 0;
		            	$status['pesan'] = $this->upload->display_errors();
		            }else{
		            	$upload_data = $this->upload->data();

		            	$status['status'] = 1;
		                $status['pesan'] = 'File '.$upload_data['file_name'].' BERHASIL di IMPORT';
		                $status['image'] = '<img src="'.base_url().$posisi.'/'.$upload_data['file_name'].'" style="max-height: 110px;" />';
		                $status['image_isi'] = '<img src="'.base_url().$posisi.'/'.$upload_data['file_name'].'" style="max-width: 600px;" />';
		            }   	
		    	}     
	        }else{
	        	$status['status'] = 0;
	            $status['pesan'] = 'Pilih terlebih dahulu file yang akan di upload';
	        }
        }else{
        	$status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        echo json_encode($status);
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
            	if($info['extension']=='jpg' or $info['extension']=='png' or $info['extension']=='jpeg' or $info['extension']=='gif'){
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