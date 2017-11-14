<?php

namespace Potato\Zendesk\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Config\Model\ResourceModel\Config;

/**
 * Class Authorization
 */
class Authorization
{
    const ZENDESK_CONFIG_API_TOKEN_PATH = 'potato_zendesk/general/token';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Authorization constructor.
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param LoggerInterface $logger
     * @param Config $config
     */
    public function __construct(
        ScopeConfigInterface $scopeConfigInterface,
        LoggerInterface $logger,
        Config $config
    ) {
        $this->scopeConfig = $scopeConfigInterface;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param null|array $postData
     * @return bool
     */
    public function isAuth($postData)
    {
        $result = true;
        if(null === $postData || !isset($postData['token'])) {
            $result = false;
            $this->logger->error('No authorisation token provided.');
            return $result;
        }
        $storeToken =  $this->scopeConfig->getValue(
            self::ZENDESK_CONFIG_API_TOKEN_PATH
        );
        if($postData['token'] != $storeToken) {
            $result = false;
            $this->logger->error('Authorisation failed.');
            return $result;
        }
        return $result;
    }

    /**
     * @return $this
     */
    public function createToken()
    {
        $token = md5(time());
        $this->config->saveConfig(self::ZENDESK_CONFIG_API_TOKEN_PATH, $token, 'default', 0);
        
        return $this;
    }
}
