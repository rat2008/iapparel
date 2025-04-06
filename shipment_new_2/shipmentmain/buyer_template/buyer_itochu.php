<?php  
//==================== buyer "itochu" ; id: B47 =====================================//
$html .= <<<EOD
<table border="0">
	<tr>
		<th class="bold-text center-align">
			<h1>I APPAREL INTERNATIONAL GROUP PTE LTD</h1>
		</th>
	</tr>

	<tr>
		<td class="center-align">
			7 KALLANG PLACE#02-08 SINGAPORE 339153
		</td>
	</tr>

	<tr>
		<td class="center-align">
			TEL & FAX: (65) 6292 9339
		</td>
	</tr>

</table>

<br>
<br>

EOD;

$html .= <<<EOD
	<h1 class="center-align">INVOICE</h1>
	<br>
	<table class="border_btm" cellpadding="2">
		<tr>
			<td style="width: 35%;">INVOICE No: </td>
			<td style="width: 40%;">$invoice_no</td>
			<td style="width: 25%;" class="dashedborder_top dashedborder_right dashedborder_left">
				For account and risks of Messrs: 
			</td>
		</tr>

		<tr>
			<td>DATE: </td>
			<td>$invoice_date</td>
			<td class="dashedborder_right dashedborder_left">
				<b>ITOCHU CORPORATION</b> 
			</td>
		</tr>

		<tr>
			<td>Date of Deliverry (ex-factory): </td>
			<td>$exfactorydate</td>
			<td class="dashedborder_right dashedborder_left">
				TOKSI 5-1 KITA-AOYAMA 2-CHOME, 
			</td>
		</tr>

		<tr>
			<td>Date of vessel: </td>
			<td>$shippeddate</td>
			<td class="dashedborder_right dashedborder_left">
				MINATO-KU, TOKYO 107-8077, JAPAN 
			</td>
		</tr>

		<tr>
			<td>Means of transportation: </td>
			<td>BY $shipmode</td>
			<td class="dashedborder_right dashedborder_left">
				TEL: 0081-3-3497-2200 
			</td>
		</tr>

		<tr>
			<td>Trade Term: </td>
			<td>$tradeterm</td>
			<td class="dashedborder_right dashedborder_left dashedborder_btm">
				FAX: 0081-3-3497-2224 
			</td>
		</tr>

		<tr>
			<td>Place of departure: </td>
			<td>$portLoading</td>
		</tr>

		<tr>
			<td>Place of destination (Port of destination): </td>
			<td>$countryName</td>
		</tr>

		<tr>
			<td colspan="3"></td>
		</tr>

	</table>
EOD;

// ========================================================================================================================== //
$html .= <<<EOD
	
	<table class="full-border" cellpadding="3">
		<thead>
			<tr>
				<td class="center-align border_btm border_right" style="width: 30%; ">CODE OF GOODS</td>
				<td class="center-align border_btm border_right" style="width: 30%; ">DESCRIPTION OF GOODS (color&style&product type)</td>
				<td class="center-align border_btm border_right" style="width: 10%; "><b>QUANTITY </b></td>
				<td class="center-align border_btm border_right" style="width: 15%; "><b>UNIT PRICE <br>(USD)</b></td>
				<td class="center-align border_btm border_right" style="width: 15%; "><b>AMOUNT <br>(USD-CMP)</b></td>
			</tr>
		</thead>

EOD;

$grand_total = 0;
$grand_qty = 0;
$detailsql = $conn->prepare("SELECT invd.*, g.styleNo,sp.BuyerPO FROM tblbuyer_invoice_detail invd 
					LEFT JOIN tblshipmentprice sp ON sp.ID=invd.shipmentpriceID 
					LEFT JOIN tblgarment g ON g.orderno=sp.Orderno
					WHERE invd.invID='$invID' AND invd.del=0
					GROUP BY invd.shipmentpriceID 
					ORDER BY invd.ID ASC ");
$detailsql->execute();
while($detailrow = $detailsql->fetch(PDO::FETCH_ASSOC)){
	$BuyerPO = $detailrow["BuyerPO"];
	$spID = $detailrow["shipmentpriceID"];
	$ht_code = $detailrow["ht_code"];
	$shipping_remark = $detailrow["shipping_marking"];
	$styleNo = $detailrow["styleNo"];


	$detailsql2 = $conn->prepare("SELECT invd.*, sgc2.colorID,
									(SELECT group_concat(c.colorName,' / ',g.styleNo separator '<br/>') 
									FROM tblship_group_color sgc 
									LEFT JOIN tblcolor c ON c.ID = sgc.colorID
									LEFT JOIN tblgarment g ON g.garmentID = sgc.garmentID
									WHERE sgc.shipmentpriceID = invd.shipmentpriceID 
									AND sgc.group_number = invd.group_number AND sgc.statusID=1) as ColorName,
									count(sgc2.group_number) as count_grp
								FROM tblbuyer_invoice_detail invd 
								LEFT JOIN tblship_group_color sgc2 ON sgc2.shipmentpriceID = invd.shipmentpriceID 
																					AND sgc2.group_number = invd.group_number
																					AND sgc2.statusID = 1
								WHERE invd.invID='$invID' AND invd.shipmentpriceID='$spID' AND invd.del=0 AND invd.group_number>0
								group by invd.ID");
	$detailsql2->execute(); 
	$rowspan= $detailsql2->rowCount();
	$c=0;
	while($detailrow2 = $detailsql2->fetch(PDO::FETCH_ASSOC)) {
		$unit_price = $detailrow2["fob_price"];
		$color_qty = $detailrow2["qty"];
		$total_amount = $detailrow2["total_amount"];
		$colorID = $detailrow2["colorID"];
		$ColorName = $detailrow2["ColorName"];
		$count_grp = $detailrow2["count_grp"];
		$gmt_unit  = ($count_grp>1? "SETS": "PCS");

		$grand_total += $total_amount;
		$grand_qty += $color_qty;

		$unit_price = number_format($unit_price,2,".","");
		$total_amount = number_format($total_amount,2,".","");

$html .= <<<EOD
		
		<tr>
			<td class="center-align border_right" style="width: 30%; "></td>
			<td class="center-align border_right" style="width: 30%; ">
				$ColorName / $styleNo / 
			</td>
			<td class="center-align border_right" style="width: 10%; ">$color_qty $gmt_unit</td>
			<td class="center-align border_right" style="width: 15%; ">$unit_price</td>
			<td class="center-align  border_right" style="width: 15%; ">$total_amount</td>
		</tr>

EOD;


	}
}

$str_grand_total = numberTowords($grand_total);
$grand_total = number_format($grand_total, 2, ".", "");
$html .= <<<EOD
		<tr>
			<td class="border_right"></td>
			<td class="border_right"></td>
			<td class="border_right"></td>
			<td class="border_right"></td>
			<td class="border_right"></td>
		</tr>

		<tr>
			<td class="border_top"></td>
			<td class="border_right border_top" align="right"><b>TOTAL: </b></td>
			<td class="border_right border_top" align="center">$grand_qty</td>
			<td class="border_right border_top"></td>
			<td class="border_right border_top" align="center">$$grand_total</td>
		</tr>

	</table>

	<br>
	<br>

	<div>
		SAY: $str_grand_total US DOLLARS ONLY <br>
		<b>*NOTE*: MADE IN CAMBODIA.</b>
	</div>

	<br>
	<br>
	
EOD;

	$html .= <<<EOD
	<br>		
	<div style="font-size: 12px; ">
		<b><u>MANUFACTURER'S NAME & ADDRESS: </u></b> <br>
		<b>IK APPAREL CO., LTD </b> <br>
		NATIONAL ROAD 3, ANLUNG ROMEIT WEST COMMUNE, <br>
		KANDAL STEUNG DISTRICT, KANDAL PROVINCE, <br>
		KINGDOM OF CAMBODIA <br>
		TEL: 855-23 969 898
	</div>
EOD;


$html .= <<<EOD
	<br pagebreak="true">
EOD;

//= =============================== packing list ====================================//

$html .= <<<EOD
<table border="0">
	<tr>
		<th class="bold-text center-align">
			<h1>I APPAREL INTERNATIONAL GROUP PTE LTD</h1>
		</th>
	</tr>

	<tr>
		<td class="center-align">
			7 KALLANG PLACE#02-08 SINGAPORE 339153
		</td>
	</tr>

	<tr>
		<td class="center-align">
			TEL & FAX: (65) 6292 9339 
		</td>
	</tr>

</table>

<br>

<h2 class="center-align"><b>PACKING LIST </b></h2>
<br>

<table class="border_btm" cellpadding="2">
		<tr>
			<td style="width: 35%;">INVOICE No: </td>
			<td style="width: 40%;">$invoice_no</td>
			<td style="width: 25%;" class="dashedborder_top dashedborder_right dashedborder_left">
				For account and risks of Messrs: 
			</td>
		</tr>

		<tr>
			<td>DATE: </td>
			<td>$invoice_date</td>
			<td class="dashedborder_right dashedborder_left">
				<b>ITOCHU CORPORATION</b> 
			</td>
		</tr>

		<tr>
			<td>Date of Deliverry (ex-factory): </td>
			<td>$exfactorydate</td>
			<td class="dashedborder_right dashedborder_left">
				TOKSI 5-1 KITA-AOYAMA 2-CHOME, 
			</td>
		</tr>

		<tr>
			<td>Date of vessel: </td>
			<td>$shippeddate</td>
			<td class="dashedborder_right dashedborder_left">
				MINATO-KU, TOKYO 107-8077, JAPAN 
			</td>
		</tr>

		<tr>
			<td>Means of transportation: </td>
			<td>BY $shipmode</td>
			<td class="dashedborder_right dashedborder_left">
				TEL: 0081-3-3497-2200 
			</td>
		</tr>

		<tr>
			<td>Trade Term: </td>
			<td>$tradeterm</td>
			<td class="dashedborder_right dashedborder_left dashedborder_btm">
				FAX: 0081-3-3497-2224 
			</td>
		</tr>

		<tr>
			<td>Place of departure: </td>
			<td>$portLoading</td>
		</tr>

		<tr>
			<td>Place of destination (Port of destination): </td>
			<td>$countryName</td>
		</tr>

		<tr>
			<td colspan="3"></td>
		</tr>

	</table>



EOD;


$html .= <<<EOD
	
	<table cellpadding="2" border="1">
		<thead>
			<tr>
				<td align="center">DESCRIPTION OF GOODS</td>
				<td align="center">CARTON NO.</td>
				<td align="center">PC/CARTON</td>
				<td align="center">QUANTITY <br>PCS</td>
				<td align="center">N. WEIGHT <br>KGS</td>
				<td align="center">G. WEIGHT <br>KGS</td>
				<td align="center">CBM</td>
			</tr>
		</thead>
	
EOD;

$grand_pack_ctn_qty = 0;
$grand_pack_totalpcs = 0;
$grand_pack_netweight = 0;
$grand_pack_grossweight = 0;
$grand_pack_cbm = 0;

$packsql = $conn->prepare("SELECT invd.*, g.styleNo, g.StyleDescription ,sp.BuyerPO
						FROM tblbuyer_invoice_detail invd 
						LEFT JOIN tblshipmentprice sp ON sp.ID=invd.shipmentpriceID 
						LEFT JOIN tblgarment g ON g.orderno=sp.Orderno
						WHERE invd.invID='$invID' AND invd.del=0
						GROUP BY invd.shipmentpriceID 
						ORDER BY invd.ID ASC ");
$packsql->execute();
while($packrow=$packsql->fetch(PDO::FETCH_ASSOC)){
	$BuyerPO = $packrow["BuyerPO"];
	$spID = $packrow["shipmentpriceID"];
	$ht_code = $packrow["ht_code"];
	$shipping_remark = $packrow["shipping_marking"];
	$styleNo = $packrow["styleNo"]; 
	$StyleDescription = $packrow["StyleDescription"]; 


$pack_ctn_qty = 0; // qty of ctn used in one po
$pack_netweight = 0;
$pack_grossweight = 0;
$pack_netnetweight = 0;
$pack_totalpcs = 0;


$arrtotalsizeqty = [];

$packsql2 = $conn->query("SELECT ch.PID, ch.ctn_num, ch.ctn_range, c.ColorName, COUNT(ch.ctn_num) as ctn_qty, c.ID as colorID, 
								ch.net_weight, ch.gross_weight, ch.net_net_weight, 
								ch.qty_in_blisterbag, ch.blisterbag_in_carton , ch.total_qty_in_carton,  
								(SELECT packing_method FROM tblship_packing spk WHERE spk.PID=ch.PID) as packing_method 
							FROM tblcarton_picklist_head ch 
							INNER JOIN tblship_group_color sgc ON sgc.group_number=ch.group_number AND ch.shipmentpriceID=sgc.shipmentpriceID 
							INNER JOIN tblcolor c ON c.ID=sgc.colorID 
							WHERE ch.shipmentpriceID='$spID' 
							GROUP BY ch.ctn_range 
							ORDER BY ch.ctn_num ");
$count_ctnrange = $packsql2->rowCount();
$count = 0;
while($packrow2 = $packsql2->fetch(PDO::FETCH_ASSOC)){
	$PID = $packrow2["PID"];
	$ctn_range = $packrow2["ctn_range"];
	$ColorName = $packrow2["ColorName"];
	$colorID = $packrow2["colorID"];
	$ctn_qty = $packrow2["ctn_qty"];
	$ctn_num = $packrow2["ctn_num"];
	$packing_method = $packrow2["packing_method"];

	$pack_ctn_qty += $ctn_qty;

	$net_weight = $packrow2["net_weight"];
	$gross_weight = $packrow2["gross_weight"];
	$net_net_weight = $packrow2["net_net_weight"];

	$net_weight2 = $net_weight*$ctn_qty;
	$gross_weight2 = $gross_weight*$ctn_qty;
	$net_net_weight2 = $net_net_weight*$ctn_qty;
	$pack_netweight += $net_weight2;
	$pack_grossweight += $gross_weight2;
	$pack_netnetweight += $net_net_weight2;

	$qty_in_blisterbag = $packrow2["qty_in_blisterbag"];
	$blisterbag_in_carton = $packrow2["blisterbag_in_carton"];
	$qty_in_carton = $packrow2["total_qty_in_carton"];
	$temp_blisterbag_in_carton = $blisterbag_in_carton;

	$qty_in_all_carton = $qty_in_carton * $ctn_qty;
	$pack_totalpcs += $qty_in_all_carton; 
	
	
	
	//if(!isset($arrpick_totalcsq["$colorID^=$ColorName"])){ 
		// foreach ($arrsize as $size) {		
			// $arrpick_totalcsq["$colorID^=$ColorName"][$size] = 0;
		// }
	// }

$html .= <<<EOD
	<tr>
EOD;

if($count == 0){

$html .= <<<EOD
		<td align="center" rowspan="$count_ctnrange">
			$BuyerPO <br>
			$StyleDescription
		</td>
EOD;

}// end if

$html .= <<<EOD
		<td align="center">
			$ctn_qty &nbsp;&nbsp;|&nbsp;&nbsp; $ctn_range
		</td>
		<td align="center">$qty_in_carton</td>
		<td align="center">$qty_in_all_carton</td>
		<td align="center">$net_weight2</td>
		<td align="center">$gross_weight2</td>
		<td></td>

	</tr>

EOD;

//*/

$count++;
}// end while fetch $packsql2

$grand_pack_ctn_qty += $pack_ctn_qty;
$grand_pack_totalpcs += $pack_totalpcs;
$grand_pack_netweight += $pack_netnetweight;
$grand_pack_grossweight += $pack_grossweight;
// $grand_pack_cbm += 0;

$html .= <<<EOD
	<tr class="font-blue">
		<td align="center">TOTAL</td>
		<td align="center">$pack_ctn_qty</td>
		<td></td>
		<td align="center">$pack_totalpcs</td>
		<td align="center">$pack_netweight</td>
		<td align="center">$pack_grossweight</td>
		<td align="center"></td>
	</tr>
	
EOD;

}// end while ftech $packsql


$html .= <<<EOD
		<tr class="font-blue">
			<td align="center">GRAND TOTAL</td>
			<td align="center">$grand_pack_ctn_qty</td>
			<td></td>
			<td align="center">$grand_pack_totalpcs</td>
			<td align="center">$grand_pack_netweight</td>
			<td align="center">$grand_pack_grossweight</td>
			<td align="center"></td>
		</tr>

	</table>

EOD;


$html .= <<<EOD
	<br pagebreak="true">
EOD;

//= =============================== packing list detail ====================================//

$html .= <<<EOD
<table border="0">
	<tr>
		<th class="bold-text center-align">
			<h1>I APPAREL INTERNATIONAL GROUP PTE LTD</h1>
		</th>
	</tr>

	<tr>
		<td class="center-align">
			7 KALLANG PLACE#02-08 SINGAPORE 339153
		</td>
	</tr>

	<tr>
		<td class="center-align">
			TEL & FAX: (65) 6292 9339 
		</td>
	</tr>

</table>

<br>

<h2 class="center-align"><b>DETAILS OF THE BAGGAGE </b></h2>
<br>

<table class="border_btm" cellpadding="2">
		<tr>
			<td style="width: 35%;">INVOICE No: </td>
			<td style="width: 40%;">$invoice_no</td>
			<td style="width: 25%;" class="dashedborder_top dashedborder_right dashedborder_left">
				For account and risks of Messrs: 
			</td>
		</tr>

		<tr>
			<td>DATE: </td>
			<td>$invoice_date</td>
			<td class="dashedborder_right dashedborder_left">
				<b>ITOCHU CORPORATION</b> 
			</td>
		</tr>

		<tr>
			<td>Date of Deliverry (ex-factory): </td>
			<td>$exfactorydate</td>
			<td class="dashedborder_right dashedborder_left">
				TOKSI 5-1 KITA-AOYAMA 2-CHOME, 
			</td>
		</tr>

		<tr>
			<td>Date of vessel: </td>
			<td>$shippeddate</td>
			<td class="dashedborder_right dashedborder_left">
				MINATO-KU, TOKYO 107-8077, JAPAN 
			</td>
		</tr>

		<tr>
			<td>Means of transportation: </td>
			<td>BY $shipmode</td>
			<td class="dashedborder_right dashedborder_left">
				TEL: 0081-3-3497-2200 
			</td>
		</tr>

		<tr>
			<td>Trade Term: </td>
			<td>$tradeterm</td>
			<td class="dashedborder_right dashedborder_left dashedborder_btm">
				FAX: 0081-3-3497-2224 
			</td>
		</tr>

		<tr>
			<td>Place of departure: </td>
			<td>$portLoading</td>
		</tr>

		<tr>
			<td>Place of destination (Port of destination): </td>
			<td>$countryName</td>
		</tr>

		<tr>
			<td colspan="3"></td>
		</tr>

	</table>

	<br>
	<br>

EOD;
//*/

$packsql = $conn->prepare("SELECT invd.*, g.styleNo, g.StyleDescription, g.orderno ,sp.BuyerPO
						FROM tblbuyer_invoice_detail invd 
						LEFT JOIN tblshipmentprice sp ON sp.ID=invd.shipmentpriceID 
						LEFT JOIN tblgarment g ON g.orderno=sp.Orderno
						WHERE invd.invID='$invID' AND invd.del=0
						GROUP BY invd.shipmentpriceID 
						ORDER BY invd.ID ASC ");
$packsql->execute();
while($packrow=$packsql->fetch(PDO::FETCH_ASSOC)){
	$BuyerPO = $packrow["BuyerPO"];
	$spID = $packrow["shipmentpriceID"];
	$ht_code = $packrow["ht_code"];
	$shipping_remark = $packrow["shipping_marking"];
	$styleNo = $packrow["styleNo"]; 
	$StyleDescription = $packrow["StyleDescription"]; 
	$orderno = $packrow["orderno"]; 



$arrsize = [];
$arrpick_totalsizeqty = []; // 
$arrpick_totalcsq = []; // total color size qty in this packing list

$size_thead = "";
$sizesql = $handle_class->getSizeNameColumnFromOrder($orderno,1);
$size_colspan=$sizesql->rowCount();
while($sizerow=$sizesql->fetch(PDO::FETCH_ASSOC)){
	$size_name = $sizerow["SizeName"];
	$arrsize[] = $size_name;
	$arrpick_totalsizeqty[$size_name] = 0;

	$size_thead .= '<td class="border_btm border_right">'.$size_name.'</td>';
	// $emptyrow .= "<td></td>";
} // end while $sizerow


$html .= <<<EOD

	<table cellpadding="2" class="full-border">
		<thead>
			<tr>
				<td class="border_btm border_right" rowspan="2">C/S No.</td>
				<td rowspan="2" class="font-red border_btm border_right">$BuyerPO</td>
				<td class="border_btm border_right" rowspan="2">C#</td>
				<td class="border_btm border_right" rowspan="2">CODE</td>
				<td class="border_btm border_right" align="center" colspan="$size_colspan">SIZE/QTY</td>
				<td class="border_btm border_right" rowspan="2">QTY</td>
			</tr>
			<tr>$size_thead</tr>
		</thead>

EOD;


$pack_ctn_qty = 0; // qty of ctn used in one po
$pack_netweight = 0;
$pack_grossweight = 0;
$pack_netnetweight = 0;
$pack_totalpcs = 0;


$arrtotalsizeqty = [];

$packsql2 = $conn->query("SELECT ch.PID, ch.ctn_num, ch.ctn_range, c.ColorName, COUNT(ch.ctn_num) as ctn_qty, c.ID as colorID, 
								ch.net_weight, ch.gross_weight, ch.net_net_weight, 
								ch.qty_in_blisterbag, ch.blisterbag_in_carton , ch.total_qty_in_carton,  
								(SELECT packing_method FROM tblship_packing spk WHERE spk.PID=ch.PID) as packing_method 
							FROM tblcarton_picklist_head ch 
							INNER JOIN tblship_group_color sgc ON sgc.group_number=ch.group_number AND ch.shipmentpriceID=sgc.shipmentpriceID 
							INNER JOIN tblcolor c ON c.ID=sgc.colorID 
							WHERE ch.shipmentpriceID='$spID' 
							GROUP BY ch.ctn_range 
							ORDER BY ch.ctn_num ");
$count_ctnrange = $packsql2->rowCount();
$count = 0;
while($packrow2 = $packsql2->fetch(PDO::FETCH_ASSOC)){
	$PID = $packrow2["PID"];
	$ctn_range = $packrow2["ctn_range"];
	$ColorName = $packrow2["ColorName"];
	$colorID = $packrow2["colorID"];
	$ctn_qty = $packrow2["ctn_qty"];
	$ctn_num = $packrow2["ctn_num"];
	$packing_method = $packrow2["packing_method"];

	$pack_ctn_qty += $ctn_qty;

	$net_weight = $packrow2["net_weight"];
	$gross_weight = $packrow2["gross_weight"];
	$net_net_weight = $packrow2["net_net_weight"];

	$net_weight2 = $net_weight*$ctn_qty;
	$gross_weight2 = $gross_weight*$ctn_qty;
	$net_net_weight2 = $net_net_weight*$ctn_qty;
	$pack_netweight += $net_weight2;
	$pack_grossweight += $gross_weight2;
	$pack_netnetweight += $net_net_weight2;

	$qty_in_blisterbag = $packrow2["qty_in_blisterbag"];
	$blisterbag_in_carton = $packrow2["blisterbag_in_carton"];
	$qty_in_carton = $packrow2["total_qty_in_carton"];
	$temp_blisterbag_in_carton = $blisterbag_in_carton;

	$qty_in_all_carton = $qty_in_carton * $ctn_qty;
	$pack_totalpcs += $qty_in_all_carton; 
	

	if(!isset($arrpick_totalcsq["$colorID^=$ColorName"])){ 
		foreach ($arrsize as $size) {		
			$arrpick_totalcsq["$colorID^=$ColorName"][$size] = 0;
		}
	}

$html .= <<<EOD
	<tr>
		<td class="border_right border_btm">$ctn_range</td>
		<td class="border_right border_btm">$ColorName</td>
		<td class="border_right border_btm"></td>
		<td class="border_right border_btm"></td>
EOD;

		$size_tbody = "";
		foreach ($arrsize as $size) {
			$packsql3 = $conn->query("SELECT qty 
									FROM tblcarton_picklist_detail cd 
									WHERE cd.PID='$PID' AND cd.ctn_num='$ctn_num' AND shipmentpriceID='$spID' AND cd.size_name='$size'");
			if($packsql3->rowCount() == 0){
				$size_qty = 0;
				$arrsizeqty[$size] = -1;
				$arrtotalsizeqty[$size] = 0;

				$sizeqty_incarton = "";
			}else{
				$size_qty = $packsql3->fetchColumn();
				$arrsizeqty[$size] = $size_qty;
				$arrtotalsizeqty[$size] = 0;

				if($packing_method == 2)
					$temp_blisterbag_in_carton = 1;


				$sizeqty_incarton = $size_qty * $ctn_qty * $temp_blisterbag_in_carton;

			} // end if

			$size_tbody .= '<td class="border_right border_btm">'.$sizeqty_incarton.'</td>';
			$arrpick_totalcsq["$colorID^=$ColorName"][$size]+=$sizeqty_incarton;

		}

$html .= <<<EOD
		$size_tbody
		<td class="border_right border_btm">$qty_in_all_carton</td>
	</tr>
EOD;



} // end while fetch $packsql2

			$totalcolorsizeqty = 0;
			$str_totalsizeqty = "";
			$str_ordersizeqty = ""; // order qty

			$arr_exportqty = [];
			$arr_orderqty=[];

			foreach ($arrsize as $size) {
				$totalsizeqty = 0;
				foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
					$totalsizeqty += $sizelist[$size];
				}
				$arr_exportqty[$size]=$totalsizeqty;
				$str_totalsizeqty .= '<td class="border_right border_btm">'.$totalsizeqty.'</td>';


				$sql4 = $conn->query("SELECT SUM(qty) FROM tblship_colorsizeqty scsq 
									WHERE scsq.shipmentpriceID='$spID' AND size_name='$size' ");
				$orderqty=$sql4->fetchColumn();
				$arr_orderqty[$size]=$orderqty;
				$str_ordersizeqty .= '<td class="border_right border_btm">'.$orderqty.'</td>';			


			}
			$totalorderqty=array_sum($arr_orderqty);


			$str_balance='';
			foreach ($arrsize as $size) {
				$balanceqty = $arr_orderqty[$size] - $arr_exportqty[$size];
				$str_balance .= '<td class="border_right border_btm">'.$balanceqty.'</td>';		
			}

			$totalbalanceqty = $totalorderqty-$pack_totalpcs;


$html .= <<<EOD
		<tr class="font-blue">
			<td class="border_right border_btm">TTL = $pack_ctn_qty</td>
			<td class="border_right border_btm" align="right">EXPORT QTY</td>
			<td class="border_right border_btm"></td>
			<td class="border_right border_btm"></td>
			$str_totalsizeqty
			<td class="border_right border_btm">$pack_totalpcs</td>
		</tr>

		<tr class="font-blue">
			<td class="border_right border_btm"></td>
			<td class="border_right border_btm" align="right">ORDER QTY</td>
			<td class="border_right border_btm"></td>
			<td class="border_right border_btm"></td>
			$str_ordersizeqty
			<td class="border_right border_btm">$totalorderqty</td>
		</tr>

		<tr class="font-blue">
			<td class="border_right border_btm"></td>
			<td class="border_right border_btm" align="right">BALANCE</td>
			<td class="border_right border_btm"></td>
			<td class="border_right border_btm"></td>
			$str_balance
			<td class="border_right border_btm">$totalbalanceqty</td>
		</tr>

	</table>
	<br>
	<br>
	<br>
EOD;

//*/

} // end while fetch $packsql


?>