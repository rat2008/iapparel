<?php

include("../lock.php");
include("../model/tblbuyer_invoice_payment_cost_head.php");
include("../model/tblbuyer_invoice_payment_cost_detail.php");
include("../function/misc.php");

class func_cb
{
	// private $conn;

	function __construct($conn) {}
}

$handle_misc = new misc();

if ($_POST) {
	// var_dump('<pre>');
	// var_dump($_POST);
	// die();
    // array of arrays
	$color               = $_POST['color'];               // array of strings
	$shipping_marking    = $_POST['shipping_marking'];    // array of strings
	$ch_new_head         = $_POST['ch_new_head'];         // array of 'n' or 'y'
	$ch_invchid          = $_POST['ch_invchid'];          // array of numeric strings
	$ch_invID            = $_POST['ch_invID'];            // array of invoice IDs
	$ch_shipmentpriceID  = $_POST['ch_shipmentpriceID'];  // array of shipment price IDs

	$item_description    = $_POST['item_description'];    // array of detail descriptions
	$unit_price          = $_POST['unit_price'];   
	$qty          		 = $_POST['qty'];       // array of unit prices
	$nnwctns             = $_POST['nnwctns'];             // array of carton weights
	$total_nnw           = $_POST['total_nnw'];           // array of total weights

	$cd_new_detail       = $_POST['cd_new_detail'];       // array 'y' or 'n'
	$cd_cost_detail_id   = $_POST['cd_cost_detail_id'];   // array of IDs
	$cd_invchid          = $_POST['cd_invchid'];          // array matching detail to section
	$cd_shipmentpriceID  = $_POST['cd_shipmentpriceID'];

	$model_cost_head = new tblbuyer_invoice_payment_cost_head($conn, $handle_misc);
	$model_cost_detail = new tblbuyer_invoice_payment_cost_detail($conn, $handle_misc);
	// foreach($ch_new_head as )
	foreach($ch_new_head as $cost_head_index => $isNew){
		$data = [];
		
		if($isNew == 'y'){
			
			$model_cost_head->invID = $ch_invID[$cost_head_index];
			$model_cost_head->shipmentpriceID = $ch_shipmentpriceID[$cost_head_index];
			$model_cost_head->colorID = $color[$cost_head_index];
			$temp_invchid = $ch_invchid[$cost_head_index];

			$model_cost_head->create();

			foreach($cd_invchid as $cd_invchid_index => $invchid){
				if($invchid == $temp_invchid){
					$model_cost_detail->INVCHID = $model_cost_head->INVCHID;
					$model_cost_detail->item_desc = $item_description[$cd_invchid_index];
					$model_cost_detail->qty = $qty[$cd_invchid_index];
					$model_cost_detail->unitprice = $unit_price[$cd_invchid_index];
					$model_cost_detail->ctn_qty = $nnwctns[$cd_invchid_index];
					$model_cost_detail->total_nnw = $total_nnw[$cd_invchid_index];

					$model_cost_detail->create();
				}
			}
			// var_dump($model_cost_head->INVCHID);
			// die();
		}

		if($isNew == 'n'){
			$data = [
				"INVCHID" => $ch_invchid[$cost_head_index],                 // required for WHERE condition
				"shipmentpriceID" => $ch_shipmentpriceID[$cost_head_index],
				"colorID" => $color[$cost_head_index],
				// "del" => 0,
				// "delBy" => null,
				// "delDate" => null
			];
			var_dump('<pre>');
			var_dump($data);
			// die();
			$model_cost_head->update($data);
		}
	}
	// var_dump('<pre>');
	// var_dump($_POST);
	// die();
}
