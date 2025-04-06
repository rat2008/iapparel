<?php 
class tblcarton_inv_payment_detail{
	private $conn;
	private $table_name  = "tblcarton_inv_payment_detail";
	private $handle_misc = "";
	
	// object properties
	public $CIDID;
	public $CIHID;
	public $shipmentpriceID;
	public $group_number;
	public $size_name;
	public $qty = 0;
	public $del = 0;
	
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
						CIDID=:CIDID, shipmentpriceID=:shipmentpriceID, CIHID=:CIHID, group_number=:group_number, size_name=:size_name, qty=:qty
					";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		$this->CIDID = $this->handle_misc->funcMaxID($this->table_name, "CIDID");
		
		// bind values
		$stmt->bindParam(":CIDID", $this->CIDID);
		$stmt->bindParam(":CIHID", $this->CIHID);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":group_number", $this->group_number);
		$stmt->bindParam(":size_name", $this->size_name);
		$stmt->bindParam(":qty", $this->qty);
		
		// execute query
		if($stmt->execute()){
			return $this->CIDID;
		}
		
		return false;
	}
	
	function update(){
		// query to insert record
		$query = "UPDATE
					" . $this->table_name . "
					SET
						 shipmentpriceID=:shipmentpriceID, group_number=:group_number, size_name=:size_name, qty=:qty, del=:del
					WHERE CIDID=:CIDID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		// bind values
		$stmt->bindParam(":CIDID", $this->CIDID);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":group_number", $this->group_number);
		$stmt->bindParam(":size_name", $this->size_name);
		$stmt->bindParam(":qty", $this->qty);
		$stmt->bindParam(":del", $this->del);
		
		// execute query
		if($stmt->execute()){
			return $this->CIDID;
		}
		
		return false;
	}
	
	function remove(){
		// query to insert record
		$query = "UPDATE
					" . $this->table_name . "
					SET
						del=:del
					WHERE CIDID=:CIDID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		$stmt->bindParam(":CIDID", $this->CIDID);
		$stmt->bindParam(":del", "1");
		
		// execute query
		if($stmt->execute()){
			return $this->CIHID;
		}
		
		return false;
	}
	
	function checkAndRemove(){
		// query to insert record
		$query = "UPDATE
					" . $this->table_name . "
					SET
						del='1'
					WHERE CIDID NOT IN (".$this->CIDID.") AND CIHID=:CIHID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":CIHID", $this->CIHID);
		//$stmt->bindParam(":del", "1");
		
		// execute query
		if($stmt->execute()){
			return true;
		}
		
		return false;
	}
	
	function checkCartonDetailDataExist(){
		// query to check record
		$query = "SELECT CIDID 
				  FROM ".$this->table_name." 
				  WHERE CIHID=:CIHID AND group_number=:group_number AND size_name=:size_name limit 1";
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":CIHID", $this->CIHID);
		$stmt->bindParam(":group_number", $this->group_number);
		$stmt->bindParam(":size_name", $this->size_name);
		$stmt->execute();
		
		//$count = $stmt->rowCount();
		$row_cid = $stmt->fetch(PDO::FETCH_ASSOC);
			$CIDID = $row_cid["CIDID"];
			$CIDID = ($CIDID==""? 0: $CIDID);
		
		return $CIDID;
	}
	
}
?>