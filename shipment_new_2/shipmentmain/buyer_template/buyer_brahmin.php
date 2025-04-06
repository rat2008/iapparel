<?php 
$pdf->SetTitle("Brahmin");

$html = <<<EOD
<style>
	.font-12{
		font-size:12px;
		font-family:calibri;
	}
	.font-11{
		font-size:11px;
		font-family:calibri;
	}
	.font-10{
		font-size:10px;
		font-family:calibri;
	}
	.font-9{
		font-size:9px;
		font-family:calibri;
	}
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

$html_style = $html;

//==================== buyer "JOE FRESH" ; id: B13 =====================================//
// ============== pdf header ===================// 

$html .= funcLetterHead($letterhead_name, $letterhead_address, $letterhead_tel, $letterhead_fax, $letterhead_title, 
						$conName, $conAddress, $conemail, $concontact, $contel, $confax, $shipper, $shipper_addr, 
						$invoice_no, $invoice_date, $portLoading, $portDischarges, $vesselname, $shippeddate, $ETA,
						$container_no, $paymentterm);


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
$query_filter = ""; $arr_all = array(); $arr_buyerpo = array();
if($acctid==0){
	$arr_buyerpo = array();
}
else{
	// $arr_array   = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
	// $arr_buyerpo = $arr_array["byBuyerPO"];
	// $arr_all     = $arr_array["arr_all"];
}



$grand_ctn = 0;
$grand_qty = 0;
$grand_amt = 0;
$grand_nw  = 0;
$grand_gw  = 0;
	
//========================== po list =========================//


$html .= '<table cellspacing="1" cellpadding="2" border="0" class="tb_joefresh font-10">
				<tr>
					<td class="top_border bottom_border" style="width:8%"><b><u>PO NO.</u></b></td>
					<td class="top_border bottom_border" style="width:10%"><b><u>STYLE NO.</u></b></td>
					<td class="top_border bottom_border" style="width:12%"><b><u>FACTORY NO.</u></b></td>
					<td class="top_border bottom_border" style="width:18%"><b><u>DESCRIPTION</u></b></td>
					<td class="top_border bottom_border" style="width:12%"><b><u>COLOR NAME</u></b></td>
					<td class="top_border bottom_border" style="width:9%"><b><u>COLORS</u></b></td>
					<td class="top_border bottom_border" style="width:9%"><b><u>PC/QTY</u></b></td>
					<td class="top_border bottom_border" style="width:10%"><b><u>UNIT PRICE</u></b></td>
					<td class="top_border bottom_border" style="width:12%"><b><u>TTL AMT</u></b></td>
					</tr>';
$html .= '<tr>';
$html .= '<td colspan="9"><u>OUTLET</u></td>';
$html .= '</tr>';

$_tblbuyer_invoice_payment_detail = new tblbuyer_invoice_payment_detail($conn, $handle_misc);
$_tblcarton_inv_payment_head      = new tblcarton_inv_payment_head($conn, $handle_misc);
$_tblcolorsizeqty                 = new tblcolorsizeqty($conn, $handle_misc);
$_tblship_group_color             = new tblship_group_color($conn, $handle_misc);

$arr_col = array("bipd.invID"=>$invID, "bipd.del"=>0, "bipd.group_number!!>"=>0);
$arrbp = $_tblbuyer_invoice_payment_detail->getAllByArr($arr_col);
$rowbp = $arrbp["row"];

$grand_qty = 0;
$grand_amt = 0;
$unit      = '';
$currency  = '';
for($i=0;$i<count($rowbp);$i++){
	extract($rowbp[$i]);
	
	$arr_grp = explode("^^", $color_grp);
	$color     = $arr_grp[0];
	$style     = $arr_grp[1];
	$styledesc = $arr_grp[2];
	$colorID   = $arr_grp[3];
	$garmentID = $arr_grp[4];
	
	$arr_col    = array("csq.garmentID"=>$garmentID, "csq.colorID"=>$colorID);
	$arrcsq     = $_tblcolorsizeqty->getAllSizeListByArr($arr_col, "group by csq.SizeName order by ID limit 1");
	$alias_name = (isset($arrcsq["row"][0]["GTN_colorname"])? $arrcsq["row"][0]["GTN_colorname"]: "");
	
	$amt     = $qty * $fob_price;
	$lbl_up  = number_format($fob_price, 2);
	$lbl_amt = number_format($amt, 2);
	
	$grand_qty += $qty;
	$grand_amt += $amt;
	
	$html .= '<tr>';
	$html .= '<td align="center">'.$actual_po.'</td>';
	$html .= '<td align="center">'.$style.'</td>';
	$html .= '<td align="center">'.$orderno.'</td>';
	$html .= '<td>'.$styledesc.'</td>';
	$html .= '<td>'.$alias_name.'</td>';
	$html .= '<td>'.$color.'</td>';
	$html .= '<td>'.$qty.' '.$unit.'</td>';
	$html .= '<td>'.$currency.' '.$lbl_up.'</td>';
	$html .= '<td>'.$currency.' '.$lbl_amt.'</td>';
	$html .= '</tr>';
}

$lbl_amt = number_format($grand_amt, 2);
$lbl_qty = number_format($grand_qty, 2);

$html .= '<tr>';
$html .= '<td class="top_border bottom_border" ></td>';
$html .= '<td class="top_border bottom_border" ></td>';
$html .= '<td class="top_border bottom_border" ></td>';
$html .= '<td class="top_border bottom_border" ></td>';
$html .= '<td class="top_border bottom_border" ><b>TOTAL:</b></td>';
$html .= '<td class="top_border bottom_border" ></td>';
$html .= '<td class="top_border bottom_border" >'.$lbl_qty.' '.$unit.'</td>';
$html .= '<td class="top_border bottom_border" ></td>';
$html .= '<td class="top_border bottom_border" >'.$currency.' '.$lbl_amt.'</td>';
$html .= '</tr>';
$html .= '</table>';

list($this_amt, $this_cent) = explode(".", $grand_amt);

$txt_amt  = strtoupper($handle_finance->convert_number($grand_amt));
$txt_cent = ($this_cent>0? "AND CENTS ".strtoupper($handle_finance->convert_number($this_cent)):"");



$html .= '<br/>';
$html .= '<font class="font-10"><b>TOTAL SUM OF U.S DOLLARS '.$txt_amt.' '.$txt_cent.' ONLY.</b></font>'; //convert_number

$pdf->writeHTML($html, true, 0, true, 0);

$pdf->AddPage('L', 'A4');

$html = $html_style;
$letterhead_title = "PACKING LIST";

$html .= funcLetterHead($letterhead_name, $letterhead_address, $letterhead_tel, $letterhead_fax, $letterhead_title, 
						$conName, $conAddress, $conemail, $concontact, $contel, $confax, $shipper, $shipper_addr, 
						$invoice_no, $invoice_date, $portLoading, $portDischarges, $vesselname, $shippeddate, $ETA,
						$container_no, $paymentterm);

$handle_lc->BICID = "";
//= =============================== packing list ====================================//

$html .= '<table cellspacing="1" cellpadding="2" border="0" class="tb_joefresh font-9">
				<tr>
					<td class="top_border bottom_border" style="width:5%"><b><u>PO NO.</u></b></td>
					<td class="top_border bottom_border" style="width:5%"><b><u>STYLE NO.</u></b></td>
					<td class="top_border bottom_border" style="width:6%"><b><u>FACTORY NO.</u></b></td>
					<td class="top_border bottom_border" style="width:10%"><b><u>DESCRIPTION</u></b></td>
					<td class="top_border bottom_border" style="width:6%"><b><u>COLOR NAME</u></b></td>
					<td class="top_border bottom_border" style="width:6%"><b><u>COLOR CODE</u></b></td>
					<td class="top_border bottom_border" style="width:5%" align="center"><b><u>TOTAL QTY</u></b></td>
					<td class="top_border bottom_border" style="width:6%" align="center"><b><u>PC/CTN</u></b></td>
					<td class="top_border bottom_border" style="width:6%" align="center"><b><u>TOTAL CTNS</u></b></td>
					<td class="top_border bottom_border" style="width:6%" align="center"><b><u>CTNS NO.</u></b></td>
					<td class="top_border bottom_border" style="width:6%" align="center"><b><u>N.W/CTN KGS</u></b></td>
					<td class="top_border bottom_border" style="width:6%" align="center"><b><u>G.W/CTN KGS</u></b></td>
					<td class="top_border bottom_border" style="width:12%" align="center"><b><u>MEASUREMENT (CM)</u><br/>L x W x H</b></td>
					<td class="top_border bottom_border" style="width:5%" align="center"><b><u>TOTAL N.W</u></b></td>
					<td class="top_border bottom_border" style="width:5%" align="center"><b><u>TOTAL G.W</u></b></td>
					<td class="top_border bottom_border" style="width:5%" align="center"><b><u>CBM</u></b></td>
					</tr>';
$arr_col = array("cph.invID"=>$invID, "bipd.del"=>0);
$arrcph = $_tblcarton_inv_payment_head->getAllByArr($arr_col);
$rowcph = $arrcph["row"];

$grand_qty = 0;
$grand_CBM = 0;
$grand_gw  = 0;
$grand_nw  = 0;
$grand_ctn = 0;

for($i=0;$i<count($rowcph);$i++){
	extract($rowcph[$i]); //group_number, shipmentpriceID
	
	$ctn_measurement = "".round($ext_length, 1)." x ".round($ext_width, 1)." x ".round($ext_height, 1);
	
	$total_nw = $total_ctn * $net_weight;
	$total_gw = $total_ctn * $gross_weight;
	$total_qty = $total_qty_in_carton * $total_ctn;
	
	$grand_qty += $total_qty;
	$grand_CBM += $total_CBM;
	$grand_gw += $total_gw;
	$grand_nw += $total_nw;
	$grand_ctn += $total_ctn;
	
	$arr_col = array("sgc.shipmentpriceID"=>$shipmentpriceID, "sgc.statusID"=>1, "sgc.group_number"=>$group_number);
	$arrsgc    = $_tblship_group_color->getAllByArr($arr_col);
	$color     = (isset($arrsgc["row"][0]["color"])? $arrsgc["row"][0]["color"]: "");
	$styleno   = (isset($arrsgc["row"][0]["styleNo"])? $arrsgc["row"][0]["styleNo"]: "");
	$Styledesc = (isset($arrsgc["row"][0]["StyleDescription"])? $arrsgc["row"][0]["StyleDescription"]: "");
	$colorID   = (isset($arrsgc["row"][0]["colorID"])? $arrsgc["row"][0]["colorID"]: "");
	$garmentID = (isset($arrsgc["row"][0]["garmentID"])? $arrsgc["row"][0]["garmentID"]: "");
	
	$arr_col    = array("csq.garmentID"=>$garmentID, "csq.colorID"=>$colorID);
	$arrcsq     = $_tblcolorsizeqty->getAllSizeListByArr($arr_col, "group by csq.SizeName order by ID limit 1");
	$alias_name = (isset($arrcsq["row"][0]["GTN_colorname"])? $arrcsq["row"][0]["GTN_colorname"]: "");
	
	$html .= '<tr>';
	$html .= '<td>'.$actual_po.'</td>';
	$html .= '<td>'.$styleno.'</td>';
	$html .= '<td>'.$orderno.'</td>';
	$html .= '<td>'.$Styledesc.'</td>';
	$html .= '<td>'.$alias_name.'</td>';
	$html .= '<td>'.$color.'</td>';
	$html .= '<td align="center">'.$total_qty.'</td>';
	$html .= '<td align="center">'.$total_qty_in_carton.'</td>';
	$html .= '<td align="center">'.$total_ctn.'</td>';
	$html .= '<td align="center">'.$start.'-'.$end_num.'</td>';
	$html .= '<td align="center">'.$net_weight.'</td>';
	$html .= '<td align="center">'.$gross_weight.'</td>';
	$html .= '<td align="center">'.$ctn_measurement.'</td>';
	$html .= '<td align="center">'.$total_nw.'</td>';
	$html .= '<td align="center">'.$total_gw.'</td>';
	$html .= '<td align="center">'.$total_CBM.'</td>';
	$html .= '</tr>';
}

$html .= '<tr>';
$html .= '<td class="top_border bottom_border"></td>';
$html .= '<td class="top_border bottom_border"></td>';
$html .= '<td class="top_border bottom_border"></td>';
$html .= '<td class="top_border bottom_border"></td>';
$html .= '<td class="top_border bottom_border"></td>';
$html .= '<td class="top_border bottom_border"><b>TOTAL:</b></td>';
$html .= '<td class="top_border bottom_border" align="center">'.$grand_qty.' '.$unit.'</td>';
$html .= '<td class="top_border bottom_border"></td>';
$html .= '<td class="top_border bottom_border" align="center">'.$grand_ctn.' CTNS</td>';
$html .= '<td class="top_border bottom_border"></td>';
$html .= '<td class="top_border bottom_border"></td>';
$html .= '<td class="top_border bottom_border"></td>';
$html .= '<td class="top_border bottom_border"></td>';
$html .= '<td class="top_border bottom_border" align="center">'.$grand_nw.'</td>';
$html .= '<td class="top_border bottom_border" align="center">'.$grand_gw.'</td>';
$html .= '<td class="top_border bottom_border" align="center">'.$grand_CBM.'</td>';
$html .= '</tr>';

$html .= '</table>';
$html .= '<br/>';
$html .= '<br/>';
$html .= '<table class="font-9">';
$html .= '<tr>';
$html .= '<td style="width:15%"><b>TOTAL CBM:</b></td>';
$html .= '<td>'.$grand_CBM.' CBM</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td><b>TOTAL GROSS WEIGHT:</b></td>';
$html .= '<td>'.$grand_gw.' KGS</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td><b>TOTAL NET WEIGHT:</b></td>';
$html .= '<td>'.$grand_nw.' KGS</td>';
$html .= '</tr>';
$html .= '</table>';



function funcLetterHead($letterhead_name, $letterhead_address, $letterhead_tel, $letterhead_fax, $letterhead_title, 
						$conName, $conAddress, $conemail, $concontact, $contel, $confax, $shipper, $shipper_addr, 
						$invoice_no, $invoice_date, $portLoading, $portDischarges, $vesselname, $shippeddate, $ETA,
						$container_no, $paymentterm){
	$html = '';
	$html .= <<<EOD
<table border="0" class="font-11">
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

$conAddress       = strtoupper($conAddress);
$letterhead_title = strtoupper($letterhead_title);

//table-bordered
$html .= <<<EOD
		<table border="0" class="font-10" cellpadding="3">
			<tr>
				<th colspan="2" class="center-align font-12"><u><b>$letterhead_title</b></u></th>
				</tr>
			<tr>
				<th style="width:50%">
					<table>
					<tr>
						<td><b>CONSIGNEE:</b><br/>
							<b>$conName</b> <br/>
							$conAddress<br/>
							Email: $conemail<br/>
							Contact Person: $concontact<br/>
							Tel: $contel<br/>
							Fax: $confax
							</td>
						<td><b>SHIPPER:</b><br/>
							<b>$shipper</b> <br/>
							$shipper_addr<br/>
							</td>
						</tr>
						</table>
				</th>
				<th style="width:5%">
				&nbsp;
				</th>
				<th>
					<table>
					<tr>
						<td style="width:42%"><b>NO:</b></td>
						<td>$invoice_no</td>
						</tr>
					<tr>
						<td><b>DATE:</b></td>
						<td>$invoice_date</td>
						</tr>
					<tr>
						<td><b>SHIPMENT FROM:</b></td>
						<td>$portLoading</td>
						</tr>
					<tr>
						<td><b>SHIPMENT TO:</b></td>
						<td>$portDischarges</td>
						</tr>
					<tr>
						<td><b>VESSEL NAME:</b></td>
						<td colspan="1">$vesselname</td>
						</tr>
					<tr>
						<td><b>VOYAGE:</b></td>
						<td colspan="1"></td>
						</tr>
					<tr>
						<td><b>ETD:</b></td>
						<td>$shippeddate</td>
						</tr>
					<tr>
						<td><b>ETA:</b></td>
						<td>$ETA</td>
						</tr>
					<tr>
						<td><b>CONTAINER NO.</b></td>
						<td>$container_no</td>
						</tr>
					<tr>
						<td><b>TERMS OF PAYMENT</b></td>
						<td>$paymentterm</td>
						</tr>
						</table>
				</th>
				</tr>
			
		</table>
EOD;

return $html;
}

?>