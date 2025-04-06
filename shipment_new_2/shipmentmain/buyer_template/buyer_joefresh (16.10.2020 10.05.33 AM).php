<?php 
$pdf->SetTitle("Joe Fresh");
//==================== buyer "JOE FRESH" ; id: B13 =====================================//
// ============== pdf header ===================// 
$html .= <<<EOD
<table border="0">
	<tr>
		<th class="bold-text center-align">
			<h1>$ownership</h1>
		</th>
	</tr>

	<tr>
		<td class="center-align">
			$owneraddress
		</td>
	</tr>

	<tr>
		<td class="center-align">
			TEL : $ownertel &nbsp;&nbsp;&nbsp;&nbsp;FAX : $ownerfax
		</td>
	</tr>

</table>

<br>
<br>

EOD;

$html .= <<<EOD
		<table border="1" class="table-bordered" cellpadding="5">
			<tr>
				<th class="center-align"><b>Vendor (Name and Address)</b></th>
				<th class="center-align"><b>Manufacturer (Name and Address)</b></th>
			</tr>
			<tr>
				<td>$ownership <br>
					$owneraddress <br>
					TEL : $ownertel <br>
					FAX : $ownerfax
				</td>
				<td>$manufacturer <br>
					$manuaddress
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
	<table class="tbljoefresh" border="1" cellpadding="5">
		<tr>
			<td>
				<table>
					<tr>
						<td>Invoice Number: </td>
						<td align="center">$invoice_no</td>
					</tr>
				</table>
			</td>
			<td>
				<table>
					<tr>
						<td>Reference: </td>
						<td align="center"></td>
					</tr>
				</table>
			</td>
			<td class="td_short">
				<table>
					<tr>
						<td>Invoice Date: </td>
						<td align="center">$invoice_date</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td>
				<table>
					<tr>
						<td>Country of Origin: </td>
						<td align="center">$manucountry</td>
					</tr>
				</table>
			</td>
			<td>
				<table>
					<tr>
						<td>Discharge Port: </td>
						<td align="center">$countryName</td>
					</tr>
				</table>
			</td>
			<td class="td_short">
				<table>
					<tr>
						<td>Ship Date: </td>
						<td align="center">$shippeddate</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td>
				<table>
					<tr>
						<td>Condition of Sale: </td>
						<td align="center">$portLoading</td>
					</tr>
				</table>
			</td>
			<td>
				<table>
					<tr>
						<td>Payment Terms: </td>
						<td align="center">$paymentterm</td>
					</tr>
				</table>
			</td>
			<td class="td_short">
				<table>
					<tr>
						<td>Transhipment Country: </td>
						<td align="center">$countryName</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="width: 70%;">
				<table>
					<tr>
						<td style="width: 17.5%;">Transportation: </td>
						<td align="center" style="width: 56%;">BY $shipmode FROM $portLoading TO $countryName</td>
					</tr>
				</table>
			</td>
			
			<td class="td_short">
				<table>
					<tr>
						<td>Currency: </td>
						<td align="center">$CurrencyCode</td>
					</tr>
				</table>
			</td>
		</tr>	
	</table>
EOD;

$html .= <<<EOD
<br>
<br>
	<table border="1" cellpadding="5">
		<tr>
			<th>Consignee</th>
			<th>Buyer / Purchaser</th>
			<th>Ship To / Delivery</th>
			<th>Shipper / Exporter</th>
		</tr>

		<tr>
			<td>$conName <br>$conAddress</td>
			<td>$BuyerAddr</td>
			<td>
				$csn_address
			</td>
			<td>
				$manufacturer <br>
				$manuaddress
			</td>
		</tr>

	</table>
EOD;

// DHL ISC (HONG KONG) LTD., 10014-10021E,10/F., <br>
				// 10014-10021E,10/F. <br>
				// ATL LOGISTICS CENTRE B BERTH 3, <br>
				// KWAI CHUNG CONTAINER TERMINAL 

				//Notify: DHL ISC, 10/F ATL LOGISTICS CENTRE B, BERTH 3 <br>
						// KWAI CHUNG CONTAINER TERMINAL, KWAI CHUNG, HONG KONG <br>
						// WAREHOUSE CONTACT: <br>
						// HERMAN SHUM -+852 2765-5850 
$html .= <<<EOD
			<br>
			<br>
			<table style="width:50%;" border="1" cellpadding="5">
				<tr>
					<th class="center-align"><b>Notify Party</b></th>
				</tr>

				<tr>
					<td>
						$csn_address
					</td>
				</tr>

				<tr>
					<td>
						LC NO. : $lc_number <br>
						DATE : $lc_date<br>
						LC ISSUING BANK : $lc_bank
					</td>
				</tr>
			</table>
EOD;

//========================== po list =========================//
$html.= <<<EOD
	<br>
	<br>

	<table border="1" cellpadding="5">
		<!--tr>
			<th>PO No.</th>
			<th>Style Code</th>
			<th>Quantity</th>
			<th>Color Name</th>
			<th>Unit Price (US$)</th>
			<th>Total Price (US$)</th>
		</tr-->
		<tr>
			<th>PO No.</th>
			<th>Style Code</th>
			<th>Quantity</th>
			<th>Item Description</th>
			<th>HT Code</th>
			<th>NG Item</th>
			<th>Color Name</th>
			<th>Unit Price (US$)</th>
			<th>Total Price (US$)</th>
		</tr>
EOD;

$grand_total = 0;
$grand_qty   = 0;
$grand_nw    = 0;
$grand_gw    = 0;
$grand_ctn   = 0;
$detailsql = $conn->prepare("SELECT invd.*, g.styleNo,sp.BuyerPO FROM tblbuyer_invoice_detail invd 
					LEFT JOIN tblshipmentprice sp ON sp.ID=invd.shipmentpriceID 
					LEFT JOIN tblgarment g ON g.orderno=sp.Orderno
					WHERE invd.invID='$invID' AND invd.del=0
					GROUP BY invd.shipmentpriceID 
					ORDER BY invd.ID ASC ");
$detailsql->execute();
while($detailrow = $detailsql->fetch(PDO::FETCH_ASSOC)){
	$BuyerPO         = $detailrow["BuyerPO"];
	$spID            = $detailrow["shipmentpriceID"];
	$ht_code         = $detailrow["ht_code"];
	$shipping_remark = $detailrow["shipping_marking"];
	$styleNo         = $detailrow["styleNo"];

	//get ng item
	$sel_ngitem=$conn->prepare("SELECT GROUP_CONCAT(DISTINCT spd.SKU)
		FROM tblshipmentprice sp
		JOIN tblship_packing spk ON sp.ID=spk.shipmentpriceID
		JOIN tblship_packing_detail spd ON spk.PID=spd.PID
		WHERE sp.ID='$spID'
		");
	$sel_ngitem->execute();
	$ng_item=$sel_ngitem->fetchColumn();

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
								WHERE invd.invID='$invID' AND invd.shipmentpriceID='$spID' AND invd.other_charge='' AND invd.del=0
								group by invd.ID");
	$detailsql2->execute(); 
	$rowspan= $detailsql2->rowCount();
	$c=0;
	while($detailrow2 = $detailsql2->fetch(PDO::FETCH_ASSOC)) {
		$unit_price   = $detailrow2["fob_price"];
		$color_qty    = $detailrow2["qty"];
		$total_amount = $detailrow2["total_amount"];
		$colorID      = $detailrow2["colorID"];
		$ColorName    = $detailrow2["ColorName"];

		$item_desc = $detailrow2['shipping_marking'];
		$ht_code   = $detailrow2['ht_code'];
		$count_grp = $detailrow2['count_grp'];
		$gmt_unit  = ($count_grp>1? "SETS": "PCS");

		$grand_total += $total_amount;
		$grand_qty   += $color_qty;


$html .= <<<EOD
		<tr>
EOD;
		if($c == 0){
			$arr_value = $handle_fabInstore->getTotalPackingWeightByBuyerPO($spID, $od_FactoryID);
			$net_weight   = $arr_value["net_weight"];
			$gross_weight = $arr_value["gross_weight"];
			$count_ctn    = $arr_value["count_ctn"];
			
			$grand_nw  += $net_weight;
			$grand_gw  += $gross_weight;
			$grand_ctn += $count_ctn;
			
			$str_display = "";//"nw:$net_weight / gw:$gross_weight / ctn:$grand_ctn ";
			
$html .= <<<EOD
			<td rowspan="$rowspan">$BuyerPO $str_display</td>
			<td rowspan="$rowspan">$styleNo </td>
EOD;
		
		}
$html .= <<<EOD
			<td>$color_qty $gmt_unit</td>
			<td>$item_desc</td>
			<td>$ht_code</td>
			<td>$ng_item</td>
			<td>$ColorName</td>
			<td>$unit_price</td>
			<td>$total_amount</td>
		</tr>
EOD;

		$c++;

	}
}

$html .= <<<EOD
	</table>

	<table border="1" cellpadding="3">
		<tr>
			<td><b>Quantity: </b></td>
			<td>$grand_qty</td>
			<td><b>Total Net Weight (KG): </b></td>
			<td>$grand_nw</td>
			<td rowspan="2" valign="center"><b>INVOICE TOTAL</b></td>
			<td rowspan="2" valign="center">$ $grand_total</td>
		</tr>

		<tr>
			<td><b>Total Cartons: </b></td>
			<td>$grand_ctn</td>
			<td><b>Total Gross Weight (KG): </b></td>
			<td>$grand_gw</td>
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
	$orderno = $packrow["orderno"]; 

	
$html .= <<<EOD
	<br pagebreak="true">
EOD;

$html .= <<<EOD
<table border="0">
	<tr>
		<th class="bold-text center-align">
			<h1>$ownership</h1>
		</th>
	</tr>

	<tr>
		<td class="center-align">
			$owneraddress
		</td>
	</tr>

	<tr>
		<td class="center-align">
			TEL : $ownertel &nbsp;&nbsp;&nbsp;&nbsp; FAX : $ownerfax	
		</td>
	</tr>

</table>

<br>

<h2 class="center-align"><b><u>PACKING LIST</u></b></h2>
<br>

EOD;

// DHL ISC (HONG KONG) LTD., 10014-10021E,10/F., <br>
				// 10014-10021E,10/F. <br>
				// ATL LOGISTICS CENTRE B BERTH 3, <br>
				// KWAI CHUNG CONTAINER TERMINAL 

$html .= <<<EOD
	<table cellpadding="2" class="full-border">
		<tr>
			<td rowspan="2">DATE: </td>
			<td rowspan="2">$invoice_date</td>
			<td>INVOICE NO.: </td>
			<td>$invoice_no</td>
		</tr>

		<tr>
			<td>PURCHASE ORDER NO.: </td>
			<td>$BuyerPO</td>
		</tr>

		<tr>
			<td rowspan="2">TO: </td>
			<td rowspan="2">
				$conAddress
			</td>
			<td>VENDOR STYLE NO.: </td>
			<td>$styleNo</td>
		</tr>

		<tr>
			<td>LC NO.: </td>
			<td>$lc_number </td>
		</tr>
	</table>

EOD;

$arrsize = [];
$arrpick_totalsizeqty = []; // 
$arrpick_totalcsq = []; // total color size qty in this packing list

$size_thead = "";
$sizesql = $handle_class->getSizeNameColumnFromOrder($orderno,1);
//$size_colspan=$sizesql->rowCount();
$size_colspan = 0;
while($sizerow=$sizesql->fetch(PDO::FETCH_ASSOC)){
	$size_name = $sizerow["SizeName"];
	
	$sqlscsq  = "SELECT sum(scsq.qty) as qty 
				FROM tblship_colorsizeqty scsq 
				WHERE scsq.shipmentpriceID='$spID' and scsq.size_name='$size_name' 
				AND scsq.statusID=1 ";
	$stmt_scsq = $conn->prepare($sqlscsq);
	$stmt_scsq->execute();
	$row_scsq = $stmt_scsq->fetch(PDO::FETCH_ASSOC);
		$this_qty = $row_scsq["qty"];
	
	if($this_qty>0){
		$arrsize[] = $size_name;
	
		$arrpick_totalsizeqty[$size_name] = 0;

		$size_thead .= '<td align="center" style="width: 5%; ">'.$size_name.'</td>';
		$size_colspan++;
	}
}

$emptyrow = "<td></td><td></td><td></td><td></td>";
foreach ($arrsize as $value) {
	$emptyrow .= '<td style="width: 5%; "></td>';
}
$emptyrow .= "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
$thead_sizewidth = $size_colspan * 5;

$html .= <<<EOD
	<table cellpadding="2" border="1">
		<thead>
			<tr>
				<td rowspan="2">CARTON NO</td>
				<td rowspan="2">NG ITEMS</td>
				<td rowspan="2">UPC NO.</td>
				<td rowspan="2">COLOR</td>
				<td align="center" colspan="$size_colspan" style="width: $thead_sizewidth%; ">SIZE QUANTITY</td>
				<td rowspan="2">SUB TOTAL <br>PER CTN</td>
				<td rowspan="2">NO OF <br>CARTON</td>
				<td rowspan="2">TOTAL QTY <br>(PCS)</td>
				<td rowspan="2">GW (KGS)</td>
				<td rowspan="2">NW (KGS)</td>
				<td rowspan="2">T.GW (KGS)</td>
				<td rowspan="2">T.NW (KGS)</td>
				<td rowspan="2">NNW (KGS)</td>
				<td rowspan="2">TNNW (KGS)</td>
			</tr>
			<tr>
				$size_thead
			</tr>
		</thead>

		<!--<tr>
			$emptyrow
		</tr>-->
	
EOD;


$pack_ctn_qty = 0; // qty of ctn used in one po
$pack_netweight = 0;
$pack_grossweight = 0;
$pack_netnetweight = 0;
$pack_totalpcs = 0;
$totalpackqty = 0;
$totalcbm = 0;

$arrtotalsizeqty = [];

list($arr_row, $arr_all_size, $ctn_qty) = $handle_class->getAllPackingInfoByBuyerPO($spID, $od_FactoryID);
for($arr=0;$arr<count($arr_row);$arr++){
	$ctn_range     = $arr_row[$arr]["ctn_range"];
	$count_ctn     = $arr_row[$arr]["count_ctn"];
	$SKU           = $arr_row[$arr]["SKU"];
	$this_ctn_qty  = $arr_row[$arr]["this_ctn_qty"];
	$total_qty     = $arr_row[$arr]["total_qty"];
	$this_nnw      = $arr_row[$arr]["this_nnw"];
	$one_nnw       = round($this_nnw / $count_ctn, 3);
	$this_nw       = $arr_row[$arr]["this_nw"];
	$one_nw        = round($this_nw / $count_ctn, 3);
	$this_gw       = $arr_row[$arr]["this_gw"];
	$one_gw        = round($this_gw / $count_ctn, 3);
	$this_cbm      = $arr_row[$arr]["cbm_total"];
	
	$arr_grp_color = $arr_row[$arr]["arr_grp_color"];
	$count_grp     = count($arr_row[$arr]["arr_grp_color"]);
	$arr_size_info = $arr_row[$arr]["arr_size_info"];
	
$html .= "<tr>
		<td>$ctn_range </td>
		<td></td>
		<td></td>";
	for($g=0;$g<count($arr_grp_color);$g++){
		list($group_number, $sku) = explode("**%%^^",$arr_grp_color[$g]);
		
		$sqlSGC = "SELECT group_concat(c.colorName,' (',g.styleNo,')' separator '<br/>') as grp_color 
					FROM tblship_group_color sgc 
					INNER JOIN tblcolor c ON c.ID = sgc.colorID
					INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
					WHERE sgc.shipmentpriceID='$spID' AND sgc.group_number='$group_number' AND sgc.statusID=1";
		$stmt_sgc = $conn->query($sqlSGC);
		$row_sgc  = $stmt_sgc->fetch(PDO::FETCH_ASSOC);
			$grp_color = $row_sgc["grp_color"];
		
		if($g>0){
			$html .= "<tr>";
		}
			
		$html .= "<td>$grp_color</td>";
		foreach ($arrsize as $size) {
			$this_qty = $arr_size_info["$group_number"]["$size"];
			$html .= '<td class="center-align" style="width: 5%; ">'.$this_qty.'</td>';
			
			$arrpick_totalcsq["$group_number^=$grp_color"][$size] += $this_qty * $count_ctn;
		}
			
		if($g>0){
			$html .= "</tr>";
		}
	}//--- End For Color ---//

$pack_ctn_qty += $count_ctn;
$totalpackqty += $total_qty;
$pack_netweight += $this_nw;
$pack_grossweight += $this_gw;
$pack_netnetweight += $this_nnw;
$totalcbm += $this_cbm;

$html .= '
		<td align="center">'.$this_ctn_qty.'</td>
		<td align="center">'.$count_ctn.'</td>
		<td align="center">'.$total_qty.'</td>
		<td align="center">'.$one_gw.'</td>
		<td align="center">'.$one_nw.'</td>
		<td align="center">'.$this_gw.'</td>
		<td align="center">'.$this_nw.'</td>
		<td align="center">'.$one_nnw.'</td>
		<td align="center">'.$this_nnw.'</td>
		</tr>';
	
}//--- End Outer For Row ---//


/*$packsql2 = $conn->query("SELECT ch.PID, ch.ctn_num, ch.ctn_range, c.ColorName, COUNT(ch.ctn_num) as ctn_qty, c.ID as colorID, 
								ch.net_weight, ch.gross_weight, ch.net_net_weight, 
								ch.qty_in_blisterbag, ch.blisterbag_in_carton, ch.group_number, 
								(SELECT packing_method FROM tblship_packing spk WHERE spk.PID=ch.PID) as packing_method 
							FROM tblcarton_picklist_head ch 
							INNER JOIN tblship_group_color sgc ON sgc.group_number=ch.group_number 
																AND ch.shipmentpriceID=sgc.shipmentpriceID  
																AND sgc.statusID = 1
							INNER JOIN tblcolor c ON c.ID=sgc.colorID 
							WHERE ch.shipmentpriceID='$spID' 
							GROUP BY ch.ctn_range 
							ORDER BY ch.ctn_num limit 0");
while($packrow2 = $packsql2->fetch(PDO::FETCH_ASSOC)){
	$PID = $packrow2["PID"];
	$ctn_range = $packrow2["ctn_range"];
	$ColorName = $packrow2["ColorName"];
	$colorID   = $packrow2["colorID"];
	$group_number = $packrow2["group_number"];
	//$ctn_qty = $packrow2["ctn_qty"];
	$ctn_num = $packrow2["ctn_num"];
	$packing_method = $packrow2["packing_method"];
	list($start_ctn, $end_ctn) = explode("-", $ctn_range);
	$ctn_qty = $end_ctn - $start_ctn + 1;
	
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

	if(!isset($arrpick_totalcsq["$colorID^=$ColorName"])){
		foreach ($arrsize as $size) {		
			$arrpick_totalcsq["$colorID^=$ColorName"][$size] = 0;
		}
	}

$html .= <<<EOD
	<tr>
		<td>$ctn_range </td>
		<td></td>
		<td></td>
		<td>$ColorName </td>

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
			$arrpick_totalcsq["$colorID^=$ColorName"][$size] += $sizeqty_incarton * $ctn_qty;


			if($sizeqty_incarton == 0 || $sizeqty == -1){
				$sizeqty_incarton = "";
			}
$html .= <<<EOD
		<td class="center-align" style="width: 5%; ">$sizeqty_incarton</td>
EOD;


		} // end foreach


// print_r($arrtotalsizeqty); echo "<br>";
$totalqty = array_sum($arrtotalsizeqty);
$html .= <<<EOD

		<td align="center">$qty_in_carton</td>
		<td align="center">$ctn_qty</td>
		<td align="center">$totalqty</td>
		<td align="center">$gross_weight</td>
		<td align="center">$net_weight</td>
		<td align="center">$gross_weight2</td>
		<td align="center">$net_weight2</td>
		<td align="center">$net_net_weight</td>
		<td align="center">$net_net_weight2</td>
	</tr>

EOD;


}// end fetch $packsql2*/



//$totalpackqty = array_sum($arrpick_totalsizeqty); // total qty for the whole pick list
$html .= <<<EOD
		<tr>
			$emptyrow
		</tr>

		<tr class="font-blue">
			<td align="center" colspan="3">TOTAL</td>
			<td></td>
			<td colspan="$size_colspan"></td>
			<td></td>
			<td align="center">$pack_ctn_qty</td>
			<td align="center">$totalpackqty</td>
			<td></td>
			<td></td>
			<td align="center">$pack_grossweight</td>
			<td align="center">$pack_netweight</td>
			<td></td>
			<td align="center">$pack_netnetweight</td>
		</tr>


	</table>
<br>
<br>
EOD;

////////////////////////  color size breakdown  /////////////////////////
$html .= <<<EOD
	<h3><u>COLOR & SIZE BREAKDOWN </u></h3>
	<table cellpadding="2" border="1">
		<thead>
			<tr>
				<td style="width: 20%; ">COLOR</td>
				$size_thead
				<td align="center">TOTAL</td>
			</tr>
		</thead>

EOD;

		foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
			$color = explode("^=", $strcolor);
			$ColorName = $color[1];
			$str_csq = "";
			foreach ($arrsize as $size) {
				$sizeqty = $sizelist[$size];
				$str_csq .= '<td align="center" style="width: 5%; ">'.$sizeqty.'</td>';
			}

			$totalcolorqty = array_sum($sizelist);
$html .= <<<EOD
		<tr>
			<td style="width: 20%; ">$ColorName</td>
			$str_csq
			<td align="center">$totalcolorqty</td>
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
			<td align="center">$totalsizeqty</td>
EOD;
			}


$html .= <<<EOD
			<td align="center">$totalcolorsizeqty</td>
		</tr>

	</table>
<br>
<br>
EOD;


$sql2 = $conn->query("SELECT GROUP_CONCAT(DISTINCT ch.ctn_measurement SEPARATOR ' / ') FROM tblcarton_picklist_head ch WHERE ch.shipmentpriceID='$spID' ");
$ctn_measurement = $sql2->fetchColumn();

$html .= <<<EOD
	<table border="1" cellpadding="2">
		<tr>
			<td style="width: 15%; ">CTN MEASUREMENT</td>
			<td style="width: 30%; ">$ctn_measurement</td>
		</tr>

		<tr>
			<td>TTL CBM</td>
			<td>$totalcbm</td>
		</tr>

		<tr>
			<td>TOTAL GW: </td>
			<td>$pack_grossweight KGS</td>
		</tr>

		<tr>
			<td>TOTAL NW: </td>
			<td>$pack_netweight KGS</td>
		</tr>

		<tr>
			<td>TOTAL NNW: </td>
			<td>$pack_netnetweight KGS</td>
		</tr>

	</table>

EOD;



} // end fetch $packsql




?>