<?php 
class tblbuyer_invoice_payment_cost_head{ 
private $conn;
private $table_name = "tblbuyer_invoice_payment_cost_head";
private $handle_misc;

// object properties
public $INVCHID;
public $invID;
public $shipmentpriceID;
public $colorID;
public $item_desc;
public $del = 0;
public $delBy = NULL;
public $delDate = NULL;

// constructor with $conn as database connection
public function __construct($conn, $handle_misc){
    $this->conn = $conn;
    $this->handle_misc = $handle_misc;
	$this->handle_misc->setConnection($this->conn);
}

public function create(){
    $query = ' INSERT INTO '.$this->table_name.' SET 
    INVCHID=:INVCHID, invID=:invID, shipmentpriceID=:shipmentpriceID, colorID=:colorID, item_desc=:item_desc, del=:del, delBy=:delBy, delDate=:delDate';

    // prepare query
    $stmt = $this->conn->prepare($query);
    $this->INVCHID = $this->handle_misc->funcMaxID($this->table_name, "INVCHID");

    // bind values
    $stmt->bindParam(":INVCHID", $this->INVCHID);
    $stmt->bindParam(":invID", $this->invID);
    $stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
    $stmt->bindParam(":colorID", $this->colorID);
	$stmt->bindParam(":item_desc", $this->item_desc);
    $stmt->bindParam(":del", $this->del);
    $stmt->bindParam(":delBy", $this->delBy);
    $stmt->bindParam(":delDate", $this->delDate);
    $stmt->execute();

}

public function update($arr_td){
	$arrbind = array();
    $query = "UPDATE tblbuyer_invoice_payment_cost_head 	SET ";
	foreach($arr_td as $key => $value){
		$query .= "".$key."=:".$key.",";
	}
	$query = rtrim($query, ",");
	$query .= " WHERE INVCHID=:INVCHID";
    $stmt = $this->conn->prepare($query);
    $stmt->execute($arr_td);

}

public function getAllByArr($arr_td, $group_by="", $order_by=""){
	$arrbind = array();

	$query = "SELECT * 
					FROM ".$this->table_name."  
					WHERE 1=1 ";
	foreach($arr_td as $key => $value){
		$arrvalue = explode(",", $value);
		$arrkey = explode("!!", $key);

		$symbol = "="; $thisnum = "";
		if(count($arrkey)>1){
			$key = $arrkey[0];
			$symbol = $arrkey[1];
			$thisnum = (isset($arrkey[2])? $arrkey[2]: "");		}//-- End if --//

		$thiskey = $key;
		if (strpos($key, ".") !== false) {
			list($prefix, $thiskey) = explode(".", $key);
		}
		$thiskey = rtrim($thiskey, ")");
		if(count($arrvalue)==1 && $symbol!="REGEXP" && $symbol!="NOTIN"){
			$query .= " AND ".$key." {$symbol} :".$thiskey."{$thisnum}";
			$arrbind[$thiskey.$thisnum] = $value;
		}
		else if($symbol=="REGEXP"){
			$query .= " AND ".$key." REGEXP ";
			$comma = "";
			for($i=0; $i<count($arrvalue); $i++){
				$query .= $comma.":".$thiskey."".$i; 
				$comma = "|"; 
				$arrbind[$thiskey.$i] = $arrvalue[$i]; 
			}
			$query .= "";
		}
		else if($symbol=="NOTIN"){
			$query .= " AND ".$key." NOT IN (";
			$comma = "";
			for($i=0; $i<count($arrvalue); $i++){
				$query .= $comma.":".$thiskey."".$i; 
				$comma = " , "; 
				$arrbind[$thiskey.$i] = $arrvalue[$i]; 
			} 
			$query .= ")";
		}
		else{
			$query .= " AND ".$key." IN (";
			$comma = "";
			for($i=0; $i<count($arrvalue); $i++){
				$query .= $comma.":".$thiskey."".$i;  
				$comma = " , ";  
				$arrbind[$thiskey.$i] = $arrvalue[$i];  
			}  
			$query .= ")";
		}
	}//-- end for

  $query .= " {$group_by} "; 
  $query .= " {$order_by}"; 

	// prepare query
	$stmt = $this->conn->prepare($query);
	$stmt->execute($arrbind);

	$count = $stmt->rowCount();
	$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);

	$arr = array("count"=>"$count", "row"=>$row);
	return $arr;
}// end getAllByArr

} // end class

?>