<?php

namespace Mageplaza\SocialLogin\Controller\Delete;

class Delete() extends \Magento\Backend\App\Action 
{
 protected $wishlist;

 public function __construct(
  \Magento\Backend\App\Action\Context $context,
     \Magento\Wishlist\Model\Wishlist $wishlist
 ) {
     $this->wishlist = $wishlist;
     parent::__construct($context);
 }

 public function execute()
 {
    echo "hello";
    die();
     $params = $this->getRequest()->getParams();

     $customerId = $params['customer_id']; // $customerId = 11;
     $productId = $params['product_id'];
    // $customerId = 23; // $customerId = 11;
    // $productId = 421;  // $productId = 16;

     $wishlistModelObject = $this->wishlist->loadByCustomerId($customerId);
     $items = $wishlistModelObject->getItemCollection();

     try{
            
            /** @var \Magento\Wishlist\Model\Item $item */
            foreach ($items as $item) {
               if ($item->getProductId() == $productId) {
                 $item->delete();
                 $wishlistModelObject->save();
               }
            }

        }catch (Exception $e){
            var_dump($e->getMessage());
            die;
        }
  //   header("Location: ".$base_url."customer/account#wishlist");
 }
}
?>