<?php

namespace Potato\Zendesk\Api;

/**
 * @api
 */
interface OrderRecentManagementInterface
{
    /**
     * @param string $email
     * @return array
     */
    public function getInfo($email);
}
