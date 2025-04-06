<?php 
//====================================== buyer "ROOTS" ; id: B26 =====================================//

$html .= <<<EOD
<style>

	.root_1st_col {
		width: 55%;
	}

	.root_1st_col_1 {
		width: 20%;
	}

	.root_1st_col_2 {
		width: 35%;
	}

	.root_2nd_col {
		width: 15%;
		text-align: right;
	}

	.root_3rd_col {
		width: 30%;
	}

</style>

<br>

<h1 class="center-align">COMMERCIAL INVOICE</h1>
<br>
<br>

	<table cellpadding="2">
		<tr>
			<td class="root_1st_col"><u>Exporter</u></td>
			<td class="root_2nd_col"><b>DATE: </b></td>
			<td align="center" class="root_3rd_col border_btm">$invoice_date</td>
		</tr>

		<tr>
			<td class="root_1st_col" rowspan="2">
				IK APPAREL CO., LTD <br>
				NATIONAL ROAD 3, ANLUNG ROMEAT VILLAGE, ANLUNG ROMEAT COMMUNE, <br>
				KANDAL STEUNG DISTICT, KANDAL PROVINCE <br>
				KINGDOM OF CAMBODIA <br>
				TEL: 855-23 969 898 &emsp; FAX: 855-23 969 090 
			</td>
			<td class="root_2nd_col"><b>INV No.: </b></td>
			<td align="center" class="root_3rd_col border_btm">$invoice_no</td>
		</tr>

		<tr>
			<td class="root_2nd_col"></td>
			<td class="root_3rd_col"></td>
			
		</tr>

		<tr>
			<td class="root_1st_col"><u><b>Seller</b></u></td>
			<td class="root_2nd_col" rowspan="2"><b>Order No(s): </b></td>
			<td class="root_3rd_col" rowspan="2">$allBuyerPO</td>
		</tr>

		<tr>
			<td class="root_1st_col">
				MULTI SOURCING ASIA LIMITED <br>
				FLAT/RM 02 26/F, UNIVERSAL TRADE CENTER, <br>
				3-5A ARBUTHNOT ROAD, CENTRAL HONG KONG <br>
			</td>
		</tr>

		<tr>
			<td class="root_1st_col"><u><b>Consignee</b></u></td>
			<td class="root_2nd_col"></td>
			<td class="root_3rd_col"></td>
		</tr>

		<tr>
			<td class="root_1st_col border_btm" rowspan="2">
				$conName <br>
				$conAddress
			</td>
			<td class="root_2nd_col"><b>Shipment Term: </b></td>
			<td class="root_3rd_col border_btm">$tradeterm</td>
		</tr>

		<tr>
			
			<td class="root_2nd_col"><b>Payment Instructions: </b></td>
			<td class="root_3rd_col border_btm">$paymentterm</td>
		</tr>

	
	</table>
<br>
<br>
	<table cellpadding="2">
		<tr>
			<td class="root_1st_col_1 "><b>Country of Origin: </b></td>
			<td class="root_1st_col_2  root_border_btm">CAMBODIA</td>
			<td class="root_2nd_col"><b>Bill of Lading / AWB No. </b></td>
			<td class="root_3rd_col border_btm"> *** </td>
		</tr>

		<tr>
			<td class="root_1st_col_1 "><b>Discharge Port: </b></td>
			<td class="root_1st_col_2 border_btm">$portLoading</td>
			<td class="root_2nd_col"><b>Load Date: </b></td>
			<td class="root_3rd_col border_btm">$shippeddate</td>
		</tr>

		<tr>
			<td class="root_1st_col_1 "><b>Load Port: </b></td>
			<td class="root_1st_col_2 border_btm">$portLoading</td>
			<td class="root_2nd_col"><b>Destination: </b></td>
			<td class="root_3rd_col border_btm">$shippeddate</td>
		</tr>
	</table>

EOD;

// ========================= buyerpo list ==================================//
$grand_total = 0;
$grand_qty = 0;		
$detailsql = $conn->prepare("SELECT invd.*, g.styleNo ,sp.BuyerPO
						FROM tblbuyer_invoice_detail invd 
						LEFT JOIN tblshipmentprice sp ON sp.ID=invd.shipmentpriceID 
						LEFT JOIN tblgarment g ON g.orderno=sp.Orderno
						WHERE invd.invID='$invID' AND invd.del=0 
						GROUP BY invd.shipmentpriceID 
						ORDER BY invd.ID ASC ");
$detailsql->execute(); 


$html .= <<<EOD

<br>
<br>
<br>
<br>

	<table class="full-border" cellpadding="2">
		<thead>
			<tr>
				<th class="border_btm border_left" align="center" style="width: 55%;"><u>DESCRIPTION: </u> </th>
				<th class="border_btm border_left"  align="center" style="width: 15%;"><u>QTY</u> </th>
				<th class="border_btm border_left" align="center" style="width: 15%;"><u>Unit price (USD)</u> </th>
				<th class="border_btm border_left" align="center" style="width: 15%;"><u>TOTAL (USD)</u></th>

			</tr>
		<thead>

EOD;

$last_buyerpo = "";
while($detailrow = $detailsql->fetch(PDO::FETCH_ASSOC)){
	$BuyerPO = $detailrow["BuyerPO"];
	$spID = $detailrow["shipmentpriceID"];
	$ht_code = $detailrow["ht_code"];
	$shipping_remark = $detailrow["shipping_marking"];
	$styleNo = $detailrow["styleNo"];

	if($last_buyerpo == "" || $last_buyerpo != $BuyerPO){
		$last_buyerpo = $BuyerPO; 
	}

	$detailsql2 = $conn->prepare("SELECT invd.*, sgc2.colorID,
								(SELECT group_concat(c.colorName,' / ',g.styleNo separator '<br/>') 
									FROM tblship_group_color sgc 
									LEFT JOIN tblcolor c ON c.ID = sgc.colorID
									LEFT JOIN tblgarment g ON g.garmentID = sgc.garmentID
									WHERE sgc.shipmentpriceID = invd.shipmentpriceID 
									AND sgc.group_number = invd.group_number AND sgc.statusID=1) as ColorName 
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

		$grand_total += $total_amount;
		$grand_qty += $color_qty;

		$unit_price = number_format($unit_price,2,".","");
		$total_amount = number_format($total_amount,2,".","");

$html .= <<<EOD
	<tr>
		<td class="border_left"></td>
		<td class="border_left"></td>
		<td class="border_left"></td>
		<td class="border_left"></td>
	</tr>
	<tr>
		<td class="border_left">$ColorName <br>PO # $BuyerPO - STY # $styleNo </td>
		<td class="border_left" align="center">$color_qty</td>
		<td class="border_left" align="center">$$unit_price</td>
		<td class="border_left" align="center">$$total_amount</td>
	</tr>
EOD;

	}


}
$grand_total = number_format($grand_total, 2,".","");
$str_grand_total = numberTowords($grand_total);
$html .=<<<EOD
	<tr>
		<td class="border_left"></td>
		<td class="border_left"></td>
		<td class="border_left"></td>
		<td class="border_left"></td>
	</tr>
	
	<tr>
		<td>Total cartons: ***</td>
		<td rowspan="2" class="border_left border_top" align="center">$grand_qty</td>
		<td rowspan="2" class="border_left border_top" align="center"></td>
		<td rowspan="2" class="border_left border_top" align="center">$$grand_total</td>
	</tr>
	
	<tr>
		<td>SAY: $str_grand_total US DOLLARS ONLY </td>
	</tr>
</table>
EOD;

$html .= <<<EOD
<table class="full-border" cellpadding="2">
	<tr>
		<td class="border_top" colspan="4"><b>"THIS SHIPMENT IS COMPRISED OF WEARING APPAREL AND DOES NOT CONTAIN ANY SOLID WOOD PACKING MATERIAL."</b></td>
	</tr>

	<tr>
		<td style="width: 55%;"></td>
		<td style="width: 25%;"></td>
		<td style="width: 20%;"></td>
	</tr>

	<tr>
		<td rowspan="3"><b>Shipping Marks: </b></td>
		<td align="right">Total Gross Wt (kg): </td>
		<td class="border_btm">*** kgs</td>

	</tr>

	<tr>
		<td align="right">Total Net Wt (kg): </td>
		<td class="border_btm">*** kgs</td>
	</tr>

	<tr>
		<td align="right">Total Net Net Wt (kg): </td>
		<td class="border_btm">*** kgs</td>
	</tr>

	<tr>
		<td colspan="3">PO #: </td>
	</tr>

	<tr>
		<td>Color: </td>
	</tr>

	<tr>
		<td>Quantity: </td>
	</tr>

	<tr>
		<td>Measurement: </td>
	</tr>

	<tr>
		<td colspan="3"></td>
	</tr>

	<tr>
		<td>Net Weight: </td>
	</tr>

	<tr>
		<td>Made in: </td>
	</tr>

	<tr>
		<td>Carton #: </td>
		<td colspan="2" class="border_btm"></td>
	</tr>

	<tr>
		<td>Style #: </td>
		<td>Authorised Signatory</td>
	</tr>

	<tr>
		<td>Size: </td>
	</tr>

</table>
EOD;




//= =============================== packing list ====================================//
$packsql = $conn->prepare("SELECT invd.*, g.styleNo, g.orderno,sp.BuyerPO FROM tblbuyer_invoice_detail invd 
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
	$orderno=$packrow["orderno"];



$html .= <<<EOD
	<br pagebreak="true">
EOD;

$html .= <<<EOD
<table border="0">
	<tr>
		<th class="bold-text center-align">
			<h1>I APPAREL INTERNATIONAL (HK) PRIVATE LIMITED</h1>
		</th>
	</tr>

	<tr>
		<td class="center-align">
			UNIT 1-2, 21/F, No.1 HUNG TO ROAD, KWUN TONG, HONG KONG
		</td>
	</tr>

	<tr>
		<td class="center-align">
			TEL: 852-2793 3684 &nbsp;&nbsp;&nbsp;&nbsp; FAX: 852-2793 3325
		</td>
	</tr>

</table>

<br>

<h2 class="center-align">PACKING LIST($spID)</h2>
<br>

EOD;

$html .= <<<EOD
	<table cellpadding="2" border="1">
		<tr>
			<td style="width: 40%; "><b>Shipping to: </b></td>
			<td style="width: 20%; ">Date: </td>
			<td style="width: 40%; ">$invoice_date</td>
		</tr>

		<tr>
			<td rowspan="7"><b>$conAddress</b></td>
			<td>Inv No.: </td>
			<td>$invoice_no</td>
		</tr>

		<tr>
			<td>PO.: </td>
			<td>$BuyerPO</td>
		</tr>

		<tr>
			<td>Style No.: </td>
			<td>$styleNo</td>
		</tr>

		<tr>
			<td>Description: </td>
			<td></td>
		</tr>

		<tr>
			<td>Mode of Shipment: </td>
			<td>$shipmode</td>
		</tr>

		<tr>
			<td>Destination: </td>
			<td>$countryName</td>
		</tr>

		<tr>
			<td>Country of Origin: </td>
			<td>CAMBODIA</td>
		</tr>

	</table>

<br>
<br>
EOD;





$html .= <<<EOD
	<table class="full-border" cellpadding="2">
		<thead>
			<tr>
				<td class="border_btm border_right" align="center">Ctn Nos.</td>
				<td class="border_btm border_right" align="center"># of Ctns</td>
				<td class="border_btm border_right" align="center">Pcs/Ctn</td>
				<td class="border_btm border_right" align="center">Prepack/Pcs/Pack</td>
				<td class="border_btm border_right" align="center">Color</td>
				<td class="border_btm border_right"></td>
EOD;
$arrsize = [];
$arrpick_totalsizeqty = []; // 
$sizesql = $handle_class->getSizeNameColumnFromOrder($orderno,1);
while($sizerow = $sizesql->fetch(PDO::FETCH_ASSOC)){
	$size_name = $sizerow["SizeName"];
	$arrsize[] = $size_name;
	$arrpick_totalsizeqty[$size_name] = 0;
$html .= <<<EOD
			<td class="border_btm border_right" align="center">$size_name</td>
EOD;
}
$html .= <<<EOD
				<td class="border_btm border_right" align="center">TTL</td>
				<td class="border_btm border_right" align="center">NT.WT</td>
				<td class="border_btm border_right" align="center">GR WT.</td>
			</tr>
		</thead>
EOD;

$emptyrow = '<td class="border_right"></td><td class="border_right"></td><td class="border_right"></td><td class="border_right"></td><td class="border_right"></td><td class="border_right"></td><td class="border_right"></td><td class="border_right"></td><td class="border_right"></td>';
foreach ($arrsize as $value) {
	$emptyrow .= '<td class="border_right"></td>';
}

$packsql2 = $conn->query("SELECT ch.PID, ch.ctn_num, ch.ctn_range, c.ColorName, COUNT(ch.ctn_num) as ctn_qty, 
								ch.net_weight, ch.gross_weight,  ch.net_net_weight, 
								ch.qty_in_blisterbag, ch.blisterbag_in_carton , 
								(SELECT packing_method FROM tblship_packing spk WHERE spk.PID=ch.PID) as packing_method 
							FROM tblcarton_picklist_head ch 
							INNER JOIN tblship_group_color sgc ON sgc.group_number=ch.group_number AND ch.shipmentpriceID=sgc.shipmentpriceID 
							INNER JOIN tblcolor c ON c.ID=sgc.colorID 
							WHERE ch.shipmentpriceID='$spID' 
							GROUP BY ch.ctn_range 
							ORDER BY ch.ctn_num ");

$pack_ctn_qty = 0; // qty of ctn used in one po
$pack_netweight = 0;
$pack_grossweight = 0;
$pack_totalpcs = 0;

$lastPID = "-999";
$arrsizeqty = []; // save the data of the size  qty
$arrtotalsizeqty = []; // save the data of total size qty in each PID
while($packrow2 = $packsql2->fetch(PDO::FETCH_ASSOC)){
	$PID = $packrow2["PID"];
	$ctn_range = $packrow2["ctn_range"];
	$ColorName = $packrow2["ColorName"];
	$ctn_qty = $packrow2["ctn_qty"];
	$ctn_num = $packrow2["ctn_num"];
	$packing_method = $packrow2["packing_method"];

	$pack_ctn_qty += $ctn_qty;

	$net_weight = $packrow2["net_weight"];
	$gross_weight = $packrow2["gross_weight"];
	$net_weight2 = $net_weight*$ctn_qty;
	$gross_weight2 = $gross_weight*$ctn_qty;
	$pack_netweight += $net_weight2;
	$pack_grossweight += $gross_weight2;

	$qty_in_blisterbag = $packrow2["qty_in_blisterbag"];
	$blisterbag_in_carton = $packrow2["blisterbag_in_carton"];
	$temp_blisterbag_in_carton = $blisterbag_in_carton;
	if($packing_method == 2){
		$temp_blisterbag_in_carton = "";
	}
	$qty_in_carton = $qty_in_blisterbag*$blisterbag_in_carton;
	

// if($lastPID == "-999" || $lastPID != "$PID-$ColorName"){
// 	$lastPID="$PID-$ColorName";

if($packing_method==1 || $packing_method==50)
	$method = "RATIO";
else if ($packing_method == 2)
	$method = "SOLID";

$html .= <<<EOD
	
		<tr>
			<td class="border_right"></td>
			<td class="border_right"></td>
			<td class="border_right"></td>
			<td class="border_right"></td>
			<td class="border_right" align="center">$ColorName</td>
			<td align="center" class="font-red border_right">$method</td>	
EOD;

	foreach ($arrsize as $size) {
		$packsql3 = $conn->query("SELECT qty 
								FROM tblcarton_picklist_detail cd 
								WHERE cd.PID='$PID' AND cd.ctn_num='$ctn_num' AND shipmentpriceID='$spID' AND cd.size_name='$size'");
		if($packsql3->rowCount() == 0){
			$size_qty = '';
			$arrsizeqty[$size] = -1;
			$arrtotalsizeqty[$size] = 0;

		}else{
			$size_qty = $packsql3->fetchColumn();
			$arrsizeqty[$size] = $size_qty;
			$arrtotalsizeqty[$size] = 0;
		}
		if($packing_method==2)
			$size_qty=1;
$html .= <<<EOD
			<td align="center" class="font-red border_right border_btm">$size_qty</td>
EOD;
	}

$html .= <<<EOD
			<td class="border_right"></td>
			<td class="border_right"></td>
			<td class="border_right"></td>
		</tr>
EOD;
// } // end if
$html .= <<<EOD
		<tr>
			<td class="border_right border_btm" align="center">$ctn_range</td>
			<td class="border_right border_btm" align="center">$ctn_qty</td>
			<td class="border_right border_btm" align="center">$qty_in_carton</td>
			<td class="border_right border_btm" align="center">$temp_blisterbag_in_carton</td>
			<td class="border_right border_btm"></td>
			<td class="border_right border_btm"></td>
EOD;
		foreach($arrsize as $size){
			$sizeqty = $arrsizeqty[$size];
			$sizeqty_incarton=0;

			if($sizeqty == -1){
				$sizeqty_incarton = "";
			}else{
				if($packing_method == 2)
					$temp_blisterbag_in_carton = 1;
				$sizeqty_incarton = $sizeqty * $ctn_qty * $temp_blisterbag_in_carton;
				$arrtotalsizeqty[$size] += $sizeqty_incarton;
				$arrpick_totalsizeqty[$size] += $sizeqty_incarton;

			}
$html .= <<<EOD
			<td class="border_right border_btm" align="center">$sizeqty_incarton</td>
EOD;
		}
$TTL = array_sum($arrtotalsizeqty);
$html .= <<<EOD
			<td class="border_right border_btm" align="center">$TTL</td>
			<td class="border_right border_btm" align="center">$net_weight2</td>
			<td class="border_right border_btm" align="center">$gross_weight2</td>
		</tr>
EOD;
	

}// end while fetch $packsql2




$html .= <<<EOD
		<tr>
			$emptyrow
		</tr>


		<tr class="font-blue">
			<td class="border_right" align="center">TOTAL</td>
			<td class="border_right" align="center">$pack_ctn_qty</td>
			<td class="border_right"></td>
			<td class="border_right"></td>
			<td class="border_right"></td>
			<td class="border_right"></td>
EOD;
		foreach ($arrsize as $size) {
			$qty = $arrpick_totalsizeqty[$size];
$html .= <<<EOD
			<td class="border_right" align="center">$qty</td>
EOD;
		}

		$TTL = array_sum($arrpick_totalsizeqty);
$html .= <<<EOD
			<td class="border_right" align="center">$TTL</td>
			<td class="border_right" align="center">$pack_netweight</td>
			<td class="border_right" align="center">$pack_grossweight</td>
EOD;


$html .= <<<EOD
		</tr>
	</table>

<br>
<br>
EOD;

$html .= <<<EOD
	<h2 class="center-align">Order Summary</h2>
	<table border="1" cellpadding="2">
		
		<thead>
			<tr>
				<td align="center">Style</td>
				<td align="center">Color</td>
				<td></td>
				<td></td>
EOD;
$arr_totalorder = array();  
$arr_totalshipment = array();
foreach ($arrsize as $size) {
	$arr_totalorder[$size] = 0;
	$arr_totalshipment[$size] = 0;
$html .= <<<EOD
			<td align="center">$size</td>
EOD;
}

$html .= <<<EOD
				<td></td>
				<td align="center">TOTAL</td>
				<td></td>
			</tr>
		</thead>
EOD;

$ordersummary = "";
$sql = $conn->query("SELECT c.ColorName, g.styleNo, c.ID as colorID FROM tblship_group_color sgc 
					LEFT OUTER JOIN tblgarment g ON g.garmentID=sgc.garmentID 
					INNER JOIN tblcolor c ON c.ID=sgc.colorID 
					WHERE sgc.shipmentpriceID='$spID'");
while($row=$sql->fetch(PDO::FETCH_ASSOC)){
	$ColorName = $row["ColorName"];
	$styleNo = $row["styleNo"];
	$colorID = $row["colorID"];

	$arr_orderqty = array();
	$arr_shipmentqty = array();


	$ordersummary .= "<tr>";
	$ordersummary .= '<td rowspan="4">'.$styleNo.'</td>';
	$ordersummary .= '<td rowspan="4">'.$ColorName.'</td>';
	$ordersummary .= '<td></td>';
	$ordersummary .= "<td>Order</td>";
	foreach ($arrsize as $size) {
		$arr_orderqty[$size] = 0;

		$sqlorder = $conn->prepare("SELECT *, (total_qty / polybag_qty_in_blisterbag * ratio_qty * gmt_qty_in_polybag) as qtySCMS, 
			(SELECT SUM(ratio_qty) FROM tblship_packing_detail s2 WHERE s2.PID=spd.PID AND s2.group_number=spd.group_number) as sum_ratio , 
			(SELECT packing_method FROM tblship_packing spk2 WHERE spk2.PID=spd.PID) as packing_method
			FROM tblship_packing_detail spd 
			WHERE spd.size_name='$size' 
				AND spd.PID in (SELECt DISTINCT PID FROM tblship_packing WHERE shipmentpriceID='$spID')  
				AND spd.group_number=(SELECT group_number FROM tblship_group_color WHERE shipmentpriceID='$spID' AND colorID='$colorID' and statusID=1) ");

		$sqlorder->execute();
		while($roworder = $sqlorder->fetch(PDO::FETCH_ASSOC)){
			$qtySCMS = $roworder["qtySCMS"];
			$total_qty = $roworder["total_qty"];
			$sum_ratio = $roworder["sum_ratio"];
			$ratio_qty = $roworder["ratio_qty"];
			$packing_method = $roworder["packing_method"];
			
			if($packing_method == 1){ // scms
				$qty = $qtySCMS;
			}else if($packing_method == 2){ // scss
				$qty = $total_qty;
			}else if($packing_method == 50){ //mcms
				$qty = $total_qty/$sum_ratio*$ratio_qty;
			}

			$arr_orderqty[$size] += $qty;
			$arr_totalorder[$size] += $qty;
		}
		$str_qty = number_format($arr_orderqty[$size], 0);
		$ordersummary .= '<td align="center">'.$str_qty .'</td>';
	}
	$total_orderqty = array_sum($arr_orderqty);
	$ordersummary .= "<td></td>";
	$ordersummary .= '<td align="center">'.$total_orderqty .'</td>';
	$ordersummary .= "<td></td>";
	$ordersummary .= "</tr>";


	$ordersummary .= "<tr>";
	$ordersummary .= "<td></td>";
	$ordersummary .= "<td>Shipment</td>";
	
	foreach ($arrsize as $size) {
		$arr_shipmentqty[$size] = 0;
		$sqlshipment = $conn->prepare("SELECT *, (total_qty / polybag_qty_in_blisterbag * ratio_qty * gmt_qty_in_polybag) as qtySCMS, 
				(SELECT SUM(ratio_qty) FROM tblship_packing_detail_prod s2 WHERE s2.PID=spdp.PID AND s2.group_number=spdp.group_number) as sum_ratio , 
				(SELECT packing_method FROM tblship_packing spk2 WHERE spk2.PID=spdp.PID) as packing_method 
			FROM tblship_packing_detail_prod spdp 
			WHERE spdp.size_name='$size' 
					AND spdp.PID in (SELECt DISTINCT PID FROM tblship_packing WHERE shipmentpriceID='$spID')  
					AND spdp.group_number=(SELECT group_number FROM tblship_group_color WHERE shipmentpriceID='$spID' AND colorID='$colorID' and statusID=1) ");
		$sqlshipment->execute();

		
		while($rowshipment = $sqlshipment->fetch(PDO::FETCH_ASSOC)){
			$qtySCMS = $rowshipment["qtySCMS"];
			$total_qty = $rowshipment["total_qty"];
			$sum_ratio = $rowshipment["sum_ratio"];
			$ratio_qty = $rowshipment["ratio_qty"];
			$packing_method = $rowshipment["packing_method"];
			
			if($packing_method == 1){ // scms
				$qty = $qtySCMS;
			}else if($packing_method == 2){ // scss
				$qty = $total_qty;
			}else if($packing_method == 50){ //mcms
				$qty = $total_qty/$sum_ratio*$ratio_qty;
			}
			
			$arr_shipmentqty[$size] += $qty;
			$arr_totalshipment[$size] += $qty;

		}
		$qty = number_format($arr_shipmentqty[$size], 0);
		$ordersummary .= '<td align="center">'.$qty.' </td>';
	}
	$total_shipmentqty = array_sum($arr_shipmentqty); 
	$pack_totalpcs += $total_shipmentqty;
	$ordersummary .= "<td></td>";
	$ordersummary .= '<td align="center">'.$total_shipmentqty.' </td>';
	$ordersummary .= "<td></td>";
	$ordersummary .= "</tr>";


	$ordersummary .= "<tr>";
	$ordersummary .= "<td></td>";
	$ordersummary .= "<td>Excess/Short </td>";
	foreach ($arrsize as $size) {
		$diff = $arr_shipmentqty[$size] - $arr_orderqty[$size];
		$ordersummary .= '<td align="center">'.$diff.' </td>';
	}
	$total_diff = $total_shipmentqty-$total_orderqty;
	$ordersummary .= "<td></td>";
	$ordersummary .= '<td align="center">'.$total_diff.' </td>';
	$ordersummary .= "<td></td>";
	$ordersummary .= "</tr>";


	$ordersummary .= "<tr>";
	$ordersummary .= "<td></td>";
	$ordersummary .= "<td>%</td>";
	foreach ($arrsize as $size) {
		$percent = $arr_shipmentqty[$size] / $arr_orderqty[$size] * 100;
		$percent = bcdiv($percent, 1, 2);
		$ordersummary .= '<td align="center">'.$percent.'% </td>';
	}
	$total_percent = $total_shipmentqty / $total_orderqty * 100;
	$total_percent = bcdiv($total_percent, 1, 2);
	$ordersummary .= "<td></td>";
	$ordersummary .= '<td align="center">'.$total_percent.'% </td>';
	$ordersummary .= "<td></td>";
	$ordersummary .= "</tr>";


}



$html .= <<<EOD
$ordersummary
	</table>

EOD;

$sql2 = $conn->query("SELECT GROUP_CONCAT(DISTINCT ch.ctn_measurement SEPARATOR ' / ') FROM tblcarton_picklist_head ch WHERE ch.shipmentpriceID='$spID' ");
$ctn_measurement = $sql2->fetchColumn();


$html .= <<<EOD
	<table class="full-border" cellpadding="2">
		<tr>
			<td style="width: 15%; "></td>
			<td style="width: 30%; "></td>
			<td style="width: 55%; "></td>
		</tr>

		<tr>
			<td>Total No. of Pcs: </td>
			<td>$pack_totalpcs</td>
			<td>For PRACHI EXPORTS</td>
		</tr>

		<tr>
			<td>Total No. of Ctns: </td>
			<td>$pack_ctn_qty</td>
			<td></td>
		</tr>

		<tr>
			<td>Total Nt. Wt: </td>
			<td>$pack_netweight KGS</td>
			<td></td>
		</tr>

		<tr>
			<td>Total Gr. Wt: </td>
			<td>$pack_grossweight KGS</td>
			<td></td>
		</tr>

		<tr>
			<td>Ctn Dimensions: </td>
			<td>$ctn_measurement</td>
			<td>Authorised Signatory</td>
		</tr>

	</table>
EOD;

	





}// end fetch $packsql

?>