<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modul_import_word extends Member_Controller {
	private $kode_menu = 'modul-import-word';
	private $kelompok = 'modul';
	private $url = 'manager/modul_import_word';
	
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
        
        $this->template->display_admin($this->kelompok.'/modul_import_word_view', 'Mengimport Soal dari Word', $data);
    }

    function import(){
    	$this->load->library('form_validation');
        
        $this->form_validation->set_rules('topik', 'Topik','required');
		$this->form_validation->set_rules('import-soal', 'Daftar Soal','required');

        if($this->form_validation->run() == TRUE){
        	$id_topik = $this->input->post('topik', true);
			$daftar_soal = $this->input->post('import-soal', false);
			
			$doc = new DOMDocument();
			$doc->loadHTML($daftar_soal);
			$tables = $doc->getElementsByTagName('table');
			if(!empty($tables->item(0)->nodeValue)){
				$rows = $tables->item(0)->getElementsByTagName('tr');
				$counter = 1;
				$soal = '<table border="0" width="100%" class="no-padding" class="table-condensed">';
				foreach ($rows as $row) {
					/*** get each column by tag name ***/ 
					$cols = $row->getElementsByTagName('td'); 
					$kosong = 0;
					
					if(empty($cols->item(1)->nodeValue)){
						$kosong++;
					}
					
					if(empty($cols->item(2)->nodeValue)){
						$kosong++;
					}
					
					if(empty($cols->item(3)->nodeValue)){
						$kosong++;
					}
					if($kosong==0){
						$jenis = strtoupper(preg_replace('/\s+/', '',$cols->item(1)->nodeValue));
						$kunci = strtoupper(preg_replace('/\s+/', '',$cols->item(3)->nodeValue));
						$kunci = intval($kunci);
						if($kunci>1){
							$kunci=1;
						}
						if($jenis=='SOAL'){
							$soal = $soal.'
								<tr>
								<td valign="top"><p>'.$counter.'</p></td>
								<td colspan="2">'.$this->innerHTML($cols->item(2)).'</td>
								<td width="15%"></td>
								</tr>
							';
							$counter++;
						}else if($jenis=='JAWABAN'){
							$soal = $soal.'
									<tr>
										<td width="5%"> </td>
										<td width="5%" valign="top">'.$kunci.'</td>
										<td width="75%">'.$this->innerHTML($cols->item(2)).'</td>
										<td width="15%"></td>
									</tr>
								';
						}
					}else{
						// menghentikan loop ketika ada yang kosong
						break;
					}
			   }
			   $soal = $soal.'</table>';
			   
			   if($counter>1){
				   $status['topik'] = $id_topik;
				   $status['soal'] = $soal;
				   $status['status'] = 1;
				   $status['pesan'] = '';
			   }else{
				   $status['status'] = 0;
				   $status['pesan'] = 'Silahkan cek format soal terlebih dahulu';
			   }
			}else{
				$status['status'] = 0;
				$status['pesan'] = 'Terjadi kesalahan format soal. Silahkan cek format soal terlebih dahulu';
			}
        }else{
        	$status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        echo json_encode($status);
    }
	
	function konfirmasi(){
		$this->load->library('form_validation');
        
        $this->form_validation->set_rules('konfirmasi-topik', 'Topik','required');
		$this->form_validation->set_rules('konfirmasi-import-soal', 'Daftar Soal','required');
		if($this->form_validation->run() == TRUE){
			$id_topik = $this->input->post('konfirmasi-topik', true);
			$daftar_soal = $this->input->post('konfirmasi-import-soal', false);
	    	$posisi = $this->config->item('upload_path').'/topik_'.$id_topik.'';
			
			$doc = new DOMDocument();
			$doc->loadHTML($daftar_soal);
			$tables = $doc->getElementsByTagName('table');
			if(!empty($tables->item(0)->nodeValue)){
				$rows = $tables->item(0)->getElementsByTagName('tr');
				$counter_soal = 0;
				$counter_jawaban = 0;
				$soal_id = 0;
				foreach ($rows as $row) {
					/*** get each column by tag name ***/ 
					$cols = $row->getElementsByTagName('td'); 
					$kosong = 0;
					
					if(empty($cols->item(1)->nodeValue)){
						$kosong++;
					}
					if(empty($cols->item(2)->nodeValue)){
						$kosong++;
					}
					if(empty($cols->item(3)->nodeValue)){
						$kosong++;
					}
					if($kosong==0){
						$jenis = strtoupper(preg_replace('/\s+/', '',$cols->item(1)->nodeValue));
						$kunci = strtoupper(preg_replace('/\s+/', '',$cols->item(3)->nodeValue));
						$kunci = intval($kunci);
						if($kunci>1){
							$kunci=1;
						}
						if($jenis=='SOAL'){
							// Menyimpan image base64 menjadi file
							$soal = $this->innerHTML($cols->item(2));
							
							$tags = $cols->item(2)->getElementsByTagName('img');
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
									
									$soal = str_replace($soal_image, base_url().$file_name, $soal);
								}
							}
							$soal = str_replace(base_url(),"[base_url]", $soal);
							
							// Simpan soal
							$question['soal_topik_id'] = $id_topik;
							$question['soal_detail'] = $soal;
							$question['soal_tipe'] = '1';
							$question['soal_difficulty'] = '1';
							$question['soal_aktif'] = 1;
							
							$soal_id = $this->cbt_soal_model->save($question);
							$counter_soal++;
						}else if($jenis=='JAWABAN'){
							//$this->innerHTML($cols->item(2))
							if($soal_id!=0){
								$jawaban = $this->innerHTML($cols->item(2));
								$tags = $cols->item(2)->getElementsByTagName('img');
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
										
										$jawaban = str_replace($jawaban_image, base_url().$file_name, $jawaban);
									}
								}
								$jawaban = str_replace(base_url(),"[base_url]", $jawaban);
								
								$data['jawaban_soal_id'] = $soal_id;
								$data['jawaban_benar'] = $kunci;
								$data['jawaban_aktif'] = 1;
								$data['jawaban_detail'] = $jawaban;
								
								$this->cbt_jawaban_model->save($data);
								$counter_jawaban++;
							}
						}
					}else{
						// menghentikan loop ketika ada yang kosong
						break;
					}
			   }
			   $soal = $soal.'</table>';
			   
			   if($counter_soal>0){
					$status['counter_soal'] = $counter_soal;
					$status['counter_jawaban'] = $counter_jawaban;
					$status['status'] = 1;
					$status['pesan'] = 'Daftar soal berhasil di import';
			   }else{
					$status['status'] = 0;
					$status['pesan'] = 'Silahkan cek format soal terlebih dahulu';
			   }
			}else{
				$status['status'] = 0;
				$status['pesan'] = 'Terjadi kesalahan format soal. Silahkan cek format soal terlebih dahulu';
			}
		}else{
			$status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        echo json_encode($status);
	}
	
	function innerHTML(DOMNode $n, $include_target_tag = false ) {
		$doc = new DOMDocument();
		$doc->appendChild( $doc->importNode( $n, true ) );
		$html = trim( $doc->saveHTML() );
		if ( $include_target_tag ) {
			return $html;
		}
		return preg_replace( '@^<' . $n->nodeName .'[^>]*>|</'. $n->nodeName .'>$@', '', $html );
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