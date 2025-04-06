<?php 
	include("../../lock.php");
	include_once("../../function/userpermission.php");
	include_once("../../function/misc.php");
	include("lc_class.php");
	$screenID = 76;
	
	if($acctid==1){
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
	}

	$script_date = "202010201347";

	// $orderno=$_GET["orderno"];
	$sql = $conn->query("SELECT COUNT(ID) FROM tblbuyer_invoice_payment ");
	$countInvoice = $sql->fetchColumn();

	$input_disabled = "";
	$input_readonly = "";
	
	$permission  = new userpermission();
	$handle_misc = new misc($conn);
	$arr         = array(1,2,3,4,5,6,7,8,9);//$permission->arrPermission($acctid,$screenID,$conn);
	
	$handle_lc = new lcClass();
	$handle_lc->setConnection($conn);
	$handle_lc->setPermission($arr);
	
	$module_name = "BUYER PAYMENT INVOICE LIST";
	$tblbuyer_inv_detail   = "tblbuyer_invoice_payment_detail";
	$tblbuyer_inv          = "tblbuyer_invoice_payment";
	$tblbuyer_inv_category = "tblbuyer_invoice_payment_category";
	$cache_name="BuyerInvPaymentList";
	$isBuyerPayment = 1;
	// if(!isset($_GET["isBuyerPayment"])){
		// $module_name = "COMMERCIAL INVOICE LIST";
		// $tblbuyer_inv_detail   = "tblbuyer_invoice_detail";
		// $tblbuyer_inv          = "tblbuyer_invoice";
		// $tblbuyer_inv_category = "tblbuyer_invoice_category";
		// $isBuyerPayment = 0;
		// $cache_name="BuyerInvList";
	// }
	$getLink = "&&isBuyerPayment=true";//(isset($_GET["isBuyerPayment"])? "&&isBuyerPayment=true":"");
	
	// echo "$cache_name <<< ";
?>
<!DOCTYPE html>
<html>
<head>
	<title><?= $module_name; ?></title>

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
	<script type="text/javascript" language="javascript" src="../../media/js/misc.js?date=<?php echo $script_date; ?>"></script>
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
			border: 2px solid #4A89DC; 
			margin: 0; 
			padding: 5px 12px; 
			cursor: pointer; 
			border-top-left-radius: 5px; 
			border-top-right-radius: 5px; 
			color: #4A89DC; 
			font-weight:bold;
			background-color: white;
		}

		h4.acc_header.active {
			background-color: #4A89DC;
			color: white;
		}

		section#add_form {
			padding: 0 8px;
			border: 2px solid #4A89DC;
			border-radius: 5px;
			background-color: #fff;
			transition: max-height 0.2s ease-out;
			
		}

		div#content {
			padding-bottom: 20px;
		}

		a.btn {
			text-decoration: none;
			color: white;
		}

		button.btn-pdf:hover {
			color: #fff;
			background-color: #d2322d;
			border-color: #ac2925;
		}

		table.datatable td,
		table.datatable th {
			border: 0px solid #bdbdbd;
		}


		.datepicker, 
		.datetimepicker {
			background: #e6e6e6 url(../images/icon-calendar.png) no-repeat scroll 200px 3px !important;
			color: black;
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
		.tb_inv td {
			border-color: black !important;
		}
		tfoot {
			display: table-header-group;
		}

	</style>

	<script type="text/javascript">
		$(document).ready(function(){
			if($(".datatable").length>0){
				console.log("datatable << ");
				var table = $(".datatable").DataTable({
								"ordering":false
							});
				
				$('#tb_list tfoot th').each( function () {
					if(parseInt($(this).index())>0){
						// if(parseInt($(this).index())==1 || parseInt($(this).index())==2 || parseInt($(this).index())==6 || parseInt($(this).index())==8){
							var css_width = "style='width:100px;'";
						// }
						var title = $('#tb_list tfoot th:not(.noFilter)').eq($(this).index()).text(); //alert(title);
						$(this).html( '<input type="text" placeholder="Search" '+css_width+' />' );
					}
				});

				$(".dropdown-toggle").dropdown();
				
				// Apply the search
				table.columns().every( function () {
					var that = this;
					$( 'input', this.footer() ).on( 'keyup change', function () {
						that
							.search( this.value )
							.draw();
							
					} );
				});
			}

			
		});
		
		function funcExcel(){
			var cache_name = $("#cache_name").val();
			window.location="buyer_inv_list_excel.php?cache_name="+cache_name;
		}

	</script>
	
	<style>
	#tb_list .tr_row:hover td{
    	background-color: #96ffcc;
	    cursor: pointer;
    }
	#tb_list thead tr.titlebar th{
		white-space:nowrap;
	}
	</style>
</head>
<body>
<?php include("../../includes/code_loading.php"); ?>
	
	<div id="content">
		<div id="inner">
			<h5 style="margin: 0 !important; color: black">Export >>> <?= $module_name ?> &nbsp; &nbsp;
				<!--<a class="btn btn-danger btn-sm" href="../../SOM/main.php?screen=20" >Back</a>-->
				<button type="button" class="btn btn-default btn-sm" onclick="location.reload()">
				<span class="glyphicon glyphicon-refresh"></span> Refresh</button>
				<?php 
				if($isBuyerPayment==0){
					echo '<a class="btn btn-success btn-sm" type="button" href="buyer_inv.php">
							<span class="glyphicon glyphicon-plus"></span> New Commercial Invoice</a>';
				}
					
					?>
				</h5>
		
	<?php 
		// 1: Buyer, 2: Orderno, 3: StyleNo, 4: PO, 5: Status, 6: Date, 7: Brand, 8: Resp By, 9: Season, 10: Shipment PO, 11: Supplier, 12: POHID, 13:Gender
		$type = "IA"; //---- whether for ne order ----//
		//$cache_name="BuyerInvList"; //---- module name ----//
		$arr_search = array("1","2","5","6","10","24"); //---- contains search item[s] ----//
		$query_statusID = "AND StatusID IN (4,6,8,11)";
		$action_link = "buyer_inv_list.php?screen=76$getLink"; //---- searching place ----//
		// $query_statusID = "AND StatusID IN (4,8,6,11,26)";
		
		$opt_date = "<option value='bi.invoice_date' >Invoice Date</option>";
		
		//echo '<div id="content"><div id="inner">';
		// include($_SERVER['DOCUMENT_ROOT']."/includes/advance_search.php"); 	
		// include("../../includes/advance_search.php"); 	
		// $where = $adv_search_where;
		
		$adv_search_where = " AND bi.invoice_date>='2025-01-01' ";
		$_SESSION[$cache_name."_filter"] = $adv_search_where;
		
		// echo $where;
		//echo '</div></div>';
	
	if($countInvoice > 0 && (isset($_POST["btn_advanceSearch"]) ||  trim($_SESSION[$cache_name."_filter"])!="")){
	?>
			<section id="primarysection">
			
			<?php 
				if($isBuyerPayment==1){
					echo '<p align="right"><button type="button" class="btn btn-success btn-sm" onclick="funcExcel()">Excel</button></p>';
				}
			?>
			<input type="hidden" name="cache_name" id="cache_name" value="<?= $cache_name ?>" />
			<table class="table table-striped tb_info datatable" id="tb_list">
				<thead>
					<tr class="titlebar">
						<th style="width:100px;"></th>
						<th>INVOICE NO</th>
						<th>INVOICE DATE</th>
						<th>BUYER</th>
						<th>CONSIGNEE</th>
						<th>BUYER PO INFO</th>
						<th>SHIP MODE</th>
						<th>DEPART FROM</th>
						<th>SHIPPED DATE</th>
						<th>Total Qty</th>
						<th>Total Amount</th>
						<th>Created By</th>
					</tr>
				</thead>
				<tfoot>
					<tr style="background-color:#fff">
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>

					<?php 
						// $sql = "SELECT bi.ID as invID, bi.invoice_no as invoice_no, bi.invoice_date, b.BuyerName_Eng as buyerName, con.Name as consigneeName, sm.Description as shipmode, lp.Description as loadingPort, bi.shippeddate, tt.Description as tradeterm, pt.Description as paymentTerm , arie.AREID, arie.document_no, ai.batchID, arie.entryID, s.StatusName inv_status, s2.StatusName bip_status, bi.statusID, bi.BuyerID, bi.inv_opt, 
						// (SELECT SUM(qty)  FROM $tblbuyer_inv_detail bid2
                                      		// INNER JOIN tblship_group_color sgc 
                                      								// ON sgc.shipmentpriceID = bid2.shipmentpriceID
                                      								// AND sgc.group_number = bid2.group_number
                                      								// AND sgc.statusID=1
											// WHERE bid2.invID=bi.ID AND bid2.del=0 AND bid2.group_number>0) as totalqty_pcs,
						// (SELECT SUM(qty) FROM $tblbuyer_inv_detail bid2  
											// WHERE bid2.invID=bid.ID AND del=0 AND group_number>0) as totalqty,
						// (SELECT SUM(total_amount) FROM $tblbuyer_inv_detail bid2 
											// WHERE bid2.invID=bi.ID AND del=0) as totalamount,  concat(uat.UserFullName,' (',bi.createddate,')') as created_by 
						
														// FROM tblbuyer_invoice bi
														// INNER JOIN tbluseraccount uat ON uat.AcctID = bi.createdby
														// LEFT JOIN tblbuyer_invoice_payment bip ON bip.invoice_no = bi.invoice_no
														// LEFT JOIN tblbuyer b ON bi.BuyerID=b.BuyerID 
														// LEFT JOIN tblconsignee con ON con.ConsigneeID=bi.ConsigneeID 
														// LEFT JOIN tblshipmode sm ON sm.ID=bi.shipmodeID 
														// LEFT JOIN tblloadingport lp ON lp.ID=bi.portLoadingID 
														// LEFT JOIN tbltradeterm tt ON tt.ID=bi.tradeTermID 
														// LEFT JOIN tblpaymentterm pt ON pt.ID=bi.paymentTermID 
														// LEFT JOIN tblfin_ar_invoice_entry arie ON bi.ID=arie.document_no AND arie.statusID!=6
														// LEFT JOIN tblfin_ar_invoice ai ON ai.ARIID = arie.ARIID
														// LEFT JOIN tblstatus s ON bi.statusID=s.StatusID
														// LEFT JOIN tblstatus s2 ON bip.statusID=s2.StatusID
														// LEFT JOIN $tblbuyer_inv_detail bid ON bid.invID = bi.ID AND bid.del=0
														// LEFT JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
														// LEFT JOIN tblorder o ON o.Orderno = sp.Orderno
													// WHERE 1=1 $adv_search_where
													// group by bi.ID ";
						$sql = "";
						// if($isBuyerPayment==1){
							$sql = " 
									SELECT bi.ID as invID, bi.invoice_no as invoice_no, bi.invoice_date, b.BuyerName_Eng as buyerName, con.Name as consigneeName, sm.Description as shipmode, lp.Description as loadingPort, bi.shippeddate, tt.Description as tradeterm, pt.Description as paymentTerm , '' as AREID, '' as document_no, '' as batchID, '' as entryID, s.StatusName inv_status, s2.StatusName bip_status, bi.statusID, bi.BuyerID, bi.inv_opt,
									(SELECT SUM(qty)  FROM tblbuyer_invoice_payment_detail bid2
                                      		INNER JOIN tblship_group_color sgc 
                                      								ON sgc.shipmentpriceID = bid2.shipmentpriceID
                                      								AND sgc.group_number = bid2.group_number
                                      								AND sgc.statusID=1
											WHERE bid2.invID=bi.ID AND bid2.del=0 AND bid2.group_number>0) as totalqty_pcs,
								(SELECT SUM(qty) FROM tblbuyer_invoice_payment_detail bid2  
													WHERE bid2.invID=bid.ID AND del=0 AND group_number>0) as totalqty,
								(SELECT SUM(total_amount) FROM tblbuyer_invoice_payment_detail bid2 
													WHERE bid2.invID=bi.ID AND del=0) as totalamount,  concat(uat.UserFullName,' (',bi.createddate,')') as created_by 
									
														FROM tblbuyer_invoice_payment bi
														LEFT JOIN tbluseraccount uat ON uat.AcctID = bi.createdby
														LEFT JOIN tblbuyer b ON bi.BuyerID=b.BuyerID 
														LEFT JOIN tblconsignee con ON con.ConsigneeID=bi.ConsigneeID 
														LEFT JOIN tblshipmode sm ON sm.ID=bi.shipmodeID 
														LEFT JOIN tblloadingport lp ON lp.ID=bi.portLoadingID 
														LEFT JOIN tbltradeterm tt ON tt.ID=bi.tradeTermID 
														LEFT JOIN tblpaymentterm pt ON pt.ID=bi.paymentTermID  
														LEFT JOIN tblstatus s ON bi.statusID=s.StatusID
														LEFT JOIN tblstatus s2 ON bi.statusID=s2.StatusID
														LEFT JOIN tblbuyer_invoice_payment_detail bid ON bid.invID = bi.ID AND bid.del=0
														LEFT JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
														LEFT JOIN tblorder o ON o.Orderno = sp.Orderno
													WHERE 1=1 AND bi.inv_opt=0 $adv_search_where
													group by bi.ID ";
						// }					
						$sql .= " ORDER BY invoice_no DESC"; //
						
						// echo "<pre>$sql</pre>";
						$invoicesql = $conn->prepare($sql);
						$invoicesql->execute();
						
						if($acctid==1){
							// echo "<pre>$sql</pre>";
						}
						$_SESSION[$cache_name."_sql"] = $sql;
						
						// while(false){
						while($invoicerow = $invoicesql->fetch(PDO::FETCH_ASSOC)){
							$invID          = $invoicerow["invID"];
							$BuyerID        = $invoicerow["BuyerID"];
							$batchID        = $invoicerow["batchID"];
							$entryID        = $invoicerow["entryID"];
							$document_no    = $invoicerow["document_no"];
							$invoice_no     = $invoicerow["invoice_no"];
							$invoice_date   = $invoicerow["invoice_date"];
							$buyerName      = $invoicerow["buyerName"];
							$consigneeName  = $invoicerow["consigneeName"];
							$shipmode       = $invoicerow["shipmode"];
							$loadingPort    = $invoicerow["loadingPort"];
							$shippeddate    = $invoicerow["shippeddate"];
							$tradeterm      = $invoicerow["tradeterm"];
							$paymentTerm    = $invoicerow["paymentTerm"];
							$invoice_status = $invoicerow['inv_status'];
							$inv_payment_status = $invoicerow['bip_status'];
							$statusID       = $invoicerow['statusID'];
							$inv_opt        = $invoicerow['inv_opt'];
							$totalqty_pcs   = $invoicerow['totalqty_pcs'];
							$totalamount    = $invoicerow['totalamount'];
							$created_by     = $invoicerow['created_by'];
							
							$invoice_status = ($isBuyerPayment==0? $invoice_status: $inv_payment_status);

							$ar_label="";
							$label_color = ($statusID==11? "label-success":"label-warning");
							$label_color = ($statusID==6? "label-danger":"$label_color");
							$status_label="<br/><span class='label label-xs $label_color'>".$invoice_status."</span>";
							if($invoicerow['AREID']!==null){
								$ar_label="<span class='label label-xs label-primary' title='Document No: $document_no (B:$batchID E:$entryID)'>AR</span>";
							}

							$url = "buyer_inv.php?id=$invID$getLink";
							$td_onclick  = "onclick='funcLoadIcon();window.location=\"$url\"' ";
							$html_dropdown_print = $handle_lc->funcGetDropDownPrint($invID, $BuyerID, "btn-xs", $isBuyerPayment);
							
							//---- Get Commercial/Buyer Invoice detail Info ----// 
							$sqlSP = "SELECT sp.Orderno, sp.BuyerPO, sum(bid.qty) as qty, bi.invoice_date 
												FROM $tblbuyer_inv bi
												LEFT JOIN $tblbuyer_inv_category bic ON bic.invID = bi.ID
												LEFT JOIN $tblbuyer_inv_detail bid ON bid.BICID = bic.BICID
												LEFT JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
												WHERE bic.invID='$invID' AND (bid.del=0 or bid.del is NULL) AND bic.del=0
												-- AND bid.shipmentpriceID is not NULL
												group by sp.ID
												order by sp.Orderno";
							$stmt_sp = $conn->prepare($sqlSP);
							$stmt_sp->execute();
							$count_sp = $stmt_sp->rowCount();
							$row_sp   = $stmt_sp->fetch(PDO::FETCH_ASSOC);
								$invoice_date = $row_sp["invoice_date"];
							
							$style_bg_result = ($isBuyerPayment==1 && $count_sp==0 && $inv_opt==0? "style='background-color:#F2D1CB'":""); 
							$label_result    = ($isBuyerPayment==1 && $count_sp==0 && $inv_opt==0? "Waiting for Buyer Payment Invoice... ":""); 
							$label_invdate   = ($isBuyerPayment==1 && $count_sp==0 && $inv_opt==0? "":"$invoice_date"); 
							$label_shipmode  = ($isBuyerPayment==1 && $count_sp==0 && $inv_opt==0? "":"$shipmode"); 
							$label_loadport  = ($isBuyerPayment==1 && $count_sp==0 && $inv_opt==0? "":"$loadingPort"); 
							$label_shipdate  = ($isBuyerPayment==1 && $count_sp==0 && $inv_opt==0? "":"$shippeddate"); 
							$html_dropdown_print = ($isBuyerPayment==1 && $count_sp==0 && $inv_opt==0? "": $html_dropdown_print);
							
							
							echo "<tr class='tr_row table-danger' style='background-color:#fff'>";//
							echo "<td align='center' $style_bg_result>
										<!--<a href='$url' class='btn btn-info btn-xs'>
												<span class='glyphicon glyphicon-pencil'></span>&nbsp;
												EDIT</a>-->
												$html_dropdown_print
										</td>";
							echo "<td $td_onclick $style_bg_result >$invoice_no $ar_label $status_label </td>";
							echo "<td $td_onclick $style_bg_result >$label_invdate</td>";
							echo "<td $td_onclick $style_bg_result >$buyerName</td>";
							echo "<td $td_onclick $style_bg_result>$consigneeName</td>";
							echo "<td $td_onclick $style_bg_result >$label_result";
								
								$html_info = "";
								$html_info .= "<table>";
								
								$html_lbl = "";
									
								$count = 0;
								$stmt_sp->execute();
								while($row_sp = $stmt_sp->fetch(PDO::FETCH_ASSOC)){
									extract($row_sp);
									$count++;
									$BuyerPO = $handle_misc->funcDecodeSpecialChar($BuyerPO);
									$css_td = ($count==$count_sp? "border-right:1px solid #bdbdbd;white-space:nowrap":
																	"border-right:1px solid #bdbdbd;border-bottom:1px solid #bdbdbd;white-space:nowrap");
									$css_td_2 = ($count==$count_sp? "white-space:nowrap": 
																	"border-bottom:1px solid #bdbdbd;white-space:nowrap");
									
									$html_info .= "<tr>";
									$html_info .= "<td style='$css_td'><font color='#009ec3'><b>$Orderno</b></font> - <font color='red'>$BuyerPO</font></td>";
									$html_info .= "<td style='$css_td_2'><b>Qty:</b> $qty</td>";
									$html_info .= "</tr>";
									
									$html_lbl .= "<label style='font-size:13px;border:1px solid #bdbdbd;padding:3px;border-radius:5px'>
													<font color='#009ec3'><b>$Orderno</b></font> - 
													<font color=''>$BuyerPO</font> <font color='#7e7e7e'><b>Qty:</b> $qty</font> &nbsp; </label> ";
								}
								$html_info .= "</table>";
								
								echo $html_lbl;
							echo "</td>";
							echo "<td $td_onclick $style_bg_result >$label_shipmode</td>";
							echo "<td $td_onclick $style_bg_result >$label_loadport</td>";
							echo "<td $td_onclick $style_bg_result >$label_shipdate</td>";
							// echo "<td onclick='window.location=\"$url\"'>$tradeterm</td>";
							// echo "<td onclick='window.location=\"$url\"'>$paymentTerm</td>";
							echo "<td>$totalqty_pcs PCS</td>";
							echo "<td>$totalamount</td>";
							echo "<td>$created_by</td>";
							echo "</tr>";
						}
					?>
					
				
			</table>

			</section>

		</div>
	</div>

	<?php 
	}
	?>

	

</body>
</html>