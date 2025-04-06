<?php 
$pdf->SetTitle("Lacoste");

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

	.border_btm_bold{
		border-bottom:3px solid black;
	}

	.border_top_bold{
		border-top:3px solid black;
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

//==================== buyer "JOE FRESH" ; id: B13 =====================================//
// ============== pdf header ===================// 
// $html .= <<<EOD
// <table border="0">
// 	<tr>
// 		<th class="bold-text center-align">
// 			<h1>$letterhead_name</h1>
// 		</th>
// 	</tr>

// 	<tr>
// 		<td class="center-align">
// 			$letterhead_address
// 		</td>
// 	</tr>

// 	<tr>
// 		<td class="center-align">
// 			TEL : $letterhead_tel &nbsp;&nbsp;&nbsp;&nbsp;FAX : $letterhead_fax
// 		</td>
// 	</tr>

// </table>

// <br>
// <br>

// EOD;

$conAddress     = strtoupper($conAddress);
$notify_address = strtoupper($notify_address);
$notify_tel   = (trim($notifyTel)==""? "":"<br/>Tel: $notifyTel");
$notify_fax   = (trim($notifyFax)==""? "":"<br/>Fax: $notifyFax");
$notify_email = (trim($notifyEmail)==""? "":"<br/>Email: $notifyEmail");

$ship_address2=nl2br($ship_address);
$bill_address2=nl2br($bill_address);
$manuaddress2=nl2br($manuaddress);
$owneraddress2=nl2br($owneraddress);

$letterhead_title_upper=strtoupper($letterhead_title);
$html .= <<<EOD
		<table cellpadding="3">
			<tr>
				<td colspan="2" class="center-align" style="font-size:12px;letter-spacing: 3px;"><b>$letterhead_title_upper</b></td>
				</tr>
			<tr>
				<td style="width:65%;font-size:9px;">
					<table border="1" cellpadding="3">
						<tr>
							<td>
								<b>SUPPLIER:</b><br/><br/>
								$ownership <br/>
								$owneraddress2
							</td>
							<td><b>MANUFACTURER:</b><br/><br/>
								$manufacturer <br/>
								$manuaddress2
							</td>
						</tr>
						<tr>
							<td><b>SHIP TO:</b><br/><br/>
								$ship_to <br/>
								$ship_address2
							</td>
							<td><b>BILL TO:</b><br/><br/>
								$bill_to <br/>
								$bill_address2
							</td>
						</tr>
					</table>
				</td>
				<td style="font-size:8px;">
					<table cellpadding="2">
						<tr>
							<td style="width:20%;text-align:right;">INVOICE NO: </td>
							<td>$invoice_no</td>
						</tr>
						<tr>
							<td style="text-align:right;">DATE: </td>
							<td>$invoice_date</td>
						</tr>
						<tr>
							<td style="text-align:right;">SEASON: </td>
							<td>$season</td>
						</tr>
						<tr>
							<td style="text-align:right;">PO NO: </td>
							<td>$allBuyerPO</td>
						</tr>
						<tr>
							<td style="text-align:right;">ASN NO: </td>
							<td></td>
						</tr>
						<tr>
							<td style="text-align:right;">SHIP MODE: </td>
							<td>$shipmode</td>
						</tr>
						<tr>
							<td style="text-align:right;">SHIPMENT TERM: </td>
							<td>$tradeterm</td>
						</tr>
						<tr>
							<td style="text-align:right;">MADE IN: </td>
							<td>$manucountry</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
EOD;

// 21 CHOM CHAO ST, DAMNAK THOM VILLAGE, <br>
					// STOEUNG MEANCHEY COMMUNE, MEANCHEY DISTRICT, <br>
					// PHNOM PENH, CAMBODIA .

$html .= <<<EOD
<br>
<br>
<style>
	.tbljoefresh td {
		width: 35%;
	}
	.tbljoefresh .td_short {
		width: 30%;
	}
</style>
EOD;

$html .= <<<EOD

EOD;

// DHL ISC (HONG KONG) LTD., 10014-10021E,10/F., <br>
				// 10014-10021E,10/F. <br>
				// ATL LOGISTICS CENTRE B BERTH 3, <br>
				// KWAI CHUNG CONTAINER TERMINAL 

				//Notify: DHL ISC, 10/F ATL LOGISTICS CENTRE B, BERTH 3 <br>
						// KWAI CHUNG CONTAINER TERMINAL, KWAI CHUNG, HONG KONG <br>
						// WAREHOUSE CONTACT: <br>
						// HERMAN SHUM -+852 2765-5850 
// $query_filter = "AND cpt.shiped=1";
// $arr_array   = $handle_class->getBuyerInvoiceDescriptionInfo($invID, $query_filter);
// $arr_buyerpo = $arr_array["byBuyerPO"];
$query_filter = ""; $arr_all = array();
if($acctid==0){
	$arr_buyerpo = array();
}
else{
	$arr_array   = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
	$arr_buyerpo = $arr_array["byBuyerPO"];
	$arr_all     = $arr_array["arr_all"];
}



// $grand_ctn = 0;
// $grand_qty = 0;
// $grand_amt = 0;
// $grand_nw  = 0;
// $grand_gw  = 0;
	
//========================== po list =========================//

$html .= '<table cellpadding="2" border="1" style="width:100%;text-align:center;">
				<tr>
					<td><b>REFERENCE</b></td>
					<td><b>COLOR</b></td>
					<td><b>DESCRIPTION</b></td>
					<td><b>COMPOSITION</b></td>
					<td><b>QUANTITY (PCS)</b></td>
					<td><b>UNIT PRICE ('.$CurrencyCode.')</b></td>
					<td><b>TOTAL AMOUNT ('.$CurrencyCode.')</b></td>
				</tr>';

	$sel_detail=$conn->prepare("SELECT g.styleNo,c.ColorName,bid.shipping_marking,g.StyleDescription,bid.fob_price,bid.qty,bid.total_amount
			FROM tblbuyer_invoice_payment_detail bid 
			JOIN tblbuyer_invoice_payment bi ON bid.invID=bi.ID
			LEFT JOIN tblship_group_color sgc ON bid.group_number=sgc.group_number AND bid.shipmentpriceID=sgc.shipmentpriceID AND sgc.statusID='1'
			LEFT JOIN tblcolor c ON sgc.colorID=c.ID
		    LEFT JOIN tblgarment g ON sgc.garmentID=g.garmentID
			LEFT JOIN tblshipmentprice sp ON sp.ID=bid.shipmentpriceID
			WHERE bid.invID='$invID' AND bid.del='0' AND bid.qty>0 AND bid.group_number>0
			GROUP BY bid.ID
		");
	$sel_detail->execute();

	$grand_qty=0;
	$grand_amt=0;
	while($row_detail=$sel_detail->fetch(PDO::FETCH_ASSOC)){
			extract($row_detail);

			$html .= '<tr>';

			$html .= '<td>'.$styleNo.'</td>';
			$html .= '<td>'.$ColorName.'</td>';
			$html .= '<td>'.$StyleDescription.'</td>';
			$html .= '<td>'.$shipping_marking.'</td>';
			$html .= '<td>'.$qty.'</td>';
			$html .= '<td>'.$fob_price.'</td>';
			$html .= '<td>'.$total_amount.'</td>';

			$html .= '</tr>';

			$grand_qty+=$qty;
			$grand_amt+=$total_amount;
								
	}


	$html .= '<tr>';

	$html .= '<td colspan="4" style="text-align:right"><b>TOTAL = </b></td>';
	$html .= '<td>'.$grand_qty.'</td>';
	$html .= '<td></td>';
	$html .= '<td>'.number_format($grand_amt,2).'</td>';

	$html .= '</tr>';
	
	
	$html .= '</table>';	

	//get total ctn
	$sel_ctn=$conn->prepare("SELECT sum(cih.total_ctn) total_ctn 
		FROM tblcarton_inv_head cih
		WHERE cih.invID='$invID' AND cih.del='0'
		");
	$sel_ctn->execute();
	$grand_ctn=$sel_ctn->fetchColumn();


$html .= '
	
	<br/><br/>
	<table border="0" cellpadding="3" width="60%">
		<tr>
			<td style="width:25%"><b>TOTAL CARTONS: </b></td>
			<td>'.$grand_ctn.'</td>
		</tr>
		<tr>
			<td style="width:25%"><b>BL: </b></td>
			<td></td>
		</tr>
		<tr>
			<td style="width:25%"><b>TERM OF PAYMENT: </b></td>
			<td>'.$paymentterm.'</td>
		</tr>
		<tr>
			<td style="width:25%"><b>BENEFICIARY: </b></td>
			<td>'.$ownership.'</td>
		</tr>
		<tr>
			<td colspan="2"><br><br></td>
		</tr>
		<tr>
			<td style="width:25%"><b>NAME OF BANK: </b></td>
			<td></td>
		</tr>
		<tr>
			<td style="width:25%"><b>ADDRESS: </b></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="2"><br><br></td>
		</tr>
		<tr>
			<td style="width:25%"><b>A/C NO.: </b></td>
			<td></td>
		</tr>
		<tr>
			<td style="width:25%"><b>SWIFT CODE: </b></td>
			<td></td>
		</tr>
		<tr>
			<td style="width:25%"><b>E-MAIL: </b></td>
			<td></td>
		</tr>
	</table>
';


//= =============================== packing list ====================================//

$html .= '<br pagebreak="true">';

$html .= <<<EOD
		<table cellpadding="3">
			<tr>
				<td style="font-size:9px;">
					<div style="font-size:14px;">FACTORY:</div>
					$manufacturer<br>
					$manuaddress2
				</td>
				<td style="font-size:9px;">
					<div style="font-size:14px;">Ship To:</div>
					$ship_to<br>
					$ship_address2
				</td>
				<td>
					
				</td>
			</tr>
EOD;

$arr_gtn_buyerpo=explode(",", $allBuyerPO);

for($i=0;$i<sizeof($arr_gtn_buyerpo);$i++){
	$this_GTN_buyerpo=$arr_gtn_buyerpo[$i];

	$html .= <<<EOD
			<tr>
				<td colspan="2" style="font-size:9px;">
					PO Number: $this_GTN_buyerpo
				</td>
				<td>

				</td>
			</tr>
			<tr>
				<td colspan="3">
					<br>
				</td>
			</tr>
			<tr>
				<table>
					<tr>
						<td class="border_btm_bold center-align">Ctn#</td>
						<td class="border_btm_bold center-align">Case ID</td>
						<td class="border_btm_bold center-align">Style</td>
						<td class="border_btm_bold center-align">Color</td>
						<td class="border_btm_bold center-align">Qty</td>
						<td class="border_btm_bold center-align">Net Net Weight (kg)</td>
						<td class="border_btm_bold center-align">Net Weight (kg)</td>
						<td class="border_btm_bold center-align">Gross Weight (kg)</td>
						<td class="border_btm_bold center-align">Length (cm)</td>
						<td class="border_btm_bold center-align">Width (cm)</td>
						<td class="border_btm_bold center-align">Height (cm)</td>
						<td class="border_btm_bold center-align">CBM</td>
					</tr>
				</table>
			</tr>
EOD;
	
	$sel_picklist=$conn->prepare("SELECT cih.total_ctn, cih.SKU, g.styleNo, c.ColorName, cih.total_qty_in_carton, cih.net_net_weight, cih.net_weight, cih.gross_weight, cih.ext_length, cih.ext_width, cih.ext_height, cih.total_CBM
		FROM tblcarton_inv_payment_head cih
		LEFT JOIN tblcarton_inv_payment_detail cid ON cih.CIHID=cid.CIHID
		LEFT JOIN tblshipmentprice sp ON cid.shipmentpriceID=sp.ID
		LEFT JOIN tblship_group_color sgc ON cid.shipmentpriceID=sgc.shipmentpriceID AND cid.group_number=sgc.group_number AND sgc.statusID='1'
		LEFT JOIN tblcolor c ON sgc.colorID=c.ID
		    LEFT JOIN tblgarment g ON sgc.garmentID=g.garmentID
		WHERE sp.GTN_BuyerPO='$this_GTN_buyerpo' AND cih.del='0'
		GROUP BY cih.CIHID");
	$sel_picklist->execute();

	$ctn_num=1;
	$sum_qty=0;
	$sum_net_net_weight=0;
	$sum_net_weight=0;
	$sum_gross_weight=0;
	$sum_cbm=0;
	while($row_picklist=$sel_picklist->fetch(PDO::FETCH_ASSOC)){
		extract($row_picklist);

		$net_net_weight=round($net_net_weight,2);
		$net_weight=round($net_weight,2);
		$gross_weight=round($gross_weight,2);
		$total_CBM=round($total_CBM,2);

		$a=1;
		while($a<=$total_ctn){
			$html.='
				<tr>
					<td style="text-align:center;">'.$ctn_num.'</td>
					<td class="center-align">'.$SKU.'</td>
					<td class="center-align">'.$styleNo.'</td>
					<td class="center-align">'.$ColorName.'</td>
					<td class="center-align">'.$total_qty_in_carton.'</td>
					<td class="center-align">'.$net_net_weight.'</td>
					<td class="center-align">'.$net_weight.'</td>
					<td class="center-align">'.$gross_weight.'</td>
					<td class="center-align">'.$ext_length.'</td>
					<td class="center-align">'.$ext_width.'</td>
					<td class="center-align">'.$ext_height.'</td>
					<td class="center-align">'.$total_CBM.'</td>
				</tr>
			';

			$a++;
			$ctn_num++;

			$sum_qty+=$total_qty_in_carton;
			$sum_net_net_weight+=$net_net_weight;
			$sum_net_weight+=$net_weight;
			$sum_gross_weight+=$gross_weight;
			$sum_cbm+=$total_CBM;
		}
	}

	$html.='
		<tr>
			<td class="border_top_bold" colspan="12"><br><br>Total</td>
		</tr>
		<tr>
			<td>'.($ctn_num-1).'</td>
			<td class="center-align"></td>
			<td class="center-align"></td>
			<td class="center-align"></td>
			<td class="center-align">'.$sum_qty.'</td>
			<td class="center-align">'.$sum_net_net_weight.'</td>
			<td class="center-align">'.$sum_net_weight.'</td>
			<td class="center-align">'.$sum_gross_weight.'</td>
			<td class="center-align"></td>
			<td class="center-align"></td>
			<td class="center-align"></td>
			<td class="center-align">'.$sum_cbm.'</td>
		</tr>
	';

}

$html.='<tr>
			<td class="center-align" colspan="12">End of Summary</td>
		</tr>';

$html.='</table>';


?>