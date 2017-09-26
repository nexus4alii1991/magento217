<?php
namespace Hiddentechies\Bizkick\Observer;
class CaseInvoice implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer){ 
        $invoice = $observer->getEvent()->getInvoice();
		$order = $invoice->getOrder();
		$orderIncrementId = $order->getIncrementId();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_order = $objectManager->create('\Magento\Sales\Model\Order')->load($order->getId());
		$image = "https://desiredesire.com/skin/frontend/arw_sebian/default/images/media/logo.png";
		$storeAddress =  "ANDPICK3D SOLUTIONS PRIVATE LIMITED, 7/1-1 HOSUR ROAD, FLAT D, ALSA EAGLEROCK, EAGLE STREET, LANGFORD TOWN, BENGALURU - 560025. INDIA";
		$orderDate = $_order->getCreatedAt();
		$payment_method = $_order->getPayment()->getMethodInstance()->getTitle();
		$orderItems = $_order->getAllVisibleItems();
		if($_order->getPayment()->getMethodInstance()->getCode() == 'cashondelivery' ){
		$codamount = $_order->getGrandTotal();
		}else{
		$codamount="";
		}
		$_shippingAddress = $_order->getShippingAddress();
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
								    <b>Order ID :'.$_order->getRealOrderId().'</b>
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
							<b>'.$_shippingAddress->getFirstname().$_shippingAddress->getLastname().'</b><br />
							'.'129, 4th j cross, kasturinagar'.'<br />
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
							<b>AWB No: </b>
						</td>
						<td colspan="6" align="left" style="border-bottom:1px solid #000;"> 
							<barcode code="" type="C128A" size="1.8" height="1" /><br /><center></center>
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
					$totalTax = 0;
					$grossTotalAmount = 0;
					$netTotalAmount = 0;
					foreach ($orderItems as $item){
						$row_total = $item->getQtyOrdered() * $item->getPrice();	
						$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
						if($product->getTaxPercent()){
						$tax = $product->getTaxPercent();
						}else{
						$tax = 0;
						}
						$netAmount = $item->getQtyOrdered() * $item->getPrice();
						
						$grossAmount = $netAmount * 100/(100 + $tax);
						
						$taxAmount = $netAmount - $grossAmount;
						$totalTax += $taxAmount;
						$grossTotalAmount += $grossAmount;
						$netTotalAmount = $netTotalAmount + $netAmount;
					
						
						$html .= '<tr id="product-details" style="border-top:2px solid #000; border-bottom:2px solid #000;">
							<td style="width:20%;" align="center">'.$item->getName().'</td>
							<td style="width:20%;" align="center">'.$item->getSku().'</td>
							<td style="width:20%;" align="center">HSNCODEEEEe</td>
							<td style="width:10%;" align="center">'.(int)$item->getQtyOrdered().'</td>
							<td style="width:15%;" align="center">&#x20b9;'.$grossAmount.'</td>
							<td style="width:15%;" align="center">('.$tax.'%)<br/>&#x20b9;'.$taxAmount.'</td>
							<td style="width:20%;" align="center">&#x20b9;'.$row_total.'</td>
						</tr>';	
                    }						
						$html .='</tbody>
				<tfoot id="totals-datails" style="border-top:2px solid #000; border-bottom:2px solid #000;">
					<tr>
						<td colspan="4" align="left" style="width:50%; border-top:2px solid #000;" ><b>Gross Total</b></td>
						<td colspan="3" align="right" style="width:50%; border-top:2px solid #000;"><b>&#x20b9;'.$grossTotalAmount.'</b></td>
					</tr>
					<tr>
						<td colspan="4" align="left" style="width:50%;"><b>Discount</b></td>
						<td colspan="3" align="right" style="width:50%;"><b>&#x20b9;'.$_order->getDiscountAmount().'</b></td>
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
						GSTIN : 29AADCH6977K2ZS <br />
						CIN NO : U72200KA2015PTC082672 <br />
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
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
		$rootPath  =  $directory->getRoot();
		$path_dir = $rootPath;
		$shipNamePdf = '/pub/media/invoices/invoice_'.$orderIncrementId.'.pdf';
	    try { 
		
			  require_once($rootPath.'/lib/internal/mpdf60/mpdf.php');
					$mpdf = new \mPDF('utf-8', 'A4-L');
                    $mpdf->WriteHTML($html);
					$mpdf->Output($path_dir.$shipNamePdf, 'F');				
		            $url= "http://api.b2btest.casemyway.com/v1/Order/UploadOrderData/";
				    $token= "0A688CAD8E14B689830C3669C1A1886F390C3998";
					$request = curl_init($url.$orderIncrementId);
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
					curl_close($request);
		    }catch (Exception $e){
		     }
	    }
}
?>