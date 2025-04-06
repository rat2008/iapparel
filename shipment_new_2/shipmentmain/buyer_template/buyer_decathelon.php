<?php 
//====================================== buyer "DECATHELON" ; id: B49 =====================================//
// ============== pdf header ===================//
$html .= <<<EOD
<table border="0">
	<tr>
		<th class="bold-text center-align"><h1>IK APPAREL CO.,LTD</h1></th>
	</tr>

	<tr>
		<td class="center-align">
			NATIONAL ROAD 3, ANLONG ROMEIT WEST COMMUNE, KANDAL STUENG DISTRICT, <br>
			KANDAL PROVINCE, KINGDOM OF CAMBODIA
		</td>
	</tr>

	<tr>
		<td class="center-align">TEL : 855-23 96 98 98
		&nbsp;&nbsp;&nbsp;&nbsp;FAX : 855-23 96 90 90</td>
	</tr>

</table>

EOD;

$html .= <<<EOD
<br>
<br>

<h2 class="center-align">COMMERCIAL INVOICE</h2>
<br>
EOD;

$html .= <<<EOD
		<table border="1" cellpadding="3">

			<tr>
				<td valign="top" rowspan="3">
					Consignee: <br/>
					$conName, 
					<p class="p-format">$conAddress</p>
					<br/>
				</td>

				<td valign="top">
					Invoice no. and date: <br/>
					$invoice_no / $invoice_date
				</td>
			</tr>

			<tr>
				<td valign="top">
					L/C No. and Date: $lc_number ($lc_date)<br/>

				</td>
			</tr>

			<tr>
				<td valign="top">
					Buyer: <br/>
					$BuyerName
				</td>
			</tr>

			<tr>
				<td valign="top">
					Shipped date: <br/>
					$shippeddate
				</td>

				<td valign="top" rowspan="2">
					Other reference: <br>
					
				</td>
			</tr>

			<tr>
				<td valign="top" rowspan="2">
					Vessel/Flight from: $shipmode <br/>
					FROM: $portLoading
				</td>
			</tr>

			<tr>
				<td valign="top" rowspan="2">
					Trade term: <br/>
					$tradeterm <br/>
					<br>
					Payment term: <br/>
					$paymentterm <br/>
				</td>
			</tr>

			<tr>
				<td valign="top">
					TO: $countryName
				</td>
			</tr>

		</table>

EOD;

// ========================= buyerpo list ==================================//

$html .= <<<EOD
<br/>
<br/>
<br/>

<table border="1" cellpadding="5">
	<thead>
		<tr>
			<th>BuyerPO</th>
			<th>StyleNo</th>
			<th>Color Name</th>
			<th>Qty </th>
			<th>Unit Price (US$)</th>
			<th>Total Price (US$)</th>
		</tr>
	<thead>
EOD;

$grand_total = 0;
$grand_qty = 0;		
$detailsql = $conn->prepare("SELECT invd.*, g.styleNo,sp.BuyerPO
						FROM tblbuyer_invoice_detail invd 
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
		$group_number = $detailrow2["group_number"];
		$count_grp = $detailrow2["count_grp"];
		$gmt_unit  = ($count_grp>1? "SETS": "PCS");

		$grand_total += $total_amount;
		$grand_qty += $color_qty;
$html .= <<<EOD
	<tr>
EOD;

		if($c == 0){

$html .= <<<EOD
	<td rowspan="$rowspan">$BuyerPO</td>
	<td rowspan="$rowspan">$styleNo</td>
EOD;
		}

$html .= <<<EOD
	<td>$ColorName</td>
	<td>$color_qty $gmt_unit</td>
	<td>$unit_price</td>
	<td>$total_amount</td>
	
EOD;

$html .= <<<EOD
	</tr>
EOD;

		$c++;
	}

}
$grand_total = number_format($grand_total, 2,".","");
$str_grandtotal = numberTowords($grand_total);
$html .= <<<EOD
		<tr>
			<td colspan="3" align="right"><b>Total Qty: </b></td>
			<td><b>$grand_qty </b></td>
			<td align="right"><b>Grand Total: </b></td>
			<td><b>$grand_total </b></td>
		</tr>
	</table>
	<h3 class="center-align">(SAY: $str_grandtotal US DOLLARS ONLY)</h3>
EOD;



//= =============================== packing list ====================================//
$packsql = $conn->prepare("SELECT invd.*, g.styleNo, sp.Orderno,sp.BuyerPO FROM tblbuyer_invoice_detail invd 
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
	$orderno = $packrow["Orderno"];

$html .= <<<EOD
	<br pagebreak="true">
EOD;

$html .= <<<EOD
<table border="0">
	<tr>
		<th class="bold-text center-align">
			<h1>I APPAREL INTERNATIONAL GROUP PRIVATE LIMITED </h1>
		</th>
	</tr>

	<tr>
		<td class="center-align">
			7 KALLANG  PLACE, # 02-08 
		</td>
	</tr>

	<tr>
		<td class="center-align">
			SINGAPORE 339513
		</td>
	</tr>

	<tr>
		<td class="center-align">
			TEL : +65 62929339 & FAX : +65 62929339 	
		</td>
	</tr>

</table>

<br>

<h2 class="center-align"><b><u>PACKING LIST($spID)</u></b></h2>
<br>

EOD;


$html .= <<<EOD
	<table cellpadding="2" border="1">
		<tr>
			<td style="width: 40%; ">CONSIGNEE / BUYER </td>
			<td style="width: 20%; ">INVOICE NO.: </td>
			<td style="width: 20%; ">$invoice_no</td>
			<td style="width: 20%; ">DATE: </td>
		</tr>

		<tr>
			<td rowspan="7">$conName <br>$conAddress</td>
			<td>IA NO.: </td>
			<td>$orderno</td>
			<td>$invoice_date</td>
		</tr>

		<tr>
			<td>STYLE NO.: </td>
			<td>$styleNo</td>
		</tr>

		<tr>
			<td>PO NO.: </td>
			<td>$BuyerPO</td>
		</tr>

		<tr>
			<td>ON/ABT: </td>
			<td>$shippeddate</td>
		</tr>

		<tr>
			<td>FROM: </td>
			<td>$portLoading</td>
		</tr>

		<tr>
			<td>TO: </td>
			<td>$countryName</td>
		</tr>

		<tr>
			<td>DESCRIPTION: </td>
			<td></td>
		</tr>



	</table>

EOD;

$emptyrow = "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";

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

	$size_thead .= '<td align="center">'.$size_name.'</td>';
	$emptyrow .= "<td></td>";
}


$html .= <<<EOD
	<table cellpadding="2" border="1">
		<thead>
			<tr>
				<td>CTN NO.</td>
				<td>TTL CTN</td>
				<td>ITEM</td>
				<td>COLOR</td>
				$size_thead
				<td>TTL PCS/CTN</td>
				<td>TTL QTY PCS</td>
				<td>NNW/CTN <br>(KGS)</td>
				<td>NW/CTN <br>(KGS)</td>
				<td>GW/CTN <br>(KGS)</td>
			</tr>
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
								ch.qty_in_blisterbag, ch.blisterbag_in_carton , 
								(SELECT packing_method FROM tblship_packing spk WHERE spk.PID=ch.PID) as packing_method 
							FROM tblcarton_picklist_head ch 
							LEFT JOIN tblship_group_color sgc ON sgc.group_number=ch.group_number AND ch.shipmentpriceID=sgc.shipmentpriceID 
							LEFT JOIN tblcolor c ON c.ID=sgc.colorID 
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
	$temp_blisterbag_in_carton = $blisterbag_in_carton;
	$qty_in_carton = $qty_in_blisterbag*$blisterbag_in_carton;

$html .= <<<EOD
		<tr>
			<td>$ctn_range</td>
			<td align="center">$ctn_qty</td>
			<td></td>
			<td>$ColorName</td>
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
			
			////////////////////////////////////////////////////////////////////
			$sizeqty = $arrsizeqty[$size];
			$sizeqty_incarton=0;

			if($sizeqty == -1){
				$sizeqty_incarton = 0;
			}else{
				if($packing_method == 2)
					$temp_blisterbag_in_carton = 1;

				$sizeqty_incarton = $sizeqty * 1 * $temp_blisterbag_in_carton;
				$arrtotalsizeqty[$size] += $sizeqty_incarton * $ctn_qty;
				$arrpick_totalsizeqty[$size] += $sizeqty_incarton * $ctn_qty;

			}

			// for the color & size breakdown
			if (array_key_exists("$colorID^=$ColorName",$arrpick_totalcsq)){
				if (array_key_exists("$size",$arrpick_totalcsq["$colorID^=$ColorName"])){
					$arrpick_totalcsq["$colorID^=$ColorName"][$size] += $sizeqty_incarton * $ctn_qty;
				}
				else{
					$arrpick_totalcsq["$colorID^=$ColorName"][$size] = $sizeqty_incarton * $ctn_qty;
				}
			}
			else{
				$arrpick_totalcsq["$colorID^=$ColorName"][$size] = $sizeqty_incarton * $ctn_qty;
			}

			
			if($sizeqty_incarton == 0 || $sizeqty == -1){
				$str_sizeqty_incarton = "";
			}else{
				$str_sizeqty_incarton = $sizeqty_incarton * $ctn_qty;
			}


$html .= <<<EOD
		<td class="center-align">$str_sizeqty_incarton</td>
EOD;


		} // end foreach


$totalqty = array_sum($arrtotalsizeqty);
$html .= <<<EOD
			<td align="center">$qty_in_carton</td>
			<td align="center">$totalqty</td>
			<td align="center">$net_net_weight</td>
			<td align="center">$net_weight</td>
			<td align="center">$gross_weight</td>
		</tr>
EOD;


}// end fetch $packsql2


$pack_totalpcs = array_sum($arrpick_totalsizeqty); // total qty for the whole pick list
$html .= <<<EOD
		<tr>
			$emptyrow
		</tr>
		<tr>
			$emptyrow
		</tr>

		<tr class="font-blue">
			<td>TOTAL</td>
			<td align="center">$pack_ctn_qty</td>
			<td></td>
			<td></td>
			<td colspan="$size_colspan"></td>
			<td></td>
			<td align="center">$pack_totalpcs</td>
		</tr>


	</table>

EOD;



////////////////////////  color size breakdown  /////////////////////////
$html .= <<<EOD
	<br>
	<br>
	<table cellpadding="2" border="1">
		<thead>
			<tr>
				<td>COLOR</td>
				$size_thead
				<td>TOTAL</td>
			</tr>
		</thead>
	
EOD;

	//$emptyrow2 = "<td></td><td></td>";
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
				$str_csq .= '<td align="center">'.$sizeqty.' </td>';

				//$emptyrow2 .= "";
			}

			$totalcolorqty = array_sum($sizelist);
$html .= <<<EOD
			$str_csq
			<td>$totalcolorqty</td>
		</tr>
EOD;
		}

$count_size = count($arrsize);

$html .= "
		<tr>
			<td></td>
			<td></td>";
			
for($cc=0;$cc<$count_size;$cc++){
	$html .= "<td></td>";
}

$html .= "</tr>";

$html .=<<<EOD
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
			<td align="center">$totalsizeqty</td>
EOD;
			}

$sql2 = $conn->query("SELECT GROUP_CONCAT(DISTINCT ch.ctn_measurement SEPARATOR ' / ') FROM tblcarton_picklist_head ch WHERE ch.shipmentpriceID='$spID' ");
$ctn_measurement = $sql2->fetchColumn();
$html .= <<<EOD
			<td>$totalcolorsizeqty</td>
		</tr>

	</table>
<!--<br>
<br>

	<table style="width: 50%; " cellpadding="2">
		<tr>
			<td>TOTAL CARTONS: </td>
			<td>$pack_ctn_qty CTNS </td>
		</tr>

		<tr>
			<td>TOTAL QUANTITY: </td>
			<td>$pack_totalpcs PCS </td>
		</tr>

		<tr>
			<td>TOTAL NET NET WEIGHT: </td>
			<td>$pack_netnetweight KGS </td>
		</tr>

		<tr>
			<td>TOTAL NET WEIGHT: </td>
			<td>$pack_netweight KGS </td>
		</tr>

		<tr>
			<td>TOTAL GROSS WEIGHT: </td>
			<td>$pack_grossweight KGS </td>
		</tr>

		<tr>
			<td>CTN MEASUREMENT: </td>
			<td>$ctn_measurement </td>
		</tr>
	</table>-->

EOD;
//*/

} // end fetch $packsql


?>