<?php

include("../../lock.php");
include("../../model/tblbuyer_invoice_payment_cost_head.php");
include("../../model/tblbuyer_invoice_payment_cost_detail.php");
include("../../function/misc.php");

$handle_misc = new misc();

if ($_POST) {
    // var_dump('<pre>');
    // var_dump($_POST);
    // die();
    // Fetch POST data
    $color                   = $_POST['color'];               // array of strings
    $shipping_marking        = $_POST['shipping_marking'];    // array of strings
    $ch_new_head             = $_POST['ch_new_head'];         // array of 'n' or 'y'
    $ch_invchid              = $_POST['ch_invchid'];          // array of numeric strings
    $ch_invID                = $_POST['ch_invID'];            // array of invoice IDs
    $ch_shipmentpriceID      = $_POST['ch_shipmentpriceID'];  // array of shipment price IDs

    $item_description        = $_POST['item_description'];    // array of detail descriptions
    $unit_price              = $_POST['unit_price'];          // array of unit prices
    $qty                       = $_POST['qty'];                // array of quantities
    $ctn_qty                 = $_POST['ctn_qty'];             // array of carton weights
    $total_nnw               = $_POST['total_nnw'];           // array of total weights

    $cd_new_detail           = $_POST['cd_new_detail'];       // array 'y' or 'n'
    $cd_cost_detail_id       = $_POST['cd_cost_detail_id'];   // array of IDs
    $cd_invchid              = $_POST['cd_invchid'];          // array matching detail to section
    $cd_shipmentpriceID      = $_POST['cd_shipmentpriceID'];

    $delete_cost_head_id     = explode(',', $_POST['delete_cost_head_id']);
    $delete_cost_detail_id  = explode(',', $_POST['delete_cost_detail_id']);

    $model_cost_head = new tblbuyer_invoice_payment_cost_head($conn, $handle_misc);
    $model_cost_detail = new tblbuyer_invoice_payment_cost_detail($conn, $handle_misc);

    // Process cost heads
    foreach ($ch_new_head as $cost_head_index => $isNew) {
        if ($isNew == 'y') {
            // Create new cost head
            $model_cost_head->invID = $ch_invID[$cost_head_index];
            $model_cost_head->shipmentpriceID = $ch_shipmentpriceID[$cost_head_index];
            $model_cost_head->colorID = $color[$cost_head_index];
            $model_cost_head->item_desc = $shipping_marking[$cost_head_index];
            $temp_invchid = $ch_invchid[$cost_head_index];

            $model_cost_head->create();

            // Create cost details for the new cost head
            foreach ($cd_invchid as $cd_invchid_index => $invchid) {
                if ($invchid == $temp_invchid) {
                    if ($cd_new_detail[$cd_invchid_index] == 'y') {
                        $model_cost_detail->ID = $cd_cost_detail_id[$cd_invchid_index];
                        $model_cost_detail->INVCHID = $model_cost_head->INVCHID;
                        $model_cost_detail->item_desc = $item_description[$cd_invchid_index];
                        $model_cost_detail->qty = $qty[$cd_invchid_index];
                        $model_cost_detail->unitprice = $unit_price[$cd_invchid_index];
                        $model_cost_detail->ctn_qty = $ctn_qty[$cd_invchid_index];
                        $model_cost_detail->total_nnw = $total_nnw[$cd_invchid_index];

                        $model_cost_detail->create();
                    }
                    if ($cd_new_detail[$cd_invchid_index] == 'n') {
                        $data = [
                            "ID" => $cd_cost_detail_id[$cd_invchid_index],             // required for WHERE condition
                            "INVCHID" => $model_cost_head->INVCHID,
                            "item_desc" => $item_description[$cd_invchid_index],
                            "qty" => $qty[$cd_invchid_index],
                            "unitprice" => $unit_price[$cd_invchid_index],
                            "ctn_qty" => $ctn_qty[$cd_invchid_index],
                            "total_nnw" => $total_nnw[$cd_invchid_index],
                            "del" => 0,
                            "delby" => null,
                            "delDate" => null
                        ];
                        $model_cost_detail->update($data);
                    }
                }
            }
        } elseif ($isNew == 'n') {
            // Update existing cost head
            $temp_invchid = $ch_invchid[$cost_head_index];
            $data = [
                "INVCHID" => $ch_invchid[$cost_head_index],                 // required for WHERE condition
                "shipmentpriceID" => $ch_shipmentpriceID[$cost_head_index],
                "colorID" => $color[$cost_head_index],
                "item_desc" => $shipping_marking[$cost_head_index],
                "del" => 0,
                "delBy" => null,
                "delDate" => null
            ];
            $model_cost_head->update($data);
            
            foreach ($cd_new_detail as $cost_detail_index => $isNew) {
                if ($cd_invchid[$cost_detail_index] == $temp_invchid) {
                    
                    if ($isNew == 'y') {
                        // Create new cost detail
                        $model_cost_detail->INVCHID = $temp_invchid;
                        $model_cost_detail->item_desc = $item_description[$cost_detail_index];
                        $model_cost_detail->qty = $qty[$cost_detail_index];
                        $model_cost_detail->unitprice = $unit_price[$cost_detail_index];
                        $model_cost_detail->ctn_qty = $ctn_qty[$cost_detail_index];
                        $model_cost_detail->total_nnw = $total_nnw[$cost_detail_index];
                        
                        $model_cost_detail->create();
                    } elseif ($isNew == 'n') {
                        // Update existing cost detail
                        $data = [
                            "ID" => $cd_cost_detail_id[$cost_detail_index],             // required for WHERE condition
                            "INVCHID" => $temp_invchid,
                            "item_desc" => $item_description[$cost_detail_index],
                            "qty" => $qty[$cost_detail_index],
                            "unitprice" => $unit_price[$cost_detail_index],
                            "ctn_qty" => $ctn_qty[$cost_detail_index],
                            "total_nnw" => $total_nnw[$cost_detail_index],
                            "del" => 0,
                            "delby" => null,
                            "delDate" => null
                        ];
                        $model_cost_detail->update($data);
                    }
                }
            }
        }
    }

    //process delete cost and detail
    foreach ($delete_cost_head_id as $cost_head_id) {
        $arr_cost_head = $model_cost_head->getAllByArr(['INVCHID' => $cost_head_id]);
        $row_cost_head = $arr_cost_head['row'];

        $data = [
            "INVCHID" => $cost_head_id,
            "del" => 1,
            "delBy" => $acctid,
            "delDate" => date('Y-m-d H:i:s')
        ];
        $model_cost_head->update($data);

        foreach ($row_cost_head as $cost_head) {
            $arr_cost_detail = $model_cost_detail->getAllByArr(['INVCHID' => $cost_head_id]);
            $row_cost_detail = $arr_cost_detail['row'];

            foreach ($row_cost_detail as $cost_detail) {
                $data = [
                    "ID" => $cost_detail['ID'],
                    "del" => 1,
                    "delBy" => $acctid,
                    "delDate" => date('Y-m-d H:i:s')
                ];
                $model_cost_detail->update($data);
            }
        }
    }
    foreach ($delete_cost_detail_id as $cost_detail_id) {
        $data = [
            "ID" => $cost_detail_id,
            "del" => 1,
            "delBy" => $acctid,
            "delDate" => date('Y-m-d H:i:s')
        ];
        $model_cost_detail->update($data);
    }

    echo "<script>
        alert('Cost Breakdown Updated');
        window.location.href = 'buyer_inv.php?id=" . $ch_invID[0] . "&isBuyerPayment=true';
    </script>";
}
