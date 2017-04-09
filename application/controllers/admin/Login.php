<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('common');
		if($this->common->is_logged_in()){
			redirect('admin/home');
		}
	}

	public function index()
	{
		$this->load->view('admin/login');
	}

	public function checkLoggedIn(){
		if($this->input->post('email') and $this->input->post('password')){
		 
		 
		 /////// Check user provide information //////////
			if($this->simpleloginsecure->login($this->input->post('email'), $this->input->post('password'))){
			
			/////// Check JavaSrcript is enable //////////	
				if($this->input->post('javascript_enable') == 'no' ){
				
				/////// Redirect to admin dashboard page if JavaScript is not enable//////////	
					redirect('home');
				} else {
				
				 /////// Return success response to AJAX request if JavaScript is enableb //////////		
					echo 'success';
				}
			} else {
				/////// Check JavaSrcript is enable //////////	
				if($this->input->post('javascript_enable') == 'no' ){
				
					/////// Redirect to login page if JavaScript is not enable and user provide wrong information //////////	
					$this->session->set_flashdata('errorMessage', LOGIN_ERROR);	
					redirect('login');
				} else {
				
				/////// Return error response to AJAX request if JavaScript is enableb and user provide wrong information //////////	
					echo LOGIN_ERROR;
			  }	
			}
			
		} else {
			/////// Check JavaSrcript is enable //////////	
			if($this->input->post('javascript_enable') == 'no' ){
					
					/////// Redirect to login page if JavaScript is not enable and user provide no information related to login  //////////	
					$this->session->set_flashdata('errorMessage', LOGIN_NOTIFY);	
					redirect('login');
				} else {
				
				/////// Return error response to AJAX request if JavaScript is enableb and user provide no information related to login  //////////	
				echo LOGIN_NOTIFY;
			}
		}
		
	}
}
