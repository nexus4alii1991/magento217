<?php 

namespace Mageplaza\SocialLogin\Controller\Wishlist;
 
 
class Wishlist extends \Magento\Framework\App\Action\Action {

public function __construct(
    \Magento\Wishlist\Model\Wishlist $wishlist,
    \Magento\Framework\App\Action\Context $context
) {
    $this->wishlist = $wishlist;
    parent::__construct($context);
}

public function execute()
{
    $customerId = $this->getRequest()->getPost('cust_id');
    $productId = $this->getRequest()->getPost('prod_id');
    $wish = $this->wishlist->loadByCustomerId($customerId);
    $items = $wish->getItemCollection();

    /** @var \Magento\Wishlist\Model\Item $item */
    foreach ($items as $item) {
        if ($item->getProductId() == $productId) {
            $item->delete();
            $wish->save();
            echo $productId;
        }else{
          echo "not matched";
        }
    }
}
}
?>