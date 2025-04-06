<?php 
class tblbuyer_invoice_charge_option{
	private $conn;
	private $table_name  = "tblbuyer_invoice_charge_option";
	private $handle_misc = "";
	
	// object properties
	public $ID;
	public $Description;
	public $type;
	public $statusID = 1;
	
	// constructor with $conn as database connection
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
					ID=:ID, Description=:Description, type=:type, statusID=:statusID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		$this->ID = $this->handle_misc->funcMaxID($this->table_name, "ID");
		
		// bind values
		$stmt->bindParam(":ID", $this->ID);
		$stmt->bindParam(":Description", $this->Description);
		$stmt->bindParam(":type", $this->type);
		$stmt->bindParam(":statusID", $this->statusID);
		
		// execute query
		if($stmt->execute()){
			return $this->ID;
		}
		
		return false;
	}
	
	function update(){
		$query = "UPDATE ".$this->table_name." SET statusID=:statusID 
					WHERE ID=:ID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":statusID", $this->statusID);
		$stmt->bindParam(":ID", $this->ID);
		$stmt->execute();
	}
	
	function checkChargeOptionExist(){
		$query = "SELECT ID, Description
					FROM tblbuyer_invoice_charge_option 
					WHERE Description=:Description AND type=:type";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		$this->Description = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->Description);
		
		$stmt->bindParam(":Description", $this->Description);
		$stmt->bindParam(":type", $this->type);
		$stmt->execute();
		
		$count = $stmt->rowCount();
		
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$ID  = $row["ID"];
		
		if($count>0){
			$this->ID = $ID;
			$this->statusID = 1;
			$this->update();//update to active if exist
		}
		
		return array("count"=>$count, "ID"=>$ID);
	}
	
	function getChargeOptionList(){
		$arr_value = array();
		
		$sql = "SELECT ID, Description 
				FROM tblbuyer_invoice_charge_option 
				WHERE (statusID=1 AND type='".$this->type."') OR ID='".$this->ID."'";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$arr = array("ID"=>$ID, "Description"=>$Description);
			$arr_value[] = $arr;
			
		}//-- End While --//
		
		return $arr_value;
	}
	
	function getChargeOptionOnlyOne(){
		$arr_value = array();
		
		$sql = "SELECT ID, Description 
				FROM tblbuyer_invoice_charge_option 
				WHERE ID='".$this->ID."'";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$arr = array("ID"=>$ID, "Description"=>$Description);
			$arr_value[] = $arr;
			
		}//-- End While --//
		
		return $arr_value;
	}
	
}
?>