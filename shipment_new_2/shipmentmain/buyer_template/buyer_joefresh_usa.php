<?php 
$pdf->SetTitle("Joe Fresh");

$letterhead_title = strtoupper($letterhead_title);

$html = <<<EOD
<style>
	.bold-text {
		font-weight: bold;
	}

	.center-align{
		text-align: center;
	}

	p.p-format {
		white-space: pre-wrap; 	
	}

	table td{
		/*font-size: 10px;*/ 
	}

	table th{
		/*font-size: 10px;*/
	}

	.table-bordered th, 
	.table-bordered td {
		border: 1px solid black;
	}
	.tb_joefresh th{
		border: 1px solid black;
	}
	
	td.all_border{
		border: 1px solid black;
	}
	td.top_border{
		border-top: 1px solid black;
	}
	td.left_border{
		border-left: 1px solid black;
	}
	td.right_border{
		border-right: 1px solid black;
	}
	td.bottom_border{
		border-bottom: 1px solid black;
	}

	.font-red {
		color: red;
	}

	.font-blue {
		color: blue;
	}

	.full-border {
		border: 1px solid black;
	}
	.border_btm, .border-b {
		border-bottom: 1px solid black;
	}
	.border_top, .border-t {
		border-top: 1px solid black;
	}
	.border_right, .border-r {
		border-right: 1px solid black;
	}
	.border_left, .border-l {
		border-left: 1px solid black;
	}
	.border-rl, .border-lr {
		border-left: 1px solid black;
		border-right: 1px solid black;
	}
	.border_left_bold {
		border-left: 3px solid black;
	}


	.dashedborder_btm {
		border-bottom: 1px dashed black;
	}
	.dashedborder_top {
		border-top: 1px dashed black;
	}
	.dashedborder_right {
		border-right: 1px dashed black;
	}
	.dashedborder_left {
		border-left: 1px dashed black;
	}

	
</style>
EOD;

$str_transit_port = ($transitPort==""? "":" VIA $transitPort");

$notify_tel   = (trim($notifyTel)==""? "":"<br/>Tel: $notifyTel");
$notify_fax   = (trim($notifyFax)==""? "":"<br/>Fax: $notifyFax");
$notify_email = (trim($notifyEmail)==""? "":"<br/>Email: $notifyEmail");

// ============== pdf header ===================// 
$html .= <<<EOD
	<table border="0" cellpadding="2">
	<tr>
		<td class="center-align" colspan="2"><u>$letterhead_title</u></td>
		</tr>
	<tr>
		<td class="center-align all_border">Vendor (Name and Address)</td>
		<td class="center-align all_border">Manufacturer (Name and Address)</td>
		</tr>
	<tr>
		<td class="all_border">
			$ownership<br/>
			$owneraddress<br/>
			TEL: $ownertel<br/>
			FAX: $ownerfax
		</td>
		<td class="all_border">
			$manufacturer<br/>
			$manuaddress<br/>
			TEL: $manutel &nbsp; &nbsp; FAX: $manufax
		</td>
		</tr>
		</table>
	
	<br/><br/>
	<table cellpadding="2">
	<tr>
		<td class="border_left border_top border_btm">Invoice Number: </td>
		<td class="border_top border_btm border_right">$invoice_no</td>
		<td class="border_left border_top border_btm">Reference: </td>
		<td class="border_top border_btm border_right"></td>
		<td class="border_left border_top border_btm">Invoice Date: </td>
		<td class="border_top border_btm border_right">$invoice_date</td>
		</tr>
	<tr>
		<td class="border_left border_top border_btm">Country of Origin: </td>
		<td class="border_top border_btm border_right">$manucountry</td>
		<td class="border_left border_top border_btm">Discharge Port: </td>
		<td class="border_top border_btm border_right">$portDischarges</td>
		<td class="border_left border_top border_btm">Ship Date: </td>
		<td class="border_top border_btm border_right">$shippeddate</td>
		</tr>
	<tr>
		<td class="border_left border_top border_btm">Condition of Sale: </td>
		<td class="border_top border_btm">$tradeterm</td>
		<td class="border_top border_btm border_right"></td>
		<td class="border_left border_top border_btm">Transhipment Country: </td>
		<td class="border_top border_btm"></td>
		<td class="border_top border_btm border_right">$buyerdest</td>
		</tr>
	<tr>
		<td class="border_left border_top border_btm">Transportation: </td>
		<td class="border_top border_btm border_right" colspan="3">BY $shipmode FROM $portLoading $str_transit_port TO $buyerdest</td>
		<td class="border_left border_top border_btm">Currency: </td>
		<td class="border_top border_btm border_right">$CurrencyCode ($cur_description)</td>
		</tr>
		</table>
	
	<br/><br/>
	<table cellpadding="2">
	<tr>
		<td class="all_border">Consignee</td>
		<td class="all_border">Buyer/Purchaser</td>
		<td class="all_border">Ship To/Delivery</td>
		<td class="all_border">Shipper/Exporter</td>
		</tr>
	<tr>
		<td class="all_border">$conName<br/>$conAddress<br/>EIN:$conEIN<br/>Tel # $contel &nbsp;<br/>Fax No. $confax</td>
		<td class="all_border">$conName<br/>$conAddress<br/>EIN:$conEIN<br/>Tel # $contel &nbsp;<br/>Fax No. $confax</td>
		<td class="all_border">$ship_to<br/>$ship_address</td>
		<td class="all_border">$shipper<br/>$shipper_addr<br/>
								TEL: $shipper_tel &nbsp; &nbsp; FAX: $shipper_fax</td>
		</tr>
		</table>
	
	<br/><br/>
	<table cellpadding="2">
	<tr>
		<td class="all_border" align="center">Notify Party</td>
		<td rowspan="3" class="all_border">
			<u>BENEFICIARY ACCOUNT</u><br/>
			BANK NAME: $bank_name<br/>
			BANK ADDRESS: $bank_address<br/>
			ACCOUNT NAME: $beneficiary_name<br/>
			SWIFT CODE: $swift_code<br/>
			ACCOUNT NUMBER: $bank_account_no
		</td>
		</tr>
	<tr>
		<td class="all_border">$notify_party<br/>$notify_address $notify_tel $notify_fax $notify_email</td>
		</tr>
	<tr>
		<td class="all_border">PAYMENT TERMS: $paymentterm</td>
		</tr>
		</table>
EOD;

$query_filter = "";
$arr_array    = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_buyerpo  = $arr_array["byBuyerPO"];


$html .= <<<EOD
		<br/><br/>
		<table cellpadding="2">
		<tr>
			<td class="all_border" align="center" style="width:10%">PO No.</td>
			<td class="all_border" align="center" style="width:5%">Quantity</td>
			<td class="all_border" align="center" style="width:8%">STYLE#</td>
			<td class="all_border" align="center" style="width:26%">NG#</td>
			<td class="all_border" align="center" style="width:25%" colspan="2">Description</td>
			<td class="all_border" align="center" style="width:10%">HTS Code</td>
			<td class="all_border" align="center" style="width:8%">Unit Price</td>
			<td class="all_border" align="center" style="width:8%">Total Price</td>
			</tr>
EOD;

$i_po = 0; $uom = ""; $all_qty = 0; $all_gw = 0; $all_amt = 0; $all_nw = 0; $all_ctn = 0;
$arr_grandqty_unit = array("PCS"=>0, "SETS"=>0);
foreach($arr_buyerpo as $key => $arr_info){
	$i_po++;
	
	$grand_qty    = $arr_info["grand_qty"];
	$styleNo      = $arr_info["styleNo"];
	$arr_info_row = $arr_info["arr_info"];
	$ship_marking = $arr_info["ship_marking"];
	$ht_code      = $arr_info["ht_code"];
	$uom          = $arr_info["uom"];
	$grand_gw     = $arr_info["grand_gw"];
	$grand_nw     = $arr_info["grand_nw"];
	$total_ctn    = $arr_info["total_ctn"];
	
	$all_qty += $grand_qty;
	$all_gw  += $grand_gw;
	$all_nw  += $grand_nw;
	$all_ctn  += $total_ctn;
	
	$arr_grandqty_unit["$uom"] += $grand_qty;
	
	$arr_ng = array(); $sub_amt = 0;
	foreach($arr_info_row as $prepack_key => $arr_value){
		list($prepack_name, $group_number) = explode("**^^", $prepack_key);		
		
		if(!in_array("$prepack_name", $arr_ng)){
			$arr_ng[] = $prepack_name;
		}
		$total_ctn_qty = $arr_value["qty"];
		$fob_price     = $arr_value["fob_price"];
		$sub_amt       += ($total_ctn_qty * $fob_price);
	}//--- End Foreach ---//
	
	$str_ng_item = implode("/", $arr_ng);
	$str_amt     = number_format($sub_amt,2); 
	$this_fob    = number_format($fob_price,3); //request by mao 3 decimal point 20220310, case BINV22000288
	$all_amt += $sub_amt;
	
	$html .= <<<EOD
		<tr>
			<td class="border_left border_right" align="center">$key</td>
			<td class="border_left border_right" align="center">$grand_qty</td>
			<td class="border_left border_right" align="center">$styleNo</td>
			<td class="border_left border_right" align="center">$str_ng_item</td>
			<td class="border_left border_right" colspan="2">$ship_marking</td>
			<td class="border_left border_right" align="center">$ht_code</td>
			<td class="border_left border_right" align="center">$this_fob</td>
			<td class="border_left border_right" align="center">$str_amt</td>
			</tr>
EOD;
	
}

$str_qty = number_format($all_qty);
$str_amt = number_format($all_amt, 2);
$goc_discount = round($all_amt * 0.025, 2);
$str_goc_discount = number_format($goc_discount, 2);

$actual_amt = $all_amt - $goc_discount;
$str_actual_amt = number_format($actual_amt,2);


$html .= <<<EOD
		<tr>
			<td class="border_left border_top border_btm" colspan="3" align="center"> 
EOD;

if($arr_grandqty_unit["PCS"]>0){
$html .= <<<EOD
				TOTAL QUANTITY (PCS):<br/>
EOD;
}
if($arr_grandqty_unit["SETS"]>0){
$html .= <<<EOD
				TOTAL QUANTITY (SETS):<br/>
EOD;
}

$html .= <<<EOD
			</td>
			<td class="border_top border_btm border_right" align="center">
EOD;

if($arr_grandqty_unit["PCS"]>0){
	$str_qty = number_format($arr_grandqty_unit["PCS"]);
	$html .= <<<EOD
			$str_qty <br/>
EOD;
}

if($arr_grandqty_unit["SETS"]>0){
	$str_qty = number_format($arr_grandqty_unit["SETS"]);
	$html .= <<<EOD
			$str_qty <br/>
EOD;
}
			
$html .= <<<EOD
			</td>
			<td class="all_border">Total Gross Weight (KG):</td>
			<td class="all_border" align="right">$all_gw</td>
			<td class="border_left border_top" colspan="2" align="right"> Total:</td>
			<td class="border_top border_right" align="center">US$$str_amt</td>
			</tr>
		<tr>
			<td class="border_left border_top border_btm" colspan="3" align="center"> TOTAL CARTONS:</td>
			<td class="border_top border_btm border_right" align="center">$all_ctn</td>
			<td class="all_border">Total Net Weight (KG):</td>
			<td class="all_border" align="right">$all_nw</td>
			<td class="border_left border_btm" colspan="2" align="right"> LESS 2.5% GOC DISCOUNT:</td>
			<td class="border_btm border_right" align="center">$str_goc_discount</td>
			</tr>
		<tr>
			<td class="all_border" colspan="3"></td>
			<td class="all_border" colspan="3"></td>
			<td class="all_border" colspan="2" align="right">INVOICE TOTAL:</td>
			<td class="all_border" align="center">US$$str_actual_amt</td>
			</tr>
			
		<tr>
			<td class="all_border" colspan="2" align="center">SHELL BUTTONS</td>
			<td class="all_border"></td>
			<td class="all_border" colspan="2"></td>
			<td class="all_border" align="center">Comman Name:</td>
			<td class="all_border" colspan="3"></td>
			</tr>
			
		<tr>
			<td class="all_border">Country of Origin</td>
			<td class="all_border"></td>
			<td class="all_border"></td>
			<td class="all_border" align="center">Source</td>
			<td class="all_border"></td>
			<td class="all_border" align="center">Total Buttons:</td>
			<td class="all_border" colspan="3"></td>
			</tr>
			
		<tr>
			<td class="all_border" colspan="2">Unit Value (Buttons):</td>
			<td class="all_border"></td>
			<td class="all_border">Total Quantity (Buttons):</td>
			<td class="all_border"></td>
			<td class="all_border"></td>
			<td class="all_border" colspan="2">Total Value (Buttons):</td>
			<td class="all_border"></td>
			</tr>
			
		<tr>
			<td class="all_border" colspan="2" align="center">If included in the total value of the goods shown above indicate amount</td>
			<td class="all_border" colspan="6">Transportation charges, expenses and insurance to the place pf direct shipment to US.</td>
			<td class="all_border"></td>
			</tr>
			
		<tr>
			<td class="all_border" colspan="2" align="center">If not included in the total value of the goods shown above indicate amount</td>
			<td class="all_border" colspan="6">Transportation charges, expenses and insurance to the place of direct shipment to US:<br/>
												Amounts for commissions order than buying commissions<br/>
												Export Packaging:</td>
			<td class="all_border"></td>
			</tr>
EOD;

$html .= <<<EOD
		</table>
EOD;
$handle_lc->BICID = "";
//= =============================== packing list ====================================//
$this_html = $handle_lc->getBuyerInvoicePackingList($invID);
$html .= $this_html;
?>