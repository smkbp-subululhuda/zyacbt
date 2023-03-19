<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cbt_jawaban_model extends CI_Model{
	public $table = 'cbt_jawaban';
	
	function __construct(){
        parent::__construct();
    }
	
    function save($data){
        $this->db->insert($this->table, $data);
    }
    
    function delete($kolom, $isi){
        $this->db->where($kolom, $isi)
                 ->delete($this->table);
    }
    
    function update($kolom, $isi, $data){
        $this->db->where($kolom, $isi)
                 ->update($this->table, $data);
    }
    
    function count_by_kolom($kolom, $isi){
        $this->db->select('COUNT(*) AS hasil')
                 ->where($kolom, $isi)
                 ->from($this->table);
        return $this->db->get();
    }
	
    function get_all(){
        $this->db->from($this->table)
                 ->order_by('jawaban_soal_id', 'ASC');
        return $this->db->get();
    }

	function get_by_kolom($kolom, $isi){
        $this->db->where($kolom, $isi)
                 ->from($this->table);
        return $this->db->get();
    }
    
    /**
     * mendapatkan jawaban berdasarkan soal dengan diacak
     */
    function get_by_soal_limit($soal, $limit){
        $sql = '(SELECT jawaban_id FROM cbt_jawaban WHERE cbt_jawaban.jawaban_soal_id='.$soal.' AND cbt_jawaban.jawaban_benar=1 LIMIT 1) UNION (SELECT jawaban_id FROM cbt_jawaban WHERE cbt_jawaban.jawaban_soal_id='.$soal.' AND cbt_jawaban.jawaban_benar=0 LIMIT '.($limit-1).') ORDER BY RAND();';

        return $this->db->query($sql);
    }

    /**
     * Mendapatkan jawaban tanpa diacak
     *
     * @param      <type>  $soal   The soal
     *
     * @return     <type>  The by soal tanpa acak.
     */
    function get_by_soal_tanpa_acak($soal){
        $this->db->select('jawaban_id')
                 ->where('jawaban_soal_id', $soal)
                 ->from($this->table)
                 ->order_by('jawaban_id', 'ASC');

        return $this->db->get();
    }

	function get_by_kolom_limit($kolom, $isi, $limit){
        $this->db->where($kolom, $isi)
                 ->from($this->table)
				 ->limit($limit);
        return $this->db->get();
    }

    function get_by_soal($soal){
        $this->db->where('jawaban_soal_id', $soal)
                 ->order_by('jawaban_id', 'ASC')
                 ->from($this->table);
        return $this->db->get();
    }
	
	function get_datatable($start, $rows, $kolom, $isi, $soal){
		$this->db->where('('.$kolom.' LIKE "%'.$isi.'%" AND jawaban_soal_id="'.$soal.'")')
                 ->from($this->table)
				 ->order_by('jawaban_id', 'ASC')
                 ->limit($rows, $start);
        return $this->db->get();
	}
    
    function get_datatable_count($kolom, $isi, $soal){
		$this->db->select('COUNT(*) AS hasil')
                 ->where('('.$kolom.' LIKE "%'.$isi.'%" AND jawaban_soal_id="'.$soal.'")')
                 ->from($this->table);
        return $this->db->get();
	}
}