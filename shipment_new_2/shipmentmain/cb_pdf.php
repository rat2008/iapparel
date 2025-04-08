<?php 
include("../../lock.php");
include("../../function/misc.php");
include("../../mpdf/mpdf.php");
include("../../model/tblbuyer_invoice_payment_cost_head.php");
include("../../model/tblbuyer_invoice_payment_cost_detail.php");

$handle_misc = new misc($conn);

// Fetch data
$invID = $_GET['invID']; // Invoice ID from request
$shipmentpriceID = $_GET['shipmentpriceID']; // Shipment price ID from request

$model_cost_head = new tblbuyer_invoice_payment_cost_head($conn, $handle_misc);
$model_cost_detail = new tblbuyer_invoice_payment_cost_detail($conn, $handle_misc);

$cost_heads = $model_cost_head->getAllByArr(['invID' => $invID]);

// Initialize mPDF
$pdf = new mPDF(); 
$pdf->SetCreator("Apparel Ezi");
$pdf->SetAutoPageBreak(TRUE);
$pdf->SetFont('droidsansfallback', '', 8);
$pdf->autoLangToFont = true;
$pdf->autoScriptToLang = true;

// Add a page
$pdf->AddPage('P', '', '', '', '', 8, 8, 5, 5); 
$pdf->SetTitle('Cost Breakdown');

// Generate HTML content
$html = '<style>
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    th, td { border: 1px solid black; padding: 5px; text-align: left; }
    th { background-color: #f2f2f2; }
    .header { font-weight: bold; font-size: 12px; }
    .highlight { background-color: yellow; }
    .no-border { border: none; }
</style>';

foreach ($cost_heads['row'] as $cost_head) {
    $cost_details = $model_cost_detail->getAllByArr(['INVCHID' => $cost_head['INVCHID']]);

    $html .= '<table>
        <tr>
            <td class="no-border header" colspan="2">ITEM/KOHLS STYLE#: ' . $cost_head['shipmentpriceID'] . '</td>
            <td class="no-border header" colspan="2">ITEM DESCRIPTION: ' . $cost_head['item_desc'] . '</td>
        </tr>
        <tr>
            <td class="no-border" colspan="4">Color: Drop down color name</td>
        </tr>
    </table>';

    $html .= '<table>
        <thead>
            <tr>
                <th>QTY</th>
                <th>UNIT PRICE</th>
                <th>TOTAL AMOUNT</th>
                <th>NNW / CTNS (KG)</th>
                <th>TOTAL NNW (KG)</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($cost_details['row'] as $detail) {
        $total_amount = $detail['qty'] * $detail['unitprice'];
        $html .= '<tr>
            <td>' . $detail['qty'] . '</td>
            <td>' . number_format($detail['unitprice'], 3) . '</td>
            <td>' . number_format($total_amount, 2) . '</td>
            <td>' . number_format($detail['ctn_qty'], 2) . '</td>
            <td>' . number_format($detail['total_nnw'], 2) . '</td>
        </tr>';
    }

    $html .= '</tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="highlight">Total Amount:</td>
                <td class="highlight">' . number_format(array_sum(array_column($cost_details['row'], 'qty')) * array_sum(array_column($cost_details['row'], 'unitprice')), 2) . '</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table><br>';
}

// Write HTML to PDF
$pdf->writeHTML($html);

// Output PDF
$pdf->Output('Cost_Breakdown.pdf', 'I');
?>