<?php 

namespace Mageplaza\SocialLogin\Controller\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Helper\Image as ImageHelper;

 
class MobileFilter extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $_storeManager;
    protected $helper;



    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Catalog\Helper\Image $helper,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->helper = $helper;
    }

    /**
     * Execute view action
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if($customerSession->isLoggedIn()) {

         $status = $this->getRequest()->getPost('ord_status');
         $message = '<div class="message info empty"><span>You have placed no orders</span></div>';
        $storeManager = $objectManager->get("\Magento\Store\Model\StoreManagerInterface");
         $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data'); // Instance of Pricing Helper
         
         if($customerSession->isLoggedIn()) {
          $cust_id = $customerSession->getCustomer()->getId();
         }
         if($status == "not"){
            $orderDatamodel = $objectManager->get('Magento\Sales\Model\Order')->getCollection()->setPageSize(5)
                            ->setOrder('created_at', 'DESC');
            $orderDatamodel1 = $orderDatamodel->addFieldToFilter('customer_id', $cust_id);
        }else if($status == "All"){
            $orderDatamodel = $objectManager->get('Magento\Sales\Model\Order')->getCollection()->setOrder('created_at', 'DESC');
            $orderDatamodel1 = $orderDatamodel->addFieldToFilter('customer_id', $cust_id);
        }else if($status == "Pending"){
            $orderDatamodel = $objectManager->get('Magento\Sales\Model\Order')->getCollection()->setOrder('created_at', 'DESC');
         $orderDatamodel1 = $orderDatamodel->addFieldToFilter('customer_id', $cust_id);
         $orderDatamodel1 = $orderDatamodel->addFieldToFilter('status', array('Pending','Processing'));
        }else{
         $orderDatamodel = $objectManager->get('Magento\Sales\Model\Order')->getCollection()->setOrder('created_at', 'DESC');
         $orderDatamodel1 = $orderDatamodel->addFieldToFilter('customer_id', $cust_id);
         $orderDatamodel1 = $orderDatamodel->addFieldToFilter('status', $status);
          }
         $html_data = '';
         foreach($orderDatamodel1 as $_order){
            $order = $objectManager->create('\Magento\Sales\Model\Order')->load($_order->getId());
            $timestamp = strtotime($_order->getCreatedAt());
            $newDate = date('d M Y', $timestamp);
            $itemCount=0;
            $canceledItems=0;
            foreach ($_order->getAllVisibleItems() as $item) {
              $itemCount++;
                    if($item->getStatus() == "canceled"){
                      $canceledItems++;
                    }
            }
          if($itemCount == $canceledItems){
           
              //$order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED);
              $order->save();
          }

         //pricrr order status
         $op = "Your order is being processed"; 
         $om = "Processing";
         $oc = "Your order is cancelled";
         $pp = "Your order has been picked up";
         $od = "Your order has been dispatched";
         $ot = "Your order is in transit";
         $oo = "Your order is out for delivery";
         $ndr = "Failed Attempt at Delivery";
         $dl = "Your order has been delivered";
         $rto = "Your order has been returned";
         $rto_ot = "Order Returned Back is in Transit";
         $rto_oo = "Order Returned Back is Out for Delivery";
         $rtp = "Order Returned Back has Reached Pickrr Warehouse";
         $rtd = "Order Returned to Consignee";
		 
		 
		 
								$order = $objectManager->create("Magento\Sales\Model\Order")->load($_order->getId());
                                $orderItems = $order->getAllItems();
                                $itemCount=0;
                                $canceledItems=0;
                               
                                                foreach ($orderItems as $_item) {
                                                        $productid = $_item->getProductId();
                                                        //$_imagehelper = $this->helper('Magento\Catalog\Helper\Image');
                                                        $product = $objectManager->create("Magento\Catalog\Model\Product")->load($productid);
                                                        $productImageAttr = $product->getCollectorsBanner();
                                                        $baseImageUrl =  $this->helper->init($product,'product_base_image')
                                                                ->setImageFile($productImageAttr)
                                                                ->getUrl();

                                                        break;
                                                }
                                                $product = $objectManager->create("Magento\Catalog\Model\Product")->load($productid);
                                                $storeManager = $objectManager->get("\Magento\Store\Model\StoreManagerInterface");
                                                $main_status = "Processing";
         // pickrr api call                    
                                        if($_order->getStatus() == "complete"){

                                                $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                                                $connection = $resource->getConnection();
                                                $tableName = $resource->getTableName('picker_shipping_info');
                                                $sql = "SELECT tracking_id FROM " . $tableName ." WHERE Id='000000193'";
                                                $result = $connection->fetchAll($sql);
                                               $track_id = $result[0]['tracking_id'];
                                               $status = "";
                                               $url = 'http://www.pickrr.com/api/tracking-json/';
                                               $data = array (
                                                'tracking_id' => $track_id
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
                                                curl_close($ch);
                                                $json = json_decode($result, true);
                                                //print_r($json); exit;
                                                $main_status = $json['status']['current_status_type'];
                                                if($main_status == "OC"){
                                                    $status_body = $oc;
                                                } else if($main_status == "PP"){
                                                    $status_body = $pp;
                                                } else if($main_status == "OD"){
                                                    $status_body = $od;
                                                } else if($main_status == "OT"){
                                                    $status_body = $ot;
                                                } else if($main_status == "OO"){
                                                    $status_body = $oo;
                                                } else if($main_status == "NDR"){
                                                    $status_body = $ndr;
                                                } else if($main_status == "DL"){
                                                    $status_body = $dl;
                                                } else if($main_status == "RTO"){
                                                    $status_body = $rto;
                                                } else if($main_status == "RTO-OT"){
                                                    $status_body = $rto_ot;
                                                } else if($main_status == "RTO-OO"){
                                                    $status_body = $rto_oo;
                                                } else if($main_status == "RTP"){
                                                    $status_body = $rtp;  
                                                } else if($main_status == "RTD"){
                                                    $status_body = $rtd;  
                                                } else{
                                                    $status_body = $op;
                                                }
                                                $status_time = $json['status']['current_status_time'];  
                                            }else{
                                                $status_body = $_order->getStatus();
                                                if($status_body == "processing"){
                                                    $status_body = $op;
                                                } else if ($status_body == "canceled"){
                                                    $status_body = "Your order has been Canceled";
                                                }
                                                $status_time = "";
                                            }
        $html ='<div class="order-list">
                        <div class="ordr-img col-sm-3 col-xs-3" >
							<a class="order-img"  href="'.$storeManager->getStore()->getBaseUrl().'view-order/?order_id='.$_order->getId().'">
								<img src="'.$baseImageUrl.'" alt="'.$_order->getId().'">
							</a>
                        </div>
						<div class="ordr-status col-sm-7 col-xs-7" >
							<div class="status-data">
								<div class="status-line">'.$status_body.'</div>
								<div class="status-time">'.$status_time.'</div>
							</div>
						</div>
						<div class="ordr-detail-link col-sm-2 col-xs-2">
							<h4><a  href="'.$storeManager->getStore()->getBaseUrl().'view-order/?order_id='.$_order->getId().'"><i class="fa fa-angle-right" aria-hidden="true"></i></a></h4>
						</div>
				</div>';
        $html_data .= $html;
    }
    $data = [];
     $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
     if(count($orderDatamodel1) > 0){
        $data['status'] = 'success';
        $data['data'] = $html_data;
    }else{
        $data['status'] = 'failure';
        $data['data'] = $message;
    }
    $resultJson->setData($data);
    return $resultJson;
    }
}
}
?>