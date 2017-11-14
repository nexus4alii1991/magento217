<?php
 
namespace Pickrr\Magento2\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
 
class paymentMethod implements ObserverInterface
{
    /**
     * payment_method_is_active event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
       $ProdustSeller = array();
       $cmw = 62; //for casemyway use 61 & for desiredesire use 62
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
       $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
       $grandTotal = $cart->getQuote()->getGrandTotal();
       $shippingAddress = $cart->getQuote()->getShippingAddress();
       $destination_pin = $shippingAddress->getPostcode();
       $items = $cart->getQuote()->getAllVisibleItems();
        foreach($items as $item) {
            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
                            $seller = $product->getData('seller');
                            if(!$seller == ""){
                                if(!in_array($seller, $ProdustSeller)){
                                $ProdustSeller[]= $seller;
                                }
                               }     
          }
          $result1 = count($ProdustSeller);
          if(in_array($cmw, $ProdustSeller)){
           $source_pin = "560025"; 
          }else{
            $source_pin = "121006";  
          }
           if($source_pin && $destination_pin){
    /**** API Call o picker for servicability check *****/

        try {
         
         $url = 'http://pickrr.com/api/check-pincode-service/';
         $token= "0A688CAD8E14B689830C3669C1A1886F390C3998";
         $data = array (
        'from_pincode' => $source_pin,
        'to_pincode' => $destination_pin,
        'auth_token' => '5b0d1704559a0467f8f2a6a985f734959569',
        );
        $params = '';
        foreach($data as $key=>$value)
                    $params .= $key.'='.$value.'&';

            $params = trim($params, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.'?'.$params ); //Url together with parameters
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Return data instead printing directly in Browser
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 7); //Timeout after 7 seconds
        curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $result = curl_exec($ch);
        $result = json_decode($result, true);
        if($result['err'] == ""){
        if(!$result == ""){
        $has_cod = $result['has_cod'];
        $cod_limit = $result['cod_limit'];
        $has_pickup = $result['has_pickup'];
        $has_prepaid = $result['has_prepaid'];
        // you can replace "checkmo" with your required payment method code
        if($has_pickup == 1){ //for checking whether pincode is servicable or not

            if($has_cod == 0){  // for cod check
                if($cod_limit < $grandTotal){  // for cod amount check
                    if($observer->getEvent()->getMethodInstance()->getCode()=="cashondelivery"){
                        $checkResult = $observer->getEvent()->getResult();
                        $checkResult->setData('is_available', false); //this is disabling the payment method at checkout page
                    }
                }
            } 
            if($has_prepaid == 0){
                if($observer->getEvent()->getMethodInstance()->getCode() == "razorpay"){
                        $checkResult = $observer->getEvent()->getResult();
                        $checkResult->setData('is_available', false); //this is disabling the payment method at checkout page
                } 
            }

        }else{
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("No payment methods available");
              }
      }
    }
       }catch (Exception $e){
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($e->getMessage());
         }
            }
    }
}