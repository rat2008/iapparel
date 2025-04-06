<?php 
	include("../../lock.php");
	include("lc_class.php");
	include("../../model/tblbuyer_invoice.php");
	include("../../model/tblbuyer_invoice_category.php");
	include("../../model/tblbuyer_invoice_detail.php");
	include("../../model/tblbuyer_invoice_payment.php");
	include("../../model/tblbuyer_invoice_payment_category.php");
	include("../../model/tblbuyer_invoice_payment_detail.php");
	include("../../model/tblcarton_inv_head.php");
	include("../../model/tblcarton_inv_detail.php");
	include("../../model/tblcarton_inv_payment_head.php");
	include("../../model/tblcarton_inv_payment_detail.php");
	include("../../model/tblquotacat.php");
	include("../../model/tblshippingadvise.php");
	include("../../model/tblshippingadviseqty.php");
	include("../../function/misc.php");
	include_once("../../shipment_new/shipmentmain/shipmentmainClass.php");
	include_once("../../model/tblbuyer_invoice_charge_option.php");

	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
	
	$arr_alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	$arr_column = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                        'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                        'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
                        'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
                        'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');

	$updatedby = $acctid;
	$updateddate = date("Y-m-d H:i:s");
	$isBuyerPayment = $_POST["isBuyerPayment"];

	$invID = "";
	$arr_shipID = array();
	$handle_misc = new misc();
	
	$handle_shipment = new shipmentmainClass();// by ckwai on 2018-06-07
	$handle_shipment->setConnection($conn);
	$handle_shipment->setlanguage($lang);
	
	$_tblshippingadvise = new tblshippingadvise($conn, $handle_misc);
	$_tblshippingadviseqty = new tblshippingadviseqty($conn, $handle_misc);
	$handle_tblbuyer_invoice = new tblbuyer_invoice($conn);
	$handle_tblbuyer_invoice->setMisc($handle_misc);
	
	$model_tblbuyer_invoice_charge_option = new tblbuyer_invoice_charge_option($conn);
	$model_tblbuyer_invoice_charge_option->setMisc($handle_misc);
	
	if($isBuyerPayment==1){// Buyer Payment Invoice
		$handle_tblbuyer_invoice_detail = new tblbuyer_invoice_payment_detail($conn);
		$handle_tblbuyer_invoice_detail->setMisc($handle_misc);
		$handle_tblbuyer_invoice_detail->setModelTblbuyer_inv_charge_option($model_tblbuyer_invoice_charge_option);
		
		$handle_tblbuyer_invoice_category = new tblbuyer_invoice_payment_category($conn);
		$handle_tblbuyer_invoice_category->setMisc($handle_misc);
		
		$handle_tblbuyer_invoice = new tblbuyer_invoice_payment($conn);
		$handle_tblbuyer_invoice->setMisc($handle_misc);
	}
	else{ // Commercial Invoice
		$handle_tblbuyer_invoice_detail = new tblbuyer_invoice_detail($conn);
		$handle_tblbuyer_invoice_detail->setMisc($handle_misc);
		
		$handle_tblbuyer_invoice_category = new tblbuyer_invoice_category($conn);
		$handle_tblbuyer_invoice_category->setMisc($handle_misc);
		
		$handle_tblbuyer_invoice = new tblbuyer_invoice($conn);
		$handle_tblbuyer_invoice->setMisc($handle_misc);
	}
	
	$handle_tblcarton_inv_head = new tblcarton_inv_head($conn);
	$handle_tblcarton_inv_head->setMisc($handle_misc);
	
	$handle_tblcarton_inv_detail = new tblcarton_inv_detail($conn);
	$handle_tblcarton_inv_detail->setMisc($handle_misc);
	
	$handle_tblcarton_inv_payment_head = new tblcarton_inv_payment_head($conn);
	$handle_tblcarton_inv_payment_head->setMisc($handle_misc);
	
	$handle_tblcarton_inv_payment_detail = new tblcarton_inv_payment_detail($conn);
	$handle_tblcarton_inv_payment_detail->setMisc($handle_misc);
	
	$handle_tblbuyer_invoice_charge_option = new tblbuyer_invoice_charge_option($conn);
	$handle_tblbuyer_invoice_charge_option->setMisc($handle_misc);
	
	$handle_tblquotacat = new tblquotacat($conn);
	$handle_tblquotacat->setMisc($handle_misc);
	
	$handle_lc = new lcClass();
	$handle_lc->setConnection($conn);
	$handle_lc->setModelCIH($handle_tblcarton_inv_head);
	$handle_lc->setModelCIPH($handle_tblcarton_inv_payment_head);
	$handle_lc->setHandleShipment($handle_shipment);
	$handle_lc->isBuyerPayment = $isBuyerPayment;

	echo "Saving...";
	try {
		$conn->beginTransaction();

		//--------------------------------------------------- HEADER PART --------------------------------------------//		
		$temp_invID  = $_POST["invID"];
		$temp_status = $_POST["temp_status"];
		
		$this_handle_tblctn_head   = $handle_tblcarton_inv_head;
		$this_handle_tblctn_detail = $handle_tblcarton_inv_detail;
		
		if($isBuyerPayment==1){ //from Buyer Payment Invoice
			$this_handle_tblctn_head   = $handle_tblcarton_inv_payment_head;
			$this_handle_tblctn_detail = $handle_tblcarton_inv_payment_detail;
		}
		
		// $temp_status = ($acctid==1? 4: $temp_status);
		if(($temp_status == "8" || $temp_status == "11") && ($temp_invID != "-999" || $temp_invID != -999) ){
			$status = (isset($_POST["status"])? $_POST["status"]: $temp_status);
			
			$handle_tblbuyer_invoice->statusID = $status;
			$handle_tblbuyer_invoice->updatedby = $acctid;
			$handle_tblbuyer_invoice->ID = $temp_invID;
			
			$handle_tblbuyer_invoice->updateStatusOnly();
			
			$invID=$temp_invID;
			
			if($isBuyerPayment==0){// only for commercial invoice
				$countPO     = $_POST["countPO"];  
				for($n=0; $n<=$countPO; $n++){
					if(isset($_POST["spID$n"])){
						$spID     = $_POST["spID$n"];
						$sp_BICID = $_POST["sp_BICID$n"];
						$valid    = (isset($_POST["valid$n"])? "1": "0");
						
						$handle_tblbuyer_invoice_detail->invID = $invID;
						$handle_tblbuyer_invoice_detail->shipmentpriceID = $spID;
						$handle_tblbuyer_invoice_detail->BICID = $sp_BICID;
						$handle_tblbuyer_invoice_detail->valid = $valid;
						$handle_tblbuyer_invoice_detail->updateValid();
						
					}
				}
			}
			
		}
		else{
			
			if(isset($_POST['tradeterm'])){
				//echo "isset <br/>";
				$tradeTermID      = $_POST["tradeterm"];
				$paymentTermID    = $_POST["paymentterm"];
				$shipmodeID       = $_POST["shipmode"];
				$portLoadingID    = $_POST["portloading"];
				$payer            = $_POST['payer'];
				$poissuer         = (isset($_POST['poissuer'])? $_POST["poissuer"]: "0");
				$PortDestID       = $_POST['portofdischarges'];
				$BuyerDestID      = $_POST['buyerdestination'];
			}
			else{
				//echo "no isset <br/>";
				$tradeTermID      = $_POST["txt_tradeterm"];
				$paymentTermID    = $_POST["txt_paymentterm"];
				$shipmodeID       = $_POST["txt_shipmode"];
				$portLoadingID    = $_POST["txt_portloading"];
				$payer            = $_POST['txt_payer'];
				$poissuer         = $_POST['txt_poissuer'];
				$PortDestID       = $_POST['txt_portofdischarges'];
				$BuyerDestID      = $_POST['txt_buyerdestination'];
			}
			
			$BuyerID      = $_POST["buyer"];
			$invoice_no   = $_POST["invoice_no"]; 
			$invoice_date = $_POST["invoice_date"];
			$poissue_date = $_POST["poissue_date"];
			$porID        = $_POST["porID"];
			$shippeddate  = $_POST["shippeddate"];
			$vesselname   = $_POST["vesselname"];
			$transitPortID    = $_POST["transitPortID"];
			$cargocutoffdate  = $_POST["cargocutoff"];
			$exfactory    = $_POST["exfactory"];
			$export_by    = $_POST['exporter'];
			$statusID     = $_POST["status"];
			$remarks      = $_POST["remarks"];
			$BID          = $_POST["BID"];
			$container_no = $_POST["container_no"];
			$ETA          = $_POST["ETA"];
			$carrier      = $_POST["carrier"];
			$seal_no      = $_POST["seal_no"];
			
			$issue_from   = $_POST['issuefrom'];
			$issue_addr   = $_POST['issue_from_address'];
			$ConsigneeID  = implode(",", $_POST["consignee"]); 
			$cons_addr    = $_POST['consignee_address'];
			$notifyID     = $_POST["sel_notify_party"];
			$notify_party = $_POST["notify_party"];
			$notify_addr  = $_POST["notify_address"];
			$shipper      = $_POST["shipper"];
			$shipper_addr = $_POST["shipper_address"];
			$ship_to      = $_POST["ship_to"];
			$ship_address = $_POST["ship_address"];
			$cdc_no       = $_POST["cdc_no"];
			$cdc_date     = $_POST["cdc_date"];
			$byr_invoice_no = (isset($_POST["byr_invoice_no"])? $_POST["byr_invoice_no"]: NULL);
			
			$join_inspection_no   = NULL;//$_POST["join_inspection_no"];
			$join_inspection_date = NULL;//$_POST["join_inspection_date"];
			$custom_procedure     = NULL;//$_POST["custom_procedure"];
			$co_number            = NULL;//$_POST["co_number"];
			$co_date              = NULL;//$_POST["co_date"];
			$conversion_rate      = $_POST["conversion_rate"];
			
			//echo "<textarea>$remarks</textarea>";
			
			
			$handle_tblbuyer_invoice->ConsigneeID        = $ConsigneeID;
			$handle_tblbuyer_invoice->consignee_address  = $cons_addr;
			$handle_tblbuyer_invoice->issue_from         = $issue_from;
			$handle_tblbuyer_invoice->issue_from_addr    = $issue_addr;
			$handle_tblbuyer_invoice->notifyID           = $notifyID;
			$handle_tblbuyer_invoice->notify_party       = $notify_party;
			$handle_tblbuyer_invoice->notify_address     = $notify_addr;
			$handle_tblbuyer_invoice->shipper            = $shipper;
			$handle_tblbuyer_invoice->shipper_address    = $shipper_addr;
			$handle_tblbuyer_invoice->ship_to            = $ship_to;
			$handle_tblbuyer_invoice->ship_address       = $ship_address;
			
			$handle_tblbuyer_invoice->BuyerID       = $BuyerID;
			$handle_tblbuyer_invoice->poissuer      = $poissuer;
			$handle_tblbuyer_invoice->poissue_date  = $poissue_date;
			$handle_tblbuyer_invoice->built_to      = $payer;
			$handle_tblbuyer_invoice->byr_invoice_no = $byr_invoice_no;
			$handle_tblbuyer_invoice->invoice_no    = $invoice_no;
			$handle_tblbuyer_invoice->invoice_date  = $invoice_date;
			$handle_tblbuyer_invoice->first_invoice_date  = $invoice_date;
			$handle_tblbuyer_invoice->shipmodeID    = $shipmodeID;
			$handle_tblbuyer_invoice->porID         = $porID;
			$handle_tblbuyer_invoice->portLoadingID = $portLoadingID;
			$handle_tblbuyer_invoice->BuyerDestID   = $BuyerDestID;
			$handle_tblbuyer_invoice->PortDestID    = $PortDestID;
			$handle_tblbuyer_invoice->transitPortID = $transitPortID;
			$handle_tblbuyer_invoice->shippeddate   = $shippeddate;
			$handle_tblbuyer_invoice->tradeTermID   = $tradeTermID;
			$handle_tblbuyer_invoice->paymentTermID = $paymentTermID;
			$handle_tblbuyer_invoice->vesselname    = $vesselname;
			$handle_tblbuyer_invoice->cargocutoffdate = $cargocutoffdate;
			$handle_tblbuyer_invoice->export_by       = $export_by;
			$handle_tblbuyer_invoice->exfactory       = $exfactory;
			$handle_tblbuyer_invoice->createdby       = $acctid;
			$handle_tblbuyer_invoice->updatedby       = $acctid;
			$handle_tblbuyer_invoice->statusID        = $statusID;
			$handle_tblbuyer_invoice->BID             = $BID;
			$handle_tblbuyer_invoice->container_no    = $container_no;
			$handle_tblbuyer_invoice->ETA             = $ETA;
			$handle_tblbuyer_invoice->carrier         = $carrier;
			$handle_tblbuyer_invoice->seal_no         = $seal_no;
			$handle_tblbuyer_invoice->cdc_no          = $cdc_no;
			$handle_tblbuyer_invoice->cdc_date        = $cdc_date;
			
			// $handle_tblbuyer_invoice->join_inspection_no   = $join_inspection_no;
			// $handle_tblbuyer_invoice->join_inspection_date = $join_inspection_date;
			// $handle_tblbuyer_invoice->custom_procedure     = $custom_procedure;
			// $handle_tblbuyer_invoice->co_number            = $co_number;
			// $handle_tblbuyer_invoice->co_date              = $co_date;
			$handle_tblbuyer_invoice->conversion_rate      = $conversion_rate;
			
			$handle_tblbuyer_invoice->remarks   = (trim($remarks)==""? NULL: $remarks);
			
			if($temp_invID == "-999" || $temp_invID == -999 || $temp_invID==0){
				//---------- insert buyer invoice ------------//
				$invID = $handle_tblbuyer_invoice->create();
				$handle_tblbuyer_invoice->ID = $invID;
				$invoice_no = $handle_tblbuyer_invoice->invoice_no;

			}
			else{
				//--------- update buyer invoice ---------//
				$invID = $temp_invID;
				$handle_tblbuyer_invoice->ID = $invID;
				$handle_tblbuyer_invoice->update();
			}
			
			// $handle_tblbuyer_invoice->updateCOInfo();
			// $handle_tblbuyer_invoice->updateCustomInfo();
			
			$handle_tblbuyer_invoice->ID = $invID;
			$handle_tblbuyer_invoice->invoice_date = $invoice_date;
			$handle_tblbuyer_invoice->updateInvoiceDate();
			
			$handle_tblbuyer_invoice->updateRemarks($remarks);
			$handle_tblbuyer_invoice->updateShipAddress($ship_address);
			//------------------------------------------------- END HEADER PART --------------------------------------------//
			
			if($isBuyerPayment==1){// Mirroring Data to Commercial Invoice
				$handle_tblbuyer_invoice->syncHeaderFromBuyerPaymentToCommercialInvoice();
			}
			else{ // Mirroring Data to Buyer Payment Invoice
				$handle_tblbuyer_invoice->syncHeaderFromCommercialInvoiceToBuyerPayment();
			}
			
			//------------------------------------------------- DETAIL PART --------------------------------------------//
			$countPO     = $_POST["countPO"]; 
			$max_options = $_POST["max_options"]; 
			$arr_exist = [];
			$arr_BICID = [];
			
			//=============================================//
			//---- Invoice Category by Option A,B,C... ----//
			for($op=0;$op<=$max_options;$op++){
				if(isset($_POST["cat_options$op"])){
					$cat_options      = $_POST["cat_options$op"];
					$BICID            = $_POST["BICID$op"];
					$cat_invoice_no   = $_POST["cat_invoice_no$op"];
					$co_number        = (isset($_POST["co_number$op"])? $_POST["co_number$op"]: NULL);
					$co_date          = (isset($_POST["co_date$op"])? $_POST["co_date$op"]: NULL);
					$custom_no        = (isset($_POST["custom_no$op"])? $_POST["custom_no$op"]: NULL);
					$custom_date      = (isset($_POST["custom_date$op"])? $_POST["custom_date$op"]: NULL);
					$custom_procedure = (isset($_POST["custom_procedure$op"])? $_POST["custom_procedure$op"]: NULL);
					
					$cat_invoice_no = ($cat_options=="0"? "$invoice_no": "$invoice_no".$arr_column[$cat_options-1]);
					
					$handle_tblbuyer_invoice_category->invID            = $invID;
					$handle_tblbuyer_invoice_category->options          = $cat_options;
					$handle_tblbuyer_invoice_category->invoice_no       = $cat_invoice_no;
					$handle_tblbuyer_invoice_category->co_number        = $co_number;
					$handle_tblbuyer_invoice_category->co_date          = ($co_date==""? NULL: $co_date);
					$handle_tblbuyer_invoice_category->custom_no        = $custom_no;
					$handle_tblbuyer_invoice_category->custom_date      = ($custom_date==""? NULL: $custom_date);
					$handle_tblbuyer_invoice_category->custom_procedure = $custom_procedure;
					
					//echo "$op / $BICID<br/>";
					
					if($BICID==0){
						$arr_result = $handle_tblbuyer_invoice_category->checkCategoryExistNotDel();
						$isExist    = $arr_result["isExist"];
							
						if($isExist==0){ //--- Create New ---//
							$BICID = $handle_tblbuyer_invoice_category->create();
						}
						else{
							$BICID    = $arr_result["BICID"];
						}
					}
					else{ //--- Update Existing ---//
						$handle_tblbuyer_invoice_category->BICID = $BICID;
						$handle_tblbuyer_invoice_category->update();
					}
					
				}
			}//--- End For Category ---//
			
			//---- End Invoice Category ----//
			//==============================//
			
				for($n=0; $n<=$countPO; $n++){
					$isNew = false;
					if(isset($_POST["BuyerPO$n"])){
						
						//=============================================//
						//---- Invoice Category by Option A,B,C... ----//
						
						$options          = $_POST["options$n"];
						$BICID            = $_POST["BICID$options"];
						$cat_invoice_no   = $_POST["cat_invoice_no$options"];
						// $co_number        = $_POST["co_number$options"];
						// $co_date          = $_POST["co_date$options"];
						// $custom_no        = $_POST["custom_no$options"];
						// $custom_date      = $_POST["custom_date$options"];
						// $custom_procedure = $_POST["custom_procedure$options"];
						
						$cat_invoice_no = ($options=="0"? "$invoice_no": "$invoice_no".$arr_column[$options-1]);
						
						$handle_tblbuyer_invoice_category->invID            = $invID;
						$handle_tblbuyer_invoice_category->options          = $options;
						// $handle_tblbuyer_invoice_category->invoice_no       = $cat_invoice_no;
						// $handle_tblbuyer_invoice_category->co_number        = $co_number;
						// $handle_tblbuyer_invoice_category->co_date          = ($co_date==""? NULL: $co_date);
						// $handle_tblbuyer_invoice_category->custom_no        = $custom_no;
						// $handle_tblbuyer_invoice_category->custom_date      = ($custom_date==""? NULL: $custom_date);
						// $handle_tblbuyer_invoice_category->custom_procedure = $custom_procedure;
						
						//echo "$BICID / $options / $cat_invoice_no /$co_number <<< <br/>";
						
						if($BICID==0){
							$arr_result = $handle_tblbuyer_invoice_category->checkCategoryExistNotDel();
							$isExist    = $arr_result["isExist"];
							$BICID      = $arr_result["BICID"];
							
							// if($isExist==0){ //--- Create New ---//
								// $BICID = $handle_tblbuyer_invoice_category->create();
							// }
							// else{
								// $BICID    = $arr_result["BICID"];
							// }
						}
						else{ //--- Update Existing ---//
							$handle_tblbuyer_invoice_category->BICID = $BICID;
							//$handle_tblbuyer_invoice_category->update();
						}
						
						//---- End Invoice Category ----//
						//==============================//
					
					$countColor  = $_POST["countColor$n"];
					$BuyerPO     = $_POST["BuyerPO$n"];
					$spID        = $_POST["spID$n"];
					$sp_BICID    = $_POST["sp_BICID$n"];
					$remarks     = (isset($_POST["remarks$n"])? $_POST["remarks$n"]: "");
					$use_ori_qty = $_POST["use_ori_qty$n"];
					$LCIID       = $_POST["LCIID$n"];
					$gmt_count   = $_POST["gmt_count$n"];
					$ht_code     = $_POST["ht_code$n"];
					$quota       = (isset($_POST["quota_cat$n"])? 0: 0); //link back to tblshipmentprice
					$mode_quota  = (isset($_POST["mode_quota_cat$n"])? "0":"0"); //0:select mode, 1:text mode
					$txt_quota   = (isset($_POST["txt_quota_cat$n"])? "":""); //0:select mode, 1:text mode
					$valid       = (isset($_POST["valid$n"])? "1": "0");
					
					if($mode_quota==1){//if edit mode quota
						$handle_tblquotacat->Description = $txt_quota;
						$this_quotaID = $handle_tblquotacat->checkQuotaExist();
						
						if($this_quotaID==0){//if not found 
							$handle_tblquotacat->isuserkey = 1;
							$quota = $handle_tblquotacat->create();
							
						}
						else{// if quota cat exist in system
							$quota = $this_quotaID;
						}
					}
					
					if($isBuyerPayment==0){
						$handle_tblbuyer_invoice_detail->invID = $invID;
						$handle_tblbuyer_invoice_detail->shipmentpriceID = $spID;
						$handle_tblbuyer_invoice_detail->BICID = $sp_BICID;
						$handle_tblbuyer_invoice_detail->valid = $valid;
						$handle_tblbuyer_invoice_detail->updateValid();
					}
					
					if($statusID==8 && $isBuyerPayment==1){ 
						$sqldel = "DELETE FROM tblshippingadviseqty WHERE tblshipmentpriceID='$spID' ";
						$stmt_del = $conn->prepare($sqldel);
						$stmt_del->execute();
						
					}
					
					//echo "$spID / $countPO<< <br/>";
					for($c=0; $c<$countColor; $c++){
						$colorID         = $_POST["colorID$n-$c"]; 
						$group_number    = $_POST["group_number$n-$c"]; 
						$color_qty       = $_POST["color_qty$n-$c"]; 
						$unit_price      = $_POST["unit_price$n-$c"]; 
						$total_amt       = $_POST["total_amt$n-$c"]; 
						$invd_ID         = $_POST["invd_ID$n-$c"]; 
						$shipping_remark = (isset($_POST["shipping_remark$n-$c"])? $_POST["shipping_remark$n-$c"]: "");
						// $shipping_remark = $handle_misc->funcConvertSpecialCharWOUpperCase($shipping_remark);
						$class_description = (isset($_POST["class_description$n-$c"])? $_POST["class_description$n-$c"]: "");
						$class_description = ($class_description==""? NULL : $class_description);
						
						//echo "$invd_ID = $n-$c <br/>";
						$handle_tblbuyer_invoice_detail->invID = $invID;
						$handle_tblbuyer_invoice_detail->BICID = $BICID;
						$handle_tblbuyer_invoice_detail->LCIID = $LCIID;
						$handle_tblbuyer_invoice_detail->shipmentpriceID = $spID;
						$handle_tblbuyer_invoice_detail->remarks = $remarks;
						$handle_tblbuyer_invoice_detail->quotaID = $quota;
						$handle_tblbuyer_invoice_detail->ht_code = $ht_code;
						// $handle_tblbuyer_invoice_detail->shipping_marking = $shipping_remark;
						$handle_tblbuyer_invoice_detail->class_description = $class_description;
						$handle_tblbuyer_invoice_detail->group_number = $group_number;
						$handle_tblbuyer_invoice_detail->fob_price = $unit_price;
						$handle_tblbuyer_invoice_detail->qty = $color_qty;
						$handle_tblbuyer_invoice_detail->total_amount = $total_amt;
						
						if($invd_ID == "0"){
							//---- Create New Buyer Invoice Detail ----//
							$invd_ID = $handle_tblbuyer_invoice_detail->create();
							$isNew = true;
						}
						else {
							$handle_tblbuyer_invoice_detail->ID = $invd_ID;
							
							//---- Update buyer invoice detail ----//
							$handle_tblbuyer_invoice_detail->update();
							
						}
						
						//=======================================================================//
						//----- UPDATE BACK SHIPPING ADVISE QTY (via Buyer payment invoice) -----//
						//=======================================================================//
						if($statusID==8 && $isBuyerPayment==1){
							
							$sqlsgc = "SELECT sgc.group_number
										FROM tblship_group_color sgc 
										WHERE  sgc.shipmentpriceID='$spID' AND sgc.statusID='1'
										group by sgc.group_number";
							$stmtsgc = $conn->prepare($sqlsgc);
							$stmtsgc->execute();
							while($rowsgc = $stmtsgc->fetch(PDO::FETCH_ASSOC)){
								$grpID = $rowsgc["group_number"];
								$handle_lc->updateShippingAdvise($spID, $grpID, $updateddate);
								
								// echo "update shipping advise qty << ";
							}
							
							if (!in_array("$spID", $arr_shipID)){
								$arr_shipID[] = $spID;
							}
							
							/*$sqlShipColor = "SELECT sgc.colorID, sgc.garmentID
												FROM tblship_group_color sgc 
												WHERE sgc.shipmentpriceID='$spID' 
												AND sgc.group_number='$group_number' AND sgc.statusID=1";
							$stmt = $conn->prepare($sqlShipColor);
							$stmt->execute();
							while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
								extract($row);
								
								$sqlShipAdvise = "SELECT * FROM tblshippingadviseqty 
													WHERE tblshipmentpriceID='$spID' 
													AND garmentID='$garmentID' AND colorID='$colorID'";
								$stmt_advise = $conn->prepare($sqlShipAdvise);
								$stmt_advise->execute();
								$count_advise = $stmt_advise->rowCount();
								
								if($count_advise==0){//--- Insert Advise ---//
									$sqlInsert = "INSERT INTO tblshippingadviseqty
													(tblshipmentpriceID, colorID, garmentID, fobprice, shippedQty, updateddate)
												  VALUES
												    (:tblshipmentpriceID, :colorID, :garmentID, :fobprice, :shippedQty, :updateddate)";
									$detailsql = $conn->prepare($sqlInsert);
									$detail_data = array("tblshipmentpriceID" => $spID, 
												"colorID" => $colorID, 
												"garmentID" => $garmentID, 
												"fobprice" => $unit_price, 
												"shippedQty" => $color_qty,
												"updateddate" => $updateddate );
									$detailsql->execute($detail_data);
									
								}
								else{ //--- Update Advise ---//
									$sqlUpdate = "UPDATE tblshippingadviseqty 
													SET fobprice='$unit_price', shippedQty='$color_qty', updateddate='$updateddate'
													WHERE tblshipmentpriceID='$spID' 
													AND garmentID='$garmentID' AND colorID='$colorID'";
									$detailsql = $conn->prepare($sqlUpdate);
									$detailsql->execute();
								}
							}//--- End While Ship Group Color ---//*/
											
						}//--- End Status=8 ---//

					}//--- End For Color ---//
					
					$arr_ht_code = array();
					for($gnum=1;$gnum<=$gmt_count;$gnum++){
						$quota_cat_d    = $_POST["quota_cat$n-$gnum"];
						$mode_quota     = $_POST["mode_quota_cat$n-$gnum"]; //0:select mode, 1:text mode
						$txt_quota      = $_POST["txt_quota_cat$n-$gnum"];
						$ht_code_d      = $_POST["ht_code$n-$gnum"];
						$this_garmentID = $_POST["this_garmentID$n-$gnum"];
						$this_sr        = $_POST["shipping_remark$n-$gnum"];
						$this_sr = $handle_misc->funcConvertSpecialCharWOUpperCase($this_sr);
						
						if($mode_quota==1){//if edit mode quota
							$handle_tblquotacat->Description = $txt_quota;
							$this_quotaID = $handle_tblquotacat->checkQuotaExist();
							
							if($this_quotaID==0){//if not found 
								$handle_tblquotacat->isuserkey = 1;
								$quota_cat_d = $handle_tblquotacat->create();
								
							}
							else{// if quota cat exist in system
								$quota_cat_d = $this_quotaID;
							}
						}
						
						$arr_ht_code[] = $ht_code_d;
						
						$tblbuyer_invoice_detail = ($isBuyerPayment==1? "tblbuyer_invoice_payment_detail":"tblbuyer_invoice_detail");
						$tblbuyer_invoice_hts    = ($isBuyerPayment==1? "tblbuyer_invoice_payment_hts":"tblbuyer_invoice_hts");
						$sqlgmt = "SELECT bid.ID as thisID
									FROM $tblbuyer_invoice_detail bid 
									INNER JOIN tblship_group_color sgc ON sgc.shipmentpriceID = bid.shipmentpriceID 
																		AND sgc.group_number = bid.group_number AND sgc.statusID=1
									WHERE bid.del=0 AND bid.shipmentpriceID = '$spID' AND bid.invID='$invID' AND bid.BICID='$BICID'
									AND bid.group_number>0 AND sgc.statusID=1
									AND sgc.garmentID='$this_garmentID'
									group by bid.ID";
						$stmt_gmt = $conn->prepare($sqlgmt);
						$stmt_gmt->execute();
						
						// echo ">>>>> $ht_code_d / $this_garmentID [$sqlgmt]<< <br/>";
						
						while($row_gmt = $stmt_gmt->fetch(PDO::FETCH_ASSOC)){
							extract($row_gmt);
							
							$sqlchk = "SELECT BIHID 
										FROM $tblbuyer_invoice_hts 
										WHERE invID='$invID' AND BICID='$BICID' 
										AND shipmentpriceID='$spID' AND garmentID='$this_garmentID'";
							$stmt_chk = $conn->prepare($sqlchk);
							$stmt_chk->execute();
							$count_chk = $stmt_chk->rowCount();
							
							// echo "-------------->> $count_chk test <br/>";
							
							if($count_chk==0){
								$BIHID = $handle_misc->funcMaxID("$tblbuyer_invoice_hts", "BIHID");
								$insertsql = "INSERT INTO $tblbuyer_invoice_hts SET 
												BIHID=:BIHID, invID=:invID, BICID=:BICID, 
												shipmentpriceID=:shipmentpriceID, garmentID=:garmentID,
												ht_code=:ht_code, quotaID=:quotaID, shipping_marking=:shipping_marking";
								$stmt_insert = $conn->prepare($insertsql);
								$stmt_insert->bindParam(":BIHID", $BIHID);
								$stmt_insert->bindParam(":invID", $invID);
								$stmt_insert->bindParam(":BICID", $BICID);
								$stmt_insert->bindParam(":shipmentpriceID", $spID);
								$stmt_insert->bindParam(":garmentID", $this_garmentID);
								$stmt_insert->bindParam(":ht_code", $ht_code_d);
								$stmt_insert->bindParam(":quotaID", $quota_cat_d);
								$stmt_insert->bindParam(":shipping_marking", $this_sr);
								$stmt_insert->execute();
								
								// echo "INSERT --> $BIHID / $ht_code_h <br/>";
							}
							else{
								$row_chk = $stmt_chk->fetch(PDO::FETCH_ASSOC);
								$BIHID = $row_chk["BIHID"];
								
								$updatesql = "UPDATE $tblbuyer_invoice_hts SET 
												ht_code=:ht_code, quotaID=:quotaID, shipping_marking=:shipping_marking
												WHERE BIHID=:BIHID";
								$stmt_update = $conn->prepare($updatesql);
								$stmt_update->bindParam(":ht_code", $ht_code_d);
								$stmt_update->bindParam(":quotaID", $quota_cat_d);
								$stmt_update->bindParam(":BIHID", $BIHID);
								$stmt_update->bindParam(":shipping_marking", $this_sr);
								$stmt_update->execute();
								
								// echo "UPDATE --> $BIHID / $ht_code_h <br/>";
							}
						}
					}
					
					//======================================================//
					//======================================================//
					//-------- SAVE PACKING LIST IN BUYER INVOICE  ---------//
					//======================================================//
					//======================================================//
					
					// echo "count_pl_row: ".$_POST["count_pl_row$n"]." << <br/>";
					
					if(isset($_POST["count_pl_row$n"])){
						$this_handle_tblctn_head->invID = $invID;
						$this_handle_tblctn_head->shipmentpriceID = $spID;
						$this_handle_tblctn_head->BICID = $BICID;
						$this_handle_tblctn_head->removeByShipmentID(); // remove all first, after that below juz update back from del=0
						
						// echo "count_pl_row: ".$_POST["count_pl_row$n"]." << <br/>";
						
						for($ctn_r=1;$ctn_r<=$_POST["count_pl_row$n"];$ctn_r++){
							$this_id = "$n-$ctn_r";
							
							if(isset($_POST["CIHID$this_id"])){
								// $CIHID                = $_POST["CIHID$this_id"];
								$CIHID = ($use_ori_qty==2 && $isBuyerPayment==1? "0": $_POST["CIHID$this_id"]);
								$start                = $_POST["start$this_id"];
								$end_num              = $_POST["end_num$this_id"];
								$is_last              = $_POST["is_last$this_id"];
								$PID                  = $_POST["PID$this_id"];
								$total_ctn            = $_POST["total_ctn$this_id"];
								$mixID                = $_POST["arr_list_detail$this_id"];
								$SKU                  = $_POST["SKU$this_id"];
								$prepack_name         = $_POST["prepack_name$this_id"];
								$masterID             = $_POST["masterID$this_id"];
								$total_qty_in_carton  = $_POST["total_qty_in_carton$this_id"];
								$total_qty            = $_POST["total_qty$this_id"];
								$weight_unitID        = $_POST["weight_unitID$this_id"];
								$net_net_weight       = $_POST["net_net_weight$this_id"];
								$net_weight           = $_POST["net_weight$this_id"];
								$gross_weight         = $_POST["gross_weight$this_id"];
								$ext_length           = $_POST["ext_length$this_id"];
								$ext_width            = $_POST["ext_width$this_id"];
								$ext_height           = $_POST["ext_height$this_id"];
								$ctn_unitID           = $_POST["ctn_unitID$this_id"];
								$total_CBM            = $_POST["total_CBM$this_id"];
								$total_CBM            = round(($ext_length/100) * ($ext_width/100) * ($ext_height/100), 3);
								$blisterbag_in_carton = $_POST["blisterbag_in_carton$this_id"];
								
								if($spID==51099){
									// echo "<br/>[$spID] $CIHID -- $start-$end_num | $mixID [$use_ori_qty / $isBuyerPayment] <<< <br/>";
								}
								
								//$mixID = str_replace("::^^","", $arr_list_detail);
								
								$this_handle_tblctn_head->invID = $invID;
								$this_handle_tblctn_head->BICID = $BICID;
								$this_handle_tblctn_head->shipmentpriceID = $spID;
								$this_handle_tblctn_head->PID = $PID;
								$this_handle_tblctn_head->SKU = $SKU;
								$this_handle_tblctn_head->prepack_name = $prepack_name;
								$this_handle_tblctn_head->masterID = $masterID;
								$this_handle_tblctn_head->start = $start;
								$this_handle_tblctn_head->end_num = $end_num;
								$this_handle_tblctn_head->is_last = $is_last;
								$this_handle_tblctn_head->total_ctn = $total_ctn;
								$this_handle_tblctn_head->mixID = $mixID;
								$this_handle_tblctn_head->blisterbag_qty = $blisterbag_in_carton;
								$this_handle_tblctn_head->total_qty_in_carton = $total_qty_in_carton;
								$this_handle_tblctn_head->net_net_weight = $net_net_weight;
								$this_handle_tblctn_head->net_weight = $net_weight;
								$this_handle_tblctn_head->gross_weight = $gross_weight;
								$this_handle_tblctn_head->weight_unitID = $weight_unitID;
								$this_handle_tblctn_head->ext_length = $ext_length;
								$this_handle_tblctn_head->ext_width = $ext_width;
								$this_handle_tblctn_head->ext_height = $ext_height;
								$this_handle_tblctn_head->ctn_unitID = $ctn_unitID;
								$this_handle_tblctn_head->total_CBM = $total_CBM * $total_ctn;
								$this_handle_tblctn_head->updatedBy = $acctid;
								
								if($CIHID==0){//insert
									$CIHID = $this_handle_tblctn_head->create();
									// echo "insert <br/>";
								}
								else{//update
									$this_handle_tblctn_head->del = 0;
									$this_handle_tblctn_head->CIHID = $CIHID;
									$this_handle_tblctn_head->update();
									// echo "update $isBuyerPayment <br/>";
								}
								
								if($mixID!=""){
									$arr_pd = explode("::^^", $mixID);
									$arr_info_check = array();
									for($i=0;$i<count($arr_pd);$i++){
										list($group_number, $size_name, $qty) = explode("**%%", $arr_pd[$i]);
										$this_key = "$group_number**^^$size_name";
										
										if (array_key_exists($this_key, $arr_info_check)) {
											$arr_info_check[$this_key] += $qty;
										}
										else{
											$arr_info_check[$this_key] = $qty;
										}
										
										$this_qty = $arr_info_check[$this_key];
										
										$this_handle_tblctn_detail->CIHID = $CIHID;
										$this_handle_tblctn_detail->shipmentpriceID = $spID;
										$this_handle_tblctn_detail->group_number = $group_number;
										$this_handle_tblctn_detail->size_name = $size_name;
										$this_handle_tblctn_detail->qty = $this_qty;
										$CIDID = $this_handle_tblctn_detail->checkCartonDetailDataExist();
										
										if($CIDID==0){
											$CIDID = $this_handle_tblctn_detail->create();
										}
										else{
											$this_handle_tblctn_detail->CIDID = $CIDID;
											$this_handle_tblctn_detail->update();
										}
										
									}//--- End For ---//
								}//--- End MixID!="" ---//
								
							}//--- End Isset Row Exist ---//
							
						}//--- End For ---//
					}//--- End Isset Count PL Row ---//
					else if($use_ori_qty>0 || $isNew==true){
						$skip_cih = ($use_ori_qty==1 && $isBuyerPayment=="0"? "true": "");
						$skip_cih = ($use_ori_qty==0 && $isBuyerPayment=="1"? "pickpack": $skip_cih);
						$skip_cih = ($use_ori_qty==1 && $isBuyerPayment=="1"? "fullqtypickpack": $skip_cih);
						$skip_cih = ($use_ori_qty==2 && $isBuyerPayment=="1"? "": $skip_cih);
						$skip_cih = ($use_ori_qty==3 && $isBuyerPayment=="0"? "oripickpackonly": $skip_cih);
						$handle_lc->BICID = $BICID;
						$ci_invID = $invID;
						if($use_ori_qty==2 && $isBuyerPayment=="1"){
							$prefixopt = substr("$invoice_no", -1);
							
							$filter_query = (in_array("$prefixopt", $arr_alphabet)? "": "  AND bic.invID='$invID' AND bic.options='$options'");//
							$sqlCIBICID = "SELECT bic.BICID, bic.invID, bic.options
														FROM tblbuyer_invoice_category bic 
														INNER JOIN tblbuyer_invoice_detail bid ON bid.invID = bic.invID AND bid.BICID = bic.BICID
														WHERE 1=1 $filter_query AND bid.del=0 AND bic.del=0 AND bid.shipmentpriceID='$spID'";
												$stmt_cibicid = $conn->prepare($sqlCIBICID);
												$stmt_cibicid->execute();
												$row_cibicid = $stmt_cibicid->fetch(PDO::FETCH_ASSOC);
													$ci_BICID = $row_cibicid["BICID"];
													$ci_invID = $row_cibicid["invID"];
							
							$handle_lc->BICID = $ci_BICID;
							$handle_lc->isBuyerPayment = 0;
						}
						
						if($use_ori_qty!=0 && $use_ori_qty!=1 && $use_ori_qty!=2 && $use_ori_qty!=3 && $isBuyerPayment=="1"){
							$filter_query = " AND ch.container_no ='$use_ori_qty' "; $factoryID = ""; 
							$arr_all  = $handle_shipment->getAllExfactoryPackingInfoByBuyerPO($spID, $factoryID, $filter_query);
							$arr_list = $arr_all["arr_row"];
						}
						else{
							$arr_list = $handle_lc->getBuyerInvoicePackingListData($spID, $skip_cih, $ci_invID);
						}
						if($use_ori_qty==2 && $isBuyerPayment=="1"){
							$handle_lc->isBuyerPayment = 1;
							$handle_lc->BICID = $BICID;
						}
						// echo "<br/>$spID = $use_ori_qty / $isBuyerPayment [$prefixopt] / $BICID <br/>";
					
						$this_handle_tblctn_head->invID = $invID;
						$this_handle_tblctn_head->shipmentpriceID = $spID;
						$this_handle_tblctn_head->BICID = $BICID;
						$this_handle_tblctn_head->removeByShipmentID(); // remove all first, after that below juz update back from del=0
						// print_r($arr_list);
						for($i=0;$i<count($arr_list);$i++){
							$str_list = "";
							for($arr=0;$arr<count($arr_list[$i]["arr_list_detail"]);$arr++){
								$this_size    = $arr_list[$i]["arr_list_detail"][$arr]["size_name"];
								$this_grp_num = $arr_list[$i]["arr_list_detail"][$arr]["group_number"];
								$this_qty     = $arr_list[$i]["arr_list_detail"][$arr]["qty"];
								
								if($arr==0){
									$str_list = "$this_grp_num**%%$this_size**%%$this_qty";
								}
								else{
									$str_list .= "::^^$this_grp_num**%%$this_size**%%$this_qty";
								}
							}//--- End For arr ---//
							
							$CIHID = ($use_ori_qty==2 && $isBuyerPayment==1? "0": $arr_list[$i]["CIHID"]);
							$mixID = ($str_list==""? $arr_list[$i]["mixID"]: $str_list);
							$this_handle_tblctn_head->invID = $invID;
							$this_handle_tblctn_head->BICID = $BICID;
							$this_handle_tblctn_head->shipmentpriceID = $spID;
							$this_handle_tblctn_head->PID = $arr_list[$i]["PID"];
							$this_handle_tblctn_head->SKU = $arr_list[$i]["SKU"];
							$this_handle_tblctn_head->prepack_name = $arr_list[$i]["prepack_name"];
							$this_handle_tblctn_head->masterID = $arr_list[$i]["masterID"];
							$this_handle_tblctn_head->start = $arr_list[$i]["start"];
							$this_handle_tblctn_head->end_num = $arr_list[$i]["end_num"];
							$this_handle_tblctn_head->is_last = $arr_list[$i]["is_last"];
							$this_handle_tblctn_head->total_ctn = $arr_list[$i]["total_ctn"];
							$this_handle_tblctn_head->mixID = $mixID;
							$this_handle_tblctn_head->blisterbag_qty = $arr_list[$i]["blisterbag_in_carton"];
							$this_handle_tblctn_head->total_qty_in_carton = $arr_list[$i]["total_qty_in_carton"];
							$this_handle_tblctn_head->net_net_weight = $arr_list[$i]["net_net_weight"];
							$this_handle_tblctn_head->net_weight = $arr_list[$i]["net_weight"];
							$this_handle_tblctn_head->gross_weight = $arr_list[$i]["gross_weight"];
							$this_handle_tblctn_head->weight_unitID = $arr_list[$i]["weight_unitID"];
							$this_handle_tblctn_head->ext_length = $arr_list[$i]["ext_length"];
							$this_handle_tblctn_head->ext_width = $arr_list[$i]["ext_width"];
							$this_handle_tblctn_head->ext_height = $arr_list[$i]["ext_height"];
							$this_handle_tblctn_head->ctn_unitID = $arr_list[$i]["ctn_unitID"];
							
							$one_CBM = round(($arr_list[$i]["ext_length"]/100 * $arr_list[$i]["ext_width"]/100 * $arr_list[$i]["ext_height"]/100), 3);
							$this_handle_tblctn_head->total_CBM = round($one_CBM * $arr_list[$i]["total_ctn"], 3);
							$this_handle_tblctn_head->updatedBy = $acctid;
							
							if($CIHID==0){//insert
								$CIHID = $this_handle_tblctn_head->create();
							}
							else{//update
								$this_handle_tblctn_head->CIHID = $CIHID;
								$this_handle_tblctn_head->update();
							}
							
							if($mixID!=""){
								$arr_CIDID =  array();
								for($arr=0;$arr<count($arr_list[$i]["arr_list_detail"]);$arr++){
									$size_name    = $arr_list[$i]["arr_list_detail"][$arr]["size_name"];
									$group_number = $arr_list[$i]["arr_list_detail"][$arr]["group_number"];
									$qty          = $arr_list[$i]["arr_list_detail"][$arr]["qty"];
									
									$this_handle_tblctn_detail->CIHID = $CIHID;
									$this_handle_tblctn_detail->shipmentpriceID = $spID;
									$this_handle_tblctn_detail->group_number = $group_number;
									$this_handle_tblctn_detail->size_name = $size_name;
									$this_handle_tblctn_detail->qty = $qty;
									$CIDID = $this_handle_tblctn_detail->checkCartonDetailDataExist();
									
									if($CIDID==0){
										$CIDID = $this_handle_tblctn_detail->create();
									}
									else{
										$this_handle_tblctn_detail->CIDID = $CIDID;
										$this_handle_tblctn_detail->update();
									}
									$arr_CIDID[] = $CIDID;
								}
							}//--- If mixID != "" ---//
							
							if(count($arr_CIDID)>0){
								$str_CIDID = implode(",", $arr_CIDID);
								$this_handle_tblctn_detail->CIDID = $str_CIDID;
								$this_handle_tblctn_detail->CIHID = $CIHID;
								$this_handle_tblctn_detail->checkAndRemove();
							}
						}//--- End For All Ctn Range ---//
					}
					
					//-------------------------------------------------------------------------------//
					//-------- UPDATE BACK to Shipping Advise (via Buyer Payment Invoice) -----------//
					//-------------------------------------------------------------------------------//
					if($statusID==8 && $isBuyerPayment==1){//--- End Check Status is confirmed ---//
						$stmt_shipping = $conn->prepare("SELECT invnumber FROM tblshippingadvise WHERE tblshipmentpriceID='$spID'");
						$stmt_shipping->execute();
						$count_shipping = $stmt_shipping->rowCount();
						
						$sqladvise = "SELECT MIN(bi.cargocutoffdate) as cargocustoffdate, MIN(bi.shippeddate) as shippeddate, 
											GROUP_CONCAT(DISTINCT bi.invoice_no) as invoice_no, MIN(bi.invoice_date) as invoice_date, 
											GROUP_CONCAT(DISTINCT bid.shipping_marking) as shipping_marking, MIN(bi.exfactory) as exfactory,
											GROUP_CONCAT(DISTINCT bi.vesselname) as vesselname
							FROM tblbuyer_invoice_payment_detail bid
							INNER JOIN tblbuyer_invoice_payment bi ON bi.ID = bid.invID
							WHERE bid.shipmentpriceID='$spID' AND bid.del=0 AND bi.statusID IN (8,11)";
						$stmtadvise = $conn->prepare($sqladvise);
						$stmtadvise->execute();
						$row_advise = $stmtadvise->fetch(PDO::FETCH_ASSOC);
							extract($row_advise);
						
						//added by Lock (2019-05-16 1715)
						if($count_shipping==0){
							$sel_orderno=$conn->prepare("SELECT DISTINCT Orderno FROM tblshipmentprice WHERE ID='$spID'");
							$sel_orderno->execute();
							$row_orderno=$sel_orderno->fetch(PDO::FETCH_ASSOC);
							$orderno=$row_orderno['Orderno'];
							
							$sql = "INSERT INTO tblshippingadvise
							(tblshipmentpriceID, orderno, cargocutoffdate, exfactory, shippeddate, vesselname, invnumber, invdate, shipping_marking)
							VALUES 
							(:spID, :orderno, :cargocutoff, :exfactory, :shippeddate, :vesselname, :invnumber, :invdate, :shipping_marking)";
							$insertsql=$conn->prepare($sql);

							$insert_data=array("spID" => $spID, 
												"orderno" => $orderno,
												"cargocutoff" => $cargocustoffdate, 
												"exfactory" => $exfactory, 
												"shippeddate" => $shippeddate, 
												"vesselname" => $vesselname, 
												"invnumber" => $invoice_no, 
												"invdate" => $invoice_date, 
												"shipping_marking" => $shipping_marking );
							$insertsql->execute($insert_data);

						}
						else{
							//================================= update data of other table =======================================//
							$updatesql = $conn->prepare("UPDATE tblshippingadvise 
														SET cargocutoffdate=:cargocutoff, 
														exfactory=:exfactory, 
														vesselname=:vesselname, 
														invnumber=:invnumber, 
														invdate=:invdate, 
														shipping_marking=:shipping_marking, 
														shippeddate= :shippeddate   
														WHERE tblshipmentpriceID=:spID");
							$update_data = array("spID" => $spID, 
												"cargocutoff" => $cargocustoffdate, 
												"exfactory" => $exfactory, 
												"vesselname" => $vesselname, 
												"invnumber" => $invoice_no, 
												"invdate" => $invoice_date, 
												"shippeddate" => $shippeddate, 
												"shipping_marking" => $shipping_marking );
							$updatesql->execute($update_data);
						}
						
						//end
						//---------------------------------------------------//
						//------------- UPDATE BACK to Buyer PO -------------//
						//---------------------------------------------------//
						$arr_ht_code = array_unique($arr_ht_code);
						$str_ht_code = implode(",", $arr_ht_code);
						$updatesql2 = $conn->prepare("UPDATE tblshipmentprice 
														SET ht_code=:ht_code, QuotaID=:QuotaID, 
															byr_inv_description=:byr_inv_description
													WHERE ID=:spID ");
						$updatesql2->bindParam("ht_code", $str_ht_code);
						$updatesql2->bindParam("QuotaID", $quota_cat_d);
						$updatesql2->bindParam("byr_inv_description", $shipping_marking);
						$updatesql2->bindParam("spID", $spID);
						$updatesql2->execute();

						array_push($arr_exist, $spID);
					}//--- End Check Status is confirmed ---//
					
					}///--- End Isset ---//
				}//--- End For Count PO ---//

			$countCharge = $_POST["countCharge"];
			$arr_exist = [];
				for($i=0; $i<$countCharge; $i++){

					if(isset($_POST['description'.$i])){
						$INVDID            = $_POST['INVDID'.$i];
						$shipmentpriceID   = $_POST['shipmentpriceID'.$i];
						$buyerpo           = $_POST['buyerpo'.$i];
						$description       = $_POST['description'.$i];
						$percentage        = $_POST['percentage'.$i];//deduct percentage
						$chargeID_deduct   = (isset($_POST['chargeID_deduct'.$i])? $_POST['chargeID_deduct'.$i]: 0);
						$purpose_deduct    = (isset($_POST['purpose_deduct'.$i])? $_POST['purpose_deduct'.$i]: "");
						$percentage_credit = (isset($_POST['percentage_credit'.$i])? $_POST['percentage_credit'.$i]: 0);
						$chargeID_credit   = (isset($_POST['chargeID_credit'.$i])? $_POST['chargeID_credit'.$i]: 0);
						$purpose_credit    = (isset($_POST['purpose_credit'.$i])? $_POST['purpose_credit'.$i]: "");
						$charge_amt        = $_POST['charge_amt'.$i];
						
						// echo "=====> $chargeID_credit / $purpose_credit <br/>";
						
						$handle_tblbuyer_invoice_detail->invID = $invID;
						$handle_tblbuyer_invoice_detail->LCIID = 0;
						$handle_tblbuyer_invoice_detail->BICID = 0;
						$handle_tblbuyer_invoice_detail->shipmentpriceID = $shipmentpriceID;
						$handle_tblbuyer_invoice_detail->ht_code = NULL;
						$handle_tblbuyer_invoice_detail->shipping_marking = NULL;
						$handle_tblbuyer_invoice_detail->group_number = 0;
						$handle_tblbuyer_invoice_detail->fob_price = 0;
						$handle_tblbuyer_invoice_detail->qty = 0;
						$handle_tblbuyer_invoice_detail->total_amount = $charge_amt;
						$handle_tblbuyer_invoice_detail->other_charge = $description;
						$handle_tblbuyer_invoice_detail->charge_percentage = $percentage;
						
						if($isBuyerPayment==1){ //if only Buyer Payment
							if($chargeID_deduct==-1 && trim($purpose_deduct)!=""){
								$handle_tblbuyer_invoice_charge_option->type = 1;//deduct purpose
								$handle_tblbuyer_invoice_charge_option->Description = trim($purpose_deduct);//deduct purpose
								$arr_result_deduct = $handle_tblbuyer_invoice_charge_option->checkChargeOptionExist();
								
								if($arr_result_deduct["count"]==0){
									$chargeID_deduct = $handle_tblbuyer_invoice_charge_option->create();
								}
								else{
									$chargeID_deduct = $arr_result_deduct["ID"];
								}
							}// end if new deduct purpose
							
							if($chargeID_credit==-1 && trim($purpose_credit)!=""){
								$handle_tblbuyer_invoice_charge_option->type = 2;//credit purpose
								$handle_tblbuyer_invoice_charge_option->Description = trim($purpose_credit);//credit purpose
								$arr_result_credit = $handle_tblbuyer_invoice_charge_option->checkChargeOptionExist();
								
								if($arr_result_credit["count"]==0){
									$chargeID_credit = $handle_tblbuyer_invoice_charge_option->create();
								}
								else{
									$chargeID_credit = $arr_result_credit["ID"];
								}
							}// end if new credit purpose
							
							$handle_tblbuyer_invoice_detail->chargeID_deduct = $chargeID_deduct;
							$handle_tblbuyer_invoice_detail->charge_percentage_credit = $percentage_credit;
							$handle_tblbuyer_invoice_detail->chargeID_credit = $chargeID_credit;
							
						}//--- End if Buyer Payment ---//
						
						if($INVDID==""){
							$INVDID = $handle_tblbuyer_invoice_detail->create();
						}
						else{
							$handle_tblbuyer_invoice_detail->ID = $INVDID;
							$handle_tblbuyer_invoice_detail->updateOtherCharge();
							
						}
						array_push($arr_exist, $INVDID);
					}//--- End Isset ---//

				}//--- End For Count Charge ---//
				
				//delete charge
				$sel_charge=$conn->prepare("SELECT * FROM tblbuyer_invoice_detail 
											WHERE other_charge!='' AND invID='$invID' AND other_charge!='Reload Buyer PO'");
				$sel_charge->execute();

				while($row_charge = $sel_charge->fetch(PDO::FETCH_ASSOC)){
					$INVDID = $row_charge['ID']; 
					
					if(!in_array($INVDID, $arr_exist) && $isBuyerPayment!=1){
						
						$handle_tblbuyer_invoice_detail->ID = $INVDID;
						// $handle_tblbuyer_invoice_detail->deleteRow();
					}
				}
				
				if($BuyerID=="B13" && $isBuyerPayment==1 && glb_profile=="iapparelintl"){//For joe fresh, check whether discount goc exist
					$handle_tblbuyer_invoice_detail->invID = $invID;
					$handle_tblbuyer_invoice_detail->checkGOCDiscountExist();
				}
				//------------------------------------------------- END DETAIL PART --------------------------------------------//
		}//--- End Else ---//
		
		if(count($arr_shipID)>0){// added by ckwai on 2023-01-19 update ship advise once issue to ex-factory
			for($s=0;$s<count($arr_shipID);$s++){
				$shipmentpriceID = $arr_shipID[$s];
				
				// $handle_tblcarton_picklist_transit->updateShipAdviseByShipID($shipmentpriceID);
				
				$arrsaq = $_tblshippingadviseqty->getTotalShipAmtByShipID($shipmentpriceID);
				$grand_amt = $arrsaq["grand_amt"];
				$grand_qty = $arrsaq["grand_qty"];
				$orderno   = $arrsaq["orderno"];
				
				$_tblshippingadvise->orderno = $orderno;
				$_tblshippingadvise->vesselname = "";
				$_tblshippingadvise->checkExist($shipmentpriceID);
				
				// echo "grand_qty: $grand_qty << ";
				
				$arr_td = array("tblshipmentpriceID"=>$shipmentpriceID, "total_ship_amt"=>$grand_amt, "shippedqty"=>$grand_qty);
				$_tblshippingadvise->update($arr_td);
			}//-- End for --//
		}
		
		$getLink = (isset($_GET["isBuyerPayment"])? "&&isBuyerPayment=true":"");
		if($acctid!=0){
			$conn->commit();
			echo "<script>window.location='buyer_inv.php?id=".$invID."$getLink';</script>";
		}
		
	} 
	catch (PDOException $e) {
		$conn->rollBack();
		throw $e;
		echo "<script language='javascript'>alert('Error');</script>";
		// echo "<script>window.parent.location.reload();</script>";
		echo "<script>window.location='buyer_inv.php';</script>";
	}




// $savesql = $conn->prepare("INSERT INTO tblbuyer_invoice (BuyerID, ConsigneeID, issue_from, poissuer, built_to, invoice_no, invoice_date, shipmodeID, portLoadingID, BuyerDestID, PortDestID, shippeddate, tradeTermID, paymentTermID, vesselname, export_by, cargocutoffdate, exfactory, createdby, createddate, updatedby, updateddate, statusID, poissue_date) 
					// VALUES(:buyer, :consignee, :issue_from, :poissuer, :built_to, :invoice_no, :invoice_date, :shipmode, :portloading, :buyerdestination, :portdischarges, :shippeddate, :tradeterm, :paymentterm, :vesselname, :export_by, :cargocutoff, :exfactory, :updatedby, :updateddate, :updatedby, :updateddate, :status, :poissue_date)");
				// $save_data = array("buyer" => $buyer, 
									// "consignee" => $consignee, 
									// "issue_from" => $issue_from,
									// "poissuer" => $poissuer,
									// "built_to" => $payer,
									// "invoice_no" => $invoice_no, 
									// "invoice_date" => $invoice_date, 
									// "shipmode" => $shipmode, 
									// "portloading" => $portloading, 
									// "buyerdestination" => $buyerdestination, 
									// "portdischarges" => $portdischarges, 
									// "shippeddate" => $shippeddate, 
									// "tradeterm" => $tradeterm, 
									// "paymentterm" => $paymentterm, 
									// "vesselname" => $vesselname, 
									// "export_by" => $exporter, 
									// "cargocutoff" => $cargocutoff, 
									// "exfactory" => $exfactory, 
									// "updatedby" => $updatedby, 
									// "updateddate" => $updateddate, 
									// "status" => $status, 
									// "poissue_date" => $poissue_date
								// );
				// $savesql->execute($save_data);

				// $invID = $conn->lastInsertId();
?>