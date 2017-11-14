<?php

namespace Potato\Zendesk\Model\Management;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Potato\Zendesk\Api\OrderRecentManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Model\UrlInterface;
use Potato\Zendesk\Model\Source\RedirectType;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * Class OrderRecent
 */
class OrderRecent implements OrderRecentManagementInterface
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
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * OrderRecent constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderItemRepositoryInterface $orderItemRepository
     * @param CurrencyInterface $currency
     * @param StoreManagerInterface $storeManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param UrlInterface $urlBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param FilterBuilder $filterBuilder
     * @param TimezoneInterface $localeDate
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderItemRepositoryInterface $orderItemRepository,
        CurrencyInterface $currency,
        StoreManagerInterface $storeManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        UrlInterface $urlBuilder,
        CustomerRepositoryInterface $customerRepository,
        FilterBuilder $filterBuilder,
        TimezoneInterface $localeDate,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->currency = $currency;
        $this->urlBuilder = $urlBuilder;
        $this->customerRepository = $customerRepository;
        $this->filterBuilder = $filterBuilder;
        $this->localeDate = $localeDate;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @param string $email
     * @return array
     */
    public function getInfo($email)
    {
        //get customers by email
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('email', $email, 'eq')
            ->create();
        $customerList = $this->customerRepository->getList($searchCriteria)->getItems();
        $customerIds = [];
        foreach ($customerList as $customer) {
            $customerIds[] = $customer->getId();
        }

        $customerEmailFilter = $this->filterBuilder
            ->setField('customer_email')
            ->setConditionType('eq')
            ->setValue($email)
            ->create();
        
        $customerIdFilter = $this->filterBuilder
            ->setField('customer_id')
            ->setConditionType('in')
            ->setValue($customerIds)
            ->create();
        $sortOrder = $this->sortOrderBuilder
            ->setField('created_at')
            ->setDescendingDirection()
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilters([$customerEmailFilter, $customerIdFilter])
            ->addSortOrder($sortOrder)
            ->create();
        $orderList = $this->orderRepository->getList($searchCriteria)->getItems();
        $orderInfo = [];
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        foreach ($orderList as $order) {
            $currency = $this->currency->getCurrency($order->getBaseCurrencyCode());

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('order_id', $order->getEntityId(), 'eq')
                ->addFilter('parent_item_id', new \Zend_Db_Expr('null'), 'is')
                ->create();
            $orderItemsList = $this->orderItemRepository->getList($searchCriteria)->getItems();

            $orderItemInfo = [];
            /** @var \Magento\Sales\Api\Data\OrderItemInterface $orderItem */
            foreach ($orderItemsList as $orderItem) {
                $orderItemInfo[] = [
                    'url' => $this->urlBuilder->getUrl('po_zendesk/index/redirect',
                        ['id' => $orderItem->getProductId(), 'type' => RedirectType::PRODUCT_TYPE]),
                    'product_id' => $orderItem->getProductId(),
                    'name' => $orderItem->getName(),
                    'row_total' => $currency->toCurrency($orderItem->getBaseRowTotal()),
                    'ordered_qty' => (int)$orderItem->getQtyOrdered()
                ];
            }

            $orderInfo[] = [
                'url' => $this->urlBuilder->getUrl('po_zendesk/index/redirect',
                    ['id' => $order->getEntityId(), 'type' => RedirectType::ORDER_TYPE]),
                'order_id' => $order->getEntityId(),
                'increment_id' => $order->getIncrementId(),
                'status' => $order->getStatusLabel(),
                'state' => $order->getState(),
                'grand_total' => $currency->toCurrency($order->getBaseGrandTotal()),
                'created_at' => $this->localeDate->formatDateTime($order->getCreatedAt(), \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::NONE),
                'items' => $orderItemInfo
            ];
        }
        return $orderInfo;
    }
}
