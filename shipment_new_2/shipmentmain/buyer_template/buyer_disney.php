<?php 
$pdf->SetTitle("Disney");

// ============== pdf header ===================// 
$html .= <<<EOD
	<table>
	<tr>
		<th class="bold-text center-align">
			<h1>$letterhead_name</h1>
		</th>
		</tr>
		</table>

<br>
<br>
EOD;

$letterhead_title = strtoupper($letterhead_title);

$html .= <<<EOD
	<table border="0"  cellpadding="5">
		<tr>
			<th colspan="2" class="center-align">$letterhead_title</th>
			</tr>
		<tr>
			<td>Invoice Number: $invoice_no</td>
			</tr>
		<tr>
			<td>Vendor Reference Number:$byr_invoice_no</td>
			</tr>
	</table>
EOD;

$html .= <<<EOD
	<table border="1" cellpadding="5">
	<tr>
		<td>Revision No</td>
		<td>0</td>
		<td>Revised Date</td>
		<td></td>
		<td>Issue Date</td>
		<td>$invoice_date</td>
		<td>Due Date</td>
		<td></td>
		</tr>
	<tr>
		<td>PO Number</td>
		<td colspan="3">$allBuyerPO</td>
		<td>Packing List Number</td>
		<td colspan="3">$byr_invoice_no</td>
		</tr>
	<tr>
		<td>Goods Description</td>
		<td colspan="3"></td>
		<td>Country/Region of Origin</td>
		<td colspan="3">$manucountry</td>
		</tr>
	</table>
EOD;

$arr_array   = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_buyerpo = $arr_array["byBuyerPO"];
$grand_nw    = $arr_array["grand_inv_nw"];
$grand_gw    = $arr_array["grand_inv_gw"];
$grand_cbm   = $arr_array["grand_inv_cbm"];

$html .= <<<EOD
	&nbsp;<br/>
	<table cellpadding="5" >
	<tr>
		<td class="border_left_bold" >Seller</td>
		<td class="border_left_bold">Shipper</td>
		</tr>
	<tr>
		<td class="full-border">$ownership<br/>$owneraddress<br/>Tel: $ownertel &nbsp; &nbsp; Fax: $ownerfax</td>
		<td class="full-border">$shipper<br/>$shipper_addr<br/>Tel: $shipper_tel &nbsp; &nbsp; Fax: $shipper_fax</td>
		</tr>
		</table>
	&nbsp;<br/>
	<table cellpadding="5" >
	<tr>
		<td class="border_left_bold" colspan="2">Bill-To</td>
		<td class="border_left_bold" colspan="2">Payment</td>
		</tr>
	<tr>
		<td class="full-border" colspan="2" rowspan="3">
			$bill_to<br/>$bill_address<br/></td>
		<td class="full-border">
			INCOTERM<br/>
			$tradeterm
			</td>
		<td class="full-border">
			L/C Number<br/>
			$lc_number
			</td>
		</tr>
	<tr>
		<td class="full-border">
			Payment Term<br/>
			$paymentterm
			</td>
		<td class="full-border">
			L/C Issued by<br/>
			
			</td>
		</tr>
	<tr>
		<td class="full-border">
			Buying Currency<br/>
			$CurrencyCode
			</td>
		<td class="full-border">
			L/C Date<br/>
			$lc_date
			</td>
		</tr>
		</table>
	&nbsp;<br/>
	<table cellpadding="5">
	<tr>
		<td class="border_left_bold" colspan="4" >SHIPMENT</td>
		</tr>
	<tr>
		<td class="full-border">Ship From (POR)<br/>$portReceive</td>
		<td class="full-border">Ship To<br/>$buyerdest</td>
		<td class="full-border">Vessel/Voyage<br/>$vesselname</td>
		<td class="full-border" rowspan="5">Consignee<br/><br/>$conName<br/><br/>$conAddress</td>
		</tr>
	<tr>
		<td class="full-border">Load Port<br/>$portLoading</td>
		<td class="full-border">Discharge Port (POD)<br/>$portDischarges</td>
		<td class="full-border">Ship Via<br/>$shipmode</td>
		</tr>
	<tr>
		<td class="full-border">ETD at POL (Sailing Date)<br/>$shippeddate</td>
		<td class="full-border">ETA at POD<br/>$ETA</td>
		<td class="full-border">Forwarder</td>
		</tr>
	<tr>
		<td class="full-border">Total Gross Weight (KGS)<br/>$grand_gw</td>
		<td class="full-border">Total Volumn (CBM)<br/>$grand_cbm</td>
		<td class="full-border" rowspan="2">Way Bill Number</td>
		</tr>
	<tr>
		<td class="full-border" colspan="2">Container# / Size/Type / Seal#
		<br/>$container_no / / $seal_no</td>
		</tr>
		</table>
EOD;


$html .= <<<EOD
	&nbsp;<br/>
	<table cellpadding="5">
	<tr>
		<td class="border_left_bold border_btm" colspan="12">LINE ITEM</td>
		</tr>
	<tr>
		<td style="width:2%">#</td>
		<td style="width:10%">PO Manufacturer</td>
		<td style="width:10%">Buyer Item<br/>Color<br/>Size</td>
		<td style="width:10%">Description</td>
		<td style="width:10%">Description Details</td>
		<td style="width:12%">Marks and Numbers</td>
		<td style="width:8%">Unit Qty<br/>(EA)</td>
		<td style="width:8%">Package<br/>(CTN)</td>
		<td style="width:8%">Unit Price<br/>($CurrencyCode)</td>
		<td style="width:6%">Discount Rate<br/>%</td>
		<td style="width:8%">Discount Price<br/>($CurrencyCode)</td>
		<td style="width:8%">Subtotal Price ($CurrencyCode)</td>
		</tr>
EOD;

$count = 0;
$grand_ctn = 0;
$grand_qty = 0;
$grand_amt = 0;
foreach($arr_buyerpo as $buyerPO => $arr_value){
	$arr_all_size_ctn = $arr_value["arr_all_size_ctn"];
	
	//print_r($arr_all_size_ctn);
	
	foreach($arr_all_size_ctn as $colorsize => $arr_result){
		$count++;
		list($group_number, $size_name) = explode("**^^", $colorsize);
		$qty          = $arr_result["qty"];
		$color        = $arr_result["color"];
		$fob_price    = $arr_result["fob_price"];
		$total_ctn    = $arr_result["total_ctn"];
		$SKU          = $arr_result["SKU"];
		$prepack_name = $arr_result["prepack_name"];
		$quota        = $arr_result["quota"];
		$ht_code      = $arr_result["ht_code"];
		$upc_code     = $arr_result["upc_code"];
		$ship_marking = $arr_result["this_shipping_marking"];
		
		$this_amt = $fob_price * $qty;
		$grand_ctn += $total_ctn;
		$grand_qty += $qty;
		$grand_amt += $this_amt;
		
		$str_amt = number_format($this_amt, 2);
		
		$html .= '<tr>';
		$html .= '<td>'.$count.'</td>';
		$html .= '<td></td>';
		$html .= '<td>'.$SKU.'<br/>'.$color.'<br/>'.$size_name.'</td>';
		$html .= '<td>UPC: '.$upc_code.'<br/>HS Code: '.$ht_code.'<br/>CAT No. '.$quota.'</td>';
		$html .= '<td>'.$ship_marking.'</td>';
		$html .= '<td></td>';
		$html .= '<td>'.$qty.'</td>';
		$html .= '<td>'.$total_ctn.'</td>';
		$html .= '<td>'.$fob_price.'</td>';
		$html .= '<td></td>';
		$html .= '<td></td>';
		$html .= '<td align="right">'.$str_amt.'</td>';
		$html .= '</tr>';
	}
	
}//--- End foreach Buyer PO ---//

$str_grand_amt = number_format($grand_amt, 2);
$str_other_charge = number_format($other_charge, 2);

$html .= '<tr>';
$html .= '<td class="border_top" colspan="5"></td>';
$html .= '<td class="border_top">TOTAL</td>';
$html .= '<td class="border_top">'.$grand_qty.'</td>';
$html .= '<td class="border_top">'.$grand_ctn.'</td>';
$html .= '<td class="border_top"></td>';
$html .= '<td class="border_top"></td>';
$html .= '<td class="border_top"></td>';
$html .= '<td class="border_top" align="right">'.$str_grand_amt.'</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td class="border_btm" colspan="5"></td>';
$html .= '<td class="border_btm">Additional Charge</td>';
$html .= '<td class="border_btm"></td>';
$html .= '<td class="border_btm"></td>';
$html .= '<td class="border_btm"></td>';
$html .= '<td class="border_btm"></td>';
$html .= '<td class="border_btm"></td>';
$html .= '<td class="border_btm" align="right">'.$str_other_charge.'</td>';
$html .= '</tr>';

$grand_amt += $other_charge;
$str_grand_amt = number_format($grand_amt, 2);

$html .= '<tr>';
$html .= '<td class="" colspan="5"></td>';
$html .= '<td class="">Grand Total</td>';
$html .= '<td class=""></td>';
$html .= '<td class=""></td>';
$html .= '<td class=""></td>';
$html .= '<td class=""></td>';
$html .= '<td class=""></td>';
$html .= '<td class="" align="right">'.$str_grand_amt.'</td>';
$html .= '</tr>';

$split_total = explode(".", $grand_amt);
$dollar = strtoupper($handle_finance->convert_number($split_total[0]));
$cent   = strtoupper($handle_finance->convert_number($split_total[1]));

$html .= '<tr>';
$html .= '<td colspan="12" align="right">'.$CurrencyCode.' '.$dollar.' AND CENTS '.$cent.' ONLY</td>';
$html .= '</tr>';
$html .= '</table>';


$html .= '<table cellpadding="5">';
$html .= '<tr>';
$html .= '<td class="border_left_bold border_btm" >Manufacturer</td>';
$html .= '<td class="border_left_bold border_btm">Shipping Marks and Numbers</td>';
$html .= '<td class="border_left_bold border_btm">Remarks</td>';
$html .= '</tr>';
$html .= '</table>';

$html .= '&nbsp;<br/><table cellpadding="5">';
$html .= '<tr>';
$html .= '<td class="full-border">'.$manufacturer.'<br/>'.$manuaddress.'</td>';
$html .= '<td class="full-border">QTY: '.$grand_qty.' <br/>GROSS WEIGHT: '.$grand_gw.' KGS<br/>NET WEIGHT: '.$grand_nw.' KGS<br/>MADE IN '.$manucountry.'</td>';
$html .= '<td class="full-border">'.$remarks.'</td>';
$html .= '</tr>';
$html .= '</table>';




?>