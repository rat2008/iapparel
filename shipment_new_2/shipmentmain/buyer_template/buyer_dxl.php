<?php 
$pdf->SetTitle("Destination XL");
//==================== buyer "JOE FRESH" ; id: B13 =====================================//
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

$invoice_date  = date_format(date_create($invoice_date), "d-M-y");
$shippeddate   = date_format(date_create($shippeddate), "d-M-y");
$exfactorydate = date_format(date_create($exfactorydate), "d-M-y");

$html .= <<<EOD
		<table border="1" class="table-bordered" cellpadding="5">
			<tr>
				<th colspan="2" class="center-align">$letterhead_title</th>
				</tr>
			<tr>
				<th>
					<table>
					<tr>
						<td><b>BILL TO:</b><br/>
							$bill_to <br/>
							$bill_address
							</td>
						<td><b>CONSIGNEE:</b><br/>
							$conName <br/>
							$conAddress
							</td>
						</tr>
						</table>
				</th>
				<th>
					<table>
					<tr>
						<td style="width:30%">INVOICE NO:</td>
						<td>$invoice_no</td>
						<td>DATE:</td>
						<td>$invoice_date</td>
						</tr>
					<tr>
						<td>FACTORY IA NO:</td>
						<td>$orderno</td>
						</tr>
					<tr>
						<td>EX-FACTORY DATE</td>
						<td>$exfactorydate</td>
						</tr>
					<tr>
						<td>PAYMENT TERM:</td>
						<td colspan="3">$paymentterm</td>
						</tr>
					<tr>
						<td>SHIPMENT TERM:</td>
						<td colspan="3">$tradeterm</td>
						</tr>
					<tr>
						<td>COUNTRY OF ORIGIN:</td>
						<td colspan="3">$manucountry</td>
						</tr>
						</table>
				</th>
				</tr>
			<tr>
				<th>
					<table>
						<tr>
							<td>NOTIFY PARTY<br/>
								$notify_party <br/>
								$notify_address</td>
							<td>SHIP TO:<br/>
								$ship_to <br/>
								$ship_address
								</td>
							</tr>
							</table>
					
				</th>
				<th rowspan="3">
					<table>
					<tr>
						<td style="width:30%">BANK ACCOUNT NUMBER:</td>
						<td>$bank_account_no</td>
						</tr>
					<tr>
						<td>BANK NAME:</td>
						<td>$bank_name<br/>$bank_address</td>
						</tr>
					<tr>
						<td>BENEFICIARY'S NAME:</td>
						<td>$beneficiary_name</td>
						</tr>
					<tr>
						<td>SWIFT CODE:</td>
						<td>$swift_code</td>
						</tr>
					</table>
				</th>
				</tr>
			<tr>
				<th>
				<table>
				<tr>
					<td>SHIPPED FROM<br/>
						$portLoading</td>
					<td>TO<br/>
						$buyerdest</td>
					</tr>
					</table>
				</th>
				</tr>
			<tr>
				<th>
				<table>
				<tr>
					<td>SHIPPED PER<br/>
						$shipmode</td>
					<td>ETD<br/>
						$shippeddate</td>
					</tr>
					</table>
				</th>
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
//$query_filter = "AND cpt.shiped=1";
// $arr_array   = $handle_class->getBuyerInvoiceDescriptionInfo($invID, $query_filter);
// $arr_buyerpo = $arr_array["byBuyerPO"];
// $arr_fabric  = $arr_array["byFabric"];

$arr_array   = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_buyerpo = $arr_array["byBuyerPO"];
$arr_fabric = $arr_array["byFabric"];

$grand_ctn = 0;
$grand_qty = 0;
$grand_amt = 0;
$grand_nw  = 0;
$grand_gw  = 0;
	
//========================== PO LIST =========================//


$count_row = 0;
$count_buyerpo = count($arr_buyerpo);

	foreach($arr_fabric as $key => $arr_info){
		$count_row += 3;//2;
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
					<th style="width:7%">NO. OF PKGS</th>
					<th style="width:51%" colspan="3">DESCRIPTION</th>
					<th style="width:9%" align="center">QTY ('.$uom.')</th>
					<th style="width:9%" align="center">UNIT PRICE <br/>('.$CurrencyCode.' / '.$uom.')</th>
					<th style="width:9%" align="center">AMOUNT <br/>('.$CurrencyCode.')</th>
					</tr>';
	$rowspan = $count_row + 1;
	$html .= '<tr>
				<td class="all_border" rowspan="'.$rowspan.'" >
					'.$remarks.' 
					</td>
				<td class="top_border left_border right_border"></td>
				<td class="top_border left_border right_border" colspan="3">DESCRIPTION OF GOODS AS APPAREL<br/>&nbsp;</td>
				<td class="top_border left_border right_border"></td>
				<td class="top_border left_border right_border"></td>
				<td class="top_border left_border right_border"></td>
				</tr>';
	$i_po = 0;	
	$grand_nnw = 0;
	$grand_nw = 0;
	$grand_gw = 0;
	$grand_ctn = 0;
	$grand_cbm = 0;
	
	foreach($arr_fabric as $key => $arr_info){
			$i_po++;
			//list($gender, $fab, $prodtype) = explode("**^^", $key);
			$str_prodtype = $key;//($prodtype==""? "": "$key");
			
			//$this_rowspan = $count_color + 3;//2;
			$css_bottom   = ($i_po==$count_buyerpo? "bottom_border":"");
			
			$html .= '<tr>
						<td class="left_border right_border" align="center"></td>';
			$html .= '<td class="left_border right_border" colspan="3">'.$str_prodtype.' </td>';
			$html .= '<td class="left_border right_border" ></td>';
			$html .= '<td class="left_border right_border" ></td>';
			$html .= '<td class="left_border right_border" ></td>';
			$html .= '</tr>';
			
			$html .= '<tr>';
			$html .= '<td class="left_border right_border"></td>';
			$html .= '<td class="left_border" >PO NO.</td>';
			$html .= '<td>STYLE NO.</td>';
			$html .= '<td class="right_border">HTS CODE</td>';
			$html .= '<td class="left_border right_border"></td>';
			$html .= '<td class="left_border right_border"></td>';
			$html .= '<td class="left_border right_border"></td>';
			$html .= '</tr>';
			//echo "$str_prodtype <== <br/>";
			$arr_FOB = array();
			for($arr=0;$arr<count($arr_info);$arr++){
				$BuyerPO   = $arr_info[$arr]["BuyerPO"];
				$styleNo   = $arr_info[$arr]["styleNo"];
				$spID      = $arr_info[$arr]["shipmentpriceID"];
				$arr_FOB   = $arr_info[$arr]["arr_FOB"];
				$total_ctn = $arr_info[$arr]["total_ctn"];
				$nn_weight = $arr_info[$arr]["grand_nnw"];
				$n_weight  = $arr_info[$arr]["grand_nw"];
				$g_weight  = $arr_info[$arr]["grand_gw"];
				$g_cbm     = $arr_info[$arr]["grand_cbm"];
				$ht_code   = $arr_info[$arr]["ht_code"];
				$f = 0;
				
				$grand_nnw += $nn_weight;
				$grand_nw += $n_weight;
				$grand_gw += $g_weight;
				$grand_ctn += $total_ctn;
				$grand_cbm += $g_cbm;
				
				//echo "-----> $BuyerPO [".count($arr_FOB)."] / $g_cbm <br/>";
				//print_r($arr_FOB);
				
				foreach($arr_FOB as $fob_price => $arr_price){
					$this_fob  = substr($fob_price,1);
					$this_qty  = $arr_price["qty"];
					$this_amt  = $this_qty * $this_fob;
					
					$this_ht_code   = ($f==0? $ht_code: "");
					$this_total_ctn = ($f==0? $total_ctn: "");
					$this_styleNo  = ($f==0? $styleNo: "");
					$this_BuyerPO  = ($f==0? $BuyerPO: "");
					
					$grand_qty += $this_qty;
					$grand_amt += $this_amt;
					
					//echo "----------------->$fob_price - $this_qty <br/>";
					
					$html .= '<tr>';
					$html .= '<td class="left_border right_border" align="center">'.$this_total_ctn.'</td>';
					$html .= '<td class="left_border" >'.$this_BuyerPO.'</td>';
					$html .= '<td class="">'.$this_styleNo.'</td>';
					$html .= '<td class="right_border">'.$this_ht_code.'</td>';
					
					$html .= '<td class="left_border right_border" align="center">'.$this_qty.'</td>';
					$html .= '<td class="left_border right_border" align="center">'.$this_fob.'</td>';
					$html .= '<td class="left_border right_border" align="center">'.$this_amt.'</td>';
					$html .= '</tr>';
					
					$f++;
				}
			}//--- End Foreach ---//
			
			$html .= '<tr>';
			$html .= '<td class="left_border right_border '.$css_bottom.'"></td>';
			$html .= '<td class="left_border '.$css_bottom.'">&nbsp;</td>';
			$html .= '<td class="'.$css_bottom.'"></td>';
			$html .= '<td class="right_border '.$css_bottom.'"></td>';
			
			$html .= '<td class="left_border right_border '.$css_bottom.'"></td>';
			$html .= '<td class="left_border right_border '.$css_bottom.'"></td>';
			$html .= '<td class="left_border right_border '.$css_bottom.'"></td>';
			$html .= '</tr>';
								
	}//--- End Foreach Buyer PO
	
	$this_grand_amt = number_format($grand_amt,2);
	
	$html .= '<tr>';
	$html .= '<td class="all_border" align="right" >TOTAL CTNS:</td>';
	$html .= '<td class="all_border" align="center">'.$grand_ctn.'</td>';
	$html .= '<td class="all_border" align="right" colspan="3" >TOTAL</td>';
	$html .= '<td class="all_border" align="center">'.$grand_qty.'</td>';
	$html .= '<td class="all_border"></td>';
	$html .= '<td class="all_border" align="center">'.$grand_amt.'</td>';
	$html .= '</tr>';
	$html .= '</table>';	


$html .= '
	
	<br/><br/>
	<table border="0" cellpadding="3" width="60%">
		<tr>
			<td colspan="3">
				WE HEREBY CERTIFY THAT:<br/>
				NO SWPM ASSOCIATED WITH THIS SHIPMENT<br/>
				NO SWPM USED AS PACKING MATERIAL FOR THIS SHIPMENT</td>
		</tr>
		<tr>
			<td style="width:25%"><b>TOTAL GROSS WEIGHT : </b></td>
			<td style="width:10%">'.$grand_gw.'</td>
			<td>KGS</td>
			</tr>
		<tr>
			<td style="width:25%"><b>TOTAL NET WEIGHT: </b></td>
			<td style="width:10%">'.$grand_nw.'</td>
			<td>KGS</td>
			</tr>
		<tr>
			<td style="width:25%"><b>TOTAL CBM: </b></td>
			<td style="width:10%">'.$grand_cbm.'</td>
			<td></td>
			</tr>
	</table>
';



//= =============================== packing list ====================================//
$this_html = $handle_lc->getBuyerInvoicePackingListTemplate2($invID);
$html .= $this_html;


?>