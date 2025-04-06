<?php
// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);

include("../../lock.php");
include('../../shipment_new/shipmentmain/shipmentmainClass.php');
include("../../phpexcel/Classes/PHPExcel.php");
include_once("lc_class.php");

$handle_class = new shipmentmainClass();// by ckwai on 2018-06-07
$handle_class->setConnection($conn);
$handle_class->setlanguage($lang);

$handle_lc = new lcClass();
$handle_lc->setConnection($conn);
$handle_lc->setHandleShipment($handle_class);

$invID = $_GET["id"];
$isBuyerPayment = (isset($_GET["isBuyerPayment"])? $_GET["isBuyerPayment"]: 0);
$displayBuyer="";
if(isset($_GET["displayBuyer"])){
	$displayBuyer = $_GET["displayBuyer"];
}

if($isBuyerPayment==0){
	$tblbuyer_invoice = "tblbuyer_invoice";
	$tblbuyer_invoice_detail = "tblbuyer_invoice_detail";
}
else{
	$tblbuyer_invoice = "tblbuyer_invoice_payment";
	$tblbuyer_invoice_detail = "tblbuyer_invoice_payment_detail";
}


$arrCol=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
				'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
				'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
				'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');

$sql = "
		SELECT inv.*, sm.Description as shipmode, lp.Description as portLoading, ifnull(tt.Description,'-') as tradeterm, ifnull(pt.Description,'-') as paymentterm, pt.Day as payterm_day,
			DATE(inv.exfactory) as exfactorydate, '' as lc_number, '' as lc_bank, '0' as LCHID, '0' as lc_type,
			'' as lc_date, cur.CurrencyCode, cur.Description as cur_description, 
			cp.CompanyName_ENG as ownership, cp.Address as owneraddress, cp.Tel as ownertel, cp.Fax as ownerfax,
			ftym.FactoryName_ENG as manufacturer, ftym.Address as manuaddress, ftym.Tel as manutel, ftym.Fax as manufax,
			cty.Description as manucountry, csn.Name as csn_name, csn.Address as csn_address, od.FactoryID, csn.ConsigneeID, '' as buyerdestination, '' as destport_countryID, '' as portDischarges,
			group_concat(distinct sp.Orderno) as orderno, 
			crmb.bank_account_no, crmb.beneficiary_name, crmb.bank_name, crmb.bank_address, crmb.swift_code,
			ct.description as country, ct.countryCode, group_concat(distinct g.styleNo) as style_no, 
			ss.Description as season, bd.Description as brand, '' as bill_to, '' as bill_address,
			group_concat(distinct sp.ID) as grp_shipmentpriceID, '' as poissuer, '' as poissuer_address,
			group_concat(distinct sp.GTN_buyerpo separator ', ') as allBuyerPO, fty_shipper.FactoryName_ENG as shipper, fty_shipper.Tel as shipper_tel, fty_shipper.Fax as shipper_fax, fty_shipper.Address as shipaddr, bnp.NotifyName, bnp.NotifyAddress, bnp.tel as notifyTel, bnp.fax as notifyFax, bnp.email as notifyEmail, '' as buyerdest_country, '' as byrdt_countrycode, cts.description as transitPort,
			uat.UserFullName as exporter_sign, uat.SignatureID, uat.AcctID as userID, uat.Tel as exporter_tel, uat.Fax as exporter_fax,
			uat.position as exporter_pos
		FROM $tblbuyer_invoice inv 
		LEFT JOIN tblshipmode sm ON sm.ID = inv.shipmodeID 
		LEFT JOIN tblloadingport lp ON lp.ID = inv.portLoadingID 
		LEFT JOIN tbltradeterm tt ON tt.ID = inv.tradeTermID 
		LEFT JOIN tblpaymentterm pt ON pt.ID = inv.paymentTermID 
		LEFT JOIN $tblbuyer_invoice_detail invd ON invd.invID = inv.ID AND invd.del = 0 AND invd.group_number > 0 
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
		LEFT JOIN tblcrm_bank crmb ON crmb.BID = inv.BID
		LEFT JOIN tblcountry ct ON ct.ID = crmb.countryID 
		LEFT JOIN tblcountry cts ON cts.ID = inv.transitPortID	
		LEFT JOIN tblbuyer_notify_party bnp ON bnp.notifyID = inv.notifyID
		LEFT JOIN tbluseraccount uat ON uat.AcctID = inv.export_by
		WHERE inv.ID='$invID' 
	";
// echo "<pre>$sql</pre>";
$invsql = $conn->prepare($sql);
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

$grp_spID      = $invrow["grp_shipmentpriceID"];
$style_no      = $invrow["style_no"];
$season        = $invrow["season"];
$brand         = $invrow["brand"];
$allBuyerPO    = $invrow["allBuyerPO"];
$portLoading   = $invrow["portLoading"];
$tradeterm     = $invrow["tradeterm"];
$paymentterm   = $invrow["paymentterm"];
$payterm_day   = $invrow["payterm_day"];

$buyerdest      = $invrow["buyerdestination"];
$byrdt_country  = $invrow["buyerdest_country"];
$byrdt_ctrcode  = $invrow["byrdt_countrycode"];
$portDischarges = $invrow["portDischarges"];
$transitPort    = $invrow["transitPort"];
$vesselname     = $invrow["vesselname"];

$exfactorydate = $invrow["exfactorydate"];
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


$handle_lc->letterhead_name    = $letterhead_name;
$handle_lc->letterhead_address = $letterhead_address;
$handle_lc->letterhead_tel     = $letterhead_tel;
$handle_lc->letterhead_fax     = $letterhead_fax;
$handle_lc->letterhead_title   = $letterhead_title;
$handle_lc->isBuyerPayment     = $isBuyerPayment;

$str_transit_port = ($transitPort==""? "":" VIA $transitPort");

$notify_tel   = (trim($notifyTel)==""? "":"Tel: $notifyTel");
$notify_fax   = (trim($notifyFax)==""? "":"Fax: $notifyFax");
$notify_email = (trim($notifyEmail)==""? "":"Email: $notifyEmail");

$sheet = new PHPExcel();
$activeSheet = $sheet->getActiveSheet();
$activeSheet->setTitle("Commercial Invoice");

$sheet->getActiveSheet()->getPageMargins()->setTop(0.4);
$sheet->getActiveSheet()->getPageMargins()->setRight(0.1);
$sheet->getActiveSheet()->getPageMargins()->setLeft(0.1);
$sheet->getActiveSheet()->getPageMargins()->setBottom(0.4);

$sheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
$sheet->getActiveSheet()->getPageSetup()->setFitToHeight(1);

$def_font_size = array(
    'font'  => array(
        'size'  => 10
    ));
$activeSheet->getDefaultStyle()->applyFromArray($def_font_size);

$border_outline = array(
  'borders' => array(
    'outline' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);

$border_top = array(
  'borders' => array(
    'top' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);

$border_bottom = array(
  'borders' => array(
    'bottom' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);

$border_allborders = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);

$style_center = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )
);

$sheet->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$sheet->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$sheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$sheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$sheet->getActiveSheet()->getColumnDimension('E')->setWidth(8);
$sheet->getActiveSheet()->getColumnDimension('F')->setWidth(8);
$sheet->getActiveSheet()->getColumnDimension('G')->setWidth(14);
$sheet->getActiveSheet()->getColumnDimension('H')->setWidth(18);
$sheet->getActiveSheet()->getColumnDimension('I')->setWidth(18);
$sheet->getActiveSheet()->getColumnDimension('J')->setWidth(25);
$sheet->getActiveSheet()->getColumnDimension('K')->setWidth(13);
$sheet->getActiveSheet()->getColumnDimension('L')->setWidth(10);
$sheet->getActiveSheet()->getColumnDimension('M')->setWidth(10);


$sheet->getActiveSheet()->mergeCells('A1:M1');
$activeSheet ->getCell('A1')->setValue($letterhead_name);
$sheet->getActiveSheet()->getStyle("A1")->getFont()->setSize(20);

$sheet->getActiveSheet()->mergeCells('A2:M2');
$activeSheet ->getCell('A2')->setValue($letterhead_address);
$sheet->getActiveSheet()->getStyle("A2")->getFont()->setSize(12);

$sheet->getActiveSheet()->mergeCells('A3:M3');
$activeSheet ->getCell('A3')->setValue("TEL : ".$letterhead_tel." / FAX : ".$letterhead_fax);
$sheet->getActiveSheet()->getStyle("A3")->getFont()->setSize(12);

// $sheet->getActiveSheet()->getStyle('A1:M3')->applyFromArray($style_center);

$row=4;

$sheet->getActiveSheet()->mergeCells('A'.$row.':M'.$row);
$activeSheet ->getCell('A'.$row)->setValue("Commercial Invoice");
$sheet->getActiveSheet()->getStyle("A".$row)->getFont()->setSize(12);
$sheet->getActiveSheet()->getStyle('A1:M'.$row)->applyFromArray($style_center);
$sheet->getActiveSheet()->getStyle('A1:M'.$row)->applyFromArray($border_outline);

$row+=1;
$first_row=$row;
$addr_box_last_row=$row;

//applicant and consignee address box
//applicant
$sheet->getActiveSheet()->mergeCells('A'.$row.':D'.$row);
$activeSheet ->getCell('A'.$row)->setValue("APPLICANT");
$sheet->getActiveSheet()->getStyle("A".$row)->getFont()->setBold(true);
$activeSheet ->getCell('A'.($row+1))->setValue($conName);

$addr_con=nl2br($conAddress);
$arr_addr_con=explode("<br />", $addr_con);
$row2=$row+2;
for($i=0;$i<sizeof($arr_addr_con);$i++){
	$sheet->getActiveSheet()->mergeCells('A'.$row2.':D'.$row2);
	$activeSheet->getCell('A'.$row2)->setValue($arr_addr_con[$i]);

	$row2++;
}
$activeSheet ->getCell('A'.$row2)->setValue("Email: ".$conemail);
$activeSheet ->getCell('A'.($row2+1))->setValue("Contact Person: ".$concontact);
$activeSheet ->getCell('A'.($row2+2))->setValue("Tel: ".$contel);
$activeSheet ->getCell('A'.($row2+3))->setValue("Fax: ".$confax);

if($addr_box_last_row<($row2+3)){
	$addr_box_last_row=$row2+3;
}

//consignee
$sheet->getActiveSheet()->mergeCells('E'.$row.':G'.$row);
$activeSheet ->getCell('E'.$row)->setValue("CONSIGNEE");
$sheet->getActiveSheet()->getStyle("E".$row)->getFont()->setBold(true);
$activeSheet ->getCell('E'.($row+1))->setValue($conName);
$row2=$row+2;
for($i=0;$i<sizeof($arr_addr_con);$i++){
	$sheet->getActiveSheet()->mergeCells('E'.$row2.':G'.$row2);
	$activeSheet->getCell('E'.$row2)->setValue($arr_addr_con[$i]);

	$row2++;
}
$activeSheet ->getCell('E'.$row2)->setValue("Email: ".$conemail);
$activeSheet ->getCell('E'.($row2+1))->setValue("Contact Person: ".$concontact);
$activeSheet ->getCell('E'.($row2+2))->setValue("Tel: ".$contel);
$activeSheet ->getCell('E'.($row2+3))->setValue("Fax: ".$confax);

if($addr_box_last_row<($row2+3)){
	$addr_box_last_row=$row2+3;
}

//invoice detail box
$sheet->getActiveSheet()->getStyle("H".$row)->getFont()->setBold(true);
$activeSheet ->getCell('H'.$row)->setValue("INVOICE NO: ");
$activeSheet ->getCell('I'.$row)->setValue($invoice_no);

$sheet->getActiveSheet()->getStyle("K".$row)->getFont()->setBold(true);
$activeSheet ->getCell('K'.$row)->setValue("DATE: ");
$activeSheet ->getCell('L'.$row)->setValue($invoice_date);

$sheet->getActiveSheet()->getStyle("H".($row+1))->getFont()->setBold(true);
$activeSheet ->getCell('H'.($row+1))->setValue("EX FACTORY DATE: ");
$activeSheet ->getCell('I'.($row+1))->setValue($exfactorydate);

$sheet->getActiveSheet()->getStyle("H".($row+2))->getFont()->setBold(true);
$activeSheet ->getCell('H'.($row+2))->setValue("ITEM NO: ");
$activeSheet ->getCell('I'.($row+2))->setValue($orderno);

$sheet->getActiveSheet()->getStyle("H".($row+4))->getFont()->setBold(true);
$activeSheet ->getCell('H'.($row+4))->setValue("SHIPMENT TERM: ");
$activeSheet ->getCell('I'.($row+4))->setValue($tradeterm);

$sheet->getActiveSheet()->getStyle("H".($row+6))->getFont()->setBold(true);
$activeSheet ->getCell('H'.($row+6))->setValue("ORIGIN: ");
$activeSheet ->getCell('I'.($row+6))->setValue("PHNOM PENH, ".$manucountry);

$last_row=$addr_box_last_row;
if($addr_box_last_row<($row+6)){
	$last_row=$row+6;
}



$sheet->getActiveSheet()->getStyle('A'.$first_row.':G'.$last_row)->applyFromArray($border_outline);
$sheet->getActiveSheet()->getStyle('H'.$first_row.':M'.$last_row)->applyFromArray($border_outline);

$row=$last_row+1;

//notify party box
$sheet->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
$activeSheet ->getCell('A'.$row)->setValue("NOTIFY PARTY");
$sheet->getActiveSheet()->getStyle("A".$row)->getFont()->setBold(true);
$activeSheet ->getCell('A'.($row+1))->setValue($notify_party);

$addr_notify=nl2br($notify_address);
$arr_addr_notify=explode("<br />", $addr_notify);
$row2=$row+2;
for($i=0;$i<sizeof($arr_addr_notify);$i++){
	$sheet->getActiveSheet()->mergeCells('A'.$row2.':G'.$row2);
	$activeSheet->getCell('A'.$row2)->setValue($arr_addr_notify[$i]);

	$row2++;
}
$activeSheet->getCell('A'.$row2)->setValue("Tel: ".$notify_tel);
$activeSheet->getCell('A'.($row2+1))->setValue("Fax: ".$notify_fax);
$activeSheet->getCell('A'.($row2+2))->setValue("Email: ".$notify_email);

$row_last_notify_box=($row2+2);

$sheet->getActiveSheet()->getStyle('A'.$row.':G'.$row_last_notify_box)->applyFromArray($border_outline);

//LC Detail Box
$lc_first_row=$row;

$activeSheet->getCell('H'.$lc_first_row)->setValue("LC#: ");
$sheet->getActiveSheet()->getStyle("H".$lc_first_row)->getFont()->setBold(true);
$activeSheet->getCell('I'.$lc_first_row)->setValue($lc_number);

$activeSheet->getCell('H'.($lc_first_row+1))->setValue("DATED: ");
$sheet->getActiveSheet()->getStyle("H".($lc_first_row+1))->getFont()->setBold(true);
$activeSheet->getCell('I'.($lc_first_row+1))->setValue($lc_date);

$activeSheet->getCell('H'.($lc_first_row+2))->setValue("LC ISSUING BANK: ");
$sheet->getActiveSheet()->getStyle("H".($lc_first_row+2))->getFont()->setBold(true);
$activeSheet->getCell('I'.($lc_first_row+2))->setValue($lc_bank);

//shipped from and to box
$row=$row_last_notify_box+1;

$sheet->getActiveSheet()->mergeCells('A'.$row.':D'.$row);
$activeSheet->getCell('A'.$row)->setValue("PORT OF LOADING");//SHIPPED FROM
$sheet->getActiveSheet()->getStyle("A".$row)->getFont()->setBold(true);
$activeSheet->getCell('A'.($row+1))->setValue($portLoading);

$sheet->getActiveSheet()->mergeCells('E'.$row.':G'.$row);
$activeSheet->getCell('E'.$row)->setValue("TO");
$sheet->getActiveSheet()->getStyle("E".$row)->getFont()->setBold(true);
$activeSheet->getCell('E'.($row+1))->setValue($buyerdest);

$sheet->getActiveSheet()->getStyle('A'.$row.':G'.($row+1))->applyFromArray($border_outline);

//shipped date detail
$row+=2;

$sheet->getActiveSheet()->mergeCells('A'.$row.':D'.$row);
$activeSheet->getCell('A'.$row)->setValue("SHIPPED PER");
$sheet->getActiveSheet()->getStyle("A".$row)->getFont()->setBold(true);
$activeSheet->getCell('A'.($row+1))->setValue($shipmode);

$sheet->getActiveSheet()->mergeCells('E'.$row.':G'.$row);
$activeSheet->getCell('E'.$row)->setValue("VESSEL ETD");
$sheet->getActiveSheet()->getStyle("E".$row)->getFont()->setBold(true);
$activeSheet->getCell('E'.($row+1))->setValue($shippeddate);

$sheet->getActiveSheet()->getStyle('A'.$row.':G'.($row+1))->applyFromArray($border_outline);

//draw lc detail box
$sheet->getActiveSheet()->getStyle('H'.$lc_first_row.':M'.($row+1))->applyFromArray($border_outline);

$row+=3;

$query_filter = "";
$arr_array   = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_buyerpo = $arr_array["byBuyerPO"];

$grand_ctn = 0;
$grand_qty = 0;
$grand_amt = 0;
$grand_nw  = 0;
$grand_gw  = 0;

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

$sheet->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
$activeSheet->getCell('A'.$row)->setValue("MARKS & NOS");
$sheet->getActiveSheet()->getStyle('A'.$row.':C'.($row+1))->applyFromArray($border_outline);

$activeSheet->getCell('D'.$row)->setValue("NO. OF");
$activeSheet->getCell('D'.($row+1))->setValue("PKGS");
$sheet->getActiveSheet()->getStyle('D'.$row.':D'.($row+1))->applyFromArray($border_outline);

$sheet->getActiveSheet()->mergeCells('E'.$row.':J'.$row);
$activeSheet->getCell('E'.$row)->setValue("DESCRIPTION");
$sheet->getActiveSheet()->getStyle('E'.$row.':J'.($row+1))->applyFromArray($border_outline);

$activeSheet->getCell('K'.$row)->setValue("QTY");
$activeSheet->getCell('K'.($row+1))->setValue($str_uom);
$sheet->getActiveSheet()->getStyle('K'.$row.':K'.($row+1))->applyFromArray($border_outline);

$activeSheet->getCell('L'.$row)->setValue("UNIT PRICE");
$activeSheet->getCell('L'.($row+1))->setValue('('.$CurrencyCode.''.$str_uom2.')');
$sheet->getActiveSheet()->getStyle('L'.$row.':L'.($row+1))->applyFromArray($border_outline);

$activeSheet->getCell('M'.$row)->setValue("AMOUNT");
$activeSheet->getCell('M'.($row+1))->setValue('('.$CurrencyCode.')');
$sheet->getActiveSheet()->getStyle('M'.$row.':M'.($row+1))->applyFromArray($border_outline);

$row+=2;
$first_row=$row;

$sheet->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
$activeSheet->getCell('A'.$row)->setValue($conName);
$activeSheet->getCell('A'.($row+1))->setValue("COUNTRY OF ORIGIN");
$activeSheet->getCell('A'.($row+2))->setValue("NG ITEMS NUMBER");
$activeSheet->getCell('A'.($row+3))->setValue("DESCRIPTION");
$activeSheet->getCell('A'.($row+4))->setValue("CTNS QTY MASTER");
$activeSheet->getCell('A'.($row+5))->setValue("CARTON NUMBER OF UPC CODE");

$sheet->getActiveSheet()->mergeCells('E'.$row.':J'.$row);
$activeSheet->getCell('E'.$row)->setValue("DESCRIPTION OF GOODS AS APPAREL");

$row+=2;

$i_po = 0;	
$arr_uomqty = array(); 
$grand_nnw = 0;
$grand_nw  = 0;
$grand_gw  = 0;
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
		$fab_order       = strtolower($arr_info["fab_order"]);
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
		
		$fab_order = str_replace("&#039;", "'", $fab_order);

		$activeSheet->getCell('D'.$row)->setValue($this_total_ctn);
		$sheet->getActiveSheet()->mergeCells('E'.$row.':J'.$row);
		$activeSheet->getCell('E'.$row)->setValue($fab_order);

		$sheet->getActiveSheet()->mergeCells('E'.($row+1).':F'.($row+1));
		$activeSheet->getCell('E'.($row+1))->setValue("PO NO.");
		$activeSheet->getCell('G'.($row+1))->setValue("STYLE NO.");
		$activeSheet->getCell('H'.($row+1))->setValue("NG ITEM#");
		$activeSheet->getCell('I'.($row+1))->setValue("COLOR");
		$activeSheet->getCell('J'.($row+1))->setValue("CAT");
		
		$row_count=$row+2;
		
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
			$this_fob  = number_format($fob_price,2);
			$str_ctn_qty  = number_format($total_ctn_qty);
			
			$sqlqc = "SELECT qc.Description as this_quotacat
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
			
			if(isset($arr_uomqty["str$uom"]["qty"])){
				$arr_uomqty["str$uom"]["qty"] += $total_ctn_qty;
			}
			else{
				$arr_uomqty["str$uom"]["qty"] = $total_ctn_qty;
			}
			
			if(isset($arr_uomqty["str$uom"]["amt"])){
				$arr_uomqty["str$uom"]["amt"] += $amt;
			}
			else{
				$arr_uomqty["str$uom"]["amt"] = $amt;
			}

			$sheet->getActiveSheet()->mergeCells('E'.$row_count.':F'.$row_count);
			$activeSheet->getCell('E'.$row_count)->setValue($this_key);
			$activeSheet->getCell('G'.$row_count)->setValue($this_styleNo);
			$activeSheet->getCell('H'.$row_count)->setValue($prepack_name);
			$activeSheet->getCell('I'.$row_count)->setValue($colorOnly);
			$activeSheet->getCell('J'.$row_count)->setValue($this_quotacat);
			$activeSheet->getCell('K'.$row_count)->setValue($str_ctn_qty);
			$activeSheet->getCell('L'.$row_count)->setValue($this_fob);
			$activeSheet->getCell('M'.$row_count)->setValue($this_amt);
			
			$row_count++;
			
			$f++;
		}//-- End Foreach --//
		
		
		$row=$row_count+1;
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

$sheet->getActiveSheet()->getStyle('A'.$first_row.':C'.$row)->applyFromArray($border_outline);
$sheet->getActiveSheet()->getStyle('D'.$first_row.':D'.$row)->applyFromArray($border_outline);
$sheet->getActiveSheet()->getStyle('E'.$first_row.':J'.$row)->applyFromArray($border_outline);
$sheet->getActiveSheet()->getStyle('K'.$first_row.':K'.$row)->applyFromArray($border_outline);
$sheet->getActiveSheet()->getStyle('L'.$first_row.':L'.$row)->applyFromArray($border_outline);
$sheet->getActiveSheet()->getStyle('M'.$first_row.':M'.$row)->applyFromArray($border_outline);

$row+=1;
$first_row=$row;

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

	$sheet->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
	$activeSheet->getCell('A'.$row)->setValue($str_title);

	$activeSheet->getCell('D'.$row)->setValue($str_ctnqty);

	$sheet->getActiveSheet()->mergeCells('E'.$row.':J'.$row);
	$activeSheet->getCell('E'.$row)->setValue($str_title2);

	$activeSheet->getCell('K'.$row)->setValue($this_grand_qty.' '.$this_uom);
	$activeSheet->getCell('M'.$row)->setValue("US$ ".$this_grand_amt);
	
	$row++;
}

$discount_goc = round($grand_amt * 0.025, 2);
$grand_amt    = $grand_amt - $discount_goc;
$str_amt      = number_format($grand_amt,2);

$activeSheet->getCell('K'.$row)->setValue("LESS: 2.5% GOC DISCOUNT");
$activeSheet->getCell('M'.$row)->setValue($discount_goc);

$activeSheet->getCell('M'.($row+1))->setValue("US$ ".$str_amt);

$sheet->getActiveSheet()->getStyle('A'.$first_row.':C'.($row+1))->applyFromArray($border_outline);
$sheet->getActiveSheet()->getStyle('D'.$first_row.':D'.($row+1))->applyFromArray($border_outline);
$sheet->getActiveSheet()->getStyle('E'.$first_row.':J'.($row+1))->applyFromArray($border_outline);
$sheet->getActiveSheet()->getStyle('K'.$first_row.':M'.($row+1))->applyFromArray($border_outline);

$row+=3;

$sheet->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
$sheet->getActiveSheet()->mergeCells('A'.$row.':C'.($row+1));
$sheet->getActiveSheet()->mergeCells('A'.$row.':C'.($row+2));
$sheet->getActiveSheet()->mergeCells('A'.$row.':C'.($row+3));
$sheet->getActiveSheet()->mergeCells('A'.$row.':C'.($row+4));
$sheet->getActiveSheet()->mergeCells('A'.$row.':C'.($row+5));
$activeSheet->getCell('A'.$row)->setValue("WE HEREBY CERTIFY THAT:");
$activeSheet->getCell('A'.($row+1))->setValue("NO SWPM ASSOCIATED WITH THIS SHIPMENT");
$activeSheet->getCell('A'.($row+2))->setValue("NO SWPM USED AS PACKING MATERIAL FOR THIS SHIPMENT");
$activeSheet->getCell('A'.($row+4))->setValue("TOTAL GROSS WEIGHT: ");
$activeSheet->getCell('A'.($row+5))->setValue("TOTAL NET WEIGHT: ");
$activeSheet->getCell('A'.($row+6))->setValue("TOTAL NET NET WEIGHT: ");

$activeSheet->getCell('D'.($row+4))->setValue($grand_gw." KGS");
$activeSheet->getCell('D'.($row+5))->setValue($grand_nw." KGS");
$activeSheet->getCell('D'.($row+6))->setValue($grand_nnw." KGS");


//detail sheet

$handle_lc->getBuyerInvoicePackingList_excel($sheet,$invID);

//end detail sheet



if($acctid!=3){

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="JoeFresh-'.$invoice_no.'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');


$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
$objWriter->save('php://output');


}
else{
	//======== HTML Display ========//
	$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'HTML');
	$objWriter->save('php://output');

	// code for direct preview PDF (remove "$objWriter->save('php://output');")
	// $objWriter->save('01simple.pdf');
	// header('location:01simple.pdf');
}
?>