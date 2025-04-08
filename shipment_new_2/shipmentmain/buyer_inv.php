<?php 
	include("../../lock.php");
	// include_once("../../function/userpermission.php");
	include_once("../../function/misc.php");
	// include_once("../../class/fabInstoreClass.php");
	include_once("../../shipment_new/shipmentmain/shipmentmainClass.php");
	include_once("../../model/tblcrm_bank.php");
	include("../../model/tblbuyer_invoice.php");
	include("../../model/tblbuyer_invoice_category.php");
	include("../../model/tblcarton_inv_head.php");
	include("../../model/tblcarton_inv_payment_head.php");
	include("../../model/tblcarton_inv_payment_detail.php");
	include("../../model/tblbuyer_invoice_payment.php");
	include("../../model/tblbuyer_invoice_payment_category.php");
	include("../../model/tblbuyer_invoice_payment_detail.php");
	include("../../model/tblbuyer_invoice_charge_option.php");
	include("lc_class.php");
	$screenID = (isset($_GET["isBuyerPayment"]) ? 66: 76);
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	$arr_alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	
	$handle_misc = new misc();
	$today_date  = $handle_misc->DateNow();
	
	// $permission = new userpermission();
	$arr = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15);//$permission->arrPermission($acctid,$screenID,$conn);
	
	$handle_tblbuyer_invoice = new tblbuyer_invoice($conn);
	$handle_tblbuyer_invoice->setMisc($handle_misc);
	// $this_inv = $handle_tblbuyer_invoice->getMaxInvoiceNumber();
	
	
	$handle_tblcarton_inv_head = new tblcarton_inv_head($conn);
	$handle_tblcarton_inv_head->setMisc($handle_misc);
	
	$handle_tblcarton_inv_payment_head = new tblcarton_inv_payment_head($conn);
	$handle_tblcarton_inv_payment_head->setMisc($handle_misc);
	
	$handle_tblcarton_inv_payment_detail = new tblcarton_inv_payment_detail($conn);
	$handle_tblcarton_inv_payment_detail->setMisc($handle_misc);
	
	$handle_tblbuyer_invoice_payment_category = new tblbuyer_invoice_payment_category($conn);
	$handle_tblbuyer_invoice_payment_category->setMisc($handle_misc);
	
	$handle_tblbuyer_invoice_charge_option = new tblbuyer_invoice_charge_option($conn);
	$handle_tblbuyer_invoice_charge_option->setMisc($handle_misc);
	
	$handle_lc = new lcClass();
	$handle_lc->setConnection($conn);
	$handle_lc->setPermission($arr);
	$handle_lc->setModelCIH($handle_tblcarton_inv_head);
	$handle_lc->setModelCIPH($handle_tblcarton_inv_payment_head);
	$handle_lc->acctid = $acctid;
	
	$handle_tblcarton_inv_payment_head->setHandleLC($handle_lc);
	$handle_tblcarton_inv_payment_head->setModelTblcarton_inv_payment_detail($handle_tblcarton_inv_payment_detail);
	
	// $handle_fabInstore = new fabInstoreClass($conn, $lang, $acctid);
	// $handle_fabInstore->setHandleMisc($handle_misc);
	
	$handle_shipment = new shipmentmainClass();
	$handle_shipment->setConnection($conn);
	$handle_lc->setHandleShipment($handle_shipment);
	
	$handle_tblbuyer_invoice_payment = new tblbuyer_invoice_payment($conn);
	$handle_tblbuyer_invoice_payment->setMisc($handle_misc);
	
	$handle_tblbuyer_invoice_payment_detail = new tblbuyer_invoice_payment_detail($conn);
	$handle_tblbuyer_invoice_payment_detail->setMisc($handle_misc);
	$handle_tblbuyer_invoice_payment_detail->setShipment($handle_shipment);
	$handle_tblbuyer_invoice_payment_detail->setHandleLC($handle_lc);
	$handle_tblbuyer_invoice_payment_detail->setModelTblbuyer_invoice_payment_category($handle_tblbuyer_invoice_payment_category);
	
	$model_tblcrm_bank = new tblcrm_bank($conn);
	$model_tblcrm_bank->setMisc($handle_misc);
	
	// $orderno=$_GET["orderno"];
	// $sql = $conn->query("SELECT COUNT(ID) FROM tblbuyer_invoice WHERE statusID=1");
	// $countInvoice = $sql->fetchColumn();

	$input_disabled = "";
	$input_readonly = "";
	
	//-------------------------------------------------------------------------//
	//-------------- CHECK WHETHER COMMERCIAL OR BUYER PAYMENT ----------------//
	//-------------------------------------------------------------------------//
	$isBuyerPayment   = "0";
	$getLink          = "";
	$module_name      = "COMMERCIAL INVOICE LIST";
	$tblinvoice       = "tblbuyer_invoice";
	$tblinvoicedetail = "tblbuyer_invoice_detail";
	$tblinvoicecat    = "tblbuyer_invoice_category";
	$btn_loadagain    = "";

	if(isset($_GET["isBuyerPayment"])){ // Buyer Payment Invoice
		$ID = $_GET["id"];
		$isBuyerPayment   = "1";
		$getLink          = "&&isBuyerPayment=true";
		$module_name      = "BUYER PAYMENT INVOICE LIST";
		$tblinvoice       = "tblbuyer_invoice_payment";
		$tblinvoicedetail = "tblbuyer_invoice_payment_detail";
		$tblinvoicecat    = "tblbuyer_invoice_payment_category";
		$btn_loadagain    = "<input type='button' class='btn btn-default btn-sm' onclick='funcReloadBuyerPOFromCommercial()'
									value='Reload Buyer PO from Commercial Invoice' />";//-- acctid==1 --//
		
		$handle_tblbuyer_invoice_payment->ID = $ID;
		$isExist = $handle_tblbuyer_invoice_payment->checkInvoicePaymentExist(); // Check whether buyer payment exist
		
		
		// $handle_tblbuyer_invoice_payment_detail->invID = $ID;
		// $isExist = $handle_tblbuyer_invoice_payment_detail->checkInvoicePaymentDetailExist(); 
		
		// echo "$ID - $isExist <<";
		
		if($isExist==0){// if not exist in buyer payment detail
			//Take action to duplicate details information from commercial invoice, but qty using shipment packing list
			
			try {
			$conn->beginTransaction();
			$handle_tblbuyer_invoice_payment_detail->invID = $ID;
			$handle_tblbuyer_invoice_payment_detail->duplicateDataFromCommercialToBuyerPayment();
			
			$handle_tblbuyer_invoice_payment->ID = $ID;
			$handle_tblbuyer_invoice_payment->duplicateHeaderFromCommercialToBuyerPayment();
			
			$handle_tblcarton_inv_payment_head->invID = $ID;
			$handle_tblcarton_inv_payment_head->updatedBy = $acctid;
			$handle_tblcarton_inv_payment_head->autoUpdateLatestCartonInfoToBuyerPaymentInvoice();
			
			$conn->commit();
			echo "<script >location.reload();</script>";
			} 
			catch (PDOException $e) {
				$conn->rollBack();
				throw $e;
				echo "<script language='javascript'>alert('Error');</script>";
			}
		}//-- End If Exist==0 --//
	}
	$handle_lc->isBuyerPayment = $isBuyerPayment;

	if(isset($_GET["id"]) && !empty($_GET["id"])){
		$ID = $_GET["id"];
		$handle_lc->invID = $ID;
		
		$handle_misc->func_logoffOneItem($conn,$acctid,$screenID,$ID); 
		$noEdit = "";//$handle_misc->noEdited($lastID,$acctid,$screenID,$ID,$conn);
		
		$sqlinv = "SELECT inv.*, csn.Address as consignee_addr, csn.brandID,
									(SELECT COUNT(ID) FROM $tblinvoicedetail WHERE invID='$ID' AND group_number>0) as countDetail, 
									(SELECT SUM(total_amount) FROM $tblinvoicedetail 
											WHERE invID='$ID' AND del=0) as totalamount,
									(SELECT SUM(qty) FROM $tblinvoicedetail 
											WHERE invID='$ID' AND del=0 AND group_number>0) as totalqty,
									(SELECT SUM(qty) 
                                      		FROM $tblinvoicedetail bid
                                      		INNER JOIN tblship_group_color sgc 
                                      								ON sgc.shipmentpriceID = bid.shipmentpriceID
                                      								AND sgc.group_number = bid.group_number
                                      								AND sgc.statusID=1
											WHERE bid.invID='$ID' AND bid.del=0 AND bid.group_number>0) as totalqty_pcs,
									(SELECT group_concat(distinct shipmentpriceID) FROM $tblinvoicedetail 
											WHERE invID='$ID' AND del=0 AND group_number>0) as invpo,
									bnp.NotifyAddress
									FROM $tblinvoice inv 
									LEFT JOIN tblconsignee csn ON csn.ConsigneeID=inv.ConsigneeID
									LEFT JOIN tblbuyer_notify_party bnp ON bnp.notifyID = inv.notifyID
									WHERE inv.ID='$ID'
									group by inv.ID";
		if($acctid==1){
			// echo "<pre>$sqlinv</pre>";
		}
		$selectedinv = $conn->prepare($sqlinv);
		$selectedinv->execute();
		$selectedrow = $selectedinv->fetch(PDO::FETCH_ASSOC);
	
		$selectedIssueFrom = $selectedrow['issue_from'];
		$selectedIssueAddr = htmlspecialchars_decode($selectedrow['issue_from_addr']);
		$selectedBuyerID   = $selectedrow["BuyerID"];
		$this_consignee    = $selectedrow["ConsigneeID"];
		$consignee_addr    = strtoupper(htmlspecialchars_decode($selectedrow["consignee_addr"]));
		$selectedNotifyID  = $selectedrow["notifyID"];
		$notify_party      = $selectedrow["notify_party"];
		$notify_address    = strtoupper(htmlspecialchars_decode($selectedrow["NotifyAddress"]));
		$shipper           = $selectedrow["shipper"];
		$shipper_address   = htmlspecialchars_decode($selectedrow["shipper_address"]);
		
		$selected_invpo  = $selectedrow["invpo"]; 
		$this_consignee  = rtrim($this_consignee,",");
		$this_consignee  = ltrim($this_consignee,",");
		$selectedConID   = str_replace(",", "|", $selectedrow["ConsigneeID"]); 
		
		$selectedCargocutoff = $selectedrow["cargocutoffdate"]; 
		$selectedExfactory   = $selectedrow["exfactory"]; 
		$byr_invoice_no      = $selectedrow["byr_invoice_no"]; 
		$selectedInvNo       = $selectedrow["invoice_no"]; 
		$selectedInvDate     = $selectedrow["invoice_date"]; 
		$poissue_date        = $selectedrow["poissue_date"]; 
		$selectedShippeddate = $selectedrow["shippeddate"]; 
		$selectedVesselname  = $selectedrow["vesselname"]; 
		$selectedStatus      = $selectedrow["statusID"]; 
		$selectedBuiltTo     = $selectedrow["built_to"]; 
		$selectedPOIssuer    = $selectedrow['poissuer'];
		$selectedTotalAmount = $selectedrow['totalamount'];
		$totalqty            = $selectedrow['totalqty'];
		$totalqty_pcs        = $selectedrow['totalqty_pcs'];

		$selectedPOL         = $selectedrow['portLoadingID'];
		$selectedporID       = $selectedrow['porID'];
		$selectedShipmode    = $selectedrow['shipmodeID'];
		$selectedTradeTerm   = $selectedrow['tradeTermID'];
		$selectedPaymentTerm = $selectedrow['paymentTermID'];

		$selectedPOD       = $selectedrow['PortDestID'];
		$transitPortID     = $selectedrow['transitPortID'];
		$selectedBD        = $selectedrow['BuyerDestID'];
		$selectedExporter  = $selectedrow['export_by'];
		$remarks           = $selectedrow['remarks'];
		$ship_to           = $selectedrow['ship_to'];
		$ship_address      = $selectedrow['ship_address'];
		$selectedBID       = $selectedrow['BID'];
		$container_no 	   = $selectedrow["container_no"];
		$ETA        	   = $selectedrow["ETA"];
		$carrier     	   = $selectedrow["carrier"];
		$seal_no     	   = $selectedrow["seal_no"];
		$cdc_no     	   = $selectedrow["cdc_no"];
		$cdc_date     	   = $selectedrow["cdc_date"];
		$brandID     	   = $selectedrow["brandID"];
		$from_invID        = $selectedrow["from_invID"];
		$from_invID        = ($from_invID==0? $ID: $from_invID);
		
		$join_inspection_no   = "";//$selectedrow["join_inspection_no"];
		$join_inspection_date = "";//$selectedrow["join_inspection_date"];
		$custom_procedure     = "";//$selectedrow["custom_procedure"];
		$co_number            = "";//$selectedrow["co_number"];
		$co_date              = "";//$selectedrow["co_date"];
		$conversion_rate      = $selectedrow["conversion_rate"];

		$createdBy   = $selectedrow['createdby'];
		$createdDate = $selectedrow['createddate'];
		$updatedBy   = $selectedrow['updatedby'];
		$updatedDate = $selectedrow['updateddate'];

		$selectedCountDetail = $selectedrow["countDetail"];
		
		$sqlAR = "SELECT aie.entryID, aie.statusID 
											FROM tblfin_ar_invoice_entry_detail aied
											INNER JOIN tblfin_ar_invoice_entry aie ON aie.AREID=aied.AREID
											WHERE aied.invoiceID='$ID' AND aied.del=0 AND aie.statusID NOT IN (6) limit 1";
		// echo $sqlAR;
		$stmt_ar = $conn->prepare($sqlAR);
		$stmt_ar->execute();
		$count_ar = $stmt_ar->rowCount();
		$lbl_ar = ($count_ar>0? "<label class='label label-info'>AR Receipt</label>":"");
	}
	else{
		$arr_cdc  = $handle_tblbuyer_invoice->getLatestCDCNoAndDate();
		
		$selectedBuyerID = "";
		$selectedConID = "";
		$consignee_addr = "";
		$selectedNotifyID = "0";
		$notify_party = "";
		$notify_address = "";
		$shipper = "G00";
		$shipper_address = "";
		$selected_invpo = "";
		$selectedCargocutoff = "";
		$selectedExfactory = "";
		$selectedInvNo = "-";
		$selectedporID = "0";
		$byr_invoice_no = "";
		$selectedInvDate = "$today_date";
		$poissue_date = "";
		$selectedShippeddate = "";
		$selectedVesselname = "";
		$selectedStatus = 4;
		$selectedPOIssuer = "";
		$selectedTotalAmount = "";
		$selectedExporter = "$acctid";
		$selectedPOD = "";
		$transitPortID = "201";
		$selectedBD = "";
		$this_consignee = "";
		$selectedPOL = "";
		$selectedTradeTerm = "";
		$selectedShipmode = "";
		$selectedPaymentTerm = "";
		$totalqty = 0;
		$totalqty_pcs = 0;
		$selectedBuiltTo = "";
		$remarks = "";
		$ship_to = "";
		$ship_address = "";
		$selectedBID = "1";//vendor bank account
		$container_no = "";
		$ETA        	   = "";
		$carrier     	   = "";
		$seal_no     	   = "";
		$join_inspection_no   = "";
		$join_inspection_date = "";
		$custom_procedure     = "";
		$co_number            = "";
		$co_date              = "";
		$conversion_rate      = "4010";
		$count_ar = 0;
		$lbl_ar = "";
		$cdc_no     	   = $arr_cdc["cdc_no"];
		$cdc_date     	   = $arr_cdc["cdc_date"];
		$from_invID        = "0";
		$brandID = "";
		
		$sql = "SELECT ID,Address FROM tblcompanyprofile WHERE isDefault=1";
		$stmt_company = $conn->prepare($sql);
		$stmt_company->execute();
		$row_company = $stmt_company->fetch(PDO::FETCH_ASSOC);
		$selectedIssueAddr = $row_company["Address"];
		$selectedIssueFrom = $row_company["ID"];
		
		$sqlShipper = "SELECT Address FROM tblfactory WHERE FactoryID='$shipper'";
		$stmt_shipper = $conn->prepare($sqlShipper);
		$stmt_shipper->execute();
		$row_shipper = $stmt_shipper->fetch(PDO::FETCH_ASSOC);
		$shipper_address = $row_shipper["Address"];
		
	}

$opt = substr("$selectedInvNo", -1);
$isInvOption = (in_array("$opt", $arr_alphabet)? "1":"0");
$disabled_action = (($selectedStatus==8 || $selectedStatus==11) && $acctid!=1? "disabled":"");
$handle_lc->setDisabledAction($disabled_action);
$script_date = "201811271252";

?>
<!DOCTYPE html>
<html>
<head>
	<title><?= $module_name ?></title>

	<meta charset="utf-8"/>
	<link rel="stylesheet" type="text/css" href="../../media/css/bootstrap.min.css?date=<?php echo $script_date; ?>" />
	<link rel="stylesheet" type="text/css" href="../css/style.css?date=<?php echo $script_date; ?>"  />
	<link rel="stylesheet" type="text/css" href="../../media/css/bootstrap-chosen.css?date=<?php echo $script_date; ?>">
	<link rel="stylesheet" type="text/css" href="../css/new_ship_style.css?date=<?php echo $script_date; ?>"  /> <!-- ckwai on 2018-06-07 -->
	
	<!---  Date Picker Css --->
	<link rel="stylesheet" type="text/css" href="../css/jquery-datepicker-ui.css?date=<?php echo $script_date; ?>" />
	<link rel="stylesheet" type="text/css" href="js/jquery-ui-timepicker-addon.css?date=<?php echo $script_date; ?>" />
	
	<script type="text/javascript" language="javascript" src="../../media/js/jquery-1.9.1.js?data=<?php echo $script_date; ?>"></script>
	<script type="text/javascript" language="javascript" src="../../media/js/bootstrap.min.js?data=<?php echo $script_date; ?>"></script>
	<script type="text/javascript" language="javascript" src="../../media/js/chosen.jquery.js?data=<?php echo $script_date; ?>" ></script> 
	 
	<!--- Date Picker Javascript -->
	<script type="text/javascript" language="javascript" src="../js/jquery-1.11.1-ui.js?date=<?php echo $script_date; ?>"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-ui-timepicker-addon.js?date=<?php echo $script_date; ?>"></script>
	<script type="text/javascript" language="javascript" src="../css/bootstrap.js" ></script> <!-- This js affect datepicker timer slider UI -->
	<script type="text/javascript" language="javascript" src="js/jquery-ui-sliderAccess.js?date=<?php echo $script_date; ?>"></script>

	<!-- datatable -->
	<link rel="stylesheet" type="text/css" href="../../media/css/dataTables.tableTools.css">
	<link rel="stylesheet" type="text/css" href="../../media/css/sommain.css">
	<script type="text/javascript" language="javascript" src="../../media/js/jquery.dataTables_1.10.7.js"></script>
	<script type="text/javascript" language="javascript" src="../../media/js/dataTables.tableTools.js"></script>
	<script type="text/javascript" language="javascript" src="../../media/js/misc.js"></script>
	<script type="text/javascript" language="javascript" src="../js/dataTables.rowsGroup.js"></script>

	<style type="text/css">
		body {
			background-color: #f3f3f4;
		}

		.td_data {
			max-width: 30%;
			width: 30%;
			/*border: 1px solid #bdbdbd;*/
			border-radius: 0.5em;
		}

		table.tb_info2 {
			width: 85% !important;
		}

		h4.acc_header {
			border: 1px solid #bdbdbd; 
			margin-bottom: 0; 
			padding: 5px 12px; 
			cursor: pointer; 
			border-top-left-radius: 5px; 
			border-top-right-radius: 5px; 
			color: #3f4c6b; 
			font-weight:bold;
			background-color: white;
			box-shadow: 0px 0px 6px 1px #bdbdbd;
		}

		h4.acc_header.active {
			background-color: #4A89DC;
			color: white;
		}

		section#add_form {
			padding: 0 8px;
			border: 1px solid #bdbdbd;
			border-radius: 0px 0px 5px 5px;
			background-color: #fff;
			transition: max-height 0.2s ease-out;
			box-shadow: 0px 0px 6px 1px #bdbdbd;
			
		}

		div#content {
			padding-bottom: 20px;
		}

		a.btn {
			text-decoration: none;
			color: inherit;
		}

		button.btn-pdf:hover {
			color: #fff;
			background-color: #d2322d;
			border-color: #ac2925;
		}

		table.datatable td,
		table.datatable th {
			border: 0.5px solid #bdbdbd;
		}

		.datepicker, 
		.datetimepicker {
			background: #e6e6e6 url(../images/icon-calendar.png) no-repeat scroll 200px 3px !important;
			color: black;
		}
		
		.datepicker_short{
			background: #e6e6e6 url(../images/icon-calendar.png) no-repeat scroll 80px 3px !important;
		}

		.txt_medium, 
		.select_medium {
			padding: 2px !important;
		}

		.chosen-single {
			height: inherit !important;
			line-height: inherit !important;
			padding: 2px !important; 
		}

		.titlebar9 {
			background-color: lightgray;
			color: black;
		}

		.tb_inv th, 
		.tb_inv td.detail {
			border: 1px solid #bdbdbd !important;
		}
		.tb_inv_expand td {
			border: 1px solid #bdbdbd !important;
		}

		a.btn_form{
			padding: 5px;
			border-radius: 5px;
			border: 1px solid #bdbdbd;
			cursor: pointer;
			color: white;
		}

		.chosen-disabled {
			opacity: 0.8 !important;
		}
		.textarea_style{
			padding:3px;
			border:1px solid #bdbdbd;
			border-radius:3px;
		}
		.tr_nondisplay{
			display:none;
		}
		.txt_xs{
			width:55px;
			border-radius:3px;
			border:1px solid #bdbdbd;
			padding:3px;
		}
		.txt_short{
			width:50px;
		}
		/* Chrome, Safari, Edge, Opera */
		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
		  -webkit-appearance: none;
		  margin: 0;
		}
		/* Firefox */
		input[type=number] {
		  -moz-appearance: textfield;
		}
		.large.tooltip-inner {
			max-width: 350px;
			width: 350px;
		}
		legend{
			font-size:14px;
			color:#258dc8;
			font-weight:bold;
		}
	</style>

	<?php include("buyer_inv_js.php"); ?>

	<?php 
	if(isset($ID)){
	?>
		<script type="text/javascript">
			$(document).ready(function(){
				func_get_consignee(1);
				//func_consignee_info('<?= $selectedConID; ?>');
				form_checking();
				// form_checking();

				<?php 
				if(($selectedStatus == 8 || $selectedStatus==11) && $acctid!=1){
				?>
				$("#form fieldset input, #form fieldset select , #form fieldset button, textarea").attr("disabled", true);
				<?php
					if(in_array(5, $arr) && $count_ar==0){
						echo "$('#form fieldset #status').removeAttr('disabled');";
						// echo "$('#form fieldset .chk_valid').removeAttr('disabled');";
						
					} 
				}

				?>

				if($("#allowconfirmstatus").val()=="1"){
					$("#status8").prop("disabled",false);
				}else{
					$("#status8").prop("disabled",true);
				}

				$('[data-toggle="tooltip"]').tooltip(); 
				
				var ele = document.getElementById("tb_header");
				var width = ele.getBoundingClientRect().width
				document.getElementById("add_form").style.width = width.toFixed(2)+"px";
				document.getElementById("lbl_header").style.width = width.toFixed(2)+"px";
				//alert(width);
				
				$('#form fieldset #status').removeAttr('disabled');
				$('#form fieldset input:checkbox').removeAttr('disabled');
				$('#form fieldset input:hidden').removeAttr('disabled');
				$('#form fieldset .btn_exp').removeAttr('disabled');
			});
			
			function funcLoadTestReport(){
				var countPO = document.getElementById("countPO").value;
				var arr_orderno = [];
				for(var i=0;i<=parseInt(countPO);i++){
					var orderno = document.getElementById("orderno"+i);
					if(orderno!=null){
						arr_orderno.push(orderno.value);
					}
				}
				
				var arr_orderno = arr_orderno.filter(onlyUnique);
				var str_orderno = arr_orderno.toString();
				
				func_display_lg("../../SOM/fabtest_summary.php?from=BP&orderno="+str_orderno);
			}
			
			function onlyUnique(value, index, self) {
			  return self.indexOf(value) === index;
			}
			
		</script>
	<?php 
	}
	else{
?>
	<script type="text/javascript">
			$(document).ready(function(){
				//alert("tooltip");
				$('[data-toggle="tooltip"]').tooltip(); 
				
				var ele = document.getElementById("tb_header");
				var width = ele.getBoundingClientRect().width
				document.getElementById("add_form").style.width = width.toFixed(2)+"px";
				document.getElementById("lbl_header").style.width = width.toFixed(2)+"px";
			});
	</script>
<?php		
	}

	?>

</head>
<body>

	<?php include("../../includes/code_loading.php"); ?>
	<?php include("../../includes/code_display_page.php"); 
		$buyer_inv_save = ($acctid==0? "buyer_inv_save2.php?a=$getLink": "buyer_inv_save.php?a=$getLink");
	?>

	<div id="content">
		<div id="inner">
			<form action="<?= $buyer_inv_save; ?>" id="form" style="padding: 8px;" method="POST" onsubmit="return checkForm();"> 
			<input type="hidden" id="allowconfirmstatus" value="0" />
			<input type="hidden" id="isBuyerPayment" name="isBuyerPayment" value="<?= $isBuyerPayment; ?>" />
			<?php if( !isset($ID) ) $label = "Add"; else $label = "Edit"; ?> 
			<table>
			<tr>
				<td><h5 style="margin: 0 !important; color: black">Export >>> <?= $module_name ?> >>> <?= $label; ?></h5></td>
				</tr>
			</table>
			
			<h4 class="acc_header" id="lbl_header" title="" > <!-- onclick="accordion('add_form',this);" -->
					<?php //if( !isset($ID) ) echo "Add"; else echo "Edit"; ?> 
				<table>
				<tr>
					<td><?php $this_backurl = "buyer_inv_list.php?a=$getLink"; ?>
						<input type="button" onclick="funcBack('<?= $this_backurl; ?>')" 
								class="btn btn-danger btn-sm" value=" Back "/></td>
					<?php 
						if( (in_array("2", $arr) || in_array("3", $arr))){
							echo '<td><button type="button" name="submitbtn" id="submitbtn" class="btn btn-success btn-sm" onclick="finalcheck()">
							<span class="glyphicon glyphicon-floppy-disk"></span> Save Invoice</button></td>';
						}//$selectedStatus !== '11' &&
						
						$this_url = ((isset($ID)) ? "buyer_inv.php?id=$ID$getLink" :"buyer_inv.php$getLink" );
					?>
					<td><button type="button" class="btn btn-default btn-sm" onclick="funcBack('<?= $this_url; ?>')">
					<span class="glyphicon glyphicon-refresh"></span> Refresh</button></td>
					<?php if((in_array("6", $arr) || in_array("7", $arr)) && isset($_GET["id"])){
							$html = $handle_lc->funcGetDropDownPrint($ID, $selectedBuyerID, "btn-sm", $isBuyerPayment);
							echo "<td>$html</td>";
						}	
					$url_upload = "upload.php?id=$selectedInvNo&&isBuyerPayment=$isBuyerPayment";
					echo "<td><button type='button' class='btn btn-info btn-sm' onclick='funcPropOut(&#39;$url_upload&#39;)'>Upload File</button></td>";

					echo "<td>$btn_loadagain</td>";
					if($isBuyerPayment==1 && $ID!="" ){ //&& $acctid==1
						echo '<td><button type="button" class="btn btn-default btn-sm" onclick="funcLoadTestReport()">	
									<span class="glyphicon glyphicon-th-list"></span> Test Report</button></td>';
						echo '<td><button type="button" class="btn btn-default btn-xs" onclick="funcAddPaymentOpt()">
										<span class="glyphicon glyphicon-plus" ></span> Add Option</button>
									<input type="hidden" id="from_invID" value="'.$from_invID.'"></td>';
						echo '<td><a type="button" class="btn btn-warning btn-xs" href="cb_index.php?invID='.$ID.'" target="_blank">
										 Cost Breakdown</a>
									<input type="hidden" id="from_invID" value="'.$from_invID.'"></td>';
						echo '<td><div class="dropdown">
								<button class="btn btn-primary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Print
								</button>
								<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									<li><a class="dropdown-item" href="cb_pdf.php?invID='.$from_invID.'">PDF</a></li>
									<li><a class="dropdown-item" href="cb_excel.php?invID='.$from_invID.'">Excel</a></li>
								</ul>
							</div></td>';
					}
						?>
					
					<td>
						<?php 
							$mirroring_txt   = "Mirroring to Commercial Invoice";
							$sync_advise_txt = "Update to shipping advice after status confirmed";
							$sync_po_txt     = "Update to Buyer PO after status confirmed";
							$notice_head = "";
							$notice_sync_advise = " &nbsp; <span style='font-size:13px;color:green' class='glyphicon glyphicon-ok-sign'></span> 
													<font style='font-size:12px;bold-weight:none'>$sync_advise_txt</font>";
							$notice_sync_po     = " &nbsp; <span style='font-size:13px;color:orange' class='glyphicon glyphicon-ok-sign'></span> 
													<font style='font-size:12px;bold-weight:none'>$sync_po_txt</font>";
													
							$icon_sync_advise = " <span style='font-size:13px;color:green' class='glyphicon glyphicon-ok-sign' data-toggle='tooltip' 
													title='$sync_advise_txt'></span>";
							$icon_sync_po     = " <span style='font-size:13px;color:orange' class='glyphicon glyphicon-ok-sign' data-toggle='tooltip' 
													title='$sync_po_txt'></span>";
							
							if($isBuyerPayment==0){ // Commercial Invoice
								$notice_head .= '<small>
												</small>';
								$mirroring_txt   = "Mirroring to Buyer Payment Invoice";
													
								$notice_sync_advise = "";
								$notice_sync_po     = "";
								$icon_sync_advise   = "";
								$icon_sync_po       = "";
								
							}
							$notice_head .= "$notice_sync_po $notice_sync_advise &nbsp; <span style='font-size:13px;color:#21b4e2' class='glyphicon glyphicon-ok-sign'></span> <font style='font-size:12px;bold-weight:none'>$mirroring_txt</font>";
							
							
							$icon_mirror = "<span style='font-size:13px;color:#21b4e2' class='glyphicon glyphicon-ok-sign' data-toggle='tooltip' 
													title='$mirroring_txt'></span>";
						echo $notice_head;
						?>
						
					</td>
					</tr>
					</table>
				
				<?php
				if(isset($_GET['id'])){
					$sel_createdBy=$conn->prepare("SELECT UserFullName FROM tbluseraccount WHERE AcctID='$createdBy'");
					$sel_createdBy->execute();
					$createdBy=$sel_createdBy->fetchColumn();

					$sel_updatedBy=$conn->prepare("SELECT UserFullName FROM tbluseraccount WHERE AcctID='$updatedBy'");
					$sel_updatedBy->execute();
					$updatedBy=$sel_updatedBy->fetchColumn();

					?>
					<span style="float: right;font-size: 12px;margin-top:-30px" class="glyphicon glyphicon-info-sign" data-html="true" data-toggle="tooltip" data-placement="bottom" data-original-title='<?php echo "<b>Created By:</b> ".$createdBy."<br>".$createdDate."<br> <b>Last Updated By:</b> ".$updatedBy." <br> ".$updatedDate ?>'></span>
					<?php
				}
				?>
			</h4>
			
			<section id="add_form">

			<?php $this_invID = (isset($ID)) ? $ID : '-999'; ?>
			<input type="hidden" name="invID" id="invID" value="<?php echo $this_invID; ?>" />
			<input type='hidden' name='temp_status' id='temp_status' value='<?= $selectedStatus; ?>' />
			

			<fieldset <?php //if($selectedStatus == 11) echo "disabled"; ?> >
				<table class="tb_info tb_info2" id="tb_header" width="100%" border=0 >
				<tbody>
				<tr>
					<td class="td_head" style="min-width:120px"><?= $icon_mirror; ?>  Buyer: </td>
					<td class="td_data">
							<select name="buyer" class="select_medium select_chosen" id="buyer_select" 
									onchange="func_get_consignee(0);clearred(this.id);" required>
								<option value="" selected="true">Select a buyer</option>
								<?php
								$buyersql = $conn->query("SELECT * FROM tblbuyer WHERE StatusID=1 or BuyerID='$selectedBuyerID'");
								while($buyerrow = $buyersql->fetch(PDO::FETCH_ASSOC)){
									
									$BuyerID = $buyerrow["BuyerID"];
									$BuyerName_Eng = $buyerrow["BuyerName_Eng"];
									$selected = ($BuyerID == $selectedBuyerID) ? "selected" : "" ;
									echo "<option value='$BuyerID' $selected>$BuyerName_Eng / $BuyerID</option>";
								}

								?>
							</select> <font color="red" id="valid_buyer_select">*</font>
						</td>
					<td class="td_head" style="min-width:120px">
						<?= $icon_mirror; ?>
						<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" 
							title="Default link from buyer master consignee payer"></span> Bill To: </td>
					<td class="td_data" id="html_payer">
						<?php
							// $filter_payer = $handle_lc->funcGetConsigneePaymentTerm($this_consignee, "payer");
							$filter_payer = ($brandID==""? "":" AND brandID='$brandID'");
							echo $html = $handle_lc->funcGetPayer($selectedBuiltTo, $filter_payer);
							?>
					</td>
					<td class="td_head" style="min-width:120px">Status: </td>
						<td class="td_data">
							<?php 
								
							?>
							<select name="status" id="status" class="select_medium" required  >
								
									<option value="4" id="status4" <?php if($selectedStatus==4) echo "selected"; ?>>Editing</option>
									<option value="6" id="status6" <?php if($selectedStatus==6) echo "selected"; ?>>Cancelled</option>
									
									<?php if(in_array(5, $arr) || $selectedStatus==8 ){//Check Permission Approve or Status is Confirmed?>
									<option value="8" id="status8" disabled="true" 
											<?php if($selectedStatus==8) echo "selected "; ?> >Confirmed</option>
									<?php
									}//--- End Check Permission or Status is Confirmed or Completed ---//
									
									if(in_array(5, $arr) || $selectedStatus==11){
									?>
									<option value="11" id="status11"
											<?php if($selectedStatus==11) echo "selected "; ?> >Completed</option>
									<?php
									}//--- End Check Permission or Status is Confirmed or Completed ---//
									?>
									
									
								
							</select><?= $lbl_ar; ?><br/>
							<span id="status_notice" style="font-size: 12px;color:red;"></span>
						</td>
					</tr>
					<tr>
						<td class="td_head"><?= $icon_mirror; ?><?= $icon_sync_advise ?> Invoice no.: 
											<input type="hidden" name="existInvoice" id="existInvoice" value="0" />
											</td>
						<td class="td_data"><input type="text" id="invoice_no" name="invoice_no" class="txt_medium" value="<?= $selectedInvNo; ?>" 
													onkeyup="clearred(this.id);checkInvoiceExist();" readonly />
						<font color="red" id="valid_invoice_no">*</font></td>
						<td class="td_head"><?= $icon_sync_advise ?> Invoice date: </td>
						<td class="td_data"><input type="text" id="invoice_date" name="invoice_date" class="txt_medium datepicker" value="<?= $selectedInvDate; ?>" onchange="clearred(this.id);" required readonly />
						<font color="red" id="valid_invoice_date">*</font> </td>
						<td class="td_head">
								<?= $icon_mirror; ?>  Shipmode: 
								<input type="hidden" name="txt_shipmode" id="txt_shipmode" value="<?= $selectedShipmode ?>" />
								</td>
						<td class="td_data" id="html_shipmode">
							
								<?php
								$filter_paymentterm = $handle_lc->funcGetConsigneePaymentTerm($this_consignee, "shipmode");
							
								echo $html = $handle_lc->funcGetShipMode($selectedShipmode, $filter_paymentterm);
								?>
							<!-- <input type="text" class="txt_medium" name="txt_shipmode" id="txt_shipmode" value="" readonly placeholder="Shipmode" /> -->
							<font color="red" id="valid_shipmode">*</font>
						</td>
						</tr>
					<tr style="display:none">
						<td class="td_head"> <?= $icon_mirror; ?> <span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" 
													title="From buyer master consignee po issuer"></span> Buyer PO Issuer: </td>
						<td class="td_data" id="html_poissuer">
							<?php
								$filter_payer = $handle_lc->funcGetConsigneePaymentTerm($this_consignee, "poissuer");
								
								echo $html = $handle_lc->funcGetPoissuer($selectedPOIssuer, $filter_payer);
								?>
						
						</td>
						<td class="td_head"><?= $icon_mirror; ?>  Buyer PO Issue Date: </td>
						<td class="td_data">
							<input type="text" id="poissue_date" name="poissue_date" class="txt_medium datepicker" 
									value="<?= $poissue_date; ?>" onchange="clearred(this.id);" required readonly />
							<font color="red" id="valid_poissue_date"></font>
						</td>
						<td></td>
						<td></td>
						</tr>
					<!------------------------------------------------------------------------------------------------------------>
					<!------------------------------------------ Address Field --------------------------------------------------->
					<!------------------------------------------------------------------------------------------------------------>
					<tr>
						<td colspan="6" align="center">
							<table>
							<tr>
								<td >
									<fieldset style="border:1px solid #bdbdbd;padding:5px;border-radius:3px">
										<legend><?= $icon_mirror; ?> Vendor</legend>
										<table>
										<tr>
											<td><label for="fname">Name: &nbsp; &nbsp;</label><br/>
												<select name="issuefrom" id="issuefrom" class="select_medium" onchange="getAddress('owner_address', this.value)" >
													<option value="0">-- SELECT --</option>
													<?php 
														$sql  = "SELECT ID as VID, CompanyName_ENG FROM tblcompanyprofile ";
														$stmt = $conn->prepare($sql);
														$stmt->execute();
														while($row_company = $stmt->fetch(PDO::FETCH_ASSOC)){
															extract($row_company);
															
															$selected = ($selectedIssueFrom==$VID? "selected":"");
															
															echo "<option value='$VID' $selected>$CompanyName_ENG</option>";
														}
													?>
												</select><br/>&nbsp;
												</td>
											</tr>
										<tr>
											<td><label for="lname">Address:</label><br/>
												<textarea name="issue_from_address" id="issue_from_address" 
															class="textarea_style" cols="40" rows="3" readonly><?= $selectedIssueAddr; ?></textarea></td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td>
									<fieldset style="border:1px solid #bdbdbd;padding:5px;border-radius:3px">
										<legend><?= $icon_mirror; ?> Consignee</legend>
										<table>
										<tr>
											<td><label for="fname">Name: &nbsp; &nbsp;</label><br/>
												<select name="consignee[]" id="consignee_select" class="select_medium select_chosen" 
												onchange="func_consignee_info(this.value);clearred(this.id);"  <?= $disabled_action; ?> required>
											<!-- <option value="" disabled=true selected=true>Select a consignee</option> -->
											</select> <font color="red" id="valid_consignee_select">*</font>
												<br/>
											<em><font color="blue">**Only display 1 consignee address</font></em>
												</td>
											</tr>
										<tr>
											<td><label for="lname">Address: </label><br/>
												<textarea name="consignee_address" id="consignee_address" 
															class="textarea_style" cols="40" rows="3" ><?= $consignee_addr; ?></textarea></td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td >
									<fieldset style="border:1px solid #bdbdbd;padding:5px;border-radius:3px">
										<legend><?= $icon_mirror; ?> Notify Party</legend>
										<table>
										<tr>
											<td><label for="fname">Name: &nbsp; &nbsp;</label><br/>
												<select name="sel_notify_party" id="sel_notify_party" class="select_medium select_chosen" 
														onchange="getAddress('notify_address', this.value)">
													<option value="0">-- SELECT --</option>
													<?php 
														$sql = "SELECT bnp.notifyID, bnp.NotifyName, bnp.NotifyAddress, 
																		bnp.tel, bnp.fax, bnp.email, bnp.IsDefault 
																FROM tblconsignee cs 
																INNER JOIN tblbuyer_notify_party bnp ON bnp.consigneeID = cs.ConsigneeID
																WHERE cs.statusID NOT IN (2) AND bnp.del = 0 AND bnp.NotifyName<>''";
														$stmt = $conn->prepare($sql);
														$stmt->execute();
														while($row_notify = $stmt->fetch(PDO::FETCH_ASSOC)){
															extract($row_notify);
															
															$selected = ($selectedNotifyID==$notifyID? "selected": "");
															
															echo "<option value='$notifyID' $selected>$NotifyName</option>";
														}
													?>
												</select>
												<input type="hidden" name="notify_party" id="notify_party" class="txt_medium" value="<?= $notify_party; ?>" />
												<br/>&nbsp;
												</td>
											</tr>
										<tr>
											<td><label for="lname">Address:</label><br/>
												<textarea name="notify_address" id="notify_address" 
															class="textarea_style" cols="40" rows="3"><?= $notify_address; ?></textarea></td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td>
									<fieldset style="border:1px solid #bdbdbd;padding:5px;border-radius:3px">
										<legend><?= $icon_mirror; ?> Shipper</legend>
										<table>
										<tr>
											<td><label for="fname">Name: &nbsp; &nbsp;</label><br/>
												<select name="shipper" id="shipper" class="select_medium" onchange="getAddress('shipper_address', this.value)" >
													<option value="0">-- SELECT --</option>
													<?php 
														$sql  = "SELECT * FROM tblfactory WHERE StatusID='1'";
														$stmt = $conn->prepare($sql);
														$stmt->execute();
														while($row_shipper = $stmt->fetch(PDO::FETCH_ASSOC)){
															extract($row_shipper);
															
															$selected = ($shipper==$FactoryID? "selected":"");
															
															echo "<option value='$FactoryID' $selected>$FactoryName_ENG</option>";
														}
													?>
												</select><br/>&nbsp;
												</td>
											</tr>
										<tr>
											<td><label for="lname">Address:</label><br/>
												<textarea name="shipper_address" id="shipper_address" 
															class="textarea_style" cols="40" rows="3" readonly><?= $shipper_address; ?></textarea></td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td>
									<fieldset style="border:1px solid #bdbdbd;padding:5px;border-radius:3px">
										<legend><?= $icon_mirror; ?> Ship To  </legend>
										<table>
										<tr>
											<td><label for="fname">Name: &nbsp; &nbsp;</label><br/>
												<input type="text" name="ship_to" id="ship_to" class="txt_medium" value="<?= $ship_to; ?>" />
												<br/>&nbsp;
												</td>
											</tr>
										<tr>
											<td><label for="lname">Address:</label><br/>
												<textarea name="ship_address" id="ship_address" 
															class="textarea_style" cols="40" rows="3"><?= $ship_address; ?></textarea></td>
											</tr>
										</table>
									</fieldset>
								</td>
								</tr>
								</table>
							
						</td>
						</tr>
					<!--------------------------------------- End Address ------------------------------------------>
					<!---------------------------------------------------------------------------------------------->
					<tr>
						<td class="td_head">
								<?= $icon_mirror; ?>  Trade term: 
								<input type="hidden" name="txt_tradeterm" id="txt_tradeterm" value="<?= $selectedTradeTerm ?>" />
								</td>
						<td class="td_data" id="html_tradeterm">
							
								<?php
								$filter_paymentterm = $handle_lc->funcGetConsigneePaymentTerm($this_consignee, "tradeterm");
								$filter_paymentterm = "";
								
								echo $html = $handle_lc->funcGetTradeTerm($selectedTradeTerm, $filter_paymentterm);
								?>
							<!-- <input type="text" class="txt_medium" name="txt_tradeterm" id="txt_tradeterm" value="" readonly placeholder="Trade term" /> -->
							<font color="red" id="valid_tradeterm"></font>
						</td>
						<td class="td_head">
							 <?= $icon_mirror; ?>  Buyer Destination:
							<input type="hidden" name="txt_buyerdestination" id="txt_buyerdestination" value="<?= $selectedBD ?>" />
							</td>
						<td class="td_data" id="html_buyerdestination">
							
								<?php
								$filter_paymentterm = $handle_lc->funcGetConsigneePaymentTerm($this_consignee, "buyerdestination");
								$filter_paymentterm = "";
								
								echo $html = $handle_lc->funcGetBuyerDestination($selectedBD, $filter_paymentterm);
								?>
							<!-- <input type="text" class="txt_medium" name="txt_portloading" id="txt_portloading" value="" readonly placeholder="Depart FROM" /> -->
							<font color="red" id="valid_buyerdestination">*</font>
						</td>
						<td class="td_head"><?= $icon_mirror; ?>  Port of Discharges:
								<input type="hidden" name="txt_portofdischarges" id="txt_portofdischarges" value="<?= $selectedPOD ?>" />
								</td>
						<td class="td_data" id="html_portofdestination">
								<?php
								$filter_paymentterm = $handle_lc->funcGetConsigneePaymentTerm($this_consignee, "portofdischarges");
								$filter_paymentterm = "";
								
								echo $html = $handle_lc->funcGetPortDestination($selectedPOD, $filter_paymentterm);
								?>
							
							<!--<select name="portofdischarges" id="portofdischarges" class="select_medium select_chosen" onchange="func_write_hidden('txt_portofdischarges',this.value);clearred(this.id);">
								<?php
								$sel=$conn->prepare("SELECT * FROM tbldestinationport WHERE StatusID=1");
								$sel->execute();

								while($row=$sel->fetch(PDO::FETCH_ASSOC)){
									?>
									<option value="<?php echo $row['ID'] ?>"
										<?php
										if($selectedPOD==$row['ID']){
											echo "SELECTED";
										}
										?>
										><?php echo $row['Description'] ?></option>
									<?php
								}
								?>
							</select> <font color="red" id="valid_portofdischarges">*</font>-->
							
							
						</td>
						
						</tr>
					<tr>
						<td class="td_head"><?= $icon_mirror; ?>  Transit Port  </td>
						<td class="td_data"><select name="transitPortID" id="transitPortID" class="select_medium select_chosen" >
											<option value='0'>-- Select Transit Port --</option>
											<?php 
												$sqltransit = "SELECT ID, Description
																FROM tblcountry_transitport 
																WHERE 1=1 order by Description asc";// AND ID IN (164, 184, 201)
												$stmt_transit=$conn->prepare($sqltransit);
												$stmt_transit->execute();
												while($row_bank = $stmt_transit->fetch(PDO::FETCH_ASSOC)){
													$this_transitID = $row_bank["ID"];
													$Description  = $row_bank["Description"];
													
													$selected = ($transitPortID==$this_transitID? "selected":"");
													
													echo "<option value='$this_transitID' $selected>$Description</option>";
													
												}
											?>
											</select></td>
						<td class="td_head">
								<?= $icon_mirror; ?>  Port of Loading: 
								<input type="hidden" name="txt_portloading" id="txt_portloading" value="<?= $selectedPOL ?>" /></td>
						<td class="td_data" id="html_portloading">
							
								<?php
								$filter_paymentterm = $handle_lc->funcGetConsigneePaymentTerm($this_consignee, "portloading");
								$filter_paymentterm = "";
								
								echo $html = $handle_lc->funcGetPortLoading($selectedPOL, $filter_paymentterm);
								?>
							<!-- <input type="text" class="txt_medium" name="txt_portloading" id="txt_portloading" value="" readonly placeholder="Depart FROM" /> -->
							<font color="red" id="valid_portloading">*</font>
						</td>
						<td class="td_head">
								<?= $icon_mirror; ?> 
								<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" 
													title="From buyer master consignee payment term"></span>  Payment term: 
								<input type="hidden" name="txt_paymentterm" id="txt_paymentterm" value="<?= $selectedPaymentTerm ?>" />
								</td>
						<td class="td_data" id="html_payment">
								<?php
								$filter_paymentterm = $handle_lc->funcGetConsigneePaymentTerm($this_consignee, "payment");
								
								echo $html = $handle_lc->funcGetPaymentTerm($selectedPaymentTerm, $filter_paymentterm);
								?>
							<!-- <input type="text" class="txt_medium" name="txt_paymentterm" id="txt_paymentterm" value="" readonly placeholder="Payment term" /> -->
							<font color="red" id="valid_paymentterm">*</font>
						</td>
						
						</tr>
					<tr>
						<td class="td_head"><?= $icon_mirror; ?>  <?= $icon_sync_advise ?> Cargo cut-off date:  </td>
						<td class="td_data"><input type="text" id="cargocutoff" name="cargocutoff" class="txt_medium datetimepicker" onchange="clearred(this.id);" 		value="<?= $selectedCargocutoff; ?>" required readonly /> <font color="red" id="valid_cargocutoff"></font></td>
						<td class="td_head"><?= $icon_mirror; ?>  <?= $icon_sync_advise ?> Ex-Factory Date: </td>
						<td class="td_data"><input type="text" id="exfactory" name="exfactory" class="txt_medium datetimepicker" onchange="clearred(this.id);" 		value="<?= $selectedExfactory; ?>" required readonly />
						 <font color="red" id="valid_exfactory"></font></td>
						<td class="td_head"><?= $icon_mirror; ?>  <?= $icon_sync_advise ?> Vessel ETD date: </td>
						<td class="td_data"><input type="text" name="shippeddate" id="shippeddate" class="txt_medium datepicker" onchange="clearred(this.id);" 		value="<?= $selectedShippeddate; ?>" required readonly />
						 <font color="red" id="valid_shippeddate">*</font></td>
						</tr>
					<tr>
						<td class="td_head"><?= $icon_mirror; ?> Carrier: </td>
						<td class="td_data">
							<input type="text" name="carrier" id="carrier" class="txt_medium" value="<?= $carrier; ?>" /></td>
						<td class="td_head"><?= $icon_mirror; ?> Seal#:</td>
						<td class="td_data">
							<input type="text" name="seal_no" id="seal_no" class="txt_medium" value="<?= $seal_no; ?>" /></td>
						<td class="td_head"><?= $icon_mirror; ?> Vessel ETA date: </td>
						<td class="td_data"><input type="text" id="ETA" name="ETA" onkeyup="clearred(this.id);"
													class="txt_medium datepicker" value="<?= $ETA; ?>" required />
											<font color="red" id="valid_ETA"></font></td>
						</tr>
					<tr>
						<td class="td_head"><?= $icon_mirror; ?> Exporter Signature: </td>
						<td class="td_data">
							<select name="exporter" id="exporter" class="select_medium select_chosen" <?= $disabled_action; ?>>
								<option value=""></option>
								<?php
								$sel_exporter=$conn->prepare("SELECT * FROM tbluseraccount WHERE sectionID='8' AND StatusID='1'");
								$sel_exporter->execute();

								while($row_exporter=$sel_exporter->fetch(PDO::FETCH_ASSOC)){
									$exporterID=$row_exporter['AcctID'];
									$exporter_name=$row_exporter['UserID'];
									?>
									<option value="<?php echo $exporterID; ?>"
										<?php
										if($exporterID==$selectedExporter){
											echo "SELECTED";
										}
										?>
										>
										<?php echo $exporter_name; ?>
									</option>
									<?php
								}
								?>
							</select></td>
						<td class="td_head"><?= $icon_mirror; ?> Container#:</td>
						<td class="td_data">
							<input type="text" name="container_no" id="container_no" class="txt_medium" value="<?= $container_no; ?>" /></td>
						<td class="td_head"><?= $icon_mirror; ?> <?= $icon_sync_advise ?> Vessel name: </td>
						<td class="td_data"><input type="text" id="vesselname" name="vesselname" onkeyup="clearred(this.id);"
													class="txt_medium" value="<?= $selectedVesselname; ?>" required />
											<font color="red" id="valid_vesselname"></font></td>
						</tr>
					<tr>
						<td class="td_head"><?= $icon_mirror; ?>  Vendor Bank Acc.  </td>
						<td class="td_data"><select name="BID" id="BID" class="select_medium select_chosen" >
											<option value='0'>-- Select Bank Account --</option>
											<?php 
												$stmt_bank = $model_tblcrm_bank->readAll();
												while($row_bank = $stmt_bank->fetch(PDO::FETCH_ASSOC)){
													$BID = $row_bank["BID"];
													$bank_account_no  = $row_bank["bank_account_no"];
													$beneficiary_name = $row_bank["beneficiary_name"];
													$bank_name      = $row_bank["bank_name"];
													$country        = $row_bank["country"];
													$countryCode    = $row_bank["countryCode"];
													
													$selected = ($selectedBID==$BID? "selected":"");
													
													echo "<option value='$BID' $selected>$beneficiary_name ($bank_account_no) - $bank_name $countryCode</option>";
													
												}
											?>
											</select></td>
						<td class="td_head"><?= $icon_mirror; ?> CONVERSION RATE (1 USD to KHR)</td>
						<td><input type="number" name="conversion_rate" id="conversion_rate" class="txt_medium" 
									value="<?= $conversion_rate ?>" /></td>
						<td class="td_head"  valign="top"><?= $icon_mirror; ?> Remarks:</td>
						<td ><input type="text" name="remarks" id="remarks" class="txt_medium" value="<?= $remarks; ?>" /></td>
						</tr>
					<tr>
						<td class="td_head"><?= $icon_mirror; ?> CDC#</td>
						<td><input type="text" name="cdc_no" id="cdc_no" class="txt_medium" value="<?= $cdc_no; ?>" /></td>
						<td class="td_head"><?= $icon_mirror; ?> CDC Date:</td>
						<td><input type="text" id="cdc_date" name="cdc_date" onkeyup="clearred(this.id);"
													class="txt_medium datepicker" value="<?= $cdc_date; ?>" required /></td>
						<td class="td_head">Total Qty:</td>
						<td><input type="text" name="totalqty" id="totalqty" class="txt_medium" value="<?= $totalqty ?>" readonly />
						/ <u><b><font class="text-primary"><?= $totalqty_pcs; ?></font></b></u> (PCS)
						</td>
						
						</tr>
					<tr>
						<?php if($isBuyerPayment==1){ ?>
						<td class="td_head">Buyer Invoice No.:</td>
						<td><input type="text" id="byr_invoice_no" name="byr_invoice_no" class="txt_medium" value="<?= $byr_invoice_no; ?>"  /></td>
						<?php }
						else{?>
						<td ></td>
						<td></td>
						<?php } ?>
						<td class="td_head"> Ship From (POR)</td>
						<td class="td_data">
							<select name="porID" id="porID" class="select_medium select_chosen" 
							onchange="clearred(this.id);">
							<?php 
							echo "<option value='0'>--</option>";
							$stmt = $conn->prepare("SELECT ID, Description FROM tblloadingport 
										WHERE IO='OUT' AND (StatusID=1) or ID='$selectedporID'");
							$stmt->execute();
							while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
								extract($row);
								
								$selected = ($selectedporID==$ID? "selected": "");
								echo "<option value='$ID' $selected >$Description</option>";
							}
							
							?>
							
							</select>
						</td>
						<td class="td_head">Total Invoice Amount:</td>
						<td class="td_data">
							<input type="text" name="totalamount" id="totalamount" class="txt_medium" value="<?= $selectedTotalAmount ?>" readonly /></td>
						</tr>
					
					<!--<tr>
						<td colspan="6"><hr style="border-top: 2px dashed #bdbdbd;"/></td>
						</tr>
					<tr>
						<td class="td_head"  valign="top"><?= $icon_mirror; ?> CUSTOM DECLARATION NO.</td>
						<td ><input type="text" name="join_inspection_no" id="join_inspection_no" class="txt_medium" 
									value="<?= $join_inspection_no; ?>" /></td>
						<td class="td_head"><?= $icon_mirror; ?> CUSTOM DECLARATION DATE</td>
						<td ><input type="text" id="join_inspection_date" name="join_inspection_date" class="txt_medium datepicker" 
									onchange="clearred(this.id);" value="<?= $join_inspection_date; ?>" /></td>
						<td class="td_head"><?= $icon_mirror; ?> CUSTOM PROCEDURE</td>
						<td ><input type="text" name="custom_procedure" id="custom_procedure" class="txt_medium" 
									value="<?= $custom_procedure; ?>" /></td>
						</tr>
					<tr>
						<td class="td_head">C/O NO.</td>
						<td><input type="text" name="co_number" id="co_number" class="txt_medium" 
									value="<?= $co_number; ?>" /></td>
						<td class="td_head">C/O DATE</td>
						<td><input type="text" id="co_date" name="co_date" class="txt_medium datepicker" 
									onchange="clearred(this.id);" value="<?= $co_date; ?>" /></td>
						
						</tr>-->
					</tbody>
				</table>
				</br>
				
			</fieldset>
			</section>
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#detailTable">PO</a></li>
				<?php 
					$btn_ci_qty = '';
					if($isBuyerPayment==1){
						echo '<li><a data-toggle="tab" href="#chargeTable">Other Charge</a></li>';
						$btn_ci_qty = '<a style="cursor:pointer" onclick="funcGetCIQty()" 
												title="Get the CI qty" data-toggle="tooltip">[CI]</a>';
					}
					$btn_pickpack = '';
					// if($acctid==1){
					if($isBuyerPayment==0){
						echo '<li><a data-toggle="tab" href="#chargeTable">Other Charge</a></li>';
						$btn_pickpack = '<a style="cursor:pointer" onclick="funcGetLatestQty(&#39;&#39;, &#39;true&#39;)" 
												title="Get the Pick & Pack qty" data-toggle="tooltip">[Pick&Pack]</a>';
					}
				?>
			</ul>
			
			<!----------------------------------------------------------------------------->
			<!---------------------------- INVOICE DETAILS -------------------------------->
			<!----------------------------------------------------------------------------->
			<div class="tab-content">
				<section id="detailTable" class="tab-pane fade in active">
					<fieldset>
						<table class="tb_info tb_inv" border="1" style="margin: 10px 0">
							<thead>
								<tr class="titlebar9">
									<th><button id="btn_add_inv" type="button" class="btn btn-xs btn-primary" data-toggle='tooltip'
												onclick="funcAddCategory()" title="Add Category" ><i class="glyphicon glyphicon-plus"></i></button></th>
									<th>Buyer PO</th>
									<th>Order No.</th>
									<th style="width: 10%;">Quota Category <?= $icon_sync_po ?></th>
									<th>HTS Code <?= $icon_sync_po ?></th>
									<th>Item Description <a style="cursor:pointer" title="Get the latest Item Description" data-toggle="tooltip" onclick="funcGetLatestItemDescription()"  >[O]</a><?= $icon_sync_advise ?></th>
									<th>Color Name / Style <?= $icon_sync_advise ?></th>
									<th style="width: 8%">
								<?php $notice_qty = ($isBuyerPayment==0? "from buyer/production pick list":"from shipment packing list"); ?>
											<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" 
													title="<?= $notice_qty; ?>"></span> Qty 
											<a style="cursor:pointer" onclick="funcGetLatestQty()" 
												title="Get the latest qty <?= $notice_qty ?>" data-toggle="tooltip" >[O]</a>
											<?= $btn_ci_qty; ?>
											<?= $btn_pickpack; ?>
											<?= $icon_sync_advise ?></th>
								<?php $notice_up = ($isBuyerPayment==0? "From Buyer PO price x 75%":"From Buyer PO price"); ?>
									<th><span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" 
													title="<?= $notice_up; ?>"></span> PO Price (US$)
										<a style="cursor:pointer" onclick="funcGetLatestPrice()" 
												title="Get the latest PO Price" data-toggle="tooltip" >[O]</a>
										</th>
									<th>Total Amount (US$)</th>

								</tr>
							</thead>

							<tbody class="tbody_inv_dt">
								<?php 
									

								
									$totalamt = 0;
									$options  = 0;
									$n = -1;
									if(isset($_GET["id"])){
										$ID = $_GET["id"];
										
										$sql = "SELECT invd.*, invc.options, invc.invoice_no as cat_invoice_no, invc.co_number, invc.co_date, invc.BICID as cat_BICID, invd.valid,
														invc.custom_no, invc.custom_date, invc.custom_procedure,
														sp.orderno,qc.Description quota, qc.ID quotaID, sp.BuyerPO, sp.GTN_buyerpo, od.FactoryID, sp.GTN_styleno
												FROM $tblinvoicecat invc
												LEFT JOIN $tblinvoicedetail invd ON invd.BICID = invc.BICID 
																				AND invd.del=0 AND invd.group_number>0
												LEFT JOIN tblshipmentprice sp ON sp.ID = invd.shipmentpriceID
												LEFT JOIN tblorder od ON od.Orderno = sp.Orderno
												LEFT JOIN tblquotacat qc ON sp.QuotaID=qc.ID
												WHERE invc.invID='$ID'  AND invc.del=0
												GROUP BY invd.shipmentpriceID, invc.BICID 
												ORDER BY invc.options, invd.ID asc  ";//sp.Orderno ASC
										
										$detailsql = $conn->prepare($sql);
										$detailsql->execute();
										
										
										// if($acctid==1){
											// echo "<pre>$sql</pre>";
											// break;
										// }
										
										$check_prod=0;
										if($detailsql->rowCount()>0){
											?>
											<script type="text/javascript">
												// $("#status8").prop("disabled",true);
												$("#allowconfirmstatus").val("1");
												$("#status_notice").html("");
											</script>
											<?php
										}

										$issue_from="";
										$orderno="";
										$arr_BICID = array();
										
										while($detailrow = $detailsql->fetch(PDO::FETCH_ASSOC)){
											$n++;

											$orderno   = $detailrow["orderno"];
											$BuyerPO   = $detailrow["BuyerPO"];
											$GTN_styleno = $detailrow["GTN_styleno"];
											$GTN_buyerpo = $detailrow["GTN_buyerpo"];
											$BuyerPO     = ($GTN_buyerpo==""? $BuyerPO: $GTN_buyerpo);
											$FactoryID = $detailrow["FactoryID"];
											$spID      = $detailrow["shipmentpriceID"];
											$ht_code   = $detailrow["ht_code"];
											$valid     = $detailrow["valid"];
											
											$BICID            = $detailrow["cat_BICID"];
											$cat_invoice_no   = $detailrow["cat_invoice_no"];
											$options          = $detailrow["options"];
											$co_number        = $detailrow["co_number"];
											$co_date          = $detailrow["co_date"];
											$custom_no        = $detailrow["custom_no"];
											$custom_date      = $detailrow["custom_date"];
											$custom_procedure = $detailrow["custom_procedure"];
											
											$shipping_remark = $detailrow["shipping_marking"];
											$quota           = $detailrow['quota'];
											//echo "$orderno / $BICID <== <br/>";
											//$quotaID         = $detailrow['quotaID'];
											//$issue_from      = $detailrow['issue_from'];
											
											// echo "$BICID << <br/>";
											
											if (!in_array("$BICID", $arr_BICID)){
												// $sqlBICID = "SELECT options, invoice_no as cat_invoice_no, co_number, co_date, 
																	// custom_no, custom_date, custom_procedure  
															// FROM tblbuyer_invoice_category WHERE BICID='$BICID'";
												// $stmt_BICID = $conn->prepare($sqlBICID);
												// $stmt_BICID->execute();
												// $rowBICID = $stmt_BICID->fetch(PDO::FETCH_ASSOC);
													// extract($rowBICID);
												
												$handle_lc->BICID                = $BICID;
												$handle_lc->cat_invoice_no       = $cat_invoice_no;
												$handle_lc->cat_options          = $options;
												$handle_lc->cat_co_number        = $co_number;
												$handle_lc->cat_co_date          = $co_date;
												$handle_lc->cat_custom_no        = $custom_no;
												$handle_lc->cat_custom_date      = $custom_date;
												$handle_lc->cat_custom_procedure = $custom_procedure;
												echo $html = $handle_lc->funcBuyerInvoiceCategory();
												
												$arr_BICID[] = $BICID;
											}
											
											$arr_list_ci = array();
											if($isBuyerPayment==0){// Commercial Invoice
												$arr_list = $handle_lc->getBuyerInvoicePackingListData($spID, "true", $ID);
											}
											else{ // Buyer Payment Invoice
												$arr_list = array();//$handle_lc->getBuyerInvoicePackingListData($spID, "fullqtypickpack", $ID);
												
												$filter_query = (in_array("$opt", $arr_alphabet)? "": " AND bic.options='$options' AND bic.invID='$ID'");
												$sqlCIBICID = "SELECT bic.BICID, bic.invID 
														FROM tblbuyer_invoice_category bic 
														INNER JOIN tblbuyer_invoice_detail bid ON bid.invID = bic.invID AND bid.BICID = bic.BICID
														WHERE bid.del=0 AND bic.del=0 AND bid.shipmentpriceID='$spID' 
														$filter_query";
												$stmt_cibicid = $conn->prepare($sqlCIBICID);
												$stmt_cibicid->execute();
												$row_cibicid = $stmt_cibicid->fetch(PDO::FETCH_ASSOC);
													$ci_BICID = $row_cibicid["BICID"];
													$ci_ID    = $row_cibicid["invID"];
												
												$handle_lc->isBuyerPayment = 0;
												$handle_lc->BICID = $ci_BICID;
												$arr_list_ci = array();//$handle_lc->getBuyerInvoicePackingListData($spID, "", $ci_ID);
												$handle_lc->isBuyerPayment = 1;
												$handle_lc->BICID = $BICID;
											}

										
											
											$sqlInner = "SELECT 
												invd.*, 
												(select sum(scsq.qty * scsq.price)/sum(scsq.qty) 
													from tblship_colorsizeqty scsq  
         											where scsq.shipmentpriceID = invd.shipmentpriceID and scsq.colorID=sgc.colorID 
													and scsq.statusID=1 
       												GROUP BY scsq.shipmentpriceID) as Buyer_price,

       												-- (select sum(scp.qty) from tblship_colorsizeqty_prod scp where scp.shipmentpriceID = invd.shipmentpriceID and scp.statusID=1 and scp.colorID=sgc.colorID) as prod_qty,

												-- (select sum(scsq.qty) from tblship_colorsizeqty scsq where scsq.shipmentpriceID = invd.shipmentpriceID and scsq.statusID=1 and scsq.colorID=sgc.colorID GROUP BY scsq.shipmentpriceID) as Buyer_qty,

												-- (select sum(saq.shippedQty) from tblshippingadviseqty saq where saq.tblshipmentpriceID = invd.shipmentpriceID and saq.colorID=sgc.colorID) as Shipped_qty,

		
												group_concat(c.colorName,' / <i>', g.styleNo,'</i>' separator '<br/>') as ColorName, 
												count(sgc.group_number) as count_grp, invd.qty as color_qty, invd.fob_price, invd.quotaID, invd.group_number
												
												FROM $tblinvoicedetail invd 
												LEFT JOIN tblship_group_color sgc ON sgc.shipmentpriceID = invd.shipmentpriceID 
																					AND sgc.group_number = invd.group_number
																					AND sgc.statusID = 1
												LEFT JOIN tblcolor c ON c.ID = sgc.colorID
												LEFT JOIN tblgarment g ON g.garmentID = sgc.garmentID
												WHERE invd.invID='$ID' AND invd.shipmentpriceID='$spID' AND invd.BICID='$BICID' AND invd.del=0 AND invd.group_number>0
												group by invd.ID
												";
											// if($spID=='33517'){
												// echo "<pre>$sqlInner</pre>";
											// }
											$detailsql2 = $conn->prepare($sqlInner);
											$detailsql2->execute(); 
											$countColor = $detailsql2->rowCount();
											$c = 0;
											
											while($detailrow2 = $detailsql2->fetch(PDO::FETCH_ASSOC)){
												$color_qty = $detailrow2["color_qty"];
												$count_grp = $detailrow2["count_grp"];
												$unitprice = $detailrow2["fob_price"]; 

												//$unitprice = $detailrow2["Buyer_price"]; // modified by SL 2022 Mar 15
												
												$LCIID     = "";//$detailrow2["LCIID"];
												$quotaID   = $detailrow2["quotaID"];
												$shipping_remark   = htmlspecialchars_decode($detailrow2["shipping_marking"]);
												$shipping_remark   = strtolower($shipping_remark);
												$shipping_remark   = html_entity_decode($shipping_remark);
												$shipping_remark   = strtoupper($shipping_remark);
												// $shipping_remark   = str_replace("&#039;","", $shipping_remark);
												
												
												$class_description = htmlspecialchars_decode($detailrow2["class_description"]);
												$remarks         = $detailrow2["remarks"];
												$selection = "";
												
												// echo "$shipping_remark << <br/>";
											
												$group_number = $detailrow2["group_number"];
												$total_amount = $color_qty * $unitprice;//$$detailrow2["total_amount"];
												$colorID      = 0;
												// $ColorName    = $detailrow2["ColorName"];
												$invd_ID      = $detailrow2["ID"];
												$gmt_unit     = ($count_grp>1? "SETS": "PCS");
												
												$arr_color = $handle_lc->getPOPrice($spID, $group_number);
												$ColorName = $arr_color["color"];
												
												// echo "$ColorName << <br/>";
												
												//$advise_qty = 0;
												//--- Get Latest Shipping Advise Qty ---//
												// foreach($arr_all_size as $key => $qty){
													// list($this_group_number, $size) = explode("**^^",$key);
													// if($this_group_number==$group_number){
														// $this_qty = $qty;
														// $advise_qty += $this_qty;
													// }
												// }
												
												$advise_qty = 0;
												//--- Get Latest Shipping Advise Qty ---//
												for($arr=0;$arr<count($arr_list);$arr++){
													$mixID     = $arr_list[$arr]["mixID"];
													$total_ctn = $arr_list[$arr]["total_ctn"];
													
													$arr_row = explode("::^^", $mixID);
													
													
													
													for($ar=0;$ar<count($arr_row);$ar++){
														list($this_group_number, $size_name, $qty) = explode("**%%", $arr_row[$ar]);
														//echo "| grp:$this_group_number / $size_name / $qty <<< ";
														
														if($this_group_number==$group_number){
															$advise_qty += ($qty * $total_ctn);
														}
													}
													//echo "<br/>";
													
												}//--- End For ---//
												
												//--- 2024-02-22 to fix slow loading for old method get advise qty from C-TPAT--//
												if($isBuyerPayment==1){
													$sqlcptd = "SELECT ifnull(sum(qty),0) as qty FROM (SELECT cptd.qty, sgc.group_number
																FROM `tblcarton_picklist_transit_detail` cptd 
																INNER JOIN tblcarton_picklist_transit cpt ON cpt.PTID = cptd.PTID 
																								AND cpt.isLocalhost = cptd.isLocalhost 
																								AND cpt.factoryID = cptd.factoryID 
																								AND cpt.shipmentpriceID = cptd.shipmentpriceID
																INNER JOIN tblship_group_color sgc ON sgc.shipmentpriceID = cptd.shipmentpriceID 
																									AND sgc.garmentID = cptd.garmentID 
																									AND sgc.colorID = cptd.colorID
																WHERE cptd.shipmentpriceID = '$spID' AND sgc.statusID=1 
																AND sgc.group_number = '$group_number' AND cpt.ct_pat='1'
																group by cptd.PTID, cptd.isLocalhost, cptd.factoryID, sgc.group_number) as tbl";
													// echo "<pre>$sqlcptd</pre>";
													$stmt_cptd = $conn->prepare($sqlcptd);
													$stmt_cptd->execute(); 
													$row_cptd = $stmt_cptd->fetch(PDO::FETCH_ASSOC);
													
													$advise_qty = $row_cptd["qty"];
												}
												
												$ci_qty = 0;
												if(count($arr_list_ci)>0){
													//--- Get Latest Commercial Invoice Qty ---//
													for($arr=0;$arr<count($arr_list_ci);$arr++){
														$mixID     = $arr_list_ci[$arr]["mixID"];
														$total_ctn = $arr_list_ci[$arr]["total_ctn"];
														
														$arr_row = explode("::^^", $mixID);
														for($ar=0;$ar<count($arr_row);$ar++){
															list($this_group_number, $size_name, $qty) = explode("**%%", $arr_row[$ar]);
															//echo "| grp:$this_group_number / $size_name / $qty <<< ";
															
															if($this_group_number==$group_number){
																$ci_qty += ($qty * $total_ctn);
															}
														}
													}
												}//--- End if count ci > 0 ---//
												
												// echo "$group_number - $advise_qty <br/>";
												$handle_lc->ci_qty = $ci_qty;
												$handle_lc->valid  = $valid;
												$handle_lc->class_description  = $class_description;
												$handle_lc->setBuyerInvoiceDetail($BuyerPO, $spID, $orderno, $quotaID, $ht_code, 
																						$shipping_remark, $ColorName, $colorID, $invd_ID, 
																						$color_qty, $unitprice, $total_amount, $check_prod, $group_number, $gmt_unit, $advise_qty, $LCIID, $BICID, $options, $remarks, $GTN_styleno);
												echo $html = $handle_lc->funcBuyerInvoiceDetail($n, $c, $countColor);

												$c++;
												$totalamt+=$total_amount;
											}//--- End While ---//
											
											echo $html = $handle_lc->funcBuyerInvoiceExpand($n);
											
										}//--- End While ---//

										if($check_prod==1){
											?>
											<script type="text/javascript">
												// $("#status8").prop("disabled",true);
												$("#allowconfirmstatus").val("0");
												$("#status_notice").html("*This invoice cannot confirm, some PO still havent ship");
											</script>
											<?php
										}
										
									}//--- End if isset invID exist ---//
									else{
										$handle_lc->BICID          = 0;
										$handle_lc->cat_invoice_no = "";
										$handle_lc->cat_options    = $options;
										echo $html = $handle_lc->funcBuyerInvoiceCategory();
										
									}


								?>
							</tbody>
						</table>
						<input type="hidden" name="countPO" id="countPO" value="<?php echo (isset($ID)) ? $n : '-1'; ?>" />
						<input type="hidden" name="total_amount" id="total_amount" value="<?= $totalamt; ?>">
						<input type="hidden" name="max_options" id="max_options" value="<?= $options; ?>">
						<input type="hidden" name="invopt" id="invopt" value="<?= $isInvOption; ?>">
					</fieldset>
				</section>
				<section id="chargeTable" class="tab-pane fade in">
					<fieldset>
						<table class="tb_info" border="1" style="margin: 10px 0">
							<thead>
								<tr class="titlebar9">
									<th style='width: 5%;'>
										<button id="btn_add_charge" type="button" class="btn btn-xs btn-primary" onclick="addChargeRow();"
												<?php echo $disabled_action; ?> >
												<i class="glyphicon glyphicon-plus"></i></button>
									</th>
									<th style='width: 45%;'>Description</th>
									<th>Deduction % <span style="cursor: pointer;" onclick="copyPercentage();"><b>[v]</b></span></th>
									<th>Addition % <span style="cursor: pointer;" onclick="copyPercentage();"><b>[v]</b></span></th>
									<th>Charge Amt</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="chargeTbody">
								<?php
								$sql = "SELECT sp.BuyerPO, sp.Orderno, invd.shipmentpriceID,SUM(invd.total_amount) total_amount, 
												invd.chargeID_deduct, invd.chargeID_credit
										FROM $tblinvoicedetail invd 
										JOIN tblshipmentprice sp ON invd.shipmentpriceID=sp.ID
										WHERE invd.invID='$ID' AND other_charge='' AND invd.del=0
										GROUP BY invd.shipmentpriceID ORDER BY invd.ID";
								$sel_detail = $conn->prepare($sql);
								$sel_detail->execute(); 
								$count_charge=0;
								while($row_detail=$sel_detail->fetch(PDO::FETCH_ASSOC)){
									$buyerpo         = $row_detail['BuyerPO'];
									$orderno         = $row_detail['Orderno'];
									$shipmentpriceID = $row_detail['shipmentpriceID'];
									$po_amt          = $row_detail['total_amount'];
									$INVDID          = "";
									$percentage      = "0";
									$charge_amt      = "0";

									//check if charge exised
									$query = "SELECT ID as INVDID, charge_percentage, charge_percentage_credit, total_amount,
																		chargeID_deduct, chargeID_credit
																FROM $tblinvoicedetail 
																WHERE invID='$ID' AND shipmentpriceID='$shipmentpriceID' AND group_number=0 AND del=0";
									// echo "<pre>$query</pre>";
									$sel_charge=$conn->prepare($query);
									$sel_charge->execute();

									if($sel_charge->rowCount()>0){
										$row_charge=$sel_charge->fetch(PDO::FETCH_ASSOC);

										$INVDID     = $row_charge['INVDID'];
										$percentage = $row_charge['charge_percentage'];
										$charge_amt = $row_charge['total_amount'];
										$percentage_credit = $row_charge['charge_percentage_credit'];
										$chargeID_deduct   = $row_charge['chargeID_deduct'];
										$chargeID_credit   = $row_charge['chargeID_credit'];

									}
									
									//---- SELECT BOX OF PURPOSE OF DEDUCTION ----//
									$html_deduct = "<select id='chargeID_deduct$count_charge' name='chargeID_deduct$count_charge' 
															style='padding:3px;width:150px' 
															onchange='funcChangeCharge(this.value, $count_charge, &#39;purpose_deduct&#39;)'>";
									$html_deduct .= "<option value='0'>-- Select Purpose --</option>";
									$html_deduct .= "<option value='-1'>New Purpose [+]</option>";
									$handle_tblbuyer_invoice_charge_option->ID   = $chargeID_deduct;
									$handle_tblbuyer_invoice_charge_option->type = 1;
									$arr_deduct = $handle_tblbuyer_invoice_charge_option->getChargeOptionList();
									for($dd=0;$dd<count($arr_deduct);$dd++){
										$selected = ($arr_deduct[$dd]["ID"]==$chargeID_deduct? "selected": "");
										$html_deduct .= "<option value='".$arr_deduct[$dd]["ID"]."' $selected >".$arr_deduct[$dd]["Description"]."</option>";
									}
									$html_deduct .= "</select>";
									
									//---- SELECT BOX OF PURPOSE OF CREDIT ----//
									$html_credit = "<select id='chargeID_credit$count_charge' name='chargeID_credit$count_charge'
															style='padding:3px;width:150px'
															onchange='funcChangeCharge(this.value, $count_charge, &#39;purpose_credit&#39;)'>";
									$html_credit .= "<option value='0'>-- Select Purpose --</option>";
									$html_credit .= "<option value='-1'>New Purpose [+]</option>";
									$handle_tblbuyer_invoice_charge_option->ID   = $chargeID_credit;
									$handle_tblbuyer_invoice_charge_option->type = 2;
									$arr_credit = $handle_tblbuyer_invoice_charge_option->getChargeOptionList();
									for($dd=0;$dd<count($arr_credit);$dd++){
										$selected = ($arr_credit[$dd]["ID"]==$chargeID_credit? "selected": "");
										$html_credit .= "<option value='".$arr_credit[$dd]["ID"]."' $selected >".$arr_credit[$dd]["Description"]."</option>";
									}
									$html_credit .= "</select>";
									
									?>
									<tr id="chargerow<?= $count_charge ?>">
										<td>
											<input type="hidden" name="INVDID<?= $count_charge ?>" value="<?= $INVDID ?>" />
											<input type="hidden" name="shipmentpriceID<?= $count_charge ?>" id="shipmentpriceID<?= $count_charge ?>" 
													value="<?= $shipmentpriceID ?>" />
											<input type="hidden" name="buyerpo<?= $count_charge ?>" value="<?= $buyerpo ?>">

											<input type="hidden" id="po_amt<?= $count_charge ?>" name="po_amt<?= $count_charge ?>" value="<?= $po_amt ?>">

											<input type="hidden" id="count_spID<?= $shipmentpriceID ?>" name="count_spID<?= $shipmentpriceID ?>" value="<?= $count_charge ?>">
										</td>
										<td>
											<input type="text" style="width: 100%;" name="description<?= $count_charge ?>" class="txt_medium" value="<?= $buyerpo.' ('.$orderno.')'; ?>" readonly>
										</td>
										<td><?php echo $html_deduct; ?>
												<input type="number" step="any" name="percentage<?= $count_charge ?>" min="0" 
													id="percentage<?= $count_charge ?>" class=" percentage_box" style="padding:2px"
													onkeyup="calChargeAmt(this.value,'<?= $count_charge ?>');calTotalAmount();" 
													value="<?= $percentage ?>" <?= $disabled_action; ?> /><br/>
												<input type="text" name="purpose_deduct<?= $count_charge ?>" id="purpose_deduct<?= $count_charge ?>"
														style="width:150px;display:none" placeholder="Purpose of deduction..." /></td>
										<td><?php echo $html_credit; ?>
												<input type="number" step="any" name="percentage_credit<?= $count_charge ?>" min="0"
													id="percentage_credit<?= $count_charge ?>" class=" percentage_box" style="padding:2px"
													onkeyup="calChargeAmt(this.value,'<?= $count_charge ?>');calTotalAmount();" 
													value="<?= $percentage_credit ?>" <?= $disabled_action; ?> /><br/>
												<input type="text" name="purpose_credit<?= $count_charge ?>" id="purpose_credit<?= $count_charge ?>"
														style="width:150px;display:none" placeholder="Purpose of credit..." />
											</td>
										<td><input type="number" step="any" name="charge_amt<?= $count_charge ?>" 
													id="charge_amt<?= $count_charge ?>" class="txt_medium amt_box" 
													value="<?= $charge_amt ?>" readonly /></td>
										<td>
											
										</td>
									</tr>
									<?php
									$count_charge++;
								}
								
								$sql = "SELECT ID as INVDID, charge_percentage, total_amount, other_charge, charge_percentage_credit,
												chargeID_deduct, chargeID_credit
														FROM $tblinvoicedetail 
														WHERE invID='$ID' AND shipmentpriceID='0'  AND del=0";//AND other_charge!=''
								$sel_extracharge=$conn->prepare($sql);
								$sel_extracharge->execute();
								while($row_extracharge=$sel_extracharge->fetch(PDO::FETCH_ASSOC)){
									$buyerpo="";
									$orderno="";
									$shipmentpriceID="";
									
									$INVDID      = $row_extracharge['INVDID'];
									$percentage  = $row_extracharge['charge_percentage'];
									$percentage_credit = $row_extracharge['charge_percentage_credit'];
									$charge_amt  = $row_extracharge['total_amount'];
									$description = $row_extracharge['other_charge'];
									$chargeID_deduct = $row_extracharge['chargeID_deduct'];
									$chargeID_credit = $row_extracharge['chargeID_credit'];
									
									//echo "$INVDID <== <br/>";
									
									//check if charge exised
									$sel_charge=$conn->prepare("SELECT SUM(total_amount) po_amt FROM $tblinvoicedetail WHERE invID='$ID' AND other_charge=''");
									$sel_charge->execute();
									$row_charge=$sel_charge->fetch(PDO::FETCH_ASSOC);
									$po_amt=$row_charge['po_amt'];
									
									$readonly = ""; $disabled_sel = "";
									$checked  = "";
									if($percentage==0 && $INVDID!==""){
										$readonly = "readonly";
										$checked  = "checked";
										$disabled_sel = "disabled";
									}
									
									$readonly_charge = "";
									if($percentage>0 || $INVDID==""){
										$readonly_charge = "readonly";
									}
									
									//---- SELECT BOX OF PURPOSE OF DEDUCTION ----//
									$html_deduct = "<select id='chargeID_deduct$count_charge' name='chargeID_deduct$count_charge' 
															style='padding:3px;width:150px' $disabled_sel
															onchange='funcChangeCharge(this.value, $count_charge, &#39;purpose_deduct&#39;)'>";
									$html_deduct .= "<option value='0'>-- Select Purpose --</option>";
									$html_deduct .= "<option value='-1'>New Purpose [+]</option>";
									$handle_tblbuyer_invoice_charge_option->ID   = $chargeID_deduct;
									$handle_tblbuyer_invoice_charge_option->type = 1;
									$arr_deduct = $handle_tblbuyer_invoice_charge_option->getChargeOptionList();
									for($dd=0;$dd<count($arr_deduct);$dd++){
										$selected = ($arr_deduct[$dd]["ID"]==$chargeID_deduct? "selected": "");
										$html_deduct .= "<option value='".$arr_deduct[$dd]["ID"]."' $selected >".$arr_deduct[$dd]["Description"]."</option>";
									}
									$html_deduct .= "</select>";
									
									//---- SELECT BOX OF PURPOSE OF CREDIT ----//
									$html_credit = "<select id='chargeID_credit$count_charge' name='chargeID_credit$count_charge'
															style='padding:3px;width:150px' $disabled_sel
															onchange='funcChangeCharge(this.value, $count_charge, &#39;purpose_credit&#39;)'>";
									$html_credit .= "<option value='0'>-- Select Purpose --</option>";
									$html_credit .= "<option value='-1'>New Purpose [+]</option>";
									$handle_tblbuyer_invoice_charge_option->ID   = $chargeID_credit;
									$handle_tblbuyer_invoice_charge_option->type = 2;
									$arr_credit = $handle_tblbuyer_invoice_charge_option->getChargeOptionList();
									for($dd=0;$dd<count($arr_credit);$dd++){
										$selected = ($arr_credit[$dd]["ID"]==$chargeID_credit? "selected": "");
										$html_credit .= "<option value='".$arr_credit[$dd]["ID"]."' $selected >".$arr_credit[$dd]["Description"]."</option>";
									}
									$html_credit .= "</select>";

									?>
									<tr id="chargerow<?= $count_charge ?>">
										<td>
											<input type="hidden" name="INVDID<?= $count_charge ?>" id="INVDID<?= $count_charge ?>" value="<?= $INVDID ?>">
											<input type="hidden" name="shipmentpriceID<?= $count_charge ?>" value="<?= $shipmentpriceID ?>">
											<input type="hidden" name="buyerpo<?= $count_charge ?>" value="<?= $buyerpo ?>">

											<input type="hidden" id="po_amt<?= $count_charge ?>" name="po_amt<?= $count_charge ?>" value="<?= $po_amt ?>">
											<button class="btn btn-danger btn-xs" onclick="removeChargeRow('<?= $count_charge ?>');"><span class="glyphicon glyphicon-trash"></span></button>
										</td>
										<td>
											<input type="text" style="width: 100%;" name="description<?= $count_charge ?>" class="txt_medium" value="<?= $description; ?>">
										</td>
										<td><?php echo $html_deduct; ?> <input type="number" step="any" name="percentage<?= $count_charge ?>" 
													id="percentage<?= $count_charge ?>" class=" percentage_box" 
													onkeyup="calChargeAmt(this.value,'<?= $count_charge ?>');calTotalAmount();" 
													value="<?= $percentage ?>" <?= $readonly; ?> /></td>
										<td><?php echo $html_credit; ?> <input type="number" step="any" name="percentage_credit<?= $count_charge ?>" 
													id="percentage_credit<?= $count_charge ?>" class=" percentage_box" 
													onkeyup="calChargeAmt(this.value,'<?= $count_charge ?>');calTotalAmount();" 
													value="<?= $percentage_credit ?>" <?= $readonly; ?> /></td>
										<td><input type="number" step="any" name="charge_amt<?= $count_charge ?>" id="charge_amt<?= $count_charge ?>" class="txt_medium amt_box" value="<?= $charge_amt ?>" onkeyup="calTotalAmount()"
											 <?= $readonly_charge; ?> ></td>
										<td>
											<input type="checkbox" onclick="enableChargeBox('<?= $count_charge ?>');" name="ableamtbox<?= $count_charge ?>" id="ableamtbox<?= $count_charge ?>"
											<?= $checked; ?> >
										</td>
									</tr>
									<?php
									$count_charge++;
								}
								?>
							</tbody>
						</table>
						<input type="hidden" name="countCharge" id="countCharge" value="<?= $count_charge ?>" />
					</fieldset>
				</section>
			</div>
			
			</form>
		</div><!-- End inner -->
	</div>


	

	<!-- modal to show buyerpo -->
	<div id="buyermodal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg" style="width: 80%">
			<div class="modal-content">
				<div class="modal-header" style="border-bottom: none !important;">
					<button type="button" class="close" onclick="func_close_modal()">&times;</button>
				</div>
				<div class="modal-body">
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" onclick="func_select_po()">SELECT</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- modal to show packing list detail -->
	<div id="buyermodal_packing" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg" style="width:50%">
			<div class="modal-content">
				<div class="modal-header" style="border-bottom: none !important;">
					<font id="modal_title" style="font-size:13px;font-weight:bold"></font>
					<button type="button" class="close" onclick="func_close_modal_packing()">&times;</button>
				</div>
				<div class="modal-body">
					
				</div>
				<div class="modal-footer" id="modal_footer">
					
				</div>
			</div>
		</div>
	</div>

</body>
</html>

<?php 
//check different price compare "Buyer PO Price"<>"Invoice PO Price"
$sql = " SELECT bipd.shipmentpriceID, sp.BuyerPO, sp.Orderno, bipd.fob_price, scsq.price
FROM `tblbuyer_invoice_payment_detail` bipd 
INNER JOIN tblbuyer_invoice_payment bip ON bip.ID = bipd.invID
INNER JOIN tblshipmentprice sp ON sp.ID = bipd.shipmentpriceID
INNER JOIN tblship_group_color sgc ON sgc.shipmentpriceID = bipd.shipmentpriceID
									AND sgc.group_number = bipd.group_number
                                    AND sgc.statusID = 1
INNER JOIN tblship_colorsizeqty scsq ON scsq.shipmentpriceID = bipd.shipmentpriceID
									 AND scsq.colorID = sgc.colorID
                                     AND scsq.garmentID = sgc.garmentID
WHERE bip.statusID IN (8,11) AND bipd.del=0 AND bipd.group_number>0 AND scsq.price>0 AND scsq.statusID=1 AND sgc.statusID=1 AND scsq.price<>bipd.fob_price
group by bipd.invID";

?>