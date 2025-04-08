<?php
include("../../lock.php");
include("../../function/misc.php");
include("../../phpexcel/Classes/PHPExcel.php");

include("../../model/tblbuyer_invoice_payment_cost_head.php");
include("../../model/tblbuyer_invoice_payment_cost_detail.php");

$handle_misc = new misc($conn);

// Initialize models
$model_cost_head = new tblbuyer_invoice_payment_cost_head($conn, $handle_misc);
$model_cost_detail = new tblbuyer_invoice_payment_cost_detail($conn, $handle_misc);

// Fetch data
$invID = $_GET['invID']; // Invoice ID from request
$shipmentpriceID = $_GET['shipmentpriceID']; // Shipment price ID from request

$cost_heads = $model_cost_head->getAllByArr(['invID' => $invID]);

// Initialize PHPExcel
$sheet = new PHPExcel();
$activeSheet = $sheet->getActiveSheet();
$activeSheet->setTitle("Cost Breakdown");

// Set document properties
$sheet->getProperties()
    ->setCreator("Your Company")
    ->setTitle("Cost Breakdown Export")
    ->setDescription("Exported cost breakdown data");

// Add headers
$activeSheet->setCellValue('A1', 'PO#')
    ->setCellValue('B1', 'ITEM/STYLE#')
    ->setCellValue('C1', 'ITEM DESCRIPTION')
    ->setCellValue('D1', 'COLOR')
    ->setCellValue('E1', 'QTY')
    ->setCellValue('F1', 'UNIT PRICE')
    ->setCellValue('G1', 'TOTAL AMOUNT')
    ->setCellValue('H1', 'NNW / CTNS (KG)')
    ->setCellValue('I1', 'TOTAL NNW (KG)');

// Style headers
$activeSheet->getStyle('A1:I1')->getFont()->setBold(true);

// Populate data
$row = 2; // Start from the second row
foreach ($cost_heads['row'] as $cost_head) {
    $cost_details = $model_cost_detail->getAllByArr(['INVCHID' => $cost_head['INVCHID']]);

    foreach ($cost_details['row'] as $detail) {
        $activeSheet->setCellValue("A$row", $cost_head['shipmentpriceID'])
            ->setCellValue("B$row", $cost_head['item_desc'])
            ->setCellValue("C$row", $detail['item_desc'])
            ->setCellValue("D$row", $cost_head['colorID'])
            ->setCellValue("E$row", $detail['qty'])
            ->setCellValue("F$row", $detail['unitprice'])
            ->setCellValue("G$row", $detail['qty'] * $detail['unitprice']) // Total Amount
            ->setCellValue("H$row", $detail['ctn_qty'])
            ->setCellValue("I$row", $detail['total_nnw']);
        $row++;
    }
}

// Auto-size columns
foreach (range('A', 'I') as $columnID) {
    $activeSheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Clear output buffer to prevent unexpected output
if (ob_get_length()) {
    ob_end_clean();
}

// Output to browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="cost_breakdown.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
$objWriter->save('php://output');
exit;
?>