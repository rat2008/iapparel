<?php 
include('../../../lock.php');
include('../../../shipment_new/shipmentmain/shipmentmainClass.php');
include_once("../lc_class.php");
include("../../../phpexcel/Classes/PHPExcel.php");

// ini_set('display_errors', 1); 
// ini_set('display_startup_errors', 1); 
// error_reporting(E_ALL);

$column = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                        'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                        'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                        'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                        'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');

//------------------------------------------------------//						
//------------------ EXCEL CELL STYLE ------------------//
//------------------------------------------------------//
$textAlignTop = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY,
                     'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                )
            );
        $textAlignCenter = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                     'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                )
            );

        $textAlignRight = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                     'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                )
            );

        $textAlignLeft = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                     'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                )
            );
            
            
          $sl_bgcolor = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'BFBDBD')
                ),
                'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '4d4d4d')
            )
            );
            
            
          $tech_bgcolor = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'C5EDF9')
                ),
                'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '4d4d4d')
            )
            );
            
          $sm_bgcolor = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'EFEFEF')
                ),
                'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '4d4d4d')
            )
            );
            
         $main_bgcolor = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '1A758C')
                ),
                  'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'ffffff')
            )
        );
            
            
          $tbh_bgcolor = array(
                /* 'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '4d4d4d')
                ), */
                  'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '383838')
            )
            );

          $tbh_cellbgcolor = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'b7b7b7')
                )
            );

            
            
           $BStyle =  array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN,
                     'color' => array(
                         'rgb' => '000000'
                     )
                 );  

        $styleArray = array(
            'font'  => array(
                'name'  => 'Times New Roman'
            ));
$isBuyerPayment = (isset($_GET["isBuyerPayment"])? $_GET["isBuyerPayment"]: 0);

$handle_shipment = new shipmentmainClass();// by ckwai on 2018-06-07
$handle_shipment->setConnection($conn);
$handle_shipment->setlanguage($lang);

$handle_lc = new lcClass();
$handle_lc->setConnection($conn);
$handle_lc->setHandleShipment($handle_shipment);
$handle_lc->isBuyerPayment = $isBuyerPayment;

$invID = $_GET["id"];

$excel_class = new excelClass();
$excel_class->setConn($conn);
$excel_class->setShipment($handle_shipment);
$excel_class->isBuyerPayment = $isBuyerPayment;
$excel_class->textAlignCenter = $textAlignCenter;
$excel_class->textAlignRight = $textAlignRight;
$excel_class->BStyle = $BStyle;
$excel_class->handle_lc = $handle_lc;
$excel_class->column = $column;

if($isBuyerPayment==0){
	$tblbuyer_invoice = "tblbuyer_invoice";
	$tblbuyer_invoice_detail = "tblbuyer_invoice_detail";
}
else{
	$tblbuyer_invoice = "tblbuyer_invoice_payment";
	$tblbuyer_invoice_detail = "tblbuyer_invoice_payment_detail";
}

$sql = "SELECT inv.*, sm.Description as shipmode, lp.Description as portLoading, ifnull(tt.Description,'-') as tradeterm,
			ifnull(pt.Description,'-') as paymentterm, pt.Day as payterm_day,
			DATE(inv.exfactory) as exfactorydate, lch.lc_number, lch.lc_bank, lci.lc_date, cur.CurrencyCode, cur.Description as cur_description, 
			cp.CompanyName_ENG as ownership, cp.Address as owneraddress, cp.Tel as ownertel, cp.Fax as ownerfax,
			ftym.FactoryName_ENG as manufacturer, ftym.Address as manuaddress, ftym.Tel as manutel, ftym.Fax as manufax,
			cty.Description as manucountry, csn.Name as csn_name, csn.Address as csn_address, od.FactoryID, csn.ConsigneeID, bdt.Description as buyerdestination, dp.countryID as destport_countryID, dp.Description as portDischarges,
			group_concat(distinct sp.Orderno) as orderno, 
			crmb.bank_account_no, crmb.beneficiary_name, crmb.bank_name, crmb.bank_address, crmb.swift_code,
			ct.description as country, ct.countryCode, group_concat(distinct g.styleNo) as style_no, 
			ss.Description as season, bd.Description as brand, pyr.Description as bill_to, pyr.address as bill_address,
			group_concat(distinct sp.ID) as grp_shipmentpriceID, pis.Description as poissuer, pis.address as poissuer_address,
			group_concat(distinct sp.BuyerPO separator ', ') as allBuyerPO, fty_shipper.FactoryName_ENG as shipper, fty_shipper.Tel as shipper_tel, fty_shipper.Fax as shipper_fax
		FROM $tblbuyer_invoice inv 
		LEFT JOIN tblshipmode sm ON sm.ID = inv.shipmodeID 
		LEFT JOIN tblloadingport lp ON lp.ID = inv.portLoadingID 
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
		WHERE inv.ID='$invID' 
	";
$invsql = $conn->prepare($sql);
$invsql->execute();
$invrow = $invsql->fetch(PDO::FETCH_ASSOC);

$BuyerID       = $invrow["BuyerID"];
$ConsigneeID   = $invrow["ConsigneeID"];
$invoice_no    = $invrow["invoice_no"];
$invoice_date  = $invrow["invoice_date"];
$portLoadingID = $invrow["portLoadingID"];
$remarks       = $invrow["remarks"];
$lc_number     = $invrow["lc_number"];
$lc_bank       = $invrow["lc_bank"];
$lc_date       = $invrow["lc_date"];

$shipper        = $invrow["shipper"];
$shipper_addr   = $invrow["shipper_address"];
$shipper_tel    = $invrow["shipper_tel"];
$shipper_fax    = $invrow["shipper_fax"];
$notify_party   = $invrow["notify_party"];
$notify_address = $invrow["notify_address"];

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
$portDischarges = $invrow["portDischarges"];
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

//======================================= Consignee detail  =================================//
$consql = $conn->prepare("SELECT con.*, c.Description as countryName, bd.Description as brand 
							FROM tblconsignee con 
							LEFT JOIN tblcountry c ON c.ID=con.countryID 
							LEFT JOIN tblbrand bd ON bd.ID = con.brandID
							WHERE con.ConsigneeID='$ConsigneeID' ");
$consql->execute();
$conrow = $consql->fetch(PDO::FETCH_ASSOC);
$brand         = $conrow["brand"];
$conName       = $conrow["Name"]." - $brand";
$conAddress    = $conrow["Address"];
//$conAddress  = str_replace("Tel", "<br>Tel", $conAddress);
$countryName   = $conrow["countryName"];
$shippingmarks = $conrow["shippingmarks"];

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


$handle_lc->letterhead_name    = $letterhead_name;
$handle_lc->letterhead_address = $letterhead_address;
$handle_lc->letterhead_tel     = $letterhead_tel;
$handle_lc->letterhead_fax     = $letterhead_fax;
$handle_lc->letterhead_title   = $letterhead_title;
$handle_lc->isBuyerPayment     = $isBuyerPayment;


//==========================================================================//
//------------------------- START EXCEL GENERATE ---------------------------//
//==========================================================================//
$startrow = 0; $i=0;
ob_start(); 
// Create new PHPExcel object
$sheet = new PHPExcel();
$activeSheet = $sheet->getActiveSheet();

// Add new sheet
$objWorkSheet = $sheet->createSheet($i); //Setting index when creating

$startrow++;
$objWorkSheet->mergeCells("A$startrow:K$startrow"); 
$objWorkSheet->getCell('A'.$startrow)->setValue($letterhead_name); //invoice shipper
$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($textAlignCenter);
$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
$objWorkSheet->getStyle("A$startrow:K$startrow")->getFont()->setBold(true)->setSize(16);
$objWorkSheet->getRowDimension(''.$startrow)->setRowHeight(25);

$startrow++;
$objWorkSheet ->mergeCells("A$startrow:K$startrow");
$objWorkSheet ->setCellValue('A'.$startrow, "$letterhead_address"); //invoice shipper address
$objWorkSheet ->getStyle('A'.$startrow)->applyFromArray($textAlignCenter);
$objWorkSheet ->getStyle('A'.$startrow)->applyFromArray($styleArray);
$objWorkSheet ->getStyle("A$startrow:K$startrow")->getFont()->setSize(10);
$objWorkSheet ->getRowDimension(''.$startrow)->setRowHeight(30);
$objWorkSheet ->getStyle('A'.$startrow)->getAlignment()->setWrapText(true);
			
$startrow++;
$objWorkSheet ->mergeCells("A$startrow:K$startrow");
$objWorkSheet ->setCellValue('A'.$startrow, "TEL :$letterhead_tel               FAX :$letterhead_fax");
$objWorkSheet ->getStyle('A'.$startrow)->applyFromArray($textAlignCenter);
$objWorkSheet ->getStyle('A'.$startrow)->applyFromArray($styleArray);
$objWorkSheet->getStyle("A$startrow:K$startrow")->getFont()->setSize(10);

$startrow++;
$startrow++;
$objWorkSheet->getStyle("A$startrow:K$startrow")->getBorders()->getTop()->applyFromArray($BStyle);
$objWorkSheet->mergeCells("A$startrow:K$startrow"); 
$objWorkSheet->getCell('A'.$startrow)->setValue($letterhead_title); 
$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($textAlignCenter);
$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
$objWorkSheet->getStyle("A$startrow:K$startrow")->getFont()->setBold(true)->setSize(16);
$objWorkSheet->getRowDimension(''.$startrow)->setRowHeight(25);

$startrow++;
$objWorkSheet->getStyle("A$startrow:K$startrow")->getBorders()->getTop()->applyFromArray($BStyle); // APPLY TOP BORDER

$objWorkSheet ->setCellValue('A'.$startrow, "SHIPPER:");
$objWorkSheet->getStyle("A$startrow")->getFont()->setBold(true)->setSize(9);
$objWorkSheet ->getColumnDimension('A')->setWidth(18); // SET COLUMN WIDTH

$merge_row = $startrow + 16;
$objWorkSheet ->setCellValue('F'.$startrow, "INVOICE NO.");
$objWorkSheet->getStyle("F".$startrow)->getFont()->setBold(true)->setSize(9);
$objWorkSheet->getStyle("F$startrow:F$merge_row")->getBorders()->getLeft()->applyFromArray($BStyle); // APPLY LEFT BORDER
$objWorkSheet ->getColumnDimension('F')->setWidth(18); // SET COLUMN WIDTH
$objWorkSheet ->setCellValue('G'.$startrow, ":");
$objWorkSheet ->getColumnDimension('G')->setWidth(1.5);
$objWorkSheet ->setCellValue('H'.$startrow, "$invoice_no");

$startrow++;
$objWorkSheet ->setCellValue('A'.$startrow, "$shipper");
$objWorkSheet ->setCellValue('F'.$startrow, "DATE");
$objWorkSheet->getStyle("F".$startrow)->getFont()->setBold(true)->setSize(9);
$objWorkSheet ->setCellValue('G'.$startrow, ":");
$objWorkSheet ->setCellValue('H'.$startrow, "$invoice_date");

$arr_shipper_addr = explode(",", $shipper_addr);

if(count($arr_shipper_addr)>0){
	for($arr=0;$arr<count($arr_shipper_addr);$arr+=2){
		$this_a = $arr+1;
		if(array_key_exists($this_a, $arr_shipper_addr)){
			$str_address = $arr_shipper_addr[$arr].",".$arr_shipper_addr[$this_a];
		}
		else{
			$str_address = $arr_shipper_addr[$arr];
		}
		
		$startrow++;
		$objWorkSheet ->setCellValue('A'.$startrow, trim($str_address));
	}
}
else{
	$startrow++;
}

$objWorkSheet ->setCellValue('F'.$startrow, "SHIPMENT TERM");
$objWorkSheet->getStyle("F".$startrow)->getFont()->setBold(true)->setSize(9);
$objWorkSheet ->setCellValue('G'.$startrow, ":");
$objWorkSheet ->setCellValue('H'.$startrow, "$tradeterm");

$startrow++;
$objWorkSheet ->setCellValue('A'.$startrow, "TEL: $shipper_tel  FAX: $shipper_fax");
$objWorkSheet ->setCellValue('F'.$startrow, "PAYMENT TERM");
$objWorkSheet->getStyle("F".$startrow)->getFont()->setBold(true)->setSize(9);
$objWorkSheet ->setCellValue('G'.$startrow, ":");
$objWorkSheet ->setCellValue('H'.$startrow, "$paymentterm");

$startrow++;
$objWorkSheet ->setCellValue('F'.$startrow, "COUNTRY OF ORIGIN");
$objWorkSheet->getStyle("F".$startrow)->getFont()->setBold(true)->setSize(9);
$objWorkSheet ->setCellValue('G'.$startrow, ":");
$objWorkSheet ->setCellValue('H'.$startrow, "$manucountry");

$startrow++;
$objWorkSheet->getStyle("A$startrow:K$startrow")->getBorders()->getTop()->applyFromArray($BStyle); // APPLY TOP BORDER
$objWorkSheet ->setCellValue('A'.$startrow, "CONSIGNEE:");
$objWorkSheet->getStyle("A$startrow")->getFont()->setBold(true)->setSize(9);
$objWorkSheet ->setCellValue('F'.$startrow, "FACTORY IA No.");
$objWorkSheet->getStyle("F".$startrow)->getFont()->setBold(true)->setSize(9);
$objWorkSheet ->setCellValue('G'.$startrow, ":");
$objWorkSheet ->setCellValue('H'.$startrow, "$orderno");

$startrow++;
$objWorkSheet ->setCellValue('A'.$startrow, "$conName");

$startrow_bank = $startrow + 1;

$arr_csn_addr = explode(",", $conAddress);
if(count($arr_csn_addr)>0){
	for($arr=0;$arr<count($arr_csn_addr);$arr+=2){
		$this_a = $arr+1;
		if(array_key_exists($this_a, $arr_csn_addr)){
			$str_address = $arr_csn_addr[$arr].",".$arr_csn_addr[$this_a];
		}
		else{
			$str_address = $arr_csn_addr[$arr];
		}
		
		$startrow++;
		$objWorkSheet ->setCellValue('A'.$startrow, trim($str_address));
	}
}
else{
	$startrow++;
}

$startrow++;
$startrow++;
$merge_row = $startrow + 3;
$objWorkSheet ->setCellValue('A'.$startrow, "PORT OF LOADING");
$objWorkSheet->getStyle("A$startrow")->getFont()->setBold(true)->setSize(9);
$objWorkSheet ->setCellValue('D'.$startrow, "DESTINATION");
$objWorkSheet->getStyle("D$startrow")->getFont()->setBold(true)->setSize(9);
$objWorkSheet->getStyle("A$startrow:E$startrow")->getBorders()->getTop()->applyFromArray($BStyle); // APPLY TOP BORDER
$objWorkSheet->getStyle("D$startrow:D$merge_row")->getBorders()->getLeft()->applyFromArray($BStyle); // APPLY LEFT BORDER

$startrow++;
$objWorkSheet ->setCellValue('A'.$startrow, "$portLoading");
$objWorkSheet ->setCellValue('D'.$startrow, "$buyerdest");

$startrow++;
$objWorkSheet ->setCellValue('A'.$startrow, "MODE OF TRANSPORTATION");
$objWorkSheet->getStyle("A$startrow")->getFont()->setBold(true)->setSize(9);
$objWorkSheet ->setCellValue('D'.$startrow, "ETD");
$objWorkSheet->getStyle("D$startrow")->getFont()->setBold(true)->setSize(9);
$objWorkSheet->getStyle("A$startrow:E$startrow")->getBorders()->getTop()->applyFromArray($BStyle); // APPLY TOP BORDER

$startrow++;
$objWorkSheet ->setCellValue('A'.$startrow, "$shipmode");
$objWorkSheet ->setCellValue('D'.$startrow, "$shippeddate");

//--------------------------------------//
//------------- RIGHT SIDE -------------//
//--------------------------------------//
$objWorkSheet ->setCellValue('F'.$startrow_bank, "BANK ACCOUNT NUMBER");
$objWorkSheet->getStyle("F".$startrow_bank)->getFont()->setBold(true)->setSize(9);
$objWorkSheet ->setCellValue('G'.$startrow_bank, ":");
$objWorkSheet ->setCellValue('H'.$startrow_bank, "$bank_account_no");

$startrow_bank++;
$objWorkSheet ->setCellValue('H'.$startrow_bank, "$bank_name");
$startrow_bank++;
$objWorkSheet ->setCellValue('H'.$startrow_bank, "$bank_address");
$objWorkSheet->getStyle('H'.$startrow_bank)->getAlignment()->setWrapText(true); //allow breakline display in cell
$objWorkSheet ->getColumnDimension('H')->setWidth(32); // SET COLUMN WIDTH
$objWorkSheet ->getColumnDimension('A')->setWidth(32); // SET COLUMN WIDTH

$startrow_bank++;
$startrow_bank++;
$objWorkSheet ->setCellValue('F'.$startrow_bank, "BENEFICIARY'S NAME");
$objWorkSheet->getStyle("F".$startrow_bank)->getFont()->setBold(true)->setSize(9);
$objWorkSheet ->setCellValue('G'.$startrow_bank, ":");
$objWorkSheet ->setCellValue('H'.$startrow_bank, "$beneficiary_name");

$startrow_bank++;
$objWorkSheet ->setCellValue('F'.$startrow_bank, "SWIFT CODE");
$objWorkSheet->getStyle("F".$startrow_bank)->getFont()->setBold(true)->setSize(9);
$objWorkSheet ->setCellValue('G'.$startrow_bank, ":");
$objWorkSheet ->setCellValue('H'.$startrow_bank, "For account of: $bank_name, $bank_country (Swift  Address: $swift_code)");

//---------------------------------------------//
//------------- PO DETAILS HEADER -------------//
//---------------------------------------------//
$startrow++;
$objWorkSheet->getStyle("A$startrow:K$startrow")->getBorders()->getTop()->applyFromArray($BStyle); // APPLY TOP BORDER
$merge_row = $startrow+1;
$objWorkSheet->mergeCells("A$startrow:B$merge_row"); 
$objWorkSheet->setCellValue('A'.$startrow, "CTNS MARKS & NUMBER");
$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(9);
$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($textAlignCenter); // ALIGN CENTER

$objWorkSheet->setCellValue('C'.$startrow, "NO. OF");
$objWorkSheet->getStyle("C".$startrow)->getFont()->setBold(true)->setSize(9);
$objWorkSheet->getStyle('C'.$startrow)->applyFromArray($textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('C'.$merge_row, "CTNS");
$objWorkSheet->getStyle('C'.$merge_row)->applyFromArray($textAlignCenter); // ALIGN CENTER
$objWorkSheet->getStyle("C".$merge_row)->getFont()->setBold(true)->setSize(9);
$objWorkSheet->getStyle("C$startrow:C$merge_row")->getBorders()->getLeft()->applyFromArray($BStyle); // APPLY LEFT BORDER

$objWorkSheet->mergeCells("D$startrow:H$merge_row"); 
$objWorkSheet->setCellValue('D'.$startrow, "DESCRIPTION");
$objWorkSheet->getStyle("D".$startrow)->getFont()->setBold(true)->setSize(9);
$objWorkSheet->getStyle('D'.$startrow)->applyFromArray($textAlignCenter); // ALIGN CENTER
$objWorkSheet->getStyle("D$startrow:D$merge_row")->getBorders()->getLeft()->applyFromArray($BStyle); // APPLY LEFT BORDER

$objWorkSheet->setCellValue('I'.$startrow, "QTY");
$objWorkSheet->getStyle("I".$startrow)->getFont()->setBold(true)->setSize(9);
$objWorkSheet->getStyle('I'.$startrow)->applyFromArray($textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('I'.$merge_row, "PCS");
$objWorkSheet->getStyle("I".$merge_row)->getFont()->setBold(true)->setSize(9);
$objWorkSheet->getStyle('I'.$merge_row)->applyFromArray($textAlignCenter); // ALIGN CENTER
$objWorkSheet->getStyle("I$startrow:I$merge_row")->getBorders()->getLeft()->applyFromArray($BStyle); // APPLY LEFT BORDER

$objWorkSheet->setCellValue('J'.$startrow, "UNIT PRICE");
$objWorkSheet->getStyle("J".$startrow)->getFont()->setBold(true)->setSize(9);
$objWorkSheet->getStyle('J'.$startrow)->applyFromArray($textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('J'.$merge_row, "($CurrencyCode)");
$objWorkSheet->getStyle("J".$merge_row)->getFont()->setBold(true)->setSize(9);
$objWorkSheet->getStyle('J'.$merge_row)->applyFromArray($textAlignCenter); // ALIGN CENTER
$objWorkSheet->getStyle("J$startrow:J$merge_row")->getBorders()->getLeft()->applyFromArray($BStyle); // APPLY LEFT BORDER

$objWorkSheet->setCellValue('K'.$startrow, "AMOUNT");
$objWorkSheet->getStyle("K".$startrow)->getFont()->setBold(true)->setSize(9);
$objWorkSheet->getStyle('K'.$startrow)->applyFromArray($textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('K'.$merge_row, "($CurrencyCode)");
$objWorkSheet->getStyle("K".$merge_row)->getFont()->setBold(true)->setSize(9);
$objWorkSheet->getStyle('K'.$merge_row)->applyFromArray($textAlignCenter); // ALIGN CENTER
$objWorkSheet->getStyle("K$startrow:K$merge_row")->getBorders()->getLeft()->applyFromArray($BStyle); // APPLY LEFT BORDER

$startrow++;
$startrow++;
$objWorkSheet->getStyle("A$startrow:K$startrow")->getBorders()->getTop()->applyFromArray($BStyle); // APPLY TOP BORDER

//---------------------------------------------//
//-------------- PO DETAILS INFO --------------//
//---------------------------------------------//

$query_filter = "";
$arr_array    = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_buyerpo  = $arr_array["byBuyerPO"];
$arr_fabric   = $arr_array["byFabric"];
$grand_inv_qty  = $arr_array["grand_inv_qty"];
$grand_inv_nw   = $arr_array["grand_inv_nw"];
$grand_inv_gw   = $arr_array["grand_inv_gw"];

$count_row = 0;
$count_buyerpo = count($arr_buyerpo);

foreach($arr_fabric as $key => $arr_info){
		$count_row += 5;//2;
		for($arr=0;$arr<count($arr_info);$arr++){
			$arr_FOB   = $arr_info[$arr]["arr_FOB"];
			$uom       = $arr_info[$arr]["uom"];
			
			foreach($arr_FOB as $fob_price => $arr_price){
				$count_row++;
				
			}//--- End Foreach FOB ---//
			$count_row+=2;
		}//--- END FOR ---//
	}//--- End Foreach Fabric ---//
	

//-----------------------------------------//
//--------- CTNS MARKS & NUMBER -----------//
//-----------------------------------------//
$arr_mark = explode(" ", $shippingmarks);
$str_1  = $arr_mark[0]." ".$arr_mark[1];
$str_2  = $arr_mark[2]." ".$arr_mark[3]." ".$arr_mark[4];
$str_3  = $arr_mark[5]." ".$arr_mark[6]." ".$arr_mark[7]." "; 
$str_4  = $arr_mark[8]." ".$arr_mark[9]." ".$arr_mark[10]." ".$arr_mark[11]; 
$str_5  = $arr_mark[12]." ".$arr_mark[13]." "; 
$str_6  = $arr_mark[14]." ".$arr_mark[15]." ".$arr_mark[16]." ".$arr_mark[17]." ".$arr_mark[18]." ".$arr_mark[19]; 
// $str_7  = $arr_mark[20]." ".$arr_mark[21]." ".$arr_mark[22]; //made in cambodia
// $str_8  = $arr_mark[23]." ".$arr_mark[24]; //NW
// $str_9  = $arr_mark[25]." ".$arr_mark[26]; //GW
// $str_10 = $arr_mark[27]." ".$arr_mark[28]; //Carton Dimensions

$mark_row = $startrow;
$mark_row++;
$objWorkSheet->setCellValue('A'.$mark_row, "$str_1");//conName
$arr_csn_addr = explode(",", $conAddress);
if(count($arr_csn_addr)>0){
	// for($arr=0;$arr<count($arr_csn_addr);$arr+=2){
		// $this_a = $arr+1;
		// if(array_key_exists($this_a, $arr_csn_addr)){
			// $str_address = $arr_csn_addr[$arr].",".$arr_csn_addr[$this_a];
		// }
		// else{
			// $str_address = $arr_csn_addr[$arr];
		// }
		
		// $mark_row++;
		// $objWorkSheet ->setCellValue('A'.$mark_row, trim($str_address));
	// }
	
	$mark_row++;
	$objWorkSheet ->setCellValue('A'.$mark_row, trim($str_2));
	$mark_row++;
	$objWorkSheet ->setCellValue('A'.$mark_row, trim($str_3));
	$objWorkSheet->getRowDimension(''.$mark_row)->setRowHeight(33);
	$objWorkSheet ->getStyle('A'.$mark_row)->getAlignment()->setWrapText(true);
	
}
else{
	$mark_row++;
}
// $mark_row++;
// $mark_row++;
// $objWorkSheet->setCellValue('A'.$mark_row, "$manufacturer");
// $mark_row++;
// $objWorkSheet->setCellValue('A'.$mark_row, "Made In $manucountry");
// $mark_row++;
// $objWorkSheet->setCellValue('A'.$mark_row, "NW: $grand_inv_nw KGS");
// $mark_row++;
// $objWorkSheet->setCellValue('A'.$mark_row, "GW: $grand_inv_gw KGS");
// $mark_row++;
// $objWorkSheet->setCellValue('A'.$mark_row, "QTY: $grand_inv_qty");

$mark_row++; 
$objWorkSheet->setCellValue('A'.$mark_row, "$str_4");
$objWorkSheet->getRowDimension(''.$mark_row)->setRowHeight(72);
$objWorkSheet ->getStyle('A'.$mark_row)->getAlignment()->setWrapText(true);
$mark_row++;
$objWorkSheet->setCellValue('A'.$mark_row, "$str_5");
$objWorkSheet->getRowDimension(''.$mark_row)->setRowHeight(58);
$objWorkSheet ->getStyle('A'.$mark_row)->getAlignment()->setWrapText(true);
$mark_row++;
$objWorkSheet->setCellValue('A'.$mark_row, "$str_6");
$objWorkSheet->getRowDimension(''.$mark_row)->setRowHeight(75);
$objWorkSheet ->getStyle('A'.$mark_row)->getAlignment()->setWrapText(true);

//-----------------------------------------//
//--------- Details Left Border -----------//
//-----------------------------------------//

$merge_row = $startrow + $count_row + 1;
$merge_row = ($mark_row>$merge_row? $mark_row: $merge_row);
$objWorkSheet->getStyle("C$startrow:C$merge_row")->getBorders()->getLeft()->applyFromArray($BStyle); // APPLY LEFT BORDER
$objWorkSheet->getStyle("D$startrow:D$merge_row")->getBorders()->getLeft()->applyFromArray($BStyle); // APPLY LEFT BORDER
$objWorkSheet->getStyle("I$startrow:I$merge_row")->getBorders()->getLeft()->applyFromArray($BStyle); // APPLY LEFT BORDER
$objWorkSheet->getStyle("J$startrow:J$merge_row")->getBorders()->getLeft()->applyFromArray($BStyle); // APPLY LEFT BORDER
$objWorkSheet->getStyle("K$startrow:K$merge_row")->getBorders()->getLeft()->applyFromArray($BStyle); // APPLY LEFT BORDER

//-----------------------------------------//
//--------- Details PO Display ------------//
//-----------------------------------------//
$grand_ctn = 0;
$grand_qty = 0;
$grand_amt = 0;
foreach($arr_fabric as $key => $arr_info){
	$startrow++;
	$objWorkSheet->setCellValue('D'.$startrow, "$key");
	$objWorkSheet->getStyle("D".$startrow)->getFont()->setBold(true)->setSize(9);
	$startrow++;
	$objWorkSheet->setCellValue('D'.$startrow, "PO#");
	$objWorkSheet->setCellValue('E'.$startrow, "STYLE#");
	
	for($arr=0;$arr<count($arr_info);$arr++){
		$arr_FOB   = $arr_info[$arr]["arr_FOB"];
		$BuyerPO   = $arr_info[$arr]["BuyerPO"];
		$spID      = $arr_info[$arr]["shipmentpriceID"];
		$styleNo   = $arr_info[$arr]["styleNo"];
		$total_ctn = $arr_info[$arr]["total_ctn"];
		$ht_code   = $arr_info[$arr]["ht_code"];
		$quotacat  = $arr_info[$arr]["quotacat"];
		
		$sqlbih = "SELECT qc.Description as quota_cate, bih.ht_code  
					FROM `tblbuyer_invoice_payment_hts` bih
					INNER JOIN tblquotacat qc ON qc.ID = bih.quotaID
					WHERE bih.shipmentpriceID = '$spID' AND bih.invID='$invID'";
		$stmt_inv = $conn->prepare($sqlbih);
		$stmt_inv->execute();
		$row_inv = $stmt_inv->fetch(PDO::FETCH_ASSOC);
			$quotacat     = $row_inv["quota_cate"];
			$ht_code      = $row_inv["ht_code"];
		
		$grand_ctn += $total_ctn;
		
		$startrow++;
		$objWorkSheet->setCellValue('C'.$startrow, "$total_ctn");
		$objWorkSheet->getStyle('C'.$startrow)->applyFromArray($textAlignCenter); // ALIGN CENTER
		$objWorkSheet->setCellValue('D'.$startrow, "$BuyerPO");
		$objWorkSheet->setCellValue('E'.$startrow, "$styleNo");
		
		$inforow = $startrow+1;
		$objWorkSheet->setCellValue('D'.$inforow, "HTS: $ht_code");
		$inforow++;
		$objWorkSheet->setCellValue('D'.$inforow, "CAT: $quotacat");
		
		foreach($arr_FOB as $fob_price => $arr_price){
			$this_fob  = substr($fob_price,1);
			$this_qty  = $arr_price["qty"];
			$this_amt  = $this_qty * $this_fob;
			
			$grand_qty += $this_qty;
			$grand_amt += $this_amt;
			
			$objWorkSheet->setCellValue('I'.$startrow, "$this_qty");
			$objWorkSheet->getStyle('I'.$startrow)->applyFromArray($textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('J'.$startrow, "$this_fob");
			$objWorkSheet->getStyle('J'.$startrow)->applyFromArray($textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('K'.$startrow, "$this_amt");
			$objWorkSheet->getStyle('K'.$startrow)->applyFromArray($textAlignCenter); // ALIGN CENTER
			
			$startrow++;
		}//-- End Foreach --//
		$startrow+=2;
	}//--- End Arr Info ---//
}//--- End Foreach ---//

$startrow = $merge_row;
$objWorkSheet->setCellValue('A'.$startrow, "TOTAL CTNS");
$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($textAlignRight); // ALIGN RIGHT
$objWorkSheet->setCellValue('C'.$startrow, "$grand_ctn");
$objWorkSheet->getStyle('C'.$startrow)->applyFromArray($textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('H'.$startrow, "TOTAL");
$objWorkSheet->getStyle('H'.$startrow)->applyFromArray($textAlignRight); // ALIGN RIGHT
$objWorkSheet->setCellValue('I'.$startrow, "$grand_qty");
$objWorkSheet->getStyle('I'.$startrow)->applyFromArray($textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('K'.$startrow, "$grand_amt");
$objWorkSheet->getStyle('K'.$startrow)->applyFromArray($textAlignCenter); // ALIGN CENTER
$objWorkSheet->getStyle("A$startrow:K$startrow")->getBorders()->getTop()->applyFromArray($BStyle); // APPLY TOP BORDER
$objWorkSheet->getStyle("K1:K$startrow")->getBorders()->getRight()->applyFromArray($BStyle); // APPLY RIGHT BORDER

$startrow++;
$objWorkSheet->getStyle("A$startrow:K$startrow")->getBorders()->getTop()->applyFromArray($BStyle); // APPLY TOP BORDER

$startrow++;
$objWorkSheet->setCellValue('A'.$startrow, "MANUFACTURER'S NAME & ADDRESS:");
$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);
$objWorkSheet->getStyle("A".$startrow)->getFont()->setUnderline(true);
$startrow++;
$objWorkSheet->setCellValue('A'.$startrow, "$manufacturer");
$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);
$arr_csn_addr = explode(",", $manuaddress);
if(count($arr_csn_addr)>0){
	for($arr=0;$arr<count($arr_csn_addr);$arr+=2){
		$this_a = $arr+1;
		if(array_key_exists($this_a, $arr_csn_addr)){
			$str_address = $arr_csn_addr[$arr].",".$arr_csn_addr[$this_a];
		}
		else{
			$str_address = $arr_csn_addr[$arr];
		}
		
		$startrow++;
		$objWorkSheet ->setCellValue('A'.$startrow, trim($str_address));
	}
	$startrow++;
		$objWorkSheet ->setCellValue('A'.$startrow, "TEL: $manutel");
}
else{
	$startrow++;
}

$objWorkSheet->setTitle("Inv");

////////////////////////////////////////////////////
//------------------ Next Tab --------------------//
////////////////////////////////////////////////////
// $i++;
// $objWorkSheet = $sheet->createSheet($i);
// $objWorkSheet->setCellValue('A1', "COUNTRY OF ORIGIN");
// $objWorkSheet->setTitle("Pick 1");
$excel_class->getBuyerInvoicePackingListTemplate($invID, $i, $sheet);

//=========== Generate excel file ==============//
if($acctid==0){
	$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'HTML');
}
else{
$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');

ob_end_clean();
    
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$invoice_no.'-Inv&PL.xlsx"');
header('Cache-Control: max-age=0');  

}
$objWriter->save('php://output');
exit;

//-----------------------------//
//-------- Excel Class --------//
class excelClass{

private $conn = ""; private $lang = "EN"; private $buyerID = ""; private $acctid = "0"; private $handle_shipment=""; 
private $from_location = "";//-- Empty represent as Buyer PO --//
public $isBuyerPayment = "";
public $textAlignCenter;
public $textAlignRight;
public $BStyle;
public $handle_lc;
public $column;

private $filter = ""; // ------------------- SQL query filter ---------------------------//

public function setConn($conn){
    $this->conn = $conn;
}

public function setFilter($filter){
    $this->filter=$filter;
}

public function setShipment($handle_shipment){
    $this->handle_shipment=$handle_shipment;
}

public function __construct(){
	 //include("../../../phpexcel/Classes/PHPExcel.php");
}

public function getBuyerInvoicePackingListTemplate($invID, $i, $sheet){
	
		$tblbuyer_invoice        = ($this->isBuyerPayment==0? "tblbuyer_invoice": "tblbuyer_invoice_payment");
		$tblbuyer_invoice_detail = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail": "tblbuyer_invoice_payment_detail");
		$sql = "SELECT bi.invoice_no, bi.invoice_date, bi.shippeddate, invd.shipmentpriceID, invd.ht_code, invd.shipping_marking, invd.BICID,
						group_concat(distinct g.styleNo) as styleNo, g.orderno, sp.BuyerPO, csn.Name as csn_name, 
						csn.Address as csn_address, bi.container_no, bi.vesselname,
						fty.FactoryName_ENG as ownership, fty.Address as owneraddress, fty.Tel as ownertel, fty.Fax as ownerfax,
						lch.lc_number, od.FactoryID as od_FactoryID,
						fty2.FactoryName_ENG as exporter, bi.shipper_address as exporter_address, fty.Tel as exporter_tel, fty.Fax as exporter_fax,
						sm.Description as shipmode, cty.Description as manucountry, bi.ship_to, bi.ship_address,
						ftym.FactoryName_ENG as manufacturer, dp.countryID as destport_countryID, dp.Description as portDischarges,
						bdt.Description as buyerdestination, lp.Description as portLoading
						
				FROM $tblbuyer_invoice_detail invd 
				INNER JOIN $tblbuyer_invoice bi ON bi.ID = invd.invID
				LEFT JOIN tblshipmentprice sp ON sp.ID = invd.shipmentpriceID 
				LEFT JOIN tblconsignee csn ON csn.ConsigneeID = sp.ConsigneeID
				LEFT JOIN tblgarment g ON g.orderno = sp.Orderno
				LEFT JOIN tblorder od ON od.Orderno = sp.Orderno
				LEFT JOIN tblfactory fty ON fty.FactoryID = od.contract_owner
				LEFT JOIN tblfactory fty2 ON fty2.FactoryID = bi.shipper
				LEFT JOIN tblfactory ftym ON ftym.FactoryID = od.manufacturer
				LEFT JOIN tblcountry cty ON cty.ID = ftym.countryID
				LEFT JOIN tbllc_assignment_detail lcd ON lcd.shipmentpriceID = invd.shipmentpriceID AND lcd.del=0 AND invd.del=0
				LEFT JOIN tbllc_assignment_info lci ON lci.LCIID = lcd.LCIID AND lci.del=0
				LEFT JOIN tbllc_assignment_head lch ON lch.LCHID = lci.LCHID
				LEFT JOIN tblshipmode sm ON sm.ID = bi.shipmodeID 
				LEFT JOIN tbldestinationport dp ON dp.ID = bi.PortDestID
				LEFT JOIN tblbuyerdestination bdt ON bdt.ID = bi.BuyerDestID
				LEFT JOIN tblloadingport lp ON lp.ID = bi.portLoadingID 
				WHERE invd.invID = '$invID' AND invd.del = 0 AND invd.group_number>0
				GROUP BY invd.shipmentpriceID 
				ORDER BY invd.ID ASC ";
		$packsql = $this->conn->prepare($sql);
		$packsql->execute(); 
		while($packrow = $packsql->fetch(PDO::FETCH_ASSOC)){
			extract($packrow);
			$i++;
			$objWorkSheet = $sheet->createSheet($i);
			$objWorkSheet ->getColumnDimension('A')->setWidth(18);
			$objWorkSheet ->getColumnDimension('B')->setWidth(22);
			$objWorkSheet ->getColumnDimension('C')->setWidth(18);
			$objWorkSheet ->getColumnDimension('D')->setWidth(18);
			$objWorkSheet ->getColumnDimension('E')->setWidth(18);
			$objWorkSheet ->getColumnDimension('J')->setWidth(25);
			
			$this->handle_lc->BICID = $BICID;
			$arr_all = $this->handle_lc->getBuyerInvoicePackingListDataFromCartonInv($shipmentpriceID, $invID);
			$arr_row   = $arr_all["arr_list"];
			$grand_qty = $arr_all["grand_qty"];
			$ctn_qty   = $arr_all["ctn_qty"];
			$grand_nw  = $arr_all["grand_nw"];
			$grand_gw  = $arr_all["grand_gw"];
			$grand_cbm = $arr_all["grand_cbm"];
			$count_color_in_grp = $arr_all["count_color_in_grp"];
			$arr_group_number   = $arr_all["arr_group_number"];
			$arr_all_size_ctn   = $arr_all["arr_all_size_ctn"];
			$arr_all_color_ctn  = $arr_all["arr_all_color_ctn"];
			$str_unit = ($count_color_in_grp==1? "PCS":"SETS");
			
			$startrow = 1;
			$merge_row = $startrow + 1;
			$objWorkSheet->mergeCells("G$startrow:N$merge_row");
			$objWorkSheet->setCellValue('G'.$startrow, "Noble Rider, LLC");
			$objWorkSheet->getStyle('G'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->getStyle("G".$startrow)->getFont()->setBold(true)->setSize(24);
			
			$startrow++;
			$objWorkSheet->setCellValue('A'.$startrow, "FACTORY:");
			$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('B'.$startrow, "$manufacturer");
			
			$startrow++;
			$objWorkSheet->getStyle("B$startrow:E$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			
			$startrow++;
			$objWorkSheet->getRowDimension(''.$startrow)->setRowHeight(18);
			$objWorkSheet->setCellValue('A'.$startrow, "Invoice #:");
			$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('B'.$startrow, "$invoice_no");
			$objWorkSheet->setCellValue('C'.$startrow, "Invoice Date:");
			$objWorkSheet->getStyle("C".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->mergeCells("D$startrow:E$startrow");
			$objWorkSheet->setCellValue('D'.$startrow, "$invoice_date");
			$objWorkSheet->getStyle('D'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			
			$startrow++;
			$objWorkSheet->getRowDimension(''.$startrow)->setRowHeight(18);
			$objWorkSheet->getStyle("B$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getStyle("D$startrow:E$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->setCellValue('A'.$startrow, "Feeder Vessel:");
			$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('B'.$startrow, "$vesselname"); // Feeder Vessel
			$objWorkSheet->mergeCells("G$startrow:H$startrow");
			$objWorkSheet->setCellValue('G'.$startrow, "Mother Vessel:");
			$objWorkSheet->getStyle("G".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->mergeCells("I$startrow:N$startrow");
			$objWorkSheet->setCellValue('I'.$startrow, ""); // Mother Vessel
			$objWorkSheet->mergeCells("O$startrow:Q$startrow");
			$objWorkSheet->setCellValue('O'.$startrow, "Approximate Sailing Date:");
			$objWorkSheet->getStyle("O".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->mergeCells("R$startrow:S$startrow");
			$objWorkSheet->setCellValue('R'.$startrow, "$shippeddate"); // Approveximate Sailing Date
			
			$startrow++;
			$objWorkSheet->getStyle("B$startrow:F$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getStyle("I$startrow:N$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getStyle("R$startrow:S$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getRowDimension(''.$startrow)->setRowHeight(18);
			$objWorkSheet->setCellValue('A'.$startrow, "From:");
			$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->mergeCells("B$startrow:F$startrow");
			$objWorkSheet->setCellValue('B'.$startrow, "$portLoading"); //Port Loading
			$objWorkSheet->mergeCells("K$startrow:L$startrow");
			$objWorkSheet->setCellValue('K'.$startrow, "Ship To Port:");
			$objWorkSheet->getStyle("K".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->mergeCells("M$startrow:S$startrow");
			$objWorkSheet->setCellValue('M'.$startrow, "$buyerdestination");//Buyer Destination 
			
			$startrow++;
			$objWorkSheet->getStyle("B$startrow:F$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getStyle("M$startrow:S$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getRowDimension(''.$startrow)->setRowHeight(18);
			$objWorkSheet->setCellValue('A'.$startrow, "Container:");
			$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->mergeCells("B$startrow:F$startrow");
			$objWorkSheet->setCellValue('B'.$startrow, "$container_no");
			
			$startrow++;
			$objWorkSheet->getStyle("B$startrow:F$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getRowDimension(''.$startrow)->setRowHeight(18);
			$objWorkSheet->mergeCells("C$startrow:D$startrow");
			$objWorkSheet->setCellValue('C'.$startrow, "Total Ctns on Pack List:");
			$objWorkSheet->getStyle("C".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('C'.$startrow)->applyFromArray($this->textAlignRight); // ALIGN RIGHT
			$objWorkSheet->mergeCells("E$startrow:F$startrow");
			$objWorkSheet->setCellValue('E'.$startrow, "$ctn_qty");
			$objWorkSheet->getStyle('E'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->getStyle("E$startrow")->getBorders()->getLeft()->applyFromArray($this->BStyle); // APPLY LEFT BORDER
			$objWorkSheet->getStyle("F$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->setCellValue('J'.$startrow, "Total Units on Pack List:");
			$objWorkSheet->getStyle("J".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->setCellValue('K'.$startrow, "$grand_qty");
			$objWorkSheet->setCellValue('L'.$startrow, "$str_unit");
			$objWorkSheet->getStyle("K$startrow")->getBorders()->getLeft()->applyFromArray($this->BStyle); // APPLY LEFT BORDER
			$objWorkSheet->getStyle("L$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("K$startrow:L$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->mergeCells("N$startrow:P$startrow");
			$objWorkSheet->setCellValue('N'.$startrow, "Booked to System By / Date:");
			$objWorkSheet->getStyle("N".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle("Q$startrow")->getBorders()->getLeft()->applyFromArray($this->BStyle); // APPLY LEFT BORDER
			$objWorkSheet->getStyle("S$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("Q$startrow:S$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			
			$startrow++;
			$objWorkSheet->getStyle("E$startrow:F$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getRowDimension(''.$startrow)->setRowHeight(18);
			$objWorkSheet->mergeCells("C$startrow:D$startrow");
			$objWorkSheet->setCellValue('C'.$startrow, "Total Ctns Received:");
			$objWorkSheet->getStyle("C".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('C'.$startrow)->applyFromArray($this->textAlignRight); // ALIGN RIGHT
			$objWorkSheet->mergeCells("E$startrow:F$startrow");
			$objWorkSheet->setCellValue('E'.$startrow, "");
			$objWorkSheet->getStyle('E'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->getStyle("E$startrow")->getBorders()->getLeft()->applyFromArray($this->BStyle); // APPLY LEFT BORDER
			$objWorkSheet->getStyle("F$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->setCellValue('J'.$startrow, "Total Units Received:");
			$objWorkSheet->getStyle("J".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->setCellValue('K'.$startrow, "");
			$objWorkSheet->setCellValue('L'.$startrow, "");
			$objWorkSheet->getStyle("K$startrow")->getBorders()->getLeft()->applyFromArray($this->BStyle); // APPLY LEFT BORDER
			$objWorkSheet->getStyle("L$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("K$startrow:L$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->mergeCells("N$startrow:P$startrow");
			$objWorkSheet->setCellValue('N'.$startrow, "Cut Closed By/Date:");
			$objWorkSheet->getStyle("N".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle("Q$startrow")->getBorders()->getLeft()->applyFromArray($this->BStyle); // APPLY LEFT BORDER
			$objWorkSheet->getStyle("S$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("Q$startrow:S$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			
			$startrow++;
			$objWorkSheet->getStyle("E$startrow:F$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getRowDimension(''.$startrow)->setRowHeight(18);
			$objWorkSheet->mergeCells("C$startrow:D$startrow");
			$objWorkSheet->setCellValue('C'.$startrow, "Date Arrived in Whse:");
			$objWorkSheet->getStyle("C".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('C'.$startrow)->applyFromArray($this->textAlignRight); // ALIGN RIGHT
			$objWorkSheet->mergeCells("E$startrow:F$startrow");
			$objWorkSheet->setCellValue('E'.$startrow, "");
			$objWorkSheet->getStyle('E'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->getStyle("E$startrow")->getBorders()->getLeft()->applyFromArray($this->BStyle); // APPLY LEFT BORDER
			$objWorkSheet->getStyle("F$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->setCellValue('J'.$startrow, "QA Exam:");
			$objWorkSheet->getStyle("J".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->setCellValue('K'.$startrow, "");
			$objWorkSheet->setCellValue('L'.$startrow, "");
			$objWorkSheet->getStyle("K$startrow")->getBorders()->getLeft()->applyFromArray($this->BStyle); // APPLY LEFT BORDER
			$objWorkSheet->getStyle("L$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("K$startrow:L$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->mergeCells("N$startrow:P$startrow");
			$objWorkSheet->setCellValue('N'.$startrow, "Posted to intransit By/Date:");
			$objWorkSheet->getStyle("N".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle("Q$startrow")->getBorders()->getLeft()->applyFromArray($this->BStyle); // APPLY LEFT BORDER
			$objWorkSheet->getStyle("S$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("Q$startrow:S$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			
			$startrow++;
			$objWorkSheet->getStyle("E$startrow:F$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getRowDimension(''.$startrow)->setRowHeight(18);
			$objWorkSheet->mergeCells("C$startrow:D$startrow");
			$objWorkSheet->setCellValue('C'.$startrow, "Turned in By/Date:");
			$objWorkSheet->getStyle("C".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('C'.$startrow)->applyFromArray($this->textAlignRight); // ALIGN RIGHT
			$objWorkSheet->mergeCells("E$startrow:F$startrow");
			$objWorkSheet->setCellValue('E'.$startrow, "");
			$objWorkSheet->getStyle('E'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->getStyle("E$startrow")->getBorders()->getLeft()->applyFromArray($this->BStyle); // APPLY LEFT BORDER
			$objWorkSheet->getStyle("F$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->setCellValue('J'.$startrow, "QA Exam Qty:");
			$objWorkSheet->getStyle("J".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->setCellValue('K'.$startrow, "");
			$objWorkSheet->setCellValue('L'.$startrow, "");
			$objWorkSheet->getStyle("K$startrow")->getBorders()->getLeft()->applyFromArray($this->BStyle); // APPLY LEFT BORDER
			$objWorkSheet->getStyle("L$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("K$startrow:L$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->mergeCells("N$startrow:P$startrow");
			$objWorkSheet->setCellValue('N'.$startrow, "Any Problem:");
			$objWorkSheet->getStyle("N".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->setCellValue('Q'.$startrow, "YES/NO");
			$objWorkSheet->getStyle("Q$startrow")->getBorders()->getLeft()->applyFromArray($this->BStyle); // APPLY LEFT BORDER
			$objWorkSheet->getStyle("S$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("Q$startrow:S$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			
			$startrow++;
			$objWorkSheet->getStyle("E$startrow:F$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getStyle("K$startrow:L$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getStyle("Q$startrow:S$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->setCellValue('A'.$startrow, "PO #:");
			$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('B'.$startrow, "$BuyerPO");
			$objWorkSheet->getStyle('B'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			
			$startrow++;
			$objWorkSheet->getStyle("B$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->setCellValue('A'.$startrow, "Style #:");
			$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('B'.$startrow, "$styleNo");
			$objWorkSheet->getStyle('B'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('C'.$startrow, "Style Descr:");
			$objWorkSheet->getStyle("C".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->mergeCells("D$startrow:J$startrow");
			$objWorkSheet->setCellValue('D'.$startrow, "$shipping_marking");
			$objWorkSheet->getStyle('D'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->mergeCells("O$startrow:P$startrow");
			$objWorkSheet->setCellValue('O'.$startrow, "Checked By / Date:");
			$objWorkSheet->getStyle('O'.$startrow)->applyFromArray($this->textAlignRight); // ALIGN RIGHT
			
			$startrow++;
			$objWorkSheet->getStyle("B$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getStyle("D$startrow:J$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getStyle("Q$startrow:S$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->setCellValue('E'.$startrow, "Total");
			$objWorkSheet->getStyle("E".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('E'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('F'.$startrow, "$grand_qty");
			$objWorkSheet->getStyle('F'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('G'.$startrow, "$str_unit");
			$objWorkSheet->getStyle('G'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			
			$startrow++;
			$objWorkSheet->getStyle("A$startrow:M$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$objWorkSheet->getStyle("A$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("B$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("C$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("D$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("E$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("F$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("G$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("H$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->getStyle("M$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
			$objWorkSheet->setCellValue('A'.$startrow, "Carton#/UCC 128");
			$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('B'.$startrow, "CLR CODE#");
			$objWorkSheet->getStyle("B".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('B'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('C'.$startrow, "Style");
			$objWorkSheet->getStyle("C".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('C'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('D'.$startrow, "SIZE");
			$objWorkSheet->getStyle("D".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('D'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('E'.$startrow, "Item UPC");
			$objWorkSheet->getStyle("E".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('E'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('F'.$startrow, "Item Qty");
			$objWorkSheet->getStyle("F".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('F'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('G'.$startrow, "N.W LBS");
			$objWorkSheet->getStyle("G".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('G'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('H'.$startrow, "G.W LBS");
			$objWorkSheet->getStyle("H".$startrow)->getFont()->setBold(true)->setSize(11);
			$objWorkSheet->getStyle('H'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
			$objWorkSheet->setCellValue('I'.$startrow, "Comments");
			$objWorkSheet->getStyle("I".$startrow)->getFont()->setBold(true)->setSize(11);
			
			$startrow++;
			$objWorkSheet->getStyle("A$startrow:M$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
			$ext_length   = "0";
			$ext_width    = "0";
			$ext_height   = "0";
			$grand_nw_lbs = "0";
			$grand_gw_lbs = "0";
			
			for($n=0;$n<count($arr_row);$n++){
				$start        = $arr_row[$n]["start"];
				$end_num      = $arr_row[$n]["end_num"];
				$total_ctn    = $arr_row[$n]["total_ctn"];
				$mixID        = $arr_row[$n]["mixID"];
				$SKU          = $arr_row[$n]["SKU"];
				$prepack_name = $arr_row[$n]["prepack_name"];
				$net_weight   = round($arr_row[$n]["net_weight"] * 2.20462262, 2);
				$gross_weight = round($arr_row[$n]["gross_weight"] * 2.20462262, 2);
				$total_qty_in_carton = $arr_row[$n]["total_qty_in_carton"];
				$ext_length = $arr_row[$n]["ext_length"];
				$ext_width  = $arr_row[$n]["ext_width"];
				$ext_height = $arr_row[$n]["ext_height"];
				$total_CBM  = $arr_row[$n]["total_CBM"];
				
				
				$arr_mix = explode("::^^", $mixID);
				$color   = "Ratio"; $size = ""; $garment = "";
				if(count($arr_mix)>0){
					list($group_number, $size, $this_qty) = explode("**%%", $arr_mix[0]);
					$color   = $arr_group_number["G$group_number"]["colorOnly"];
					$garment = $arr_group_number["G$group_number"]["garmentOnly"];
				}
				
				for($ctn=1;$ctn<=$total_ctn;$ctn++){
					$grand_nw_lbs += $net_weight;
					$grand_gw_lbs += $gross_weight;
					
					$objWorkSheet->getStyle("A$startrow:M$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
					$objWorkSheet->getStyle("A$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
					$objWorkSheet->getStyle("B$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
					$objWorkSheet->getStyle("C$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
					$objWorkSheet->getStyle("D$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
					$objWorkSheet->getStyle("E$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
					$objWorkSheet->getStyle("F$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
					$objWorkSheet->getStyle("G$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
					$objWorkSheet->getStyle("H$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
					$objWorkSheet->getStyle("M$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
					$objWorkSheet->setCellValue('A'.$startrow, "");
					$objWorkSheet->getStyle('A'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
					$objWorkSheet->setCellValue('B'.$startrow, "$color");
					$objWorkSheet->getStyle('B'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
					$objWorkSheet->setCellValue('C'.$startrow, "$garment");
					$objWorkSheet->getStyle('C'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
					$objWorkSheet->setCellValue('D'.$startrow, "$size");
					$objWorkSheet->getStyle('D'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
					$objWorkSheet->setCellValue('E'.$startrow, "$prepack_name");
					$objWorkSheet->getStyle('E'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
					$objWorkSheet->setCellValue('F'.$startrow, "$total_qty_in_carton");
					$objWorkSheet->getStyle('F'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
					$objWorkSheet->setCellValue('G'.$startrow, "$net_weight");
					$objWorkSheet->getStyle('G'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
					$objWorkSheet->setCellValue('H'.$startrow, "$gross_weight");
					$objWorkSheet->getStyle('H'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
					
					$startrow++;
				}
				
			}//--- End For ---//
			
			$grand_qty = $arr_all["grand_qty"];
			$ctn_qty   = $arr_all["ctn_qty"];
			$grand_cbm = $arr_all["grand_cbm"];
			
$objWorkSheet->getStyle("A$startrow:M$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
$objWorkSheet->getStyle("A$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("B$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("C$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("D$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("E$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("F$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("G$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("H$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("M$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->setCellValue('A'.$startrow, "Carton Dimensions:");	
$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->setCellValue('C'.$startrow, "TOTAL CTNS");	
$objWorkSheet->getStyle("C".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('C'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('D'.$startrow, "$ctn_qty");	
$objWorkSheet->getStyle("D".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('D'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('F'.$startrow, "$grand_qty");	
$objWorkSheet->getStyle("F".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('F'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('G'.$startrow, "$grand_nw_lbs");	
$objWorkSheet->getStyle("G".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('G'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('H'.$startrow, "$grand_gw_lbs");	
$objWorkSheet->getStyle("H".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('H'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('J'.$startrow, "Total Measurement:");	
$objWorkSheet->getStyle("J".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('J'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->mergeCells("K$startrow:L$startrow");
$objWorkSheet->setCellValue('K'.$startrow, "$grand_cbm");	
$objWorkSheet->getStyle("K".$startrow)->getFont()->setBold(true)->setSize(11);
$objWorkSheet->getStyle('K'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('M'.$startrow, "CBM");
$objWorkSheet->getStyle("M".$startrow)->getFont()->setBold(true)->setSize(11);

$startrow++;
$objWorkSheet->getStyle("A$startrow:M$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
$objWorkSheet->getStyle("A$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("B$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("C$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("D$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("I$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("J$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("L$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("M$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->setCellValue('B'.$startrow, "CM");
$objWorkSheet->getStyle("B".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('B'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('D'.$startrow, "CTNS");
$objWorkSheet->getStyle("D".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('D'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('J'.$startrow, "Total N.W:");
$objWorkSheet->getStyle("J".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('J'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->mergeCells("K$startrow:L$startrow");
$objWorkSheet->setCellValue('K'.$startrow, "$grand_nw_lbs");
$objWorkSheet->getStyle("K".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('K'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('M'.$startrow, "LBS");
$objWorkSheet->getStyle("M".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('M'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER

$startrow++;
$objWorkSheet->getStyle("A$startrow:D$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
$objWorkSheet->getStyle("J$startrow:M$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
$objWorkSheet->getStyle("A$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("B$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("C$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("D$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("I$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("J$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("L$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("M$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->setCellValue('B'.$startrow, "$ext_length x $ext_width x $ext_height");
$objWorkSheet->getStyle("B".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('B'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('D'.$startrow, "$ctn_qty");
$objWorkSheet->getStyle("D".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('D'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('J'.$startrow, "Total G.W:");
$objWorkSheet->getStyle("J".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('J'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->mergeCells("K$startrow:L$startrow");
$objWorkSheet->setCellValue('K'.$startrow, "$grand_gw_lbs");
$objWorkSheet->getStyle("K".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('K'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER
$objWorkSheet->setCellValue('M'.$startrow, "LBS");
$objWorkSheet->getStyle("M".$startrow)->getFont()->setBold(true)->setSize(11);	
$objWorkSheet->getStyle('M'.$startrow)->applyFromArray($this->textAlignCenter); // ALIGN CENTER

$startrow++;
$objWorkSheet->getStyle("A$startrow:D$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
$objWorkSheet->getStyle("J$startrow:M$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER

$startrow++;
$objWorkSheet->setCellValue('A'.$startrow, "Color & Size Assortment");
$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);

$startrow++;
$arrsize = [];
$arrsizeqty = [];
$objWorkSheet->getStyle("A$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("B$startrow")->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->setCellValue('A'.$startrow, "CLR CODE #/ NAME");
$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);
$objWorkSheet->setCellValue('B'.$startrow, "");
$sizesql = $this->handle_shipment->getSizeNameColumnFromOrder($orderno, 1);
$col=2;
while($sizerow = $sizesql->fetch(PDO::FETCH_ASSOC)){
	$size_name = $sizerow["SizeName"];
	
	$sqlscsq  = "SELECT sum(scsq.qty) as qty 
							FROM tblship_colorsizeqty scsq 
							WHERE scsq.shipmentpriceID='$shipmentpriceID' and scsq.size_name='$size_name' 
							AND scsq.statusID=1 ";
	$stmt_scsq = $this->conn->prepare($sqlscsq);
	$stmt_scsq->execute();
	$row_scsq = $stmt_scsq->fetch(PDO::FETCH_ASSOC);
		$this_qty = $row_scsq["qty"];
	
	if($this_qty>0){
		$arrsize[] = $size_name;
		$objWorkSheet->setCellValue($this->column[$col].$startrow, "$size_name");
		$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);		
		$objWorkSheet->getStyle($this->column[$col].$startrow)->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
		$col++;
	}
}//--- End While ---//

$objWorkSheet->setCellValue($this->column[$col].$startrow, "TOTAL");
$objWorkSheet->getStyle($this->column[$col].$startrow)->getFont()->setBold(true)->setSize(11);
$objWorkSheet->getStyle($this->column[$col].$startrow)->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER

$col_size_count = count($arrsize) + 2;
$objWorkSheet->getStyle("A$startrow:".$this->column[$col_size_count]."$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER

$startrow++;
$objWorkSheet->getStyle("A$startrow:".$this->column[$col_size_count]."$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
foreach($arr_all_color_ctn as $group_number => $ctn_number){
	$color   = $arr_group_number["G$group_number"]["colorOnly"];
	$objWorkSheet->setCellValue("A".$startrow, "$color");
	$objWorkSheet->getStyle("A".$startrow)->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
	$objWorkSheet->getStyle("B".$startrow)->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
	
	$col = 1;
	$sub_qty = 0;
	for($s=0;$s<count($arrsize);$s++){
		$this_size = $arrsize[$s];
		$this_qty = $arr_all_size_ctn["$group_number**^^$this_size"]["qty"];
		$col++;
		$objWorkSheet->setCellValue($this->column[$col].$startrow, "$this_qty");
		$objWorkSheet->getStyle($this->column[$col].$startrow)->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
		$sub_qty += $this_qty;
		$arrsizeqty["size".$this_size] += $this_qty;
	}
	$col++;
	$objWorkSheet->setCellValue($this->column[$col].$startrow, "$sub_qty");
	$objWorkSheet->getStyle($this->column[$col].$startrow)->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
	
	$startrow++;
	$objWorkSheet->getStyle("A$startrow:".$this->column[$col_size_count]."$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER
}//--- End Foreach ---//

$objWorkSheet->setCellValue('A'.$startrow, "TOTAL");
$objWorkSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(11);		
$objWorkSheet->getStyle("A".$startrow)->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$objWorkSheet->getStyle("B".$startrow)->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
$col = 1;
$sub_qty = 0;
for($s=0;$s<count($arrsize);$s++){
	$this_size = $arrsize[$s];
	$this_qty  = $arrsizeqty["size".$this_size];
	$sub_qty += $this_qty;
	$col++;
	$objWorkSheet->setCellValue($this->column[$col].$startrow, "$this_qty");
	$objWorkSheet->getStyle($this->column[$col].$startrow)->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER
}
$col++;
$objWorkSheet->setCellValue($this->column[$col].$startrow, "$sub_qty");
$objWorkSheet->getStyle($this->column[$col].$startrow)->getBorders()->getRight()->applyFromArray($this->BStyle); // APPLY RIGHT BORDER


$startrow++;
$objWorkSheet->getStyle("A$startrow:".$this->column[$col_size_count]."$startrow")->getBorders()->getTop()->applyFromArray($this->BStyle); // APPLY TOP BORDER

$objWorkSheet->setTitle("$BuyerPO ($orderno)");
			
			
		}//--- End While ---//
}
}//--- End Excel Class ---//

//-------- End Excel Class --------//
//---------------------------------//

?>