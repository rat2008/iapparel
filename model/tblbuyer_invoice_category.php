<?php 
class tblbuyer_invoice_category{
	private $conn;
	private $table_name  = "tblbuyer_invoice_category";
	private $handle_misc = "";
	private $handle_tblcarton_inv_head = "";
	
	// object properties
	public $BICID;
	public $invID;
	public $options;
	public $invoice_no = NULL;
	public $co_number = NULL;
	public $co_date = NULL;
	public $custom_no = NULL;
	public $custom_date = NULL;
	public $custom_procedure = NULL;
	public $del = 0;

	
	// constructor with $db as database connection
    public function __construct($conn){
        $this->conn = $conn;
    }
	
	public function setMisc($handle_misc){
		$this->handle_misc = $handle_misc;
		$this->handle_misc->setConnection($this->conn);
	}
	
	public function setTblcarton_inv_head($handle_tblcarton_inv_head){
		$this->handle_tblcarton_inv_head = $handle_tblcarton_inv_head;
	}
	
	function create(){
		// query to insert record
		$query = "INSERT INTO
					" . $this->table_name . "
					SET
					BICID=:BICID, invID=:invID, options=:options, invoice_no=:invoice_no, 
					co_number=:co_number, co_date=:co_date, custom_no=:custom_no, custom_date=:custom_date,
					custom_procedure=:custom_procedure, del=:del";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		$this->BICID = $this->handle_misc->funcMaxID($this->table_name, "BICID");
		
		$this->co_number        = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->co_number);
		$this->custom_no        = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->custom_no);
		$this->custom_procedure = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->custom_procedure);
		
		// bind values
		$stmt->bindParam(":BICID", $this->BICID);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->bindParam(":options", $this->options);
		$stmt->bindParam(":invoice_no", $this->invoice_no);
		$stmt->bindParam(":co_number", $this->co_number);
		$stmt->bindParam(":co_date", $this->co_date);
		$stmt->bindParam(":custom_no", $this->custom_no);
		$stmt->bindParam(":custom_date", $this->custom_date);
		$stmt->bindParam(":custom_procedure", $this->custom_procedure);
		$stmt->bindParam(":del", $this->del);
		
		// execute query
		if($stmt->execute()){
			return $this->BICID;
		}
		
		return false;
	}
	
	function update(){
		// query to update record
		$query = "UPDATE
					" . $this->table_name . "
					SET
					invID=:invID, options=:options, invoice_no=:invoice_no, 
					co_number=:co_number, co_date=:co_date, custom_no=:custom_no, custom_date=:custom_date,
					custom_procedure=:custom_procedure, del=:del
				  WHERE BICID=:BICID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		$this->co_number        = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->co_number);
		$this->custom_no        = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->custom_no);
		$this->custom_procedure = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->custom_procedure);
		
		// bind values
		$stmt->bindParam(":BICID", $this->BICID);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->bindParam(":options", $this->options);
		$stmt->bindParam(":invoice_no", $this->invoice_no);
		$stmt->bindParam(":co_number", $this->co_number);
		$stmt->bindParam(":co_date", $this->co_date);
		$stmt->bindParam(":custom_no", $this->custom_no);
		$stmt->bindParam(":custom_date", $this->custom_date);
		$stmt->bindParam(":custom_procedure", $this->custom_procedure);
		$stmt->bindParam(":del", $this->del);
		
		
		// execute query
		if($stmt->execute()){
			return $this->BICID;
		}
		
		return false;
	}
	
	function checkCategoryExistNotDel(){
		$sql = "SELECT BICID, invoice_no
				FROM ".$this->table_name." bic 
				WHERE invID=:invID AND options=:options AND del='0'";
		$stmt = $this->conn->prepare($sql);
		
		$stmt->bindParam(":invID", $this->invID);
		$stmt->bindParam(":options", $this->options);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$BICID = $row["BICID"];
		
		$arr_value = array("isExist"=>$stmt->rowCount(), "BICID"=>$BICID);
		
		return $arr_value;
	}
	
	function deleteRow(){
		
		$query = "UPDATE
					" . $this->table_name . "
					SET
					del='1'
				  WHERE BICID=:BICID";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":BICID", $this->BICID);
		
		if($stmt->execute()){
			return $this->BICID;
		}
		
		return false;
	}
	
	function deleteAllRelatedRow(){
		$sql = "SELECT bid.shipmentpriceID as spID, bid.invID, bid.BICID
				FROM tblbuyer_invoice_detail bid 
				WHERE bid.del=0 AND bid.BICID=:BICID
				group by bid.shipmentpriceID";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(":BICID", $this->BICID);
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$sqldel = "UPDATE tblbuyer_invoice_detail SET del=1 WHERE shipmentpriceID='$spID' AND invID='$invID' AND BICID='$BICID'";
			$deletesql = $this->conn->prepare($sqldel);
			$deletesql->execute();
			
			$sql = "SELECT * FROM tblbuyer_invoice_detail WHERE shipmentpriceID='$spID' AND invID='$invID' AND group_number>0 AND del=0";
			$stmt_chk = $this->conn->prepare($sql);
			$stmt_chk->execute();
			$count_sp = $stmt_chk->rowCount();
			
			if($count_sp==0){// remove Buyer PO other charge 
				$deletesql = $this->conn->prepare("UPDATE tblbuyer_invoice_detail SET del=1 
											WHERE shipmentpriceID='$spID' AND invID='$invID'");
				$deletesql->execute();
			}
			
			$this->handle_tblcarton_inv_head->shipmentpriceID = $spID;
			$this->handle_tblcarton_inv_head->invID = $invID;
			$this->handle_tblcarton_inv_head->BICID = $BICID;
			$this->handle_tblcarton_inv_head->removeByShipmentID();
		}
		
		$this->deleteRow();
	}
}

?>