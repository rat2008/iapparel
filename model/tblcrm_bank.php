<?php 
class tblcrm_bank{
	private $conn;
	private $table_name  = "tblcrm_bank";
	private $handle_misc = "";
	private $lang = "EN";
	
	// object properties
	public $BID;
	public $crmType;
	public $crmID;
	public $bank_account_no;
	public $beneficiary_name;
	public $bank_name;
	public $bank_address;
	public $swift_code;
	public $countryID = 0;
	public $IsDefault = 0;
	public $del = 0;
	
	// constructor with $db as database connection
    public function __construct($conn, $handle_misc="", $lang="EN"){
        $this->conn = $conn;
        $this->handle_misc = $handle_misc;
        $this->lang = $lang;
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
					BID=:BID, crmType=:crmType, crmID=:crmID, bank_account_no=:bank_account_no, beneficiary_name=:beneficiary_name,
					bank_name=:bank_name, bank_address=:bank_address, swift_code=:swift_code,
					countryID=:countryID, IsDefault=:IsDefault";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		$this->BID = $this->handle_misc->funcMaxID($this->table_name, "BID");
		
		$this->bank_account_no  = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->bank_account_no);
		$this->beneficiary_name = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->beneficiary_name);
		$this->bank_name        = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->bank_name);
		$this->bank_address     = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->bank_address);
		$this->swift_code       = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->swift_code);
		
		// bind values
		$stmt->bindParam(":BID", $this->BID);
		$stmt->bindParam(":crmType", $this->crmType);
		$stmt->bindParam(":crmID", $this->crmID);
		$stmt->bindParam(":bank_account_no", $this->bank_account_no);
		$stmt->bindParam(":beneficiary_name", $this->beneficiary_name);
		$stmt->bindParam(":bank_name", $this->bank_name);
		$stmt->bindParam(":bank_address", $this->bank_address);
		$stmt->bindParam(":swift_code", $this->swift_code);
		$stmt->bindParam(":countryID", $this->countryID);
		$stmt->bindParam(":IsDefault", $this->IsDefault);
		
		// execute query
		if($stmt->execute()){
			return $this->BID;
		}
		
		return false;
	}
	
	function update(){
		// query to update record
		$query = "UPDATE
					" . $this->table_name . "
					SET
					crmType=:crmType, crmID=:crmID, bank_account_no=:bank_account_no, beneficiary_name=:beneficiary_name,
					bank_name=:bank_name, bank_address=:bank_address, swift_code=:swift_code,
					countryID=:countryID, IsDefault=:IsDefault
					WHERE BID=:BID";
		// prepare query
		$stmt = $this->conn->prepare($query);

		$this->bank_account_no  = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->bank_account_no);
		$this->beneficiary_name = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->beneficiary_name);
		$this->bank_name        = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->bank_name);
		$this->bank_address     = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->bank_address);
		$this->swift_code       = $this->handle_misc->funcConvertSpecialCharWOUpperCase($this->swift_code);
		
		// bind values
		$stmt->bindParam(":BID", $this->BID);
		$stmt->bindParam(":crmType", $this->crmType);
		$stmt->bindParam(":crmID", $this->crmID);
		$stmt->bindParam(":bank_account_no", $this->bank_account_no);
		$stmt->bindParam(":beneficiary_name", $this->beneficiary_name);
		$stmt->bindParam(":bank_name", $this->bank_name);
		$stmt->bindParam(":bank_address", $this->bank_address);
		$stmt->bindParam(":swift_code", $this->swift_code);
		$stmt->bindParam(":countryID", $this->countryID);
		$stmt->bindParam(":IsDefault", $this->IsDefault);
		
		// execute query
		if($stmt->execute()){
			return $this->BID;
		}
		
		return false;
	}
	
	function updateAddress(){
		// query to update record
		$query = "UPDATE
					" . $this->table_name . "
					SET
					bank_address=:bank_address
					WHERE BID=:BID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		// bind values
		$stmt->bindParam(":BID", $this->BID);
		$stmt->bindParam(":bank_address", $this->bank_address);
		
		// execute query
		if($stmt->execute()){
			return $this->BID;
		}
		
		return false;
	}
	
	function remove(){
		// query to insert record
		$query = "UPDATE
					" . $this->table_name . "
					SET
					del='1'
					WHERE BID=:BID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		// bind values
		$stmt->bindParam(":BID", $this->BID);
		
		// execute query
		if($stmt->execute()){
			return $this->BID;
		}
		
		return false;
	}
	
	function readOnlyOneCRM(){
		$query = "SELECT crmb.BID, crmb.crmType, crmb.crmID, crmb.bank_account_no, crmb.beneficiary_name,
							crmb.bank_name, crmb.bank_address, crmb.swift_code, crmb.countryID, crmb.IsDefault, 
							c.Description as country, c.countryCode
					FROM " . $this->table_name . " crmb 
					LEFT JOIN tblcountry c ON c.ID = crmb.countryID
					WHERE crmb.crmID=:crmID AND crmb.crmType=:crmType AND del=0 AND crmb.crmID!=''";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		// bind values
		$stmt->bindParam(":crmID", $this->crmID);
		$stmt->bindParam(":crmType", $this->crmType);
		
		// execute query
		$stmt->execute();
		
		return $stmt;
		
	}
	
	function readAll(){
		$query = "SELECT crmb.BID, crmb.crmType, crmb.crmID, crmb.bank_account_no, crmb.beneficiary_name,
							crmb.bank_name, crmb.bank_address, crmb.swift_code, crmb.countryID, crmb.IsDefault, 
							c.Description as country, c.countryCode
					FROM " . $this->table_name . " crmb 
					LEFT JOIN tblcountry c ON c.ID = crmb.countryID
					WHERE del=0";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		// bind values
		// $stmt->bindParam(":crmID", $this->crmID);
		// $stmt->bindParam(":crmType", $this->crmType);
		
		// execute query
		$stmt->execute();
		
		return $stmt;
	}
	
	function readOne(){
		$query = "SELECT crmb.BID, crmb.crmType, crmb.crmID, crmb.bank_account_no, crmb.beneficiary_name,
							crmb.bank_name, crmb.bank_address, crmb.swift_code, crmb.countryID, crmb.IsDefault, 
							c.Description as country, c.countryCode
					FROM " . $this->table_name . " crmb 
					LEFT JOIN tblcountry c ON c.ID = crmb.countryID
					WHERE crmb.BID=:BID AND del=0";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		// bind values
		$stmt->bindParam(":BID", $this->BID);
		
		// execute query
		$stmt->execute();
		
		return $stmt;
		
	}
	
	
	public function getAllByArr($arr_td, $group_by="", $order_by=" order by TRIM(crmb.bank_name) asc"){
		$arrbind = array();
		
		$query = "SELECT * 
					FROM ".$this->table_name." crmb 
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
			
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->execute($arrbind);
		
		$count = $stmt->rowCount();
		$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);
			
		$arr = array("count"=>"$count", "row"=>$row);
			
		return $arr;
	}
	
	public function getSelectOption($selected_id, $arr_td = array("crmb.del"=>0, "crmb.crmType"=>3)){
		$lang = $this->lang;
		$path = "../lang/{$lang}.php";
		$path2 = "../../lang/{$lang}.php";
		$chk = file_exists($path);
		$url = ($chk==1? $path: $path2);
		include($url);
		$isSelect = 0;
		
		$arrsm = $this->getAllByArr($arr_td);
		$option_html = '<option value="0">-- '.$hdlang["Select"].' --</option>';
		for($i=0;$i<count($arrsm["row"]);$i++){
			extract($arrsm["row"][$i]);
			
			$selected = ($BID==$selected_id? "selected": "");
			$isSelect = ($BID==$selected_id? 1: $isSelect);
			$option_html .= '<option value="'.$BID.'" '.$selected.'>'.$bank_name.'</option>';
		}
		
		$arr = array("option_html"=>$option_html, "isSelect"=>$isSelect);
		
		return $arr;
	}

	
	
}

?>