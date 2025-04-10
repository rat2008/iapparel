<?php
include("../../lock.php");
include("../../function/misc.php");
include("../../phpexcel/Classes/PHPExcel.php");

include("../../model/tblbuyer_invoice_payment_cost_head.php");
include("../../model/tblbuyer_invoice_payment_cost_detail.php");
include_once("../../cf/cs_cb.php");

$misc = new misc($conn);

// Initialize models
$model_cost_head = new tblbuyer_invoice_payment_cost_head($conn, $misc);
$model_cost_detail = new tblbuyer_invoice_payment_cost_detail($conn, $misc);

$buyer_po_header = new cs_cb($conn, $misc);
$row_buyer_po = $buyer_po_header->select_buyer_po($_GET['invID']);

// Fetch data
$invID = $_GET['invID']; // Invoice ID from request
// $shipmentpriceID = $_GET['shipmentpriceID']; // Shipment price ID from request
$style_center = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )
);

$border_allborders = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$cost_heads = $model_cost_head->getAllByArr(['invID' => $invID]);

// Initialize PHPExcel
$sheet = new PHPExcel();
$activeSheet = $sheet->getActiveSheet();
$activeSheet->setTitle("Cost Breakdown");

// Set document properties
$sheet->getProperties()
    // ->setCreator("Your Company")
    ->setTitle("Cost Breakdown Export")
    ->setDescription("Exported cost breakdown data");

// Add headers
$activeSheet->setCellValue('A1', 'Inv Attachment');
$activeSheet->setCellValue('A2', 'Cost and Weight breakdown');
$sheet->getActiveSheet()->mergeCells('A1:G1');
$sheet->getActiveSheet()->mergeCells('A2:G2');
$sheet->getActiveSheet()->getStyle('A1:A2')->applyFromArray($style_center);
$activeSheet->getStyle('A1:A2')->getFont()->setBold(true);

$row = 3;
foreach ($row_buyer_po as $buyer_po) {
    $activeSheet->setCellValue("A$row", 'PO#')
        ->setCellValue("B$row", $buyer_po['GTN_buyerpo']);

    $row++;
    $row++;

    $activeSheet->setCellValue("A$row", 'ITEM/KOHL\'S STYLE#')
        ->setCellValue("B$row", $buyer_po['GTN_styleno']);

    $row++;

    $row_cost_head = $buyer_po_header->select_cost_head($_GET['invID'], $buyer_po['shipmentpriceID']);

    foreach ($row_cost_head as $cost_head) {
        $row_color = $buyer_po_header->select_po_color($_GET['invID'], $buyer_po['shipmentpriceID']);
        $color_arr = [];
        $qty = 0;
        $total_amount = 0;
        $final_total_nnw = 0;

        foreach ($row_color as $color) {
            if (in_array($color['colorID'], explode(',', $cost_head['colorID']))) {
                $color_arr[] = $color['color'];
            }
        }

        $activeSheet->setCellValue("A$row", 'ITEM DESCRIPTION')
            ->setCellValue('B' . $row, $cost_head['item_desc']);
        $sheet->getActiveSheet()->mergeCells('B' . $row . ':G' . $row);

        $row++;

        $activeSheet->setCellValue('A' . ($row), 'Color')
            ->setCellValue('B' . ($row), implode(', ', $color_arr));
        $sheet->getActiveSheet()->mergeCells('B' . $row . ':C' . $row);
        $activeSheet->getStyle('B' . ($row))->getAlignment()->setWrapText(true);
        $activeSheet->getColumnDimension('B')->setWidth(40);

        $row++;

        $activeSheet->setCellValue('B' . $row, 'QTY');
        $sheet->getActiveSheet()->mergeCells('B' . $row . ':C' . $row);
        $activeSheet->setCellValue('D' . $row, 'UNIT PRICE')
            ->setCellValue('E' . $row, "TOTAL \n AMOUNT")
            ->setCellValue('F' . $row, "NNW /CTNS \n ( KG)")
            ->setCellValue('G' . $row, "TOTAL NNW \n (KG)");

        $sheet->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($border_allborders);

        $sheet->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setWrapText(true);
        $sheet->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);
        $sheet->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setWrapText(true);
        $sheet->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setWrapText(true);
        $sheet->getActiveSheet()->getStyle('B' . $row . ':G' . $row)->applyFromArray($style_center);

        $row++;

        $row_cost_detail = $buyer_po_header->select_cost_detail($cost_head['INVCHID']);

        foreach ($row_cost_detail as $cost_detail) {
            $qty = $cost_detail['qty'];
            (double)$amount = (double)$cost_detail['qty'] * (double)$cost_detail['unitprice'];
            $total_amount = $total_amount + $amount;
            $final_total_nnw = $final_total_nnw + $cost_detail['total_nnw'];

            $activeSheet->setCellValue('A' . $row, $cost_detail['item_desc'])
                ->setCellValue('B' . $row, $cost_detail['qty'])
                ->setCellValue('C' . $row, 'PCS')
                ->setCellValue('D' . $row, '$' . $cost_detail['unitprice'])
                ->setCellValue('E' . $row, '$' . $amount)
                ->setCellValue('F' . $row, '$' . $cost_detail['ctn_qty'])
                ->setCellValue('G' . $row, '$' . $cost_detail['total_nnw']);

            $sheet->getActiveSheet()->getStyle('B' . $row . ':G' . $row)->applyFromArray($style_center);
            $sheet->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($border_allborders);

            $row++;
        }
        $activeSheet->setCellValue('A' . $row, 'Total Amount:')
            ->setCellValue('B' . $row, $qty)
            ->setCellValue('C' . $row, 'SETS')
            ->setCellValue('E' . $row, '$' . $total_amount)
            ->setCellValue('G' . $row, $final_total_nnw);

        $sheet->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($style_center);

        $sheet->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($border_allborders);

        $activeSheet->getStyle('A' . $row)->getFont()->setBold(true);
        $activeSheet->getStyle('B' . $row)->getFont()->setBold(true);
        $activeSheet->getStyle('E' . $row)->getFont()->setBold(true);
        $activeSheet->getStyle('G' . $row)->getFont()->setBold(true);

        $row++;
        $row++;
    }
    $row++;
}

// Auto-size columns
foreach (range('A', 'I') as $columnID) {
    $activeSheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Clear output buffer to prevent unexpected output
// if (ob_get_length()) {
//     ob_end_clean();
// }

// Output to browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="cost_breakdown.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
$objWriter->save('php://output');
exit;
