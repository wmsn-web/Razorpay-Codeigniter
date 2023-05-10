<?php 
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
class VerifyPayment extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		
	}

	public function index()
	{
		//$setUp = $this->PaymentModel->paymentSettings();
		$success = true;

		$error = "Payment Failed";

		if (empty($_POST['razorpay_payment_id']) === false)
		{
		    $api = new Api($this->config->item('razorpay_id'), $this->config->item('secret_key'));

		    try
		    {
		        // Please note that the razorpay order ID must
		        // come from a trusted source (session here, but
		        // could be database or something else)
		        $attributes = array(
		        	'user_id'			=>1,
		            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
		            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
		            'razorpay_signature' => $_POST['razorpay_signature']
		        );

		        $api->utility->verifyPaymentSignature($attributes);
		    }
		    catch(SignatureVerificationError $e)
		    {
		        $success = false;
		        $error = 'Razorpay Error : ' . $e->getMessage();
		    }
		}

		if ($success === true)
		{
		    echo "<pre>";
		    print_r($attributes);
		   
		}
		else
		{
		    $this->session->set_flashdata("errMsg","Payment Failed!");
		    return redirect(base_url('Payment'));
		}
	}
}