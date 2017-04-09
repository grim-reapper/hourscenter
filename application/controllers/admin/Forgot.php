<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forgot extends CI_Controller {

	public function __construct()
	{	
		parent::__construct();
		$this->load->library('common');
		$this->load->model('admin_model');
		
	}
	
	/////// Load login form//////////
	public function index()
	{
		/////// Redirect to admin login //////////	
		redirect('admin/login');
	}
	
	
	public function forgotpassword(){
			
		
		if($this->input->post('email_forgot')){
		 
		 /////// Check user provide information //////////
			if($this->common->checkEmailAddress($this->input->post('email_forgot'))){
				$this->load->model('email_model');
			
				$email = $this->input->post('email_forgot');
				$this->db->select('*');
				$this->db->from('users');
				$this->db->where('user_email', $email);
				$query = $this->db->get();
				$result_users = $query->row();

				$first_name = $result_users->first_name;
				$last_name = $result_users->last_name;


				$user_complete_name = $first_name.' '.$last_name;
				$token = $email . time() . rand(0, 5000000);
				$token = md5($token);


				$this->db->set('token', $token);
				$this->db->set('token_active', 'y');
				$this->db->where('user_email', $email);
				$this->db->update('users');
                $reset_link = site_url() . 'admin/forgot/reset/' . $token;
                $email_data = [];
				$email_data['from_email'] = 'support@hourscenter.com';
				$email_data['from_name'] = 'HOURSCENTER';
				$email_data['to_email'] = "$user_complete_name <$email>";
                $email_data['subject'] = 'Reset Your Password';
				$email_data['message'] = $this->load->view('email_templates/password_reset/password_reset','',true);
                $email_data['message'] = str_replace('{{RESETLINK}}',$reset_link,$email_data['message']);
                $email_data['message'] = str_replace('{{FNAME}}',$first_name,$email_data['message']);
                $email_data['message'] = str_replace('{{LNAME}}',$last_name,$email_data['message']);
			/////// Send email for reset password //////////
				$this->email_model->send_email($email_data);
				
			/////// Check JavaSrcript is enable //////////	
				if($this->input->post('javascript_enable_forgot') == 'no' ){
				$this->session->set_flashdata('errorMessage', FORGOT_SUCCUSS);
				/////// Redirect to admin dashboard page if JavaScript is not enable//////////	
					redirect('admin/login');
				} else {
				
				 /////// Return success response to AJAX request if JavaScript is enableb //////////		
					echo FORGOT_SUCCUSS;
				}
			
			/**  by akhtra   **/
			} else {
				/////// Check JavaSrcript is enable //////////	
				if($this->input->post('javascript_enable_forgot') == 'no' ){
				
					/////// Redirect to login page if JavaScript is not enable and user provide wrong information //////////	
					$this->session->set_flashdata('errorMessage', FORGOT_ERROR);	
					redirect('admin/login');
				} else {
				
				/////// Return error response to AJAX request if JavaScript is enableb and user provide wrong information //////////	
					echo FORGOT_ERROR;
			  }	
			}
			
			/** end by akhtar **/
			
		} else {
			/////// Check JavaSrcript is enable //////////	
			if($this->input->post('javascript_enable_forgot') == 'no' ){
					
					/////// Redirect to login page if JavaScript is not enable and user provide no information related to login  //////////	
					$this->session->set_flashdata('errorMessage', FORGOT_NOTIFY);	
					redirect('admin/login');
				} else {
				
				/////// Return error response to AJAX request if JavaScript is enableb and user provide no information related to login  //////////	
				echo FORGOT_NOTIFY;
			}
		}
		
	
	
	
		
	}
	
	/////// Check user provide information  //////////
	public function reset(){
		 
		if($this->uri->segment(4)){
		 
		 /////// Check user provide information //////////
			if($this->admin_model->authenticate_recovery_request($this->uri->segment(4))){
				/////// Display recovery form ///////////////
				$this->load->view('admin/password_recovery');
			
			} else {
			$this->session->set_flashdata('forgot_request_error', 'yes');
			$this->session->set_flashdata('errorMessage', FORGOT_AUTHENTICATE_ERROR);
			redirect('admin/login');
			
			}
			
		} else {
			$this->session->set_flashdata('forgot_request_error', 'yes');
			$this->session->set_flashdata('errorMessage', FORGOT_REQUEST_ERROR);
			redirect('admin/login');
			
		}
		
	}
	
	
	/////// Check user provide information  //////////
	public function update_password(){
		 
		
		if($this->input->post('newpassword') and $this->input->post('confirmpassword')){
		
		
		
		 /////// Check user provide information //////////
			if($this->input->post('newpassword') == $this->input->post('confirmpassword')){
			
			
			if(preg_match("/^.*(?=.{6,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).*$/", $this->input->post('newpassword')) === 0){			
				
				/////// Check JavaSrcript is enable //////////	
				if($this->input->post('javascript_enable_forgot') == 'no' ){
				$this->session->set_flashdata('succesMessage', RECOVERY_ERROR_NOTIFY);
				/////// Redirect to login dashboard page if JavaScript is not enable//////////	
					redirect('admin/login');
				} else {
				
				 /////// Return success response to AJAX request if JavaScript is enableb //////////		
					echo RECOVERY_ERROR_NOTIFY;
				}
				
			 
			
			}else {
			
			
			//////// Create hash password ///////////
			$user_pass_hashed = $this->simpleloginsecure->create_hash($this->input->post('newpassword'));
			
			/////// Update last login information //////////
				$this->admin_model->update_password($user_pass_hashed);				
			
			/////// Check JavaSrcript is enable //////////	
				if($this->input->post('javascript_enable_forgot') == 'no' ){
				$this->session->set_flashdata('succesMessage', PASSWORD_RECOVERY_SUCCESS);
				/////// Redirect to login dashboard page if JavaScript is not enable//////////	
					redirect('admin/login');
				} else {
				 /////// Return success response to AJAX request if JavaScript is enableb //////////		
					echo PASSWORD_RECOVERY_SUCCESS;
				}
			 }
				
				
			} else {
				/////// Check JavaSrcript is enable //////////	
				if($this->input->post('javascript_enable_forgot') == 'no' ){
				
					/////// Redirect to Reset Password page if JavaScript is not enable and user provide wrong information //////////	
					$this->session->set_flashdata('errorMessage', RECOVERY_MISMATCH_NOTIFY);	
					redirect('admin/forgot/reset/'.$this->input->post('tokenkey'));
				} else {
				
				/////// Return error response to AJAX request if JavaScript is enableb and user provide wrong information //////////	
					echo RECOVERY_MISMATCH_NOTIFY;
			  }	
			}
			
			 
		} else {
			/////// Check JavaSrcript is enable //////////	
			if($this->input->post('javascript_enable_forgot') == 'no' ){
					
					/////// Redirect to Reset Password page if JavaScript is not enable and user provide no information related to recover password  //////////	
					$this->session->set_flashdata('errorMessage', RECOVERY_NOTIFY);	
					redirect('admin/forgot/reset/'.$this->input->post('tokenkey'));
				} else {
				
				/////// Return error response to AJAX request if JavaScript is enableb and user provide no information related to recover password  //////////	
				echo RECOVERY_NOTIFY;
			}
		}
		
	}

}