<?php 
$pdf->SetTitle("CAREGIVER USA CORP");

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

$invoice_date = date_format(date_create($invoice_date), "d-M-y");
$shippeddate  = date_format(date_create($shippeddate), "d-M-y");

$html .= <<<EOD
		<table border="0"  cellpadding="3">
			<tr>
				<th colspan="4" class="center-align border_top border_btm">$letterhead_title</th>
				</tr>
			<tr>
				<td rowspan="5" style="width:10%">To: </td>
				<td rowspan="5" style="width:40%">$conName<br/>$conAddress</td>
				<td style="width:10%">Invoice No:</td>
				<td>$invoice_no</td>
				</tr>
			<tr>
				<td>Date:</td>
				<td>$invoice_date</td>
				</tr>
			<tr>
				<td>Payment Terms:</td>
				<td>$paymentterm</td>
				</tr>
			<tr>
				<td>Courier Inv#:</td>
				<td></td>
				</tr>
			<tr>
				<td>Trade Terms:</td>
				<td>$tradeterm</td>
				</tr>
		</table>
		<br/>
		<br/>
		<table cellpadding="3">
		<tr>
			<td style="width:10%">From:</td>
			<td style="width:20%" class="border_btm">$portLoading</td>
			<td style="width:5%"></td>
			<td style="width:10%">To:</td>
			<td style="width:20%" class="border_btm">$buyerdest</td>
			<td style="width:5%"></td>
			<td style="width:10%"></td>
			<td style="width:20%"></td>
			</tr>
		<tr>
			<td>Shipped By:</td>
			<td class="border_btm">$vesselname</td>
			<td></td>
			<td>AWB No:</td>
			<td class="border_btm"></td>
			<td></td>
			<td >On/About:</td>
			<td class="border_btm">$shippeddate</td>
			</tr>
			</table>
		<br/><br/>
	
EOD;

// $arr_array   = $handle_class->getBuyerInvoiceDescriptionInfo($invID, $query_filter);
// $arr_buyerpo = $arr_array["byBuyerPO"];
// $arr_fabric  = $arr_array["byFabric"];

$query_filter = "";
$arr_array    = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$byBuyerPO    = $arr_array["byBuyerPO"];
$arr_fabric   = $arr_array["byFabric"];

$count_row = 1;
$count_buyerpo = count($arr_buyerpo);

	foreach($arr_fabric as $key => $arr_info){
		//$count_row += 3;//2;
		for($arr=0;$arr<count($arr_info);$arr++){
			$arr_FOB   = $arr_info[$arr]["arr_FOB"];
			$uom       = $arr_info[$arr]["uom"];
			
			foreach($arr_FOB as $fob_price => $arr_price){
				$count_row++;
				
			}//--- End Foreach FOB ---//
		}//--- END FOR ---//
	}//--- End Foreach Fabric ---//

$html .= '<table cellspacing="1" cellpadding="3" border="0" class="tb_joefresh">
				<tr>
					<th style="width:15%">MARKS & NOS. </th>
					<th style="width:7%">ITEM NO.</th>
					<th style="width:20%">PO</th>
					<th style="width:20%">STYLE #</th>
					<th style="width:11%">IA #</th>
					<th style="width:9%" align="center">QTY ('.$uom.')</th>
					<th style="width:9%" align="center">UNIT PRICE <br/>('.$CurrencyCode.' / '.$uom.')</th>
					<th style="width:9%" align="center">AMOUNT <br/>('.$CurrencyCode.')</th>
					</tr>';
	$i_po = 0;	
	$grand_nnw = 0;
	$grand_nw = 0;
	$grand_gw = 0;
	$grand_ctn = 0;
	$grand_cbm = 0;
	$grand_qty = 0;
	$grand_amt = 0;
	
	foreach($arr_fabric as $key => $arr_info){
		
		for($arr=0;$arr<count($arr_info);$arr++){
			$BuyerPO   = $arr_info[$arr]["BuyerPO"];
			$styleNo   = $arr_info[$arr]["styleNo"];
			$Orderno   = $arr_info[$arr]["Orderno"];
			$arr_FOB   = $arr_info[$arr]["arr_FOB"];
			$uom       = $arr_info[$arr]["uom"];
			$total_ctn = $arr_info[$arr]["total_ctn"];
			$nn_weight = $arr_info[$arr]["grand_nnw"];
			$n_weight  = $arr_info[$arr]["grand_nw"];
			$g_weight  = $arr_info[$arr]["grand_gw"];
			$g_cbm     = $arr_info[$arr]["grand_cbm"];
			$f = 0;
			
			$grand_nnw += $nn_weight;
			$grand_nw += $n_weight;
			$grand_gw += $g_weight;
			$grand_ctn += $total_ctn;
			$grand_cbm += $g_cbm;
			
			foreach($arr_FOB as $fob_price => $arr_price){
				$this_fob  = substr($fob_price,1);
				$this_qty  = $arr_price["qty"];
				$this_styleNo  = ($f==0? $styleNo: "");
				$this_BuyerPO  = ($f==0? $BuyerPO: "");
				$this_orderno  = ($f==0? $Orderno: "");
				$css_class     = ($i_po==0? "border_top": "");
				$this_i = "";
				if($f==0){
					$i_po++;
					$this_i = $i_po;
				}
				
				$this_amt = $this_fob * $this_qty;
				$str_amt  = number_format($this_amt, 2);
				
				$grand_qty += $this_qty;
				$grand_amt += $this_amt;
				
				$html .= '<tr>';
				$html .= ($i_po==1? '<td rowspan="'.$count_row.'" class="border_left border_right border_top border_btm" ></td>':'');
				$html .= '<td class="border_left '.$css_class.'" align="center">'.$this_i.'</td>';
				$html .= '<td class="'.$css_class.'">'.$this_BuyerPO.'</td>';
				$html .= '<td class="border_right '.$css_class.'">'.$this_styleNo.'</td>';
				$html .= '<td class="border_left border_right '.$css_class.'" align="center">'.$this_orderno.'</td>';
				$html .= '<td class="border_left border_right '.$css_class.'" align="center">'.$this_qty.'</td>';
				$html .= '<td class="border_left border_right '.$css_class.'" align="center">'.$this_fob.'</td>';
				$html .= '<td class="border_left border_right '.$css_class.'" align="right">'.$str_amt.'</td>';
				$html .= '</tr>';
				
				$f++;
			}//--- End Foreach fobprice ---//
		}
	}//--- End Foreach fabric ---//
	
	$html .= '<tr>';
	$html .= '<td colspan="3" class="border_left border_right border_btm">
				<table width="100%">
				<tr>
					<td style="width:30%">TOTAL CARTON:</td>
					<td>'.$grand_ctn.'</td>
					</tr>
				<tr>
					<td style="width:30%">TOTAL GROSS WEIGHT:</td>
					<td>'.$grand_gw.' KGS</td>
					</tr>
				<tr>
					<td style="width:30%">TOTAL MEASUREMENT:</td>
					<td>'.$grand_cbm.' CBM</td>
					</tr>
					</table>
				
				</td>';
	$html .= '<td class="border_left border_right border_btm"></td>';
	$html .= '<td class="border_left border_right border_btm"></td>';
	$html .= '<td class="border_left border_right border_btm"></td>';
	$html .= '<td class="border_left border_right border_btm"></td>';
	$html .= '</tr>';
	
	$str_amt  = number_format($grand_amt, 2);
	$html .= '<tr>';
	$html .= '<td class="all_border" colspan="5"></td>';
	$html .= '<td class="all_border" align="center">'.$grand_qty.'</td>';
	$html .= '<td class="all_border" align="right">TOTAL:</td>';
	$html .= '<td class="all_border" align="right">'.$str_amt.'</td>';
	$html .= '</tr>';
					
$html .= '</table>';

//------------------------------------------------------------//
//---------------------- PICK LIST ---------------------------//
//------------------------------------------------------------//
$html .= '<br pagebreak="true">';
$html .= '<table border="0">
						<tr>
							<th class="bold-text center-align">
								<h1>'.$letterhead_name.'</h1>
							</th>
						</tr>

						<tr>
							<td class="center-align">
								'.$letterhead_address.'
							</td>
						</tr>

						<tr>
							<td class="center-align">
								TEL : '.$letterhead_tel.' &nbsp;&nbsp;&nbsp;&nbsp; FAX : '.$letterhead_fax.'	
							</td>
						</tr>

					</table><br/><br/>';
					
$html .= '<table>
			<tr>
				<td align="center" class="border_top border_btm"><h2>PACKING LIST</h2></td>
				</tr>
				</table>';
$html .= '<br/>
		  <br/>
		<table width="100%">
			<tr>
				<td rowspan="2" width="10%">To:</td>
				<td rowspan="2" width="60%">'.$conName.'<br/>'.$conAddress.'</td>
				<td width="10%">Invoice No:</td>
				<td >'.$invoice_no.'</td>
				</tr>
			<tr>
				<td>Date:</td>
				<td>'.$invoice_date.'</td>
				</tr>
				</table>
		<br/>
		<br/>
		<table cellpadding="3">
		<tr>
			<td style="width:10%">From:</td>
			<td style="width:20%" class="border_btm">'.$portLoading.'</td>
			<td style="width:5%"></td>
			<td style="width:10%">To:</td>
			<td style="width:20%" class="border_btm">'.$buyerdest.'</td>
			<td style="width:5%"></td>
			<td style="width:10%"></td>
			<td style="width:20%"></td>
			</tr>
		<tr>
			<td>Shipped By:</td>
			<td class="border_btm">'.$vesselname.'</td>
			<td></td>
			<td>AWB No:</td>
			<td class="border_btm"></td>
			<td></td>
			<td >On/About:</td>
			<td class="border_btm">'.$shippeddate.'</td>
			</tr>
			</table>
		<br/><br/>';

$html .= '<table  cellpadding="5">
			<tr>
				<td class="full-border" align="center" style="width:5%">Item</td>
				<td class="full-border" align="center" style="width:15%">PO#</td>
				<td class="full-border" align="center" style="width:10%">IA#</td>
				<td class="full-border" align="center" style="width:15%">STYLE NO</td>
				<td class="full-border" align="center" style="width:15%">COLOR</td>
				<td class="full-border" align="center" style="width:10%">QTY IN PCS</td>
				<td class="full-border" align="center" style="width:10%">QTY IN CARTON</td>
				<td class="full-border" align="center" style="width:10%">KGS</td>
				<td class="full-border" align="center" style="width:10%">CBM</td>
				</tr>';
$count = 0; $grand_qty = 0; $grand_gw = 0; $grand_cbm = 0; $grand_ctn = 0; $arr_all_ctn_measurement = array();
foreach($arr_fabric as $key => $arr_info){
		
		for($arr=0;$arr<count($arr_info);$arr++){
			$BuyerPO   = $arr_info[$arr]["BuyerPO"];
			$styleNo   = $arr_info[$arr]["styleNo"];
			$Orderno   = $arr_info[$arr]["Orderno"];
			$arr_FOB   = $arr_info[$arr]["arr_FOB"];
			$uom       = $arr_info[$arr]["uom"];
			$total_ctn = $arr_info[$arr]["total_ctn"];
			$nn_weight = $arr_info[$arr]["grand_nnw"];
			$n_weight  = $arr_info[$arr]["grand_nw"];
			$g_weight  = $arr_info[$arr]["grand_gw"];
			$g_cbm     = $arr_info[$arr]["grand_cbm"];
			$arr_ctn   = $arr_info[$arr]["arr_ctn_measurement"];
			$arr_all_ctn_measurement = array_merge($arr_ctn, $arr_all_ctn_measurement);
			
			//print_r($arr_ctn);
			
			$f = 0;
			$count++;
			$grand_ctn += $total_ctn;
			$grand_gw  += $g_weight;
			$grand_cbm += $g_cbm;
			
			$this_po_qty = 0; $str_color = "";
			foreach($arr_FOB as $fob_price => $arr_price){
				$this_fob  = substr($fob_price,1);
				$this_qty  = $arr_price["qty"];
				$color     = $arr_price["color"];
				
				$str_count   = ($f==0? $count: "");
				$str_buyerpo = ($f==0? $BuyerPO: "");
				$str_orderno = ($f==0? $Orderno: "");
				$str_styleno = ($f==0? $styleNo: "");
				$str_ctn     = ($f==0? $total_ctn: "");
				$str_gw      = ($f==0? $g_weight: "");
				$str_cbm     = ($f==0? $g_cbm: "");
				
				
				$html .= '<tr>';
				$html .= '<td class="full-border" align="center">'.$str_count.'</td>';
				$html .= '<td class="full-border">'.$str_buyerpo.'</td>';
				$html .= '<td class="full-border">'.$str_orderno.'</td>';
				$html .= '<td class="full-border">'.$str_styleno.'</td>';
				$html .= '<td class="full-border">'.$color.'</td>';
				$html .= '<td class="full-border" align="center">'.$this_qty.'</td>';
				$html .= '<td class="full-border" align="center">'.$str_ctn.'</td>';
				$html .= '<td class="full-border" align="center">'.$str_gw.'</td>';
				$html .= '<td class="full-border" align="center">'.$str_cbm.'</td>';
				$html .= '</tr>';
				
				$grand_qty += $this_qty;
				$f++;
			}//--- End Foreach ---//
			
		}//--- End For ---//
		
}//--- End Foreach arr_fabric ---//

$html .= '<tr>';
$html .= '<td class="full-border">TOTAL</td>';
$html .= '<td class="full-border"></td>';
$html .= '<td class="full-border"></td>';
$html .= '<td class="full-border"></td>';
$html .= '<td class="full-border"></td>';
$html .= '<td class="full-border" align="center">'.$grand_qty.'</td>';
$html .= '<td class="full-border"></td>';
$html .= '<td class="full-border"></td>';
$html .= '<td class="full-border"></td>';
$html .= '</tr>';

$html .= '</table>';

$arr_all_ctn_measurement = array_unique($arr_all_ctn_measurement);
$str_ctn_measurement = implode(", ", $arr_all_ctn_measurement);

$html .= '<br/><br/><table>';
$html .= '<tr>';
$html .= '<td style="width:20%">TOTAL:</td>';
$html .= '<td style="width:80%">'.$grand_ctn.' ctn</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td>TOTAL GROSS WEIGHT:</td>';
$html .= '<td>'.$grand_gw.' KGS</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td>TOTAL MEASUREMENT:</td>';
$html .= '<td>'.$grand_cbm.'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td>CARTON DIMENSION:</td>';
$html .= '<td>'.$str_ctn_measurement.'</td>';
$html .= '</tr>';
$html .= '</table>';

?>