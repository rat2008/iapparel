<?php 
//==================== buyer "NRLLC" ; id: B37 =====================================//


$html .= <<<EOD
<table border="0">
	<tr>
		<th class="bold-text center-align">
			<h1>I APPAREL INTERNATIONAL GROUP PRIVATE LIMITED</h1>
		</th>
	</tr>

	<tr>
		<td class="center-align">
			7 KALLANG  PLACE, # 02-08 SINGAPORE 339513
		</td>
	</tr>

	<tr>
		<td class="center-align">
			TEL : +65 62929339 & FAX : +65 62929339
		</td>
	</tr>

</table>

<br>
<br>

EOD;

$html .= <<<EOD
	<h1 class="center-align border_top border_btm">COMMERCIAL INVOICE</h1>

	<table class="border_btm" cellpadding="2">
		<tr>
			<td style="width: 50%;"><b><u>SHIPPER : </u></b></td>
			<td style="width: 20%;"><b>INVOICE NO. : </b></td>
			<td style="width: 30%;">$invoice_no</td>
		</tr>

		<tr>
			<td rowspan="4">
				IK APPAREL CO., LTD <br>
				NATIONAL ROAD 3, ANLUNG ROMEIT WEST COMMUNE, <br>
				KANDAL STEUNG DISTRICT, KANDAL PROVINCE, <br>
				KINGDOM OF CAMBODIA <br>
				TEL: 855-23 969 898
			</td>
			<td>DATE: </td>
			<td>$invoice_date</td>
		</tr>

		
		<tr>
			<td>SHIPMENT TERM: </td>
			<td>$tradeterm</td>
		</tr>

		<tr>
			<td>PAYMENT TERM: </td>
			<td>$paymentterm</td>
		</tr>

		<tr>
			<td>COUNTRY OF ORIGIN: </td>
			<td>CAMBODIA</td>
		</tr>

	</table>
EOD;

$html .= <<<EOD
	
	<table border="0" cellpadding="2">
		<tr>
			<td colspan="2" style="width: 50%"><b><u>CONSIGNEE: </u></b></td>
			<td style="width: 20%">FACTORY IA No.: </td>
			<td style="width: 30%">9628</td>
		</tr>

		<tr>
			<td class="border_btm" colspan="2" rowspan="3">
				$conName <br>
				$conAddress
			</td>
			<td colspan="2"></td>
		</tr>

		<tr>
			<td><b>BANK ACCOUNT NUMBER: </b></td>
			<td>0003-023777-01-3-022</td>
		</tr>

		<tr>
			<td rowspan="2"><b>BANK NAME: </b></td>
			<td rowspan="2">
				DBB BANK <br>
				12 Marina Boulevard <br>
				DBS Asia Central <br>
				Marina Bay Financial Centre Tower 3 <br>
				Singapore 018982 
			</td>
		</tr>

		<tr>
			<td class="border_btm border_right" rowspan="2" style="width: 30%; ">
				<b><u>PORT OF LOADING: </u></b> <br>
				$portLoading
			</td>
			<td class="border_btm" rowspan="2" style="width: 20%; ">
				<b><u>DESTINATION: </u></b> <br>
				$countryName			
			</td>
			
		</tr>

		<tr>
			<td><b>BENEFICIARY'S NAME: </b></td>
			<td>I APPAREL INTERNATIONAL GROUP PTE LTD</td>
		</tr>

		<tr>
			<td class="border_btm border_right" rowspan="2" style="width: 30%; ">
				<b><u>MODES OF TRANSPORTATION: </u></b> <br>
				BY $shipmode
			</td>
			<td class="border_btm" rowspan="2" style="width: 20%; ">
				<b><u>ETD: </u></b> <br>
				$shippeddate			
			</td>

			<td><b>SWIFT CODE: </b></td>
			<td>
				For account of: DBS Bank Ltd, Singapore (Swift  Address: DBSSSGSG)
			</td>
		</tr>

		<tr>
			<td class="border_btm" colspan="2"></td>
		</tr>


	</table>

EOD;


$html .= <<<EOD
	
	<table class="full-border" cellpadding="3">
		<thead>
			<tr>
				<td class="center-align border_btm border_right" style="width: 21%; ">CTNS MARKS & NUMBER</td>
				<td class="center-align border_btm border_right" style="width: 6%; ">NO. OF CTNS</td>
				<td class="center-align border_btm border_right" style="width: 33%; " colspan="3"><b>DESCRIPTION</b></td>
				<td class="center-align border_btm border_right" style="width: 10%; "><b>QTY <br></b></td>
				<td class="center-align border_btm border_right" style="width: 15%; "><b>UNIT PRICE <br>(USD)</b></td>
				<td class="center-align border_btm border_right" style="width: 15%; "><b>AMOUNT <br>(USD)</b></td>
			</tr>
		</thead>

EOD;

$grand_total = 0;
$grand_qty = 0;
$detailsql = $conn->prepare("SELECT invd.*, g.styleNo, sp.BuyerPO FROM tblbuyer_invoice_detail invd 
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
		<td class="border_right" style="width: 21%; "></td>
		<td class="border_right" style="width: 6%; "></td>
		<td class="border_right" colspan="3" style="width: 33%; "></td>
		<td class="border_right" style="width: 10%; "></td>
		<td class="border_right" style="width: 15%; "></td>
		<td class="border_right" style="width: 15%; "></td>
		
	</tr>

	<tr>
		<td class="border_right" style="width: 21%; "></td>
		<td class="border_right" style="width: 6%; "></td>
		<td class="border_right" style="width: 33%; " colspan="3">$ColorName</td>
		<td class="border_right" style="width: 10%; "></td>
		<td class="border_right" style="width: 15%; "></td>
		<td class="border_right" style="width: 15%; "></td>
	</tr>

	<tr>
		<td class="border_right"></td>
		<td class="border_right"></td>
		<td class="">PO# <br>$BuyerPO</td>
		<td class="" align="center">STYLE# <br>$styleNo</td>
		<td class="border_right" align="center">HTS# <br> </td>
		<td class="border_right" align="center">
			<i class="empty"></i> <br>
			$color_qty $gmt_unit
		</td>
		<td class="border_right" align="center">
			<i class="empty"></i> <br>
			$unit_price
		</td>
		<td class="border_right" align="center">
			<i class="empty"></i> <br>
			$total_amount
		</td>
	</tr>

EOD;


	}

}


$grand_total = number_format($grand_total, 2, ".", "");
$html .= <<<EOD
		<tr>
			<td class="border_right" ></td>
			<td class="border_right" ></td>
			<td class="border_right" colspan="3"></td>
			<td class="border_right" ></td>
			<td class="border_right" ></td>
			<td class="border_right" ></td>
		</tr>
		<tr>
			<td class="border_right" ></td>
			<td class="border_right" ></td>
			<td class="border_right" colspan="3"></td>
			<td class="border_right" ></td>
			<td class="border_right" ></td>
			<td class="border_right" ></td>
		</tr>

		<tr>
			<td class="border_right" ></td>
			<td class="border_right" ></td>
			<td class="border_right" colspan="3" align="right">TOTAL: </td>
			<td class="border_right border_top" align="center">$grand_qty</td>
			<td class="border_top"></td>
			<td class="border_right border_top" align="center">US$$grand_total</td>
		</tr>	

	</table>

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

//= =============================== packing list ====================================//
$packsql = $conn->prepare("SELECT invd.*, g.styleNo, g.StyleDescription, g.orderno, sp.BuyerPO FROM tblbuyer_invoice_detail invd 
						LEFT JOIN tblshipmentprice sp ON sp.ID=invd.shipmentpriceID 
						LEFT JOIN tblgarment g ON g.orderno=sp.Orderno
						WHERE invd.invID='$invID' AND invd.del=0
						GROUP BY invd.shipmentpriceID 
						ORDER BY invd.ID ASC ");
$packsql->execute(); 
while($packrow = $packsql->fetch(PDO::FETCH_ASSOC)){
	$BuyerPO = $packrow["BuyerPO"];
	$spID = $packrow["shipmentpriceID"];
	$ht_code = $packrow["ht_code"];
	$shipping_remark = $packrow["shipping_marking"];
	$styleNo = $packrow["styleNo"]; 
	$StyleDescription = $packrow["StyleDescription"]; 
	$orderno = $packrow["orderno"]; 
	
$html .= <<<EOD
	<br pagebreak="true">
EOD;


$html .= <<<EOD
	<h1 class="center-align">Noble Rider, LLC</h1>
<br>
<br>
EOD;

$html .= <<<EOD
	<table cellpadding="2" style="width: 50%; ">
		<tr>
			<td><b>FACTORY: </b></td>
			<td class="border_btm">IK APPAREL CO., LTD</td>
			<td colspan="2"></td>
		</tr>

		<tr>
			<td><b>Invoice #: </b></td>
			<td class="border_btm">$invoice_no</td>
			<td><b>Invoice Date: </b></td>
			<td class="border_btm">$invoice_date</td>
		</tr>
	</table>

	<table cellpadding="2" border="1">
		<tr>
			<td>Feeder Vessel: </td>
			<td class="border_btm"></td>
			<td>Mother Vessel: </td>
			<td class="border_btm"></td>
			<td>Approximate Sailing: </td>
			<td class="border_btm"></td>
		</tr>

		<tr>
			<td>From: </td>
			<td class="border_btm">$portLoading</td>
			<td>Ship To Port: </td>
			<td class="border_bottom">$countryName</td>
			<td></td>
			<td></td>
		</tr>

		<tr>
			<td>Container: </td>
			<td class="border_btm"></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>

	</table>
	
	<br>
	<br>
EOD;

$sql3 = $conn->query("SELECT COUNT(ctn_num) as pack_ctn_qty, SUM(total_qty_in_carton) as pack_totalpcs  
						FROM tblcarton_picklist_head ch 
						WHERE ch.shipmentpriceID ='$spID' ");
$row3 = $sql3->fetch(PDO::FETCH_ASSOC);
$pack_ctn_qty = $row3["pack_ctn_qty"];
$pack_totalpcs = $row3["pack_totalpcs"];

$html .= <<<EOD
	<table cellpadding="2">
		<tr>
			<td align="right">Total Ctns on Pack List: </td>
			<td class="full-border" align="center">$pack_ctn_qty</td>
			<td align="right">Total Units on Pack List: </td>
			<td class="full-border" align="center">$pack_totalpcs PCS</td>
			<td align="right">Booked to System By / Date: </td>
			<td class="full-border" align="center"></td>
		</tr>

		<tr>
			<td align="right">Total Ctns Received: </td>
			<td class="full-border" align="center"></td>
			<td align="right">Total Units Received: </td>
			<td class="full-border" align="center"></td>
			<td align="right">Cut Closed By / Date: </td>
			<td class="full-border" align="center"></td>
		</tr>

		<tr>
			<td align="right">Date Arrived in Whse: </td>
			<td class="full-border" align="center"></td>
			<td align="right">QA Exam: </td>
			<td class="full-border" align="center"></td>
			<td align="right">Posted to intransit By / Date: </td>
			<td class="full-border" align="center"></td>
		</tr>

		

		<tr>
			<td align="right">Turn in by / Date: </td>
			<td class="full-border" align="center"></td>
			<td align="right">QA Exam Qty: </td>
			<td class="full-border" align="center"></td>
			<td align="right">Any Problems: </td>
			<td class="" align="center"> YES / NO</td>
		</tr>

	</table>
	<br>
	<br>
EOD;

$html .= <<<EOD

	<table cellpadding="2" style="width: 50%; ">
		<tr>
			<td>PO #: </td>
			<td class="border_btm">$BuyerPO</td>
			<td colspan="2"></td>
		</tr>

		<tr>
			<td>Style #: </td>
			<td class="border_btm">$styleNo</td>
			<td>Style Descr #: </td>
			<td class="border_btm">$StyleDescription</td>
		</tr>
	</table> 
	<br>
	<br>

EOD;

$arrsize = [];
$arrpick_totalsizeqty = []; // 
$arrpick_totalcsq = []; // total color size qty in this packing list
$size_thead = "";
$sizesql = $handle_class->getSizeNameColumnFromOrder($orderno,1);
$size_rowspan=$sizesql->rowCount();
while($sizerow=$sizesql->fetch(PDO::FETCH_ASSOC)){
	$size_name = $sizerow["SizeName"];
	$arrsize[] = $size_name;
	$arrpick_totalsizeqty[$size_name] = 0;
	$size_thead .= "<td>$size_name</td>";
}

$html .= <<<EOD
	<table border="1" cellpadding="2">
		<thead>
			<tr>
				<td align="center">Carton#/UCC 123</td>
				<td align="center">CLR CODE #</td>
				<td></td>
				<td align="center">SIZE</td>
				<td align="center">Item UPC</td>
				<td align="center">Item Qty</td>
				<td align="center">N.W. LBS</td>
				<td align="center">G.W. LBS</td>
				<td align="center" colspan="2">Comments</td>
			</tr>
		</thead>
EOD;

$emptyrow = "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";

$pack_netweight = 0;
$pack_grossweight = 0;
$pack_netnetweight = 0;

$arrtotalsizeqty = [];

$packsql2 = $conn->query("SELECT ch.PID, ch.ctn_num, ch.ctn_range, c.ColorName, COUNT(ch.ctn_num) as ctn_qty, c.ID as colorID, 
								ch.net_weight, ch.gross_weight, ch.net_net_weight, 
								ch.qty_in_blisterbag, ch.blisterbag_in_carton , 
								(SELECT packing_method FROM tblship_packing spk WHERE spk.PID=ch.PID) as packing_method 
							FROM tblcarton_picklist_head ch 
							INNER JOIN tblship_group_color sgc ON sgc.group_number=ch.group_number AND ch.shipmentpriceID=sgc.shipmentpriceID 
							INNER JOIN tblcolor c ON c.ID=sgc.colorID 
							WHERE ch.shipmentpriceID='$spID' 
							GROUP BY ch.ctn_range 
							ORDER BY ch.ctn_num ");
while($packrow2 = $packsql2->fetch(PDO::FETCH_ASSOC)){
	$PID = $packrow2["PID"];
	$ctn_range = $packrow2["ctn_range"];
	$ColorName = $packrow2["ColorName"];
	$colorID = $packrow2["colorID"];
	$ctn_qty = $packrow2["ctn_qty"];
	$ctn_num = $packrow2["ctn_num"];
	$packing_method = $packrow2["packing_method"];


	$net_weight = $packrow2["net_weight"] * 2.20462;
	$gross_weight = $packrow2["gross_weight"] * 2.20462;
	$net_net_weight = $packrow2["net_net_weight"] * 2.20462;

	$net_weight = number_format($net_weight,2);
	$gross_weight = number_format($gross_weight,2);
	$net_net_weight = number_format($net_net_weight,2);

	$net_weight2 = $net_weight*$ctn_qty;
	$gross_weight2 = $gross_weight*$ctn_qty;
	$net_net_weight2 = $net_net_weight*$ctn_qty;
	$pack_netweight += $net_weight2;
	$pack_grossweight += $gross_weight2;
	$pack_netnetweight += $net_net_weight2;

	$qty_in_blisterbag = $packrow2["qty_in_blisterbag"];
	$blisterbag_in_carton = $packrow2["blisterbag_in_carton"];
	$temp_blisterbag_in_carton = $blisterbag_in_carton;
	$qty_in_carton = $qty_in_blisterbag*$blisterbag_in_carton;

	if(!isset($arrpick_totalcsq["$colorID^=$ColorName"])){
		foreach ($arrsize as $size) {		
			$arrpick_totalcsq["$colorID^=$ColorName"][$size] = 0;
		}
	}

	$count = 0; // used for the rowspan
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

		// for the color & size breakdown
		$arrpick_totalcsq["$colorID^=$ColorName"][$size] += $sizeqty_incarton ;

$html .= <<<EOD
		<tr>
EOD;
if($count == 0){
$html .= <<<EOD
			<td align="center" rowspan="$size_rowspan">$ctn_range</td>
			<td align="center" rowspan="$size_rowspan">$ColorName</td>
EOD;
}// end if($count==0)

$html .= <<<EOD
			<td align="center"></td>
			<td align="center">$size</td>
			<td align="center"></td>
			<td align="center">$sizeqty_incarton</td>
EOD;

if($count == 0){
$html .= <<<EOD
			<td align="center" rowspan="$size_rowspan">$net_weight2</td>
			<td align="center" rowspan="$size_rowspan">$gross_weight2</td>
			<td align="center" colspan="2" rowspan="$size_rowspan"></td>
EOD;
}// end if($count==0)

$html .= <<<EOD
		</tr>
EOD;



		$count++;
	}// end foreach



} // end fetch $packsql2



$html .= <<<EOD
		<tr>$emptyrow</tr>
		<tr>$emptyrow</tr>

		<tr class="font-blue">
			<td colspan="3" align="right">TOTAL CTN: </td>
			<td align="center">$pack_ctn_qty</td>
			<td></td>
			<td></td>
			<td align="center">$pack_netweight</td>
			<td align="center">$pack_grossweight</td>
			<td align="right">Total Measurement: </td>
			<td align="center">0.102 CBM</td>
		</tr>

		<tr class="font-blue">
			<td colspan="8"></td>
			<td align="right">Total N.W.: </td>
			<td align="center">$pack_netweight LBS</td>
		</tr>

		<tr class="font-blue">
			<td colspan="8"></td>
			<td align="right">Total G.W.: </td>
			<td align="center">$pack_grossweight LBS</td>
		</tr>

	</table>
EOD;

$html .= <<<EOD
	<h3>Carton Dimensions:</h3>
	<table style="width: 30%; " cellpadding="2" border="1">
		
		<tr>
			<td align="center">INCH</td>
			<td align="center">CTNS</td>
		</tr>
EOD;

$sql2 = $conn->query("SELECT ch.ctn_measurement, COUNT(ctn_num) as countctn FROM tblcarton_picklist_head ch WHERE ch.shipmentpriceID='$spID' GROUP BY ch.ctn_measurement");
while($row2=$sql2->fetch(PDO::FETCH_ASSOC)){

	$ctn_measurement = $row2["ctn_measurement"];
	$countctn = $row2["countctn"];

$html .= <<<EOD
	<tr>
		<td align="center">$ctn_measurement</td>
		<td align="center">$countctn</td>
	</tr>
EOD;

}
$html .= <<<EOD

	</table>

EOD;


$html .= <<<EOD
	<h3>Color & Size Assortment </h3>
	<table cellpadding="2" border="1">
		<thead>
			<tr>
				<td>CLR CODE #/ NAME</td>
				$size_thead
				<td>TOTAL</td>
			</tr>
		</thead>

EOD;

		foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
			$color = explode("^=", $strcolor);
			$ColorName = $color[1];
$html .= <<<EOD
		<tr>
			<td>$ColorName</td>

EOD;
			$str_csq = "";
			foreach ($arrsize as $size) {
				$sizeqty = $sizelist[$size];
				$str_csq .= "<td>$sizeqty</td>";

				//$emptyrow2 .= "<td></td>";
			}

			$totalcolorqty = array_sum($sizelist);
$html .= <<<EOD
			$str_csq
			<td>$totalcolorqty</td>
		</tr>
EOD;
		}



$html .= <<<EOD
		<tr>
			<td><b>TOTAL </b></td>
EOD;
			$totalcolorsizeqty = 0;
			foreach ($arrsize as $size) {
				$totalsizeqty = 0;
				foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
					$totalsizeqty += $sizelist[$size];
				}
				$totalcolorsizeqty += $totalsizeqty;
$html .= <<<EOD
			<td>$totalsizeqty</td>
EOD;
			}


$html .= <<<EOD
			<td>$totalcolorsizeqty</td>
		</tr>

	</table>
<br>
<br>
EOD;
//*/

}// end fetch $packsql