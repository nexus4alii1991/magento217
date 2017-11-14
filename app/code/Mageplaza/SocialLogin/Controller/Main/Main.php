<?php 

namespace Mageplaza\SocialLogin\Controller\Main;
 
 
class Main extends \Magento\Framework\App\Action\Action {

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
	    $mobile = $this->getRequest()->getPost('mobile');
		$otp = $this->getRequest()->getPost('otp');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
        $sql = "Select mobile FROM otp_verification where mobile='".$mobile."' AND otp=".$otp;
        $result = $connection->fetchAll($sql);
		if(empty($result)){
		echo "not matched";
		}else{	
		echo "matched";
		}
    }
}
?>