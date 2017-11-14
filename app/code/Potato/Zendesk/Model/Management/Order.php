<?php

namespace Potato\Zendesk\Model\Management;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Potato\Zendesk\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Model\UrlInterface;
use Potato\Zendesk\Model\Source\RedirectType;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Order
 */
class Order implements OrderManagementInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderItemRepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var AddressRenderer
     */
    protected $addressRenderer;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * Order constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderItemRepositoryInterface $orderItemRepository
     * @param CurrencyInterface $currency
     * @param AddressRenderer $addressRenderer
     * @param StoreManagerInterface $storeManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderItemRepositoryInterface $orderItemRepository,
        CurrencyInterface $currency,
        AddressRenderer $addressRenderer,
        StoreManagerInterface $storeManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        UrlInterface $urlBuilder,
        TimezoneInterface $localeDate
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->currency = $currency;
        $this->storeManager = $storeManager;
        $this->addressRenderer = $addressRenderer;
        $this->urlBuilder = $urlBuilder;
        $this->localeDate = $localeDate;
    }

    /**
     * @param string $orderIncrementId
     * @return array
     */
    public function getInfo($orderIncrementId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('increment_id', $orderIncrementId, 'eq')
            ->create();
        $orderList = $this->orderRepository->getList($searchCriteria)->getItems();
        $orderInfo = [];
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        foreach ($orderList as $order) {
            $billingAddress = $order->getBillingAddress();
            $shippingAddress = $order->getShippingAddress();
            
            if (!$shippingAddress) {
                $shippingAddress = $billingAddress;
            }

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('order_id', $order->getEntityId(), 'eq')
                ->addFilter('parent_item_id', new \Zend_Db_Expr('null'), 'is')
                ->create();
            $orderItemsList = $this->orderItemRepository->getList($searchCriteria)->getItems();

            $currency = $this->currency->getCurrency($order->getBaseCurrencyCode());
            $orderItemInfo = [];
            /** @var \Magento\Sales\Api\Data\OrderItemInterface $orderItem */
            foreach ($orderItemsList as $orderItem) {
                $orderItemInfo[] = [
                    'url' => $this->urlBuilder->getUrl('po_zendesk/index/redirect',
                        ['id' => $orderItem->getProductId(), 'type' => RedirectType::PRODUCT_TYPE]),
                    'product_id' => $orderItem->getProductId(),
                    'name' => $orderItem->getName(),
                    'sku' => $orderItem->getSku(),
                    'price' => $currency->toCurrency($orderItem->getBasePrice()),
                    'ordered_qty' => (int)$orderItem->getQtyOrdered(),
                    'invoiced_qty' => (int)$orderItem->getQtyInvoiced(),
                    'shipped_qty' => (int)$orderItem->getQtyShipped(),
                    'refunded_qty' => (int)$orderItem->getQtyRefunded(),
                    'row_total' => $currency->toCurrency($orderItem->getBaseRowTotal())
                ];
            }

            $orderInfo = [
                'url' => $this->urlBuilder->getUrl('po_zendesk/index/redirect',
                    ['id' => $order->getEntityId(), 'type' => RedirectType::ORDER_TYPE]),
                'order_id' => $order->getEntityId(),
                'increment_id' => $order->getIncrementId(),
                'store' => $this->storeManager->getStore($order->getStoreId())->getName(),
                'created_at' => $this->localeDate->formatDateTime($order->getCreatedAt(), \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::SHORT),
                'billing_address' => $this->addressRenderer->format($billingAddress, null),
                'shipping_address' => $this->addressRenderer->format($shippingAddress, null),
                'status' => $order->getStatusLabel(),
                'state' => $order->getState(),
                'totals' => [
                    'subtotal' => $currency->toCurrency($order->getBaseSubtotal()),
                    'shipping' => $currency->toCurrency($order->getBaseShippingAmount()),
                    'discount' => $currency->toCurrency($order->getBaseDiscountAmount()),
                    'tax' => $currency->toCurrency($order->getBaseTaxAmount()),
                    'grand_total' => $currency->toCurrency($order->getBaseGrandTotal())
                ],
                'items' => $orderItemInfo
            ];
        }
        return $orderInfo;
    }
}
