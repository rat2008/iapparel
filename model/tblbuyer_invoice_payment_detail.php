<?php 
class tblbuyer_invoice_payment_detail{
	private $conn;
	private $table_name  = "tblbuyer_invoice_payment_detail";
	private $handle_misc = "";
	private $handle_shipment = "";
	private $handle_lc = "";
	private $model_tblcarton_inv_payment_head = "";
	private $model_tblbuyer_invoice_payment_category = "";
	private $model_tblbuyer_invoice_charge_option = "";
	
	// object properties
	public $ID;
	public $invID;
	public $BICID = 0;
	public $LCIID = 0;
	public $shipmentpriceID;
	public $remarks = NULL;
	public $quotaID = 0;
	public $ht_code = NULL;
	public $shipping_marking = NULL;
	public $class_description = NULL;
	public $group_number = 0;
	public $fob_price = 0;
	public $qty = 0;
	public $total_amount = 0;
	public $other_charge = NULL;
	public $charge_percentage = 0;
	public $chargeID_deduct = 0;
	public $charge_percentage_credit = 0;
	public $chargeID_credit = 0;
	public $del = 0;
	public $delBy = null;
	public $delDate = null;
	
	// constructor with $db as database connection
    public function __construct($conn){
        $this->conn = $conn;
    }
	
	public function setMisc($handle_misc){
		$this->handle_misc = $handle_misc;
		$this->handle_misc->setConnection($this->conn);
	}
	public function setShipment($handle_shipment){
		$this->handle_shipment = $handle_shipment;
		$this->handle_shipment->setConnection($this->conn);
	}
	public function setHandleLC($handle_lc){
		$this->handle_lc = $handle_lc;
	}
	
	public function setModelTblcarton_inv_payment_head($model_tblcarton_inv_payment_head){
		$this->model_tblcarton_inv_payment_head = $model_tblcarton_inv_payment_head;
	}
	public function setModelTblbuyer_invoice_payment_category($model_tblbuyer_invoice_payment_category){
		$this->model_tblbuyer_invoice_payment_category = $model_tblbuyer_invoice_payment_category;
	}
	public function setModelTblbuyer_inv_charge_option($model_tblbuyer_invoice_charge_option){
		$this->model_tblbuyer_invoice_charge_option = $model_tblbuyer_invoice_charge_option;
	}
	
	function getTotalAmountOfInvoiceNotIncludeOtherCharge(){
		$sql = "SELECT sum(total_amount) as total_amount 
				FROM ".$this->table_name." 
				WHERE invID=:invID AND group_number>0 AND del=0";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$total_amount = $row["total_amount"];
		
		return $total_amount;
	}
	
	function create(){
		// query to insert record
		$query = "INSERT INTO
					" . $this->table_name . "
					SET
					ID=:ID, BICID=:BICID, invID=:invID, shipmentpriceID=:shipmentpriceID, quotaID=:quotaID, ht_code=:ht_code,
					shipping_marking=:shipping_marking, group_number=:group_number, fob_price=:fob_price, remarks=:remarks,
					qty=:qty, total_amount=:total_amount, other_charge=:other_charge, charge_percentage=:charge_percentage,
					chargeID_deduct=:chargeID_deduct, charge_percentage_credit=:charge_percentage_credit, chargeID_credit=:chargeID_credit, class_description=:class_description,
					del=:del";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		$this->ID = $this->handle_misc->funcMaxID($this->table_name, "ID");
		
		$this->ht_code           = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->ht_code);
		$this->shipping_marking  = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->shipping_marking);
		$this->class_description = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->class_description);
		$this->other_charge      = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->other_charge);
		$this->remarks           = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->remarks);
		
		// bind values
		$stmt->bindParam(":ID", $this->ID);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->bindParam(":BICID", $this->BICID);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":remarks", $this->remarks);
		$stmt->bindParam(":quotaID", $this->quotaID);
		$stmt->bindParam(":ht_code", $this->ht_code);
		$stmt->bindParam(":shipping_marking", $this->shipping_marking);
		$stmt->bindParam(":class_description", $this->class_description);
		$stmt->bindParam(":group_number", $this->group_number);
		$stmt->bindParam(":fob_price", $this->fob_price);
		$stmt->bindParam(":qty", $this->qty);
		$stmt->bindParam(":total_amount", $this->total_amount);
		$stmt->bindParam(":other_charge", $this->other_charge);
		$stmt->bindParam(":charge_percentage", $this->charge_percentage);
		$stmt->bindParam(":chargeID_deduct", $this->chargeID_deduct);
		$stmt->bindParam(":charge_percentage_credit", $this->charge_percentage_credit);
		$stmt->bindParam(":chargeID_credit", $this->chargeID_credit);
		$stmt->bindParam(":del", $this->del);
		
		// execute query
		if($stmt->execute()){
			return $this->ID;
		}
		
		return false;
	}
	
	function update(){
		// query to update record
		$query = "UPDATE
					" . $this->table_name . "
					SET
					invID=:invID, shipmentpriceID=:shipmentpriceID, quotaID=:quotaID, ht_code=:ht_code,
					shipping_marking=:shipping_marking, group_number=:group_number, fob_price=:fob_price,
					qty=:qty, total_amount=:total_amount, BICID=:BICID, remarks=:remarks, class_description=:class_description
				  WHERE ID=:ID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		$this->ht_code           = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->ht_code);
		$this->shipping_marking  = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->shipping_marking);
		$this->class_description = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->class_description);
		$this->remarks           = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->remarks);
		
		// bind values
		$stmt->bindParam(":ID", $this->ID);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->bindParam(":BICID", $this->BICID);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":quotaID", $this->quotaID);
		$stmt->bindParam(":ht_code", $this->ht_code);
		$stmt->bindParam(":shipping_marking", $this->shipping_marking);
		$stmt->bindParam(":class_description", $this->class_description);
		$stmt->bindParam(":remarks", $this->remarks);
		$stmt->bindParam(":group_number", $this->group_number);
		$stmt->bindParam(":fob_price", $this->fob_price);
		$stmt->bindParam(":qty", $this->qty);
		$stmt->bindParam(":total_amount", $this->total_amount);
		
		// execute query
		if($stmt->execute()){
			return $this->ID;
		}
		
		return false;
	}
	
	function updateOtherCharge(){
		// query to update record
		$query = "UPDATE
					" . $this->table_name . "
					SET
					shipmentpriceID=:shipmentpriceID, total_amount=:total_amount,
					other_charge=:other_charge, charge_percentage=:charge_percentage,
					chargeID_deduct=:chargeID_deduct, charge_percentage_credit=:charge_percentage_credit, 
					chargeID_credit=:chargeID_credit
				  WHERE ID=:ID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		$this->other_charge     = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->other_charge);
		
		// bind values
		$stmt->bindParam(":ID", $this->ID);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":total_amount", $this->total_amount);
		$stmt->bindParam(":other_charge", $this->other_charge);
		$stmt->bindParam(":charge_percentage", $this->charge_percentage);
		$stmt->bindParam(":chargeID_deduct", $this->chargeID_deduct);
		$stmt->bindParam(":charge_percentage_credit", $this->charge_percentage_credit);
		$stmt->bindParam(":chargeID_credit", $this->chargeID_credit);
		
		// execute query
		if($stmt->execute()){
			return $this->ID;
		}
		
		return false;
	}
	
	function updateGroupNumberQtyAndAmount(){
		// query to update record
		$query = "UPDATE
					" . $this->table_name . "
					SET
					qty=:qty, total_amount=:total_amount
				  WHERE shipmentpriceID=:shipmentpriceID AND invID=:invID
				  AND group_number=:group_number AND del=0";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		// bind values
		$stmt->bindParam(":total_amount", $this->total_amount);
		$stmt->bindParam(":qty", $this->qty);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":group_number", $this->group_number);
		$stmt->bindParam(":invID", $this->invID);
		
		// execute query
		if($stmt->execute()){
			return $this->shipmentpriceID;
		}
		
		return false;
	}
	
	function checkInvoicePaymentDetailBuyerPOExist(){
		$query = "SELECT * 
					FROM ".$this->table_name." 
					WHERE invID=:invID AND del=0 AND shipmentpriceID=:shipmentpriceID AND group_number=:group_number";
		$stmt  = $this->conn->prepare($query);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":group_number", $this->group_number);
		$stmt->execute();
		
		return $stmt->rowCount();
	}
	
	function checkInvoicePaymentDetailExist(){
		$query = "SELECT * 
					FROM ".$this->table_name." 
					WHERE invID=:invID AND del=0";
		$stmt  = $this->conn->prepare($query);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->execute();
		
		return $stmt->rowCount();
	}
	
	function checkGOCDiscountExist(){// only for Joe Fresh Buyer
		$sql = "SELECT * 
				FROM ".$this->table_name." 
				WHERE chargeID_deduct='4' AND del=0 AND invID=:invID";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->execute();
		$count = $stmt->rowCount();
		
		if($count==0){//if not exist then add GOC Discount
			$this->model_tblbuyer_invoice_charge_option->ID = 4;
			$arr_charge  = $this->model_tblbuyer_invoice_charge_option->getChargeOptionOnlyOne();
			$Description = $arr_charge[0]["Description"];
			
			$total_all_amount = $this->getTotalAmountOfInvoiceNotIncludeOtherCharge();
			$total_amount     = round($total_all_amount * 0.025, 2) * -1;
			
			$this->chargeID_deduct   = 4;
			$this->charge_percentage = 2.5;
			$this->other_charge      = $Description;
			$this->total_amount      = $total_amount;
			$this->BICID             = 0;
			$this->shipmentpriceID   = 0;
			$this->group_number      = 0;
			$this->fob_price         = 0;
			$this->qty               = 0;
			$this->charge_percentage_credit = 0;
			$this->chargeID_credit          = 0;
			$this->create();
		}
		
	}
	
	function duplicateDataFromCommercialToBuyerPayment(){
		
		$this->model_tblbuyer_invoice_payment_category->invID = $this->invID;
		$this->model_tblbuyer_invoice_payment_category->duplicateDataFromCommercialToBuyerPayment();
		
		$sql = "SELECT bid.*, od.FactoryID as factoryID, 
				bic.options, bic.invoice_no, bic.co_number, bic.co_date, bic.custom_no, bic.custom_date, bic.custom_procedure
				FROM tblbuyer_invoice_detail bid
				INNER JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
				INNER JOIN tblorder od ON od.Orderno = sp.Orderno
				LEFT JOIN tblbuyer_invoice_category bic ON bic.BICID = bid.BICID
				WHERE bid.invID=:invID AND bid.del=0";
		$stmt  = $this->conn->prepare($sql);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			//echo "$ID <== <br/>";
			$qty = 0;
			if($group_number>0){ //-- if details is Buyer PO color but not other charges --//
				$query_filter = "";
				// $arr_all = $this->handle_shipment->getAllPackingInfoByBuyerPO($shipmentpriceID, $factoryID, $query_filter);
				// $arr_all_size = $arr_all["arr_all_size"];
				
				$skip_cih = "";//"pickpack";
				$this->handle_lc->isBuyerPayment = 0;
				$this->handle_lc->BICID = $BICID;
				$arr_list = $this->handle_lc->getBuyerInvoicePackingListData($shipmentpriceID, $skip_cih, $invID);
				
				$sqlgrp = "SELECT garmentID, colorID 
							FROM tblship_group_color 
							WHERE group_number='$group_number' AND shipmentpriceID='$shipmentpriceID' AND statusID='1'";
				$stmt_grp = $this->conn->prepare($sqlgrp);
				$stmt_grp->execute();
				$row_grp = $stmt_grp->fetch(PDO::FETCH_ASSOC);
					$garmentID = $row_grp["garmentID"];
					$colorID   = $row_grp["colorID"];
				
				$sql_price = "SELECT scp.price as Buyer_price
								from tblship_colorsizeqty scp  
								where scp.shipmentpriceID = '$shipmentpriceID' and scp.statusID=1 
								AND scp.garmentID='$garmentID' AND scp.colorID='$colorID' AND scp.price>0
								GROUP BY scp.shipmentpriceID";//sum(scp.qty*scp.price) / sum(scp.qty) as Buyer_price
				$sel_qty = $this->conn->prepare($sql_price);
				$sel_qty->execute();
				$row_qty = $sel_qty->fetch(PDO::FETCH_ASSOC);
					$fob_price = $row_qty["Buyer_price"];
				
				for($arr=0;$arr<count($arr_list);$arr++){
					$mixID     = $arr_list[$arr]["mixID"];
					$total_ctn = $arr_list[$arr]["total_ctn"];
					
					$arr_mix = explode("::^^", $mixID);
					for($mm=0;$mm<count($arr_mix);$mm++){
						list($this_group_number, $this_size, $this_qty) = explode("**%%", $arr_mix[$mm]);
						
						if($this_group_number==$group_number){
							$qty += ($this_qty * $total_ctn);
						}
					}//--- End For MixID ---//
				}//--- End For Arr List ---//
				
				$total_amount = $qty * $fob_price;
			}//--- End If ---//
			
			$total_amount      = ($group_number==0? 0: $total_amount);
			$charge_percentage = ($group_number==0? 0: $charge_percentage);
			
			$BICID = 0;
			if($group_number>0){//if not other charges, check buyer PO belong to which category
				$this->model_tblbuyer_invoice_payment_category->invID   = $invID;
				$this->model_tblbuyer_invoice_payment_category->options = $options;
				$arr_result = $this->model_tblbuyer_invoice_payment_category->checkCategoryExistNotDel(); // check category whether exist
				$isExist = $arr_result["isExist"];
				
				if($isExist==0){// create new BICID
					$this->model_tblbuyer_invoice_payment_category->invID            = $invID;
					$this->model_tblbuyer_invoice_payment_category->options          = $options;
					$this->model_tblbuyer_invoice_payment_category->invoice_no       = $invoice_no;
					$this->model_tblbuyer_invoice_payment_category->co_number        = $co_number;
					$this->model_tblbuyer_invoice_payment_category->co_date          = $co_date;
					$this->model_tblbuyer_invoice_payment_category->custom_no        = $custom_no;
					$this->model_tblbuyer_invoice_payment_category->custom_date      = $custom_date;
					$this->model_tblbuyer_invoice_payment_category->custom_procedure = $custom_procedure;
					$BICID = $this->model_tblbuyer_invoice_payment_category->create();
				}
				else{ //use back BICID
					$BICID = $arr_result["BICID"];
				}	
			}//--- End Group Number ---//
			
			$this->invID = $invID;
			$this->shipmentpriceID = $shipmentpriceID;
			$this->group_number = $group_number;
			$count = $this->checkInvoicePaymentDetailBuyerPOExist();
			
			// if($count==0){
				$this->ID = $ID;
				$this->invID = $invID;
				$this->BICID = $BICID;
				$this->shipmentpriceID = $shipmentpriceID;
				$this->quotaID = $quotaID;
				$this->ht_code = $ht_code;
				$this->shipping_marking = $shipping_marking;
				$this->group_number = $group_number;
				$this->fob_price = $fob_price;
				$this->qty = $qty;
				$this->total_amount = $total_amount;
				$this->charge_percentage = $charge_percentage;
				$this->create();
			// }
			
		}//--- End While ---//
		
	}
	
	function deleteRow(){
		// query to update record
		$query = "UPDATE
					" . $this->table_name . "
					SET
					del='1'
				  WHERE ID=:ID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		// bind values
		$stmt->bindParam(":ID", $this->ID);
		
		// execute query
		if($stmt->execute()){
			return $this->ID;
		}
		
		return false;
	}
	
	function deleteAllCategoryAndDetailRow(){
		
		$sqlBID = "UPDATE ".$this->table_name." 
					SET del='1', delBy=:delBy, delDate=now(),
						other_charge=:other_charge
					WHERE invID=:invID";
		$stmt = $this->conn->prepare($sqlBID);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->bindParam(":delBy", $this->delBy);
		$stmt->bindParam(":other_charge", $this->other_charge);
		$stmt->execute();
		
		$sqlBIC = "UPDATE tblbuyer_invoice_payment_category SET del='1'
					WHERE invID=:invID";
		$stmtBIC = $this->conn->prepare($sqlBIC);
		$stmtBIC->bindParam(":invID", $this->invID);
		$stmtBIC->execute();
		
		$sqlCIH = "UPDATE tblcarton_inv_payment_head cih 
					LEFT JOIN tblcarton_inv_payment_detail cid ON cid.CIHID = cih.CIHID
					SET cih.del='1', cid.del='1'
					WHERE cih.invID=:invID";
		$stmtCIH = $this->conn->prepare($sqlCIH);
		$stmtCIH->bindParam(":invID", $this->invID);
		$stmtCIH->execute();
		
	}
	
	function readAllDetailAndChargeGroupPOByInvID($invID, $filter_query=""){
		$query = "SELECT bipd.*, sp.Orderno, sp.BuyerPO, sp.GTN_buyerpo, sp.GTN_styleno, 
						sum(bipd.total_amount) as total_amount,  'revenue' as item_type, bip.statusID, st.StatusName
				FROM `tblbuyer_invoice_payment_detail`bipd 
				INNER JOIN tblbuyer_invoice_payment_category bipc ON bipc.BICID = bipd.BICID
				INNER JOIN tblbuyer_invoice_payment bip ON bip.ID = bipd.invID
				INNER JOIN tblstatus st ON st.StatusID = bip.statusID
				INNER JOIN tblshipmentprice sp ON sp.ID = bipd.shipmentpriceID
				WHERE bipd.del=0 AND bipc.del=0 AND bipd.invID = '$invID' $filter_query
				group by bipd.invID, bipd.shipmentpriceID

				UNION ALL 

				SELECT bipd.*, sp.Orderno, sp.BuyerPO, sp.GTN_buyerpo, sp.GTN_styleno, bipd.total_amount,
						'charge' as item_type, bip.statusID, st.StatusName
				FROM `tblbuyer_invoice_payment_detail`bipd  
				INNER JOIN tblbuyer_invoice_payment bip ON bip.ID = bipd.invID
				INNER JOIN tblstatus st ON st.StatusID = bip.statusID
				LEFT JOIN tblshipmentprice sp ON sp.ID = bipd.shipmentpriceID
				WHERE bipd.del=0  AND bipd.invID = '$invID' AND bipd.group_number=0 $filter_query
				AND (bipd.total_amount>0 or bipd.total_amount<0) ";
		$stmt = $this->conn->prepare($query); 
		$stmt->execute();
				
		$count = $stmt->rowCount();
		$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);
			
		$arr = array("count"=>"$count", "row"=>$row);
			
		return $arr;
	}
	
	function readBuyerPaymentInvoiceStyleByShipmentpriceID(){
		$query = "SELECT GROUP_CONCAT(distinct g.styleNo) as garment
					FROM `tblbuyer_invoice_payment_detail` bipd 
					INNER JOIN tblbuyer_invoice_payment_category bipc ON bipc.BICID = bipd.BICID
					INNER JOIN tblship_group_color sgc ON sgc.group_number = bipd.group_number
														AND sgc.shipmentpriceID = bipd.shipmentpriceID
					INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
					WHERE bipd.invID=:invID AND bipd.del=0 AND bipd.qty>0 AND sgc.statusID=1 AND bipc.del=0 AND bipd.shipmentpriceID=:shipmentpriceID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		// bind values
		$stmt->bindParam(":invID", $this->invID);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->execute();
				
		$count = $stmt->rowCount();
		$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);
			
		$arr = array("count"=>"$count", "row"=>$row);
			
		return $arr;
		
	}
	
	function getAllByArr($arr_td, $group_by=" group by bipd.shipmentpriceID, bipd.group_number, bipd.fob_price ", 
							$order_by=" order by sp.Orderno, sp.ID, bipd.group_number"){
		$arrbind = array();
		$query = "SELECT sp.GTN_buyerpo as actual_po, sp.Orderno as orderno, 
						(SELECT CONCAT(c.ColorName,'^^',g.styleNo,'^^',g.StyleDescription,'^^',sgc.colorID,'^^',sgc.garmentID)
						 FROM tblship_group_color sgc 
						 INNER JOIN tblcolor c ON c.ID = sgc.colorID
						 INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
						 INNER JOIN tblunit u ON u.ID = sp.uom
						 WHERE sgc.shipmentpriceID = bipd.shipmentpriceID 
						 AND sgc.group_number = bipd.group_number AND sgc.statusID=1) as color_grp,
						 bipd.fob_price, sum(bipd.qty) as qty, u.Description as unit, cur.CurrencyCode as currency
						 
				FROM `tblbuyer_invoice_payment_detail` bipd
				INNER JOIN tblshipmentprice sp ON sp.ID = bipd.shipmentpriceID
				INNER JOIN tblunit u ON u.ID = sp.uom
				INNER JOIN tblorder od ON od.Orderno = sp.Orderno
				LEFT JOIN tblcurrency cur ON cur.ID = od.currencyID
				WHERE 1=1 
				";
		foreach($arr_td as $key => $value){
			$arrvalue = explode(",", $value);
			$arrkey = explode("!!", $key);
					
			$symbol = "="; $thisnum = '';
			if(count($arrkey)>1){
				$key     = $arrkey[0];
				$symbol  = $arrkey[1];
				$thisnum = (isset($arrkey[2])? $arrkey[2]: "");
			}
					
			$thiskey = $key;
			if (strpos($key, ".") !== false) {
				// echo "Dot Found!! $key << <br/>";
				list($prefix, $thiskey) = explode(".", $key);
			}
			$arr_keytype = explode("(", $thiskey);
			if(count($arr_keytype)>1){
				$thiskey = $arr_keytype[1];
			}
			$thiskey = rtrim($thiskey, ")");
					
			if(count($arrvalue)==1 && $symbol==">DATE_SUB(DATE(now())" && $symbol!="NULL"){
				$query .= " AND DATE(".$key.") {$symbol} , INTERVAL {$value} DAY)";
			}
			else if(count($arrvalue)==1 && $symbol!="REGEXP" && $symbol!="NOTIN" && $symbol!="NULL"){
				$query .= " AND ".$key." {$symbol} :".$thiskey."{$thisnum}";
				$arrbind[$thiskey.$thisnum] = $value;
			}
			else if($symbol=="REGEXP" && $symbol!="NULL"){
				$query .= " AND ".$key." REGEXP ";
				$comma = "";
					for($i=0; $i<count($arrvalue); $i++){
					  $query .= $comma.":".$thiskey."".$i;       // :p0, :p1, ...
					  $comma = "|";
					  $arrbind[$thiskey.$i] = $arrvalue[$i];
					}
				$query .= "";
			}
			else if($symbol=="NOTIN" && $symbol!="NULL"){
					$query .= " AND ".$key." NOT IN (";
					$comma = "";
						for($i=0; $i<count($arrvalue); $i++){
						  $query .= $comma.":".$thiskey."".$i;       // :p0, :p1, ...
						  $comma = " , ";
						  $arrbind[$thiskey.$i] = $arrvalue[$i];
						}
					$query .= ")";
			}
			else if($symbol!="NULL"){
				$query .= " AND ".$key." IN (";
				$comma = "";
					for($i=0; $i<count($arrvalue); $i++){
					  $query .= $comma.":".$thiskey."".$i;       // :p0, :p1, ...
					  $comma = " , ";
					  $arrbind[$thiskey.$i] = $arrvalue[$i];
					}
				$query .= ")";
			}
			else if($symbol=="NULL"){
				$query .= " AND $key is NULL ";
			}
		}
		
		$query .= " {$group_by} ";
		$query .= " {$order_by} ";
			
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->execute($arrbind);
		
		$count = $stmt->rowCount();
		$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);
			
		$arr = array("count"=>"$count", "row"=>$row);
			
		return $arr;
	}
	
}

?>