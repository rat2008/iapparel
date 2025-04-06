<?php 
$html = <<<EOD
<style>
	.font_size_title{
		font-size:11px;
		font-family: Arial, Helvetica, sans-serif;
	}
	.font_size{
		font-size:7px;
		font-family: Arial, Helvetica, sans-serif;
	}
	.font_size_info{
		font-size:5px;
	}
	.font_size_info_6{
		font-size:6px;
	}
	.font_size_info_7{
		font-size:7px;
	}
	.font_size_info_8{
		font-size:8px;
	}
	.font_size_info2{
		font-size:2px;
	}
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
	.font_times{
		font-family: "Times New Roman", Times, serif;
	}
</style>
EOD;

$html_css = $html;
$conEIN      = (trim($conEIN)==""? "":"<br/>EIN: $conEIN");
$contel      = (trim($contel)==""? "":"<br/>Tel: $contel");
$confax      = (trim($confax)==""? "":"<br/>Fax: $confax");
$conemail    = (trim($conemail)==""? "":"<br/>Email: $conemail");
$concontact  = (trim($concontact)==""? "":"<br/>Contact Person: $concontact");
$transitPort = (trim($transitPort)==""? "":"VIA $transitPort");
$buyerdest   = (trim($buyerdest)==""? "":"TO $buyerdest");
$portDischarges   = (trim($portDischarges)==""? "":"TO $portDischarges");

$conAddress  = strtoupper($conAddress);
$manucountry = strtoupper($manucountry);

$d = date_create($shippeddate);
$shipdate = date_format($d, "d-M-Y");

$d = date_create($exfactorydate);
$exftydate = date_format($d, "d-M-Y");

$factory_ship = ($BuyerID=="B36"? "":"FACTORY SHIP DATE: &nbsp; &nbsp; <b>$exftydate</b>");
$paymentterm  = ($BuyerID=="B36"? "":"$paymentterm");

$html .= <<<EOD

<table class="font_size_title" border="0" width="100%">
	<tr>
		<td rowspan="2" width="8%">
			<img src="buyer_template/canada_flag.png" height="45" />
		</td>
		<td>Revenue Canada</td>
		<td>&nbsp; &nbsp;Revenu Canada</td>
		<td style="width:35%">&nbsp; &nbsp; &nbsp; &nbsp; CANADA CUSTOMS INVOICE</td>
	</tr>
	<tr>
		<td>Customs and Excise</td>
		<td>Dousanes et Accise</td>
		<td>FACTURE DES DOUANES CANADIENNES</td>
	</tr>
</table>

<hr>

<table cellpadding="2" border="0" width="100%" class="font_size">
	<tr>
		<td width="50%" class="border_top border_left border_right">
			<font class="font_size_info_6">1. Vendor(Name and Address)/Vendeur(nom et adresse)</font>
			<br>
			$ownership<br/>
			$owneraddress<br/>
			Tel:$ownertel &nbsp;&nbsp;&nbsp; Fax:$ownerfax
		</td>
		<td width="50%" colspan="2" class="border_right">
			<font class="font_size_info_6">2.Date of direct shipment to Canada/Date d'expedition directe vers le Canada</font>
			<br>
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
			ON/ABOUT  &nbsp; &nbsp; <b>$shipdate</b>
			<br>
			<br>
			<font class="font_size_info_6">3. Other References (Include Purchaser's Order No.) 
			<br>
			Autres references (Inclure le n de commande de l'acheteur)</font>
			
		</td>
	</tr>

	<tr>
		<td rowspan="3" width="50%" class="border_top border_left border_right">
			<font class="font_size_info_6">4. Consignee(Name and Address)/Destinataire(Nom et adresse)</font>
			<br>
			$conName
			<br>
			$conAddress $conemail $concontact $contel $confax
			<br><br>
		</td>
		<td width="50%" colspan="2" class="border_top border_right">
			<font class="font_size_info_6">5. Purchaser's Name and Address(If other than Consignee) 
 			<br>
 			Nom et adresse de l'achereur(s'il differe du destrinataire)</font>
 			<br>	
		</td>
	</tr>

	<tr>
		<td width="50%" colspan="2" class="border_top border_right">
			<font class="font_size_info_6">6. Country of Transhipment/Pays de transbordement</font>
			<br>&nbsp;
		</td>
	</tr>

	<tr>
		<td class="border_top right_border">
		<font class="font_size_info_6">7.Country of Origin of Goods<br/>
			Pays d'origine des marchandises	</font>
			
			<font class="font_size_info2">&nbsp;</font><br/>
			<font align="center" style="padding-top:10px">$manucountry</font>
		</td>
		<td class="border_top right_border font_size_info">
			<span style="margin-top:9px">IF SHIPMENT INCLUDES GOODS OF DIFFERENT ORINGIN
			SENTER ORIGINS AGINST ITEMS IN 12
			<br>
			<i>SI L EXTEDMON COMPREND DES MARCHANDISES D'ORIGINES 
		     DIFFERENTES.PRECISER LEUR PROVENANCE EN 12</i></span>
		</td>
	</tr>
	<tr>
		<td rowspan="2" class="border_top left_border right_border border_btm">
			<font class="font_size_info_6">8. Transportation:Give Mode and Place of Direct Shipment to Canada
    		<br>Transport: Preciser mode et point d'expcdition directe vers le Canada</font>
    		<br>
    		<br>
			<table>
    			<tr>
    				<td><b>BY $shipmode</b></td>
    				<td>ON/ABOUT: &nbsp; &nbsp; <b>$shipdate</b></td>
    			</tr>
    		</table>
    		<br>FROM $portLoading $transitPort
    		<br>$buyerdest
    		<br>$factory_ship
    		
		</td>
		<td colspan="2" class="border_top right_border">
			<font class="font_size_info_6">9. Conditions of Sale and Terms of Payment
			<br> (i.e. Sale, Consignment Shipment, Leased goods, etc.)
			<br> Conditions de vente et modalites de paiement
			<br> (p.ex.vente, expedition en consignation, location de marchandises, etc.)</font>
			<p>$tradeterm</p>
			<p>$paymentterm</p>		
		
		</td>
	</tr>

	<tr>
		<td colspan="2" class="border_top right_border border_btm">
			<font class="font_size_info_6">10.Currency of Settlement/Devises du paiement</font>
			<br> US DOLLARS
		</td>
	</tr>

</table>

EOD;

//====================================== print invoice detail ======================================//
$html .= <<<EOD

<table cellpadding="2" class="font_size">
	
		<tr>
			<th class="border-rl" rowspan="3" style="width:10%">
				<font class="font_size_info_6">11. No.of Pkgs
				<br>Nbre de colis</font>
			</th>
			<th class="border-rl" rowspan="3" colspan="5" style="width:62%">
				<font class="font_size_info_6">12. Specification of Commodities(Kind of packages, Marks and Numbers, General
				<br> Description and Characteristices, i.e. Grade, Quality)
				<br> Designation des articles(Nature des colis, marques ct numeros, description generale
				<br> et caracteristiques.p.ex.classe, qualite)</font>
			</th>
			<th class="border-rl" rowspan="2" align="center" style="width:10%">
				<font class="font_size_info_6">13. Quantity<br/>(State Unit)
				 <br> Quantite<br/>(Preciser l'unite)</font>
			</th>
			<th class="full-border" align="center" colspan="2" style="width:18%">
				Selling Price/Prix de vente
			</th>
		</tr>
		<tr>
			<th class="border-rl" align="center">
				<font class="font_size_info_6">14. Unit price
				<br> Prix unitaire</font>
			</th>
			<th class="border-rl" align="center">
				<font class="font_size_info_6">15. Total</font></th>
		</tr>
		<tr>
			<th class="border-rl" align="center">PCS</th>
			<th class="border-rl" align="center">USD/PCS</th>
			<th class="border-rl" align="center">USD</th>
		</tr>
	


EOD;



// load count ctn, and weight 
// $sql_ctn_weight = $conn->prepare("
		// SELECT count(cpt.ctn_num) as count_ctn, sum(cpt.net_weight) as net_weight, sum(cpt.gross_weight) as gross 
		// FROM tblcarton_picklist_transit cpt 
		// WHERE cpt.shipmentpriceID IN 
		// (SELECT bid.shipmentpriceID FROM tblbuyer_invoice_detail bid WHERE bid.invID = :invID AND bid.del=0)
	// "); 
// $sql_ctn_weight->execute([
	// "invID" => $invID 
// ]);
// $obj_ctn_weight = $sql_ctn_weight->fetchObject();

$count_ctn = 0;//$obj_ctn_weight->count_ctn;
$net_weight = 0;//$obj_ctn_weight->net_weight;
$gross = 0;//$obj_ctn_weight->gross;

// $arr_value   = $handle_class->getBuyerInvoiceDescriptionInfo($invID);
// $arr_buyerpo = $arr_value["byBuyerPO"];

$query_filter = " ";
$handle_lc->isBuyerPayment = 1;
$arr_value   = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_buyerpo = $arr_value["byBuyerPO"];

foreach($arr_buyerpo as $key => $arr_info){
	$total_ctn = $arr_info["total_ctn"];
	$grand_nw  = $arr_info["grand_nw"];
	$grand_gw  = $arr_info["grand_gw"];
	
	$count_ctn += $total_ctn;
	$net_weight += $grand_nw;
	$gross += $grand_gw;
}




// load invoice detail
$totalprice = 0;
$totalqty = 0;	
$haveShowCtn = false;	
// $detailsql = $conn->prepare("
	// SELECT invd.*, g.styleNo, sp.Orderno ,sp.BuyerPO
	// FROM tblbuyer_invoice_detail invd 
	// LEFT JOIN tblshipmentprice sp ON sp.ID=invd.shipmentpriceID 
	// LEFT JOIN tblgarment g ON g.garmentID IN (sp.StyleNo) 
	// WHERE invd.invID=:invID AND invd.del=0 AND invd.group_number>0
	// GROUP BY invd.shipmentpriceID 
	// ORDER BY invd.ID ASC 
// ");
// $detailsql->execute([
	// "invID" => $invID 
// ]); 
$arr_hts = array();
$ponum = 0; $count_color = 0; $count_po = 0;
// while($detailrow = $detailsql->fetch(PDO::FETCH_ASSOC)){
foreach($arr_buyerpo as $BuyerPO => $arr_value){
	$ponum++;
	$Orderno      = "";
	$spID         = $arr_value["shipmentpriceID"];
	$ht_code      = $arr_value["ht_code"];
	$ship_marking = $arr_value["ship_marking"];
	$styleNo      = $arr_value["styleNo"];
	$arr_info     = $arr_value["arr_info"];
	array_push($arr_hts, $ht_code);
	
	// print_r($arr_value);
	// echo "<hr/>";
	
	$ship_marking = strtolower($ship_marking);
	$ship_marking = html_entity_decode($ship_marking);
	$ship_marking = strtoupper($ship_marking);
	
	$arrbuyerpo = explode("%%", $BuyerPO);
	$BuyerPO = $arrbuyerpo[0];
	
	$arrstyleno = explode("_", $styleNo);
	$styleNo = $arrstyleno[0];
	
	$count_po++;
	
// use to display the total carton
$temp_row = "";
if(!$haveShowCtn) {

$temp_row = <<<EOD
<tr>
	<td class="border-rl">
		$count_ctn CARTONS
	</td>
	<td class="border-rl" colspan="5">$ship_marking</td>
	<td class="border-rl"></td>
	<td class="border-rl"></td>
	<td class="border-rl"></td>
</tr>
EOD;

	$haveShowCtn = true;
} // end if
else{
	$temp_row = <<<EOD
<tr>
	<td class="border-rl"></td>
	<td class="border-rl" colspan="5">
		$ship_marking
	</td>
	<td class="border-rl"></td>
	<td class="border-rl"></td>
	<td class="border-rl"></td>
</tr>
EOD;
}

$lbl_color = ($BuyerID=="B36"? "":"COLOR");
$lbl_cat   = ($BuyerID=="B36"? "":"CAT");
$lbl_ng    = ($BuyerID=="B36"? "CLASS NO":"NG ITEM#");
$lbl_ng    = (glb_profile=="lushbax"? "$lbl_color": $lbl_ng);
$lbl_cat   = (glb_profile=="lushbax"? "": $lbl_cat);
$lbl_color = (glb_profile=="lushbax"? "": $lbl_color);

$html .= <<<EOD
$temp_row
<tr>
	<td class="border-rl"></td>
	<td align="center" style="width:12%">PO NO: </td>
	<td align="center" style="width:12%">STYLE NO: </td>
	<td align="center" style="width:12%">$lbl_ng </td>
	<td align="center" style="width:18%">$lbl_color</td>
	<td align="center" class="border_right" style="width:8%">$lbl_cat</td>
	<td class="border-rl"></td>
	<td class="border-rl"></td>
	<td class="border-rl"></td>
</tr>
EOD;


$count=0; 
// load the ng item, and color
// $sql = $conn->prepare("
		// SELECT spk.PID, spd.SKU as ng_item, c.ColorName as color, qc.Description as quo_cat,
			// (SELECT sum(scsq.qty) FROM tblship_colorsizeqty scsq 
			// WHERE scsq.shipmentpriceID = spk.shipmentpriceID 
			// AND scsq.colorID = sgc.colorID 
			// AND scsq.garmentID = scsq.garmentID AND scsq.statusID = 1) as total_qty, scsq2.price 
		// FROM `tblship_packing` spk 
		// INNER JOIN tblship_packing_detail spd ON spd.PID = spk.PID
		// INNER JOIN tblship_group_color sgc ON sgc.shipmentpriceID = spk.shipmentpriceID 
											// AND sgc.group_number = spd.group_number 
											// AND sgc.statusID = 1
		// INNER JOIN tblcolor c ON c.ID = sgc.colorID
		// INNER JOIN tblship_colorsizeqty scsq2 ON scsq2.shipmentpriceID = spk.shipmentpriceID AND scsq2.colorID = sgc.colorID AND scsq2.statusID = 1
		// INNER JOIN tblshipmentprice sp ON sp.ID = spk.shipmentpriceID
		// INNER JOIN tblquotacat qc ON qc.ID = sp.QuotaID
		// WHERE spk.shipmentpriceID = :spID AND spk.statusID = 1 AND spd.statusID = '1' 
		// group by spd.SKU
	// ");
// $sql->execute([
	// "spID" => $spID
// ]);

// $rowspan = $sql->rowCount();
// while($row = $sql->fetch(PDO::FETCH_ASSOC)){
$rowspan = count($arr_info);
foreach($arr_info as $key => $arr_Prepackname){
	list($ng_item, $group_number) = explode("**^^", $key);
	

	//$ng_item = $row["ng_item"]; 
	$color       = $arr_Prepackname["colorOnly"];
	$garmentID   = $arr_Prepackname["garmentID"];
	$fob_price   = $arr_Prepackname["fob_price"];
	
	$sqlqc = "SELECT qc.Description as this_quotacat
                     FROM tblbuyer_invoice_payment_hts bih
                     LEFT JOIN tblquotacat qc ON qc.ID = bih.quotaID
                     WHERE bih.invID='$invID' AND bih.shipmentpriceID='$spID' AND bih.garmentID='$garmentID' 
					 AND qc.Description !=''";
	// echo "<pre>$sqlqc</pre><hr/>";
	$stmtqc = $conn->query($sqlqc);
	$rowqc = $stmtqc->fetch(PDO::FETCH_ASSOC);
		$this_quotacat = $rowqc["this_quotacat"];
	
	$color = ($BuyerID=="B36"? "":"$color");	
	// $color   = $arr_Prepackname["color"]; 
	$quo_cat = $arr_value["quotacat"]; 
	$quo_cat = ($BuyerID=="B36"? "":"$this_quotacat");	
	$qty     = $arr_Prepackname["qty"]; 
	$price   = round($fob_price, 2); 
	$subtotal = $qty * $price; 
	
	// echo "quo_cat: $quo_cat << <br/>";

	$totalprice+=$subtotal; 
	$totalqty+=$qty; 

	$temp="";
	$temp1="";

if($count == 0){
$temp = <<<EOD
	<td align="center" rowspan="$rowspan">$BuyerPO </td>
	<td align="center" rowspan="$rowspan">$styleNo</td>
EOD;
$temp1 = <<<EOD
	<td align="center" rowspan="$rowspan" class="border_right">$quo_cat</td>
EOD;
}// end if

$str_fob = number_format($price, 2);
$str_total = number_format($subtotal, 2);

$ng_item = (glb_profile=="lushbax"? "$color": $ng_item);
$color   = (glb_profile=="lushbax"? "": $color);

$html .= <<<EOD
<tr>
	<td class="border-rl"></td>
	$temp
	<td align="center">$ng_item</td>
	<td align="center">$color</td>
	$temp1
	<td class="border-rl" align="center">$qty</td>
	<td class="border-rl" align="center">$str_fob</td>
	<td class="border-rl" align="center">$str_total</td>
</tr>
EOD;

$count++;
$count_color++;
}// end while




// empty row
$empty_row = <<<EOD
	<tr>
		<td class="border-rl" style="min-height:2px;max-height:2px;height:2px"></td>
		<td class="border-rl" style="min-height:2px;max-height:2px;height:2px" colspan="5"></td>
		<td class="border-rl" style="min-height:2px;max-height:2px;height:2px"></td>
		<td class="border-rl" style="min-height:2px;max-height:2px;height:2px"></td>
		<td class="border-rl" style="min-height:2px;max-height:2px;height:2px"></td>
	</tr>
EOD;

$lbl_discount = '';
$str_discount = '';
if(count($arr_buyerpo)<>$ponum){
$html .= <<<EOD
	$empty_row
EOD;
}
else if(glb_profile!="lushbax"){
	if($BuyerID=="B36"){// for AEO only
		$lbl_discount = "TOTAL COST OF MERCHANDISE LESS PAYMENT DISCOUNT";
		
		$sqloth = "SELECT sum(total_amount) as total_amount
					FROM `tblbuyer_invoice_payment_detail` 
					WHERE invID='$invID' AND del=0 AND group_number=0 AND total_amount<0";//AND shipmentpriceID=0
		$stmt_oth = $conn->prepare($sqloth);
		$stmt_oth->execute();
		$rowoth = $stmt_oth->fetch(PDO::FETCH_ASSOC);
			$other_charge = $rowoth["total_amount"];
			$other_charge = ($other_charge==""? 0: $other_charge);
		
		$goc_discount = $other_charge;
		$str_discount = number_format($other_charge, 2);
		$goc_discount = $other_charge * -1;
	}
	else if($BuyerID=="B82" && glb_profile=="iapparelintl"){
		// $lbl_discount = "LESS:40% GOC DISCOUNT";
		// $goc_discount = round($totalprice * 0.4, 2);
		// $str_discount = number_format($goc_discount, 2);
	}
	else{
		$lbl_discount = "LESS:2.5% GOC DISCOUNT";
		$goc_discount = round($totalprice * 0.025, 2);
		$str_discount = number_format($goc_discount, 2);
	}
	
	$html .= <<<EOD
	<tr>
		<td class="border-rl"></td>
		<td class="border-rl" colspan="5">$lbl_discount</td>
		<td class="border-rl"></td>
		<td class="border-rl"></td>
		<td class="border-rl" align="center">$str_discount</td>
	</tr>
EOD;
}


} // end foreach

$this_ht = implode(", ", $arr_hts);
$str_ht = "";//($this_ht==""? "": "HTS: $this_ht");

$empty_row = <<<EOD
	<tr>
		<td class="border-rl border_top"></td>
		<td class="border-rl border_top" colspan="5">$str_ht</td>
		<td class="border-rl border_top"></td>
		<td class="border-rl border_top"></td>
		<td class="border-rl border_top"></td>
	</tr>
EOD;

// empty row 
// $html .= <<<EOD
	// <tr>
		// <td class="border-b border-rl"></td>
		// <td class="border-b border-rl" colspan="5"></td>
		// <td class="border-b border-rl"></td>
		// <td class="border-b border-rl"></td>
		// <td class="border-b border-rl"></td>
	// </tr>
// EOD;

$sqlBID = "SELECT other_charge, total_amount, charge_percentage
			FROM `tblbuyer_invoice_detail`
			WHERE invID = '$invID' AND group_number = 0 
			AND del=0 AND total_amount<>0";
$stmt_bid = $conn->prepare($sqlBID);
$stmt_bid->execute();
$row_bid = $stmt_bid->fetch(PDO::FETCH_ASSOC);
	$other_charge      = $row_bid["other_charge"];
	$charge_percentage = $row_bid["charge_percentage"];
	$total_amount = $row_bid["total_amount"];
	$total_amount = ($total_amount==""? 0: $total_amount);
	$str_charge   = ($total_amount==0? "": $total_amount);
	$str_description = ($other_charge==""?"": "$other_charge");
//$discount = $totalprice * 0.025; 
$discount = number_format($total_amount,2); 
$totalprice += (Double)$discount; 

$totalprice = $totalprice - $goc_discount;
$str_totalprice = number_format($totalprice, 2);

// $html .= <<<EOD
// <tr>
	// <td class="border-rl"></td>
	// <td class="border-rl" colspan="5">
		// <!--LESS: 2.5% GOC DISCOUNT -->
		// $str_description
	// </td>
	// <td class="border-rl"></td>
	// <td class="border-rl"></td>
	// <td class="border-rl" align="center">$str_charge</td>
// </tr>

// EOD;

$lbl_marking = (glb_profile=="lushbax"? "": "MARKS & NOS AS PER COMMERCIAL INVOICE");

$html .= <<<EOD
<tr>
	<td class="border-b border-rl "></td>
	<td class="border-b border-rl " colspan="5">$lbl_marking
	</td>
	<td class="border-b border-rl"></td>
	<td class="border-b border-rl"></td>
	<td class="border-b border-rl"></td>
</tr>
EOD;

if(($count_color>6 && $count_po<6)  || (($count_po<8 && $count_color<20) && ($count_po>6 && $count_color<6))){ //|| ($count_po>1 && $count_color>4)
	$html .= '</table>';
	$pdf->writeHTML($html, true, 0, true, 0);
	$html = $html_css.' <br pagebreak="true"><table cellpadding="2" class="font_size">';
}

// 16-18
$html .= <<<EOD
		<tr>
			<td class="full-border" colspan="6" rowspan="2">
				<font class="font_size_info_6">18.
				<br> If any of fields 1 to 17 are included on an attached commercial invoice, check this box
				<br> Si les renseignements des zones 1 a 17 figurent sur la facture commerciale, cocher cette boite
				<br> Commercial Inoice No.IN de la facture commerciale : </font>
					 
					<b>
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
					$invoice_no</b>
			</td>
			<td class="full-border" colspan="2">
				<font class="font_size_info_6">16. Total Weight / Poids Total</font><br/>&nbsp;
			</td>
			<td class="border_left border_right border_top" rowspan="1">
				<font class="font_size_info_6">17. Invoice Total
				<br> Total de la facture</font>
			</td>
		</tr>
		<tr>
			<td class="full-border">
				<font class="font_size_info_6">Net</font>
				<br> <b>$net_weight KGS</b>
			</td>
			<td class="full-border">
				<font class="font_size_info_6">Gross / Brut</font>
				<br> <b>$gross KGS</b>
			</td>
			<td class="border_left border_right border_btm">
				&nbsp; <br/>
				<b>USD $str_totalprice</b></td>
		</tr>
EOD;

// manufacturer
$sql = $conn->prepare("
		SELECT DISTINCT o.manufacturer, f.* 
		FROM tblbuyer_invoice_detail invd 
		LEFT JOIN tblshipmentprice sp ON invd.shipmentpriceID=sp.ID
		LEFT JOIN tblorder o ON o.Orderno=sp.Orderno 
		LEFT JOIN tblfactory f ON f.FactoryID=o.manufacturer 
		WHERE invd.invID=:invID AND invd.del=0 AND invd.group_number>0
	");
$sql->execute([
	"invID" => $invID 
]);
$strManufacturer="";
// while($manufacturer = $sql->fetch(PDO::FETCH_ASSOC)) {
	// $FactoryName_ENG = strtoupper($manufacturer["FactoryName_ENG"]); 
	// $Address = $manufacturer["Address"]; 
	// $Tel = $manufacturer["Tel"]; 
	// $Fax = $manufacturer["Fax"]; 

$strManufacturer .= <<<EOD
<p class="p-format"><b>$shipper
	<br>$shipper_addr
	<br>TEL: $shipper_tel &nbsp; &nbsp; FAX: $shipper_fax</b>
</p>
EOD;
// }// end manufacturer


// contract owner

$strContractOwner="";

$ownership = strtoupper($ownership);
$owneraddress = strtoupper($owneraddress);
$ownertel = strtoupper($ownertel);
$ownerfax = strtoupper($ownerfax);

$strContractOwner .= <<<EOD
<p class="p-format">
	$ownership
	<br>$owneraddress
	<br>Tel: $ownertel &nbsp;&nbsp;&nbsp; Fax: $ownerfax
</p>
EOD;

// $strManufacturer  = strtoupper(htmlspecialchars_decode($strManufacturer));
// $strContractOwner = strtoupper($strContractOwner);

// 19-22
$html .= <<<EOD
	<tr>
		<td class="full-border" colspan="5">
			<font class="font_size_info_6">19. Exporter's Name and Address(If other than Vendor)
			<br> Nom et adresse de I'exportateur (S'il differe du vendeur)</font>
$strManufacturer
		</td>
		<td class="full-border" colspan="4">
			<font class="font_size_info_6">20. Originator(Name and Address) / Expediteur d'origine (Nom et adresse)</font>
			$strContractOwner
		</td>
	</tr>
	<tr>
		<td class="full-border" colspan="5">
			<font class="font_size_info_6">21. Departmental Ruling(if applicable) / Decision du Ministere(s'il y a lieu)</font>
		</td>
		<td class="full-border" colspan="4">
			<font class="font_size_info_6">22.</font>
			<br>
			<table border="0">
				<tr>
					<td width="65%">
						<font class="font_size_info_6">&nbsp; If fields 23 to 25 are not applicable, check this box
						<br>&nbsp; Si les zones 23 a 25 sont sans objet. coch er cette boite</font>
					</td>
					<td align="center" width="35%"><img src="buyer_template/square.png" height="15" /></td>
				</tr>
			</table>
		</td>
	</tr>

	</table>
EOD;

$lbl_trucking  = ($BuyerID=="B36"? "":"TBA (TRUCKING COST FROM BROKER) <br>");
$lbl_tbacharge = ($BuyerID=="B36"? "":"TBA (TBA  (CHARGES FROM DHL))");
// 23-25
$html .= <<<EOD

<table cellpadding="2" border="1" class="font_size">
	<tr>
		<td>
			<font class="font_size_info_6">23. If included in field 17 indicate amount:
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Si compris dans le total a la rone 17, preciser:
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			(i) Transportation charges, expenses and insurance
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			from the place of direct shipment to Canada
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Les frais de transport depenses et assurances
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			a partir du point d'expedirion directe vers le Canada
			<br>
			<font class="font_size_info_8">
				 $lbl_trucking 
			 $lbl_tbacharge</font>
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			(ii) Costs for construction, erection and assembly
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			incurred after importation into Canada
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Lcs couts de construction, d'erection et
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			d'assemblage apres importation ou Canada
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			$<span> ______________________________________ </span>
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			(iii) Export packing
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Le cout de l'emballage d'exportation
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			$<span> ______________________________________ </span></font>

		</td>

		<td>
			<font class="font_size_info_6">24. If not included in field 17 indicate amount:
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Si non compris dans le total a la zone 17. preciser:
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			(i) Transportation charges, expenses and insurance
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			to the place of direct shipment to Canada
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Les frais de transport, deperses et assurances
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			jusau'ou point d'expedition directe vers le Canada
			<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			$<span> ______________________________________ </span>
			<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			(ii) Amounts for commissions other than buying commissions
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Les commissions outres que celles versees pour l'achat
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			$<span> ______________________________________ </span>
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			(iii) Export packing
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Le cout de l'emballage d'exportartion
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			$<span> ______________________________________ </span></font>

		</td>

		<td>
			<font class="font_size_info_6">25. Check (If applicable):
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Cocher ( s'il y a lieu):
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			(i) Royalty payments or subsequent proceeds are
			paid or payable  by the purchaser
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Des redevances ou produits ont ete ou seront
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			verser par l'acheteur</font>
			<br>
			<div align="center"><img src="buyer_template/square.png" height="30" /></div>
			<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<font class="font_size_info_6">(ii) The purchaser has supplied goods or services
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			for use in the production of these Goods
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			L'acheteur a fourni des marchandises ou des
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			services pour la production des marchandises</font>
			<br>
			<div align="center"><img src="buyer_template/square.png" height="30" /></div>
		</td>
	</tr>
</table>

EOD;





?>