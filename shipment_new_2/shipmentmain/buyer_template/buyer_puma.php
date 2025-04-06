<?php 
$pdf->SetTitle("Puma ");

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

$invoice_date = date_format(date_create($invoice_date), "M.d.Y");
$lc_date = date_format(date_create($lc_date), "M.d.Y"); //class="table-bordered"
$shippeddate = date_format(date_create($shippeddate), "M.d.Y"); //class="table-bordered"

$html .= <<<EOD
		<table border="1"  cellpadding="5">
			<tr>
				<th colspan="4" class="center-align full-border">$letterhead_title</th>
				</tr>
			<tr>
				<td colspan="2" class="border_left border_right border_top">Shipper/Exporter</td>
				<td colspan="2" class="border_left border_right border_top">No. & Date of Invoice</td>
				</tr>
			<tr>
				<td colspan="2" rowspan="3" class="border_left border_right border_btm">$shipper<br/>$shipper_addr<br/>TEL:$shipper_tel &nbsp; &nbsp; FAX:$shipper_fax</td>
				<td align="center" class="border_left border_btm">$invoice_no</td>
				<td align="center" class="border_right border_btm">$invoice_date</td>
				</tr>
			<tr>
				<td colspan="2" class="border_left border_right border_top">No. & Date of L/C</td>
				</tr>
			<tr>
				<td align="center" class="border_left border_btm">$lc_number</td>
				<td align="center" class="border_right border_btm">$lc_date</td>
				</tr>
			<tr>
				<td colspan="2" class="border_left border_right border_top">Applicant</td>
				<td colspan="2" class="border_left border_right border_top">L/C Issuing Bank</td>
				</tr>
			<tr>
				<td colspan="2" rowspan="3" class="border_left border_right border_btm">$poissuer<br/>$poissuer_address</td>
				<td colspan="2" class="border_left border_right border_btm">$lc_bank</td>
				</tr>
			<tr>
				<td colspan="2" class="border_left border_right border_top">Consignee</td>
				</tr>
			<tr>
				<td colspan="2" class="border_left border_right border_btm">$conName</td>
				</tr>
			<tr>
				<td colspan="2" class="border_left border_right border_top">
					Notify Party<br/>
					$notify_party<br/>
					$notify_address
					</td>
				<td colspan="2" class="border_left border_right border_top">Remarks</td>
				</tr>
			<tr>
				<td colspan="2" class="border_left border_right border_btm"></td>
				<td colspan="2" class="border_left border_right">$remarks</td>
				</tr>
			<tr>
				<td class="border_left border_right border_top">Port of Loading</td>
				<td class="border_left border_right border_top">Final Destination</td>
				<td colspan="2" valign="baseline" rowspan="4" class="border_left border_right border_btm">
					<table>
						<tr>
							<td style="width:25%">COUNTRY OF ORIGIN</td>
							<td>$manucountry</td>
							</tr>
						<tr>
							<td>CONTAINER NO.</td>
							<td>$container_no</td>
							</tr>
						<tr>
							<td>SEAL NO.</td>
							<td>$seal_no</td>
							</tr>
							</table>
				</td>
				</tr>
			<tr>
				<td align="center" class="border_left border_right border_btm">$portLoading</td>
				<td align="center" class="border_left border_right border_btm">$buyerdest</td>
				</tr>
			<tr>
				<td class="border_left border_right border_top">Carrier</td>
				<td class="border_left border_right border_top">Sailing on for about</td>
				</tr>
			<tr>
				<td align="center" class="border_left border_right border_btm">$carrier</td>
				<td align="center" class="border_left border_right border_btm">$shippeddate</td>
				</tr>
		</table>	
				
EOD;

$html .= '<table cellpadding="5">';
$html .= '<tr>';
$html .= '<td class="full-border">MARKS</td>
		  <td colspan="5" class="full-border">DESCRIPTION</td>
		  <td class="full-border">COLOR</td>
		  <td class="full-border">SIZE</td>
		  <td class="full-border">QTY</td>
		  <td class="full-border">PRODUCT</td>
		  <td class="full-border">UNIT PRICE</td>
		  <td class="full-border">PRICE BREAK</td>
		  <td class="full-border">AMOUNT</td>
		  </tr>';
// $query_filter = "AND cpt.shiped=1";
// $arr_array   = $handle_class->getBuyerInvoiceDescriptionInfo($invID, $query_filter);  
// $arr_buyerpo = $arr_array["byBuyerPO"];
// $arr_fabric  = $arr_array["byFabric"];

$query_filter = "";
$arr_array    = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_buyerpo  = $arr_array["byBuyerPO"];
$arr_fabric   = $arr_array["byFabric"];

$grand_nw  = 0;
$grand_gw  = 0;
$grand_ctn = 0;
$grand_cbm = 0;
$count_row = 2;
$str_htcode = "";

foreach($arr_fabric as $ship_marking => $arr_info){
		$count_row += 3;//2;
		for($arr=0;$arr<count($arr_info);$arr++){
			$arr_skucolorsize  = $arr_info[$arr]["arr_skucolorsize"];
			$arr_skuOnly  = $arr_info[$arr]["arr_skuOnly"];
			$arr_size  = $arr_info[$arr]["arr_all_size"];
			$arr_FOB   = $arr_info[$arr]["arr_FOB"];
			$uom       = $arr_info[$arr]["uom"];
			$this_nnw = $arr_info[$arr]["grand_nnw"];
			$this_nw  = $arr_info[$arr]["grand_nw"];
			$this_gw  = $arr_info[$arr]["grand_gw"];
			$this_cbm = $arr_info[$arr]["grand_cbm"];
			$this_ctn = $arr_info[$arr]["total_ctn"];
			$quotacat = $arr_info[$arr]["quotacat"];
			$ht_code  = $arr_info[$arr]["ht_code"];
			
			$grand_nw  += $this_nw;
			$grand_gw  += $this_gw;
			$grand_ctn += $this_ctn;
			$grand_cbm += $this_cbm;
			$str_htcode = $ht_code;
			$count_row++;
			
			foreach($arr_skuOnly as $SKU => $arr_value){
				
				foreach($arr_value as $key => $arr_result){
					$count_row++;
				}
				$count_row++;
			}//--- End Foreach FOB ---//
		}//--- END FOR ---//
		
		$count_row++;
	}//--- End Foreach Fabric ---//

$html .= '<tr>';
$html .= '<td class="border_left border_right border_top border_btm" rowspan="'.$count_row.'">
				<br/>
				GW: '.$grand_gw.' KGS<br/><br/>
				NW: '.$grand_nw.' KGS<br/><br/>
				CBM: '.$grand_cbm.'
				</td>';
$html .= '<td class="border_left border_right border_top " align="right">TOTAL</td>';
$html .= '<td class="border_left border_right border_top " align="center">'.$grand_ctn.'</td>';
$html .= '<td class="border_left border_right border_top ">CARTONS</td>';
$html .= '<td class="border_left border_right "></td>';
$html .= '<td class="border_left border_right "></td>';
$html .= '<td class="border_left border_right "></td>';
$html .= '<td class="border_left border_right "></td>';
$html .= '<td class="border_left border_right "></td>';
$html .= '<td class="border_left border_right "></td>';
$html .= '<td class="border_left border_right border_top " colspan="3" >'.$tradeterm.'</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td class="border_left border_right" colspan="2" >HTS CODE: </td>';
$html .= '<td class="border_left border_right" >'.$str_htcode.'</td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" colspan="2"></td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td class="border_left border_right" colspan="2" > </td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '<td class="border_left border_right" colspan="2"></td>';
$html .= '</tr>';

$grand_qty = 0;
$grand_amt = 0;
foreach($arr_fabric as $ship_marking => $arr_info){
	$html .= '<tr>';
	$html .= '<td class="border_left border_right" colspan="4" >'.$ship_marking.'</td>';
	$html .= '<td class="border_left border_right" ></td>';
	$html .= '<td class="border_left border_right" ></td>';
	$html .= '<td class="border_left border_right" ></td>';
	$html .= '<td class="border_left border_right" ></td>';
	$html .= '<td class="border_left border_right" ></td>';
	$html .= '<td class="border_left border_right" ></td>';
	$html .= '<td class="border_left border_right" colspan="2"></td>';
	$html .= '</tr>';
	
	
	
	for($arr=0;$arr<count($arr_info);$arr++){
		$arr_skucolorsize  = $arr_info[$arr]["arr_skucolorsize"];
		$arr_skuOnly  = $arr_info[$arr]["arr_skuOnly"];
		$arr_size  = $arr_info[$arr]["arr_all_size"];
		$arr_FOB   = $arr_info[$arr]["arr_FOB"];
		$BuyerPO   = $arr_info[$arr]["BuyerPO"];
		$styleNo   = $arr_info[$arr]["styleNo"];
		$uom       = $arr_info[$arr]["uom"];
		$this_nnw = $arr_info[$arr]["grand_nnw"];
		$this_nw  = $arr_info[$arr]["grand_nw"];
		$this_gw  = $arr_info[$arr]["grand_gw"];
		$this_ctn = $arr_info[$arr]["total_ctn"];
		$quotacat = $arr_info[$arr]["quotacat"];
		$ht_code  = $arr_info[$arr]["ht_code"];
		$CurrencyCode  = $arr_info[$arr]["CurrencyCode"];
		$product_type  = $arr_info[$arr]["product_type"];
		$arr_prodtype = explode("_", $product_type);
		$str_producttype = $arr_prodtype[0];
		
			$html .= '<tr>';
			$html .= '<td class="border_left border_right border_btm" >PO NO.</td>';
			$html .= '<td class="border_left border_right border_btm" >ITEM #</td>';
			$html .= '<td class="border_left border_right border_btm" >STYLE NO.</td>';
			$html .= '<td class="border_left border_right border_btm" >CONTENT</td>';
			$html .= '<td class="border_left border_right border_btm" >UPC CODE</td>';
			$html .= '<td class="border_left border_right border_btm" ></td>';
			$html .= '<td class="border_left border_right border_btm" ></td>';
			$html .= '<td class="border_left border_right border_btm" ></td>';
			$html .= '<td class="border_left border_right border_btm" ></td>';
			$html .= '<td class="border_left border_right border_btm" ></td>';
			$html .= '<td class="border_left border_right border_btm" colspan="2"></td>';
			$html .= '</tr>';
		
		foreach($arr_skuOnly as $SKU => $arr_value){
			//list($SKU, $group_number, $size_name) = explode("**^^", $color_size);
			
			$sub_qty = 0;
			$sub_amt = 0;
			
			foreach($arr_value as $key => $arr_result){
				list($group_number, $size_name) = explode("**^^", $key);
				$qty       = $arr_result["qty"];
				$color     = $arr_result["color"];
				$fob_price = $arr_result["fob_price"];
				$upc_code  = $arr_result["upc_code"];
				$this_amt  = $fob_price * $qty;
				$str_amt   = number_format($this_amt, 2);
				
				$html .= '<tr>';
				$html .= '<td class="border_left border_right" >'.$BuyerPO.'</td>';
				$html .= '<td class="border_left border_right" >'.$SKU.'</td>';
				$html .= '<td class="border_left border_right" >'.$styleNo.'</td>';
				$html .= '<td class="border_left border_right" >'.$ship_marking.'</td>';
				$html .= '<td class="border_left border_right" >'.$upc_code.'</td>';
				$html .= '<td class="border_left border_right" >'.$color.'</td>';
				$html .= '<td class="border_left border_right" align="center">'.$size_name.'</td>';
				$html .= '<td class="border_left border_right" align="center">'.$qty.'</td>';
				$html .= '<td class="border_left border_right" align="center">'.$str_producttype.'</td>';
				$html .= '<td class="border_left border_right" align="center">'.$fob_price.'</td>';
				$html .= '<td class="border_left border_right" colspan="2" align="right">'.$CurrencyCode.' '.$str_amt.'</td>';
				$html .= '</tr>';
				
				$sub_qty += $qty;
				$sub_amt += $this_amt;
				
				$grand_qty += $qty;
				$grand_amt += $this_amt;
			}
			
			$str_sub_qty = number_format($sub_qty, 0);
			$str_sub_amt = number_format($sub_amt, 2);
			
			$html .= '<tr style="background-color:#f3f3f4">';
			$html .= '<td class="border_left border_right border_btm border_top" colspan="7">S.TOTAL</td>';
			$html .= '<td class="border_left border_right border_btm border_top" align="center">'.$str_sub_qty.'</td>';
			$html .= '<td class="border_left border_right border_btm border_top" ></td>';
			$html .= '<td class="border_left border_right border_btm border_top" ></td>';
			$html .= '<td class="border_left border_right border_btm border_top" colspan="2" align="right">'.$CurrencyCode.' '.$str_sub_amt.'</td>';
			$html .= '</tr>';
			
		}//--- END FOREACH arr_skucolorsize ---//
		
	}//--- END FOR INFO ---//
	
	$str_grand_qty = number_format($grand_qty, 0);
	$str_grand_amt = number_format($grand_amt, 2);
	
	$html .= '<tr style="background-color:#bdbdbd">';
	$html .= '<td class="border_left border_right border_btm border_top" colspan="7">G.TOTAL</td>';
	$html .= '<td class="border_left border_right border_btm border_top" align="center">'.$str_grand_qty.'</td>';
	$html .= '<td class="border_left border_right border_btm border_top" ></td>';
	$html .= '<td class="border_left border_right border_btm border_top" ></td>';
	$html .= '<td class="border_left border_right border_btm border_top" colspan="2" align="right">'.$CurrencyCode.' '.$str_grand_amt.'</td>';
	$html .= '</tr>';
	
}//--- End FOREACH Fabric ---//

$html .= '</table>';

$split_total = explode(".", $grand_amt);
$dollar = strtoupper($handle_finance->convert_number($split_total[0]));
$cent   = strtoupper($handle_finance->convert_number($split_total[1]));
$str_words = strtoupper($cur_description." ".$dollar." AND ".$cent." CENTS ONLY.");

$html .= '<p style="font-size:11px">SAY TOTAL US DOLLARS: '.$str_words.'</p>';
$html .= '<table>';
$html .= '<tr>';
$html .= '<th>Manufacturer(s) Name & Address</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td>Name: '.$manufacturer.'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td>Address: '.$manuaddress.'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td>Tel: '.$manutel.' &nbsp; &nbsp; Fax: '.$manufax.'</td>';
$html .= '</tr>';
$html .= '</table>';

$html .= '<br/>';
$html .= '<br/>';
$html .= '<br/>';

$html .= '<table border="0" cellpadding="0" cellspacing="0">';
$html .= '<tr>
			<td style="width:40%" >';

	$html .= '<table >';
	$html .= '<tr>
				<th colspan="2">BANK DETAILS</th></tr>';
	$html .= '<tr>
				<td style="width:30%">Beneficiary Name:</td>
				<td style="width:70%">'.$beneficiary_name.'</td>
				</tr>';
	$html .= '<tr>
				<td colspan="2">Manufacturer Bank Info</td>
				</tr>';
	$html .= '<tr>
				<td>Bank Name</td>
				<td>'.$bank_name.'</td>
				</tr>';
	$html .= '<tr>
				<td>Beneficiary Account</td>
				<td>'.$bank_account_no.'</td>
				</tr>';
	$html .= '<tr>
				<td>SWIFT BIC</td>
				<td>'.$swift_code.'</td>
				</tr>';
	$html .= '<tr>
				<td>Address</td>
				<td>'.$bank_address.'</td>
				</tr>';
	$html .= '</table>';

$html .= '</td>';
$html .= '<td style="width:20%">&nbsp;</td>';
$html .= '<td style="width:40%" class="border_btm">';
	
	

$html .= '</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td></td>';
$html .= '<td></td>';
$html .= '<td align="center">AUTHORIZED SIGNATURE</td>';
$html .= '</tr>';

$html .= '</table>';

// $pdf->writeHTML($html, true, 0, true, 0);
// $pdf->SetFont('arialuni', '', 8, '', false); //heavy
// $pdf->AddPage('L', 'A4');

// $html = '';
// $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// $html .= '<hr />';

//===============================================//
// -------------- Packing List ----------------- // 
//===============================================//

// ============== pdf header =================== // 

$html .= $handle_lc->getBuyerInvoicePackingListTemplate6($invID);


?>