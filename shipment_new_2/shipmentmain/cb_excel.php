<?php 
include("../../lock.php");
include("../../function/misc.php");
include("../../phpexcel/Classes/PHPExcel.php");

$handle_misc = new misc($conn);

$mainurl = $handle_misc->getAPIURL();


$method = "POST";
$url    = $mainurl."/";
$data   = array();

$arr_result = $handle_misc->funcCallAPI($method, $url, $data);


$arrCol=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
				'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
				'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
				'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');
				

$sheet = new PHPExcel();
$activeSheet = $sheet->getActiveSheet();
$activeSheet->setTitle("Cost Breakdown");


$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'HTML');
$objWriter->save('php://output');


?>