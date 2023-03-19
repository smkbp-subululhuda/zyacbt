<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modul_jawaban extends Member_Controller {
	private $kode_menu = 'modul-soal';
	private $kelompok = 'modul';
	private $url = 'manager/modul_jawaban';
	
    function __construct(){
		parent:: __construct();
		$this->load->model('cbt_modul_model');
		$this->load->model('cbt_topik_model');
		$this->load->model('cbt_jawaban_model');
		$this->load->model('cbt_soal_model');
		$this->load->model('cbt_tes_soal_jawaban_model');
		$this->load->helper('directory');
		$this->load->helper('file');

		parent::cek_akses($this->kode_menu);
	}
	
    public function index($id_soal=null, $id_jawaban=null){
        $data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;

        if($id_soal!=null){
        	$is_edit = 0;

	        if($id_jawaban!=null){
	        	$query_jawaban = $this->cbt_jawaban_model->get_by_kolom_limit('jawaban_id', $id_jawaban, 1);
	        	if($query_jawaban->num_rows()>0){
	        		$query_jawaban = $query_jawaban->row();
	        		$data['data_id'] = $query_jawaban->jawaban_id;
	        		$data['data_jawaban'] = $query_jawaban->jawaban_detail;
	        		$data['data_benar'] = $query_jawaban->jawaban_benar;

	        		$is_edit = 1;
	        	}
	        }

        	$query_soal = $this->cbt_soal_model->get_by_kolom_limit('soal_id', $id_soal, 1);
        	if($query_soal->num_rows()>0){
        		$query_soal = $query_soal->row();

        		if($query_soal->soal_tipe!=2){
        			$data['id_soal'] = $query_soal->soal_id;
	        		$soal = $query_soal->soal_detail;
					$soal = str_replace("[base_url]", base_url(), $soal);

					if(!empty($query_soal->soal_audio)){
						$posisi = $this->config->item('upload_path').'/topik_'.$query_soal->soal_topik_id;
						$soal = $soal.'
							<audio controls>
							  <source src="'.base_url().$posisi.'/'.$query_soal->soal_audio.'" type="audio/mpeg">
							Your browser does not support the audio element.
							</audio>
						';
					}

					$data['soal'] = $soal;

					$query_topik = $this->cbt_topik_model->get_by_kolom_limit('topik_id', $query_soal->soal_topik_id, 1)->row();
					$data['topik'] = $query_topik->topik_nama;
					$data['id_topik'] = $query_topik->topik_id;
        		}else{
        			redirect('manager/modul_soal');
        		}

        	}else{
        		redirect('manager/modul_soal');
        	}
        }else{
        	redirect('manager/modul_soal');
        }

        if($is_edit==1){
        	$data['data_jawaban'] = '
        		edit(\''.$id_jawaban.'\');
        	';
        }
        
        $this->template->display_admin($this->kelompok.'/modul_jawaban_view', 'Mengelola Jawaban', $data);
    }

    function tambah(){
    	$this->load->library('form_validation');
        
        $this->form_validation->set_rules('tambah-soal-id', 'Jawaban','required|strip_tags');
        $this->form_validation->set_rules('tambah-jawaban', 'Jawaban','required');
        $this->form_validation->set_rules('tambah-benar', 'Status Jawaban','required|strip_tags');
        
        if($this->form_validation->run() == TRUE){
        	$id_soal = $this->input->post('tambah-soal-id', TRUE);
        	$id_jawaban = $this->input->post('tambah-jawaban-id', TRUE);

        	$jawaban = $this->input->post('tambah-jawaban', FALSE);

        	$data['jawaban_soal_id'] = $id_soal;
	        $data['jawaban_benar'] = $this->input->post('tambah-benar', TRUE);
	        $data['jawaban_aktif'] = 1;

	        $id_topik = $this->cbt_soal_model->get_by_kolom_limit('soal_id', $id_soal, 1)->row()->soal_topik_id;
	        $posisi = $this->config->item('upload_path').'/topik_'.$id_topik.'';

	        $doc = new DOMDocument();
			$doc->loadHTML($jawaban);
			$tags = $doc->getElementsByTagName('img');
			foreach ($tags as $tag) {
				$jawaban_image = $tag->getAttribute('src');
				if (strpos($jawaban_image, 'data:image/') !== false) {
					$jawaban_image_array = preg_split("@[:;,]+@", $jawaban_image);
					$extensi = explode('/', $jawaban_image_array[1]);

					$file_name = $posisi.'/'.uniqid().'.'.$extensi[1];

					if(!is_dir($posisi)){
			        	mkdir($posisi);
			        }

					// menyimpan file dari base64
					file_put_contents($file_name, base64_decode($jawaban_image_array[3]));

					//echo $data[1].'<br />'; // tipe file
					//echo $data[3].'<br />'; // data base64
					
					$jawaban = str_replace($jawaban_image, base_url().$file_name, $jawaban);
				}
			}

	        $jawaban = str_replace(base_url(),"[base_url]", $jawaban);

	        $data['jawaban_detail'] = $jawaban;

	        if(!empty($id_jawaban)){
	        	/**
	        	 * Jika jawban salah
	        	 */
	        	$this->cbt_jawaban_model->update('jawaban_id', $id_jawaban, $data);
	        	$status['status'] = 1;
	        	$status['pesan'] = 'Jawaban yang dirubah berhasil disimpan';
	        }else{
	        	/**
	        	 * Jika jawaban baru
	        	 */
	        	$this->cbt_jawaban_model->save($data);	
	        	$status['status'] = 1;
	        	$status['pesan'] = 'Jawaban berhasil disimpan';
	        }
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }

    function hapus_jawaban(){
    	$this->load->library('form_validation');
        
        $this->form_validation->set_rules('hapus-id', 'Soal','required');
        
        if($this->form_validation->run() == TRUE){
        	$id = $this->input->post('hapus-id', TRUE);

        	if($this->cbt_tes_soal_jawaban_model->count_by_kolom('soaljawaban_jawaban_id', $id)->row()->hasil>0){
        		$status['status'] = 0;
            	$status['pesan'] = 'Jawaban tidak bisa dihapus, Jawaban masih dipakai dalam Tes.';
        	}else{
        		$this->cbt_jawaban_model->delete('jawaban_id', $id);
	        	$status['status'] = 1;
	        	$status['pesan'] = 'Jawaban berhasil dihapus';
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
			$query = $this->cbt_jawaban_model->get_by_kolom_limit('jawaban_id', $id, 1);
			if($query->num_rows()>0){
				$query = $query->row();
				$data['data'] = 1;
				$data['id'] = $query->jawaban_id;
				$data['id_soal'] = $query->jawaban_soal_id;
				$jawaban = $query->jawaban_detail;
				$jawaban = str_replace("[base_url]", base_url(), $jawaban);
				$data['jawaban'] = $jawaban;
				$data['benar'] = $query->jawaban_benar;
			}
		}
		echo json_encode($data);
    }

    function get_soal_id($id=null){
    	$data['data'] = 0;
		if(!empty($id)){
			$query = $this->cbt_soal_model->get_by_kolom('soal_id', $id);
			if($query->num_rows()>0){
				$query = $query->row();
				$data['data'] = 1;
				$data['id_soal'] = $query->soal_id;
				$soal = $query->soal_detail;
				$soal = str_replace("[base_url]", base_url(), $soal);

				if(!empty($query->soal_audio)){
					$posisi = $this->config->item('upload_path').'/topik_'.$query->soal_topik_id;
					$soal = $soal.'
						<audio controls>
						  <source src="'.base_url().$posisi.'/'.$query->soal_audio.'" type="audio/mpeg">
						Your browser does not support the audio element.
						</audio>
					';
				}

				$data['soal'] = $soal;
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
			    $config['allowed_types'] = 'jpg|png|jpeg';
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
		$soal = $this->input->get('soal');

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
		$query = $this->cbt_jawaban_model->get_datatable($start, $rows, 'jawaban_detail', $search, $soal);
		$iFilteredTotal = $query->num_rows();
		
		$iTotal= $this->cbt_jawaban_model->get_datatable_count('jawaban_detail', $search, $soal)->row()->hasil;
	    
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
			$jawaban = $temp->jawaban_detail;
			$jawaban = str_replace("[base_url]", base_url(), $jawaban);

            $record[] = $jawaban;
            if($temp->jawaban_benar==1){
            	$record[] = 'Benar';
            }else{
            	$record[] = 'Salah';
            }

            $record[] = '<div style="text-align: center;">
            	<a onclick="edit(\''.$temp->jawaban_id.'\')" title="Edit Jawaban" style="cursor: pointer;"><span class="glyphicon glyphicon-edit"></span></a>
            	<a onclick="hapus(\''.$temp->jawaban_id.'\')" title="Hapus Jawaban" style="cursor: pointer;"><span class="glyphicon glyphicon-remove"></span></a>
            	</div>
            ';

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