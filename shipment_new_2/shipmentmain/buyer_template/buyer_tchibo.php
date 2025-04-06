<?php

include('../../../lock.php');
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE); 
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Asia/Singapore');

if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');

/** Include PHPExcel */
//require_once dirname(__FILE__) . '/../PHPExcel-1.8/Classes/PHPExcel.php';
include("../../../phpexcel/Classes/PHPExcel.php");

function convert_number($number){ 
    if (($number < 0) || ($number > 999999999)) 
    { 
        return "$number"; 
    } 

    $Gn = floor($number / 1000000);  // Millions (giga) / 
    $number -= $Gn * 1000000; 
    $kn = floor($number / 1000);     // Thousands (kilo) / 
    $number -= $kn * 1000; 
    $Hn = floor($number / 100);      // Hundreds (hecto) / 
    $number -= $Hn * 100; 
    $Dn = floor($number / 10);       // Tens (deca) / 
    $n = $number % 10;               // Ones / 

    $res = ""; 

    if ($Gn) 
    { 
        $res .= convert_number($Gn) . " Million"; 
    } 

    if ($kn) 
    { 
        $res .= (empty($res) ? "" : " ") . 
            convert_number($kn) . " Thousand"; 
    } 

    if ($Hn) 
    { 
        $res .= (empty($res) ? "" : " ") . 
            convert_number($Hn) . " Hundred"; 
    } 

    $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
        "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
        "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", 
        "Nineteen"); 
    $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", 
        "Seventy", "Eigthy", "Ninety"); 

    if ($Dn || $n) 
    { 
        if (!empty($res)) 
        { 
    //            $res .= " and "; 
               $res .= " "; 
            } 

        if ($Dn < 2) 
        { 
            $res .= $ones[$Dn * 10 + $n]; 
        } 
        else 
        { 
            $res .= $tens[$Dn]; 

            if ($n) 
            { 
                $res .= "-" . $ones[$n]; 
            } 
        } 
    } 

    if (empty($res)) 
    { 
        $res = "zero"; 
    } 

    return $res; 
}


// Create new PHPExcel object
$sheet = new PHPExcel();
$activeSheet = $sheet->getActiveSheet();

$objRichText = new PHPExcel_RichText();
$objAppendix = new PHPExcel_RichText();
$objPrintedDate = new PHPExcel_RichText();
$objBuyer = new PHPExcel_RichText();
$objStyle = new PHPExcel_RichText();
$objSeason = new PHPExcel_RichText();
$objOrderno = new PHPExcel_RichText();
$objFactory = new PHPExcel_RichText();
$objGrandTotal = new PHPExcel_RichText();

$objRichText2 = new PHPExcel_RichText();

$column = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');

$countColumn = count($column);
for ($x=0; $x<$countColumn; $x++){
    $c = $column[$x];
    $activeSheet ->getColumnDimension($c)->setWidth(15);
}

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
    
    
   $BtStyle =  array(
             'style' => PHPExcel_Style_Border::BORDER_THIN,
             'color' => array(
                 'rgb' => '000000'
             )
         );  

   $BmStyle =  array(
             'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
             'color' => array(
                 'rgb' => '000000'
             )
         );  

   $BdtStyle =  array(
             'style' => PHPExcel_Style_Border::BORDER_DASHED,
             'color' => array(
                 'rgb' => '000000'
             )
         );

    $BdStyle =  array(
             'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
             'color' => array(
                 'rgb' => '000000'
             )
         );    

   $styleArray = array(
    'font'  => array(
        'name'  => 'Times New Roman'
    ));

   $bgBlue = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'DAEEF3')
        )
    );

   $bgRed = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'FCD5B4')
        )
    );
// ==================================== end excel style =================================================================//

$id = $_GET["id"];

$sql_bi="SELECT bi.ConsigneeID,bi.issue_from,bi.invoice_no,bi.invoice_date,bi.portLoadingID,bi.issue_from,f.FactoryCode,f.FactoryName_ENG,f.Tel,f.Fax,f.Address,l.Description as lpDescription 
FROM tblbuyer_invoice bi 
JOIN tblfactory f ON bi.issue_from=f.FactoryID 
JOIN tblloadingport l ON bi.portLoadingID=l.ID 
WHERE bi.ID='$id'";
$result_bi = $conn->prepare($sql_bi);
$result_bi->execute();
$row_bi = $result_bi->fetch(PDO::FETCH_ASSOC);
$ConsigneeID=$row_bi['ConsigneeID'];
$issue_from=$row_bi['issue_from'];
$invoice_no=$row_bi['invoice_no'];
$invoice_date=$row_bi['invoice_date'];
$portLoadingID=$row_bi['portLoadingID'];
$issue_from=$row_bi['issue_from'];

$FactoryCode = $row_bi['FactoryCode'];
$FactoryName_ENG = $row_bi['FactoryName_ENG'];
$Tel = $row_bi['Tel'];
$Fax = $row_bi['Fax'];
$Address = $row_bi['Address'];

$lpDescription = $row_bi['lpDescription'];

// $gdImage = imagecreatefromjpeg('media/img/logo.jpg');
// Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
// $objDrawing->setName('Logo image');$objDrawing->setDescription('Logo image');
// $objDrawing->setImageResource($gdImage);
// $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
// $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
// $objDrawing->setHeight(80);
// $objDrawing->setCoordinates('A1');
$objDrawing->setWorksheet($activeSheet);

$activeSheet->getColumnDimension('A')->setWidth(15);
$activeSheet->getColumnDimension('B')->setWidth(2);
$activeSheet->getColumnDimension('C')->setWidth(15);
$activeSheet->getColumnDimension('D')->setWidth(15);
$activeSheet->getColumnDimension('E')->setWidth(20);
$activeSheet->getColumnDimension('F')->setWidth(15);
$activeSheet->getColumnDimension('G')->setWidth(15);
$activeSheet->getColumnDimension('H')->setWidth(15);
$activeSheet->getColumnDimension('I')->setWidth(15);
$activeSheet->getColumnDimension('J')->setWidth(15);
$activeSheet->getColumnDimension('K')->setWidth(2);
$activeSheet->getColumnDimension('L')->setWidth(23);

$activeSheet->mergeCells("A1:L1"); 
$activeSheet->getCell('A1')->setValue("I APPAREL INTERNATIONAL GROUP PTE LTD");
$activeSheet->getStyle('A1')->applyFromArray($textAlignCenter);
$activeSheet->getStyle('A1')->applyFromArray($styleArray);
$activeSheet->getStyle("A1:L1")->getFont()->setBold(true)->setSize(22);
$activeSheet->getRowDimension('1')->setRowHeight(28);

$activeSheet->mergeCells("A2:L2"); 
$activeSheet->getCell('A2')->setValue("7 KALLANG PLACE#02-08 SINGAPORE 339153");
$activeSheet->getStyle('A2')->applyFromArray($textAlignCenter);
$activeSheet->getStyle('A2')->applyFromArray($styleArray);
$activeSheet->getStyle("A2:L2")->getFont()->setSize(12);

$activeSheet->mergeCells("A3:L3"); 
$activeSheet->getCell('A3')->setValue("TEL & FAX: (65) 6292 9339");
$activeSheet->getStyle('A3')->applyFromArray($textAlignCenter);
$activeSheet->getStyle('A3')->applyFromArray($styleArray);
$activeSheet->getStyle("A3:L3")->getFont()->setSize(12);

$activeSheet->getStyle("A4:L4")->getBorders()->getBottom()->applyFromArray($BmStyle);

$activeSheet->mergeCells("A6:L6"); 
$activeSheet->getCell('A6')->setValue("COMMERCIAL INVOICE");
$activeSheet->getStyle('A6')->applyFromArray($textAlignCenter);
$activeSheet->getStyle('A6')->applyFromArray($styleArray);
$activeSheet->getStyle("A6:L6")->getFont()->setBold(true)->setSize(20);
$activeSheet->getRowDimension('6')->setRowHeight(24);

$activeSheet->getCell('A8')->setValue("Messrs");
$activeSheet->getStyle('A8')->applyFromArray($textAlignRight);
$activeSheet->getStyle('A8')->applyFromArray($styleArray);
$activeSheet->getStyle("A8")->getFont()->setSize(10);

$activeSheet->getCell('B8')->setValue("：");
$activeSheet->getStyle('B8')->applyFromArray($textAlignCenter);
$activeSheet->getStyle('B8')->applyFromArray($styleArray);
$activeSheet->getStyle("B8")->getFont()->setSize(10);

$startrow = 8;
$arr_con = explode(",", $ConsigneeID);
for ($i=0; $i < count($arr_con); $i++) { 
    $arr_con_id = $arr_con[$i];
    $sql_con="SELECT * FROM tblconsignee WHERE ConsigneeID='$arr_con_id'";
    $result_con = $conn->prepare($sql_con);
    $result_con->execute();
    while ($row_con = $result_con->fetch(PDO::FETCH_ASSOC)) {
        $Consignee_name=$row_con['Name'];
        $Consignee_address=$row_con['Address'];

        $activeSheet->getCell('C'.$startrow)->setValue($Consignee_name);
        $activeSheet->getStyle('C'.$startrow)->applyFromArray($styleArray);
        $activeSheet->getStyle('C'.$startrow)->getFont()->setSize(10);

        $activeSheet->getCell('C'.($startrow+1))->setValue($Consignee_address);
        $activeSheet->getStyle('C'.($startrow+1))->applyFromArray($styleArray);
        $activeSheet->getStyle('C'.($startrow+1))->getFont()->setSize(10);

        $startrow+=2;
    }
}

$activeSheet->getCell('J8')->setValue("Date");
$activeSheet->getStyle('J8')->applyFromArray($textAlignRight);
$activeSheet->getStyle('J8')->applyFromArray($styleArray);
$activeSheet->getStyle("J8")->getFont()->setSize(10);

$activeSheet->getCell('K8')->setValue(":");
$activeSheet->getStyle('K8')->applyFromArray($textAlignCenter);
$activeSheet->getStyle('K8')->applyFromArray($styleArray);
$activeSheet->getStyle("K8")->getFont()->setSize(10);

$activeSheet->getCell('L8')->setValue($invoice_date);
$activeSheet->getStyle('L8')->applyFromArray($textAlignLeft);
$activeSheet->getStyle('L8')->applyFromArray($styleArray);
$activeSheet->getStyle("L8")->getFont()->setSize(10);

$activeSheet->getCell('J9')->setValue("Invoice");
$activeSheet->getStyle('J9')->applyFromArray($textAlignRight);
$activeSheet->getStyle('J9')->applyFromArray($styleArray);
$activeSheet->getStyle("J9")->getFont()->setSize(10);

$activeSheet->getCell('K9')->setValue(":");
$activeSheet->getStyle('K9')->applyFromArray($textAlignCenter);
$activeSheet->getStyle('K9')->applyFromArray($styleArray);
$activeSheet->getStyle("K9")->getFont()->setSize(10);

$activeSheet->getCell('L9')->setValue($invoice_no);
$activeSheet->getStyle('L9')->applyFromArray($textAlignLeft);
$activeSheet->getStyle('L9')->applyFromArray($styleArray);
$activeSheet->getStyle("L9")->getFont()->setSize(10);

$activeSheet->getCell('J10')->setValue("firm delivery-date");
$activeSheet->getStyle('J10')->applyFromArray($textAlignRight);
$activeSheet->getStyle('J10')->applyFromArray($styleArray);
$activeSheet->getStyle("J10")->getFont()->setSize(10);

$activeSheet->getCell('K10')->setValue(":");
$activeSheet->getStyle('K10')->applyFromArray($textAlignCenter);
$activeSheet->getStyle('K10')->applyFromArray($styleArray);
$activeSheet->getStyle("K10")->getFont()->setSize(10);

$activeSheet->getCell('J11')->setValue("CU LICENSE NO");
$activeSheet->getStyle('J11')->applyFromArray($textAlignRight);
$activeSheet->getStyle('J11')->applyFromArray($styleArray);
$activeSheet->getStyle("J11")->getFont()->setSize(10);

$activeSheet->getCell('K11')->setValue(":");
$activeSheet->getStyle('K11')->applyFromArray($textAlignCenter);
$activeSheet->getStyle('K11')->applyFromArray($styleArray);
$activeSheet->getStyle("K11")->getFont()->setSize(10);

    $sql_bid="SELECT * FROM tblbuyer_invoice_detail WHERE invID='$id' GROUP BY shipmentpriceID";
    $result_bid = $conn->prepare($sql_bid);
    $result_bid->execute();
    $totalcarton=0;
    while ($row_bid = $result_bid->fetch(PDO::FETCH_ASSOC)) {
        $shipmentpriceID=$row_bid['shipmentpriceID'];

        $sql_cph="SELECT * FROM tblcarton_picklist_head WHERE shipmentpriceID='$shipmentpriceID' GROUP BY PID,ctn_range";
        $result_cph = $conn->prepare($sql_cph);
        $result_cph->execute();
        $carton_number=0;
        while ($row_cph = $result_cph->fetch(PDO::FETCH_ASSOC)) {
            $ctn_range=$row_cph['ctn_range'];
            $arr_ctn = explode("-", $ctn_range);
            $carton_number = $arr_ctn[1]-$arr_ctn[0]+1;
            $totalcarton=$totalcarton+$carton_number;
        }
    }

$activeSheet->getCell('J11')->setValue("Total Carton");
$activeSheet->getStyle('J11')->applyFromArray($textAlignRight);
$activeSheet->getStyle('J11')->applyFromArray($styleArray);
$activeSheet->getStyle("J11")->getFont()->setSize(10);

$activeSheet->getCell('K11')->setValue(":");
$activeSheet->getStyle('K11')->applyFromArray($textAlignCenter);
$activeSheet->getStyle('K11')->applyFromArray($styleArray);
$activeSheet->getStyle("K11")->getFont()->setSize(10);

$activeSheet->getCell('L11')->setValue($totalcarton);
$activeSheet->getStyle('L11')->applyFromArray($textAlignLeft);
$activeSheet->getStyle('L11')->applyFromArray($styleArray);
$activeSheet->getStyle("L11")->getFont()->setSize(10);

if ($startrow<=11) {
	$startrow=11;
}

$activeSheet->getCell('A'.$startrow)->setValue("Project No.");
$activeSheet->getStyle('A'.$startrow)->applyFromArray($textAlignRight);
$activeSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(10);

$activeSheet->getCell('B'.$startrow)->setValue(":");
$activeSheet->getStyle('B'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('B'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("B".$startrow)->getFont()->setBold(true)->setSize(10);

$startrow+=3;
$tablestartrow = $startrow;

$activeSheet->getStyle("A".$startrow.":L".$startrow)->getBorders()->getTop()->applyFromArray($BmStyle);
$activeSheet->getCell('A'.$startrow)->setValue("ORDER -NO.");
$activeSheet->getStyle('A'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('C'.$startrow)->setValue("PO NO.");
$activeSheet->getStyle('C'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('C'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("C".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('D'.$startrow)->setValue("ART. -NR.");
$activeSheet->getStyle('D'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('D'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("D".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('E'.$startrow)->setValue("DESCRIPTION OF GOODS AND/OR SERVICES");
$activeSheet->getStyle('E'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('E'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("E".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('F'.$startrow)->setValue("HTS CODE");
$activeSheet->getStyle('F'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('F'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("F".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('G'.$startrow)->setValue("COLOR");
$activeSheet->getStyle('G'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('G'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("G".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('H'.$startrow)->setValue("QUANTITY");
$activeSheet->getStyle('H'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('H'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("H".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('I'.$startrow)->setValue("QUANTITY");
$activeSheet->getStyle('I'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('I'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("I".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('J'.$startrow)->setValue("UNIT PRICE / USD");
$activeSheet->getStyle('J'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('J'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("J".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('L'.$startrow)->setValue("AMOUNT / USD");
$activeSheet->getStyle('L'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('L'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("L".$startrow)->getFont()->setBold(true)->setSize(9);
$activeSheet->getStyle("A".$startrow.":L".$startrow)->getBorders()->getBottom()->applyFromArray($BtStyle);

$startrow++;

$activeSheet->getCell('H'.$startrow)->setValue("IN SU");
$activeSheet->getStyle('H'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('H'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("H".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('I'.$startrow)->setValue("IN VKE");
$activeSheet->getStyle('I'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('I'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("I".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('J'.$startrow)->setValue("Per SU");
$activeSheet->getStyle('J'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('J'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("J".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('L'.$startrow)->setValue("FOB SIHANOUKVILLE PORT");
$activeSheet->getStyle('L'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('L'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("L".$startrow)->getFont()->setBold(true)->setSize(9);

// $activeSheet->getRowDimension('16')->setRowHeight(30);

$activeSheet->getStyle("A".$startrow.":L".$startrow)->getBorders()->getBottom()->applyFromArray($BtStyle);
$startrow++;

$sql_bid2="SELECT bid.*,sp.Orderno,sp.BuyerPO FROM tblbuyer_invoice_detail bid 
JOIN tblshipmentprice sp ON bid.shipmentpriceID = sp.ID
WHERE invID='$id'  GROUP BY shipmentpriceID";
$result_bid2 = $conn->prepare($sql_bid2);
$result_bid2->execute();
$totalqty=0;
$totalamount=0;
while ($row_bid2 = $result_bid2->fetch(PDO::FETCH_ASSOC)) {
    $shipmentpriceID=$row_bid2['shipmentpriceID'];
    $Orderno=$row_bid2['Orderno'];
    $BuyerPO=$row_bid2['BuyerPO'];
    $ht_code=$row_bid2['ht_code'];
    $shipping_marking=$row_bid2['shipping_marking'];

$sql_spaq="SELECT spaq.*,c.ColorName FROM tblshippingadviseqty spaq
        JOIN tblcolor c ON spaq.colorID = c.ID
        WHERE spaq.tblshipmentpriceID='$shipmentpriceID'
        GROUP BY spaq.colorID,spaq.garmentID";  
        // ,g.styleNo
        // JOIN tblgarment g ON spaq.garmentID=g.garmentID 
    $result_spaq = $conn->prepare($sql_spaq);
    $result_spaq->execute();
   
    while ($row_spaq = $result_spaq->fetch(PDO::FETCH_ASSOC)) {
    	$fobprice=$row_spaq['fobprice'];
    	$shippedQty=$row_spaq['shippedQty'];
        $colorID=$row_spaq['colorID'];
        // $garmentID=$row_spaq['garmentID'];
        // $styleNo=$row_scsq2['styleNo'];
        $ColorName=$row_spaq['ColorName'];

        $activeSheet->getCell('A'.$startrow)->setValue($Orderno);
		$activeSheet->getStyle('A'.$startrow)->applyFromArray($textAlignCenter);
		$activeSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
		$activeSheet->getStyle("A".$startrow)->getFont()->setSize(9);

		$activeSheet->getCell('C'.$startrow)->setValue($BuyerPO);
		$activeSheet->getStyle('C'.$startrow)->applyFromArray($textAlignCenter);
		$activeSheet->getStyle('C'.$startrow)->applyFromArray($styleArray);
		$activeSheet->getStyle("C".$startrow)->getFont()->setSize(9);

		$activeSheet->getCell('D'.$startrow)->setValue("");
		$activeSheet->getStyle('D'.$startrow)->applyFromArray($textAlignCenter);
		$activeSheet->getStyle('D'.$startrow)->applyFromArray($styleArray);
		$activeSheet->getStyle("D".$startrow)->getFont()->setSize(9);

		$activeSheet->getCell('E'.$startrow)->setValue($shipping_marking);
		$activeSheet->getStyle('E'.$startrow)->applyFromArray($textAlignCenter);
		$activeSheet->getStyle('E'.$startrow)->applyFromArray($styleArray);
		$activeSheet->getStyle("E".$startrow)->getFont()->setSize(9);

		$activeSheet->getCell('F'.$startrow)->setValue($ht_code);
		$activeSheet->getStyle('F'.$startrow)->applyFromArray($textAlignCenter);
		$activeSheet->getStyle('F'.$startrow)->applyFromArray($styleArray);
		$activeSheet->getStyle("F".$startrow)->getFont()->setSize(9);

		$activeSheet->getCell('G'.$startrow)->setValue($ColorName);
		$activeSheet->getStyle('G'.$startrow)->applyFromArray($textAlignCenter);
		$activeSheet->getStyle('G'.$startrow)->applyFromArray($styleArray);
		$activeSheet->getStyle("G".$startrow)->getFont()->setSize(9);

		$activeSheet->getCell('H'.$startrow)->setValue($shippedQty);
		$activeSheet->getStyle('H'.$startrow)->applyFromArray($textAlignCenter);
		$activeSheet->getStyle('H'.$startrow)->applyFromArray($styleArray);
		$activeSheet->getStyle("H".$startrow)->getFont()->setSize(9);

		$activeSheet->getCell('I'.$startrow)->setValue($shippedQty);
		$activeSheet->getStyle('I'.$startrow)->applyFromArray($textAlignCenter);
		$activeSheet->getStyle('I'.$startrow)->applyFromArray($styleArray);
		$activeSheet->getStyle("I".$startrow)->getFont()->setSize(9);

		$activeSheet->getCell('J'.$startrow)->setValue($fobprice);
		$activeSheet->getStyle('J'.$startrow)->applyFromArray($textAlignCenter);
		$activeSheet->getStyle('J'.$startrow)->applyFromArray($styleArray);
		$activeSheet->getStyle("J".$startrow)->getFont()->setSize(9);

		$totalqty=$totalqty+$shippedQty;
		$amount=$shippedQty*$fobprice;
		$totalamount=$totalamount+$amount;
		$activeSheet->getCell('L'.$startrow)->setValue($amount);
		$activeSheet->getStyle('L'.$startrow)->applyFromArray($textAlignCenter);
		$activeSheet->getStyle('L'.$startrow)->applyFromArray($styleArray);
		$activeSheet->getStyle("L".$startrow)->getFont()->setSize(9);
		$activeSheet->getStyle("A".$startrow.":L".$startrow)->getBorders()->getBottom()->applyFromArray($BtStyle);

		$startrow++;
    }

}

$activeSheet->getCell('A'.$startrow)->setValue("TOTAL:");
$activeSheet->getStyle('A'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('H'.$startrow)->setValue($totalqty);
$activeSheet->getStyle('H'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('H'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("H".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('I'.$startrow)->setValue($totalqty);
$activeSheet->getStyle('I'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('I'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("I".$startrow)->getFont()->setBold(true)->setSize(9);

$activeSheet->getCell('L'.$startrow)->setValue($totalamount);
$activeSheet->getStyle('L'.$startrow)->applyFromArray($textAlignCenter);
$activeSheet->getStyle('L'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("L".$startrow)->getFont()->setBold(true)->setSize(9);
$activeSheet->getStyle("A".$startrow.":L".$startrow)->getBorders()->getBottom()->applyFromArray($BmStyle);




$activeSheet->getStyle("A".$tablestartrow.":A".$startrow)->getBorders()->getRight()->applyFromArray($BtStyle);
$activeSheet->getStyle("B".$tablestartrow.":B".$startrow)->getBorders()->getRight()->applyFromArray($BtStyle);
$activeSheet->getStyle("C".$tablestartrow.":C".$startrow)->getBorders()->getRight()->applyFromArray($BtStyle);
$activeSheet->getStyle("D".$tablestartrow.":D".$startrow)->getBorders()->getRight()->applyFromArray($BtStyle);
$activeSheet->getStyle("E".$tablestartrow.":E".$startrow)->getBorders()->getRight()->applyFromArray($BtStyle);
$activeSheet->getStyle("F".$tablestartrow.":F".$startrow)->getBorders()->getRight()->applyFromArray($BtStyle);
$activeSheet->getStyle("G".$tablestartrow.":G".$startrow)->getBorders()->getRight()->applyFromArray($BtStyle);
$activeSheet->getStyle("H".$tablestartrow.":H".$startrow)->getBorders()->getRight()->applyFromArray($BtStyle);
$activeSheet->getStyle("I".$tablestartrow.":I".$startrow)->getBorders()->getRight()->applyFromArray($BtStyle);
$activeSheet->getStyle("J".$tablestartrow.":J".$startrow)->getBorders()->getRight()->applyFromArray($BtStyle);
$activeSheet->getStyle("K".$tablestartrow.":K".$startrow)->getBorders()->getRight()->applyFromArray($BtStyle);

$startrow+=2;
$activeSheet->getCell('A'.$startrow)->setValue("READY MADE GARMENTS :");
$activeSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(10);

$startrow+=2;
$activeSheet->getCell('A'.$startrow)->setValue("TOTAL AMOUNT :");
$activeSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(10);

$numberToText = convert_number($totalamount);
$activeSheet->getCell('C'.$startrow)->setValue($numberToText);
$activeSheet->getStyle('C'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("C".$startrow)->getFont()->setBold(true)->setSize(10);

$startrow+=2;
$activeSheet->getCell('A'.$startrow)->setValue("Payment Terms :");
$activeSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(10);

$startrow+=2;
$activeSheet->getCell('A'.$startrow)->setValue("BANK ACCOUNT NUMBER:");
$activeSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(10);

$startrow+=2;
$activeSheet->getCell('A'.$startrow)->setValue("BANK NAME:");
$activeSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(10);

$startrow+=2;
$activeSheet->getCell('A'.$startrow)->setValue("BANK ADDRESS:");
$activeSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(10);

$startrow+=2;
$activeSheet->getCell('A'.$startrow)->setValue("BENEFICIARY'S NAME:");
$activeSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(10);

$startrow+=2;
$activeSheet->getCell('A'.$startrow)->setValue("SWIFT CODE:");
$activeSheet->getStyle('A'.$startrow)->applyFromArray($styleArray);
$activeSheet->getStyle("A".$startrow)->getFont()->setBold(true)->setSize(10);
$activeSheet->getStyle("A".$startrow.":L".$startrow)->getBorders()->getBottom()->applyFromArray($BmStyle);

$activeSheet->getStyle("A1:L".$startrow)->getBorders()->getRight()->applyFromArray($BtStyle);

// Redirect output to a client?¡¥s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Buyer Tchibo-'.$id.'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
$objWriter->setPreCalculateFormulas(true);
$objWriter->save('php://output');
exit;
?>