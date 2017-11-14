<?php
namespace Pickrr\Magento2\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Bootstrap;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Pickrr\Magento2\Helper\ExportShipment;
use Magento\Sales\Model\Order;
 
class orderCommitAfter implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
 
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ScopeConfigInterface $scopeConfig,
        ExportShipment $helper
    ) {
        $this->helper = $helper;
        $this->_objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
    }
 
    /**
     * customer register event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug('came inside execute function');
        if ("0" == $this->scopeConfig->getValue('pickrr_magento2/general/automatic_shipment_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
          return NULL;
        $invoice = $observer->getEvent()->getInvoice();
		$order = $invoice->getOrder();
        $payment = $order->getPayment();
        $ProdustSeller = array();
        foreach ($order->getAllItems() as $item){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
                $seller = $product->getData('seller');
					$cmw = 62;//for casemyway use 61 & for desiredesire use 62
				if(!$seller == ""){
					if(!in_array($seller, $ProdustSeller)){
					$ProdustSeller[]= $seller;
					}
				   }
	    }
        if($payment->getMethod() == "cashondelivery")
            $cod_amount = $order->getGrandTotal();
        else
            $cod_amount = 0.0;

/* For razorpay order syncing  to cmw, have commented below line */

        // if ($order->getState() != Order::STATE_NEW && $order->getState() != Order::STATE_PENDING_PAYMENT )
        //    return NULL;

/* finished*/
        $auth_token = $this->scopeConfig->getValue('pickrr_magento2/general/auth_token', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         
        $pickup_time = $this->scopeConfig->getValue('pickrr_magento2/shipment_details/pickup_time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
       if(in_array($cmw, $ProdustSeller)){
        $from_name = $this->scopeConfig->getValue('pickrr_magento2/shipment_details/from_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $from_phone_number = $this->scopeConfig->getValue('pickrr_magento2/shipment_details/from_phone_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $from_pincode = $this->scopeConfig->getValue('pickrr_magento2/shipment_details/from_pincode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $from_address = $this->scopeConfig->getValue('pickrr_magento2/shipment_details/from_address', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	     }else{
        $from_name = $this->scopeConfig->getValue('pickrr_magento2/cmw_shipment_details/cmw_from_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $from_phone_number = $this->scopeConfig->getValue('pickrr_magento2/cmw_shipment_details/cmw_from_phone_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $from_pincode = $this->scopeConfig->getValue('pickrr_magento2/cmw_shipment_details/cmw_from_pincode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $from_address = $this->scopeConfig->getValue('pickrr_magento2/cmw_shipment_details/cmw_from_address', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
       $this->helper->createOrderShipment($auth_token, $order, $from_name, $from_phone_number, $from_pincode, $from_address, $pickup_time, $cod_amount);
       \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug('helper createOrderShipment got called');
    }
}