<?php


$today_date=date("Y-m-d H:i:s");

// $pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle("AEO");


$html="";

//$html.='<body style="font-family: arial;font-size:11px;">';

//header
$html.='<div style="font-family:arial;font-size:10px;">';
	$html.='
		<div style="font-family:arialuni;font-size:16px;text-align:center;">
			★ ★ ★ '.strtoupper($letterhead_title).' ★ ★ ★</div>
		<table style="width:100%;border:1px solid black;" cellpadding="3">
			<tr>
				<td style="width:50%;border-right:1px solid black;border-bottom:1px solid black;vertical-align:top;">
					<table>
						<tr>
							<td><b>Invoice No: </b></td>
							<td>'.$invoice_no.'</td>
						</tr>
						<tr>
							<td><b>Invoice Date: </b></td>
							<td>'.$invoice_date.'</td>
						</tr>
						<tr>
							<td><b>Terms of Sale: </b></td>
							<td>'.$tradeterm.'</td>
						</tr>
						<tr>
							<td><b>Currency: </b></td>
							<td>'.$CurrencyCode.'</td>
						</tr>
						<tr>
							<td><b>AE Payment Terms: </b></td>
							<td>'.$payterm_day.'</td>
						</tr>
						<tr>
							<td><b>Buyer Invoice No.: </b></td>
							<td>'.$byr_invoice_no.'</td>
						</tr>
					</table>
				</td>
				<td style="width:50%;border-bottom:1px solid black;">
					<table style="width:100%;">
						<tr>
							<td><b>Country of Origin: </b></td>
							<td>'.$manucountry.'</td>
						</tr>
						<tr>
							<td><b>Port of Export: </b></td>
							<td>'.$portLoading.'</td>
						</tr>
						<tr>
							<td><b>Port of Transship: </b></td>
							<td>'.$transitPort.'</td>
						</tr>
						<tr>
							<td><b>Port of Entry: </b></td>
							<td>'.$portDischarges.'</td>
						</tr>
						<tr>
							<td><b>Ultimate Destination: </b></td>
							<td>'.$buyerdest.'</td>
						</tr>
						<tr>
							<td><b>Mode: </b></td>
							<td>'.$shipmode.'</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="border-right:1px solid black;vertical-align:top;">
					<span><b>Beneficiary: </b></span><br>
					'.$letterhead_name.'<br>'.$letterhead_address.'<br><br>
					<span><b>Seller: </b></span><br>
					'.$letterhead_name.'<br>'.$letterhead_address.'<br><br>
					<span><b>Tax ID: </b></span>
					<br/>'.$taxID.'
				</td>
				<td>
					<span><b>For the Account and Risk of / Buyer: </b></span>
					<br>'.$bill_to.'<br>'.$bill_address.'<br><br>
					<span><b>Consignee/Importer: </b></span><br>
					'.$csn_name.'<br>'.$csn_address.'<br><br>
					<span><b>Tax ID: '.$ein_no.'</b></span> 
					<br><br>
					<span><b>Ultimate Consignee: </b></span>
					<br>'.$ship_to.'<br>'.$ship_address.'
				</td>
			</tr>
		</table>
		&nbsp;<br/>';
		
$html .= '<table cellpadding="3" style="border:1px solid #000">
			<tr>
				<td><b>Manufacturer:</b><br/>
						'.$manufacturer.'<br/>'.$manuaddress.'
						</td>
				<td><b>MID:</b><br/>'.$shipper_MID.'</td>
				</tr>
				</table>';
				
$query_filter = "";
$arr_array     = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_buyerpo   = $arr_array["byBuyerPO"];
$arr_fabric    = $arr_array["byFabric"];
$grand_inv_gw  = $arr_array["grand_inv_gw"];
$grand_inv_nw  = $arr_array["grand_inv_nw"];
$grand_inv_ctn = $arr_array["grand_inv_ctn"];
$grand_inv_qty = $arr_array["grand_inv_qty"];

$this_grand_inv_ctn = number_format($grand_inv_ctn);
$str_grand_inv_qty = number_format($grand_inv_qty);
$str_grand_inv_nw  = number_format($grand_inv_nw, 3);
$str_grand_inv_gw  = number_format($grand_inv_gw, 3);
				
$html .= '&nbsp;<br/>
		<table border="1" style="width:100%;" cellpadding="3" style="font-family: arial;font-size:8px;">
			<tr>
				<td style="width:7%"><b>Order No</b></td>
				<td style="width:5%"><b>Class</b></td>
				<td style="width:8%"><b>Style</b></td>
				<td style="width:8%"><b>Color</b></td>
				<td style="width:13%"><b>Description of Goods</b></td>
				<td style="width:10%"><b>Fabrication</b></td>
				<td style="width:10%"><b>HTS No</b></td>
				<td style="width:10%"><b>Packing List</b></td>
				<td style="width:6%"><b>Cartons</b></td>
				<td style="width:5%"><b>QTY</b></td>
				<td style="width:8%"><b>Price (USD)</b></td>
				<td style="width:10%"><b>Total Value (USD)</b></td>
			</tr>';
	$grand_amt = 0;
	$arr_spID = array();
	foreach($arr_fabric as $ship_marking => $arr_value){
		
		for($i=0;$i<count($arr_value);$i++){
			$arr_group_number = $arr_value[$i]["arr_group_number"];
			$ht_code          = $arr_value[$i]["ht_code"];
			$fab_order        = $arr_value[$i]["fab_order"];
			$BuyerPO          = $arr_value[$i]["BuyerPO"];
			$shipmentpriceID  = $arr_value[$i]["shipmentpriceID"];
			$arr_spID[] = $shipmentpriceID;
			
			// echo "$BuyerPO << <br/>";
			// print_r($arr_group_number);
			// echo "<hr/>";
			
			foreach($arr_group_number as $key => $arr){
				$class_description   = $arr["class_description"];
				$colorOnly   = $arr["colorOnly"];
				$garmentOnly = $arr["garmentOnly"];
				$qty         = $arr["qty"];
				$fob_price   = $arr["fob_price"];
				$total_ctn   = $arr["total_ctn"];
				$this_amt    = $qty * $fob_price;
				$str_amt     = number_format($this_amt, 2);
				$grand_amt   += $this_amt;
				
				$sqlmaster = "SELECT count(CIHID) as count_ctn, count(DISTINCT masterID) as  count_master
								FROM tblcarton_inv_payment_head
								WHERE shipmentpriceID = '$shipmentpriceID' AND del=0 
								AND masterID!='' AND masterID!='0'"; // for case id:375
				$stmt_master = $conn->prepare($sqlmaster);
				$stmt_master->execute();
				$row_master = $stmt_master->fetch(PDO::FETCH_ASSOC);
					$count_ctn    = $row_master["count_ctn"];
					$count_master = $row_master["count_master"];
					$total_ctn = $total_ctn - $count_ctn + $count_master;
				
				$html .= '<tr>';
				$html .= '<td>'.$BuyerPO.'</td>';
				$html .= '<td>'.$class_description.'</td>';
				$html .= '<td>'.$garmentOnly.'</td>';
				$html .= '<td>'.$colorOnly.'</td>';
				$html .= '<td>'.$ship_marking.'</td>';
				$html .= '<td>'.$fab_order.'</td>';
				$html .= '<td>'.$ht_code.'</td>';
				$html .= '<td></td>';
				$html .= '<td>'.$total_ctn.'</td>';
				$html .= '<td>'.$qty.'</td>';
				$html .= '<td>'.$fob_price.'</td>';
				$html .= '<td align="right">'.$str_amt.'</td>';
				$html .= '</tr>';
				
				
				
			}//--- End Foreach Color ---//
			
		}//--- End For i ---//
		
	}//--- End Foreach Buyer PO ---//
	

$sqloth = "SELECT sum(total_amount) as total_amount
			FROM `tblbuyer_invoice_payment_detail` 
			WHERE invID='$invID' AND del=0 AND group_number=0 ";
$stmt_oth = $conn->prepare($sqloth);
$stmt_oth->execute();
$rowoth = $stmt_oth->fetch(PDO::FETCH_ASSOC);
	$other_charge = $rowoth["total_amount"];
	$other_charge = ($other_charge==""? 0: $other_charge);

$str_grand_amt = number_format($grand_amt, 2);
$grand_amt += $other_charge;

$str_grand_charge = number_format($grand_amt, 2);
$str_other_charge = number_format($other_charge, 2);

$str_shipmentpriceID = implode(",", $arr_spID);
$str_shipmentpriceID = ($str_shipmentpriceID!=""? $str_shipmentpriceID: 0);

$sqlmaster = "SELECT sum(total_ctn) as count_ctn, count(DISTINCT masterID) as  count_master
				FROM tblcarton_inv_payment_head
				WHERE shipmentpriceID IN ($str_shipmentpriceID) AND del=0 
				AND masterID!='' AND masterID!='0'
				group by shipmentpriceID"; // for case id:375
$stmt_master = $conn->prepare($sqlmaster);
$stmt_master->execute();

$grand_countctn = 0;
$grand_countmtr = 0;
while($row_master = $stmt_master->fetch(PDO::FETCH_ASSOC)){
	$count_ctn    = $row_master["count_ctn"];
	$count_master = $row_master["count_master"];
	
	$grand_countctn += $count_ctn;
	$grand_countmtr += $count_master;
	
}
	
	$grand_inv_ctn = $grand_inv_ctn - $grand_countctn + $grand_countmtr;
	$str_grand_inv_ctn = number_format($grand_inv_ctn);

			
$html .= '<tr>
				<td colspan="12">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="11"><i><b>Total Cost of Merchandise Before Discount</b></i></td>
				<td align="right">'.$str_grand_amt.'</td>
			</tr>
			<tr>
				<td colspan="11"><i><b>Total Cost of Merchandise Less Payment Discount</b></i></td>
				<td align="right">'.$str_other_charge.'</td>
			</tr>
			<tr>
				<td colspan="11"><i><b>Total Cost of Merchandise After Discount</b></i></td>
				<td align="right">'.$str_grand_charge.'</td>
			</tr>
		</table>';

		
$html .= '&nbsp;<br>
		<table border="0" style="width:100%;font-family: arial;font-size:9px;">
			<tr>
				<td align="right" style="width:40%"></td>
				<td style="border:1px solid #000;width:20%">Total Cartons</td>
				<td style="border:1px solid #000;width:10%" align="right">'.$str_grand_inv_ctn.'</td>
				<td style="border:1px solid #000;width:20%">Net Weight (KGs)</td>
				<td style="border:1px solid #000;width:10%" align="right">'.$str_grand_inv_nw.'</td>
				</tr>
				</table>
		<br>
		<table border="0" style="width:100%;font-family: arial;font-size:9px;">
			<tr>
				<td align="right" style="width:40%"></td>
				<td style="border:1px solid #000;width:20%">Total Qty</td>
				<td style="border:1px solid #000;width:10%" align="right">'.$str_grand_inv_qty.'</td>
				<td style="border:1px solid #000;width:20%">Gross Weight (KGs)</td>
				<td style="border:1px solid #000;width:10%" align="right">'.$str_grand_inv_gw.'</td>
				</tr>
				</table>
		&nbsp;<br>
		<table border="1" style="width:100%;">
			<tr>
				<td><i>ADDITIONAL REMARKS: '.$remarks.' <br/>ETD: '.$shippeddate.'</i></td>
			</tr>
		</table>
		<br>
		<i>-- The exporter/shipper of this merchandise cetifies and guarantees thhat subject shipment does not contain any wood packing material in accordance with the International Standards for Phytosanitary Measures (ISPM) number 15</i>
		<br><br>
		<i>-- Goods must ship directly to United States</i>
		<br><br>
		<div style="width:100%;font-size:10px;text-align:center;">
			<i>Report Created On '.$today_date.'</i>
		</div>
	';
$html.='</div><br>';

//$html.='</body>';
$pdf->writeHTML($html, true, 0, true, 0);
$pdf->AddPage('L', 'A4');
$html = "";
//$html .= '<br pagebreak="true">';
$this_num = 0;
foreach($arr_buyerpo as $buyerPO => $arr_value){
	$this_num++;
	
	$arrbp = explode("%%",$buyerPO);
	$buyerPO = $arrbp[0];
	
	$html .= ($this_num>1? '<br pagebreak="true">':'');
	$html .= '<table style="font-family: arial;font-size:10px;" cellpadding="3">';
	$html .= '<tr>';
	$html .= '<td align="center" style="border:1px solid #000;text-align:center" colspan="5">
				&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
				&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
				&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
				&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
				&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
				<table style="width:60%;"><tr><td>'.$letterhead_name.'<br/>'.$letterhead_address.'</td></tr></table></td>';
	$html .= '</tr>';
	$html .= '<tr><td align="center" style="border:1px solid #000;" colspan="5">Packing List</td></tr>';
	$html .= '<tr>
				<td align="center" style="border:1px solid #000;">Term of Sale</td>
				<td align="center" style="border:1px solid #000;">Payment Terms</td>
				<td align="center" style="border:1px solid #000;">Invoice Date</td>
				<td align="center" style="border:1px solid #000;">Invoice Number</td>
				<td align="center" style="border:1px solid #000;">Packing List No</td>
				</tr>';
	$html .= '<tr>
				<td align="center" style="border:1px solid #000;">'.$tradeterm.'</td>
				<td align="center" style="border:1px solid #000;">'.$payterm_day.'</td>
				<td align="center" style="border:1px solid #000;">'.$invoice_date.'</td>
				<td align="center" style="border:1px solid #000;">'.$invoice_no.'</td>
				<td align="center" style="border:1px solid #000;"></td>
				</tr>';
	$html .= '<tr><td align="center" style="border:1px solid #000;" colspan="5"></td></tr>';
	
	$html .= '<tr>
				<td colspan="3" style="border:1px solid #000;" >
					SHIPPER/EXPORTER<br/>
					<table cellpadding="3">
					<tr>
						<td style="width:10%">Name: </td>
						<td>'.$shipper.'</td>
						</tr>
					<tr>
						<td style="width:10%">Address: </td>
						<td>'.$shipper_addr.'</td>
						</tr>
						</table>
					</td>
				<td colspan="2" style="border:1px solid #000;" >
					CONSIGNEE<br/>
					<table cellpadding="3">
					<tr>
						<td style="width:15%">Name: </td>
						<td>'.$csn_name.'</td>
						</tr>
					<tr>
						<td style="width:15%">Address: </td>
						<td>'.$csn_address.'</td>
						</tr>
						</table>
				</td>
				</tr>';
	$html .= '<tr>
				<td colspan="5" style="border:1px solid #000;">
					<table>
					<tr>
						<td style="width:20%">Country Of Ultimate Destination:</td>
						<td>'.$buyerdest.'</td>
						</tr>
					<tr>
						<td style="width:20%">Country Of Manufacturer:</td>
						<td>'.$manucountry.'</td>
						</tr>
						</table>&nbsp;<br/>';
						
		$html .= '<table cellpadding="3">
					<tr>
						<td style="width:10%;border:1px solid #000;background-color:#f3f3f4">PO Number:</td>
						<td style="width:10%;border:1px solid #000">'.$buyerPO.'</td>
						</tr>
					<tr>
						<td style="width:10%;border:1px solid #000;background-color:#f3f3f4">Trans Mode:</td>
						<td style="width:10%;border:1px solid #000">'.$shipmode.'</td>
						</tr>
					<tr>
						<td style="width:10%;border:1px solid #000;background-color:#f3f3f4">Floorset:</td>
						<td style="width:10%;border:1px solid #000"></td>
						</tr>
					<tr>
						<td style="width:10%;border:1px solid #000;background-color:#f3f3f4">Barcode Prefix#:</td>
						<td style="width:10%;border:1px solid #000"></td>
						</tr>
					<tr>
						<td style="width:10%;border:1px solid #000;background-color:#f3f3f4">Floorset Code:</td>
						<td style="width:10%;border:1px solid #000"></td>
						</tr>
					<tr>
						<td style="width:10%;border:1px solid #000;background-color:#f3f3f4">PO Department:</td>
						<td style="width:10%;border:1px solid #000"></td>
						</tr>
					<tr>
						<td style="width:10%;border:1px solid #000;background-color:#f3f3f4">Store #:</td>
						<td style="width:10%;border:1px solid #000"></td>
						</tr>
						</table>&nbsp;<br/>';
		$html .= '<table cellpadding="3" style="font-family: arial;font-size:9px;">
					<tr style="background-color:#f3f3f4">
						<td style="width:3%;border:1px solid #000;">Order Row No</td>
						<td style="width:5%;border:1px solid #000;">Start Barcode</td>
						<td style="width:5%;border:1px solid #000;"># of Ctns</td>
						<td style="width:5%;border:1px solid #000;">End Barcode</td>
						<td style="width:5%;border:1px solid #000;">Class</td>
						<td style="width:5%;border:1px solid #000;">Style</td>
						<td style="width:5%;border:1px solid #000;">Color </td>
						<td style="width:5%;border:1px solid #000;">Size</td>
						<td style="width:6%;border:1px solid #000;">SKU#</td>
						<td style="width:6%;border:1px solid #000;">Description English</td>
						<td style="width:5%;border:1px solid #000;">Pks per Ctn</td>
						<td style="width:5%;border:1px solid #000;">Unit Per Pack</td>
						<td style="width:5%;border:1px solid #000;">Ctn Qty</td>
						<td style="width:5%;border:1px solid #000;">Total Qty</td>
						<td style="width:5%;border:1px solid #000;">Master Carton ID</td>
						<td style="width:5%;border:1px solid #000;">Ctn Type</td>
						<td style="width:5%;border:1px solid #000;">G.W per Ctn</td>
						<td style="width:5%;border:1px solid #000;">N.W per Ctn</td>
						<td style="width:5%;border:1px solid #000;">Carton Size<br/>(LxWxH)</td>
						<td style="width:5%;border:1px solid #000;">Total CBM</td>
						</tr>';
			$arr_list     = $arr_value["arr_list"];
			$ship_marking = $arr_value["ship_marking"];
			$grand_qty    = $arr_value["grand_qty"];
			$grand_ctn    = $arr_value["total_ctn"];
			$grand_gw     = $arr_value["grand_gw"];
			$grand_nw     = $arr_value["grand_nw"];
			$grand_cbm    = $arr_value["grand_cbm"];
			
			$arr_group_number = array();
			$arr_masterID     = array();
			$arr_master       = array();
			
			$grand_ctn = 0;
			for($i=0;$i<count($arr_list);$i++){
				$start     = $arr_list[$i]["start"];
				$end_num   = $arr_list[$i]["end_num"];
				$total_ctn = $arr_list[$i]["total_ctn"];
				$mixID     = $arr_list[$i]["mixID"];
				$SKU       = $arr_list[$i]["SKU"];
				$masterID  = $arr_list[$i]["masterID"];
				$total_qty_in_carton = $arr_list[$i]["total_qty_in_carton"];
				$total_qty = $arr_list[$i]["total_qty"];
				$this_nnw  = $arr_list[$i]["this_nnw"];
				$this_nw   = $arr_list[$i]["this_nw"];
				$this_gw   = $arr_list[$i]["this_gw"];
				$one_gw    = $arr_list[$i]["gross_weight"];
				$one_nw    = $arr_list[$i]["net_weight"];
				$total_CBM = $arr_list[$i]["total_CBM"];
				$ctn_measurement = $arr_list[$i]["ctn_measurement"];
				$blisterbag_in_carton = $arr_list[$i]["blisterbag_in_carton"];
				
				$group_number = "";
				$size_name    = "";
				$qty          = "";
				$colorOnly    = $arr_list[$i]["colorOnly"];
				$garmentOnly  = $arr_list[$i]["garmentOnly"];
				$group_number = $arr_list[$i]["group_number"];
				
				$arr_mix = explode("::^^", $mixID);
				if(count($arr_mix)==1){
					list($group_number, $size_name, $qty) = explode("**%%", $arr_mix[0]);
					$colorOnly   = $arr_list[$i]["colorOnly"];
					$garmentOnly = $arr_list[$i]["garmentOnly"];
				}
				
				if (array_key_exists("G$group_number**^^$size_name", $arr_group_number)){
					$arr_group_number["G$group_number**^^$size_name"]["qty"] += $total_qty;
				}
				else{
					$arr_group_number["G$group_number**^^$size_name"] = array("qty"=>$total_qty, "PO"=>"$buyerPO", 
																				"color"=>$colorOnly, "garment"=>$garmentOnly,
																				"size"=>$size_name);
				}
				
				$pack_per_ctn = $blisterbag_in_carton;
				$unit_per_pack = $total_qty_in_carton / $pack_per_ctn;
				$grand_ctn += $total_ctn;
				
				$html .= '<tr>
							<td style="border:1px solid #000;"></td>
							<td style="border:1px solid #000;"></td>
							<td style="border:1px solid #000;">'.$total_ctn.'</td>
							<td style="border:1px solid #000;">'.$total_ctn.'</td>
							<td style="border:1px solid #000;"></td>
							<td style="border:1px solid #000;">'.$garmentOnly.'</td>
							<td style="border:1px solid #000;">'.$colorOnly.'</td>
							<td style="border:1px solid #000;">'.$size_name.'</td>
							<td style="border:1px solid #000;">'.$SKU.'</td>
							<td style="border:1px solid #000;">'.$ship_marking.'</td>
							<td style="border:1px solid #000;">'.$pack_per_ctn.'</td>
							<td style="border:1px solid #000;">'.$unit_per_pack.'</td>
							<td style="border:1px solid #000;">'.$total_qty_in_carton.'</td>
							<td style="border:1px solid #000;">'.$total_qty.'</td>
							<td style="border:1px solid #000;">'.$masterID.'</td>
							<td style="border:1px solid #000;"></td>
							<td style="border:1px solid #000;">'.$one_gw.'</td>
							<td style="border:1px solid #000;">'.$one_nw.'</td>
							<td style="border:1px solid #000;">'.$ctn_measurement.'</td>
							<td style="border:1px solid #000;">'.$total_CBM.'</td>
							</tr>';
							
				if(trim($masterID)!="" && trim($masterID)!="0"){
					
					$arr_masterID["M$masterID"][] = $masterID;
					$arr_master[] = $masterID;
				}
			}
			
			$distinct_master_count = 0;
			foreach($arr_masterID as $key => $value){
				$m_count = count($arr_masterID[$key]);
				$distinct_master_count += ($m_count - 1);
			}
			// $grand_ctn = $grand_ctn - 1;
			
			$arr_munique    = array_unique($arr_master);
			$grand_ctn = $grand_ctn - count($arr_master) + count($arr_munique);
			
			$str_grand_qty = number_format($grand_qty);
			$str_grand_ctn = number_format($grand_ctn);
			$str_grand_gw  = number_format($grand_gw, 2);
			$str_grand_nw  = number_format($grand_nw, 2);
			$str_test      = implode(",", $arr_master);
			$arr_master    = array_unique($arr_master);
			$count_master  = count($arr_master);
			
			
			$html .= '</table>&nbsp;<br/>
			
				<table style="width:100%" cellpadding="3">
				<tr>
					<td style="width:50%"></td>
					<td style="width:10%"></td>
					<td style="width:10%"></td>
					<td style="width:10%;border:1px solid #000;" align="center"><b>Total Units:</b></td>
					<td style="width:10%;border:1px solid #000;" align="right">'.$str_grand_qty.'</td>
					<td style="width:10%;border:1px solid #000;" align="center"><b>Units</b></td>
					</tr>
				<tr>
					<td style="width:50%"></td>
					<td style="width:10%;border:1px solid #000;" align="center"><b>Total Master Ctns</b></td>
					<td style="width:10%;border:1px solid #000;">'.$count_master.'</td>
					<td style="width:10%;border:1px solid #000;" align="center"><b>Total Cartons:</b></td>
					<td style="width:10%;border:1px solid #000;" align="right">'.$str_grand_ctn.'</td>
					<td style="width:10%;border:1px solid #000;" align="center"><b>Ctns</b></td>
					</tr>
				<tr>
					<td style="width:50%"></td>
					<td style="width:10%"></td>
					<td style="width:10%"></td>
					<td style="width:10%;border:1px solid #000;" align="center"><b>Total G.W:</b></td>
					<td style="width:10%;border:1px solid #000;" align="right">'.$str_grand_gw.'</td>
					<td style="width:10%;border:1px solid #000;" align="center"><b>Kgs</b></td>
					</tr>
				<tr>
					<td style="width:50%"></td>
					<td style="width:10%"></td>
					<td style="width:10%"></td>
					<td style="width:10%;border:1px solid #000;" align="center"><b>Total N.W:</b></td>
					<td style="width:10%;border:1px solid #000;" align="right">'.$str_grand_nw.'</td>
					<td style="width:10%;border:1px solid #000;" align="center"><b>Kgs</b></td>
					</tr>
				<tr>
					<td style="width:50%"></td>
					<td style="width:10%"></td>
					<td style="width:10%"></td>
					<td style="width:10%;border:1px solid #000;" align="center"><b>Total CBM:</b></td>
					<td style="width:10%;border:1px solid #000;" align="right">'.$grand_cbm.'</td>
					<td style="width:10%;border:1px solid #000;" align="center"><b>CBM</b></td>
					</tr>
					</table>';
					
		$html .= '
				<table style="width:70%" cellpadding="3">
				<tr>
					<td colspan="6"><b>Size Breakdown</b></td>
					</tr>
				<tr>
					<td>PO</td>
					<td>Style</td>
					<td>Color</td>
					<td>Pack</td>
					<td>Size</td>
					<td>Qty</td>
					</tr>';
				foreach($arr_group_number as $key => $arr){
					$qty     = number_format($arr["qty"]);
					$PO      = $arr["PO"];
					$color   = $arr["color"];
					$garment = $arr["garment"];
					$size    = $arr["size"];
					$Pack    = ($size==""? "PPK":"BLK");
					
					
					$html.= '<tr>
								<td>'.$PO.'</td>
								<td>'.$garment.'</td>
								<td>'.$color.'</td>
								<td>'.$Pack.'</td>
								<td>'.$size.'</td>
								<td>'.$qty.'</td>
								</tr>';
					
				}//--- End Foreach ---//
					
		$html .= '</table>&nbsp;<br/>
				<br/>
				<br/>
				SIGNATURE OF SHIPPER/EXPORTER<br>
				<b>Shipper: '.$shipper.'</b>
				
				<br/>
				<br/>
				<br/>
				<br/>
				<table style="width:40%">
				<tr>
					<td style="border-bottom:1px solid #000">Authorized Signature:</td>
					</tr>
				<tr>
					<td >AUTHORIZED AGENT</td>
					</tr>
					</table>
				</td>
				</tr>';
	
	$html .= '</table>';
}

?>