<?php 
class tblbuyer_invoice_payment{
	private $conn;
	private $table_name  = "tblbuyer_invoice_payment";
	private $handle_misc = "";
	
	// object properties
	public $ID;
	public $BuyerID;
	public $issue_from;
	public $issue_from_addr = NULL;
	public $ConsigneeID;
	public $consignee_address = NULL;
	public $ship_to = NULL;
	public $ship_address = NULL;
	public $shipper = NULL;
	public $shipper_address = NULL;
	public $notifyID = NULL;
	public $notify_party = NULL;
	public $notify_address = NULL;
	
	public $BID = 0;
	public $poissuer;
	public $poissue_date = NULL;
	public $built_to;
	public $byr_invoice_no = NULL;
	public $invoice_no;
	public $invoice_date;
	public $shipmodeID;
	public $portLoadingID;
	public $porID = 0;
	public $BuyerDestID = 0;
	public $PortDestID = 0;
	public $transitPortID = NULL;
	public $shippeddate;
	public $ETA = NULL;
	public $tradeTermID;
	public $paymentTermID;
	public $vesselname;
	public $container_no = NULL;
	public $carrier = NULL;
	public $seal_no = NULL;
	public $cargocutoffdate;
	public $export_by;
	public $exfactory;
	public $remarks = NULL;
	public $createdby;
	public $createddate;
	public $updatedby;
	public $updateddate;
	public $join_inspection_no = NULL;
	public $join_inspection_date = NULL;
	public $custom_procedure = NULL;
	public $cdc_no = NULL;
	public $cdc_date = NULL;
	public $co_number = NULL;
	public $co_date = NULL;
	public $conversion_rate = 4010;
	public $statusID = 4;
	public $inv_opt = 0;
	public $from_invID = 0;
	
	// constructor with $db as database connection
    public function __construct($conn){
        $this->conn = $conn;
    }
	
	public function setMisc($handle_misc){
		$this->handle_misc = $handle_misc;
		$this->handle_misc->setConnection($this->conn);
	}
	
	function create(){
		// query to insert record
		$query = "INSERT INTO
					" . $this->table_name . "
					SET
					ID=:ID, BuyerID=:BuyerID, ConsigneeID=:ConsigneeID, byr_invoice_no=:byr_invoice_no,
					consignee_address=:consignee_address, issue_from=:issue_from, issue_from_addr=:issue_from_addr,
					poissuer=:poissuer, poissue_date=:poissue_date, built_to=:built_to, invoice_no=:invoice_no,
					shipmodeID=:shipmodeID, portLoadingID=:portLoadingID, BuyerDestID=:BuyerDestID, ETA=:ETA,
					PortDestID=:PortDestID, shippeddate=:shippeddate, tradeTermID=:tradeTermID, paymentTermID=:paymentTermID,
					vesselname=:vesselname, cargocutoffdate=:cargocutoffdate, export_by=:export_by, exfactory=:exfactory,
					createdby=:createdby, createddate=:createddate, updatedby=:updatedby, updateddate=:updateddate,
					join_inspection_no=:join_inspection_no, join_inspection_date=:join_inspection_date, custom_procedure=:custom_procedure, cdc_no=:cdc_no, cdc_date=:cdc_date, statusID=:statusID, remarks=:remarks, BID=:BID, container_no=:container_no,
					ship_to=:ship_to, ship_address=:ship_address, shipper=:shipper, shipper_address=:shipper_address,
					notifyID=:notifyID, notify_party=:notify_party, notify_address=:notify_address, co_number=:co_number, co_date=:co_date,
					conversion_rate=:conversion_rate, carrier=:carrier, seal_no=:seal_no, transitPortID=:transitPortID, 
					inv_opt=:inv_opt, from_invID=:from_invID, porID=:porID";
		
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		$this->ID = $this->handle_misc->funcMaxID($this->table_name, "ID");
		
		$this->issue_from         = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->issue_from);
		$this->poissuer           = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->poissuer);
		$this->built_to           = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->built_to);
		$this->invoice_no         = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->invoice_no);
		$this->vesselname         = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->vesselname);
		$this->join_inspection_no = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->join_inspection_no);
		$this->custom_procedure   = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->custom_procedure);
		$this->cdc_no             = ($this->cdc_no==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->cdc_no));
		$this->remarks            = ($this->remarks==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->remarks));
		$this->container_no       = ($this->container_no==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->container_no));
		$this->co_number          = ($this->co_number==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->co_number));
		$this->carrier            = ($this->carrier==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->carrier));
		$this->seal_no            = ($this->seal_no==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->seal_no));
		$this->createddate        = $this->handle_misc->TimeNow();
		$this->updateddate        = $this->handle_misc->TimeNow();
		
		$this->issue_from_addr  = ($this->issue_from_addr==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->issue_from_addr));
		$this->consignee_address  = ($this->consignee_address==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->consignee_address));
		$this->ship_to            = ($this->ship_to==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->ship_to));
		$this->ship_address       = ($this->ship_address==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->ship_address));
		$this->shipper_address    = ($this->shipper_address==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->shipper_address));
		$this->notify_party       = ($this->notify_party==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->notify_party));
		$this->notify_address     = ($this->notify_address==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->notify_address));
		
		// bind values
		$stmt->bindParam(":ID", $this->ID);
		$stmt->bindParam(":BuyerID", $this->BuyerID);
		$stmt->bindParam(":ConsigneeID", $this->ConsigneeID);
		$stmt->bindParam(":consignee_address", $this->consignee_address);
		$stmt->bindParam(":issue_from", $this->issue_from);
		$stmt->bindParam(":issue_from_addr", $this->issue_from_addr);
		$stmt->bindParam(":poissuer", $this->poissuer);
		$stmt->bindParam(":poissue_date", $this->poissue_date);
		$stmt->bindParam(":built_to", $this->built_to);
		$stmt->bindParam(":byr_invoice_no", $this->byr_invoice_no);
		$stmt->bindParam(":invoice_no", $this->invoice_no);
		$stmt->bindParam(":shipmodeID", $this->shipmodeID);
		$stmt->bindParam(":portLoadingID", $this->portLoadingID);
		$stmt->bindParam(":BuyerDestID", $this->BuyerDestID);
		$stmt->bindParam(":PortDestID", $this->PortDestID);
		$stmt->bindParam(":shippeddate", $this->shippeddate);
		$stmt->bindParam(":ETA", $this->ETA);
		$stmt->bindParam(":tradeTermID", $this->tradeTermID);
		$stmt->bindParam(":paymentTermID", $this->paymentTermID);
		$stmt->bindParam(":vesselname", $this->vesselname);
		$stmt->bindParam(":cargocutoffdate", $this->cargocutoffdate);
		$stmt->bindParam(":export_by", $this->export_by);
		$stmt->bindParam(":exfactory", $this->exfactory);
		$stmt->bindParam(":remarks", $this->remarks);
		$stmt->bindParam(":createdby", $this->createdby);
		$stmt->bindParam(":createddate", $this->createddate);
		$stmt->bindParam(":updatedby", $this->updatedby);
		$stmt->bindParam(":updateddate", $this->updateddate);
		$stmt->bindParam(":join_inspection_no", $this->join_inspection_no);
		$stmt->bindParam(":join_inspection_date", $this->join_inspection_date);
		$stmt->bindParam(":custom_procedure", $this->custom_procedure);
		$stmt->bindParam(":cdc_no", $this->cdc_no);
		$stmt->bindParam(":cdc_date", $this->cdc_date);
		$stmt->bindParam(":statusID", $this->statusID);
		$stmt->bindParam(":BID", $this->BID);
		$stmt->bindParam(":container_no", $this->container_no);
		$stmt->bindParam(":carrier", $this->carrier);
		$stmt->bindParam(":seal_no", $this->seal_no);
		$stmt->bindParam(":ship_to", $this->ship_to);
		$stmt->bindParam(":ship_address", $this->ship_address);
		$stmt->bindParam(":shipper", $this->shipper);
		$stmt->bindParam(":shipper_address", $this->shipper_address);
		$stmt->bindParam(":notifyID", $this->notifyID);
		$stmt->bindParam(":notify_party", $this->notify_party);
		$stmt->bindParam(":notify_address", $this->notify_address);
		$stmt->bindParam(":co_number", $this->co_number);
		$stmt->bindParam(":co_date", $this->co_date);
		$stmt->bindParam(":conversion_rate", $this->conversion_rate);
		$stmt->bindParam(":transitPortID", $this->transitPortID);
		$stmt->bindParam(":inv_opt", $this->inv_opt);
		$stmt->bindParam(":from_invID", $this->from_invID);
		$stmt->bindParam(":porID", $this->porID);
		
		// execute query
		if($stmt->execute()){
			return $this->ID;
		}
		
		return false;
	}
	
	function update(){
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					BuyerID=:BuyerID, ConsigneeID=:ConsigneeID, byr_invoice_no=:byr_invoice_no, 
					consignee_address=:consignee_address, issue_from=:issue_from, issue_from_addr=:issue_from_addr,
					poissuer=:poissuer, built_to=:built_to, invoice_no=:invoice_no,
					poissue_date=:poissue_date, shipmodeID=:shipmodeID, portLoadingID=:portLoadingID, BuyerDestID=:BuyerDestID,
					PortDestID=:PortDestID, shippeddate=:shippeddate, tradeTermID=:tradeTermID, paymentTermID=:paymentTermID,
					vesselname=:vesselname, export_by=:export_by, cargocutoffdate=:cargocutoffdate, exfactory=:exfactory,
					updatedby=:updatedby, updateddate=:updateddate, statusID=:statusID, remarks=:remarks,
					BID=:BID, container_no=:container_no, ship_to=:ship_to, ship_address=:ship_address,
					shipper=:shipper, shipper_address=:shipper_address,
					notifyID=:notifyID, notify_party=:notify_party, notify_address=:notify_address, conversion_rate=:conversion_rate,
					ETA=:ETA, carrier=:carrier, seal_no=:seal_no, transitPortID=:transitPortID, cdc_no=:cdc_no, cdc_date=:cdc_date, porID=:porID
				WHERE
					ID=:ID";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		
		$this->issue_from   = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->issue_from);
		$this->poissuer     = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->poissuer);
		$this->built_to     = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->built_to);
		$this->invoice_no   = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->invoice_no);
		$this->vesselname   = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->vesselname);
		$this->cdc_no       = ($this->cdc_no==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->cdc_no));
		$this->remarks      = ($this->remarks==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->remarks));
		$this->container_no = ($this->container_no==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->container_no));
		$this->carrier      = ($this->carrier==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->carrier));
		$this->seal_no      = ($this->seal_no==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->seal_no));
		$this->updateddate  = $this->handle_misc->TimeNow();
	
		$this->ship_to      = ($this->ship_to==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->ship_to));
		$this->notify_party = ($this->notify_party==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->notify_party));
		
		$stmt->bindParam(":ID", $this->ID);
		$stmt->bindParam(":BuyerID", $this->BuyerID);
		$stmt->bindParam(":ConsigneeID", $this->ConsigneeID);
		$stmt->bindParam(":consignee_address", $this->consignee_address);
		$stmt->bindParam(":issue_from", $this->issue_from);
		$stmt->bindParam(":issue_from_addr", $this->issue_from_addr);
		$stmt->bindParam(":poissuer", $this->poissuer);
		$stmt->bindParam(":poissue_date", $this->poissue_date);
		$stmt->bindParam(":built_to", $this->built_to);
		$stmt->bindParam(":byr_invoice_no", $this->byr_invoice_no);
		$stmt->bindParam(":invoice_no", $this->invoice_no);
		$stmt->bindParam(":shipmodeID", $this->shipmodeID);
		$stmt->bindParam(":portLoadingID", $this->portLoadingID);
		$stmt->bindParam(":BuyerDestID", $this->BuyerDestID);
		$stmt->bindParam(":PortDestID", $this->PortDestID);
		$stmt->bindParam(":shippeddate", $this->shippeddate);
		$stmt->bindParam(":tradeTermID", $this->tradeTermID);
		$stmt->bindParam(":paymentTermID", $this->paymentTermID);
		$stmt->bindParam(":vesselname", $this->vesselname);
		$stmt->bindParam(":cargocutoffdate", $this->cargocutoffdate);
		$stmt->bindParam(":export_by", $this->export_by);
		$stmt->bindParam(":exfactory", $this->exfactory);
		$stmt->bindParam(":remarks", $this->remarks);
		$stmt->bindParam(":updatedby", $this->updatedby);
		$stmt->bindParam(":updateddate", $this->updateddate);
		$stmt->bindParam(":statusID", $this->statusID);
		$stmt->bindParam(":BID", $this->BID);
		$stmt->bindParam(":container_no", $this->container_no);
		$stmt->bindParam(":ship_to", $this->ship_to);
		$stmt->bindParam(":ship_address", $this->ship_address);
		$stmt->bindParam(":shipper", $this->shipper);
		$stmt->bindParam(":shipper_address", $this->shipper_address);
		$stmt->bindParam(":notifyID", $this->notifyID);
		$stmt->bindParam(":notify_party", $this->notify_party);
		$stmt->bindParam(":notify_address", $this->notify_address);
		$stmt->bindParam(":conversion_rate", $this->conversion_rate);
		$stmt->bindParam(":ETA", $this->ETA);
		$stmt->bindParam(":carrier", $this->carrier);
		$stmt->bindParam(":seal_no", $this->seal_no);
		$stmt->bindParam(":transitPortID", $this->transitPortID);
		$stmt->bindParam(":cdc_no", $this->cdc_no);
		$stmt->bindParam(":cdc_date", $this->cdc_date);
		$stmt->bindParam(":porID", $this->porID);
		
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	public function updateByArr($arr_td){
		$query = "UPDATE ".$this->table_name."  
				SET ";
				foreach($arr_td as $key => $value){
					$query .= "".$key."=:".$key.",";
				}
				$query = rtrim($query, ",");
		$query .= " WHERE ID=:ID";
		$stmt = $this->conn->prepare($query);
		$stmt->execute($arr_td);
	}
	
	function updateInvoiceDate(){
		$query = "UPDATE
					" . $this->table_name . "
				SET
					invoice_date=:invoice_date
				WHERE
					ID=:ID";
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":invoice_date", $this->invoice_date);
		$stmt->bindParam(":ID", $this->ID);
		
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	function updateShipAddress($ship_address){
		$query = "UPDATE
					" . $this->table_name . "
				SET
					ship_address=:ship_address
				WHERE
					ID=:ID";
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":ship_address", $ship_address);
		$stmt->bindParam(":ID", $this->ID);
		
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	function updateRemarks($remarks){
		$query = "UPDATE
					" . $this->table_name . "
				SET
					remarks=:remarks
				WHERE
					ID=:ID";
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":remarks", $remarks);
		$stmt->bindParam(":ID", $this->ID);
		
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	function updateStatusOnly(){
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					statusID=:statusID, updatedby=:updatedby, updateddate=:updateddate
				WHERE
					ID=:ID";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		
		$this->statusID      = $this->statusID;
		$this->updateddate   = $this->handle_misc->TimeNow();
		
		$stmt->bindParam(':statusID', $this->statusID);
		$stmt->bindParam(':updatedby', $this->updatedby);
		$stmt->bindParam(':updateddate', $this->updateddate);
		$stmt->bindParam(':ID', $this->ID);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	function updateCOInfo(){
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					co_number=:co_number, co_date=:co_date
				WHERE
					ID=:ID";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		
		$this->co_number = ($this->co_number==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->co_number));
		
		$stmt->bindParam(':co_number', $this->co_number);
		$stmt->bindParam(':co_date', $this->co_date);
		$stmt->bindParam(':ID', $this->ID);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	function updateCustomInfo(){
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					join_inspection_no=:join_inspection_no, join_inspection_date=:join_inspection_date, custom_procedure=:custom_procedure
				WHERE
					ID=:ID";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		
		$this->join_inspection_no = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->join_inspection_no);
		$this->custom_procedure   = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->custom_procedure);
		
		$stmt->bindParam(':join_inspection_no', $this->join_inspection_no);
		$stmt->bindParam(':join_inspection_date', $this->join_inspection_date);
		$stmt->bindParam(':custom_procedure', $this->custom_procedure);
		$stmt->bindParam(':ID', $this->ID);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	function duplicateHeaderFromCommercialToBuyerPayment(){
		
		$sql = "INSERT INTO tblbuyer_invoice_payment 
				(ID, BuyerID, ConsigneeID, consignee_address, ship_to, ship_address, shipper, shipper_address,
				 notifyID, notify_party, notify_address, BID, issue_from, issue_from_addr, poissuer, poissue_date, 
				 built_to, invoice_no, invoice_date, shipmodeID, portLoadingID, BuyerDestID, PortDestID, 
				 shippeddate, tradeTermID, paymentTermID, vesselname, container_no, export_by, cargocutoffdate,  
				 exfactory, remarks, createdby, createddate, updatedby, updateddate, join_inspection_no, join_inspection_date, 
				 custom_procedure, cdc_no, cdc_date, co_number, co_date, conversion_rate, statusID, ETA, carrier, seal_no, transitPortID)  
				
				SELECT ID, BuyerID, ConsigneeID, consignee_address, ship_to, ship_address, shipper, shipper_address,
				 notifyID, notify_party, notify_address, BID, issue_from, issue_from_addr, poissuer, poissue_date, 
				 built_to, invoice_no, shippeddate, shipmodeID, portLoadingID, BuyerDestID, PortDestID, 
				 shippeddate, tradeTermID, paymentTermID, vesselname, container_no, export_by, cargocutoffdate,  
				 exfactory, remarks, createdby, createddate, updatedby, updateddate, join_inspection_no, join_inspection_date, 
				 custom_procedure, cdc_no, cdc_date, co_number, co_date, conversion_rate, '4' as statusID, ETA, carrier, seal_no, transitPortID
				 FROM tblbuyer_invoice WHERE ID=:ID";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(':ID', $this->ID);
		$stmt->execute();
		
	}
	
	function duplicateHeaderToNewOption(){
		$arr_alphabet = array('','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		$sql = "SELECT invoice_no, max(inv_opt) as inv_opt, 
						 (SELECT invoice_no FROM tblbuyer_invoice_payment bi WHERE bi.ID=:ID) as inv
				FROM `tblbuyer_invoice_payment` WHERE from_invID=:from_invID";
		//prepare query statement
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(":ID", $this->ID);
		$stmt->bindParam(":from_invID", $this->from_invID);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$inv     = $row["inv"];
			$inv_opt = $row["inv_opt"];
			$inv_opt = ($inv_opt==""? 1: ++$inv_opt);
			
		$new_inv    = "$inv".$arr_alphabet[$inv_opt];
		$newID      = $this->funcMaxInvOptionID();
		$this->createddate        = $this->handle_misc->TimeNow();
		$this->updateddate        = $this->handle_misc->TimeNow();
		
		$sql = "INSERT INTO tblbuyer_invoice_payment 
				(ID, BuyerID, ConsigneeID, consignee_address, ship_to, ship_address, shipper, shipper_address,
				 notifyID, notify_party, notify_address, BID, issue_from, issue_from_addr, poissuer, poissue_date, 
				 built_to, invoice_no, invoice_date, shipmodeID, portLoadingID, BuyerDestID, PortDestID, 
				 shippeddate, tradeTermID, paymentTermID, vesselname, container_no, export_by, cargocutoffdate,  
				 exfactory, remarks, createdby, createddate, updatedby, updateddate, join_inspection_no, join_inspection_date, 
				 custom_procedure, cdc_no, cdc_date, co_number, co_date, conversion_rate, statusID, ETA, carrier, seal_no, transitPortID,
				 inv_opt, from_invID)  
				
				SELECT '$newID', BuyerID, ConsigneeID, consignee_address, ship_to, ship_address, shipper, shipper_address,
				 notifyID, notify_party, notify_address, BID, issue_from, issue_from_addr, poissuer, poissue_date, 
				 built_to, '$new_inv', shippeddate, shipmodeID, portLoadingID, BuyerDestID, PortDestID, 
				 shippeddate, tradeTermID, paymentTermID, vesselname, container_no, export_by, cargocutoffdate,  
				 exfactory, remarks, '".$this->createdby."', '".$this->createddate."', '".$this->updatedby."', '".$this->updateddate."', join_inspection_no, join_inspection_date, 
				 custom_procedure, cdc_no, cdc_date, co_number, co_date, conversion_rate, '4' as statusID, ETA, carrier, seal_no, transitPortID, 
				 '$inv_opt', ID
				 FROM tblbuyer_invoice_payment WHERE ID=:ID";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(':ID', $this->ID);
		$stmt->execute();
		
		$arr_result = array("newID"=>$newID, "new_inv"=>$new_inv);
		
		return $arr_result;
	}
	
	function funcMaxInvOptionID(){
		$sql = "SELECT max(ID) as ID 
				FROM tblbuyer_invoice_payment WHERE inv_opt>0";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$ID = $row["ID"];
			
		if(trim($ID)==""){
			$newID = 1000000001;
		}
		else{
			++$ID;
			$newID = $ID;
		}
		
		return $newID;
	}
	
	function syncHeaderFromBuyerPaymentToCommercialInvoice(){
		$sql = "UPDATE tblbuyer_invoice_payment bip
				INNER JOIN tblbuyer_invoice bi ON bip.ID = bi.ID
				SET bi.BuyerID=bip.BuyerID, bi.ConsigneeID=bip.ConsigneeID, bi.consignee_address=bip.consignee_address, 
				bi.ship_to=bip.ship_to, bi.ship_address=bip.ship_address, bi.shipper=bip.shipper, bi.shipper_address=bip.shipper_address,
				bi.notifyID=bip.notifyID, bi.notify_party=bip.notify_party, bi.notify_address=bip.notify_address, bi.BID=bip.BID, bi.issue_from=bip.issue_from, 
				bi.issue_from_addr=bip.issue_from_addr, bi.poissuer=bip.poissuer, bi.poissue_date=bip.poissue_date, bi.built_to=bip.built_to,
				bi.shipmodeID=bip.shipmodeID, bi.portLoadingID=bip.portLoadingID, bi.BuyerDestID=bip.BuyerDestID, bi.PortDestID=bip.PortDestID, 
				bi.shippeddate=bip.shippeddate, bi.tradeTermID=bip.tradeTermID, bi.paymentTermID=bip.paymentTermID, bi.vesselname=bip.vesselname,
				bi.container_no=bip.container_no, bi.export_by=bip.export_by, bi.cargocutoffdate=bip.cargocutoffdate, bi.exfactory=bip.exfactory,
				bi.remarks=bip.remarks, bi.join_inspection_no=bip.join_inspection_no, bi.join_inspection_date=bip.join_inspection_date, 
				bi.custom_procedure=bip.custom_procedure, bi.cdc_no=bip.cdc_no, bi.co_number=bip.co_number, bi.co_date=bip.co_date, 
				bi.conversion_rate=bip.conversion_rate, bi.ETA=bip.ETA, bi.carrier=bip.carrier, bi.seal_no=bip.seal_no, bi.transitPortID=bip.transitPortID, bi.cdc_date=bip.cdc_date
				WHERE bip.ID=:ID AND bi.statusID NOT IN (8)";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(':ID', $this->ID);
		$stmt->execute();
	}
	
	function checkInvoicePaymentExist(){
		$query = "SELECT * 
					FROM ".$this->table_name." 
					WHERE ID=:ID";
		$stmt  = $this->conn->prepare($query);
		$stmt->bindParam(":ID", $this->ID);
		$stmt->execute();
		
		return $stmt->rowCount();
	}
	
	function checkInvoiceNoExist(){
		$count = 0;
		$query = "SELECT * 
				  FROM ".$this->table_name." bi
				  WHERE bi.invoice_no=:invoice_no AND bi.ID<>:ID";
		//prepare query statement
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":invoice_no", $this->invoice_no);
		$stmt->bindParam(":ID", $this->ID);	
		$stmt->execute();	
		
		$count = $stmt->rowCount();
		return $count;
	}
	
	
	public function getAllBOMWasteByArr($arr_td, $group_by=" group by sp.Orderno ", $order_by=" order by od.createdDate desc "){
		$arrbind = array();
		$query = "SELECT sp.Orderno as orderno, fty.FactoryName_ENG as factory, fty.FactoryCode as ftycode, b.BuyerName_Eng as buyer,
							bd.Description as brand, st.StatusName as statusname, 
							(SELECT GROUP_CONCAT(bf.losspercent)
								FROM tblmpurchase mp 
								INNER JOIN tblbomfabric bf ON bf.bomfabricID = mp.bomfabricID AND bf.subID = mp.subID AND mp.part=1
								WHERE mp.orderno = sp.Orderno AND mp.isMainBody=1) as losspercent, 
								'Body' as fabric, '0.5' as spread_length, GROUP_CONCAT(distinct CONCAT(bi.invoice_no,'!!',bi.shippeddate)) as invoice_no,
								GROUP_CONCAT(distinct bi.ID) as invID, '0' as WRMID 
					FROM `tblbuyer_invoice_payment` bi 
					INNER JOIN tblbuyer_invoice_payment_category bic ON bic.invID = bi.ID
					INNER JOIN tblbuyer_invoice_payment_detail bipd ON bipd.invID = bic.invID
																	AND bipd.BICID = bic.BICID
					INNER JOIN tblshipmentprice sp ON sp.ID = bipd.shipmentpriceID
					INNER JOIN tblorder od ON od.Orderno = sp.Orderno
					LEFT JOIN tblfactory fty ON fty.FactoryID = od.FactoryID
					LEFT JOIN tblbuyer b ON b.BuyerID = od.buyerID
					LEFT JOIN tblbrand bd ON bd.ID = od.brandID
					LEFT JOIN tblstatus st ON st.StatusID = od.statusID
					WHERE bic.del=0 AND bipd.del=0 
					";
					// AND bipd.group_number>0 
					// AND bi.shippeddate >= '2024-08-01' AND bi.shippeddate<='2024-08-31' AND bi.statusID = 8
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
		
		// echo "<pre>$query</pre>";
			
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->execute($arrbind);
		
		$count = $stmt->rowCount();
		$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);
			
		$arr = array("count"=>"$count", "row"=>$row);
			
		return $arr;
	}
	
	public function getAllByArr($arr_td, $group_by=" group by bip.ID ", $order_by=" order by bip.ID asc"){
		$arrbind = array();
		
		$query = "SELECT bip.*, GROUP_CONCAT(distinct CONCAT(bip.invoice_no,'!!',bip.shippeddate)) as grp_invoice_no, 
							cp.CompanyName_ENG as vendor_name, cp.Address as vendor_addr, 
							cp.Tel as vendor_tel, cp.Fax as vendor_fax,
                            cs.Name as consignee_name, cs.Address as consignee_addr, 
                            cty.Description as manucountry, sm.Description as shipmode,
                            tt.Description as tradeterm, lp.Description as portloading,
                            cts.Description as transitport, byrd.Description as buyer_dest,
                            ptt.Description as paymentterm, 
							fty_shipper.FactoryName_ENG as shipper, fty_shipper.Tel as shipper_tel, 
							fty_shipper.Fax as shipper_fax, fty_shipper.Address as shipaddr
							
					FROM ".$this->table_name." bip  
                    LEFT JOIN tblcompanyprofile cp ON cp.ID = bip.issue_from
                    LEFT JOIN tblconsignee cs ON cs.ConsigneeID = bip.ConsigneeID
                    LEFT JOIN tblbuyer_invoice_payment_detail bipd ON bipd.invID = bip.ID AND bipd.del=0
                    LEFT JOIN tblshipmentprice sp ON sp.ID = bipd.shipmentpriceID
                    LEFT JOIN tblorder od ON od.Orderno = sp.Orderno
                    LEFT JOIN tblfactory ftym ON ftym.FactoryID = od.manufacturer
                    LEFT JOIN tblcountry cty ON cty.ID = ftym.countryID
                    LEFT JOIN tblshipmode sm ON sm.ID = bip.shipmodeID
                    LEFT JOIN tbltradeterm tt ON tt.ID = bip.tradeTermID
                    LEFT JOIN tblloadingport lp ON lp.ID = bip.portLoadingID
					LEFT JOIN tblcountry cts ON cts.ID = bip.transitPortID	
                    LEFT JOIN tblbuyerdestination byrd ON byrd.ID = bip.BuyerDestID
                    LEFT JOIN tblpaymentterm ptt ON ptt.ID = bip.paymentTermID
					LEFT JOIN tblfactory fty_shipper ON fty_shipper.FactoryID = bip.shipper
					WHERE 1=1 ";
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
		
		// echo "<pre>$query</pre>";
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->execute($arrbind);
		
		$count = $stmt->rowCount();
		$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);
			
		$arr = array("count"=>"$count", "row"=>$row);
			
		return $arr;
	}
	
	public function getSumQtyByInvID($invID, $orderno=""){
		$invID   = rtrim($invID, ",");;
		$arrbind = array("Orderno"=>$orderno);
		$query   = "SELECT sum(qty) as qty, unit, sum(gross_weight) as gross_weight, GROUP_CONCAT(DISTINCT tbl.factoryID) as factoryID,
							tbl.BICID, tbl.shipmentpriceID, GROUP_CONCAT(distinct tbl.invoice_no) as invoice_no, tbl.uom
					FROM (SELECT bipd.BICID, bipd.shipmentpriceID, sum(bipd.qty) as qty, u.Description as unit,
							(SELECT sum(cph.gross_weight * cph.total_ctn)
							 FROM tblcarton_inv_payment_head cph 
							 WHERE cph.shipmentpriceID = bipd.shipmentpriceID AND cph.del=0 
							 AND cph.invID = bipd.invID AND cph.BICID = bipd.BICID) as gross_weight, od.FactoryID as factoryID,
							 group_concat(distinct bip.invoice_no) as invoice_no, sp.uom
					FROM `tblbuyer_invoice_payment` bip 
					INNER JOIN tblbuyer_invoice_payment_detail bipd ON bipd.invID = bip.ID
					INNER JOIN tblshipmentprice sp ON sp.ID = bipd.shipmentpriceID
					INNER JOIN tblorder od ON od.Orderno = sp.Orderno
					LEFT JOIN tblunit u ON u.ID = sp.uom
					WHERE bip.ID IN ($invID) AND bipd.del=0 AND bipd.group_number>0 AND sp.Orderno=:Orderno
					group by bipd.BICID, bipd.shipmentpriceID, od.FactoryID) as tbl
					group by tbl.factoryID";
		// echo "<pre>$query</pre>";
		$stmt = $this->conn->prepare($query);
		$stmt->execute($arrbind);
		
		$count = $stmt->rowCount();
		$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);
			
		$arr = array("count"=>"$count", "row"=>$row);
		
		return $arr;
	}
}

?>