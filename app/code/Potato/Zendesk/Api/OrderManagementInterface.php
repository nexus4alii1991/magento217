<?php

namespace Potato\Zendesk\Api;

/**
 * @api
 */
interface OrderManagementInterface
{
    /**
     * @param int $orderIncrementId
     * @return array
     */
    public function getInfo($orderIncrementId);
}
