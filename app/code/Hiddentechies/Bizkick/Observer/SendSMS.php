<?php
namespace Hiddentechies\Bizkick\Observer;

class SendSMS implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer){
	
	       try {
		    $order = $observer->getEvent()->getOrder();
			$orderid = $order->getIncrementId();
			$otp = $orderid;
			$number = $order->getBillingAddress()->getTelephone();
			$status = $order->getStatusLabel(); 
			$product_names = array();
			foreach($order->getAllItems() as $item){
			  $ProdustIds[]= $item->getProductId();
			  $product_names[] = $item->getName();	
			}
			  if(count($product_names) > 1){
				$product = $product_names[0].' and more';
			  }else{
			    $product = $product_names[0];
			  }
			if($status == 'Pending'){
	        $message = "Hi!, Your order $otp for $product has been successfully placed. It will be shipped in 2-15 working days";
			}
			else if($status == 'Processing'){
			$message = "Hi!, Your Order No. $otp is processing";
			}
			else if($status == 'Complete'){
			$message = "Hi! your order $otp for $product has been shipped";
			}
			else if($status == 'Canceled'){
			$message = "Hi! your order $otp has been canceled";
			}
			$apiKey = rawurlencode('pNgD+phaB+0-QzhVw3TJlXLw7X9GcO8364Hrs7wqxJ');
			$sender = rawurlencode('DESIRE');
			$message = rawurlencode($message);
			$data = array();
	        $data = 'apikey=' . $apiKey . '&numbers=' . $number . "&sender=" . $sender . "&message=" . $message;
			$ch = curl_init('https://api.textlocal.in/send/?' . $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch); 
			\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($response);
			curl_close($ch);
			}
			catch(Exception $e){
			\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($e->getMessage());  
		    }
	    }
}
?>