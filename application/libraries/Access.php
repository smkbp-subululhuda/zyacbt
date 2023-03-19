<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* ZYA CBT
* Achmad Lutfi
* achmdlutfi@gmail.com
* achmadlutfi.wordpress.com
*/
class Access{
	public $user;
	
	function __construct(){
		$this->CI =& get_instance();
		
		$this->CI->load->helper('cookie');
		$this->CI->load->model('users_model');
		
		$this->users_model =& $this->CI->users_model;
	}
	
	
	/**
	 * proses login
	 * 0 = username tak ada
	 * 1 = sukses
	 * 2 = password salah
	 * @param unknown_type $username
	 * @param unknown_type $password
	 * @return boolean
	 */
	function login($username, $password){
		$result = $this->users_model->get_login_info($username);
		if($result){
			$password = sha1($password);
			if($password === $result->password){
				$this->CI->session->set_userdata('cbt_user_id',$result->username);
                $this->CI->session->set_userdata('cbt_nama',$result->nama);
                $this->CI->session->set_userdata('cbt_level',$result->level);
                $this->CI->session->set_userdata('cbt_opsi1',$result->opsi1);
                $this->CI->session->set_userdata('cbt_opsi2',$result->opsi2);
				return 1;
			}else{
				return 2;
			}
		}
		return 0;
	}
	
	/**
	 * cek apakah sudah login
	 * @return boolean
	 */
	function is_login(){
		return (($this->CI->session->userdata('cbt_user_id')) ? TRUE : FALSE);
	}
	
	/*
	 * Cek akses kode_menu
	 */
	function cek_akses($kode_menu){
		$level=$this->CI->session->userdata('cbt_level');
		if($this->users_model->cek_akses($kode_menu, $level)>0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
    
    /**
     * cek akses crud
     * tipe : 0 = add
     * tipe : 1 = edit
     */     
    function cek_akses_crud($kode_menu, $tipe){
        $level=$this->CI->session->userdata('cbt_level');
        
        if($this->users_model->cek_akses_crud($kode_menu, $level, $tipe)>0){
			return TRUE;
		}else{
			return FALSE;
		}
    }
	
	function get_username(){
		return $this->CI->session->userdata('cbt_user_id');
	}
    
    function get_nama(){
		return $this->CI->session->userdata('cbt_nama');
	}
	
	function get_level(){
		return $this->CI->session->userdata('cbt_level');
	}
    
    function get_opsi1(){
		return $this->CI->session->userdata('cbt_opsi1');
	}
    
    function get_opsi2(){
		return $this->CI->session->userdata('cbt_opsi2');
	}
	
	/**
	 * logout
	 */
	function logout(){
		$this->CI->session->unset_userdata('cbt_user_id');
		$this->CI->session->unset_userdata('cbt_nama');
		$this->CI->session->unset_userdata('cbt_level');
		$this->CI->session->unset_userdata('cbt_opsi1');
		$this->CI->session->unset_userdata('cbt_opsi2');
	}
}