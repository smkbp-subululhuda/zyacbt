<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Users_model extends CI_Model{	
	function __construct(){
		parent::__construct();
	}
    
    function save($data){
        $this->db->insert('user', $data);
    }
    
    function delete($username){
        $this->db->where('username', $username)
                 ->delete('user');
    }
    
    function update($data, $username){
        $this->db->where('username', $username)
                 ->update('user', $data);
    }
    
    function get_user_by_id($id){
        $this->db->where('id', $id)
                 ->from('user')
                 ->limit(1);
        return $this->db->get();
    }

    function get_user_by_username($username){
        $this->db->where('username', $username)
                 ->from('user')
                 ->limit(1);
        return $this->db->get();
    }
	
	function get_login_info($username){
		$this->db->where('username',$username);
		$this->db->limit(1);
		$query = $this->db->get('user');
		return ($query->num_rows() > 0) ? $query->row() : FALSE;
	}
    
    /**
     * Change Password
     * 
     */ 
    function change_password($username, $password){
        $this->db->where('username', $username);
        $this->db->update('user', array('password' => sha1($password)));
	}
    
    function get_user_count($username, $password){
        $this->db->where('username', $username)
                 ->where('password', sha1($password))
                 ->from('user');
        return $this->db->count_all_results();	
	}
    
    function get_user_count_by_level($level){
        $this->db->select('COUNT(*) AS hasil')
                 ->where('level', $level)
                 ->from('user');
        return $this->db->get();
    }
    
    /*
	 * mendapatkan hak akses suatu menu
	 */
	function cek_akses($kode_menu, $level){
		$sql='SELECT COUNT(*) AS hasil FROM user_akses WHERE user_akses.kode_menu="'.$kode_menu.'" AND user_akses.level="'.$level.'"';
		$hasil=$this->db->query($sql)->row()->hasil;

		return $hasil;
	}
    
    /*
	 * mendapatkan hak akses crud menu
	 */
	function cek_akses_crud($kode_menu, $level, $tipe){
        if($tipe==0){
            $sql='SELECT COUNT(*) AS hasil FROM user_akses WHERE user_akses.kode_menu="'.$kode_menu.'" AND user_akses.level="'.$level.'" AND user_akses.add=1';
        }else{
            $sql='SELECT COUNT(*) AS hasil FROM user_akses WHERE user_akses.kode_menu="'.$kode_menu.'" AND user_akses.level="'.$level.'" AND user_akses.edit=1';
        }
		$hasil=$this->db->query($sql)->row()->hasil;

		return $hasil;
	}
    
    function get_all_user($start, $rows, $search){
        $this->db->where('(username LIKE "%'.$search.'%" OR nama LIKE "%'.$search.'%")')
                 ->where('username !=','admin')
                 ->from('user')
                 ->limit($rows, $start);    
        
        return $this->db->get();
    }
    
    function get_all_user_count($search){
        $this->db->select('COUNT(*) AS hasil')
                 ->like('user.username', $search)
                 ->or_like('user.nama', $search)
                 ->where('user.username !=','admin')
                 ->from('user');
        return $this->db->get();
    }
    
    function get_parent_menu($child_menu){
        $sql = 'SELECT `user_menu`.`parent` FROM user_menu WHERE `user_menu`.`kode_menu`="'.$child_menu.'"';
        $query = $this->db->query($sql);
        if($query->num_rows()>0){
            $hasil = $query->row()->parent;
        }else{
            $hasil = 'KOSONG';
        }
        return $hasil;
        
    }
    
    function get_menu_detail($kode_menu){
        $sql = 'SELECT `user_menu`.* FROM user_menu WHERE `user_menu`.`kode_menu`="'.$kode_menu.'" LIMIT 1';
        return $this->db->query($sql);
    }
    
    /**
     * Mendapatkan menu dashboard secara dynamic 
     */ 
    function get_menu($kode_menu, $level){
        $sql = 'SELECT user_menu.* FROM `user_akses` INNER JOIN `user_level` ON (`user_akses`.`level` = `user_level`.`level`) 
            INNER JOIN `user_menu` ON (`user_akses`.`kode_menu` = `user_menu`.`kode_menu`) WHERE user_akses.`level`="'.$level.'"
            GROUP BY `user_menu`.`parent` ORDER BY user_menu.parent ASC';
        $result=$this->db->query($sql);
        $parent_kode_menu = $this->get_parent_menu($kode_menu);
        
        $menu = '';
        
        if($result->num_rows()>0){
            foreach ($result->result() as $temp){
                if(empty($temp->parent)){
                    $parent = $this->get_menu_detail($temp->kode_menu)->row();
                
                    $parent_active='';
                    if($parent->kode_menu==$parent_kode_menu){
    					$parent_active=' active ';
    				}
                }else{
                    $parent = $this->get_menu_detail($temp->parent)->row();
                
                    $parent_active='';
                    if($parent->kode_menu==$parent_kode_menu){
    					$parent_active=' active ';
    				}
                }
                
                $sql_child = 'SELECT user_menu.* FROM `user_akses` INNER JOIN `user_level` ON (`user_akses`.`level` = `user_level`.`level`) 
                    INNER JOIN `user_menu` ON (`user_akses`.`kode_menu` = `user_menu`.`kode_menu`) WHERE user_akses.`level`="'.$level.'" 
                    AND user_menu.`tipe`=1 AND user_menu.parent="'.$parent->kode_menu.'" ORDER BY user_menu.`urutan` ASC';
                $result_child = $this->db->query($sql_child);
                
                $menu_child = '';
                $menu_child_count = 0;
                if($result_child->num_rows()>0){
                    $menu_child = $menu_child.'<ul class="treeview-menu">';
                    foreach ($result_child->result() as $child){
                        $child_active='';
                        if($kode_menu==$child->kode_menu){
                            $child_active='active';
                        }
                        $menu_child = $menu_child.'
                            <li class="'.$child_active.'"><a href="'.site_url().'/'.$child->url.'"><i class="fa fa-circle-o"></i> '.$child->nama_menu.'</a></li>
                        ';
                        
                        $menu_child_count++;
                    }
                    $menu_child = $menu_child.'</ul>';
                }
                
                $menu = $menu.'
                    <li class="treeview '.$parent_active.'">
                        <a href="'.site_url().'/'.$parent->url.'">
        					<i class="'.$parent->icon.'"></i>
        					<span>'.$parent->nama_menu.'</span>';
                if($menu_child_count>0){
                    $menu = $menu.'<i class="fa fa-angle-left pull-right"></i>';
                }
                
                $menu = $menu.'</a>';
                
                $menu = $menu.'
                    '.$menu_child;
                
                $menu = $menu.'</li>';
            }
        }
        
        return $menu;
    }
}