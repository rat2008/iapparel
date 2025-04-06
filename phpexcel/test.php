<?php

include('../Site/lock.php');
include_once 'Classes/PHPExcel.php';

$mpohid = $_GET['mpohid'];

$sheet= new PHPExcel();
$activeSheet = $sheet->getActiveSheet();

$objRichText = new PHPExcel_RichText();
$objRichText2 = new PHPExcel_RichText();
//$objRichText->createText('This text is ');

//style
$underline = array(
	'borders' => array(
          'bottom' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN,
    )
    ));
	
	
$styleArray2 = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FF0000'),
        'size'  => 11,
        'name'  => 'Verdana'
    ));
	
$itemCode = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 16,
        'name'  => 'Calibri'
    ),
	'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'C2FFFF')
    ),
	'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
    ));
	
$itemCode2 = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 16,
        'name'  => 'Calibri'
    ),
	'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
          )
    ),
    'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )	);
	
$referencelayout = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 12,
        'name'  => 'Calibri'
    ),
    'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )	);
	
$header = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 12,
        'name'  => 'Calibri'
    ),
	'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
          )
    ),
    'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )	);
	
$tableContent = array(
    'font'  => array(
        'bold'  => false,
        'size'  => 13,
        'name'  => 'Calibri'
    ),
	'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
    ),
    'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )	);
	
$yellowBg = array(
    'font'  => array(
        'bold'  => false,
        'size'  => 12,
        'name'  => 'Calibri'
    ),
	'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'FFFF66')
    ),	
	'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
    ),
    'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    ));
	
$shippingMarks = array(
    'font'  => array(
        'bold'  => false,
        'size'  => 13,
        'name'  => 'Calibri'
    ),
	'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
    ));

$remark = array(
    'font'  => array(
        'bold'  => false,
        'size'  => 9,
        'name'  => 'Calibri'
    ));
	
//cell width
$column = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
$countColumn = count($column);
for ($x=0; $x<$countColumn; $x++){
	
	$c = $column[$x];
	$activeSheet ->getColumnDimension($c)->setWidth(5);
}	

$objBold = $objRichText->createTextRun('UPC Label Order Form (JMA-PL-6155, JMA-WV-5749)');//make font bold
$objBold->getFont()->setBold(true)->setSize(16);
//$activeSheet->getStyle("A1:Z1")->getFont()->setSize(26);//set font size
//$objRichText->createText(' within the cell.');

		$mpoheader = "SELECT *, 
		(select MAX(Version) from tblmpolog where tblmpolog.MPOHID = tblmpo_header.MPOHID) as version,
		tblsupplier.Address as supplier_addr,  
		tblcontact.Tel as ContactTel, tblcontact.Email as ContactEmail, tblcontact.Fax as ContactFax , tblbilladdr.Address as bill_addr,
	    (select UserFullName from tbluseraccount where AcctID = lastupdateBy) as last_updatedBy,
		(select UserFullName from tbluseraccount where AcctID = createdBy) as created_By
		FROM tblmpo_header
		INNER JOIN tbluseraccount ON tbluseraccount.acctid = tblmpo_header.acctid
		INNER JOIN tblbilladdr ON tblbilladdr.ID = tblmpo_header.BillAddrID
		INNER JOIN tblsupplier ON tblsupplier.SupplierID = tblmpo_header.supplierID
		INNER JOIN tblcontact ON tblcontact.ID = tblsupplier.SupplierID
		INNER JOIN tblpaymentterm ON tblpaymentterm.ID = tblmpo_header.paymenttermID
		INNER JOIN tblcurrency ON tblcurrency.ID = tblmpo_header.currencyID
		INNER JOIN tbltradeterm ON tbltradeterm.ID = tblmpo_header.tradetermID	
        INNER JOIN tblmaterialrecaddr ON tblmaterialrecaddr.ID = tblmpo_header.recAddrID			
		WHERE  tblmpo_header.MPOHID= '$mpohid'  ";
		
		$mpo_header = $conn->query($mpoheader);
		$row_mpo_head = $mpo_header->fetch(PDO::FETCH_ASSOC);
		
		$username =$row_mpo_head["UserFullName"];
		//$mpohid =$row_mpo_head["MPOHID"];
		$lastupdateBy =$row_mpo_head["last_updatedBy"];
		$updatedate =$row_mpo_head["lastupdateDate"];
		$podate=$row_mpo_head["PODate"];
		$billaddress=$row_mpo_head["bill_addr"];
		$sugrecDate=$row_mpo_head["sugrecDate"];
		$supp_addr=$row_mpo_head["supplier_addr"];
		$company_name=$row_mpo_head["companyName"];
		$tel=$row_mpo_head["ContactTel"];
		$email=$row_mpo_head["ContactEmail"];
		$fax=$row_mpo_head["ContactFax"];
		$version=$row_mpo_head["version"];
		$TypeID=$row_mpo_head["Type"];

$activeSheet ->getCell('G1')->setValue($objRichText);
$activeSheet->getRowDimension(8)->setRowHeight(-1); 
$activeSheet->getStyle('C8')->getAlignment()->setWrapText(true);
$activeSheet ->setCellValue('B4', 'Order Date:');
$activeSheet ->setCellValue('F4', '');
$activeSheet ->getStyle('E4:H4')->applyFromArray($underline);

$activeSheet ->setCellValue('K4', 'Ship Date:');
$activeSheet ->setCellValue('M4', '');
$activeSheet ->getStyle('M4:P4')->applyFromArray($underline);

$activeSheet ->setCellValue('S4', 'PO#:');
$activeSheet ->setCellValue('U4', $mpohid);
$activeSheet ->getStyle('U4:X4')->applyFromArray($underline);



$styleArray = array( 'font' => array( 'bold' => true, 'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE),);

$activeSheet ->setCellValue('B6', 'Bill To:');
$activeSheet ->getStyle('D6:L6')->applyFromArray($underline);
$activeSheet ->getStyle('D7:L7')->applyFromArray($underline);
$activeSheet ->getStyle('D8:L8')->applyFromArray($underline);

$activeSheet ->setCellValue('B9', 'Attn:');
$activeSheet ->getStyle('D9:L9')->applyFromArray($underline);

$activeSheet ->setCellValue('B10', 'Email:');
$activeSheet ->setCellValue('D10', $email);
$activeSheet ->getStyle('D10:L10')->applyFromArray($underline);

$activeSheet ->setCellValue('B11', 'Phone:');
$activeSheet ->setCellValue('D11', $tel);
$activeSheet ->getStyle('D11:G11')->applyFromArray($underline);

$activeSheet ->setCellValue('H11', 'Fax:');
$activeSheet ->setCellValue('I11', $fax);
$activeSheet ->getStyle('I11:M11')->applyFromArray($underline);

$activeSheet ->setCellValue('N6', 'Ship To:');
$activeSheet ->getStyle('P6:X6')->applyFromArray($underline);
$activeSheet ->getStyle('P7:X7')->applyFromArray($underline);
$activeSheet ->getStyle('P8:X8')->applyFromArray($underline);

$activeSheet ->setCellValue('N9', 'Attn:');
$activeSheet ->getStyle('P9:X9')->applyFromArray($underline);

$activeSheet ->setCellValue('N10', 'Email:');
$activeSheet ->setCellValue('P10', $email);
$activeSheet ->getStyle('P10:X10')->applyFromArray($underline);

$activeSheet ->setCellValue('N11', 'Phone:');
$activeSheet ->setCellValue('P11', $tel);
$activeSheet ->getStyle('P11:R11')->applyFromArray($underline);

$activeSheet ->setCellValue('T11', 'Fax:');
$activeSheet ->setCellValue('U11', $fax);
$activeSheet ->getStyle('U11:X11')->applyFromArray($underline);
//$activeSheet->getStyle('N12')->applyFromArray($styleArray);

//line 14
$activeSheet ->setCellValue('B14', 'Item Code');
$activeSheet ->getStyle('B14:D14')->applyFromArray($itemCode);

$activeSheet ->setCellValue('G14', '***********');
$activeSheet ->getStyle('E14:I14')->applyFromArray($itemCode2);

$activeSheet ->setCellValue('S14', 'Reference Layout');
$activeSheet ->getStyle('S14')->applyFromArray($referencelayout);


//line 16
$line16 = 16;
$activeSheet ->mergeCells('B'.$line16.':E'.$line16);
$activeSheet ->setCellValue('B'.$line16, 'Style Number');

$activeSheet ->mergeCells('F'.$line16.':I'.$line16);
$activeSheet ->setCellValue('F'.$line16, 'UPC');

$activeSheet ->mergeCells('J'.$line16.':M'.$line16);
$activeSheet ->setCellValue('J'.$line16, 'Order Qty');
$activeSheet ->getStyle('B'.$line16.':M'.$line16)->applyFromArray($header);

$i =0;
$tblcategory = "SELECT * FROM tblcategory";
$tbl_category = $conn->query($tblcategory);
while($row_tbl_category=$tbl_category->fetch(PDO::FETCH_BOTH)){
		
$categoryID =$row_tbl_category["ID"];
$Category =$row_tbl_category["Category"];
$i++;
$lines = $line16 + $i;	
$activeSheet ->mergeCells('B'.$lines.':E'.$lines);
$activeSheet ->setCellValue('B'.$lines, '      '.$Category.'      ');		

$activeSheet ->mergeCells('F'.$lines.':I'.$lines);
$activeSheet ->setCellValue('F'.$lines, '      '.$Category.'      ');	

$activeSheet ->mergeCells('J'.$lines.':M'.$lines);	
$activeSheet ->setCellValue('J'.$lines, '      '.$Category.'      ');		
//$activeSheet ->getStyle('B'.$lines.':J'.$lines)->applyFromArray($tableContent);	
}	

$lines_total= $lines + 1;
$activeSheet ->mergeCells('H'.$lines_total.':I'.$lines_total);
$activeSheet ->setCellValue('H'.$lines_total, 'Total');	
$activeSheet ->mergeCells('J'.$lines_total.':M'.$lines_total);	
$activeSheet ->getStyle('J'.$lines_total.':M'.$lines_total)->applyFromArray($header);

$linesNow= $lines_total + 2;
$activeSheet ->mergeCells('B'.$linesNow.':M'.$linesNow);	
$activeSheet ->setCellValue('B'.$linesNow, 'Ship Via');

$activeSheet ->mergeCells('N'.$linesNow.':X'.$linesNow);	
$activeSheet ->setCellValue('N'.$linesNow, 'Courier Account No');

$activeSheet ->getStyle('B'.$linesNow.':M'.$linesNow)->applyFromArray($yellowBg);
$activeSheet ->getStyle('N'.$linesNow.':X'.$linesNow)->applyFromArray($yellowBg);

$linesNow2= $linesNow + 1;
$activeSheet ->mergeCells('B'.$linesNow2.':M'.$linesNow2);
$activeSheet ->mergeCells('N'.$linesNow2.':X'.$linesNow2);
$activeSheet ->getStyle('B'.$linesNow2.':M'.$linesNow2)->applyFromArray($tableContent);
$activeSheet ->getStyle('N'.$linesNow2.':X'.$linesNow2)->applyFromArray($tableContent);

//$objBold2 = $objRichText2->createTextRun('MOQ & Round-up qty is 50pcs/sku, pls consult our customer service for details.');//make font bold
//$objBold2->getFont()->setBold(true)->setSize(13);
//$activeSheet ->getCell('A48')->setValue($objRichText2);


$linesNow3= $linesNow2 + 3;
$activeSheet ->setCellValue('B'.$linesNow3, 'Please Email to:');

$linesNow4= $linesNow3 + 2;
$activeSheet ->setCellValue('B'.$linesNow4, 'MOQ & Round-up qty is 50pcs/sku, pls consult our customer service for details.');
$activeSheet ->getStyle('B'.$linesNow4)->applyFromArray($styleArray2);

$linesNow5= $linesNow4 + 1;
$activeSheet ->setCellValue('B'.$linesNow5, 'Disclamier:');
$activeSheet ->setCellValue('D'.$linesNow5, 'If no specific shipping instruction are provided within 72 hours (excluding Saturday, Sunday and public holiday) from the date of our order');
$activeSheet ->getStyle('B'.$linesNow5)->applyFromArray($remark);
$activeSheet ->getStyle('D'.$linesNow5)->applyFromArray($remark);

$linesNow6= $linesNow5 + 1;
$activeSheet ->setCellValue('D'.$linesNow6, 'acknowledgement, Avery Dennison will ship out your orders with our assigned carriers.');
$activeSheet ->getStyle('D'.$linesNow6)->applyFromArray($remark);

$linesNow7= $linesNow6 + 1;
$activeSheet ->setCellValue('D'.$linesNow7, '请贵司在我司发出订单确认函后72小时内(不包括周末, 周日及公众假期)将订单的具体出货方式通知我司。');
$activeSheet ->getStyle('D'.$linesNow7)->applyFromArray($remark);

$linesNow8= $linesNow7 + 2;
$linesNow9= $linesNow8 + 10;
$activeSheet ->getStyle('B'.$linesNow4)->applyFromArray($shippingMarks);
$activeSheet ->mergeCells('B'.$linesNow8.':M'.$linesNow9);



//test loop rows
/* $i =0;
$tblcategory = "SELECT * FROM tblcategory";
$tbl_category = $conn->query($tblcategory);
while($row_tbl_category=$tbl_category->fetch(PDO::FETCH_BOTH)){
		
$categoryID =$row_tbl_category["ID"];
$Category =$row_tbl_category["Category"];
$i++;
$count = 55 + $i;			
$activeSheet ->setCellValue('A'.$count, $Category);		
} */

//test loop columns
/* $column = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

$i =0;
$tblcategory = "SELECT * FROM tblcategory";
$tbl_category = $conn->query($tblcategory);
while($row_tbl_category=$tbl_category->fetch(PDO::FETCH_BOTH)){
		
$categoryID =$row_tbl_category["ID"];
$Category =$row_tbl_category["Category"];
$i++;

$count = $column[$i];
	
$activeSheet ->setCellValue($count.'55', $Category);		
} */


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="report.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
$objWriter->save('php://output');

exit;

?>