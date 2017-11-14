<?php

namespace Sejal\Flavor\Plugin;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartItemRepositoryInterface as QuoteItemRepository;

class ConfigProviderPlugin extends \Magento\Framework\Model\AbstractModel
{
    
    public function __construct(
        CheckoutSession $checkoutSession,
        QuoteItemRepository $quoteItemRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->quoteItemRepository = $quoteItemRepository;
    }

    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, array $result)
    {
        $quoteId = $this->checkoutSession->getQuote()->getId();  
        if ($quoteId) {            
                $itemOptionCount = count($result['totalsData']['items']);
                $quoteItems = $this->quoteItemRepository->getList($quoteId);
                $isbnOptions = array();
                foreach ($quoteItems as $index => $quoteItem) {
                    $quoteItemId = $quoteItem['item_id'];
                    $isbnOptions[$quoteItemId] = $quoteItem['isbn'];               
                }
                for ($i=0; $i < $itemOptionCount; $i++) {
                $quoteParentId = $result['totalsData']['items'][$i]['item_id'];
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                $base_url = $storeManager->getStore()->getBaseUrl();
                $productId = $result['quoteItemData'][$i]['product']['entity_id'];
                $productObj = $objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
                $productFlavours = $productObj->getResource()->getAttribute('dispatch_eta')->getFrontend()->getValue($productObj);
                $pro_price = $productObj->getFinalprice();
                $pro_fla = $productFlavours+5;
                $tomorrow_timestamp = strtotime("+ ".$pro_fla." day");
                $tomorrow_date = date("D, d M Y", $tomorrow_timestamp);
                $result['quoteItemData'][$i]['flavor'] = $tomorrow_date;
                $result['quoteItemData'][$i]['crice'] = $pro_price;
               $result['quoteItemData'][$i]['edit'] = $base_url.'checkout/cart';
                $result['quoteItemData'][$i]['remove'] = $base_url.'checkout/cart';
                json_encode($result);
                }
            }
        return $result;
    }

}