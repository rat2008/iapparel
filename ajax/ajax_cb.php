<?php
include("../lock.php");
include("../function/misc.php");
include_once("../cf/cs_cb.php");
include("../model/tblbuyer_invoice_payment_cost_head.php");

$handle_misc = new misc($conn);

$_misc = new misc($conn);

$buyer_po_header = new cs_cb($conn, $_misc);

$type = $_POST['type'];


$color_id =	[];
$color_qty = 0;
$i = 0;
$row_buyer_po = $buyer_po_header->select_buyer_po($_POST['invID']);
$last_cost_head_id = $handle_misc->funcMaxID('tblbuyer_invoice_payment_cost_head', "INVCHID") + 1;


if ($type == 'getQty') {
	$shipmentpriceID = $_POST['shipmentpriceID'];
	$row_color = $buyer_po_header->select_po_color($_POST['invID'], $shipmentpriceID);
	$color_id = explode(',', $_POST['color_id']);
	$bicid_array = [];
	$total_ctn = 0;
	$total_nnw = 0;
	$group_number_array = [];

	foreach ($row_color as $color) {
		if (in_array($color['colorID'], $color_id)) {
			$color_qty = $color_qty + $color['qty'];
			$group_number_array[] = $color['group_number'];
			$bicid_array[] = $color['BICID'];

			// if(!in_array($color['BICID'], $bicid_array)){
			// 	$bicid_array[] = $color['BICID'];
			// }
		}
	}
	foreach ($bicid_array as $bicidIndex => $bicid) {
		$row_carton_n_nnw = $buyer_po_header->select_total_carton_and_nnw($_POST['invID'], $shipmentpriceID, $bicid, $group_number_array[$bicidIndex]);

		foreach ($row_carton_n_nnw as $carton_n_nnw) {
			$total_ctn = $total_ctn + $carton_n_nnw['total_ctn'];
			$total_nnw = $total_nnw + $carton_n_nnw['total_nnw'];
		}
	}
	echo json_encode([
		'color_qty' => $color_qty,
		'total_ctn' => $total_ctn,
		'total_nnw' => $total_nnw,
	]);
}

if ($type == 'getSections') {
	echo generateSections($_POST['invID']);
}

if ($type == 'addRow') {
	echo generateRow([], $_POST['INVCHID'], $_POST['shipmentpriceID'], 0, 0);
}

if ($type == 'addSection') {
	echo generateSection(true, $_POST['INVCHID'], $_POST['shipmentpriceID'], $_POST['invID']);
}

// Function to generate all sections

function generateSections($invID)
{
	global $buyer_po_header;
	global $last_cost_head_id;

	// Fetch buyer PO records
	$row_buyer_po = $buyer_po_header->select_buyer_po($invID);
	$html = '';

	foreach ($row_buyer_po as $buyer_po) {
		// Fetch existing cost_head records for this shipmentpriceID
		$row_cost_head = $buyer_po_header->select_cost_head($invID, $buyer_po['shipmentpriceID']);

		$html .= '<div class="card card-default order-section mb-2" data-section="' . $buyer_po['shipmentpriceID'] . '">
            <div class="card-header">
                <table>
                    <tr>
                        <th>PO#:</th>
                        <td>' . $buyer_po['GTN_buyerpo'] . '</td>
                        <th class="pl-2">ITEM/STYLE#:</th>
                        <td>' . $buyer_po['GTN_styleno'] . '</td>
                        <td class="pl-2"><button type="button" class="btn btn-sm btn-primary" onclick="addSection(this, 0, ' . $buyer_po['shipmentpriceID'] . ')"><i class="fa-solid fa-plus"></i></button></td>
                    </tr>
                </table>
            </div>
            <div class="card-body" id="cost_head_' . $buyer_po['shipmentpriceID'] . '">';

		// If no cost_head records exist, generate one new section with one new row
		if (empty($row_cost_head)) {
			$html .= generateSection(true, $last_cost_head_id++, $buyer_po['shipmentpriceID'], $invID); // Pass 0 for INVCHID to indicate a new section
		} else {
			// Generate sections for each cost_head
			foreach ($row_cost_head as $cost_head) {
				$html .= generateSection(false, $cost_head['INVCHID'], $cost_head['shipmentpriceID'], $invID);
			}
		}

		$html .= '</div></div>';
	}
	$html .= '<input type="hidden" id="last_cost_head_id" value="' . $last_cost_head_id . '">';

	return $html;
}

// Function to generate a section

function generateSection($isNew, $INVCHID, $shipmentpriceID, $invID)
{
	global $buyer_po_header;
	global $handle_misc;
	global $conn;

	$existing_color_ids = [];
	$shipping_marking = '';
	$existing_color_str = ''; // Default to an empty string
	$isNewSection = 'n'; // Determine if this is a new section
	$total_ctn = 0;
	$total_nnw = 0;

	$row_shipping_marking = $buyer_po_header->select_shipping_marking($invID, $shipmentpriceID);

	// Fetch colors for this shipmentpriceID
	$row_color = $buyer_po_header->select_po_color($invID, $shipmentpriceID);

	// Fetch existing cost_detail records for this cost_head
	$row_cost_detail = $INVCHID != 0 ? $buyer_po_header->select_cost_detail($INVCHID) : [];

	$model_cost_head = new tblbuyer_invoice_payment_cost_head($conn, $handle_misc);
	$arr_cost_head = $model_cost_head->getAllByArr(['INVCHID' => $INVCHID]);
	$row_cost_head = $arr_cost_head['row'];

	if ($isNew) {
		$existing_color_str = '';
		$isNewSection = 'y'; // Set to 'y' for new section
		if (isset($row_shipping_marking[0])) {
			$shipping_marking = $row_shipping_marking[0]['shipping_marking'];
		}
	} else {
		if (!empty($row_cost_head)) {
			$row_cost_head = $row_cost_head[0];
			$existing_color_ids = explode(',', $row_cost_head['colorID']);
			$shipping_marking = $row_cost_head['item_desc'];
			$existing_color_str = $row_cost_head['colorID']; // Set to the colorID string if available
		}
	}
	foreach ($row_cost_detail as $cost_detail) {
		$total_ctn = $total_ctn + $cost_detail['ctn_qty'];
		$total_nnw = $total_nnw + $cost_detail['total_nnw'];
	}

	$html = '
        <div class="cost-head-section border p-2 mb-2">
            <table class="mb-2 cost-head-row">
                <tr>
                    <td>
                        <button type="button" class="btn btn-danger btn-xs pull-right" onclick="removeSection(this, ' . $INVCHID . ')"><i class="fa-solid fa-trash"></i></button>
                    </td>
                    <td>
                        <strong>Color:</strong>
                    </td>
                    <td style="width:30%">
                        <select name="color_array[' . $INVCHID . '][]" data-INVCHID="' . $INVCHID . '" data-invID="' . $invID . '" data-shipmentpriceID="' . $shipmentpriceID . '" class="form-control color-select" multiple>
                            ' . generateColorOptions($row_color, $existing_color_ids) . '
                        </select>
                        <font color="red">*</font>
                        <br>
                        <font color="red" class="valid_color"></font>
                        <input type="hidden" name="color[]" class="color-string" value="' . $existing_color_str . '">
                        <input type="hidden" name="" class="ctn" value="' . $total_ctn . '">
                        <input type="hidden" name="" class="total_nnw" value="' . $total_nnw . '">
                    </td>
                    <td>
                        <strong>Description:</strong>
                    </td>
                    <td style="width:50%">
                        <input name="shipping_marking[]" class="form-control" value="' . $shipping_marking . '">
                    </td>
                    <td>
                        <input type="hidden" name="ch_new_head[]" value="' . $isNewSection . '">
                        <input type="hidden" name="ch_invchid[]" value="' . $INVCHID . '">
                        <input type="hidden" name="ch_invID[]" value="' . $invID . '">
                        <input type="hidden" name="ch_shipmentpriceID[]" value="' . $shipmentpriceID . '">
                    </td>
                </tr>
            </table>
            <table class="table table-bordered cost_detail_row">
                <thead>
                    <tr>
                        <th><button type="button" class="btn btn-success btn-xs" onclick="addRow(this, ' . $shipmentpriceID . ', ' . $INVCHID . ')">+</button></th>
                        <th>Item Description</th>
                        <th>Qty</th>
                        <th>Unit Price<font color="red">*</font></th>
                        <th>Total Amount</th>
						<th>Carton qty</th>
                        <th>NNW per carton(KG)</th>
                        <th>Total NNW (KG)</th>
                    </tr>
                </thead>
                <tbody class="items">';

	// Generate rows for each cost_detail
	if (!empty($row_cost_detail)) {
		foreach ($row_cost_detail as $cost_detail) {
			$html .= generateRow($cost_detail, $INVCHID, $shipmentpriceID, $total_ctn, $total_nnw);
		}
	} else {
		// If no cost_detail records exist, generate one new row
		$html .= generateRow([], $INVCHID, $shipmentpriceID, $total_ctn, $total_nnw);
	}

	$html .= '</tbody></table></div>';

	return $html;
}

// Function to generate a row
function generateRow($cost_detail, $INVCHID, $shipmentpriceID, $total_ctn, $total_nnw)
{
	$isNew = empty($cost_detail) ? 'y' : 'n';
	$ID = $isNew === 'y' ? '' : $cost_detail['ID'];
	$item_desc = $isNew === 'y' ? '' : $cost_detail['item_desc'];
	$qty = $isNew === 'y' ? '' : $cost_detail['qty'];
	$unitprice = $isNew === 'y' ? '' : $cost_detail['unitprice'];
	$ctn_qty = $isNew === 'y' ? '' : $cost_detail['ctn_qty'];
	$nnw = $isNew === 'y' ? '' : $cost_detail['total_nnw'];
	$nnw_pc = 0;
	if ($total_nnw != 0 && $ctn_qty != 0) {
		$nnw_pc = number_format($nnw / $ctn_qty, 2);
	}

	return '
        <tr>
            <td>
                <button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this, ' . $ID . ')"><i class="fa-solid fa-trash"></i></button>
            </td>
            <td><input type="text" name="item_description[]" class="form-control" value="' . $item_desc . '"></td>
            <td><input type="text" name="qty[]" class="form-control qty-' . $INVCHID . '" value="' . $qty . '" readonly></td>
            <td>
                <input type="number" name="unit_price[]" data-INVCHID="' . $INVCHID . '" class="form-control unit-price" value="' . $unitprice . '" oninput="calculateTotal(this, ' . $INVCHID . ')">
                <font color="red" class="valid_unit_price"></font>
            </td>
            <td class="total-amount">' . ($qty * $unitprice) . '</td>
			<td><input type="text" name="ctn_qty[]" class="form-control carton-qty" value="' . $ctn_qty . '" readonly></td>
            <td><input type="text" name="nnw_pc[]" class="form-control nnw_pc" value="' . $nnw_pc . '" readonly></td>
            <td>
                <input type="text" name="total_nnw[]" class="form-control nnw" value="' . $nnw . '" readonly>
                <input type="hidden" name="cd_new_detail[]" value="' . $isNew . '">
                <input type="hidden" name="cd_cost_detail_id[]" value="' . $ID . '">
                <input type="hidden" name="cd_invchid[]" value="' . $INVCHID . '">
                <input type="hidden" name="cd_shipmentpriceID[]" value="' . $shipmentpriceID . '">
            </td>
        </tr>';
}

// Function to generate color options
function generateColorOptions($row_color, $existing_color_ids)
{
	$options = '';
	foreach ($row_color as $color) {
		$selected = in_array($color['colorID'], $existing_color_ids) ? 'selected' : '';
		$options .= '<option value="' . $color['colorID'] . '" ' . $selected . '>' . $color['color'] . '</option>';
	}
	return $options;
}
