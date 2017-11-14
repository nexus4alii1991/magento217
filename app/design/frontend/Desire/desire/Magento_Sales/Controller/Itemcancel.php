<?php 

namespace Magento\Sales\Block\Order;

class Itemcancel extends \Magento\Framework\View\Element\Template
{echo "bsddhbfs";exit;

   function index($order_id,$item_id){
echo "bsddhbfs";exit;
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $orderDetail = $objectManager->create('Magento\Sales\Model\Order')->load(16);
if ($orderDetail->canCancel()) {
    $orderItems = $orderDetail->getAllItems();        
    echo $orderDetail->getRealOrderId();
    foreach ($orderItems as $value) {
        if($value->getProductType() == 'simple'){
            continue;
        }
        if($value->getId()==40){    
            $value->setQtyCanceled($value['qty_ordered']);
            $value->save();  
        }else{
         continue;
        }
    }
    $res = $orderDetail->save();
    print_r($res);
}

}

}
 ?>