<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ZYA CBT
* Achmad Lutfi
* achmdlutfi@gmail.com
* achmadlutfi.wordpress.com
*/
class Tool_exportimport_soal extends Member_Controller {
	private $kode_menu = 'tool-exportimport-soal';
	private $kelompok = 'tool';
	private $url = 'manager/tool_exportimport_soal';
	
    function __construct(){
		parent:: __construct();
		$this->load->model('cbt_user_grup_model');
		$this->load->model('cbt_user_model');
		$this->load->model('cbt_topik_model');
		$this->load->model('cbt_modul_model');
		$this->load->model('cbt_soal_model');
		$this->load->model('cbt_jawaban_model');

		parent::cek_akses($this->kode_menu);
	}
	
    public function index(){
        $data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;

        $data['post_max_size'] = ini_get('post_max_size');
        $data['upload_max_filesize'] = ini_get('upload_max_filesize');

        $dir1 = './public/uploads/';
        $dir2 = './uploads/';

        $data['dir_public_uploads'] = 'Not Writeable';
        if(is_writable($dir1)){
        	$data['dir_public_uploads'] = 'Writeable';
        }

        $data['dir_uploads'] = 'Not Writeable';
        if(is_writable($dir2)){
        	$data['dir_uploads'] = 'Writeable';
        }
        
        $this->template->display_admin($this->kelompok.'/tool_exportimport_soal_view', 'Export / Import Data Soal', $data);
    }

    public function exportsoal(){
    	ini_set("memory_limit","-1");
		ini_set('max_execution_time', 400);

		$this->load->library('zip');

		$sql = '';
		$delimiter = ';**;**;';

		$query_modul = $this->cbt_modul_model->get_all();
		$query_topik = $this->cbt_topik_model->get_all();
		$query_soal = $this->cbt_soal_model->get_all();
		$query_jawaban = $this->cbt_jawaban_model->get_all();

		if($query_modul->num_rows()>0){
			$query_modul = $query_modul->result();
			foreach ($query_modul as $modul) {
				$sql = $sql.'INSERT INTO `cbt_modul` (`modul_id`, `modul_nama`, `modul_aktif`) VALUES (\''.$modul->modul_id.'\', \''.$modul->modul_nama.'\', '.$modul->modul_aktif.')'.$delimiter;
			}			
		}

		if($query_topik->num_rows()>0){
			$query_topik = $query_topik->result();
			foreach ($query_topik as $topik) {
				$sql = $sql.'INSERT INTO `cbt_topik` (`topik_id`, `topik_modul_id`, `topik_nama`, `topik_detail`, `topik_aktif`) VALUES (\''.$topik->topik_id.'\', \''.$topik->topik_modul_id.'\', \''.addslashes($topik->topik_nama).'\', \''.addslashes($topik->topik_detail).'\', '.$topik->topik_aktif.')'.$delimiter;
			}			
		}

		if($query_soal->num_rows()>0){
			$query_soal = $query_soal->result();
			foreach ($query_soal as $soal) {
				if(empty($soal->soal_kunci)){
					$soal_kunci = 'NULL';
				}else{
					$soal_kunci = '\''.$soal->soal_kunci.'\'';
				}
				if(empty($soal->soal_audio)){
					$soal_audio = 'NULL';
				}else{
					$soal_audio = '\''.$soal->soal_audio.'\'';
				}
				if(empty($soal->soal_timer)){
					$soal_timer = 'NULL';
				}else{
					$soal_timer = '\''.$soal->soal_timer.'\'';
				}

				$sql = $sql.'INSERT INTO `cbt_soal` (`soal_id`, `soal_topik_id`, `soal_detail`, `soal_tipe`, `soal_kunci`, `soal_difficulty`, `soal_aktif`, `soal_audio`, `soal_audio_play`, `soal_timer`, `soal_inline_answers`, `soal_auto_next`) VALUES (\''.$soal->soal_id.'\', \''.$soal->soal_topik_id.'\', \''.addslashes($soal->soal_detail).'\', '.$soal->soal_tipe.', '.$soal_kunci.', '.$soal->soal_difficulty.', '.$soal->soal_aktif.', '.$soal_audio.', '.$soal->soal_audio_play.', '.$soal_timer.', '.$soal->soal_inline_answers.', '.$soal->soal_auto_next.')'.$delimiter;
			}			
		}

		if($query_jawaban->num_rows()>0){
			$query_jawaban = $query_jawaban->result();
			foreach ($query_jawaban as $jawaban) {
				$sql = $sql.'INSERT INTO `cbt_jawaban` (`jawaban_id`, `jawaban_soal_id`, `jawaban_detail`, `jawaban_benar`, `jawaban_aktif`) VALUES (\''.$jawaban->jawaban_id.'\', \''.$jawaban->jawaban_soal_id.'\', \''.addslashes($jawaban->jawaban_detail).'\', '.$jawaban->jawaban_benar.', '.$jawaban->jawaban_aktif.')'.$delimiter;
			}			
		}

		$this->zip->add_data('zyacbt-soal.sql', $sql);

		$path = $this->config->item('upload_path');
		$this->zip->read_dir($path);

		$this->zip->download('zyacbt-soal.zip');
    }

	/*
    public function exportsoal(){
    	ini_set("memory_limit","-1");

		$this->load->dbutil();
		$this->load->library('zip');

		$prefs = array(
	        'tables'        => array('cbt_modul', 'cbt_topik', 'cbt_soal', 'cbt_jawaban'),
	        'ignore'        => array(),
	        'format'        => 'txt',
	        'filename'      => 'zyacbt-soal.sql',
	        'add_drop'      => TRUE,
	        'add_insert'    => TRUE,
	        'newline'       => "\n"
		);		
		$backup = $this->dbutil->backup($prefs);

		$this->zip->add_data('zyacbt-soal.sql', $backup);

		$path = $this->config->item('upload_path');
		$this->zip->read_dir($path);

		$this->zip->download('zyacbt-soal.zip');
    }*/

    public function importsoal(){
    	$data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;
        $delimiter = ';**;**;';

        $data['post_max_size'] = ini_get('post_max_size');
        $data['upload_max_filesize'] = ini_get('upload_max_filesize');

        $dir1 = './public/uploads/';
        $dir2 = './uploads/';

        $data['dir_public_uploads'] = 'Not Writeable';
        if(is_writable($dir1)){
        	$data['dir_public_uploads'] = 'Writeable';
        }

        $data['dir_uploads'] = 'Not Writeable';
        if(is_writable($dir2)){
        	$data['dir_uploads'] = 'Writeable';
        }

        $this->load->library('form_validation');

        $data['error'] = '';
        $data['error_upload'] = '';

        if(!empty($_FILES['userfile']['name'])){    
	        // Cek apakah masih ada topik atau tidak, jika topik masih ada, maka proses digagalkan
	        if($this->cbt_topik_model->count_all()->row()->hasil>0){
	        	$data['error'] = 'Data Soal Gagal di Import. Masih ada Topik di ZYA CBT. Silahkan hapus Topik terlebih dahulu.';
	        }else{
	        	$config['upload_path'] = './public/uploads/';
		        $config['allowed_types'] = 'zip';
		        $config['max_size']	= '0';
		        $config['overwrite'] = true;
		        $config['file_name'] = $_FILES['userfile']['name'];

	        	$this->load->library('upload', $config);
	            if (!$this->upload->do_upload()){
	            	$data['error_upload'] = $this->upload->display_errors().'Tipe file yang di upload adalah '.$_FILES['userfile']['type'];
	            }else{
	            	$upload_data = $this->upload->data();
	                $data['filename'] = 'File '.$upload_data['file_name'].' BERHASIL di UPLOAD';
	                        
	                // disini proses import data soal dimulai
	                ini_set("memory_limit","-1");
					ini_set('max_execution_time', 200);
					
	                $this->load->library('zip');

	                // Extract file hasil upload
	                // jika tidak bisa extract, maka proses digagalkan
	                $zip = new ZipArchive;
 
			        if ($zip->open($config['upload_path'].$upload_data['file_name']) === TRUE)			        {
			            $zip->extractTo($config['upload_path'].'import/');
			            $zip->close();

			            $error_sql = 0;
			            $count_import = 0;
			            // Memulai transaction mysql
						$this->db->trans_start();

			            // Hapus Semua Data Modul jika ada
			            $this->cbt_modul_model->empty_table();
			            
			            // Import SQL
			            if (file_exists($config['upload_path'].'import/zyacbt-soal.sql')) {
			            	$isi_file = file_get_contents($config['upload_path'].'import/zyacbt-soal.sql'); 
	          				//$string_query = rtrim( $isi_file, "\n;" );
	          				//$string_query = str_replace('_ci;', ');', $isi_file);
	          				$array_query = explode($delimiter, $isi_file);
	          				foreach($array_query as $query){
	                    		if(preg_match('/INSERT INTO/', $query)){
	                    			//echo $query; 
	                    			//echo '<hr />';
	                    			$this->db->query($query);
	                    			$count_import++;
	                    		}
	                    	}
			            }
			            
			            // Menutup transaction mysql
						$this->db->trans_complete(); 
						if ($this->db->trans_status() === FALSE){
        					// generate an error... or use the log_message() function to log your error
        					$error_sql = 1;
        				}

        				// Jika tidak ada data yang di import, maka tambahkan Modul Default karena diawal tadi dihapus
        				//if($count_import==0){
        				//	$data_modul['modul_nama'] = 'Default';
	            		//	$data_modul['modul_aktif'] = 1;
	            		//	$this->cbt_modul_model->save($data_modul);
        				//}

			            // Pindahkan folder gambar dan audio ke uploads jika proses import SQL berhasil
			            if($error_sql!=1 and $count_import>0){
			            	// Cek apakah folder import/uploads ada
			            	if(is_dir($config['upload_path'].'import/uploads/')){
			            		$this->recurse_copy($config['upload_path'].'import/uploads/', './uploads/');
			            	}
							
							$data['filename'] = $data['filename'].'<br />Data Soal Berhasil Di Import. Silahkan cek Soal dan Jawaban.';
			            }else{
			            	if($error_sql==1){
			            		$data['error'] = 'Terjadi Kesalahan pada File SQL SOAL.';
			            	}else{
			            		$data['error'] = 'Tidak ada Data Soal yang di Import. Silahkan cek kembali.';
			            	}
			            }
			        }else{
			        	$data['error'] = 'Data Soal Rusak. Silahkan cek kembali Data yang akan di import.';
			        }

			        if (file_exists($config['upload_path'].'import')) {
			        	$this->rmdir_recursive($config['upload_path'].'import');
			        }
	            }   
	        }       
        }else{
        	$data['error_upload'] = 'Pilih File yang akan di IMPORT';
        }

        $this->template->display_admin($this->kelompok.'/tool_exportimport_soal_view', 'Export / Import Data Soal', $data);	
    }

    function recurse_copy($src,$dst) {
		$dir = opendir($src);
		if(!is_dir($dst)){
			mkdir($dst);
		}
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
				}else {
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}

	function rmdir_recursive($dir) {
	    foreach(scandir($dir) as $file) {
	        if ('.' === $file || '..' === $file) continue;
	        if (is_dir("$dir/$file")) $this->rmdir_recursive("$dir/$file");
	        else unlink("$dir/$file");
	    }
	    rmdir($dir);
	}
}