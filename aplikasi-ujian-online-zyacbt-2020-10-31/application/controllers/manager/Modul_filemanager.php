<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modul_filemanager extends Member_Controller {
	private $kode_menu = 'modul-filemanager';
	private $kelompok = 'modul';
	private $url = 'manager/modul_filemanager';
	
    function __construct(){
		parent:: __construct();
		$this->load->model('cbt_modul_model');
		$this->load->helper('directory');
		$this->load->helper('file');

		parent::cek_akses($this->kode_menu);
	}
	
    public function index(){
        $data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;
        $data['upload_path'] = $this->config->item('upload_path');
        
        $this->template->display_admin($this->kelompok.'/modul_filemanager_view', 'File Manager', $data);
    }

    function tambah_dir(){
    	$this->load->library('form_validation');
        
		$this->form_validation->set_rules('tambah-dir', 'Direktori','required|strip_tags');
        
        if($this->form_validation->run() == TRUE){
            $dir = $this->input->post('tambah-dir', true);
            $posisi = $this->input->post('tambah-posisi', true);
            $posisi = $this->config->item('upload_path').'/'.$posisi;

            if(is_dir($posisi.'/'.$dir)){
            	$status['status'] = 0;
            	$status['pesan'] = 'Direktori sudah ada, silahkan cek kembali';
            }else{
            	mkdir($posisi.'/'.$dir);

            	$status['status'] = 1;
            	$status['pesan'] = 'Direktori berhasil dibuat';
            }
            
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }

    function upload_file(){
    	$posisi = $this->input->post('upload-posisi', true);
    	$posisi = $this->config->item('upload_path').'/'.$posisi;

    	$field_name = 'upload-file';
        if(!empty($_FILES[$field_name]['name'])){
	    	$config['upload_path'] = $posisi;
		    $config['allowed_types'] = 'jpg|png|jpeg|mp3';
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
	            }   	
	    	}     
        }else{
        	$status['status'] = 0;
            $status['pesan'] = 'Pilih terlebih dahulu file yang akan di upload';
        }
        echo json_encode($status);
    }

    function hapus_file(){
    	$this->load->library('form_validation');
        
		$this->form_validation->set_rules('hapus-file', 'File','required|strip_tags');
        
        if($this->form_validation->run() == TRUE){
            $file = $this->input->post('hapus-file', true);
            $posisi = $this->input->post('hapus-posisi', true);
            $posisi = $this->config->item('upload_path').'/'.$posisi;

            if(is_dir($posisi.'/'.$file)){
            	delete_files($posisi.'/'.$file, TRUE);
            	rmdir($posisi.'/'.$file);

            	$status['status'] = 1;
            	$status['pesan'] = 'Direktori berhasil dihapus ';
            }else{
            	unlink($posisi.'/'.$file);

            	$status['status'] = 1;
            	$status['pesan'] = 'File berhasil dihapus ';
            }
            
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }

    function get_datatable(){
    	$posisi = $this->input->get('posisi');

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
		$posisi = $this->config->item('upload_path').'/'.$posisi;
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

			if(is_dir($posisi.'/'.$temp)){
            	$record[] = '<a style="cursor:pointer;" onclick="open_dir(\''.$temp.'\')"><b>'.$temp.'</b></a>';
            	$is_dir=1;
        	}else{
        		if($info['extension']=='jpg' or $info['extension']=='png' or $info['extension']=='jpeg'){
            		$record[] = '<a style="cursor:pointer;" onclick="open_image(\''.$temp.'\')">'.$temp.'</a>';
            		$is_image=1;
            	}else{
            		$record[] = $temp;
            	}
        	}

            $file_info = get_file_info($posisi.'/'.$temp);

            if($is_dir==1){
            	$record[] = 'Direktori';
            }else{
            	if($is_image==1){
            		$record[] = '<a style="cursor:pointer;" onclick="open_image(\''.$temp.'\')"><img src="'.base_url().$posisi.'/'.$temp.'" height="50" /></a>';
            	}else{
            		$record[] = 'File bukan gambar';
            	}
            }

            $record[] = date('Y-m-d H:i:s', $file_info['date']);
            $record[] = '<div style="text-align:right;">'.number_format($file_info['size']).' B</div>';
            $record[] = '<a onclick="hapus_file(\''.$temp.'\')" style="cursor: pointer;" class="btn btn-default btn-xs">Hapus</a>';

			$output['aaData'][] = $record;

			$iTotal++;
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