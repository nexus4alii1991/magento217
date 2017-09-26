<?php 

namespace Mageplaza\SocialLogin\Controller\Mobile;
 
 
class Mobile extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;

    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
	    $otp = rand(1000,9999);
	    $mobile = $this->getRequest()->getPost('mobile');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
        $sql = "Select value FROM customer_entity_text where attribute_id=155 AND value=".$mobile;
        $result = $connection->fetchAll($sql);
		if(empty($result)){
		echo 'not';
		}
		else{	
		$number  =  $mobile;
		$apiKey = urlencode('pNgD+phaB+0-QzhVw3TJlXLw7X9GcO8364Hrs7wqxJ');
		$sender = urlencode('DESIRE');
		$message = "OTP for sign in is $otp";
		$message = urlencode($message);
		$data = 'apikey=' . $apiKey . '&numbers=' . $number . "&sender=" . $sender . "&message=" . $message;
		// Send the POST request with cURL
		$ch = curl_init('https://api.textlocal.in/send/?' . $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch); 
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($response);
		curl_close($ch);
		$sql2 = "Select mobile FROM otp_verification where mobile=".$mobile;
        $result2 = $connection->fetchAll($sql2);
		if(empty($result2)){
		$sql = "Insert Into otp_verification (mobile, otp) Values ('".$mobile."','".$otp."')";
        if($connection->query($sql)){
		echo 'sent';
		}
		}else{
		$sql = "UPDATE otp_verification SET otp = '".$otp."' WHERE mobile=".$mobile;
        if($connection->query($sql)){
		echo 'sent';
		}	
		}
		
		}
    }
}
?>