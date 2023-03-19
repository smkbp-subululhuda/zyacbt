<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Laporan_analisis_butir_soal extends Member_Controller {
	private $kode_menu = 'laporan-analisis-butir-soal';
	private $kelompok = 'laporan';
	private $url = 'manager/laporan_analisis_butir_soal';
	
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
	
    public function index(){
		$data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;

        $username = $this->access->get_username();
		$user_id = $this->users_model->get_login_info($username)->id;

        $query_group = $this->cbt_user_grup_model->get_group();
        $select = '';
        if($query_group->num_rows()>0){
        	$query_group = $query_group->result();
        	foreach ($query_group as $temp) {
        		$select = $select.'<option value="'.$temp->grup_id.'">'.$temp->grup_nama.'</option>';
        	}

        }else{
        	$select = '<option value="0">Tidak Ada Group</option>';
        }
        $data['select_group'] = $select;
		
		$query_tes = $this->cbt_tes_user_model->get_by_group();
        $select = '';
        if($query_tes->num_rows()>0){
        	$query_tes = $query_tes->result();
        	foreach ($query_tes as $temp) {
        		$select = $select.'<option value="'.$temp->tes_id.'">'.$temp->tes_nama.'</option>';
        	}
        }else{
			$select = '<option value="kosong">Belum Ada Tes yang Dilakukan</option>';
		}
        $data['select_tes'] = $select;
        
        $this->template->display_admin($this->kelompok.'/laporan_analisis_butir_soal_view', 'Analisis Butir Soal', $data);
    }

    public function export(){
    	$this->load->library('form_validation');
        
        $this->form_validation->set_rules('pilih-grup', 'Grup','required|strip_tags');
        $this->form_validation->set_rules('nama-grup', 'Grup','required|strip_tags');
        $this->form_validation->set_rules('pilih-tes', 'Nama Tes','required|strip_tags');
		$this->form_validation->set_rules('nama-tes', 'Nama Tes','required|strip_tags');

        $this->load->library('excel');
            
        $inputFileName = './public/form/form-data-analisis-butir-soal.xlsx';
        $excel = PHPExcel_IOFactory::load($inputFileName);
        $worksheet = $excel->getSheet(0);
		
		$nama_grup = '';
        
        if($this->form_validation->run() == TRUE){
            $tes = $this->input->post('pilih-tes', true);
            $grup = $this->input->post('pilih-grup', true);
            $nama_grup = $this->input->post('nama-grup', true);
			$nama_tes = $this->input->post('nama-tes', true);

            // Mengambil Data Peserta berdasarkan grup
            $query_user = $this->cbt_tes_user_model->get_by_tes_group($tes, $grup);
			
            $worksheet->setCellValueByColumnAndRow(2, 3, $nama_grup);
            $worksheet->setCellValueByColumnAndRow(2, 4, $nama_tes);

            if($query_user->num_rows()>0){
            	$query_user = $query_user->result();

            	$row = 8;
            	foreach ($query_user as $user) {
            		$worksheet->setCellValueByColumnAndRow(0, $row, ($row-7));
					$worksheet->setCellValueByColumnAndRow(1, $row, $user->user_name);
                    $worksheet->setCellValueByColumnAndRow(2, $row, stripslashes($user->user_firstname));

					//Mengambil data jawaban yang telah dikerjakan
					$query_soal_tes = $this->cbt_tes_soal_model->get_by_testuser_order_soal($user->tesuser_id);
					if($query_soal_tes->num_rows()){
						$query_soal_tes = $query_soal_tes->result();
						$kolom = 3;
						foreach ($query_soal_tes as $soal_tes) {
							if($soal_tes->tessoal_nilai>0){
								$worksheet->setCellValueByColumnAndRow($kolom, $row, '1');
							}else{
								$worksheet->setCellValueByColumnAndRow($kolom, $row, '0');
							}
							$kolom++;
						}
					}

                    $row++;
            	}
            }
        }

        $filename = 'Analisis Butir Soal - '.$nama_grup.'.xlsx'; //save our workbook as this file name
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
                 
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
}