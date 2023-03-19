<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modul_import extends Member_Controller {
	private $kode_menu = 'modul-import';
	private $kelompok = 'modul';
	private $url = 'manager/modul_import';
	
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
                        $counter++;

                        $jml_soal = $this->cbt_soal_model->count_by_kolom('soal_topik_id', $topik->topik_id)->row()->hasil;

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
                                $counter++;

                                $jml_soal = $this->cbt_soal_model->count_by_kolom('soal_topik_id', $topik->topik_id)->row()->hasil;
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
        
        $this->template->display_admin($this->kelompok.'/modul_import_view', 'Mengimport Soal', $data);
    }

    function import(){
    	$this->load->library('form_validation');
        
        $this->form_validation->set_rules('topik', 'Topik','required');

        if($this->form_validation->run() == TRUE){
        	$id_topik = $this->input->post('topik', true);
	    	$posisi = './public/uploads/';

	        if(!empty($_FILES['userfile']['name'])){
		    	$config['upload_path'] = $posisi;
			    $config['allowed_types'] = 'xls|xlsx';
			    $config['max_size']	= '0';
			    $config['overwrite'] = true;
			    $config['file_name'] = $_FILES['userfile']['name'];

			    $this->load->library('upload', $config);
			    if (!$this->upload->do_upload()){
		        	$status['status'] = 0;
		            $status['pesan'] = $this->upload->display_errors().'Tipe file yang di upload adalah '.$_FILES['userfile']['type'];
		        }else{
		        	$upload_data = $this->upload->data();
		        	$data['filename'] = 'File '.$upload_data['file_name'].' BERHASIL di IMPORT';
                    
                    $status['status'] = 1;

                	// disini proses import data
                	$status['pesan'] = $this->import_file($upload_data['file_name'], $id_topik);
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

    function import_file($inputfile, $id_topik){
        $this->load->library('excel');
        $inputFileName = './public/uploads/'.$inputfile;

        $excel = PHPExcel_IOFactory::load($inputFileName);
        $worksheet = $excel->getSheet(0);
        $highestRow = $worksheet->getHighestRow();
        $pesan='<div class="alert alert-info alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-info"></i> Informasi!</h4>';
        
        if($highestRow>10){
            $jmlsoalsukses=0;
            $jmlsoalerror=0;
            $row=6;
            $kosong=0;
            while($kosong<2){
                $kosong=0;
                $kolom1 = $worksheet->getCellByColumnAndRow(2, $row)->getValue();//jenis, soal atau jawaban
                $kolom2 = $worksheet->getCellByColumnAndRow(3, $row)->getValue();//isi 
                $kolom3 = $worksheet->getCellByColumnAndRow(4, $row)->getValue();//jawaban benar atau salah
                $kolom4 = $worksheet->getCellByColumnAndRow(5, $row)->getValue();//tingkat kesulitan
                
                if(empty($kolom1)){ $kosong=+2; }
                if(empty($kolom2)){ $kosong=+2; }
                if(empty($kolom4) and $kolom1=='Q'){ $kosong++; }
                
                if($kosong==0){
                	// Merubah html special char menjadi kode
                	$kolom2 = htmlspecialchars($kolom2);

                	// Menambah tag br untuk baris baru
                	$kolom2 = str_replace("\r","<br />",$kolom2);
                	$kolom2 = str_replace("\n","<br />",$kolom2);
                	/**
                	 * Jika tipe adalah Question
                	 */
                	if($kolom1=='Q'){
                		$question['soal_topik_id'] = $id_topik;
			        	$question['soal_detail'] = $kolom2;
			        	$question['soal_tipe'] = '1';
			        	$question['soal_difficulty'] = $kolom4;
			        	$question['soal_aktif'] = 1;

                		$soal_id = $this->cbt_soal_model->save($question);
                		$jmlsoalsukses++;


                	/**
                	 * Jika tipe adalah Answer
                	 */
                	}else if($kolom1=='A'){
				        $answer['jawaban_detail'] = $kolom2;
				        if(!empty($kolom3)){
				        	$answer['jawaban_benar'] = $kolom3;
				        }else{
				        	$answer['jawaban_benar'] = '0';
				        }
				        $answer['jawaban_aktif'] = 1;
				        $answer['jawaban_soal_id'] = $soal_id;

				        $this->cbt_jawaban_model->save($answer);
                	}

                }else{
                	if($kosong<2){
                		$pesan=$pesan.'Baris ke  '.$row.' GAGAL di simpan : '.$kolom2.'<br>';
                    	$jmlsoalerror++;
                	}
                }
                
                $row++;
            }
            $pesan = $pesan.'<br>Jumlah soal yang berhasil diimport adalah '.$jmlsoalsukses.'<br>
                            Jumlah soal yang gagal di dimport adalah '.$jmlsoalerror.'<br>
                            Jumlah total baris yang diproses adalah '.($row-6).'<br>';
        }else{
            $pesan = $pesan.'Tidak Ada Yang Berhasil Di IMPORT. Cek kembali file excel yang dikirim';
        }
        $pesan = $pesan.'</div>';
        
        return $pesan;
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