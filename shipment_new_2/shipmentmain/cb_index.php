<?php
include("../../lock.php");
include("../../function/misc.php");
include_once("../../cf/cs_cb.php");

$handle_misc = new misc($conn);

$mainurl = $handle_misc->getAPIURL();


$method = "POST";
$url    = $mainurl . "/";
$data   = array();

$arr_result = $handle_misc->funcCallAPI($method, $url, $data);

$_misc = new misc($conn);

$buyer_po_header = new cs_cb($conn, $_misc);
$i = 0;
$invID = $_GET['invID'];
$row_buyer_po = $buyer_po_header->select_buyer_po($_GET['invID']);

$last_cost_head_id = $handle_misc->funcMaxID('tblbuyer_invoice_payment_cost_head', "INVCHID");
$INVCHID = $last_cost_head_id;
// var_dump('<pre>');
// 	var_dump($last_cost_head_id);
// 	die();
if (!empty($_POST)) {
	var_dump('<pre>');
	var_dump($_POST);
	die();
}

?>
<!DOCTYPE html>
<html>

<head>
	<?php include '../../media/medialink.php'; ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

	<style>
		.container {
			font-size: 12px;
		}

		.form-control {
			font-size: 12px;
		}
	</style>
</head>

<body>
	<div class="container">
		<h3>Order Form</h3>
		<form id="order-form" action="../../cf/func_cb.php" method="POST">
			<div id="order-sections">
				<input name="delete_cost_head_id" type="text">
				<input name="delete_cost_detail_id" type="text">
				<?php foreach ($row_buyer_po as $index => $buyer_po) {
					$row_shipping_marking = $buyer_po_header->select_shipping_marking($_GET['invID'], $buyer_po['shipmentpriceID']);
					$row_color = $buyer_po_header->select_po_color($_GET['invID'], $buyer_po['shipmentpriceID']);
					$row_cost_head = $buyer_po_header->select_cost_head($_GET['invID'], $buyer_po['shipmentpriceID']);
					$cost_head_colors = [];

					if (!empty($row_cost_head)) {
						$cost_head_colors = explode(',', $row_cost_head[0]['colorID']);
					}

					$shipping_marking = '';
					if (isset($row_shipping_marking)) {
						$shipping_marking = $row_shipping_marking[0]['shipping_marking'];
					}
				?>
					<div class="card card-default order-section mb-2" data-section="<?= $buyer_po['shipmentpriceID'] ?>">
						<div class="card-header">
							<table>
								<tr>
									<th>PO#:</th>
									<td><?= $buyer_po['GTN_buyerpo'] ?></td>
									<th class="pl-2">ITEM/STYLE#:</th>
									<td><?= $buyer_po['GTN_styleno'] ?></td>
									<td class="pl-2"><button type="button" class="btn btn-sm btn-primary" onclick="addSection(this, <?= $INVCHID ?>,<?= $buyer_po['shipmentpriceID'] ?>)"><i class="fa-solid fa-plus"></i></button></td>
								</tr>
							</table>
						</div>
						<div class="card-body" id="cost_head_<?= $INVCHID ?>">
							<?php foreach ($row_cost_head as $cost_head) {
								$color_qty = 0;
								$color_id = [];
								$INVCHID = $cost_head['INVCHID'];

								$color_id = explode(',', $cost_head['colorID']);

								foreach ($row_color as $color) {
									if (in_array($color['colorID'], $color_id)) {
										$color_qty = $color_qty + $color['qty'];
									}
								}

								if (!empty($cost_head['item_desc'])) {
									$shipping_marking = $cost_head['item_desc'];
								}
							?>
								<div class="cost-head-section border p-2 mb-2">
									<table class="mb-2">
										<tr>
											<td>
												<button type="button" class="btn btn-danger btn-xs pull-right" onclick="removeSection(this, <?= $INVCHID ?>)">&times;</button>
											</td>
											<td>
												<strong>Color:</strong>
											</td>
											<td style="width:30%">
												<select name="color_array[<?= $index ?>][]" data-INVCHID="<?= $INVCHID ?>" data-invID="<?= $_GET['invID'] ?>" data-shipmentpriceID="<?= $buyer_po['shipmentpriceID'] ?>" class="form-control color-select" readonly multiple>
													<?php foreach ($row_color as $color_option) {
														$selected_color = in_array($color_option['colorID'], explode(',', $cost_head['colorID'])) ? 'selected' : '' ?>
														<option value="<?= $color_option['colorID'] ?>" <?= $selected_color ?>><?= $color_option['color'] ?></option>
													<?php } ?>
												</select>
												<input type="hidden" name="color[]" class="color-string" value="<?= $cost_head['colorID'] ?>">
												<input type="hidden" name="" class="ctn" value="0">
												<input type="hidden" name="" class="nnw" value="0">
											</td>
											<td>
												<strong>Description:</strong>
											</td>
											<td style="width:50%">
												<input name="shipping_marking[]" class="form-control" value="<?= $shipping_marking ?>">
												<input type="hidden" name="ch_new_head[]" value="n">
												<input type="hidden" name="ch_invchid[]" value="<?= $cost_head['INVCHID'] ?>">
												<input type="hidden" name="ch_invID[]" value="<?= $_GET['invID'] ?>">
												<input type="hidden" name="ch_shipmentpriceID[]" value="<?= $buyer_po['shipmentpriceID'] ?>">
											</td>
										</tr>
									</table>
									<table class="table table-bordered cost_detail_row">
										<thead>
											<tr>
												<th><button type="button" class="btn btn-success btn-xs" onclick="addRow(this, <?= $buyer_po['shipmentpriceID'] ?>,<?= $INVCHID ?>)">+</button></th>
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
														<button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this, <?= $cost_detail['ID'] ?>)">&times;</button>
													</td>
													<td><input type="text" name="item_description[]" class="form-control" value="<?= $cost_detail['item_desc'] ?>"></td>
													<td><input type="text" name="qty[]" class="form-control qty-<?= $INVCHID ?>" value="<?= $color_qty ?>" readonly></td>
													<td><input type="text" name="unit_price[]" class="form-control unit-price" data-INVCHID="<?= $INVCHID ?>" oninput="calculateTotal(this, <?= $INVCHID ?>)" value="<?= $cost_detail['unitprice'] ?>"></td>
													<td class="total-amount"><?= $color_qty * $cost_detail['unitprice'] ?></td>
													<td><input type="text" name="nnwctns[]" class="form-control nnwctns" value="<?= $cost_detail['ctn_qty'] ?>" readonly></td>
													<td>
														<input type="text" name="total_nnw[]" class="form-control total_nnw" value="<?= $cost_detail['total_nnw'] ?>" readonly>
														<input type="hidden" name="cd_new_detail[]" value="n">
														<input type="hidden" name="cd_cost_detail_id[]" value="<?= $cost_detail['ID'] ?>">
														<input type="hidden" name="cd_invchid[]" value="<?= $INVCHID ?>">
														<input type="hidden" name="cd_shipmentpriceID[]" value="<?= $buyer_po['shipmentpriceID'] ?>">
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
													<td><input type="text" name="total_nnw[]" class="form-control total_nnw" readonly>
														<input type="hidden" name="cd_new_detail[]" value="y">
														<input type="hidden" name="cd_cost_detail_id[]" value="">
														<input type="hidden" name="cd_invchid[]" value="<?= $INVCHID ?>">
														<input type="hidden" name="cd_shipmentpriceID[]" value="<?= $buyer_po['shipmentpriceID'] ?>">
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							<?php } ?>
							<?php if (empty($row_cost_head)) { ?>
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
												<select name="color_array[<?= $index ?>][]" class="form-control color-select" data-INVCHID="<?= $INVCHID ?>" data-invID="<?= $_GET['invID'] ?>" data-shipmentpriceID="<?= $buyer_po['shipmentpriceID'] ?>" readonly multiple>
													<?php foreach ($row_color as $color) {
														$select = in_array($color['colorID'], $cost_head_colors) ? 'selected' : '' ?>
														<option value="<?= $color['colorID'] ?>" <?= $select ?>><?= $color['color'] ?></option>
													<?php } ?>
												</select>
												<input type="hidden" name="color[]" class="color-string" value="<?= implode(',', $cost_head_colors) ?>">
												<input type="hidden" name="" class="ctn" value="">
												<input type="hidden" name="" class="nnw" value="">
											</td>
											<td>
												<strong>Description:</strong>
											</td>
											<td style="width:50%">
												<input name="shipping_marking[]" class="form-control" value="<?= $shipping_marking ?>">
												<input type="hidden" name="ch_new_head[]" value="y">
												<input type="hidden" name="ch_invchid[]" value="<?= $INVCHID ?>">
												<input type="hidden" name="ch_invID[]" value="<?= $_GET['invID'] ?>">
												<input type="hidden" name="ch_shipmentpriceID[]" value="<?= $buyer_po['shipmentpriceID'] ?>">
											</td>
										</tr>
									</table>
									<table class="table table-bordered cost_detail_row">
										<thead>
											<tr>
												<th><button type="button" class="btn btn-success btn-xs" onclick="addRow(this, <?= $buyer_po['shipmentpriceID'] ?>,<?= $INVCHID ?>)">+</button></th>
												<th>Item Description</th>
												<th>Qty</th>
												<th>Unit Price</th>
												<th>Total Amount</th>
												<th>NNWCTNS (KG)</th>
												<th>Total NNW (KG)</th>
											</tr>
										</thead>
										<tbody class="items">
											<tr>
												<td>
													<button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this)">&times;</button>
												</td>
												<td><input type="text" name="item_description[]" class="form-control"></td>
												<td><input type="text" name="qty[]" class="form-control qty-<?= $INVCHID ?>" readonly></td>
												<td><input type="text" name="unit_price[]" class="form-control unit-price" data-INVCHID="<?= $INVCHID ?>" oninput="calculateTotal(this, <?= $INVCHID ?>)"></td>
												<td class="total-amount">0</td>
												<td><input type="text" name="nnwctns[]" class="form-control nnwctns" readonly></td>
												<td>
													<input type="text" name="total_nnw[]" class="form-control total_nnw" readonly>
													<input type="hidden" name="cd_new_detail[]" value="y">
													<input type="hidden" name="cd_cost_detail_id[]" value="">
													<input type="hidden" name="cd_invchid[]" value="<?= $INVCHID ?>">
													<input type="hidden" name="cd_shipmentpriceID[]" value="<?= $buyer_po['shipmentpriceID'] ?>">
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<?php $INVCHID++; ?>
							<?php } ?>
						</div>
					</div>
				<?php $i++;
				} ?>
			</div>
			<div style="float:right">
				<button type="submit" class="btn btn-success">Save</button>
			</div>
		</form>
	</div>
</body>

</html>
<script>
	$(document).ready(function() {
		$('.color-select').select2();
		
	});

	let last_invchid = <?= $INVCHID ?>;

	function addSection(btn, INVCHID, shipmentpriceID) {
		// sectionCount++;
		last_invchid++;
		let js_shipmentpriceID = shipmentpriceID;
		$.ajax({
			url: "../../ajax/ajax_cb.php",
			method: "POST",
			data: {
				INVCHID: last_invchid,
				shipmentpriceID: shipmentpriceID,

				// last_invchid: last_invchid,
				invID: <?= $invID ?>,
				type: 'addSection'
			},
			success: function(data) {
				$(btn).closest('.card').find('.card-body').append(data);
				// $('#cost_head_' + INVCHID).append(data);
				// $('#spid'+shipmentpriceID).val(js_cost_detail_id)
				$('.color-select').select2();

				$(btn).closest('.card').find('.color-select').each(function() {
					updateColorOptions($(this));
				});
			}
		})

		// $('#cost_head_'+INVCHID).append(sectionHtml);
		// $('.color-select').select2();
	}

	async function addRow(btn, shipmentpriceID, INVCHID) {
		try {
			const response = await $.ajax({
				url: "../../ajax/ajax_cb.php",
				method: "POST",
				data: {
					INVCHID,
					shipmentpriceID,
					invID: <?= $invID ?>,
					type: 'addRow'
				}
			});

			$(btn).closest('table').find('.items').append(response);
			updateAllColorSelects();
			calculateAllNNWCTNS();
		} catch (error) {
			console.error("Error adding row:", error);
		}
	}

	function removeSection(btn, INVCHID) {
		if (INVCHID) {
			temp = $('input[name="delete_cost_head_id"]').val();
			let deleteStr = INVCHID + ',' + temp;
			$('input[name="delete_cost_head_id"]').val(deleteStr);
		}
		$(btn).closest('.cost-head-section').remove();
	}

	function removeRow(btn, ID) {
		if (ID) {
			temp = $('input[name="delete_cost_detail_id"]').val();
			let deleteStr = ID + ',' + temp;
			$('input[name="delete_cost_detail_id"]').val(deleteStr);
		}
		$(btn).closest('tr').remove();
	}

	function calculateTotal(input, INVCHID) {
		let unitPrice = parseFloat($(input).val()) || 0;
		let qty = parseFloat($('.qty-' + INVCHID).val()) || 0;
		let totalAmount = unitPrice * qty;
		$(input).closest('tr').find('.total-amount').text(totalAmount.toFixed(2));
		calculateAllNNWCTNS();
	}

	$(document).on('change', '.color-select', function() {
		updateColorOptions($(this));
		const selected = $(this).val(); // e.g. ["Red", "Green"]

		const colorStr = selected ? selected.join(',') : '';
		let obj = $(this);

		$(this).closest('td').find('.color-string').val(colorStr);

		let invID = $(this).attr("data-invID");
		let INVCHID = $(this).attr("data-INVCHID");
		let shipmentpriceID = $(this).attr("data-shipmentpriceID");

		$.ajax({
			url: "../../ajax/ajax_cb.php",
			method: "POST",
			data: {
				invID: invID,
				INVCHID: INVCHID,
				shipmentpriceID: shipmentpriceID,
				color_id: colorStr,
				type: 'getQty'
			},
			success: function(data) {
				data = JSON.parse(data);
				$('.qty-' + INVCHID).val(data['color_qty']);
				obj.closest('td').find('.ctn').val(data['total_ctn']);
				obj.closest('td').find('.nnw').val(data['total_nnw']);
				calculateAllTotal();
				calculateAllNNWCTNS();
			}
		})
	});

	function updateAllColorSelects() {
		$('.color-select').each(function() {
			const selected = $(this).val();
			const colorStr = selected ? selected.join(',') : '';

			$(this).closest('td').find('.color-string').val(colorStr);

			let obj = $(this);
			let invID = $(this).attr("data-invID");
			let INVCHID = $(this).attr("data-INVCHID");
			let shipmentpriceID = $(this).attr("data-shipmentpriceID");

			$.ajax({
				url: "../../ajax/ajax_cb.php",
				method: "POST",
				data: {
					invID: invID,
					INVCHID: INVCHID,
					shipmentpriceID: shipmentpriceID,
					color_id: colorStr,
					type: 'getQty'
				},
				success: function(data) {
					data = JSON.parse(data);
					$('.qty-' + INVCHID).val(data['color_qty']);
					obj.closest('td').find('.ctn').val(data['total_ctn']);
					obj.closest('td').find('.nnw').val(data['total_nnw']);
				}
			});
		});
	}

	function calculateAllTotal() {
		$('.unit-price').each(function() {
			let unitPrice = parseFloat($(this).val()) || 0;
			let INVCHID = $(this).attr("data-INVCHID");
			let qty = parseFloat($('.qty-' + INVCHID).val()) || 0;
			let totalAmount = unitPrice * qty;

			$(this).closest('tr').find('.total-amount').text(totalAmount.toFixed(2));
		});

		calculateAllNNWCTNS();
	}

	function calculateAllNNWCTNS() {
		$('.cost_detail_row').each(function() {
			let unit_price_array = [];
			let ratio_array = [];
			let total_ctn = 0;
			let total_nnw = 0;
			let total_unit_price = 0;

			total_ctn = $(this).closest('.cost-head-section').find('.ctn').val();
			total_nnw = $(this).closest('.cost-head-section').find('.nnw').val();
			total_ctn = parseFloat(total_ctn);
			total_nnw = parseFloat(total_nnw);

			$(this).find('.unit-price').each(function() {
				let unitPrice = parseFloat($(this).val()) || 0;
				let INVCHID = $(this).attr("data-INVCHID");
				let qty = parseFloat($('.qty-' + INVCHID).val()) || 0;
				let totalAmount = unitPrice * qty;
				total_unit_price = total_unit_price + unitPrice;

				unit_price_array.push(unitPrice);
			});

			unit_price_array.forEach(function(item, index, arr) {
				let temp_unit_price = item || 0;
				ratio_array[index] = parseFloat(item) / total_unit_price;
			});

			$(this).find('.nnwctns').each(function(index) {
				let ratio = ratio_array[index] || 0;
				let ctn = ratio * total_ctn;
				$(this).val(ctn.toFixed(2));
			});
			$(this).find('.total_nnw').each(function(index) {
				let ratio = ratio_array[index] || 0;
				let nnw = ratio * total_nnw;
				$(this).val(nnw.toFixed(2));
			});
		});
	}

	function updateColorOptions(changedSelect) {
		// Get the parent cost-head-section of the changed select
		let costHeadSection = changedSelect.closest('.card-body');

		// Collect all selected colors in the same cost-head-section
		let selectedColors = [];
		costHeadSection.find('.color-select').each(function() {
			let selected = $(this).val();
			if (selected) {
				selectedColors = selectedColors.concat(selected);
			}
		});

		// Update options for all color-select inputs in the same cost-head-section
		costHeadSection.find('.color-select').each(function() {
			let currentSelect = $(this);
			let currentSelected = currentSelect.val() || [];

			// Remove all options
			currentSelect.find('option').each(function() {
				$(this).prop('disabled', false); // Enable all options first
			});

			// Disable options that are selected in other inputs
			costHeadSection.find('.color-select').not(currentSelect).each(function() {
				let otherSelected = $(this).val() || [];
				otherSelected.forEach(function(color) {
					currentSelect.find(`option[value="${color}"]`).prop('disabled', true);
				});
			});

			// Reapply the current selection
			currentSelect.val(currentSelected).trigger('change.select2');
		});
	}
</script>