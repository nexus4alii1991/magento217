<?php

namespace Potato\Zendesk\Api;

/**
 * @api
 */
interface CustomerManagementInterface
{
    /**
     * @param string $email
     * @return array
     */
    public function getInfo($email);

    /**
     * @param string $email
     * @param string $incrementId
     * @return array
     */
    public function getInfoFromOrder($email, $incrementId);
}
