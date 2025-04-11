<?php 
include("../../lock.php");
include("../../function/misc.php");
include("../../mpdf/mpdf.php");
include("../../model/tblbuyer_invoice_payment_cost_head.php");
include("../../model/tblbuyer_invoice_payment_cost_detail.php");
include_once("../../cf/cs_cb.php");

$handle_misc = new misc($conn);

// Fetch data
$invID = $_GET['invID']; // Invoice ID from request
$model_cost_head = new tblbuyer_invoice_payment_cost_head($conn, $handle_misc);
$model_cost_detail = new tblbuyer_invoice_payment_cost_detail($conn, $handle_misc);
$buyer_po_header = new cs_cb($conn, $handle_misc);

$row_buyer_po = $buyer_po_header->select_buyer_po($invID);

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
    .header { font-weight: bold; font-size: 12px; }
    .highlight { background-color: yellow; }
    .no-border { border: none; }
    .text-center { text-align: center; }
</style>';

$html .= '
    <div style="text-align: center;">
        Inv Attachment
    </div>
    <div style="text-align: center;">
        Cost and Weight Breakdown
    </div>
    <br>';

foreach ($row_buyer_po as $buyer_po) {
    $html .= '<table>
        <tr>
            <td class="no-border header" style="width:40%">PO#:</td>
            <td class="no-border">' . $buyer_po['GTN_buyerpo'] . '</td>
        </tr>
        <tr>
            <td class="no-border header" style="width:40%">ITEM/KOHL\'S STYLE#:</td>
            <td class="no-border">' . $buyer_po['GTN_styleno'] . '</td>
        </tr>
    </table><br>';

    $row_cost_head = $buyer_po_header->select_cost_head($invID, $buyer_po['shipmentpriceID']);
    foreach ($row_cost_head as $cost_head) {
        $row_color = $buyer_po_header->select_po_color($invID, $buyer_po['shipmentpriceID']);
        $color_arr = [];
        foreach ($row_color as $color) {
            if (in_array($color['colorID'], explode(',', $cost_head['colorID']))) {
                $color_arr[] = $color['color'];
            }
        }

        $html .= '<table>
            <tr>
                <td class="header" style="width:40%">ITEM DESCRIPTION:</td>
                <td >' . $cost_head['item_desc'] . '</td>
            </tr>
            <tr>
                <td class="header" style="width:40%">Color:</td>
                <td >' . implode(', ', $color_arr) . '</td>
            </tr>
        </table>';

        $html .= '<table>
            <thead>
                <tr>
                    <th class="text-center" style="width:40%"></th>
                    <th class="text-center" colspan="2">QTY</th>
                    <th class="text-center">UNIT PRICE</th>
                    <th class="text-center">TOTAL AMOUNT</th>
                    <th class="text-center">NNW / CTNS (KG)</th>
                    <th class="text-center">TOTAL NNW (KG)</th>
                </tr>
            </thead>
            <tbody>';

        $row_cost_detail = $buyer_po_header->select_cost_detail($cost_head['INVCHID']);
        $total_amount = 0;
        $final_total_nnw = 0;
        $qty = 0;

        foreach ($row_cost_detail as $cost_detail) {
            $amount = $cost_detail['qty'] * $cost_detail['unitprice'];
            $total_amount += $amount;
            $final_total_nnw += $cost_detail['total_nnw'];
            $qty = $cost_detail['qty'];

            $html .= '<tr>
                <td style="width:40%">' . $cost_detail['item_desc'] . '</td>
                <td class="text-center">' . $cost_detail['qty'] . '</td>
                <td class="text-center">PCS</td>
                <td class="text-center">' . number_format($cost_detail['unitprice'], 3) . '</td>
                <td class="text-center">$' . number_format($amount, 2) . '</td>
                <td class="text-center">' . number_format($cost_detail['ctn_qty'], 2) . '</td>
                <td class="text-center">' . number_format($cost_detail['total_nnw'], 2) . '</td>
            </tr>';
        }

        $html .= '</tbody>
            <tfoot>
                <tr>
                    <th class="text-center">Total Amount:</th>
                    <th class="text-center">' . $qty . '</th>
                    <td class="text-center">SETS</td>
                    <th class=""></th>
                    <th class="text-center">$' . number_format($total_amount, 2) . '</th>
                    <th class=""></th>
                    <th class="text-center">' . number_format($final_total_nnw, 2) . '</th>
                </tr>
            </tfoot>
        </table><br>';
    }
}

// Write HTML to PDF
$pdf->writeHTML($html);

// Output PDF
$pdf->Output('Cost_Breakdown.pdf', 'I');
?>