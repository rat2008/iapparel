<?php 
$pdf->SetTitle("Joe Fresh Certification of Conformity");

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
td.all_border, th.all_border{
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
.lbl_title{
	
}
</style>

<table border="0" class="tb_bic">
	<tr>
		<th class="bold-text center-align">
			<h2>Loblaw / JFS Inc.</h2><br/><br/>
			<font class="lbl_title"><u><b>Joe Fresh Certification of Conformity</b></u></font>
			<br/>
			For U.S. Consumer Product Safety Improvement Act
		</th>
	</tr>
</table>

<br/>
<br/>

EOD;

$date = date_create("$shippeddate");
$this_date = date_format($date,"d-M-Y");

$html .= <<<EOD
	<table class="tb_bic">
	<tr>
		<td><b>ADULT WEARING APPAREL</b></td>
		<td style="width:30%">&nbsp;</td>
		<td style="width:12%" align="right">Date of Issue: &nbsp; </td>
		<td class="all_border" style="width:15%" align="center"> $this_date</td>
		</tr>
	<tr>
		<td colspan="4">
			The merchandise listed herein is in compliance with the following applicable rules, bans, standards and regulations<br/>
			enforced by the CPSC based on testing. (check only those which are applicable to the listed products):</td>
		</tr>
		</table>
	
	<br/>
	<br/>
	
	<table class="tb_bic">
	<tr>
		<td class="all_border" style="width:10%"></td>
		<td style="width:90%"> 16 CFR 1610 (Standard for the Flammability of Clothing Texiles</td>
		</tr>
	<tr>
		<td class="all_border" style="width:10%"></td>
		<td> 16 CFR 1610 (Standard for the Flammability of Clothing Texiles and are exempt from testing under the</td>
		</tr>
	<tr>
		<td colspan="2"> Flammable Fabrics Act Regulation, Section 1610.1 (d) as they are comprised of one or more of the following<br/>
			exempted texiles.</td>
		</tr>
		</table>
	
	<br/>
	<br/>
	
	<table class="tb_bic">
	<tr>
		<td style="width:8%"></td>
		<td style="width:92%">"(1) Plain surface fabrics, regardless of fiber content, weighing 2.6 ounces per square yard or more; and (2)<br/>
			All fabrics, both plain surface and raised-fiber surface, regardless of weight, made entirely from any of the <br/>
			following fibers or entirely from combination of the following fibers; acrylic, modacrylic, nylon, olefin,<br/>
			polyester, wool."</td>
		</tr>
		</table>
	<br/>
	<br/>
EOD;

$html .= <<<EOD
		<table class="tb_bic" cellpadding="3">
		<tr>
			<th class="all_border" align="center" colspan="2">Vendor Style#<br/>&<br/>Purchase Order#</th>
			<th class="all_border" align="center">Month and Year of Manufacture</th>
			<th class="all_border" align="center">Manufacturer<br/>Name and Place of <br/>Manufacture<br/>(city/country)</th>
			<th class="all_border" align="center">Country of origin</th>
			<th class="all_border" align="center">Date Tested</th>
			<th class="all_border" align="center">Test Report #</th>
			<th class="all_border" align="center">Place of<br/>Testing <br/>(City/Country)<br/>
				- Complete full details below*</th>
			</tr>
EOD;

$sql = "SELECT spp.SuppName_ENG as supplier, spp2.SuppName_ENG as supplier2, csa.test_date, csa.test_no, sp.GTN_buyerpo as buyerpo,
				fty.FactoryName_ENG as factory, c.Description as country, sp.ID, fty.state, csa.manufacturer_date,
				spp.Email as email1, spp.Address as address1, spp.Tel as tel1,
				spp2.Email as email2, spp2.Address as address2, spp2.Tel as tel2
		FROM tblshipmentprice sp 
		INNER JOIN tblgarment g ON g.orderno = sp.Orderno
		INNER JOIN tblcolorsizeattach csa ON csa.garmentID = g.garmentID
		INNER JOIN tblsupplier spp ON spp.SupplierID = csa.supplierID
		INNER JOIN tblsupplier spp2 ON spp2.SupplierID = csa.supplierID2
		INNER JOIN tblorder od ON od.Orderno = sp.Orderno
		INNER JOIN tblfactory fty ON fty.FactoryID = od.FactoryID
		INNER JOIN tblcountry c ON c.ID = fty.countryID
		WHERE sp.ID IN ($grp_spID)
		group by sp.ID
		order by csa.manufacturer_date asc";
$stmt = $conn->prepare($sql);
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	extract($row);
	
	$sqlsp = "SELECT group_concat(distinct g.styleNo) as style 
				FROM tblship_colorsizeqty scsq 
				INNER JOIN tblgarment g ON g.garmentID = scsq.garmentID
				WHERE scsq.shipmentpriceID = '$ID' AND scsq.statusID='1' AND scsq.qty>0
				";
	$stmtsp = $conn->prepare($sqlsp);
	$stmtsp->execute();
	$rowsp = $stmtsp->fetch(PDO::FETCH_ASSOC);
		$style = $rowsp["style"];
	
	$html .= <<<EOD
		<tr>
			<td align="center" class="left_border">$buyerpo</td>
			<td align="center" class="right_border">$style</td>
			<td align="center" class="left_border right_border">$manufacturer_date</td>
			<td align="center" class="left_border right_border"><font style="font-size:8px">$factory <br/>($state/$country)</font></td>
			<td align="center" class="left_border right_border">$country</td>
			<td align="center" class="left_border right_border">$test_date</td>
			<td align="center" class="left_border right_border">$test_no</td>
			<td align="center" class="left_border right_border"></td>
			</tr>
EOD;
}

// $arr_addr = explode("Ein:", $conAddrOnly);


$html .= <<<EOD
		<tr>
			<td align="center" class="left_border bottom_border"></td>
			<td align="center" class="right_border bottom_border"></td>
			<td align="center" class="left_border right_border bottom_border"></td>
			<td align="center" class="left_border right_border bottom_border"></td>
			<td align="center" class="left_border right_border bottom_border"></td>
			<td align="center" class="left_border right_border bottom_border"></td>
			<td align="center" class="left_border right_border bottom_border"></td>
			<td align="center" class="left_border right_border bottom_border"></td>
			</tr>
		</table>
		
		<br/>
		<br/>
		
		<table class="tb_bic" >
		<tr>
			<td style="width:50%">Importer Name: </td>
			<td style="width:50%" class="bottom_border">$conName</td>
			</tr>
		<tr>
			<td >Full Mailing Address:</td>
			<td class="bottom_border">$conAddrOnly</td>
			</tr>
		<tr>
			<td >Tel:</td>
			<td class="bottom_border">$contel</td>
			</tr>
		<tr>
			<td >Email:</td>
			<td class="bottom_border">$conemail</td>
			</tr>
		<tr>
			<td >Name of 3rd Party Testing Lab*:</td>
			<td class="bottom_border">$supplier</td>
			</tr>
		<tr>
			<td >Full Mailing Address:</td>
			<td class="bottom_border">$address1</td>
			</tr>
		<tr>
			<td ></td>
			<td class="bottom_border"></td>
			</tr>
		<tr>
			<td >Phone Number:</td>
			<td class="bottom_border">$tel1</td>
			</tr>
		<tr>
			<td >Name of Individual Maintaining Test Results:</td>
			<td class="bottom_border">$supplier2</td>
			</tr>
		<tr>
			<td >Email:</td>
			<td class="bottom_border">$email2</td>
			</tr>
		<tr>
			<td >Full Mailing Address:</td>
			<td class="bottom_border">$address2</td>
			</tr>
		<tr>
			<td >Phone Number:</td>
			<td class="bottom_border">$tel2</td>
			</tr>
			</table>
EOD;

?>