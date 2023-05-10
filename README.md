![status: archive](https://github.com/GIScience/badges/raw/master/status/archive.svg)
![License: MIT](https://camo.githubusercontent.com/ad8758fbaebbced78645b98e446c0bb5ec223676ed61700184320887cadbfb8e/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f6c6963656e73652d4d49542d627269676874677265656e2e7376673f7374796c653d666c61742d737175617265)

# Integrate with Standard Web Checkout In Codeigniter
	The merchant SDK can be used for integrating  APIs.

## Manage Config file
- Create a file name *razorpay.php* in your *config folder*.

``` php
    defined('BASEPATH') OR exit('No direct script access allowed');

	$config['razorpay_id'] = "RAZORPAY_ID";
	$config['secret_key'] = "SECRET_KEY";
	$config['currency'] = "INR";
	$config['company_name'] = "Your Company Name";
```
- Load *razorpay* config at *autoload.php*

``` php
	$autoload['config'] = array('razorpay');
```
- Open Controller directory add Controllers
- Create a file name *payment.php* in *Application* folder
``` php
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
```
- Manage Callback Data *VerifyPayment.php* Controller file

```php
		require('razorpay-php/Razorpay.php');
		use Razorpay\Api\Api;
		use Razorpay\Api\Errors\SignatureVerificationError;
```
```php
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
```

## Support
> Please contact [Technical Support](wmsn.web@gmail.com) for any live or account issues.

## Razorpay Documentations 
> For full Documentation [Visit Here](https://razorpay.com/docs/#home-payments)

## Thank you for visit GitHub.
