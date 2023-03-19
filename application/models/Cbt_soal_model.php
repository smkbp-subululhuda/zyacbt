<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cbt_soal_model extends CI_Model{
	public $table = 'cbt_soal';
	
	function __construct(){
        parent::__construct();
    }
	
    function save($data){
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
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
                 ->order_by('soal_id', 'ASC');
        return $this->db->get();
    }
	
	function get_by_kolom($kolom, $isi){
        $this->db->where($kolom, $isi)
                 ->from($this->table);
        return $this->db->get();
    }

    function get_by_topik_tipe_kesulitan_select_limit($topik, $tipe, $kesulitan, $select, $limit){
        $tipe_sql = '';
        if($tipe!=0){
            $tipe_sql = ' AND soal_tipe="'.$tipe.'"';
        }
        $sql = 'SELECT '.$select.' FROM cbt_soal WHERE soal_topik_id="'.$topik.'" AND soal_difficulty="'.$kesulitan.'" '.$tipe_sql.' ORDER BY RAND() LIMIT '.$limit;


        return $this->db->query($sql);
    }

    function get_by_topik_tipe_kesulitan_select_limit_tanpa_acak($topik, $tipe, $kesulitan, $select, $limit){
        $tipe_sql = '';
        if($tipe!=0){
            $tipe_sql = ' AND soal_tipe="'.$tipe.'"';
        }
        $sql = 'SELECT '.$select.' FROM cbt_soal WHERE soal_topik_id="'.$topik.'" AND soal_difficulty="'.$kesulitan.'" '.$tipe_sql.' ORDER BY soal_id ASC LIMIT '.$limit;


        return $this->db->query($sql);
    }
	
	function get_by_kolom_limit($kolom, $isi, $limit){
        $this->db->where($kolom, $isi)
                 ->from($this->table)
				 ->limit($limit);
        return $this->db->get();
    }
	
	function get_datatable($start, $rows, $kolom, $isi, $topik){
		$this->db->where('('.$kolom.' LIKE "%'.$isi.'%" AND soal_topik_id="'.$topik.'")')
                 ->from($this->table)
				 ->order_by('soal_id', 'ASC')
                 ->limit($rows, $start);
        return $this->db->get();
	}
    
    function get_datatable_count($kolom, $isi, $topik){
		$this->db->select('COUNT(*) AS hasil')
                 ->where('('.$kolom.' LIKE "%'.$isi.'%" AND soal_topik_id="'.$topik.'")')
                 ->from($this->table);
        return $this->db->get();
	}
}