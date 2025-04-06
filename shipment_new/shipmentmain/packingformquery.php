<?php
//get default data from consignee
function get_consignee($conn, $soID){
	$queryc = $conn->prepare("SELECT c.ConsigneeID, c.ForwarderID, c.ShipModeID, c.ShipName, c.BuyerDestination, c.PortOfDestination, c.PortOfLoading, c.PaymentTermID
	FROM tblconsignee AS c
	LEFT JOIN tblorder AS o ON o.buyerID = c.BuyerID
	WHERE o.Orderno = :Orderno
	LIMIT 1");
	$queryc->bindParam(':Orderno', $soID);
	$queryc->execute();

	$rowc = $queryc->fetch(PDO::FETCH_ASSOC);
	
    return $rowc;   
}

//calculate how many column
function get_column($conn, $soID, $ans){
	$columnsql2 = $conn->prepare("select DISTINCT SizeName from tblcolorsizeqty where orderno = :orderno ORDER BY ID");
	$columnsql2->bindParam(':orderno', $soID);
	$columnsql2->execute();	
	
	$num_column = $columnsql2->rowCount();
	
	if($ans == "0"){
		return $num_column;
	}else{
		return $columnsql2;
	}
}

//calculate how many row
function get_row($conn, $soID, $ans){
	$rowsql2 = $conn->prepare("select DISTINCT g.garmentID, g.styleNo, c.colorID, co.colorName from tblcolorsizeqty AS c 
	INNER JOIN tblcolor AS co ON c.colorID = co.ID 
	INNER JOIN tblgarment AS g ON g.garmentID = c.garmentID
	where c.orderno = :orderno ORDER BY g.garmentID, c.colorID ASC");
	$rowsql2->bindParam(':orderno', $soID);
	$rowsql2->execute();	
	
	$num_row = $rowsql2->rowCount();
	
	if($ans == "0"){
		return $num_row;
	}else{
		return $rowsql2;
	}
}

//query to check had planning or not (UPC used)
function get_upc($conn, $soID){
	$queryupcp = $conn->prepare("SELECT APID FROM tblapurchase WHERE orderno = :orderno AND AMID != '' LIMIT 1");
	$queryupcp->bindParam(':orderno', $soID);
	$queryupcp->execute();
	
	$num_upcp = $queryupcp->rowCount();

	return $num_upcp;
}
?>