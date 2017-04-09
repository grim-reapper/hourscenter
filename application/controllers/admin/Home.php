<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('common');
		if(!$this->common->is_logged_in()){
			redirect('admin/login');
		}
	}

	public function index()
	{
		$this->template->write_view('content','admin/index');
		$this->template->render();
	}
}
