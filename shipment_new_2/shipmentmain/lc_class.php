<?php 

class lcClass{
	private $conn = "";
	private $lcdate = ""; private $expired = "";private $details = "";
	private $arr_permission = array();
	private $handle_shipment = "";
	private $model_carton_inv_head = "";
	private $model_carton_inv_payment_head = "";
	private $disabled_action = "";
	public $letterhead_name = "";
	public $letterhead_address = "";
	public $letterhead_tel = "";
	public $letterhead_fax = "";
	public $letterhead_title = "";
	public $isBuyerPayment = "0";
	public $acctid = "0";
	
	public function setConnection($conn){ 
		$this->conn = $conn;
	}
	public function setPermission($arr_permission){ 
		$this->arr_permission = $arr_permission;
	}
	public function setHandleShipment($handle_shipment){ 
		$this->handle_shipment = $handle_shipment;
	}
	public function setModelCIH($model_carton_inv_head){ 
		$this->model_carton_inv_head = $model_carton_inv_head;
	}
	public function setModelCIPH($model_carton_inv_payment_head){ 
		$this->model_carton_inv_payment_head = $model_carton_inv_payment_head;
	}
	public function setDisabledAction($disabled_action){ 
		$this->disabled_action = $disabled_action;
	}

	public function funcMaxID($tbl_name, $columnID){ // supplier_submit.php 
		$sql = "SELECT max($columnID) as maxID FROM $tbl_name";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$maxID = $row["maxID"] + 1;
		
		return $maxID;
	}
	
	public function funcInsert_tbllc_assignment_head($LCHID,$lc_number,$lc_bank,$buyerID,$statusID,$createdBy,$createdDate){

		// $maxID = $this->funcMaxID("tbllc_assignment_head","LCHID");

		$stmt=$this->conn->prepare("INSERT INTO tbllc_assignment_head
									(LCHID,lc_number,lc_bank,buyerID,statusID,createdBy,createdDate) 
									VALUES 
									(:LCHID,:lc_number,:lc_bank,:buyerID,:statusID,:createdBy,:createdDate)");
		$stmt->bindParam(':LCHID', $LCHID);
		$stmt->bindParam(':lc_number', $lc_number);
		$stmt->bindParam(':lc_bank', $lc_bank);
		$stmt->bindParam(':buyerID', $buyerID);
		$stmt->bindParam(':statusID', $statusID);
		$stmt->bindParam(':createdBy', $createdBy);
		$stmt->bindParam(':createdDate', $createdDate);
		$stmt->execute();

		return true;

	}

	public function funcInsert_tbllc_assignment_info($LCIID,$LCHID,$lc_date,$expired_date,$draft_no,$del){
		$stmt=$this->conn->prepare("INSERT INTO tbllc_assignment_info(LCIID,LCHID,lc_date,expired_date,draft_no,del) 
										VALUES (:LCIID,:LCHID,:lc_date,:expired_date,:draft_no,:del)");
		$stmt->bindParam(':LCIID', $LCIID);
		$stmt->bindParam(':LCHID', $LCHID);
		$stmt->bindParam(':lc_date', $lc_date);
		$stmt->bindParam(':expired_date', $expired_date);
		$stmt->bindParam(':draft_no', $draft_no);
		$stmt->bindParam(':del', $del);
		$stmt->execute();

		return true;
	}

	public function funcInsert_tbllc_assignment_detail($LCDID, $LCIID, $shipmentpriceID, $PID, $garmentID, $colorID, $size_name, $del, $group_number){
		$stmt=$this->conn->prepare("INSERT INTO tbllc_assignment_detail
									(LCDID,LCIID,shipmentpriceID,PID,garmentID,colorID,size_name,del,group_number) 
									VALUES 
									(:LCDID,:LCIID,:shipmentpriceID,:PID,:garmentID,:colorID,:size_name,:del,:group_number)");
		$stmt->bindParam(':LCDID', $LCDID);
		$stmt->bindParam(':LCIID', $LCIID);
		$stmt->bindParam(':shipmentpriceID', $shipmentpriceID);
		$stmt->bindParam(':PID', $PID);
		$stmt->bindParam(':garmentID', $garmentID);
		$stmt->bindParam(':colorID', $colorID);
		$stmt->bindParam(':size_name', $size_name);
		$stmt->bindParam(':group_number', $group_number);
		$stmt->bindParam(':del', $del);
		$stmt->execute();

		return true;
	}

	public function funcUpdate_tbllc_assignment_head($LCHID,$lc_number,$lc_bank,$buyerID,$statusID,$updatedBy,$updatedDate){

		$stmt=$this->conn->prepare("UPDATE tbllc_assignment_head SET 
				lc_number=:lc_number,
				lc_bank=:lc_bank,
				buyerID=:buyerID,
				statusID=:statusID,
				updatedBy=:updatedBy,
				updatedDate=:updatedDate
				WHERE LCHID=:LCHID
			");
		$stmt->bindParam(':LCHID', $LCHID);
		$stmt->bindParam(':lc_number', $lc_number);
		$stmt->bindParam(':lc_bank', $lc_bank);
		$stmt->bindParam(':buyerID', $buyerID);
		$stmt->bindParam(':statusID', $statusID);
		$stmt->bindParam(':updatedBy', $updatedBy);
		$stmt->bindParam(':updatedDate', $updatedDate);
		$stmt->execute();

		return true;

	}

	public function funcUpdate_tbllc_assignment_info($LCIID,$LCHID,$lc_date,$expired_date,$draft_no,$del){
		$stmt=$this->conn->prepare("UPDATE tbllc_assignment_info SET
				LCHID=:LCHID,
				lc_date=:lc_date,
				expired_date=:expired_date,
				draft_no=:draft_no,
				del=:del
				WHERE LCIID=:LCIID
			");
		$stmt->bindParam(':LCIID', $LCIID);
		$stmt->bindParam(':LCHID', $LCHID);
		$stmt->bindParam(':lc_date', $lc_date);
		$stmt->bindParam(':expired_date', $expired_date);
		$stmt->bindParam(':draft_no', $draft_no);
		$stmt->bindParam(':del', $del);
		$stmt->execute();

		return true;
	}

	public function funcUpdate_tbllc_assignment_detail($LCDID,$LCIID,$PID,$garmentID,$colorID,$size_name,$del){
		$stmt=$this->conn->prepare("UPDATE tbllc_assignment_info SET
				LCIID=:LCIID,
				PID=:PID,
				garmentID=:garmentID,
				colorID=:colorID,
				size_name=:size_name,
				del=:del
				WHERE LCDID=:LCDID
			");
		$stmt->bindParam(':LCIID', $LCIID);
		$stmt->bindParam(':PID', $PID);
		$stmt->bindParam(':garmentID', $garmentID);
		$stmt->bindParam(':colorID', $colorID);
		$stmt->bindParam(':size_name', $size_name);
		$stmt->bindParam(':del', $del);
		$stmt->bindParam(':LCDID', $LCDID);
		$stmt->execute();

		return true;
	}
	
	public function funcInsert_tblbuyer_invoice_detail($invID, $shipmentpriceID, $LCIID, $ht_code, $shipping_remark, $group_number, $unitprice,
														$color_qty, $total_amt){
		$sql = "INSERT INTO tblbuyer_invoice_detail
				(invID, shipmentpriceID, LCIID, ht_code, shipping_marking, group_number, fob_price, qty, total_amount) 
				VALUES
				(:invID, :shipmentpriceID, :LCIID, :ht_code, :shipping_remark, :group_number, :fob_price, :qty, :total_amount) ";
		$detailsql = $this->conn->prepare($sql);
		$detail_data = array("invID" => $invID, 
							"shipmentpriceID" => $shipmentpriceID, 
							"LCIID" => $LCIID, 
							"ht_code" => $ht_code, 
							"shipping_remark" => $shipping_remark,
							"group_number" => $group_number, 
							"fob_price" => $unitprice, 
							"qty" => $color_qty, 
							"total_amount" => $total_amt );
		$detailsql->execute($detail_data);
		
		return true;
	}
	
	public function funcCheckBuyerInvoiceOpened($LCIID, $filter_query=""){
		$sql = "SELECT lcd.shipmentpriceID, bid.shipmentpriceID 
				FROM `tbllc_assignment_info` lci 
				LEFT JOIN tbllc_assignment_detail lcd ON lcd.LCIID = lci.LCIID AND lcd.del=0
				LEFT JOIN tblbuyer_invoice_detail bid ON bid.shipmentpriceID = lcd.shipmentpriceID AND bid.del=0 
				WHERE lci.LCIID='$LCIID' AND bid.shipmentpriceID is not NULL $filter_query";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		$count_bi = $stmt->rowCount();
		
		
		return $count_bi;
	}
	
	public function funcUpdateNewPOToBuyerInvoiceDetailFromLCDraft($LCIID, $shipmentpriceID){
		
		$sqlInv = "SELECT invID FROM tblbuyer_invoice_detail bid WHERE bid.LCIID='$LCIID' and bid.del=0 limit 1";
		$stmt_inv = $this->conn->prepare($sqlInv);
		$stmt_inv->execute();
		$count_inv = $stmt_inv->rowCount();
		
		if($count_inv>0){
			$row_inv = $stmt_inv->fetch(PDO::FETCH_ASSOC);
			extract($row_inv);
			
			$sql = "SELECT sgc.group_number, 
							count(sgc.group_number) as count_grp, 
							(SELECT scsq.price FROM tblship_colorsizeqty scsq 
							WHERE scsq.shipmentpriceID=sgc.shipmentpriceID 
							AND scsq.garmentID = sgc.garmentID AND scsq.colorID = sgc.colorID
							AND scsq.statusID=1 AND scsq.price>0 AND scsq.qty>0 limit 1) as unitprice
					FROM tblship_group_color sgc 
					INNER JOIN tblcolor c ON c.ID = sgc.colorID
					INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
					WHERE sgc.shipmentpriceID='$shipmentpriceID' AND sgc.statusID=1
					group by sgc.group_number";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute();
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				extract($row);
				$color_qty = $this->funcGetPOShippedQty($group_number, $shipmentpriceID, $count_grp);
				$total_amount = $color_qty * $unitprice;
				
				$ht_code = NULL;
				$shipping_remark = NULL;
				
				//echo "$invID $shipmentpriceID $LCIID $group_number $unitprice $total_amount";
				$this->funcInsert_tblbuyer_invoice_detail($invID, $shipmentpriceID, $LCIID, $ht_code, $shipping_remark, $group_number, $unitprice,
														$color_qty, $total_amount);
			}
			
		}//--- End If Count Inv ---//
	}
	
	public function funcGetPOShippedQty($group_number, $shipmentpriceID, $count_grp){
		$color_qty = 0;
		
		$sqladvise = "SELECT sum(shippedQty) as color_qty
							FROM tblship_group_color sgc 
							INNER JOIN tblshippingadviseqty saq ON saq.tblshipmentpriceID = sgc.shipmentpriceID 
																AND saq.colorID = sgc.colorID
																AND saq.garmentID = sgc.garmentID
							WHERE sgc.group_number = '$group_number' AND sgc.shipmentpriceID='$shipmentpriceID' AND sgc.statusID=1";
		$stmt_saq = $this->conn->prepare($sqladvise);
		$stmt_saq->execute();
		$count_saq = $stmt_saq->rowCount();
			
		if($count_saq>0){
			$row_saq = $stmt_saq->fetch(PDO::FETCH_ASSOC);
			$color_qty = $row_saq["color_qty"] / $count_grp;
		}
		
		return $color_qty;
	}

	public function resetValueDate(){
		$this->lcdate="";
		$this->expired="";
		$this->details="";
		$this->draft_no="";
	}

	public function setValueDate($lc_date, $expired_date, $details, $draft_no){
		$this->lcdate   = $lc_date;
		$this->expired  = $expired_date;
		$this->details  = $details;
		$this->draft_no = $draft_no;
	}

	public function funcLoadInfo($id,$LCIID=''){

		// $lcdate="";
		// $expireddate="";

		// if($LCIID!==""){
		// 	$sel_date=$this->conn->prepare("SELECT * FROM tbllc_assignment_info WHERE LCIID='$LCIID'");
		// 	$sel_date->execute();
		// 	$row_date=$sel_date->fetch(PDO::FETCH_ASSOC);

		// 	$lcdate=$row_date['lc_date'];
		// 	$expired=$row_date['expired_date'];
		// }

		$html="";

		$html.='<div id="infohead_'.$id.'" class="panel-heading">
					<table align="center" width="100%" border="0">
						<tr>
							<td class="" style="min-width:120px;width:120px;">
								<button type="button" class="btn btn-default btn-xs" onclick="funcToggleContent(\''.$id.'\');">
								<span id="icon_'.$id.'" class="glyphicon glyphicon-chevron-down"></span></button>

								<button type="button" class="btn btn-primary btn-xs" onclick="funcAddDetail(\''.$LCIID.'\',\''.$id.'\');"> <!-- js/lc_script.js -> lc_ajax_custom.php -> funcAddDetail() -->
								<span id="icon_'.$id.'" class="glyphicon glyphicon-plus"></span> Add</button>
								<small>'.$LCIID.'</small>';
		$count_byr_inv = $this->funcCheckBuyerInvoiceOpened($LCIID);
		if(in_array(4,$this->arr_permission) && $count_byr_inv==0){
			$html .= ' <button type="button" class="btn btn-danger btn-xs" onclick="funcRemoveInfo(\''.$id.'\',\''.$LCIID.'\');">
									<span class="glyphicon glyphicon-trash"></span></button>';
		}
		if($count_byr_inv>0){
			$html .= ' &nbsp; <span class="label label-xs label-info">Buyer Invoice</span>';
		}

		$html .= '<input type="hidden"  name="LCIID'.$id.'" id="LCIID'.$id.'" value="'.$LCIID.'" />
								<input type="hidden" name="selectedDetail'.$id.'" id="selectedDetail'.$id.'" value="'.$this->details.'"/>
							</td> 
							<!--<td class="label_wd" align="right"><b>Draft no. </b>&nbsp;</td>
							<td class="label_txt_wd"><input type="text" name="draft_no'.$id.'" id="draft_no'.$id.'" 
															value="'.$this->draft_no.'" /></td>-->
							<td class="label_wd" align="right"><b>LC Date: </b>&nbsp; <button >+</button></td>
							<td class="label_txt_wd"><input type="text" name="lcdate'.$id.'" id="lcdate'.$id.'" class="datepicker" 
												value="'.$this->lcdate.'" readonly=""></td>
							<td class="label_wd" align="right"><b>Expired Date: </b>&nbsp;</td>
							<td class="label_txt_wd"><input type="text" name="expireddate'.$id.'" id="expireddate'.$id.'" class="datepicker" 
												value="'.$this->expired.'" readonly=""></td>
							<td class="label_wd" align="right"><b>LC Price: </b>&nbsp;</td>
							<td id="lcprice'.$id.'" class="label_txt_wd">0</td>
							<td class="label_wd" align="right"><b>Total PO: </b>&nbsp;</td>
							<td id="totalpo'.$id.'" class="">0</td>
						</tr>
					</table>
				</div>
				<div id="collapse_'.$id.'" class="panel-body panel-collapse collapse">
					<table id="detailTable'.$id.'" class="table table-bordered">
						<thead>
							<tr>
								<th></th>
								<th>At Sight (PO)</th>
								<!--<th>Code (NG Item/SKU)</th>
								<th>Garment / Color</th>
								<th>Size</th>
								<!--th>Packing Method</th-->
								<th>PO Qty</th>
								<th><span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" title="Link from Buyer PO" style="cursor:pointer"></span> PO Price</th>
								<th>LC Amt</th>
								<th>Buyer PO Ship Date</th>
								<th><span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" title="Link from Buyer Invoice" style="cursor:pointer"></span> Invoice No.</th>
								<th><span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" title="Link from Buyer Invoice" style="cursor:pointer"></span> Invoice Ship Date</th>
								<th><span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" title="Link from Buyer Invoice" style="cursor:pointer"></span> Ship Qty</th>
								<th>Over/Short</th>
								<th>Draw Amt</th>
							</tr>
						</thead>
						<tbody id="detailTbody'.$id.'">';
							if($this->details!==""){
								$sel_buyer=$this->conn->prepare("SELECT lch.buyerID 
																FROM tbllc_assignment_info lci 
																INNER JOIN tbllc_assignment_head lch ON lci.LCHID=lch.LCHID 
																WHERE lci.LCIID='$LCIID'");
								$sel_buyer->execute();
								$buyer=$sel_buyer->fetchColumn();

								$html.= $this->funcLoadDetail($id,$LCIID,$this->details,$buyer);
							}
				$html.='</tbody>
				</table>
			</div>';

		return $html;
	}

	public function funcAddDetail($buyer,$LCIID,$count,$selected){

		$arr_selected=explode(",", $selected);

		$html="";

		$html.='<div id="addDetailModal" class="modal fade" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Details</h4>
							</div>
							<div class="modal-body">
								<table id="seldetailTable" class="table table-bordered">
									<thead>
										<tr>
											<th>Orderno</th>
											<th>At Sight (PO)</th>
											<th>Ship Date</th>
											<!--th>Code (NG Item/SKU)</th-->
											<th>PO Qty</th>
											<!--th>FOB Price</th-->
											<th>PO Amt</th>
										</tr>
									</thead>';

									$sql = "SELECT od.buyerID, SUM(csq.qty) total_qty, 
													csq.price, sp.BuyerPO, sp.ID shipmentpriceID, sp.Orderno, 
													(SELECT sgc.group_number FROM tblship_group_color sgc 
														WHERE sgc.shipmentpriceID = sp.ID AND sgc.statusID=1 limit 1) as group_number,
													cur.CurrencyCode, sp.Shipdate
										FROM tblshipmentprice sp
										INNER JOIN tblorder od ON od.Orderno = sp.Orderno 
										INNER JOIN tblship_colorsizeqty csq ON csq.shipmentpriceID = sp.ID AND csq.statusID = 1 AND csq.price>0
										INNER JOIN tblcurrency cur ON cur.ID = od.currencyID
										WHERE od.buyerID = '$buyer' AND sp.statusID = 1 and od.statusID!=6
										group by sp.ID ";
									$sel_detail=$this->conn->prepare($sql);
									$sel_detail->execute();

									while($row_detail=$sel_detail->fetch(PDO::FETCH_ASSOC)){

										$Orderno         = $row_detail['Orderno'];
										$buyer_po        = $row_detail['BuyerPO'];
										$sku             = "";
										$po_qty          = $row_detail['total_qty'];
										$fob_price       = $row_detail['price'];
										$shipmentpriceID = $row_detail['shipmentpriceID'];
										$group_number    = $row_detail['group_number'];
										$CurrencyCode    = $row_detail['CurrencyCode'];
										$Shipdate    = $row_detail['Shipdate'];
										
										$sel_check=$this->conn->prepare("SELECT * FROM tbllc_assignment_detail 
																			WHERE shipmentpriceID='$shipmentpriceID' AND del='0'");
										$sel_check->execute();

										if($sel_check->rowCount()==0){
											
											$sql_grp = "SELECT * FROM tblship_group_color sgc 
														WHERE sgc.group_number='$group_number' AND sgc.shipmentpriceID='$shipmentpriceID' 
														AND sgc.statusID=1";
											$stmt_grp = $this->conn->prepare($sql_grp);
											$stmt_grp->execute();
											$count_grp = $stmt_grp->rowCount();
											$str_unit  = ($count_grp>1? "SETS":"PCS"); 
											
											if($count_grp==0){
												$count_grp=1;
												//echo "sp:$shipmentpriceID grp:[$group_number] // $count_grp <== <br/>";
											}
											
											$po_qty = $po_qty / $count_grp;
											$lc_amt = $po_qty * $fob_price;
											
											$html.='<tr onclick="funcSelectDetail(\''.$shipmentpriceID.'\');"';

												if(in_array($shipmentpriceID, $arr_selected)){
													$html.=' class="selected"';
												}

											$html.='>
														<td>'.$Orderno.'</td>
														<td>'.$buyer_po.'</td>
														<td>'.$Shipdate.'</td>
														<!--td>'.$sku.'</td-->
														<td>'.$po_qty.' '.$str_unit.'</td>
														<!--td>'.$fob_price.'</td-->
														<td>'.$CurrencyCode.' '.$lc_amt.'</td>
													</tr>';
										}

									}

									$html.='</table>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-success" onclick="funcSubmitDetail(\''.$count.'\',\''.$LCIID.'\');">Add</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>

					</div>
				</div>';

			return $html;
								
	}

	public function funcLoadDetail($count,$LCIID,$details,$buyer,$pdf='0'){

		$html="";
		
		$details = ltrim($details, ",");
		$str_shipmentpriceID = rtrim($details, ",");
		$arr_shipmentprice = explode(",", $str_shipmentpriceID);
		
		// $sql = "SELECT spd.ID as PDID, spd.PID, spd.group_number, spd.size_name, 
												// sum(spd.total_qty) as total_qty, spd.SKU, spk.packing_method, od.buyerID, 
													// csq.garmentID, csq.colorID, csq.price, sp.BuyerPO, DATE(sp.Shipdate) as Shipdate, sp.Orderno, sp.ID as shipmentpriceID, DATE(adv.shippeddate) as shippeddate, adv.invnumber, c.colorName as color, g.styleNo, cur.CurrencyCode
									// FROM `tblship_packing_detail` spd
									// INNER JOIN tblship_packing spk ON spk.PID = spd.PID
									// INNER JOIN tblshipmentprice sp ON sp.ID = spk.shipmentpriceID
									// LEFT JOIN tblshippingadvise adv ON adv.tblshipmentpriceID = sp.ID
									// INNER JOIN tblorder od ON od.Orderno = sp.Orderno
									// INNER JOIN tblship_colorsizeqty csq ON csq.shipmentpriceID = spk.shipmentpriceID
																		// AND csq.size_name = spd.size_name 
																		// AND csq.statusID = 1 AND csq.price>0
									// LEFT JOIN tblcolor c ON c.ID = csq.colorID
									// LEFT JOIN tblgarment g ON g.garmentID = csq.garmentID
                                    // INNER JOIN tblcurrency cur ON cur.ID = od.currencyID
									// WHERE sp.ID IN ($str_shipmentpriceID) AND od.buyerID = '$buyer' AND spd.statusID = 1 AND spk.packing_method IN (1,2,50) 
											// AND spk.statusID = 1 AND sp.statusID = 1
									// group by spk.PID, spd.group_number, spd.size_name
									// ORDER BY PID, group_number, spd.ID";
		$sql = "SELECT scsq.shipmentpriceID, sp.BuyerPO, sp.Orderno, sum(scsq.qty) as total_qty, sp.Shipdate, scsq.price, 
						GROUP_CONCAT(DISTINCT g.styleNo) as styleNo, cur.CurrencyCode, 
						'' as PID, '' as group_number, '' as buyerID, '' as size_name, '' as SKU,
						'' as color, '' as colorID, '' as garmentID, '' as shippeddate, '' as invnumber, '' as packing_method,
						od.Qunit, st.description as str_unit
				FROM tblship_colorsizeqty scsq 
				INNER JOIN tblshipmentprice sp ON sp.ID = scsq.shipmentpriceID
				INNER JOIN tblgarment g ON g.garmentID = scsq.garmentID
				INNER JOIN tblorder od ON od.Orderno = sp.Orderno
				INNER JOIN tblset st ON st.ID = od.Qunit
				INNER JOIN tblcurrency cur ON cur.ID = od.currencyID
				WHERE scsq.shipmentpriceID IN ($str_shipmentpriceID) AND scsq.statusID = 1 AND scsq.qty>0
				group by scsq.shipmentpriceID 
				order by scsq.shipmentpriceID asc ";
		$sel_detail = $this->conn->prepare($sql); //
		$sel_detail->execute();

		$count_info=$count;
		$count_detail=1;

		$totalpo = count($arr_shipmentprice);
		$lcprice = 0;

		$arr_exist=[];
		$script_str='';
		while($row_detail = $sel_detail->fetch(PDO::FETCH_ASSOC)){

			$PID          = $row_detail['PID'];
			$group_number = $row_detail['group_number'];
			$buyerID      = $row_detail['buyerID'];
			$styleNo      = $row_detail['styleNo'];
			$size_name    = $row_detail['size_name'];
			$color        = $row_detail['color'];
			$colorID      = $row_detail['colorID'];
			$garmentID    = $row_detail['garmentID'];
			$orderno      = $row_detail['Orderno'];
			$sp_Shipdate  = $row_detail['Shipdate'];
			$Qunit        = $row_detail['Qunit'];
			$str_unit     = $row_detail['str_unit'];
			
			$CurrencyCode   = $row_detail['CurrencyCode'];
			$shipmentpriceID= $row_detail['shipmentpriceID'];
			$shippeddate    = $row_detail['shippeddate'];
			$invnumber      = $row_detail['invnumber'];
			$buyer_po       = $row_detail['BuyerPO'];
			$sku            = $row_detail['SKU'];
			$po_qty         = $row_detail['total_qty'];
			$fob_price      = $row_detail['price'];
			$packing_method = $row_detail['packing_method'];
			
			$sqlLCDID = "SELECT lad.LCDID 
							FROM tbllc_assignment_detail lad 
							WHERE lad.shipmentpriceID='$shipmentpriceID' AND lad.del=0";
			$sel_LCDID = $this->conn->prepare($sqlLCDID); //
			$sel_LCDID->execute();
			$count_LCDID = $sel_LCDID->rowCount();
			$row_LCDID = $sel_LCDID->fetch(PDO::FETCH_ASSOC);
				$LCDID = $row_LCDID["LCDID"];
			
			
			// $sqlChkLocation = "SELECT * FROM tblcarton_picklist_transit WHERE shipmentpriceID='$shipmentpriceID'";
			// $stmt_location = $this->conn->prepare($sqlChkLocation);
			// $stmt_location->execute();
			// $count_IK = $stmt_location->rowCount();
			
			//$filter_query = " AND cpt.shiped=1";
			//$isIAL = ($count_IK>0? "":"_ial");
			// $arr_size_qty      = $this->handle_shipment->funcGetCTPATDetail($shipmentpriceID, $PID,  $filter_query, $isIAL);
			// $arr_size_qty_last = $this->handle_shipment->funcGetCTPATDetailLastCarton($shipmentpriceID, $filter_query, $isIAL);
			
			// $sql_LCDID = "SELECT lcd.LCDID, group_concat(g.styleNo,' / ',c.colorName separator '<br/>') as po_color, 
								// group_concat(g.garmentID,'**',c.ID separator '**^^%%') as po_colorID,
								// count(sgc.colorID) as count_color,
								// (SELECT sum(ratio_qty) FROM tblship_packing_detail spd2 
									// WHERE spd2.PID=spk.PID AND spd2.group_number=spd.group_number 
									// AND spd2.statusID=1) as sum_ratio_qty
						
						// FROM tblship_packing spk 
						// INNER JOIN tblship_packing_detail spd ON spd.PID = spk.PID
						// INNER JOIN tblship_group_color sgc ON sgc.shipmentpriceID = spk.shipmentpriceID
															// AND sgc.group_number = spd.group_number
															// AND sgc.statusID = 1
						// INNER JOIN tblcolor c ON c.ID = sgc.colorID
						// INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
						// LEFT JOIN tbllc_assignment_detail lcd ON lcd.PID = spk.PID 
																// AND lcd.group_number = sgc.group_number 
																// AND lcd.size_name = spd.size_name 
																// AND lcd.shipmentpriceID = spk.shipmentpriceID
																// AND lcd.del = 0 AND lcd.LCIID='$LCIID'
						// WHERE spk.PID='$PID' AND spd.group_number='$group_number'
						// AND spd.size_name='$size_name' AND spd.statusID=1";
			// $sel_LCDID=$this->conn->prepare($sql_LCDID);
			// $sel_LCDID->execute();
			// $row_LCDID = $sel_LCDID->fetch(PDO::FETCH_ASSOC);
			// extract($row_LCDID);
			
			// $arr_po_colorID = explode("**^^%%", $po_colorID);
			//$shipqty = 0;
			// for($nn=0;$nn<count($arr_po_colorID);$nn++){
				// list($this_garmentID, $this_colorID) = explode("**", $arr_po_colorID[$nn]);
				
				// $shipqty += $arr_size_qty["$size_name**$this_garmentID**$this_colorID"];
				// $shipqty += $arr_size_qty_last["$size_name**$this_garmentID**$this_colorID"];
			// }
			
			/*switch($packing_method){
				case 1: //-- Single Color Ratio Pack --//
						$sqlSPD = "SELECT spd.total_qty, spd.ratio_qty, spd.polybag_qty_in_blisterbag 
								FROM tblship_packing_detail spd 
								WHERE spd.PID='$PID' AND spd.group_number='$group_number' 
								AND spd.size_name='$size_name' AND spd.statusID='1'";
						$stmt_spd = $this->conn->prepare($sqlSPD);
						$stmt_spd->execute();
						$row_spd = $stmt_spd->fetch(PDO::FETCH_ASSOC);
						extract($row_spd);
						
						//$po_qty = $total_qty / $polybag_qty_in_blisterbag * $ratio_qty; break;
				case 2: //-- Single Color Single Pack --//
						$sqlSPD = "SELECT spd.total_qty
								FROM tblship_packing_detail spd 
								WHERE spd.PID='$PID' AND spd.group_number='$group_number' 
								AND spd.size_name='$size_name' AND spd.statusID='1'";
						$stmt_spd = $this->conn->prepare($sqlSPD);
						$stmt_spd->execute();
						$row_spd = $stmt_spd->fetch(PDO::FETCH_ASSOC);
						extract($row_spd);
						
						//$po_qty = $total_qty; break;
				case 50: //-- Multi Color Ratio Pack --//
						$sqlSPD = "SELECT spd.total_qty, spd.ratio_qty, spd.polybag_qty_in_blisterbag 
								FROM tblship_packing_detail spd 
								WHERE spd.PID='$PID' AND spd.group_number='$group_number' 
								AND spd.size_name='$size_name' AND spd.statusID='1'";
						$stmt_spd = $this->conn->prepare($sqlSPD);
						$stmt_spd->execute();
						$row_spd = $stmt_spd->fetch(PDO::FETCH_ASSOC);
						extract($row_spd);
						
						//$po_qty = $ratio_qty * ($total_qty / $sum_ratio_qty);
						break;
			}//*/
			
			/*if($Qunit>1){// if set order, calculate po qty by sets
				$po_qty = 0; $shipqty = 0;
				$sqlgrp = "SELECT group_concat(distinct sgc.colorID) as colorID, 
									group_concat(distinct sgc.garmentID) as garmentID, count(sgc.colorID) as count_color 
							FROM tblship_group_color sgc 
							WHERE sgc.shipmentpriceID='$shipmentpriceID' AND sgc.statusID='1' 
							group by sgc.group_number";
				$stmt_grp = $this->conn->prepare($sqlgrp); //
				$stmt_grp->execute();
				while($row_grp = $stmt_grp->fetch(PDO::FETCH_ASSOC)){
					$colorID     = $row_grp["colorID"];
					$garmentID   = $row_grp["garmentID"];
					$count_color = $row_grp["count_color"];
					
					//=================================//
					//--------- Buyer PO qty ----------//
					$sqlscsq = "SELECT sum(scsq.qty) as qty 
								FROM tblship_colorsizeqty scsq 
								WHERE scsq.shipmentpriceID='$shipmentpriceID' 
								AND scsq.colorID IN ($colorID) AND scsq.garmentID IN ($garmentID) AND scsq.statusID = 1";
					$stmt_scsq = $this->conn->prepare($sqlscsq); //
					$stmt_scsq->execute();
					$row_scsq = $stmt_scsq->fetch(PDO::FETCH_ASSOC);
						$this_po_qty = $row_scsq["qty"] / $count_color;
						$po_qty += $this_po_qty;
						
					//=================================//
					//------ Shipping Advise qty ------//
					$sqlShipAdvise = "SELECT sum(saq.shippedQty) as shipqty, DATE(sa.shippeddate) as shippeddate, sa.invnumber
									FROM tblshippingadviseqty saq
									INNER JOIN tblshippingadvise sa ON sa.tblshipmentpriceID = saq.tblshipmentpriceID
									WHERE saq.tblshipmentpriceID='$shipmentpriceID' 
									AND saq.colorID IN ($colorID) AND saq.garmentID IN ($garmentID)";
					$stmt_ship = $this->conn->prepare($sqlShipAdvise); //
					$stmt_ship->execute();
					$row_ship = $stmt_ship->fetch(PDO::FETCH_ASSOC);
						$this_shipqty = $row_ship["shipqty"] / $count_color;
						$shipqty += $this_shipqty;
					
				}//--- End While Group Color ---//
			}
			else{ 
				//--- Shipping advise qty ---//
				//--- if pcs order, ---//
				$sqlShipAdvise = "SELECT sum(saq.shippedQty) as shipqty, DATE(sa.shippeddate) as shippeddate, sa.invnumber
								FROM tblshippingadviseqty saq
								INNER JOIN tblshippingadvise sa ON sa.tblshipmentpriceID = saq.tblshipmentpriceID
								WHERE saq.tblshipmentpriceID='$shipmentpriceID'";
				$sel_shipadvise = $this->conn->prepare($sqlShipAdvise); //
				$sel_shipadvise->execute();
				$row_shipadvise = $sel_shipadvise->fetch(PDO::FETCH_ASSOC);
					extract($row_shipadvise);
			}//*/
			
			//---- If shipping advise not yet buyer invoice, check buyer invoice ----//
			//if($invnumber==""){
				$sqlBI = "SELECT bi.invoice_no as invnumber, DATE(bi.shippeddate) as shippeddate, sum(bid.qty) as shipqty 
							FROM tblbuyer_invoice_detail bid 
							INNER JOIN tblbuyer_invoice bi ON bi.ID = bid.invID
							WHERE bid.del=0 AND bid.shipmentpriceID = '$shipmentpriceID'";
				$stmt_bi = $this->conn->prepare($sqlBI); //
				$stmt_bi->execute();
				$row_bi = $stmt_bi->fetch(PDO::FETCH_ASSOC);
					$invnumber   = $row_bi["invnumber"];
					$shippeddate = $row_bi["shippeddate"];
					$shipqty     = $row_bi["shipqty"];
					$shipqty     = ($shipqty==""? 0: $shipqty);
			//}
			
			$lc_amt          = $po_qty * $fob_price;
			$shipdate        = "";
			//$shipqty         = $shipqty / $count_color;
			$balanceqty      = "";
			$method          = "";
			$str_unit        = ($Qunit>1? "SETS":"PCS");
			$lcprice         += $lc_amt;


			$balanceqty = $shipqty - $po_qty;
			$balanceqty = ($balanceqty>=0? "+".abs($balanceqty): "-".abs($balanceqty));
			$draw_amt   = $shipqty * $fob_price;
			//$css_blue   = ($LCDID==""? 'style="background-color:powderblue;"': '');
			$css_blue   = ($count_LCDID==0? 'style="background-color:powderblue;"': '');
			
			if($po_qty>0){ //--- Check PO Qty > 0 ---//
				
			$html.='<tr id="detailRow_'.$count_info.'-'.$count_detail.'" class="detailRows rows_'.$shipmentpriceID.'" '.$css_blue.'>';
					
			$display_orderno='('.$orderno.')';
			if($pdf=="0"){
				$filter_query = " AND lcd.shipmentpriceID='$shipmentpriceID'";
				$count_byr_inv = $this->funcCheckBuyerInvoiceOpened($LCIID, $filter_query);
				
				$html.='<td>';
					if(!in_array($shipmentpriceID, $arr_exist) && $count_byr_inv>0){
						$html .= '<span class="label label-xs label-info">Buyer Invoice</span>';
					}
				
					if(!in_array($shipmentpriceID, $arr_exist) && in_array(4,$this->arr_permission) && $count_byr_inv==0){
						$html.='<button type="button" class="btn btn-danger btn-xs" onclick="funcRemoveDetail(\''.$count_info.'\',\''.$shipmentpriceID.'\',\''.$LCIID.'\')"><span class="glyphicon glyphicon-trash"></span></button>';
						array_push($arr_exist, $shipmentpriceID);
					}
				
				$html.'</td>';

				$display_orderno = '<label class="label label-info label-xs" 
										style="padding:5px;border-radius:3px;font-size:11px">'.$orderno.'</label>';

				$script_str='
				<script>
					$("#select_buyer").prop("disabled",true).trigger("chosen:updated");
					$("#lcprice'.$count_info.'").html("'.$lcprice.'");
					$("#totalpo'.$count_info.'").html("'.$totalpo.'");
				</script>';
			}

			$html.='<td>
							<font color="#009ec3"><b>'.$orderno.'</b></font> - '.$buyer_po.' 
							<input type="hidden" name="LCDID_'.$count_info.'-'.$count_detail.'" 
												id="LCDID_'.$count_info.'-'.$count_detail.'" value="'.$LCDID.'">
							<input type="hidden" name="LCIID_'.$count_info.'-'.$count_detail.'" 
												id="LCIID_'.$count_info.'-'.$count_detail.'" value="'.$LCIID.'">
							<input type="hidden" name="shipmentpriceID_'.$count_info.'-'.$count_detail.'" 
												id="shipmentpriceID_'.$count_info.'-'.$count_detail.'" value="'.$shipmentpriceID.'">
							<input type="hidden" name="PID_'.$count_info.'-'.$count_detail.'" 
												id="PID_'.$count_info.'-'.$count_detail.'" value="'.$PID.'">
							<input type="hidden" name="garmentID_'.$count_info.'-'.$count_detail.'" 
												id="garmentID_'.$count_info.'-'.$count_detail.'" value="'.$garmentID.'">
							<input type="hidden" name="colorID_'.$count_info.'-'.$count_detail.'" 
												id="colorID_'.$count_info.'-'.$count_detail.'" value="'.$colorID.'">
							<input type="hidden" name="sizename_'.$count_info.'-'.$count_detail.'" 
												id="sizename_'.$count_info.'-'.$count_detail.'" value="'.$size_name.'">
							<input type="hidden" name="group_number_'.$count_info.'-'.$count_detail.'" 
												id="group_number_'.$count_info.'-'.$count_detail.'" value="'.$group_number.'">
						</td>';
						
			// $html .= '<td>'.$sku.'</td>';
			// $html .= '<td>'.$po_color.'</td>';
			// $html .= '<td>'.$size_name.'</td>';
			$html .= '<!--td>'.$method.'</td-->';
			$html .= '<td>'.$po_qty.' '.$str_unit.'</td>
						<td>'.$CurrencyCode.' '.$fob_price.'</td>
						<td id="lcamt_'.$count_info.'-'.$count_detail.'" class="lcamt_'.$shipmentpriceID.'">'.$CurrencyCode.' '.$lc_amt.'</td>
						<td>'.$sp_Shipdate.'</td>
						<td>'.$invnumber.'</td>
						<td>'.$shippeddate.'</td>
						<td>'.$shipqty.' '.$str_unit.'</td>
						<td>'.$balanceqty.'</td>
						<td>'.$CurrencyCode.' '.$draw_amt.'</td>
					</tr>';

			$count_detail++;
			
			
			}//--- End If PO Qty > 0 ---//

		}//--- End While ---//
		$html.='
		<input type="hidden" name="count_detail'.$count_info.'" id="count_detail'.$count_info.'" value="'.$count_detail.'">
		'.$script_str;

		return $html;
		
	}
	
	public function setBuyerInvoiceDetail($BuyerPO, $spID, $orderno, $quotaID, $ht_code, $shipping_remark, $ColorName, $colorID, 
											$invd_ID, $color_qty, $unitprice, $total_amount, $check_prod, $group_number, $gmt_unit, $advise_qty, $LCIID, $BICID, $options, $remarks="", $GTN_styleno=""){//buyer_inv.php, ajax_get_shipment.php
		$this->BuyerPO   = $BuyerPO;
		$this->spID      = $spID;
		$this->orderno   = $orderno;
		$this->quotaID   = $quotaID;
		$this->ht_code   = $ht_code;
		$this->ColorName = $ColorName;
		$this->colorID   = $colorID;
		$this->invd_ID   = $invd_ID;
		$this->color_qty = $color_qty;
		$this->unitprice = $unitprice;
		$this->check_prod      = $check_prod;
		$this->total_amount    = $total_amount;
		$this->shipping_remark = $shipping_remark;
		$this->group_number    = $group_number;
		$this->gmt_unit        = $gmt_unit;
		$this->advise_qty      = $advise_qty;
		$this->LCIID           = $LCIID;
		$this->BICID           = $BICID;
		$this->options         = $options;
		$this->remarks         = $remarks;
		$this->GTN_styleno     = $GTN_styleno;
	}
	
	public $invID                = "0";
	public $BICID                = "0";
	public $cat_invoice_no       = "";
	public $cat_options          = "0";
	public $cat_co_number        = "";
	public $cat_co_date          = "";
	public $cat_custom_no        = "";
	public $cat_custom_date      = "";
	public $cat_custom_procedure = "";
	public $ci_qty = "0";
	public $valid = "1";
	public $GTN_styleno = "";
	public function funcBuyerInvoiceCategory(){ //buyer_inv.php, ajax_custom.php
		$html = "";
		$n = $this->cat_options;
		$css_custom = ($this->isBuyerPayment==0? "":"display:none;");
		$css_co     = ($this->isBuyerPayment==0? "display:none;":"");
		
		$btn_remove = '';
		if(in_array(4, $this->arr_permission) && $n>0){
			$btn_remove = "<button type='button' class='btn btn-xs btn-danger' onclick='funcDeleteCategory($n)' ".$this->disabled_action.">
								<span class='glyphicon glyphicon-trash'></span></button>";
		}
		$btn_expand = "<button type='button' class='btn btn-xs btn-default' onclick='funcToggleExpandCategory($n)'>
								<span class='glyphicon glyphicon-chevron-up' id='icon_category$n'></span></button>";
		$btn_load_po = "<button type='button' class='btn btn-xs btn-default' onclick='func_get_buyerpo($n)'  title='Add Buyer PO' data-toggle='tooltip'>
								<span class='glyphicon glyphicon-plus'></span></button>";
		
		$html .= '<tr id="category'.$n.'" style="background-color:#FCF8D1">';
		$html .= '<td colspan="2" style="white-space:nowrap"> 
					'.$btn_expand.' 
					<input type="text" name="cat_invoice_no'.$n.'" id="cat_invoice_no'.$n.'" value="'.$this->cat_invoice_no.'" class="txt_medium" readonly />  
					<input type="hidden" name="cat_options'.$n.'" id="cat_options'.$n.'" value="'.$n.'" />  
					<input type="hidden" name="BICID'.$n.'" id="BICID'.$n.'" value="'.$this->BICID.'" />
					</td>';
		$html .= '<td colspan="1" style="white-space:nowrap">'.$btn_remove.' '.$btn_load_po.'</td>';
		$html .= '<td colspan="7">
					&nbsp; &nbsp; <b style="'.$css_co.'">C/O No.</b> 
							<input type="text" name="co_number'.$n.'" id="co_number'.$n.'" style="'.$css_co.'"
									placeholder="C/O No...." value="'.$this->cat_co_number.'"/>
					&nbsp; &nbsp; <b style="'.$css_co.'" >C/O Date</b> 
							<input type="text" name="co_date'.$n.'" id="co_date'.$n.'" style="width:100px;'.$css_co.'" 
									class="txt_medium  datepicker_short" value="'.$this->cat_co_date.'"/>
					&nbsp; &nbsp; <b style="'.$css_custom.'" >Custom Declaration No.</b> 
							<input type="text" name="custom_no'.$n.'" id="custom_no'.$n.'" 
									style="'.$css_custom.'" value="'.$this->cat_custom_no.'"/>
					&nbsp; &nbsp; <b style="'.$css_custom.'" >Custom Declaration Date.</b> 
							<input type="text" name="custom_date'.$n.'" id="custom_date'.$n.'" 
									style="width:100px;'.$css_custom.'" class="txt_medium  
									datepicker_short" value="'.$this->cat_custom_date.'"/>
					&nbsp; &nbsp; <b style="'.$css_custom.'" >Custom Procedure.</b> 
							<input type="text" name="custom_procedure'.$n.'" id="custom_procedure'.$n.'" 
									style="'.$css_custom.'" value="'.$this->cat_custom_procedure.'" />
						</td>';
		$html .= '</tr>';
		
		return $html;
	}
	
	public $class_description = "";
	public function funcBuyerInvoiceDetail($n, $c, $countColor){ //advise_qty
		$html = "";
		
		$html .= "<tr id='po_row$n' class='invrow' data-invrownum='$n'>";

		if($c == 0){
			$html .= "<td rowspan='$countColor'>";
			
			if(in_array(4, $this->arr_permission)){
				$html .= " &nbsp; <button type='button' class='btn btn-xs btn-danger' onclick='func_delete_po(\"$n\")' ".$this->disabled_action.">
								<span class='glyphicon glyphicon-trash'></span></button>";
			}
			
			$checked = ($this->valid==1? "checked":"");
			$chkbox = ($this->isBuyerPayment==0? "<br/>&nbsp; Valid: <input type='checkbox' class='chk_valid' name='valid$n' id='valid$n' 
																			value='$n' $checked />": "");
			
			$lbl_invoice = $this->funcCheckExistInOtherInvoice($this->invID, $this->spID);
			$txt_remarks = "<br/><textarea name='remarks$n' id='remarks$n' placeholder='Remarks...' 
											style='padding:3px' ".$this->disabled_action.">".$this->remarks."</textarea>";
			
			$html .= " <button type='button' id='btn_exp-$n' class='btn btn-default btn-xs btn_exp' onclick='funcExpand($n)'>
							<span class='glyphicon glyphicon-th-list'></span></button>";
							
			$html .= "<input type='hidden' name='countColor$n' id='countColor$n' value='$countColor' />
					  <input type='hidden' class='blockconfirm' name='blockconfirm".$n."' value='".$this->check_prod."' />
					  $chkbox
					</td>";
			$url = "../../shipment_new/shipmentmain/preview.php?pono=&&soID=".$this->orderno."&&id=".$this->spID."&&lock=0&&screen=201&&frombinv=yes";
			$link_buyer_po = ($this->isBuyerPayment==0? "<a class='btn btn-link' onclick='func_display_lg(&#39;$url&#39;)'>
								".$this->BuyerPO."</a>": 
							"<a class='btn btn-link' onclick='func_display_lg(&#39;$url&#39;)'>
								".$this->BuyerPO."</a>");
			$str_style = ($this->GTN_styleno==""? "": " <u><i>".$this->GTN_styleno."</i></u>");
			
			$html_opt = '';
			if($this->isBuyerPayment==1 || $this->isBuyerPayment==0){//&& acctid==1
				// $html_opt = "<span class='dropdown'>
						// <button class='btn btn-primary btn-xs dropdown-toggle center-block' type='button' 
								// data-toggle='dropdown'>Container <span class='caret'></span></button>
						// <ul class='dropdown-menu' role='menu'>";
				
				$sql = "SELECT ch.container_no, DATE(ch.issuedDate) as exfactory_date
						FROM `tblci_detail` cd 
						INNER JOIN tblci_header ch ON ch.CIHID = cd.CIHID
						INNER JOIN tblshipmentprice sp ON sp.ID = cd.shipmentpriceID
						WHERE 1=1  
						AND ch.trf_type = 2 AND cd.del = 0 
						AND cd.shipmentpriceID = '".$this->spID."'
						group by cd.shipmentpriceID, ch.container_no
						order by cd.shipmentpriceID, ch.container_no;";
				$stmt_cptd = $this->conn->prepare($sql);
				$stmt_cptd->execute(); 
				while($row_cptd = $stmt_cptd->fetch(PDO::FETCH_ASSOC)){
					$container_no   = $row_cptd["container_no"];
					$exfactory_date = $row_cptd["exfactory_date"];
					
					$html_opt .= "<br/><a style='cursor:pointer' onclick='ajaxLoadPackingList($n, &#39;&#39;, &#39;&#39;, &#39;$container_no&#39;)'>$container_no</a>";
				}
				
				// $html_opt .= "</ul>";
				// $html_opt .= "</span>";
			}
			
			$html .= "<td rowspan='$countColor'>".$link_buyer_po." $str_style $lbl_invoice $txt_remarks<br/>
						<small><font color='#bdbdbd'>".$this->spID."</font></small> 
						<input type='hidden' id='LCIID$n' name='LCIID$n' value='".$this->LCIID."' /> 
						<input type='hidden' id='options$n' name='options$n' value='".$this->options."' /> 
						<input type='hidden' id='spID$n' name='spID$n' value='".$this->spID."' /> 
						<input type='hidden' id='sp_BICID$n' name='sp_BICID$n' value='".$this->BICID."' /> 
						<input type='hidden' id='use_ori_qty$n' name='use_ori_qty$n' value='0' /> 
						<input type='hidden' id='orderno$n' name='orderno$n' value='".$this->orderno."' /> 
						<input type='hidden' id='BuyerPO$n' name='BuyerPO$n' value='".$this->BuyerPO."' />
						$html_opt
						</td>";
			$html .= "<td rowspan='$countColor'>".$this->orderno."</td>";
			$html .= "<td rowspan='$countColor' style='white-space:nowrap'>
						<table >";
				
				$gnum = 0;
				$tblbuyer_invoice_detail = ($this->isBuyerPayment==0? "tblbuyer_invoice_payment_detail": "tblbuyer_invoice_payment_detail");
				$tblbuyer_invoice_hts    = ($this->isBuyerPayment==0? "tblbuyer_invoice_payment_hts": "tblbuyer_invoice_payment_hts");
				$sqlgmt = "SELECT g.garmentID as gID, g.styleNo as style,
								(SELECT quotaID FROM $tblbuyer_invoice_hts bih 
								 WHERE bih.invID='".$this->invID."' AND bih.BICID='".$this->BICID."' 
								 AND bih.shipmentpriceID = sgc.shipmentpriceID 
								 AND bih.garmentID = sgc.garmentID limit 1) as this_quotaID,
								 (SELECT quotaID 
								 FROM tblbuyer_invoice_payment_hts bih 
								 INNER JOIN tblbuyer_invoice_payment_category bic ON bic.BICID = bih.BICID
								 WHERE bih.invID='".$this->invID."' AND bic.options='".$this->options."' 
								 AND bih.shipmentpriceID = sgc.shipmentpriceID 
								 AND bih.garmentID = sgc.garmentID limit 1) as ci_quotaID
						FROM tblship_group_color sgc 
						INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
						WHERE sgc.shipmentpriceID='".$this->spID."' AND sgc.statusID=1
						group by g.garmentID";
				// echo "<pre>$sqlgmt</pre>";
				$stmt_gmt = $this->conn->prepare($sqlgmt);
				$stmt_gmt->execute();
				while($row_gmt = $stmt_gmt->fetch(PDO::FETCH_ASSOC)){
					extract($row_gmt);
					$gnum++;
					$html .= "<tr>";
					$html .= "<td>$style : </td>";
					$html .= "<td  id='td_quota_cat$n-$gnum' >";
					
					$html .= "<select name='quota_cat$n-$gnum' id='quota_cat$n-$gnum' class='select_chosen' style='width:100px;' 
										".$this->disabled_action." >";
					$html .= "<option value='0'>-</option>";

					$sel_quota = $this->conn->prepare("SELECT * FROM tblquotacat WHERE StatusID=1");
					$sel_quota->execute();
					$sel_str_quota = "";
					while($row_quota = $sel_quota->fetch(PDO::FETCH_ASSOC)){
						$row_quotaID   = $row_quota['ID'];
						$row_quotaName = $row_quota['Description'];
						$selected = "";

						if($row_quotaID == $this_quotaID){
							$selected="SELECTED";
							$sel_str_quota = "$row_quotaName";
						}

						$html .= "<option value='$row_quotaID' $selected>$row_quotaName</option>";
					}//--- End While Quota Cat ---//

					$html .= "</select></td>";
					// $sqlci = "SELECT bid.quotaID 
								// FROM `tblbuyer_invoice_detail` bid 
								// INNER JOIN tblbuyer_invoice_category bic ON bic.BICID = bid.BICID AND bic.invID = bid.invID
								// WHERE bid.invID='".$this->invID."' AND bic.options='".$this->options."' AND bid.del=0 limit 1";
					// $sel_ci = $this->conn->prepare($sqlci);
					// $sel_ci->execute();
					// $row_ci = $sel_ci->fetch(PDO::FETCH_ASSOC);
						// $ci_quotaID = $row_ci["quotaID"];
						
					$html .= "<td >
						<input type='hidden' name='ci_quota_cat$n-$gnum' id='ci_quota_cat$n-$gnum' value='$ci_quotaID' >
						<input type='text' name='txt_quota_cat$n-$gnum' id='txt_quota_cat$n-$gnum' value='$sel_str_quota'
								class='txt_short' style='display:none' />
						<input type='hidden' name='mode_quota_cat$n-$gnum' id='mode_quota_cat$n-$gnum' value='0'>
						<button type='button' id='btn_quota_cat$n-$gnum' class='btn btn-default btn-xs' onclick='funcEditMode(&#39;quota_cat&#39;, &#39;$n-$gnum&#39;)' 
								title='Edit Mode' data-toggle='tooltip'>
							<span class='glyphicon glyphicon-edit' id='icon_quota_cat$n-$gnum'></span></button>
							</td></tr>";
				}
							
					$html .= "</table></td>";
							
			$html .= "<td rowspan='$countColor'>";
			
			$html .= "<input type='text' name='ht_code$n' class='txt_medium' value='".$this->ht_code."' 
								required style='display:none' placeholder='HTS Code...'  />";
			
			$gnum = 0;
			$tblbuyer_invoice_detail = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail": "tblbuyer_invoice_payment_detail");
			$tblbuyer_invoice_hts    = ($this->isBuyerPayment==0? "tblbuyer_invoice_hts": "tblbuyer_invoice_payment_hts");
			$sqlgmt = "SELECT g.garmentID as gID, g.styleNo as style, 
                                 gd.Description as gender,  ptt.Description as product_type,
								(SELECT ht_code FROM $tblbuyer_invoice_hts bih 
								 WHERE bih.invID='".$this->invID."' AND bih.BICID='".$this->BICID."' 
								 AND bih.shipmentpriceID = sgc.shipmentpriceID 
								 AND bih.garmentID = sgc.garmentID limit 1) as this_ht_code,
								 (SELECT ht_code 
								 FROM tblbuyer_invoice_payment_hts bih 
								 INNER JOIN tblbuyer_invoice_payment_category bic ON bic.BICID = bih.BICID
								 WHERE bih.invID='".$this->invID."' AND bic.options='".$this->options."' 
								 AND bih.shipmentpriceID = sgc.shipmentpriceID 
								 AND bih.garmentID = sgc.garmentID limit 1) as ci_ht_code,
                                 
                                 '' as fabric_garment,
								 
								 '' as fs_shipping_marking,
								  
								  (SELECT shipping_marking FROM $tblbuyer_invoice_hts bih 
								 WHERE bih.invID='".$this->invID."' AND bih.BICID='".$this->BICID."' 
								 AND bih.shipmentpriceID = sgc.shipmentpriceID 
								 AND bih.garmentID = sgc.garmentID limit 1) as this_shipping_marking,
								 
								 (SELECT shipping_marking 
								 FROM tblbuyer_invoice_payment_hts bih 
								 INNER JOIN tblbuyer_invoice_payment_category bic ON bic.BICID = bih.BICID
								 WHERE bih.invID='".$this->invID."' AND bic.options='".$this->options."' 
								 AND bih.shipmentpriceID = sgc.shipmentpriceID 
								 AND bih.garmentID = sgc.garmentID limit 1) as ci_shipping_marking
								 
								 
						FROM tblship_group_color sgc 
						INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
						LEFT JOIN tblgender gd ON gd.ID = g.genderID
                        LEFT JOIN tblproducttype ptt ON ptt.ID = g.gmttype
						WHERE sgc.shipmentpriceID='".$this->spID."' AND sgc.statusID=1
						group by g.garmentID";
			// echo "<pre>$sqlgmt</pre>";
			$stmt_gmt = $this->conn->prepare($sqlgmt);
			$stmt_gmt->execute();
			while($row_gmt = $stmt_gmt->fetch(PDO::FETCH_ASSOC)){
				extract($row_gmt);
				$gnum++;
				
				$html .= "$style: ";
				$html .= "<input type='text' name='ht_code$n-$gnum' id='ht_code$n-$gnum' class='txt_medium' value='".$this_ht_code."' 
								required ".$this->disabled_action." placeholder='HTS Code...'  /><br/><br/>";
				$html .= "<input type='hidden' name='this_garmentID$n-$gnum' value='$gID' >";
				$html .= "<input type='hidden' name='ci_ht_code$n-$gnum' id='ci_ht_code$n-$gnum' value='$ci_ht_code' >";
			}
			
			$html .= "<input type='hidden' name='gmt_count$n' id='gmt_count$n' value='$gnum' >";
			$html .= "</td>";
			
			// if($this->acctid==1){
				$html .= "<td rowspan='$countColor'>";
				$gnum = 0;
				$stmt_gmt->execute();
				while($row_gmt = $stmt_gmt->fetch(PDO::FETCH_ASSOC)){
					extract($row_gmt);
					$gnum++;
					
					$ori_shipping_marking  = (trim($fs_shipping_marking)!=""? $fs_shipping_marking: "$gender $fabric_garment $product_type");
					$this_shipping_marking = (trim($this_shipping_marking)!=""? $this_shipping_marking: $ori_shipping_marking);
					
					$this_shipping_marking   = htmlspecialchars_decode($this_shipping_marking);
					$this_shipping_marking   = strtolower($this_shipping_marking);
					$this_shipping_marking   = html_entity_decode($this_shipping_marking);
					$this_shipping_marking   = strtoupper($this_shipping_marking);
					
					$ci_shipping_marking   = htmlspecialchars_decode($ci_shipping_marking);
					$ci_shipping_marking   = strtolower($ci_shipping_marking);
					$ci_shipping_marking   = html_entity_decode($ci_shipping_marking);
					$ci_shipping_marking   = strtoupper($ci_shipping_marking);
					
					$html .= "$style: ";
					$html .= "<input type='text' name='shipping_remark$n-$gnum' id='shipping_remark$n-$gnum'
									class='txt_medium shipping_remark' value='$this_shipping_marking' required ".$this->disabled_action." /><br/>";
					$html .= "<input type='hidden' name='ci_shipping_remark$n-$gnum' id='ci_shipping_remark$n-$gnum' value='$ci_shipping_marking' >";
					$html .= "<input type='hidden' name='ori_shipping_remark$n-$gnum' id='ori_shipping_remark$n-$gnum' 
										value='$ori_shipping_marking' >";
				}
				
				$html .= "</td>";
			// }
			
		}//--- End C==0 ---//
		
		$sqlci = "SELECT bid.shipping_marking, bid.class_description
					FROM `tblbuyer_invoice_detail` bid 
					INNER JOIN tblbuyer_invoice_category bic ON bic.BICID = bid.BICID
					WHERE bid.shipmentpriceID = '".$this->spID."' AND bid.group_number>0 
					AND bid.del=0 AND bid.invID='".$this->invID."' AND bic.del=0 AND bic.options='".$this->options."'";
		$stmt_ci = $this->conn->prepare($sqlci);
		$stmt_ci->execute();
		$row_ci = $stmt_ci->fetch(PDO::FETCH_ASSOC);
			$ci_shipping_marking  = html_entity_decode($row_ci["shipping_marking"]);
			$ci_class_description = $row_ci["class_description"];
		
		$link_sync = "<a style='cursor:pointer' onclick='funcSync(&#39;shipping_remark&#39;, $n, $c)'>[v]</a>";
		
		if($this->acctid!=0){
			
		}
		else{
			$html .= "<td ><input type='text' name='shipping_remark$n-$c' id='shipping_remark$n-$c'
									class='txt_medium' value='".$this->shipping_remark."' required 
									".$this->disabled_action." /> $link_sync</td>";
		}
		$html .= "<td>".$this->ColorName."<br/>
					Class: <input type='text' name='class_description$n-$c' id='class_description$n-$c' 
							value='".$this->class_description."' placeholder='Class...' class='txt_short' />
					<input type='hidden' name='ci_class_description$n-$c' id='ci_class_description$n-$c' value='".$ci_class_description."' />
					<input type='hidden' name='ci_shipping_remark$n-$c' id='ci_shipping_remark$n-$c' value='".$ci_shipping_marking."' />
					<input type='hidden' name='group_number$n-$c' id='group_number$n-$c' value='".$this->group_number."' />
					<input type='hidden' name='colorID$n-$c' id='colorID$n-$c' value='".$this->colorID."' /> 
					<input type='hidden' name='advise_qty$n-$c' id='advise_qty$n-$c' value='".$this->advise_qty."' /> 
					<input type='hidden' name='ci_qty$n-$c' id='ci_qty$n-$c' value='".$this->ci_qty."' /> 
					<input type='hidden' name='invd_ID$n-$c' value='".$this->invd_ID."' /></td>";
		$html .= "<td><input type='number' name='color_qty$n-$c' id='color_qty$n-$c' class='txt_short' 
						value='".$this->color_qty."' onkeyup='funcCalculateAll()' readonly /> ".$this->gmt_unit."</td>";
		$up_readonly = ($this->isBuyerPayment==1? "readonly":"");//readonly
		$link_sync = ($c==0 && $this->isBuyerPayment==0? "<a style='cursor:pointer' onclick='funcSync(&#39;unit_price&#39;, $n, $c)'>[v]</a>":
							"");
		$arrBPP = $this->getBuyerPOPrice($this->spID, $this->group_number);
		$poprice = $arrBPP["poprice"];
		$lbl_alert = ($poprice!=$this->unitprice && $this->isBuyerPayment==1? "<font color='red'>**PO Price was changed in Buyer PO <u>$poprice</u></font>":"");
		
		$html .= "<td><input type='number' name='unit_price$n-$c' id='unit_price$n-$c' step='any' min='0' class='txt_short'
						 onkeyup='funcCalculateAll()' value='".$this->unitprice."' $up_readonly /> $link_sync
						 <input type='hidden' name='ori_poprice$n-$c' id='ori_poprice$n-$c' value='".$poprice."' />$lbl_alert
						 </td>";
		$html .= "<td><input type='text' name='total_amt$n-$c' id='total_amt$n-$c' class='txt_short total_amt$n amt_box po_amt' value='".$this->total_amount."' readonly /></td>";
		$html .= "</tr>";
		
		
		
		return $html;
	}
	
	public function funcCheckExistInOtherInvoice($invID, $spID){
		$tblbuyer_inv_detail = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail": "tblbuyer_invoice_payment_detail");
		$tblbuyer_inv        = ($this->isBuyerPayment==0? "tblbuyer_invoice": "tblbuyer_invoice_payment");
		$lbl_invoice = "";
		
		$sql = "SELECT bi.invoice_no, bi.ID 
				FROM $tblbuyer_inv_detail bid
				INNER JOIN $tblbuyer_inv bi ON bi.ID = bid.invID
				WHERE bid.invID<>'$invID' AND bid.shipmentpriceID='$spID' 
				AND bid.group_number>0 AND bid.del=0 
				group by bi.ID";
		$stmt_bi = $this->conn->prepare($sql); //
		$stmt_bi->execute();
		while($row_bi = $stmt_bi->fetch(PDO::FETCH_ASSOC)){
			$ID         = $row_bi["ID"];
			$invoice_no = $row_bi["invoice_no"];
			
			$url         = ($this->isBuyerPayment==0? "buyer_inv.php?id=$ID": "buyer_inv.php?id=$ID&isBuyerPayment=true");
			$lbl_invoice .= ($invoice_no==""? "":" <a href='$url' target='_blank' class='label label-info'>$invoice_no</a>");
		}
		
		
		
		return $lbl_invoice;
	}
	
	public function funcBuyerInvoiceExpand($n){
		$html = "";
		//=========================================//
		//------------ Table Expand ---------------//
		$html .= "<tr id='expand$n' class='tr_nondisplay'>";
		$html .= "<td ></td>";
		$html .= "<td colspan='9' id='expand_td$n'></td>";
		$html .= "</tr>";
		
		return $html;
	}
	
	public function funcBuyerInvoiceExpandPackingListHead($this_n){
		$html = '';
		
		// $sizesql = $this->handle_shipment->getSizeNameColumnFromOrder($orderno, 1);
		// $size_colspan = 0;//$sizesql->rowCount();
		// while($sizerow = $sizesql->fetch(PDO::FETCH_ASSOC)){
			// $size_name = $sizerow["SizeName"];
			
		// }
		$html .= '<table class="tb_inv_expand">';
		$html .= '<tr>';
		$html .= '<td ></td>';
		$html .= '<td >CARTON NO <button type="button" class="btn btn-default btn-xs" onclick="funcAddCtnRow('.$this_n.')">
									<span class="glyphicon glyphicon-plus"></span></button></td>';
		$html .= '<td >NO OF CARTON</td>';
		$html .= '<td >NG ITEMS / SKU</td>';
		$html .= '<td >UPC</td>';
		$html .= '<td >Master ID</td>';
		//$html .= '<td >COLOR SIZE INFO</td>';
		$html .= '<td >SUB TOTAL PER CARTON</td>';
		$html .= '<td >TOTAL QTY</td>';
		$html .= '<td >NNW (KGS)<br/>/ PER CTN</td>';
		$html .= '<td >NW (KGS)<br/>/ PER CTN</td>';
		$html .= '<td >GW (KGS)<br/>/ PER CTN</td>';
		$html .= '<td >EXT LENGTH (CM)</td>';
		$html .= '<td >EXT WIDTH (CM)</td>';
		$html .= '<td >EXT HEIGHT (CM)</td>';
		$html .= '<td >CBM / PER CTN</td>';
		$html .= '</tr>';
		
		return $html;
	}
	
	public function funcBuyerInvoiceExpandPackingListFooter($this_n, $c){
		$html = '';
		$html .= "<tr id='tr_last$this_n'></tr></table>
					<input type='hidden' name='count_pl_row$this_n' id='count_pl_row$this_n' value='$c' />";
		
		return $html;
	}
	
	public $CIHID = 0;
	public $cih_spID = 0;
	public $PID = 0;
	public $start = 0;
	public $end_num = 0;
	public $total_ctn = 0;
	public $SKU = "";
	public $prepack_name = "";
	public $masterID = "";
	public $is_last = 0;
	public $blisterbag_in_carton = 1;
	public $total_qty_in_carton = 0;
	public $total_qty = 0;
	public $weight_unitID = 44;
	public $net_net_weight = 0;
	public $net_weight = 0;
	public $gross_weight = 0;
	public $ext_length = 0;
	public $ext_width = 0;
	public $ext_height = 0;
	public $ctn_unitID = 16;//CM
	public $total_CBM = 0;
	public $arr_list_detail = "";
	public function funcBuyerInvoiceExpandPackingListDetail($n, $c){ //ajax_custom.php
		$id = "$n-$c";
		$html = "";
		
		$html .= '<tr id="tr_ctn_row'.$id.'">';
		$html .= '<td><button type="button" class="btn btn-danger btn-xs" onclick="funcRemoveCtnRow('.$n.','.$c.')">
							<span class="glyphicon glyphicon-trash"></span></button>
					   <input type="hidden" name="CIHID'.$id.'" id="CIHID'.$id.'" value="'.$this->CIHID.'" />
					   <input type="hidden" name="cih_spID'.$id.'" id="cih_spID'.$id.'" value="'.$this->cih_spID.'" />
					   <input type="hidden" name="PID'.$id.'" id="PID'.$id.'" value="'.$this->PID.'" />
					   <input type="hidden" name="is_last'.$id.'" id="is_last'.$id.'" value="'.$this->is_last.'" />
					   <input type="hidden" name="weight_unitID'.$id.'" id="weight_unitID'.$id.'" value="'.$this->weight_unitID.'" />
					   <input type="hidden" name="ctn_unitID'.$id.'" id="ctn_unitID'.$id.'" value="'.$this->ctn_unitID.'" />
					   <input type="hidden" name="blisterbag_in_carton'.$id.'" id="blisterbag_in_carton'.$id.'" value="'.$this->blisterbag_in_carton.'" />
					   </td>';
		$html .= '<td style="white-space:nowrap"><input type="number" name="start'.$id.'" id="start'.$id.'" min="0" placeholder="Start..." class="txt_xs" 
							value="'.$this->start.'" onkeyup="funcStartRearrange('.$n.','.$c.')"  onchange="funcStartRearrange('.$n.','.$c.')" /> - 
						<input type="number" name="end_num'.$id.'" id="end_num'.$id.'" min="0" placeholder="End..." class="txt_xs" 
								value="'.$this->end_num.'" onkeyup="funcEndRearrange('.$n.','.$c.')" onchange="funcEndRearrange('.$n.','.$c.')"/></td>';
		$html .= '<td ><input type="number" name="total_ctn'.$id.'" id="total_ctn'.$id.'" min="0" class="txt_xs" 
								value="'.$this->total_ctn.'" onkeyup="funcStartRearrange('.$n.',1)"  onchange="funcStartRearrange('.$n.',1)" /></td>';
		$html .= '<td ><input type="text" name="SKU'.$id.'" id="SKU'.$id.'" value="'.$this->SKU.'" />
						<a style="cursor:pointer" onclick="funcSyncValue('.$n.','.$c.',&#39;SKU&#39;)">[v]</a></td>';
		$html .= '<td ><input type="text" name="prepack_name'.$id.'" id="prepack_name'.$id.'" value="'.$this->prepack_name.'" />
						<a style="cursor:pointer" onclick="funcSyncValue('.$n.','.$c.',&#39;prepack_name&#39;)">[v]</a></td>';
		$html .= '<td ><input type="number" name="masterID'.$id.'" id="masterID'.$id.'" class="txt_xs"
								min="0" value="'.$this->masterID.'" />
						<a style="cursor:pointer" onclick="funcSyncValue('.$n.','.$c.',&#39;masterID&#39;)">[v]</a></td>';
		//$html .= '<td ></td>';
		
		$tooltip_html = $this->funcGetTooltipDetailColorSize($this->cih_spID, $this->arr_list_detail);
		
		$html .= '<td ><input type="number" name="total_qty_in_carton'.$id.'" id="total_qty_in_carton'.$id.'" min="0" class="txt_short" 
								value="'.$this->total_qty_in_carton.'" readonly />
					   <a style="cursor:pointer" onclick="funcLoadPackingListDetail('.$n.','.$c.')" class="tt_large" 
							title="'.$tooltip_html.'" data-toggle="tooltip" data-html="true" id="tt_title'.$id.'">
							&nbsp; <span class="glyphicon glyphicon-th"></span> &nbsp;</a>
				<input type="hidden" name="arr_list_detail'.$id.'" id="arr_list_detail'.$id.'" value="'.$this->arr_list_detail.'" /></td>';
		$html .= '<td ><input type="number" name="total_qty'.$id.'" id="total_qty'.$id.'" min="0" 
								class="txt_short" value="'.$this->total_qty.'" readonly /></td>';
		$html .= '<td style="white-space:nowrap"><input type="number" name="net_net_weight'.$id.'" id="net_net_weight'.$id.'" min="0" step="any" class="txt_xs"
							value="'.$this->net_net_weight.'" /> 
						<a style="cursor:pointer" onclick="funcSyncValue('.$n.','.$c.',&#39;net_net_weight&#39;)">[v]</a></td>';
		$html .= '<td style="white-space:nowrap"><input type="number" name="net_weight'.$id.'" id="net_weight'.$id.'" min="0" step="any" class="txt_xs"
							value="'.$this->net_weight.'" />
						<a style="cursor:pointer" onclick="funcSyncValue('.$n.','.$c.',&#39;net_weight&#39;)">[v]</a></td>';
		$html .= '<td style="white-space:nowrap"><input type="number" name="gross_weight'.$id.'" id="gross_weight'.$id.'" min="0" step="any" class="txt_xs"
							value="'.$this->gross_weight.'" />
						<a style="cursor:pointer" onclick="funcSyncValue('.$n.','.$c.',&#39;gross_weight&#39;)">[v]</a></td>';
		$html .= '<td ><input type="number" name="ext_length'.$id.'" id="ext_length'.$id.'" min="0" step="any" class="txt_xs"
							value="'.$this->ext_length.'" onkeyup="calCBM('.$n.','.$c.')" />
						<a style="cursor:pointer" onclick="funcSyncValue('.$n.','.$c.',&#39;ext_length&#39;)">[v]</a></td>';
		$html .= '<td ><input type="number" name="ext_width'.$id.'" id="ext_width'.$id.'" min="0" step="any" class="txt_xs" 
							value="'.$this->ext_width.'" onkeyup="calCBM('.$n.','.$c.')" />
						<a style="cursor:pointer" onclick="funcSyncValue('.$n.','.$c.',&#39;ext_width&#39;)">[v]</a></td>';
		$html .= '<td ><input type="number" name="ext_height'.$id.'" id="ext_height'.$id.'" min="0" step="any" class="txt_xs"
							value="'.$this->ext_height.'" onkeyup="calCBM('.$n.','.$c.')" />
						<a style="cursor:pointer" onclick="funcSyncValue('.$n.','.$c.',&#39;ext_height&#39;)">[v]</a></td>';
		$html .= '<td><input type="number" name="total_CBM'.$id.'" id="total_CBM'.$id.'" min="0" step="any" class="txt_xs"
							value="'.$this->total_CBM.'" readonly /></td>';
		$html .= '</tr>';
		
		return $html;
	}
	
	public function funcGetTooltipDetailColorSize($cih_spID, $arr_list_detail){
		$html = '';
		
		$html = "<table>";
		$html .= "<tr>";
		$html .= "<td >Color Size</td>";
		$html .= "<td style='white-space:nowrap'>Qty (Per Carton)</td>";
		$html .= "</tr>";
		$html .= "<tr>";
		
		if($arr_list_detail!=""){
			$arr_pd = explode("::^^", $arr_list_detail);
			for($i=0;$i<count($arr_pd);$i++){
				list($group_number, $size_name, $qty) = explode("**%%", $arr_pd[$i]);
				
				$sql = "SELECT sgc.group_number, GROUP_CONCAT(c.ColorName,' (',g.styleNo,')' separator ', ') as grp_color
						FROM tblship_group_color sgc 
						INNER JOIN tblcolor c ON c.ID = sgc.colorID
						INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
						WHERE sgc.shipmentpriceID='$cih_spID' AND sgc.statusID=1 AND sgc.group_number='$group_number'
						group by sgc.group_number";
				$stmt_sgc = $this->conn->prepare($sql);
				$stmt_sgc->execute();
				$row_sgc = $stmt_sgc->fetch(PDO::FETCH_ASSOC);
					$grp_color = $row_sgc["grp_color"];
					
				if($qty>0){
					$html .= "<tr>";
					$html .= "<td>$grp_color - $size_name</td>";
					$html .= "<td>$qty</td>";
					$html .= "</tr>";
				}
			}
		}
		
		$html .= "</table>";
		
		return $html;
	}
	
	public function funcBuyerInvoiceExpandPackingListDetailColorSize($n, $c, $r, $shipmentpriceID, $group_number, $size_name, $qty){
		$sql = "SELECT ccs.gmt_pcs_weight, sp.Orderno, cco.ctn_weight  
				FROM `tblcarton_calculator_sizeinfo` ccs
				INNER JOIN tblshipmentprice sp ON sp.Orderno = ccs.orderno
				INNER JOIN tblcarton_calculator_head cch ON cch.orderno = ccs.orderno
                INNER JOIN tblcarton_calculator_picklist ccp ON ccp.CCHID = cch.CCHID
                INNER JOIN tblcarton_calculator_option cco ON cco.CCPID = ccp.CCPID
				WHERE sp.ID = '$shipmentpriceID' AND ccs.size_name='$size_name' AND ccs.statusID=1 AND ccs.gmt_pcs_weight>0
				AND cco.statusID=1 AND cco.selected=1 limit 1";
		$stmt_sgc = $this->conn->prepare($sql);
		$stmt_sgc->execute();
		$row_sgc = $stmt_sgc->fetch(PDO::FETCH_ASSOC);
			$gmt_pcs_weight = $row_sgc["gmt_pcs_weight"];
			$Orderno = $row_sgc["Orderno"];
			$ctn_weight = $row_sgc["ctn_weight"];
			
		$sql2 = "SELECT shipmentpriceID, net_net_weight, net_weight, gross_weight, total_qty_in_carton
					FROM `tblcarton_picklist_head` 
					WHERE shipmentpriceID='$shipmentpriceID' limit 1";
		$stmt = $this->conn->prepare($sql2);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$total_qty_in_carton = $row["total_qty_in_carton"];
			$net_net_weight      = round($row["net_net_weight"], 3);
			$net_net_weight      = round($net_net_weight / $total_qty_in_carton, 5);
			$net_weight          = round($row["net_weight"], 3);
			$net_weight          = round($net_weight / $total_qty_in_carton, 5);
			$gross_weight        = round($row["gross_weight"], 3);
			$gross_weight        = round($gross_weight / $total_qty_in_carton, 5);
			
		$sql = "SELECT sum(amt.acc_weight) as acc_weight 
			FROM tblapurchase ap
			INNER JOIN tblapurchase_detail apd ON apd.APID = ap.APID
			INNER JOIN tblasizecolor ascl ON ascl.ASCID = apd.ASCID
			INNER JOIN tblamaterial amt ON amt.AMID = ascl.AMID
			WHERE ap.statusID NOT IN (6) AND amt.AtypeID NOT IN (67)
			AND amt.trimtypeID IN (2) AND ap.orderno='$Orderno'"; //apd.unitprice>0 AND
		$result_pack = $this->conn->prepare($sql);
		$result_pack->execute();
		$rowpack = $result_pack->fetch(PDO::FETCH_ASSOC);
			$acc_weight = $rowpack["acc_weight"];
			$net_weight = round(($acc_weight / $total_qty_in_carton) + $gmt_pcs_weight, 5);
			$gross_weight = round(($ctn_weight / $total_qty_in_carton) + $net_weight, 5);
		
		$html = "";
		$html .= '<tr id="tr_csqrow'.$r.'">';
		$html .= '<td><button type="button" class="btn btn-danger btn-xs" onclick="funcRemoveCSQRow('.$r.')">
						<span class="glyphicon glyphicon-trash"></span></button></td>';
		$html .= '<td><select name="colorsize'.$r.'" id="colorsize'.$r.'" class="select_long select_chosen" style="width:600px">';
			$html .= $this->getBuyerInfoColorSize($shipmentpriceID, $group_number, $size_name);
		$html .= '</select></td>';
		$html .= '<td><input type="number" name="csq_qty'.$r.'" id="csq_qty'.$r.'" min="0" 
								class="txt_xs" value="'.$qty.'" onkeyup="funcCalColorSizeQty()" />
					<input type="hidden" name="csq_nnw'.$r.'" id="csq_nnw'.$r.'" value="'.$gmt_pcs_weight.'" />
					<input type="hidden" name="csq_nw'.$r.'" id="csq_nw'.$r.'" value="'.$net_weight.'" />
					<input type="hidden" name="csq_gw'.$r.'" id="csq_gw'.$r.'" value="'.$gross_weight.'" />
					</td>';
		$html .= '</tr>';
		
		return $html;
	}
	
	public function getBuyerInfoColorSize($shipmentpriceID, $selected_grpID, $selected_size){
		$html = '';
			
			$arr_size = array();
			$sqlSize = "SELECT scsq.size_name 
						FROM tblship_colorsizeqty scsq
						WHERE scsq.shipmentpriceID = '$shipmentpriceID' AND scsq.statusID=1 AND scsq.qty>0 
						group by scsq.size_name 
						order by scsq.ID asc";
			$stmt_size = $this->conn->prepare($sqlSize);
			$stmt_size->execute();
			while($row_size = $stmt_size->fetch(PDO::FETCH_ASSOC)){
				extract($row_size);
				$arr_size [] = $size_name;
			}//--- End While Size ---//
		
		$sql = "SELECT sgc.group_number, GROUP_CONCAT(c.ColorName,' (',g.styleNo,')' separator ', ') as grp_color
				FROM tblship_group_color sgc 
				INNER JOIN tblcolor c ON c.ID = sgc.colorID
				INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
				WHERE sgc.shipmentpriceID='$shipmentpriceID' AND sgc.statusID=1
				group by sgc.group_number";
		$stmt_sgc = $this->conn->prepare($sql);
		$stmt_sgc->execute();
		while($row_sgc = $stmt_sgc->fetch(PDO::FETCH_ASSOC)){
			extract($row_sgc);
			
			for($i=0;$i<count($arr_size);$i++){
				$size_name = $arr_size[$i];
				
				$selected = ($group_number==$selected_grpID && $size_name==$selected_size? "selected": "");
				$this_id = "$group_number**%%$size_name";
				$html .= '<option value="'.$this_id.'" '.$selected.'>'.$grp_color.' - '.$size_name.'</option>';
				//$html .= '<option '.$selected.'>'.$grp_color.' - '.$size_name.'</option>';
			}
			
			
		}//--- End While group_number color ---//
		
		return $html;
	}
	
	public function getBuyerInvoicePDFInvoice($id, $query_filter=""){ //buyer_joefresh.php, buyer_dxl.php, buyer_buffalo_tw.php, buyer_buffalo_cn.php, buyer_noble.php, buyer_hunnybunny.php, buyer_puma.php, cci_template.php, buyer_disney.php
		$arr_buyerpo   = array();
		$arr_shipID    = array();
		$arr_all       = array();
		$grand_inv_gw  = 0;
		$grand_inv_nw  = 0;
		$grand_inv_nnw = 0;
		$grand_inv_ctn = 0;
		$grand_inv_qty = 0;
		$grand_inv_cbm = 0;
		
		$tblbyr_inv           = ($this->isBuyerPayment==0? "tblbuyer_invoice":"tblbuyer_invoice_payment");
		$tblbyr_inv_detail    = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail":"tblbuyer_invoice_payment_detail");
		$tblbyr_inv_category  = ($this->isBuyerPayment==0? "tblbuyer_invoice_category":"tblbuyer_invoice_payment_category");
		$tblctn               = ($this->isBuyerPayment==0? "tblcarton_inv_head":"tblcarton_inv_payment_head");
		$tblbuyer_invoice_hts = ($this->isBuyerPayment==0? "tblbuyer_invoice_hts":"tblbuyer_invoice_payment_hts");
		
		$query_filter = ($query_filter==""? ", bid.shipping_marking ":"$query_filter");
		
		// $sqlInv = " SELECT bid.shipmentpriceID, sp.Orderno, sp.BuyerPO, sp.GTN_buyerpo as actual_BuyerPO, bid.fob_price, se.Description as uom, se.ID as setID,
					// g.styleNo as styleNo, qtc.Description as quotacat, od.FactoryID,
					// bid.shipping_marking, gd.Description as gender, ptt.Description as product_type, bid.ht_code,
					// cur.CurrencyCode, bid.BICID, bic.options, bi.shippeddate, bi.BuyerID
					
				// FROM $tblbyr_inv_detail bid 
				// INNER JOIN $tblbyr_inv bi ON bi.ID = bid.invID
				// INNER JOIN $tblbyr_inv_category bic ON bic.BICID = bid.BICID
				// INNER JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
				// INNER JOIN tblorder od ON od.Orderno = sp.Orderno
				// INNER JOIN tblset se ON od.Qunit = se.ID
				// INNER JOIN tblgarment g ON find_in_set(g.garmentID, sp.StyleNo)
				// LEFT JOIN tblgender gd ON gd.ID = g.genderID
				// LEFT JOIN tblproducttype ptt ON ptt.ID = g.gmttype
				// INNER JOIN tblquotacat qtc ON qtc.ID = bid.quotaID
				// LEFT JOIN tblcurrency cur ON cur.ID = od.currencyID
				// -- INNER JOIN tblnewenquiry ne ON ne.QDID = g.QDID
				// WHERE bid.invID='$id' AND bid.del='0' AND bid.group_number>0
				// group by bid.BICID, bid.shipmentpriceID $query_filter
				// order by bic.options asc, bid.ID asc";
		$arr_PO = array();
		$arr_info = array();
		$grand_gw  = 0;
		$grand_nw  = 0;
		$grand_nnw = 0;
		$total_ctn = 0;
		$grand_cbm = 0;
		$grand_qty = 0; 
		//qtc.Description as quotacat,
		$sqlInv = "SELECT * FROM (SELECT bid.shipmentpriceID, sp.Orderno, sp.BuyerPO, sp.GTN_buyerpo as actual_BuyerPO, bid.fob_price, se.Description as uom, se.ID as setID, sp.StyleNo as sp_garmentID,
					'' as styleNo,  od.FactoryID,
					bid.shipping_marking, '' as gender, '' as product_type, bid.ht_code,
					cur.CurrencyCode, bid.BICID, bic.options, bi.shippeddate, bi.BuyerID,
                    (SELECT GROUP_CONCAT(DISTINCT qc.Description)
                     FROM $tblbuyer_invoice_hts bih 
                     INNER JOIN tblquotacat qc ON qc.ID = bih.quotaID
                     WHERE bih.invID = bid.invID AND bih.BICID = bid.BICID 
                     AND bih.shipmentpriceID = bid.shipmentpriceID ) as quotacat, MIN(bid.ID) as minID
					
				FROM $tblbyr_inv_detail bid 
				INNER JOIN $tblbyr_inv bi ON bi.ID = bid.invID
				INNER JOIN $tblbyr_inv_category bic ON bic.BICID = bid.BICID
				INNER JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
				INNER JOIN tblorder od ON od.Orderno = sp.Orderno
				INNER JOIN tblset se ON od.Qunit = se.ID
				-- INNER JOIN tblgarment g ON find_in_set(g.garmentID, sp.StyleNo)
				-- LEFT JOIN tblgender gd ON gd.ID = g.genderID
				-- LEFT JOIN tblproducttype ptt ON ptt.ID = g.gmttype
				-- INNER JOIN tblquotacat qtc ON qtc.ID = bid.quotaID
				LEFT JOIN tblcurrency cur ON cur.ID = od.currencyID
				-- INNER JOIN tblnewenquiry ne ON ne.QDID = g.QDID
				WHERE bid.invID='$id' AND bid.del='0' AND bid.group_number>0
				group by bid.BICID, bid.shipmentpriceID $query_filter
				order by sp.GTN_buyerpo, bic.options asc, bid.ID asc) as tbl 
				order by options, minID";
		// echo "<pre>$sqlInv</pre>";
		$stmt_inv = $this->conn->prepare($sqlInv);
		$stmt_inv->execute();
		while($row_inv = $stmt_inv->fetch(PDO::FETCH_ASSOC)){
			$shippeddate     = $row_inv["shippeddate"];
			$Orderno         = $row_inv["Orderno"];
			$gender          = $row_inv["gender"];
			$product_type    = $row_inv["product_type"];
			$shipmentpriceID = $row_inv["shipmentpriceID"];
			$fob_price       = $row_inv["fob_price"];
			$BuyerPO         = $row_inv["BuyerPO"];
			$BuyerPO         = (trim($row_inv["actual_BuyerPO"])==""? trim($BuyerPO): trim($row_inv["actual_BuyerPO"]));
			$uom             = $row_inv["uom"];
			$setID           = $row_inv["setID"];
			$styleNo         = $row_inv["styleNo"];
			$quotacat        = $row_inv["quotacat"];
			$ht_code         = $row_inv["ht_code"];
			$FactoryID       = $row_inv["FactoryID"];
			// $ship_marking    = $row_inv["shipping_marking"];
			$CurrencyCode    = $row_inv["CurrencyCode"];
			$BICID           = $row_inv["BICID"];
			$options         = $row_inv["options"];
			$BuyerID         = $row_inv["BuyerID"];
			$sp_garmentID    = $row_inv["sp_garmentID"];
			$str_ial = "";//($FactoryID=="F22"?"_ial":"");
			
			// echo "- quotacat: $quotacat << <br/>";
			
			if(!in_array($BuyerPO, $arr_PO)){
				$arr_PO[]  = $BuyerPO;
				$arr_info  = array();
				$grand_gw  = 0;
				$grand_nw  = 0;
				$grand_nnw = 0;
				$total_ctn = 0;
				$grand_cbm = 0;
				$grand_qty = 0;
				// echo "======= RESET =========<br/>";
			}
			
			$sqlhts = "SELECT ht_code, shipping_marking
						FROM $tblbuyer_invoice_hts  bih 
                        INNER JOIN $tblbyr_inv_category bic oN bic.BICID = bih.BICID
						WHERE bih.invID='$id' AND bih.shipmentpriceID='$shipmentpriceID' AND bih.ht_code!='' AND bic.del=0";
			$stmt_hts = $this->conn->prepare($sqlhts);
			$stmt_hts->execute();
			$row_hts = $stmt_hts->fetch(PDO::FETCH_ASSOC);
				$ht_code = $row_hts["ht_code"];
				$ship_marking = $row_hts["shipping_marking"];
			
			$sp_garmentID = str_replace(",","','", $sp_garmentID);
			$sqlgmt = "SELECT GROUP_CONCAT(DISTINCT g.styleNo) as style,
							gd.Description as gender, ptt.Description as product_type
						FROM `tblgarment` g 
						LEFT JOIN tblgender gd ON gd.ID = g.genderID
						LEFT JOIN tblproducttype ptt ON ptt.ID = g.gmttype
						WHERE g.garmentID IN ('$sp_garmentID')";
			$stmt_gmt = $this->conn->prepare($sqlgmt);
			$stmt_gmt->execute();
			$row_gmt  = $stmt_gmt->fetch(PDO::FETCH_ASSOC);
				$styleNo      = $row_gmt["style"];
				$gender       = $row_gmt["gender"];
				$product_type = $row_gmt["product_type"];
			
			$sqlmp = "SELECT mmd.FabricContent as fab_order, ct.Description as fab_country
					 FROM tblmpurchase mp
					 INNER JOIN tblmpurchase_detail mpd ON mpd.MPID = mp.MPID
					 INNER JOIN tblmpo_detail mpod ON mpod.MPDID = mpd.MPDID
					 INNER JOIN tblmm_color mmc ON mmc.MMCID = mpd.MMCID
					 INNER JOIN tblmm_detail mmd ON mmd.MMID = mmc.MMID
					 INNER JOIN tblmpo_header mpoh ON mpoh.MPOHID = mpod.MPOHID
					 INNER JOIN tblsupplier spp ON spp.SupplierID = mpoh.supplierID
					 LEFT JOIN tblcountry ct ON ct.ID = spp.countryID
					 WHERE mp.orderno = '$Orderno' AND mp.part=1 limit 1";
			$stmt_mp = $this->conn->prepare($sqlmp);
			$stmt_mp->execute();
			$row_mp = $stmt_mp->fetch(PDO::FETCH_ASSOC);
			$fab_order    = $row_mp["fab_order"];
			$fab_country  = $row_mp["fab_country"];
			$ship_marking = ($ship_marking==""? "$gender $fab_order $product_type": $ship_marking);
			$skip_cih = "";
			
			$this->BICID = $BICID;
			if($BuyerID=="B13" && $this->isBuyerPayment==1 && glb_profile=="iapparelintl"){
				$arr_list  = $this->getBuyerInvoiceJFPackingListDataFromCartonInv($shipmentpriceID, $id, $tblctn, $tblbyr_inv_detail);
			}
			else{
				$arr_list  = $this->getBuyerInvoicePackingListDataFromCartonInv($shipmentpriceID, $id, $tblctn, $tblbyr_inv_detail, $arr_info, $grand_gw, $grand_nw, $grand_nnw, $total_ctn, $grand_cbm, $grand_qty);
			}
			
			// echo "[$shipmentpriceID] $BuyerID / ".$this->isBuyerPayment." / ".glb_profile."<br/>";
			$arr_all_size_ctn     = $arr_list["arr_all_size_ctn"];
			$arr_ctn_measurement  = $arr_list["arr_ctn_measurement"];
			$arr_skucolorsize     = $arr_list["arr_skucolorsize"];
			$arr_skuOnly          = $arr_list["arr_skuOnly"];
			$arr_info  = $arr_list["arr_Prepackname"];
			$arr_FOB   = $arr_list["arr_FOBPrice"];
			
			$grand_gw  = $arr_list["grand_gw"];
			$grand_nw  = $arr_list["grand_nw"];
			$grand_nnw = $arr_list["grand_nnw"];
			$total_ctn = $arr_list["ctn_qty"];
			$this_total_ctn = $arr_list["this_total_ctn"];
			$grand_cbm = $arr_list["grand_cbm"];
			$grand_qty = $arr_list["grand_qty"];
			
			// echo "[$shipmentpriceID / $BuyerPO] BICID: $BICID grand_gw: $grand_gw << <br/>";
			
			$arr_all_size      = $arr_list["arr_all_size"];
			$arr_group_number  = $arr_list["arr_group_number"];
			$this_arr_list     = $arr_list["arr_list"];
			
			$arr_all["$BICID-$shipmentpriceID"] = $arr_list;
			
			if($shipmentpriceID==50741){
			// echo "$BICID - $shipmentpriceID / $total_ctn / $this_total_ctn << <br/>";
				// print_r($arr_info);
			}
			//print_r($arr_info);
			// echo "$shipmentpriceID / $id / $tblctn / $tblbyr_inv_detail / $total_ctn<br/>";
			
			$this_buyerpo = ($BuyerID=="B36"? "$BuyerPO%%$BICID" :"$BuyerPO%%$Orderno");//if AEO
			
			$arr_buyerpo["byBuyerPO"]["$this_buyerpo"] = array("shipmentpriceID"=>"$shipmentpriceID","od_FactoryID"=>$FactoryID, 
											"styleNo"=>"$styleNo","total_ctn"=>"$total_ctn", "this_total_ctn"=>$this_total_ctn, "quotacat"=>"$quotacat", "ht_code"=>$ht_code,
											"fab_order"=>"$ship_marking", "fob_price"=>$fob_price, "ship_marking"=>"$ship_marking",
											"count_row"=>count($arr_info), "arr_info"=>$arr_info, "product_type"=>$product_type, "fab_country"=>$fab_country,
											"grand_qty"=>$grand_qty, "grand_cbm"=>$grand_cbm, 
											"grand_nnw"=>$grand_nnw, "grand_nw"=>$grand_nw, "grand_gw"=>$grand_gw, "uom"=>$uom,
											"CurrencyCode"=>$CurrencyCode, "arr_all_size_ctn"=>$arr_all_size_ctn, 
											"arr_group_number"=>$arr_group_number, "arr_list"=>$this_arr_list, 
											"shippeddate"=>$shippeddate, "setID"=>$setID);
											
			$arr_buyerpo["byFabric"]["$ship_marking"][] = array("arr_FOB"=>$arr_FOB, "arr_all_size"=>$arr_all_size, "arr_skuOnly"=>$arr_skuOnly,
																"arr_group_number"=>$arr_group_number, "arr_skucolorsize"=>$arr_skucolorsize,
																"BuyerPO"=>$BuyerPO, "styleNo"=>$styleNo, "quotacat"=>"$quotacat", "ht_code"=>$ht_code,
																"total_ctn"=>$total_ctn, "this_total_ctn"=>$this_total_ctn, "shipmentpriceID"=>$shipmentpriceID, "Orderno"=>$Orderno,
																"grand_nnw"=>$grand_nnw, "grand_nw"=>$grand_nw, "grand_gw"=>$grand_gw, "grand_cbm"=>$grand_cbm, "uom"=>$uom, "grand_qty"=>$grand_qty, "arr_ctn_measurement"=>$arr_ctn_measurement, "product_type"=>$product_type,
																"CurrencyCode"=>$CurrencyCode, "fab_order"=>$fab_order, "fab_country"=>$fab_country,);
			
			$this_sp_bicid = "$shipmentpriceID-$BICID";
			if(!in_array($this_sp_bicid, $arr_shipID)){
				array_push($arr_shipID, $this_sp_bicid);
				$grand_inv_gw  += $grand_gw;
				$grand_inv_nw  += $grand_nw;
				$grand_inv_nnw += $grand_nnw;
				$grand_inv_ctn += $total_ctn;
				$grand_inv_cbm += $grand_cbm;
				$grand_inv_qty += $grand_qty;
			}
			
			
			
		}//--- End While ---//
		
		$arr_buyerpo["grand_inv_gw"]  = $grand_inv_gw;
		$arr_buyerpo["grand_inv_nw"]  = $grand_inv_nw;
		$arr_buyerpo["grand_inv_nnw"] = $grand_inv_nnw;
		$arr_buyerpo["grand_inv_ctn"] = $grand_inv_ctn;
		$arr_buyerpo["grand_inv_cbm"] = $grand_inv_cbm;
		$arr_buyerpo["grand_inv_qty"] = $grand_inv_qty;
		$arr_buyerpo["arr_all"] = $arr_all;
		
		return $arr_buyerpo;
	}
	
	public $istransit = 0;
	public function getBuyerInvoicePackingListData($spID, $skip_cih="", $invID="", $module="invoice", $container_no="", $isexpand=""){ //buyer_inv.php, buyer_inv_save.php, ajax_custom.php, ajax_get_shipment.php, ajax_table_buyerpo.php, model/tblbuyer_invoice_payment_detail, logistic/loading_planClass.php
		$arr_list = array();
		
		$sql_cphp = "SELECT * 
					FROM tblcarton_picklist_head_prod cphp 
					INNER JOIN tblshipmentprice_prod spp ON spp.ID = cphp.shipmentpriceID
					WHERE cphp.shipmentpriceID='$spID' AND spp.statusID=1";
		$stmt_cphp = $this->conn->prepare($sql_cphp);
		$stmt_cphp->execute();
		$cphp_exist = $stmt_cphp->rowCount();
		
		$sql = "SELECT * FROM tblcarton_picklist_head WHERE shipmentpriceID='$spID'";
		$stmt_cph = $this->conn->prepare($sql);
		$stmt_cph->execute();
		$cph_exist = $stmt_cph->rowCount();
		
		//======================================================//
		//---------------- Commercial Invoice ------------------//
		//======================================================//
		$this->model_carton_inv_head->shipmentpriceID = $spID;
		$this->model_carton_inv_head->invID = $invID;
		$this->model_carton_inv_head->BICID = $this->BICID;
		$cih_exist = $this->model_carton_inv_head->checkBuyerPOExist();
		$cih_existInOtherInvoice = $this->model_carton_inv_head->checkBuyerPOExistInOtherInvoice();
		
		//======================================================//
		//>>>>>>>>>>>>>> Buyer Payment Invoice <<<<<<<<<<<<<<<<<//
		//======================================================//
		if($this->isBuyerPayment!=100){
			$this->model_carton_inv_payment_head->shipmentpriceID = $spID;
			$this->model_carton_inv_payment_head->invID           = $invID;
			$this->model_carton_inv_payment_head->BICID           = $this->BICID;
			$ciph_exist_payment       = $this->model_carton_inv_payment_head->checkBuyerPOExist();
			$ciph_existInOtherInvoice = $this->model_carton_inv_payment_head->checkBuyerPOExistInOtherInvoice();
		}
		
		// echo "spID: $spID / invID: $invID BICID: ".$this->BICID."/ $ciph_existInOtherInvoice / $skip_cih / $cphp_exist / ciph_exist_payment: $ciph_exist_payment <br/>";
		
		//======================================================//
		//---------------- Commercial Invoice ------------------//
		//======================================================//
		//-------- If Packing List already save in Commercial Invoice Packing List --------//
		if($cih_exist>0 && $skip_cih=="" && trim($container_no)==""){
			$arr_all = $this->getBuyerInvoicePackingListDataFromCartonInv($spID, $invID);
			$arr_list = $arr_all["arr_list"];
			// echo "A";
		}
		//-------- If Packing List already save in Other Commercial Invoice, but still hv balance and current commercial invoice not yet save --------//
		else if(($cih_exist==0 && $cih_existInOtherInvoice>0 && $skip_cih=="" && trim($container_no)=="") || ($cih_existInOtherInvoice>0 && $skip_cih=="true" && trim($container_no)=="")){
			$str_prod = ($cphp_exist>0? "_prod":"");
			
			$arr_list = $this->getBuyerInvoicePackingListDataFromBalanceQty($invID, $spID, $str_prod);
			// echo "B [$spID] / $cih_existInOtherInvoice / $str_prod <br/>";
		}
		//-------- Get Packing List default from Cutting Pick List --------//
		else if(($cphp_exist>0 && $skip_cih<>'pickpack' && $skip_cih<>'fullqtypickpack' && $skip_cih<>"oripickpackonly" && $skip_cih<>"shippingweeklyplan" && trim($container_no)=="") || ($skip_cih=="full_qty" && $cphp_exist>0 && trim($container_no)=="")){
			$arr_list = $this->getBuyerInvoicePackingListDataFromPlanning($spID, "_prod");
			// echo "C [$cphp_exist] / $skip_cih / $spID [$cih_exist] <br/>";
		}
		else if(($cph_exist>0 && $skip_cih<>'pickpack' && $skip_cih<>'fullqtypickpack' && $skip_cih<>"oripickpackonly" && $skip_cih<>"shippingweeklyplan" && trim($container_no)=="") || ($skip_cih=="full_qty" && $cph_exist>0 && trim($container_no)=="")){
			$arr_list = $this->getBuyerInvoicePackingListDataFromPlanning($spID, "");
			// echo "D <br/>";
		}
		//======================================================//
		//>>>>>>>>>>>>>> Buyer Payment Invoice <<<<<<<<<<<<<<<<<//
		//======================================================//
		else if(($ciph_exist_payment>0 && $skip_cih=="pickpack" && trim($container_no)=="") ){// exist in payment invoice packing list 
			$arr_all  = $this->getBuyerInvoicePackingListDataFromCartonInv($spID, $invID, "tblcarton_inv_payment_head", "tblbuyer_invoice_payment_detail");
			$arr_list = $arr_all["arr_list"];
			
			// echo "E <br/>";
		}
		//--- Balance Qty deduct from other invoice ---//
		else if(($ciph_exist_payment==0 && $ciph_existInOtherInvoice>0 && $skip_cih=="pickpack" && trim($container_no)=="") || 
					($ciph_existInOtherInvoice>0 && $skip_cih=="fullqtypickpack" && trim($container_no)=="")){
			$arr_list = $this->getBuyerPaymentInvoicePackingListDataFromBalanceQty($invID, $spID);
			// echo "F $invID<br/>";
		}
		else if(($ciph_exist_payment==0 && $skip_cih=="pickpack" && trim($container_no)=="") || ($skip_cih=="fullqtypickpack" && trim($container_no)=="") || ($skip_cih=="oripickpack" && trim($container_no)=="")){ // Use Default packing list
			// $sql = "SELECT od.FactoryID
					// FROM tblshipmentprice sp 
					// INNER JOIN tblorder od ON od.Orderno = sp.Orderno
					// WHERE sp.ID='$spID'";
			// $stmt_fty = $this->conn->prepare($sql);
			// $stmt_fty->execute();
			// $rowfty = $stmt_fty->fetch(PDO::FETCH_ASSOC);
			
			$filter_query = " AND cpt.ct_pat=1"; $factoryID = ""; 
			// $factoryID    = $rowfty["FactoryID"];
			$arr_all  = $this->handle_shipment->getAllPackingInfoByBuyerPO($spID, $factoryID, $filter_query);
			$arr_list = $arr_all["arr_row"];
			
			// echo "G <br/>";
		}
		else if($skip_cih=="oripickpackonly" && trim($container_no)==""){ // Use Default packing list
			// $sql = "SELECT od.FactoryID
					// FROM tblshipmentprice sp 
					// INNER JOIN tblorder od ON od.Orderno = sp.Orderno
					// WHERE sp.ID='$spID'";
			// $stmt_fty = $this->conn->prepare($sql);
			// $stmt_fty->execute();
			// $rowfty = $stmt_fty->fetch(PDO::FETCH_ASSOC);
			
			$filter_query = " "; $factoryID = ""; //AND cpt.ct_pat=1
			// $factoryID    = $rowfty["FactoryID"];
			$arr_all  = $this->handle_shipment->getAllPackingInfoByBuyerPO($spID, $factoryID, $filter_query);
			$arr_list = $arr_all["arr_row"];
			
			// echo "H <br/>";
		}
		//========================================================//
		//---------------- Shipping Weekly Plan ------------------//
		//========================================================//
		else if($skip_cih=="shippingweeklyplan" && $invID!="" && $invID!="0" && trim($container_no)==""){
			$this->isBuyerPayment = 100; //from shipping weekly plan
			$arr_all = $this->getBuyerInvoicePackingListDataFromCartonInv($spID, $invID);
			$arr_list = $arr_all["arr_list"];
			
			// echo "I << <br/>";
		}
		//========================================================//
		//------------------ Load By Container -------------------//
		//========================================================//
		else if(trim($container_no)!=""){
			$filter_query = " AND ch.container_no ='$container_no' "; $factoryID = ""; 
			$arr_all  = $this->handle_shipment->getAllExfactoryPackingInfoByBuyerPO($spID, $factoryID, $filter_query);
			$arr_list = $arr_all["arr_row"];
		}
				
		return $arr_list;
	}
	
	public function getBuyerInvoicePackingListDataFromCartonInv($spID, $invID, $tblctn="tblcarton_inv_head", $tblinvdetail="tblbuyer_invoice_detail", $arr_Prepackname=array(), $grand_gw=0, $grand_nw=0, $grand_nnw=0, $ctn_qty=0, $grand_cbm=0, $grand_qty=0){ //buyer_kohls.php, buyer_inv_excel.php
		$arr_list_detail = array();
		// $arr_Prepackname = array();
		$arr_skucolorsize = array();
		$arr_skuOnly = array();
		$arr_FOBPrice = array();
		$arr_ctn_measurement = array();
		$arr_all_garment = array();
		$arr_all_garment_ctn = array();
		$arr_all_garment_gw = array();
		$arr_all_garment_nw = array();
		$arr_all_size = array();
		$arr_all_size_wfob = array();
		$arr_all_size_ctn = array();
		$arr_group_number = array();
		$arr_all_color_qty = array();
		$arr_all_color_ctn = array();
		$arr_all_color_gw = array();
		$arr_all_color_nw = array();
		$arr_all_color_cbm = array();
		$arr_all_size_color = array();
		
		$arr_all_gw_wfobcol = array();
		$arr_all_gw_wfob = array();
		$arr_all_nw_wfobcol = array();
		$arr_all_nw_wfob = array();
		$arr_all_cbm_wfobcol = array();
		$arr_all_cbm_wfob = array();
		// $ctn_qty = 0;
		// $grand_nw = 0;
		// $grand_nnw = 0;
		// $grand_gw = 0;
		// $grand_qty = 0;
		// $grand_cbm = 0;
		$grand_qty_in_pcs = 0;
		$shipping_marking = "";
		$this_total_ctn = 0;
		
		$tblctn       = ($this->isBuyerPayment==0? "tblcarton_inv_head":"tblcarton_inv_payment_head");
		$tblctndetail = ($this->isBuyerPayment==0? "tblcarton_inv_detail":"tblcarton_inv_payment_detail");
		$tblinvdetail = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail":"tblbuyer_invoice_payment_detail");
		$this->BICID  = ($this->BICID==""? 0: $this->BICID);
		
		if($this->isBuyerPayment==100){//shipment weekly container plan used
			$sql = "SELECT cih.CIHID, cih.PID, cih.SKU, cih.prepack_name, cih.masterID, cih.start, cih.end_num, cih.is_last, cih.total_ctn, count(DISTINCT cih.masterID) as count_master,
							cih.mixID, cih.total_qty_in_carton, cih.net_net_weight, cih.net_weight, cih.gross_weight, cih.weight_unitID,
							cih.ext_length, cih.ext_width, cih.ext_height, cih.ctn_unitID, cih.total_CBM, 
							'' as shipping_marking,
							cih.blisterbag_qty as blisterbag_in_carton, spk.packing_method, '' as class_description
					FROM tblship_weekly_carton_head cih 
					INNER JOIN tblship_weekly_carton_detail cid ON cid.CIHID = cih.CIHID
					LEFT JOIN tblship_packing spk ON spk.PID = cih.PID
					WHERE cih.shipmentpriceID='$spID' AND cih.invID='$invID' AND cih.del=0 AND cih.total_ctn>0 AND cid.del=0 
					group by cih.CIHID
					order by cih.start asc";
		}
		else{ //Commercial and Buyer Payment Invoice
			$sql = "SELECT cih.CIHID, cih.PID, cih.SKU, cih.prepack_name, cih.masterID, cih.start, cih.end_num, cih.is_last, cih.total_ctn, count(DISTINCT cih.masterID) as count_master,
							cih.mixID, cih.total_qty_in_carton, cih.net_net_weight, cih.net_weight, cih.gross_weight, cih.weight_unitID,
							cih.ext_length, cih.ext_width, cih.ext_height, cih.ctn_unitID, cih.total_CBM, 
							group_concat(distinct bid.shipping_marking separator ' / ') as shipping_marking,
							cih.blisterbag_qty as blisterbag_in_carton, spk.packing_method, bid.class_description
					FROM $tblctn cih 
					INNER JOIN $tblctndetail cid ON cid.CIHID = cih.CIHID
					LEFT JOIN tblship_packing spk ON spk.PID = cih.PID
					INNER JOIN $tblinvdetail bid ON bid.invID = cih.invID 
														AND bid.shipmentpriceID = cih.shipmentpriceID AND bid.del=0
														AND bid.group_number = cid.group_number
					WHERE cih.shipmentpriceID='$spID' AND cih.invID='$invID' AND cih.del=0 AND cih.BICID IN (".$this->BICID.") AND cih.total_ctn>0 AND cid.del=0 
					group by cih.CIHID
					order by cih.start asc";
		}
		// echo "<pre>$sql</pre>";
		$stmt_cphp = $this->conn->prepare($sql);
		$stmt_cphp->execute();
		$count_cphp = $stmt_cphp->rowCount();
		
		if($spID==52588){
			// echo "<pre>$sql</pre> <br/>";
		}
		
		if($count_cphp>0){
			while($row_cphp = $stmt_cphp->fetch(PDO::FETCH_ASSOC)){
				extract($row_cphp);
				
				// $gross_weight = round($gross_weight, 1);
				$net_weight   = ($this->isBuyerPayment==100? $net_weight: round($net_weight, 2));
				
				$arr_list_detail = array();
				$arr_grp_color   = array();
				$arr_size_info   = array();
				$arr_temp = array();
				$arr_pd = ($mixID==""? array(): explode("::^^", $mixID));
				$new_range = "";
				$new_range2 = "";
				//echo "Mix:$mixID <br/>";
				for($i=0;$i<count($arr_pd);$i++){
					list($group_number, $size_name, $qty) = explode("**%%", $arr_pd[$i]);
					
					$arr_list_detail[] = array("size_name"=>$size_name, "group_number"=>$group_number, "qty"=>$qty);
					
					$count_accgmt = 0;
					if($this->isBuyerPayment!=100){ //from shipping weekly plan
						$arr_fob = $this->getPOPrice($spID, $group_number, $size_name, $invID);
						$fob_price   = $arr_fob["fob_price"];
						$color       = $arr_fob["color"];
						$garmentID   = $arr_fob["garmentID"];
						$colorID     = $arr_fob["colorID"];
						$upc_code    = $arr_fob["upc_code"];
						$quota       = $arr_fob["quota"];
						$ht_code     = $arr_fob["ht_code"];
						$colorOnly   = $arr_fob["colorOnly"];
						$garmentOnly = $arr_fob["garmentOnly"];
						$this_shipping_marking = $arr_fob["shipping_marking"];
						$count_color_in_grp    = $arr_fob["count_color_in_grp"]; 
						$count_accgmt    = $arr_fob["count_accgmt"]; 
					}
					
					/////////////////////////////////////////////////////////////
					//////////--- Array Store Color Size Total Qty ---///////////
					$gs_qty = $qty * $total_ctn;
					if (array_key_exists("$group_number**^^$size_name", $arr_all_size)){
						$arr_all_size["$group_number**^^$size_name"] += $gs_qty; 
					}
					else{
						$arr_all_size["$group_number**^^$size_name"] = $gs_qty;
					}
					
					if (array_key_exists("$group_number**^^$size_name**^^$fob_price", $arr_all_size_wfob)){
						$arr_all_size_wfob["$group_number**^^$size_name**^^$fob_price"] += $gs_qty; 
					}
					else{
						$arr_all_size_wfob["$group_number**^^$size_name**^^$fob_price"] = $gs_qty;
					}
					
					if(array_key_exists("$garmentID", $arr_all_garment)){
						$arr_all_garment["$garmentID"] += $gs_qty; 
					}
					else{
						$arr_all_garment["$garmentID"] = $gs_qty; 
					}
					
					if(array_key_exists("$group_number", $arr_all_color_qty)){
						$arr_all_color_qty["$group_number"] += $gs_qty;
					}
					else{
						$arr_all_color_qty["$group_number"] = $gs_qty;
					}
					
					// if($spID==27911)
					// echo "$CIHID - [$garmentID] $total_ctn - $packing_method - $i <br/>";
					
					if(!in_array($garmentID, $arr_temp)){//&& $garmentID==4765
						if(array_key_exists("g$garmentID", $arr_all_garment_ctn) && 
							($packing_method==1 || $packing_method==2 || ($packing_method==50 && $i==0))){
							$arr_all_garment_ctn["g$garmentID"] += $total_ctn;
						}
						else if(!($packing_method==50 && $i>0)){
							$arr_all_garment_ctn["g$garmentID"] = $total_ctn;
						}
						$arr_temp[] = $garmentID;
						
					}
					
					if (array_key_exists("$group_number**^^$size_name", $arr_all_size_ctn)){
						$arr_all_size_ctn["$group_number**^^$size_name"]["qty"] += $gs_qty; 
					}
					else{
						$arr_all_size_ctn["$group_number**^^$size_name"] = array("qty"=>$gs_qty, "color"=>$color, "fob_price"=>$fob_price, 
																				"total_ctn"=>"0", "SKU"=>$SKU, "prepack_name"=>$prepack_name,
																				"quota"=>$quota, "ht_code"=>$ht_code, "upc_code"=>$upc_code,
																				"this_shipping_marking"=>$this_shipping_marking);
					}
					
					if($new_range==""){// to store color contains how many carton qty
						if (array_key_exists("$group_number", $arr_all_color_ctn)){
							$arr_all_color_ctn["$group_number"] += $total_ctn;
							$arr_all_color_gw["$group_number"]  += $gross_weight * $total_ctn;
							$arr_all_color_nw["$group_number"]  += $net_weight * $total_ctn;
							$arr_all_color_cbm["$group_number"] += $total_CBM;
							// $arr_all_color_qty["$group_number"] += $gs_qty;
						}
						else{
							$arr_all_color_ctn["$group_number"] = $total_ctn;
							$arr_all_color_gw["$group_number"]  = $gross_weight * $total_ctn;
							$arr_all_color_nw["$group_number"]  = $net_weight * $total_ctn;
							$arr_all_color_cbm["$group_number"] = $total_CBM;
							// $arr_all_color_qty["$group_number"] = $gs_qty;
							
						}
						
						$new_range = "No";
					}
					
					if (array_key_exists("$SKU**^^$group_number**^^$size_name", $arr_skucolorsize)){
						$arr_skucolorsize["$SKU**^^$group_number**^^$size_name"]["qty"] += $gs_qty;
					}
					else{
						$arr_skucolorsize["$SKU**^^$group_number**^^$size_name"] = array("qty"=>$gs_qty, "color"=>$color, "fob_price"=>$fob_price);
					}
					
					if (array_key_exists("$SKU", $arr_skuOnly)){
						if (array_key_exists("$group_number**^^$size_name", $arr_skuOnly["$SKU"])){
							$arr_skuOnly["$SKU"]["$group_number**^^$size_name"]["qty"] += $gs_qty;
						}
						else{
							$arr_skuOnly["$SKU"]["$group_number**^^$size_name"] = array("qty"=>$gs_qty, "color"=>$color, "fob_price"=>$fob_price, "upc_code"=>$upc_code);
						}
					}
					else{
						$arr_skuOnly["$SKU"]["$group_number**^^$size_name"] = array("qty"=>$gs_qty, "color"=>$color, "fob_price"=>$fob_price, "upc_code"=>$upc_code);
					}
					
					///////////////////////////////////////////////////////////
					////////////--- Array Store Color & SKU ---////////////////
					if (!in_array("$group_number**%%^^$SKU", $arr_grp_color)){
						array_push($arr_grp_color, "$group_number**%%^^$SKU");
					}
					$arr_size_info["$group_number"]["$size_name"] = $qty;
					
					if(array_key_exists("g$group_number**^^$color", $arr_all_size_color)){
						if(array_key_exists("$size_name", $arr_all_size_color["g$group_number**^^$color"])){
							$arr_all_size_color["g$group_number**^^$color"]["$size_name"] += ($qty * $total_ctn);
						}
						else{
							$arr_all_size_color["g$group_number**^^$color"]["$size_name"] = ($qty * $total_ctn);
						}
					}
					else{
						$arr_all_size_color["g$group_number**^^$color"]["$size_name"] = ($qty * $total_ctn);
					}
					
					//////////////////////////////////////////////////////////////
					////////////////--- Array Based On SKU ---////////////////////
					$prepack_qty = ($qty * $total_ctn);
					if (array_key_exists("$SKU**^^$group_number", $arr_Prepackname)){
						$arr_Prepackname["$SKU**^^$group_number"]["qty"] += $prepack_qty;
					}
					else{
						$arr_Prepackname["$SKU**^^$group_number"] = array("qty"=>$prepack_qty, "color"=>$color, "fob_price"=>$fob_price, "colorOnly"=>$colorOnly, "garmentID"=>$garmentID);
					}
					/////////// End Array Based On SKU //////////
					/////////////////////////////////////////////
					
					if (array_key_exists("G$group_number", $arr_group_number)){
						$arr_group_number["G$group_number"]["qty"] += $prepack_qty;
						
					}
					else{
						$arr_group_number["G$group_number"] = array("qty"=>$prepack_qty, "color"=>$color, "fob_price"=>$fob_price, 
																	"garmentID"=>$garmentID, "colorID"=>$colorID, 
																	"colorOnly"=>$colorOnly, "garmentOnly"=>$garmentOnly, 
																	"total_ctn"=>"0", "class_description"=>$class_description);
																	
					}
					
					//echo "=====> $group_number - $prepack_qty <br/>";
					
					//////////////////////////////////////////////////////////////
					//////////////--- Array Based On PO Price ---/////////////////
					if (array_key_exists("F$fob_price", $arr_FOBPrice)){
						$arr_FOBPrice["F$fob_price"]["qty"] += $prepack_qty;
					}
					else{
						$arr_FOBPrice["F$fob_price"] = array("qty"=>$prepack_qty, "color"=>$color);
					}
					//////// End Array Based On PO Price ////////
					/////////////////////////////////////////////
					
				}//-- End for carton detail --//
				
				
				$arr_all_size_ctn["$group_number**^^$size_name"]["total_ctn"] += $total_ctn; 
				if (array_key_exists("^^$fob_price", $arr_all_gw_wfob)){
					$arr_all_gw_wfob["^^$fob_price"] += ($gross_weight * $total_ctn); 
					// echo " [2] $fob_price / [$gross_weight x $total_ctn] <<<< <br/>";
				}
				else{
					$arr_all_gw_wfob["^^$fob_price"] = ($gross_weight * $total_ctn);
					// echo " [1] $fob_price / [$gross_weight x $total_ctn] <<<< <br/>";
				}
				
				if (array_key_exists("^^$fob_price-$colorID", $arr_all_gw_wfobcol)){
					$arr_all_gw_wfobcol["^^$fob_price-$colorID"] += ($gross_weight * $total_ctn); 
					// echo " [2] $fob_price / [$gross_weight x $total_ctn] = ".$arr_all_gw_wfobcol["^^$fob_price-$colorID"]." <<<< <br/>";
				}
				else{
					$arr_all_gw_wfobcol["^^$fob_price-$colorID"] = ($gross_weight * $total_ctn);
					// echo " [1] $fob_price / [$gross_weight x $total_ctn] = ".$arr_all_gw_wfobcol["^^$fob_price-$colorID"]." <<<< <br/>";
				}
				
				if (array_key_exists("^^$fob_price", $arr_all_nw_wfob)){
					$arr_all_nw_wfob["^^$fob_price"] += ($net_weight * $total_ctn); 
				}
				else{
					$arr_all_nw_wfob["^^$fob_price"] = ($net_weight * $total_ctn);
				}
				
				if (array_key_exists("^^$fob_price-$colorID", $arr_all_nw_wfobcol)){
					$arr_all_nw_wfobcol["^^$fob_price-$colorID"] += ($net_weight * $total_ctn); 
				}
				else{
					$arr_all_nw_wfobcol["^^$fob_price-$colorID"] = ($net_weight * $total_ctn);
				}
				
				$one_cbm = round(($ext_length/100) * ($ext_width/100) * ($ext_height/100), 3);
				// echo "spID: $spID | one_cbm: $one_cbm = [$ext_length x $ext_width x $ext_height] x $total_ctn<< ($colorID) <br/>";
				if (array_key_exists("^^$fob_price", $arr_all_cbm_wfob)){
					$arr_all_cbm_wfob["^^$fob_price"] += ($one_cbm * $total_ctn); 
				}
				else{
					$arr_all_cbm_wfob["^^$fob_price"] = ($one_cbm * $total_ctn);
				}
				
				if (array_key_exists("^^$fob_price-$colorID", $arr_all_cbm_wfobcol)){
					$arr_all_cbm_wfobcol["^^$fob_price-$colorID"] += number_format($one_cbm * $total_ctn, 3); 
				}
				else{
					$arr_all_cbm_wfobcol["^^$fob_price-$colorID"] = ($one_cbm * $total_ctn);
				}
				
				// if($colorID==37597){
					// echo round($arr_all_cbm_wfobcol["^^$fob_price-$colorID"],3)." = $one_cbm x $total_ctn << <br/> ";
				// }
				
				
				$grp_ctn = $total_ctn;
				if(trim($masterID)!="" && trim($masterID)!="0"){
					
				}
				$arr_group_number["G$group_number"]["total_ctn"] += $grp_ctn;
				
				$one_cbm = ($ext_length/100) * ($ext_width/100) * ($ext_height/100);
				// $one_cbm = floor($one_cbm * 100000) / 100000;
				$one_cbm = round($one_cbm, 5);
				$total_CBM = $one_cbm * $total_ctn;
				
				$this_ctn_range = "$start-$end_num";
				$total_qty      = $total_ctn * $total_qty_in_carton;
				$this_nnw 		= $net_net_weight * $total_ctn;
				$this_nw  		= $net_weight * $total_ctn;
				$this_gw 	    = $gross_weight * $total_ctn;
				$cbm_total      = $total_CBM;
				$ctn_measurement = round($ext_length,1)." x ".round($ext_width,1)." x ".round($ext_height,1)." (cm)";
				
				if(array_key_exists("g$garmentID", $arr_all_garment_gw)){
					$arr_all_garment_gw["g$garmentID"] += ($gross_weight * $total_ctn);
					$arr_all_garment_nw["g$garmentID"] += ($net_weight * $total_ctn);
				}
				else{
					$arr_all_garment_gw["g$garmentID"] = ($gross_weight * $total_ctn);
					$arr_all_garment_nw["g$garmentID"] = ($net_weight * $total_ctn);
				}
				
				if (!in_array("$ctn_measurement", $arr_ctn_measurement)){
					array_push($arr_ctn_measurement, "$ctn_measurement");
					//echo "$ctn_measurement <=== <br/>";
				}
				
				$arr_list[] = array("CIHID"=>$CIHID, "PID"=>$PID, "SKU"=>$SKU, "prepack_name"=>$prepack_name, "masterID"=>$masterID,
									"start"=>$start, "end_num"=>$end_num, 
									"is_last"=>$is_last, "total_ctn"=>$total_ctn, "mixID"=>$mixID, "total_qty_in_carton"=>$total_qty_in_carton,
									"net_net_weight"=>$net_net_weight, "net_weight"=>$net_weight, "gross_weight"=>$gross_weight,
									"weight_unitID"=>$weight_unitID, "ext_length"=>$ext_length, "ext_width"=>$ext_width, "ext_height"=>$ext_height, "ctn_unitID"=>$ctn_unitID, "total_CBM"=>$total_CBM, "arr_list_detail"=>$arr_list_detail, "colorOnly"=>$colorOnly, "garmentOnly"=>$garmentOnly, "group_number"=>$group_number, "blisterbag_in_carton"=>$blisterbag_in_carton,
									
									"ctn_range"=>$this_ctn_range, "count_ctn"=>$total_ctn, "this_ctn_qty"=>$total_qty_in_carton,
									"this_nnw"=>$this_nnw, "this_nw"=>$this_nw, "this_gw"=>$this_gw,
									"total_qty"=>$total_qty, "cbm_total"=>$cbm_total, "ctn_measurement"=>$ctn_measurement, 
									"arr_grp_color"=>$arr_grp_color, "arr_size_info"=>$arr_size_info);
				
				$grand_nw  += ($net_weight * $total_ctn);
				$grand_nnw += ($net_net_weight * $total_ctn);
				$grand_gw  += ($gross_weight * $total_ctn);
				$grand_cbm += ($total_CBM);
				$ctn_qty   += $total_ctn;
				$this_total_ctn   += $total_ctn;
				$grand_qty += $total_qty_in_carton * $total_ctn;
				$grand_qty_in_pcs += ($total_qty_in_carton * $count_color_in_grp) * $total_ctn;
				
				if($spID==52587){
					// echo "<pre>$ctn_qty / $total_ctn / grand_qty: $grand_qty = $total_qty_in_carton x $total_ctn</pre> <br/>";
				}
				
			}//--- End While ---//
		}//--- End Count>0 ---//
		
		// echo "<hr/>"; arr_group_number
		//print_r($arr_Prepackname);
		$arr_all = array("arr_list"=>$arr_list, "grand_nw"=>$grand_nw, "grand_nnw"=>$grand_nnw, "grand_gw"=>$grand_gw, "grand_cbm"=>$grand_cbm,
						 "ctn_qty"=>$ctn_qty, "this_total_ctn"=>$this_total_ctn, "arr_Prepackname"=>$arr_Prepackname, "arr_ctn_measurement"=>$arr_ctn_measurement, "arr_FOBPrice"=>$arr_FOBPrice, "grand_qty"=>$grand_qty, "grand_qty_in_pcs"=>$grand_qty_in_pcs,
						 "arr_all_color_ctn"=>$arr_all_color_ctn, "arr_all_color_gw"=>$arr_all_color_gw, 
						 "arr_all_color_nw"=>$arr_all_color_nw, "arr_all_color_cbm"=>$arr_all_color_cbm, "arr_all_color_qty"=>$arr_all_color_qty,
						 "arr_all_size"=>$arr_all_size, "arr_group_number"=>$arr_group_number, "arr_skucolorsize"=>$arr_skucolorsize, "shipping_marking"=>$shipping_marking, "arr_all_size_color"=>$arr_all_size_color, "arr_skuOnly"=>$arr_skuOnly, "arr_all_size_ctn"=>$arr_all_size_ctn, "count_color_in_grp"=>$count_color_in_grp,
						 "arr_all_garment"=>$arr_all_garment, "arr_all_garment_ctn"=>$arr_all_garment_ctn, 
						 "arr_all_garment_gw"=>$arr_all_garment_gw, "arr_all_garment_nw"=>$arr_all_garment_nw,
						 "arr_all_size_wfob"=>$arr_all_size_wfob, "arr_all_gw_wfob"=>$arr_all_gw_wfob,
						 "arr_all_nw_wfob"=>$arr_all_nw_wfob, "arr_all_cbm_wfob"=>$arr_all_cbm_wfob,
						 "arr_all_gw_wfobcol"=>$arr_all_gw_wfobcol,
						 "arr_all_nw_wfobcol"=>$arr_all_nw_wfobcol, "arr_all_cbm_wfobcol"=>$arr_all_cbm_wfobcol);
		
		return $arr_all;
	}
	
	public function getBuyerInvoiceJFPackingListDataFromCartonInv($spID, $invID, $tblctn="tblcarton_inv_payment_head", $tblinvdetail="tblbuyer_invoice_payment_detail"){ //buyer_joefresh.php
		$arr_list_detail = array();
		$arr_Prepackname = array();
		$arr_skucolorsize = array();
		$arr_skuOnly = array();
		$arr_FOBPrice = array();
		$arr_ctn_measurement = array();
		$arr_all_garment = array();
		$arr_all_garment_ctn = array();
		$arr_all_garment_gw = array();
		$arr_all_garment_nw = array();
		$arr_all_size = array();
		$arr_all_size_wfob = array();
		$arr_all_size_ctn = array();
		$arr_group_number = array();
		$arr_all_color_qty = array();
		$arr_all_color_ctn = array();
		$arr_all_color_gw = array();
		$arr_all_color_nw = array();
		$arr_all_color_cbm = array();
		$arr_all_size_color = array();
		
		$arr_all_gw_wfob = array();
		$arr_all_nw_wfob = array();
		$arr_all_cbm_wfob = array();
		$ctn_qty = 0;
		$grand_nw = 0;
		$grand_nnw = 0;
		$grand_gw = 0;
		$grand_qty = 0;
		$grand_qty_in_pcs = 0;
		$grand_cbm = 0;
		$shipping_marking = "";
		
		$tblctn       = ($this->isBuyerPayment==0? "tblcarton_inv_head":"tblcarton_inv_payment_head");
		$tblctndetail = ($this->isBuyerPayment==0? "tblcarton_inv_detail":"tblcarton_inv_payment_detail");
		$tblinvdetail = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail":"tblbuyer_invoice_payment_detail");
		
		$sql = "SELECT tblcih.CIHID, tblcih.PID, tblcih.SKU, tblcih.prepack_name, tblcih.masterID, 
						MIN(tblcih.start) as start, MAX(tblcih.end_num) as this_end_num, 
						tblcih.is_last, sum(tblcih.total_ctn) as total_ctn, 
						count(DISTINCT tblcih.masterID) as count_master,
						tblcih.mixID, tblcih.total_qty_in_carton, 
						sum(tblcih.net_net_weight) as net_net_weight, sum(tblcih.net_weight) as net_weight, sum(tblcih.gross_weight) as gross_weight, tblcih.weight_unitID, tblcih.ext_length, tblcih.ext_width, tblcih.ext_height, tblcih.ctn_unitID, sum(tblcih.total_CBM) as total_CBM, 
						'' as shipping_marking, tblcih.blisterbag_qty as blisterbag_in_carton, tblcih.packing_method, '' as class_description

				FROM (SELECT  cih.CIHID, cih.invID, cih.BICID, cih.PID, cih.SKU, cih.prepack_name, cih.masterID, cih.start, cih.end_num, cih.is_last, (cih.total_ctn) as total_ctn, count(DISTINCT cih.masterID) as count_master,
				cih.mixID, cih.total_qty_in_carton, sum(cih.net_net_weight * cih.total_ctn) as net_net_weight, sum(cih.net_weight * cih.total_ctn) as net_weight, sum(cih.gross_weight * cih.total_ctn) as gross_weight, cih.weight_unitID,
				cih.ext_length, cih.ext_width, cih.ext_height, cih.ctn_unitID, sum(cih.total_CBM) as total_CBM, 
				'' as shipping_marking, cih.blisterbag_qty, spk.packing_method, 
				'' as class_description
				FROM `$tblctn` cih 
				LEFT JOIN tblship_packing spk ON spk.PID = cih.PID

				WHERE cih.shipmentpriceID = '{$spID}' AND cih.del=0 AND cih.total_ctn>0 
				AND cih.invID='{$invID}' AND cih.BICID IN ('{$this->BICID}') 
				group by cih.CIHID
				order by cih.start asc) as tblcih
				group by tblcih.invID, tblcih.BICID, tblcih.PID, tblcih.SKU, tblcih.prepack_name, tblcih.mixID, tblcih.total_qty_in_carton,
				tblcih.ext_length, tblcih.ext_width, tblcih.ext_height
				order by tblcih.start";
		$stmt_cphp = $this->conn->prepare($sql);
		$stmt_cphp->execute();
		$count_cphp = $stmt_cphp->rowCount();
		
		// echo "<pre>$sql</pre> <br/>";
		
		if($count_cphp>0){
			$start = 1; $end_num = 0;
			while($row_cphp = $stmt_cphp->fetch(PDO::FETCH_ASSOC)){
				extract($row_cphp);
				
				$net_net_weight = $net_net_weight / $total_ctn;
				$net_weight     = round($net_weight / $total_ctn, 2);
				$gross_weight   = round($gross_weight / $total_ctn,2);
				// $net_weight     = $net_weight / $total_ctn;
				// $gross_weight   = $gross_weight / $total_ctn;
				
				$start   = $end_num + 1;
				$end_num = $start + $total_ctn - 1;
				
				// echo "===>> GW: $gross_weight << <br/>";
				
				$arr_gp    = array(0);
				$arr_mixID = explode("::^^", $mixID);
				for($i=0;$i<count($arr_mixID);$i++){
					list($group_number, $size_name, $qty) = explode("**%%", $arr_mixID[$i]);
					$arr_gp[] = $group_number;
				}
				
				$str_gp = implode(",", $arr_gp);
				$sqlbid = "SELECT bid.class_description, 
									group_concat(distinct bid.shipping_marking separator ' / ') as shipping_marking
							FROM $tblinvdetail bid 
							WHERE bid.invID='{$invID}' AND bid.shipmentpriceID='{$spID}' 
							AND bid.group_number IN ({$str_gp})";
				$stmt_bid = $this->conn->prepare($sqlbid);
				$stmt_bid->execute();
				$row_bid = $stmt_bid->fetch(PDO::FETCH_ASSOC);
					$class_description = (isset($row_bid["class_description"])? $row_bid["class_description"]: "");
					$shipping_marking  = (isset($row_bid["shipping_marking"])? $row_bid["shipping_marking"]: "");
				
				// $gross_weight = round($gross_weight, 1);
				// $net_weight   = round($net_weight, 1);
				
				$arr_list_detail = array();
				$arr_grp_color   = array();
				$arr_size_info   = array();
				$arr_temp = array();
				$arr_pd = ($mixID==""? array(): explode("::^^", $mixID));
				$new_range = "";
				$new_range2 = "";
				//echo "Mix:$mixID <br/>";
				for($i=0;$i<count($arr_pd);$i++){
					list($group_number, $size_name, $qty) = explode("**%%", $arr_pd[$i]);
					
					$arr_list_detail[] = array("size_name"=>$size_name, "group_number"=>$group_number, "qty"=>$qty);
					
					if($this->isBuyerPayment!=100){ //from shipping weekly plan
						$arr_fob = $this->getPOPrice($spID, $group_number, $size_name, $invID);
						$fob_price   = $arr_fob["fob_price"];
						$color       = $arr_fob["color"];
						$garmentID   = $arr_fob["garmentID"];
						$colorID     = $arr_fob["colorID"];
						$upc_code    = $arr_fob["upc_code"];
						$quota       = $arr_fob["quota"];
						$ht_code     = $arr_fob["ht_code"];
						$colorOnly   = $arr_fob["colorOnly"];
						$garmentOnly = $arr_fob["garmentOnly"];
						$this_shipping_marking = $arr_fob["shipping_marking"];
						$count_color_in_grp    = $arr_fob["count_color_in_grp"]; 
					}
					
					/////////////////////////////////////////////////////////////
					//////////--- Array Store Color Size Total Qty ---///////////
					$gs_qty = $qty * $total_ctn;
					if (array_key_exists("$group_number**^^$size_name", $arr_all_size)){
						$arr_all_size["$group_number**^^$size_name"] += $gs_qty; 
					}
					else{
						$arr_all_size["$group_number**^^$size_name"] = $gs_qty;
					}
					
					if (array_key_exists("$group_number**^^$size_name**^^$fob_price", $arr_all_size_wfob)){
						$arr_all_size_wfob["$group_number**^^$size_name**^^$fob_price"] += $gs_qty; 
					}
					else{
						$arr_all_size_wfob["$group_number**^^$size_name**^^$fob_price"] = $gs_qty;
					}
					
					if(array_key_exists("$garmentID", $arr_all_garment)){
						$arr_all_garment["$garmentID"] += $gs_qty;
						
						if(isset($arr_all_color_qty["$group_number"])){
							$arr_all_color_qty["$group_number"] += $gs_qty;
						}
						else{
							$arr_all_color_qty["$group_number"] = $gs_qty;
						}
					}
					else{
						$arr_all_garment["$garmentID"] = $gs_qty;
						$arr_all_color_qty["$group_number"] = $gs_qty;
					}
					
					// if($spID==27911)
					// echo "$CIHID - [$garmentID] $total_ctn - $packing_method - $i <br/>";
					
					if(!in_array($garmentID, $arr_temp)){//&& $garmentID==4765
						if(array_key_exists("g$garmentID", $arr_all_garment_ctn) && 
							($packing_method==1 || $packing_method==2 || ($packing_method==50 && $i==0))){
							$arr_all_garment_ctn["g$garmentID"] += $total_ctn;
						}
						else if(!($packing_method==50 && $i>0)){
							$arr_all_garment_ctn["g$garmentID"] = $total_ctn;
						}
						$arr_temp[] = $garmentID;
						
					}
					
					if (array_key_exists("$group_number**^^$size_name", $arr_all_size_ctn)){
						$arr_all_size_ctn["$group_number**^^$size_name"]["qty"] += $gs_qty; 
					}
					else{
						$arr_all_size_ctn["$group_number**^^$size_name"] = array("qty"=>$gs_qty, "color"=>$color, "fob_price"=>$fob_price, 
																				"total_ctn"=>"0", "SKU"=>$SKU, "prepack_name"=>$prepack_name,
																				"quota"=>$quota, "ht_code"=>$ht_code, "upc_code"=>$upc_code,
																				"this_shipping_marking"=>$this_shipping_marking);
					}
					
					if($new_range==""){// to store color contains how many carton qty
						if (array_key_exists("$group_number", $arr_all_color_ctn)){
							$arr_all_color_ctn["$group_number"] += $total_ctn;
							$arr_all_color_gw["$group_number"]  += $gross_weight * $total_ctn;
							$arr_all_color_nw["$group_number"]  += $net_weight * $total_ctn;
							$arr_all_color_cbm["$group_number"] += $total_CBM;
							// $arr_all_color_qty["$group_number"] += $gs_qty;
						}
						else{
							$arr_all_color_ctn["$group_number"] = $total_ctn;
							$arr_all_color_gw["$group_number"]  = $gross_weight * $total_ctn;
							$arr_all_color_nw["$group_number"]  = $net_weight * $total_ctn;
							$arr_all_color_cbm["$group_number"] = $total_CBM;
							// $arr_all_color_qty["$group_number"] = $gs_qty;
							
						}
						
						$new_range = "No";
					}
					
					if (array_key_exists("$SKU**^^$group_number**^^$size_name", $arr_skucolorsize)){
						$arr_skucolorsize["$SKU**^^$group_number**^^$size_name"]["qty"] += $gs_qty;
					}
					else{
						$arr_skucolorsize["$SKU**^^$group_number**^^$size_name"] = array("qty"=>$gs_qty, "color"=>$color, "fob_price"=>$fob_price);
					}
					
					if (array_key_exists("$SKU", $arr_skuOnly)){
						if (array_key_exists("$group_number**^^$size_name", $arr_skuOnly["$SKU"])){
							$arr_skuOnly["$SKU"]["$group_number**^^$size_name"]["qty"] += $gs_qty;
						}
						else{
							$arr_skuOnly["$SKU"]["$group_number**^^$size_name"] = array("qty"=>$gs_qty, "color"=>$color, "fob_price"=>$fob_price, "upc_code"=>$upc_code);
						}
					}
					else{
						$arr_skuOnly["$SKU"]["$group_number**^^$size_name"] = array("qty"=>$gs_qty, "color"=>$color, "fob_price"=>$fob_price, "upc_code"=>$upc_code);
					}
					
					///////////////////////////////////////////////////////////
					////////////--- Array Store Color & SKU ---////////////////
					if (!in_array("$group_number**%%^^$SKU", $arr_grp_color)){
						array_push($arr_grp_color, "$group_number**%%^^$SKU");
					}
					$arr_size_info["$group_number"]["$size_name"] = $qty;
					if(isset($arr_all_size_color["g$group_number**^^$color"]["$size_name"])){//added by ckwai 20230821
						$arr_all_size_color["g$group_number**^^$color"]["$size_name"] += ($qty * $total_ctn);
					}
					else{
						$arr_all_size_color["g$group_number**^^$color"]["$size_name"] = ($qty * $total_ctn);
					}
					
					//////////////////////////////////////////////////////////////
					////////////////--- Array Based On SKU ---////////////////////
					$prepack_qty = ($qty * $total_ctn);
					
					// echo ">>> [$spID] $SKU - $group_number << <br/>";
					if (array_key_exists("$SKU**^^$group_number", $arr_Prepackname)){
						$arr_Prepackname["$SKU**^^$group_number"]["qty"] += $prepack_qty;
					}
					else{
						$arr_Prepackname["$SKU**^^$group_number"] = array("qty"=>$prepack_qty, "color"=>$color, "fob_price"=>$fob_price, "colorOnly"=>$colorOnly, "garmentID"=>$garmentID);
					}
					/////////// End Array Based On SKU //////////
					/////////////////////////////////////////////
					
					if (array_key_exists("G$group_number", $arr_group_number)){
						$arr_group_number["G$group_number"]["qty"] += $prepack_qty;
						
					}
					else{
						$arr_group_number["G$group_number"] = array("qty"=>$prepack_qty, "color"=>$color, "fob_price"=>$fob_price, 
																	"garmentID"=>$garmentID, "colorID"=>$colorID, 
																	"colorOnly"=>$colorOnly, "garmentOnly"=>$garmentOnly, 
																	"total_ctn"=>"0", "class_description"=>$class_description);
																	
					}
					
					//echo "=====> $group_number - $prepack_qty <br/>";
					
					//////////////////////////////////////////////////////////////
					//////////////--- Array Based On PO Price ---/////////////////
					if (array_key_exists("F$fob_price", $arr_FOBPrice)){
						$arr_FOBPrice["F$fob_price"]["qty"] += $prepack_qty;
					}
					else{
						$arr_FOBPrice["F$fob_price"] = array("qty"=>$prepack_qty, "color"=>$color);
					}
					//////// End Array Based On PO Price ////////
					/////////////////////////////////////////////
					
				}
				
				$arr_all_size_ctn["$group_number**^^$size_name"]["total_ctn"] += $total_ctn; 
				if (array_key_exists("^^$fob_price", $arr_all_gw_wfob)){
					$arr_all_gw_wfob["^^$fob_price"] += ($gross_weight * $total_ctn); 
				}
				else{
					$arr_all_gw_wfob["^^$fob_price"] = ($gross_weight * $total_ctn);
				}
				
				if (array_key_exists("^^$fob_price", $arr_all_nw_wfob)){
					$arr_all_nw_wfob["^^$fob_price"] += ($net_weight * $total_ctn); 
				}
				else{
					$arr_all_nw_wfob["^^$fob_price"] = ($net_weight * $total_ctn);
				}
				
				$one_cbm = ($ext_length/100) * ($ext_width/100) * ($ext_height/100);
				if (array_key_exists("^^$fob_price", $arr_all_cbm_wfob)){
					$arr_all_cbm_wfob["^^$fob_price"] += ($one_cbm * $total_ctn); 
				}
				else{
					$arr_all_cbm_wfob["^^$fob_price"] = ($one_cbm * $total_ctn);
				}
				
				$arr_all_size_ctn["$group_number**^^$size_name"]["total_ctn"] += $total_ctn; 
				$arr_group_number["G$group_number"]["total_ctn"] += $total_ctn;
				
				$one_cbm = ($ext_length/100) * ($ext_width/100) * ($ext_height/100);
				// $one_cbm = floor($one_cbm * 100000) / 100000;
				$one_cbm = round($one_cbm, 5);
				$total_CBM = $one_cbm * $total_ctn;
				
				$this_ctn_range = "$start-$end_num";
				$total_qty      = $total_ctn * $total_qty_in_carton;
				$this_nnw 		= $net_net_weight * $total_ctn;
				$this_nw  		= $net_weight * $total_ctn;
				$this_gw 	    = $gross_weight * $total_ctn;
				$cbm_total      = $total_CBM;
				$ctn_measurement = round($ext_length,1)." x ".round($ext_width,1)." x ".round($ext_height,1)." (cm)";
				
				if(isset($arr_all_garment_gw["g$garmentID"])){ //added by ckwai 20230821
					$arr_all_garment_gw["g$garmentID"] += ($gross_weight * $total_ctn);
				}
				else{
					$arr_all_garment_gw["g$garmentID"] = ($gross_weight * $total_ctn);
				}
				
				if(isset($arr_all_garment_nw["g$garmentID"])){ //added by ckwai 20230821
					$arr_all_garment_nw["g$garmentID"] += ($net_weight * $total_ctn);
				}
				else{
					$arr_all_garment_nw["g$garmentID"] = ($net_weight * $total_ctn);
				}
				
				if (!in_array("$ctn_measurement", $arr_ctn_measurement)){
					array_push($arr_ctn_measurement, "$ctn_measurement");
					//echo "$ctn_measurement <=== <br/>";
				}
				
				$arr_list[] = array("CIHID"=>$CIHID, "PID"=>$PID, "SKU"=>$SKU, "prepack_name"=>$prepack_name, "masterID"=>$masterID, 
									"start"=>$start, "end_num"=>$end_num, 
									"is_last"=>$is_last, "total_ctn"=>$total_ctn, "mixID"=>$mixID, "total_qty_in_carton"=>$total_qty_in_carton,
									"net_net_weight"=>$net_net_weight, "net_weight"=>$net_weight, "gross_weight"=>$gross_weight,
									"weight_unitID"=>$weight_unitID, "ext_length"=>$ext_length, "ext_width"=>$ext_width, "ext_height"=>$ext_height, "ctn_unitID"=>$ctn_unitID, "total_CBM"=>$total_CBM, "arr_list_detail"=>$arr_list_detail, "colorOnly"=>$colorOnly, "garmentOnly"=>$garmentOnly, "group_number"=>$group_number, "blisterbag_in_carton"=>$blisterbag_in_carton,
									
									"ctn_range"=>$this_ctn_range, "count_ctn"=>$total_ctn, "this_ctn_qty"=>$total_qty_in_carton,
									"this_nnw"=>$this_nnw, "this_nw"=>$this_nw, "this_gw"=>$this_gw,
									"total_qty"=>$total_qty, "cbm_total"=>$cbm_total, "ctn_measurement"=>$ctn_measurement, 
									"arr_grp_color"=>$arr_grp_color, "arr_size_info"=>$arr_size_info);
				
				$grand_nw  += ($net_weight * $total_ctn);
				$grand_nnw += ($net_net_weight * $total_ctn);
				$grand_gw  += ($gross_weight * $total_ctn);
				$grand_cbm += ($total_CBM);
				$ctn_qty   += $total_ctn;
				$grand_qty += $total_qty_in_carton * $total_ctn;
				$grand_qty_in_pcs += ($total_qty_in_carton * $count_color_in_grp) * $total_ctn;
				
				// if($spID==52588){
					// echo "============>> $grand_qty << <br/>";
				// }
				
			}//--- End While ---//
		}//--- End Count>0 ---//
		
		// echo "<hr/>";
		//print_r($arr_Prepackname);
		$arr_all = array("arr_list"=>$arr_list, "grand_nw"=>$grand_nw, "grand_nnw"=>$grand_nnw, "grand_gw"=>$grand_gw, "grand_cbm"=>$grand_cbm,
						 "ctn_qty"=>$ctn_qty, "arr_Prepackname"=>$arr_Prepackname, "arr_ctn_measurement"=>$arr_ctn_measurement, "arr_FOBPrice"=>$arr_FOBPrice, "grand_qty"=>$grand_qty, "grand_qty_in_pcs"=>$grand_qty_in_pcs,
						 "arr_all_color_ctn"=>$arr_all_color_ctn, "arr_all_color_gw"=>$arr_all_color_gw, 
						 "arr_all_color_nw"=>$arr_all_color_nw, "arr_all_color_cbm"=>$arr_all_color_cbm, "arr_all_color_qty"=>$arr_all_color_qty,
						 "arr_all_size"=>$arr_all_size, "arr_group_number"=>$arr_group_number, "arr_skucolorsize"=>$arr_skucolorsize, "shipping_marking"=>$shipping_marking, "arr_all_size_color"=>$arr_all_size_color, "arr_skuOnly"=>$arr_skuOnly, "arr_all_size_ctn"=>$arr_all_size_ctn, "count_color_in_grp"=>$count_color_in_grp,
						 "arr_all_garment"=>$arr_all_garment, "arr_all_garment_ctn"=>$arr_all_garment_ctn, 
						 "arr_all_garment_gw"=>$arr_all_garment_gw, "arr_all_garment_nw"=>$arr_all_garment_nw,
						 "arr_all_size_wfob"=>$arr_all_size_wfob, "arr_all_gw_wfob"=>$arr_all_gw_wfob,
						 "arr_all_nw_wfob"=>$arr_all_nw_wfob, "arr_all_cbm_wfob"=>$arr_all_cbm_wfob);
		
		return $arr_all;
	}
	
	public function getBuyerPOPrice($this_spID, $group_number){
		$sql = "SELECT scsq.price 
				FROM `tblship_colorsizeqty` scsq 
				INNER JOIN tblship_group_color sgc ON sgc.shipmentpriceID = scsq.shipmentpriceID
													AND sgc.garmentID = scsq.garmentID
													AND sgc.colorID = scsq.colorID
													AND sgc.statusID=1
				WHERE scsq.shipmentpriceID = '$this_spID' 
				AND scsq.statusID=1 AND scsq.price>0 AND sgc.group_number='$group_number' 
				AND scsq.qty>0 limit 1";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$price = $row["price"];
			$price = ($price==""? 0: $price);
		
		$arr = array("poprice"=>"$price");
		
		return $arr;
	}
	
	public function getPOPrice($this_spID, $group_number, $size_name="", $invID=""){
		$colorName = "";
		
		$tblbuyer_invoice_detail = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail":"tblbuyer_invoice_payment_detail");
		
		//------------- FOB PRICE / PO PRICE --------------//
		$sqlFOB = " SELECT sgc.garmentID, sgc.colorID,  
							count(DISTINCT sgc.GCID) as count_color_in_grp, GROUP_CONCAT(DISTINCT sgc.GCID) as GCID,
                    (SELECT scsq.price FROM tblship_colorsizeqty scsq WHERE scsq.shipmentpriceID = sgc.shipmentpriceID AND scsq.garmentID = sgc.garmentID AND scsq.colorID = sgc.colorID 
					AND scsq.statusID=1 AND scsq.price>0 limit 1) as fob_price,
                    GROUP_CONCAT(distinct sgc.garmentID) as grp_garmentID
					
					FROM tblship_group_color sgc 
					WHERE sgc.shipmentpriceID = '$this_spID' AND sgc.group_number='$group_number' 
					AND sgc.statusID=1 ";
					
					// echo "<pre>$sqlFOB</pre>";
				$result_scsq = $this->conn->prepare($sqlFOB);
				$result_scsq->execute();
				$row_scsq = $result_scsq->fetch(PDO::FETCH_ASSOC);
					//$fob_price = $row_scsq["fob_price"];
					$grp_garmentID = $row_scsq["grp_garmentID"];
					$garmentID = $row_scsq["garmentID"];
					$colorID   = $row_scsq["colorID"];
					//$colorName = $row_scsq["colorName"];
					$GCID               = $row_scsq["GCID"];
					$count_color_in_grp = $row_scsq["count_color_in_grp"];
		
		$count_accgmt = 0;
		if(trim($grp_garmentID)!=""){
			$sqlgmttype = "SELECT garmentID
							FROM `tblgarment` 
							WHERE garmentID IN ($grp_garmentID) AND gmttype IN (75,76,89,90,91)";
			$result_gmttype = $this->conn->prepare($sqlgmttype);
			$result_gmttype->execute();
			
			$count_accgmt = $result_gmttype->rowCount();
		}
		
		//echo "GCID => $GCID <br/>";
		if($GCID!=""){
			$sqlSGC = "SELECT concat(csq.GTN_colorname,' / <i>', g.styleNo,'</i>') as colorName_actual,
							concat(c.ColorName,' / <i>', g.styleNo,'</i>') as colorName,
							group_concat(DISTINCT csq.GTN_colorname) as colorOnly_actual,
                            group_concat(DISTINCT c.ColorName) as colorOnly, 
                            group_concat(DISTINCT g.styleNo) as garmentOnly
						FROM tblcolorsizeqty csq 
						LEFT JOIN tblgarment g ON g.garmentID = csq.garmentID
                        INNER JOIN tblcolor c ON c.ID = csq.colorID
						WHERE csq.garmentID='$garmentID' AND csq.colorID = '$colorID'";
			// $sqlSGC = "SELECT group_concat(c.colorName,' / <i>', g.styleNo,'</i>' separator '<br/>') as colorName,
							// group_concat(c.colorName) as colorOnly,  group_concat(g.styleNo) as garmentOnly
						// FROM tblship_group_color sgc 
						// INNER JOIN tblcolor c ON c.ID = sgc.colorID
						// INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
						// WHERE sgc.GCID IN ($GCID)";
			$result_sgc = $this->conn->prepare($sqlSGC);
			$result_sgc->execute();
			$row_sgc = $result_sgc->fetch(PDO::FETCH_ASSOC);
				$colorName_actual   = $row_sgc["colorName_actual"];
				$colorOnly_actual   = $row_sgc["colorOnly_actual"];
				
				$colorName   = ($colorOnly_actual==""? $row_sgc["colorName"]: $colorName_actual);
				$colorOnly   = ($colorOnly_actual==""? $row_sgc["colorOnly"]: $colorOnly_actual);
				$garmentOnly = $row_sgc["garmentOnly"];
			//echo "==> $colorName <br/>"; 
		}
		
		$sqlINV = "SELECT qc.Description as quota, bid.ht_code, bid.shipping_marking, bid.fob_price
					FROM $tblbuyer_invoice_detail bid 
                    LEFT JOIN tblquotacat qc ON qc.ID = bid.quotaID
					WHERE bid.invID='$invID' AND shipmentpriceID='$this_spID' 
					AND group_number='$group_number' AND del=0 AND bid.BICID='".$this->BICID."'";
		// echo "<pre>$sqlINV</pre>";
		$result_inv = $this->conn->prepare($sqlINV);
		$result_inv->execute();
		$row_inv = $result_inv->fetch(PDO::FETCH_ASSOC);
			$quota   = $row_inv["quota"];
			$ht_code = $row_inv["ht_code"];
			$shipping_marking = $row_inv["shipping_marking"];
			$fob_price = $row_inv["fob_price"];
					
		$sqlUPC = "SELECT upc_code 
					FROM tblship_upc_detail 
					WHERE shipmentpriceID='$this_spID' AND garmentID='$garmentID' 
					AND colorID='$colorID' AND size_name='$size_name' AND statusID=1";
		$result_upc = $this->conn->prepare($sqlUPC);
		$result_upc->execute();
		$row_upc = $result_upc->fetch(PDO::FETCH_ASSOC);
			$upc_code = $row_upc["upc_code"];
					
		$arr_all = array("fob_price"=>$fob_price, "color"=>$colorName, "garmentID"=>$garmentID, "colorID"=>$colorID, 
							"quota"=>$quota, "ht_code"=>$ht_code, "shipping_marking"=>$shipping_marking,
							"upc_code"=>$upc_code, "count_color_in_grp"=>$count_color_in_grp, 
							"colorOnly"=>$colorOnly, "garmentOnly"=>$garmentOnly, "count_accgmt"=>$count_accgmt);
		
		return $arr_all;
	}
	
	public function getBuyerInvoicePackingListDataFromPlanning($spID, $str_prod=""){
		$arr_list = array();
		
		$CIHID = 0; $SKU = ""; $weight_unitID = 44; $ctn_unitID = 16;//CM
		$mixID = ""; 
		$sql = "SELECT cph.PID, cph.ctn_num, cph.ctn_range, cph.is_last, cph.prepack_name, cph.group_number as this_grp_num,
						cph.qty_in_blisterbag, cph.blisterbag_in_carton, cph.total_qty_in_carton, 
						cph.net_net_weight, cph.net_weight, cph.gross_weight,
						cph.ctn_measurement, cph.ext_length, cph.ext_width, cph.ext_height, cph.total_CBM, od.buyerID,
						spk.packing_method, spk.is_polybag, spk.is_blisterbag, cph.ctn_measurement_last,
						(SELECT spd.case_upc 
							FROM `tblship_packing_detail` spd 
							WHERE spd.PID = cph.PID AND spd.statusID=1 AND spd.group_number=cph.group_number limit 1) as case_upc
						
			FROM tblcarton_picklist_head$str_prod cph
			INNER JOIN tblshipmentprice sp ON sp.ID = cph.shipmentpriceID
			INNER JOIN tblorder od ON od.Orderno = sp.Orderno
			LEFT JOIN tblship_packing spk ON spk.PID = cph.PID
			WHERE cph.shipmentpriceID = '$spID'";
		// echo "<pre>$sql</pre>";
		$stmt_cphp = $this->conn->prepare($sql);
		$stmt_cphp->execute();
		while($row_cphp = $stmt_cphp->fetch(PDO::FETCH_ASSOC)){
			extract($row_cphp);
			list($start, $end_num) = explode("-", $ctn_range);
			$total_ctn = $end_num - $start + 1;
				
			$SKU = ($buyerID=="B13" && glb_profile=="iapparelintl"? "$prepack_name": $SKU);
			$prepack_name = ($buyerID=="B13" && glb_profile=="iapparelintl"? "$case_upc": "$prepack_name");
			$ori_blisterbag_in_carton = $blisterbag_in_carton;
			$blisterbag_in_carton = ($packing_method==1? $blisterbag_in_carton: 1);
			$blisterbag_in_carton = ($packing_method==50 && $is_polybag==1? $qty_in_blisterbag: $blisterbag_in_carton);
			
			if($packing_method==50 && $is_blisterbag==1 && $is_polybag==0){//to solve case (Sheila) spID: 28401 on 2022-03-17 
				$sqlgrp = "SELECT * 
							FROM `tblship_packing_detail` WHERE PID = '$PID' 
							AND statusID=1 group by group_number";
				$stmt_grp = $this->conn->prepare($sqlgrp);
				$stmt_grp->execute();
				
				$count_grp = $stmt_grp->rowCount();
				
				$blisterbag_in_carton = $ori_blisterbag_in_carton / $count_grp;
			}
			
			$arr_list_detail = array();
			$sql_d = "SELECT cphd.size_name, cphd.group_number, cphd.qty,
							(SELECT scsq.ID FROM tblship_colorsizeqty scsq 
                                  WHERE scsq.shipmentpriceID = cphd.shipmentpriceID 
                                  AND scsq.size_name = cphd.size_name order by scsq.ID asc limit 1) as sizeID
						FROM tblcarton_picklist_detail$str_prod cphd
						WHERE cphd.shipmentpriceID='$spID' AND cphd.ctn_num='$ctn_num' 
						group by cphd.shipmentpriceID, cphd.ctn_num, cphd.group_number, cphd.size_name
						order by cphd.group_number, sizeID ";//limit 0
			$stmt_cphd = $this->conn->prepare($sql_d);
			$stmt_cphd->execute();
			$count = 0;
			while($row_cphd = $stmt_cphd->fetch(PDO::FETCH_ASSOC)){
				extract($row_cphd);
				$gs_qty = $qty  * $blisterbag_in_carton;
				
				if($gs_qty>0){
					$arr_list_detail[] = array("size_name"=>$size_name, "group_number"=>$group_number, "qty"=>$gs_qty);
					$count++;
					if($count==1){
						$mixID = "$group_number**%%$size_name**%%$gs_qty";
					}
					else{
						$mixID .= "::^^$group_number**%%$size_name**%%$gs_qty";
					}
				}
			}//--- End While ---//
			
			if($ctn_measurement_last!=""){
				list($this_length, $this_width, $this_last) = explode("x", $ctn_measurement_last);
				list($this_height, $unit) = explode("(", $this_last);
				
				if($unit=="cm)"){
					$ext_length = trim($this_length);
					$ext_width  = trim($this_width);
					$ext_height = trim($this_height);
				}
				else{
					$ext_length = round($this_length / 2.54, 2);
					$ext_width  = round($this_width / 2.54, 2);
					$ext_height = round($this_height / 2.54, 2);
				}
				
			}
				
			$arr_list[] = array("CIHID"=>$CIHID, "PID"=>$PID, "SKU"=>$SKU, "prepack_name"=>$prepack_name, "start"=>$start, "end_num"=>$end_num, 
								"is_last"=>$is_last, "total_ctn"=>$total_ctn, "mixID"=>$mixID, "total_qty_in_carton"=>$total_qty_in_carton,
								"net_net_weight"=>$net_net_weight, "net_weight"=>$net_weight, "gross_weight"=>$gross_weight,
								"weight_unitID"=>$weight_unitID, "ext_length"=>$ext_length, "ext_width"=>$ext_width, "ext_height"=>$ext_height, "ctn_unitID"=>$ctn_unitID, "total_CBM"=>$total_CBM, "arr_list_detail"=>$arr_list_detail,
								"blisterbag_in_carton"=>$blisterbag_in_carton, "masterID"=>"");
		}//---- End While ----//
		
		return $arr_list;
	}
	
	public function getBuyerInvoicePackingListDataFromBalanceQty($invID, $spID, $str_prod){
		$arr_list = array();
		$arr_list_onlyColor = array();//color size record
		$arr_list_onlyRow = array();
		
		$this->model_carton_inv_head->shipmentpriceID = $spID;
		$this->model_carton_inv_head->istransit = $this->istransit;
		$this->model_carton_inv_head->invID = $invID;
		$max_end_num   = $this->model_carton_inv_head->getMaxEndNumInOtherInvoice();
		$min_start_num = $this->model_carton_inv_head->getMinStartNumInOtherInvoice();
		
		$CIHID = 0; $SKU = ""; $weight_unitID = 44; $ctn_unitID = 16;//CM
		$mixID = ""; 
		$sql = "SELECT cph.PID, cph.ctn_num, cph.ctn_range, cph.is_last, cph.prepack_name, cph.group_number as this_grp_num,
						cph.qty_in_blisterbag, cph.blisterbag_in_carton, cph.total_qty_in_carton, 
						cph.net_net_weight, cph.net_weight, cph.gross_weight,
						cph.ctn_measurement, cph.ext_length, cph.ext_width, cph.ext_height, cph.total_CBM, od.buyerID,
						spk.packing_method
			FROM tblcarton_picklist_head$str_prod cph
			INNER JOIN tblshipmentprice sp ON sp.ID = cph.shipmentpriceID
			INNER JOIN tblorder od ON od.Orderno = sp.Orderno
			LEFT JOIN tblship_packing spk ON spk.PID = cph.PID
			WHERE cph.shipmentpriceID = '$spID'";
		
		// echo "<pre>$sql</pre>";
		$stmt_cphp = $this->conn->prepare($sql);
		$stmt_cphp->execute();
		$count = 0;
		while($row_cphp = $stmt_cphp->fetch(PDO::FETCH_ASSOC)){
			extract($row_cphp);
			$count++;
			
			list($this_start, $this_end_num) = explode("-", $ctn_range);
			$total_ctn = $this_end_num - $this_start + 1;
			
			$SKU = ($buyerID=="B13" && glb_profile=="iapparelintl"? "$prepack_name": $SKU);
			$prepack_name = ($buyerID=="B13" && glb_profile=="iapparelintl"? "": "$prepack_name");
			$blisterbag_in_carton = ($packing_method==1? $blisterbag_in_carton: 1);
			
			$arr_list_detail = array(); 
			$str_list = "";
			$arr = 0;
			$sql_d = "SELECT cphd.size_name, cphd.group_number, cphd.qty,
							(SELECT scsq.ID FROM tblship_colorsizeqty scsq 
                                  WHERE scsq.shipmentpriceID = cphd.shipmentpriceID 
                                  AND scsq.size_name = cphd.size_name order by scsq.ID asc limit 1) as sizeID
						FROM tblcarton_picklist_detail$str_prod cphd
						WHERE cphd.shipmentpriceID='$spID' AND cphd.ctn_num='$ctn_num' 
						group by cphd.shipmentpriceID, cphd.ctn_num, cphd.group_number, cphd.size_name
						order by cphd.group_number, sizeID";
			$stmt_cphd = $this->conn->prepare($sql_d);
			$stmt_cphd->execute();
			while($row_cphd = $stmt_cphd->fetch(PDO::FETCH_ASSOC)){
				extract($row_cphd);
				
				$gs_qty = $qty  * $blisterbag_in_carton;
				
				$arr_list_detail[] = array("size_name"=>$size_name, "group_number"=>$group_number, "qty"=>$gs_qty);
				
				if($qty>0){
					$arr++;
					if($arr==1){
						$str_list = "$group_number**%%$size_name**%%$gs_qty";
					}
					else{
						$str_list .= "::^^$group_number**%%$size_name**%%$gs_qty";
					}
				}
				
				$key = "$group_number**%%$size_name";
				if (array_key_exists($key, $arr_list_onlyColor)) {
					$arr_list_onlyColor[$key] += ($gs_qty * $total_ctn);
				}
				else{
					$arr_list_onlyColor[$key] = ($gs_qty * $total_ctn);
				}
			}//--- End While Packing Detail ---//
			
			$row_key = "$str_list";
			if (array_key_exists($row_key, $arr_list_onlyRow)) {
				$arr_list_onlyRow[$row_key]["total_ctn"] += $total_ctn;
			}
			else{
				$arr_list_onlyRow[$row_key] = array("CIHID"=>$CIHID, "PID"=>$PID, "SKU"=>$SKU, "prepack_name"=>$prepack_name,
													"is_last"=>$is_last, "mixID"=>$row_key, "total_qty_in_carton"=>$total_qty_in_carton, "total_ctn"=>$total_ctn, 
													"net_net_weight"=>$net_net_weight, "net_weight"=>$net_weight, "gross_weight"=>$gross_weight,
													"ext_length"=>$ext_length, "ext_width"=>$ext_width, "ext_height"=>$ext_height,
													"ctn_unitID"=>$ctn_unitID, "weight_unitID"=>$weight_unitID, "total_CBM"=>$total_CBM, 
													"arr_list_detail"=>$arr_list_detail, "blisterbag_in_carton"=>$blisterbag_in_carton);
			}
			
		}//--- End While Carton Row ---//
		
		// print_r($arr_list_onlyRow);
		
		$i = 0; $end = 0;
		foreach($arr_list_onlyRow as $mixID => $arr_value){
			
			$CIHID               = $arr_value["CIHID"];
			$PID                 = $arr_value["PID"];
			$SKU                 = $arr_value["SKU"];
			$prepack_name        = $arr_value["prepack_name"];
			$is_last             = $arr_value["is_last"];
			$total_ctn_qty       = $arr_value["total_ctn"];
			$total_qty_in_carton = $arr_value["total_qty_in_carton"];
			$net_net_weight      = $arr_value["net_net_weight"];
			$net_weight          = $arr_value["net_weight"];
			$gross_weight        = $arr_value["gross_weight"];
			$weight_unitID       = $arr_value["weight_unitID"];
			$ext_length          = $arr_value["ext_length"];
			$ext_width           = $arr_value["ext_width"];
			$ext_height          = $arr_value["ext_height"];
			$blisterbag_in_carton = $arr_value["blisterbag_in_carton"];
			
			$this->model_carton_inv_head->shipmentpriceID = $spID;
			$this->model_carton_inv_head->invID = $invID;
			$this->model_carton_inv_head->mixID = $mixID;
			$other_inv_ctn_qty = ($this->istransit==1? 0: $this->model_carton_inv_head->checkOtherInvoiceCartonQty());//modify by ckwai on 20230824 for unlimit load PO to internal transit in container plan
			$arr_result    = $this->model_carton_inv_head->checkExistingInvoiceCartonQty();
			$this_ctn_qty  = $arr_result["count"];
			$this_start    = $arr_result["start"];
			$this_end      = $arr_result["end"];
			
			$balance_ctn_qty = $total_ctn_qty - $other_inv_ctn_qty;
			$temp_qty = $total_qty_in_carton * $balance_ctn_qty;
			
			$arr_list_detail = array(); 
			$arr_row = explode("::^^", $mixID);
			for($arr=0;$arr<count($arr_row);$arr++){
				list($group_number, $size_name, $qty) = explode("**%%", $arr_row[$arr]);
				$g_qty = $qty * $other_inv_ctn_qty;
				
				$arr_list_onlyColor["$group_number**%%$size_name"] -= $g_qty;
				$arr_list_detail[] = array("size_name"=>$size_name, "group_number"=>$group_number, "qty"=>$qty);
			}
			// echo "balance_ctn_qty: $balance_ctn_qty << ".$this->istransit."<br/>";
			if($balance_ctn_qty<=0){
				unset($arr_list_onlyRow["$mixID"]);
			}
			else if($balance_ctn_qty==$this_ctn_qty){
				$start = $this_start;
				$end   = $this_end;
				
				$total_CBM = round(($ext_length/100 * $ext_width/100 * $ext_height/100) * $balance_ctn_qty, 3);
				
				$arr_mix = explode("::^^", $mixID);
				for($mm=0;$mm<count($arr_mix);$mm++){
					list($this_gn, $this_size, $this_qty) = explode("**%%", $arr_mix[$mm]);
					$arr_list_onlyColor["$this_gn**%%$this_size"] -= ($this_qty * $balance_ctn_qty);
				}
				
				// echo "$start - $end << <br/>";
				$arr_list[] = array("CIHID"=>$CIHID, "PID"=>$PID, "SKU"=>$SKU, "prepack_name"=>$prepack_name, "start"=>$start, "end_num"=>$end, 
								"is_last"=>$is_last, "total_ctn"=>$balance_ctn_qty, "mixID"=>$mixID, "total_qty_in_carton"=>$total_qty_in_carton,
								"net_net_weight"=>$net_net_weight, "net_weight"=>$net_weight, "gross_weight"=>$gross_weight,
								"weight_unitID"=>$weight_unitID, "ext_length"=>$ext_length, "ext_width"=>$ext_width, "ext_height"=>$ext_height, "ctn_unitID"=>$ctn_unitID, "total_CBM"=>$total_CBM, "arr_list_detail"=>$arr_list_detail,
								"blisterbag_in_carton"=>$blisterbag_in_carton);
				
			}
			else{
				$i++;
				if($i==1 && count($arr_list)==0){
					$start = ($min_start_num>1? 1: $max_end_num + 1);
					$end   = $start + $balance_ctn_qty - 1;
				}
				else{
					$start = $end + 1;
					$end   = $start + $balance_ctn_qty - 1;
				}
				
				$total_CBM = round(($ext_length/100 * $ext_width/100 * $ext_height/100) * $balance_ctn_qty, 3);
				
				$arr_mix = explode("::^^", $mixID);
				$arr_list_detail = array(); 
				for($mm=0;$mm<count($arr_mix);$mm++){
					list($this_gn, $this_size, $this_qty) = explode("**%%", $arr_mix[$mm]);
					$arr_list_onlyColor["$this_gn**%%$this_size"] -= ($this_qty * $balance_ctn_qty);
					
					$arr_list_detail[] = array("size_name"=>$this_size, "group_number"=>$this_gn, "qty"=>$this_qty);
				}
				
				$arr_list[] = array("CIHID"=>$CIHID, "PID"=>$PID, "SKU"=>$SKU, "prepack_name"=>$prepack_name, "start"=>$start, "end_num"=>$end, 
								"is_last"=>$is_last, "total_ctn"=>$balance_ctn_qty, "mixID"=>$mixID, "total_qty_in_carton"=>$total_qty_in_carton,
								"net_net_weight"=>$net_net_weight, "net_weight"=>$net_weight, "gross_weight"=>$gross_weight,
								"weight_unitID"=>$weight_unitID, "ext_length"=>$ext_length, "ext_width"=>$ext_width, "ext_height"=>$ext_height, "ctn_unitID"=>$ctn_unitID, "total_CBM"=>$total_CBM, "arr_list_detail"=>$arr_list_detail,
								"blisterbag_in_carton"=>$blisterbag_in_carton);
			}
		}//--- End Foreach ---//
		
		foreach($arr_list_onlyColor as $mixID => $total_qty_in_carton){ // foreach balance qty of color size put to last
			if($total_qty_in_carton>0){
				list($group_number, $size_name) = explode("**%%", $mixID);
				$mixID = "$mixID**%%$total_qty_in_carton";
				
				$start = $end + 1;
				$end   = $start;
				$balance_ctn_qty = 1;
				
				$arr_list[] = array("CIHID"=>$CIHID, "PID"=>$PID, "SKU"=>"", "prepack_name"=>"", "start"=>$start, "end_num"=>$end, 
								"is_last"=>$is_last, "total_ctn"=>$balance_ctn_qty, "mixID"=>$mixID, "total_qty_in_carton"=>$total_qty_in_carton,
								"net_net_weight"=>$net_net_weight, "net_weight"=>$net_weight, "gross_weight"=>$gross_weight,
								"weight_unitID"=>$weight_unitID, "ext_length"=>$ext_length, "ext_width"=>$ext_width, "ext_height"=>$ext_height, "ctn_unitID"=>$ctn_unitID, "total_CBM"=>$total_CBM, "arr_list_detail"=>array(), 
								"blisterbag_in_carton"=>$blisterbag_in_carton);
				
			}
		}//--- End Foreach ---//


		return $arr_list;
	}
	
	public function getBuyerPaymentInvoicePackingListDataFromBalanceQty($invID, $spID){
		$skip_cih="oripickpack";
		$arr_ori = $this->getBuyerInvoicePackingListData($spID, $skip_cih, $invID);
		
		$this->model_carton_inv_payment_head->shipmentpriceID = $spID;
		$this->model_carton_inv_payment_head->invID = $invID;
		$max_end_num   = $this->model_carton_inv_payment_head->getMaxEndNumInOtherInvoice();
		$min_start_num = $this->model_carton_inv_payment_head->getMinStartNumInOtherInvoice();
		
		$arr_list           = array();
		$arr_list_onlyRow   = array();
		$arr_list_onlyColor = array();//color size record
		
		for($i=0;$i<count($arr_ori);$i++){
			$CIHID               = $arr_ori[$i]["CIHID"];
			$PID                 = $arr_ori[$i]["PID"];
			$start               = $arr_ori[$i]["start"];
			$end_num             = $arr_ori[$i]["end_num"];
			$SKU                 = $arr_ori[$i]["SKU"];
			$prepack_name        = $arr_ori[$i]["prepack_name"];
			$is_last             = $arr_ori[$i]["is_last"];
			$mixID               = $arr_ori[$i]["mixID"];
			$total_ctn           = $arr_ori[$i]["total_ctn"];
			$total_qty_in_carton = $arr_ori[$i]["total_qty_in_carton"];
			$net_net_weight      = $arr_ori[$i]["net_net_weight"];
			$net_weight          = $arr_ori[$i]["net_weight"];
			$gross_weight        = $arr_ori[$i]["gross_weight"];
			$ext_length          = $arr_ori[$i]["ext_length"];
			$ext_width           = $arr_ori[$i]["ext_width"];
			$ext_height          = $arr_ori[$i]["ext_height"];
			$ctn_unitID          = $arr_ori[$i]["ctn_unitID"];
			$weight_unitID       = $arr_ori[$i]["weight_unitID"];
			$total_CBM           = $arr_ori[$i]["total_CBM"];
			$blisterbag_in_carton = $arr_ori[$i]["blisterbag_in_carton"];
			
			//echo "$start $end_num [$min_start_num] $invID<br/>";
			
			$arr_mix = explode("::^^", $mixID);
			for($m=0;$m<count($arr_mix);$m++){
				list($group_number, $size_name, $this_qty) = explode("**%%", $arr_mix[$m]);
				
				$key = "$group_number**%%$size_name";
				if (array_key_exists($key, $arr_list_onlyColor)) {
					$arr_list_onlyColor[$key] += ($this_qty * $total_ctn);
				}
				else{
					$arr_list_onlyColor[$key] = ($this_qty * $total_ctn);
				}
				
			}//--- End For Loading Carton Detail Info ---//
			
			$row_key = "$mixID";
			if (array_key_exists($row_key, $arr_list_onlyRow)) {
				$arr_list_onlyRow[$row_key]["total_ctn"] += $total_ctn;
			}
			else{
				$arr_list_onlyRow[$row_key] = array("CIHID"=>$CIHID, "PID"=>$PID, "SKU"=>$SKU, "prepack_name"=>$prepack_name,
													"is_last"=>$is_last, "mixID"=>$row_key, "total_qty_in_carton"=>$total_qty_in_carton, "total_ctn"=>$total_ctn, 
													"net_net_weight"=>$net_net_weight, "net_weight"=>$net_weight, "gross_weight"=>$gross_weight,
													"ext_length"=>$ext_length, "ext_width"=>$ext_width, "ext_height"=>$ext_height,
													"ctn_unitID"=>$ctn_unitID, "weight_unitID"=>$weight_unitID, "total_CBM"=>$total_CBM, 
													"arr_list_detail"=>array(), "blisterbag_in_carton"=>$blisterbag_in_carton);
			}
		}//--- End For Original Shipment Packing List ---//
		
		
		$i = 0;
		foreach($arr_list_onlyRow as $mixID => $arr_value){
			
			$CIHID               = $arr_value["CIHID"];
			$PID                 = $arr_value["PID"];
			$SKU                 = $arr_value["SKU"];
			$prepack_name        = $arr_value["prepack_name"];
			$is_last             = $arr_value["is_last"];
			$total_ctn_qty       = $arr_value["total_ctn"];
			$total_qty_in_carton = $arr_value["total_qty_in_carton"];
			$net_net_weight      = $arr_value["net_net_weight"];
			$net_weight          = $arr_value["net_weight"];
			$gross_weight        = $arr_value["gross_weight"];
			$weight_unitID       = $arr_value["weight_unitID"];
			$ext_length          = $arr_value["ext_length"];
			$ext_width           = $arr_value["ext_width"];
			$ext_height          = $arr_value["ext_height"];
			$blisterbag_in_carton = $arr_value["blisterbag_in_carton"];
			
			$this->model_carton_inv_payment_head->shipmentpriceID = $spID;
			$this->model_carton_inv_payment_head->invID = $invID;
			$this->model_carton_inv_payment_head->mixID = $mixID;
			$other_inv_ctn_qty = $this->model_carton_inv_payment_head->checkOtherInvoiceCartonQty();
			
			$balance_ctn_qty = $total_ctn_qty - $other_inv_ctn_qty;
			$temp_qty = $total_qty_in_carton * $balance_ctn_qty;
			
			$arr_row = explode("::^^", $mixID);
			for($arr=0;$arr<count($arr_row);$arr++){
				list($group_number, $size_name, $qty) = explode("**%%", $arr_row[$arr]);
				$g_qty = $qty * $other_inv_ctn_qty;
				
				$arr_list_onlyColor["$group_number**%%$size_name"] -= $g_qty;
			}
			
			if($balance_ctn_qty<=0){
				unset($arr_list_onlyRow["$mixID"]);
			}
			else{
				$i++;
				if($i==1){
					$start = ($min_start_num>1? 1: $max_end_num + 1);
					$end   = $start + $balance_ctn_qty - 1;
				}
				else{
					$start = $end + 1;
					$end   = $start + $balance_ctn_qty - 1;
				}
				
				$total_CBM = round(($ext_length/100 * $ext_width/100 * $ext_height/100) * $balance_ctn_qty, 3);
				
				$arr_mix = explode("::^^", $mixID);
				for($mm=0;$mm<count($arr_mix);$mm++){
					list($this_gn, $this_size, $this_qty) = explode("**%%", $arr_mix[$mm]);
					$arr_list_onlyColor["$this_gn**%%$this_size"] -= ($this_qty * $balance_ctn_qty);
				}
				
				$arr_list[] = array("CIHID"=>$CIHID, "PID"=>$PID, "SKU"=>$SKU, "prepack_name"=>$prepack_name, "start"=>$start, "end_num"=>$end, 
								"is_last"=>$is_last, "total_ctn"=>$balance_ctn_qty, "mixID"=>$mixID, "total_qty_in_carton"=>$total_qty_in_carton,
								"net_net_weight"=>$net_net_weight, "net_weight"=>$net_weight, "gross_weight"=>$gross_weight,
								"weight_unitID"=>$weight_unitID, "ext_length"=>$ext_length, "ext_width"=>$ext_width, "ext_height"=>$ext_height, "ctn_unitID"=>$ctn_unitID, "total_CBM"=>$total_CBM, "arr_list_detail"=>array(), "blisterbag_in_carton"=>$blisterbag_in_carton);
			}
		}//--- End Foreach ---//
	
		
		foreach($arr_list_onlyColor as $mixID => $total_qty_in_carton){ // foreach balance qty of color size put to last 
			if($total_qty_in_carton>0){
				list($group_number, $size_name) = explode("**%%", $mixID);
				$mixID = "$mixID**%%$total_qty_in_carton";
				
				$start = $end + 1;
				$end   = $start;
				$balance_ctn_qty = 1;
				
				$arr_list[] = array("CIHID"=>$CIHID, "PID"=>$PID, "SKU"=>"", "prepack_name"=>"", "start"=>$start, "end_num"=>$end, 
								"is_last"=>$is_last, "total_ctn"=>$balance_ctn_qty, "mixID"=>$mixID, "total_qty_in_carton"=>$total_qty_in_carton,
								"net_net_weight"=>$net_net_weight, "net_weight"=>$net_weight, "gross_weight"=>$gross_weight,
								"weight_unitID"=>$weight_unitID, "ext_length"=>$ext_length, "ext_width"=>$ext_width, "ext_height"=>$ext_height, "ctn_unitID"=>$ctn_unitID, "total_CBM"=>$total_CBM, "arr_list_detail"=>array(), 
								"blisterbag_in_carton"=>$blisterbag_in_carton);
				
			}
		}//--- End Foreach ---//
		
		return $arr_list;
	}
	
	public function funcGetDropDownPrint($invID, $buyerID="", $btn_size="btn-xs", $isBuyerPayment="0", $num=0){//buyer_inv_list.php, buyer_inv.php
		$html = "";
		$arr_alphabet = array('','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		//<!--<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B49' target='_blank'>DECATHELON</a></li>-->
		//<!--<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B26' target='_blank'>ROOTS</a></li>-->
		
		$tblbuyer_invoice="";
		if($isBuyerPayment==0){
			$tblbuyer_invoice = "tblbuyer_invoice";
		}
		else{
			$tblbuyer_invoice = "tblbuyer_invoice_payment";
		}
		$sel_dest = $this->conn->prepare("SELECT dp.countryID 
						FROM $tblbuyer_invoice inv 
						JOIN tbldestinationport dp ON dp.ID=inv.PortDestID
						WHERE inv.ID='$invID'");
		$sel_dest->execute();
		$destport_countryID=$sel_dest->fetchColumn();
		
		$html .= "<span class='dropdown'>
						<button class='btn btn-primary $btn_size dropdown-toggle center-block' type='button' 
								data-toggle='dropdown'><span class='glyphicon glyphicon-print'></span> Print <span class='caret'></span></button>
						<ul class='dropdown-menu' role='menu'>";
				if($isBuyerPayment==0 || $isBuyerPayment==1){
				  $getLink = "&isBuyerPayment=$isBuyerPayment";
				  $html .= "<li class='nav-header' style='color:#939393'> &nbsp;PDF </li>
							<!--<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B13$getLink' target='_blank'>JOE FRESH*</a></li>
							<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B28$getLink' target='_blank'>S OLIVER*</a></li>
							<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B35$getLink' target='_blank'>WALMART </a></li>
							<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B36$getLink' target='_blank'>AEO </a></li>
							<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B37$getLink' target='_blank'>NOBLE </a></li>
							<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B47$getLink' target='_blank'>ITOCHU </a></li>
							<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B53$getLink' target='_blank'>Puma</a></li>
							<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B56$getLink' target='_blank'>Hunny Bunny*</a></li>
							<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B59$getLink' target='_blank'>DXL*</a></li>
							<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B63$getLink' target='_blank'>BUFFALO* </a></li>
							<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B64$getLink' target='_blank'>Kohls*</a></li>
							<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B66$getLink' target='_blank'>Disney</a></li>
							<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B68$getLink' target='_blank'>Caregiver USA*</a></li>-->";
				if($buyerID!="B37" && $isBuyerPayment==1){
					$html .= "<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=$buyerID$getLink' target='_blank'>
										Invoice & Packing List</a></li>";
				} 
				
				if($isBuyerPayment==1){	
					// $html .= "<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=CCI&&isBuyerPayment=$isBuyerPayment' target='_blank'>CCI</a></li>";
				}
				// $html .= "<li><a href='buyer_inv_exporter.php?id=$invID$getLink' target='_blank'>Exporter</a></li>";
				if(($buyerID=="B13" || $destport_countryID==33) && glb_profile=="iapparelintl"){
					$html .= "<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=BCC$getLink' target='_blank'>Cert of Conformity</a></li>";
					$html .= "<li><a href='buyer_template/buyer_B2552.php?id=$invID' target='_blank'>B255 COVER LETTER</a></li>";
				}		
				// $html .= "<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=MCO$getLink' target='_blank'>Multiple Country of Origin</a></li>
							// <li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=BIC$getLink' target='_blank'>Benificiary</a></li>
							// <li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=BIC2$getLink' target='_blank'>Benificiary 2</a></li>
							// <li><a href='buyer_inv_certificate_dxl.php?id=$invID' target='_blank'>Benificiary DXL</a></li>
							// <li class='divider'></li>";
				}
				
				if($buyerID=="B36" && glb_profile=="iapparelintl"){
					$html .= "<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B36C$getLink' target='_blank'>Contract</a></li>";
					$html .= "<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B36O$getLink' target='_blank'>CERT OF ORIGIN</a></li>";
					$html .= "<li><a href='buyer_inv_pdf.php?id=$invID&displayBuyer=B36D$getLink' target='_blank'>No Wood Declaration</a></li>";
				}
				
				$html .= ($buyerID=="B37" && glb_profile=="lushbax"? "": "<li class='nav-header' style='color:#939393'> &nbsp;Excel</li>");
				if($isBuyerPayment==0 && glb_mainproduct=="BAG"){			
					
					$html .= "<li><a href='buyer_inv_excel_colorlvl.php?invID=$invID' target='_blank'>Local Clearance Document</a></li>
							";
				}
				else if($isBuyerPayment==0){
					$html .= "<li><a href='buyer_inv_excel.php?invID=$invID' target='_blank'>Local Clearance Document</a></li>
							";
				}
				
				if(($buyerID=="B13" || $buyerID=="B64") && $isBuyerPayment==1 && glb_profile=="iapparelintl"){

					$link="";
					switch($destport_countryID){
						case 205: 
							$link="buyer_excel_joefresh_usa.php?id=$invID&displayBuyer=$buyerID&isBuyerPayment=$isBuyerPayment";
							break;
						default: 
							$link="buyer_excel_joefresh.php?id=$invID&displayBuyer=$buyerID&isBuyerPayment=$isBuyerPayment";
						 	break;
					}

					$html .= "<li><a href='$link' target='_blank'>Invoice & Packing List</a></li>";
				}
				
				if($isBuyerPayment==1 && $buyerID=="B37" && glb_profile=="iapparelintl"){
					// $html .= "<li class='nav-header' style='color:#939393'> &nbsp;Excel</li>";
					$html .= "<li><a href='buyer_template/buyer_noble_excel.php?id=$invID&displayBuyer=CCI&&isBuyerPayment=$isBuyerPayment' target='_blank'>Invoice & Packing List</a></li>";
				}
				else if($isBuyerPayment==1 && $buyerID=="B08" && glb_profile=="lushbax"){
					$html .= "<li><a href='buyer_excel_brahmin.php?id=$invID&displayBuyer=CCI&&isBuyerPayment=$isBuyerPayment' target='_blank'>Invoice & Packing List</a></li>";
				}
				else if($isBuyerPayment==1 && $buyerID=="B10" && glb_profile=="lushbax"){
					$html .= "<li><a href='buyer_template/buyer_lacoste_excel.php?id=$invID&displayBuyer=B10&&isBuyerPayment=$isBuyerPayment' target='_blank'>Invoice & Packing List</a></li>";
				}
				if($isBuyerPayment==1  ){//&& glb_profile=="iapparelintl"
					// $html .= "<li><a href='buyer_inv_excel_cci.php?id=$invID&&isBuyerPayment=$isBuyerPayment' target='_blank'>CCI (Excel)</a></li>";
				}
				$html .= '</ul></span>';
							
		return $html;
	}
	
	public function funcGetConsigneePaymentTerm($this_consignee, $mode="payment"){
		$filter_paymentterm = "";
		
		switch($mode){
			case "payment": 
			if($this_consignee!=""){
				$sql = "SELECT (SELECT group_concat(PaymentTermID) 	
							FROM tblconsignee 
							WHERE ConsigneeID IN ($this_consignee)) as PaymentTermID,
							(SELECT group_concat(PaymentTermOthID) 	
							FROM tblconsignee 
							WHERE ConsigneeID IN ($this_consignee) AND PaymentTermID!='') as PaymentTermOthID";
				$stmt_consignee = $this->conn->prepare($sql);
				$stmt_consignee->execute();
				$row_consignee = $stmt_consignee->fetch(PDO::FETCH_ASSOC);
				extract($row_consignee);
										
				$PaymentTermID  = rtrim($PaymentTermID,",");
				$PaymentTermID  = ltrim($PaymentTermID,",");
				$PaymentTermOthID  = rtrim($PaymentTermOthID,",");
				$PaymentTermOthID  = ltrim($PaymentTermOthID,",");
										
				if($PaymentTermOthID!=""){
					$filter_paymentterm = "AND ID IN ($PaymentTermID,$PaymentTermOthID)";
				}
				else if($PaymentTermID!="" && $PaymentTermID!="0"){
					$filter_paymentterm = "AND ID IN ($PaymentTermID)";
				}
				else{
					$filter_paymentterm = "";//AND ID IN ($PaymentTermID)
				}
			}break;
			
			case "buyerdestination":
			if($this_consignee!=""){
				$sql = "SELECT GROUP_CONCAT(BuyerDestination) as BuyerDestination
							FROM `tblconsignee` 
							WHERE ConsigneeID IN ($this_consignee) AND BuyerDestination!=''";
				$stmt_consignee = $this->conn->prepare($sql);
				$stmt_consignee->execute();
				$row_consignee = $stmt_consignee->fetch(PDO::FETCH_ASSOC);
				extract($row_consignee);
				
				$BuyerDestination  = rtrim($BuyerDestination,",");
				$BuyerDestination  = ltrim($BuyerDestination,",");
				
				if($BuyerDestination!=""){
					$filter_paymentterm = "AND ID IN ($BuyerDestination)";
				}
				
			}break;
			
			case "portofdischarges":
			if($this_consignee!=""){
				$sql = "SELECT GROUP_CONCAT(PortOfDestination) as PortOfDestination
							FROM `tblconsignee` 
							WHERE ConsigneeID IN ($this_consignee) AND PortOfDestination!=''";
				$stmt_consignee = $this->conn->prepare($sql);
				$stmt_consignee->execute();
				$row_consignee = $stmt_consignee->fetch(PDO::FETCH_ASSOC);
				extract($row_consignee);
				
				$PortOfDestination  = rtrim($PortOfDestination,",");
				$PortOfDestination  = ltrim($PortOfDestination,",");
				
				if($PortOfDestination!=""){
					$filter_paymentterm = "AND ID IN ($PortOfDestination)";
				}
			}break;
			
			case "shipmode":
			if($this_consignee!=""){
				$sql = "SELECT GROUP_CONCAT(ShipModeID) as ShipModeID
							FROM `tblconsignee` 
							WHERE ConsigneeID IN ($this_consignee) AND ShipModeID!=''";
				$stmt_consignee = $this->conn->prepare($sql);
				$stmt_consignee->execute();
				$row_consignee = $stmt_consignee->fetch(PDO::FETCH_ASSOC);
				extract($row_consignee);
				
				$ShipModeID  = rtrim($ShipModeID,",");
				$ShipModeID  = ltrim($ShipModeID,",");
				
				if($ShipModeID!=""){
					$filter_paymentterm = "AND ID IN ($ShipModeID)";
				}
				
			}break;
			
			case "tradeterm":
			if($this_consignee!=""){
				$sql = "SELECT GROUP_CONCAT(TradeTermID) as TradeTermID
							FROM `tblconsignee` 
							WHERE ConsigneeID IN ($this_consignee) AND TradeTermID!=''";
				$stmt_consignee = $this->conn->prepare($sql);
				$stmt_consignee->execute();
				$row_consignee = $stmt_consignee->fetch(PDO::FETCH_ASSOC);
				extract($row_consignee);
				
				$TradeTermID  = rtrim($TradeTermID,",");
				$TradeTermID  = ltrim($TradeTermID,",");
				
				if($TradeTermID!=""){
					$filter_paymentterm = "AND ID IN ($TradeTermID)";
				}
				
			}break;
			
			case "portloading":
			if($this_consignee!=""){
				$sql = "SELECT GROUP_CONCAT(PortOfLoading) as PortOfLoading
							FROM `tblconsignee` 
							WHERE ConsigneeID IN ($this_consignee) AND PortOfLoading!=''";
				$stmt_consignee = $this->conn->prepare($sql);
				$stmt_consignee->execute();
				$row_consignee = $stmt_consignee->fetch(PDO::FETCH_ASSOC);
				extract($row_consignee);
				
				$PortOfLoading  = rtrim($PortOfLoading,",");
				$PortOfLoading  = ltrim($PortOfLoading,",");
				
				if($PortOfLoading!=""){
					$filter_paymentterm = "AND ID IN ($PortOfLoading)";
				}
				
			}break;
			
			case "payer":
			if($this_consignee!=""){
				$sql = "SELECT group_concat(pyr.ID) as payerID, pyr.Description, pyr.address, pyr.brandID, pyr.fin_code, pyr.statusID
						FROM tblconsignee csn 
						INNER JOIN tblpayer pyr ON pyr.ID = csn.payer
						WHERE csn.ConsigneeID IN ($this_consignee) AND pyr.statusID = 1";
				$stmt_consignee = $this->conn->prepare($sql);
				$stmt_consignee->execute();
				$row_consignee = $stmt_consignee->fetch(PDO::FETCH_ASSOC);
				extract($row_consignee);
				
				$payerID  = rtrim($payerID,",");
				$payerID  = ltrim($payerID,",");
				
				if($payerID!=""){
					$filter_paymentterm = "AND ID IN ($payerID)";
				}
				else{
					$filter_paymentterm = " ";//limit 0
				}
			}break;
			
			case "poissuer":
			if($this_consignee!=""){
				$sql = "SELECT GROUP_CONCAT(pi.ID) as poissuerID, pi.Description, pi.address, pi.fin_code, pi.statusID 
						FROM `tblpoissuer` pi
						INNER JOIN tblconsignee csn ON csn.poissuer = pi.ID
						WHERE pi.statusID = 1 AND csn.ConsigneeID IN ($this_consignee)";
				$stmt_consignee = $this->conn->prepare($sql);
				$stmt_consignee->execute();
				$row_consignee = $stmt_consignee->fetch(PDO::FETCH_ASSOC);
				extract($row_consignee);
				
				$poissuerID  = rtrim($poissuerID,",");
				$poissuerID  = ltrim($poissuerID,",");
				
				if($poissuerID!=""){
					$filter_paymentterm = "AND ID IN ($poissuerID)";
				}
				else{
					$filter_paymentterm = " ";//limit 0
				}
			}break;
			
			case "csn_address":
			if($this_consignee!=""){
				$sql = "SELECT csn.Address
						FROM tblconsignee csn
						WHERE csn.ConsigneeID IN ($this_consignee)";
				$stmt_consignee = $this->conn->prepare($sql);
				$stmt_consignee->execute();
				$row_consignee = $stmt_consignee->fetch(PDO::FETCH_ASSOC);
				extract($row_consignee);
				
				$filter_paymentterm = $Address;
				
			}break;
			
			case "owner_address": 
			if($this_consignee!=""){
				$sql = "SELECT ID, CompanyName_ENG, Address
						FROM tblcompanyprofile
						WHERE ID='$this_consignee'";
				$stmt = $this->conn->prepare($sql);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				extract($row);
				
				$filter_paymentterm = $Address;
				
			}break;
			
			case "shipper_address":
			if($this_consignee!=""){
				$sql = "SELECT FactoryID, Address 
						FROM tblfactory 
						WHERE FactoryID='$this_consignee'";
				$stmt = $this->conn->prepare($sql);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				extract($row);
				
				$filter_paymentterm = $Address;

			}break;
			
			case "notify_address":
			if($this_consignee!=""){
				$sql = "SELECT notifyID, NotifyAddress 
						FROM tblbuyer_notify_party 
						WHERE notifyID='$this_consignee'";
				$stmt = $this->conn->prepare($sql);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				extract($row);
				
				$filter_paymentterm = $NotifyAddress;

			}break;
			
			case "getNotifyPartyID":
			if($this_consignee!=""){
				$sql = "SELECT notifyID, NotifyAddress 
						FROM tblbuyer_notify_party 
						WHERE consigneeID='$this_consignee'";
				$stmt = $this->conn->prepare($sql);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				extract($row);
				
				$filter_paymentterm = $notifyID;

			}break;
		}
		
		return $filter_paymentterm;
	}
	
	public function funcGetPaymentTerm($selected_ID, $filter_query){
		$html = '<select name="paymentterm" id="paymentterm" class="select_medium select_chosen" 
							onchange="func_write_hidden(&#39;txt_paymentterm&#39;,this.value);clearred(this.id);">';
		$sql = "SELECT ID, Description FROM tblpaymentterm WHERE (StatusID=1  $filter_query) or ID='$selected_ID'";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$selected = ($selected_ID==$ID? "selected": "");
			$html .= "<option value='$ID' $selected >$Description</option>";
		}
		
		$html .= '</select>';
		return $html;
	}
	
	public function funcGetBuyerDestination($selected_ID, $filter_query){
		$html = '<select name="buyerdestination" id="buyerdestination" class="select_medium select_chosen" 
							onchange="func_write_hidden(&#39;txt_buyerdestination&#39;,this.value);clearred(this.id);">';
		
		// echo "SELECT ID, Description FROM tblbuyerdestination WHERE (StatusID=1  $filter_query) or ID='$selected_ID'";
		$stmt = $this->conn->prepare("SELECT ID, Description FROM tblbuyerdestination WHERE (StatusID=1  $filter_query) or ID='$selected_ID'");
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$selected = ($selected_ID==$ID? "selected": "");
			$html .= "<option value='$ID' $selected >$Description</option>";
		}
		
		$html .= '</select>';
		return $html;
	}
	
	public function funcGetPortDestination($selected_ID, $filter_query){
		$html = '<select name="portofdischarges" id="portofdischarges" class="select_medium select_chosen" 
							onchange="func_write_hidden(&#39;txt_portofdischarges&#39;,this.value);clearred(this.id);">';
		
		$stmt = $this->conn->prepare("SELECT ID, Description FROM tbldestinationport WHERE (StatusID=1  $filter_query) or ID='$selected_ID'");
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$selected = ($selected_ID==$ID? "selected": "");
			$html .= "<option value='$ID' $selected >$Description</option>";
		}
		
		$html .= '</select>';
		return $html;
	}
	
	public function funcGetShipMode($selected_ID, $filter_query){
		$html = '<select name="shipmode" id="shipmode" class="select_medium select_chosen" 
							onchange="func_write_hidden(&#39;txt_shipmode&#39;,this.value);clearred(this.id);">';
		
		$stmt = $this->conn->prepare("SELECT ID, Description FROM tblshipmode WHERE (StatusID=1  ) or ID='$selected_ID'");//$filter_query
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$selected = ($selected_ID==$ID? "selected": "");
			$html .= "<option value='$ID' $selected >$Description</option>";
		}
		
		$html .= '</select>';
		return $html;
	}
	
	public function funcGetTradeTerm($selected_ID, $filter_query){
		$html = '<select name="tradeterm" id="tradeterm" class="select_medium select_chosen" 
							onchange="func_write_hidden(&#39;txt_tradeterm&#39;,this.value);clearred(this.id);">';
		
		$stmt = $this->conn->prepare("SELECT ID, Description FROM tbltradeterm WHERE (StatusID=1  $filter_query) or ID='$selected_ID'");
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$selected = ($selected_ID==$ID? "selected": "");
			$html .= "<option value='$ID' $selected >$Description</option>";
		}
		
		$html .= '</select>';
		return $html;
	}
	
	public function funcGetPortLoading($selected_ID, $filter_query){
		$html = '<select name="portloading" id="portloading" class="select_medium select_chosen" 
							onchange="func_write_hidden(&#39;txt_portloading&#39;,this.value);clearred(this.id);">';
		
		$stmt = $this->conn->prepare("SELECT ID, Description FROM tblloadingport 
										WHERE IO='OUT' AND (StatusID=1  $filter_query) or ID='$selected_ID'");
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$selected = ($selected_ID==$ID? "selected": "");
			$html .= "<option value='$ID' $selected >$Description</option>";
		}
		
		$html .= '</select>';
		return $html;
	}
	
	public function funcGetPayer($selected_ID, $filter_query){
		$html = '<select name="payer" id="payer" class="select_medium select_chosen" 
							onchange="func_write_hidden(&#39;txt_payer&#39;,this.value);clearred(this.id);">';
		$html .= '<option value="0"></option>';
		
		$stmt = $this->conn->prepare("SELECT ID, Description 
										FROM tblpayer 
										WHERE statusID=1 $filter_query");
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$selected = ($selected_ID==$ID? "selected": "");
			$html .= "<option value='$ID' $selected >$Description</option>";
		}
		
		$html .= '</select>  <font color="red" id="valid_payer"></font>
				<input type="hidden" name="txt_payer" id="txt_payer" value="'.$selected_ID.'">';
		return $html;
	}
	
	public function funcGetPoissuer($selected_ID, $filter_query){
		$html = '<select name="poissuer" id="poissuer" class="select_medium select_chosen" 
							onchange="func_write_hidden(&#39;txt_poissuer&#39;,this.value);clearred(this.id);">';
		$html .= '<option value="0"></option>';
		
		$stmt = $this->conn->prepare("SELECT ID, Description 
										FROM tblpoissuer 
										WHERE statusID=1 $filter_query");
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$selected = ($selected_ID==$ID? "selected": "");
			$html .= "<option value='$ID' $selected >$Description</option>";
		}
		
		$html .= '</select>  <font color="red" id="valid_poissuer"></font>
				<input type="hidden" name="txt_poissuer" id="txt_poissuer" value="'.$selected_ID.'">';
		return $html;
	}
	
	public $transit_etd = "";
	public function getFabricCountryOrigin($shipmentpriceID){ // buyer_oliver.php, buyer_multi_country.php
		$fabric_country = "";
		$sql = "SELECT (SELECT min(mh.vesseletd) 
					 FROM tblmr_header mh
					 INNER JOIN tblmr_detail mrd ON mrd.MRID = mh.MRID
					 WHERE mh.trackingno!='' AND mh.vesseletd!='' AND mrd.MPOHID = mpoh.MPOHID) as transit_etd,
					 
					 (SELECT group_concat(distinct ct.Description)
					 FROM tblmr_header mh
					 INNER JOIN tblmr_detail mrd ON mrd.MRID = mh.MRID
					 LEFT JOIN tblcountry ct ON ct.ID = mh.countryID
					 WHERE mh.trackingno!='' AND mh.vesseletd!='' AND mrd.MPOHID = mpoh.MPOHID) as fabric_country
					 
				FROM tblshipmentprice sp
				INNER JOIN tblmpurchase mp ON mp.orderno = sp.Orderno
				INNER JOIN tblmpurchase_detail mpd ON mpd.MPID = mp.MPID
				INNER JOIN tblmpo_detail mpod ON mpod.MPDID = mpd.MPDID
				INNER JOIN tblmpo_header mpoh ON mpoh.MPOHID = mpod.MPOHID
				INNER JOIN tblsupplier spp ON spp.SupplierID = mpoh.supplierID
				INNER JOIN tblcountry c ON c.ID = spp.countryID
				INNER JOIN tblposition ps ON ps.ID = mp.positionID
				WHERE sp.ID IN ($shipmentpriceID) AND mp.part=1 AND mpoh.statusID NOT IN (6) AND mpoh.Type=7 
				AND spp.iswarehouse=0 AND (ps.Description like '%body%'  or ps.Description like 'BOD%') limit 1";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(); 
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$fabric_country = $row["fabric_country"];
			$transit_etd    = $row["transit_etd"];
			
			$this->transit_etd = $transit_etd;
		return $fabric_country;
	}
	
	public function getBuyerPOOrderQty($shipmentpriceID){
		$arr_row = array();
		$sql = "SELECT sgc.garmentID, sgc.colorID, sgc.group_number
				FROM tblship_group_color sgc 
				WHERE sgc.shipmentpriceID='$shipmentpriceID' AND statusID=1 
				group by sgc.group_number ";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(); 
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$sqlSCSQ = "SELECT scsq.size_name, sum(scsq.qty) as qty
						FROM tblship_colorsizeqty scsq 
						WHERE scsq.shipmentpriceID='$shipmentpriceID' 
						AND scsq.garmentID='$garmentID' AND scsq.colorID='$colorID' AND scsq.statusID=1
						group by scsq.size_name
						order by scsq.ID asc";
			$stmt_scsq = $this->conn->prepare($sqlSCSQ);
			$stmt_scsq->execute(); 
			while($row_scsq = $stmt_scsq->fetch(PDO::FETCH_ASSOC)){
				extract($row_scsq);
				
				$arr_row["$group_number**^^$size_name"] = $qty;
			}
		}
		
		return $arr_row;
	}
	
	//=====================================================================================================//
	//=====================================================================================================//
	//=====================================================================================================//
	//========================= BUYER INVOICE PDF PICK LIST TEMPLATE ======================================//
	//=====================================================================================================//
	//=====================================================================================================//
	//=====================================================================================================//
	
	public function getBuyerInvoicePackingList($invID, $this_spID="", $arr_getall=array()){ //buyer_joefresh.php, buyer_joefresh_usa.php, buyer_kohls.php, buyer_hunnybunny.php
		$html = '';
		$tblbuyer_invoice    = ($this->isBuyerPayment==0? "tblbuyer_invoice": "tblbuyer_invoice_payment");
		$tblbuyer_inv_detail = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail": "tblbuyer_invoice_payment_detail");
		$tblctn              = ($this->isBuyerPayment==0? "tblcarton_inv_head": "tblcarton_inv_payment_head");
		
		$filter_sp = ($this_spID==""? "":" AND invd.shipmentpriceID='$this_spID'");
		$filter_bicid = ($this->BICID==""? "":" AND invd.BICID='".$this->BICID."'");
		$sql = "SELECT bi.invoice_no, bi.invoice_date, invd.shipmentpriceID, invd.ht_code, invd.shipping_marking, 
						'' as styleNo, sp.StyleNo as sp_garmentID, sp.Orderno as orderno, group_concat(distinct sp.Orderno) as grp_ia, sp.GTN_buyerpo as actual_BuyerPO, sp.BuyerPO, csn.Name as csn_name, csn.Address as csn_address, csn.EIN, csn.tel as csn_tel, csn.fax as csn_fax, csn.email as csn_email, csn.contactperson as csn_contactperson,
						cp.CompanyName_ENG as ownership, cp.Address as owneraddress, cp.Tel as ownertel, cp.Fax as ownerfax,
						'' as lc_number, '' as lc_date, od.FactoryID as od_FactoryID, invd.group_number, 
						'1' as lc_type, '' as LCHID,
						(SELECT count(distinct sgc.garmentID)
						FROM tblship_group_color sgc 
						WHERE sgc.shipmentpriceID = invd.shipmentpriceID 
						AND sgc.group_number = invd.group_number AND sgc.statusID=1) as count_gmt, invd.BICID
						
				FROM $tblbuyer_inv_detail invd 
				INNER JOIN $tblbuyer_invoice bi ON bi.ID = invd.invID
				LEFT JOIN tblshipmentprice sp ON sp.ID = invd.shipmentpriceID 
				LEFT JOIN tblconsignee csn ON csn.ConsigneeID = bi.ConsigneeID
				-- LEFT JOIN tblgarment g ON find_in_set(g.garmentID, sp.StyleNo)
				LEFT JOIN tblorder od ON od.Orderno = sp.Orderno
				LEFT JOIN tblcompanyprofile cp ON cp.ID = bi.issue_from
				-- INNER JOIN tblnewenquiry ne ON ne.QDID = g.QDID
				WHERE invd.invID = '$invID' AND invd.del = 0 AND invd.group_number>0 $filter_sp $filter_bicid
				GROUP BY invd.shipmentpriceID, invd.BICID 
				ORDER BY invd.ID ASC ";//limit 40, 40 
		// echo "<pre>$sql</pre>";
		$packsql = $this->conn->prepare($sql);
		$packsql->execute(); 
		while($packrow = $packsql->fetch(PDO::FETCH_ASSOC)){
			$invoice_no   = $packrow["invoice_no"];
			$invoice_date = $packrow["invoice_date"];
			
			
			$actual_BuyerPO = $packrow["actual_BuyerPO"];
			$BuyerPO        = (trim($actual_BuyerPO)==""? $packrow["BuyerPO"]: $actual_BuyerPO);

			$spID        = $packrow["shipmentpriceID"];
			$ht_code     = $packrow["ht_code"];
			$ship_remark = $packrow["shipping_marking"];
			$styleNo     = $packrow["styleNo"]; 
			$orderno     = $packrow["orderno"]; 
			$grp_ia      = $packrow["grp_ia"]; 
			$count_gmt   = $packrow["count_gmt"]; 
			$str_unit    = ($count_gmt==1? "PCS":"SETS");
			
			$group_number = $packrow["group_number"]; 
			$ownership    = $packrow["ownership"]; 
			$owneraddress = $packrow["owneraddress"]; 
			$ownertel     = $packrow["ownertel"]; 
			$ownerfax     = $packrow["ownerfax"]; 
			$csn_name     = $packrow["csn_name"]; 
			$csn_address  = $packrow["csn_address"]; 
			$contact      = $packrow["csn_contactperson"];
			$str_contact  = (trim($contact)==""? "":"<br/>Contact Person: $contact");			
			$email        = $packrow["csn_email"]; 
			$str_email    = (trim($email)==""? "":"<br/>Email: $email");
			$EIN          = $packrow["EIN"]; 
			$str_EIN      = (trim($EIN)==""? "":"<br/>EIN: $EIN");
			$csn_tel      = $packrow["csn_tel"]; 
			$str_tel      = (trim($csn_tel)==""? "":"<br/>TEL#: $csn_tel");
			$csn_fax      = $packrow["csn_fax"]; 
			$str_fax      = (trim($csn_fax)==""? "":"<br/>FAX: $csn_fax");
			$lc_number    = (trim($packrow["lc_number"])==""? "N/A":$packrow["lc_number"]); 
			$lc_date      = $packrow["lc_date"]; 
			$lc_type      = $packrow["lc_type"]; 
			$LCHID        = $packrow["LCHID"]; 
			$od_FactoryID = $packrow["od_FactoryID"]; 
			$BICID        = $packrow["BICID"];
			$sp_garmentID = $packrow["sp_garmentID"];
			$this->BICID  = $packrow["BICID"];
			
			$sp_garmentID = str_replace(",","','", $sp_garmentID);
			$sqlgmt = "SELECT GROUP_CONCAT(DISTINCT styleNo) as style 
						FROM `tblgarment` WHERE garmentID IN ('$sp_garmentID')";
			$stmt_gmt = $this->conn->prepare($sqlgmt);
			$stmt_gmt->execute();
			$row_gmt  = $stmt_gmt->fetch(PDO::FETCH_ASSOC);
				$styleNo = $row_gmt["style"];
			
			if($lc_type>0){
				$sqllc = "SELECT lci.lc_date
							FROM tbllc_assignment_info lci 
							WHERE lci.LCHID='$LCHID' AND lci.lc_type='0' AND lci.del='0'";
				$stmt_lc = $this->conn->prepare($sqllc);
				$stmt_lc->execute();
				$row_lc  = $stmt_lc->fetch(PDO::FETCH_ASSOC);
				$lc_date = $row_lc["lc_date"];
			}
			$lc_date      = (trim($lc_date)==""? "N/A": $lc_date); 
			
			$html .= '<br pagebreak="true">';
			
			$html .= '<table border="0">
						<tr>
							<th class="bold-text center-align">
								<h1>'.$this->letterhead_name.'</h1>
							</th>
						</tr>

						<tr>
							<td class="center-align">
								'.$this->letterhead_address.'
							</td>
						</tr>

						<tr>
							<td class="center-align">
								TEL : '.$this->letterhead_tel.' &nbsp;&nbsp;&nbsp;&nbsp; FAX : '.$this->letterhead_fax.'	
							</td>
						</tr>

					</table>
					
					</br>

					<h2 class="center-align"><b><u>PACKING LIST</u></b></h2>
					</br>';
					
			 $html .= '<table cellpadding="2" style="width:100%;">
						<tr>
							<td rowspan="2" style="width:5%">DATE: </td>
							<td rowspan="2" style="width:68%">'.$invoice_date.'</td>
							<td style="width:15%" align="right">INVOICE NO: </td>
							<td style="width:12%">'.$invoice_no.'</td>
						</tr>

						<tr>
							<td align="right">PURCHASE ORDER NO: </td>
							<td>'.$BuyerPO.'</td>
						</tr>

						<tr>
							<td rowspan="4">TO: </td>
							<td rowspan="4">
								'.$csn_name.' <br/>
								'.$csn_address.' '.$str_EIN.' '.$str_email.' '.$str_contact.' '.$str_tel.' '.$str_fax.'
							</td>
							<td align="right">VENDOR STYLE NO.: </td>
							<td>'.$styleNo.'</td>
						</tr>

						<tr>
							<td align="right">LC NO.: </td>
							<td>'.$lc_number.'</td>
						</tr>
						
						<tr>
							<td align="right">DATE:</td>
							<td>'.$lc_date.'</td>
							</tr>
						<tr>
							<td align="right">IA#:</td>
							<td>'.$grp_ia.'</td>
							</tr>
					</table>';
					
			$arrsize = [];
			$arrpick_totalsizeqty = []; // 
			$arrpick_totalcsq = []; // total color size qty in this packing list
			
			$size_thead = "";
			$size_thead_summary = "";
			$sizesql = $this->handle_shipment->getSizeNameColumnFromOrder($orderno, 1);
			$size_colspan = 0;//$sizesql->rowCount();
			while($sizerow = $sizesql->fetch(PDO::FETCH_ASSOC)){
				$size_name = $sizerow["SizeName"];
				
				$sqlscsq  = "SELECT sum(scsq.qty) as qty 
							FROM tblship_colorsizeqty scsq 
							WHERE scsq.shipmentpriceID='$spID' and scsq.size_name='$size_name' 
							AND scsq.statusID=1 ";
				$stmt_scsq = $this->conn->prepare($sqlscsq);
				$stmt_scsq->execute();
				$row_scsq = $stmt_scsq->fetch(PDO::FETCH_ASSOC);
					$this_qty = $row_scsq["qty"];
				
				if($this_qty>0){
					$arrsize[] = $size_name;				
					$arrpick_totalsizeqty[$size_name] = 0;

					$size_colspan++;
				}
			}//--- End While Size Range ---//
			
			$emptyrow = "<td></td><td></td><td></td><td></td>";
			
			$css_size_wd = (count($arrsize)==1? 10: 3);
			foreach ($arrsize as $size_name) {
				$size_thead .= '<td class="border_btm" align="center" style="width:'.$css_size_wd.'%; ">'.$size_name.'</td>';
				$size_thead_summary .= '<td class="all_border" align="center" style="width: 5%; ">'.$size_name.'</td>';
				$emptyrow .= '<td style="width: 4%; "></td>';
			}
			$emptyrow .= "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
			$thead_sizewidth = (count($arrsize)==1? 10: $size_colspan * 3);
			//29
			//34 = 63
			$balance_width = 100 - (63 + $thead_sizewidth);
			$each_extra = floor(($balance_width / 11) * 10)/10;
			
			
			$ctn_width = 6 + $each_extra;
			$upc_width = 9 + $each_extra;
			$col_width = 8 + $each_extra;
			$sub_width = 4 + $each_extra;
			$cbm_width = 2 + $each_extra;
			$oth_width = 5 + $each_extra;
			
			$html .= '<table cellpadding="2" cellspacing="0" style="width:100%;font-size:7px">
						<thead>
							<tr >
								<td class="border_top border_btm" rowspan="2" style="width:'.$ctn_width.'%;">CARTON NO.</td>
								<td class="border_top border_btm" rowspan="2" style="width:'.$ctn_width.'%">NG ITEMS</td>
								<td class="border_top border_btm" rowspan="2" style="width:'.$upc_width.'%">UPC NO.</td>
								<td class="border_top border_btm" rowspan="2" style="width:'.$col_width.'%">COLOR</td>
								<td class="border_top " align="center" colspan="'.$size_colspan.'" style="width: '.$thead_sizewidth.'%; ">SIZE QUANTITY</td>
								<td class="border_top border_btm" rowspan="2" style="width:'.$sub_width.'%">SUB TOTAL <br>PER CTN</td>
								<td class="border_top border_btm" rowspan="2" style="width:'.$sub_width.'%">NO OF <br>CARTON</td>
								<td class="border_top border_btm" rowspan="2" style="width:'.$cbm_width.'%">T.CBM<br/>(CUBE)</td>
								<td class="border_top border_btm" rowspan="2" style="width:'.$oth_width.'%" align="center">T.QTY<br>('.$str_unit.')</td>
								<td class="border_top border_btm" rowspan="2" style="width:'.$oth_width.'%">GW (KGS)</td>
								<td class="border_top border_btm" rowspan="2" style="width:'.$oth_width.'%">NW (KGS)</td>
								<td class="border_top border_btm" rowspan="2" style="width:'.$oth_width.'%">T.GW (KGS)</td>
								<td class="border_top border_btm" rowspan="2" style="width:'.$oth_width.'%">T.NW (KGS)</td>
								<!--<td class="border_top border_btm" rowspan="2">NNW (KGS)</td>
								<td class="border_top border_btm" rowspan="2">TNNW (KGS)</td>-->
							</tr>
							<tr>
								'.$size_thead.'
							</tr>
						</thead>

						<!--<tr>
							'.$emptyrow.'
						</tr>-->';
						
			$pack_ctn_qty = 0; // qty of ctn used in one po
			$pack_netweight = 0;
			$pack_grossweight = 0;
			$pack_netnetweight = 0;
			$pack_totalpcs = 0;
			$totalpackqty = 0;
			$totalcbm = 0;

			$arrtotalsizeqty = [];
			$query_filter = " AND cpt.shiped='1'";
			//list($arr_row, $arr_all_size, $ctn_qty) = $handle_class->getAllPackingInfoByBuyerPO($spID, $od_FactoryID);
			//$arr_all = $this->handle_shipment->getAllPackingInfoByBuyerPO($spID, $od_FactoryID, $query_filter);
			//$arr_all = $this->handle_shipment->getAllCuttingPickListByBuyerPO($spID);
			//$arr_row = $arr_all["arr_row"];
			// echo count($arr_getall);
			$arr_row = array();
			
			// if($this->acctid==1){
				// echo "$BICID - $spID << <br/>";
				// print_r($arr_getall["$BICID-$spID"]["arr_list"]);
				// continue;
			// }
			
			if(!array_key_exists("$BICID-$spID", $arr_getall)){
				$arr_getall["$BICID-$spID"]["arr_list"] = array();
			}
			
			if(count($arr_getall["$BICID-$spID"]["arr_list"])>0){
				$arr_row = $arr_getall["$BICID-$spID"]["arr_list"];
			}
			else{
				$arr_all = $this->getBuyerInvoicePackingListDataFromCartonInv($spID, $invID, $tblctn, $tblbuyer_inv_detail);
				$arr_row = $arr_all["arr_list"];
			}
			
			// $arr_row = array();
			$arr_ctn_measurement = array();
			for($arr=0;$arr<count($arr_row);$arr++){
				$ctn_range     = $arr_row[$arr]["ctn_range"];
				$count_ctn     = $arr_row[$arr]["count_ctn"];
				$SKU           = $arr_row[$arr]["SKU"];
				$prepack_name  = $arr_row[$arr]["prepack_name"];
				$this_ctn_qty  = $arr_row[$arr]["this_ctn_qty"];
				$total_qty     = $arr_row[$arr]["total_qty"];
				$ext_length    = round($arr_row[$arr]["ext_length"], 1);
				$ext_width     = round($arr_row[$arr]["ext_width"], 1);
				$ext_height    = round($arr_row[$arr]["ext_height"], 1);
				$this_nnw      = $arr_row[$arr]["this_nnw"];
				$one_nnw       = round($this_nnw / $count_ctn, 3);
				$this_nw       = $arr_row[$arr]["this_nw"];
				$one_nw        = round($this_nw / $count_ctn, 2);//2
				$this_gw       = $arr_row[$arr]["this_gw"];
				$one_gw        = round($this_gw / $count_ctn, 2);//2
				
				$this_nw       = round($one_nw * $count_ctn, 2);//2
				$this_gw       = round($one_gw * $count_ctn, 2);//2
				//$this_cbm      = $arr_row[$arr]["cbm_total"];
				$this_cbm      = ((($ext_length/100) * ($ext_width/100) * ($ext_height/100)) * $count_ctn);
				$this_cbm      = round($this_cbm, 2);
				
				$arr_grp_color = $arr_row[$arr]["arr_grp_color"];
				$count_grp     = count($arr_row[$arr]["arr_grp_color"]);
				$arr_size_info = $arr_row[$arr]["arr_size_info"];
				
				$ctn_measurement = "$ext_length x $ext_width x $ext_height (cm)";
				
				if(!in_array("$ctn_measurement", $arr_ctn_measurement)){
					$arr_ctn_measurement[] = $ctn_measurement;
				}
				
				// echo "$ctn_range << <br/>";
				
			$html .= '<tr>
					<td rowspan="'.$count_grp.'" style="width:'.$ctn_width.'%">'.$ctn_range.'</td>
					<td rowspan="'.$count_grp.'" style="width:'.$ctn_width.'%">'.$SKU.'</td>
					<td rowspan="'.$count_grp.'" style="width:'.$upc_width.'%">'.$prepack_name.'</td>';
				for($g=0;$g<count($arr_grp_color);$g++){
					list($group_number, $sku) = explode("**%%^^",$arr_grp_color[$g]);
					
					// $sqlSGC = "SELECT group_concat(c.colorName,' ' separator '<br/>') as grp_color 
								// FROM tblship_group_color sgc 
								// INNER JOIN tblcolor c ON c.ID = sgc.colorID
								// INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
								// WHERE sgc.shipmentpriceID='$spID' AND sgc.group_number='$group_number' AND sgc.statusID=1";
					// $stmt_sgc = $this->conn->query($sqlSGC);
					// $row_sgc  = $stmt_sgc->fetch(PDO::FETCH_ASSOC);
						// $grp_color = $row_sgc["grp_color"];
					$arr_color = $this->getPOPrice($spID, $group_number);
					$grp_color = $arr_color["colorOnly"];
					
					if($g>0){
						$html .= "<tr>";
					}
						
					$html .= '<td style="width:'.$col_width.'%">'.$grp_color.'</td>';
					foreach ($arrsize as $size) {
						$this_qty = (array_key_exists("$size", $arr_size_info["$group_number"])? $arr_size_info["$group_number"]["$size"]: 0);
						$this_qty = ($this_qty==""? 0 : $this_qty);
						$html .= '<td class="center-align" style="width: '.$css_size_wd.'%; ">'.$this_qty.'</td>';
						
						if(!array_key_exists("$group_number^=$grp_color", $arrpick_totalcsq)){
							$arrpick_totalcsq["$group_number^=$grp_color"][$size] = $this_qty * $count_ctn;
						}
						else{
							
							if(!array_key_exists("$size", $arrpick_totalcsq["$group_number^=$grp_color"])){
								$arrpick_totalcsq["$group_number^=$grp_color"][$size] = $this_qty * $count_ctn;
							}
							else{
								$arrpick_totalcsq["$group_number^=$grp_color"][$size] += $this_qty * $count_ctn;
							}
						}
					}//--- End Foreach size qty ---//
						
					if($g>0){
						$html .= "</tr>";
					}
					else{
						$str_onegw = number_format($one_gw, 2);//2, modified to 3 20220302 request by MAO
						$str_onenw = number_format($one_nw, 2);//2, change back to 2 20220321 request by MAO
						$str_gw    = number_format($this_gw, 2);//2
						$str_nw    = number_format($this_nw, 2);//2
						
						$html .= '
						<td align="center" rowspan="'.$count_grp.'" style="width:'.$sub_width.'%">'.$this_ctn_qty.'</td>
						<td align="center" rowspan="'.$count_grp.'" style="width:'.$sub_width.'%">'.$count_ctn.'</td>
						<td align="center" rowspan="'.$count_grp.'" style="width:'.$cbm_width.'%">'.$this_cbm.'</td>
						<td align="center" rowspan="'.$count_grp.'" style="width:'.$oth_width.'%">'.$total_qty.'</td>
						<td align="center" rowspan="'.$count_grp.'" style="width:'.$oth_width.'%">'.$str_onegw.'</td>
						<td align="center" rowspan="'.$count_grp.'" style="width:'.$oth_width.'%">'.$str_onenw.'</td>
						<td align="center" rowspan="'.$count_grp.'" style="width:'.$oth_width.'%">'.$str_gw.'</td>
						<td align="center" rowspan="'.$count_grp.'" style="width:'.$oth_width.'%">'.$str_nw.'</td>
						<!--<td align="center" rowspan="'.$count_grp.'">'.$one_nnw.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$this_nnw.'</td>-->
						</tr>';
						
					}
				}//--- End For Color ---//

				$pack_ctn_qty += $count_ctn;
				$totalpackqty += $total_qty;
				$pack_netweight += $this_nw;
				$pack_grossweight += $this_gw;
				$pack_netnetweight += $this_nnw;
				$totalcbm += $this_cbm;

				
			}//--- End Outer For Row ---//
			
			$strpack_grossweight = number_format($pack_grossweight, 2);//2, modified to 3 20220302 request by MAO
			$strpack_netweight   = number_format($pack_netweight, 2);
			
				$html .= '<!--<tr> '.$emptyrow.' </tr>-->
						  <tr>
							<td colspan="4">&nbsp;</td>
							<td colspan="'.$size_colspan.'"></td>
							<td colspan="8"></td>
							</tr>
							
						  <tr class="font-blue_">
							<td align="center"></td>
							<td align="center"></td>
							<td class="border_top border_btm" align="center">TOTAL </td>
							<td></td>
							<td colspan="'.$size_colspan.'"></td>
							<td></td>
							<td class="border_top border_btm" align="center">'.$pack_ctn_qty.'</td>
							<td class="border_top border_btm" align="center">'.$totalcbm.'</td>
							<td class="border_top border_btm" align="center">'.$totalpackqty.'</td>
							<td></td>
							<td></td>
							<td class="border_top border_btm" align="center">'.$strpack_grossweight.'</td>
							<td class="border_top border_btm" align="center">'.$strpack_netweight.'</td>
							<!--<td></td>
							<td align="center">'.$pack_netnetweight.'</td>-->
							</tr>
						</table>
					<br>
					<br>';
				
				////////////////////////////////////////////////
				////======== Color & Size Breakdown ========////
				////////////////////////////////////////////////
				$html .= '<table border="0">
							<tr>
								<td style="width:20%"></td>
								<td align="center" style="width:80%">';
				$num_colspan = count($arrsize) + 2;
				$html .= '
						<table cellpadding="2" cellspacing="0">
							<thead>
								<tr>
									<td colspan="'.$num_colspan.'" align="left"><u>COLOR & SIZE BREAKDOWN</u></td>
									</tr>
								<tr>
									<td class="all_border" style="width: 20%; ">COLOR</td>
									'.$size_thead_summary.'
									<td class="all_border" align="center">TOTAL</td>
								</tr>
							</thead>';
				
				foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
					$color = explode("^=", $strcolor);
					$ColorName = $color[1];
					$str_csq = "";
					foreach ($arrsize as $size) {
						$sizeqty = $sizelist[$size];
						$str_csq .= '<td class="all_border" align="center" style="width: 5%; ">'.$sizeqty.'</td>';
					}

					$totalcolorqty = array_sum($sizelist);
					$html .= '<tr>
								<td class="all_border" style="width: 20%; ">'.$ColorName.'</td>
								'.$str_csq.'
								<td class="all_border" align="center">'.$totalcolorqty.'</td>
								</tr>';
					
				}//--- End Foreach Color Name ---// 
				$html .= '<tr><td class="all_border"><b>TOTAL </b></td>';
				
				$totalcolorsizeqty = 0;
				foreach ($arrsize as $size) {
					$totalsizeqty = 0;
					foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
						$totalsizeqty += $sizelist[$size];
					}
					$totalcolorsizeqty += $totalsizeqty;
					$html .= '<td class="all_border" align="center">'.$totalsizeqty.'</td>';
				}
				
				$html .= '<td class="all_border" align="center">'.$totalcolorsizeqty.'</td>
						</tr>
						</table>
					</td></tr></table>
					<br>
					<br>';
					
				$ctn_measurement = "";
				$count_m = 0;
				// $stmt_cpt = $this->conn->query("SELECT cpt.ext_length, cpt.ext_width, cpt.ext_height
												// FROM tblcarton_picklist_head_prod cpt 
												// WHERE cpt.shipmentpriceID='$spID'
												// group by cpt.ext_length, cpt.ext_width, cpt.ext_height");
				// while($row_cpt = $stmt_cpt->fetch(PDO::FETCH_ASSOC)){
					// extract($row_cpt);
					// $count_m++;
					// $str_separator    = ($count_m==1? "": " / ");
					// $ctn_measurement .= $str_separator."".round($ext_length,1)." x ".round($ext_width)." x ".round($ext_height)." (cm)";
				// }
				
				$str_ctn_measurement = implode("<br/>", $arr_ctn_measurement);
				$totalcbm = round($totalcbm, 2);
				
				$pack_grossweight  = number_format($pack_grossweight, 2);//2, modified to 3 20220302 request by MAO
				$pack_netweight    = number_format($pack_netweight, 2);
				$pack_netnetweight = number_format($pack_netnetweight, 2);
				
				$html .= '<table border="0" cellpadding="2" cellspacing="0">
							<tr>
								<td style="width: 25%; ">CTN MEASUREMENT <br/>(MASTER CARTON DIMENSION)</td>
								<td style="width: 30%; ">'.$str_ctn_measurement.'</td>
							</tr>

							<tr>
								<td>TTL CBM</td>
								<td>'.$totalcbm.'</td>
							</tr>

							<tr>
								<td>TOTAL GW: </td>
								<td>'.$pack_grossweight.' KGS</td>
							</tr>

							<tr>
								<td>TOTAL NW: </td>
								<td>'.$pack_netweight.' KGS</td>
							</tr>

							<tr>
								<td>TOTAL NNW: </td>
								<td>'.$pack_netnetweight.' KGS</td>
							</tr>

						</table>';
					
			
			
		}//--- End While Buyer Invoice Detail ---//
		
		return $html;
	}//--- End Function getBuyerInvoicePackingList ---//
	
	public function getBuyerInvoicePackingListTemplate2($invID){ //buyer_dxl.php, buyer_noble.php
		$html = '';
		
		$tblbuyer_invoice        = ($this->isBuyerPayment==0? "tblbuyer_invoice": "tblbuyer_invoice_payment");
		$tblbuyer_invoice_detail = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail": "tblbuyer_invoice_payment_detail");
		$sql = "SELECT bi.invoice_no, bi.invoice_date, bi.shippeddate, invd.shipmentpriceID, invd.ht_code, invd.shipping_marking, 
						g.styleNo, g.orderno, sp.GTN_buyerpo as BuyerPO, csn.Name as csn_name, csn.Address as csn_address,
						fty.FactoryName_ENG as ownership, fty.Address as owneraddress, fty.Tel as ownertel, fty.Fax as ownerfax,
						lch.lc_number, od.FactoryID as od_FactoryID,
						fty2.FactoryName_ENG as exporter, bi.shipper_address as exporter_address, fty.Tel as exporter_tel, fty.Fax as exporter_fax,
						sm.Description as shipmode, cty.Description as manucountry, bi.ship_to, bi.ship_address, invd.BICID,
						csn.EIN, csn.tel as csn_tel, csn.fax as csn_fax
						
				FROM $tblbuyer_invoice_detail invd 
				INNER JOIN $tblbuyer_invoice bi ON bi.ID = invd.invID
				LEFT JOIN tblshipmentprice sp ON sp.ID = invd.shipmentpriceID 
				LEFT JOIN tblconsignee csn ON csn.ConsigneeID = bi.ConsigneeID
				LEFT JOIN tblgarment g ON g.orderno = sp.Orderno
				LEFT JOIN tblorder od ON od.Orderno = sp.Orderno
				LEFT JOIN tblfactory fty ON fty.FactoryID = od.contract_owner
				LEFT JOIN tblfactory fty2 ON fty2.FactoryID = bi.shipper
				LEFT JOIN tblfactory ftym ON ftym.FactoryID = od.manufacturer
				LEFT JOIN tblcountry cty ON cty.ID = ftym.countryID
				LEFT JOIN tbllc_assignment_detail lcd ON lcd.shipmentpriceID = invd.shipmentpriceID AND lcd.del=0 AND invd.del=0
				LEFT JOIN tbllc_assignment_info lci ON lci.LCIID = lcd.LCIID AND lci.del=0
				LEFT JOIN tbllc_assignment_head lch ON lch.LCHID = lci.LCHID
				LEFT JOIN tblshipmode sm ON sm.ID = bi.shipmodeID 
				WHERE invd.invID = '$invID' AND invd.del = 0 AND invd.group_number>0
				GROUP BY invd.shipmentpriceID 
				ORDER BY invd.ID ASC ";
		$packsql = $this->conn->prepare($sql);
		$packsql->execute(); 
		while($packrow = $packsql->fetch(PDO::FETCH_ASSOC)){
			$invoice_no   = $packrow["invoice_no"];
			$invoice_date = $packrow["invoice_date"];
			
			$BuyerPO     = $packrow["BuyerPO"];
			$shippeddate = $packrow["shippeddate"];
			$spID        = $packrow["shipmentpriceID"];
			$ht_code     = $packrow["ht_code"];
			$ship_remark = $packrow["shipping_marking"];
			$styleNo     = $packrow["styleNo"]; 
			$orderno     = $packrow["orderno"]; 
			$shipmode    = $packrow["shipmode"]; 
			
			$ownership    = $packrow["ownership"]; 
			$owneraddress = $packrow["owneraddress"]; 
			$ownertel     = $packrow["ownertel"]; 
			$ownerfax     = $packrow["ownerfax"]; 
			$csn_name     = $packrow["csn_name"]; 
			$csn_address  = $packrow["csn_address"]; 
			$lc_number    = $packrow["lc_number"]; 
			$od_FactoryID = $packrow["od_FactoryID"]; 
			$manucountry  = $packrow["manucountry"]; 
			$this->BICID  = $packrow["BICID"]; 
			$EIN          = $packrow["EIN"]; 
			$csn_tel      = $packrow["csn_tel"]; 
			$csn_fax      = $packrow["csn_fax"]; 
			
			$exporter      = strtoupper($packrow["exporter"]); 
			$exporter_addr = strtoupper($packrow["exporter_address"]); 
			$ship_to     = strtoupper($packrow["ship_to"]); 
			$ship_addr   = strtoupper($packrow["ship_address"]); 
			
			$exporter_tel = $packrow["exporter_tel"]; 
			$exporter_fax = $packrow["exporter_fax"]; 
			
			$html .= '<br pagebreak="true">';
			
			$sqlCH = "SELECT ctn_range 
						FROM tblcarton_picklist_head cph 
						WHERE cph.shipmentpriceID='$spID' order by ctn_num desc limit 1";
			$stmt_ch = $this->conn->prepare($sqlCH);
			$stmt_ch->execute(); 
			$row_ch = $stmt_ch->fetch(PDO::FETCH_ASSOC);
				$ctn_range = $row_ch["ctn_range"];
				list($s, $order_ctn_qty) = explode("-", $ctn_range);
			
			$arr_csqorder = $this->getBuyerPOOrderQty($spID);
			$total_order_qty = array_sum($arr_csqorder);
			
			//$query_filter = " AND cpt.shiped='1'";
			//$arr_all = $this->handle_shipment->getAllPackingInfoByBuyerPO($spID, $od_FactoryID, $query_filter);
			// $arr_all   = $this->handle_shipment->getAllCuttingPickListByBuyerPO($spID);
			// $arr_row   = $arr_all["arr_row"];
			// $grand_qty = $arr_all["grand_qty"];
			// $ctn_qty   = $arr_all["ctn_qty"];
			
			$arr_all = $this->getBuyerInvoicePackingListDataFromCartonInv($spID, $invID);
			$arr_row   = $arr_all["arr_list"];
			$grand_qty = $arr_all["grand_qty"];
			$ctn_qty   = $arr_all["ctn_qty"];
			
			
			$html .= '<table border="0" width="100%" cellpadding="3">
						<tr>
							<td align="right" style="width:15%">
								SHIPPER / EXPORTER
							</td >
							<td style="width:35%">
								'.$exporter.'<br/>'.$exporter_addr.'
							</td>
							<td align="right" style="width:15%">
								SHIP TO
							</td>
							<td style="width:35%">
								'.$ship_to.'<br/>'.$ship_addr.'
							</td>
						</tr>
					</table>
					
					</br>

					<h2 class="center-align"><b><u>DETAIL PACKING LIST</u></b></h2>
					</br>';
			 $html .= '<table cellpadding="3" border="1">
						<tr>
							<td align="center">INVOICE NO.</td>
							<td align="center">CTN QTY</td>
							<td align="center">ORDER QTY</td>
							<td align="center">SHIP CTN</td>
							<td align="center">SHIP QTY</td>
							<td align="center">SHIP DATE</td>
							<td align="center">STYLE NO.</td>
							<td align="center">PO#</td>
							<td align="center">SHIP MODE</td>
							<td align="center">COUNTRY OF ORIGIN</td>
							</tr>';
			 $html .= '<tr>
							<td align="center">'.$invoice_no.'</td>
							<td align="center">'.$order_ctn_qty.'</td>
							<td align="center">'.$total_order_qty.'</td>
							<td align="center">'.$ctn_qty.'</td>
							<td align="center">'.$grand_qty.'</td>
							<td align="center">'.$shippeddate.'</td>
							<td align="center">'.$styleNo.'</td>
							<td align="center">'.$BuyerPO.'</td>
							<td align="center">'.$shipmode.'</td>
							<td align="center">'.$manucountry.'</td>
							</tr>
						</table>';
			 $html .= '<br/><br/>';
					
			$arrsize = [];
			$arrpick_totalsizeqty = []; // 
			$arrpick_totalcsq = []; // total color size qty in this packing list
			
			$size_thead = "";
			$size_thead_summary = "";
			$sizesql = $this->handle_shipment->getSizeNameColumnFromOrder($orderno, 1);
			$size_colspan = 0;
			while($sizerow = $sizesql->fetch(PDO::FETCH_ASSOC)){
				$size_name = $sizerow["SizeName"];
				
				$sqlscsq  = "SELECT sum(scsq.qty) as qty 
							FROM tblship_colorsizeqty scsq 
							WHERE scsq.shipmentpriceID='$spID' and scsq.size_name='$size_name' 
							AND scsq.statusID=1 ";
				$stmt_scsq = $this->conn->prepare($sqlscsq);
				$stmt_scsq->execute();
				$row_scsq = $stmt_scsq->fetch(PDO::FETCH_ASSOC);
					$this_qty = $row_scsq["qty"];
				
				if($this_qty>0){
					$arrsize[] = $size_name;
				
					$arrpick_totalsizeqty[$size_name] = 0;

					$size_thead .= '<td align="center" style="width: 5%; ">'.$size_name.'</td>';
					$size_thead_summary .= '<td align="center" style="width: 5%; ">'.$size_name.'</td>';
					$size_colspan++;
				}
			}//--- End While Size Range ---//
			
			$emptyrow = "<td></td><td></td><td></td><td></td>";
			foreach ($arrsize as $value) {
				$emptyrow .= '<td style="width: 5%; "></td>';
			}
			$emptyrow .= "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
			$thead_sizewidth = $size_colspan * 5;
			
			$html .= '<table cellpadding="2" cellspacing="0" border="1" style="width:100%">
						<thead>
							<tr>
								<td rowspan="2" align="center" style="width:5%">CTN NO.</td>
								<td rowspan="2" style="width:5%">CTN QTY</td>
								<td rowspan="2" align="center">UPC NO.</td>
								<td rowspan="2" align="center" style="width:8%">SKU</td>
								<td rowspan="2" style="width:10%">COLOR</td>
								<td align="center" colspan="'.$size_colspan.'" style="width: '.$thead_sizewidth.'%; ">SIZE QUANTITY</td>
								
								<td align="center">PCS</td>
								<td align="center">TOTAL</td>
								<td rowspan="2">T.NW (KGS)</td>
								<td rowspan="2">T.GW (KGS)</td>
								<td rowspan="2">CBM</td>
								<td rowspan="2" style="width:10%">CTN MEASUREMENT</td>
							</tr>
							<tr>
								'.$size_thead.'
								<td align="center">PER CTN</td>
								<td align="center">PCS</td>
							</tr>
						</thead>

						<!--<tr>
							'.$emptyrow.'
						</tr>-->';
						
			$pack_ctn_qty = 0; // qty of ctn used in one po
			$pack_netweight = 0;
			$pack_grossweight = 0;
			$pack_netnetweight = 0;
			$pack_totalpcs = 0;
			$totalpackqty = 0;
			$totalcbm = 0;

			$arrtotalsizeqty = [];
			
			for($arr=0;$arr<count($arr_row);$arr++){
				$ctn_range     = $arr_row[$arr]["ctn_range"];
				$count_ctn     = $arr_row[$arr]["count_ctn"];
				$SKU           = $arr_row[$arr]["SKU"];
				$prepack_name  = $arr_row[$arr]["prepack_name"];
				$this_ctn_qty  = $arr_row[$arr]["this_ctn_qty"];
				$total_qty     = $arr_row[$arr]["total_qty"];
				$this_nnw      = $arr_row[$arr]["this_nnw"];
				$one_nnw       = round($this_nnw / $count_ctn, 3);
				$this_nw       = $arr_row[$arr]["this_nw"];
				$one_nw        = round($this_nw / $count_ctn, 3);
				$this_gw       = $arr_row[$arr]["this_gw"];
				$one_gw        = round($this_gw / $count_ctn, 3);
				$this_cbm      = $arr_row[$arr]["cbm_total"];
				$ctn_measurement = $arr_row[$arr]["ctn_measurement"];
				$ext_length = $arr_row[$arr]["ext_length"];
				$ext_width  = $arr_row[$arr]["ext_width"];
				$ext_height = $arr_row[$arr]["ext_height"];
				$ctn_cm     = round($ext_length)." x ".round($ext_width)." x ".round($ext_height)." (cm)";
				
				$arr_grp_color = $arr_row[$arr]["arr_grp_color"];
				$count_grp     = count($arr_row[$arr]["arr_grp_color"]);
				$arr_size_info = $arr_row[$arr]["arr_size_info"];
				
			$html .= '<tr>
					<td rowspan="'.$count_grp.'" style="width:5%">'.$ctn_range.'</td>
					<td align="center" rowspan="'.$count_grp.'" style="width:5%">'.$count_ctn.'</td>
					<td rowspan="'.$count_grp.'">'.$prepack_name.'</td>
					<td rowspan="'.$count_grp.'" style="width:8%">'.$SKU.'</td>';
				for($g=0;$g<count($arr_grp_color);$g++){
					list($group_number, $sku) = explode("**%%^^",$arr_grp_color[$g]);
					
					$sqlSGC = "SELECT group_concat(c.colorName,' (',g.styleNo,')' separator '<br/>') as grp_color 
								FROM tblship_group_color sgc 
								INNER JOIN tblcolor c ON c.ID = sgc.colorID
								INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
								WHERE sgc.shipmentpriceID='$spID' AND sgc.group_number='$group_number' AND sgc.statusID=1";
					$stmt_sgc = $this->conn->query($sqlSGC);
					$row_sgc  = $stmt_sgc->fetch(PDO::FETCH_ASSOC);
						$grp_color = $row_sgc["grp_color"];
					
					if($g>0){
						$html .= "<tr>";
					}
						
					$html .= '<td style="width:10%">'.$grp_color.'</td>';
					foreach ($arrsize as $size) {
						$this_qty = (array_key_exists($size,$arr_size_info["$group_number"]) ? 
										$arr_size_info["$group_number"]["$size"]:"");
						$html .= '<td class="center-align" style="width: 5%; ">'.$this_qty.'</td>';
						
						//if(array_key_exists("$group_number^=$grp_color", $arrpick_totalcsq)){
							//if(array_key_exists($size ,$arrpick_totalcsq["$group_number^=$grp_color"])){
								$arrpick_totalcsq["$group_number^=$grp_color"][$size] += $this_qty * $count_ctn;
							//}
						//}
						
					}//--- End Foreach size qty ---//
						
					if($g>0){
						$html .= "</tr>";
					}
					else{
						$html .= '
						<td align="center" rowspan="'.$count_grp.'">'.$this_ctn_qty.'</td>
						
						<td align="center" rowspan="'.$count_grp.'">'.$total_qty.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$this_nw.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$this_gw.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$this_cbm.'</td>
						<td align="center" rowspan="'.$count_grp.'" style="width:10%">'.$ctn_cm.'</td>
						</tr>';
						
					}
				}//--- End For Color ---//

				$pack_ctn_qty += $count_ctn;
				$totalpackqty += $total_qty;
				$pack_netweight += $this_nw;
				$pack_grossweight += $this_gw;
				$pack_netnetweight += $this_nnw;
				$totalcbm += $this_cbm;

				
			}//--- End Outer For Row ---//
				$html .= '<!--<tr> '.$emptyrow.' </tr>-->
						  <tr class="font-blue">
							<td align="center" colspan="1">TOTAL</td>
							<td align="center">'.$pack_ctn_qty.'</td>
							<td></td>
							<td></td>
							<td></td>
							<td colspan="'.$size_colspan.'"></td>
							<td align="center"></td>
							<td align="center">'.$totalpackqty.'</td>
							<td align="center">'.$pack_grossweight.'</td>
							<td align="center">'.$pack_netweight.'</td>
							<td align="center">'.$totalcbm.'</td>
							<td align="center"></td>
							</tr>
						</table>
					<br>
					<br>';
				
				////////////////////////////////////////////////
				////======== Color & Size Breakdown ========////
				////////////////////////////////////////////////
				$html .= '<h3><u>SUMMARY OF COLOR & SIZE BREAKDOWN </u></h3>
						<table cellpadding="2" cellspacing="0" border="1">
							<!--<thead>-->
								<tr>
									<td style="width: 20%; ">COLOR</td>
									<td ></td>
									'.$size_thead_summary.'
									<td align="center">TOTAL</td>
								</tr>
							<!--</thead>-->';
				
				foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
					$color = explode("^=", $strcolor);
					$group_number = $color[0];
					$ColorName    = $color[1];
					$str_csq = "";
					$str_csqorder = "";
					$str_csqshort = "";
					$str_csqpercent = "";
					$subtotal_orderqty = 0;
					$subtotal_shortqty = 0;
					foreach ($arrsize as $size) {
						$sizeqty = $sizelist[$size];
						$orderqty = $arr_csqorder["$group_number**^^$size"];
						$this_short = $sizeqty - $orderqty;
						$this_percent = round($sizeqty / $orderqty * 100, 2);
						
						$subtotal_orderqty += $orderqty;
						$subtotal_shortqty += $this_short;
						
						$str_csq .= '<td align="center" style="width: 5%; ">'.$sizeqty.'</td>';
						$str_csqorder .= '<td align="center" style="width: 5%; ">'.$orderqty.'</td>';
						$str_csqshort .= '<td align="center" style="width: 5%; ">'.$this_short.'</td>';
						$str_csqpercent .= '<td align="center" style="width: 5%; ">'.$this_percent.'%</td>';
					}

					$totalcolorqty = array_sum($sizelist);
					$grand_percent = round($totalcolorqty / $subtotal_orderqty * 100, 2);
					$html .= '<tr>
								<td rowspan="4" style="width: 20%;">'.$ColorName.'</td>
								<td>Order Qty</td>
								'.$str_csqorder.'
								<td align="center">'.$subtotal_orderqty.'</td>
								</tr>';
					$html .= '<tr>
								<td>Ship Qty</td>
								'.$str_csq.'
								<td align="center">'.$totalcolorqty.'</td>
								</tr>';
					$html .= '<tr>
								<td>Short / Excess</td>
								'.$str_csqshort.'
								<td align="center">'.$subtotal_shortqty.'</td>
								</tr>';
					$html .= '<tr>
								<td>Percentage</td>
								'.$str_csqpercent.'
								<td align="center">'.$grand_percent.'%</td>
								</tr>';
					
				}//--- End Foreach Color Name ---// 
				$this_colspan = count($arrsize) + 3;
				$html .= '<tr>
							<td colspan="'.$this_colspan.'"></td>
							</tr>';
				
				//================================================//
				//--------------- TOTAL ORDER QTY ----------------//
				$html .= '<tr>
							<td style="width: 20%; "><b>TOTAL ORDER QTY</b></td>
							<td></td>';
				$totalcolorsizeqty_order = 0;
				foreach ($arrsize as $size) {
					$totalsizeqty = 0;
					foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
						$color = explode("^=", $strcolor);
						$group_number = $color[0];
						
						$totalsizeqty += $arr_csqorder["$group_number**^^$size"];
					}
					$totalcolorsizeqty_order += $totalsizeqty;
					$html .= '<td align="center">'.$totalsizeqty.'</td>';
				}
				$html .= '<td align="center">'.$totalcolorsizeqty_order.'</td>
						</tr>';
				
				//================================================//
				//--------------- TOTAL SHIP QTY -----------------//
				$html .= '<tr>
							<td style="width: 20%; "><b>TOTAL SHIP QTY</b></td>
							<td></td>';
				
				$totalcolorsizeqty_ship = 0;
				foreach ($arrsize as $size) {
					$totalsizeqty = 0;
					foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
						$totalsizeqty += $sizelist[$size];
					}
					$totalcolorsizeqty_ship += $totalsizeqty;
					$html .= '<td align="center">'.$totalsizeqty.'</td>';
				}
				
				$html .= '<td align="center">'.$totalcolorsizeqty_ship.'</td>
						</tr>';
						
				//================================================//
				//------------- TOTAL SHORT/ EXCESS --------------//
				$html .= '<tr>
							<td style="width: 20%; "><b>TOTAL SHORT / EXCESS</b></td>
							<td></td>';
				
				$totalcolorsizeqty = 0;
				foreach ($arrsize as $size) {
					$totalsizeqty = 0;
					$totalsizeqty_order = 0;
					foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
						$color = explode("^=", $strcolor);
						$group_number = $color[0];
						
						$totalsizeqty += $sizelist[$size];
						$totalsizeqty_order += $arr_csqorder["$group_number**^^$size"];
					}
					$this_diff = $totalsizeqty - $totalsizeqty_order; 
					$totalcolorsizeqty += $this_diff;
					$html .= '<td align="center">'.$this_diff.'</td>';
				}
				
				$html .= '<td align="center">'.$totalcolorsizeqty.'</td>
						</tr>';
						
				//================================================//
				//--------------- TOTAL PERCENTAGE ---------------//
				$html .= '<tr>
							<td style="width: 20%; "><b>TOTAL PERCENTAGE</b></td>
							<td></td>';
				
				$totalcolorsizeqty = 0;
				foreach ($arrsize as $size) {
					$totalsizeqty = 0;
					$totalsizeqty_order = 0;
					foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
						$color = explode("^=", $strcolor);
						$group_number = $color[0];
						
						$totalsizeqty += $sizelist[$size];
						$totalsizeqty_order += $arr_csqorder["$group_number**^^$size"];
					}
					$this_percent = ($totalsizeqty_order==0? 0: round($totalsizeqty / $totalsizeqty_order * 100, 2)); 
					//$totalcolorsizeqty += $this_diff;
					$html .= '<td align="center">'.$this_percent.'%</td>';
				}
				$percentage = ($totalcolorsizeqty_order==0? 0: round($totalcolorsizeqty_ship / $totalcolorsizeqty_order * 100, 2));
				$html .= '<td align="center">'.$percentage.'%</td>
						</tr>';
				
						
				$html .= '</table>
					<br>
					<br>';
					
				$ctn_measurement = "";
				$count_m = 0;
				$stmt_cpt = $this->conn->query("SELECT cpt.ext_length, cpt.ext_width, cpt.ext_height
												FROM tblcarton_picklist_head_prod cpt 
												WHERE cpt.shipmentpriceID='$spID'
												group by cpt.ext_length, cpt.ext_width, cpt.ext_height");
				while($row_cpt = $stmt_cpt->fetch(PDO::FETCH_ASSOC)){
					extract($row_cpt);
					$count_m++;
					$str_separator    = ($count_m==1? "": " / ");
					$ctn_measurement .= $str_separator."".round($ext_length,1)." x ".round($ext_width)." x ".round($ext_height)." (cm)";
				}
				
				$html .= '<table border="1" cellpadding="2" cellspacing="0">
							<tr>
								<td style="width: 15%; ">CTN MEASUREMENT </td>
								<td style="width: 30%; ">'.$ctn_measurement.'</td>
							</tr>

							<tr>
								<td>TTL CBM</td>
								<td>'.$totalcbm.'</td>
							</tr>

							<tr>
								<td>TOTAL GW: </td>
								<td>'.$pack_grossweight.' KGS</td>
							</tr>

							<tr>
								<td>TOTAL NW: </td>
								<td>'.$pack_netweight.' KGS</td>
							</tr>

							<tr>
								<td>TOTAL NNW: </td>
								<td>'.$pack_netnetweight.' KGS</td>
							</tr>

						</table>';
					
			
			
		}//--- End While Buyer Invoice Detail ---//
		
		return $html;
	}//--- End Function getBuyerInvoicePackingList ---//
	
	public function getBuyerInvoicePackingListTemplate3($invID){ //buyer_buffalo_tw.php
		$html = '';
		
		$tblbuyer_invoice        = ($this->isBuyerPayment==0? "tblbuyer_invoice": "tblbuyer_invoice_payment");
		$tblbuyer_invoice_detail = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail": "tblbuyer_invoice_payment_detail");
		
		$sql = "SELECT bi.invoice_no, bi.invoice_date, bi.shippeddate, invd.shipmentpriceID, invd.ht_code, invd.shipping_marking, 
						g.styleNo, g.orderno, sp.GTN_buyerpo as BuyerPO, csn.Name as csn_name, csn.Address as csn_address,
						cp.CompanyName_ENG as ownership, cp.Address as owneraddress, cp.Tel as ownertel, cp.Fax as ownerfax,
						lch.lc_number, st.Description as uom, od.FactoryID as od_FactoryID, bd.Description as brand,
						fty2.FactoryName_ENG as exporter, fty.Address as exporter_address, fty.Tel as exporter_tel, fty.Fax as exporter_fax,
						sm.Description as shipmode, cty.Description as manucountry, bi.ship_to, bi.ship_address,
						pyr.Description as bill_to, pyr.address as bill_address, invd.BICID
						
				FROM $tblbuyer_invoice_detail invd 
				INNER JOIN $tblbuyer_invoice bi ON bi.ID = invd.invID
				LEFT JOIN tblcompanyprofile cp ON cp.ID = bi.issue_from
				LEFT JOIN tblshipmentprice sp ON sp.ID = invd.shipmentpriceID 
				LEFT JOIN tblconsignee csn ON csn.ConsigneeID = bi.ConsigneeID
				LEFT JOIN tblgarment g ON g.orderno = sp.Orderno
				LEFT JOIN tblorder od ON od.Orderno = sp.Orderno
				LEFT JOIN tblfactory fty ON fty.FactoryID = od.contract_owner
				LEFT JOIN tblfactory fty2 ON fty2.FactoryID = od.importCompany
				LEFT JOIN tblfactory ftym ON ftym.FactoryID = od.manufacturer
				LEFT JOIN tblcountry cty ON cty.ID = ftym.countryID
				LEFT JOIN tbllc_assignment_detail lcd ON lcd.shipmentpriceID = invd.shipmentpriceID AND lcd.del=0 AND invd.del=0
				LEFT JOIN tbllc_assignment_info lci ON lci.LCIID = lcd.LCIID AND lci.del=0
				LEFT JOIN tbllc_assignment_head lch ON lch.LCHID = lci.LCHID
				LEFT JOIN tblshipmode sm ON sm.ID = bi.shipmodeID 
				LEFT JOIN tblbrand bd ON bd.ID = od.brandID
				LEFT JOIN tblset st ON st.ID = od.Qunit
				LEFT JOIN tblpayer pyr ON pyr.ID = bi.built_to
				WHERE invd.invID = '$invID' AND invd.del = 0 AND invd.group_number>0
				GROUP BY invd.shipmentpriceID 
				ORDER BY invd.ID ASC ";
		$packsql = $this->conn->prepare($sql);
		$packsql->execute(); 
		while($packrow = $packsql->fetch(PDO::FETCH_ASSOC)){
			$invoice_no   = $packrow["invoice_no"];
			$invoice_date = $packrow["invoice_date"];
			
			$BuyerPO     = $packrow["BuyerPO"];
			$shippeddate = $packrow["shippeddate"];
			$spID        = $packrow["shipmentpriceID"];
			$ht_code     = $packrow["ht_code"];
			$ship_remark = $packrow["shipping_marking"];
			$styleNo     = $packrow["styleNo"]; 
			$orderno     = $packrow["orderno"]; 
			$shipmode    = $packrow["shipmode"]; 
			$brand       = $packrow["brand"]; 
			$uom         = $packrow["uom"]; 
			
			$ownership    = $packrow["ownership"]; 
			$owneraddress = $packrow["owneraddress"]; 
			$ownertel     = $packrow["ownertel"]; 
			$ownerfax     = $packrow["ownerfax"]; 
			$csn_name     = $packrow["csn_name"]; 
			$csn_address  = $packrow["csn_address"]; 
			$lc_number    = $packrow["lc_number"]; 
			$od_FactoryID = $packrow["od_FactoryID"]; 
			$manucountry  = $packrow["manucountry"]; 
			
			$bill_to      = $packrow["bill_to"]; 
			$bill_address = $packrow["bill_address"]; 
			
			$exporter      = strtoupper($packrow["exporter"]); 
			$exporter_addr = strtoupper($packrow["exporter_address"]); 
			$ship_to       = strtoupper($packrow["ship_to"]); 
			$ship_addr     = strtoupper($packrow["ship_address"]); 
			
			$exporter_tel = $packrow["exporter_tel"]; 
			$exporter_fax = $packrow["exporter_fax"]; 
			$this->BICID  = $packrow["BICID"]; 
			
			$html .= '<br pagebreak="true">';
			
			$sqlCH = "SELECT ctn_range 
						FROM tblcarton_picklist_head cph 
						WHERE cph.shipmentpriceID='$spID' order by ctn_num desc limit 1";
			$stmt_ch = $this->conn->prepare($sqlCH);
			$stmt_ch->execute(); 
			$row_ch = $stmt_ch->fetch(PDO::FETCH_ASSOC);
				$ctn_range = $row_ch["ctn_range"];
				list($s, $order_ctn_qty) = explode("-", $ctn_range);
			
			$arr_csqorder = $this->getBuyerPOOrderQty($spID);
			$total_order_qty = array_sum($arr_csqorder);
			
			//$query_filter = " AND cpt.shiped='1'";
			//$arr_all = $this->handle_shipment->getAllPackingInfoByBuyerPO($spID, $od_FactoryID, $query_filter);
			// $arr_all   = $this->handle_shipment->getAllCuttingPickListByBuyerPO($spID);
			// $arr_row   = $arr_all["arr_row"];
			// $grand_qty = $arr_all["grand_qty"];
			// $ctn_qty   = $arr_all["ctn_qty"];
			// $shipping_marking   = $arr_all["shipping_marking"];
			
			$arr_all = $this->getBuyerInvoicePackingListDataFromCartonInv($spID, $invID);
			$arr_row   = $arr_all["arr_list"];
			$grand_qty = $arr_all["grand_qty"];
			$ctn_qty   = $arr_all["ctn_qty"];
			$shipping_marking   = $arr_all["shipping_marking"];
			
					
			$arrsize = [];
			$arrpick_totalsizeqty = []; // 
			$arrpick_totalcsq = []; // total color size qty in this packing list
			
			$size_thead = "";
			$size_thead_summary = "";
			$sizesql = $this->handle_shipment->getSizeNameColumnFromOrder($orderno, 1);
			$size_colspan = 0;
			while($sizerow = $sizesql->fetch(PDO::FETCH_ASSOC)){
				$size_name = $sizerow["SizeName"];
				
				$sqlscsq  = "SELECT sum(scsq.qty) as qty 
							FROM tblship_colorsizeqty scsq 
							WHERE scsq.shipmentpriceID='$spID' and scsq.size_name='$size_name' 
							AND scsq.statusID=1 ";
				$stmt_scsq = $this->conn->prepare($sqlscsq);
				$stmt_scsq->execute();
				$row_scsq = $stmt_scsq->fetch(PDO::FETCH_ASSOC);
					$this_qty = $row_scsq["qty"];
				
				if($this_qty>0){
					$arrsize[] = $size_name;
				
					$arrpick_totalsizeqty[$size_name] = 0;

					$size_thead .= '<td align="center" style="width: 3%; ">'.$size_name.'</td>';
					$size_thead_summary .= '<td align="center" style="width: 5%; ">'.$size_name.'</td>';
					$size_colspan++;
				}
			}//--- End While Size Range ---//
			
			$emptyrow = "<td></td><td></td><td></td><td></td>";
			foreach ($arrsize as $value) {
				$emptyrow .= '<td style="width: 3%; "></td>';
			}
			$emptyrow .= "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
			//$thead_sizewidth = $size_colspan * 3;
			
			$all_colspan = $size_colspan + 10;
			$left_colspan = $size_colspan + 5;
			$right_colspan = 5;
			
			$css_size_wd = $size_colspan * 3;
			$css_goods = (100 - $css_size_wd) * 0.20;
			$css_color = (100 - $css_size_wd - $css_goods) * 0.18;
			$css_ctn   = (100 - $css_size_wd - $css_goods - $css_color) / 8;
			
			$html .= '<table cellpadding="2" cellspacing="0" border="1" style="width:100%">
							<tr>
								<td colspan="'.$all_colspan.'" align="center">
										<font style="font-size:14px">'.$this->letterhead_name.'</font><br/>
										'.$this->letterhead_address.'<br/>
										TEL:'.$this->letterhead_tel.' & FAX:'.$this->letterhead_fax.'</td>
								</tr>
							<tr>
								<td colspan="'.$all_colspan.'" align="center">&nbsp;<br/><u>PACKING LIST</u><br/>&nbsp;</td>
								</tr>
							<tr>
								<td colspan="'.$left_colspan.'">SOLD TO:</td>
								<td colspan="'.$right_colspan.'" rowspan="4">
									<table width="100%" cellpadding="2">
									<tr>
										<td>COSTCO PO#</td>
										<td>'.$BuyerPO.'</td>
										</tr>
									<tr>
										<td>COSTCO ITEM#</td>
										<td></td>
										</tr>
									<tr>
										<td>INVOICE DATE:</td>
										<td>'.$invoice_date.'</td>
										</tr>
									<tr>
										<td>INVOICE NO.:</td>
										<td>'.$invoice_no.'</td>
										</tr>
									<tr>
										<td>BRAND:</td>
										<td>'.$brand.'</td>
										</tr>
									<tr>
										<td>STYLE NO.:</td>
										<td>'.$styleNo.'</td>
										</tr>
									<tr>
										<td>STYLE NAME:</td>
										<td></td>
										</tr>
									</table>
								</td>
								</tr>
							<tr>
								<td colspan="'.$left_colspan.'">'.$bill_to.'<br/>'.$bill_address.'</td>
								</tr>
							<tr>
								<td colspan="'.$left_colspan.'">SHIP TO:</td>
								</tr>
							<tr>
								<td colspan="'.$left_colspan.'">'.$ship_to.'<br/>'.$ship_addr.'</td>
								</tr>
							<tr>
								<td colspan="'.$all_colspan.'" align="center">DESCRIPTION OF GOODS: '.$shipping_marking.'</td>
								</tr>
							<tr>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">PO#</td>
								
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">ITEM#</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">CTN NO.</td>
								<td rowspan="2" style="width:'.$css_color.'%">COLOR</td>
								<td align="center" colspan="'.$size_colspan.'" style="width: '.$css_size_wd.'%; ">SIZE QUANTITY</td>
								
								<td rowspan="2" align="center" style="width:'.$css_goods.'%">DESCRIPTION OF GOODS</td>
								<td align="center" style="width:'.$css_ctn.'%">TOTAL PCS/CTN</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">TOTAL <br/>CARTONS</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">TOTAL QUANTITY</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">TOTAL GROSS WEIGHT (KGS)</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">TOTAL NET WEIGHT (KGS)</td>
								
							</tr>
							<tr>
								'.$size_thead.'
								<td align="center"></td>
							</tr>
						

						<!--<tr>
							'.$emptyrow.'
						</tr>-->';
						
			$pack_ctn_qty = 0; // qty of ctn used in one po
			$pack_netweight = 0;
			$pack_grossweight = 0;
			$pack_netnetweight = 0;
			$pack_totalpcs = 0;
			$totalpackqty = 0;
			$totalcbm = 0;

			$arrtotalsizeqty = [];
			
			for($arr=0;$arr<count($arr_row);$arr++){
				$ctn_range     = $arr_row[$arr]["ctn_range"];
				$count_ctn     = $arr_row[$arr]["count_ctn"];
				$SKU           = $arr_row[$arr]["SKU"];
				$this_ctn_qty  = $arr_row[$arr]["this_ctn_qty"];
				$total_qty     = $arr_row[$arr]["total_qty"];
				$this_nnw      = $arr_row[$arr]["this_nnw"];
				$one_nnw       = round($this_nnw / $count_ctn, 3);
				$this_nw       = $arr_row[$arr]["this_nw"];
				$one_nw        = round($this_nw / $count_ctn, 3);
				$this_gw       = $arr_row[$arr]["this_gw"];
				$one_gw        = round($this_gw / $count_ctn, 3);
				$this_cbm      = $arr_row[$arr]["cbm_total"];
				$ctn_measurement = $arr_row[$arr]["ctn_measurement"];
				$ext_length = $arr_row[$arr]["ext_length"];
				$ext_width  = $arr_row[$arr]["ext_width"];
				$ext_height = $arr_row[$arr]["ext_height"];
				$ctn_cm     = round($ext_length)." x ".round($ext_width)." x ".round($ext_height)." (cm)";
				
				$arr_grp_color = $arr_row[$arr]["arr_grp_color"];
				$count_grp     = count($arr_row[$arr]["arr_grp_color"]);
				$arr_size_info = $arr_row[$arr]["arr_size_info"];
				
			$html .= '<tr>
					<td rowspan="'.$count_grp.'" >'.$BuyerPO.'</td>
					<td rowspan="'.$count_grp.'">'.$SKU.'</td>
					<td rowspan="'.$count_grp.'" align="center">'.$ctn_range.'</td>';
				for($g=0;$g<count($arr_grp_color);$g++){
					list($group_number, $sku) = explode("**%%^^",$arr_grp_color[$g]);
					
					$sqlSGC = "SELECT group_concat(c.colorName,' (',g.styleNo,')' separator '<br/>') as grp_color, bid.shipping_marking 
								FROM tblship_group_color sgc 
								INNER JOIN tblcolor c ON c.ID = sgc.colorID
								INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
								LEFT JOIN tblbuyer_invoice_detail bid ON bid.shipmentpriceID = sgc.shipmentpriceID 
																	AND bid.group_number = sgc.group_number 
																	AND bid.del=0 AND sgc.statusID = 1
								WHERE sgc.shipmentpriceID='$spID' AND sgc.group_number='$group_number' AND sgc.statusID=1";
					$stmt_sgc = $this->conn->query($sqlSGC);
					$row_sgc  = $stmt_sgc->fetch(PDO::FETCH_ASSOC);
						$grp_color = $row_sgc["grp_color"];
						$shipping_marking = $row_sgc["shipping_marking"];
					
					if($g>0){
						$html .= "<tr>";
					}
						
					$html .= '<td >'.$grp_color.'</td>';
					foreach ($arrsize as $size) {
						$this_qty = (array_key_exists($size,$arr_size_info["$group_number"]) ? 
										$arr_size_info["$group_number"]["$size"]:0);
						$html .= '<td class="center-align" style="width:3%; ">'.$this_qty.'</td>';
						
						//if(array_key_exists("$group_number^=$grp_color", $arrpick_totalcsq)){
							//if(array_key_exists($size ,$arrpick_totalcsq["$group_number^=$grp_color"])){
								$arrpick_totalcsq["$group_number^=$grp_color"][$size] += $this_qty * $count_ctn;
							//}
						//}
						
					}//--- End Foreach size qty ---//
					
					$html .= '<td align="center" >'.$shipping_marking.'</td>';
					if($g>0){
						$html .= "</tr>";
					}
					else{
						$html .= '
						
						<td align="center" rowspan="'.$count_grp.'">'.$this_ctn_qty.'</td>
						<td align="center" rowspan="'.$count_grp.'" >'.$count_ctn.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$total_qty.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$this_gw.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$this_nw.'</td>
						</tr>';
						
					}
				}//--- End For Color ---//

				$pack_ctn_qty += $count_ctn;
				$totalpackqty += $total_qty;
				$pack_netweight += $this_nw;
				$pack_grossweight += $this_gw;
				$pack_netnetweight += $this_nnw;
				$totalcbm += $this_cbm;

				
			}//--- End Outer For Row ---//class="font-blue"
				$html .= '<!--<tr> '.$emptyrow.' </tr>-->
						  <tr >
							<td align="center" colspan="1"></td>
							
							<td></td>
							<td></td>
							<td></td>
							<td colspan="'.($size_colspan + 1).'" align="center">TOTAL:</td>
							<td align="center"></td>
							<td align="center">'.$pack_ctn_qty.'</td>
							<td align="center">'.$totalpackqty.'</td>
							<td align="center">'.$pack_grossweight.'</td>
							<td align="center">'.$pack_netweight.'</td>
							
							</tr>
						</table>
					<br>
					<br>';
				
				////////////////////////////////////////////////
				////======== Color & Size Breakdown ========////
				////////////////////////////////////////////////
				$html .= '<h3><u>SUMMARY </u></h3>
						<table cellpadding="2" cellspacing="0" border="1">
						<tr>
									<td></td>
									<td style="width: 20%; ">COLOR</td>
									<td style="width:5%" align="center">UNITS</td>
									'.$size_thead_summary.'
									<td align="center">TOTAL</td>
								</tr>
						';
				//=======================//
				///////// ORDERED /////////
				//=======================//
				$html .= '<tr>
							<td rowspan="'.count($arrpick_totalcsq).'" align="center">ORDERED</td>';
				$order_count = 0;
				foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
					$color = explode("^=", $strcolor);
					$group_number = $color[0];
					$ColorName    = $color[1];
					$order_count++;
					
					$html .= ($order_count>1? '<tr>':'');
					
					$html .= '<td>'.$ColorName.'</td>';
					$html .= '<td align="center">'.$uom.'</td>';
					$total_qty = 0;
					foreach ($arrsize as $size) {
						$orderqty = $arr_csqorder["$group_number**^^$size"];
						
						$html .= '<td align="center">'.$orderqty.'</td>';
						$total_qty += $orderqty;
					}
					
					$html .= '<td align="center">'.$total_qty.'</td>';
					$html .= '</tr>';
				}
				
				//=======================//
				///////// SHIPPED /////////
				//=======================//
				$size_colspan = count($arrsize) + 4;
				$html .= '<tr><td colspan="'.$size_colspan.'">&nbsp;</td></tr>';
				$html .= '<tr>
							<td rowspan="'.count($arrpick_totalcsq).'" align="center">SHIPPED</td>';	
				$order_count = 0;
				$grand_qty = 0;
				foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
					$color = explode("^=", $strcolor);
					$group_number = $color[0];
					$ColorName    = $color[1];
					$order_count++;
					
					$html .= ($order_count>1? '<tr>':'');
					
					$html .= '<td>'.$ColorName.'</td>';
					$html .= '<td align="center">'.$uom.'</td>';
					$total_qty = 0;
					foreach ($arrsize as $size) {
						$sizeqty  = $sizelist[$size];
						
						$html .= '<td align="center">'.$sizeqty.'</td>';
						$total_qty += $sizeqty;
					}
					$html .= '<td align="center">'.$total_qty.'</td>';
					$html .= '</tr>';
					$grand_qty += $total_qty;
				}
				
				//==================================//
				///////// SHORT/OVER SHIPPED /////////
				//==================================//
				$html .= '<tr><td colspan="'.$size_colspan.'">&nbsp;</td></tr>';
				$html .= '<tr>
							<td rowspan="'.count($arrpick_totalcsq).'" align="center">SHORT/OVER SHIPPED</td>';	
				$order_count = 0;
				foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
					$color = explode("^=", $strcolor);
					$group_number = $color[0];
					$ColorName    = $color[1];
					$order_count++;
					
					$html .= ($order_count>1? '<tr>':'');
					
					$html .= '<td>'.$ColorName.'</td>';
					$html .= '<td align="center">'.$uom.'</td>';
					$total_qty = 0;
					foreach ($arrsize as $size) {
						$sizeqty  = $sizelist[$size];
						$orderqty = $arr_csqorder["$group_number**^^$size"];
						$shortqty = $sizeqty - $orderqty;
						
						$html .= '<td align="center">'.$shortqty.'</td>';
						$total_qty += $shortqty;
					}
					$html .= '<td align="center">'.$total_qty.'</td>';
					$html .= '</tr>';
				}
						
				$html .= '</table>
					<br>
					<br>';
					
				$ctn_measurement = "";
				$count_m = 0;
				$stmt_cpt = $this->conn->query("SELECT cih.ext_length, cih.ext_width, cih.ext_height
												FROM tblcarton_inv_head cih 
												WHERE cih.shipmentpriceID='$spID' AND cih.del=0
												group by cih.ext_length, cih.ext_width, cih.ext_height");
				while($row_cpt = $stmt_cpt->fetch(PDO::FETCH_ASSOC)){
					extract($row_cpt);
					$count_m++;
					$str_separator    = ($count_m==1? "": " / ");
					$ctn_measurement .= $str_separator."".round($ext_length,1)." x ".round($ext_width,1)." x ".round($ext_height,1)." (cm)";
				}
				
				$html .= '<table border="0" cellpadding="2" cellspacing="0">
							<tr>
								<td style="width: 15%; ">TOTAL QTY </td>
								<td style="width: 30%; ">'.$grand_qty.' '.$uom.'</td>
							</tr>
							
							<tr>
								<td>TOTAL CTNS: </td>
								<td>'.$ctn_qty.' CTNS</td>
							</tr>
							
							<tr>
								<td>TOTAL G.W: </td>
								<td>'.$pack_grossweight.' KGS</td>
							</tr>

							<tr>
								<td>TOTAL N.W: </td>
								<td>'.$pack_netweight.' KGS</td>
							</tr>
							
							<tr>
								<td >CTN MEASUREMENT </td>
								<td >'.$ctn_measurement.'</td>
							</tr>

							<tr>
								<td>TOTAL CBM</td>
								<td>'.$totalcbm.'</td>
							</tr>
						</table>';
					
			
			
		}//--- End While Buyer Invoice Detail ---//
		
		return $html;
	}

	public function getBuyerInvoicePackingListTemplate4($invID){ //buyer_buffalo_cn.php
		$html = '';
		
		$tblbuyer_invoice        = ($this->isBuyerPayment==0? "tblbuyer_invoice": "tblbuyer_invoice_payment");
		$tblbuyer_invoice_detail = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail": "tblbuyer_invoice_payment_detail");
		
		$sql = "SELECT bi.invoice_no, bi.invoice_date, bi.shippeddate, invd.shipmentpriceID, invd.ht_code, invd.shipping_marking, 
						g.styleNo, g.orderno, sp.GTN_buyerpo as BuyerPO, csn.Name as csn_name, csn.Address as csn_address,
						cp.CompanyName_ENG as ownership, cp.Address as owneraddress, cp.Tel as ownertel, cp.Fax as ownerfax,
						lch.lc_number, st.Description as uom, od.FactoryID as od_FactoryID, bd.Description as brand,
						fty.FactoryName_ENG as exporter, fty.Address as exporter_address, fty.Tel as exporter_tel, fty.Fax as exporter_fax,
						sm.Description as shipmode, cty.Description as manucountry, bi.ship_to, bi.ship_address, bi.container_no,
						group_concat(distinct c.colorName) as color, pyr.Description as bill_to, pyr.address as bill_address, invd.BICID 
						
				FROM $tblbuyer_invoice_detail invd 
				INNER JOIN $tblbuyer_invoice bi ON bi.ID = invd.invID
				LEFT JOIN tblshipmentprice sp ON sp.ID = invd.shipmentpriceID 
				LEFT JOIN tblconsignee csn ON csn.ConsigneeID = bi.ConsigneeID
				LEFT JOIN tblgarment g ON g.orderno = sp.Orderno
				LEFT JOIN tblorder od ON od.Orderno = sp.Orderno
				LEFT JOIN tblfactory fty ON fty.FactoryID = bi.shipper
				LEFT JOIN tblcompanyprofile cp ON cp.ID = bi.issue_from
				LEFT JOIN tblfactory ftym ON ftym.FactoryID = od.manufacturer
				LEFT JOIN tblcountry cty ON cty.ID = ftym.countryID
				LEFT JOIN tbllc_assignment_detail lcd ON lcd.shipmentpriceID = invd.shipmentpriceID AND lcd.del=0 AND invd.del=0
				LEFT JOIN tbllc_assignment_info lci ON lci.LCIID = lcd.LCIID AND lci.del=0
				LEFT JOIN tbllc_assignment_head lch ON lch.LCHID = lci.LCHID
				LEFT JOIN tblshipmode sm ON sm.ID = bi.shipmodeID 
				LEFT JOIN tblbrand bd ON bd.ID = od.brandID
				LEFT JOIN tblset st ON st.ID = od.Qunit
				LEFT JOIN tblship_group_color sgc ON sgc.shipmentpriceID = sp.ID AND sgc.statusID=1
				LEFT JOIN tblcolor c ON c.ID = sgc.colorID
				LEFT JOIN tblpayer pyr ON pyr.ID = bi.built_to
				WHERE invd.invID = '$invID' AND invd.del = 0 AND invd.group_number>0
				GROUP BY invd.shipmentpriceID 
				ORDER BY invd.ID ASC ";
		$packsql = $this->conn->prepare($sql);
		$packsql->execute(); 
		while($packrow = $packsql->fetch(PDO::FETCH_ASSOC)){
			$invoice_no   = $packrow["invoice_no"];
			$invoice_date = $packrow["invoice_date"];
			
			$BuyerPO     = $packrow["BuyerPO"];
			$shippeddate = $packrow["shippeddate"];
			$spID        = $packrow["shipmentpriceID"];
			$ht_code     = $packrow["ht_code"];
			$ship_remark = $packrow["shipping_marking"];
			$styleNo     = $packrow["styleNo"]; 
			$orderno     = $packrow["orderno"]; 
			$shipmode    = $packrow["shipmode"]; 
			$brand       = $packrow["brand"]; 
			$color       = $packrow["color"]; 
			$uom         = strtoupper($packrow["uom"]); 
			
			$ownership    = $packrow["ownership"]; 
			$owneraddress = $packrow["owneraddress"]; 
			$ownertel     = $packrow["ownertel"]; 
			$ownerfax     = $packrow["ownerfax"]; 
			$csn_name     = $packrow["csn_name"]; 
			$csn_address  = $packrow["csn_address"]; 
			$lc_number    = $packrow["lc_number"]; 
			$od_FactoryID = $packrow["od_FactoryID"]; 
			$manucountry  = $packrow["manucountry"]; 
			$bill_to      = $packrow["bill_to"]; 
			$bill_address = $packrow["bill_address"]; 
			$container_no = $packrow["container_no"]; 
			
			$exporter      = strtoupper($packrow["exporter"]); 
			$exporter_addr = strtoupper($packrow["exporter_address"]); 
			$ship_to       = strtoupper($packrow["ship_to"]); 
			$ship_addr     = strtoupper($packrow["ship_address"]); 
			
			$exporter_tel = $packrow["exporter_tel"]; 
			$exporter_fax = $packrow["exporter_fax"]; 
			$this->BICID  = $packrow["BICID"]; 
			
			$html .= '<br pagebreak="true">';
			
			$sqlCH = "SELECT ctn_range 
						FROM tblcarton_picklist_head cph 
						WHERE cph.shipmentpriceID='$spID' order by ctn_num desc limit 1";
			$stmt_ch = $this->conn->prepare($sqlCH);
			$stmt_ch->execute(); 
			$row_ch = $stmt_ch->fetch(PDO::FETCH_ASSOC);
				$ctn_range = $row_ch["ctn_range"];
				list($s, $order_ctn_qty) = explode("-", $ctn_range);
			
			$arr_csqorder = $this->getBuyerPOOrderQty($spID);
			$total_order_qty = array_sum($arr_csqorder);
			
			//$query_filter = " AND cpt.shiped='1'";
			//$arr_all = $this->handle_shipment->getAllPackingInfoByBuyerPO($spID, $od_FactoryID, $query_filter);
			// $arr_all   = $this->handle_shipment->getAllCuttingPickListByBuyerPO($spID);
			// $arr_row   = $arr_all["arr_row"];
			// $grand_qty = $arr_all["grand_qty"];
			// $ctn_qty   = $arr_all["ctn_qty"];
			// $shipping_marking   = $arr_all["shipping_marking"];
			
			$arr_all = $this->getBuyerInvoicePackingListDataFromCartonInv($spID, $invID);
			$arr_row   = $arr_all["arr_list"];
			$grand_qty = $arr_all["grand_qty"];
			$ctn_qty   = $arr_all["ctn_qty"];
			$shipping_marking   = $arr_all["shipping_marking"];
			
			$arrsize = [];
			$arrpick_totalsizeqty = []; // 
			$arrpick_totalcsq = []; // total color size qty in this packing list
			
			$size_thead = "";
			$size_thead_summary = "";
			$sizesql = $this->handle_shipment->getSizeNameColumnFromOrder($orderno, 1);
			$size_colspan = 0;
			while($sizerow = $sizesql->fetch(PDO::FETCH_ASSOC)){
				$size_name = $sizerow["SizeName"];
				
				$sqlscsq  = "SELECT sum(scsq.qty) as qty 
							FROM tblship_colorsizeqty scsq 
							WHERE scsq.shipmentpriceID='$spID' and scsq.size_name='$size_name' 
							AND scsq.statusID=1 ";
				$stmt_scsq = $this->conn->prepare($sqlscsq);
				$stmt_scsq->execute();
				$row_scsq = $stmt_scsq->fetch(PDO::FETCH_ASSOC);
					$this_qty = $row_scsq["qty"];
				
				if($this_qty>0){
					$arrsize[] = $size_name;
				
					$arrpick_totalsizeqty[$size_name] = 0;

					$size_thead .= '<td align="center" style="width: 3%; ">'.$size_name.'</td>';
					$size_thead_summary .= '<td align="center" style="width: 5%; ">'.$size_name.'</td>';
					$size_colspan++;
				}
			}//--- End While Size Range ---//
			
			$emptyrow = "<td></td><td></td><td></td><td></td>";
			foreach ($arrsize as $value) {
				$emptyrow .= '<td style="width: 3%; "></td>';
			}
			$emptyrow .= "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
			//$thead_sizewidth = $size_colspan * 3;
			
			$all_colspan = $size_colspan + 10;
			$left_colspan = $size_colspan + 5;
			$right_colspan = 5;
			
			$css_size_wd = $size_colspan * 3;
			$css_goods = (100 - $css_size_wd) * 0.20;
			$css_color = (100 - $css_size_wd - $css_goods) * 0.18;
			$css_ctn   = (100 - $css_size_wd - $css_goods - $css_color) / 6;
			
			$invoice_date = strtoupper(date_format(date_create($invoice_date), "d-M-Y"));
			$shippeddate  = strtoupper(date_format(date_create($shippeddate), "M d,Y"));
			//$ship_to = htmlspecialchars_decode($ship_to);
			$ship_to = str_replace("&AMP;", "&", $ship_to);
			
			$html .= '<table cellpadding="2" cellspacing="0" border="0" style="width:100%">
							<tr>
								<td colspan="5" align="center">
										<font style="font-size:14px">'.$this->letterhead_name.'</font><br/>
										'.$this->letterhead_address.'<br/>
										TEL:'.$this->letterhead_tel.' & FAX:'.$this->letterhead_fax.'</td>
								</tr>
							<tr>
								<td colspan="5" align="center">&nbsp;<br/><u>PACKING LIST</u><br/>&nbsp;</td>
								</tr>
							<tr>	
								<td colspan="5">
									Invoice Number: '.$invoice_no.' &nbsp; &nbsp; &nbsp; 
									Date: '.$invoice_date.'</td>
								</tr>
							<tr>
								<td style="width:10%">BUYER / SOLD TO:</td>
								<td style="width:20%">'.$bill_to.'<br/>'.$bill_address.'</td>
								<td style="width:40%"></td>
								<td style="width:10%"></td>
								<td ></td>
								</tr>
							<tr>
								<td ></td>
								<td ></td>
								<td ></td>
								<td ></td>
								<td ></td>
								</tr>
							<tr>
								<td >SHIP TO:</td>
								<td >'.$ship_to.'<br/>'.$ship_addr.'</td>
								<td ></td>
								<td ></td>
								<td ></td>
								</tr>
							<tr>
								<td ><br/></td>
								<td ></td>
								<td ></td>
								<td ></td>
								<td ></td>
								</tr>
							
							<tr>
								<td rowspan="3" valign="middle"><br/><br/>DC NO:</td>
								<td rowspan="3"></td>
								<td>PO#: '.$BuyerPO.'</td>
								<td>BRAND: </td>
								<td>'.$brand.'</td>
								</tr>
							<tr>
								<td>STYLE NAME & NUMBER: '.$styleNo.'</td>
								<td>COLOR</td>
								<td>'.$color.'</td>
								</tr>
							<tr>
								<td>MADE IN '.$manucountry.'</td>
								<td>MATERIAL</td>
								<td></td>
								</tr>
							<tr>
								<td>DESCRIPTION: </td>
								<td colspan="4">'.$shipping_marking.'</td>
								</tr>
							<tr>
								<td colspan="5">Transport</td>
								</tr>
							<tr>
								<td>ETD: </td>
								<td colspan="4">'.$shippeddate.'</td>
								</tr>
							<tr>
								<td>CONTAINER#: </td>
								<td colspan="4">'.$container_no.'</td>
								</tr>
							</table><br/><br/>';
							
					$html .= '<table cellpadding="2" border="1" style="width:100%">
							<tr>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">CTN NO.</td>
								<td rowspan="2" style="width:'.$css_color.'%">COLOR</td>
								<td align="center" colspan="'.$size_colspan.'" style="width: '.$css_size_wd.'%; ">SIZE QUANTITY</td>
								
								<td rowspan="2" align="center" style="width:'.$css_goods.'%">DESCRIPTION OF GOODS</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">SUB TOTAL PER CTN</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">NO OF CARTONS</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">'.$uom.'</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">G.W (KGS)</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">N.W (KGS)</td>
								
							</tr>
							<tr>
								'.$size_thead.'
							</tr>
						

						<!--<tr>
							'.$emptyrow.'
						</tr>-->';
						
			$pack_ctn_qty = 0; // qty of ctn used in one po
			$pack_netweight = 0;
			$pack_grossweight = 0;
			$pack_netnetweight = 0;
			$pack_totalpcs = 0;
			$totalpackqty = 0;
			$totalcbm = 0;

			$arrtotalsizeqty = [];
			
			for($arr=0;$arr<count($arr_row);$arr++){
				$ctn_range     = $arr_row[$arr]["ctn_range"];
				$count_ctn     = $arr_row[$arr]["count_ctn"];
				$SKU           = $arr_row[$arr]["SKU"];
				$this_ctn_qty  = $arr_row[$arr]["this_ctn_qty"];
				$total_qty     = $arr_row[$arr]["total_qty"];
				$this_nnw      = $arr_row[$arr]["this_nnw"];
				$one_nnw       = round($this_nnw / $count_ctn, 3);
				$this_nw       = $arr_row[$arr]["this_nw"];
				$one_nw        = round($this_nw / $count_ctn, 3);
				$this_gw       = $arr_row[$arr]["this_gw"];
				$one_gw        = round($this_gw / $count_ctn, 3);
				$this_cbm      = $arr_row[$arr]["cbm_total"];
				$ctn_measurement = $arr_row[$arr]["ctn_measurement"];
				$ext_length = $arr_row[$arr]["ext_length"];
				$ext_width  = $arr_row[$arr]["ext_width"];
				$ext_height = $arr_row[$arr]["ext_height"];
				$ctn_cm     = round($ext_length)." x ".round($ext_width)." x ".round($ext_height)." (cm)";
				
				$arr_grp_color = $arr_row[$arr]["arr_grp_color"];
				$count_grp     = count($arr_row[$arr]["arr_grp_color"]);
				$arr_size_info = $arr_row[$arr]["arr_size_info"];
				
			$html .= '<tr>
					<td rowspan="'.$count_grp.'" align="center">'.$ctn_range.'</td>';
				for($g=0;$g<count($arr_grp_color);$g++){
					list($group_number, $sku) = explode("**%%^^",$arr_grp_color[$g]);
					
					$sqlSGC = "SELECT group_concat(c.colorName,' (',g.styleNo,')' separator '<br/>') as grp_color, bid.shipping_marking 
								FROM tblship_group_color sgc 
								INNER JOIN tblcolor c ON c.ID = sgc.colorID
								INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
								LEFT JOIN tblbuyer_invoice_detail bid ON bid.shipmentpriceID = sgc.shipmentpriceID 
																	AND bid.group_number = sgc.group_number 
																	AND bid.del=0 AND sgc.statusID = 1
								WHERE sgc.shipmentpriceID='$spID' AND sgc.group_number='$group_number' AND sgc.statusID=1";
					$stmt_sgc = $this->conn->query($sqlSGC);
					$row_sgc  = $stmt_sgc->fetch(PDO::FETCH_ASSOC);
						$grp_color = $row_sgc["grp_color"];
						$shipping_marking = $row_sgc["shipping_marking"];
					
					if($g>0){
						$html .= "<tr>";
					}
						
					$html .= '<td >'.$grp_color.'</td>';
					foreach ($arrsize as $size) {
						$this_qty = (array_key_exists($size,$arr_size_info["$group_number"]) ? 
										$arr_size_info["$group_number"]["$size"]:0);
						$str_qty  = ($this_qty==0? "": $this_qty);
						$html .= '<td class="center-align" style="width:3%; ">'.$str_qty.'</td>';
						$arrtotalsizeqty["$size"] += ($this_qty * $count_ctn);
						
						//if(array_key_exists("$group_number^=$grp_color", $arrpick_totalcsq)){
							//if(array_key_exists($size ,$arrpick_totalcsq["$group_number^=$grp_color"])){
								$arrpick_totalcsq["$group_number^=$grp_color"][$size] += $this_qty * $count_ctn;
							//}
						//}
						
					}//--- End Foreach size qty ---//
					
					$html .= '<td align="center" >'.$shipping_marking.'</td>';
					if($g>0){
						$html .= "</tr>";
					}
					else{
						$html .= '
						
						<td align="center" rowspan="'.$count_grp.'">'.$this_ctn_qty.'</td>
						<td align="center" rowspan="'.$count_grp.'" >'.$count_ctn.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$total_qty.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$this_gw.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$this_nw.'</td>
						</tr>';
						
					}
				}//--- End For Color ---//

				$pack_ctn_qty += $count_ctn;
				$totalpackqty += $total_qty;
				$pack_netweight += $this_nw;
				$pack_grossweight += $this_gw;
				$pack_netnetweight += $this_nnw;
				$totalcbm += $this_cbm;

				
			}//--- End Outer For Row ---//class="font-blue"
				$html .= '<!--<tr> '.$emptyrow.' </tr>-->
						  <tr >
							<td colspan="2">SUB TOTAL BY COLOR/WEIGHT</td>';
				foreach ($arrsize as $size) {
					$size_total = $arrtotalsizeqty["$size"];
					$html .= '<td align="center">'.$size_total.'</td>';
				}
				
				$html .= '<td align="center"></td>
							<td align="center"></td>
							<td align="center">'.$pack_ctn_qty.'</td>
							<td align="center">'.$totalpackqty.'</td>
							<td align="center">'.$pack_grossweight.'</td>
							<td align="center">'.$pack_netweight.'</td>
							
							</tr>
						</table>
					<br>
					<br>';
				
				////////////////////////////////////////////////
				////======== Color & Size Breakdown ========////
				////////////////////////////////////////////////
				$html .= '<h3><u>TOTAL MUST BE BY INSEAM OR COLOR </u></h3>
						<table cellpadding="2" cellspacing="0" border="1">
						<tr>
									<td></td>
									<td style="width: 20%; ">COLOR</td>
									<td style="width:5%" align="center">UNITS</td>
									'.$size_thead_summary.'
									<td align="center">TOTAL</td>
								</tr>
						';
				//=======================//
				///////// ORDERED /////////
				//=======================//
				$html .= '<tr>
							<td rowspan="'.count($arrpick_totalcsq).'" align="left">ORDER (PO#)</td>';
				$order_count = 0;
				foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
					$color = explode("^=", $strcolor);
					$group_number = $color[0];
					$ColorName    = $color[1];
					$order_count++;
					
					$html .= ($order_count>1? '<tr>':'');
					
					$html .= '<td>'.$ColorName.'</td>';
					$html .= '<td align="center">'.$uom.'</td>';
					$total_qty = 0;
					foreach ($arrsize as $size) {
						$orderqty = $arr_csqorder["$group_number**^^$size"];
						
						$html .= '<td align="center">'.$orderqty.'</td>';
						$total_qty += $orderqty;
					}
					
					$html .= '<td align="center">'.$total_qty.'</td>';
					$html .= '</tr>';
				}
				
				//=======================//
				///////// SHIPPED /////////
				//=======================//
				$size_colspan = count($arrsize) + 4;
				$html .= '<tr><td colspan="'.$size_colspan.'">&nbsp;</td></tr>';
				$html .= '<tr>
							<td rowspan="'.count($arrpick_totalcsq).'" align="left">PACKED</td>';	
				$order_count = 0;
				$grand_qty = 0;
				foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
					$color = explode("^=", $strcolor);
					$group_number = $color[0];
					$ColorName    = $color[1];
					$order_count++;
					
					$html .= ($order_count>1? '<tr>':'');
					
					$html .= '<td>'.$ColorName.'</td>';
					$html .= '<td align="center">'.$uom.'</td>';
					$total_qty = 0;
					foreach ($arrsize as $size) {
						$sizeqty  = $sizelist[$size];
						
						$html .= '<td align="center">'.$sizeqty.'</td>';
						$total_qty += $sizeqty;
					}
					$html .= '<td align="center">'.$total_qty.'</td>';
					$html .= '</tr>';
					$grand_qty += $total_qty;
				}
				
				//==================================//
				///////// SHORT/OVER SHIPPED /////////
				//==================================//
				$html .= '<tr><td colspan="'.$size_colspan.'">&nbsp;</td></tr>';
				$html .= '<tr>
							<td rowspan="'.count($arrpick_totalcsq).'" align="left">SHORT/OVER SHIPPED</td>';	
				$order_count = 0;
				foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
					$color = explode("^=", $strcolor);
					$group_number = $color[0];
					$ColorName    = $color[1];
					$order_count++;
					
					$html .= ($order_count>1? '<tr>':'');
					
					$html .= '<td>'.$ColorName.'</td>';
					$html .= '<td align="center">'.$uom.'</td>';
					$total_qty = 0;
					foreach ($arrsize as $size) {
						$sizeqty  = $sizelist[$size];
						$orderqty = $arr_csqorder["$group_number**^^$size"];
						
						$shortqty = $sizeqty - $orderqty;
						
						$html .= '<td align="center">'.$shortqty.'</td>';
						$total_qty += $shortqty;
					}
					$html .= '<td align="center">'.$total_qty.'</td>';
					$html .= '</tr>';
				}
						
				$html .= '</table>
					<br>
					<br>';
					
				$ctn_measurement = "";
				$count_m = 0;
				$stmt_cpt = $this->conn->query("SELECT cpt.ext_length, cpt.ext_width, cpt.ext_height
												FROM tblcarton_picklist_head_prod cpt 
												WHERE cpt.shipmentpriceID='$spID'
												group by cpt.ext_length, cpt.ext_width, cpt.ext_height");
				while($row_cpt = $stmt_cpt->fetch(PDO::FETCH_ASSOC)){
					extract($row_cpt);
					$count_m++;
					$str_separator    = ($count_m==1? "": " / ");
					$ctn_measurement .= $str_separator."".round($ext_length,1)." x ".round($ext_width)." x ".round($ext_height)." (cm)";
				}
				
				$html .= '<table border="0" cellpadding="2" cellspacing="0">
							<tr>
								<td style="width: 15%; ">TOTAL QTY </td>
								<td style="width: 30%; ">'.$grand_qty.' '.$uom.'</td>
							</tr>
							
							<tr>
								<td>TOTAL CTNS: </td>
								<td>'.$ctn_qty.' CTNS</td>
							</tr>
							
							<tr>
								<td>TOTAL G.W: </td>
								<td>'.$pack_grossweight.' KGS</td>
							</tr>

							<tr>
								<td>TOTAL N.W: </td>
								<td>'.$pack_netweight.' KGS</td>
							</tr>
							
							<tr>
								<td >CTN MEASUREMENT </td>
								<td >'.$ctn_measurement.'</td>
							</tr>

							<tr>
								<td>TOTAL CBM</td>
								<td>'.$totalcbm.'</td>
							</tr>
						</table>';
					
			
			
		}//--- End While Buyer Invoice Detail ---//
		
		return $html;
	}
	
	public function getBuyerInvoicePackingListTemplate5($invID){ //buyer_oliver.php
		$html = '';
		
		$tblbuyer_invoice        = ($this->isBuyerPayment==0? "tblbuyer_invoice": "tblbuyer_invoice_payment");
		$tblbuyer_invoice_detail = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail": "tblbuyer_invoice_payment_detail");
		
		$sql = "SELECT bi.invoice_no, bi.invoice_date, bi.shippeddate, invd.shipmentpriceID, invd.ht_code, invd.shipping_marking, 
						g.styleNo, g.orderno, sp.GTN_buyerpo as BuyerPO, csn.Name as csn_name, csn.Address as csn_address,
						cp.CompanyName_ENG as ownership, cp.Address as owneraddress, cp.Tel as ownertel, cp.Fax as ownerfax,
						lch.lc_number, st.Description as uom, od.FactoryID as od_FactoryID, bd.Description as brand,
						fty2.FactoryName_ENG as exporter, fty.Address as exporter_address, fty.Tel as exporter_tel, fty.Fax as exporter_fax,
						sm.Description as shipmode, cty.Description as manucountry, bi.ship_to, bi.ship_address, 
						group_concat(distinct c.colorName) as color, pyr.Description as bill_to, pyr.address as bill_address,
						cty2.Description as destcountry, sp.Toleranceminus, sp.Toleranceplus, spk.packing_type, invd.BICID  
						
				FROM $tblbuyer_invoice_detail invd 
				INNER JOIN $tblbuyer_invoice bi ON bi.ID = invd.invID
				LEFT JOIN tblshipmentprice sp ON sp.ID = invd.shipmentpriceID 
				LEFT JOIN tblship_packing spk ON spk.shipmentpriceID = sp.ID AND spk.statusID=1
				LEFT JOIN tblconsignee csn ON csn.ConsigneeID = bi.ConsigneeID
				LEFT JOIN tblgarment g ON g.orderno = sp.Orderno
				LEFT JOIN tblorder od ON od.Orderno = sp.Orderno
				LEFT JOIN tblcompanyprofile cp ON cp.ID = bi.issue_from
				LEFT JOIN tblfactory fty ON fty.FactoryID = bi.shipper
				LEFT JOIN tblfactory fty2 ON fty2.FactoryID = od.importCompany
				LEFT JOIN tblfactory ftym ON ftym.FactoryID = od.manufacturer
				LEFT JOIN tblcountry cty ON cty.ID = ftym.countryID
				LEFT JOIN tbllc_assignment_detail lcd ON lcd.shipmentpriceID = invd.shipmentpriceID AND lcd.del=0 AND invd.del=0
				LEFT JOIN tbllc_assignment_info lci ON lci.LCIID = lcd.LCIID AND lci.del=0
				LEFT JOIN tbllc_assignment_head lch ON lch.LCHID = lci.LCHID
				LEFT JOIN tblshipmode sm ON sm.ID = bi.shipmodeID 
				LEFT JOIN tblbrand bd ON bd.ID = od.brandID
				LEFT JOIN tblset st ON st.ID = od.Qunit
				LEFT JOIN tblship_group_color sgc ON sgc.shipmentpriceID = sp.ID AND sgc.statusID=1
				LEFT JOIN tblcolor c ON c.ID = sgc.colorID
				LEFT JOIN tblpayer pyr ON pyr.ID = bi.built_to
				LEFT JOIN tblbuyerdestination bdt ON bdt.ID = bi.BuyerDestID
				LEFT JOIN tblcountry cty2 ON cty2.ID = bdt.countryID
				WHERE invd.invID = '$invID' AND invd.del = 0 AND invd.group_number>0
				GROUP BY invd.shipmentpriceID 
				ORDER BY invd.ID ASC ";
		$packsql = $this->conn->prepare($sql);
		$packsql->execute(); 
		while($packrow = $packsql->fetch(PDO::FETCH_ASSOC)){
			$invoice_no   = $packrow["invoice_no"];
			$invoice_date = $packrow["invoice_date"];
			
			$BuyerPO     = $packrow["BuyerPO"];
			$shippeddate = $packrow["shippeddate"];
			$spID        = $packrow["shipmentpriceID"];
			$ht_code     = $packrow["ht_code"];
			$ship_remark = $packrow["shipping_marking"];
			$styleNo     = $packrow["styleNo"]; 
			$orderno     = $packrow["orderno"]; 
			$shipmode    = $packrow["shipmode"]; 
			$brand       = $packrow["brand"]; 
			$color       = $packrow["color"]; 
			$uom         = strtoupper($packrow["uom"]); 
			
			$ownership    = $packrow["ownership"]; 
			$owneraddress = $packrow["owneraddress"]; 
			$ownertel     = $packrow["ownertel"]; 
			$ownerfax     = $packrow["ownerfax"]; 
			$csn_name     = $packrow["csn_name"]; 
			$csn_address  = $packrow["csn_address"]; 
			$lc_number    = $packrow["lc_number"]; 
			$od_FactoryID = $packrow["od_FactoryID"]; 
			$manucountry  = $packrow["manucountry"]; 
			$bill_to      = $packrow["bill_to"]; 
			$bill_address = $packrow["bill_address"]; 
			
			$exporter      = strtoupper($packrow["exporter"]); 
			$exporter_addr = strtoupper($packrow["exporter_address"]); 
			$ship_to       = strtoupper($packrow["ship_to"]); 
			$ship_addr     = strtoupper($packrow["ship_address"]); 
			$destcountry   = strtoupper($packrow["destcountry"]); 
			
			$exporter_tel = $packrow["exporter_tel"]; 
			$exporter_fax = $packrow["exporter_fax"]; 
			$Toleranceminus = $packrow["Toleranceminus"]; 
			$Toleranceplus  = $packrow["Toleranceplus"]; 
			$packing_type   = $packrow["packing_type"]; 
			$this->BICID    = $packrow["BICID"];
			
			$str_packing_type = "";
			switch($packing_type){
				case 0: $str_packing_type = "Flat Packed"; break;
				case 1: $str_packing_type = "Hanger Packed"; break;
			}
			
			$fabric_country = $this->getFabricCountryOrigin($spID);
			
			$html .= '<br pagebreak="true">';
			
			$sqlCH = "SELECT ctn_range 
						FROM tblcarton_picklist_head cph 
						WHERE cph.shipmentpriceID='$spID' order by ctn_num desc limit 1";
			$stmt_ch = $this->conn->prepare($sqlCH);
			$stmt_ch->execute(); 
			$row_ch = $stmt_ch->fetch(PDO::FETCH_ASSOC);
				$ctn_range = $row_ch["ctn_range"];
				list($s, $order_ctn_qty) = explode("-", $ctn_range);
			
			$arr_csqorder = $this->getBuyerPOOrderQty($spID);
			$total_order_qty = array_sum($arr_csqorder);
			
			//$query_filter = " AND cpt.shiped='1'";
			//$arr_all = $this->handle_shipment->getAllPackingInfoByBuyerPO($spID, $od_FactoryID, $query_filter);
			$arr_order = $this->getBuyerPOOrderQty($spID);
			
			// $arr_all   = $this->handle_shipment->getAllCuttingPickListByBuyerPO($spID);
			// $arr_row              = $arr_all["arr_row"];
			// $arr_all_size_color   = $arr_all["arr_all_size_color"];
			// $grand_qty            = $arr_all["grand_qty"];
			// $ctn_qty              = $arr_all["ctn_qty"];
			// $shipping_marking     = $arr_all["shipping_marking"];
			
			$arr_all = $this->getBuyerInvoicePackingListDataFromCartonInv($spID, $invID);
			$arr_row            = $arr_all["arr_list"];
			$grand_qty          = $arr_all["grand_qty"];
			$ctn_qty            = $arr_all["ctn_qty"];
			$arr_all_size_color = $arr_all["arr_all_size_color"];
			$shipping_marking   = $arr_all["shipping_marking"];
			
					
			$arrsize = [];
			$arrpick_totalsizeqty = []; // 
			$arrpick_totalcsq = []; // total color size qty in this packing list
			
			$size_thead = "";
			$size_thead_summary = "";
			$sizesql = $this->handle_shipment->getSizeNameColumnFromOrder($orderno, 1);
			$size_colspan = 0;
			while($sizerow = $sizesql->fetch(PDO::FETCH_ASSOC)){
				$size_name = $sizerow["SizeName"];
				
				$sqlscsq  = "SELECT sum(scsq.qty) as qty 
							FROM tblship_colorsizeqty scsq 
							WHERE scsq.shipmentpriceID='$spID' and scsq.size_name='$size_name' 
							AND scsq.statusID=1 ";
				$stmt_scsq = $this->conn->prepare($sqlscsq);
				$stmt_scsq->execute();
				$row_scsq = $stmt_scsq->fetch(PDO::FETCH_ASSOC);
					$this_qty = $row_scsq["qty"];
				
				if($this_qty>0){
					$arrsize[] = $size_name;
				
					$arrpick_totalsizeqty[$size_name] = 0;

					$size_thead .= '<td align="center" style="width: 3%; ">'.$size_name.'</td>';
					$size_thead_summary .= '<td align="center" style="width: 5%; ">'.$size_name.'</td>';
					$size_colspan++;
				}
			}//--- End While Size Range ---//
			
			//$arrsize = array("32","34","36","38","40","42","44");
			
			$emptyrow = "<td></td><td></td><td></td><td></td>";
			foreach ($arrsize as $value) {
				$emptyrow .= '<td style="width: 3%; "></td>';
			}
			$emptyrow .= "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
			//$thead_sizewidth = $size_colspan * 3;
			
			$all_colspan = $size_colspan + 10;
			$left_colspan = $size_colspan + 5;
			$right_colspan = 5;
			
			$css_size_wd = $size_colspan * 3;
			$css_goods = (100 - $css_size_wd) * 0.20;
			$css_color = (100 - $css_size_wd - $css_goods) * 0.18;
			$css_ctn   = (100 - $css_size_wd - $css_goods - $css_color) / 6;
			
			//$invoice_date = strtoupper(date_format(date_create($invoice_date), "d-M-Y"));
			//$shippeddate  = strtoupper(date_format(date_create($shippeddate), "M d,Y"));
			//$ship_to = htmlspecialchars_decode($ship_to);
			$ship_to = str_replace("&AMP;", "&", $ship_to);
			
			$html .= '<table cellpadding="2" cellspacing="0" border="0" style="width:100%">
							<tr>
								<td colspan="3" align="center"><u>PACKING LIST</u></td>
								</tr>
							<tr>
							<td style="width:40%">
								<table cellpadding="2" style="width:100%" border="1">
								<tr>
									<td style="width:50%">Style NO. SAP (long)</td>
									<td style="width:50%">'.$styleNo.'</td>
									</tr>
								<tr>
									<td style="width:50%">Order NO.</td>
									<td style="width:50%">'.$BuyerPO.'</td>
									</tr>
								<tr>
									<td style="width:50%">Style Description</td>
									<td style="width:50%">'.$shipping_marking.'</td>
									</tr>
								<tr>
									<td style="width:50%">Packing Type</td>
									<td style="width:50%">'.$str_packing_type.'</td>
									</tr>
								<tr>
									<td style="width:50%">Total Shipped Quantity</td>
									<td style="width:50%">'.$grand_qty.'</td>
									</tr>
								<tr>
									<td style="width:50%">Total Carton Quantity</td>
									<td style="width:50%">'.$ctn_qty.'</td>
									</tr>
								<tr>
									<td style="width:50%">Handover Date</td>
									<td style="width:50%">'.$shippeddate.'</td>
									</tr>
								<tr>
									<td style="width:50%">Shipment From</td>
									<td style="width:50%">'.$manucountry.'</td>
									</tr>
								<tr>
									<td style="width:50%">Shipment To</td>
									<td style="width:50%">'.$destcountry.'</td>
									</tr>
								<tr>
									<td style="width:50%">Invoice Number</td>
									<td style="width:50%">'.$invoice_no.'</td>
									</tr>
								<tr>
									<td style="width:50%">Shipping Method</td>
									<td style="width:50%">'.$shipmode.'</td>
									</tr>
								<tr>
									<td style="width:50%">Shipment Date</td>
									<td style="width:50%">'.$shippeddate.'</td>
									</tr>
								<tr>
									<td style="width:50%">Country Of Origin</td>
									<td style="width:50%">'.$manucountry.'</td>
									</tr>
								<tr>
									<td style="width:50%">Fabric\'s Country Of Origin</td>
									<td style="width:50%">'.$fabric_country.'</td>
									</tr>
								<tr>
									<td style="width:50%">Shortship Limit in %</td>
									<td style="width:50%">-'.$Toleranceminus.'</td>
									</tr>
								<tr>
									<td style="width:50%">Overship Limit in %</td>
									<td style="width:50%">'.$Toleranceplus.'</td>
									</tr>
									</table>
							</td>
							<td style="width:30%">VENDOR:<br/>'.$ownership.'<br/>'.$owneraddress.'</td>
							<td style="width:30%">CONSIGNEE:<br/>'.$csn_name.'<br/>'.$csn_address.'</td>
							</tr>
							</table><br/><br/>';
					
					$max_size_length = 4;
					$start_length   = 0;
					$count_size_row = 0;
					$grand_ship  = 0;
					$grand_order = 0;
					for($i=0;$i<count($arrsize);$i++){
						$this_size = $arrsize[$i];
						$start_length++;
						
						if($start_length==1){
							$html .= '<table cellpadding="2" border="1" width="100%">';
							$html .= '<tr>';
							$html .= '<td align="center" style="width:11%" rowspan="2">Color/Size</td>';
							$html .= '<td align="center" style="width:4%" rowspan="2">Length</td>';
						}
						
						$html.= '<td align="center" style="width:18%" colspan="5">'.$this_size.'</td>';
						
						if($start_length==4){
							$html .= '<td style="width:13%" rowspan="2" align="center">Sum / Color</td>';
							$html .= '</tr>';
							
							$sec_row = $i - 3;
							$html .= '<tr>';
							for($s=$sec_row;$s<=$i;$s++){
								$html .= '<td align="center">Ship</td>';
								$html .= '<td align="center">Order</td>';
								$html .= '<td align="center">Diff</td>';
								$html .= '<td align="center">Diff%</td>';
								$html .= '<td align="center">Qty -/+</td>';
							}
							$html .= '</tr>';
							
							//-----------------------//
							//---- Looping Color ----//
							$arr_size_total = array();
							foreach($arr_all_size_color as $key => $qty){
								$this_key = substr($key,1);
								list($group_number, $color) = explode("**^^",$this_key);
								
								$html .= '<tr>';
								$html .= '<td align="center">'.$color.'</td>';
								$html .= '<td></td>';
								$total_shipqty = 0;
								for($s=$sec_row;$s<=$i;$s++){
									$this_size = $arrsize[$s];
									$ship_qty  = $arr_all_size_color["$key"]["$this_size"];
									$order_qty = $arr_order["$group_number**^^$this_size"];
									$diff = $ship_qty - $order_qty;
									$diff_percent = number_format($diff / $order_qty * 100, 2);
									$total_shipqty += $ship_qty;
									
									$minus_qty = ceil($order_qty * ($Toleranceminus/100)) * -1;
									$plus_qty  = ceil($order_qty * $Toleranceplus/100);
									$diff1     = ($diff<0 && $diff_percent<$Toleranceminus? $minus_qty: 0);
									$diff1     = ($diff>0 && $diff_percent>$Toleranceplus? $plus_qty: $diff1);
									
									$html .= '<td align="center">'.$ship_qty.'</td>';
									$html .= '<td align="center">'.$order_qty.'</td>';
									$html .= '<td align="center">'.$diff.'</td>';
									$html .= '<td align="center">'.$diff_percent.'</td>';
									$html .= '<td align="center">'.$diff1.'</td>';
									
									$arr_size_total["s$this_size"]["ship_qty"] += $ship_qty;
									$arr_size_total["s$this_size"]["order_qty"] += $order_qty;
									
									$grand_ship  += $ship_qty;
									$grand_order += $order_qty;
								}//--- End for size range ---//
								
								$html .= '<td align="center">'.$total_shipqty.'</td>';
								$html .= '</tr>';
							}
							//--- End Foreach Color ---//
							//-------------------------//
							
							//----------------------------//
							//---- Looping Size Total ----//
							$html .= '<tr>';
							$html .= '<td align="center">Total </td>';
							$html .= '<td></td>';
							foreach($arr_size_total as $this_size => $arr_value){
								$subtt_ship_qty  = $arr_value["ship_qty"];
								$subtt_order_qty = $arr_value["order_qty"];
								
								$html .= '<td align="center">'.$subtt_ship_qty.'</td>';
								$html .= '<td align="center">'.$subtt_order_qty.'</td>';
								$html .= '<td align="center"></td>';
								$html .= '<td align="center"></td>';
								$html .= '<td align="center"></td>';
							}
							$html .= '<td align="center"></td>';
							$html .= '</tr>';
							//--------------------------//
							//--------------------------//
							
							$html .= '</table><br/><br/>';
							$start_length = 0;
							$count_size_row++;
						}//--- End Check Start_length==4 ---//
						
					}//--- End For arrsize ---//
					
					if($start_length!=4 && $start_length!=0){
						$this_s  = ($count_size_row * 4);
						$sec_row = count($arrsize) - ($count_size_row * 4);
						$html .= '<td style="width:13%" rowspan="2" align="center">Sum / Color </td>';
						$html .= '</tr>';
						$html .= '<tr>';
						for($s=$this_s;$s<count($arrsize);$s++){
							$html .= '<td>Ship</td>';
							$html .= '<td>Order</td>';
							$html .= '<td>Diff</td>';
							$html .= '<td>Diff%</td>';
							$html .= '<td>Qty -/+</td>';
						}
						$html .= '</tr>';
						
						//-----------------------//
						//---- Looping Color ----//
						$arr_size_total = array();
						foreach($arr_all_size_color as $key => $qty){
							$this_key = substr($key,1);
							list($group_number, $color) = explode("**^^",$this_key);
								
							$html .= '<tr>';
							$html .= '<td align="center">'.$color.'</td>';
							$html .= '<td></td>';
							$total_shipqty = 0;
							for($s=$this_s;$s<count($arrsize);$s++){
								$this_size = $arrsize[$s];
								$ship_qty  = $arr_all_size_color["$key"]["$this_size"];
								$order_qty = $arr_order["$group_number**^^$this_size"];
								$diff = $ship_qty - $order_qty;
								$diff_percent = number_format($ship_qty / $order_qty * 100, 0);
								$total_shipqty += $ship_qty;
								
								$minus_qty = ceil($order_qty * ($Toleranceminus/100)) * -1;
								$plus_qty  = ceil($order_qty * $Toleranceplus/100);
								$diff1     = ($diff<0 && $diff_percent<$Toleranceminus? $minus_qty: 0);
								$diff1     = ($diff>0 && $diff_percent>$Toleranceplus? $plus_qty: $diff1);
								
								$html .= '<td align="center">'.$ship_qty.'</td>';
								$html .= '<td align="center">'.$order_qty.'</td>';
								$html .= '<td align="center">'.$diff.'</td>';
								$html .= '<td align="center">'.$diff_percent.'%</td>';
								$html .= '<td align="center">'.$diff1.'</td>';
									
								$arr_size_total["s$this_size"]["ship_qty"] += $ship_qty;
								$arr_size_total["s$this_size"]["order_qty"] += $order_qty;
								
								$grand_ship  += $ship_qty;
								$grand_order += $order_qty;
							}//--- End for size range ---//
								
							$html .= '<td align="center">'.$total_shipqty.'</td>';
							$html .= '</tr>';
						}
						//--- End Foreach Color ---//
						//-------------------------//
						
						//----------------------------//
						//---- Looping Size Total ----//
						$html .= '<tr>';
						$html .= '<td align="center">Total </td>';
						$html .= '<td></td>';
						foreach($arr_size_total as $this_size => $arr_value){
							$subtt_ship_qty  = $arr_value["ship_qty"];
							$subtt_order_qty = $arr_value["order_qty"];
								
							$html .= '<td align="center">'.$subtt_ship_qty.'</td>';
							$html .= '<td align="center">'.$subtt_order_qty.'</td>';
							$html .= '<td align="center"></td>';
							$html .= '<td align="center"></td>';
							$html .= '<td align="center"></td>';
						}
						$html .= '<td align="center"></td>';
						$html .= '</tr>';
						//--------------------------//
						//--------------------------//
						
						$html .= '</table><br/><br/>';
					}//--- End Check last row of size range ---//
					
					$grand_diff = $grand_ship - $grand_order;
					$grand_percent = round($grand_diff / $grand_order * 100,2);
					
					$html .= '<table cellpadding="2">';
					$html .= '<tr>';
					$html .= '<td style="width:50%"></td>';
					$html .= '<td align="right">';
					$html .= '<table cellpadding="2" border="1">
								<tr>
									<td colspan="5" align="center">TOTAL</td>
									</tr>
								<tr>
									<td align="center">Ship</td>
									<td align="center">Order</td>
									<td align="center">Diff.</td>
									<td align="center">Diff.%</td>
									<td align="center">Qty -/+</td>
									</tr>
								<tr>
									<td style="background-color:#e8e8e8" align="center">'.$grand_ship.'</td>
									<td style="background-color:#e8e8e8" align="center">'.$grand_order.'</td>
									<td style="background-color:#e8e8e8" align="center">'.$grand_diff.'</td>
									<td style="background-color:#e8e8e8" align="center">'.$grand_percent.'</td>
									<td style="background-color:#e8e8e8" align="center"></td>
									</tr>
									</table>';
					$html .= '</td></tr>';
					$html .= '</table><br/><br/>';
					
							
					$html .= '<table cellpadding="2" border="1" style="width:100%">
							<tr>
								<td rowspan="2" align="center" style="width:'.$css_color.'%">COLOR</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">CTN NO.</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">NO OF CARTONS</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">TOTAL SHIPPED QUANTITY ('.$uom.')</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">UNITS PER CARTON</td>
								<td align="center" colspan="'.$size_colspan.'" style="width: '.$css_size_wd.'%; ">SIZE QUANTITY</td>
								
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">GROSS WEIGHT (KGS)</td>
								<td rowspan="2" align="center" style="width:'.$css_ctn.'%">NET WEIGHT (KGS)</td>
								<td colspan="3" align="center" style="width:12%">CARTON MEASUREMENTS</td>
								
							</tr>
							<tr>
								'.$size_thead.'
								<td align="center" style="width:4%">Length<br/>in cm</td>
								<td align="center" style="width:4%">Width<br/>in cm</td>
								<td align="center" style="width:4%">Height<br/>in cm</td>
							</tr>
						

						<!--<tr>
							'.$emptyrow.'
						</tr>-->';
						
			$pack_ctn_qty = 0; // qty of ctn used in one po
			$pack_netweight = 0;
			$pack_grossweight = 0;
			$pack_netnetweight = 0;
			$pack_totalpcs = 0;
			$totalpackqty = 0;
			$totalcbm = 0;

			$arrtotalsizeqty = [];
			
			for($arr=0;$arr<count($arr_row);$arr++){
				$ctn_range     = $arr_row[$arr]["ctn_range"];
				$count_ctn     = $arr_row[$arr]["count_ctn"];
				$SKU           = $arr_row[$arr]["SKU"];
				$this_ctn_qty  = $arr_row[$arr]["this_ctn_qty"];
				$total_qty     = $arr_row[$arr]["total_qty"];
				$this_nnw      = $arr_row[$arr]["this_nnw"];
				$one_nnw       = round($this_nnw / $count_ctn, 3);
				$this_nw       = $arr_row[$arr]["this_nw"];
				$one_nw        = round($this_nw / $count_ctn, 3);
				$this_gw       = $arr_row[$arr]["this_gw"];
				$one_gw        = round($this_gw / $count_ctn, 3);
				$this_cbm      = $arr_row[$arr]["cbm_total"];
				$ctn_measurement = $arr_row[$arr]["ctn_measurement"];
				$ext_length = $arr_row[$arr]["ext_length"];
				$ext_width  = $arr_row[$arr]["ext_width"];
				$ext_height = $arr_row[$arr]["ext_height"];
				
				// list($ext_length, $ext_width, $other_height) = explode("x", $ctn_measurement);
				// list($ext_height, $other_unit) = explode("(", $other_height);
				// list($ctn_unit, $empty) = explode(")", $other_unit);
				
				if($ctn_unit=="cm"){
					$ctn_cm     = round($ext_length)." x ".round($ext_width)." x ".round($ext_height)." (cm)";
				}
				else{//inch
					// $ext_length = round($ext_length * 2.54, 1);
					// $ext_width  = round($ext_width * 2.54, 1);
					// $ext_height = round($ext_height * 2.54, 1);
					
					$ctn_cm     = round($ext_length)." x ".round($ext_width)." x ".round($ext_height)." (cm)";
				}
				
				$arr_grp_color = $arr_row[$arr]["arr_grp_color"];
				$count_grp     = count($arr_row[$arr]["arr_grp_color"]);
				$arr_size_info = $arr_row[$arr]["arr_size_info"];
				
			$html .= '<tr>';
			
				for($g=0;$g<count($arr_grp_color);$g++){
					list($group_number, $sku) = explode("**%%^^",$arr_grp_color[$g]);
					
					$sqlSGC = "SELECT group_concat(c.colorName,' (',g.styleNo,')' separator '<br/>') as grp_color, bid.shipping_marking 
								FROM tblship_group_color sgc 
								INNER JOIN tblcolor c ON c.ID = sgc.colorID
								INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
								LEFT JOIN tblbuyer_invoice_detail bid ON bid.shipmentpriceID = sgc.shipmentpriceID 
																	AND bid.group_number = sgc.group_number 
																	AND bid.del=0 AND sgc.statusID = 1
								WHERE sgc.shipmentpriceID='$spID' AND sgc.group_number='$group_number' AND sgc.statusID=1";
					$stmt_sgc = $this->conn->query($sqlSGC);
					$row_sgc  = $stmt_sgc->fetch(PDO::FETCH_ASSOC);
						$grp_color = $row_sgc["grp_color"];
						$shipping_marking = $row_sgc["shipping_marking"];
					
					if($g>0){
						$html .= "<tr>";
					}
						
					$html .= '<td >'.$grp_color.'</td>';
					if($g==0){
						$html .= '<td rowspan="'.$count_grp.'" align="center" >'.$ctn_range.'</td>';
						$html .= '<td rowspan="'.$count_grp.'" align="center" >'.$count_ctn.'</td>';
						$html .= '<td rowspan="'.$count_grp.'" align="center" >'.$total_qty.'</td>';
						$html .= '<td rowspan="'.$count_grp.'" align="center" >'.$this_ctn_qty.'</td>';
					}
					
					foreach ($arrsize as $size) {
						$this_qty = (array_key_exists($size,$arr_size_info["$group_number"]) ? 
										$arr_size_info["$group_number"]["$size"]:0);
						$str_qty  = ($this_qty==0? "": $this_qty);
						$html .= '<td class="center-align" style="width:3%; ">'.$str_qty.'</td>';
						$arrtotalsizeqty["$size"] += ($this_qty * $count_ctn);
						
						//if(array_key_exists("$group_number^=$grp_color", $arrpick_totalcsq)){
							//if(array_key_exists($size ,$arrpick_totalcsq["$group_number^=$grp_color"])){
								$arrpick_totalcsq["$group_number^=$grp_color"][$size] += $this_qty * $count_ctn;
							//}
						//}
						
					}//--- End Foreach size qty ---//
					
					if($g>0){
						$html .= "</tr>";
					}
					else{
						$html .= '
						
						<td align="center" rowspan="'.$count_grp.'">'.$this_gw.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$this_nw.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$ext_length.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$ext_width.'</td>
						<td align="center" rowspan="'.$count_grp.'">'.$ext_height.'</td>
						</tr>';
						
					}
				}//--- End For Color ---//

				$pack_ctn_qty += $count_ctn;
				$totalpackqty += $total_qty;
				$pack_netweight += $this_nw;
				$pack_grossweight += $this_gw;
				$pack_netnetweight += $this_nnw;
				$totalcbm += $this_cbm;

				
			}//--- End Outer For Row ---//class="font-blue"
				$html .= '<!--<tr> '.$emptyrow.' </tr>-->
						  <tr >
							<td colspan="2">TOTAL</td>
							<td align="center">'.$pack_ctn_qty.'</td>
							<td align="center">'.$totalpackqty.'</td>
							<td align="center"></td>';
				foreach ($arrsize as $size) {
					$size_total = $arrtotalsizeqty["$size"];
					$html .= '<td align="center">'.$size_total.'</td>';
				}
				
				$html .= '  <td align="center">'.$pack_grossweight.'</td>
							<td align="center">'.$pack_netweight.'</td>
							<td align="center"></td>
							<td align="center"></td>
							<td align="center"></td>
							</tr>
						</table>
					<br>
					<br>';
				
				
				$html .= '<table border="0" cellpadding="2" cellspacing="0">
							
							<tr>
								<td style="width:15%">TOTAL GROSS WEIGHT: </td>
								<td>'.$pack_grossweight.' KGS</td>
							</tr>

							<tr>
								<td>TOTAL NET WEIGHT: </td>
								<td>'.$pack_netweight.' KGS</td>
							</tr>
							
							<tr>
								<td>TOTAL CARTON QUANTITY: </td>
								<td>'.$ctn_qty.' CTNS</td>
							</tr>
							
						</table>';
					
			
			
		}//--- End While Buyer Invoice Detail ---//
		
		return $html;
	}
	
	public function getBuyerInvoicePackingListTemplate6($invID){ //buyer_puma.php
		$html = '';
		
		$tblbuyer_invoice        = ($this->isBuyerPayment==0? "tblbuyer_invoice": "tblbuyer_invoice_payment");
		$tblbuyer_invoice_detail = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail": "tblbuyer_invoice_payment_detail");
		
		$sql = "SELECT bi.invoice_no, bi.invoice_date, bi.shippeddate, invd.shipmentpriceID, invd.ht_code, group_concat(distinct invd.shipping_marking) as shipping_marking, 
						g.styleNo, g.orderno, sp.GTN_buyerpo as BuyerPO, csn.Name as csn_name, csn.Address as csn_address, bi.vesselname,
						cp.CompanyName_ENG as ownership, cp.Address as owneraddress, cp.Tel as ownertel, cp.Fax as ownerfax,
						lch.lc_number, st.Description as uom, od.FactoryID as od_FactoryID, bd.Description as brand,
						fty2.FactoryName_ENG as exporter, fty.Address as exporter_address, fty.Tel as exporter_tel, fty.Fax as exporter_fax,
						sm.Description as shipmode, cty.Description as manucountry, bi.ship_to, bi.ship_address, bi.container_no,
						group_concat(distinct c.colorName) as color, pyr.Description as bill_to, pyr.address as bill_address,
						cty2.Description as destcountry, sp.Toleranceminus, sp.Toleranceplus, spk.packing_type, lci.lc_date,
						bdt.Description as buyer_dest, invd.BICID  
						
				FROM $tblbuyer_invoice_detail invd 
				INNER JOIN $tblbuyer_invoice bi ON bi.ID = invd.invID
				LEFT JOIN tblshipmentprice sp ON sp.ID = invd.shipmentpriceID 
				LEFT JOIN tblship_packing spk ON spk.shipmentpriceID = sp.ID AND spk.statusID=1
				LEFT JOIN tblconsignee csn ON csn.ConsigneeID = bi.ConsigneeID
				LEFT JOIN tblgarment g ON g.orderno = sp.Orderno
				LEFT JOIN tblorder od ON od.Orderno = sp.Orderno
				LEFT JOIN tblcompanyprofile cp ON cp.ID = bi.issue_from
				LEFT JOIN tblfactory fty ON fty.FactoryID = bi.shipper
				LEFT JOIN tblfactory fty2 ON fty2.FactoryID = od.importCompany
				LEFT JOIN tblfactory ftym ON ftym.FactoryID = od.manufacturer
				LEFT JOIN tblcountry cty ON cty.ID = ftym.countryID
				LEFT JOIN tbllc_assignment_detail lcd ON lcd.shipmentpriceID = invd.shipmentpriceID AND lcd.del=0 AND invd.del=0
				LEFT JOIN tbllc_assignment_info lci ON lci.LCIID = lcd.LCIID AND lci.del=0
				LEFT JOIN tbllc_assignment_head lch ON lch.LCHID = lci.LCHID
				LEFT JOIN tblshipmode sm ON sm.ID = bi.shipmodeID 
				LEFT JOIN tblbrand bd ON bd.ID = od.brandID
				LEFT JOIN tblset st ON st.ID = od.Qunit
				LEFT JOIN tblship_group_color sgc ON sgc.shipmentpriceID = sp.ID AND sgc.statusID=1
				LEFT JOIN tblcolor c ON c.ID = sgc.colorID
				LEFT JOIN tblpayer pyr ON pyr.ID = bi.built_to
				LEFT JOIN tblbuyerdestination bdt ON bdt.ID = bi.BuyerDestID
				LEFT JOIN tblcountry cty2 ON cty2.ID = bdt.countryID
				WHERE invd.invID = '$invID' AND invd.del = 0 AND invd.group_number>0
				GROUP BY invd.shipmentpriceID 
				ORDER BY invd.ID ASC ";
		$packsql = $this->conn->prepare($sql);
		$packsql->execute(); 
		while($packrow = $packsql->fetch(PDO::FETCH_ASSOC)){
			$invoice_no   = $packrow["invoice_no"];
			$invoice_date = $packrow["invoice_date"];
			$container_no = $packrow["container_no"];
			
			$BuyerPO     = $packrow["BuyerPO"];
			$shippeddate = $packrow["shippeddate"];
			$spID        = $packrow["shipmentpriceID"];
			$ht_code     = $packrow["ht_code"];
			$ship_remark = $packrow["shipping_marking"];
			$styleNo     = $packrow["styleNo"]; 
			$orderno     = $packrow["orderno"]; 
			$shipmode    = $packrow["shipmode"]; 
			$vesselname  = $packrow["vesselname"]; 
			$buyer_dest  = $packrow["buyer_dest"]; 
			$brand       = $packrow["brand"]; 
			$color       = $packrow["color"]; 
			$uom         = strtoupper($packrow["uom"]); 
			
			$ownership    = $packrow["ownership"]; 
			$owneraddress = $packrow["owneraddress"]; 
			$ownertel     = $packrow["ownertel"]; 
			$ownerfax     = $packrow["ownerfax"]; 
			$csn_name     = $packrow["csn_name"]; 
			$csn_address  = $packrow["csn_address"]; 
			$lc_number    = $packrow["lc_number"]; 
			$lc_date      = $packrow["lc_date"]; 
			$od_FactoryID = $packrow["od_FactoryID"]; 
			$manucountry  = $packrow["manucountry"]; 
			$bill_to      = $packrow["bill_to"]; 
			$bill_address = $packrow["bill_address"]; 
			
			$exporter      = strtoupper($packrow["exporter"]); 
			$exporter_addr = strtoupper($packrow["exporter_address"]); 
			$ship_to       = strtoupper($packrow["ship_to"]); 
			$ship_addr     = strtoupper($packrow["ship_address"]); 
			$destcountry   = strtoupper($packrow["destcountry"]); 
			
			$exporter_tel   = $packrow["exporter_tel"]; 
			$exporter_fax   = $packrow["exporter_fax"]; 
			$Toleranceminus = $packrow["Toleranceminus"]; 
			$Toleranceplus  = $packrow["Toleranceplus"]; 
			$packing_type   = $packrow["packing_type"]; 
			$this->BICID    = $packrow["BICID"]; 
			
			$arr_all = $this->getBuyerInvoicePackingListDataFromCartonInv($spID, $invID);
			$arr_row            = $arr_all["arr_list"];
			$grand_qty          = $arr_all["grand_qty"];
			$ctn_qty            = $arr_all["ctn_qty"];
			$arr_all_size       = $arr_all["arr_all_size"];
			$arr_all_size_color = $arr_all["arr_all_size_color"];
			$shipping_marking   = $arr_all["shipping_marking"];
			$grand_nw           = $arr_all["grand_nw"];
			$grand_gw           = $arr_all["grand_gw"];
			$grand_cbm          = $arr_all["grand_cbm"];
			$arr_ctn_measurement = $arr_all["arr_ctn_measurement"];
			$str_ctn_measurement = $arr_ctn_measurement[0];
			list($ext_length, $ext_width, $str_other) = explode("x", $str_ctn_measurement);
			list($ext_height, $str_other) = explode("(", $str_other);
			$uom = strtoupper(substr($str_other,0,2));
			
			$arr_size = array();
			foreach($arr_all_size as $key => $value){
				list($group_number, $size) = explode("**^^", $key);
				if(!in_array($size, $arr_size)){
					$arr_size[] = $size;
				}
			}
			
			
			$html .= '<br pagebreak="true">';
			
			$html .= '<table border="0" cellpadding="3" >
						<tr>
							<th class="bold-text center-align" align="center" colspan="12">
								<h1>DETAIL PACKING LIST</h1>
							</th>
						</tr>
						<tr>
							<td class="border_left border_right border_top" style="width:10%">INVOICE#</td>
							<td class="border_left border_right border_top" style="width:8%">INVOICE DATE</td>
							<td class="border_left border_right border_top" colspan="2"></td>
							<td class="border_left border_right border_top" style="width:8%">PO#</td>
							<td class="border_left border_right border_top" style="width:8%">LC#</td>
							<td class="border_left border_right border_top" style="width:11%">LC ISSUANCE DATE</td>
							<td class="border_left border_right border_top" style="width:11%">MADE IN (ORIGIN)</td>
							<td class="border_left border_right border_top" style="width:14%" colspan="2">CONTAINER#</td>
							<td class="border_left border_right border_top" style="width:14%" colspan="2">SEAL#</td>
							</tr>
						<tr>
							<td class="border_left border_right border_btm" align="center">'.$invoice_no.'</td>
							<td class="border_left border_right border_btm" align="center">'.$invoice_date.'</td>
							<td class="border_left border_right border_btm" align="center" colspan="2"></td>
							<td class="border_left border_right border_btm" align="center">'.$BuyerPO.'</td>
							<td class="border_left border_right border_btm" align="center">'.$lc_number.'</td>
							<td class="border_left border_right border_btm" align="center">'.$lc_date.'</td>
							<td class="border_left border_right border_btm" align="center">'.$manucountry.'</td>
							<td class="border_left border_right border_btm" align="center" colspan="2">'.$container_no.'</td>
							<td class="border_left border_right border_btm" align="center" colspan="2"></td>
							</tr>
						<tr>
							<td class="border_left border_right border_top" colspan="4">DESCRIPTION</td>
							<td class="border_left border_right border_top" colspan="2">HTS CODE</td>
							<td class="border_left border_right border_top">SHIP MODE</td>
							<td class="border_left border_right border_top">CARRIER/FLIGHT/VESSEL</td>
							<td class="border_left border_right border_top" colspan="2">FCR or BL/HAWB#</td>
							<td class="border_left border_right border_top" colspan="2">FINAL DESTINATION</td>
							</tr>
						<tr>
							<td class="border_left border_right border_btm" colspan="4">'.$ship_remark.'</td>
							<td class="border_left border_right border_btm" colspan="2">'.$ht_code.'</td>
							<td class="border_left border_right border_btm">'.$shipmode.'</td>
							<td class="border_left border_right border_btm">'.$vesselname.'</td>
							<td class="border_left border_right border_btm" colspan="2"></td>
							<td class="border_left border_right border_btm" colspan="2">'.$buyer_dest.'</td>
							</tr>
						<tr>
							<td class="border_left border_right border_top" style="width:10%">ETD Date</td>
							<td class="border_left border_right border_top" style="width:8%">ETA Date</td>
							<td class="border_left border_right border_top">TOTAL PCS</td>
							<td class="border_left border_right border_top">TOTAL CTNS</td>
							<td class="border_left border_right border_top" style="width:8%">NET WEIGHT</td>
							<td class="border_left border_right border_top" style="width:8%">GROSS WEIGHT</td>
							<td class="border_left border_right border_top" style="width:11%">CBM</td>
							<td class="border_left border_right border_top border_btm" style="width:11%" rowspan="2">CARTON MEASUREMENT</td>
							<td class="border_left border_right border_top" style="width:7%">L</td>
							<td class="border_left border_right border_top" style="width:7%">W</td>
							<td class="border_left border_right border_top" style="width:7%">H</td>
							<td class="border_left border_right border_top" style="width:7%">CTN UOM</td>
							</tr>
						<tr>
							<td class="border_left border_right border_btm" align="center"></td>
							<td class="border_left border_right border_btm" align="center"></td>
							<td class="border_left border_right border_btm" align="center">'.$grand_qty.'</td>
							<td class="border_left border_right border_btm" align="center">'.$ctn_qty.'</td>
							<td class="border_left border_right border_btm" align="center">'.$grand_nw.'</td>
							<td class="border_left border_right border_btm" align="center">'.$grand_gw.'</td>
							<td class="border_left border_right border_btm" align="center">'.$grand_cbm.'</td>
							<td class="border_left border_right border_btm" align="center">'.$ext_length.'</td>
							<td class="border_left border_right border_btm" align="center">'.$ext_width.'</td>
							<td class="border_left border_right border_btm" align="center">'.$ext_height.'</td>
							<td class="border_left border_right border_btm" align="center">'.$uom.'</td>
							</tr>
					</table>';
			
			$html .= '<br/>';
			$html .= '<br/>';
			$number_size = count($arr_size);
			$colspan = $number_size + 3;
			
			//============================================//
			//------------- PACKING DETAIL  --------------//
			//============================================//
			$html .= '<table cellpadding="3">';
			$html .= '<tr>
						<td class="full-border" colspan="3">PACKING DETAILS</td>
						<td colspan="'.$colspan.'"></td>
						<td class="full-border" colspan="3">CONTAINER#</td>
						<td class="full-border" colspan="3">SEAL#</td>
						</tr>';
			$html .= '<tr>
						<td class="full-border" colspan="2" >CARTON NUMBER</td>
						<td class="full-border" rowspan="2">PO#</td>
						<td class="full-border" rowspan="2">ITEM#</td>
						<td class="full-border" rowspan="2">STYLE#</td>
						<td class="full-border" rowspan="2">COLOR</td>
						<td class="full-border" colspan="'.$number_size.'">SIZE</td>
						<td class="full-border" rowspan="2">PCS / CTN</td>
						<td class="full-border" rowspan="2">NO. OF CTN</td>
						<td class="full-border" rowspan="2">TOTAL PCS</td>
						<td class="full-border" rowspan="2">NET WEIGHT</td>
						<td class="full-border" rowspan="2">GROSS WEIGHT</td>
						<td class="full-border" rowspan="2">CBM</td>
						</tr>';
			$html .= '<tr>
						<td class="full-border" >FROM#</td>
						<td class="full-border" >TO#</td>';
						for($s=0;$s<count($arr_size);$s++){
							$html .= '<td class="full-border" >'.$arr_size[$s].'</td>';
						}
				$html .= '</tr>';
				
			for($arr=0;$arr<count($arr_row);$arr++){
				$ctn_range     = $arr_row[$arr]["ctn_range"];
				$count_ctn     = $arr_row[$arr]["count_ctn"];
				$mixID         = $arr_row[$arr]["mixID"];
				$SKU           = $arr_row[$arr]["SKU"];
				$this_ctn_qty  = $arr_row[$arr]["this_ctn_qty"];
				$total_qty     = $arr_row[$arr]["total_qty"];
				$this_nnw      = $arr_row[$arr]["this_nnw"];
				$one_nnw       = round($this_nnw / $count_ctn, 3);
				$this_nw       = $arr_row[$arr]["this_nw"];
				$one_nw        = round($this_nw / $count_ctn, 3);
				$this_gw       = $arr_row[$arr]["this_gw"];
				$one_gw        = round($this_gw / $count_ctn, 3);
				$this_cbm      = $arr_row[$arr]["cbm_total"];
				$ctn_measurement = $arr_row[$arr]["ctn_measurement"];
				$ext_length = $arr_row[$arr]["ext_length"];
				$ext_width  = $arr_row[$arr]["ext_width"];
				$ext_height = $arr_row[$arr]["ext_height"];
				$arr_grp_color = $arr_row[$arr]["arr_grp_color"];
				$color_row     = count($arr_grp_color);
				
				$arr_grp = explode("::^^", $mixID);
				list($start, $end) = explode("-", $ctn_range);
				
				list($group_number, $SKU) = explode("**%%^^", $arr_grp_color[0]);
				$this_color = $this->getGroupNumberColor($spID, $group_number);
				
				$str_total_qty = number_format($total_qty, 0);
				
				$html .= '<tr>
						<td class="full-border" rowspan="'.$color_row.'">'.$start.'</td>
						<td class="full-border" rowspan="'.$color_row.'">'.$end.'</td>
						<td class="full-border" rowspan="'.$color_row.'">'.$BuyerPO.'</td>
						<td class="full-border" rowspan="'.$color_row.'">'.$SKU.'</td>
						<td class="full-border" rowspan="'.$color_row.'">'.$styleNo.'</td>
						<td class="full-border" >'.$this_color.'</td>';
						for($s=0;$s<count($arr_size);$s++){
							$this_size = $arr_size[$s];
							
							$this_qty = 0;
							for($q=0;$q<count($arr_grp);$q++){
								list($gn, $size, $qty) = explode("**%%", $arr_grp[$q]);
								
								if($gn==$group_number && $this_size==$size){
									$this_qty += $qty;
								}
							}
							
							$html .= '<td class="full-border" align="center">'.$this_qty.'</td>';
						}
				$html .= '<td class="full-border" align="center" rowspan="'.$color_row.'">'.$this_ctn_qty.'</td>
						<td class="full-border" align="center" rowspan="'.$color_row.'">'.$count_ctn.'</td>
						<td class="full-border" align="center" rowspan="'.$color_row.'">'.$str_total_qty.'</td>
						<td class="full-border" align="center" rowspan="'.$color_row.'">'.$this_nw.'</td>
						<td class="full-border" align="center" rowspan="'.$color_row.'">'.$this_gw.'</td>
						<td class="full-border" align="center" rowspan="'.$color_row.'">'.$this_cbm.'</td>
						</tr>';
				for($c=1;$c<count($arr_grp_color);$c++){
					list($group_number, $SKU) = explode("**%%^^", $arr_grp_color[$c]);
					$this_color = $this->getGroupNumberColor($spID, $group_number);
					
					$html .= '<tr>
								<td class="full-border">'.$this_color.'</td>';
						for($s=0;$s<count($arr_size);$s++){
							$this_size = $arr_size[$s];
							
							$this_qty = 0;
							for($q=0;$q<count($arr_grp);$q++){
								list($gn, $size, $qty) = explode("**%%", $arr_grp[$q]);
								
								if($gn==$group_number && $this_size==$size){
									$this_qty += $qty;
								}
							}
							
							$html .= '<td class="full-border" align="center">'.$this_qty.'</td>';
						}
					$html .= '</tr>';
				}
				
				
			}//--- END Foreach ---//
			
			$html .= '<tr>
						<td class="full-border"></td>
						<td class="full-border"></td>
						<td class="full-border"></td>
						<td class="full-border"></td>
						<td class="full-border"></td>
						<td class="full-border" align="center">TOTAL</td>
						<td class="full-border" colspan="'.$number_size.'"></td>
						<td class="full-border"></td>
						<td class="full-border" align="center">'.$ctn_qty.'</td>
						<td class="full-border" align="center">'.$grand_qty.'</td>
						<td class="full-border" align="center">'.$grand_nw.'</td>
						<td class="full-border" align="center">'.$grand_gw.'</td>
						<td class="full-border" align="center">'.$grand_cbm.'</td>
						</tr>';
			$html .= '</table>';
			
			
			//============================================//
			//------------- SUMMARY QTY TABLE ------------//
			//============================================//
			$html .= '<br/><br/>';
			$html .= '<table cellpadding="3">';
			$html .= '<tr>
						<td class="full-border" colspan="3">PACKING SUMMARY</td>
						<td colspan="'.$colspan.'"></td>
						<td class="" ></td>
						</tr>';
			$html .= '<tr>
						<td class="full-border" colspan="2" >CARTON NUMBER</td>
						<td class="full-border" rowspan="2">PO#</td>
						<td class="full-border" rowspan="2">ITEM#</td>
						<td class="full-border" rowspan="2">STYLE#</td>
						<td class="full-border" rowspan="2">COLOR</td>
						<td class="full-border" colspan="'.$number_size.'">SIZE</td>
						<td class="full-border" rowspan="2">TOTAL PCS</td>
						</tr>';
			$html .= '<tr>
						<td class="full-border" >FROM#</td>
						<td class="full-border" >TO#</td>';
						for($s=0;$s<count($arr_size);$s++){
							$html .= '<td class="full-border" >'.$arr_size[$s].'</td>';
						}
				$html .= '</tr>';
			
			$arr_summary_size = array();
			for($arr=0;$arr<count($arr_row);$arr++){
				$ctn_range     = $arr_row[$arr]["ctn_range"];
				$count_ctn     = $arr_row[$arr]["count_ctn"];
				$mixID         = $arr_row[$arr]["mixID"];
				$SKU           = $arr_row[$arr]["SKU"];
				$this_ctn_qty  = $arr_row[$arr]["this_ctn_qty"];
				$total_qty     = $arr_row[$arr]["total_qty"];
				$this_nnw      = $arr_row[$arr]["this_nnw"];
				$one_nnw       = round($this_nnw / $count_ctn, 3);
				$this_nw       = $arr_row[$arr]["this_nw"];
				$one_nw        = round($this_nw / $count_ctn, 3);
				$this_gw       = $arr_row[$arr]["this_gw"];
				$one_gw        = round($this_gw / $count_ctn, 3);
				$this_cbm      = $arr_row[$arr]["cbm_total"];
				$ctn_measurement = $arr_row[$arr]["ctn_measurement"];
				$ext_length = $arr_row[$arr]["ext_length"];
				$ext_width  = $arr_row[$arr]["ext_width"];
				$ext_height = $arr_row[$arr]["ext_height"];
				$arr_grp_color = $arr_row[$arr]["arr_grp_color"];
				$color_row     = count($arr_grp_color);
				
				$arr_grp = explode("::^^", $mixID);
				list($start, $end) = explode("-", $ctn_range);
				
				list($group_number, $SKU) = explode("**%%^^", $arr_grp_color[0]);
				$this_color = $this->getGroupNumberColor($spID, $group_number);
				
				$html .= '<tr>
						<td class="full-border" rowspan="'.$color_row.'">'.$start.'</td>
						<td class="full-border" rowspan="'.$color_row.'">'.$end.'</td>
						<td class="full-border" rowspan="'.$color_row.'">'.$BuyerPO.'</td>
						<td class="full-border" rowspan="'.$color_row.'">'.$SKU.'</td>
						<td class="full-border" rowspan="'.$color_row.'">'.$styleNo.'</td>
						<td class="full-border" >'.$this_color.'</td>';
						$sub_qty = 0;
						for($s=0;$s<count($arr_size);$s++){
							$this_size = $arr_size[$s];
							
							$this_qty = 0;
							for($q=0;$q<count($arr_grp);$q++){
								list($gn, $size, $qty) = explode("**%%", $arr_grp[$q]);
								
								if($gn==$group_number && $this_size==$size){
									$this_qty += $qty;
								}
							}
							$this_qty = $this_qty * $count_ctn;
							$sub_qty += $this_qty;
							$arr_summary_size["$this_size"] += $this_qty;
							
							$html .= '<td class="full-border" align="center">'.$this_qty.'</td>';
						}//--- End Arr Size ---//
				
				$html .= '<td class="full-border" align="center">'.$sub_qty.'</td>
						</tr>';
				for($c=1;$c<count($arr_grp_color);$c++){
					list($group_number, $SKU) = explode("**%%^^", $arr_grp_color[$c]);
					$this_color = $this->getGroupNumberColor($spID, $group_number);
					
					$html .= '<tr>
								<td class="full-border">'.$this_color.'</td>';
						$sub_qty = 0;
						for($s=0;$s<count($arr_size);$s++){
							$this_size = $arr_size[$s];
							
							$this_qty = 0;
							for($q=0;$q<count($arr_grp);$q++){
								list($gn, $size, $qty) = explode("**%%", $arr_grp[$q]);
								
								if($gn==$group_number && $this_size==$size){
									$this_qty += $qty;
								}
							}
							$this_qty = $this_qty * $count_ctn;
							$sub_qty += $this_qty;
							$arr_summary_size["$this_size"] += $this_qty;
							
							$html .= '<td class="full-border" align="center">'.$this_qty.'</td>';
						}
					$html .= '<td class="full-border" align="center">'.$sub_qty.'</td>';
					$html .= '</tr>';
				}//--- End Arr Grp Color ---//
				
				
			}//--- END Foreach ---//
			
			$html .= '<tr>
						<td class="full-border"></td>
						<td class="full-border"></td>
						<td class="full-border"></td>
						<td class="full-border"></td>
						<td class="full-border"></td>
						<td class="full-border" align="center">TOTAL</td>';
						for($s=0;$s<count($arr_size);$s++){
							$this_size = $arr_size[$s];
							$size_qty = $arr_summary_size["$this_size"];
							
							$html .= '<td class="full-border" align="center">'.$size_qty.'</td>';
						}
			  $html .= '<td class="full-border" align="center">'.$grand_qty.'</td>
						</tr>';
			
			$html .= '</table>';
			
		}//--- End While ---//
		
		return $html;
	}
	
	function getBuyerInvoiceLoadRow($isBuyerPayment, $buyerID, $invID, $shippeddate, $po_selected="", $search_orderno=""){
		$i=0;
		$from = date('Y-m-d', strtotime('-90 days', strtotime($shippeddate))); 
		$to   = date('Y-m-d', strtotime('+90 days', strtotime($shippeddate))); 
		
		$tblbuyer_inv_detail = ($isBuyerPayment==0? "tblbuyer_invoice_detail": "tblbuyer_invoice_payment_detail");
		$search_orderno = str_replace(",","','", $search_orderno);
		$filter_order = (trim($search_orderno)!=""? " AND od.Orderno IN ('$search_orderno')": "");
		
		$html = '';
		$limit = ($this->acctid==1? " ": "");
		$sql = "SELECT sp.ID as shipmentpriceID, sp.Orderno, sp.BuyerPO, sp.GTN_buyerpo,
							sp.Shipdate, od.statusID, cur.CurrencyCode, 
							sum(bid.qty) as bi_qty, od.FactoryID, group_concat(distinct bi.invoice_no separator ' / ') as invoice_no
					FROM tblshipmentprice sp
					INNER JOIN tblorder od ON od.Orderno = sp.Orderno
					LEFT JOIN tblcurrency cur ON cur.ID = od.currencyID
					LEFT JOIN $tblbuyer_inv_detail bid ON bid.shipmentpriceID = sp.ID AND bid.group_number>0 AND bid.del=0 
					LEFT JOIN tblbuyer_invoice bi ON bi.ID = bid.invID
					WHERE od.buyerID='$buyerID' AND sp.statusID=1  AND od.statusID NOT IN (6)
					AND sp.Shipdate >= DATE_SUB('$shippeddate', INTERVAL 90 day) 
					AND sp.Shipdate<= DATE_SUB('$shippeddate', INTERVAL -90 day)  $po_selected $filter_order
					group by sp.ID $limit";//CURDATE()
					
		$query = "<pre>$sql</pre>";		
		$buyersql = $this->conn->prepare($sql);
		$buyersql->execute();
		while($buyerrow = $buyersql->fetch(PDO::FETCH_ASSOC)){
				$count           = $i + 1;
				$LCIID           = 0;//$buyerrow["LCIID"];
				$lc_number       = "";//$buyerrow["lc_number"];
				$lc_date         = "";//$buyerrow["lc_date"];
				$spID            = $buyerrow["shipmentpriceID"];
				$ref_BuyerPO     = $buyerrow["BuyerPO"];
				$BuyerPO         = $buyerrow["BuyerPO"];
				$GTN_buyerpo     = $buyerrow["GTN_buyerpo"];
				$BuyerPO         = ($GTN_buyerpo==""? $BuyerPO: $GTN_buyerpo);
				$Orderno         = $buyerrow["Orderno"];
				$invoice_no      = $buyerrow["invoice_no"];
				$shipdate        = $buyerrow["Shipdate"];
				$shipmentpriceID = $buyerrow["shipmentpriceID"];
				$CurrencyCode    = $buyerrow["CurrencyCode"];
				$FactoryID       = $buyerrow["FactoryID"];
				$bi_qty          = $buyerrow["bi_qty"];
				$bi_qty          = ($bi_qty==""? 0: $bi_qty);
				
					
					// $filter_query = " AND cpt.shiped=1";
					// $arr_value = $handle_fabInstore->getTotalPackingWeightByBuyerPO($spID, $FactoryID, $filter_query);
					// $totalqty += $arr_value["total_qty"];
					
					// $arr_all = $handle_shipment->getAllCuttingPickListByBuyerPO($shipmentpriceID);
					// $arr_row = $arr_all["arr_row"];
					// $arr_all_size = $arr_all["arr_all_size"];
					// foreach($arr_all_size as $key => $qty){
						// $totalqty += $qty;
					// }
					
					$totalqty = "0";
					$skip_cih = ($isBuyerPayment==0? "full_qty":"oripickpack");
					$arr_list = $this->getBuyerInvoicePackingListData($spID, "$skip_cih", $invID);// get PO planning qty 
					for($al=0;$al<count($arr_list);$al++){
						$mixID               = $arr_list[$al]["mixID"];
						$total_ctn           = $arr_list[$al]["total_ctn"];
						$total_qty_in_carton = $arr_list[$al]["total_qty_in_carton"];
						$totalqty += ($total_ctn * $total_qty_in_carton);
					}
					
					$group_number = 1;
					if(count($arr_list)>0){
						$arr_mix = explode("::^^", $mixID);
						list($group_number, $size, $this_qty) = explode("**%%", $arr_mix[0]);
					}
					
					if($isBuyerPayment==1){
						$totalqty = $totalqty - $bi_qty; // PO planning qty deduct buyer invoice qty
					}
					// echo "$totalqty / $bi_qty<br/>";
					//====================================================================//
					//--- if balance qty still > 0, then able to open in other invoice ---//
					//====================================================================//
					if($totalqty>0 || ($totalqty==0 && $bi_qty==0)){
						$grand_amt = 0;
						// $count_grp = 1;
						$sql_price = "SELECT sum(scp.qty*scp.price) / sum(scp.qty) as Buyer_price
												from tblship_colorsizeqty scp  
												where scp.shipmentpriceID = '$spID' and scp.statusID=1 
												GROUP BY scp.shipmentpriceID";
						$sel_qty = $this->conn->prepare($sql_price);
						$sel_qty->execute();
						$row_qty = $sel_qty->fetch(PDO::FETCH_ASSOC);
						
						$sqlgrp = "SELECT * FROM tblship_group_color 
									WHERE shipmentpriceID='$spID' AND group_number='$group_number' AND statusID=1";
						$sel_grp = $this->conn->prepare($sqlgrp);
						$sel_grp->execute();
						$count_grp = $sel_grp->rowCount();
						
						$str_existInOtherInv = "<label class='label label-info'>$invoice_no</label>";
						$html .= "<tr data-rownum='$i'>";
						$html .= "<td><br/><small><font color='#bdbdbd'>$count</font></small>
										<input type='checkbox' id='check$i' class='hidden_chk' name='pocheckbox[]' value='$shipmentpriceID' />
										</td>";
						// echo "<td>$lc_number </td>";
						// echo "<td>$lc_date</td>";
						$html .= "<td>";
						
							//$totalqty  = $totalqty / $count_grp;
							$price     = $row_qty['Buyer_price'];
							$price     = ($isBuyerPayment==0? round($price * 0.75, 2): $price);//commercial default discount 25%
							$str_price = number_format($price, 2);
							$total_amt = $totalqty * $price;
							$str_set   = ($count_grp>1? "SETS" : "PCS");    
							$grand_amt += $total_amt;
									$html .= "<font color='#009ec3'><b>$Orderno</b></font> -  <font color='red'>$ref_BuyerPO</font>  
											&nbsp; / &nbsp;<b>Ship Date:</b> $shipdate
											&nbsp; / &nbsp;<b>Qty:</b> $totalqty $str_set
											&nbsp; / &nbsp;<b>PO Price:</b> $CurrencyCode $str_price
											&nbsp; &nbsp; $str_existInOtherInv";
											
						$html .= "<br><small><font color='#bdbdbd'>$spID</font></small></td>";
							
						$grand_amt = number_format($grand_amt, 2);
						$html .= "<td>$CurrencyCode $grand_amt</td>";
						$html .= "</tr>";
					}//--- End if balance qty > 0 ---//
					
					$i++;
			}
			
			if($this->acctid==1){
				// $html .= '<tr>';
				// $html .= '<td colspan=""></td>';
				// $html .= '<td colspan="">'.$query.'</td>';
				// $html .= '<td colspan=""></td>';
				// $html .= '</tr>';
			}
			$arr = array("html"=>$html);
			
			return $arr;
	}
	
	function getGroupNumberColor($spID, $group_number){
		$sql = "SELECT sgc.group_number, GROUP_CONCAT(c.ColorName,' (',g.styleNo,')' separator ', ') as grp_color
						FROM tblship_group_color sgc 
						INNER JOIN tblcolor c ON c.ID = sgc.colorID
						INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
						WHERE sgc.shipmentpriceID='$spID' AND sgc.statusID=1 AND sgc.group_number='$group_number'
						group by sgc.group_number";
				$stmt_sgc = $this->conn->prepare($sql);
				$stmt_sgc->execute();
				$row_sgc = $stmt_sgc->fetch(PDO::FETCH_ASSOC);
					$grp_color = $row_sgc["grp_color"];
					
		return $grp_color;
	}


	function getBuyerInvoicePackingList_excel($sheet,$invID){
		//detail sheet

		$tblbuyer_invoice    = ($this->isBuyerPayment==0? "tblbuyer_invoice": "tblbuyer_invoice_payment");
		$tblbuyer_inv_detail = ($this->isBuyerPayment==0? "tblbuyer_invoice_detail": "tblbuyer_invoice_payment_detail");
		$tblctn              = ($this->isBuyerPayment==0? "tblcarton_inv_head": "tblcarton_inv_payment_head");
		
		$i = 1;
		$arrCol=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
				'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
				'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
				'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');

		$sql = "SELECT bi.invoice_no, bi.invoice_date, bi.BuyerID, invd.shipmentpriceID, invd.ht_code, invd.shipping_marking, 
						g.styleNo as styleNo, g.orderno, group_concat(distinct g.orderno) as grp_ia, sp.GTN_buyerpo as actual_BuyerPO, sp.BuyerPO, csn.Name as csn_name, csn.Address as csn_address, csn.EIN, csn.tel as csn_tel, csn.fax as csn_fax, csn.email as csn_email, csn.contactperson as csn_contactperson,
						cp.CompanyName_ENG as ownership, cp.Address as owneraddress, cp.Tel as ownertel, cp.Fax as ownerfax,
						lch.lc_number, max(lci.lc_date) as lc_date, od.FactoryID as od_FactoryID, invd.group_number, 
						(SELECT count(distinct sgc.garmentID)
						FROM tblship_group_color sgc 
						WHERE sgc.shipmentpriceID = invd.shipmentpriceID 
						AND sgc.group_number = invd.group_number AND sgc.statusID=1) as count_gmt, invd.BICID
						
				FROM $tblbuyer_inv_detail invd 
				INNER JOIN $tblbuyer_invoice bi ON bi.ID = invd.invID
				LEFT JOIN tblshipmentprice sp ON sp.ID = invd.shipmentpriceID 
				LEFT JOIN tblconsignee csn ON csn.ConsigneeID = bi.ConsigneeID
				LEFT JOIN tblgarment g ON find_in_set(g.garmentID, sp.StyleNo)
				LEFT JOIN tblorder od ON od.Orderno = sp.Orderno
				LEFT JOIN tblcompanyprofile cp ON cp.ID = bi.issue_from
				LEFT JOIN tbllc_assignment_detail lcd ON lcd.shipmentpriceID = invd.shipmentpriceID AND lcd.del=0 AND invd.del=0
				LEFT JOIN tbllc_assignment_info lci ON lci.LCIID = lcd.LCIID AND lci.del=0
				LEFT JOIN tbllc_assignment_head lch ON lch.LCHID = lci.LCHID
				INNER JOIN tblnewenquiry ne ON ne.QDID = g.QDID
				WHERE invd.invID = '$invID' AND invd.del = 0 AND invd.group_number>0
				GROUP BY invd.shipmentpriceID 
				ORDER BY invd.ID ASC "; 
		$packsql = $this->conn->prepare($sql);
		$packsql->execute(); 
		while($packrow = $packsql->fetch(PDO::FETCH_ASSOC)){
			$invoice_no   = $packrow["invoice_no"];
			$invoice_date = $packrow["invoice_date"];
			
			
			$actual_BuyerPO = $packrow["actual_BuyerPO"];
			$BuyerPO        = (trim($actual_BuyerPO)==""? $packrow["BuyerPO"]: $actual_BuyerPO);

			$spID        = $packrow["shipmentpriceID"];
			$ht_code     = $packrow["ht_code"];
			$ship_remark = $packrow["shipping_marking"];
			$styleNo     = $packrow["styleNo"]; 
			$orderno     = $packrow["orderno"]; 
			$grp_ia      = $packrow["grp_ia"]; 
			$count_gmt   = $packrow["count_gmt"]; 
			$str_unit    = ($count_gmt==1? "PCS":"SETS");
			
			$group_number = $packrow["group_number"]; 
			$ownership    = $packrow["ownership"]; 
			$owneraddress = $packrow["owneraddress"]; 
			$ownertel     = $packrow["ownertel"]; 
			$ownerfax     = $packrow["ownerfax"]; 
			$csn_name     = $packrow["csn_name"]; 
			$csn_address  = $packrow["csn_address"]; 
			$contact      = $packrow["csn_contactperson"];
			$str_contact  = (trim($contact)==""? "":"Contact Person: $contact");			
			$email        = $packrow["csn_email"]; 
			$str_email    = (trim($email)==""? "":"Email: $email");
			$EIN          = $packrow["EIN"]; 
			$str_EIN      = (trim($EIN)==""? "":"EIN: $EIN");
			$csn_tel      = $packrow["csn_tel"]; 
			$str_tel      = (trim($csn_tel)==""? "":"TEL#: $csn_tel");
			$csn_fax      = $packrow["csn_fax"]; 
			$str_fax      = (trim($csn_fax)==""? "":"FAX: $csn_fax");
			$lc_number    = (trim($packrow["lc_number"])==""? "N/A":$packrow["lc_number"]); 
			$lc_date      = (trim($packrow["lc_date"])==""? "N/A": $packrow["lc_date"]); 
			$od_FactoryID = $packrow["od_FactoryID"]; 
			$BICID    = $packrow["BICID"]; 
			$BuyerID  = $packrow["BuyerID"]; 

			$this->BICID=$BICID;
			
			// $html .= '<br pagebreak="true">';

			$sheet2 = $sheet->createSheet($i);

			$def_font_size = array(
			    'font'  => array(
			        'size'  => 10
			    ));

			$border_outline = array(
			  'borders' => array(
			    'outline' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
			);

			$border_top = array(
			  'borders' => array(
			    'top' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
			);

			$border_bottom = array(
			  'borders' => array(
			    'bottom' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
			);

			$border_allborders = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
			);

			$style_center = array(
			    'alignment' => array(
			        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			    )
			);

			$sheet2->getPageMargins()->setTop(0.6);
			$sheet2->getPageMargins()->setRight(0.1);
			$sheet2->getPageMargins()->setLeft(0.1);
			$sheet2->getPageMargins()->setBottom(0.6);

			$sheet2->getPageSetup()->setFitToWidth(1);
			$sheet2->getPageSetup()->setFitToHeight(1);

			$sheet2->getColumnDimension('A')->setWidth(3);
			$sheet2->getColumnDimension('B')->setWidth(1);
			$sheet2->getColumnDimension('C')->setWidth(3);
			$sheet2->getColumnDimension('D')->setWidth(12);
			$sheet2->getColumnDimension('E')->setWidth(25);
			$sheet2->getColumnDimension('F')->setWidth(12);
			$sheet2->getColumnDimension('G')->setWidth(4);
			$sheet2->getColumnDimension('H')->setWidth(4);
			$sheet2->getColumnDimension('I')->setWidth(4);
			$sheet2->getColumnDimension('J')->setWidth(4);
			$sheet2->getColumnDimension('K')->setWidth(4);
			$sheet2->getColumnDimension('L')->setWidth(4);
			$sheet2->getColumnDimension('M')->setWidth(5);
			$sheet2->getColumnDimension('N')->setWidth(5);
			$sheet2->getColumnDimension('O')->setWidth(5);
			$sheet2->getColumnDimension('P')->setWidth(7);
			$sheet2->getColumnDimension('Q')->setWidth(4);
			$sheet2->getColumnDimension('R')->setWidth(5);
			$sheet2->getColumnDimension('S')->setWidth(5);
			$sheet2->getColumnDimension('T')->setWidth(5);
			$sheet2->getColumnDimension('U')->setWidth(5);
			$sheet2->getColumnDimension('V')->setWidth(5);
			$sheet2->getColumnDimension('W')->setWidth(5);
			$sheet2->getColumnDimension('X')->setWidth(5);
			$sheet2->getColumnDimension('Y')->setWidth(5);
			$sheet2->getColumnDimension('Z')->setWidth(5);
			
			$sheet2->getDefaultStyle()->applyFromArray($def_font_size);

			$sheet2->mergeCells('A1:V1');
			$sheet2->getStyle("A1:V1")->getFont()->setSize(20);
			$sheet2->getCell('A1')->setValue($this->letterhead_name);
			$sheet2->mergeCells('A2:V2');
			$sheet2->getStyle("A2:V2")->getFont()->setSize(12);
			$sheet2->getCell('A2')->setValue($this->letterhead_name);
			$sheet2->mergeCells('A3:V3');
			$sheet2->getStyle("A3:V3")->getFont()->setSize(12);
			$sheet2->getCell('A3')->setValue('TEL : '.$this->letterhead_tel.' / FAX : '.$this->letterhead_fax);

			$sheet2->mergeCells('A6:V6');
			$sheet2->getStyle("A6:V6")->getFont()->setSize(16);
			$sheet2->getCell('A6')->setValue('PACKING LIST');
			$sheet2->getStyle('A6')->getFont()->setBold(true);
			$sheet2->getStyle('A6')->getFont()->setUnderline(true);

			$sheet2->getStyle('A1:V6')->applyFromArray($style_center);
			
			$row=9;

			$sheet2->mergeCells('A'.$row.':C'.$row);
			$sheet2->getCell('A'.$row)->setValue('DATE:');
			$sheet2->getCell('D'.$row)->setValue($invoice_date);

			$sheet2->mergeCells('M'.$row.':P'.$row);
			$sheet2->getCell('M'.$row)->setValue('INVOICE NO.:');
			$sheet2->getCell('Q'.$row)->setValue($invoice_no);

			$row+=1;

			$sheet2->mergeCells('M'.$row.':P'.$row);
			$sheet2->getCell('M'.$row)->setValue('PURCHASE ORDER NO.:');
			$sheet2->getCell('Q'.$row)->setValue($BuyerPO." ");

			$row+=1;

			$sheet2->mergeCells('A'.$row.':C'.$row);
			$sheet2->getCell('A'.$row)->setValue('TO: ');
			$sheet2->getCell('D'.$row)->setValue($csn_name);

			$row_to=$row;
			for($i=1;$i<=5;$i++){
				$value="";
				if($i==1){
					$value=$str_EIN;
				}else if($i==2){
					$value=$str_email;
				}else if($i==3){
					$value=$str_contact;
				}else if($i==4){
					$value=$str_tel;
				}else if($i==5){
					$value=$str_fax;
				}

				if($value!==""){
					$sheet2->getCell('D'.$row_to)->setValue($value);
				}

				$row_to++;
			}

			$sheet2->mergeCells('M'.$row.':P'.$row);
			$sheet2->getCell('M'.$row)->setValue('VENDOR STYLE NO.:');
			$sheet2->getCell('Q'.$row)->setValue($styleNo);
			$sheet2->getCell('M'.($row+1))->setValue('LC NO.:');
			$sheet2->getCell('Q'.($row+1))->setValue($lc_number);
			$sheet2->getCell('M'.($row+2))->setValue('DATE:');
			$sheet2->getCell('Q'.($row+2))->setValue($lc_date);
			$sheet2->getCell('M'.($row+3))->setValue('IA#:');
			$sheet2->getCell('Q'.($row+3))->setValue($grp_ia);

			$row=$row_to;
			if($row_to<($row+3)){
				$row=$row+4;
			}


			$row+=1;

			$arrsize = [];
			$arrpick_totalsizeqty = []; // 
			$arrpick_totalcsq = []; // total color size qty in this packing list
			
			$size_thead = "";
			$size_thead_summary = "";
			$sizesql = $this->handle_shipment->getSizeNameColumnFromOrder($orderno, 1);
			$size_colspan = 0;//$sizesql->rowCount();
			while($sizerow = $sizesql->fetch(PDO::FETCH_ASSOC)){
				$size_name = $sizerow["SizeName"];
				
				$sqlscsq  = "SELECT sum(scsq.qty) as qty 
							FROM tblship_colorsizeqty scsq 
							WHERE scsq.shipmentpriceID='$spID' and scsq.size_name='$size_name' 
							AND scsq.statusID=1 ";
				$stmt_scsq = $this->conn->prepare($sqlscsq);
				$stmt_scsq->execute();
				$row_scsq = $stmt_scsq->fetch(PDO::FETCH_ASSOC);
					$this_qty = $row_scsq["qty"];
				
				if($this_qty>0){
					$arrsize[] = $size_name;				
					$arrpick_totalsizeqty[$size_name] = 0;

					$size_colspan++;
				}
			}//--- End While Size Range ---//
			
			// $emptyrow = "<td></td><td></td><td></td><td></td>";
			
			// $css_size_wd = (count($arrsize)==1? 10: 3);
			// foreach ($arrsize as $size_name) {
			// 	$size_thead .= '<td class="border_btm" align="center" style="width:'.$css_size_wd.'%; ">'.$size_name.'</td>';
			// 	$size_thead_summary .= '<td class="all_border" align="center" style="width: 5%; ">'.$size_name.'</td>';
			// 	$emptyrow .= '<td style="width: 4%; "></td>';
			// }
			// $emptyrow .= "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
			$thead_sizewidth = (count($arrsize)==1? 10: $size_colspan * 3);
			//29
			//34 = 63
			$balance_width = 100 - (63 + $thead_sizewidth);
			$each_extra = floor(($balance_width / 11) * 10)/10;
			
			
			$ctn_width = 6 + $each_extra;
			$upc_width = 9 + $each_extra;
			$col_width = 8 + $each_extra;
			$sub_width = 4 + $each_extra;
			$oth_width = 5 + $each_extra;

			$sheet2->mergeCells('A'.$row.':C'.$row);
			$sheet2->mergeCells('A'.($row+1).':C'.($row+1));
			$sheet2->getCell('A'.$row)->setValue('CARTON');
			$sheet2->getCell('A'.($row+1))->setValue('NO');
			$sheet2->getCell('D'.$row)->setValue('NG ITEMS');
			$sheet2->getCell('E'.$row)->setValue('UPC NO.');
			$sheet2->getCell('F'.$row)->setValue('COLOR');

			$col_num=6; //G
			$col_num_plus_size=$col_num+(sizeof($arrsize)-1);
			$sheet2->mergeCells($arrCol[$col_num].''.$row.':'.$arrCol[$col_num_plus_size].''.$row);
			$sheet2->getCell($arrCol[$col_num].''.$row)->setValue('SIZE QUANTITY');
			$sheet2->getStyle($arrCol[$col_num].''.$row.':'.$arrCol[$col_num_plus_size].''.$row)->applyFromArray($style_center);

			for($i=0;$i<sizeof($arrsize);$i++){
				$sheet2->getCell($arrCol[($col_num+$i)].''.($row+1))->setValue($arrsize[$i]);
			}

			$col=$col_num_plus_size+1;

			$col_subtotal_per_ctn  = $arrCol[$col];
			$col_subtotal_per_ctn2 = $arrCol[($col+1)];
			$col_no_of_ctn         = $arrCol[($col+2)];
			$col_no_of_ctn2        = $arrCol[($col+3)];
			$col_cbm               = $arrCol[($col+4)];
			$col_total_qty         = $arrCol[($col+5)];
			$col_total_qty2        = $arrCol[($col+6)];
			$col_gw                = $arrCol[($col+7)];
			// $col_gw2=$arrCol[($col+7)];
			$col_nw                = $arrCol[($col+8)];
			// $col_nw2=$arrCol[($col+9)];
			$col_tgw               = $arrCol[($col+9)];
			// $col_tgw2=$arrCol[($col+11)];
			$col_tnw               = $arrCol[($col+10)];
			// $col_tnw2=$arrCol[($col+13)];
			$col_nnw               = $arrCol[($col+11)];
			// $col_nnw2=$arrCol[($col+15)];
			$col_tnnw              = $arrCol[($col+12)];
			// $col_tnnw2=$arrCol[($col+17)];

			$sheet2->mergeCells($col_subtotal_per_ctn.$row.':'.$col_subtotal_per_ctn2.$row);
			$sheet2->mergeCells($col_subtotal_per_ctn.($row+1).':'.$col_subtotal_per_ctn2.($row+1));
			$sheet2->getCell($col_subtotal_per_ctn.$row)->setValue('SUB TOTAL');
			$sheet2->getCell($col_subtotal_per_ctn.($row+1))->setValue('PER CTN');

			$sheet2->mergeCells($col_no_of_ctn.$row.':'.$col_no_of_ctn2.$row);
			$sheet2->getCell($col_no_of_ctn.$row)->setValue('NO OF CTN');
			
			
			$sheet2->getCell($col_cbm.''.$row)->setValue('T.CBM');

			$sheet2->mergeCells($col_total_qty.$row.':'.$col_total_qty2.$row);
			$sheet2->mergeCells($col_total_qty.($row+1).':'.$col_total_qty2.($row+1));
			$sheet2->getCell($col_total_qty.''.$row)->setValue('TOTAL QTY');
			$sheet2->getCell($col_total_qty.''.($row+1))->setValue('(PCS)');

			// $sheet2->mergeCells($col_gw.$row.':'.$col_gw2.$row);
			$sheet2->getCell($col_gw.''.$row)->setValue('GW');
			$sheet2->getCell($col_gw.''.($row+1))->setValue('(KGS)');

			// $sheet2->mergeCells($col_nw.$row.':'.$col_nw2.$row);
			$sheet2->getCell($col_nw.$row)->setValue('NW');
			$sheet2->getCell($col_nw.''.($row+1))->setValue('(KGS)');

			// $sheet2->mergeCells($col_tgw.$row.':'.$col_tgw2.$row);
			$sheet2->getCell($col_tgw.$row)->setValue('T.GW');
			$sheet2->getCell($col_tgw.''.($row+1))->setValue('(KGS)');

			// $sheet2->mergeCells($col_tnw.$row.':'.$col_tnw2.$row);
			$sheet2->getCell($col_tnw.$row)->setValue('T.NW');
			$sheet2->getCell($col_tnw.''.($row+1))->setValue('(KGS)');

			// $sheet2->mergeCells($col_nnw.$row.':'.$col_nnw2.$row);
			// $sheet2->getCell($col_nnw.$row)->setValue('NNW');

			// $sheet2->mergeCells($col_tnnw.$row.':'.$col_tnnw2.$row);
			// $sheet2->getCell($col_tnnw.$row)->setValue('T.NNW');

			$sheet2->getStyle('A'.$row.':'.$col_tnw.$row)->applyFromArray($border_top);
			$sheet2->getStyle('A'.($row+1).':'.$col_tnw.($row+1))->applyFromArray($border_bottom);
			$sheet2->getStyle('A'.$row.':'.$col_tnw.($row+1))->getFont()->setSize(8);
			$sheet2->getStyle('A'.$row.':'.$col_tnw.($row+1))->getFont()->setBold(true);

			$row+=2;

						
			$pack_ctn_qty = 0; // qty of ctn used in one po
			$pack_netweight = 0;
			$pack_grossweight = 0;
			$pack_netnetweight = 0;
			$pack_totalpcs = 0;
			$totalpackqty = 0;
			$totalcbm = 0;

			$arrtotalsizeqty = [];
			$query_filter = " AND cpt.shiped='1'";
			//list($arr_row, $arr_all_size, $ctn_qty) = $handle_class->getAllPackingInfoByBuyerPO($spID, $od_FactoryID);
			//$arr_all = $this->handle_shipment->getAllPackingInfoByBuyerPO($spID, $od_FactoryID, $query_filter);
			//$arr_all = $this->handle_shipmeent->getAllCuttingPickListByBuyerPO($spID);
			//$arr_row = $arr_all["arr_row"];
			if($BuyerID=="B13" && $this->isBuyerPayment==1 && glb_profile=="iapparelintl"){//for joe fresh only
				$arr_all = $this->getBuyerInvoiceJFPackingListDataFromCartonInv($spID, $invID, $tblctn, $tblbuyer_inv_detail);
			}
			else{
				$arr_all = $this->getBuyerInvoicePackingListDataFromCartonInv($spID, $invID, $tblctn, $tblbuyer_inv_detail);
			}
			
			$arr_row = $arr_all["arr_list"];
			$arr_ctn_measurement = array();
			for($arr=0;$arr<count($arr_row);$arr++){
				$ctn_range     = $arr_row[$arr]["ctn_range"];
				$count_ctn     = $arr_row[$arr]["count_ctn"];
				$SKU           = $arr_row[$arr]["SKU"];
				$prepack_name  = $arr_row[$arr]["prepack_name"];
				$this_ctn_qty  = $arr_row[$arr]["this_ctn_qty"];
				$total_qty     = $arr_row[$arr]["total_qty"];
				$ext_length    = round($arr_row[$arr]["ext_length"], 1);
				$ext_width     = round($arr_row[$arr]["ext_width"], 1);
				$ext_height    = round($arr_row[$arr]["ext_height"], 1);
				$this_nnw      = $arr_row[$arr]["this_nnw"];
				$one_nnw       = round($this_nnw / $count_ctn, 3);
				$this_nw       = $arr_row[$arr]["this_nw"];
				$one_nw        = round($this_nw / $count_ctn, 2);//2, modified to 3 20220302 request by MAO
				$this_gw       = $arr_row[$arr]["this_gw"];
				$one_gw        = round($this_gw / $count_ctn, 2);//2
				
				$this_nw       = round($one_nw * $count_ctn, 2);//2
				$this_gw       = round($one_gw * $count_ctn, 2);//2
				//$this_cbm      = $arr_row[$arr]["cbm_total"];
				$this_cbm      = ((($ext_length/100) * ($ext_width/100) * ($ext_height/100)) * $count_ctn);
				$this_cbm      = round($this_cbm, 2);
				
				$arr_grp_color = $arr_row[$arr]["arr_grp_color"];
				$count_grp     = count($arr_row[$arr]["arr_grp_color"]);
				$arr_size_info = $arr_row[$arr]["arr_size_info"];
				
				$ctn_measurement = "$ext_length x $ext_width x $ext_height (cm)";
				
				if(!in_array("$ctn_measurement", $arr_ctn_measurement)){
					$arr_ctn_measurement[] = $ctn_measurement;
				}


				$sheet2->mergeCells('A'.$row.':C'.$row);
				$sheet2->getCell('A'.$row)->setValue($ctn_range);
				$sheet2->getCell('D'.$row)->setValue($SKU);
				$sheet2->getCell('E'.$row)->setValue($prepack_name);


				for($g=0;$g<count($arr_grp_color);$g++){
					list($group_number, $sku) = explode("**%%^^",$arr_grp_color[$g]);
					
					// $sqlSGC = "SELECT group_concat(c.colorName,' ' separator '<br/>') as grp_color 
								// FROM tblship_group_color sgc 
								// INNER JOIN tblcolor c ON c.ID = sgc.colorID
								// INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
								// WHERE sgc.shipmentpriceID='$spID' AND sgc.group_number='$group_number' AND sgc.statusID=1";
					// $stmt_sgc = $this->conn->query($sqlSGC);
					// $row_sgc  = $stmt_sgc->fetch(PDO::FETCH_ASSOC);
						// $grp_color = $row_sgc["grp_color"];
					$arr_color = $this->getPOPrice($spID, $group_number);
					$grp_color = $arr_color["colorOnly"];
					
					// if($g>0){
					// 	$html .= "<tr>";
					// }
						
					$sheet2->getCell('F'.$row)->setValue($grp_color);
					$col_num=6; //G
					// $col_num_plus_size=$col_num+(sizeof($arrsize)-1);
					$count_size=0;
					foreach ($arrsize as $size) {
						$this_qty = (isset($arr_size_info["$group_number"]["$size"])? $arr_size_info["$group_number"]["$size"]:0);
						$this_qty = ($this_qty==""? 0 : $this_qty);
						
						// echo "$this_qty << $group_number / $size << <br/>";
						
						$sheet2->getCell($arrCol[($col_num+$count_size)].$row)->setValue($this_qty);

						// $html .= '<td class="center-align" style="width: '.$css_size_wd.'%; ">'.$this_qty.'</td>';
						
						if(isset($arrpick_totalcsq["$group_number^=$grp_color"][$size])){
							$arrpick_totalcsq["$group_number^=$grp_color"][$size] += $this_qty * $count_ctn;
						}
						else{
							$arrpick_totalcsq["$group_number^=$grp_color"][$size] = $this_qty * $count_ctn;
						}

						$count_size++;
					}//--- End Foreach size qty ---//
						
					if($g>0){
						// $html .= "</tr>";
					}
					else{
						$str_onegw = number_format($one_gw, 2);//2, modified to 3 20220302 request by MAO
						$str_onenw = number_format($one_nw, 2);
						$str_gw    = number_format($this_gw, 2);
						$str_nw    = number_format($this_nw, 2);

						// $col=$col_num_plus_size+1;

						// $col_subtotal_per_ctn=$arrCol[$col];
						// $col_subtotal_per_ctn2=$arrCol[($col+1)];
						// $col_no_of_ctn=$arrCol[($col+2)];
						// $col_no_of_ctn2=$arrCol[($col+3)];
						// $col_total_qty=$arrCol[($col+4)];
						// $col_total_qty2=$arrCol[($col+5)];
						// $col_gw=$arrCol[($col+6)];
						// $col_nw=$arrCol[($col+7)];
						// $col_tgw=$arrCol[($col+8)];
						// $col_tnw=$arrCol[($col+9)];
						// $col_nnw=$arrCol[($col+10)];
						// $col_tnnw=$arrCol[($col+11)];

						$sheet2->mergeCells($col_subtotal_per_ctn.$row.':'.$col_subtotal_per_ctn2.$row);
						$sheet2->getCell($col_subtotal_per_ctn.$row)->setValue($this_ctn_qty);

						$sheet2->mergeCells($col_no_of_ctn.$row.':'.$col_no_of_ctn2.$row);
						$sheet2->getCell($col_no_of_ctn.$row)->setValue($count_ctn);
						
						$sheet2->getCell($col_cbm.''.$row)->setValue($this_cbm);
						
						$sheet2->mergeCells($col_total_qty.$row.':'.$col_total_qty2.$row);
						$sheet2->getCell($col_total_qty.''.$row)->setValue($total_qty);

						$sheet2->getCell($col_gw.''.$row)->setValue($str_onegw);

						$sheet2->getCell($col_nw.$row)->setValue($str_onenw);

						$sheet2->getCell($col_tgw.$row)->setValue($str_gw);

						$sheet2->getCell($col_tnw.$row)->setValue($str_nw);
						
						// echo "$str_onenw / $str_gw / $str_nw <<< <br/>";
						
					}
				}//--- End For Color ---//

				$pack_ctn_qty += $count_ctn;
				$totalpackqty += $total_qty;
				$pack_netweight += $this_nw;
				$pack_grossweight += $this_gw;
				$pack_netnetweight += $this_nnw;
				$totalcbm += $this_cbm;

				$row++;
			}//--- End Outer For Row ---//


			$sheet2->getCell('E'.$row)->setValue("TOTAL");
			$sheet2->getStyle('E'.$row)->getFont()->setBold(true);
			$sheet2->getStyle('E'.$row)->applyFromArray($border_top);
			$sheet2->getStyle('E'.$row)->applyFromArray($border_bottom);
			
			$sheet2->getCell($col_no_of_ctn.$row)->setValue($pack_ctn_qty);
			$sheet2->mergeCells($col_no_of_ctn.$row.':'.$col_no_of_ctn2.$row);
			$sheet2->getStyle($col_no_of_ctn.$row.':'.$col_no_of_ctn2.$row)->getFont()->setBold(true);
			$sheet2->getStyle($col_no_of_ctn.$row.':'.$col_no_of_ctn2.$row)->applyFromArray($border_top);
			$sheet2->getStyle($col_no_of_ctn.$row.':'.$col_no_of_ctn2.$row)->applyFromArray($border_bottom);
			
			$sheet2->getCell($col_cbm.$row)->setValue($totalcbm);
			$sheet2->getStyle($col_cbm.$row.':'.$col_cbm.$row)->getFont()->setBold(true);
			$sheet2->getStyle($col_cbm.$row.':'.$col_cbm.$row)->applyFromArray($border_top);
			$sheet2->getStyle($col_cbm.$row.':'.$col_cbm.$row)->applyFromArray($border_bottom);

			$sheet2->getCell($col_total_qty.$row)->setValue($totalpackqty);
			$sheet2->mergeCells($col_total_qty.$row.':'.$col_total_qty2.$row);
			$sheet2->getStyle($col_total_qty.$row.':'.$col_total_qty2.$row)->getFont()->setBold(true);
			$sheet2->getStyle($col_total_qty.$row.':'.$col_total_qty2.$row)->applyFromArray($border_top);
			$sheet2->getStyle($col_total_qty.$row.':'.$col_total_qty2.$row)->applyFromArray($border_bottom);

			$sheet2->getCell($col_tgw.$row)->setValue($pack_grossweight);
			$sheet2->getStyle($col_tgw.$row)->getFont()->setBold(true);
			$sheet2->getStyle($col_tgw.$row)->applyFromArray($border_top);
			$sheet2->getStyle($col_tgw.$row)->applyFromArray($border_bottom);

			$sheet2->getCell($col_tnw.$row)->setValue($pack_netweight);
			$sheet2->getStyle($col_tnw.$row)->getFont()->setBold(true);
			$sheet2->getStyle($col_tnw.$row)->applyFromArray($border_top);
			$sheet2->getStyle($col_tnw.$row)->applyFromArray($border_bottom);

			$row+=2;

			$sheet2->getCell("F".$row)->setValue("COLOR & SIZE BREAKDOWN");
			$sheet2->getStyle("F".$row)->getFont()->setBold(true);
			$sheet2->getStyle("F".$row)->getFont()->setUnderline(true);

			$row+=1;
			$first_row=$row;
			$sheet2->getCell("F".$row)->setValue("COLOR");

			$col_num=6;
			for($i=0;$i<sizeof($arrsize);$i++){
				$sheet2->getCell($arrCol[($col_num+$i)].''.$row)->setValue($arrsize[$i]);
			}

			$col_total=$col_num_plus_size+2;
			$sheet2->mergeCells($arrCol[$col_total].$row.':'.$arrCol[$col_total+1].$row);
			$sheet2->getCell($arrCol[$col_total].$row)->setValue("TOTAL");

			$sheet2->getStyle("F".$row.":".$col_tnw.$row)->getFont()->setBold(true);

			$row++;
			foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
				$color = explode("^=", $strcolor);
				$ColorName = $color[1];
				$str_csq = "";

				$totalcolorqty = array_sum($sizelist);

				$sheet2->getCell("F".$row)->setValue($ColorName);

				// $html .= '<tr>
				// 			<td class="all_border" style="width: 20%; ">'.$ColorName.'</td>
				// 			'.$str_csq.'
				// 			<td class="all_border" align="center">'.$totalcolorqty.'</td>
				// 			</tr>';
				$count_size=0;
				foreach ($arrsize as $size) {
					$sizeqty = $sizelist[$size];

					$sheet2->getCell($arrCol[($col_num+$count_size)].''.$row)->setValue($sizeqty);

					// $str_csq .= '<td class="all_border" align="center" style="width: 5%; ">'.$sizeqty.'</td>';
					$count_size++;
				}

				$sheet2->mergeCells($arrCol[$col_total].$row.':'.$arrCol[$col_total+1].$row);
				$sheet2->getCell($arrCol[$col_total].$row)->setValue($totalcolorqty);
				
				$row++;
			}//--- End Foreach Color Name ---// 

			$sheet2->getCell("F".$row)->setValue("TOTAL");
			// $html .= '<tr><td class="all_border"><b>TOTAL </b></td>';
				
			$totalcolorsizeqty = 0;
			$count_size=0;
			foreach ($arrsize as $size) {
				$totalsizeqty = 0;
				foreach ($arrpick_totalcsq as $strcolor => $sizelist) {
					$totalsizeqty += $sizelist[$size];
				}
				$totalcolorsizeqty += $totalsizeqty;
				// $html .= '<td class="all_border" align="center">'.$totalsizeqty.'</td>';

				$sheet2->getCell($arrCol[($col_num+$count_size)].''.$row)->setValue($totalsizeqty);

				$count_size++;
			}


			$sheet2->mergeCells($arrCol[$col_total].$row.':'.$arrCol[$col_total+1].$row);
			$sheet2->getCell($arrCol[$col_total].$row)->setValue($totalcolorsizeqty);

			$sheet2->getStyle("F".$row.":".$arrCol[$col_total].$row)->getFont()->setBold(true);
			$sheet2->getStyle("F".$first_row.":".$arrCol[$col_total].$row)->getFont()->setSize(8);
			$sheet2->getStyle("F".$first_row.":".$arrCol[$col_total+1].$row)->applyFromArray($border_allborders);

			$row+=2;

			$str_ctn_measurement = implode(" / ", $arr_ctn_measurement);

			$sheet2->mergeCells("A".$row.':'."D".$row);
			$sheet2->getCell("A".$row)->setValue("CTN MEASUREMENT");
			$sheet2->mergeCells("A".($row+1).':'."D".($row+1));
			$sheet2->getCell("A".($row+1))->setValue("(MASTER CARTON DIMENSION)");
			$sheet2->getCell("E".$row)->setValue($str_ctn_measurement);
			
			$str_cbm    = number_format($totalcbm, 2);
			$sheet2->mergeCells("A".($row+2).':'."D".($row+2));
			$sheet2->getCell("A".($row+2))->setValue("TTL CBM");
			$sheet2->getCell("E".($row+2))->setValue($str_cbm);

			$sheet2->mergeCells("A".($row+3).':'."D".($row+3));
			$sheet2->getCell("A".($row+3))->setValue("TTL GW");
			$sheet2->getCell("E".($row+3))->setValue($pack_grossweight);

			$sheet2->mergeCells("A".($row+4).':'."D".($row+4));
			$sheet2->getCell("A".($row+4))->setValue("TTL NW");
			$sheet2->getCell("E".($row+4))->setValue($pack_netweight);

			$sheet2->mergeCells("A".($row+5).':'."D".($row+5));
			$sheet2->getCell("A".($row+5))->setValue("TTL NNW");
			$sheet2->getCell("E".($row+5))->setValue($pack_netnetweight);
			
			$sheet2->getStyle("A".$row.":E".($row+5))->getFont()->setSize(9);
					
			// echo "$BuyerPO / $spID << <br/>";
			// $BuyerPO = "123";
			$sheet2->setTitle($BuyerPO);
		}//--- End While Buyer Invoice Detail ---//

		//end detail sheet
	}
	
	function updateShippingAdvise($spID, $group_number, $updatedDate, $acctid=0){
		
		$grand_qty = 0;
		
		$sqlsgc = "SELECT * 
							FROM tblship_group_color sgc 
							WHERE sgc.group_number='$group_number' AND sgc.shipmentpriceID='$spID'
							AND sgc.statusID='1'";
		$stmtsgc = $this->conn->prepare($sqlsgc);
		$stmtsgc->execute();
		
		$count_set = $stmtsgc->rowCount(); 
		
		$sql = "SELECT cih.BICID, cih.invID, cih.CIHID, cih.total_ctn, cid.group_number, cid.size_name, cid.qty,
						bipd.fob_price as unit_price
				FROM `tblcarton_inv_payment_head` cih 
				INNER JOIN tblcarton_inv_payment_detail cid ON cid.CIHID = cih.CIHID
				INNER JOIN tblbuyer_invoice_payment_category bic ON bic.BICID = cih.BICID
				INNER JOIN tblbuyer_invoice_payment bip ON bip.ID = bic.invID
				INNER JOIN tblbuyer_invoice_payment_detail bipd ON bipd.invID = bic.invID 
												AND bipd.BICID = bic.BICID
                                                AND bipd.shipmentpriceID = cih.shipmentpriceID
				WHERE cih.shipmentpriceID='$spID' AND cid.group_number='$group_number'
				AND cih.del=0 AND cid.del=0 AND bic.del=0 AND bip.statusID!=6 AND bipd.del=0
				group by cid.CIDID";
		// echo "<pre>$sql</pre>";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$sqlsgc = "SELECT cih.BICID 
						FROM `tblcarton_inv_payment_head` cih 
						INNER JOIN tblcarton_inv_payment_detail cid ON cid.CIHID = cih.CIHID
						INNER JOIN tblbuyer_invoice_payment_category bic ON bic.BICID = cih.BICID
						INNER JOIN tblbuyer_invoice_payment bip ON bip.ID = bic.invID
						INNER JOIN tblbuyer_invoice_payment_detail bipd ON bipd.invID = bic.invID 
												AND bipd.BICID = bic.BICID
                                                AND bipd.shipmentpriceID = cih.shipmentpriceID
						WHERE cih.shipmentpriceID='$spID' AND cid.group_number='$group_number' AND cid.size_name='$size_name' 
						AND cih.del=0 AND cid.del=0 AND bic.del=0 AND bip.statusID!=6 AND bipd.del=0 AND cih.total_ctn>0 AND cid.qty>0
						AND cih.invID='$invID'
						group by cih.BICID";
			$stmtsgc = $this->conn->prepare($sqlsgc);
			$stmtsgc->execute();
			
			
			$count_sgc = $stmtsgc->rowCount();
			
			if($count_set>1){
				$total_qty = ($total_ctn * $qty) / $count_sgc;
			}
			else{
				$total_qty = ($total_ctn * $qty);
			}
			$grand_qty += $total_qty;
			if($spID==45766){
			// echo "------>>>>[$CIHID] $total_qty =  $total_ctn x $qty / [BICID: $BICID / $invID] $count_sgc [$grand_qty] / $group_number / $size_name</br> ";
			}
			
		}//-- End get size level qty --//
		
			$sqlShipColor = "SELECT sgc.colorID, sgc.garmentID, c.colorName as color, 
								(SELECT scsq.price
								 FROM tblship_colorsizeqty scsq 
								 WHERE scsq.shipmentpriceID = sgc.shipmentpriceID 
								 AND scsq.garmentID = sgc.garmentID AND scsq.colorID = sgc.colorID AND scsq.statusID=1 AND scsq.price>0 limit 1) as unit_price
							FROM tblship_group_color sgc 
							INNER JOIN tblcolor c ON c.ID = sgc.colorID
							WHERE sgc.shipmentpriceID='$spID' 
							AND sgc.group_number='$group_number' AND sgc.statusID=1";
			$stmtsgc = $this->conn->prepare($sqlShipColor);
			$stmtsgc->execute();
			while($rowsgc = $stmtsgc->fetch(PDO::FETCH_ASSOC)){
				extract($rowsgc);
				
				
				$sqlShipAdvise = "SELECT * FROM tblshippingadviseqty 
									WHERE tblshipmentpriceID='$spID' 
									AND garmentID='$garmentID' AND colorID='$colorID'";
				$stmt_advise = $this->conn->prepare($sqlShipAdvise);
				$stmt_advise->execute();
				$count_advise = $stmt_advise->rowCount();
				
				if($spID==45766){
				// echo ">>>[$spID] GRP:$group_number / $color [gmt: $garmentID / col:$colorID] $grand_qty / up: $unit_price/  $count_advise <br/>";
				}
				
				if($count_advise==0 && $grand_qty>0){//--- Insert Advise ---//
					$sqlInsert = "INSERT INTO tblshippingadviseqty
									(tblshipmentpriceID, colorID, garmentID, fobprice, shippedQty, updateddate, from_cronjob)
									  VALUES
								   ('$spID', '$colorID', '$garmentID', '$unit_price', '$grand_qty', '$updatedDate','10')";
					$detailsql = $this->conn->prepare($sqlInsert);
					// $detail_data = array("tblshipmentpriceID" => $spID, 
													// "colorID" => $colorID, 
													// "garmentID" => $garmentID, 
													// "fobprice" => $unit_price, 
													// "shippedQty" => $grand_qty,
													// "updateddate" => $updateddate );
					$detailsql->execute();
										
				}
				else if($grand_qty>0){ //--- Update Advise ---//
					$sqlUpdate = "UPDATE tblshippingadviseqty 
									SET fobprice='$unit_price', shippedQty='$grand_qty', updateddate='$updatedDate', from_cronjob='10'
									WHERE tblshipmentpriceID='$spID' 
									AND garmentID='$garmentID' AND colorID='$colorID'";
					$detailsql = $this->conn->prepare($sqlUpdate);
					$detailsql->execute();
				}
				
			}//--- End for color garment ---//
		
	}
	
	function test($spID, $group_number, $updateddate){
		
		//until shipmentpriceID, group_number, size level then divide how many option A,B,C
	
		$sql = "SELECT bipd.BICID, bipd.group_number, sum(bipd.qty) as color_qty, bipd.fob_price as unit_price
				FROM `tblbuyer_invoice_payment_detail` bipd 
				INNER JOIN tblbuyer_invoice_payment_category bipc ON bipc.BICID = bipd.BICID
				INNER JOIN tblbuyer_invoice_payment bip ON bip.ID = bipd.invID
				WHERE bipd.del=0 AND bipc.del=0 AND bipd.shipmentpriceID='$spID' AND bipd.group_number='$group_number'
				AND bipd.qty>0 AND bip.statusID IN (8,11)
				group by bipd.shipmentpriceID, bipd.group_number, bipd.fob_price";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$sqlsgc = "SELECT * 
							FROM tblship_group_color sgc 
							WHERE sgc.group_number='$group_number' AND sgc.shipmentpriceID='$spID'
							AND sgc.statusID='1'";
			$stmtsgc = $this->conn->prepare($sqlsgc);
			$stmtsgc->execute();
			
			$count_sgc = $stmtsgc->rowCount();
			$color_qty = $color_qty / 1;
			
			
			$sqlShipColor = "SELECT sgc.colorID, sgc.garmentID
								FROM tblship_group_color sgc 
								WHERE sgc.shipmentpriceID='$spID' 
								AND sgc.group_number='$group_number' AND sgc.statusID=1";
			$stmtsgc = $this->conn->prepare($sqlShipColor);
			$stmtsgc->execute();
			while($rowsgc = $stmtsgc->fetch(PDO::FETCH_ASSOC)){
				extract($rowsgc);
				
				
				$sqlShipAdvise = "SELECT * FROM tblshippingadviseqty 
									WHERE tblshipmentpriceID='$spID' 
									AND garmentID='$garmentID' AND colorID='$colorID'";
				$stmt_advise = $this->conn->prepare($sqlShipAdvise);
				$stmt_advise->execute();
				$count_advise = $stmt_advise->rowCount();
				
				echo ">>>GRP:$group_number [$garmentID / $colorID] $color_qty / $count_advise <br/>";
				
				if($count_advise==0){//--- Insert Advise ---//
					$sqlInsert = "INSERT INTO tblshippingadviseqty
									(tblshipmentpriceID, colorID, garmentID, fobprice, shippedQty, updateddate)
									  VALUES
								   (:tblshipmentpriceID, :colorID, :garmentID, :fobprice, :shippedQty, :updateddate)";
					$detailsql = $this->conn->prepare($sqlInsert);
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
					$detailsql = $this->conn->prepare($sqlUpdate);
					$detailsql->execute();
				}
				
			}//--- End for color garment ---//
			
		}//--- End buyer payment detail ---//
		
	}
}
?>