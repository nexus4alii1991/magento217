<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SocialLogin
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\SocialLogin\Helper;

use Mageplaza\SocialLogin\Helper\Data as HelperData;
use Razorpay\Api\Api;
use Razorpay\Api\Request;




/**
 * Class Social
 * @package Mageplaza\SocialLogin\Helper
 */
class Social extends HelperData
{
	/**
	 * @type
	 */
	protected $_type;

	/**
	 * @param null $type
	 * @return null
	 */
	public function setType($type)
	{
		$listTypes = $this->getSocialTypes();
		if (!$type || !array_key_exists($type, $listTypes)) {
			return null;
		}

		$this->_type = $type;

		return $listTypes[$type];
	}

	/**
	 * @return array
	 */
	public function getSocialTypes()
	{
		return [
			'facebook'   => 'Facebook',
			'google'     => 'Google',
			'twitter'    => 'Twitter',
			'amazon'     => 'Amazon',
			'linkedin'   => 'LinkedIn',
			'yahoo'      => 'Yahoo',
			'foursquare' => 'Foursquare',
			'vkontakte'  => 'Vkontakte',
			'instagram'  => 'Instagram',
			'github'     => 'Github'
		];
	}

	/**
	 * @param $type
	 * @return array
	 */
	public function getSocialConfig($type)
	{
		$apiData = [
			'Facebook'  => ["trustForwarded" => false, 'scope' => 'email, user_about_me'],
			'Twitter'   => ["includeEmail" => true],
			'LinkedIn'  => ["fields" => ['id', 'first-name', 'last-name', 'email-address']],
			'Vkontakte' => ['wrapper' => ['class' => '\Mageplaza\SocialLogin\Model\Providers\Vkontakte']],
			'Instagram' => ['wrapper' => ['class' => '\Mageplaza\SocialLogin\Model\Providers\Instagram']],
			'Github'    => ['wrapper' => ['class' => '\Mageplaza\SocialLogin\Model\Providers\GitHub']],
			'Amazon'    => ['wrapper' => ['class' => '\Mageplaza\SocialLogin\Model\Providers\Amazon']]
		];

		if ($type && array_key_exists($type, $apiData)) {
			return $apiData[$type];
		}

		return [];
	}

	/**
	 * @return array|null
	 */
	public function getAuthenticateParams($type)
	{
		return null;
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function isEnabled($storeId = null)
	{
		return $this->getConfigValue("sociallogin/{$this->_type}/is_enabled", $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getAppId($storeId = null)
	{
		return $this->getConfigValue("sociallogin/{$this->_type}/app_id", $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getAppSecret($storeId = null)
	{
		return $this->getConfigValue("sociallogin/{$this->_type}/app_secret", $storeId);
	}

	/**
	 * @param $type
	 * @return string
	 */
	public function getAuthUrl($type)
	{
		$authUrl = $this->getBaseAuthUrl();

		$type = $this->setType($type);
		switch ($type) {
			case 'Facebook':
				$param = 'hauth_done=' . $type;
				break;
			case 'Live':
				$param = null;
				break;
			default:
				$param = 'hauth.done=' . $type;
		}

		return $authUrl . ($param ? (strpos($authUrl, '?') ? '&' : '?') . $param : '');
	}

	/**
	 * @return string
	 */
	public function getBaseAuthUrl()
	{
		return $this->_getUrl('sociallogin/social/callback', array('_nosid' => true, '_scope' => $this->getScopeUrl()));
	}

	/**
	 * @return int
	 */
	protected function getScopeUrl()
	{
		$scope = $this->_request->getParam(\Magento\Store\Model\ScopeInterface::SCOPE_STORE) ?: $this->storeManager->getStore()->getId();

		if ($website = $this->_request->getParam(\Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE)) {
			$scope = $this->storeManager->getWebsite($website)->getDefaultStore()->getId();
		}

		return $scope;
	}

	public function refund($orderId,$itemId,$itemPrice,$comments)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('sales_order_item_canceled');
        $orderDetail = $objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        $razorpayKeyID = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/razorpay/key_id');
        $razorpayKeySecret = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/razorpay/key_secret');
   
			if ($orderDetail->canCancel()) {
			    $orderItems = $orderDetail->getAllItems();       
			   	$itemCount = 0;
			   	$canceledItems = 0;
			    foreach ($orderItems as $value) {
			    $product = $value->getProduct();
				$parent = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($product->getId());
			    
			        $product = $value->getProduct();
							$parent = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($product->getId());
							
							if(($value->getProductType() == 'simple') && isset($parent[0])){
								continue;
			        		}
			        if($value->getId() == $itemId){ 
			           $value->setQtyCanceled($value['qty_ordered']);
			           $value->save();
			           $orderDetail->save(); 
			           		if(($value->getProductType() == 'simple') && isset($parent[0])){
								continue;
						    }else{
						    	$itemCount++;
						    	if($value->getStatus() == "Canceled"){
						        $canceledItems++;
						    	}
						    } 
			        }else{
			         continue;
			        }
			        
			    }
			    /*echo $itemCount;
			    echo $canceledItems;exit;*/
			    /*if($itemCount == $canceledItems){
			    	$orderDetail->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED);
					$orderDetail->save();
			    }*/
			   
			    if($orderDetail->save()){ 
				$sql = "Insert Into " . $tableName . " (order_id, item_id, qty_canceled, comments) Values (".$orderId.",".$itemId.",".$value['qty_ordered'].",'".$comments."')";
				$connection->query($sql);
				$select = $connection->select()
			    ->from(
			        ['ce' => 'sales_payment_transaction'],
			        ['txn_id']
			    )
			    ->where('order_id = '.$orderId.''); 
				$data = $connection->fetchAll($select);
				if(!empty($data)){ 
				$txn_id = $data[0]['txn_id'];
				$api = new Api($razorpayKeyID, $razorpayKeySecret);
				$refundAmount = round($itemPrice * 100 * $value['qty_ordered']);
				if($refundAmount != 0){
				$payment = $api->payment->fetch($txn_id);
				$refund = $payment->refund(array('amount' => $refundAmount )); //for partial refund
				}
				}
				}
			}

	}
}