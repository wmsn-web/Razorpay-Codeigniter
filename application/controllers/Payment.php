<?php /**
 * 
 */
require('razorpay-php/Razorpay.php');
		use Razorpay\Api\Api;
class Payment extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$setup = $this->config->item('razorpay_id');
		
		//User data fetch from database
		$usr = array(
			"name"	=> "User Name",
			"email" => "email@email.com",
			"phone" => "1234567890"
		);

		$amt = 1;

		$keyId = $this->config->item('razorpay_id');
		$keySecret = $this->config->item('secret_key');
		$displayCurrency = $this->config->item('currency');
		$api = new Api($this->config->item('razorpay_id'), $this->config->item('secret_key'));
		$orderData = [
		    'receipt'         => mt_rand(000000,999999),
		    'amount'          => $amt *100, // 2000 rupees in paise
		    'currency'        => $displayCurrency,
		    'payment_capture' => 1,
		];
		$razorpayOrder = $api->order->create($orderData);
		$razorpayOrderId = $razorpayOrder['id'];
		$_SESSION['razorpay_order_id'] = $razorpayOrderId;
		$displayAmount = $amount = $orderData['amount'];

		if ($displayCurrency !== 'INR')
		{
		    $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR";
		    $exchange = json_decode(file_get_contents($url), true);

		    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
		}
		$checkout = 'automatic';
		if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true))
		{
		    $checkout = $_GET['checkout'];
		}

		$data = [
		    "key"               => $this->config->item('razorpay_id'),
		    "amount"            => $amount,
		    "name"              => $this->config->item('company_name'),
		    "description"       => $this->config->item('company_name'),
		    "image"             => "",
		    "prefill"           => [
			    "name"              => $usr['name'],
			    "email"             => $usr['email'],
			    "contact"           => $usr['phone'],
			 ],
		    
		    "theme"             => [
			    "color"             => "#F37254"
			    ],
		    "order_id"          => $razorpayOrderId,
		];
		if ($displayCurrency !== 'INR')
		{
		    $data['display_currency']  = $displayCurrency;
		    $data['display_amount']    = $displayAmount;
		}

		$json = json_encode($data);
		$data['disp'] = $amt;
		require("checkout/{$checkout}.php");
		$this->load->view("paymemt",$data);
	}

	
}