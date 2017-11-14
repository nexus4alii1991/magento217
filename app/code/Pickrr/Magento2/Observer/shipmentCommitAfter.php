<?php
namespace Pickrr\Magento2\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Bootstrap;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Pickrr\Magento2\Helper\ExportShipment;
 
class shipmentCommitAfter implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
 
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ScopeConfigInterface $scopeConfig,
        ExportShipment $helper
    ) {
        $this->helper = $helper;
        $this->_objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
    }

    /* Shipment Label pdf function */

     public function getContent($order){

        $orderIncrementId = $order->getIncrementId();  
        $image = "https://desiredesire.com/skin/frontend/arw_sebian/default/images/media/logo.png";
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                $base_url = $storeManager->getStore()->getBaseUrl();
                $spli = explode("//",$base_url);
                $spli2 = explode(".",$spli[1]);
                if($spli2[0] == "desiredesire"){
                    $spli2[0] = "DD2";
                }

        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $_order = $objectManager->create('\Magento\Sales\Model\Order')->load($order->getId());
        $sql = "Select tracking_id FROM picker_shipping_info where Id=".$_order->getIncrementId();
        $result2 = $connection->fetchAll($sql);     
        $tracking_id = $result2[0]['tracking_id'];
        $orderDate = $_order->getCreatedAt();
        $payment_method = $_order->getPayment()->getMethodInstance()->getTitle();
        $ProdustSeller = array();
        $orderItems = $_order->getAllVisibleItems();
        foreach ($order->getAllItems() as $item){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
                $seller = $product->getData('seller');
                    $cmw = 62;
                if(!$seller == ""){
                    if(!in_array($seller, $ProdustSeller)){
                    $ProdustSeller[]= $seller;
                    }
                   }
        }
        if($_order->getPayment()->getMethodInstance()->getCode() == 'cashondelivery'){
        $codamount = $_order->getGrandTotal();
        }else{
        $codamount="";
        }
        $_shippingAddress = $_order->getShippingAddress();
        $_billingAddress = $_order->getBillingAddress();
         if(in_array($cmw, $ProdustSeller)){
         $storeAddress = "HANDPICK3D SOLUTIONS PRIVATE LIMITED, 7/1-1,Hosur Road,Flat D,Alsa Eaglerock Eagle St., Langford Town, Bangalore -560025";
         }else{
         $storeAddress = "Shashank Singal, SB Ecommerce Solution,1st Floor SCF 18, SEC11D,Behind Milan Hotel, Faridabad, 121002";
         }
            $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Print Invoice</title>
            <style>
                *
                {
                    margin:0;
                    padding:0;
                    font-family:Arial;
                    font-size:10pt;
                    color:#000;
                }
                body
                {
                    width:600px;
                    font-family:Arial;
                    font-size:10pt;
                    margin:0;
                    padding:0;
                }
                 
                p
                {
                    margin:0;
                    padding:0;
                }
                 
                #wrapper
                {
                    width:600px;
                    margin:0;
                }
         
                table
                {
                    border-spacing:0;
                    border-collapse: collapse; 
                     
                }
                 
                table td 
                {
                    padding: 2mm;
                }
                tbody tr {
                    border-top: 1px solid #000;
                }
                #totals-datails{
                    border-bottom: 2px solid #000;
                    border-top: 2px solid #000;
                }
            </style>
        </head>
        <body>
        <div id="wrapper">
            <table width="600">
                <thead>
                    <tr id="logo">
                        <td colspan="3"></td>
                        <td colspan="4" align="right"><img src="'.$image.'" width="100" /></td>
                    </tr>
                    <tr id="heading">
                        <td colspan="7" align="center"><strong style="text-decoration:underline">RETAIL INVOICE</strong></td>
                    </tr>
                    <tr id="order-details">
                        <td colspan="4" align="left" valign="middle" style="border-bottom:1px solid #000;"> 
                            <div class="ord" style="width:75%;">
                                <b>Order ID :'.$orderIncrementId.'</b>
                                
                            </div>
                        </td>
                        <td colspan="3" align="left" style="border-bottom:1px solid #000;">
                            <strong>'.$payment_method.'</strong>
                            <p>Order Date : '.$orderDate.'</p>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr id="shipping-datails">
                        <td colspan="1" valign="top" style="border-bottom:1px solid #000;">
                            <b>Ship To : </b>
                        </td>
                        <td colspan="6" style="border-bottom:1px solid #000;">
                            <b> '.$_shippingAddress->getFirstname().' '.$_shippingAddress->getLastname().'</b><br />
                            '.$_shippingAddress->getStreet()[0].'<br />
                            '.$_shippingAddress->getCity().','.$_shippingAddress->getRegion().'<br />
                            '.$_shippingAddress->getPostcode().'<br />
                            '.$_shippingAddress->getTelephone().'
                        </td>
                    </tr>
            
                    <tr class="shipping-datails">
                        <td colspan="1" valign="top" style="border-bottom:1px solid #000;">
                            <b>Payment Mode : </b>
                        </td>
                        <td colspan="6" style="border-bottom:1px solid #000;">
                            <span>'.$payment_method.'</span> <br/>
                            <span>Shipping Charges : &#x20b9;'.$_order->getShippingAmount().'</span>
                        </td>
                    </tr>
            
                    <tr class="awb-datails">
                        <td colspan="1" valign="top" style="border-bottom:1px solid #000;">
                            <b>AWB No:'.$tracking_id.'</b>
                        </td>
                        <td colspan="6" align="left" style="border-bottom:1px solid #000;"> 
                            <barcode code="'.$tracking_id.'" type="C128A" size="1.8" height="1" /><br /><center>'.$tracking_id.'</center>
                        </td>
                    </tr>
                    
                    <tr class="product-heading-details">
                        <td style="width:200px; border-bottom:1px solid #000;" align="center"><b> PRODUCT </b></td>
                        <td style="width:50px; border-bottom:1px solid #000;" align="center"><b> SKU </b></td>
                        <td style="width:50px; border-bottom:1px solid #000;" align="center"><b> HSN CODE </b></td>
                        <td style="width:50px; border-bottom:1px solid #000;" align="center"><b> QTY </b></td>
                        <td style="width:50px; border-bottom:1px solid #000;" align="center"><b> GROSS AMT. </b></td>
                        <td style="width:50px; border-bottom:1px solid #000;" align="center"><b> GST </b></td>
                        <td style="width:50px; border-bottom:1px solid #000;" align="center"><b> TOTAL </b></td>
                    </tr>';
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug('after html');        
                    $grossTotalAmount = 0;
                    $netTotalAmount = 0;
                    $totalTax = 0;
                    $ProdustSeller1 = array();
                    foreach ($order->getAllItems() as $item){
                            if ($item->getParentItem()) {
                                continue;
                            }
                            $netAmount = $item->getQtyOrdered() * $item->getPrice();
                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
                            $seller1 = $product->getData('seller');
                                $cmw1 = 61; //for casemyway use 61 & for desiredesire use 62
                            if(!$seller1 == ""){
                                if(!in_array($seller1, $ProdustSeller1)){
                                $ProdustSeller1[]= $seller1;
                                }
                               }
                            if($product->getTaxPercent()){
                            $tax = $product->getTaxPercent();
                            }else{
                            $tax = 0;
                            }
                            $taxAmount = $netAmount * ($tax/100);
                            $totalTax += $taxAmount;
                            $grossAmount  =  $netAmount - $taxAmount;
                        
                            $grossTotalAmount += $grossAmount;
                            $netTotalAmount += $netAmount;
                    
                        
                        $html .= '<tr id="product-details" style="border-top:2px solid #000; border-bottom:2px solid #000;">
                            <td style="width:20%;" align="center">'.$item->getName().'</td>
                            <td style="width:20%;" align="center">'.$item->getSku().'</td>
                            <td style="width:20%;" align="center">'.$product->getHsncode().'</td>
                            <td style="width:10%;" align="center">'.(int)$item->getQtyOrdered().'</td>
                            <td style="width:15%;" align="center">&#x20b9;'.$grossAmount.'</td>
                            <td style="width:15%;" align="center">('.$tax.'%)<br/>&#x20b9;'.$taxAmount.'</td>
                            <td style="width:20%;" align="center">&#x20b9;'.$row_total.'</td>
                        </tr>';         
                    }
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug('name is'.$item->getName());
                $html .='</tbody>
                
                <tfoot id="totals-datails" style="border-top:2px solid #000; border-bottom:2px solid #000;">
                    <tr>
                        <td colspan="4" align="left" style="width:50%; border-top:2px solid #000;" ><b>Gross Total</b></td>
                        <td colspan="3" align="right" style="width:50%; border-top:2px solid #000;"><b>&#x20b9;'.$grossTotalAmount.'</b></td>
                    </tr>
                    <tr>
                        <td colspan="4" align="left" style="width:50%;"><b>Discount</b></td>
                        <td colspan="3" align="right" style="width:50%;"><b>&#x20b9;'.$order->getDiscountAmount().'</b></td>
                    </tr>';
                    
                    if( $_shippingAddress->getRegion() == 'Karnataka'){
                        $gst = $totalTax / 2 ;
                        
                        $html .='<tr>
                            <td colspan="4" align="left" style="width:50%;">CGST</td>
                            <td colspan="3" align="right" style="width:50%;">&#x20b9;'.$gst.'</td>
                        </tr>
                        <tr>
                            <td colspan="4" align="left" style="width:50%;">SGST</td>
                            <td colspan="3" align="right" style="width:50%;">&#x20b9;'.$gst.'</td>
                        </tr>';
                        }else{
                        $html .='<tr>
                            <td colspan="4" align="left" style="width:50%;">IGST</td>
                            <td colspan="3" align="right" style="width:50%;">&#x20b9;'.$totalTax.'</td>
                        </tr>';
                        }
                    $html .='<tr>
                        <td colspan="4" align="left" style="width:50%;  border-bottom:2px solid #000;"><b>Net Total</b></td>
                        <td colspan="3" align="right" style="width:50%;  border-bottom:2px solid #000;"><b>&#x20b9;'.$netTotalAmount.'</b></td>
                    </tr>
                </tfoot>
            </table>
            <table width="600">
                <tr>
                    <td style="width:45%;" valign="top">
                        GSTIN:29AADCH6977K2ZS<br/>
                        CIN:U72200KA2015PTC082672
                    </td>
                    
                    
                    <td style="width:45%;" valign="top">
                        <b>Return Address :</b><br />
                        '.$storeAddress.'
                    </td>
                </tr>
                <tr><td style="font-size:10pt;" colspan="2" align="center">This is a computer generated Invoice. Does not require signature</td></tr>
            </table>
             
        </body>
        </html>';
    \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug('final address is'.$storeAddress);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $rootPath  =  $directory->getRoot();
        $path_dir = $rootPath;
        $shipNamePdf = '/pub/media/shipment/shipment'.$orderIncrementId.'.pdf';
        try { 

            $result1 = count($ProdustSeller1);
    \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug('count is'.$result1);
              require_once($rootPath.'/lib/internal/mpdf60/mpdf.php');
                    $mpdf = new \mPDF('utf-8', 'A4-L');
                    $mpdf->WriteHTML($html);
                    $mpdf->Output($path_dir.$shipNamePdf, 'F');             
                    $url= "http://api.b2b.casemyway.com/v1/Order/UploadOrderData/";
                    //$url= "http://api.b2btest.casemyway.com/v1/Order/UploadOrderData/";
                    $token= "0A688CAD8E14B689830C3669C1A1886F390C3998";
                    if($result1 == 1){
                 if(in_array($cmw1, $ProdustSeller1)){
    \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug('its cmw');
                    $request = curl_init($url.$spli2[0].$order->getIncrementId());
                    $cfile = new \CurlFile($path_dir.$shipNamePdf,'application/pdf',basename($shipNamePdf));
                    curl_setopt($request, CURLOPT_POST, true);
                    curl_setopt(
                        $request,
                        CURLOPT_POSTFIELDS,
                        array(
                          'file' => $cfile //'@' . realpath($shipNamePdf)
                        )
                    );
                    curl_setopt($request, CURLOPT_SAFE_UPLOAD, true);
                    curl_setopt($request, CURLOPT_HTTPHEADER, array('X-Token:'.$token,'Content-type: multipart/form-data'));
                    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
                    $response =  curl_exec($request);
                    \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($response);
                    curl_close($request);
                }}
            }catch (Exception $e){
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($e->getMessage());
             }
    }


 
    /**
     * customer register event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ("0" == $this->scopeConfig->getValue('pickrr/automatic_export_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
          return NULL;

        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $this->helper->export($shipment);
        $this->getContent($order);
    }

   
}