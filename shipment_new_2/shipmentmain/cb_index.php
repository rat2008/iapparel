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
// $INVCHID = $last_cost_head_id;

$INVCHID = 0;
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
	<div class="container p-3 mb-5">
		<form id="order-form" action="cb_index_save.php" method="POST" onsubmit="return validateForm();">
			<input type="hidden" name="delete_cost_head_id">
			<input type="hidden" name="delete_cost_detail_id">
			<div class="row mb-2">
				<div class="col-md-12">
					<button type="button" onclick="finalcheck()" class="btn btn-success">Save</button>
				</div>
			</div>
			<div id="order-sections">
			</div>
		</form>
	</div>
</body>

</html>
<script>
	$(document).ready(function() {
		loadSections();
		$('.color-select').select2();
	});

	let last_invchid = <?= $last_cost_head_id ?>;

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
		// Count the number of remaining sections
		const remainingSections = $(btn).closest('.card-body').find('.cost-head-section').length;

		// Prevent deletion if only one section remains
		if (remainingSections <= 1) {
			alert("At least one section must remain.");
			return;
		}

		if (INVCHID) {
			temp = $('input[name="delete_cost_head_id"]').val();
			let deleteStr = INVCHID + ',' + temp;
			$('input[name="delete_cost_head_id"]').val(deleteStr);
		}
		$(btn).closest('.cost-head-section').remove();
	}

	function removeRow(btn, ID) {
		// Count the number of remaining rows in the current section
		const remainingRows = $(btn).closest('tbody').find('tr').length;

		// Prevent deletion if only one row remains
		if (remainingRows <= 1) {
			alert("At least one row must remain.");
			return;
		}

		if (ID) {
			temp = $('input[name="delete_cost_detail_id"]').val();
			let deleteStr = ID + ',' + temp;
			$('input[name="delete_cost_detail_id"]').val(deleteStr);
		}
		$(btn).closest('tr').remove();
		calculateAllTotal();
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
					obj.closest('td').find('.total_nnw').val(data['total_nnw']);
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
			total_nnw = $(this).closest('.cost-head-section').find('.total_nnw').val();
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

			$(this).find('.carton-qty').each(function(index) {
				let ratio = ratio_array[index] || 0;
				let ctn_qty = ratio * total_ctn;
				$(this).val(ctn_qty.toFixed(0));
			});
			$(this).find('.nnw').each(function(index) {
				let ratio = ratio_array[index] || 0;
				let nnw = ratio * total_nnw;
				let ctn_qty = $(this).closest('tr').find('.carton-qty').val() || 0;
				let ctn = 0;

				if (ctn_qty != 0) {
					ctn = nnw / ctn_qty;
				}

				$(this).closest('tr').find('.nnw_pc').val(ctn.toFixed(2));

				$(this).val(nnw.toFixed(2));

			});
			$(this).find('.display-total-nnw').each(function(index) {
				$(this).html(total_nnw);
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

	function loadSections() {
		$.ajax({
			url: "../../ajax/ajax_cb.php",
			method: "POST",
			data: {
				invID: <?= $invID ?>,
				type: 'getSections'
			},
			success: function(data) {
				$('#order-sections').html(data);
				$('.color-select').select2();
			},
			error: function(error) {
				console.error("Error loading sections:", error);
			}
		});
	}

	function validateForm() {
		let isValid = true;

		// Check if any unit price is empty
		$('.unit-price').each(function() {
			if ($(this).val() === '') {
				isValid = false;
				$(this).closest('tr').find('.valid_unit_price').text('Unit Price is required');
			} else {
				$(this).closest('tr').find('.valid_unit_price').text('');
			}
		});

		$('.color-select').each(function() {
			if ($(this).val() === null || $(this).val().length === 0) {
				isValid = false;
				$(this).closest('td').find('.valid_color').text('Color is required');
			} else {
				$(this).closest('td').find('.valid_color').text('');
			}
		});

		if (!isValid) {
			alert("Please fill in all required fields.");
			return false;
		}

		return true;
	}

	function finalcheck() {
		if (confirm("Save?")) {
			$("#order-form").submit();
		} else {
			return false;
		}
	}
</script>