<?php 
$pdf->SetTitle("Joe Fresh");

$html = <<<EOD
<style>
	.bold-text {
		font-weight: bold;
	}

	.center-align{
		text-align: center;
	}

	p.p-format {
		white-space: pre-wrap; 	
	}

	table td{
		/*font-size: 10px;*/ 
	}

	table th{
		/*font-size: 10px;*/
	}

	.table-bordered th, 
	.table-bordered td {
		border: 1px solid black;
	}
	.tb_joefresh th{
		border: 1px solid black;
	}
	
	td.all_border{
		border: 1px solid black;
	}
	td.top_border{
		border-top: 1px solid black;
	}
	td.left_border{
		border-left: 1px solid black;
	}
	td.right_border{
		border-right: 1px solid black;
	}
	td.bottom_border{
		border-bottom: 1px solid black;
	}

	.font-red {
		color: red;
	}

	.font-blue {
		color: blue;
	}

	.full-border {
		border: 1px solid black;
	}
	.border_btm, .border-b {
		border-bottom: 1px solid black;
	}
	.border_top, .border-t {
		border-top: 1px solid black;
	}
	.border_right, .border-r {
		border-right: 1px solid black;
	}
	.border_left, .border-l {
		border-left: 1px solid black;
	}
	.border-rl, .border-lr {
		border-left: 1px solid black;
		border-right: 1px solid black;
	}
	.border_left_bold {
		border-left: 3px solid black;
	}


	.dashedborder_btm {
		border-bottom: 1px dashed black;
	}
	.dashedborder_top {
		border-top: 1px dashed black;
	}
	.dashedborder_right {
		border-right: 1px dashed black;
	}
	.dashedborder_left {
		border-left: 1px dashed black;
	}

	
</style>
EOD;

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

$conAddress     = strtoupper($conAddress);
$notify_address = strtoupper($notify_address);
$notify_tel   = (trim($notifyTel)==""? "":"<br/>Tel: $notifyTel");
$notify_fax   = (trim($notifyFax)==""? "":"<br/>Fax: $notifyFax");
$notify_email = (trim($notifyEmail)==""? "":"<br/>Email: $notifyEmail");

$html .= <<<EOD
		<table border="1" class="table-bordered" cellpadding="3">
			<tr>
				<th colspan="2" class="center-align">$letterhead_title</th>
				</tr>
			<tr>
				<th>
					<table>
					<tr>
						<td><b>APPLICANT</b><br/>
							$conName <br/>
							$conAddress<br/>
							Email: $conemail<br/>
							Contact Person: $concontact<br/>
							Tel: $contel<br/>
							Fax: $confax
							</td>
						<td><b>CONSIGNEE</b><br/>
							$conName <br/>
							$conAddress<br/>
							Email: $conemail<br/>
							Contact Person: $concontact<br/>
							Tel: $contel<br/>
							Fax: $confax
							</td>
						</tr>
						</table>
				</th>
				<th>
					<table>
					<tr>
						<td>INVOICE NO:</td>
						<td>$invoice_no</td>
						<td>DATE:</td>
						<td>$invoice_date</td>
						</tr>
					<tr>
						<td>EX_FACTORY DATE:</td>
						<td>$exfactorydate</td>
						</tr>
					<tr>
						<td>ITEM NO:</td>
						<td>$orderno</td>
						</tr>
					<tr>
						<td>SHIPMENT TERM:</td>
						<td colspan="3">$tradeterm</td>
						</tr>
					<tr>
						<td>ORIGIN:</td>
						<td colspan="3">PHNOM PENH, $manucountry</td>
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
								$notify_address $notify_tel $notify_fax $notify_email</td>
							</tr>
							</table>
					
				</th>
				<th rowspan="3">
					<table>
					<tr>
						<td>LC#:</td>
						<td>$lc_number</td>
						<td></td>
						<td></td>
						</tr>
					<tr>
						<td>DATED:</td>
						<td>$lc_date</td>
						<td></td>
						<td></td>
						</tr>
					<tr>
						<td>LC ISSUING BANK:</td>
						<td>$lc_bank</td>
						<td></td>
						<td></td>
						</tr>
					</table>
				</th>
				</tr>
			<tr>
				<th>
				<table>
				<tr>
					<td>PORT OF LOADING<br/>
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
					<td>VESSEL ETD<br/>
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
// $query_filter = "AND cpt.shiped=1";
// $arr_array   = $handle_class->getBuyerInvoiceDescriptionInfo($invID, $query_filter);
// $arr_buyerpo = $arr_array["byBuyerPO"];
$query_filter = ""; $arr_all = array();
if($acctid==0){
	$arr_buyerpo = array();
}
else{
	$arr_array   = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
	$arr_buyerpo = $arr_array["byBuyerPO"];
	$arr_all     = $arr_array["arr_all"];
}



$grand_ctn = 0;
$grand_qty = 0;
$grand_amt = 0;
$grand_nw  = 0;
$grand_gw  = 0;
	
//========================== po list =========================//

		
$count_row = 0;
$count_buyerpo = count($arr_buyerpo);
	$arr_uom = array();
	foreach($arr_buyerpo as $key => $arr_info){
		$arr_info_row  = $arr_info["arr_info"];
		$uom           = $arr_info["uom"];
		$count_color   = count($arr_info["arr_info"]);
		$arr_uom[]     = $uom;
		
		$count_row += 2;//2;
		foreach($arr_info_row as $prepack_key => $arr_value){
			$count_row++;
		}
	}
	$arr_uom  = array_unique($arr_uom);
	// $str_uom3  = implode(",",$arr_uom);
	$str_uom  = (count($arr_uom)==1? "$uom":"");
	$str_uom2 = (count($arr_uom)==1? " / $uom":"");
	$count = count($arr_uom);

$html .= '<table cellspacing="1" cellpadding="2" border="0" class="tb_joefresh">
				<tr>
					<td class="left_border top_border bottom_border right_border" style="width:15%">MARKS & NOS. </td>
					<td class="right_border top_border bottom_border" style="width:6%">NO. OF PKGS</td>
					<td class="right_border top_border bottom_border" style="width:53%" colspan="5">DESCRIPTION</td>
					<td class="right_border top_border bottom_border" style="width:8%">QTY '.$str_uom.'</td>
					<td class="right_border top_border bottom_border" style="width:8%">UNIT PRICE <br/>('.$CurrencyCode.''.$str_uom2.')</td>
					<td class="top_border bottom_border right_border" style="width:10%">AMOUNT <br/>('.$CurrencyCode.')</td>
					</tr>';
	$rowspan = $count_row + 1;
	$html .= '<tr>
				<td class="left_border right_border" rowspan="'.$rowspan.'" >
					'.$conName.'<br/>
					COUNTRY OF ORIGIN<br/>
					NG ITEMS NUMBER<br/>
					DESCRIPTION<br/>
					CTNS QTY MASTER<br/>
					CARTON NUMBER OF UPC CODE
					</td>
				<td class="  right_border"></td>
				<td class="  right_border" colspan="5">DESCRIPTION OF GOODS AS APPAREL<br/>&nbsp;</td>
				<td class="  right_border"></td>
				<td class="  right_border"></td>
				<td class="  right_border"></td>
				</tr>';
	$i_po = 0;	
	$arr_uomqty = array();
	foreach($arr_buyerpo as $key => $arr_info){
			$i_po++;
			$shipmentpriceID = $arr_info["shipmentpriceID"];
			$od_FactoryID    = $arr_info["od_FactoryID"];
			$this_total_ctn  = $arr_info["total_ctn"];
			$this_count_row  = $arr_info["count_row"];
			$netnet_weight   = $arr_info["grand_nnw"];
			$net_weight      = $arr_info["grand_nw"];
			$gross_weight    = $arr_info["grand_gw"];
			$arr_info_row    = $arr_info["arr_info"];
			$uom             = $arr_info["uom"];
			$fab_order       = ($arr_info["fab_order"]);
			$fab_order       = strtolower($fab_order);
			$fab_order       = html_entity_decode($fab_order);
			$fab_order       = strtoupper($fab_order);
			$styleNo         = $arr_info["styleNo"];
			$quotacat        = $arr_info["quotacat"];
			$count_color     = count($arr_info["arr_info"]);
			$grand_ctn += $this_total_ctn;
		
			$grand_nnw += $netnet_weight;
			$grand_nw += $net_weight;
			$grand_gw += $gross_weight;
			
			$this_rowspan = $count_color + 2;//2;
			$css_bottom   = ($i_po==$count_buyerpo? "":"");//bottom_border
			//echo "$shipmentpriceID / ".count($arr_info_row)."<< <br/>";
			
			$html .= '<tr>
						<td class="right_border '.$css_bottom.'" align="center" rowspan="'.$this_rowspan.'">'.$this_total_ctn.' </td>';
			$html .= '<td class=" right_border" colspan="5">'.$fab_order.' </td>';
			$html .= '<td class=" right_border" ></td>';
			$html .= '<td class=" right_border" ></td>';
			$html .= '<td class=" right_border" ></td>';
			$html .= '</tr>';
			
			$html .= '<tr>';
			$html .= '<td class="" style="width:10%">PO NO.</td>';
			$html .= '<td style="width:11%">STYLE NO.</td>';
			$html .= '<td style="width:11%">NG ITEM#</td>';
			$html .= '<td style="width:16%">COLOR</td>';
			$html .= '<td class="right_border" style="width:5%">CAT</td>';
			$html .= '<td class=" right_border"></td>';
			$html .= '<td class=" right_border"></td>';
			$html .= '<td class=" right_border"></td>';
			$html .= '</tr>';
			
			$f = 0;
			foreach($arr_info_row as $prepack_key => $arr_value){
				list($prepack_name, $group_number) = explode("**^^", $prepack_key);
				$colorOnly     = $arr_value["colorOnly"];
				$color         = $arr_value["color"];
				$total_ctn_qty = $arr_value["qty"];
				$fob_price     = $arr_value["fob_price"];
				$garmentID     = $arr_value["garmentID"];
				
				$amt       = $total_ctn_qty * $fob_price;
				$grand_qty += $total_ctn_qty;
				$grand_amt += $amt;
				$this_amt  = number_format($amt,2);
				$this_fob  = number_format($fob_price,3); //request by mao 3 decimal point 20220310, case BINV22000288
				$str_ctn_qty  = number_format($total_ctn_qty);
				
				$sqlqc = "SELECT qc.Description as this_quotacat, bih.shipping_marking
                     FROM tblbuyer_invoice_payment_hts bih
                     LEFT JOIN tblquotacat qc ON qc.ID = bih.quotaID
                     WHERE bih.invID='$invID' AND bih.shipmentpriceID='$shipmentpriceID' AND bih.garmentID='$garmentID'";
				$stmtqc = $conn->query($sqlqc);
				$rowqc = $stmtqc->fetch(PDO::FETCH_ASSOC);
					$quotacat = $rowqc["this_quotacat"];
				
				$this_quotacat = ($f==0? $quotacat: "");
				$this_styleNo  = ($f==0? $styleNo: "");
				$this_key      = ($f==0? $key: "");
				$po_rowspan    = count($arr_info_row);
				
				$arr_uomqty["str$uom"]["qty"] += $total_ctn_qty;
				$arr_uomqty["str$uom"]["amt"] += $amt;
				
				$arr_key = explode("%%", $this_key);
				$this_po = (isset($arr_key[0])? $arr_key[0]:"");
				
				$html .= '<tr>';
				$html .= ($f==0? '<td class="" rowspan="'.$po_rowspan.'">'.$this_po.'</td>':'');
				$html .= '<td class="">'.$this_styleNo.'</td>';
				$html .= '<td class="">'.$prepack_name.'</td>';
				$html .= '<td class="">'.$colorOnly.'</td>';
				$html .= '<td class="right_border">'.$this_quotacat.'</td>';
				
				$html .= '<td class=" right_border" align="center">'.$str_ctn_qty.'</td>';
				$html .= '<td class=" right_border" align="center">'.$this_fob.'</td>';
				$html .= '<td class=" right_border" align="center">'.$this_amt.'</td>';
				$html .= '</tr>';
				
				$f++;
			}//-- End Foreach --//
			
			
			
			// $html .= '<tr>';
			// $html .= '<td class="left_border '.$css_bottom.'">&nbsp;</td>';
			// $html .= '<td class="'.$css_bottom.'"></td>';
			// $html .= '<td class="'.$css_bottom.'"></td>';
			// $html .= '<td class="'.$css_bottom.'"></td>';
			// $html .= '<td class="right_border '.$css_bottom.'"></td>';
			
			// $html .= '<td class="left_border right_border '.$css_bottom.'"></td>';
			// $html .= '<td class="left_border right_border '.$css_bottom.'"></td>';
			// $html .= '<td class="left_border right_border '.$css_bottom.'"></td>';
			// $html .= '</tr>';
								
	}//--- End Foreach Buyer PO
	
	
	$this_grand_qty = number_format($grand_qty);
	
	$arr = array_unique($arr_uom);
	$str = implode(",",$arr);
	$arr_uom = explode(",", $str);
	
	for($u=0;$u<count($arr_uom);$u++){
		$this_u = $arr_uom[$u];
		$this_uom = $arr_uom[$u];
		$this_qty = $arr_uomqty["str$this_uom"]["qty"];
		$this_amt = $arr_uomqty["str$this_uom"]["amt"];
		
		$this_grand_qty = number_format($this_qty);
		$this_grand_amt = number_format($this_amt,2);
		
		$css_top    = ($u==0? "border_top":"");
		$str_title  = ($u==0? "TOTAL CTNS:":"");
		$str_title2 = ($u==0? "TOTAL":"");
		$str_ctnqty = ($u==0? "$grand_ctn":"");
		
		$html .= '<tr>';
		$html .= '<td class="'.$css_top.' border_left border_right" align="right" >'.$str_title.'</td>';
		$html .= '<td class="'.$css_top.'  border_right" align="center">'.$str_ctnqty.'</td>';
		$html .= '<td class="'.$css_top.'  border_right" align="right" colspan="5" ><u>'.$str_title2.'</u></td>';
		$html .= '<td class="'.$css_top.' " align="center">'.$this_grand_qty.' '.$this_uom.'</td>';
		$html .= '<td class="'.$css_top.'"></td>';
		$html .= '<td class="'.$css_top.' border_right" align="center">US$'.$this_grand_amt.'</td>';
		$html .= '</tr>';
	}
	
	$discount_goc = round($grand_amt * 0.025, 2);
	$grand_amt    = $grand_amt - $discount_goc;
	$str_amt      = number_format($grand_amt,2);
	
	$html .= '<tr>';
	$html .= '<td class=" border_left border_right"></td>';
	$html .= '<td class=" border_right" ></td>';
	$html .= '<td class=" border_right" colspan="5"></td>';
	$html .= '<td class="" align="right" colspan="2">LESS: 2.5% GOC DISCOUNT</td>';
	$html .= '<td class="border_right" align="center" >'.$discount_goc.'</td>';
	$html .= '</tr>';
	
	$html .= '<tr>';
	$html .= '<td class="border_btm border_left border_right"></td>';
	$html .= '<td class="border_btm  border_right"></td>';
	$html .= '<td class="border_btm  border_right" colspan="5" align="right"></td>';
	$html .= '<td class="border_btm " align="right" colspan="2"></td>';
	$html .= '<td class="border_btm border_right" align="center" >US$'.$str_amt.'</td>';
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
			<td style="width:25%"><b>TOTAL NET NET WEIGHT: </b></td>
			<td style="width:10%">'.$grand_nnw.'</td>
			<td>KGS</td>
			</tr>
	</table>
';

$handle_lc->BICID = "";
//= =============================== packing list ====================================//

if($acctid==0){
	
}
else{
// $html = '<style>
	// .bold-text {
		// font-weight: bold;
	// }

	// .center-align{
		// text-align: center;
	// }

	// p.p-format {
		// white-space: pre-wrap; 	
	// }

	// table td{
		// /*font-size: 10px;*/ 
	// }

	// table th{
		// /*font-size: 10px;*/
	// }

	// .table-bordered th, 
	// .table-bordered td {
		// border: 1px solid black;
	// }
	// .tb_joefresh th{
		// border: 1px solid black;
	// }
	
	// td.all_border{
		// border: 1px solid black;
	// }
	// td.top_border{
		// border-top: 1px solid black;
	// }
	// td.left_border{
		// border-left: 1px solid black;
	// }
	// td.right_border{
		// border-right: 1px solid black;
	// }
	// td.bottom_border{
		// border-bottom: 1px solid black;
	// }

	// .font-red {
		// color: red;
	// }

	// .font-blue {
		// color: blue;
	// }

	// .full-border {
		// border: 1px solid black;
	// }
	// .border_btm, .border-b {
		// border-bottom: 1px solid black;
	// }
	// .border_top, .border-t {
		// border-top: 1px solid black;
	// }
	// .border_right, .border-r {
		// border-right: 1px solid black;
	// }
	// .border_left, .border-l {
		// border-left: 1px solid black;
	// }
	// .border-rl, .border-lr {
		// border-left: 1px solid black;
		// border-right: 1px solid black;
	// }
	// .border_left_bold {
		// border-left: 3px solid black;
	// }


	// .dashedborder_btm {
		// border-bottom: 1px dashed black;
	// }
	// .dashedborder_top {
		// border-top: 1px dashed black;
	// }
	// .dashedborder_right {
		// border-right: 1px dashed black;
	// }
	// .dashedborder_left {
		// border-left: 1px dashed black;
	// }

	
// </style>';
$this_spID = '';
// print_r($arr_all);
// echo "$this_spID << <br/>";



if($acctid!=0){
	$handle_lc->acctid = $acctid;
	
	$this_spID = ($acctid==1? "":"");//44465 //44681
	$this_html = $handle_lc->getBuyerInvoicePackingList($invID, $this_spID, $arr_all);
}

// echo $this_html;
$html .= $this_html;
}


?>