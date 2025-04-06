<?php 
$pdf->SetTitle("Joe Fresh");

//==================== buyer "JOE FRESH" ; id: B13 =====================================//
// ============== pdf header ===================// 
$html .= <<<EOD
<style>
.center-align {
	text-align: center;
}
table.tb_bic{
	font-family: "Calibri", Candara, Segoe;
	font-size:10px;
}
table.tb_detail{
	font-family: "Calibri", Candara, Segoe;
	font-size:7px;
	font-weight:normal;
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
</style>

<table border="0" class="tb_bic">
	<tr>
		<th class="bold-text center-align">
			<h1>$manufacturer</h1>
		</th>
	</tr>

	<tr>
		<td class="center-align">
			$manuaddress
		</td>
	</tr>

	<tr>
		<td class="center-align">
			TEL : $manutel &nbsp;&nbsp;&nbsp;&nbsp;FAX : $manufax
		</td>
	</tr>

</table>

EOD;

$name = strtoupper("$exporter_sign");
$byrdt_ctrcode = ($byrdt_ctrcode=="US"? "USA":$byrdt_ctrcode);

$html .= <<<EOD
	<h3 align="center">*** MULTIPLE COUNTRY OF ORIGIN ***</h3>
	
	<table >
	<tr>
		<td style="width:60%">I, $name (name), declare that the articles described below and covered by the invoice or entry, which this declaration 
			relates were exported from the country identified below on the dates listed and were subjected to assembling, manufacturing or
			processing operation in, and/or incorporate materials originating in the foreign territory or country "or countried" or the U.S.
			or an insular possession of the U.S. identified below. I declare that the information set forth in this declaration is correct
			and true to the best of my information, knowledge, and belief.</td>
		<td></td>
		</tr>
		</table>
	<br/>
	<br/>
EOD;

	$fab_country = $handle_lc->getFabricCountryOrigin($grp_spID);
	$transit_etd = $handle_lc->transit_etd;
$html .= <<<EOD
	<table>
	<tr>
		<td style="width:2%" class="bottom_border">A</td>
		<td style="width:25%" class="bottom_border">$manucountry</td>
		<td style="width:5%" class="bottom_border">(Country)</td>
		<td style="width:26%"></td>
		<td style="width:2%" class="bottom_border">C.</td>
		<td style="width:25%" class="bottom_border">$byrdt_country</td>
		<td style="width:5%" class="bottom_border">(Country)</td>
		<td style="width:10%" ></td>
		</tr>
	<tr>
		<td style="width:2%" class="bottom_border">B</td>
		<td style="width:25%" class="bottom_border">$fab_country</td>
		<td style="width:5%" class="bottom_border">(Country)</td>
		<td style="width:26%"></td>
		<td style="width:2%" class="bottom_border">D.</td>
		<td style="width:25%" class="bottom_border"></td>
		<td style="width:5%" class="bottom_border">(Country)</td>
		<td style="width:10%" ></td>
		</tr>
		</table>
EOD;

if(true){// AEO
$html .= <<<EOD
	<br/>
	<br/>
	<table cellpadding="2" class="tb_detail">
	<tr>
		<td class="all_border" rowspan="2" style="width:9%" align="center"><br/>Mark Of Identification Numbers</td>
		<td class="all_border" rowspan="2" style="width:28%" align="center" valign="middle">
			<br/>Description Articles and quantity</td>
		<td class="all_border" rowspan="2" style="width:12%" align="center">Description of manufacturing and/or processing operations </td>
		<td class="all_border" colspan="3" style="width:28%" align="center">Date and country of manufacturer and/or</td>
		<td class="all_border" colspan="2" style="width:21%" align="center"><br/>Materials</td>
		</tr>
	<tr>
		<td class="all_border" style="width:7%">Country</td>
		<td class="all_border" style="width:7%">Date of Export</td>
		<td class="all_border" style="width:14%">Description of Material</td>
		<td class="all_border" style="width:11%">Country of Production</td>
		<td class="all_border" style="width:10%">Date of Exportation</td>
		</tr>
EOD;
}
else{

$html .= <<<EOD
	<br/>
	<br/>
	<table cellpadding="2" class="tb_detail">
	<tr>
		<td class="all_border" rowspan="2" style="width:9%" align="center"><br/>Mark Of Identification Numbers</td>
		<td class="all_border" rowspan="2" style="width:2%"></td>
		<td class="all_border" rowspan="2" style="width:28%" align="center" valign="middle"><br/>Description Articles and quantity</td>
		<td class="all_border" style="width:12%" align="center">Description of manufacturing and/or processing operations </td>
		<td class="all_border" style="width:8%" align="center">Date and country of manufacturer and/or</td>
		<td class="all_border" colspan="3" style="width:41%" align="center"><br/>Materials</td>
		</tr>
	<tr>
		<td class="all_border">Country</td>
		<td class="all_border">Date of Exportation</td>
		<td class="all_border" style="width:26%">Description of Material</td>
		<td class="all_border" style="width:8%">Country of Production</td>
		<td class="all_border" style="width:7%">Date of Exportation</td>
		</tr>
EOD;

}
$query_filter = "";
$arr_array    = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_buyerpo  = $arr_array["byBuyerPO"];

$count_po = 0;
foreach($arr_buyerpo as $buyerPO => $arr_info){
	$ship_marking = $arr_info["ship_marking"];
	$count_po += 2;
	// $count_po += 1;
}

$rowspan = $count_po + 3;
// $rowspan = $count_po + 2;

$lbl_info = ($BuyerID=="B36"? "AS PER ATTACHED PACKING LIST" : "SAME AS COMMERCIAL INVOICE");
$lbl_inspection = ($BuyerID=="B36"? "LABELING <br/>IRONING<br/>FINISHING<br/>PACKING":"INSPECTION");

if(true){// AEO
$html .= <<<EOD
	<tr>
		<td class="right_border left_border bottom_border" align="center" rowspan="$rowspan">$lbl_info</td>
		<td class="right_border left_border"></td>
		<td class="right_border left_border bottom_border" align="center" rowspan="$rowspan">CUTTING<br/>SEWING<br/><br/>$lbl_inspection</td>
		<td class="right_border left_border" ></td>
		<td class="right_border left_border"></td>
		<td class="right_border left_border " ></td>
		<td class="right_border left_border " ></td>
		<td class="right_border left_border " ></td>
		</tr>
	<tr>
		<td class="right_border left_border">
			<table>
			<tr>
				<td><u>PO#</u></td>
				<td><u>STYLE#</u></td>
				<td><u>QTY</u></td>
				</tr>
				</table>
			</td>
		<td class="right_border left_border"></td>
		<td class="right_border left_border"></td>
		<td class="right_border left_border"></td>
		<td class="right_border left_border"></td>
		<td class="right_border left_border"></td>
		</tr>
EOD;
}
else{
$html .= <<<EOD
	<tr>
		<td class="right_border left_border bottom_border" align="center" rowspan="$rowspan">$lbl_info</td>
		<td class="right_border left_border bottom_border" rowspan="$rowspan"></td>
		<td class="right_border left_border"><u>$invoice_no</u><br/>&nbsp;</td>
		<td class="right_border left_border bottom_border" align="center" rowspan="$rowspan">CUTTING<br/>SEWING<br/><br/>$lbl_inspection</td>
		<td class="right_border left_border" ></td>
		<td class="right_border left_border"></td>
		<td class="right_border left_border " ></td>
		<td class="right_border left_border " ></td>
		</tr>
	<tr>
		<td class="right_border left_border">
			<table>
			<tr>
				<td><u>PO#</u></td>
				<td><u>STYLE#</u></td>
				<td><u>QTY</u></td>
				</tr>
				</table>
			</td>
		<td class="right_border left_border"></td>
		<td class="right_border left_border"></td>
		<td class="right_border left_border"></td>
		<td class="right_border left_border"></td>
		</tr>
EOD;
}

$all_grand_qty = 0; $arr_grandqty_unit = array("PCS"=>0, "SETS"=>0);
foreach($arr_buyerpo as $buyerPO => $arr_info){
	$shippeddate     = $arr_info["shippeddate"];
	$shipmentpriceID = $arr_info["shipmentpriceID"];
	$ship_marking = $arr_info["ship_marking"];
	$grand_qty    = $arr_info["grand_qty"];
	$styleNo      = $arr_info["styleNo"];
	$ht_code      = $arr_info["ht_code"];
	$uom          = $arr_info["uom"];
	$FabricContent = $ship_marking;
	
	$arrbuyerpo = explode("%%", $buyerPO);
	$buyerPO = $arrbuyerpo[0];
	
	$arrstyleno = explode("_", $styleNo);
	$styleNo = $arrstyleno[0];
	
	$d = date_create($shippeddate);
	$shipdate = date_format($d, "d-M-Y");
	
	$d = date_create($transit_etd);
	$transitdate = date_format($d, "d-M-Y");
	
	if(true){
		$sqlfab = "SELECT mmd.FabricContent, ps.Description as position
					FROM tblshipmentprice sp 
					INNER JOIN `tblmpurchase` mp ON mp.orderno = sp.Orderno
					INNER JOIN tblmm_detail mmd ON mmd.MMID = mp.MMID
					INNER JOIN tblposition ps ON ps.ID = mp.positionID
					WHERE sp.ID='$shipmentpriceID' AND mmd.FabricContent<>'' 
					AND (ps.Description like '%Body%' or ps.Description like 'BOD%')";
		$stmtfab = $conn->prepare($sqlfab);
		$stmtfab->execute();
		$rowfab = $stmtfab->fetch(PDO::FETCH_ASSOC);
			$FabricContent = $rowfab["FabricContent"];
	}
	
	$all_grand_qty += $grand_qty;
	
	$arr_grandqty_unit["$uom"] += $grand_qty;
	
	if(true){//AEO
		$html .= <<<EOD
		<tr>
			<td class="right_border left_border">$ship_marking</td>
			<td class="right_border left_border" align="center">A</td>
			<td class="right_border left_border">$shipdate </td>
			<td class="right_border left_border">$FabricContent </td>
			<td class="right_border left_border" align="center">B</td>
			<td class="right_border left_border">$transitdate</td>
			</tr>
		<tr>
			<td class="right_border left_border">
				<table>
				<tr>
					<td>$buyerPO</td>
					<td>$styleNo</td>
					<td>$grand_qty $uom</td>
					</tr>
				<tr>
					<td colspan="3">HTS CODE: $ht_code</td>
					</tr>
					</table>
			</td>
			<td class="right_border left_border"></td>
			<td class="right_border left_border"></td>
			<td class="right_border left_border"></td>
			<td class="right_border left_border"></td>
			<td class="right_border left_border"></td>
		</tr>
EOD;
	}
	else{
	
	$html .= <<<EOD
		<tr>
			<td class="right_border left_border">$ship_marking</td>
			<td class="right_border left_border">$shippeddate</td>
			<td class="right_border left_border">$FabricContent</td>
			<td class="right_border left_border">$manucountry</td>
			<td class="right_border left_border">$shippeddate</td>
			</tr>
		<tr>
			<td class="right_border left_border">
				<table>
				<tr>
					<td>$buyerPO</td>
					<td>$styleNo</td>
					<td>$grand_qty $uom</td>
					</tr>
				<tr>
					<td colspan="3">HTS CODE: $ht_code</td>
					</tr>
					</table>
			</td>
			<td class="right_border left_border"></td>
			<td class="right_border left_border"></td>
			<td class="right_border left_border"></td>
			<td class="right_border left_border"></td>
		</tr>
EOD;

	}
}//--- End foreach ---//

if(true){//AEO
$html .= <<<EOD
	<tr>
		<td class="right_border left_border bottom_border">&nbsp;<br/>
			<table>
				<tr><td> </td>
					<td align="right">&nbsp; GRAND TOTAL: </td>
					<td class="top_border bottom_border" align="center">
EOD;

if($arr_grandqty_unit["PCS"]>0){
	$all_grand_qty = $arr_grandqty_unit["PCS"];
$html .= <<<EOD
					<b>$all_grand_qty PCS</b> <br/>
EOD;
}

if($arr_grandqty_unit["SETS"]>0){
	$all_grand_qty = $arr_grandqty_unit["SETS"];
$html .= <<<EOD
					<b>$all_grand_qty SETS</b>
EOD;
}

$html .= <<<EOD
					</td>
					</tr></table><br/>&nbsp;
					</td>
		<td class="right_border left_border bottom_border"></td>
		<td class="right_border left_border bottom_border"></td>
		<td class="right_border left_border bottom_border"></td>
		<td class="right_border left_border bottom_border"></td>
		<td class="right_border left_border bottom_border"></td>
		</tr>
EOD;
}
else{
$html .= <<<EOD
	<tr>
		<td class="right_border left_border bottom_border">&nbsp;<br/>
			<table>
				<tr><td><u>TOTAL: </u> </td>
					<td>&nbsp;</td>
					<td><b>$all_grand_qty $uom</b></td>
					</tr></table><br/>&nbsp;
					</td>
		<td class="right_border left_border bottom_border"></td>
		<td class="right_border left_border bottom_border"></td>
		<td class="right_border left_border bottom_border"></td>
		<td class="right_border left_border bottom_border"></td>
		</tr>
EOD;
}

$html .= <<<EOD
	</table>
EOD;

$date = date_create("$invoice_date");
$this_date = date_format($date,"d-M-y");


if($SignatureID=="" || $SignatureID == NULL){
	$img_order = "";
	$pic_sign = "";
}
else{
	//$img_order =  '<img src="data:image/jfif/jpg/png;base64,'.base64_encode($signatureID).'" width="100px" height="100px"/>';
	$file = fopen("img/".$userID."_approve.jpg","w");
	$pic_sign = "img/".$userID."_approve.jpg";
	fwrite($file, $SignatureID);
}

$html .= <<<EOD
	&nbsp;<br/>
	<table>
	<tr>
		<td style="width:9%" >DATE: </td>
		<td style="width:2%" ></td>
		<td style="width:20%" class="bottom_border" >$this_date</td>
		<td style="width:5%"></td>
		</tr>
	<tr>
		<td colspan="4">
			&nbsp;<br/><br/><br/>
			&nbsp;<br/><br/><br/>
			</td>
		</tr>
	<tr>
		<td valign="bottom" style="vertical-align: bottom;">
			<br/><br/><br/>
			<br/><br/><br/>
			<br/><br/><br/>
			SIGNATURE: </td>
		<td ></td>
		<td class="bottom_border"><img src="$pic_sign" width="100px" height="100px" /></td>
		<td></td>
		</tr>
	<tr>
		<td>&nbsp;<br/>COMPANY ADDRESS: </td>
		<td ></td>
		<td colspan="2">&nbsp;<br/>$manufacturer<br/>$manuaddress<br/>TEL : $manutel &nbsp;&nbsp;&nbsp;&nbsp;FAX : $manufax</td>
		</tr>
		</table>
	<br/>&nbsp;<br/>
	This country will be identified in the above declaration by the alphabetical appearing next to the named Country.
	
EOD;
?>