<?php 
$pdf->SetTitle("S OLIVER");

$html .= <<<EOD
<table border="0">
	<tr>
		<th class="bold-text center-align">
			<h3>$letterhead_title</h3>
		</th>
	</tr>
</table>
<br>
<br>
EOD;

$html .= <<<EOD
	<table border="0" cellpadding="3">
	<tr>
		<th colspan="2" class="bold-text center-align full-border">NAME AND ADDRESS OF BUYER / CONSIGNEE</th>
		<th colspan="2" class="bold-text center-align full-border">NAME AND ADDRESS OF INVOICING PARTY / VENDOR / MANUFACTURER</th>
		</tr>
	<tr>
		<td class="full-border" colspan="2" rowspan="3" >
			<u>BUYER / CONSIGNEE:</u><br/><br/>
			$conName<br/>
			$conAddress
		</td>
		<td class="border_left border_top" >INVOICING PARTY:</td>
		<td class="border_top border_right" >$letterhead_name<br/>
			 $letterhead_address<br/>
			 TEL: $letterhead_tel &nbsp; &nbsp; FAX: $letterhead_fax
			 </td>
		</tr>
	<tr>
		<td class="border_left">VENDOR:</td>
		<td class="border_right">$ownership<br/>
			 $owneraddress<br/>
			 TEL: $ownertel &nbsp; &nbsp; FAX: $ownerfax
			 </td>
		</tr>
	<tr>
		<td class="border_left ">MANUFACTURER:</td>
		<td class="border_right">$manufacturer<br/>
								$manuaddress <br/></td>
		</tr>	
	<tr>
		<td class="border_left border_top border_btm" align="center">DELIVERY NOTE NO.:</td>
		<td class="border_right border_top border_btm"></td>
		<td class="border_left border_right border_btm" colspan="2" ></td>
		</tr>
	<tr>
		<td class="full-border" align="center">INVOICE NO.<br/>$invoice_no</td>
		<td class="full-border" align="center">INVOICE DATE<br/>$invoice_date</td>
		<td class="full-border" align="center">INSPECTION CERT NO.</td>
		<td class="full-border" align="center">INCOTERMS/TERMS OF DELIVERY<br/>$tradeterm</td>
		</tr>
	</table>
	<table border="0" cellpadding="3">
	<tr>
		<td class="full-border" align="center">CARRIER<br/></td>
		<td class="full-border" align="center">DATE OF DEPARTURE<br/>$shippeddate</td>
		<td class="full-border" align="center">PORT OF LOADING<br/>$portLoading</td>
		<td class="full-border" align="center">PORT OF DISCHARGE<br/>$portDischarges</td>
		<td class="full-border" align="center">FINAL DESTINATION<br/>$buyerdest</td>
		</tr>
		</table>
EOD;

$html .= <<<EOD
	<table border="0" cellpadding="3">
	<tr>
		<td class="border_left border_top border_right" align="center" style="width:15%">STYLE NO. SAP (long)</td>
		<td class="border_left border_top border_right" align="center" style="width:10%">ORDER NO.</td>
		<td class="border_left border_top border_right" align="center" style="width:25%">DESCRIPTION</td>
		<td class="border_left border_top border_right" align="center" style="width:10%">HS CODE</td>
		<td class="border_left border_top border_right" align="center" style="width:10%">COLOUR</td>
		<td class="border_left border_top border_right" align="center" style="width:10%">NO. OF UNITS</td>
		<td class="border_left border_top border_right" align="center" style="width:10%">UNIT PURCHASE PRICE</td>
		<td class="border_left border_top border_right" align="center" style="width:10%">PURCHASE PRICE VALUE</td>
		</tr>
EOD;

// $query_filter = "AND cpt.shiped=1";
// $arr_array   = $handle_class->getBuyerInvoiceDescriptionInfo($invID, $query_filter);
// $arr_fabric  = $arr_array["byFabric"];

$query_filter = "";
$arr_array    = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_fabric   = $arr_array["byFabric"];

$grand_inv_gw  = $arr_array["grand_inv_gw"];
$grand_inv_nw  = $arr_array["grand_inv_nw"];
$grand_inv_nnw = $arr_array["grand_inv_nnw"];
$grand_inv_ctn = $arr_array["grand_inv_ctn"];
$grand_inv_cbm = $arr_array["grand_inv_cbm"];

$grand_gw  = 0;//
$grand_nw  = 0;//
$total_ctn = 0;//$arr_array["total_ctn"];
$grand_cbm = 0;//$arr_array["grand_cbm"];
$grand_ctn = 0;
$grand_amt = 0;

$fabric_country = $handle_lc->getFabricCountryOrigin($grp_spID);


//=====================================================//
//--------------- BUYER PO DISPLAY INFO ---------------//
//=====================================================//

foreach($arr_fabric as $key => $arr_info){
	$str_prodtype = $key;
	//echo "$str_prodtype ".count($arr_info)." <== <br/>";
	for($arr=0;$arr<count($arr_info);$arr++){
		$BuyerPO          = $arr_info[$arr]["BuyerPO"];
		$arr_group_number = $arr_info[$arr]["arr_group_number"];
		$styleNo          = $arr_info[$arr]["styleNo"];
		$ht_code          = $arr_info[$arr]["ht_code"];
		
		//echo "------>> $BuyerPO ".count($arr_group_number);
		
		$count_po = 0;
		foreach($arr_group_number as $grp_key => $arr_color_info){
			$this_qty  = $arr_color_info["qty"];
			$color     = $arr_color_info["color"];
			$fob_price = $arr_color_info["fob_price"];
			$this_amt  = $this_qty * $fob_price;
			
			$this_po     = ($count_po==0? $BuyerPO:"");
			$this_style  = ($count_po==0? $styleNo:"");
			$this_prod   = ($count_po==0? $str_prodtype:"");
			$this_htcode = ($count_po==0? $ht_code:"");
			
			$str_qty = number_format($this_qty);
			$str_fob = number_format($fob_price,2);
			$str_amt = number_format($this_amt,2);
			
			$grand_qty += $this_qty;
			$grand_amt += $this_amt;
			
			$html .= '<tr>';
			$html .= '<td class="border_left border_right" align="center">'.$this_style.'</td>';
			$html .= '<td class="border_left border_right" align="center">'.$this_po.'</td>';
			$html .= '<td class="border_left border_right" align="center">'.$this_prod.'</td>';
			$html .= '<td class="border_left border_right" align="center">'.$this_htcode.'</td>';
			$html .= '<td class="border_left border_right" align="center">'.$color.'</td>';
			$html .= '<td class="border_left border_right" align="center">'.$str_qty.'</td>';
			$html .= '<td class="border_left border_right" align="center">'.$str_fob.' '.$CurrencyCode.'</td>';
			$html .= '<td class="border_left border_right" align="center">'.$str_amt.' '.$CurrencyCode.'</td>';
			$html .= '</tr>';
			
			$count_po++;
		}//--- End foreach ---//
		
	}//--- End for arr_info ---//
	
}//--- END foreach arr_fabric ---//

$str_grand_qty = number_format($grand_qty);

$html .= '<tr>';
$html .= '<td class="border_left border_right">&nbsp; </td>';
$html .= '<td class="border_left border_right"></td>';
$html .= '<td class="border_left border_right"></td>';
$html .= '<td class="border_left border_right"></td>';
$html .= '<td class="border_left border_right"></td>';
$html .= '<td class="border_left border_right"></td>';
$html .= '<td class="border_left border_right"></td>';
$html .= '<td class="border_left border_right"></td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td class="border_left border_right border_btm"></td>';
$html .= '<td class="border_left border_right border_btm"></td>';
$html .= '<td class="border_left border_right border_btm"></td>';
$html .= '<td class="full-border" colspan="2">TOTAL NO. OF UNITS</td>';
$html .= '<td class="full-border" align="center">'.$str_grand_qty.'</td>';
$html .= '<td class="border_left border_right border_btm"></td>';
$html .= '<td class="border_left border_right border_btm"></td>';
$html .= '</tr>';

$arr_discount  = $handle_class->getBuyerInvoiceDiscountOtherCharges($invID, $tblbuyer_invoice_detail);
$rowspan       = count($arr_discount) + 5;
$str_grand_amt = number_format($grand_amt,2);

$html .= '<tr>';
$html .= '<td class="border_left border_right border_top" align="center" colspan="3">SHIPPING MARKS AND NUMBERS</td>';
$html .= '<td class="border_left border_right border_top" colspan="4">TOTAL PURCHASE PRICE</td>';
$html .= '<td class="border_left border_right border_top" align="center">'.$str_grand_amt.' '.$CurrencyCode.'</td>';
$html .= '</tr>';

//=====================================================//
//--------------- DISCOUNT DISPLAY INFO ---------------//
//=====================================================//
$html .= '<tr>';
$html .= '<td class="border_left border_right" colspan="3" rowspan="'.$rowspan.'">
			<table cellpadding="2" width="100%">
			<tr>
				<td colspan="2" align="center"></td>
				</tr>
			<tr>
				<td style="width:30%">Country Of Origin</td>
				<td>'.$manucountry.'</td>
				</tr>
			<tr>
				<td>Fabric\'s Country Of Origin</td>
				<td>'.$fabric_country.'</td>
				</tr>
			<tr>
				<td>Total Gross Weight</td>
				<td>'.$grand_inv_gw.'</td>
				</tr>
			<tr>
				<td>Total Net Weight</td>
				<td>'.$grand_inv_nw.'</td>
				</tr>
			<tr>
				<td>Total Carton</td>
				<td>'.$grand_inv_ctn.'</td>
				</tr>
			<tr>
				<td>Total CBM</td>
				<td>'.$grand_inv_cbm.'</td>
				</tr>
				</table>
			
			</td>';
$html .= '<td class="border_left border_right" colspan="4">- Discount</td>';
$html .= '<td class="border_left border_right" ></td>';
$html .= '</tr>';

for($i=0;$i<count($arr_discount);$i++){
	$ID = $arr_discount[$i]["ID"];
	$discount_name = $arr_discount[$i]["discount_name"];
	$percentage    = $arr_discount[$i]["percentage"];
	$discount_amt  = $arr_discount[$i]["discount_amt"];
	
	$str_discount_amt = number_format($discount_amt, 2);
	$grand_amt -= $discount_amt;
	
	$html .= '<tr>';
	$html .= '<td class="border_left" colspan="2">'.$discount_name.'</td>';
	$html .= '<td>'.$percentage.'%</td>';
	$html .= '<td class="border_right"></td>';
	$html .= '<td class="border_left border_right" align="center">'.$str_discount_amt.' '.$CurrencyCode.'</td>';
	$html .= '</tr>';
}

$html .= '<tr>';
$html .= '<td class="border_left border_right border_btm" colspan="4"></td>';
$html .= '<td class="border_right border_btm">&nbsp;</td>';
$html .= '</tr>';

$str_grand_amt = number_format($grand_amt, 2);
$html .= '<tr>';
$html .= '<td class="border_left border_right border_top" colspan="4">SUB TOTAL</td>';
$html .= '<td class="border_right border_top" align="center">'.$str_grand_amt.' '.$CurrencyCode.'</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td class="border_left border_right" colspan="4">- D/N</td>';
$html .= '<td class="border_right" align="center"></td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td class="border_left border_right border_btm" colspan="4">D/N number</td>';
$html .= '<td class="border_right border_btm" align="center"></td>';
$html .= '</tr>';

//============================================================//
//--------------- SHIPPING ADDRESS / TOTAL AMT ---------------//
//============================================================//

$html .= '<tr>';
$html .= '<td class="border_left border_right border_top" align="center" colspan="3">
				SHIPPING ADDRESS
				
				</td>';
$html .= '<td class="full-border" colspan="4">TOTAL AMOUNT</td>';
$html .= '<td class="full-border" align="center">'.$str_grand_amt.' '.$CurrencyCode.'</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td class="border_left border_right border_btm" colspan="3" rowspan="2">'.$ship_to.'<br/>'.$ship_address.'</td>';
$html .= '<td class="full-border" colspan="5" align="center">TERM OF PAYMENT + LC-NUMBER<br/>'.$paymentterm.' / '.$lc_number.'</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td class="full-border" colspan="5" align="center">PACKING DETAILS SEE ENCLOSED PACKING LIST</td>';
$html .= '</tr>';

//============================================================//
//-------------- BANK BENEFICIARY ACCOUNT INFO ---------------//
//============================================================//

$html .= '<tr>';
$html .= '<td class="full-border" colspan="5" rowspan="2" align="center" >BANK DETAILS<br/>
				Beneficiary Name: '.$beneficiary_name.'<br/>
				Bank Name: '.$bank_name.'<br/>
				Beneficiary Account#: '.$bank_account_no.'<br/>
				Swift Code: '.$swift_code.'<br/>
				Address: '.$bank_address.'<br/></td>';
$html .= '<td class="full-border" colspan="3" align="center">
			THIS IS TO CERTIFY THAT PARTICULARS STATED HEREIN ARE AGREEABLE WITH THE PURCHASE ORDER MADE AMD GOODS BEING INSPECTED AT RANDOM</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td class="full-border" colspan="3" align="center">VENDOR\'S SIGNATURE & STAMP
			<br/><br/><br/><br/><br/>&nbsp;</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td class="full-border" colspan="5" rowspan="2" align="center">AGENCY</td>';
$html .= '<td class="full-border" colspan="3" align="center">INVOICING PARTY\'S AUTHORIZED SIGNATURE<br/><br/></td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td class="full-border" colspan="3">&nbsp; &nbsp; DATE: '.$invoice_date.'</td>';
$html .= '</tr>';

$html .= '</table>';

//= =============================== packing list ====================================//
$this_html = $handle_lc->getBuyerInvoicePackingListTemplate5($invID);
$html .= $this_html;


?>