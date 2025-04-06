<?php 
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);
include('../../../lock.php');
include("../../../phpexcel/Classes/PHPExcel.php");

if(empty($_GET["id"]) || !isset($_GET['id'])){
	header("Location: buyer_inv_list.php");
}
$invID = $_GET["id"];
if(isset($_GET["displayBuyer"])){
	$displayBuyer = $_GET["displayBuyer"];
}

$sheet = new PHPExcel();
$activeSheet = $sheet->getActiveSheet();
$activeSheet->setTitle("Invoice");

$isBuyerPayment = (isset($_GET["isBuyerPayment"])? $_GET["isBuyerPayment"]: 0);

if($isBuyerPayment==0){
	$tblbuyer_invoice = "tblbuyer_invoice";
	$tblbuyer_invoice_detail = "tblbuyer_invoice_detail";
}
else{
	$tblbuyer_invoice = "tblbuyer_invoice_payment";
	$tblbuyer_invoice_detail = "tblbuyer_invoice_payment_detail";
}

$invsql = $conn->prepare("
		SELECT inv.*, sm.Description as shipmode, lp.Description as portLoading, lp2.Description as portReceive,
			ifnull(tt.Description,'-') as tradeterm, ifnull(pt.Description,'-') as paymentterm, pt.Day as payterm_day,
			DATE(inv.exfactory) as exfactorydate, lch.lc_number, lch.lc_bank, lch.LCHID, lci.lc_type,
			lci.lc_date, cur.CurrencyCode, cur.Description as cur_description, 
			cp.CompanyName_ENG as ownership, cp.Address as owneraddress, cp.Tel as ownertel, cp.Fax as ownerfax, cp.taxID,
			ftym.FactoryName_ENG as manufacturer, ftym.Address as manuaddress, ftym.Tel as manutel, ftym.Fax as manufax,
			cty.Description as manucountry, csn.Name as csn_name, csn.Address as csn_address, od.FactoryID, csn.ConsigneeID, bdt.Description as buyerdestination, dp.countryID as destport_countryID, dp.Description as portDischarges,
			group_concat(distinct sp.Orderno) as orderno, 
			crmb.bank_account_no, crmb.beneficiary_name, crmb.bank_name, crmb.bank_address, crmb.swift_code,
			ct.description as country, ct.countryCode, group_concat(distinct g.styleNo) as style_no, 
			ss.Description as season, bd.Description as brand, pyr.Description as bill_to, pyr.address as bill_address,
			group_concat(distinct sp.ID) as grp_shipmentpriceID, pis.Description as poissuer, pis.address as poissuer_address,
			group_concat(distinct sp.GTN_buyerpo separator ', ') as allBuyerPO, 
			group_concat(distinct sp.BuyerPO separator ', ') as allBuyerPO_notori, 
			fty_shipper.FactoryName_ENG as shipper, fty_shipper.Tel as shipper_tel, fty_shipper.Fax as shipper_fax, fty_shipper.Address as shipaddr, bnp.NotifyName, bnp.NotifyAddress, bnp.tel as notifyTel, bnp.fax as notifyFax, bnp.email as notifyEmail, cbd.description as buyerdest_country, cbd.countryCode as byrdt_countrycode, cts.description as transitPort, fty_shipper.MID,
			uat.UserFullName as exporter_sign, uat.SignatureID, uat.AcctID as userID, uat.Tel as exporter_tel, uat.Fax as exporter_fax,
			uat.position as exporter_pos,csn.EIN
		FROM $tblbuyer_invoice inv 
		LEFT JOIN tblshipmode sm ON sm.ID = inv.shipmodeID 
		LEFT JOIN tblloadingport lp ON lp.ID = inv.portLoadingID 
		LEFT JOIN tblloadingport lp2 ON lp2.ID = inv.porID 
		LEFT JOIN tbltradeterm tt ON tt.ID = inv.tradeTermID 
		LEFT JOIN tblpaymentterm pt ON pt.ID = inv.paymentTermID 
		LEFT JOIN $tblbuyer_invoice_detail invd ON invd.invID = inv.ID AND invd.del = 0 AND invd.group_number > 0
		LEFT JOIN tbllc_assignment_detail lcd ON lcd.shipmentpriceID = invd.shipmentpriceID AND lcd.del=0 AND invd.del=0
		LEFT JOIN tbllc_assignment_info lci ON lci.LCIID = lcd.LCIID AND lci.del=0 
		LEFT JOIN tbllc_assignment_head lch ON lch.LCHID = lci.LCHID
		LEFT JOIN tblshipmentprice sp ON invd.shipmentpriceID = sp.ID
		LEFT JOIN tblgarment g ON find_in_set(g.garmentID, sp.StyleNo)
		LEFT JOIN tblorder od ON od.Orderno = sp.Orderno
		LEFT JOIN tblseason ss ON ss.ID = od.seasonID
		LEFT JOIN tblbrand bd ON bd.ID = od.brandID
		LEFT JOIN tblcurrency cur ON cur.ID = od.currencyID
		LEFT JOIN tblcompanyprofile cp ON cp.ID = inv.issue_from
        LEFT JOIN tblfactory ftym ON ftym.FactoryID = od.manufacturer
        LEFT JOIN tblfactory fty_shipper ON fty_shipper.FactoryID = inv.shipper
        LEFT JOIN tblcountry cty ON cty.ID = ftym.countryID
		LEFT JOIN tblconsignee csn ON find_in_set(csn.ConsigneeID, inv.ConsigneeID)
		LEFT JOIN tblpayer pyr ON pyr.ID = inv.built_to
		LEFT JOIN tblpoissuer pis ON pis.ID = inv.poissuer
		LEFT JOIN tblbuyerdestination bdt ON bdt.ID = inv.BuyerDestID
		LEFT JOIN tbldestinationport dp ON dp.ID = inv.PortDestID
		LEFT JOIN tblcrm_bank crmb ON crmb.BID = inv.BID
		LEFT JOIN tblcountry ct ON ct.ID = crmb.countryID
		LEFT JOIN tblcountry cbd ON cbd.ID = bdt.countryID
		LEFT JOIN tblcountry cts ON cts.ID = inv.transitPortID	
		LEFT JOIN tblbuyer_notify_party bnp ON bnp.notifyID = inv.notifyID
		LEFT JOIN tbluseraccount uat ON uat.AcctID = inv.export_by
		WHERE inv.ID='$invID' 
	");
$invsql->execute();
$invrow = $invsql->fetch(PDO::FETCH_ASSOC);

$exporter_sign = $invrow["exporter_sign"];
$exporter_tel  = $invrow["exporter_tel"];
$exporter_fax  = $invrow["exporter_fax"];
$exporter_pos  = $invrow["exporter_pos"];
$SignatureID   = $invrow["SignatureID"];
$userID        = $invrow["userID"];

$BuyerID       = $invrow["BuyerID"];
$ConsigneeID   = $invrow["ConsigneeID"];
$byr_invoice_no = $invrow["byr_invoice_no"];
$invoice_no    = $invrow["invoice_no"];
$invoice_date  = $invrow["invoice_date"];
$portLoadingID = $invrow["portLoadingID"];
$remarks       = $invrow["remarks"];
$lc_number     = $invrow["lc_number"];
$lc_bank       = $invrow["lc_bank"];
$lc_date       = $invrow["lc_date"];
$lc_type       = $invrow["lc_type"];
$LCHID         = $invrow["LCHID"];

$shipper        = $invrow["shipper"];
$shipper_addr   = $invrow["shipaddr"];
$shipper_tel    = $invrow["shipper_tel"];
$shipper_fax    = $invrow["shipper_fax"];
$shipper_MID    = $invrow["MID"];

$notify_party   = $invrow["NotifyName"];
$notify_address = $invrow["NotifyAddress"];
$notifyTel      = $invrow["notifyTel"];
$notifyFax      = $invrow["notifyFax"];
$notifyEmail    = $invrow["notifyEmail"];

$ship_to       = $invrow["ship_to"];
$ship_address  = $invrow["ship_address"];
$shipmode      = $invrow["shipmode"];
$shippeddate   = $invrow["shippeddate"];
$ETA           = $invrow["ETA"];
$container_no  = $invrow["container_no"];
$seal_no       = $invrow["seal_no"];
$carrier       = $invrow["carrier"];

$grp_spID      = $invrow["grp_shipmentpriceID"];
$style_no      = $invrow["style_no"];
$season        = $invrow["season"];
$brand         = $invrow["brand"];
$allBuyerPO_notori = $invrow["allBuyerPO_notori"];
$allBuyerPO    = $invrow["allBuyerPO"];
$allBuyerPO    = ($allBuyerPO==""? $allBuyerPO_notori: $allBuyerPO);
$portLoading   = $invrow["portLoading"];
$portReceive   = $invrow["portReceive"];
$tradeterm     = $invrow["tradeterm"];
$paymentterm   = $invrow["paymentterm"];
$payterm_day   = $invrow["payterm_day"];

$buyerdest      = $invrow["buyerdestination"];
$byrdt_country  = $invrow["buyerdest_country"];
$byrdt_ctrcode  = $invrow["byrdt_countrycode"];
$portDischarges = $invrow["portDischarges"];
$transitPort    = $invrow["transitPort"];
$vesselname     = $invrow["vesselname"];
$ein_no			= $invrow["EIN"];

$exfactorydate = $invrow["exfactorydate"];
$taxID         = $invrow["taxID"];
$ownership     = $invrow["ownership"];
$owneraddress  = $invrow["owneraddress"];
$ownertel      = $invrow["ownertel"];
$ownerfax      = $invrow["ownerfax"];
$manufacturer  = $invrow["manufacturer"];
$manuaddress   = $invrow["manuaddress"];
$manutel       = $invrow["manutel"];
$manufax       = $invrow["manufax"];
$manucountry   = $invrow["manucountry"];
$csn_name      = $invrow["csn_name"];
$csn_address   = $invrow["csn_address"];
$od_FactoryID  = $invrow["FactoryID"]; 
$orderno       = $invrow["orderno"];

$bill_to       = $invrow["bill_to"];
$bill_address  = $invrow["bill_address"];
$poissuer          = $invrow["poissuer"];
$poissuer_address  = $invrow["poissuer_address"];
$po_date           = $invrow['poissue_date'];

$bank_account_no  = $invrow["bank_account_no"];
$beneficiary_name = $invrow["beneficiary_name"];
$bank_name        = $invrow["bank_name"];
$bank_address     = $invrow["bank_address"];
$swift_code       = $invrow["swift_code"];
$bank_country     = $invrow["country"];
$destport_countryID   = $invrow["destport_countryID"];
$CurrencyCode    = $invrow["CurrencyCode"];
$cur_description = $invrow["cur_description"];

$sqlCharge = "SELECT sum(total_amount) as other_charge 
				FROM $tblbuyer_invoice_detail 
				WHERE invID='$invID' AND del=0 AND group_number=0";
$stmt = $conn->prepare($sqlCharge);
$stmt->execute();
$chargerow  = $stmt->fetch(PDO::FETCH_ASSOC);
$other_charge = $chargerow["other_charge"];

if($lc_type>0){
	$sqllc = "SELECT lci.lc_date
				FROM tbllc_assignment_info lci 
				WHERE lci.LCHID='$LCHID' AND lci.lc_type='0' AND lci.del='0'";
	$stmt_lc = $conn->prepare($sqllc);
	$stmt_lc->execute();
	$row_lc  = $stmt_lc->fetch(PDO::FETCH_ASSOC);
	$lc_date = $row_lc["lc_date"];
}


//======================================= Consignee detail  =================================//
$consql = $conn->prepare("SELECT con.*, c.Description as countryName, bd.Description as brand 
							FROM tblconsignee con 
							LEFT JOIN tblcountry c ON c.ID=con.countryID 
							LEFT JOIN tblbrand bd ON bd.ID = con.brandID
							WHERE con.ConsigneeID='$ConsigneeID' ");
$consql->execute();
$conrow = $consql->fetch(PDO::FETCH_ASSOC);
$brand       = $conrow["brand"];
$conName     = $conrow["Name"]."";
$conAddrOnly = $conrow["Address"];
$conEIN      = $conrow["EIN"];
$contel      = $conrow["tel"];
$confax      = $conrow["fax"];
$conemail    = $conrow["email"];
$concontact  = $conrow["contactperson"];
$conAddress  = str_replace("Tel", "<br>Tel", $conAddrOnly);
$countryName = $conrow["countryName"];
$countryID   = $conrow["countryID"];

//======================================= buyer detail  =================================//
$buyersql = $conn->prepare("SELECT b.* FROM tblbuyer b WHERE b.BuyerID='$BuyerID' ");
$buyersql->execute();
$buyerrow  = $buyersql->fetch(PDO::FETCH_ASSOC);
$BuyerName = $buyerrow["BuyerName_Eng"];
$BuyerAddr = $buyerrow["Address"];


$letterhead_name    = ($isBuyerPayment==1? "$ownership":"$shipper");
$letterhead_address = ($isBuyerPayment==1? "$owneraddress":"$shipper_addr");
$letterhead_tel     = ($isBuyerPayment==1? "$ownertel":"$shipper_tel");
$letterhead_fax     = ($isBuyerPayment==1? "$ownerfax":"$shipper_fax");
$letterhead_title   = ($isBuyerPayment==1? "Buyer Payment Invoice":"Commercial Invoice");
$letterhead_title   = "Commercial Invoice";

$ship_address2=nl2br($ship_address);
$bill_address2=nl2br($bill_address);
$manuaddress2=nl2br($manuaddress);
$owneraddress2=nl2br($owneraddress);

$style_BOutline = array(
    'borders' => array(
        'outline' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN, // You can use other styles such as BORDER_THIN, BORDER_DASHED, etc.
            'color' => array('argb' => 'FF000000'), // Black color
        ),
    ),
);


$style_BBtm_bold = array(
    'borders' => array(
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THICK, // You can use other styles such as BORDER_THIN, BORDER_DASHED, etc.
            'color' => array('argb' => 'FF000000'), // Black color
        ),
    ),
);

$arrCol=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
				'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
				'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
				'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');

$sheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$sheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$sheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$sheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$sheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$sheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$sheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$sheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$sheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$sheet->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$sheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$sheet->getActiveSheet()->getColumnDimension('L')->setWidth(30);

$activeSheet->getCell('A2')->setValue("C O M M E R I A L   I N V O I C E");
$activeSheet->getStyle('A2')->getFont()->setSize(16);
$activeSheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$activeSheet->mergeCells("A2:L2");

$row_num=3;

//SUPPLIER BOX
$activeSheet->getCell('A'.$row_num)->setValue("SUPPLIER:");
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("A".$row_num.":E".$row_num);

$activeSheet->getCell('A'.($row_num+1))->setValue($ownership);
$activeSheet->mergeCells("A".($row_num+1).":E".($row_num+1));

$arr_owneraddr=explode("<br />", $owneraddress2);
for ($i=0; $i < sizeof($arr_owneraddr); $i++) { 
	$activeSheet->getCell('A'.($row_num+2+$i))->setValue($arr_owneraddr[$i]);
	$activeSheet->mergeCells("A".($row_num+2+$i).":E".($row_num+2+$i));
}

//MANFACTURER BOX
$activeSheet->getCell('F'.$row_num)->setValue("MANUFACTURER:");
$activeSheet->getStyle('F'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("F".$row_num.":J".$row_num);

$activeSheet->getCell('F'.($row_num+1))->setValue($manufacturer);
$activeSheet->mergeCells("F".($row_num+1).":J".($row_num+1));

$arr_manuaddr=explode("<br />", $manuaddress2);
for ($i=0; $i < sizeof($arr_manuaddr); $i++) { 
	$activeSheet->getCell('F'.($row_num+2+$i))->setValue($arr_manuaddr[$i]);
	$activeSheet->mergeCells("F".($row_num+2+$i).":J".($row_num+2+$i));
}

if(sizeof($arr_owneraddr)>sizeof($arr_manuaddr)){
	$start_row_num=$row_num;

	$row_num+=1; //+1 row for name row
	$row_num+=sizeof($arr_owneraddr);
}else{
	$start_row_num=$row_num;

	$row_num+=1; //+1 row for name row
	$row_num+=sizeof($arr_manuaddr);
}

$activeSheet->getStyle('A'.$start_row_num.':E'.$row_num)->applyFromArray($style_BOutline);
$activeSheet->getStyle('F'.$start_row_num.':J'.$row_num)->applyFromArray($style_BOutline);

$row_num+=1;

//SHIP TO BOX
$activeSheet->getCell('A'.$row_num)->setValue("SHIP TO:");
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("A".$row_num.":E".$row_num);

$activeSheet->getCell('A'.($row_num+1))->setValue($ship_to);
$activeSheet->mergeCells("A".($row_num+1).":E".($row_num+1));

$arr_shipaddr=explode("<br />", $ship_address2);
for ($i=0; $i < sizeof($arr_shipaddr); $i++) { 
	$activeSheet->getCell('A'.($row_num+2+$i))->setValue($arr_shipaddr[$i]);
	$activeSheet->mergeCells("A".($row_num+2+$i).":E".($row_num+2+$i));
}

//BILL TO BOX
$activeSheet->getCell('F'.$row_num)->setValue("BILL TO:");
$activeSheet->getStyle('F'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("F".$row_num.":J".$row_num);

$activeSheet->getCell('F'.($row_num+1))->setValue($bill_to);
$activeSheet->mergeCells("F".($row_num+1).":J".($row_num+1));

$arr_billaddr=explode("<br />", $bill_address2);
for ($i=0; $i < sizeof($arr_billaddr); $i++) { 
	$activeSheet->getCell('F'.($row_num+2+$i))->setValue($arr_billaddr[$i]);
	$activeSheet->mergeCells("F".($row_num+2+$i).":J".($row_num+2+$i));
}

if(sizeof($arr_shipaddr)>sizeof($arr_billaddr)){
	$start_row_num=$row_num;

	$row_num+=1; //+1 row for name row
	$row_num+=sizeof($arr_shipaddr);
}else{
	$start_row_num=$row_num;

	$row_num+=1; //+1 row for name row
	$row_num+=sizeof($arr_billaddr);
}

$activeSheet->getStyle('A'.$start_row_num.':E'.$row_num)->applyFromArray($style_BOutline);
$activeSheet->getStyle('F'.$start_row_num.':J'.$row_num)->applyFromArray($style_BOutline);

$row_num+=1;

//info at the right side
$row_num_right=3;

$activeSheet->getCell('K'.$row_num_right)->setValue("INVOICE NO:");
$activeSheet->getCell('L'.$row_num_right)->setValue($invoice_no);
$row_num_right++;

$activeSheet->getCell('K'.$row_num_right)->setValue("DATE:");
$activeSheet->getCell('L'.$row_num_right)->setValue($invoice_date);
$row_num_right++;

$activeSheet->getCell('K'.$row_num_right)->setValue("SEASON:");
$activeSheet->getCell('L'.$row_num_right)->setValue($season);
$row_num_right++;

$activeSheet->getCell('K'.$row_num_right)->setValue("PO NO:");
$activeSheet->getCell('L'.$row_num_right)->setValue($allBuyerPO);
$row_num_right++;

$activeSheet->getCell('K'.$row_num_right)->setValue("ASN NO:");
$activeSheet->getCell('L'.$row_num_right)->setValue("");
$row_num_right++;

$activeSheet->getCell('K'.$row_num_right)->setValue("SHIP MODE:");
$activeSheet->getCell('L'.$row_num_right)->setValue($shipmode);
$row_num_right++;

$activeSheet->getCell('K'.$row_num_right)->setValue("SHIPMENT TERM:");
$activeSheet->getCell('L'.$row_num_right)->setValue($tradeterm);
$row_num_right++;

$activeSheet->getCell('K'.$row_num_right)->setValue("MADE IN:");
$activeSheet->getCell('L'.$row_num_right)->setValue($manucountry);
$row_num_right++;

if($row_num_right>$row_num){
	$row_num=$row_num_right;
}


$row_num+=1;

$activeSheet->getCell('A'.$row_num)->setValue("REFERENCE");
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->getStyle('A'.$row_num.':B'.$row_num)->applyFromArray($style_BOutline);
$activeSheet->mergeCells("A".$row_num.":B".$row_num);

$activeSheet->getCell('C'.$row_num)->setValue("COLOR");
$activeSheet->getStyle('C'.$row_num)->getFont()->setBold(true);
$activeSheet->getStyle('C'.$row_num.':D'.$row_num)->applyFromArray($style_BOutline);
$activeSheet->mergeCells("C".$row_num.":D".$row_num);

$activeSheet->getCell('E'.$row_num)->setValue("DESCRIPTION");
$activeSheet->getStyle('E'.$row_num)->getFont()->setBold(true);
$activeSheet->getStyle('E'.$row_num.':F'.$row_num)->applyFromArray($style_BOutline);
$activeSheet->mergeCells("E".$row_num.":F".$row_num);

$activeSheet->getCell('G'.$row_num)->setValue("COMPOSITION");
$activeSheet->getStyle('G'.$row_num)->getFont()->setBold(true);
$activeSheet->getStyle('G'.$row_num.':H'.$row_num)->applyFromArray($style_BOutline);
$activeSheet->mergeCells("G".$row_num.":H".$row_num);

$activeSheet->getCell('I'.$row_num)->setValue("QUANTITY (PCS)");
$activeSheet->getStyle('I'.$row_num)->getFont()->setBold(true);
$activeSheet->getStyle('I'.$row_num.':J'.$row_num)->applyFromArray($style_BOutline);
$activeSheet->mergeCells("I".$row_num.":J".$row_num);

$activeSheet->getCell('K'.$row_num)->setValue("UNIT PRICE (USD)");
$activeSheet->getStyle('K'.$row_num)->getFont()->setBold(true);
$activeSheet->getStyle('K'.$row_num)->applyFromArray($style_BOutline);

$activeSheet->getCell('L'.$row_num)->setValue("TOTAL AMOUNT (USD)");
$activeSheet->getStyle('L'.$row_num)->getFont()->setBold(true);
$activeSheet->getStyle('L'.$row_num)->applyFromArray($style_BOutline);

$row_num++;

$sel_detail=$conn->prepare("SELECT g.styleNo,c.ColorName,bid.shipping_marking,g.StyleDescription,bid.fob_price,bid.qty,bid.total_amount
		FROM tblbuyer_invoice_payment_detail bid 
		JOIN tblbuyer_invoice_payment bi ON bid.invID=bi.ID
		LEFT JOIN tblship_group_color sgc ON bid.group_number=sgc.group_number AND bid.shipmentpriceID=sgc.shipmentpriceID AND sgc.statusID='1'
		LEFT JOIN tblcolor c ON sgc.colorID=c.ID
	    LEFT JOIN tblgarment g ON sgc.garmentID=g.garmentID
		LEFT JOIN tblshipmentprice sp ON sp.ID=bid.shipmentpriceID
		WHERE bid.invID='$invID' AND bid.del='0' AND bid.qty>0 AND bid.group_number>0
		GROUP BY bid.ID
	");
$sel_detail->execute();

$grand_qty=0;
$grand_amt=0;
while($row_detail=$sel_detail->fetch(PDO::FETCH_ASSOC)){
		extract($row_detail);

		$activeSheet->getCell('A'.$row_num)->setValue(htmlspecialchars_decode($styleNo,ENT_QUOTES));
		$activeSheet->getStyle('A'.$row_num.':B'.$row_num)->applyFromArray($style_BOutline);
		$activeSheet->mergeCells("A".$row_num.":B".$row_num);

		$activeSheet->getCell('C'.$row_num)->setValue(htmlspecialchars_decode($ColorName,ENT_QUOTES));
		$activeSheet->getStyle('C'.$row_num.':D'.$row_num)->applyFromArray($style_BOutline);
		$activeSheet->mergeCells("C".$row_num.":D".$row_num);

		$activeSheet->getCell('E'.$row_num)->setValue(htmlspecialchars_decode($StyleDescription,ENT_QUOTES));
		$activeSheet->getStyle('E'.$row_num.':F'.$row_num)->applyFromArray($style_BOutline);
		$activeSheet->mergeCells("E".$row_num.":F".$row_num);

		$activeSheet->getCell('G'.$row_num)->setValue(htmlspecialchars_decode($shipping_marking,ENT_QUOTES));
		$activeSheet->getStyle('G'.$row_num.':H'.$row_num)->applyFromArray($style_BOutline);
		$activeSheet->mergeCells("G".$row_num.":H".$row_num);

		$activeSheet->getCell('I'.$row_num)->setValue($qty);
		$activeSheet->getStyle('I'.$row_num.':J'.$row_num)->applyFromArray($style_BOutline);
		$activeSheet->mergeCells("I".$row_num.":J".$row_num);

		$activeSheet->getCell('K'.$row_num)->setValue($fob_price);
		$activeSheet->getStyle('K'.$row_num)->applyFromArray($style_BOutline);

		$activeSheet->getCell('L'.$row_num)->setValue($total_amount);
		$activeSheet->getStyle('L'.$row_num)->applyFromArray($style_BOutline);

		$grand_qty+=$qty;
		$grand_amt+=$total_amount;

		$row_num++;
}

$activeSheet->getCell('A'.$row_num)->setValue("TOTAL =");
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->getStyle('A'.$row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$activeSheet->getStyle('A'.$row_num.':H'.$row_num)->applyFromArray($style_BOutline);
$activeSheet->mergeCells("A".$row_num.":H".$row_num);

$activeSheet->getCell('I'.$row_num)->setValue($grand_qty);
$activeSheet->getStyle('I'.$row_num.':J'.$row_num)->applyFromArray($style_BOutline);
$activeSheet->mergeCells("I".$row_num.":J".$row_num);

$activeSheet->getCell('K'.$row_num)->setValue("");
$activeSheet->getStyle('K'.$row_num)->applyFromArray($style_BOutline);

$activeSheet->getCell('L'.$row_num)->setValue($grand_amt);
$activeSheet->getStyle('L'.$row_num)->applyFromArray($style_BOutline);

$row_num+=2;

//get total ctn
$sel_ctn=$conn->prepare("SELECT sum(cih.total_ctn) total_ctn 
	FROM tblcarton_inv_head cih
	WHERE cih.invID='$invID' AND cih.del='0'
	");
$sel_ctn->execute();
$grand_ctn=$sel_ctn->fetchColumn();

$activeSheet->getCell('A'.$row_num)->setValue("TOTAL CARTONS:");
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("A".$row_num.":B".$row_num);
$activeSheet->getCell('C'.$row_num)->setValue($grand_ctn);
$row_num++;

$activeSheet->getCell('A'.$row_num)->setValue("BL:");
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("A".$row_num.":B".$row_num);
$activeSheet->getCell('C'.$row_num)->setValue("");
$row_num++;

$activeSheet->getCell('A'.$row_num)->setValue("TERM OF PAYMENT:");
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("A".$row_num.":B".$row_num);
$activeSheet->getCell('C'.$row_num)->setValue($paymentterm);
$row_num++;

$activeSheet->getCell('A'.$row_num)->setValue("BENECIARY:");
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("A".$row_num.":B".$row_num);
$activeSheet->getCell('C'.$row_num)->setValue($ownership);

$row_num+=2;

$activeSheet->getCell('A'.$row_num)->setValue("NAME OF BANK:");
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("A".$row_num.":B".$row_num);
$activeSheet->getCell('C'.$row_num)->setValue("");
$row_num++;

$activeSheet->getCell('A'.$row_num)->setValue("ADDRESS:");
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("A".$row_num.":B".$row_num);
$activeSheet->getCell('C'.$row_num)->setValue("");

$row_num+=2;

$activeSheet->getCell('A'.$row_num)->setValue("A/C NO:");
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("A".$row_num.":B".$row_num);
$activeSheet->getCell('C'.$row_num)->setValue("");
$row_num++;

$activeSheet->getCell('A'.$row_num)->setValue("SWIFT CODE:");
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("A".$row_num.":B".$row_num);
$activeSheet->getCell('C'.$row_num)->setValue("");
$row_num++;

$activeSheet->getCell('A'.$row_num)->setValue("E-MAIL:");
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("A".$row_num.":B".$row_num);
$activeSheet->getCell('C'.$row_num)->setValue("");
$row_num++;


// $row_num+=6;
$activeSheet = $sheet->createSheet($i);
$activeSheet->setTitle("Packing Detail List");

$row_num=1;



$activeSheet->getCell('A'.$row_num)->setValue("Packing List Detail");
$activeSheet->getStyle('A'.$row_num)->getFont()->setSize(20);
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->getStyle('A'.$row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$activeSheet->mergeCells("A".$row_num.":L".$row_num);

$row_num+=2;

//FACTORY AREA
$activeSheet->getCell('A'.$row_num)->setValue("FACTORY:");
$activeSheet->getStyle('A'.$row_num)->getFont()->setSize(16);
$activeSheet->getStyle('A'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("A".$row_num.":E".$row_num);

$activeSheet->getCell('A'.($row_num+1))->setValue($manufacturer);
$activeSheet->mergeCells("A".($row_num+1).":E".($row_num+1));

$arr_manuaddr=explode("<br />", $manuaddress2);
for ($i=0; $i < sizeof($arr_manuaddr); $i++) { 
	$activeSheet->getCell('A'.($row_num+2+$i))->setValue($arr_manuaddr[$i]);
	$activeSheet->mergeCells("A".($row_num+2+$i).":E".($row_num+2+$i));
}

//SHIP TO AREA
$activeSheet->getCell('F'.$row_num)->setValue("SHIP TO:");
$activeSheet->getStyle('F'.$row_num)->getFont()->setSize(16);
$activeSheet->getStyle('F'.$row_num)->getFont()->setBold(true);
$activeSheet->mergeCells("F".$row_num.":J".$row_num);

$activeSheet->getCell('F'.($row_num+1))->setValue($ship_to);
$activeSheet->mergeCells("F".($row_num+1).":J".($row_num+1));

$arr_shipaddr=explode("<br />", $ship_address2);
for ($i=0; $i < sizeof($arr_shipaddr); $i++) { 
	$activeSheet->getCell('F'.($row_num+2+$i))->setValue($arr_shipaddr[$i]);
	$activeSheet->mergeCells("F".($row_num+2+$i).":J".($row_num+2+$i));
}

if(sizeof($arr_shipaddr)>sizeof($arr_manuaddr)){
	$start_row_num=$row_num;

	$row_num+=1; //+1 row for name row
	$row_num+=sizeof($arr_shipaddr);
}else{
	$start_row_num=$row_num;

	$row_num+=1; //+1 row for name row
	$row_num+=sizeof($arr_manuaddr);
}


$activeSheet->getStyle('A'.($start_row_num+1).':J'.$row_num)->getFont()->setSize(10);


$arr_gtn_buyerpo=explode(",", $allBuyerPO);
for($i=0;$i<sizeof($arr_gtn_buyerpo);$i++){
	$this_GTN_buyerpo=$arr_gtn_buyerpo[$i];

	$row_num+=1;

	$activeSheet->getCell('A'.$row_num)->setValue("PO Number: ".$this_GTN_buyerpo);
	$activeSheet->getStyle('A'.$row_num)->getFont()->setSize(10);

	$row_num+=2;


	$start_row_num=$row_num;

	$activeSheet->getCell('A'.$row_num)->setValue("Ctn#");
	$activeSheet->getCell('B'.$row_num)->setValue("Case ID");
	$activeSheet->getCell('C'.$row_num)->setValue("Style");
	$activeSheet->getCell('D'.$row_num)->setValue("Color");
	$activeSheet->getCell('E'.$row_num)->setValue("Qty");
	$activeSheet->getCell('F'.$row_num)->setValue("Net Net Weight (kg)");
	$activeSheet->getCell('G'.$row_num)->setValue("Net Weight (kg)");
	$activeSheet->getCell('H'.$row_num)->setValue("Gross Weight (kg)");
	$activeSheet->getCell('I'.$row_num)->setValue("Length (cm)");
	$activeSheet->getCell('J'.$row_num)->setValue("Width (cm)");
	$activeSheet->getCell('K'.$row_num)->setValue("Height (cm)");
	$activeSheet->getCell('L'.$row_num)->setValue("CBM");

	$activeSheet->getStyle('A'.$row_num.':L'.$row_num)->applyFromArray($style_BBtm_bold);

	$row_num++;

	$sel_picklist=$conn->prepare("SELECT cih.total_ctn, cih.SKU, g.styleNo, c.ColorName, cih.total_qty_in_carton, cih.net_net_weight, cih.net_weight, cih.gross_weight, cih.ext_length, cih.ext_width, cih.ext_height, cih.total_CBM
		FROM tblcarton_inv_payment_head cih
		LEFT JOIN tblcarton_inv_payment_detail cid ON cih.CIHID=cid.CIHID
		LEFT JOIN tblshipmentprice sp ON cid.shipmentpriceID=sp.ID
		LEFT JOIN tblship_group_color sgc ON cid.shipmentpriceID=sgc.shipmentpriceID AND cid.group_number=sgc.group_number AND sgc.statusID='1'
		LEFT JOIN tblcolor c ON sgc.colorID=c.ID
		    LEFT JOIN tblgarment g ON sgc.garmentID=g.garmentID
		WHERE sp.GTN_BuyerPO='$this_GTN_buyerpo' AND cih.del='0'
		GROUP BY cih.CIHID");
	$sel_picklist->execute();

	$ctn_num=1;
	$sum_qty=0;
	$sum_net_net_weight=0;
	$sum_net_weight=0;
	$sum_gross_weight=0;
	$sum_cbm=0;
	while($row_picklist=$sel_picklist->fetch(PDO::FETCH_ASSOC)){
		extract($row_picklist);

		$net_net_weight=round($net_net_weight,2);
		$net_weight=round($net_weight,2);
		$gross_weight=round($gross_weight,2);
		$total_CBM=round($total_CBM,2);

		$a=1;
		while($a<=$total_ctn){
			$activeSheet->getCell('A'.$row_num)->setValue($ctn_num);
			$activeSheet->getCell('B'.$row_num)->setValue($SKU);
			$activeSheet->getCell('C'.$row_num)->setValue($styleNo);
			$activeSheet->getCell('D'.$row_num)->setValue($ColorName);
			$activeSheet->getCell('E'.$row_num)->setValue($total_qty_in_carton);
			$activeSheet->getCell('F'.$row_num)->setValue($net_net_weight);
			$activeSheet->getCell('G'.$row_num)->setValue($net_weight);
			$activeSheet->getCell('H'.$row_num)->setValue($gross_weight);
			$activeSheet->getCell('I'.$row_num)->setValue($ext_length);
			$activeSheet->getCell('J'.$row_num)->setValue($ext_width);
			$activeSheet->getCell('K'.$row_num)->setValue($ext_height);
			$activeSheet->getCell('L'.$row_num)->setValue($total_CBM);

			$a++;
			$ctn_num++;

			$sum_qty+=$total_qty_in_carton;
			$sum_net_net_weight+=$net_net_weight;
			$sum_net_weight+=$net_weight;
			$sum_gross_weight+=$gross_weight;
			$sum_cbm+=$total_CBM;

			$row_num++;
		}
	}

	$activeSheet->getCell('A'.$row_num)->setValue("Total");
	$activeSheet->mergeCells("A".$row_num.":L".$row_num);

	$row_num++;

	$activeSheet->getCell('A'.$row_num)->setValue(($ctn_num-1));
	$activeSheet->getCell('E'.$row_num)->setValue($sum_qty);
	$activeSheet->getCell('F'.$row_num)->setValue($sum_net_net_weight);
	$activeSheet->getCell('G'.$row_num)->setValue($sum_net_weight);
	$activeSheet->getCell('H'.$row_num)->setValue($sum_gross_weight);
	$activeSheet->getCell('L'.$row_num)->setValue($sum_cbm);

}

$row_num++;

$activeSheet->getCell('A'.$row_num)->setValue("End of Summary");
$activeSheet->mergeCells("A".$row_num.":L".$row_num);


$activeSheet->getStyle('A'.$start_row_num.':L'.$row_num)->getFont()->setSize(10);
$activeSheet->getStyle('A'.$start_row_num.':L'.$row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Invoice - '.$invoice_no.'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');


$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
$objWriter->save('php://output');

?>