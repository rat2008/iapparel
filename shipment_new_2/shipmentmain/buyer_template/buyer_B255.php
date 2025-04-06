<?php 
$html = <<<EOD
<style>
	.font_size{
		font-size:10px;
	}
	.font_size_info{
		font-size:8px;
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

$html .= <<<EOD
<table cellpadding="1" class="font_size">
	<tr>
		<td rowspan="2" width="8%">
			<img src="buyer_template/canada_flag.png" height="40" />
		</td>
		<td width="1%"></td>
		<td width="16%">Canada Customs<br/>and Revenue Agency</td>
		<td width="60%">Agence des douanes<br/>et du revenu du Canada</td>
		<td width="15%"><img src="buyer_template/icon001.jpg" height="40" /></td>
	</tr>
</table>
<br/>
<br/>

<table  class="font_size">
	<tr>
		<td align="center">
			<h4>CERTIFICATE OF ORIGIN</h4></td>
		</tr>
	<tr>
		<td align="center"><h4>Textile and Apparel Goods Originating in a Least Developed Country</h4></td>
		</tr>
	<tr>
		<td align="center"><h4>(Instructions attached)</h4></td>
		</tr>
		</table>
EOD;

$transitPort = (trim($transitPort)==""? "": "VIA $transitPort");
$d = date_create($shippeddate);
$shipdate = date_format($d, "d-M-Y");

$d = date_create($exfactorydate);
$exfactory_date = date_format($d, "d-M-Y");
$conAddrOnly = strtoupper($conAddrOnly);

$html .= <<<EOD
	<div class="font_size_info">Please print or type</div>
	<table cellpadding="2" class="font_size_info">
	<tr>
		<td width="50%" class="border_left border_right border_top"><b>1. Exporter's business name, address and country</b></td>
		<td width="50%" class="border_left border_right border_top"><b>2. Business name and address of importer in Canada</b></td>
		</tr>
	<tr>
		<td class="border_left border_right border_btm">
				<div style="padding:5px;" class="font_times">$shipper<br/>$shipper_addr<br/>TEL: $shipper_tel &nbsp; &nbsp; FAX: $shipper_fax</div></td>
		<td class="border_left border_right border_btm">
				<div style="padding:5px" class="font_times">$conName<br/>$conAddrOnly<br/>TEL: $contel &nbsp; &nbsp; FAX: $confax</div></td>
		</tr>
	<tr>
		<td colspan="2" class="border_left border_right"><b>3. Means of transport and route (if known)</b>
				<br/>
				<div class="font_times">
				BY $shipmode FROM $portLoading $transitPort <br/>TO $buyerdest
				<br/>
				<br/>
				SHIPPED ON: $shipdate
				</div>
				<br/>
			</td>
		</tr>
		</table>
EOD;

$html .= <<<EOD
	<table cellpadding="1" class="font_size_info">
	<tr>
		<td class="border_left border_right border_btm border_top" width="14%"> <b>4. Markings and number of packages</b></td>
		<td class="border_right border_btm border_top" colspan="4" width="52%"> <b>5. Description of goods As Apparel</b></td>
		<td class="border_right border_btm border_top" width="14%"> <b>6. Preference crit erion</b></td>
		<td class="border_right border_btm border_top" width="20%"> <b>7. Number and date of invoices</b></td>
		</tr>
		
EOD;

$query_filter = "";
$arr_array   = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_buyerpo = $arr_array["byBuyerPO"];
$count_row   = 1;
$grand_ctn   = 0;
$all_qty     = 0;
$fab_country = "";

$count_color = 0;
$count_po    = 0;

	foreach($arr_buyerpo as $key => $arr_info){
		$arr_info_row  = $arr_info["arr_info"];
		$total_ctn     = $arr_info["total_ctn"];
		$grand_qty     = $arr_info["grand_qty"];
		$uom           = $arr_info["uom"];
		$fab_country   = $arr_info["fab_country"];
		$count_color   = count($arr_info["arr_info"]);
		
		$grand_ctn += $total_ctn;
		$all_qty += $grand_qty;
		$count_row += 3;
		foreach($arr_info_row as $prepack_key => $arr_value){
			$count_row++;
		}
	}
$html .= <<<EOD
	<tr>
		<td rowspan="$count_row" class="left_border right_border" >
			<br/>
			<br/>
			<div class="font_times">AS PER <br/>COMMERCIAL <br/>INVOICE</div>
			</td>
		<td colspan="4" class=" right_border"> <b>$grand_ctn CTNS</b></td>
		<td rowspan="$count_row" align="center" class=" right_border">
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				"E"</td>
		<td rowspan="$count_row" class=" right_border">
			<br/>
			<br/>
			<br/>
			<br/>
			<table cellpadding="2">
			<tr><td>
				INVOICE:<br/>
				$invoice_no<br/>
				DATE:<br/>
				$shipdate</td>
			</tr></table>
		</td>
		</tr>
EOD;

foreach($arr_buyerpo as $key => $arr_info){
	$arr_info_row  = $arr_info["arr_info"];
	$ship_marking  = $arr_info["ship_marking"];
	$styleNo       = $arr_info["styleNo"];
	$quotacat      = $arr_info["quotacat"];
	
	$count_po++;

$html.= <<<EOD
	<tr>
		<td colspan="4" class=" right_border" > $ship_marking</td>
		<!--<td class="left_border right_border" ></td>-->
		</tr>
	<tr>
		<td class="" width="13%"> P.O NUMBER</td>
		<td width="13%">STYLE #</td>
		<td width="13%">NG ITEM#</td>
		<td class="right_border" width="13%">COLOUR</td>
		<!-- <td class="left_border right_border"></td>-->
		</tr>
	
EOD;

$this_po    = "$key";
$this_style = "$styleNo";

foreach($arr_info_row as $prepack_key => $arr_value){
	list($prepack_name, $group_number) = explode("**^^", $prepack_key);
	$colorOnly     = $arr_value["colorOnly"];
	$total_ctn_qty = $arr_value["qty"];
	$fob_price     = $arr_value["fob_price"];
	$this_po       = (trim($this_po)!=""? "$this_po": "");
	$this_style    = (trim($this_style)!=""? "$this_style": "");
	
	$count_color++;

$html .= <<<EOD
	<tr>
		<td class=""> $this_po</td>
		<td>$this_style</td>
		<td>$prepack_name</td>
		<td class="right_border">$colorOnly</td>
		<!--<td class="left_border right_border"></td>-->
		</tr>
EOD;

$this_po    = "";
$this_style = "";
	
	
}//--- end foreach po color level ---//
	
$html .= <<<EOD
	<tr>
		<td colspan="2" class=""> CATEGORY NO: $quotacat</td>
		<td></td>
		<td class="right_border"></td>
		<!--<td class="left_border right_border"></td>-->
		</tr>
EOD;

}//--- End foreach buyer po ---//

$word_qty      = strtoupper($handle_finance->convert_number($all_qty)." $uom ONLY");
$exporter_sign = strtoupper($exporter_sign);
$exporter_pos  = strtoupper($exporter_pos);
$exporter_fax  = (trim($exporter_fax)==""? "":"FAX: $exporter_fax");

$str_qty = number_format($all_qty);
$html .= <<<EOD
	<tr>
		<td class="left_border right_border border_btm"></td>
		<td colspan="4" class=" right_border border_btm"><h4> QTY: $str_qty $uom</h4><br/>
				&nbsp;Fabric Origin: $fab_country<br/>
				&nbsp;Yarn Origin: $fab_country
				<h4> TOTAL: $word_qty</h4>
				</td>
		<td class=" right_border border_btm"></td>
		<td class=" right_border border_btm"></td>
		</tr>
	</table>
EOD;

$html .= <<<EOD
	<br/>
EOD;

if((($count_color>16 && $count_po>10) || (($count_color>29 && $count_po>4))) && ($count_color<75 && $count_po<21)){
	$html .= ' <br pagebreak="true">';
}

$html .= <<<EOD
	<table cellpadding="2" class="font_size_info">
	<tr>
		<td colspan="2"></td>
		</tr>
	<tr>
		<td colspan="2" class="all_border">8. "As the exporter, I hereby declare that the above details and statements are correct, namely:<br/>
			<br/>
			<table>
			<tr>
				<td width="5%"></td>
				<td width="20%">1. all the goods were produced in </td>
				<td class="bottom_border" align="center"><div class="font_times">KINGDOM OF CAMBODIA</div></td>
				<td>; and</td>
				</tr>
			<tr>
				<td></td>
				<td></td>
				<td  align="center">(name of country)</td>
				<td></td>
				</tr>
				</table>
			<br/>
			<table>
			<tr>
				<td width="5%"></td>
				<td width="95%">2. the goods comply with the requirements specified for those goods in the <i>General Preferential Tariff and Least Developed Country Tariff Rules of Origin Regulations.</i>"</td>
				</tr>
				</table>
			</td>
		</tr>
	<tr>
		<td class="left_border right_border">Name:</td>
		<td class="left_border right_border">Position in the company:</td>
		</tr>
	<tr>
		<td class="left_border right_border bottom_border" align="center"><div class="font_times">$exporter_sign</div></td>
		<td class="left_border right_border bottom_border" align="center"><div class="font_times">$exporter_pos</div></td>
		</tr>
	<tr>
		<td class="left_border right_border">Telephone Number:</td>
		<td class="left_border right_border">Facsimile Number:</td>
		</tr>
	<tr>
		<td class="left_border right_border bottom_border" align="center"><div class="font_times">$exporter_tel</div></td>
		<td class="left_border right_border bottom_border" align="center"><div class="font_times">$exporter_fax</div></td>
		</tr>
	<tr>
		<td class="all_border" colspan="2" style="padding:5px" align="center">
			<table cellpadding="3" align="center">
			<tr>
				<td width="5%"></td>
				<td class="bottom_border" width="45%">&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;</td>
				<td class="bottom_border" width="45%">&nbsp; <br/> <div class="font_times">$shipdate</div></td>
				<td width="5%"></td>
				</tr>
			<tr>
				<td ></td>
				<td align="center">Signature</td>
				<td align="center">Date (yyyy/mm/dd)</td>
				<td ></td>
				</tr>
				</table>
		</td>
		</tr>
		</table>
	
EOD;
?>