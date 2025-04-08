<?php 
class tblbuyer_invoice_payment_cost_detail{ 
private $conn;
private $table_name = "tblbuyer_invoice_payment_cost_detail";
private $handle_misc;

// object properties
public $ID;
public $INVCHID;
public $item_desc = NULL;
public $qty = 0;
public $unitprice = 0.000;
public $ctn_qty = 0;
public $total_nnw = 0.000;
public $del = 0;
public $delby = NULL;
public $delDate = NULL;

// constructor with $conn as database connection
public function __construct($conn, $handle_misc){
    $this->conn = $conn;
    $this->handle_misc = $handle_misc;
}

public function create(){
    $query = ' INSERT INTO '.$this->table_name.' SET 
    ID=:ID, INVCHID=:INVCHID, item_desc=:item_desc, qty=:qty, unitprice=:unitprice, ctn_qty=:ctn_qty, total_nnw=:total_nnw, del=:del, delby=:delby, delDate=:delDate';

    // prepare query
    $stmt = $this->conn->prepare($query);
    $this->ID = $this->handle_misc->funcMaxID($this->table_name, "ID");

    // bind values
    $stmt->bindParam(":ID", $this->ID);
    $stmt->bindParam(":INVCHID", $this->INVCHID);
    $stmt->bindParam(":item_desc", $this->item_desc);
    $stmt->bindParam(":qty", $this->qty);
    $stmt->bindParam(":unitprice", $this->unitprice);
    $stmt->bindParam(":ctn_qty", $this->ctn_qty);
    $stmt->bindParam(":total_nnw", $this->total_nnw);
    $stmt->bindParam(":del", $this->del);
    $stmt->bindParam(":delby", $this->delby);
    $stmt->bindParam(":delDate", $this->delDate);

    if (!$stmt->execute()) {
        // Log or display the error information
        $errorInfo = $stmt->errorInfo();
        echo "Error Code: " . $errorInfo[0] . "<br>";
        echo "Error Message: " . $errorInfo[2] . "<br>";
        throw new Exception("Database error: " . $errorInfo[2]);
    }

	
}

public function update($arr_td){
	$arrbind = array();
    $query = "UPDATE tblbuyer_invoice_payment_cost_detail 	SET ";
	foreach($arr_td as $key => $value){
		$query .= "".$key."=:".$key.",";
	}
	$query = rtrim($query, ",");
	$query .= " WHERE ID=:ID";
    $stmt = $this->conn->prepare($query);
	
	if (!$stmt->execute($arr_td)) {
        // Log or display the error information
        $errorInfo = $stmt->errorInfo();
        echo "Error Code: " . $errorInfo[0] . "<br>";
        echo "Error Message: " . $errorInfo[2] . "<br>";
        throw new Exception("Database error: " . $errorInfo[2]);
    }

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