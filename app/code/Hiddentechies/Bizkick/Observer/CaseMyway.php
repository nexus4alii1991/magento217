<?php
namespace Hiddentechies\Bizkick\Observer;

class CaseMyway implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer){
	        
	    try {  /* parameters can be change according to requirment */
		        $order = $observer->getEvent()->getOrder();
				$order_id = $order->getIncrementId();
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("order id is".$order_id);
				$shippingAddress = $order->getShippingAddress();
		        $params = array();
				$params['OrderNo'] = $order->getIncrementId();
				$params['FirstName'] = $shippingAddress->getFirstname();
				$params['LastName'] = $shippingAddress->getLastname();
				$params['CustomerAddress1'] = $shippingAddress->getStreet(1);
				$params['CustomerAddress2'] = $shippingAddress->getStreet(2);
				$params['City'] = $shippingAddress->getCity();
				$params['State'] = $shippingAddress->getRegion();
				$params['Country'] = 'India';
				$params['Zip'] = $shippingAddress->getPostalcode();
				$params['PhoneNumber'] = $order->getTelephone();
				$params['OrderDate'] = $order->getCreatedAt();
				$params['OrderReceivedDate'] = "";
				$params['ShippingCompany'] = "Delhivery";
				$params['ShippingService'] = "Air";

					$item_data= array('Id'=>0);
					$item_data['Brand'] = "Sony";
					$item_data['Model'] = "Xperia T2 ultra";
					$item_data['MaterialType'] = "Plastic";
					$item_data['CaseType'] = "Slim";
					$item_data['FinishMaterial'] = "Matte";
					$item_data["DocketNo"] = "";
				    $item_data["OrderStatus"] = "0";
                    $item_data["Quantity"] = 1;
                    $item_data["PrintFileCodeString"]= "";
                    $item_data["ShippingCompany"] = "Delhivery";
                    $item_data["ShippingService"] ="Air";
				    $item_data["InvoiceNo"] = null;
				    $item_data["ImageUrl"]= "https://s3.amazonaws.com/cmwoms/printFiles/Partner+1/100223619-BEE-OF-THE-WOR-NGD-AP-IPH6-1Qty.jpg";
				    $item_data["PreviewImageUrl"]= "https://s3.amazonaws.com/cmwoms/previewFiles/Partner+1/100223619-BEE-OF-THE-WOR-NGD-AP-IPH6-1Qty.png";
				    $params['OrderItems'][] = $item_data;					

				$url= "http://api.b2btest.casemyway.com/v1/Order/PlaceOrder";
				$token= "0A688CAD8E14B689830C3669C1A1886F390C3998";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				//curl_setopt($ch,CURLOPT_HEADER);
				curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($params));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Token:'.$token,'Content-Type:application/json'));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
				$response = curl_exec($ch);
				curl_close($ch);
		   } catch (Exception $e) { 
		   }
	    }

}
?>