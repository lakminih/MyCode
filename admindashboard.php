<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Admindashboard extends MY_Controller
{
	//default function to run.
	public function index($page = 'admindashboard')
	{	
		if ( !file_exists('application/views/backend/pages/'.$page.'.php'))
		{
			// Whoops, we don't have a page for that!
			show_404();

		}			//if ( ! file_exists('application/views/'.$page.'.php'))
		else
		{
			if ($this->isLoggedIn() == 1)		//if the user logged in?
			{
				//yes, then redirect to dashboard
				$data['pending_appointments']                  = $this->get_all_pending_appointments();			//get all pending appointments to display
				
				$data['get_all_customer']                      = $this->get_all_customer_details();			//get all customer details

				$data['get_all_subcategory_reservations']      = $this->get_all_subcategory_reservations();			//get all sub category reservations

				$data['get_all_reservations_for_current']      = $this->get_all_reservations_for_current();			//get all reservations for current date

				$data['services_subcategory']                  = $this->get_all_service_sub_category();			//get all service sub category

				$data['service_vice_appointments']             = $this->get_service_vice_appointments();			//get service vice appointments details

				$data['get_all_source_details']                = $this->get_all_source_details();			//get all source details

				$data['get_all_income_per_year']               = $this->get_all_income_per_year();			//get all incomes month wise

				$data['employee_works_on_calendar']            = $this->get_all_empolees_of_salon_to_calendar();			//get all empolees of salon to calendar

				$data['title'] = ucfirst($page); // Capitalize the first letter				
				$this->load->view('backend/template/be_header', $data);				
				$this->load->view('backend/pages/'.$page, $data);
				$this->load->view('backend/template/be_footer', $data);
			}
			else
			{
				//Oops no, then no direct access allowed
				$data['title'] = ucfirst('login'); // Capitalize the first letter				
				$this->load->view('backend/template/be_header', $data);				
				$this->load->view('backend/pages/login', $data);
				$this->load->view('backend/template/be_footer', $data);
			}

		}			//if($this->isLoggedIn())

	}			//public function index($page = 'login')

	//function to get all the pending appointments to get the approval
	public function get_all_pending_appointments()
	{
		$this->load->model('reservationrequest_model');

		$query_pending_reservation_details = $this->reservationrequest_model->get_all_pending_events();

		if (!$query_pending_reservation_details == 0)
		{
			return $query_pending_reservation_details;
		}

	}			//public function get_all_pending_appointments()	

	//function to get all the customer details
	public function get_all_customer_details()
	{
		$this->load->model('user_model');

		$query_customer_details = $this->user_model->get_all_record();

		if (!$query_customer_details == 0)
		{
			return $query_customer_details;
		}

	}			//public function get_all_customer_details()

	//function to get all the subcategory reservations
	public function get_all_subcategory_reservations()
	{
		$this->load->model('servicesubcategoryeservationrequest_model');

		$query_subcategory_details = $this->servicesubcategoryeservationrequest_model->get_all_record();

		if (!$query_subcategory_details == 0)
		{
			return $query_subcategory_details;
		}

	}			//public function get_all_subcategory_reservations()

	//function to get all reservations for current date
	public function get_all_reservations_for_current()
	{
		$this->load->model('reservationrequest_model');

		$query_reservations_for_current = $this->reservationrequest_model->get_all_reservations_for_current();

		if (!$query_reservations_for_current == 0)
		{
			return $query_reservations_for_current;
		}

	}			//public function get_all_reservations_for_current()

	//function to get all services sub category
	public function get_all_service_sub_category()
	{
		$this->load->model('servicessubcategory_model');

		$query_service_sub_category_details = $this->servicessubcategory_model->get_all_service_sub_category_for_report();

		if(!$query_service_sub_category_details == 0)
		{   
			return $query_service_sub_category_details;
		}

	}			//public function get_all_service_sub_category()

	//function to get service vice appointments
	public function get_service_vice_appointments()
	{
		$this->load->model('reports_model');

		$query_service_vice_appointments = $this->reports_model->get_service_vice_appointments();

		if(!$query_service_vice_appointments == 0)
		{   
			return $query_service_vice_appointments;
		}

	}			//public function get_service_vice_appointments()

	//function to get all source details
	public function get_all_source_details()
	{
		$this->load->model('source_model');

		$query_get_all_source = $this->source_model->get_all_sources();

		if(!$query_get_all_source == 0)
		{   
			return $query_get_all_source;
		}

	}			//public function get_all_source_details()

	//function to get all income per month
	public function get_all_income_per_year()
	{
		$this->load->model('reports_model');		

		//get the income per year by month
		$query_get_all_income_per_year = $this->reports_model->get_all_income_per_year();

		$returnValue = array();			

		//payments available?
		if(!$query_get_all_income_per_year == 0)
		{  
			//yes then create an array to display the data in chart
			$test = array();
			foreach ($query_get_all_income_per_year as $key => $value)
			{
				if (@sizeof($test[$value['Year']]['month'])==0)
				{					
					$test[$value['Year']]['month'] = array(0,0,0,0,0,0,0,0,0,0,0,0);
				}

				$index = ((1*$value['Month'])-1);				
				$test[$value['Year']]['month'][$index] = (1*$value['total_sales']);
				
			}
				
			$json_array = array();
			foreach ($test as $key => $year_value)
			{
				$data['name'] = $key;
				$data['data'] = $year_value['month'];

				array_push($json_array, $data);
			}

			return (json_encode($json_array));

		}			//if(!$query_get_all_income_per_year == 0)

	}			//public function get_all_income_per_year()

	//function to get all employee esource details to display in calendar
	public function get_all_empolees_of_salon_to_calendar()
	{
		$this->load->model('user_model');
		$this->load->model('userrole_model');

		$query_user_id = $this->userrole_model->get_all_employee_details()->result_array();		

		if (!$query_user_id == 0)
		{
			$array_to_return = array();

			$array1_index = 1;
			for ($index=0; $index<sizeof($query_user_id);$index++)
			{
				$array_to_return[]   = array('id'=>'resource'.$array1_index,'name'=>$query_user_id[$index]['first_name']);
				$array1_index++;			
				
			}			//for ($index=0; $index<sizeof($query_user_id))
			
			return json_encode($array_to_return);
			
		}			//if (!$query_user_id == 0)

	}			//public function get_all_empolees_of_salon_to_calendar()

	//function to accept the customer appointmnet
	public function accept_appointment()
	{
		$returnValue = array();		//final array to return

		if (isset($_POST['submitMode']) && $_POST['submitMode'] == 'ajax')		//if the submit mode is ajax?
		{
			//yes, then get the reservation request id
			$reservation_request_id = $_POST['reservation_request_id'];
			$hidden_customer_id     = $_POST['hidden_customer_id'];

			$this->load->model('reservationrequest_model');
			$this->load->model('user_model');

			$data = array();
			$data['reservationrequest_status'] = 'Active';

			$get_result = $this->reservationrequest_model->change_reservation_status($reservation_request_id,$data);

			if ($get_result != 0)			//if data successfully returned?
			{
				//get customer details to send the confirmation mail
				$get_customer_details = $this->user_model->get_customer_name($hidden_customer_id);
				
				foreach ($get_customer_details as $key => $get_customer_detail)
				{
					$to_email      = $get_customer_detail->email;
					$customer_name = $get_customer_detail->user_title.' '.$get_customer_detail->first_name.' '.$get_customer_detail->last_name;
				}
				
				$subject                 = 'Confirmation of the appointment request';
				// $email_content           = "Dear ".$customer_name.",<br><br><br>Your appointment (request id: ".$reservation_request_id.") was successfully sent. We will send a confirmation mail regarding your appointment within shortly.<br><br>Your appointment summery:<br><br>".$summery."<br><br>Date: ".$_POST['appointment_date']."<br><br>From: ".$_POST['appointment_from']."<br><br>To: ".$_POST['appointment_to']."<br><br>From Salon Dilu";
				$email_content           = "Dear ".$customer_name.",<br><br>Your appointment (request id: ".$reservation_request_id.") was approved. Our team is ready to serve their service to you.<br><br>Thank you,<br><br>Team Salon Dilu";

				$return = $this->create_email($to_email,$subject,$email_content);

				//yes, then return the data
				$returnValue['status'] = 'success';
				$returnValue['msg']    = 'Appointment successfully accepted';
			}
			else
			{
				//no, then return an error message
				$returnValue['status'] = 'error';
				$returnValue['msg']    = 'Appointment did not successfully accept';
			}
		}

		echo (json_encode($returnValue));

	}			//public function accept_appointment()	

	//function to ignore the customer appointmnet
	public function ignore_appointment()
	{
		$returnValue = array();		//final array to return

		if (isset($_POST['submitMode']) && $_POST['submitMode'] == 'ajax')		//if the submit mode is ajax?
		{
			//yes, then get the reservation request id
			$reservation_request_id = $_POST['reservation_request_id'];
			$hidden_customer_id     = $_POST['hidden_customer_id'];

			$this->load->model('reservationrequest_model');
			$this->load->model('user_model');

			$data = array();
			$data['reservationrequest_status'] = 'Cancelled';

			$get_result = $this->reservationrequest_model->change_reservation_status($reservation_request_id,$data);

			if ($get_result != 0)			//if data successfully returned?
			{
				//get customer details to send the confirmation mail
				$get_customer_details = $this->user_model->get_customer_name($hidden_customer_id);
				
				foreach ($get_customer_details as $key => $get_customer_detail)
				{
					$to_email      = $get_customer_detail->email;
					$customer_name = $get_customer_detail->user_title.' '.$get_customer_detail->first_name.' '.$get_customer_detail->last_name;
				}
				
				$subject                 = 'Confirmation of the appointment request';
				// $email_content           = "Dear ".$customer_name.",<br><br><br>Your appointment (request id: ".$reservation_request_id.") was successfully sent. We will send a confirmation mail regarding your appointment within shortly.<br><br>Your appointment summery:<br><br>".$summery."<br><br>Date: ".$_POST['appointment_date']."<br><br>From: ".$_POST['appointment_from']."<br><br>To: ".$_POST['appointment_to']."<br><br>From Salon Dilu";
				$email_content           = "Dear ".$customer_name.",<br><br>Your appointment (request id: ".$reservation_request_id.") was rejected. If you want to know about more details please feel free to contact us.<br><br>Thank you,<br><br>Team Salon Dilu";

				$return = $this->create_email($to_email,$subject,$email_content);

				//yes, then return the data
				$returnValue['status'] = 'success';
				$returnValue['msg']    = 'Appointment successfully ignored';
			}
			else
			{
				//no, then return an error message
				$returnValue['status'] = 'error';
				$returnValue['msg']    = 'Appointment did not successfully ignore';
			}
		}

		echo (json_encode($returnValue));

	}			//public function ignore_appointment()	

	//function to get calendar data
    public function getCalenderData()
    {
        //load the modal files
         $this->load->model('reservationrequest_model');
         $this->load->model('user_model');
         $this->load->model('userreservationrequest_model');
         $this->load->model('servicesubcategoryeservationrequest_model');
         $this->load->model('servicessubcategory_model');

         //get all temprary and active events
        $queryEventDetails = $this->reservationrequest_model->get_all_events_for_calendar();
        
        //iterate the events details array
        $allEvents = array();
        foreach ($queryEventDetails->result() as $key => $event)
        {            
        	//get employee details for a specific event
            $getUserId = $this->userreservationrequest_model->get_user_id_for_reservation_request($event->reservationrequest_id);            

            $oneEvent = array();

            //get customer name for specific event to display
            $getCustomerName = $this->user_model->get_customer_name($event->user_id);

            foreach ($getCustomerName as $key=>$customer)
            {
                $oneEvent['custName']      = $customer->user_title.' '.$customer->first_name.' '.$customer->last_name;
                $oneEvent['email']		   = $customer->email;
                $oneEvent['contactNo']	   = $customer->contact_no1;


            }           //foreach ($getCustomerName as $key=>$customer)
            
            $oneEvent['title']           = ' | '.'Reservation '.$event->reservationrequest_id;
            $oneEvent['services']        = $event->sersub;
            $oneEvent['date']            = $event->reservationrequest_date;
            $oneEvent['appointmentDate'] = $event->reservationrequest_date;
            $oneEvent['start']           = $event->reservationrequest_date.' '.$event->reservationrequest_from;
            $oneEvent['end']             = $event->reservationrequest_date.' '.$event->reservationrequest_to;
            $oneEvent['startTime']       = $event->reservationrequest_from;
            $oneEvent['endTime']         = $event->reservationrequest_to;            
            $oneEvent['status']          = $event->reservationrequest_status;            
            $oneEvent['allDay']          = false; 			// will make the time showend    

            if ($getUserId['user_id'] == '1')
	    	{                	
	        	$oneEvent['resources']    = 'resource1';  			// will make the time showend             		
	    	}
	    	else
	    	{
	    		if ($getUserId['user_id'] == '2')
	    		{
	        		$oneEvent['resources']    = 'resource2';  			// will make the time showend             		
	    		}
	    		else
	    		{
	    			if($getUserId['user_id'] == '3')
	    			{
	        			$oneEvent['resources']    = 'resource3';  			// will make the time showend	    				
	    			}
	    			else
	    			{
	        			$oneEvent['resources']    = '';  			// will make the time showend	    					    				
	    			}
	    		}
	    	}       
            // $oneEvent['end']          = gmdate("Y-m-d\H:i:s", $event->reservationrequest_to);
            // $oneEvent['status']      = $event->reservationrequest_status;
            //$oneEvent['custName']      = $event->Customer_id;                        

            $allEvents[sizeof($allEvents)] = $oneEvent;

        }           //foreach ($queryEventDetails->result() as $key => $event)

        echo json_encode($allEvents);

    }           //public function getCalenderData()

}			//class Admindashboard extends MY_Controller