<?php 

//$pdf->SetFont('times', '', 6, '', false);
$pdf->SetTitle("BUFFALO - $title");
// ============== pdf header ===================//
$html .= <<<EOD
<table border="0">
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

$arr_orderno = explode(",", $orderno);
$soID = $arr_orderno[0];
$mode = 1;
$stmt = $handle_class->getSizeNameColumnFromOrder($soID, $mode);
$count_size = $stmt->rowCount();
$colspan      = 8 + $count_size;
$colspan_left = 5 + $count_size;

$freight = '';
if (strpos($tradeterm, 'FOB') !== false) {
    $freight =  'COLLECT';
}

$html .= <<<EOD
		<table border="1" class="table-bordered" cellpadding="5">
			<tr>
				<th colspan="$colspan" class="center-align">$letterhead_title </th>
				</tr>
			<tr>
				<td colspan="$colspan" style="background-color:#bdbdbd">SOLD TO/INVOICE TO </td>
				</tr>
			<tr>
				<td colspan="$colspan_left">
					$bill_to<br/>
					$bill_address<br/><br/><br/></td>
				<td colspan="3" rowspan="5">
				<table cellpadding="2">
				<tr>
					<td>Invoice Date:</td>
					<td>$invoice_date</td>
					</tr>
				<tr>
					<td>Invoice No.:</td>
					<td>$invoice_no</td>
					</tr>
				<tr>
					<td>Terms:</td>
					<td>$paymentterm</td>
					</tr>
				<tr>
					<td>Costco P.O.#:</td>
					<td>$remarks</td>
					</tr>
				<tr>
					<td>Costco Item#:</td>
					<td>$remarks</td>
					</tr>
				<tr>
					<td>Store#:</td>
					<td></td>
					</tr>
				<tr>
					<td>Season:</td>
					<td>$season</td>
					</tr>
				<tr>
					<td>Brand:</td>
					<td>$brand</td>
					</tr>
				<tr>
					<td>Style Name:</td>
					<td>$style_no</td>
					</tr>
				<tr>
					<td>Style#:</td>
					<td>$style_no</td>
					</tr>
				<tr>
					<td>Lot#:</td>
					<td></td>
					</tr>
				<tr>
					<td>Ship Via:</td>
					<td>$shipmode</td>
					</tr>
				<tr>
					<td>Freight:</td>
					<td>$freight</td>
					</tr>
				<tr>
					<td>F.O.B:</td>
					<td>$tradeterm</td>
					</tr>
				<tr>
					<td>Port Of Loading:</td>
					<td>$portLoading</td>
					</tr>
				<tr>
					<td>Port Of Discharge:</td>
					<td>$portDischarges</td>
					</tr>
				<tr>
					<td>Place of Delivery:</td>
					<td>$buyerdest</td>
					</tr>
					</table>
				</td>
				</tr>
			<tr>
				<td colspan="$colspan_left" style="background-color:#bdbdbd">CONSIGNEE</td>
				</tr>
			<tr>
				<td colspan="$colspan_left">
					$conName<br/>$conAddress
					<br/><br/><br/><br/></td>
				</tr>
			<tr>
				<td colspan="$colspan_left" style="background-color:#bdbdbd">SHIP TO</td>
				</tr>
			<tr>
				<td colspan="$colspan_left">
				$ship_to<br/>$ship_address
				</td>
				</tr>
			
EOD;

$query_filter = ",bid.shipmentpriceID";
// $arr_array    = $handle_class->getBuyerInvoiceDescriptionInfoMethod2($invID, $query_filter);
// $arr_fabric   = $arr_array["byFabric"];

$arr_array    = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_fabric   = $arr_array["byFabric"];

$arr_size = array();

$css_size_wd = $count_size * 4;
$css_goods = (100 - $css_size_wd) * 0.25;
$css_color = (100 - $css_size_wd - $css_goods) * 0.18;
$css_ctn   = (100 - $css_size_wd - $css_goods - $css_color) / 6;


$css_pcs = 0;
$css_cost = 0;
$css_amt = 0;
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	$size = $row["SizeName"];
	$arr_size[] = $size;
}

foreach($arr_fabric as $shipping_marking => $arr_info){
	$html .= '<tr>
				<td colspan="'.$colspan.'" align="center" style="background-color:#f3f3f4">DESCRIPTION OF GOODS: '.$shipping_marking.'</td></tr>';
	$html .= '<tr>
				<td rowspan="2" style="width:'.$css_ctn.'%">CTN NO.</td>
				<td rowspan="2" style="width:'.$css_color.'%">COLOR </td>
				<td colspan="'.$count_size.'" style="width:'.$css_size_wd.'%">SIZE</td>
				<td rowspan="2" style="width:'.$css_goods.'%">DESCRIPTION OF GOODS</td>
				<td rowspan="2" style="width:'.$css_ctn.'%">TOTAL PCS</td>
				<td rowspan="2" style="width:'.$css_ctn.'%">TOTAL CARTONS</td>
				<td rowspan="2" style="width:'.$css_ctn.'%">TOTAL QUANTITY (UNITS)</td>
				<td rowspan="2" style="width:'.$css_ctn.'%">UNIT COST ('.$CurrencyCode.')</td>
				<td rowspan="2" style="width:'.$css_ctn.'%">TOTAL AMOUNT</td>
				</tr>';
	$html .= '<tr>';
	for($s=0;$s<count($arr_size);$s++){
		$size = $arr_size[$s];
		$html .= '<td style="width:4%">'.$size.'</td>';	
		
	}
	$html .= '</tr>';
	
	$grand_ctn = 0;
	$grand_qty = 0;
	$grand_amt = 0;
	
	//echo "$shipping_marking ".count($arr_info)."<br/>";
	
	for($i=0;$i<count($arr_info);$i++){
		$shipmentpriceID  = $arr_info[$i]["shipmentpriceID"];
		$total_ctn        = $arr_info[$i]["total_ctn"];
		$arr_all_size     = $arr_info[$i]["arr_all_size"];
		$arr_group_number = $arr_info[$i]["arr_group_number"];
		$count_grp = count($arr_group_number);
		
		//echo "---- $shipmentpriceID ".count($arr_group_number)."<br/>";
		
		$count_row = 0;
		foreach($arr_group_number as $key => $arr_details){
			$count_row++;
			$this_group_number = substr($key,1);
			$color     = $arr_details["color"];
			$garmentID = $arr_details["garmentID"];
			$colorID   = $arr_details["colorID"];
			$fob_price = $arr_details["fob_price"];
			
			$html .= '<tr>';
			$html .= ($count_row==1? '<td rowspan="'.$count_grp.'">1 - '.$total_ctn.'</td>': '');
			$html .= '<td>'.$color.'</td>';
			
			$sub_qty = 0;
			for($s=0;$s<count($arr_size);$s++){
				$this_size = $arr_size[$s];
				$qty = (array_key_exists("$this_group_number**^^$this_size", $arr_all_size)? 
							$arr_all_size["$this_group_number**^^$this_size"]: 0);
				
				//echo "$qty $this_group_number / $this_size << <br/>";
				
				$html .= '<td>'.$qty.'</td>';
				$sub_qty += $qty;
			}
			$sub_amt = $sub_qty * $fob_price; 
			
			$grand_qty += $sub_qty;
			$grand_amt += $sub_amt;
			
			$str_amt = number_format($sub_amt, 2);
			
			$sqlBID = "SELECT bid.shipping_marking
						FROM $tblbuyer_invoice_detail bid 
						WHERE bid.shipmentpriceID='$shipmentpriceID' AND group_number='$this_group_number' AND bid.del=0";
			$stmt = $conn->prepare($sqlBID);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$shipping_marking = $row["shipping_marking"];
			
			$html .= '<td>'.$shipping_marking.'</td>';
			$html .= '<td align="center">'.$sub_qty.'</td>';
			$html .= ($count_row==1? '<td rowspan="'.$count_grp.'" align="center">'.$total_ctn.'</td>': '');
			$html .= '<td align="center">'.$sub_qty.'</td>';
			$html .= '<td align="center">'.$fob_price.'</td>';
			$html .= '<td align="center">'.$str_amt.'</td>';
			$html .= '</tr>';
		}
		$grand_ctn += $total_ctn;
	}//--- End For Arr_info ---//*/
	
	$str_grand_amt = number_format($grand_amt, 2);
	
	$colspan_tt = $count_size + 2;
	$html .= '<tr>';
	$html .= '<td></td>';
	$html .= '<td colspan="'.$colspan_tt.'">TOTAL: CTNS & PCS</td>';
	$html .= '<td></td>';
	$html .= '<td align="center">'.$grand_ctn.'</td>';
	$html .= '<td align="center">'.$grand_qty.'</td>';
	$html .= '<td>SUB TOTAL ('.$CurrencyCode.'):</td>';
	$html .= '<td align="center">'.$str_grand_amt.'</td>';
	$html .= '</tr>';
	
}//--- End Foreach ---//*/

$html .= '<tr>';
$html .= '<td colspan="'.$colspan.'">COUNTRY OF ORIGIN: '.$manucountry.'</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td colspan="'.$colspan.'">MANUFACTURER<br/>'.$manufacturer.'<br/>'.$manuaddress.'<br/>Tel:'.$manutel.' / Fax:'.$manufax.'</td>';
$html .= '</tr>';


$html .= <<<EOD
	</table>
EOD;

//= =============================== packing list ====================================//
$this_html = $handle_lc->getBuyerInvoicePackingListTemplate3($invID);
$html .= $this_html;

?>