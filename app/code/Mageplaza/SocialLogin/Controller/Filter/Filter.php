<?php 

namespace Mageplaza\SocialLogin\Controller\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
 
class Filter extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $_storeManager;


    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory

    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
	     $status = $this->getRequest()->getPost('ord_status');
         $message = '<div class="message info empty"><span>You have placed no orders</span></div>';
		 $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
     $storeManager = $objectManager->get("\Magento\Store\Model\StoreManagerInterface");
         $customerSession = $objectManager->get('Magento\Customer\Model\Session');
		 
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
                    if($item->getStatus() == "Canceled"){
                      $canceledItems++;
                    }
            }
          if($itemCount == $canceledItems){
           
              //$order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED);
              $order->save();
          }
            
     $html = '<div class="recent-orders-discription table-order-items recent" id="my-orders-table">
                <div class="recent-orders-header">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-3">
                                    <h5>Order Date</h5>
                                </div>
                                <div class="col-md-2">
                                    <h5>Delivery</h5>
                                </div>
                                <div class="col-md-2">
                                    <h5>Total</h5>
                                </div>
                                <div class="col-md-3">
                                    <h5>Ship to</h5>
                                </div>
                                <div class="col-md-2">
                                    <h5>Status</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 ordr-id-data">
                             <h5>Order #: '.$_order->getRealOrderId().'</h5>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-3">
                                    <h6>'.
                                    $newDate
                                    .'</h6>
                                </div>
                                <div class="col-md-2">';
                                    $status = $_order->getStatusLabel();
                                    if($status == "Pending"){
                                    $html.='<h4>Pending</h4>';
                                      }elseif($status == "Complete"){ 
                                    $html.='<h4>In Transit</h4>';
                                    }elseif($status == "Canceled"){
                                    $html.='<h4>NA</h4>';
                                  } 
                                    
                                $html.='</div>
                                <div class="col-md-2">
                                    <h6>'.$priceHelper->currency($_order->getGrandTotal(), true, false).'</h6>
                                </div>
                                <div class="col-md-3 dropdown">
                                    <h4 class="dropbtn">'.$_order->getShippingAddress()->getName().'<i class="fa fa-angle-down" aria-hidden="true"></i></h4>
                                    <div class="dropdown-content">
                                       <p>'.$order->getShippingAddress()->getFirstname(). $order->getShippingAddress()->getLastname().'</p>
                                       <p>'.$order->getShippingAddress()->getStreet()[0]." ,".$order->getShippingAddress()->getCity().'</p>
                                       <p>'.$order->getShippingAddress()->getRegion()." ,".$order->getShippingAddress()->getPostcode().' India</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <h4>'.$order->getStatusLabel().'</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 ordr-id-data">

                        <h4><a  href="'.$storeManager->getStore()->getBaseUrl().'sales/order/view?order_id='.$_order->getId().'">Order Details <i class="fa fa-angle-right" aria-hidden="true"></i></a></h4>
                        </div>
                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-3" style="text-align:center;">';
                                $order = $objectManager->create("Magento\Sales\Model\Order")->load($_order->getId());
                                $orderItems = $order->getAllItems();
                                $itemCount=0;
                                $canceledItems=0;
                                foreach ($orderItems as $_item) {
                                   //  $product = $_item->getProduct();
                                   // $parent = $objectManager->create("Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable")->getParentIdsByChild($product->getId());
                                   //  if(($_item->getProductType() == "simple") && isset($parent[0])){
                                   //                  continue;
                                   //  }else{
                                   //      $itemCount++;
                                   //      if($_item->getStatus() == "Canceled"){
                                   //      $canceledItems++;
                                   //      }
                                   //  } 
                                }
                                
                             // $html.='<img src="'; 
                                                foreach ($orderItems as $_item) {
                                                        $productid = $_item->getProductId();
                                                        break;
                                                }
                                                $product = $objectManager->create("Magento\Catalog\Model\Product")->load($productid);
                                                $storeManager = $objectManager->get("\Magento\Store\Model\StoreManagerInterface");
                 $html.='<img src="'.$storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product'.$product->getData('image').'" alt="img">';
                 $html.='</div>
                            <div class="col-md-9">
                                <div class="recent-orders-header-description">
                                     <h4>'.$product->getName().'</h4> 
                                    <p>'.$product->getData('tag_line').'</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="recent-orders-header-btn">';
                            if($_order->hasShipments()){

                            $html.='<a href="'.$storeManager->getStore()->getBaseUrl().'track-order/?id='.$_order->getIncrementId().'">Track Order</a>';
                              } 
                            /*$html.='<a href="#">Write a review</a>';*/
                            if($_order->canCancel()){
                            $html.='<a href="'.$storeManager->getStore()->getBaseUrl().'sales/order/view?order_id='.$_order->getId().'">Cancel Order</a>';
                            }else if($_order->getStatusLabel() == "Complete"){
                            $CompletedDate = $_order->getUpdatedAt(); 
                            $date = strtotime("+30 days", strtotime($CompletedDate));
                            $returnExpireDate = date("Y-m-d", $date);
                            $currentDate = date("Y-m-d");
                            if($currentDate < $returnExpireDate){

                            $html.='<a href="'.$storeManager->getStore()->getBaseUrl().'sales/order/view?order_id='.$_order->getRealOrderId().'">Return Order</a>';
                        }

                        }
                       $html.='</div>
                    </div>
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
?>