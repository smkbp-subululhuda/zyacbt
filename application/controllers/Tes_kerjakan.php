<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ZYA CBT
* Achmad Lutfi
* achmdlutfi@gmail.com
* achmadlutfi.wordpress.com
*/
class Tes_kerjakan extends Tes_Controller {
	private $kelompok = 'ujian';
	private $url = 'tes_kerjakan';
    private $username;
    private $user_id;

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

        $this->username = $this->access_tes->get_username();
        $this->user_id = $this->cbt_user_model->get_by_kolom_limit('user_name', $this->username, 1)->row()->user_id;
	}
    
    public function index($tes_id=null){
        if(!empty($tes_id)){
            $data['nama'] = $this->access_tes->get_nama();
            $data['group'] = $this->access_tes->get_group();
            $data['url'] = $this->url;
            $data['timestamp'] = strtotime(date('Y-m-d H:i:s'));

            $query_tes = $this->cbt_tes_user_model->get_by_user_tes_limit($this->user_id, $tes_id, 1);
            if($query_tes->num_rows()>0){
                $query_tes = $query_tes->row();
                $tanggal = new DateTime();
                // Cek apakah tes sudah melebihi batas waktu
                $tanggal_tes = new DateTime($query_tes->tesuser_creation_time);
                $tanggal_tes->modify('+'.$query_tes->tes_duration_time.' minutes');
                if($tanggal>=$tanggal_tes){
                    // jika waktu sudah melebihi waktu ketentuan, maka diarahkan ke dashboard
                    redirect('tes_dashboard');
                }else{
                    // mengambil soal sesuai dengan tes yang dikerjakan
                    $data['tes_id'] = $tes_id;
                    $data['tes_user_id'] = $query_tes->tesuser_id;
                    $data['tes_name'] = $query_tes->tes_nama;
                    $data['tes_waktu'] = $query_tes->tes_duration_time;
                    $data['tes_dibuat'] = $query_tes->tesuser_creation_time;
                    $data['tanggal'] = $tanggal->format('Y-m-d H:i:s');

                    // Mengambil selisih jam
                    $tanggal_tes = new DateTime($query_tes->tesuser_creation_time);
                    $tanggal_diff = $tanggal_tes->diff($tanggal);

                    $detik_berjalan = ($tanggal_diff->h*60*60)+($tanggal_diff->i*60)+$tanggal_diff->s;
                    $detik_total = $query_tes->tes_duration_time*60;

                    // untuk menangani Jika tes setelah ditambah waktunya melebihi jam saat itu
                    // jika time saat ini lebih besar dari time creation
                    if($tanggal>=$tanggal_tes){
                        $detik_sisa = $detik_total-$detik_berjalan;
                    
                    // jika time creation lebih besar dari tanggal saat ini
                    }else{
                        $detik_sisa = $detik_total+$detik_berjalan;
                    }

                    $data['detik_berjalan'] = $detik_berjalan;
                    $data['detik_total'] = $detik_total;
                    $data['detik_sisa'] = $detik_sisa;

                    // Mengambil menu daftar semua soal
                    $data_soal = $this->get_daftar_soal($tes_id);

                    $data['tes_daftar_soal'] = $data_soal['tes_soal'];
                    $data['tes_soal_jml'] = $data_soal['tes_soal_jml'];

                    // Mengambil data soal ke 1
                    $tessoal = $this->cbt_tes_soal_model->get_by_testuser_limit($query_tes->tesuser_id, 1)->row();
                    $data_soal = $this->get_soal($tessoal->tessoal_id, $query_tes->tesuser_id);

                    $data['tes_soal'] = $data_soal['tes_soal'];
                    $data['tes_ragu'] = $data_soal['tes_ragu'];
                    $data['tes_soal_id'] = $tessoal->tessoal_id;
                    $data['tes_soal_nomor'] = $tessoal->tessoal_order;

                    
                    $this->template->display_tes($this->kelompok.'/tes_kerjakan_view', 'Kerjakan Tes', $data);
                }
            }else{
                redirect('tes_dashboard');
            }
        }else{
            redirect('tes_dashboard');
        }
    }

    /**
     * Menghentikan tes yang sudah berjalan
     */
    function hentikan_tes(){
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('hentikan-tes-id', 'Tes','required|strip_tags');
        $this->form_validation->set_rules('hentikan-tes-user-id', 'Tes','required|strip_tags');
        $this->form_validation->set_rules('hentikan-tes-nama', 'Nama Tes','required|strip_tags');
        
        if($this->form_validation->run() == TRUE){
            $tesuser_id = $this->input->post('hentikan-tes-user-id', TRUE);
            
            $centang = $this->input->post('hentikan-centang', TRUE);
            if(!empty($centang)){
                $data_tes['tesuser_status']=4;
                $this->cbt_tes_user_model->update('tesuser_id', $tesuser_id, $data_tes);

                $status['status'] = 1;
                $status['pesan'] = "Tes berhasil dihentikan";   
            }else{
                $status['status'] = 0;
                $status['pesan'] = "Centang terlebih dahulu kolom yang tersedia !";
            }
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }

    /**
     * Menyimpan jawaban yang dipilih oleh User
     */
    function simpan_jawaban(){
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('tes-id', 'Tes','required|strip_tags');
        $this->form_validation->set_rules('tes-user-id', 'Tes User','required|strip_tags');
        $this->form_validation->set_rules('tes-soal-id', 'Soal','required|strip_tags');
        $this->form_validation->set_rules('tes-soal-nomor', 'Nomor Soal','required|strip_tags');
        $this->form_validation->set_rules('soal-jawaban', 'Jawaban','required|strip_tags');
        
        if($this->form_validation->run() == TRUE){
            $jawaban = $this->input->post('soal-jawaban', TRUE);
            $tes_id = $this->input->post('tes-id', TRUE);
            $tes_user_id = $this->input->post('tes-user-id', TRUE);
            $tes_soal_id = $this->input->post('tes-soal-id', TRUE);
            $tes_soal_nomor = $this->input->post('tes-soal-nomor', TRUE);

            // Mengecek apakah tes masih berjalan dan waktu masih mencukupi
            //if($this->cbt_tes_user_model->count_by_status_waktu($tes_user_id)->row()->hasil>0){
            //
            // revisi 2018-11-15
            // agar waktu mengambil dari waktu php, bukan mysql
            $waktuuser = date('Y-m-d H:i:s');
            if($this->cbt_tes_user_model->count_by_status_waktuuser($tes_user_id, $waktuuser)->row()->hasil>0){

                // Mengecek apakah soal ada
                $query_soal = $this->cbt_tes_soal_model->get_by_tessoal_limit($tes_soal_id, 1);
                if($query_soal->num_rows()>0){
                    $query_soal = $query_soal->row();

                    $data_tes_soal['tessoal_change_time'] = date('Y-m-d H:i:s');

                    // menonatifkan ragu-ragu
                    $data_tes_soal['tessoal_ragu'] = 0;

                    // Memulai transaction mysql
                    $this->db->trans_start();

                    // Mengecek jenis soal
                    if($query_soal->soal_tipe==1){
                        // Mendapatkan data tes
                        $query_tes = $this->cbt_tes_model->get_by_kolom_limit('tes_id', $tes_id, 1)->row();

                        // Mendapatkan data jawaban
                        $query_jawaban = $this->cbt_tes_soal_jawaban_model->get_by_tessoal_answer($tes_soal_id, $jawaban)->row();

                        // Mengupdate pilihan jawaban benar
                        $data_jawaban['soaljawaban_selected']=1;
                        $this->cbt_tes_soal_jawaban_model->update_by_tessoal_answer($tes_soal_id, $jawaban, $data_jawaban);
                        // Mengupdate pilihan jawaban salah
                        $data_jawaban['soaljawaban_selected']=0;
                        $this->cbt_tes_soal_jawaban_model->update_by_tessoal_answer_salah($tes_soal_id, $jawaban, $data_jawaban);

                        // Mengupdate score, change time jika pilihan benar
                        if($query_jawaban->jawaban_benar==1){
                            $data_tes_soal['tessoal_nilai'] = $query_tes->tes_score_right;
                        }else{
                            $data_tes_soal['tessoal_nilai'] = $query_tes->tes_score_wrong;
                        }

                        $this->cbt_tes_soal_model->update('tessoal_id', $tes_soal_id, $data_tes_soal);

                        $status['status'] = 1;
                        $status['nomor_soal'] = $tes_soal_nomor;
                        $status['pesan'] = 'Jawaban yang dipilih berhasil disimpan';
                        
                    }else if($query_soal->soal_tipe==2){
                        // Mengupdate change time, dan jawaban essay
                        $data_tes_soal['tessoal_jawaban_text'] = $jawaban;
                        $data_tes_soal['tessoal_nilai'] = 0;
                        $this->cbt_tes_soal_model->update('tessoal_id', $tes_soal_id, $data_tes_soal);

                        $status['status'] = 1;
                        $status['nomor_soal'] = $tes_soal_nomor;
                        $status['pesan'] = 'Jawaban yang dimasukkan berhasil disimpan';
                    }else if($query_soal->soal_tipe==3){
                        // Mendapatkan data tes
                        $query_tes = $this->cbt_tes_model->get_by_kolom_limit('tes_id', $tes_id, 1)->row();
                        
                        // Mengupdate change time, dan jawaban essay
                        $data_tes_soal['tessoal_jawaban_text'] = $jawaban;
                        if(strtoupper($query_soal->soal_kunci)==strtoupper($jawaban)){
                            $data_tes_soal['tessoal_nilai'] = $query_tes->tes_score_right;
                        }else{
                            $data_tes_soal['tessoal_nilai'] = $query_tes->tes_score_wrong;
                        }
                        $this->cbt_tes_soal_model->update('tessoal_id', $tes_soal_id, $data_tes_soal);

                        $status['status'] = 1;
                        $status['nomor_soal'] = $tes_soal_nomor;
                        $status['pesan'] = 'Jawaban yang dimasukkan berhasil disimpan';
                    }

                    // Menutup transaction mysql
                    $this->db->trans_complete();
                }else{
                    $status['status'] = 0;
                    $status['pesan'] = 'Terjadi Kesalahan, silahkan hubungi Administrator';
                }
            }else{
                $status['status'] = 2;
                $status['pesan'] = 'Terjadi Kesalahan, Tes sudah selesai';
            }
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }

    /**
     * Mendapatkan info tes
     * 1. nama tes
     * 2. jumlah soal yang belum dijawab
     * 3. jumlah soal yang sudah dijawab
     *
     * @param      <type>  $tes_user_id  The tes user identifier
     */
    function get_tes_info($tes_id=null){
        $data['data'] = 0;
        if(!empty($tes_id)){
            $query_tes = $this->cbt_tes_user_model->get_by_user_tes_limit($this->user_id, $tes_id, 1);
            if($query_tes->num_rows()>0){
                $query_tes = $query_tes->row();
                $data['data'] = 1;
                $data['tes_id'] = $tes_id;
                $data['tes_user_id'] = $query_tes->tesuser_id;
                $data['tes_nama'] = $query_tes->tes_nama;
                $data['tes_dijawab'] = $this->cbt_tes_soal_model->count_by_tesuser_dijawab($query_tes->tesuser_id)->row()->hasil.' Soal';
                $data['tes_blum_dijawab'] = $this->cbt_tes_soal_model->count_by_tesuser_blum_dijawab($query_tes->tesuser_id)->row()->hasil.' Soal';
            }
        }

        echo json_encode($data);
    }

    /**
     * Mendapatkan data cbt_tes_soal berdasarkan tessoal_id
     * @param  [type] $tessoal_id [description]
     * @return [type]            [description]
     */
    function get_tes_soal_by_tessoal($tessoal_id=null){
        $data['data'] = 0;
        if(!empty($tessoal_id)){
            $query_tes_soal = $this->cbt_tes_soal_model->get_by_kolom_limit('tessoal_id', $tessoal_id, 1);
            if($query_tes_soal->num_rows()>0){
                $query_tes_soal = $query_tes_soal->row();
                $data['data'] = 1;
                $data['tessoal_id'] = $query_tes_soal->tessoal_id;
                $data['tessoal_ragu'] = $query_tes_soal->tessoal_ragu;

                $data['tessoal_dikerjakan'] = 0;
                if(!empty($query_tes_soal->tessoal_change_time)){
                    $data['tessoal_dikerjakan'] = 1;
                }
            }
        }

        echo json_encode($data);
    }

    function update_tes_soal_ragu($tessoal_id=null, $ragu=null){
        $data['data'] = 1;

        if(!empty($tessoal_id)){
            if(!empty($ragu)){
                $data_tes_soal['tessoal_ragu'] = $ragu;    
            }else{
                $data_tes_soal['tessoal_ragu'] = 0;
            }

            $this->cbt_tes_soal_model->update('tessoal_id', $tessoal_id, $data_tes_soal);
        }

        echo json_encode($data);
    }

    /**
     * Mendapatkan setiap soal dan jawaban dengan output json 
     */
    function get_soal_by_tessoal($tessoal_id=null, $tesuser_id=null){
        $data['data'] = 0;
        if(!empty($tessoal_id) AND !empty($tesuser_id)){
            $data_soal = $this->get_soal($tessoal_id, $tesuser_id);
            $data['data'] = $data_soal['data'];
            if(!empty($data_soal['tes_soal'])){
                $data['tes_soal'] = $data_soal['tes_soal'];
                $data['tes_ragu'] = $data_soal['tes_ragu'];
                $data['tes_soal_id'] = $data_soal['tes_soal_id'];
                $data['tes_soal_nomor'] = $data_soal['tes_soal_nomor'];
            }
        }

        echo json_encode($data);
    }

    /**
     * Mendapatkan daftar soal berupa tombol untuk memilih soal yang akan dikerjakan
     *
     * @param      <type>  $tes_id  The tes identifier
     *
     * @return     <type>  The daftar soal.
     */
    private function get_daftar_soal($tes_id=null){
        $data['tes_soal_jml'] = '';
        $data['tes_soal'] = '';
        $jml_soal = 0;
        $data_soal = '';
        if(!empty($tes_id)){
            $query_tes = $this->cbt_tes_user_model->get_by_user_tes_limit($this->user_id, $tes_id);

            if($query_tes->num_rows()>0){
                $query_tes = $query_tes->row();

                $query_soal = $this->cbt_tes_soal_model->get_by_testuser($query_tes->tesuser_id);
                $jml_soal = $query_soal->num_rows();

                if($jml_soal>0){
                    $query_soal = $query_soal->result();
                    foreach ($query_soal as $soal) {
                        // Jika jawaban sudah diisi
                        if(!empty($soal->tessoal_change_time)){
                            if($soal->tessoal_ragu==0){
                                // Jika soal tidak ragu-ragu
                                $data_soal = $data_soal.'<button id="btn-soal-'.$soal->tessoal_order.'" onclick="soal(\''.$soal->tessoal_id.'\')" class="btn btn-primary" style="margin-bottom: 5px;" title="Soal ke '.$soal->tessoal_order.'">'.$soal->tessoal_order.'</button>

                                ';
                            }else{
                                // Jika soal ragu-ragu
                                $data_soal = $data_soal.'<button id="btn-soal-'.$soal->tessoal_order.'" onclick="soal(\''.$soal->tessoal_id.'\')" class="btn btn-warning" style="margin-bottom: 5px;" title="Soal ke '.$soal->tessoal_order.'">'.$soal->tessoal_order.'</button>

                                ';
                            }
                        }else{
                            if($soal->tessoal_ragu==0){
                                // Jika soal tidak ragu-ragu
                                $data_soal = $data_soal.'<button id="btn-soal-'.$soal->tessoal_order.'" onclick="soal(\''.$soal->tessoal_id.'\')" class="btn btn-default" style="margin-bottom: 5px;" title="Soal ke '.$soal->tessoal_order.'">'.$soal->tessoal_order.'</button>

                                ';
                            }else{
                                // Jika soal ragu-ragu
                                $data_soal = $data_soal.'<button id="btn-soal-'.$soal->tessoal_order.'" onclick="soal(\''.$soal->tessoal_id.'\')" class="btn btn-warning" style="margin-bottom: 5px;" title="Soal ke '.$soal->tessoal_order.'">'.$soal->tessoal_order.'</button>

                                ';
                            }
                        }
                    }
                }
            }
        }
        $data['tes_soal_jml'] = $jml_soal;
        $data['tes_soal'] = $data_soal;

        return $data;
    }

    /**
     * Mendapatkan soal dan jawaban dalam bentuk html     *
     * @param      <type>  $tessoal_id  The tessoal identifier
     *
     * @return     string  The soal.
     */
    private function get_soal($tessoal_id=null, $tesuser_id=null){
        $data['tes_soal_id'] = '';
        $data['tes_soal'] = '';
        $data['data'] = 0;
        if(!empty($tessoal_id) AND !empty($tesuser_id)){
            // Mengecek apakah tes masih berjalan
            // mengambil tesuser_id terus mendapatkan datanya, dicek statusnya dan waktunya
            //if($this->cbt_tes_user_model->count_by_status_waktu($tesuser_id)->row()->hasil>0){
            //
            // revisi 2018-11-15
            // agar waktu mengambil dari waktu php, bukan mysql
            $waktuuser = date('Y-m-d H:i:s');
            if($this->cbt_tes_user_model->count_by_status_waktuuser($tesuser_id, $waktuuser)->row()->hasil>0){
                $data['data'] = 1;
                $query_soal = $this->cbt_tes_soal_model->get_by_tessoal_limit($tessoal_id, 1);
                $soal = '';
                if($query_soal->num_rows()>0){
                    $data['tes_soal_id'] = $tessoal_id;

                    $query_soal = $query_soal->row();

                    // Soal Ragu-ragu
                    $data['tes_ragu'] = $query_soal->tessoal_ragu;

                    // Mengupdate tessoal_display_time pada table test_log
                    $data_tes_soal['tessoal_display_time'] = date('Y-m-d H:i:s');
                    $this->cbt_tes_soal_model->update('tessoal_id', $tessoal_id, $data_tes_soal);
                    
                    // mengganti [baseurl] ke alamat sesungguhnya
                    $soal = $query_soal->soal_detail;
                    $soal = str_replace("[base_url]", base_url(), $soal);

                    // memberi file audio jika ada
                    if(!empty($query_soal->soal_audio)){
                        $audio_play = 0;
                        if($query_soal->soal_audio_play==1){
                            $audio_play = 1;
                        }
                        // jika batasan play audio masih bernilai 0
                        if($query_soal->tessoal_audio_play==0){
                            $posisi = $this->config->item('upload_path').'/topik_'.$query_soal->soal_topik_id;
                            $soal = $soal.'
                                <audio volume="1.0" id="audio-player" onended="audio_ended(\''.$audio_play.'\')">
                                  <source src="'.base_url().$posisi.'/'.$query_soal->soal_audio.'" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                                <div style="max-width:350px" id="audio-control">
                                    <div class="box">
                                        <div class="box-body">
                                            <input type="hidden" id="audio-player-status" value="0" />
                                            <input type="hidden" id="audio-player-update" value="0" />
                                            <a class="btn btn-app" onclick="audio(\''.$audio_play.'\')">
                                                <i class="fa fa-play" id="audio-player-judul-logo"></i> <span id="audio-player-judul">Play</span>
                                            </a>
                                            &nbsp;&nbsp;Klik Play untuk memutar Audio
                                        </div>
                                    </div>
                                </div>
                            ';
                        }
                    }

                    $soal = $soal.'<hr />';

                    $data['tes_soal_nomor'] = $query_soal->tessoal_order;

                    $soal = $soal.'<div class="form-group">';
                    if($query_soal->soal_tipe==1){
                        $query_jawaban = $this->cbt_tes_soal_jawaban_model->get_by_tessoal($query_soal->tessoal_id);
                        if($query_jawaban->num_rows()>0){
                            $query_jawaban = $query_jawaban->result();
                            foreach ($query_jawaban as $jawaban) {
                                // mengganti [baseurl] ke alamat sesungguhnya pada tag img / gambars
                                $temp_jawaban = $jawaban->jawaban_detail;
                                $temp_jawaban = str_replace("[base_url]", base_url(), $temp_jawaban);

                                if($jawaban->soaljawaban_selected==1){
                                    $soal = $soal.'<div class="radio"><label><input type="radio" onchange="jawab()" name="soal-jawaban" value="'.$jawaban->soaljawaban_jawaban_id.'" checked> '.$temp_jawaban.'</label></div>';
                                }else{
                                    $soal = $soal.'<div class="radio"><label><input type="radio" onchange="jawab()" name="soal-jawaban" value="'.$jawaban->soaljawaban_jawaban_id.'" > '.$temp_jawaban.'</label></div>';
                                }
                            }
                        }
                    }else if($query_soal->soal_tipe==2){
                        if(!empty($query_soal->tessoal_jawaban_text)){
                            $soal = $soal.'<textarea class="textarea" id="soal-jawaban" name="soal-jawaban" style="width: 100%; height: 150px; font-size: 13px; line-height: 25px; border: 1px solid #dddddd; padding: 10px;">'.$query_soal->tessoal_jawaban_text.'</textarea>
                                <button type="button" onclick="jawab()" class="btn btn-default" style="margin-bottom: 5px;" title="Simpan Jawaban">Simpan Jawaban</button>
                                ';
                        }else{
                            $soal = $soal.'<textarea class="textarea" id="soal-jawaban" name="soal-jawaban" style="width: 100%; height: 150px; font-size: 13px; line-height: 25px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                                <button type="button" onclick="jawab()" class="btn btn-default" style="margin-bottom: 5px;" title="Simpan Jawaban">Simpan Jawaban</button>
                                ';
                        }
                    }else if($query_soal->soal_tipe==3){
                        if(!empty($query_soal->tessoal_jawaban_text)){
                            $soal = $soal.'
                                <input type="text" class="form-control" style="max-width: 500px;" id="soal-jawaban" name="soal-jawaban" value="'.$query_soal->tessoal_jawaban_text.'" autocomplete="off" />
                                <br />
                                <button type="button" onclick="jawab()" class="btn btn-default" style="margin-bottom: 5px;" title="Simpan Jawaban">Simpan Jawaban</button>
                                ';
                        }else{
                            $soal = $soal.'
                                <input type="text" class="form-control" style="max-width: 500px;" id="soal-jawaban" name="soal-jawaban" autocomplete="off" />
                                <br />
                                <button type="button" onclick="jawab()" class="btn btn-default" style="margin-bottom: 5px;" title="Simpan Jawaban">Simpan Jawaban</button>
                                ';
                        }
                    }
                    $soal = $soal.'</div>';

                    $data['tes_soal'] = $soal;
                }
            }else{
                $data['data'] = 2;
            }
        }

        return $data;
    }

    function update_status_audio($tessoal_id=null){
        $data['data'] = 0;
        if(!empty($tessoal_id)){
            $data['data'] = 1;
            $data_tes['tessoal_audio_play'] = 1;
            $this->cbt_tes_soal_model->update('tessoal_id ', $tessoal_id, $data_tes);
            $data['pesan'] = 'Audio berhasil diputar';
        }
        echo json_encode($data);
    }
}