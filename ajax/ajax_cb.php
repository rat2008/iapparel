<?php
include("../lock.php");
include("../function/misc.php");
include_once("../cf/cs_cb.php");

$handle_misc = new misc($conn);

$_misc = new misc($conn);

$buyer_po_header = new cs_cb($conn, $_misc);

$type = $_POST['type'];

$shipmentpriceID = $_POST['shipmentpriceID'];
// $sectionCount = $_POST['sectionCount'];
$INVCHID = $_POST['INVCHID'];
$color_id =	[];
$color_qty = 0;
$i = 0;
$row_buyer_po = $buyer_po_header->select_buyer_po($_POST['invID']);
$row_color = $buyer_po_header->select_po_color($_POST['invID'], $shipmentpriceID);

$row_shipping_marking = $buyer_po_header->select_shipping_marking($_POST['invID'], $shipmentpriceID);
$shipping_marking = '';

if (isset($row_shipping_marking[0])) {
	$shipping_marking = $row_shipping_marking[0]['shipping_marking'];
}

if ($type == 'getQty') {
	$color_id = explode(',', $_POST['color_id']);
	$bicid_array = [];
	$total_ctn = 0;
	$total_nnw = 0;

	foreach ($row_color as $color) {
		if (in_array($color['colorID'], $color_id)) {
			$color_qty = $color_qty + $color['qty'];
			$bicid_array[] = $color['BICID'];
		}
	}
	foreach ($bicid_array as $bicid) {
		$row_carton_n_nnw = $buyer_po_header->select_total_carton_and_nnw($_POST['invID'], $shipmentpriceID, $bicid);

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
if ($type == 'addRow') {
?>
	<tr>
		<td>
			<button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this)">&times;</button>
		</td>
		<td><input type="text" name="item_description[]" class="form-control"></td>
		<td><input type="text" name="qty[]" class="form-control qty-<?= $INVCHID ?>" readonly></td>
		<td><input type="text" name="unit_price[]" data-INVCHID="<?= $INVCHID ?>" class="form-control unit-price" oninput="calculateTotal(this, <?= $INVCHID ?>)"></td>
		<td class="total-amount">0</td>
		<td><input type="text" name="nnwctns[]" class="form-control nnwctns" readonly></td>
		<td>
			<input type="text" name="total_nnw[]" class="form-control total_nnw" readonly>
			<input type="hidden" name="cd_new_detail[]" value="y">
			<input type="hidden" name="cd_cost_detail_id[]" value="">
			<input type="hidden" name="cd_invchid[]" value="<?= $INVCHID ?>">
			<input type="hidden" name="cd_shipmentpriceID[]" value="<?= $shipmentpriceID ?>">
		</td>
	</tr>
<?php }
if ($type == 'addSection') { ?>
	<div class="cost-head-section border p-2 mb-2">
		<table class="mb-2">
			<tr>
				<td>
					<button type="button" class="btn btn-danger btn-xs pull-right" onclick="removeSection(this)">&times;</button>
				</td>
				<td>
					<strong>Color:</strong>
				</td>
				<td style="width:30%">
					<select name="color_array[][]" data-INVCHID="<?= $INVCHID ?>" data-invID="<?= $_POST['invID'] ?>" data-shipmentpriceID="<?= $shipmentpriceID ?>" class="form-control color-select" multiple>
						<?php foreach ($row_color as $color) { ?>
							<option value="<?= $color['colorID'] ?>"><?= $color['color'] ?></option>
						<?php } ?>
					</select>
					<input type="hidden" name="color[]" class="color-string" value="">
					<input type="hidden" name="" class="ctn" value="0">
					<input type="hidden" name="" class="nnw" value="0">
				</td>
				<td>
					<strong>Description:</strong>
				</td>
				<td style="width:50%">
					<input name="shipping_marking[]" class="form-control" value="<?= $shipping_marking ?>">
				</td>
				<td>
					<input type="hidden" name="ch_new_head[]" value="y">
					<input type="hidden" name="ch_invchid[]" value="<?= $INVCHID ?>">
					<input type="hidden" name="ch_invID[]" value="<?= $_POST['invID'] ?>">
					<input type="hidden" name="ch_shipmentpriceID[]" value="<?= $shipmentpriceID ?>">
				</td>
			</tr>
		</table>
		<table class="table table-bordered cost_detail_row">
			<thead>
				<tr>
					<th><button type="button" class="btn btn-success btn-xs" onclick="addRow(this, <?= $shipmentpriceID ?>,<?= $INVCHID ?>)">+</button></th>
					<th>Item Description</th>
					<th>Qty</th>
					<th>Unit Price</th>
					<th>Total Amount</th>
					<th>NNWCTNS (KG)</th>
					<th>Total NNW (KG)</th>
				</tr>
			</thead>
			<tbody class="items">
				<?php
				$row_cost_detail = $buyer_po_header->select_cost_detail($INVCHID);

				foreach ($row_cost_detail as $cost_detail) { ?>
					<tr>
						<td>
							<button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this)">&times;</button>
						</td>
						<td><input type="text" name="item_description[]" class="form-control" value="<?= $cost_detail['item_desc'] ?>"></td>
						<td><input type="text" name="qty[]" class="form-control qty-<?= $INVCHID ?>" value="<?= $color_qty ?>" readonly></td>
						<td><input type="text" name="unit_price[]" class="form-control unit-price" data-INVCHID="<?= $INVCHID ?>" oninput="calculateTotal(this, <?= $INVCHID ?>)" value="<?= $cost_detail['unitprice'] ?>"></td>
						<td class="total-amount">0</td>
						<td><input type="text" name="nnwctns[]" class="form-control nnwctns" value="<?= $cost_detail['ctn_qty'] ?>" readonly></td>
						<td>
							<input type="text" name="total_nnw[]" class="form-control total_nnw" value="<?= $cost_detail['total_nnw'] ?>" readonly>
							<input type="hidden" name="cd_new_detail[]" value="n">
							<input type="hidden" name="cd_cost_detail_id[]" value="<?= $cost_detail['ID'] ?>">
							<input type="hidden" name="cd_invchid[]" value="<?= $INVCHID ?>">
							<input type="hidden" name="cd_shipmentpriceID[]" value="<?= $shipmentpriceID ?>">
						</td>
					</tr>
				<?php
				}
				if (empty($row_cost_detail)) { ?>
					<tr>
						<td>
							<button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this)">&times;</button>
						</td>
						<td><input type="text" name="item_description[]" class="form-control"></td>
						<td><input type="text" name="qty[]" class="form-control qty-<?= $INVCHID ?>" value="<?= $color_qty ?>" readonly></td>
						<td><input type="text" name="unit_price[]" class="form-control unit-price" data-INVCHID="<?= $INVCHID ?>" oninput="calculateTotal(this, <?= $INVCHID ?>)"></td>
						<td class="total-amount">0</td>
						<td><input type="text" name="nnwctns[]" class="form-control nnwctns" readonly></td>
						<td>
							<input type="text" name="total_nnw[]" class="form-control total_nnw" readonly>
							<input type="hidden" name="cd_new_detail[]" value="y">
							<input type="hidden" name="cd_cost_detail_id[]" value="">
							<input type="hidden" name="cd_invchid[]" value="<?= $INVCHID ?>">
							<input type="hidden" name="cd_shipmentpriceID[]" value="<?= $shipmentpriceID ?>">
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
<?php } ?>