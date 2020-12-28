<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_Sagepay extends Controller
{
	public function action_debug_cart()
	{
		header('content-type: text/plain');
		$c = new Model_Checkout();
		print_r($c->get_cart());
		exit();
	}

	public function action_testcharge()
	{
		header('content-type: text/plain');

		$billing_address = array( 'firstname' => 'Kakarotto',
									'lastname' => 'Goku',
									'address1' => '88',
									'address2' => 'bbbb',
									'city' => 'london',
									'country' => 'GB',
									'postcode' => '412' );
		
		$delivery_address = array( 'firstname' => 'Kakarotto',
									'lastname' => 'Goku',
									'address1' => '88',
									'address2' => 'bbbb',
									'city' => 'london',
									'country' => 'GB',
									'postcode' => '412' );

		$card_details = array( 'cardNumber' => '4929 0000 0000 6',
								'cardHolder' => 'John doe',
								'expiryDate' => '0318',
								'cv2' => '123',
								'cardType' => 'VISA' );
		$items = array();
		$items[] = array( 'description' => 'Intel Core i7 4790K',
							'sku' => 'stepping10',
							'code' => 'I74790K',
							'net' => 100,
							'tax' => 23,
							'quantity' => 1 );
		/*$items[] = array( 'description' => 'Flat Shipping',
							'net' => 10,
							'quantity' => 1 );*/
		$deliveryAmount = 10;
		$discounts = array();
		$discounts['discount'] = array( 'fixed' => 5, 'description' => 'it manager discount' );
		$m_sagepay = new Model_Sagepay();
		$result = $m_sagepay->charge_basket($card_details, $items, $billing_address, $delivery_address, $deliveryAmount, $discounts);

		if(@$result['errors']){
			return array('status' => 'error', 'errors' => $result['errors'] );
		} else if($result['Status'] == '3DAUTH' && $result['3DSecureStatus'] == 'OK'){
			$this->start3d($result);
			return array('status' => 'inprocess', 'process' => '3dstart');
		} else if($result['Status'] == 'OK'){
			return array('status' => 'ok', 'details' => $result);
		} else {
			return array('status' => 'failed', 'details' => $result);
		}
		exit();
	}
	
	public function start3d($sagepay_response)
	{
		echo View::factory('3dstart', $sagepay_response)->render();
	}
	
	public function action_3dcallback()
	{
		header('content-type: text/plain');
		$m_sagepay = new Model_Sagepay();
		$m_sagepay->complete3d($_POST);
	}
	
	public function action_3dcomplete()
	{
		?>
		<html>
		<body>
		<p>Please wait...</p>
		<script>
		var f=window.top.document.forms.creditCardForm;
		f.MD.value = "<?=isset($_POST['MD']) ? $_POST['MD'] : ''?>";
		f.MDX.value = "<?=isset($_POST['MDX']) ? $_POST['MDX'] : ''?>";
		f.PaRes.value = "<?=isset($_POST['PaRes']) ? $_POST['PaRes'] : ''?>";
		//f.submit();
		window.top.submitCheckout();
		//window.top.d3securedialog.dialog('close');
		</script>
		</body>
		</html>
		<?php
		exit();
	}
	
	public function action_notify()
	{
		file_put_contents('sagepay-log-' . date('YmdHis') . '.txt', print_r($GLOBALS, 1));
	}
}
