<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {

	public function __construct()
	{	
		parent::__construct();						
	}

	/////// Logout function /////////
	public function index()
	{
		// Unset user related seasions //	
		$this->simpleloginsecure->logout();
		
		// Redirect to login page  //	
		redirect('/admin/login');
		
	}
	
}

?>