<?php
	include("../lock.php");
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	$sql = "SELECT table_name as tbl 
			FROM information_schema.tables
			WHERE table_schema = 'apparelezi_demo'";
	
	$tblname = "tblbuyer_invoice_payment_cost_detail";
	createTableClass($conn, $tblname);
	
	
function createTableClass($conn, $tblname){
	
	$arr_columns = array();
	$sql = "SELECT *
			FROM $tblname limit 1";
	// prepare query
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	for ($i = 0; $i < $stmt->columnCount(); $i++) {
			$col = $stmt->getColumnMeta($i);
			$name = $col['name'];
			$null = (isset($col['flags'][0])? $col['flags'][0]: "null");
			// $null = $col['Null'];
			
			$sqldef = "SELECT column_default 
						FROM information_schema.`COLUMNS` where table_name = '$tblname' and column_name = '$name' 
						order by column_default desc";
			// prepare query
			$stmtdef = $conn->prepare($sqldef);
			$stmtdef->execute();
			$drow_def = $stmtdef->fetch(PDO::FETCH_ASSOC);
				$column_default = $drow_def["column_default"];
			
			$grp = "$name:$null:$column_default";
			
			$arr_columns[] = $grp;
			
			// print_r($col); 
			// echo "[$i] <br/>";
			// echo "";
	}
	
	
	$html = "<?php \n";
	$html .= "class ".$tblname."{ \n";
	$html .= 'private $conn;';
	$html .= "\n";
	$html .= 'private $table_name = "'.$tblname.'";';
	$html .= "\n";
	$html .= 'private $handle_misc;';
	$html .= "\n";
	$html .= "\n";
	$html .= "// object properties";
	$html .= "\n";
	for($i=0;$i<count($arr_columns);$i++){
		list($name, $isNull, $default_value) = explode(":",$arr_columns[$i]);
		
		$equal_value = (strtoupper(trim($isNull))=="NULL"? ' = NULL':"");
		$equal_value = (trim($default_value)==""? $equal_value:' = '.$default_value);
		
		$html .= 'public $'.$name.$equal_value.';';
		$html .= "\n";
	}
	
	$html .= "\n";
	$html .= '// constructor with $conn as database connection';
	$html .= "\n";
	$html .= 'public function __construct($conn, $handle_misc){';
	$html .= "\n";
	$html .= "    ";
	$html .= '$this->conn = $conn;';
	$html .= "\n";
	$html .= "    ";
	$html .= '$this->handle_misc = $handle_misc;';
	$html .= "\n";
	$html .= '}';
	$html .= "\n";
	$html .= "\n";
	
	$html .= 'public function create(){';
	$html .= "\n";
	$html .= "    ";
	$html .= '$query = \' INSERT INTO \'.$this->table_name.\' SET ';
	$html .= "\n";
	$html .= "    ";
	
	$first_col = '';
	for($i=0;$i<count($arr_columns);$i++){
		list($name, $isNull, $default_value) = explode(":",$arr_columns[$i]);
		
		if($i==0){
			$html .= ''.$name.'=:'.$name;
			$first_col = $name;
		}
		else{
			$html .= ', '.$name.'=:'.$name;
		}
	}
	$html .= '\';';
	$html .= "\n";
	$html .= "\n";
	$html .= '    // prepare query';
	$html .= "\n";
	$html .= '    $stmt = $this->conn->prepare($query);';
	$html .= "\n";
	$html .= '    $this->'.$first_col.' = $this->handle_misc->funcMaxID($this->table_name, "'.$first_col.'");';
	$html .= "\n";
	$html .= "\n";
	$html .= "    // bind values";
	$html .= "\n";
	
	for($i=0;$i<count($arr_columns);$i++){
		list($name, $isNull, $default_value) = explode(":",$arr_columns[$i]);
		
		$html .= '    $stmt->bindParam(":'.$name.'", $this->'.$name.');';
		$html .= "\n";
	}
	$html .= '    $stmt->execute();';
	$html .= "\n";
	$html .= "\n";
	$html .= '}';// end create
	
	$html .= "\n";
	$html .= "\n";
	// $html .= 'public function update($'.$first_col.', $query_filter){';
	// $html .= "\n";
	// $html .= "\n";
	// $html .= '    ';
	// $html .= '$query = "UPDATE '.$tblname.' SET '.$first_col.'=\'$'.$first_col.'\' $query_filter WHERE '.$first_col.'=\'$'.$first_col.'\'";';
	// $html .= "\n";
	// $html .= '    $stmt = $this->conn->prepare($query);';
	// $html .= "\n";
	// $html .= '    $stmt->execute();';
	// $html .= "\n";
	// $html .= "\n";
	
	// $html .= '}';// end update
	$html .= 'public function update($arr_td){';
	$html .= "\n";
	$html .= '	$arrbind = array();';
	$html .= "\n";
	$html .= '    ';
	$html .= '$query = "UPDATE '.$tblname.' ';
	$html .= '	SET ";';
	$html .= "\n";
	$html .= '	foreach($arr_td as $key => $value){';
	$html .= "\n";
	$html .= '		$query .= "".$key."=:".$key.",";';
	$html .= "\n";
	$html .= '	}';
	$html .= "\n";
	$html .= '	$query = rtrim($query, ",");';
	$html .= "\n";
	$html .= '	$query .= " WHERE '.$first_col.'=:'.$first_col.'";';
	
	$html .= "\n";
	$html .= '    $stmt = $this->conn->prepare($query);';
	$html .= "\n";
	$html .= '    $stmt->execute($arr_td);';
	$html .= "\n";
	$html .= "\n";
	
	$html .= '}';// end update
	
	$html .= "\n";
	$html .= "\n";
	$html .= 'public function getAllByArr($arr_td, $group_by="", $order_by=""){';
	$html .= "\n";
	$html .= '	$arrbind = array();';
	$html .= "\n";
	$html .= "\n";
	$html .= '	$query = "SELECT * 
					FROM ".$this->table_name."  
					WHERE 1=1 ";';
	$html .= "\n";
	$html .= '	foreach($arr_td as $key => $value){';
	$html .= "\n";
	$html .= '		$arrvalue = explode(",", $value);';
	$html .= "\n";
	$html .= '		$arrkey = explode("!!", $key);';
	$html .= "\n";
	$html .= "\n";
	$html .= '		$symbol = "="; $thisnum = "";';
	$html .= "\n";
	$html .= '		if(count($arrkey)>1){';
	$html .= "\n";
	$html .= '			$key = $arrkey[0];';
	$html .= "\n";
	$html .= '			$symbol = $arrkey[1];';
	$html .= "\n";
	$html .= '			$thisnum = (isset($arrkey[2])? $arrkey[2]: "");';
	$html .= '		}//-- End if --//';
	$html .= "\n";
	$html .= "\n";
	$html .= '		$thiskey = $key;';
	$html .= "\n";
	$html .= '		if (strpos($key, ".") !== false) {';
	$html .= "\n";
	$html .= '			list($prefix, $thiskey) = explode(".", $key);';
	$html .= "\n";
	$html .= '		}';
	$html .= "\n";
	$html .= '		$thiskey = rtrim($thiskey, ")");';
	$html .= "\n";
	$html .= '		if(count($arrvalue)==1 && $symbol!="REGEXP" && $symbol!="NOTIN"){';
	$html .= "\n";
	$html .= '			$query .= " AND ".$key." {$symbol} :".$thiskey."{$thisnum}";';
	$html .= "\n";
	$html .= '			$arrbind[$thiskey.$thisnum] = $value;';
	$html .= "\n";
	$html .= '		}';
	$html .= "\n";
	$html .= '		else if($symbol=="REGEXP"){';
	$html .= "\n";
	$html .= '			$query .= " AND ".$key." REGEXP ";';
	$html .= "\n";
	$html .= '			$comma = "";';
	$html .= "\n";
	$html .= '			for($i=0; $i<count($arrvalue); $i++){';
	$html .= "\n";
	$html .= '				$query .= $comma.":".$thiskey."".$i; ';
	$html .= "\n";
	$html .= '				$comma = "|"; ';
	$html .= "\n";
	$html .= '				$arrbind[$thiskey.$i] = $arrvalue[$i]; ';
	$html .= "\n";
	$html .= '			}';
	$html .= "\n";
	$html .= '			$query .= "";';
	$html .= "\n";
	$html .= '		}';
	$html .= "\n";
	$html .= '		else if($symbol=="NOTIN"){';
	$html .= "\n";
	$html .= '			$query .= " AND ".$key." NOT IN (";';
	$html .= "\n";
	$html .= '			$comma = "";';
	$html .= "\n";
	$html .= '			for($i=0; $i<count($arrvalue); $i++){';
	$html .= "\n";
	$html .= '				$query .= $comma.":".$thiskey."".$i; ';
	$html .= "\n";
	$html .= '				$comma = " , "; ';
	$html .= "\n";
	$html .= '				$arrbind[$thiskey.$i] = $arrvalue[$i]; ';
	$html .= "\n";
	$html .= '			} ';
	$html .= "\n";
	$html .= '			$query .= ")";';
	$html .= "\n";
	$html .= '		}';
	$html .= "\n";
	$html .= '		else{';
	$html .= "\n";
	$html .= '			$query .= " AND ".$key." IN (";';
	$html .= "\n";
	$html .= '			$comma = "";';
	$html .= "\n";
	$html .= '			for($i=0; $i<count($arrvalue); $i++){';
	$html .= "\n";
	$html .= '				$query .= $comma.":".$thiskey."".$i;  ';
	$html .= "\n";
	$html .= '				$comma = " , ";  ';
	$html .= "\n";
	$html .= '				$arrbind[$thiskey.$i] = $arrvalue[$i];  ';
	$html .= "\n";
	$html .= '			}  ';
	$html .= "\n";
	$html .= '			$query .= ")";';
	$html .= "\n";
	$html .= '		}';
	$html .= "\n";
	$html .= '	}//-- end for';
	
	$html .= "\n";
	$html .= "\n";
	$html .= '  $query .= " {$group_by} "; ';
	$html .= "\n";
	$html .= '  $query .= " {$order_by}"; ';
	$html .= "\n";
	$html .= "\n";
	$html .= '	// prepare query'; $html .= "\n";
	$html .= '	$stmt = $this->conn->prepare($query);'; $html .= "\n";
	$html .= '	$stmt->execute($arrbind);'; 
	$html .= "\n";
	$html .= "\n";
	$html .= '	$count = $stmt->rowCount();'; $html .= "\n";
	$html .= '	$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);'; 
	$html .= "\n";
	$html .= "\n";
	$html .= '	$arr = array("count"=>"$count", "row"=>$row);'; 
	$html .= "\n";
	$html .= '	return $arr;';
	$html .= "\n";
	
	$html .= '}// end getAllByArr';// end getAllByArr
	
	$html .= "\n\n";
	$html .= "} // end class";//end class 
	$html .= "\n\n";
	$html .= "?>";
	
	$filename = $tblname.".php";
	
	// echo "HiHI!!";
	
	if (file_exists($filename)) {
		echo "The file $filename exists <br/>";
	}
	else{
		// The file $filename does not exist
		$file = fopen($filename,"w");
		echo fwrite($file, $html);
		fclose($file);
		
		echo "Create file $filename successful!! <br/>";
	}
	
	// print_r($arr_columns);
	// print_r($col);
	
}
	
?>