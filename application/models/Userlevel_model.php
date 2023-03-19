<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ZYA CBT
* Achmad Lutfi
* achmdlutfi@gmail.com
* achmadlutfi.wordpress.com
*/
class Userlevel_model extends CI_Model{
    function save($data){
        $this->db->insert('user_level', $data);
    }
    
    function delete($level){
        $this->db->where('level', $level)
                 ->delete('user_level');
    }
    
    function update($data, $id){
        $this->db->where('id', $id)
                 ->update('user_level', $data);
    }
    
    function cek_level($level){
        $this->db->select('COUNT(*) AS hasil')
                 ->where('level', $level)
                 ->from('user_level');
        return $this->db->get();
    }
    
    function get_level(){
        $this->db->from('user_level')
                 ->order_by('level', 'ASC');
        return $this->db->get();
    }
    
    function get_level_by_id($id){
        $this->db->where('id', $id)
                 ->from('user_level')
                 ->limit(1);
        return $this->db->get();
    }
    
    function get_all_level($start, $rows, $search){
        $this->db->like('level', $search)
                 ->or_like('keterangan', $search)
                 ->from('user_level')
                 ->limit($rows, $start);
        return $this->db->get();
    }
    
    function get_all_level_count($search){
        $this->db->select('COUNT(*) AS hasil')
                 ->like('level', $search)
                 ->or_like('keterangan', $search)
                 ->from('user_level');
        return $this->db->get();
    }
    
    function get_menu_by_level($level){
        $this->db->select('user_menu.*, user_akses.add, user_akses.edit')
                 ->from('user_akses')
                 ->join('user_menu', 'user_akses.kode_menu = user_menu.kode_menu')
                 ->join('user_level', 'user_akses.level = user_level.level')
                 ->where('user_level.level', $level)
                 ->order_by('user_menu.nama_menu', 'ASC');
        return $this->db->get();
    }
}