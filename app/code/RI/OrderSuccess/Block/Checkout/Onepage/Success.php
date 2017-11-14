<?php
namespace RI\OrderSuccess\Block\Checkout\Onepage;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @return int
     */
    public function getGrandTotal()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_orderFactory->create()->load($this->getLastOrderId());
        return $order->getGrandTotal();
    }

    public function getOrder() {
        return $this->_checkoutSession->getLastRealOrder();
    }
    public function getOrderItems(){
    	return $this->_checkoutSession->getLastRealOrder()->getAllVisibleItems();
    }
}