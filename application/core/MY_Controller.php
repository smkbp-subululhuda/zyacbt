<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends CI_Controller{
	function __construct(){
		parent::__construct();
	}
}

class Member_Controller extends CI_Controller{
	function __construct(){
		parent::__construct();
		if(!$this->access->is_login()){
			// diredirect ke bagian login
			redirect('manager/welcome');
		}
	}
	
	function is_login(){
		return $this->access->is_login();
	}
	
	function cek_akses($kode_menu){
		if(!$this->access->cek_akses($kode_menu)){
			redirect('manager');
		}
	}
    
    function cek_akses_crud($kode_menu, $tipe){
		if(!$this->access->cek_akses_crud($kode_menu, $tipe)){
			redirect('manager');
		}
	}
}

class Tes_Controller extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->library('access_tes');
		
		if(!$this->access_tes->is_login()){
			// diredirect ke bagian login
			redirect('welcome');
		}
	}
	
	function is_login(){
		return $this->access_tes->is_login();
	}
}