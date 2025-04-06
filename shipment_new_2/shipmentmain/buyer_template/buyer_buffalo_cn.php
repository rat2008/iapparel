<?php 

//$pdf->SetFont('times', '', 6, '', false);
$pdf->SetTitle("BUFFALO - $title");
// ============== pdf header ===================//
$html .= <<<EOD
<table border="0">
	<tr>
		<th class="bold-text center-align">
			<h1>$letterhead_name</h1>
		</th>
	</tr>

	<tr>
		<td class="center-align">
			$letterhead_address
		</td>
	</tr>

	<tr>
		<td class="center-align">
			TEL : $letterhead_tel &nbsp;&nbsp;&nbsp;&nbsp;FAX : $letterhead_fax
		</td>
	</tr>

</table>

<br>
<br>

EOD;

$invoice_date = date_format(date_create($invoice_date), "d-M-Y");
$shippeddate  = date_format(date_create($shippeddate), "M d,Y");

$arr_orderno = explode(",", $orderno);
$soID = $arr_orderno[0];
//$mode = 1;
// $stmt = $handle_class->getSizeNameColumnFromOrder($soID, $mode);
// $count_size = $stmt->rowCount();
// $colspan      = 8 + $count_size;
// $colspan_left = 5 + $count_size;

$html .= '<style>
			.all_border{
				border:1px solid #383838;
			}
			</style>';

$html .= <<<EOD
		<table border="0" class="" cellpadding="5">
			<tr>
				<th colspan="9" class="center-align">$letterhead_title </th>
				</tr>
			<tr>
				<td colspan="2">Invoice Number: $invoice_no</td>
				<td colspan="3">Date: $invoice_date</td>
				<td colspan="4"></td>
				</tr>
			<tr>
				<td colspan="5" class="all_border" style="width:70%">
					Manufacturer Full Name And Address:<br/>
					$manufacturer <br/>
					$manuaddress <br/>
					TEL:$manutel / FAX:$manufax
					</td>
				<td colspan="4" class="all_border" style="width:30%"></td>
				</tr>
			<tr>
				<td colspan="5" class="all_border" style="width:70%">
					Buyer / Sold To:<br/>
					$bill_to<br/>
					$bill_address
					
					</td>
				<td colspan="4" class="all_border" style="width:30%">
					Ship To:<br/>
					$ship_to <br/>
					$ship_address
					</td>
				</tr>
			<tr>
				<td colspan="5" class="all_border" style="width:70%">Payment Terms: $paymentterm</td>
				<td colspan="4" class="all_border" style="width:30%"></td>
				</tr>
			<tr>
				<td colspan="4" class="all_border" style="width:40%">Vessel / Flight: $shipmode</td>
				<td colspan="1" class="all_border" style="width:30%">ETD: $shippeddate</td>
				<td colspan="4" class="all_border" style="width:30%">
					Shipping Term: $tradeterm
				</td>
				</tr>
			<tr>
				<td colspan="4" style="width:40%" class="all_border">
					Ship From: $portLoading &nbsp; &nbsp; &nbsp; Ship To: $portDischarges</td>
				<td colspan="1" style="width:30%" class="all_border">Port: $portLoading</td>
				<td colspan="4" style="width:30%" class="all_border">Country Of Origin: $manucountry</td>
				</tr>
			<tr>
				<td align="center" class="all_border" style="width:10%">PO#</td>
				<td align="center" class="all_border" style="width:7%"># Of Cartons</td>
				<td align="center" class="all_border" style="width:13%">Material #</td>
				<td align="center" class="all_border" style="width:10%">Style #</td>
				<td align="center" class="all_border" style="width:30%">Style Description</td>
				<td align="center" class="all_border" style="width:8%"></td>
				<td align="center" class="all_border" style="width:6%">Qty</td>
				<td align="center" class="all_border" style="width:8%">Unit Price $CurrencyCode</td>
				<td align="center" class="all_border" style="width:8%">Amount / $CurrencyCode</td>
				</tr>
EOD;

// $arr_array    = $handle_class->getBuyerInvoiceDescriptionInfoMethod2($invID, $query_filter);
// $arr_fabric   = $arr_array["byFabric"];

$query_filter = ",bid.shipmentpriceID";
$arr_array  = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_fabric = $arr_array["byFabric"];

$grand_ctn = 0;
$grand_qty = 0;
$grand_amt = 0;
$uom = "";

foreach($arr_fabric as $shipping_marking => $arr_info){
	for($i=0;$i<count($arr_info);$i++){
		$BuyerPO   = $arr_info[$i]["BuyerPO"];//($i==0? $arr_info[$i]["BuyerPO"]:"");
		$total_ctn = $arr_info[$i]["total_ctn"];//($i==0? $arr_info[$i]["total_ctn"]:"");
		$styleNo   = $arr_info[$i]["styleNo"];//($i==0? $arr_info[$i]["styleNo"]:"");
		//$grand_qty = $arr_info[$i]["grand_qty"];
		$arr_FOB   = $arr_info[$i]["arr_FOB"];
		$uom       = $arr_info[$i]["uom"];
		
		//if($i==0){
			$grand_ctn += $total_ctn;
		//}
		
		foreach($arr_FOB as $unitprice => $arr){
			$this_qty = $arr["qty"];
			$this_up  = substr($unitprice, 1);
			$this_amt = $this_up * $this_qty;
			
			$grand_qty += $this_qty;
			$grand_amt += $this_amt;
			
			$html .= '<tr>';
			$html .= '<td align="center" class="all_border">'.$BuyerPO.'</td>';
			$html .= '<td align="center" class="all_border">'.$total_ctn.'</td>';
			$html .= '<td align="center" class="all_border"></td>';
			$html .= '<td align="center" class="all_border">'.$styleNo.'</td>';
			$html .= '<td align="center" class="all_border">'.$shipping_marking.'</td>';
			$html .= '<td align="center" class="all_border"></td>';
			$html .= '<td align="center" class="all_border">'.$this_qty.'</td>';
			$html .= '<td align="center" class="all_border">'.$this_up.'</td>';
			$html .= '<td align="center" class="all_border">'.$this_amt.'</td>';
			$html .= '</tr>';
			
		}//--- End Foreach arr_FOB ---//
	}//--- End For arr_info ---//
}//--- End Foreach arr_fabric ---//

$html .= '<tr>';
$html .= '<td class="all_border">&nbsp;</td>';
$html .= '<td class="all_border"></td>';
$html .= '<td class="all_border"></td>';
$html .= '<td class="all_border"></td>';
$html .= '<td class="all_border"></td>';
$html .= '<td class="all_border"></td>';
$html .= '<td class="all_border"></td>';
$html .= '<td class="all_border"></td>';
$html .= '<td class="all_border"></td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td align="center" class="all_border">TOTAL CARTON</td>';
$html .= '<td align="center" class="all_border">'.$grand_ctn.'</td>';
$html .= '<td class="all_border"></td>';
$html .= '<td class="all_border"></td>';
$html .= '<td class="all_border"></td>';
$html .= '<td class="all_border">TOTAL '.$uom.'</td>';
$html .= '<td class="all_border" align="center">'.$grand_qty.'</td>';
$html .= '<td class="all_border"></td>';
$html .= '<td class="all_border"></td>';
$html .= '</tr>';

$html .= <<<EOD
	</table>
EOD;

$split_total = explode(".", $grand_amt);
$dollar = strtoupper($handle_finance->convert_number($split_total[0]));
$cent   = strtoupper($handle_finance->convert_number($split_total[1]));

$str_words = strtoupper($cur_description." ".$dollar." AND ".$cent." CENTS ONLY.");

$html .= '<br/>';
$html .= '<br/>';
$html .= '<table width="100%" cellpadding="2">
			<tr>
				<td align="center" style="width:70%">IN WORDS: '.$str_words.'</td>
				<td align="center" class="all_border" style="width:22%">Grand Total - '.$CurrencyCode.'</td>
				<td align="center" class="all_border" style="width:8%">'.$grand_amt.'</td>
				</tr></table>';
				
$html .= '<br/>';
$html .= '<br/>';

$html .= '<table cellpadding="2" width="100%">';
$html .= '<tr>
			<td style="width:12%">Beneficiary Bank Info:</td> 
			<td></td>
			</tr>';
$html .= '<tr>
			<td >Bank Account Number:</td> 
			<td>'.$bank_account_no.'</td>
			</tr>';
$html .= '<tr>
			<td >Bank Name:</td> 
			<td>'.$bank_name.'</td>
			</tr>';
$html .= '<tr>
			<td>Bank Address:</td> 
			<td>'.$bank_address.'</td>
			</tr>';
$html .= '<tr>
			<td>Beneficiary Name:</td> 
			<td>'.$beneficiary_name.'</td>
			</tr>';
$html .= '<tr>
			<td>Swift Code:</td> 
			<td>'.$swift_code.'</td>
			</tr>';
$html .= '</table>';

//= =============================== packing list ====================================//
$this_html = $handle_lc->getBuyerInvoicePackingListTemplate4($invID);
$html .= $this_html;

?>