<?php
namespace Hiddentechies\Bizkick\Observer;
class CaseInvoice implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer){ 
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $invoice = $observer->getEvent()->getInvoice();
		$order = $invoice->getOrder();
		$orderIncrementId = $order->getIncrementId();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
			    $base_url = $storeManager->getStore()->getBaseUrl();
			    $spli = explode("//",$base_url);
			    $spli2 = explode(".",$spli[1]);
			    if($spli2[0] == "desiredesire"){
			    	$spli2[0] = "DD2";
			    }
        $_order = $objectManager->create('\Magento\Sales\Model\Order')->load($order->getId());
		$image = "https://desiredesire.com/skin/frontend/arw_sebian/default/images/media/logo.png";
		$ProdustSeller = array();
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
	     if(in_array($cmw, $ProdustSeller)){
         $storeAddress = "HANDPICK3D SOLUTIONS PRIVATE LIMITED, 7/1-1,Hosur Road,Flat D,Alsa Eaglerock Eagle St., Langford Town, Bangalore -560025";
	     }else{
	     $storeAddress = "Shashank Singal, SB Ecommerce Solution,1st Floor SCF 18, SEC11D,Behind Milan Hotel, Faridabad, 121002";
	     }
		$orderDate = $_order->getCreatedAt();
		$payment_method = $_order->getPayment()->getMethodInstance()->getTitle();
		$orderItems = $_order->getAllVisibleItems();
		if($_order->getPayment()->getMethodInstance()->getCode() == 'cashondelivery' ){
		$codamount = $_order->getGrandTotal();
		}else{
		$codamount="";
		}
		$shippingAddress = $_order->getShippingAddress();
		$billingAddress = $_order->getBillingAddress();
		$html = '<!DOCTYPE html>
			<html lang="en">
			  <head>
				<meta charset="utf-8">
				<style>
				.clearfix:after {
				  content: "";
				  display: table;
				  clear: both;
				}

				a {
				  color: #5D6975;
				  text-decoration: underline;
				}

				body {
				  position: relative;
				  width: 21cm;  
				  height: 29.7cm; 
				  margin: 0 auto; 
				  color: #001028;
				  background: #FFFFFF; 
				  font-family: Arial, sans-serif; 
				  font-size: 12px; 
				  font-family: Arial;
				}

				header {
				  padding: 10px 0;
				  margin-bottom: 30px;
				}

				#logo {
				  text-align: center;
				  margin-bottom: 10px;
				}

				#logo img {
				  width: 90px;
				}

				h1 {
				  border-top: 1px solid  #5D6975;
				  border-bottom: 1px solid  #5D6975;
				  color: #5D6975;
				  font-size: 2.4em;
				  line-height: 1.4em;
				  font-weight: normal;
				  text-align: center;
				  margin: 0 0 20px 0;
				  background: url(dimension.png);
				}

				#project {
				  float: left;
				}

				#project span {
				  color: #5D6975;
				  text-align: right;
				  width: 52px;
				  margin-right: 10px;
				  display: inline-block;
				  font-size: 0.8em;
				}

				#company {
				  float: right;
				  text-align: right;
				}

				#project div,
				#company div {
				  white-space: nowrap;        
				}

				table {
				  width: 100%;
				  border-collapse: collapse;
				  border-spacing: 0;
				  margin-bottom: 20px;
				}

				table tr:nth-child(2n-1) td {
				  background: #F5F5F5;
				}
				table th,
				table td {
				  text-align: center;
				}

				table th {
				  padding: 5px 20px;
				  color: #5D6975;
				  border-bottom: 1px solid #C1CED9;
				  white-space: nowrap;        
				  font-weight: normal;
				}

				table .service,
				table .desc {
				  text-align: left;
				}

				table td {
				  padding: 10px;
				}

				table td.service,
				table td.desc {
				  vertical-align: top;
				}

				table td.unit,
				table td.qty,
				table td.total {
				  /*font-size: 1.2em;*/
				}

				table td.grand {
				  border-top: 1px solid #5D6975;;
				}

				#notices .notice {
				  color: #5D6975;
				  font-size: 1.2em;
				}

				footer {
				  color: #5D6975;
				  width: 100%;
				  height: 30px;
				  position: absolute;
				  bottom: 0;
				  border-top: 1px solid #C1CED9;
				  padding: 8px 0;
				  text-align: center;
				}
				</style>
				
			  </head>
		<body>
				<header class="clearfix">
				<div id="logo" style="text-align:right; padding-right:10px;">
					<img src="'.$image.'" width="100" />
				</div>
				<div style="text-align:left;float:left;padding:10px 10px;width: 100%;" >
					<table>
					   <tbody>
						  <tr>
							  <td align="left" colspan="4">
								  <div><span>ORDER NO</span> :'.$orderIncrementId.'</div>
								  <div><span>DATE</span> :'.$orderDate.'</div>
							  </td>
							  <td align="right" colspan="4">
								'.$storeAddress.'
								<p>
									GSTIN:29AADCH6977K2ZS<br/>
									CIN:U72200KA2015PTC082672
								</p>
							  </td>
						  </tr>
					   </tbody>
					</table>
				</div>
				</header>
				<main>
				  <div style="clear:both;border-top:1px solid;margin-top:5px;border-bottom:1px solid;margin-bottom:10px;float:left;width:100%;padding:10px 10px;">
					  <div id="project" style="width:50%">
						<h3>Billing Address</h3>
						<div>'.$billingAddress->getName().'</div>
						<div>'.$billingAddress->getStreet()[0].'</div>
						<div>'.$billingAddress->getCity().','.$billingAddress->getRegion().'</div>
						<div>'.$billingAddress->getPostcode().','.$billingAddress->getCountryId().','.$billingAddress->getTelephone().'</div>
						
				  
					  </div>
					  
					  <div id="project" style="float:right;width:50%">
						<h3>Shipping Address</h3> 
						<div>'.$shippingAddress->getName().'</div>
						<div>'.$shippingAddress->getStreet()[0].'</div>
						<div>'.$shippingAddress->getCity().','.$shippingAddress->getRegion().'</div>
						<div>'.$shippingAddress->getPostcode().','.$shippingAddress->getCountryId().'<br/>Mobile:'.$shippingAddress->getTelephone().'</div>
				  
					  </div>
					 
					  
				  </div>	
				  <div style="padding:0px 10px;">
				  <table>
					<thead>
					  <tr>
							<th class="service" style="width:350px">ITEM NAME</th>
							<th class="sku" style="width:100px">SKU</th>
							<th class="hsn" style="width:100px">HSN CODE</th>
							<th style="width:50px">QTY</th>
							<th style="width:100px">GROSS AMT.</th>
							<th style="width:100px">NET AMT.(Tax Incl.)</th>
							<th style="width:100px">GST</th>
							<th style="width:100px">GST AMT.</th>
					  </tr>
					</thead>';
					$producLine = '';
					$subtotal = 0;
					$grandTotal = 0;
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
							
						   $producLine .= '<tr>
							<td class="service">'.$item->getName().'</td>
							<td class="sku">'.$item->getSku().'</td>
							<td class="hsn">HSNCODEEEEe</td>
							<td class="qty">'.(int)$item->getQtyOrdered().'</td>
							<td class="total">&#x20b9;'.$grossAmount.'</td>
							<td class="total">&#x20b9;'.$netAmount.'</td>
							<td class="total">&#x20b9;'.$tax.'%</td>
							<td class="total">&#x20b9;'.$taxAmount.'</td>
					  </tr>';			  
					}		
					$html .= '<tbody>'.
					 $producLine.'
					  
					   <tr>
						<td colspan="8" style="background: none;">&nbsp;</td>
						
					  </tr>
					  <tr>
						<td colspan="8" style="background: none;">&nbsp;</td>
						
					  </tr>
					  
					  <tr>
						<td colspan="7" align="right" class="grand total" style="background: none;">SUBTOTAL</td>
						<td class="grand total" align="right">&#x20b9;'.$grossTotalAmount.'</td>
					  </tr>';
					  $discount = $order->getDiscountAmount();
					  if($discount) {
							   $html .= '<tr>
								<td colspan="7" align="right" style="background: none;">DISCOUNT TOTAL</td>
								<td class="total" style="background: none;" align="right">&#x20b9;'.$discount.'</td>
							  </tr>';
							  
							  $netTotalAmount = $netTotalAmount - abs($discount);
							  
					  }
					  
					  if( $shippingAddress->getRegion() == "Karnataka"){
						$gstpercentage = $tax / 2;
						$gst = $totalTax / 2 ;
						
							$html .= '<tr>
								<td colspan="7" align="right" style="background: none;">CGST</td>
								<td class="total" align="right" style="background: none;">&#x20b9;'.$gst.'</td>
							</tr>';
							$html .= '<tr>
								<td colspan="7" align="right" style="background: none;">SGST</td>
								<td class="total" align="right" style="background: none;">&#x20b9;'.$gst.'</td>
							</tr>';
						 
						}else{
							$html .= '<tr>
								<td colspan="7" align="right" style="background: none;">IGST</td>
								<td class="total" align="right" style="background: none;">&#x20b9;'.$totalTax.'</td>
							</tr>';
						}
					  $html .= '
						  <tr>
							<td colspan="7" align="right" class="total" style="background: none;">GRAND TOTAL</td>
							<td class="total" style="background: none;" align="right">&#x20b9;'.$netTotalAmount.'</td>
						  </tr>
					</tbody>
				  </table>
				  </div>
				</main>
				<footer>
				  Invoice was created on a computer and is valid without the signature and seal.
				</footer>
			  </body>
			</html>';

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
		$rootPath  =  $directory->getRoot();
		$path_dir = $rootPath;
		$shipNamePdf = '/pub/media/invoices/invoice_'.$orderIncrementId.'.pdf';
	    try { 
	    	$result1 = count($ProdustSeller1);
			  require_once($rootPath.'/lib/internal/mpdf60/mpdf.php');
					$mpdf = new \mPDF('utf-8', 'A4-L');
                    $mpdf->WriteHTML($html);
					$mpdf->Output($path_dir.$shipNamePdf, 'F');				
		            $url= "http://api.b2b.casemyway.com/v1/Order/UploadOrderData/"; //live cmw url
		            //$url= "http://api.b2btest.casemyway.com/v1/Order/UploadOrderData/"; // test cmw url
				    $token= "0A688CAD8E14B689830C3669C1A1886F390C3998";
				    if($result1 == 1){
                 if(in_array($cmw1, $ProdustSeller1)){
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
					\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("atul is".$response);
					curl_close($request);
				}}
		    }catch (Exception $e){
			\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($e->getMessage());
		     }
	    }
}
?>