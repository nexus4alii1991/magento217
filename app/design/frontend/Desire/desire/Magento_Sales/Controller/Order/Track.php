<?php 

namespace Magento\Sales\Controller\Order;

class Track extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;

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
        $orderDetail = $objectManager->create('Magento\Sales\Model\Order')->load($_POST['orderid']);
   
        if ($orderDetail->canCancel()) {
            $orderItems = $orderDetail->getAllItems();       
            $itemCount = 0;
            $canceledItems = 0;
                foreach ($orderItems as $value) {
                $product = $value->getProduct();
                $parent = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($product->getId());
                    if(($value->getProductType() == 'simple') && isset($parent[0])){
                        continue;
                    }else{
                        $itemCount++;
                        if($value->getStatus() == "Canceled"){
                        $canceledItems++;
                        }
                    } 

                    $product = $value->getProduct();
                            $parent = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($product->getId());
                            
                            if(($value->getProductType() == 'simple') && isset($parent[0])){
                                continue;
                            }
                    if($value->getId() == $_POST['itemid']){ 
                       $value->setQtyCanceled($value['qty_ordered']);
                       $value->save();
                       $orderDetail->save();
                    }else{
                     continue;
                    }
                    

                }

                if($itemCount == $canceledItems){
                    $orderDetail->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED);
                    $orderDetail->save();
                }
           
                if($orderDetail->save()){ 
                $sql = "Insert Into " . $tableName . " (order_id, item_id, qty_canceled, comments) Values (".$_POST['orderid'].",".$_POST['itemid'].",".$value['qty_ordered'].",'".$_POST['comments']."')";
                $connection->query($sql);

                $select = $connection->select()
                ->from(
                    ['ce' => 'sales_payment_transaction'],
                    ['txn_id']
                )
                ->where('order_id = '.$_POST['orderid'].''); 
                $data = $connection->fetchAll($select);
                    if(!empty($data)){ 
                    $txn_id = $data[0]['txn_id'];
                    $api = new Api('rzp_test_XU74hwtdyhkpOR', 'ZMd0pt7HdfijddqkJyWDL8dR');
                    $refundAmount = round($_POST['itemprice'] * 100 * $value['qty_ordered']);
                    $payment = $api->payment->fetch(''.$txn_id.'');
                    $refund = $payment->refund(array('amount' => $refundAmount )); //for partial refund 
                    }
            }
            $_order = $objectManager->create('Magento\Sales\Model\Order')->load($Entity_id); 
            $items = $_order->getAllItems();
        }
    }

}
 ?>