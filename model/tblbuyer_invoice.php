<?php 
class tblbuyer_invoice{
	private $conn;
	private $table_name  = "tblbuyer_invoice";
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
	public $invoice_date = "2021-01-19";
	public $first_invoice_date = NULL;
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
	
	public function getMaxInvoiceNumber(){
		$prefix = glb_binv_prefix;
		$datetime  = $this->handle_misc->TimeNow();
		$timestamp = strtotime($this->first_invoice_date);// strtotime("".$datetime);
		$sql_year  = date("Y", $timestamp);
		$year      = date("y", $timestamp);
		
		$sql  = "SELECT count(invoice_no) as num FROM ".$this->table_name." WHERE 1=1 AND YEAR(first_invoice_date)='$sql_year'";
		$stmt = $this->conn->query($sql);
		$row  = $stmt->fetch(PDO::FETCH_ASSOC);
			$nn = $row["num"] + 0000; //current invoice running number until BINV21000001 //000000
			
		$number = $nn + 1;
		$num = $this->handle_misc->numberFormat($number, glb_binv_length);//6
		$invoice_no = $prefix.$year.$num;
		
		//echo "$year / $sql_year <<< ";
		
		return $invoice_no;
	}
	
	
	public function getLatestCDCNoAndDate(){
		$sql = "SELECT cdc_no, cdc_date 
				FROM ".$this->table_name." WHERE 1=1 AND cdc_no is not NULL and cdc_no!='' order by ID desc limit 1";
		$stmt = $this->conn->query($sql);
		$row  = $stmt->fetch(PDO::FETCH_ASSOC);
			$cdc_no   = $row["cdc_no"];
			$cdc_date = $row["cdc_date"];
			
		$arr = array("cdc_no"=>"$cdc_no", "cdc_date"=>"$cdc_date");
		return $arr;
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
					PortDestID=:PortDestID, shippeddate=:shippeddate, tradeTermID=:tradeTermID, paymentTermID=:paymentTermID, first_invoice_date=:first_invoice_date,
					vesselname=:vesselname, cargocutoffdate=:cargocutoffdate, export_by=:export_by, exfactory=:exfactory,
					createdby=:createdby, createddate=:createddate, updatedby=:updatedby, updateddate=:updateddate,
					cdc_no=:cdc_no, cdc_date=:cdc_date, statusID=:statusID, remarks=:remarks, BID=:BID, container_no=:container_no,
					ship_to=:ship_to, ship_address=:ship_address, shipper=:shipper, shipper_address=:shipper_address,
					notifyID=:notifyID, notify_party=:notify_party, notify_address=:notify_address,
					conversion_rate=:conversion_rate, carrier=:carrier, seal_no=:seal_no, transitPortID=:transitPortID, porID=:porID";
		
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		$this->ID = $this->handle_misc->funcMaxID($this->table_name, "ID");
		
		$this->issue_from         = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->issue_from);
		$this->poissuer           = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->poissuer);
		$this->built_to           = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->built_to);
		$this->invoice_no         = $this->getMaxInvoiceNumber();//$this->handle_misc->funcConvertSpecialCharWOUpperCase($this->invoice_no);
		$this->vesselname         = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->vesselname);
		$this->cdc_no             = ($this->cdc_no==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->cdc_no));
		$this->remarks            = ($this->remarks==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->remarks));
		$this->container_no       = ($this->container_no==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->container_no));
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
		$stmt->bindParam(":first_invoice_date", $this->first_invoice_date);
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
		$stmt->bindParam(":conversion_rate", $this->conversion_rate);
		$stmt->bindParam(":transitPortID", $this->transitPortID);
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
		
		$this->issue_from         = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->issue_from);
		$this->poissuer           = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->poissuer);
		$this->built_to           = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->built_to);
		$this->invoice_no         = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->invoice_no);
		$this->vesselname         = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->vesselname);
		$this->cdc_no             = ($this->cdc_no==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->cdc_no));
		$this->remarks            = ($this->remarks==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->remarks));
		$this->container_no       = ($this->container_no==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->container_no));
		$this->carrier            = ($this->carrier==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->carrier));
		$this->seal_no            = ($this->seal_no==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->seal_no));
		$this->updateddate        = $this->handle_misc->TimeNow();
		
		//$this->issue_from_addr    = ($this->issue_from_addr==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->issue_from_addr));
		//$this->consignee_address  = ($this->consignee_address==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->consignee_address));
		$this->ship_to            = ($this->ship_to==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->ship_to));
		//$this->ship_address       = ($this->ship_address==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->ship_address));
		//$this->shipper_address    = ($this->shipper_address==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->shipper_address));
		$this->notify_party       = ($this->notify_party==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->notify_party));
		//$this->notify_address     = ($this->notify_address==NULL? NULL: $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->notify_address));
		
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
	
	function syncHeaderFromCommercialInvoiceToBuyerPayment(){
		$sql = "UPDATE tblbuyer_invoice bip
				INNER JOIN tblbuyer_invoice_payment bi ON bip.ID = bi.ID
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
	
	public function getAllByArr($arr_td, $group_by="", $order_by=""){
		$arrbind = array();
		
		$query = "SELECT bi.*, bic.BICID as linkID, CONCAT(bid.invID,'-',bid.shipmentpriceID) as linkID2, sm.Description as shipmode ,
						group_concat(distinct sp.Orderno separator ', ')  as orderno,
						group_concat(distinct sp.BuyerPO separator ', ')  as buyerpo,
						bic.BICID, bic.invoice_no as bic_invoice,
						sum(bid.qty) as qty,
						count(distinct bic.BICID) as count_bic, MIN(bid.ID) as BIDID, u.Description as unit, bid.ht_code as hts_code
					FROM ".$this->table_name." bi 
					LEFT JOIN tblshipmode sm ON sm.ID = bi.shipmodeID  
					LEFT JOIN tblbuyer_invoice_category bic ON bic.invID = bi.ID 
					LEFT JOIN tblbuyer_invoice_detail bid ON bid.invID = bi.ID AND bid.BICID = bic.BICID
					LEFT JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
					LEFT JOIN tblunit u ON u.ID = sp.uom 
					WHERE 1=1 ";
		foreach($arr_td as $key => $value){
				$arrvalue = explode(",", $value);
				$arrkey = explode("!!", $key);
						
				$symbol = "="; $thisnum = '';
				if(count($arrkey)>1){
					$key = $arrkey[0];
					$symbol = $arrkey[1];
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
						
				if(count($arrvalue)==1 && $symbol==">DATE_SUB(DATE(now())"){
					$query .= " AND DATE(".$key.") {$symbol} , INTERVAL {$value} DAY)";
				}
				else if(count($arrvalue)==1 && $symbol!="REGEXP" && $symbol!="NOTIN"){
					$query .= " AND ".$key." {$symbol} :".$thiskey."{$thisnum}";
					$arrbind[$thiskey.$thisnum] = $value;
				}
				else if($symbol=="REGEXP"){
					$query .= " AND ".$key." REGEXP ";
					$comma = "";
						for($i=0; $i<count($arrvalue); $i++){
						  $query .= $comma.":".$thiskey."".$i;       // :p0, :p1, ...
						  $comma = "|";
						  $arrbind[$thiskey.$i] = $arrvalue[$i];
						}
					$query .= "";
				}
				else if($symbol=="NOTIN"){
						$query .= " AND ".$key." NOT IN (";
						$comma = "";
							for($i=0; $i<count($arrvalue); $i++){
							  $query .= $comma.":".$thiskey."".$i;       // :p0, :p1, ...
							  $comma = " , ";
							  $arrbind[$thiskey.$i] = $arrvalue[$i];
							}
						$query .= ")";
				}
				else{
					$query .= " AND ".$key." IN (";
					$comma = "";
						for($i=0; $i<count($arrvalue); $i++){
						  $query .= $comma.":".$thiskey."".$i;       // :p0, :p1, ...
						  $comma = " , ";
						  $arrbind[$thiskey.$i] = $arrvalue[$i];
						}
					$query .= ")";
				}
			}
		
		$query .= " {$group_by} ";
		$query .= " {$order_by} ";
			
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->execute($arrbind);
		
		$count = $stmt->rowCount();
		$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);
			
		$arr = array("count"=>"$count", "row"=>$row, "query"=>$query);
			
		return $arr;
	}
	
	public function getAllGroupByHTScode($arr_td, $group_by = " group by bid.invID, bid.shipmentpriceID, bid.BICID, bid.group_number, sgc.garmentID, bid.ht_code ", $order_by = "" ){
		$arrbind = array();
		$query = "SELECT min(bih.BIHID) as linkID2, bid.invID, bi.invoice_no, sp.Orderno as orderno,
						sp.BuyerPO as buyerpo, bid.BICID, bid.shipmentpriceID, 
						sp.Shipdate as shippeddate, bid.group_number, bid.fob_price, GROUP_CONCAT(DISTINCT g.styleNo separator ', ') as styleno, group_concat(distinct bih.ht_code separator ', ') as hts_code, 
						(SELECT sum(bid2.qty) 
						FROM tblbuyer_invoice_detail bid2
						WHERE bid2.shipmentpriceID = bid.shipmentpriceID AND bid2.del=0 AND bid2.group_number>0) as qty, 
						GROUP_CONCAT(distinct bih.shipping_marking) as shipping_marking, u.Description as unit, sgc.garmentID
				FROM `tblbuyer_invoice_detail` bid 
				INNER JOIN tblbuyer_invoice bi ON bi.ID = bid.invID
				LEFT JOIN tblbuyer_invoice_category bic ON bic.invID = bi.ID 
				INNER JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
				LEFT JOIN tblunit u ON u.ID = sp.uom 
				INNER JOIN tblship_group_color sgc ON sgc.shipmentpriceID = bid.shipmentpriceID 
													AND sgc.group_number = bid.group_number
													AND sgc.statusID = 1
				INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
				INNER JOIN tblbuyer_invoice_hts bih ON bih.invID = bid.invID 
													AND bih.BICID = bid.BICID 
													AND bih.shipmentpriceID = bid.shipmentpriceID 
													AND bih.garmentID = sgc.garmentID
				WHERE 1=1 AND bid.del=0  AND bid.group_number>0
				";//AND bid.invID = 6982  //BINV24001425
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
			
		$arr = array("count"=>"$count", "row"=>$row, "query"=>$query);
			
		return $arr;
		
	}
	
	public function getAllLogisticTCByArr($arr_td, $group_by="group by tbl.invID, tbl.shipmentpriceID, tbl.hts_code, tbl.fob_price", $order_by=""){
		$arrbind = array();
		$query = "SELECT group_concat(distinct grpID) as grpID, replace(group_concat(distinct grpID), ',','_') as linkID2, 
						invID, invoice_no, group_concat(distinct BICID) as BICID, bic_option, shipmentpriceID, orderno, buyerpo, styleno, 
						(count_sgc) as count_sgc, GROUP_CONCAT(DISTINCT group_number) as group_number, hts_code, GROUP_CONCAT(DISTINCT shipping_marking) as shipping_marking, fob_price, sum(qty) as qty, unit, hts_code, sum(count_shipping_remark) as count_shipping_remark, sum(count_hts) as count_hts, (count_bih) as count_bih, GROUP_CONCAT(DISTINCT BIHID) as BIHID,
						GROUP_CONCAT(DISTINCT CIHID) as CIHID, shipdate

				FROM (SELECT group_concat(distinct bid.ID) as grpID, bi.ID as invID, bi.invoice_no, bid.BICID, bic.invoice_no as bic_option,  bid.shipmentpriceID, sp.Orderno as orderno, sp.BuyerPO as buyerpo, sp.Shipdate as shipdate, g.styleNo as styleno, count(DISTINCT sgc.GCID) as count_sgc,
				 
				 group_concat(DISTINCT bid.group_number) as group_number,
				 GROUP_CONCAT(DISTINCT trim(bih.ht_code)) as hts_code,
				 GROUP_CONCAT(DISTINCT trim(bih.shipping_marking)) as shipping_marking,
				 bid.fob_price, bid.qty, u.Description as unit,
				 
				 count(DISTINCT bih.shipping_marking)  as count_shipping_remark,
				 count(DISTINCT bih.ht_code) as count_hts,
				 count(DISTINCT bih.BIHID) as count_bih,
				 GROUP_CONCAT(DISTINCT bih.BIHID) as BIHID,
				 (SELECT GROUP_CONCAT(DISTINCT cih.CIHID)
				FROM `tblcarton_inv_head` cih 
				INNER JOIN tblcarton_inv_detail cid ON cid.CIHID = cih.CIHID
				WHERE cih.invID = bid.invID AND cih.BICID = bid.BICID AND cih.del=0 AND cih.shipmentpriceID = bid.shipmentpriceID AND cid.group_number = bid.group_number AND cid.del=0) as CIHID
				 
				FROM `tblbuyer_invoice_detail` bid 
				LEFT JOIN tblbuyer_invoice_category bic ON bic.BICID = bid.BICID
				LEFT JOIN tblship_group_color sgc on sgc.shipmentpriceID = bid.shipmentpriceID 
													AND sgc.group_number = bid.group_number
													AND sgc.statusID = 1
				LEFT JOIN tblgarment g ON g.garmentID = sgc.garmentID
				LEFT JOIN tblbuyer_invoice_hts bih ON bih.invID = bid.invID 
													AND bih.BICID = bid.BICID 
													AND bih.shipmentpriceID = bid.shipmentpriceID 
													AND bih.garmentID = sgc.garmentID 
													
				LEFT JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
				LEFT JOIN tblunit u ON u.ID = sp.uom
				LEFT JOIN tblbuyer_invoice bi ON bi.ID = bid.invID 
				WHERE  bid.del=0 AND bid.group_number>0  AND bid.qty>0
				 -- AND bi.invoice_no = 'BINV24001382' 
				
				";//bi.invoice_date >='2024-01-01' AND BINV24001385, BINV24001464, BINV24001435
		
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
	
		$query .= " group by bid.invID, bid.BICID, bid.shipmentpriceID, bid.group_number
				
				order by bid.invID) as tbl";
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
	
}

?>