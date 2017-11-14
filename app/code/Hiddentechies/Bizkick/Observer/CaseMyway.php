<?php
namespace Hiddentechies\Bizkick\Observer;

class CaseMyway implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer){
	        
	    try {  /* parameters can be change according to requirment */
	    	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		        $order = $observer->getEvent()->getOrder();
				$order_id = $order->getIncrementId();
				$shippingAddress = $order->getShippingAddress();
			    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
			    $currentStore = $storeManager->getStore();
			    $base_url = $storeManager->getStore()->getBaseUrl();
			    $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
			    $spli = explode("//",$base_url);
			    $spli2 = explode(".",$spli[1]);
			    if($spli2[0] == "desiredesire"){
			    	$spli2[0] = "DD2";
			    }
		        $params = array();
				$params['OrderNo'] = $spli2[0].$order->getIncrementId();
				$params['FirstName'] = $shippingAddress->getFirstname();
				$params['LastName'] = $shippingAddress->getLastname();
				$params['CustomerAddress1'] = $shippingAddress->getStreet()[0];
				$params['City'] = $shippingAddress->getCity();
				$params['State'] = $shippingAddress->getRegion();
				$params['Country'] = 'India';
				$params['Zip'] = $shippingAddress->getPostcode();
				$params['PhoneNumber'] = $order->getTelephone();
				$params['OrderDate'] = $order->getCreatedAt();
				$params['OrderReceivedDate'] = "";
				$params['ShippingCompany'] = "Pickrr";
				$params['ShippingService'] = "Air";

                $ProdustSeller = array();
				foreach($order->getAllItems() as $item){
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
                $base_img = $product->getData('image');
                $sku = $product->getSku();
				$seller = $product->getData('seller');
				$brand = $product->getData('brand');
				$brand_attr = $product->getResource()->getAttribute('brand')->setStoreId(0);
				 if ($brand_attr->usesSource()) {
				            $brand = $brand_attr->getSource()->getOptionText($brand);
				 }
				$finish = $product->getData('finish');
				$finish_attr = $product->getResource()->getAttribute('finish');
				 if ($finish_attr->usesSource()) {
				       $finish = $finish_attr->getSource()->getOptionText($finish);
				 }
				$model = $product->getData('model');
				$model_attr = $product->getResource()->getAttribute('model')->setStoreId(0);
				 if ($model_attr->usesSource()) {
				          $model = $model_attr->getSource()->getOptionText($model);
				 }
				//$material_type = $product->getData('design_type');
				$case_type = $product->getData('case_type');
				$case_attr = $product->getResource()->getAttribute('case_type');
				 if ($case_attr->usesSource()) {
				       $case_type = $case_attr->getSource()->getOptionText($case_type);
				 }
	
				$cmw = 61; //for cmw use 61 and desiredesire use 62
			if(!$seller == ""){
				if(!in_array($seller, $ProdustSeller)){
				$ProdustSeller[]= $seller;
				}
			   }
			        $item_data= array('Id'=>0);
					$item_data['Brand'] = "Apple"; //$brand
					$item_data['Model'] = "iPhone 7"; //$model
					$item_data['MaterialType'] = "Plastic";
					$item_data['CaseType'] = $case_type;
					$item_data['FinishMaterial'] = "Matte"; //$finish
					$item_data["DocketNo"] = "";
				    $item_data["OrderStatus"] = "0";
                    $item_data["Quantity"] = $item->getQtyOrdered();;
                    $item_data["PrintFileCodeString"]= "";
                    $item_data["ShippingCompany"] = "Pickrr";
                    $item_data["ShippingService"] ="Air";
				    $item_data["InvoiceNo"] = null;
				    $item_data["ImageUrl"]= $mediaUrl."catalog/product".$product->getData('cmw_print_file');
				    $item_data["PreviewImageUrl"]= $mediaUrl."catalog/product".$product->getData('cmw_preview_file'); 
				    $params['OrderItems'][] = $item_data;
				}
\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("Print Image is".$mediaUrl."catalog/product".$product->getData('cmw_print_file'));
\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("Preview Image is".$mediaUrl."catalog/product".$product->getData('cmw_preview_file'));
				$result = count($ProdustSeller);	
				if($result == 1){
                 if(in_array($cmw, $ProdustSeller)){					
				$url= "http://api.b2b.casemyway.com/v1/Order/PlaceOrder"; //live cmw url
				//$url= "http://api.b2btest.casemyway.com/v1/Order/PlaceOrder"; // test cmw url
				$token= "0A688CAD8E14B689830C3669C1A1886F390C3998";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				//curl_setopt($ch,CURLOPT_HEADER);
				curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($params));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Token:'.$token,'Content-Type:application/json'));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
				$response = curl_exec($ch);
			\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($response);
				curl_close($ch);
				}
				}
		   } catch (Exception $e) { 
		   		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($e->getMessage());
		   }
	    }

}
?>