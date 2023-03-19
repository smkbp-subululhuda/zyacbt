<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ZYA CBT
* Achmad Lutfi
* achmdlutfi@gmail.com
* achmadlutfi.wordpress.com
*/
class Usermenu_model extends CI_Model{
    function save($data){
        $this->db->insert('user_menu', $data);
    }
    
    function delete($id){
        $this->db->where('id', $id)
                 ->delete('user_menu');
    }
    
    function update($data, $id){
        $this->db->where('id', $id)
                 ->update('user_menu', $data);
    }
    
    function cek_kode_menu($kode_menu){
        $this->db->select('COUNT(*) AS hasil')
                 ->where('kode_menu', $kode_menu)
                 ->from('user_menu');
        return $this->db->get();
    }
    
    function get_all_menu($start, $rows, $search){
		$sql = 'SELECT * FROM user_menu WHERE user_menu.kode_menu LIKE "%'.$search.'%" OR user_menu.nama_menu LIKE "%'.$search.'%" ORDER BY tipe ASC, nama_menu ASC LIMIT '.$start.','.$rows;
		return $this->db->query($sql);
	}
    
    function get_all_menu_count($search){
		$sql = 'SELECT COUNT(*) AS hasil FROM user_menu WHERE user_menu.kode_menu LIKE "%'.$search.'%" OR user_menu.nama_menu LIKE "%'.$search.'%"';
		return $this->db->query($sql);
	}
    
    function get_menu_by_id($id){
        $this->db->where('id', $id)
                 ->from('user_menu')
                 ->limit(1);
        return $this->db->get();
    }
    
    function get_menu_by_tipe($tipe){
        $this->db->where('tipe', $tipe)
                 ->from('user_menu')
                 ->order_by('nama_menu', 'asc');
        return $this->db->get();
    }
    
    function get_menu(){
        $this->db->from('user_menu')
                 ->order_by('nama_menu', 'asc');
        return $this->db->get();
    }
    
    function get_menu_by_parent($parent){
        $this->db->where('parent', $parent)
                 ->from('user_menu')
                 ->order_by('nama_menu', 'asc');
        return $this->db->get();
    }
}