<?php
namespace RI\Artist\Controller\Refund;
use Razorpay\Api\Api;


class Index extends \Magento\Framework\App\Action\Action
{
  protected $orderFactory;


  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Sales\Model\OrderFactory $orderFactory
 ) {
    $this->orderFactory = $orderFactory;
    parent::__construct($context);

  }

  public function execute() { 

    $orderId = $this->getRequest()->getPost('OrderId'); 

  	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection = $resource->getConnection();
    $razorpayKeyID = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/razorpay/key_id');
    $razorpayKeySecret = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/razorpay/key_secret');
   
	  $orderDetail = $objectManager->create('Magento\Sales\Model\Order')->load($orderId);
      $comment = "Order canceled by customer";
      $orderDetail->setIsCustomerNotified(false);
      $orderDetail->setState("canceled")->setStatus("canceled");
      $historyItem = $orderDetail->addStatusHistoryComment($comment, "canceled");
      $historyItem->setIsCustomerNotified(1)->save();
      $orderDetail->save();
      $orderCommentSender = $objectManager
          ->create('Magento\Sales\Model\Order\Email\Sender\OrderCommentSender');
      $orderCommentSender->send($orderDetail, true, ' ');
	  if($orderDetail->save()){ 

                $select = $connection->select()
                ->from(
                    ['ce' => 'sales_payment_transaction'],
                    ['txn_id']
                )
                ->where('order_id = '.$orderId.''); 
                $data = $connection->fetchAll($select);
                    if(!empty($data)){ 
                    $txn_id = $data[0]['txn_id'];
                    $api = new Api($razorpayKeyID, $razorpayKeySecret);
                    $payment = $api->payment->fetch(''.$txn_id.'');
                    $refund = $payment->refund(); //refund 
                    }
            }
		
  }
}