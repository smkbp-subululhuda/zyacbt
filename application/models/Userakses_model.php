<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ZYA CBT
* Achmad Lutfi
* achmdlutfi@gmail.com
* achmadlutfi.wordpress.com
*/
class Userakses_model extends CI_Model{
    function save($data){
        $this->db->insert('user_akses', $data);
    }
    
    function delete($id){
        $this->db->where('id', $id)
                 ->delete('user_akses');
    }
    
    function delete_by_level($level){
        $this->db->where('level', $level)
                 ->delete('user_akses');
    }
    
    function update($data, $id){
        $this->db->where('id', $id)
                 ->update('user_akses', $data);
    }
    
    function get_akses_by_level($level){
        $this->db->where('level', $level)
                 ->from('user_akses');
        return $this->db->get();
    }
    
    function get_akses_by_level_kodemenu($level, $kode_menu){
        $this->db->where('level', $level)
                 ->where('kode_menu', $kode_menu)
                 ->from('user_akses')
                 ->limit(1);
        return $this->db->get();
    }
}