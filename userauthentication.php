<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// User Login, User logOut,  these functions are in the class
class UserAuthentication extends MY_Controller
{
	// User authontication 
	public function userLogin()
	{
		if($this->isLoggedIn()==1)
		{
			// check user type and load each user's page
			// eg:Admin for admin page			
			$this->defaultPageLoadForUser();

		}								// Chek user name & password empty
		else if(isset($_POST['user_input_username']) && isset($_POST['user_input_password']))
		{
			$userName = $_POST['user_input_username'];
			$password = $_POST['user_input_password'];
			
						// load model
			$this->load->model('user_model');
			$this->load->model('userrole_model');
			$this->load->model('role_model');

								// check user in data base
			$loginDetail = $this->user_model->user_name_password_authonticate($userName,$password);

			if($loginDetail == 1)
			{
								// get basic data for current user
				$userDetails = $this->user_model->get_user_sata($userName);
								// user valid create session for current user
				
							//get the role id for the current user
				$roleDetails = $this->userrole_model->get_role_id_for_specific_customer($userDetails['user_id']);
				
							//get the role name for the current user
				$roleName    = $this->role_model->get_role_name_for_specific_customer($roleDetails['role_id']);
				
				$setUserCookies = array(					
					'userId'  	  => $userDetails['user_id'],
					'userName'    => $userDetails['email'],
					'firstName'	  => $userDetails['first_name'],
					'lastName'	  => $userDetails['last_name'],
					'email'       => $userDetails['email'],
					'userType'	  => $roleName['role_name'],					
					'loginStatus' => TRUE
				);

								// create sessin using basic data
				$this->session->set_userdata($setUserCookies);

								// check user type and load each user's page
								// eg:Admin for admin page
				$this->defaultPageLoadForUser();
			}
								//	invalid user name or password redirect to login page
			else
			{
				$data['title']         = " - Login";
				$data['errorMsgLogin'] = "User name and password do not match.";

								// check user type and load each user's page
								// eg:Admin for admin page
				
				$data['title'] = ucfirst('login'); // Capitalize the first letter	
				$this->load->view('backend/template/be_header', $data);				
				$this->load->view('backend/pages/login', $data);
				$this->load->view('backend/template/be_footer', $data);

			}			//if($loginDetail == 1)
		}
		else
		{			
			// echo "not logged user";
			//  page load for each user
			$this->defaultPageLoadForUser();
			
		}

	}			//public function userLogin()

	// destroy all sessions and redirect to login page
	public function userLogOut()
	{
		$this->session->sess_destroy();  // Session destroy

		// redirect to login page		
		$data['log_out'] = "You've signed out. See you again soon!"; // Capitalize the first letter	
		$this->load->view('backend/template/be_header', $data);				
		$this->load->view('backend/pages/login', $data);
		$this->load->view('backend/template/be_footer', $data);
		
	}			//public function userLogOut()


public function userForgotPassword()
	{
		 if(isset($_POST['user_input_email']))
		{
			$userEmail = $_POST['user_input_email'];// get email input address


			// load model
			$this->load->model('user_model');

			$emailDetail = $this->user_model->emailAuthonticate($userEmail);


			if($emailDetail == 1)
			{

				$get_user_details = $this->user_model->get_customer_name_by_email($userEmail);

				foreach ($get_user_details as $key => $get_user_details)
				{
				
				$customer_name = $get_user_details->user_title.' '.$get_user_details->first_name.' '.$get_user_details->last_name;
				$password      = $get_user_details->user_password;
				}

				$toEmail	 	= $userEmail;
				$subject 		= 'Login details of Salon dilu';
				$emailContent	= "Dear ".$customer_name.",<br><br><br>Your current login Password is: ".$password."<br><br><br> Please click on below link to login.<br><br>http://salondilu.webuda.com/index.php/backend/login<br><br><br>Thank you,<br><br>Team Salon Dilu";

				$this->create_email($toEmail,$subject,$emailContent);

				$data['sucesslogin'] = " Your password is sent to your emaill address sucessfully";

				$data['title'] = ucfirst('login'); // Capitalize the first letter	
				$this->load->view('backend/template/be_header', $data);				
				$this->load->view('backend/pages/login', $data);
				$this->load->view('backend/template/be_footer', $data);

			}
			
			else
			{
				$data['title']         = " - Login";
				$data['errorMsgLogin'] = "Your email address incorrect";

								// check user type and load each user's page
								// eg:Admin for admin page
				
				$data['title'] = ucfirst('login'); // Capitalize the first letter	
				$this->load->view('backend/template/be_header', $data);				
				$this->load->view('backend/pages/forgotpassword', $data);
				$this->load->view('backend/template/be_footer', $data);

			}	
			
		
		}

	}


	
}			//class UserAuthentication extends MY_Controller