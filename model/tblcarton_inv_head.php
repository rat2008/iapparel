<?php 
class tblcarton_inv_head{
	private $conn;
	private $table_name  = "tblcarton_inv_head";
	private $handle_misc = "";
	
	// object properties
	public $CIHID;
	public $invID;
	public $BICID = 0;
	public $shipmentpriceID;
	public $PID;
	public $SKU = NULL;
	public $prepack_name = NULL;
	public $masterID = NULL;
	public $start;
	public $end_num;
	public $is_last = 0;
	public $total_ctn;
	public $mixID;
	public $blisterbag_qty = 1;
	public $total_qty_in_carton;
	public $net_net_weight;
	public $net_weight;
	public $gross_weight;
	public $weight_unitID = 44;//default KGS
	public $ext_length;
	public $ext_width;
	public $ext_height;
	public $ctn_unitID = 16;//default CM
	public $total_CBM;
	public $updatedBy;
	public $updatedDate;
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
						CIHID=:CIHID, invID=:invID, BICID=:BICID, shipmentpriceID=:shipmentpriceID, PID=:PID, SKU=:SKU, blisterbag_qty=:blisterbag_qty,
						prepack_name=:prepack_name, start=:start, end_num=:end_num, is_last=:is_last, total_ctn=:total_ctn, mixID=:mixID,
						total_qty_in_carton=:total_qty_in_carton, net_net_weight=:net_net_weight, net_weight=:net_weight,
						gross_weight=:gross_weight, weight_unitID=:weight_unitID, ext_length=:ext_length, ext_width=:ext_width,
						ext_height=:ext_height, ctn_unitID=:ctn_unitID, total_CBM=:total_CBM, updatedBy=:updatedBy, updatedDate=:updatedDate, masterID=:masterID
					";
		// prepare query
		$stmt = $this->conn->prepare($query);
		
		$this->CIHID = $this->handle_misc->funcMaxID($this->table_name, "CIHID");
		$this->updatedDate = $this->handle_misc->TimeNow();
		
		// bind values
		$stmt->bindParam(":CIHID", $this->CIHID);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->bindParam(":BICID", $this->BICID);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":PID", $this->PID);
		$stmt->bindParam(":SKU", $this->SKU);
		$stmt->bindParam(":prepack_name", $this->prepack_name);
		$stmt->bindParam(":start", $this->start);
		$stmt->bindParam(":end_num", $this->end_num);
		$stmt->bindParam(":is_last", $this->is_last);
		$stmt->bindParam(":total_ctn", $this->total_ctn);
		$stmt->bindParam(":mixID", $this->mixID);
		$stmt->bindParam(":blisterbag_qty", $this->blisterbag_qty);
		$stmt->bindParam(":total_qty_in_carton", $this->total_qty_in_carton);
		$stmt->bindParam(":net_net_weight", $this->net_net_weight);
		$stmt->bindParam(":net_weight", $this->net_weight);
		$stmt->bindParam(":gross_weight", $this->gross_weight);
		$stmt->bindParam(":weight_unitID", $this->weight_unitID);
		$stmt->bindParam(":ext_length", $this->ext_length);
		$stmt->bindParam(":ext_width", $this->ext_width);
		$stmt->bindParam(":ext_height", $this->ext_height);
		$stmt->bindParam(":ctn_unitID", $this->ctn_unitID);
		$stmt->bindParam(":total_CBM", $this->total_CBM);
		$stmt->bindParam(":updatedBy", $this->updatedBy);
		$stmt->bindParam(":updatedDate", $this->updatedDate);
		$stmt->bindParam(":masterID", $this->masterID);
		
		// execute query
		if($stmt->execute()){
			return $this->CIHID;
		}
		
		return false;
		
	}
	
	function update(){
		// query to insert record
		$query = "UPDATE
					" . $this->table_name . "
					SET
						invID=:invID, shipmentpriceID=:shipmentpriceID, PID=:PID, SKU=:SKU, blisterbag_qty=:blisterbag_qty,
						prepack_name=:prepack_name, start=:start, end_num=:end_num, is_last=:is_last, total_ctn=:total_ctn, mixID=:mixID, masterID=:masterID,
						total_qty_in_carton=:total_qty_in_carton, net_net_weight=:net_net_weight, net_weight=:net_weight,
						gross_weight=:gross_weight, weight_unitID=:weight_unitID, ext_length=:ext_length, ext_width=:ext_width,
						ext_height=:ext_height, ctn_unitID=:ctn_unitID, total_CBM=:total_CBM, updatedBy=:updatedBy, updatedDate=:updatedDate, del='0'
					WHERE CIHID=:CIHID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		$this->updatedDate = $this->handle_misc->TimeNow();
		
		// bind values
		$stmt->bindParam(":CIHID", $this->CIHID);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":PID", $this->PID);
		$stmt->bindParam(":SKU", $this->SKU);
		$stmt->bindParam(":prepack_name", $this->prepack_name);
		$stmt->bindParam(":start", $this->start);
		$stmt->bindParam(":end_num", $this->end_num);
		$stmt->bindParam(":is_last", $this->is_last);
		$stmt->bindParam(":total_ctn", $this->total_ctn);
		$stmt->bindParam(":mixID", $this->mixID);
		$stmt->bindParam(":masterID", $this->masterID);
		$stmt->bindParam(":blisterbag_qty", $this->blisterbag_qty);
		$stmt->bindParam(":total_qty_in_carton", $this->total_qty_in_carton);
		$stmt->bindParam(":net_net_weight", $this->net_net_weight);
		$stmt->bindParam(":net_weight", $this->net_weight);
		$stmt->bindParam(":gross_weight", $this->gross_weight);
		$stmt->bindParam(":weight_unitID", $this->weight_unitID);
		$stmt->bindParam(":ext_length", $this->ext_length);
		$stmt->bindParam(":ext_width", $this->ext_width);
		$stmt->bindParam(":ext_height", $this->ext_height);
		$stmt->bindParam(":ctn_unitID", $this->ctn_unitID);
		$stmt->bindParam(":total_CBM", $this->total_CBM);
		$stmt->bindParam(":updatedBy", $this->updatedBy);
		$stmt->bindParam(":updatedDate", $this->updatedDate);
		
		// execute query
		if($stmt->execute()){
			return $this->CIHID;
		}
		
		return false;
	}
	
	function remove(){
		// query to remove header record
		$query = "UPDATE
					" . $this->table_name . "
					SET
						del='1'
					WHERE CIHID=:CIHID";
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":CIHID", $this->CIHID);
		$stmt->execute();
		
		// query to remove detail record
		$query_d = "UPDATE
					tblcarton_inv_detail
					SET
						del='1'
					WHERE CIHID=:CIHID";
		// prepare query
		$stmt_d = $this->conn->prepare($query_d);
		$stmt_d->bindParam(":CIHID", $this->CIHID);
		$stmt_d->execute();
	}
	
	function removeByShipmentID(){
		// query to remove header & detail record by using shipmentpriceID & invID
		$query_d = "UPDATE
					tblcarton_inv_head cih
					LEFT JOIN tblcarton_inv_detail cid ON cih.CIHID = cid.CIHID
					SET
						cid.del='1', cih.del='1'
					WHERE cih.shipmentpriceID=:shipmentpriceID AND cih.invID=:invID AND cih.BICID=:BICID";
		// prepare query
		$stmt_d = $this->conn->prepare($query_d);
		$stmt_d->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt_d->bindParam(":invID", $this->invID);
		$stmt_d->bindParam(":BICID", $this->BICID);
		$stmt_d->execute();
		
	}
	
	function checkBuyerPOExist(){
		$query = "SELECT * 
					FROM ".$this->table_name." 
					WHERE shipmentpriceID=:shipmentpriceID AND invID=:invID AND BICID=:BICID AND del=0";
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->bindParam(":BICID", $this->BICID);
		$stmt->execute();
		$count = $stmt->rowCount();
		
		return $count;
	}
	
	function checkBuyerPOExistInOtherInvoice(){
		$query = "SELECT * 
					FROM ".$this->table_name." cih
					INNER JOIN tblbuyer_invoice bi ON bi.ID = cih.invID
					INNER JOIN tblbuyer_invoice_detail bid ON bid.invID = bi.ID AND bid.shipmentpriceID = cih.shipmentpriceID
					WHERE cih.shipmentpriceID=:shipmentpriceID AND cih.invID<>:invID 
					AND cih.del=0 AND bi.statusID NOT IN (6) AND bid.del=0 AND bid.valid=1 AND bid.group_number>0";
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->execute();
		$count = $stmt->rowCount();
		
		return $count;
	}
	
	function getMaxEndNumInOtherInvoice(){
		$query = "SELECT max(end_num) as end_num 
					FROM ".$this->table_name." 
					WHERE shipmentpriceID=:shipmentpriceID AND invID<>:invID AND del=0";
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->execute();
		
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$end_num = $row["end_num"];
			
		return $end_num;
	}
	
	function getMinStartNumInOtherInvoice(){
		$query = "SELECT min(start) as start 
					FROM ".$this->table_name." 
					WHERE shipmentpriceID=:shipmentpriceID AND invID<>:invID AND del=0";
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->execute();
		
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$start = $row["start"];
			
		return $start;
	}
	
	function checkOtherInvoiceCartonQty(){
		$count = 0;
		$query = "SELECT CIHID, start, end_num
					FROM ".$this->table_name." 
					WHERE shipmentpriceID=:shipmentpriceID AND invID<>:invID AND mixID=:mixID AND del=0";
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->bindParam(":mixID", $this->mixID);
		$stmt->execute();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$start = $row["start"];
			$end_num = $row["end_num"];
			
			$count += ($end_num - $start + 1);
		}
			
		return $count;
	}
	
	function checkExistingInvoiceCartonQty(){
		$count = 0; $first_start = 1; $end_num = "";
		$query = "SELECT CIHID, start, end_num
					FROM ".$this->table_name." 
					WHERE shipmentpriceID=:shipmentpriceID AND invID=:invID AND mixID=:mixID AND del=0";
		// prepare query
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":shipmentpriceID", $this->shipmentpriceID);
		$stmt->bindParam(":invID", $this->invID);
		$stmt->bindParam(":mixID", $this->mixID);
		$stmt->execute();
		
		$i = 0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$start = $row["start"];
			$end_num = $row["end_num"];
			
			$first_start = ($i==0? $start: $first_start);
			
			$count += ($end_num - $start + 1);
			$i++;
		}
		
		$arr_result = array("start"=>"$first_start", "end"=>"$end_num", "count"=>"$count");
		
		return $arr_result;
	}
	
	function getCartonInfoByArr($arr_td, $group_by="", $order_by=""){
		$arrbind = array();
		$query = "SELECT ifnull(sum(tbl.total_ctn),0) as total_ctn, ifnull(sum(tbl.gross_weight),0) as gross_weight, 
							ifnull(sum(tbl.net_weight),0) as net_weight
					FROM (SELECT cih.total_ctn, cih.gross_weight, cih.net_weight
					FROM `tblcarton_inv_head` cih 
					INNER JOIN tblcarton_inv_detail cid ON cid.CIHID = cih.CIHID
					WHERE 1=1 AND cih.del=0  AND cid.del=0 
					-- AND cih.invID = 6982 AND cih.BICID = 12591  AND cih.shipmentpriceID = 48249 AND cid.group_number = 1 
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
	
		$query .= " group by cih.CIHID) as tbl";
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