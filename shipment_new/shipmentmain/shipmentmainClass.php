<?php 
class shipmentmainClass{

private $conn = ""; private $connlog="";private $lang = "EN"; private $buyerID = ""; private $acctid = "0"; 
public $from_location = "";//-- Empty represent as Buyer PO --//
private $prod_pocount = 0; public $pdf_display = "0"; private $linkPOwith = ""; private $byEachSize = 0; private $count_attach = 0;
//private $hdlang = array();
/*-----------------A-----------------*/
//private $hdlang["accessories"] = "Accessories";
	
public function setConnection($conn){
	$this->conn = $conn;
}

public function setConnectionlog($connlog){
	$this->connlog = $connlog;
}

public function setlanguage($lang){
	$this->lang = $lang;
}

public function setAcctID($acctid){
	$this->acctid = $acctid;
}

public function setCountAttach($count_attach){
	$this->count_attach = $count_attach;
}

public function setBuyerID($buyerID){
	$this->buyerID = "$buyerID";
}

public function setfrom_location($from_location){
	$this->from_location = $from_location;
}

public function setprod_pocount($prod_pocount){
	$this->prod_pocount = $prod_pocount;
}

public function setLinkPOWith($linkPOwith){ //-- added by ckwain on 201907231605 --// 
	$this->linkPOwith = $linkPOwith;
}

public function setByEachSize($byEachSize){ //-- added by ckwain on 201907231605 --// 
	$this->byEachSize = $byEachSize;
}

public $wg_unit = 0;
//to check is use LBS (41) or KG (16) (2018-10-05 w)
public function check_kg_lbs($soID, $PID, $is_standard, $packing_type){
	$unit_sql = $this->conn->prepare("SELECT unit, wg_unit FROM tblcarton_calculator_head 
										WHERE orderno = :soID AND PID = :PID  LIMIT 1");
	$unit_sql->bindParam(':PID', $PID);
	$unit_sql->bindParam(':soID', $soID);	
	$unit_sql->execute();
	$unit_row = $unit_sql->fetch(PDO::FETCH_ASSOC);
	$unit = $unit_row["unit"];
	$wg_unit = $unit_row["wg_unit"];
	
	//echo "$unit // $PID // $soID <===== <br/>";
	
	//find PID = 0 if no result (use default)
	if($unit == ""){
		$unit_sql = $this->conn->prepare("SELECT unit, wg_unit FROM tblcarton_calculator_head 
											WHERE orderno = :soID AND PID = '0' AND is_standard='$is_standard' AND packing_type='$packing_type' LIMIT 1");
		$unit_sql->bindParam(':soID', $soID);	
		$unit_sql->execute();
		$unit_row = $unit_sql->fetch(PDO::FETCH_ASSOC);
		$unit = $unit_row["unit"];	
		$wg_unit = $unit_row["wg_unit"];	
			//echo "$unit // $PID // $soID <----- <br/>";		
	}	
	
	if($unit==""){
		$unit_sql = $this->conn->prepare("SELECT unit, wg_unit FROM tblcarton_calculator_head 
											WHERE orderno = :soID AND PID = '0'  LIMIT 1");
		$unit_sql->bindParam(':soID', $soID);	
		$unit_sql->execute();
		$unit_row = $unit_sql->fetch(PDO::FETCH_ASSOC);
		$unit = $unit_row["unit"];	
		$wg_unit = $unit_row["wg_unit"];	
	}
	
	$this->wg_unit = $wg_unit;
	return $unit;
}

//convert number to 1 decimal (2018-10-05 w)
public function to_one_dec($num){
	$num = number_format($num, 1, '.', ' ');
	return $num;
}

//================================================================//
//=============== INSERT, UPDATE, DELETE QUERY ===================//
//================================================================//
public function insertTblship_colorsizeqty($shipmentpriceID, $colorID, $garmentID, $size_name, $qty, $price, $statusID){
	$from_location = $this->from_location;
	$ID = $this->getMaxID("SELECT max(ID) as maxID FROM tblship_colorsizeqty$from_location");
	$columnsql2 = $this->conn->prepare("INSERT INTO tblship_colorsizeqty$from_location 
											(ID, shipmentpriceID, colorID, garmentID, size_name, qty, price, statusID)
										VALUES(:ID, :shipmentpriceID, :colorID, :garmentID, :size_name, :qty, :price, :statusID)");
	$columnsql2->bindParam(':ID', $ID);
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':colorID', $colorID);
	$columnsql2->bindParam(':garmentID', $garmentID);
	$columnsql2->bindParam(':size_name', $size_name);
	$columnsql2->bindParam(':qty', $qty);
	$columnsql2->bindParam(':price', $price);
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->execute();	
	
	return true;
}

public function updateTblship_colorsizeqty($shipmentpriceID, $colorID, $garmentID, $size_name, $qty, $statusID, $ID=""){
	$from_location = $this->from_location;
	
	$query = ($ID==""? "":" AND ID='$ID'");
	$columnsql2 = $this->conn->prepare("UPDATE tblship_colorsizeqty$from_location SET statusID=:statusID, qty=:qty
												WHERE shipmentpriceID=:shipmentpriceID AND colorID=:colorID 
												AND garmentID=:garmentID AND size_name=:size_name $query");
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->bindParam(':qty', $qty);
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':colorID', $colorID);
	$columnsql2->bindParam(':garmentID', $garmentID);
	$columnsql2->bindParam(':size_name', $size_name);
	$columnsql2->execute();	
	
	return true;
}

public function updateTblship_colorsizeqty_price($shipmentpriceID, $colorID, $garmentID, $size_name, $price){
	$columnsql2 = $this->conn->prepare("UPDATE tblship_colorsizeqty SET price='$price'
												WHERE shipmentpriceID='$shipmentpriceID' AND colorID='$colorID' 
												AND garmentID='$garmentID' AND size_name='$size_name'");
	$columnsql2->execute();	

	// $this->updatetblbuyer_invoice_payment_detail_price($shipmentpriceID, $colorID, $garmentID, $price);
	
	return true;
}


// //modified by SL 2022 Mar 15
// public function updatetblbuyer_invoice_payment_detail_price($shipmentpriceID, $colorID,$garmentID, $price){
// 	$columnsql2 = $this->conn->prepare("UPDATE tblbuyer_invoice_payment_detail invd 
// INNER JOIN tblship_group_color sg ON sg.shipmentpriceID = invd.shipmentpriceID and sg.garmentID='$garmentID' and sg.colorID = '$colorID' and sg.statusID=1
// SET invd.fob_price='$price' 
// WHERE invd.shipmentpriceID='$shipmentpriceID' 
// and invd.del=0
// and invd.valid=1
// and invd.qty>0
// and invd.BICID>0");
// 	$columnsql2->execute();	
	
// 	return true;
// }

public function delAllTbl_colorsizeqty($shipmentpriceID){
	$from_location = $this->from_location;
	$columnsql2 = $this->conn->prepare("UPDATE tblship_colorsizeqty$from_location SET statusID='2'
												WHERE shipmentpriceID='$shipmentpriceID'");
	$columnsql2->execute();	
	
	return true;
}

public function getTblship_group_color($shipmentpriceID, $group_number, $garmentID, $colorID, $is_group, $mode){
	$columnsql2 = $this->conn->prepare("SELECT * FROM tblship_group_color 
										WHERE shipmentpriceID=:shipmentpriceID AND group_number=:group_number 
										AND garmentID=:garmentID AND colorID=:colorID AND is_group=:is_group
										order by group_number asc");
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':group_number', $group_number);
	$columnsql2->bindParam(':garmentID', $garmentID);
	$columnsql2->bindParam(':colorID', $colorID);
	$columnsql2->bindParam(':is_group', $is_group);
	$columnsql2->execute();
	$num_column = $columnsql2->rowCount();
	
	//---- Mode is 0, return total number of data row ----//
	if($mode == "0"){
		return $num_column;
	}
	//---- Mode is 1, return SizeName value from table ----//
	else{
		return $columnsql2;
	}
}

public function insertTblship_group_color($shipmentpriceID, $group_number, $garmentID, $colorID, $GTN_color, $is_group, $statusID){
	$GCID = $this->getMaxID("SELECT max(GCID) as maxID FROM tblship_group_color");
	
	$columnsql2 = $this->conn->prepare("INSERT INTO tblship_group_color (GCID, shipmentpriceID, group_number, garmentID, colorID, GTN_color, is_group, statusID)
																VALUES(:GCID, :shipmentpriceID, :group_number, :garmentID, :colorID, :GTN_color, :is_group, :statusID)");
	$columnsql2->bindParam(':GCID', $GCID);
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':group_number', $group_number);
	$columnsql2->bindParam(':garmentID', $garmentID);
	$columnsql2->bindParam(':colorID', $colorID);
	$columnsql2->bindParam(':GTN_color', $GTN_color);
	$columnsql2->bindParam(':is_group', $is_group);
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->execute();	
	
	return $GCID;
}

public function updateTblship_group_color($shipmentpriceID, $group_number, $garmentID, $colorID, $is_group, $statusID){
	$sql = "SELECT GCID FROM tblship_group_color 
			WHERE shipmentpriceID=:shipmentpriceID AND group_number=:group_number 
			AND garmentID=:garmentID AND colorID=:colorID AND is_group=:is_group order by GTN_color desc 
			limit 1";
	$columnsql2 = $this->conn->prepare($sql);
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':group_number', $group_number);
	$columnsql2->bindParam(':garmentID', $garmentID);
	$columnsql2->bindParam(':colorID', $colorID);
	$columnsql2->bindParam(':is_group', $is_group);
	$columnsql2->execute();
	
	$row   = $columnsql2->fetchALL(PDO::FETCH_ASSOC);
	$GCID = (isset($row[0]["GCID"])? $row[0]["GCID"]: "");
	
	$columnsql2 = $this->conn->prepare("UPDATE tblship_group_color SET statusID=:statusID 
										WHERE GCID=:GCID ");
	$columnsql2->bindParam(':GCID', $GCID); 
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->execute();	
	
	return true;
}

public function getTblship_packing($shipmentpriceID, $statusID, $mode){
	$from_location = ($this->prod_pocount>0 ? $this->from_location:"");
	$columnsql2 = $this->conn->prepare("SELECT spk.*,
		 
		 (SELECT GROUP_CONCAT(DISTINCT spd2.size_name order by spd2.size_name asc) 
									 FROM tblship_packing_detail$from_location spd2
									 WHERE spd2.PID = spk.PID AND spd2.statusID=1 AND spd2.group_number = spd.group_number AND spd2.ratio_qty>0) as grp_size 
											FROM tblship_packing$from_location spk
											INNER JOIN tblship_packing_detail$from_location spd ON spd.PID = spk.PID
											WHERE spk.shipmentpriceID=:shipmentpriceID 
											AND spk.statusID=:statusID AND spd.statusID=1
											group by spk.PID
											order by spk.PID asc");
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->execute();
	$num_column = $columnsql2->rowCount();
	
	//---- Mode is 0, return total number of data row ----//
	if($mode == "0"){
		return $num_column;
	}
	//---- Mode is 1, return SizeName value from table ----//
	else{
		return $columnsql2;
	}
}

public function insertTblship_packing($PID, $shipmentpriceID, $is_polybag, $is_blisterbag, $is_ctnblister, $blisterbag_qty, $packing_method, 
										$statusID, $this_standard, $last_ctn_by_SCSS, $last_ctn_num, $packing_type, $ship_remark, $order_by_color=0,
										$is_multi_gender=0){
	$from_location = $this->from_location;
	
	if($PID==0){
		$PID = $this->getMaxID("SELECT max(PID) as maxID FROM tblship_packing$from_location");
	}

	$columnsql2 = $this->conn->prepare("INSERT INTO tblship_packing$from_location
											(PID, shipmentpriceID, is_polybag, is_blisterbag, is_ctnblister, blisterbag_qty, packing_method, tmode, 
																		last_ctn_num_size, last_ctn_by_SCSS, statusID, packing_type, ship_remark, order_by_color, is_multi_gender)
											VALUES(:PID, :shipmentpriceID, :is_polybag, :is_blisterbag, :is_ctnblister, :blisterbag_qty, :packing_method, :tmode, 
																		:last_ctn_num_size, :last_ctn_by_SCSS, :statusID, :packing_type, :ship_remark, :order_by_color, :is_multi_gender)");
	$columnsql2->bindParam(':PID', $PID);
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':is_polybag', $is_polybag);
	$columnsql2->bindParam(':is_blisterbag', $is_blisterbag);
	$columnsql2->bindParam(':is_ctnblister', $is_ctnblister);
	$columnsql2->bindParam(':blisterbag_qty', $blisterbag_qty);
	$columnsql2->bindParam(':packing_method', $packing_method);
	$columnsql2->bindParam(':tmode', $this_standard);
	$columnsql2->bindParam(':last_ctn_num_size', $last_ctn_num);
	$columnsql2->bindParam(':last_ctn_by_SCSS', $last_ctn_by_SCSS);
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->bindParam(':packing_type', $packing_type);
	$columnsql2->bindParam(':ship_remark', $ship_remark);	
	$columnsql2->bindParam(':order_by_color', $order_by_color);	
	$columnsql2->bindParam(':is_multi_gender', $is_multi_gender);	
	$columnsql2->execute();	
	
	return $PID;
}

public function updateTblship_packing($PID, $is_polybag, $is_blisterbag, $is_ctnblister, $blisterbag_qty, $statusID, 
										$this_standard, $last_ctn_by_SCSS, $last_ctn_num, $packing_type, $ship_remark, $order_by_color=0,
										$is_multi_gender=0){
	$from_location = $this->from_location;
	$columnsql2 = $this->conn->prepare("UPDATE tblship_packing$from_location 
												SET is_polybag=:is_polybag, is_blisterbag=:is_blisterbag, is_ctnblister=:is_ctnblister,
												blisterbag_qty=:blisterbag_qty, statusID=:statusID, tmode=:tmode, last_ctn_by_SCSS=:last_ctn_by_SCSS,
												last_ctn_num_size=:last_ctn_num_size, packing_type=:packing_type, ship_remark = :ship_remark, order_by_color=:order_by_color, is_multi_gender=:is_multi_gender
										WHERE PID=:PID");
	$columnsql2->bindParam(':PID', $PID);
	$columnsql2->bindParam(':is_polybag', $is_polybag);
	$columnsql2->bindParam(':is_blisterbag', $is_blisterbag);
	$columnsql2->bindParam(':is_ctnblister', $is_ctnblister);
	$columnsql2->bindParam(':blisterbag_qty', $blisterbag_qty);
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->bindParam(':tmode', $this_standard);
	$columnsql2->bindParam(':last_ctn_by_SCSS', $last_ctn_by_SCSS);
	$columnsql2->bindParam(':last_ctn_num_size', $last_ctn_num);
	$columnsql2->bindParam(':packing_type', $packing_type);
	$columnsql2->bindParam(':ship_remark', $ship_remark);	
	$columnsql2->bindParam(':order_by_color', $order_by_color);	
	$columnsql2->bindParam(':is_multi_gender', $is_multi_gender);	
	$columnsql2->execute();	
	
	return $PID;
}

public function getTblship_packing_detail($PID, $group_number, $size_name, $mode){
	$from_location = $this->from_location;
	$columnsql2 = $this->conn->prepare("SELECT * FROM tblship_packing_detail$from_location 
											WHERE PID=:PID AND group_number=:group_number AND size_name=:size_name");
	$columnsql2->bindParam(':PID', $PID);
	$columnsql2->bindParam(':group_number', $group_number);
	$columnsql2->bindParam(':size_name', $size_name);
	$columnsql2->execute();
	$num_column = $columnsql2->rowCount();
	
	//---- Mode is 0, return total number of data row ----//
	if($mode == "0"){
		return $num_column;
	}
	//---- Mode is 1, return SizeName value from table ----//
	else{
		return $columnsql2;
	}
}

public function insertTblship_packing_detail($PID, $group_number, $size_name, $ratio_qty, $gmt_qty_in_polybag, $polybag_qty_in_blisterbag, $blisterbag_in_carton,
												$total_qty, $total_qty_log, $statusID, $SKU, $master_upc="", $case_upc=""){
	$polybag_qty_in_blisterbag = ($polybag_qty_in_blisterbag==""? 0 : $polybag_qty_in_blisterbag);
	$blisterbag_in_carton = ($blisterbag_in_carton==""? 0 : $blisterbag_in_carton);
	//echo "$gmt_qty_in_polybag // $polybag_qty_in_blisterbag // $blisterbag_in_carton <br/>";
	$from_location = $this->from_location;
	
	$ID = $this->getMaxID("SELECT max(ID) as maxID FROM tblship_packing_detail$from_location");
	$columnsql2 = $this->conn->prepare("INSERT INTO tblship_packing_detail$from_location 
											(ID, PID, group_number, size_name, ratio_qty, gmt_qty_in_polybag, polybag_qty_in_blisterbag, 
																				blisterbag_in_carton, total_qty, total_qty_log, statusID, SKU, master_upc, case_upc)
											VALUES(:ID, :PID, :group_number, :size_name, :ratio_qty, :gmt_qty_in_polybag, :polybag_qty_in_blisterbag, 
																				:blisterbag_in_carton, :total_qty, :total_qty_log, :statusID, :SKU, :master_upc, :case_upc)");
	$columnsql2->bindParam(':ID', $ID);
	$columnsql2->bindParam(':PID', $PID);
	$columnsql2->bindParam(':group_number', $group_number);
	$columnsql2->bindParam(':size_name', $size_name);
	$columnsql2->bindParam(':ratio_qty', $ratio_qty);
	$columnsql2->bindParam(':gmt_qty_in_polybag', $gmt_qty_in_polybag);
	$columnsql2->bindParam(':polybag_qty_in_blisterbag', $polybag_qty_in_blisterbag);
	$columnsql2->bindParam(':blisterbag_in_carton', $blisterbag_in_carton);
	$columnsql2->bindParam(':total_qty', $total_qty);
	$columnsql2->bindParam(':total_qty_log', $total_qty_log);
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->bindParam(':SKU', $SKU);
	$columnsql2->bindParam(':master_upc', $master_upc);
	$columnsql2->bindParam(':case_upc', $case_upc);
	$columnsql2->execute();	
	
	return $ID;
}

public function updateTblship_packing_detail($total_qty, $ratio_qty, $gmt_qty_in_polybag, $polybag_qty_in_blisterbag, $blisterbag_in_carton, 
												$statusID, $total_qty_log, $PID, $group_number, $size_name, $SKU, $master_upc="", $case_upc=""){
	$polybag_qty_in_blisterbag = ($polybag_qty_in_blisterbag==""? 0 : $polybag_qty_in_blisterbag);
	$blisterbag_in_carton = ($blisterbag_in_carton==""? 0 : $blisterbag_in_carton);
	$from_location = $this->from_location;
	
	$columnsql2 = $this->conn->prepare("UPDATE tblship_packing_detail$from_location 
											SET total_qty=:total_qty, ratio_qty=:ratio_qty, gmt_qty_in_polybag=:gmt_qty_in_polybag, 
											total_qty_log=:total_qty_log, polybag_qty_in_blisterbag=:polybag_qty_in_blisterbag, 
											blisterbag_in_carton=:blisterbag_in_carton, statusID=:statusID, SKU=:SKU, master_upc=:master_upc, case_upc=:case_upc
										WHERE PID=:PID AND group_number=:group_number AND size_name=:size_name");
	$columnsql2->bindParam(':total_qty', $total_qty);
	$columnsql2->bindParam(':ratio_qty', $ratio_qty);
	$columnsql2->bindParam(':gmt_qty_in_polybag', $gmt_qty_in_polybag);
	$columnsql2->bindParam(':total_qty_log', $total_qty_log);
	$columnsql2->bindParam(':polybag_qty_in_blisterbag', $polybag_qty_in_blisterbag);
	$columnsql2->bindParam(':blisterbag_in_carton', $blisterbag_in_carton);
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->bindParam(':SKU', $SKU);
	$columnsql2->bindParam(':master_upc', $master_upc);
	$columnsql2->bindParam(':case_upc', $case_upc);
	$columnsql2->bindParam(':PID', $PID);
	$columnsql2->bindParam(':group_number', $group_number);
	$columnsql2->bindParam(':size_name', $size_name);
	$columnsql2->execute();	
	
	if(trim($from_location)==""){
		$sql = "UPDATE tblship_packing_detail_prod 
											SET  SKU=:SKU, master_upc=:master_upc, case_upc=:case_upc
										WHERE PID=:PID AND group_number=:group_number AND size_name=:size_name";
		$columnsql2 = $this->conn->prepare($sql);
		$columnsql2->bindParam(':SKU', $SKU);
		$columnsql2->bindParam(':master_upc', $master_upc);
		$columnsql2->bindParam(':case_upc', $case_upc);
		$columnsql2->bindParam(':PID', $PID);
		$columnsql2->bindParam(':group_number', $group_number);
		$columnsql2->bindParam(':size_name', $size_name);
		$columnsql2->execute();	
	}
	
	return true;
}

public function insertTblship_packing_carton($PID, $carton_type, $carton_range_from, $carton_range_to, $total_carton, $total_qty, $GCID, $statusID){
	$PCID = $this->getMaxID("SELECT max(PCID) as maxID FROM tblship_packing_carton");
	$columnsql2 = $this->conn->prepare("INSERT INTO tblship_packing_carton (PCID, PID, carton_type, carton_range_from, carton_range_to, total_carton, total_qty, group_number, statusID)
															VALUES(:PCID, :PID, :carton_type, :carton_range_from, :carton_range_to, :total_carton, :total_qty, :GCID, :statusID)");
	$columnsql2->bindParam(':PCID', $PCID);
	$columnsql2->bindParam(':PID', $PID);
	$columnsql2->bindParam(':carton_type', $carton_type);
	$columnsql2->bindParam(':carton_range_from', $carton_range_from);
	$columnsql2->bindParam(':carton_range_to', $carton_range_to);
	$columnsql2->bindParam(':total_carton', $total_carton);
	$columnsql2->bindParam(':total_qty', $total_qty);
	$columnsql2->bindParam(':GCID', $GCID);
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->execute();	
	return true;
}

public function insertTblship_packing_carton_detail($PCID, $size_name, $qty){
	$PCDID = $this->getMaxID("SELECT max(PCDID) as maxID FROM tblship_packing_carton_detail");
	$columnsql2 = $this->conn->prepare("INSERT INTO tblship_packing_carton_detail (PCDID, PCID, size_name, qty)
																			VALUES(:PCDID, :PCID, :size_name, :qty)");
	$columnsql2->bindParam(':PCDID', $PCDID);
	$columnsql2->bindParam(':PCID', $PCID);
	$columnsql2->bindParam(':size_name', $size_name);
	$columnsql2->bindParam(':qty', $qty);
	$columnsql2->execute();	
	return true;
}

public function updateTblship_packing_carton_detail($PCDID, $qty){
	$columnsql2 = $this->conn->prepare("UPDATE tblship_packing_carton_detail SET qty=:qty WHERE PCDID=:PCDID");
	$columnsql2->bindParam(':PCDID', $PCDID);
	$columnsql2->bindParam(':qty', $qty);
	$columnsql2->execute();	
	return true;
}

public function insertTblship_upc($shipmentpriceID, $APID, $createdby, $createdDate, $updatedBy, $updatedDate, $statusID){
	$SUID = $this->getMaxID("SELECT max(SUID) as maxID FROM tblship_upc");
	$columnsql2 = $this->conn->prepare("INSERT INTO tblship_upc (SUID, shipmentpriceID, APID, createdby, createdDate, updatedBy, updatedDate, statusID)
										VALUES(:SUID, :shipmentpriceID, :APID, :createdby, :createdDate, :updatedBy, :updatedDate, :statusID)");
	$columnsql2->bindParam(':SUID', $SUID);
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':APID', $APID);
	$columnsql2->bindParam(':createdby', $createdby);
	$columnsql2->bindParam(':createdDate', $createdDate);
	$columnsql2->bindParam(':updatedBy', $updatedBy);
	$columnsql2->bindParam(':updatedDate', $updatedDate);
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->execute();	
	
	return $SUID;
}

public function updateTblship_upc($SUID, $APID, $updatedBy, $updatedDate, $statusID){
	$columnsql2 = $this->conn->prepare("UPDATE tblship_upc SET APID=:APID, updatedBy=:updatedBy, updatedDate=:updatedDate, statusID=:statusID 
											WHERE SUID=:SUID");
	$columnsql2->bindParam(':APID', $APID);
	$columnsql2->bindParam(':updatedBy', $updatedBy);
	$columnsql2->bindParam(':updatedDate', $updatedDate);
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->bindParam(':SUID', $SUID);
	$columnsql2->execute();	
	return true;
}

public function insertTblship_upc_detail($SUID, $shipmentpriceID, $garmentID, $colorID, $size_name, $upc_code, $statusID, $upc_dim=""){
	$SUDID = $this->getMaxID("SELECT max(SUDID) as maxID FROM tblship_upc_detail");
	$columnsql2 = $this->conn->prepare("INSERT INTO tblship_upc_detail (SUDID, SUID, shipmentpriceID, garmentID, colorID, size_name, upc_code, statusID, DIM)
										VALUES(:SUDID, :SUID, :shipmentpriceID, :garmentID, :colorID, :size_name, :upc_code, :statusID, :DIM)");
	$columnsql2->bindParam(':SUDID', $SUDID);
	$columnsql2->bindParam(':SUID', $SUID);
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':garmentID', $garmentID);
	$columnsql2->bindParam(':colorID', $colorID);
	$columnsql2->bindParam(':size_name', $size_name);
	$columnsql2->bindParam(':upc_code', $upc_code);
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->bindParam(':DIM', $upc_dim);
	$columnsql2->execute();	
	
	return $SUDID;
}

public function updateTblship_upc_detail($SUDID, $upc_code, $statusID, $upc_dim=""){
	$columnsql2 = $this->conn->prepare("UPDATE tblship_upc_detail SET upc_code=:upc_code, statusID=:statusID, DIM=:DIM 
											WHERE SUDID=:SUDID");
	$columnsql2->bindParam(':upc_code', $upc_code);
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->bindParam(':DIM', $upc_dim);
	$columnsql2->bindParam(':SUDID', $SUDID);
	$columnsql2->execute();	
	return true;
}

//=================================================================//
//=============== UPDATE PICKLIST CARTON NUMBER ===================//
//=================================================================//
public function deleteTblcarton_picklist($shipmentpriceID){
	$from_location = $this->from_location;
	$columnsql2 = $this->conn->prepare("DELETE FROM tblcarton_picklist_head$from_location
											WHERE shipmentpriceID='$shipmentpriceID'");
	$columnsql2->execute();	
	
	$columnsql2 = $this->conn->prepare("DELETE FROM tblcarton_picklist_detail$from_location
											WHERE shipmentpriceID='$shipmentpriceID'");
	$columnsql2->execute();	
}

public function insertTblcarton_picklist_head($shipmentpriceID, $PID, $ctn_num, $group_number, $temp_grp, $qty_in_blisterbag, $blisterbag_in_carton,
												$total_qty_in_carton, $net_net_weight, $net_weight, $gross_weight, $ctn_measurement, $total_CBM,
												$updatedBy, $updatedDate, $ctn_range, $is_last, $prepack_name, $ext_length, $ext_width, $ext_height, $ctn_measurement_last=NULL){
	$opt_tb = $this->from_location;
	
	if($this->acctid==3){
		//echo "$shipmentpriceID // $PID // $ctn_num // <br/>";
	}
	$blisterbag_in_carton = ($blisterbag_in_carton==""? 1: $blisterbag_in_carton);
	$qty_in_blisterbag = ($qty_in_blisterbag==""? 1: $qty_in_blisterbag); 
	
	$sql = "INSERT INTO tblcarton_picklist_head$opt_tb 
										VALUES(:shipmentpriceID, :PID, :ctn_num, :ctn_range, :is_last, :prepack_name, :group_number, :temp_grp, :qty_in_blisterbag, 
												:blisterbag_in_carton, :total_qty_in_carton, :net_net_weight, :net_weight, :gross_weight, :ctn_measurement, :ctn_measurement_last, 
												:ext_length, :ext_width, :ext_height, :total_CBM, :updatedBy, :updatedDate)";
	
//echo "$shipmentpriceID // $PID // $ctn_num <br/>";	
	$columnsql2 = $this->conn->prepare("INSERT INTO tblcarton_picklist_head$opt_tb 
										(shipmentpriceID, PID, ctn_num, ctn_range, is_last, prepack_name, group_number, temp_grp, qty_in_blisterbag, 
												blisterbag_in_carton, total_qty_in_carton, net_net_weight, net_weight, gross_weight, ctn_measurement, ctn_measurement_last, 
												ext_length, ext_width, ext_height, total_CBM, updatedBy, updatedDate)
										VALUES(:shipmentpriceID, :PID, :ctn_num, :ctn_range, :is_last, :prepack_name, :group_number, :temp_grp, :qty_in_blisterbag, 
												:blisterbag_in_carton, :total_qty_in_carton, :net_net_weight, :net_weight, :gross_weight, :ctn_measurement, :ctn_measurement_last,
												:ext_length, :ext_width, :ext_height, :total_CBM, :updatedBy, :updatedDate)");
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':PID', $PID);
	$columnsql2->bindParam(':ctn_num', $ctn_num);
	$columnsql2->bindParam(':ctn_range', $ctn_range);
	$columnsql2->bindParam(':is_last', $is_last);
	$columnsql2->bindParam(':prepack_name', $prepack_name);
	$columnsql2->bindParam(':group_number', $group_number);
	$columnsql2->bindParam(':temp_grp', $temp_grp);
	$columnsql2->bindParam(':qty_in_blisterbag', $qty_in_blisterbag);
	$columnsql2->bindParam(':blisterbag_in_carton', $blisterbag_in_carton);
	$columnsql2->bindParam(':total_qty_in_carton', $total_qty_in_carton);
	$columnsql2->bindParam(':net_net_weight', $net_net_weight);
	$columnsql2->bindParam(':net_weight', $net_weight);
	$columnsql2->bindParam(':gross_weight', $gross_weight);
	$columnsql2->bindParam(':ctn_measurement', $ctn_measurement);
	$columnsql2->bindParam(':ctn_measurement_last', $ctn_measurement_last);
	$columnsql2->bindParam(':ext_length', $ext_length);
	$columnsql2->bindParam(':ext_width', $ext_width);
	$columnsql2->bindParam(':ext_height', $ext_height);
	$columnsql2->bindParam(':total_CBM', $total_CBM);
	$columnsql2->bindParam(':updatedBy', $updatedBy);
	$columnsql2->bindParam(':updatedDate', $updatedDate);
	$columnsql2->execute();	
}

public function insertTblcarton_picklist_detail($shipmentpriceID, $PID, $ctn_num, $size_name, $group_number, $qty){
	$qty = ($qty==""? 0: $qty);
	//$group_number = 0;
	//echo "$shipmentpriceID - $PID - $ctn_num - $size_name - $qty <br/>";
	$opt_tb = $this->from_location;
	$columnsql2 = $this->conn->prepare("INSERT INTO tblcarton_picklist_detail$opt_tb 
										(shipmentpriceID, PID, ctn_num, size_name, group_number, qty)
										VALUES(:shipmentpriceID, :PID, :ctn_num, :size_name, :group_number, :qty)");
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':PID', $PID);
	$columnsql2->bindParam(':ctn_num', $ctn_num);
	//$columnsql2->bindParam(':group_number', $group_number);
	$columnsql2->bindParam(':size_name', $size_name);
	$columnsql2->bindParam(':group_number', $group_number);
	$columnsql2->bindParam(':qty', $qty);
	$columnsql2->execute();	
	//echo "aaa";
}

public function insertTblcarton_picklist_transit($shipmentpriceID, $PID, $ctn_num, $group_number, $temp_grp, $qty_in_blisterbag, $blisterbag_in_carton,
													$total_qty_in_carton, $net_net_weight, $net_weight, $gross_weight, $ctn_measurement, $total_CBM,
													$ct_pat, $ex_factory, $createdBy, $createdDate, $updatedBy, $updatedDate){
	$columnsql2 = $this->conn->prepare("INSERT INTO tblcarton_picklist_head 
										VALUES(:shipmentpriceID, :PID, :ctn_num, :group_number, :temp_grp, :qty_in_blisterbag, :blisterbag_in_carton, 
												:total_qty_in_carton, :net_net_weight, :net_weight, :gross_weight, :ctn_measurement, :total_CBM,
												:ct_pat, :ex_factory, :createdBy, :createdDate, :updatedBy, :updatedDate)");
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':PID', $PID);
	$columnsql2->bindParam(':ctn_num', $ctn_num);
	$columnsql2->bindParam(':group_number', $group_number);
	$columnsql2->bindParam(':temp_grp', $temp_grp);
	$columnsql2->bindParam(':qty_in_blisterbag', $qty_in_blisterbag);
	$columnsql2->bindParam(':blisterbag_in_carton', $blisterbag_in_carton);
	$columnsql2->bindParam(':total_qty_in_carton', $total_qty_in_carton);
	$columnsql2->bindParam(':net_net_weight', $net_net_weight);
	$columnsql2->bindParam(':net_weight', $net_weight);
	$columnsql2->bindParam(':gross_weight', $gross_weight);
	$columnsql2->bindParam(':ctn_measurement', $ctn_measurement);
	$columnsql2->bindParam(':total_CBM', $total_CBM);
	$columnsql2->bindParam(':ct_pat', $ct_pat);
	$columnsql2->bindParam(':ex_factory', $ex_factory);
	$columnsql2->bindParam(':createdBy', $createdBy);
	$columnsql2->bindParam(':createdDate', $createdDate);
	$columnsql2->bindParam(':updatedBy', $updatedBy);
	$columnsql2->bindParam(':updatedDate', $updatedDate);
	$columnsql2->execute();	
}

public function updateTblcarton_picklist_transit($shipmentpriceID, $PID, $ctn_num, $group_number, $ct_pat, $ex_factory, $updatedBy, $updatedDate){
	$columnsql2 = $this->conn->prepare("UPDATE tblcarton_picklist_head 
										SET ct_pat=:ct_pat, ex_factory=:ex_factory, updatedBy=:updatedBy, updatedDate=:updatedDate
										WHERE shipmentpriceID=:shipmentpriceID AND PID=:PID AND ctn_num=:ctn_num AND group_number=:group_number");
	$columnsql2->bindParam(':ct_pat', $ct_pat);
	$columnsql2->bindParam(':ex_factory', $ex_factory);
	$columnsql2->bindParam(':updatedBy', $updatedBy);
	$columnsql2->bindParam(':updatedDate', $updatedDate);
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':PID', $PID);
	$columnsql2->bindParam(':ctn_num', $ctn_num);
	$columnsql2->bindParam(':group_number', $group_number);
	$columnsql2->execute();	
}

public function insertTblcarton_picklist_transit_detail($shipmentpriceID, $PID, $ctn_num, $size_name, $qty){
	$columnsql2 = $this->conn->prepare("INSERT INTO tblcarton_picklist_transit_detail 
										VALUES(:shipmentpriceID, :PID, :ctn_num, :size_name, :qty)");
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':PID', $PID);
	$columnsql2->bindParam(':ctn_num', $ctn_num);
	//$columnsql2->bindParam(':group_number', $group_number);
	$columnsql2->bindParam(':size_name', $size_name);
	$columnsql2->bindParam(':qty', $qty);
	$columnsql2->execute();	
}

public function updateTblcarton_picklist_transit_detail($shipmentpriceID, $PID, $ctn_num, $size_name, $qty){
	$columnsql2 = $this->conn->prepare("UPDATE tblcarton_picklist_transit_detail 
										SET qty=:qty 
										WHERE shipmentpriceID=:shipmentpriceID AND PID=:PID 
										AND ctn_num=:ctn_num AND size_name=:size_name");
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->bindParam(':PID', $PID);
	$columnsql2->bindParam(':ctn_num', $ctn_num);
	$columnsql2->bindParam(':size_name', $size_name);
	$columnsql2->bindParam(':qty', $qty);
	$columnsql2->execute();	
}

//============== END PICKLIST CARTON NUMBER =============//
//=======================================================//

public function suspenseAllTblship_upc_detail($SUID, $statusID){
	$columnsql2 = $this->conn->prepare("UPDATE tblship_upc_detail SET statusID=:statusID 
											WHERE SUID=:SUID");
	$columnsql2->bindParam(':statusID', $statusID);
	$columnsql2->bindParam(':SUID', $SUID);
	$columnsql2->execute();	
	return true;
}

public function delPickList($packingID){
	$columnsql2 = $this->conn->prepare("UPDATE tblship_packing SET statusID='2' WHERE PID='$packingID'");
	$columnsql2->execute();	
	
	return "true";
}

public function getMaxID($query){
	$maxID = 1;
	$columnsql2 = $this->conn->prepare($query);
	$columnsql2->execute();
	$num_column = $columnsql2->rowCount();
	if($num_column>0){
		$rowtitle2=$columnsql2->fetch(PDO::FETCH_ASSOC);
			$maxID = $rowtitle2["maxID"] + 1;
	}
	return $maxID;
}

public function shipmentback($spID, $acctid){
		$updatedby = $acctid;	
		$slogdate = date("Y-m-d H:i:s");	
		//tblshipmentprice

		// echo "INSERT INTO apparelezi_dberplog.tblshipmentprice_log 
		// 						(slogdate, ID, sby, sfrom,  Orderno, BuyerPO, GTN_buyerpo, GTN_syncshipdate, StyleNo, Toleranceminus, Toleranceplus, ConsigneeID, BuyerDestID, PortDestID, 
		// 						QuotaID, PacktypeID, o_shipdate, o_shipmode,ShipmodeID, Shipdate, PONumber, PaymentID, ForwarderID, ShipName, portLoadingID, PackingMethoID, Store, packingID, 
		// 						Factorydate, purposedate, remark, Hangtag, Price, CurrencyID, Str, Carton, Shiptotal, Shipprice, Multistr, Multicarton, Packingmethod, 
		// 						Balmethod, useGroup, groupColor, packresult, poAmount, comboAmount, gramount, grgroup, grprice, aFOBPrice, aShippedQty, grouporcolor, 
		// 						createdby, createddate, updatedby, updateddate, flag, book_for_final_inspect_date, final_inspect_date, final_inspect_cert_date,	in_dc_date, 
		// 						s_414, d_414, s_415, d_415, s_416, d_416, s_417, d_417, s_418, d_418, s_419, d_419, s_420, d_420, s_421, d_421, s_422, d_422, s_423, d_423,
		// 						s_424, d_424, s_425, d_425, s_426, d_426, s_427, d_427, s_428, d_428, s_429, d_429, bfi_status, fid_status, ficd_status,
		// 						revised_indc_date, statusID)
		// SELECT '$slogdate' AS slogdate, ID, '$updatedby' AS sby, '1', Orderno, BuyerPO, GTN_buyerpo, GTN_syncshipdate, StyleNo, Toleranceminus, Toleranceplus, ConsigneeID, BuyerDestID, PortDestID, 
		// 						QuotaID, PacktypeID, o_shipdate, o_shipmode,ShipmodeID, Shipdate, PONumber, PaymentID, ForwarderID, ShipName, portLoadingID, PackingMethoID, Store, packingID, 
		// 						Factorydate, purposedate, remark, Hangtag, Price, CurrencyID, Str, Carton, Shiptotal, Shipprice, Multistr, Multicarton, Packingmethod, 
		// 						Balmethod, useGroup, groupColor, packresult, poAmount, comboAmount, gramount, grgroup, grprice, aFOBPrice, aShippedQty, grouporcolor, 
		// 						createdby, createddate, updatedby, updateddate, flag, book_for_final_inspect_date, final_inspect_date, final_inspect_cert_date, in_dc_date,
		// 						s_414, d_414, s_415, d_415, s_416, d_416, s_417, d_417, s_418, d_418, s_419, d_419, s_420, d_420, s_421, d_421, s_422, d_422, s_423, d_423,
		// 						s_424, d_424, s_425, d_425, s_426, d_426, s_427, d_427, s_428, d_428, s_429, d_429, bfi_status, fid_status, ficd_status,
		// 						revised_indc_date, statusID
		// 						FROM apparelezi_dberp.tblshipmentprice WHERE ID=$spID";

		$sql = "INSERT INTO tblshipmentprice_log 
								(slogdate, ID, sby, sfrom,  Orderno, BuyerPO, GTN_buyerpo, GTN_syncshipdate, StyleNo, Toleranceminus, Toleranceplus, ConsigneeID, BuyerDestID, PortDestID, 
								QuotaID, PacktypeID, o_shipdate, o_shipmode,ShipmodeID, Shipdate, PONumber, PaymentID, ForwarderID, ShipName, portLoadingID, PackingMethoID, Store, packingID, 
								Factorydate, purposedate, remark, Hangtag, Price, CurrencyID, Str, Carton, Shiptotal, Shipprice, Multistr, Multicarton, Packingmethod, 
								Balmethod, useGroup, groupColor, packresult, poAmount, comboAmount, gramount, grgroup, grprice, aFOBPrice, aShippedQty, grouporcolor, 
								createdby, createddate, updatedby, updateddate, flag, book_for_final_inspect_date, final_inspect_date, final_inspect_cert_date,	in_dc_date, 
								s_414, d_414, s_415, d_415, s_416, d_416, s_417, d_417, s_418, d_418, s_419, d_419, s_420, d_420, s_421, d_421, s_422, d_422, s_423, d_423,
								s_424, d_424, s_425, d_425, s_426, d_426, s_427, d_427, s_428, d_428, s_429, d_429, bfi_status, fid_status, ficd_status,
								revised_indc_date, statusID)
		SELECT '$slogdate' AS slogdate, ID, '$updatedby' AS sby, '1', Orderno, BuyerPO, GTN_buyerpo, GTN_syncshipdate, StyleNo, Toleranceminus, Toleranceplus, ConsigneeID, BuyerDestID, PortDestID, 
								QuotaID, PacktypeID, o_shipdate, o_shipmode,ShipmodeID, Shipdate, PONumber, PaymentID, ForwarderID, ShipName, portLoadingID, PackingMethoID, Store, packingID, 
								Factorydate, purposedate, remark, Hangtag, Price, CurrencyID, Str, Carton, Shiptotal, Shipprice, Multistr, Multicarton, Packingmethod, 
								Balmethod, useGroup, groupColor, packresult, poAmount, comboAmount, gramount, grgroup, grprice, aFOBPrice, aShippedQty, grouporcolor, 
								createdby, createddate, updatedby, updateddate, flag, book_for_final_inspect_date, final_inspect_date, final_inspect_cert_date, in_dc_date,
								s_414, d_414, s_415, d_415, s_416, d_416, s_417, d_417, s_418, d_418, s_419, d_419, s_420, d_420, s_421, d_421, s_422, d_422, s_423, d_423,
								s_424, d_424, s_425, d_425, s_426, d_426, s_427, d_427, s_428, d_428, s_429, d_429, bfi_status, fid_status, ficd_status,
								revised_indc_date, statusID
		FROM tblshipmentprice WHERE ID=:spID";
		
		// echo "<pre>$sql / $spID</pre>";
		$insertq = $this->conn->prepare($sql);
		$insertq->bindParam(':spID', $spID);
		$insertq->execute();
		
		// $sql_pk = "INSERT INTO tblship_packing_log(log_date, log_by, PID, shipmentpriceID, is_polybag, is_blisterbag, polybag_qty, blisterbag_qty, packing_method, statusID)
					// SELECT '$slogdate' as log_date, '$updatedby' as log_by, PID, shipmentpriceID, is_polybag, is_blisterbag, polybag_qty, blisterbag_qty, packing_method, statusID
					// FROM tblship_packing WHERE shipmentpriceID='$spID' AND statusID='1'";
		// $insertpk = $this->conn->prepare($sql_pk);
		// $insertpk->execute();
		
		// $sql_pkd = "INSERT INTO tblship_packing_detail_log(log_date, log_by, ID, PID, group_number, size_name, ratio_qty, gmt_qty_in_polybag, 
															// polybag_qty_in_blisterbag, blisterbag_in_carton, total_qty, total_qty_log, statusID)
					// SELECT '$slogdate' as log_date, '$updatedby' as log_by, spd.ID, spd.PID, spd.group_number, spd.size_name, spd.ratio_qty, spd.gmt_qty_in_polybag, 
							// spd.polybag_qty_in_blisterbag, spd.blisterbag_in_carton, spd.total_qty, spd.total_qty_log, spd.statusID
					// FROM tblship_packing_detail spd
					// INNER JOIN tblship_packing sp ON sp.PID = spd.PID
					// WHERE sp.shipmentpriceID='$spID' AND spd.statusID='1' AND sp.statusID='1'";
		// $insertpkd = $this->conn->prepare($sql_pkd);
		// $insertpkd->execute();
		
		// $sql_col = "INSERT INTO tblship_group_color_log(log_date, log_by, GCID, shipmentpriceID, group_number, colorID, is_group, GTN_color, statusID)
					// SELECT '$slogdate' as log_date, '$updatedby' as log_by, GCID, shipmentpriceID, group_number, colorID, is_group, GTN_color, statusID
					// FROM tblship_group_color WHERE shipmentpriceID='$spID' AND statusID='1'";
		// $insertcol = $this->conn->prepare($sql_col);
		// $insertcol->execute();
		
		$sql_csq = "INSERT INTO tblship_colorsizeqty_log(log_date, log_by, ID, shipmentpriceID, colorID, garmentID, size_name, qty, price, statusID)
					SELECT '$slogdate' as log_date, '$updatedby' as log_by, ID, shipmentpriceID, colorID, garmentID, size_name, qty, price, statusID
					FROM tblship_colorsizeqty WHERE shipmentpriceID='$spID' AND statusID='1'";
		
		// echo $sql_csq;
		$insertcsq = $this->conn->prepare($sql_csq);
		$insertcsq->execute();
		
}


//===============================================================//
//=============== CHECK QUERY AND RETURN DATA ===================//
//===============================================================//

public function getSizeNameColumnFromOrder($soID, $mode){ //-------- Get All Size Range From tblcolorsizeqty --------//
	$columnsql2 = $this->conn->prepare("select DISTINCT SizeName from tblcolorsizeqty where orderno = :orderno ORDER BY ID");
	$columnsql2->bindParam(':orderno', $soID);
	$columnsql2->execute();	
	
	$num_column = $columnsql2->rowCount();
	
	//---- Mode is 0, return total number of data row ----//
	if($mode == "0"){
		return $num_column;
	}
	//---- Mode is 1, return SizeName value from table ----//
	else{
		return $columnsql2;
	}
}

public function getSizeNameColumnFromSPID($shipmentpriceID, $mode){
	$query = "select scsq.size_name as SizeName, 
						(SELECT MIN(csq.ID) 
						 FROM tblcolorsizeqty csq 
						 WHERE csq.orderno = sp.Orderno AND csq.SizeName = scsq.size_name) as minID
				from tblship_colorsizeqty scsq
				INNER JOIN tblshipmentprice sp ON sp.ID = scsq.shipmentpriceID
				where scsq.shipmentpriceID =:shipmentpriceID AND scsq.qty>0 AND scsq.statusID=1
				group by scsq.size_name
				ORDER BY minID";
	$columnsql2 = $this->conn->prepare($query);
	$columnsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$columnsql2->execute();	
	
	$num_column = $columnsql2->rowCount();
	
	//---- Mode is 0, return total number of data row ----//
	if($mode == "0"){
		return $num_column;
	}
	//---- Mode is 1, return SizeName value from table ----//
	else{
		return $columnsql2;
	}
}

//-------- Get All Colors From tblcolorsizeqty --------//

public function getColorNameColumnFromOrder($soID, $mode, $garmentID){
	$sub_query = "";//($garmentID==""? "": " AND g.garmentID IN ($garmentID)");
	$limit_query = "";//($garmentID==""? "limit 0": "");
	
	$sql = "select DISTINCT g.garmentID, g.styleNo, c.colorID, 
											co.colorName, c.GTN_colorname,c.transfer_from_ia 
									from tblcolorsizeqty AS c 
									INNER JOIN tblcolor AS co ON c.colorID = co.ID 
									INNER JOIN tblgarment AS g ON g.garmentID = c.garmentID
									where c.orderno = :orderno $sub_query 
									ORDER BY g.garmentID, c.colorID ASC $limit_query";
	$rowsql2 = $this->conn->prepare($sql);
	
	$rowsql2->bindParam(':orderno', $soID);
	$rowsql2->execute();	
	
	$num_row = $rowsql2->rowCount();
	
	//---- Mode is 0, return total number of data row ----//
	if($mode == "0"){
		return $num_row;
	}
	//---- Mode is 1, return ColorName value from table ----//
	else{
		return $rowsql2;
	}
}

//-------- Get All Colors Size (Qty & Price)  From tblship_colorsizeqty --------//
//-------- shipmentmainClass.php --------//
public function getColorSizeQtyOfShipment($shipmentpriceID, $colorID, $size_name, $mode, $garmentID, $column){
	$prod_pocount = $this->prod_pocount;
	$from_location = ($prod_pocount>0 ? $this->from_location: "");
	$from_location = ($column=="price"? "": $from_location);
	
	$sub_query = ($garmentID==""? "": " AND scsq.garmentID IN ($garmentID)");
	$limit_query = ($garmentID==""? "limit 0": "");
	$sql = "select $column
									from tblship_colorsizeqty$from_location scsq
									where scsq.shipmentpriceID = :shipmentpriceID AND colorID=:colorID AND size_name=:size_name 
									AND scsq.statusID='1' $sub_query $limit_query";
	// echo "<pre>$sql</pre>";
	$rowsql2 = $this->conn->prepare($sql);
	$rowsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
	$rowsql2->bindParam(':colorID', $colorID);
	$rowsql2->bindParam(':size_name', $size_name);
	$rowsql2->execute();	
	$num_row = $rowsql2->rowCount();
	
	//---- Mode is 0, return total number of data row ----//
	if($mode == "0"){
		return $num_row;
	}
	//---- Mode is 1, return qty, price value from table ----//
	else{
		return $rowsql2;
	}
}

public function getColorSelectedForShipment($shipmentpriceID, $garmentID, $colorID, $group_num, $is_group, $mode){
	$sub_query = ($garmentID==""? "": " AND sgc.garmentID IN ($garmentID)");
	
	if($is_group==1){
		$rowsql2 = $this->conn->prepare("select * 
										from tblship_group_color sgc
										where sgc.shipmentpriceID = :shipmentpriceID AND colorID=:colorID 
										AND group_number=:group_number AND is_group=:is_group AND statusID='1' $sub_query");
		$rowsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
		$rowsql2->bindParam(':colorID', $colorID);
		$rowsql2->bindParam(':group_number', $group_num);
		$rowsql2->bindParam(':is_group', $is_group);
		$rowsql2->execute();
		$num_row = $rowsql2->rowCount();
	}
	else{
		$rowsql2 = $this->conn->prepare("select * 
										from tblship_group_color sgc
										where sgc.shipmentpriceID = :shipmentpriceID AND colorID=:colorID 
										AND is_group=:is_group AND statusID='1' $sub_query");
		$rowsql2->bindParam(':shipmentpriceID', $shipmentpriceID);
		$rowsql2->bindParam(':colorID', $colorID);
		//$rowsql2->bindParam(':group_number', $group_num);
		$rowsql2->bindParam(':is_group', $is_group);
		$rowsql2->execute();
		$num_row = $rowsql2->rowCount();
	}
	//---- Mode is 0, return total number of data row ----//
	if($mode == "0"){
		return $num_row;
	}
	//---- Mode is 1, return qty, price value from table ----//
	else{
		return $rowsql2;
	}
}

public function getWholeColorGrpFromShipment($shipmentpriceID, $mode, $garmentID){
	$sub_query = ($garmentID==""? "": " AND sgc.garmentID IN ($garmentID)");
	$row = $this->conn->prepare("select sgc.group_number
									FROM tblship_group_color  sgc
									INNER JOIN tblcolor c ON c.ID = sgc.colorID
									WHERE sgc.shipmentpriceID='$shipmentpriceID' AND sgc.statusID='1' $sub_query
									group by sgc.group_number order by sgc.group_number asc");
	$row->execute();
	$num_row = $row->rowCount();
	
	//---- Mode is 0, return total number of data row ----//
	if($mode==0){
		return $num_row;
	}
	else{
		return $row;
	}
}

public function getColorAndStyleName($colorAndGarment, $mode){
	$str_colorID=""; $str_gmtID="";
	
	if($colorAndGarment!=""){
		$arr_temp = explode(":", $colorAndGarment);
		$str_colorID = $arr_temp[0];
		$str_gmtID = $arr_temp[1];
	}
	
	$row = $this->conn->prepare("select c.ColorName as color, csq.GTN_colorname, g.styleNo as styling, csq.colorID, g.garmentID 
									FROM tblcolorsizeqty csq
									INNER JOIN tblgarment g ON g.garmentID = csq.garmentID
									INNER JOIN tblcolor c ON c.ID = csq.colorID 
									WHERE csq.colorID='$str_colorID' AND g.garmentID='$str_gmtID'
									group by csq.colorID, csq.garmentID");
	$row->execute();
	$num_row = $row->rowCount();
	
	//---- Mode is 0, return total number of data row ----//
	if($mode==0){
		return $num_row;
	}
	else{
		return $row;
	}
}

public function funcCalculateShipmentSalesAmount($soID, $mid, $is_group){
	$chk = $this->delAllTbl_colorsizeqty($mid);
	$from_location = $this->from_location;
	
	$this_style = "";
	$arr_colorsize_qty = array();
	$arr_colorgmt_qty = array();
	$num_color_column = $this->getColorNameColumnFromOrder($soID, "0", $this_style);
		$colorcolumnresult = $this->getColorNameColumnFromOrder($soID, "1", $this_style);				
		for($r=0;$r<$num_color_column;$r++){
			$rowtitle2=$colorcolumnresult->fetch(PDO::FETCH_ASSOC);
			$color = $rowtitle2['colorName'];
			$colorID = $rowtitle2['colorID'];
			$styleNo = $rowtitle2['styleNo'];
			$garmentID = $rowtitle2['garmentID'];
	
			$num_column = $this->getSizeNameColumnFromOrder($soID, "0");
			$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				$arr_colorsize_qty["$colorID=^$garmentID=^$size"] = 0;
				$arr_colorgmt_qty["$colorID=^$garmentID"] = 0;
			}//--- End For Col ---//	
		}//--- End For Row ---//

	$sql_pack = "SELECT PID, packing_method FROM tblship_packing$from_location WHERE shipmentpriceID='$mid' AND statusID='1'";
	$result_pack = $this->conn->prepare($sql_pack);
	$result_pack->execute();
	while($row_pack=$result_pack->fetch(PDO::FETCH_ASSOC)){
		$PID = $row_pack["PID"];
		$pack_method = $row_pack["packing_method"];
		//echo "$mid <=========== $pack_method <br/>";
		switch($pack_method){
			//---- Single Color Multi Size ----//
			//case 50:
			case 1: $sql_detail = "SELECT spd.total_qty, spd.polybag_qty_in_blisterbag, spd.blisterbag_in_carton, 
												spd.ratio_qty, spd.group_number, spd.size_name 
									FROM tblship_packing_detail$from_location spd
									WHERE spd.PID='$PID' AND spd.statusID='1'
									GROUP BY spd.group_number, spd.size_name";
					// echo "<pre>$sql_detail</pre>";
					$result_detail = $this->conn->prepare($sql_detail);
					$result_detail->execute();
					while($row_detail=$result_detail->fetch(PDO::FETCH_ASSOC)){
						$total_qty = $row_detail["total_qty"];
						$ratio_qty = $row_detail["ratio_qty"];
						$polybag_qty_in_blisterbag = $row_detail["polybag_qty_in_blisterbag"];
						$blisterbag_in_carton = $row_detail["blisterbag_in_carton"];
						$group_number = $row_detail["group_number"];
						$size_name = $row_detail["size_name"];
						$total_qty_in_carton = $polybag_qty_in_blisterbag * $blisterbag_in_carton;
						$able_pack = ($total_qty_in_carton==0? 0: ($total_qty % $total_qty_in_carton));
						
						$test2 = ($total_qty % $total_qty_in_carton);
						// echo "able_pack: $able_pack / $test2 | $total_qty % $total_qty_in_carton << <br/>";
						//--- If Ratio able to pack ---//
						if($able_pack==0){
							$this_qty = $total_qty / $total_qty_in_carton * $ratio_qty * $blisterbag_in_carton;
							// echo "$this_qty [$size_name] [$group_number] $total_qty / $total_qty_in_carton * $ratio_qty * $blisterbag_in_carton<br/>";
						}
						//--- Else Ratio got balance ---//
						else{
							$topup_total_qty = (floor($total_qty / $total_qty_in_carton) + 1) * $total_qty_in_carton;
							//$this_qty = $topup_total_qty / $total_qty_in_carton * $ratio_qty;
							$this_qty = (($total_qty_in_carton / $polybag_qty_in_blisterbag) * $ratio_qty) * (floor($total_qty / $total_qty_in_carton) + 1);
							$this_qty = ($total_qty / $polybag_qty_in_blisterbag * $ratio_qty);
							// echo "[$group_number] $size_name - $this_qty2 [$able_pack] / $total_qty_in_carton/$polybag_qty_in_blisterbag <br/>";
						}
							//------ Check Group Color ------//
							$sql_color = "SELECT sgc.garmentID, sgc.colorID 
											FROM tblship_group_color sgc 
											WHERE sgc.shipmentpriceID='$mid' AND sgc.is_group='$is_group' 
											AND sgc.statusID='1' AND sgc.group_number='$group_number'";
							// echo "<pre>$sql_color</pre>";
							$result_color = $this->conn->prepare($sql_color);
							$result_color->execute();
							while($row_color=$result_color->fetch(PDO::FETCH_ASSOC)){
								$garmentID = $row_color["garmentID"];
								$colorID = $row_color["colorID"];
								$arr_colorsize_qty["$colorID=^$garmentID=^$size_name"] += $this_qty;
								$arr_colorgmt_qty["$colorID=^$garmentID"] += $this_qty;
								//echo "[$group_number] [$colorID / $garmentID] $size_name => $this_qty [$ratio_qty]<br/>";
							}//--- End While tblship_group_color ---//
		
					}//--- End While tblship_packing_detail ---//
					
					break;
			//---- Multi Color Ratio Size & Multi Color ----//
			case 50:$sql_detail = "SELECT spd.total_qty, spd.polybag_qty_in_blisterbag, spd.blisterbag_in_carton, 
												spd.ratio_qty, spd.group_number, spd.size_name,
									(SELECT sum(spd2.ratio_qty) FROM tblship_packing_detail$from_location spd2 
									WHERE spd2.PID = spd.PID AND spd2.group_number = spd.group_number) as total_ratio
									FROM tblship_packing_detail$from_location spd
									WHERE spd.PID='$PID' AND spd.statusID='1'
									GROUP BY spd.group_number, spd.size_name";
					$result_detail = $this->conn->prepare($sql_detail);
					$result_detail->execute();
					while($row_detail=$result_detail->fetch(PDO::FETCH_ASSOC)){
						$total_qty = $row_detail["total_qty"];
						$ratio_qty = $row_detail["ratio_qty"];
						$total_ratio = $row_detail["total_ratio"];
						$polybag_qty_in_blisterbag = $row_detail["polybag_qty_in_blisterbag"];
						$blisterbag_in_carton = $row_detail["blisterbag_in_carton"];
						$blisterbag_in_carton = ($blisterbag_in_carton==0? 1: $blisterbag_in_carton);
						$group_number = $row_detail["group_number"];
						$size_name = $row_detail["size_name"];
						$total_qty_in_carton = $polybag_qty_in_blisterbag * $blisterbag_in_carton;
						$able_pack = ($total_qty % $total_ratio);
						
						if($group_number==4){
							// echo "[$size_name] // $able_pack // $total_qty / $total_ratio  <-=-=-=-=-=- <br/>";
						}
						//--- If Ratio able to pack ---//
						if($able_pack==0){
							if($total_qty>0){
								$this_qty = $total_qty / $total_ratio * $ratio_qty ;
								//echo "$this_qty [$size_name] [$group_number] $total_qty / $total_ratio * $ratio_qty<br/>";
							}
							else{
								$this_qty = 0;
							}
							
						}
						//--- Else Ratio got balance ---//
						else{
							$topup_total_qty = (floor($total_qty / $total_qty_in_carton) + 1) * $total_qty_in_carton;
							//$this_qty = $topup_total_qty / $total_qty_in_carton * $ratio_qty;
							$this_qty = (($total_qty_in_carton / $polybag_qty_in_blisterbag) * $ratio_qty) * (floor($total_qty / $total_qty_in_carton) + 1);
							$this_qty = ($total_qty / $polybag_qty_in_blisterbag * $ratio_qty);
							//echo "[$group_number] $size_name - $this_qty2 [$able_pack] / $total_qty_in_carton/$polybag_qty_in_blisterbag <br/>";
						}
							//------ Check Group Color ------//
							$sql_color = "SELECT sgc.garmentID, sgc.colorID 
											FROM tblship_group_color sgc 
											WHERE sgc.shipmentpriceID='$mid' AND sgc.is_group='$is_group' 
											AND sgc.statusID='1' AND sgc.group_number='$group_number'";
							$result_color = $this->conn->prepare($sql_color);
							$result_color->execute();
							while($row_color=$result_color->fetch(PDO::FETCH_ASSOC)){
								$garmentID = $row_color["garmentID"];
								$colorID = $row_color["colorID"];
								$arr_colorsize_qty["$colorID=^$garmentID=^$size_name"] += $this_qty;
								$arr_colorgmt_qty["$colorID=^$garmentID"] += $this_qty;
								if($this_qty>0 && $colorID==37898){
									// echo "[$group_number] [$colorID / $garmentID] $size_name => $this_qty [$ratio_qty]<br/>";
								}
							}//--- End While tblship_group_color ---//
		
					}//--- End While tblship_packing_detail ---//
					
					break;
			case 51:
			case 52:
			case 53:
			case 2: $sql_detail = "SELECT spd.total_qty, spd.polybag_qty_in_blisterbag, spd.blisterbag_in_carton, 
												spd.ratio_qty, spd.group_number, spd.size_name 
									FROM tblship_packing_detail$from_location spd
									WHERE spd.PID='$PID' AND spd.statusID='1'
									GROUP BY spd.group_number, spd.size_name";
					$result_detail = $this->conn->prepare($sql_detail);
					$result_detail->execute();
					while($row_detail=$result_detail->fetch(PDO::FETCH_ASSOC)){
						$this_qty = $row_detail["total_qty"];
						$size_name = $row_detail["size_name"];
						$group_number = $row_detail["group_number"];
						
						//------ Check Group Color ------//
							$sql_color = "SELECT sgc.garmentID, sgc.colorID 
											FROM tblship_group_color sgc 
											WHERE sgc.shipmentpriceID='$mid' AND sgc.is_group='$is_group' 
											AND sgc.statusID='1' AND sgc.group_number='$group_number'
											group by sgc.garmentID, sgc.colorID";
							$result_color = $this->conn->prepare($sql_color);
							$result_color->execute();
							while($row_color=$result_color->fetch(PDO::FETCH_ASSOC)){
								$garmentID = $row_color["garmentID"];
								$colorID = $row_color["colorID"];
								$arr_colorsize_qty["$colorID=^$garmentID=^$size_name"] += $this_qty;
								$arr_colorgmt_qty["$colorID=^$garmentID"] += $this_qty;
								
							}//--- End While tblship_group_color ---//
						
					}//--- End While tblship_packing_detail ---//
					break;
		}//--- End Switch ---//
		
	}//--- End While tblship_packing ---//
	
	$sql_qty = "UPDATE tblship_colorsizeqty$from_location scsq 
							SET scsq.statusID = '2'
							WHERE scsq.shipmentpriceID='$mid' ";
	$result_scqty = $this->conn->prepare($sql_qty);
	$result_scqty->execute();
	
	// echo "<pre>$sql_qty</pre>";
	// print_r($arr_colorsize_qty);
	
	foreach ($arr_colorsize_qty as $key => $total_qty){
		$arr_temp = explode("=^", $key);
		$colorID = $arr_temp[0];
		$garmentID = $arr_temp[1];
		$size_name = $arr_temp[2];
		$price = 0;
		$statusID = 1;
		
		$sql_sales_qty = "SELECT * FROM tblship_colorsizeqty$from_location scsq 
							WHERE scsq.shipmentpriceID='$mid' AND scsq.garmentID='$garmentID' 
							AND scsq.colorID='$colorID' AND scsq.size_name='$size_name'";
		$result_qty = $this->conn->prepare($sql_sales_qty);
		$result_qty->execute();
		$num_row = $result_qty->rowCount();
		
		// echo "$garmentID - $colorID - $size_name -  $total_qty << <br/>";
		
		//---- Insert New ----//
		if($num_row==0){
			$this->insertTblship_colorsizeqty($mid, $colorID, $garmentID, $size_name, $total_qty, $price, $statusID);
		}
		//---- Update ----//
		else{
			$row_qty = $result_qty->fetch(PDO::FETCH_ASSOC);
			$ID = $row_qty["ID"];
			$this->updateTblship_colorsizeqty($mid, $colorID, $garmentID, $size_name, $total_qty, $statusID, $ID);
		}
	
		//echo "$key [$colorID / $garmentID / $size_name] => $total_qty <br/>";
	}//--- End Foreach ---//
	
	foreach ($arr_colorgmt_qty as $key => $total_qty){
		$arr_temp = explode("=^", $key);
		$colorID = $arr_temp[0];
		$garmentID = $arr_temp[1];
		
		$sqlscsq = "select ifnull(sum(ticket_qty),0) as ticket_qty
					  from tblship_colorsizeqty 
					  where shipmentpriceID='$mid'
					  and garmentID = '$garmentID'
					  and colorID = '$colorID' AND statusID=1";
		$result_scsq = $this->conn->prepare($sqlscsq);
		$result_scsq->execute();
		$row_scsq = $result_scsq->fetch(PDO::FETCH_ASSOC);
			$ticket_qty = $row_scsq["ticket_qty"];
			$plan_qty   = ($ticket_qty>$total_qty? $ticket_qty: $total_qty);
		
		$sql_sales_qty = "UPDATE tblship_group_color sgc 
							SET sgc.total_qty_pcs='$total_qty', plan_qty='$plan_qty'
							WHERE sgc.shipmentpriceID='$mid' AND sgc.garmentID='$garmentID' 
							AND sgc.colorID='$colorID' AND sgc.statusID=1";
		$result_qty = $this->conn->prepare($sql_sales_qty);
		$result_qty->execute();
		
	}
	
	if($from_location=="" && $this->acctid!="0"){
		$chk = $this->funcUpdateAllColorSizeQty($soID);
	}
	
	return true;
}

public function funcUpdateAllColorSizeQty($orderno){
	$arr_sp = array();
	
	// $sqlsp = "SELECT ID 
				// FROM tblshipmentprice sp WHERE sp.Orderno='$orderno' AND sp.statusID=1";
	// $result = $this->conn->prepare($sqlsp);
	// $result->execute();
	// while($row=$result->fetch(PDO::FETCH_ASSOC)){
		// $spID = $row["ID"];
		// $arr_sp[] = $spID;
	// }
	
	// $str_spID = implode(",", $arr_sp);
	
	$sql = "SELECT * FROM tblcolorsizeqty csq WHERE csq.orderno='$orderno'";
	$result = $this->conn->prepare($sql);
	$result->execute();
	while($row=$result->fetch(PDO::FETCH_ASSOC)){
		$garmentID = $row["garmentID"];
		$colorID = $row["colorID"];
		$size_name = $row["SizeName"];
		$this_qty = $row["Qty"];
		
		$sql_ship = "SELECT sum(scsq.qty) as total_qty, sum(scsq.ticket_qty) as ticket_qty
					FROM tblship_colorsizeqty scsq 
					INNER JOIN tblshipmentprice sp ON sp.ID = scsq.shipmentpriceID
					WHERE sp.Orderno='$orderno' AND scsq.garmentID='$garmentID' 
					AND scsq.colorID='$colorID' AND scsq.size_name='$size_name' AND scsq.statusID='1' 
					AND sp.statusID='1' ";//AND sp.ID IN ($str_spID)
		// echo "<pre>$sql_ship</pre>";
		$result_ship = $this->conn->prepare($sql_ship);
		$result_ship->execute();
		$row_ship = $result_ship->fetch(PDO::FETCH_ASSOC);
			$this_total_qty  = $row_ship["total_qty"];
			$this_ticket_qty = $row_ship["ticket_qty"];
		
		// if($garmentID==3200){
			// echo "$garmentID - $colorID - $size_name => $this_total_qty <br/>";
		// }
		
		$columnsql2 = $this->conn->prepare("UPDATE tblcolorsizeqty SET Qty='$this_total_qty'
												WHERE orderno='$orderno' AND garmentID='$garmentID' 
												AND colorID='$colorID' AND SizeName='$size_name'");
		$columnsql2->execute();	
	}//--- End While ---//
	
	return true;
}
//===============================================================//
//=============== CHECK QUERY AND RETURN HTML ===================//
//===============================================================//
public function funcGetShipmentSalesAmountTable($soID, $mid, $formmode, $this_style, $Qunit=1){
	$html = "";
	$from_location = $this->from_location;
	
	//-------- If new style, display all color --------//
	if($mid==0){
		$sql_gmt = "SELECT group_concat(garmentID) as grp_gmt 
					FROM tblgarment WHERE orderno='$soID' group by orderno";
		$result_gmt = $this->conn->prepare($sql_gmt);
		$result_gmt->execute();
		$row_gmt = $result_gmt->fetch(PDO::FETCH_ASSOC);
			$this_style = $row_gmt["grp_gmt"]; 		
	}
	
	$arr_garment = explode(",", $this_style); 
	$lang = $this->lang;
	include("../../lang/{$lang}.php");
	
	$html .= "<b class='subTitle' id='sales_order_amt'>".$hdlang["summary_color_size"]."</b>";
	$html .= "<table class='tb_detail' id='tb_detail' cellspacing=0'>";
	$html .= "<tr class='titlebar'>";
		$html .= "<th class='titlecol'>".$hdlang["Color"]."</th>";//-- Color --//
						
			$num_column = $this->getSizeNameColumnFromOrder($soID, "0");
			$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
			$arr_size = array();
			$arr_size_total = array();
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				$html .= "<th>$size</th>";
				array_push($arr_size, $size);
				array_push($arr_size_total, 0);
							
			}
			$html .= "<th class='titlecol'>".$hdlang["Total"]."</th>";//-- Total --//
			$html .= "</tr>";
					
		$num_color_column = $this->getColorNameColumnFromOrder($soID, "0", $this_style);
		$colorcolumnresult = $this->getColorNameColumnFromOrder($soID, "1", $this_style);

		$html .= "<input type='hidden' name='advice_row' id='advice_row' value='$num_color_column' /><input type='hidden' name='advice_col' id='advice_col' value='$num_column' />";	//for advise (2018-07-02 w)
		
		for($r=0;$r<$num_color_column;$r++){
			$rowtitle2=$colorcolumnresult->fetch(PDO::FETCH_ASSOC);
			$color = $rowtitle2['colorName'];
			$alias_colorName = $rowtitle2["GTN_colorname"];
			$colorID = $rowtitle2['colorID'];
			$styleNo = $rowtitle2['styleNo'];
			$garmentID = $rowtitle2['garmentID'];
			$css_display = (in_array("$garmentID",$arr_garment)? "":"display:none");//-- Check whether selected garmentID --//
			$display_value = (in_array("$garmentID",$arr_garment)? "1":"0");//--- 0 is none display, 1 is display ---//
			
			//---- Check Whether selected Color by ckwai on 2018-08-07 ----//
			$sql = "SELECT count(*) FROM tblship_group_color sgc WHERE sgc.shipmentpriceID='$mid' AND statusID='1' AND colorID='$colorID'";
			$result = $this->conn->prepare($sql); 
			$result->execute(); 
			$number_of_rows = $result->fetchColumn(); 
			$css_display = ($number_of_rows==0? "display:none": $css_display);
			
			//csq_colorname : for advise (2018-07-02 w)
			$html .= "<tr id='tr$garmentID-$colorID' class='salesQtyTable$garmentID' style='$css_display'>";
			$html .= "<th class='topcolortd'><font size='1px'><i><b>$alias_colorName</b></i></font> &nbsp; $color &nbsp; </font> / &nbsp; <font color='blue'>$styleNo
												<input type='hidden' name='csq_colorname$r' id='csq_colorname$r' value='$color ($styleNo)' />
												<input type='hidden' name='csq_colorID$r' id='csq_colorID$r' value='$colorID' />
												<input type='hidden' name='csq_garmentID$r' id='csq_garmentID$r' value='$garmentID' />
												</th>";
						
			$color_total = 0;
			for($c=0;$c<$num_column;$c++){
				$this_size = $arr_size[$c];
				$sizepriceresult = $this->getColorSizeQtyOfShipment($mid, $colorID, $this_size, "1", $garmentID, "qty");
				$rowtitle2=$sizepriceresult->fetch(PDO::FETCH_ASSOC);
				$this_qty = $rowtitle2['qty'];
				$this_qty = ($this_qty==""? 0: $this_qty);
				$color_total += $this_qty;
				
				if($display_value==1){
					$arr_size_total[$c] += $this_qty;
				}			
				$html .= "<th class='topcolortd leftcolortd' id='csizetotal_{$r}_{$c}'>$this_qty</th>";
			}
			$html .= "<th class='topcolortd leftcolortd'>$color_total</th></tr>";
			$html .= "<tr id='tr_exp$garmentID-$colorID' class='salesQtyTable$garmentID' style='$css_display'>";
			$disabled_formmode = ($formmode==3? "disabled":"");
			$disabled_sync = ($formmode==3? "":"<a class='url_link' onclick='changeprice(mainprice{$r}.value, $r, $num_column)' $disabled_formmode >[V]</a>");
						
			//---- Preview Mode ----//
			if($formmode==0 || $from_location=="_prod"){
				$str_notice = ($Qunit==1 || $Qunit==0?" &nbsp;<font color='red'><b>(PC)</b></font>": 
											" &nbsp;<font color='red'><b>(SET)</b></font>");
				$html .= "<th>PO ".$hdlang["Price"]." ".$str_notice.":</th>";//-- Price --//
			}
			//---- Edit Mode ----//
			else if($formmode==1 || $formmode==2 || $formmode==3){
				$html .= "<th> &nbsp;PO ".$hdlang["Price"].": 
							<input type='hidden' id='display_soa_row$r' name='display_soa_row$r' value='$display_value' />
							<input type='number' id='mainprice{$r}' name='mainprice{$r}' step='any' min='0'
										class='txt_medium' value='0' style='width:70px;'/>
							$disabled_sync </th>";//-- Price --//
			}
						
			for($c=0;$c<$num_column;$c++){
				$this_size = $arr_size[$c];
				$sizepriceresult = $this->getColorSizeQtyOfShipment($mid, $colorID, $this_size, "1", $garmentID, "price");
				$rowtitle2=$sizepriceresult->fetch(PDO::FETCH_ASSOC);
				$this_price = $rowtitle2['price'];
				$this_price = ($this_price==""? "0.00": $this_price);
							
				//---- Preview Mode ----//
				if($formmode==0 || $from_location=="_prod"){
					$html .= "<th class='leftcolortd' style='background-color:#fff'>$this_price</th>";
				}
				//---- Edit Mode ----//
				else if($formmode==1 || $formmode==2 || $formmode==3){
					$html .= "<th class='leftcolortd' style='background-color:#fff'> 
								<input type='number' id='oriprice{$r}-{$c}' step='any' min='0'
										name='oriprice{$r}-{$c}' class='txt_medium' value='$this_price' style='width:70px;'  $disabled_formmode />
								<input type='hidden' name='csq_size_name$r-$c' id='csq_size_name$r-$c' value='$this_size' /></th>";
				}
			}
			$html .= "<th class='leftcolortd' style='background-color:#fff'></th></tr>";
					
		}
		//total row
			$html .= "<tr>";
			$html .= "<th class='topcolortd' style='background-color:#fff'>".$hdlang["Total"]."</th>";
			$total_all = 0;
			for($c=0;$c<count($arr_size_total);$c++){
				$this_size_total = $arr_size_total[$c];
				$total_all += $this_size_total;
				$html .= "<th class='topcolortd leftcolortd' style='background-color:#fff'>$this_size_total</th>";
			}
			$html .= "<th class='topcolortd leftcolortd' style='background-color:#fff'>$total_all
							<input type='hidden' name='size_range' id='size_range' value='$num_column' />
							<input type='hidden' name='color_range' id='color_range' value='$num_color_column' />
							</th>";
			$html .= "</tr></table><br/><br/>";
	
	return $html;
}

public function funcGetUPCTable($soID, $mid, $formmode, $this_style, $upc_count, $SUID, $APID){
	$html = "";
	$arr_garment = explode(",", $this_style); 
	$lang = $this->lang;
	include("../../lang/{$lang}.php");
	$disabled_upc = ($formmode==1||$formmode==2? "":"disabled"); //--- Check Whether Edit Mode ---//
	$k = $upc_count;
	
	// echo "$formmode << <br/>";
	
	$html .= "<hr/>";
	$html .= "<button type='button' class='btn btn-danger btn-xs' onclick='funcDeleteUPC(&#39;$k&#39;)' $disabled_upc>
						<span class='glyphicon glyphicon-trash'></span></button> <b class='subTitle'>UPC $upc_count</b> &nbsp; &nbsp; ";
	if($SUID>0){
		$html .= "<button type='button' class='btn btn-info btn-xs' 
							onclick='funcAjaxCopyUPCToAll(&#39;$soID&#39; , &#39;$mid&#39;)'>
							Copy UPC to All Buyer PO of $soID</button>";//onclick='funcAjaxCopyUPCToAll(&#39;$soID&#39; , &#39;$mid&#39;)'
	}
	$html .= "<input type='hidden' name='upc_SUID$k' id='upc_SUID$k' value='$SUID' />";
	
	$html .= "<select class='select_medium' name='sel_acc$k' id='sel_acc$k' style='text-align:left;color:#686868;display:none' $disabled_upc>";
	$html .= "<option value='0'>-- SELECT ACCESSORIES --</option>";
		$sql_acc = "SELECT ap.APID, act.Description as acc_content 
					FROM tblapurchase ap 
					INNER JOIN tblapurchase_detail apd ON apd.APID = ap.APID
					INNER JOIN tblasizecolor ascl ON ascl.ASCID = apd.ASCID
					INNER JOIN tblamaterial amt ON amt.AMID = ascl.AMID
                    INNER JOIN tblacontent act ON act.ID = amt.contentID
					WHERE ap.statusID='4' AND ap.orderno='$soID' group by ap.APID";
		$result_acc = $this->conn->prepare($sql_acc);
		$result_acc->execute();
		while($row_acc = $result_acc->fetch(PDO::FETCH_ASSOC)){
			$this_APID = $row_acc["APID"];
			$acc_content = $row_acc["acc_content"];
			$selected = ($this_APID == $APID? "selected":"");
			
			$html .= "<option value='$this_APID' $selected>APID: $this_APID - &nbsp; $acc_content</option>";
		}//--- End While ---//
	$html .= "</select>";
	
	$num_column = $this->getSizeNameColumnFromOrder($soID, "0"); //-- Size Total Number --//
	$num_color_column = $this->getColorNameColumnFromOrder($soID, "0", $this_style);//-- Color Total Number --//
	
	$html .= "<table class='tb_detail' id='tb_upc_main$k' cellspacing=0'>";
	$html .= "<tr class='titlebarupc'>";
		$html .= "<th class='titlecolupc'>".$hdlang["Color"]."
											<input type='hidden' name='upc_size_num$k' id='upc_size_num$k' value='$num_column' />
											<input type='hidden' name='upc_color_num$k' id='upc_color_num$k' value='$num_color_column' />
											</th>"; //-- Color --//
		
		$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
		$arr_size = array();
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				array_push($arr_size, $size);
				
				$html .= "<th class='titlecolupc'>$size</th>";
			}//--- For Size Name ---//
	$html .= "</tr>";
	
	
	$colorcolumnresult = $this->getColorNameColumnFromOrder($soID, "1", $this_style);
	for($r=0;$r<$num_color_column;$r++){
			$rowtitle2=$colorcolumnresult->fetch(PDO::FETCH_ASSOC);
			$color = $rowtitle2['colorName'];
			$alias_colorName = $rowtitle2['GTN_colorname'];
			$colorID = $rowtitle2['colorID'];
			$styleNo = $rowtitle2['styleNo'];
			$garmentID = $rowtitle2['garmentID'];
			$css_display = (in_array("$garmentID",$arr_garment)? "":"display:none");//-- Check whether selected garmentID --//
			$display_value = (in_array("$garmentID",$arr_garment)? "1":"0");//--- 0 is none display, 1 is display ---//
			
			
			$count_upc = 1;
			if($SUID!=0){
				$sql_upc = "SELECT SUDID, upc_code 
							FROM tblship_upc_detail 
							WHERE SUID='$SUID' AND shipmentpriceID='$mid' 
							AND garmentID='$garmentID' AND colorID='$colorID' AND statusID=1";
				$result_upc = $this->conn->prepare($sql_upc);
				$result_upc->execute();
				// $count_upc = $result_upc->rowCount();
			}
			
			// echo "$garmentID / $css_display / $count_upc / $SUID <br/> << ";
			if($count_upc>0){
			$html .= "<tr class='upcTable$k-$garmentID' id='tr_upc_grp$k-$r' style='$css_display'>";
			$html .= "<th class='topcolortdupc'><font size='1px'><i>$alias_colorName</i></font> &nbsp; $color &nbsp; / &nbsp; <font color='blue'>$styleNo</font>
												<input type='hidden' name='upc_colorID$k-$r' id='upc_colorID$k-$r' value='$colorID' />
												<input type='hidden' name='upc_garmentID$k-$r' id='upc_garmentID$k-$r' value='$garmentID' />
												<input type='hidden' name='upc_display$k-$r' id='upc_display$k-$r' value='$display_value' />
												</th>";
			for($c=0;$c<$num_column;$c++){
				$this_SUDID = 0;
				$this_size = $arr_size[$c];
				
				$sql_sud = "SELECT SUDID, upc_code , DIM
							FROM tblship_upc_detail 
							WHERE SUID='$SUID' AND shipmentpriceID='$mid' 
							AND garmentID='$garmentID' AND colorID='$colorID' 
							AND size_name='$this_size' AND statusID='1'";
				$result = $this->conn->prepare($sql_sud);
				$result->execute();
				$row = $result->fetch(PDO::FETCH_ASSOC);
				$upc_code = $row["upc_code"];
				$upc_dim = $row["DIM"];
				$SUDID = $row["SUDID"];
				$this_SUDID = ($SUDID==""? 0: $SUDID);
				
				$synlink = ($c==0 && $formmode==1? "<a onclick='funcSynUPC(&#39;upc_main_txt&#39; , &#39;$k&#39; , &#39;$r&#39;)' style='cursor:pointer;'>[V]</a>":"");
				$syndim = ($c==0 && $formmode==1? "<a onclick='funcSynUPC(&#39;upc_dim&#39; , &#39;$k&#39; , &#39;$r&#39;)' style='cursor:pointer;'>[V]</a>":"");
				$html .= "<th class='topcolortdupc'>
								<div style='width:170px;'>
									<input type='text' name='upc_main_txt$k-$r-$c' id='upc_main_txt$k-$r-$c' class='txt_medium' style='width:150px;display:inline;' value='$upc_code' />
									$synlink
								</div>
								<div style='width:170px;'>
									<input type='text' name='upc_dim$k-$r-$c' id='upc_dim$k-$r-$c' class='txt_medium' style='width:150px;display:inline;' value='$upc_dim' placeholder='DIM / COLLECTION#' />
									$syndim
								</div>

								<input type='hidden' name='upc_main_size$k-$r-$c' id='upc_main_size$k-$r-$c' value='$this_size' />
								<input type='hidden' name='upc_main_SUDID$k-$r-$c' id='upc_main_SUDID$k-$r-$c' value='$this_SUDID' />
								</th>"; //$disabled_upc //$disabled_upc // Take out disabled due Laty Case IA14385 many UPC missing through PO Matching 
			}//--- End For Col Size ---//
			$html .= "</tr>";
			
			}
	}//--- End For Row Color ---//
	
	$html .= "</table>";
	
	return $html;
}

public function chkUpdateColorSingle($soID, $mid, $new_grp, $garmentID, $colorID){ //--- Debug group number color purpose on 2018-08-01 ---//
	$sql = "SELECT sgc.group_number 
			FROM tblship_group_color sgc
			WHERE sgc.shipmentpriceID='$mid' AND garmentID='$garmentID' AND colorID='$colorID' 
			AND is_group='0' AND statusID='1'";
	$result = $this->conn->prepare($sql);
	$result->execute();	
	$num_row = $result->rowCount();
	if($num_row>0){
		$row=$result->fetch(PDO::FETCH_ASSOC);
		$old_grp = $row["group_number"];
		if($old_grp!=$new_grp){
			//echo "[$mid] $old_grp ==> $new_grp <br/>";
			$sql_update = "UPDATE tblship_packing_detail spd 
							INNER JOIN tblship_packing spk ON spk.PID = spd.PID
							INNER JOIN tblshipmentprice sp ON sp.ID = spk.shipmentpriceID
							SET spd.temp_grp='$new_grp'
							WHERE spd.statusID='1' AND sp.statusID='1' AND spk.statusID='1' 
							AND spd.group_number='$old_grp' AND sp.ID='$mid'";
			$result_update = $this->conn->prepare($sql_update);
			$result_update->execute();

			$sql_update = "UPDATE tblship_packing_detail_prod spd 
							INNER JOIN tblship_packing spk ON spk.PID = spd.PID
							INNER JOIN tblshipmentprice sp ON sp.ID = spk.shipmentpriceID
							SET spd.temp_grp='$new_grp'
							WHERE spd.statusID='1' AND sp.statusID='1' AND spk.statusID='1' 
							AND spd.group_number='$old_grp' AND sp.ID='$mid'";
			$result_update = $this->conn->prepare($sql_update);
			$result_update->execute();
			
			$sql_update_2 = "UPDATE tblcarton_picklist_head SET temp_grp='$new_grp' WHERE shipmentpriceID='$mid' AND group_number='$old_grp'";
			$result_update_2 = $this->conn->prepare($sql_update_2);
			$result_update_2->execute();
			
			$sql_update_2 = "UPDATE tblcarton_picklist_head_prod SET temp_grp='$new_grp' WHERE shipmentpriceID='$mid' AND group_number='$old_grp'";
			$result_update_2 = $this->conn->prepare($sql_update_2);
			$result_update_2->execute();
		}
	}//--- Num>0 ---//
}
//--- Debug group number color purpose on 2018-08-01 ---//
public function updateAllPickListColor($mid, $arr_color_info){
	$arr_grp_oldnew = array();
	
	$sql_acc = "SELECT spd.ID, spd.group_number, spd.temp_grp 
				FROM tblship_packing spk
				INNER JOIN tblship_packing_detail spd ON spk.PID = spd.PID
				INNER JOIN tblshipmentprice sp ON sp.ID = spk.shipmentpriceID
				WHERE spk.shipmentpriceID='$mid' AND spk.statusID='1' AND spd.statusID='1' AND sp.statusID='1'
				AND spd.temp_grp!= spd.group_number
				order by spd.group_number asc";
	$result_acc = $this->conn->prepare($sql_acc);
	$result_acc->execute();	
	while($row_acc = $result_acc->fetch(PDO::FETCH_ASSOC)){
		$ID           = $row_acc["ID"];
		$temp_grp     = $row_acc["temp_grp"];
		$group_number = $row_acc["group_number"];
		
		if($temp_grp!=0){
			$sql_update_spd = "UPDATE tblship_packing_detail SET group_number='$temp_grp' WHERE ID='$ID'";
			$result_update_spd = $this->conn->prepare($sql_update_spd);
			$result_update_spd->execute();	
			
			//---- Debug update latest group number (2024-02-15) ----//
			$sql_update_inv = "UPDATE tblbuyer_invoice_detail 
									SET group_number='$temp_grp' 
									WHERE shipmentpriceID='$mid' AND group_number='$group_number' AND del=0";
			$stmt_inv = $this->conn->prepare($sql_update_inv);
			$stmt_inv->execute();	
			
			$sql_update_inv = "UPDATE tblbuyer_invoice_payment_detail 
									SET group_number='$temp_grp' 
									WHERE shipmentpriceID='$mid' AND group_number='$group_number' AND del=0";
			$stmt_paymentinv = $this->conn->prepare($sql_update_inv);
			$stmt_paymentinv->execute();
			
			$sql_update_ctn = "UPDATE tblcarton_inv_payment_detail 
									SET group_number='$temp_grp' 
									WHERE shipmentpriceID='$mid' AND group_number='$group_number' AND del=0";
			$stmt_paymentinv = $this->conn->prepare($sql_update_ctn);
			$stmt_paymentinv->execute();
			
			$sql_update_ctn = "UPDATE tblcarton_inv_detail 
									SET group_number='$temp_grp' 
									WHERE shipmentpriceID='$mid' AND group_number='$group_number' AND del=0";
			$stmt_paymentinv = $this->conn->prepare($sql_update_ctn);
			$stmt_paymentinv->execute();
			
			$arr_grp_oldnew[] = "$temp_grp-$group_number";
			
		}
	}//--- End While ---//
	
	$sql_acc = "SELECT spd.ID, spd.temp_grp 
				FROM tblship_packing spk
				INNER JOIN tblship_packing_detail_prod spd ON spk.PID = spd.PID
				INNER JOIN tblshipmentprice sp ON sp.ID = spk.shipmentpriceID
				WHERE spk.shipmentpriceID='$mid' AND spk.statusID='1' AND spd.statusID='1' AND sp.statusID='1'
				AND spd.temp_grp!=spd.group_number";
	$result_acc = $this->conn->prepare($sql_acc);
	$result_acc->execute();	
	while($row_acc = $result_acc->fetch(PDO::FETCH_ASSOC)){
		$ID = $row_acc["ID"];
		$temp_grp = $row_acc["temp_grp"];
		
		if($temp_grp!=0){
			$sql_update_spd = "UPDATE tblship_packing_detail_prod SET group_number='$temp_grp' WHERE ID='$ID'";
			$result_update_spd = $this->conn->prepare($sql_update_spd);
			$result_update_spd->execute();	
		}
	}//--- End While ---//
	
	$sql_ctn = "SELECT shipmentpriceID, PID, ctn_num, temp_grp FROM tblcarton_picklist_head WHERE shipmentpriceID='$mid' AND temp_grp>0";
	$result_ctn = $this->conn->prepare($sql_ctn);
	$result_ctn->execute();
	while($row_ctn = $result_acc->fetch(PDO::FETCH_ASSOC)){
		$shipmentpriceID = $row_ctn["shipmentpriceID"];
		$PID = $row_ctn["PID"];
		$ctn_num = $row_ctn["ctn_num"];
		$temp_grp = $row_ctn["temp_grp"];
		
		if($temp_grp!=0){
			$sql_update_ctn = "UPDATE tblcarton_picklist_head SET group_number='$temp_grp', temp_grp='0' 
									WHERE shipmentpriceID='$mid' AND PID='$PID' AND ctn_num='$ctn_num'";
			$result_update_ctn = $this->conn->prepare($sql_update_ctn);
			$result_update_ctn->execute();	
		}
	}
	
	$sql_ctn = "SELECT shipmentpriceID, PID, ctn_num, temp_grp FROM tblcarton_picklist_head_prod WHERE shipmentpriceID='$mid' AND temp_grp>0";
	$result_ctn = $this->conn->prepare($sql_ctn);
	$result_ctn->execute();
	while($row_ctn = $result_acc->fetch(PDO::FETCH_ASSOC)){
		$shipmentpriceID = $row_ctn["shipmentpriceID"];
		$PID = $row_ctn["PID"];
		$ctn_num = $row_ctn["ctn_num"];
		$temp_grp = $row_ctn["temp_grp"];
		
		if($temp_grp!=0){
			$sql_update_ctn = "UPDATE tblcarton_picklist_head_prod SET group_number='$temp_grp', temp_grp='0' 
									WHERE shipmentpriceID='$mid' AND PID='$PID' AND ctn_num='$ctn_num'";
			$result_update_ctn = $this->conn->prepare($sql_update_ctn);
			$result_update_ctn->execute();	
		}
	}
	
	for($i=0;$i<count($arr_color_info);$i++){
		list($grp, $garmentID, $colorID) = explode(":::",$arr_color_info[$i]);
		
		$sql_grp = "UPDATE tblship_group_color SET group_number='$grp' 
					WHERE shipmentpriceID='$mid' AND garmentID='$garmentID' AND colorID='$colorID' 
					AND is_group='0' and statusID='1'";
		$result_update_grp = $this->conn->prepare($sql_grp);
		$result_update_grp->execute();	
		//echo "$sql_grp<br/>";
	}
	
	//---- Debug update latest group number for barcode master (2024-02-15) ----//
	$arr_CBDID = array();
	for($i=0;$i<count($arr_grp_oldnew);$i++){
		list($new_grp, $old_grp) = explode("-",$arr_grp_oldnew[$i]);
		
		$sqlbarcodeM = "SELECT cbmi.ID, cbmi.CBDID, cbmi.shipmentpriceID, 
									cbmi.PID, cbmi.group_number, cbmi.size_name, cbmi.qty, 
								(SELECT TRIM(sud.upc_code)
								FROM tblship_group_color sgc 
								INNER JOIN tblship_upc_detail sud ON sud.shipmentpriceID = sgc.shipmentpriceID 
																	AND sud.garmentID = sgc.garmentID
																	AND sud.colorID = sgc.colorID 
																	AND sud.statusID = 1
								WHERE sud.shipmentpriceID = cbmi.shipmentpriceID 
								AND sud.colorID = sgc.colorID AND sud.garmentID = sgc.garmentID AND sud.size_name = cbmi.size_name AND sgc.group_number = '$new_grp' AND sud.statusID=1 AND sud.upc_code!='' limit 1) as upc_code
						FROM tblcarton_barcode_master_info_d cbmi 
						WHERE cbmi.shipmentpriceID='$mid' AND cbmi.group_number='$old_grp'
						group by cbmi.ID
                        order by cbmi.ID asc";
		$result_bm = $this->conn->prepare($sqlbarcodeM);
		$result_bm->execute();	
		while($row_bm = $result_bm->fetch(PDO::FETCH_ASSOC)){
			extract($row_bm);
			
			$key = "k-$CBDID";
			
			if(!in_array("$new_grp**$size_name**$qty**$upc_code", $arr_CBDID[$key])){
				$arr_CBDID[$key][] = "$new_grp**$size_name**$qty**$upc_code";
			}
			
			$sqlupdate = "UPDATE tblcarton_barcode_master_info_d 
						SET group_number='$new_grp', upc_code='$upc_code'
						WHERE shipmentpriceID='$mid' AND ID='$ID' ";
			$result_update = $this->conn->prepare($sqlupdate);
			$result_update->execute();	
		}//-- End While --//
		
	}//-- End For --//
	
	foreach($arr_CBDID as $key=>$arr_carton_info){
		list($k, $CBDID) = explode("-", $key); 
		$str_carton_info = implode(",", $arr_carton_info); 
		
		$sqlupdate = "UPDATE tblcarton_barcode_master_info 
						SET carton_info='$str_carton_info'
						WHERE CBDID='$CBDID'";
		$result_update = $this->conn->prepare($sqlupdate);
		$result_update->execute();
	}//-- End foreach --//
}

public function funcGetColorList($soID, $mid, $this_style, $formmode, $acctid=0){
	$html = "";
	$arr_garment = explode(",", $this_style); 
	$lang = $this->lang;
	include("../../lang/{$lang}.php");
	$arr_color = array();
	$is_group = 0;
	$formmode_disabled = ($formmode==3 || $this->from_location=="_prod"? "disabled":"");
	$arr_color_info = array();
	
	$html .= "<b class='subTitle'>".$hdlang["Color"]."</b>";
	$html .= "<table class='tb_detail' id='tb_detail' cellspacing='0'>
				<tr class='titlebarcombo'>
					<th>".$hdlang["Color"]."</th>
					</tr>
				<tr>
					<th class='topcolortdcombo'><center>";
			$num_row = $this->getColorNameColumnFromOrder($soID, "0", $this_style);
			for($r=1;$r<=$num_row;$r++){						
				$rowresult2 = $this->getColorNameColumnFromOrder($soID, "1", $this_style);

				for($c=1; $c <= $num_row; $c++){
					$checked = "0";
					
					$row=$rowresult2->fetch(PDO::FETCH_ASSOC);
					if($r == $c){
						$scolorID = $row['colorID'];
						$scolorName = $row['colorName'];
						$alias_colorName = $row["GTN_colorname"];
						$garmentID = $row['garmentID'];
						$nstyleno = $row['styleNo'];
						$css_display = (in_array("$garmentID",$arr_garment)? "":"display:none");//-- Check whether selected garmentID --//
						$display_value = (in_array("$garmentID",$arr_garment)? "1":"0");//--- 0 is none display, 1 is display ---//
						if($display_value==1){
							array_push($arr_color, "$scolorID:$garmentID-1");
						}
						else{
							array_push($arr_color, "$scolorID:$garmentID-0");
						}
						
						$filter_query = " AND spk.shipmentpriceID='$mid' AND lcd.group_number='$r' AND lcd.shipmentpriceID='$mid'";
						$count_lc = $this->checkLCAssignmentExist($filter_query);
						$disabled_lc = ($count_lc!=""? "disabled": "");
						
						$colorfield = "CID";
						$bbb = "";
						$html .= "	<input type='hidden' name='group_number$r' id='group_number$r' value='$r' />
									<input type='hidden' name='display_singlecolor_row$r' id='display_singlecolor_row$r' value='$display_value' />
									<input type='hidden' name='2colorinput$r' id='2colorinput$r' value='$scolorID:$garmentID' />
									$count_lc
									<input type='checkbox' class='icheckbox_flat-blue checkhide_$garmentID' 
											onclick='funcUpdateQtyTbl(&#39;$garmentID&#39;, &#39;$scolorID&#39;);updateAllPickListColor()' 
										name='2colorcheck$r$colorfield$c' value='$scolorName ($nstyleno)' id='2colorcheck$r$colorfield$c' $formmode_disabled $disabled_lc
										style='$css_display'";
						$this->chkUpdateColorSingle($soID, $mid, $r, $garmentID, $scolorID); //--- Debug color grpID purpose on 2018-08-01 ---//
						array_push($arr_color_info, "$r:::$garmentID:::$scolorID");//--- Debug color grpID purpose on 2018-08-01 ---//
						
						//--- Check color whether selected ---//
						$count_color = 0;
						if($acctid!=0){
							$count_color = $this->getColorSelectedForShipment($mid, $garmentID, $scolorID, "$r", "$is_group", "0");
						}
						
						
						if(($count_color>0)  && ($css_display == "")){
							$html .= " checked ";
						}
						if($formmode==0){//--- Preview Mode ---//
							$html .= " disabled ";
						}
						
						$html .= "/><span style='margin-right:30px;$css_display' class='trhide_$garmentID'> $scolorName ($nstyleno)  <font size='1px'><i>$alias_colorName</i></font></span> "; // $garmentID / $scolorID
					}
				}//---- End For Loop ----//
			}//---- End For Loop ----//
		//print_r($arr_color_info);
		if($acctid!=0){
			$this->updateAllPickListColor($mid, $arr_color_info);//--- Debug color grpID purpose on 2018-08-01 ---//
		}
		else{
			// print_r($arr_color_info);
		}
		$html .= "</center><input type='hidden' name='total_color' id='total_color' value='$num_row'  /></th></tr></table>";
		
	return array($html, $arr_color);
}

public function funcGetComboColorList($soID, $mid, $this_style, $formmode){
	$html = "";
	$arr_garment = explode(",", $this_style); 
	$lang = $this->lang;
	include("../../lang/{$lang}.php");
	$formmode_disabled = ($formmode==3 || $this->from_location=="_prod"? "disabled":"");
	$arr_color = array();
	$is_group = 1;
	
	$num_row = $this->getColorNameColumnFromOrder($soID, "0", $this_style);
	$rowresult_gmt = $this->getColorNameColumnFromOrder($soID, "1", $this_style);
	$html .= "<b class='subTitle'>".$hdlang["combo_group"]."</b>
				<table class='tb_detail' id='tb_detail' cellspacing=0'>
				<tr class='titlebarcombo'>
					<input type='hidden' name='totalgroup' id='totalgroup' value='$num_row' />
					<th class='titlecolcombo'>".$hdlang["Group"]."</th>
					<th>".$hdlang["Color"]."</th>
					</tr>";
	for($r=1;$r<=$num_row;$r++){
		$row_gmt = $rowresult_gmt->fetch(PDO::FETCH_ASSOC);
		$this_garment = $row_gmt["garmentID"];
		$css_display = (in_array("$this_garment",$arr_garment)? "":"display:none");//-- Check whether selected garmentID --//
		$display_value = (in_array("$this_garment",$arr_garment)? "1":"0");//--- 0 is none display, 1 is display ---//
		
		$filter_query = " AND spk.shipmentpriceID='$mid' AND lcd.group_number='$r'";
		$count_lc = $this->checkLCAssignmentExist($filter_query);
		$disabled_lc = ($count_lc!=""? "disabled": "");
		
		$html .= "<tr style='$css_display'>	
					<th class='topcolortdcombo'>Group $r <input type='hidden' name='display_combocolor_row$r' id='display_combocolor_row$r' value='$display_value' />
					<input type='hidden' name='groupname$r' id='groupname$r' value='Group $r' >
					<input type='hidden' name='groupcolor$r' id='groupcolor$r' />
					<input type='hidden' name='groupgarmentID$r' id='groupgarmentID$r' >
					<input type='hidden' name='groupcolorcode$r' id='groupcolorcode$r'>
					<input type='hidden' name='group_number$r' id='group_number$r' value='$r'> $count_lc
					</th>
					<th class='topcolortdcombo'>";
					
			$rowresult2 = $this->getColorNameColumnFromOrder($soID, "1", $this_style);
			$arr_sub_color = array();
			for($c=1; $c <= $num_row; $c++){	
				$checked = "0";
				//edit mode, load combo checked
				$row=$rowresult2->fetch(PDO::FETCH_ASSOC);
						
				$scolorID = $row['colorID'];
				$scolorName = $row['colorName'];
				$alias_colorName = $row["GTN_colorname"];
				$garmentID = $row['garmentID'];
				$nstyleno = $row['styleNo'];
				
				$css_display = (in_array("$garmentID",$arr_garment)? "":"display:none");
				//--- Check color whether selected ---//
				$count_color = $this->getColorSelectedForShipment($mid, $garmentID, $scolorID, "$r", "$is_group", "0");
				$colorfield = "CID";
				$html .= "<input type='checkbox' class='icheckbox_flat-blue checkhide_$garmentID' 
								onclick='checkisselect(\"$r\", \"$c\", \"0\")' 
									name='colorcheck$r$colorfield$c' value='$scolorName ($nstyleno)' id='colorcheck$r$colorfield$c' $formmode_disabled
									style='$css_display' $disabled_lc";
				
				if($count_color>0 && in_array("$garmentID",$arr_garment)){
					$html .= " checked ";
					array_push($arr_sub_color, "$scolorID:$garmentID");
				}	
				if($formmode==0){//--- Preview Mode ---//
					$html .= " disabled ";
				}
				$html .= "/><span style='margin-right:30px;$css_display' class='trhide_$garmentID'> <i>$alias_colorName</i> $scolorName ($nstyleno)</span>";
						
				//hidden field record check box garment and color ID
				$html .= "<input type='hidden' value='$scolorID:$garmentID' id='colorinput$r$colorfield$c' name='colorinput$r$colorfield$c'/>";
		}//---- End For Loop ----//
		
		if(count($arr_sub_color)==0){
			array_push($arr_sub_color, "");
		}
		array_push($arr_color, $arr_sub_color);
		$html .= "<input type='hidden' name='grouptotal$r' id='grouptotal$r' value='1' />";
		$html .= "</th></tr>";
	}//---- End For Loop ----//
	
	$html .= "</table><br/><br/>";
	
	return array($html, $arr_color);
}

//==================================================================//
//================= Packing List Display Function ==================//
//==================================================================//
public function funcLinkToCalculator($soID, $PID, $pack_method, $is_standard, $packing_type){
	$lang = $this->lang;
	include("../../lang/{$lang}.php");
	$sql = "SELECT cpl.CCHID 
			FROM tblcarton_calculator_head cpl 
			INNER JOIN tblcarton_calculator_picklist ccp ON ccp.CCHID = cpl.CCHID
			WHERE cpl.PID='$PID' AND ccp.statusID=1";
	$result_pack = $this->conn->prepare($sql);
	$result_pack->execute();
	$num_column = $result_pack->rowCount();
	$str_label = (($num_column>0 )? "<span class='label label-danger label-xs'>Individual</span>":
									"<span class='label label-default label-xs'>Default</span>");//&& $pack_method!=50
	$btn_disabled = ($pack_method==50? "disabledddd":"");

	$str = "&nbsp; <a href='#' 
						onclick='func_displayCalculator(&#39;$soID&#39; , &#39;$PID&#39; , &#39;$pack_method&#39; , &#39;$is_standard&#39; , &#39;$packing_type&#39;)' 
							class='btn btn-default btn-xs' $btn_disabled >
							<span class='glyphicon glyphicon-inbox'></span> ".$hdlang["ctn_calculator"]."</a>
							&nbsp; $str_label";
			//../../form/ctn_index.php?orderno=$soID&PID=$PID&packmethod=$pack_method
	
	return $str;
}

public function funcGetPackFactor($orderno, $PID, $pack_method, $size_name, $prepack_qty, $is_standard, $packing_type, $grp_size=""){
	$sub_query = ""; $pack_factor=0;
	switch($pack_method){
		case 1:$sub_query = " AND ccp.prepack_qty='$prepack_qty' ";break; //--- Single Color Ratio Pack ---//
		case 2:$sub_query = " AND ccp.size_name='$size_name' ";break; //--- Single Color Single Pack ---//
		case 50:$sub_query = "";break; //--- Multi Color Ratio Pack ---//
	}
		
		//add variable "packing type" in query (2018-10-25 w)
		$sql = "SELECT cco.pack_factor
				FROM `tblcarton_calculator_picklist` ccp 
				INNER JOIN tblcarton_calculator_option cco ON cco.CCPID = ccp.CCPID
				INNER JOIN tblcarton_calculator_head cch ON cch.CCHID = ccp.CCHID
				WHERE ccp.PID='$PID' AND cco.selected='1' AND ccp.statusID='1' AND cco.statusID='1' 
				$sub_query AND ccp.packing_method='$pack_method' AND ccp.is_standard='$is_standard' AND cch.packing_type='$packing_type' AND cco.pack_factor>0 order by cco.pack_factor desc";
		// echo "<pre>$sql</pre><br/><br/>";
		$result_pack = $this->conn->prepare($sql);
			$result_pack->execute();
			$num_column = $result_pack->rowCount();
		if($num_column>0){ //--- Check whether got calculator Pack Factor for this pick list ---//
			$row_pack=$result_pack->fetch(PDO::FETCH_ASSOC);
			$pack_factor = $row_pack["pack_factor"];
		}
		else{ //--- if not, get Default Calculator Pack Factor ---//
			$query_size = ($pack_method==1 && $grp_size!=""? " AND TRIM(ccp.grp_size)='$grp_size'": "");
			$sql = "SELECT cco.pack_factor
				FROM `tblcarton_calculator_picklist` ccp 
				INNER JOIN tblcarton_calculator_option cco ON cco.CCPID = ccp.CCPID
				INNER JOIN tblcarton_calculator_head cch ON cch.CCHID = ccp.CCHID
				WHERE ccp.PID='0' AND cco.selected='1' AND ccp.statusID='1' AND cco.statusID='1' 
				$sub_query $query_size AND ccp.packing_method='$pack_method' AND cch.orderno='$orderno' AND ccp.is_standard='$is_standard' AND cch.packing_type='$packing_type' AND cco.pack_factor>0 
				order by cco.pack_factor desc";
			// echo "<pre>$sql</pre> <br/>";
			if($this->acctid==1210){
				// echo "<pre> total_qty_in_carton : $total_qty_in_carton actual_total_carton :  $actual_total_carton  this_factor : $this_factor </pre>";
				echo "<pre>$sql</pre>";
			}
			$result_pack = $this->conn->prepare($sql);
				$result_pack->execute();
				$num_column = $result_pack->rowCount();
			if($num_column>0){
				$row_pack=$result_pack->fetch(PDO::FETCH_ASSOC);
				$pack_factor = $row_pack["pack_factor"];
			}
		}
		
		if($PID==84272){
			// echo "<pre>".$sql."</pre> / $grp_size / $pack_factor <br/>";
		}
	
	return $pack_factor;
}

public function funcPickListMenuBar($k, $chk_polybag, $chk_blisterbag, $chk_ctn_blister, $formmode_disabled, 
									$chk_nonstandard, $chk_standard, $chk_standard_online, $pack_method, 
									$chk_last_ctn_by, $last_ctn_num_size, $packing_type, $ship_remark, $order_by_color=0){
	$html = "";
	$lang = $this->lang;
	$from_location = $this->from_location;
	$css_hide = ($from_location=="_prod"? "display:none":"");
	$disabled_prod = ($this->prod_pocount>0 && $css_hide==""? "disabled":"");
	include("../../lang/{$lang}.php");
	// $disabled_poly = ($pack_method==1? "disabled":"");
	$disabled_poly = "";
	
	$selected_flag = ($packing_type=="0"? "selected":"");
	$selected_hanger = ($packing_type=="1"? "selected":"");
	
	$disabled_last_ctn = ($chk_last_ctn_by=="checked"? "":"disabled");
	$html .= "<table class='table table-bordered' style='max-width:700px;min-width:700px;$css_hide' >
				<tr><td style='white-space:nowrap'>
						<input type='checkbox' id='chk_polybag$k' name='chk_polybag$k' $chk_polybag class='icheckbox_flat-blue' 
								onclick='chkTickedOfPolyBlisterBag(&#39;$k&#39;)' $disabled_poly $formmode_disabled $disabled_prod /> Poly Bag &nbsp; &nbsp; 
						<input type='checkbox' id='chk_blisterbag$k' name='chk_blisterbag$k' $chk_blisterbag $disabled_prod class='icheckbox_flat-blue' 
								onclick='chkTickedOfPolyBlisterBag(&#39;$k&#39;)' $formmode_disabled /> Blister Bag &nbsp; &nbsp; 
						<input type='checkbox' name='chk_ctn_blister$k' id='chk_ctn_blister$k' $disabled_prod
								class='icheckbox_flat-blue' $formmode_disabled $chk_ctn_blister /> Carton Blister Required &nbsp; &nbsp; 
					</td>";
	
	//$html .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ";
	$html .= "<td style='white-space:nowrap'>";
	$html .= "	<input type='radio' name='standard$k' onclick='updateAllPickListColor()' value='1' $chk_nonstandard  /> 
				".$hdlang["non_standard"]." &nbsp; &nbsp;"; 
	$html .= "	<input type='radio' name='standard$k' onclick='updateAllPickListColor()' value='0' $chk_standard  /> 
				Buyer ".$hdlang["standard"]." &nbsp; &nbsp; ";
	$html .= "	<input type='radio' name='standard$k' onclick='updateAllPickListColor()' value='2' $chk_standard_online /> 
				Buyer ".$hdlang["standard_online"]." &nbsp; &nbsp; 
				<br/>";
	$html .= "<center>Folding Method: <select name='packing_type$k' id='packing_type$k'  >";//$disabled_prod
				$sqlpm = "SELECT ID, Description 
							FROM tblpackingmethod 
							WHERE StatusID=1 or ID='$packing_type'";
				$result = $this->conn->prepare($sqlpm);
				$result->execute();
				while($row = $result->fetch(PDO::FETCH_ASSOC)){
					$ID = $row["ID"];
					$Description = $row["Description"];
					$selected = ($ID==$packing_type? "selected":"" );
					$html .= "<option value='$ID' $selected>$Description</option>";
				}
		$html .= "</select></center>";
	$html .= "</td>";
	if($pack_method==2){ //-- only SCSS needed --//
		$html .= "<td style='white-space:nowrap'><input type='checkbox' name='last_ctn_by_SCSS$k' id='last_ctn_by_SCSS$k' data-toggle='tooltip' 
													title=''
														class='icheckbox_flat-blue' value='1' $chk_last_ctn_by
														onclick='funcCheckLastCartonRequest(&#39;$k&#39;)' /> 
												Last carton by Single Color 
												<input type='number' min='0' name='last_ctn_num$k' id='last_ctn_num$k' 
														style='width:40px' $disabled_last_ctn
														value='$last_ctn_num_size' /> 
												Size pack
									<br><small style='margin-left:18px;'><i>* Last carton is always with mix size IF NOT TICK.</i></small>
												</td>";
		$checked = ($order_by_color==1? "checked": "");
		$html .= "<td style='white-space:nowrap'>
					<input type='checkbox' name='order_by_color$k' class='icheckbox_flat-blue' $checked  />
					Order by color group </td>";
	}
	//remark field (2018-10-25 w)
	$html .= "<td><b>Remark: </b><input type='text' name='ship_remark_{$k}' id='ship_remark_{$k}' value='{$ship_remark}' class='txt_medium' /></td>";
	$html .= "</tr></table>";
	return $html;
}

public function funcGetPickListSingleColorMultiSize($soID, $mid, $this_style, $formmode, $pack_count, $color_type, 
														$arr_color, $pack_method, $PID, $packing_type=1, $order_by_color=0){
	$html = "";
	$k = $pack_count;
	$lang = $this->lang;
	include("../../lang/{$lang}.php");
	$formmode_disabled = ($formmode==3? "disabled":""); 
	$chk_polybag = "";
	$chk_blisterbag = "checked";
	$chk_ctn_blister = "";
	$str_notice = ($color_type==0? " &nbsp; <font color='red'><i>*(Packing pack factor always first color)</i></font>":"");
	$chk_standard = "";
	$chk_nonstandard = "checked";
	$chk_standard_online = "";
	$btn_carton_calculator = "";
	$str_PID = "";
	$is_standard = 1;
	$this_PID = $PID;
	
	$filter_query = " AND lcd.PID='$PID'";
	$lbl_lc_assignment = $this->checkLCAssignmentExist($filter_query);
	
	$from_location = ($this->prod_pocount>0 ? $this->from_location:"");
	$css_hide = ($this->from_location=="_prod"? "display:none": "");
	$css_hide = ($this->prod_pocount>0 || $lbl_lc_assignment!=""? "display:none": "$css_hide");
	$GTN_styleno = "";
	
	$ship_remark = ''; $flag_sync = 0;
	//--- If Packing List Data From Database --//
	if($PID!=0){
		$columnsql2 = $this->conn->prepare("SELECT spk.*, sp.GTN_styleno, b.flag_sync
											FROM tblship_packing$from_location spk 
											INNER JOIN tblshipmentprice sp ON sp.ID =spk.shipmentpriceID
											INNER JOIN tblorder od ON od.Orderno = sp.orderno
											LEFT JOIN tblbuyer b ON b.BuyerID = od.buyerID
											WHERE spk.PID='$PID'");
		$columnsql2->execute();
		$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
			$flag_sync   = $row["flag_sync"];
			$GTN_styleno = $row["GTN_styleno"];
			$is_polybag = $row["is_polybag"];
			$is_blisterbag = $row["is_blisterbag"];
			$is_ctnblister = $row["is_ctnblister"];
			$tmode = $row["tmode"];
			$is_standard = $row["tmode"];
			$chk_polybag = ($is_polybag==1? "checked":"");
			$chk_blisterbag = ($is_blisterbag==1? "checked":"");
			$chk_ctn_blister = ($is_ctnblister==1? "checked":"");
			$chk_standard = ($tmode==0? "checked":"");
			$chk_nonstandard = ($tmode==1? "checked":"");
			$chk_standard_online = ($tmode==2? "checked":"");
			$ship_remark = $row["ship_remark"];
			$btn_carton_calculator = $this->funcLinkToCalculator($soID, $PID, $pack_method, $tmode, $packing_type);
			$str_PID = " <em>PID: <b><u>$PID</u></b></em> &nbsp; ";
	}
	
	// if($from_location=="_prod" && $prod_pocount==0){
		// $this_PID = 0;
	// }
	
	$html .= "<hr style='background-color:#bdbdbd;padding:1px' />";
	$html .= "<div>$lbl_lc_assignment <button type='button' style='$css_hide' class='btn btn-danger btn-xs' onclick='funcDeletePickList(&#39;$k&#39;)'>
						<span class='glyphicon glyphicon-trash'></span></button> &nbsp; 
					<input type='hidden' name='packingID$k' id='packingID$k' value='$this_PID' />
					<b class='subTitle'>".$hdlang["pick_list"]." $k </b>"; //--- Pick List ---//
	$html .= "<span class='glyphicon glyphicon-list-alt btntop editcode' data-toggle='modal' 
							data-target='#methodbox' data-id='$PID' data-id='$PID' data-backdrop='static' data-keyboard='false' title='".$hdlang["ship_12"]."' 
							style='display: inline-block'></span> &nbsp; "; //--- Packing Method Attachment ---//
							
	$html .= " $str_PID ".$hdlang["packing_method"].": ".$hdlang["SCRP"]." $str_notice $btn_carton_calculator"; //-- Packing Method --// //-- Single Color (Carton) - Prepack By Single Color Multiple Size --// 
	$html .= "<br/>";
	$chk_last_ctn_by = 0; $last_ctn_num_size=1;
	$html .= $this->funcPickListMenuBar($k, $chk_polybag, $chk_blisterbag, $chk_ctn_blister, $formmode_disabled, $chk_nonstandard, $chk_standard, $chk_standard_online, 
									$pack_method, $chk_last_ctn_by, $last_ctn_num_size, $packing_type, $ship_remark, $order_by_color);
	
	//-------------------------------------//
	//-------- Start Table Display --------//
	//-------------------------------------//
	$html .= "<table class='tb_detail pick_list' id='tb_detail' cellspacing=0'>";
	$html .= "<tr class='titlebar'>";
		$html .= "<th class='titlecol' rowspan='2' style='width:150px;min-width:150px'>".$hdlang["Color"]."</th>";//-- Color --//
		$html .= "<th class='titlecol' rowspan='2'>Total Qty of Color</th>";//-- Total Qty of Color --//
						
			$num_column = $this->getSizeNameColumnFromOrder($soID, "0");
			$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
			$arr_size       = array();
			$arr_size_total = array();
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				$html .= "<th colspan='2'>$size</th>";
				array_push($arr_size, $size);
				array_push($arr_size_total, 0);
							
			}
			
			$html .= "<th class='titlecol' rowspan='2' id='scss_1st$k'># of Ratio Pack<br/>in 1 Blister Bag</th>";//-- No# of Ratio Pack in 1 Blister Bag --//
			$html .= "<th class='titlecol' rowspan='2' id='scss_2nd$k'># of Blister Bag in 1 Carton</th>";//-- No# of Blister Bag in 1 Carton --//
			$html .= "<th class='titlecol' rowspan='2'>Total Qty in 1 Carton";//-- Total Qty in 1 Carton --//
				$html .= "<input type='hidden' name='size_count$k' id='size_count$k' value='$num_column' />
							<input type='hidden' name='pick_list_method$k' id='pick_list_method$k' value='$pack_method' /></th>";
			$html .= "<th class='titlecol' rowspan='2'>Total Qty of Color</th></tr>";
			$html .= "<tr class='titlebar'>";
			for($c=0;$c<$num_column;$c++){
				//$html .= "<th class='sub_title' >Total Qty</th>";
				$html .= "<th class='sub_title' >Ratio Qty</th>";
				$html .= "<th class='sub_title' id='scss_3rd$k-$c' ># of Gmt <br/>in 1 Poly Bag</th>";
			}
			
			$html .= "</tr>";
					
		//$num_color_column = $this->getColorNameColumnFromOrder($soID, "0", $this_style); //$this->getColorNameColumnFromOrder($soID, "0", $this_style);
		//$colorcolumnresult = $this->getColorNameColumnFromOrder($soID, "1", $this_style);//getWholeColorGrpFromShipment
								
		for($r=0;$r<count($arr_color);$r++){
			// $rowtitle2=$colorcolumnresult->fetch(PDO::FETCH_ASSOC);
			// $color = $rowtitle2['colorName'];
			// $colorID = $rowtitle2['colorID'];
			// $styleNo = $rowtitle2['styleNo'];
			// $garmentID = $rowtitle2['garmentID'];
			$row_display_css = "0";
			//--- Combo Color ---//
			if($color_type==0){
				$arr_grp_color = array();
				$str_all_color = "<div style=&#39;text-align:left&#39;>";
				for($col_num=0;$col_num<count($arr_color[$r]);$col_num++){
					$this_color_garment = $arr_color[$r][$col_num];
					array_push($arr_grp_color, $this_color_garment);
					
					$result_color = $this->getColorAndStyleName($arr_color[$r][$col_num], "1");
					$row_color = $result_color->fetch(PDO::FETCH_ASSOC);
						$str_color = $row_color["color"];
						$alias_colorName = $row_color["GTN_colorname"];
						$colorID = $row_color["colorID"];
						$str_styling = $row_color["styling"];
						$str_all_color .= "<font size=&#39;1px&#39;><i>$alias_colorName</i></font> &nbsp; $str_color ($str_styling) &nbsp; <br/>";
				}//--- End Load All color in one group ---//
				$str_all_color .= "</div>";
				$str_color_garment = implode(",", $arr_grp_color);
				
				$this_num = $r + 1;
				$display = "<font data-toggle='tooltip2' data-html='true' style='cursor:pointer' id='group_color$k-$r'
									title='$str_all_color' class='tt_large' >Group $this_num </font>
								<input type='hidden' name='group_number$k-$r' id='group_number$k-$r' value='$this_num' />
								<input type='hidden' name='sr_color_garment$k-$r' id='sr_color_garment$k-$r' value='$str_color_garment' />";//-- Group colorID & gmtID --//
				$row_display_css = ($arr_color[$r][0]==""? "display:none":"");//--- If Group Color not contains color, make it none display ---//
				$display_value = ($arr_color[$r][0]==""? "0":"1");//--- 0 is none display, 1 is display ---//
			}
			//--- Single Color ---//
			else{
				$arr_temp = explode("-",$arr_color[$r]);
				$str_col_gmt = $arr_temp[0];
				$str_checked = $arr_temp[1];
				$result_color = $this->getColorAndStyleName($str_col_gmt, "1");
				$row_color = $result_color->fetch(PDO::FETCH_ASSOC);
					$str_color = $row_color["color"];
					$alias_colorName = $row_color["GTN_colorname"];
					$str_styling = $row_color["styling"];
					$colorID = $row_color["colorID"];
					$garmentID = $row_color["garmentID"];
				
				$this_num = $r + 1;//$colorID."/".$garmentID;
				$display = "<font size='1px'><i> $alias_colorName</i></font> &nbsp;$str_color &nbsp; / &nbsp; <font color='blue' data-toggle='tooltip' title='' style='cursor:pointer'>$str_styling</font>
												<input type='hidden' id='sr_color$k-$r' name='sr_color$k-$r' value='$colorID' />
												<input type='hidden' id='sr_garment$k-$r' name='sr_garment$k-$r' value='$garmentID' />
												<input type='hidden' id='group_number$k-$r' name='group_number$k-$r' value='$this_num'  />
												";
				$row_display_css = ($str_checked=="0"? "display:none":"");//--- If Group Color not contains color, make it none display ---//
				$display_value = ($str_checked==""? "0":"1");//--- 0 is none display, 1 is display ---//
			}
			
			//--- If Packing List Data From Database --//
			$this_col_total_qty = 0; $this_polybag_qty_in_blisterbag=0; $this_blisterbag_in_carton=0; 
			$this_SKU="";$master_upc="";$case_upc="";
			if($PID!=0){
				$columnsql2 = $this->conn->prepare("SELECT total_qty, polybag_qty_in_blisterbag, blisterbag_in_carton, SKU, master_upc, case_upc FROM tblship_packing_detail$from_location WHERE PID='$PID' AND group_number='$this_num' AND statusID='1' limit 1");
				$columnsql2->execute();
				$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
					$this_col_total_qty = $row["total_qty"];
					$this_polybag_qty_in_blisterbag = $row["polybag_qty_in_blisterbag"];
					$this_blisterbag_in_carton = $row["blisterbag_in_carton"];
					$this_SKU = $row["SKU"];
					$master_upc = $row["master_upc"];
					$case_upc = $row["case_upc"];
			}
			
			$row_qty = 1;
			if($GTN_styleno!="" && $flag_sync==1){ //if link from SPS, then only display qty > 0 
				$row_qty = $this_col_total_qty;
			}
			
			//--- Check Pack Factor Qty ---// Modified by ckwai on 2018-07-24
				//----- Check From Pack Factor Qty -----//
				/*$sql_factor = "SELECT amount, tmode FROM tblpackfactor WHERE orderno='$soID' AND colorID = '$colorID' AND size='color' AND del='0'";
				$result_factor = $this->conn->prepare($sql_factor);
				$result_factor->execute();
				$html_packfactor = "";
				while($row_factor = $result_factor->fetch(PDO::FETCH_ASSOC)){
					$this_tmode = $row_factor["tmode"];
					$this_factor = $row_factor["amount"];
					$this_factor = ($this_factor==""? 0: $this_factor);
					$html_packfactor .= "<input type='text' id='pick_list_tmode$k-$r-$this_tmode' value='$this_factor' />";
				}//--- End While ---//*/
				
				// Modified by ckwai on 2018-07-24
			$size_name = ""; $html_packfactor = ""; $prepack_qty = $this_polybag_qty_in_blisterbag; $tt0=0; $tt1=1;
			
			
			if($row_qty>0){
			$sqlasn = "SELECT ifnull(sum(ShipQty * PackValue),0) as sum_asn
						FROM `tblasn_shipment_orderdetail` 
						WHERE PID = '$PID' AND ConsumerPackageCode='$this_SKU' AND del=0 AND ASOHID>0";
			$stmt_asn = $this->conn->prepare($sqlasn);
			$stmt_asn->execute();
			$count_asn = $stmt_asn->rowCount();
			$rowasn = $stmt_asn->fetch(PDO::FETCH_ASSOC);
					$sum_asn   = $rowasn["sum_asn"];
					$asn_min_qty = $sum_asn;
					
			$onchange = ($count_asn>0? "getMinASNQty($k, $r)":"");
			
				
			$readonly = ($GTN_styleno=="" || $flag_sync==0? "": "readonly");
			$html .= "<tr id='color_row$k-$r' style='$row_display_css' >"; //-- Single Color (pickListID - $colorID), Combo Color (pickListID - groupID) --//
			$html .= "<th class='topcolortd' id='color_display$k-$r'> $display
											<input type='hidden' name='display_scms_row$k-$r' id='display_scms_row$k-$r' value='$display_value' />
											</th>";
			$html .= "<th class='topcolortd'>
									<input type='hidden' name='asn_min$k-$r' id='asn_min$k-$r' value='$asn_min_qty'>
									<input type='hidden' name='count_asn$k-$r' id='count_asn$k-$r' value='$count_asn'>
									<table border='0'>
									<tr>
										<td style='border:0px solid #000' align='right'>Total Qty:</td>
										<td style='border:0px solid #000'><input type='number' name='txt_total_color$k-$r' id='txt_total_color$k-$r' min='0' onclick='funcRatioQty(&#39;$k&#39; , &#39;$r&#39;)' onchange='$onchange' onkeyup='funcRatioQty(&#39;$k&#39; , &#39;$r&#39;)'	class='txt_medium' style='width:120px' value='$this_col_total_qty'
									$formmode_disabled /></td>
										</tr>
									<tr>
										<td style='border:0px solid #000' align='right'>CODES:</td>
										<td style='border:0px solid #000'><input type='text' name='txt_SKU$k-$r' id='txt_SKU$k-$r' class='txt_medium' style='width:120px' placeholder='CODES...' value='$this_SKU' $readonly /></td>
										</tr>
									<tr>
										<td style='border:0px solid #000' align='right'>Master UPC:</td> 
										<td style='border:0px solid #000'><input type='text' name='master_upc$k-$r' id='master_upc$k-$r' class='txt_medium' style='width:120px' placeholder='Master UPC...' value='$master_upc' /></td>
										</tr>
									<tr>
										<td style='border:0px solid #000' align='right'>Case UPC:</td>
										<td style='border:0px solid #000'><input type='text' name='case_upc$k-$r' id='case_upc$k-$r' class='txt_medium' style='width:120px' placeholder='Case UPC...' value='$case_upc' /></td>
										</tr>
										</table>
									</th>";
						
			$color_total = 0; $arr_size_ratio = array();
			for($c=0;$c<$num_column;$c++){
				$this_size = $arr_size[$c];
				
				//--- If Packing List Data From Database --//
				$this_ratio_qty = 0;
				$this_total_qty = 0;
				$this_gmt_qty_in_polybag = 1;
				if($PID!=0){
					$columnsql2 = $this->conn->prepare("SELECT ratio_qty, gmt_qty_in_polybag, total_qty 
														FROM tblship_packing_detail$from_location 
														WHERE PID='$PID' AND group_number='$this_num' AND size_name='$this_size' AND statusID=1");
					$columnsql2->execute();
					$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
						$this_ratio_qty = $row["ratio_qty"];
						$this_gmt_qty_in_polybag = $row["gmt_qty_in_polybag"];
						$this_total_qty = $row["total_qty"];
				}
				
				if($this_ratio_qty>0){
					$arr_size_ratio[] = $this_size;
				}
				
				// $html .= "<th class='topcolortd leftcolortd' style='background-color:#fff;'>
								// <input type='number' id='total_qty$k-$r-$c' name='total_qty$k-$r-$c' min='0' onclick='funcRatioQty(&#39;$k&#39; , &#39;$r&#39;)'
										// onkeyup='funcRatioQty(&#39;$k&#39; , &#39;$r&#39;)' class='txt_medium' style='width:50px;' value='$this_total_qty' /></td>";
				$html .= "<th class='topcolortd leftcolortd' style='background-color:#fff;'>
								<input type='number' id='ratio_qty$k-$r-$c' name='ratio_qty$k-$r-$c' min='0'  value='$this_ratio_qty'
										class='txt_medium' style='width:50px;' onclick='funcRatioQty(&#39;$k&#39; , &#39;$r&#39;)'
										onkeyup='funcRatioQty(&#39;$k&#39; , &#39;$r&#39;)' $formmode_disabled $readonly /></th>";
				$html .= "<th class='topcolortd' style='background-color:#fff;'>
								<input type='text' id='pb_qty$k-$r-$c' name='pb_qty$k-$r-$c' onclick='funcRatioQty(&#39;$k&#39; , &#39;$r&#39;)'
										onkeyup='funcRatioQty(&#39;$k&#39; , &#39;$r&#39;)' class='txt_medium' style='width:50px;' 
										min='0' value='$this_gmt_qty_in_polybag' $readonly />
								<input type='hidden' id='size$k-$r-$c' name='size$k-$r-$c' class='txt_medium' value='$this_size' />
								</th>";
			}//--- End Size Loop ---//
			
			$grp_size = implode(",", $arr_size_ratio);
			$this_factor = $this->funcGetPackFactor($soID, $PID, $pack_method, $size_name, $prepack_qty, $is_standard, $packing_type, $grp_size);
			$html_packfactor .= "<input type='hidden' id='pick_list_tmode$k-$r-$tt0' value='$this_factor' />";
			$html_packfactor .= "<input type='hidden' id='pick_list_tmode$k-$r-$tt1' value='$this_factor' />";
			
			//--- # of ratio pack in one Blister Bag ---//
			$html .= "<th class='topcolortd leftcolortd' style='background-color:#fff'>
							$html_packfactor
							<input type='number' id='totalQtyOfRatio$k-$r' name='totalQtyOfRatio$k-$r' min='0' value='$this_polybag_qty_in_blisterbag' 
									class='txt_medium' style='width:50px' readonly /></th>";
			$html .= "<th class='topcolortd leftcolortd' style='background-color:#fff'>
							<input type='number' id='bbInCarton$k-$r' name='bbInCarton$k-$r' min='0' 
									onclick='funcRatioQty(&#39;$k&#39; , &#39;$r&#39;)' value='$this_blisterbag_in_carton' $formmode_disabled readonly
									class='txt_medium' style='width:50px' onkeyup='funcRatioQty(&#39;$k&#39; , &#39;$r&#39;)'  /></th>";//$this_blisterbag_in_carton
			$html .= "<th class='topcolortd leftcolortd' style='background-color:#fff'>
							<input type='text' id='gmtQtyInCarton$k-$r' name='gmtQtyInCarton$k-$r' class='txt_medium' style='width:50px' readonly />
							/ <font color='red' id='str_factor_qty$k-$r'>$this_factor</font> <input type='hidden' id='factor_qty$k-$r' value='$this_factor' /> </th>";
			$html .= "<th class='topcolortd leftcolortd' style='background-color:#fff'>
							<input type='text' id='total_color_qty$k-$r' name='total_color_qty$k-$r' class='txt_medium' style='width:50px' readonly /></th>";
			$html .= "</tr>";
			
			}//-- end check row qty > 0 --//
			
		}//---- End Color Loop ----//
		//total row
			$html .= "<tr>";
			$html .= "<th class='topcolortd'></th>";
			$html .= "<th class='topcolortd'></th>";
			$colspan_num = (count($arr_size_total) * 2) + 1;
			$html .= "<th class='topcolortd leftcolortd' colspan='$colspan_num' style='background-color:#fff'></th>";
			//$html .= "<th class='topcolortd leftcolortd'></th>";
			$html .= "<th class='topcolortd leftcolortd' style='background-color:#fff'></th>";
			$html .= "<th class='topcolortd leftcolortd'>".$hdlang["Total"]."</th>";
			$html .= "<th class='topcolortd leftcolortd'><input type='text' name='total_qty$k' id='total_qty$k' class='txt_medium' style='width:50px' readonly /></th>";
			$html .= "</tr></table><br/><br/>";
	
	echo $html;
}

public function funcGetPickListSingleColorSingleSize($soID, $mid, $this_style, $formmode, $pack_count, $color_type, 
														$arr_color, $pack_method, $PID, $packing_type, $order_by_color=0){
	$html = "";
	$k = $pack_count;
	$lang = $this->lang;
	include("../../lang/{$lang}.php");
	$formmode_disabled = ($formmode==3? "disabled":""); 
	$chk_polybag = "checked";
	$chk_blisterbag = "checked";
	$chk_ctn_blister = "";
	$chk_standard = "checked";
	$chk_nonstandard = "";
	$btn_carton_calculator = "";
	$str_PID = "";
	$is_standard = 1;
	$last_ctn_num_size = 1;
	$this_PID = $PID;
	$chk_last_ctn_by = "checked";
	
	$filter_query = " AND lcd.PID='$PID'";
	$lbl_lc_assignment = $this->checkLCAssignmentExist($filter_query);
	
	$from_location = ($this->prod_pocount>0 ? $this->from_location:"");
	$css_hide = ($this->from_location=="_prod"? "display:none": "");
	$css_hide = ($this->prod_pocount>0 || $lbl_lc_assignment!=""? "display:none": "$css_hide");
	
	//--- If Packing List Data From Database ---//
	if($PID!=0){
		$columnsql2 = $this->conn->prepare("SELECT spk.*, sp.GTN_styleno, b.flag_sync
											FROM tblship_packing$from_location spk 
											INNER JOIN tblshipmentprice sp ON sp.ID =spk.shipmentpriceID
											INNER JOIN tblorder od ON od.Orderno = sp.orderno
											LEFT JOIN tblbuyer b ON b.BuyerID = od.buyerID
											WHERE spk.PID='$PID'");
		$columnsql2->execute();
		$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
			$flag_sync = $row["flag_sync"];
			$GTN_styleno = $row["GTN_styleno"];
			$is_polybag = $row["is_polybag"];
			$is_blisterbag = $row["is_blisterbag"];
			$is_ctnblister = $row["is_ctnblister"];
			$last_ctn_by_SCSS = $row["last_ctn_by_SCSS"];
			$last_ctn_num_size = $row["last_ctn_num_size"];
			$tmode = $row["tmode"];
			$is_standard = $row["tmode"];
			$chk_polybag = ($is_polybag==1? "checked":"");
			$chk_blisterbag = ($is_blisterbag==1? "checked":"");
			$chk_ctn_blister = ($is_ctnblister==1? "checked":"");
			$chk_standard = ($tmode==0? "checked":"");
			$chk_nonstandard = ($tmode==1? "checked":"");
			$chk_standard_online = ($tmode==2? "checked":"");
			$chk_last_ctn_by = ($last_ctn_by_SCSS==1? "checked":"");
			$ship_remark = $row["ship_remark"];			
			$btn_carton_calculator = $this->funcLinkToCalculator($soID, $PID, $pack_method, $tmode, $packing_type);
			$str_PID = " <em>PID: <b><u>$PID</u></b></em> &nbsp; ";
	}
	else{
		$GTN_styleno = "";
		$columnsql2 = $this->conn->prepare("SELECT buyerID FROM tblorder WHERE orderno='$soID'");
		$columnsql2->execute();
		$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
			$buyerID = $row["buyerID"];
			$chk_standard = ($buyerID=="B12"? "checked":"");
			$chk_nonstandard = ($tmode=="B12"? "":"checked");
	}
	
	// if($from_location=="_prod" && $prod_pocount==0){
		// $this_PID = 0;
	// }
	
	$html .= "<hr style='background-color:#bdbdbd;padding:1px' />";
	$html .= "<div>$lbl_lc_assignment <button type='button' style='$css_hide' class='btn btn-danger btn-xs' onclick='funcDeletePickList(&#39;$k&#39;)'>
						<span class='glyphicon glyphicon-trash'></span></button> &nbsp; 
					<input type='hidden' name='packingID$k' id='packingID$k' value='$this_PID' />
					<b class='subTitle'>".$hdlang["pick_list"]." $k </b>";//--- Pick List ---//
	$html .= "<span class='glyphicon glyphicon-list-alt btntop editcode' data-toggle='modal' 
							data-target='#methodbox' data-id='$PID' data-backdrop='static' data-keyboard='false' title='".$hdlang["ship_12"]."' 
							style='display: inline-block'></span> &nbsp; "; //--- Packing Method Attachment ---//
							
	$html .= " $str_PID ".$hdlang["packing_method"].": ".$hdlang["SCSS"]." $btn_carton_calculator"; //-- Packing Method --// //-- Single Color (Carton) - Prepack Single Size --// 
	$html .= "<br/>";
	$html .= $this->funcPickListMenuBar($k, $chk_polybag, $chk_blisterbag, $chk_ctn_blister, $formmode_disabled, $chk_nonstandard, $chk_standard, $chk_standard_online, 
									$pack_method, $chk_last_ctn_by, $last_ctn_num_size, $packing_type, $ship_remark, $order_by_color);
	//-------------------------------------//
	//-------- Start Table Display --------//
	//-------------------------------------//
	$html .= "<table class='tb_detail pick_list' id='tb_detail' cellspacing=0'>";
	//--- Row 1 ---//
	$html .= "<tr class='titlebar2'>";
		$html .= "<th class='topcolortd2' rowspan='2'>".$hdlang["Color"]."</th>";//-- Color Group --//
						
			$num_column = $this->getSizeNameColumnFromOrder($soID, "0");
			$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
			$arr_size = array();
			$arr_size_total = array();
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				$html .= "<th class='topcolortd2' colspan='5'>$size</th>";
				array_push($arr_size, $size);
				array_push($arr_size_total, 0);
							
			}//--- End For Loop Size Range ---//
			
			$html .= "<th class='topcolortd2' rowspan='2'>".$hdlang["Total"]."";//-- Total --//
				$html .= "<input type='hidden' name='size_count$k' id='size_count$k' value='$num_column' />
							<input type='hidden' name='pick_list_method$k' id='pick_list_method$k' value='$pack_method' /></th></tr>";
			
			//--- Row 2 ---//				
			$html .= "<tr class='titlebar2'>";
			for($c=0;$c<$num_column;$c++){
				$html .= "<th class='sub_title' >Total Qty</th>";
				$html .= "<th class='sub_title' id='scss_3rd$k-$c' ># of Gmt <br/>in 1 Poly Bag</th>";
				$html .= "<th class='sub_title' id='scss_4th$k-$c' ># of P.Bag <br/>in 1 B.Bag</th>";
				$html .= "<th class='sub_title' id='scss_2nd$k-$c' ># of B.Bag <br/>in 1 Carton</th>";
				$html .= "<th class='sub_title' id='' >Total Qty<br/>in 1 Carton</th>";
			}
			
			$html .= "</tr>";
		// echo $arr_color." << ";
		for($r=0;$r<count($arr_color);$r++){
			$row_display_css = "0";
			
			// echo "$r << $arr_color[$r] [$color_type]";
			
			if($this->acctid!=0){
			//--- Combo Color ---//
			if($color_type==0){
				$arr_grp_color = array();
				$str_all_color = "<div style=&#39;text-align:left&#39;>";
				for($col_num=0;$col_num<count($arr_color[$r]);$col_num++){
					$this_color_garment = $arr_color[$r][$col_num];
					array_push($arr_grp_color, $this_color_garment);
					
					$result_color = $this->getColorAndStyleName($arr_color[$r][$col_num], "1");
					$row_color = $result_color->fetch(PDO::FETCH_ASSOC);
						$garmentID = $row_color["garmentID"];
						$colorID = $row_color["colorID"];
						$str_color = $row_color["color"];
						$alias_colorName = $row_color["GTN_colorname"];
						$str_styling = $row_color["styling"];
						$str_all_color .= "<font size=&#39;1px&#39;><i>$alias_colorName</i></font> &nbsp; $str_color ($str_styling) &nbsp; <br/>";
				}//--- End Load All color in one group ---//
				$str_all_color .= "</div>";
				$str_color_garment = implode(",", $arr_grp_color);
				
				$this_num = $r + 1;
				$display = "<font data-toggle='tooltip2' data-html='true' style='cursor:pointer' id='group_color$k-$r'
									title='$str_all_color' class='tt_large' >Group $this_num </font>
								<input type='hidden' name='group_number$k-$r' id='group_number$k-$r' value='$this_num' />
								<input type='hidden' name='sr_group$k-$r' id='sr_group$k-$r' value='$r' />
								<input type='hidden' name='sr_color_garment$k-$r' id='sr_color_garment$k-$r' value='$str_color_garment' />";//-- Group colorID & gmtID --//
				$row_display_css = ($arr_color[$r][0]==""? "display:none":"");//--- If Group Color not contains color, make it none display ---//
				$display_value = ($arr_color[$r][0]==""? "0":"1");//--- 0 is none display, 1 is display ---//
			}
			//--- Single Color ---//
			else{
				$arr_temp = explode("-",$arr_color[$r]);
				$str_col_gmt = $arr_temp[0];
				$str_checked = $arr_temp[1];
				
				// if($str_checked=="0"){
					// continue;
				// }
				// echo "$str_col_gmt << <br/>";
				
				$result_color = $this->getColorAndStyleName($str_col_gmt, "1");
				$row_color = $result_color->fetch(PDO::FETCH_ASSOC);
					$str_color = $row_color["color"];
					$alias_colorName = $row_color["GTN_colorname"];
					$str_styling = $row_color["styling"];
					$colorID = $row_color["colorID"];
					$garmentID = $row_color["garmentID"];
				
				$this_num = $r + 1;//$colorID."/".$garmentID;
				$display = "<font size='1px'><i>$alias_colorName</i></font> &nbsp; $str_color &nbsp; <br/><font color='blue' data-toggle='tooltip' title='' style='cursor:pointer'>$str_styling</font>
												<input type='hidden' name='group_number$k-$r' id='group_number$k-$r' value='$this_num' />
												<input type='hidden' id='sr_color$k-$r' name='sr_color$k-$r' value='$colorID' />
												<input type='hidden' id='sr_garment$k-$r' name='sr_garment$k-$r' value='$garmentID' />
												";
				$row_display_css = ($str_checked=="0"? "display:none":"");//--- If Group Color not contains color, make it none display ---//
				$display_value = ($str_checked==""? "0":"1");//--- 0 is none display, 1 is display ---//
			}
			
			$html .= "<tr id='color_row$k-$r' style='$row_display_css' >"; //-- Single Color (pickListID - $colorID), Combo Color (pickListID - groupID) --//
			$html .= "<th class='topcolortd2' id='color_display$k-$r' style='white-space:nowrap'>$display
													<input type='hidden' name='display_scms_row$k-$r' id='display_scms_row$k-$r' value='$display_value' /></th>";
						
			$color_total = 0;
			for($c=0;$c<$num_column;$c++){
				$this_size = $arr_size[$c];
				$sizepriceresult = $this->getColorSizeQtyOfShipment($mid, $colorID, $this_size, "1", $this_style, "qty");
				$rowtitle2=$sizepriceresult->fetch(PDO::FETCH_ASSOC);
				$this_qty = $rowtitle2['qty'];
				$this_qty = ($this_qty==""? 0: $this_qty);
				$color_total += $this_qty;
				$arr_size_total[$c] += $this_qty;
				
				//--- If Packing List Data From Database --//
				$this_col_total_qty=0; $this_polybag_qty_in_blisterbag=0; $this_blisterbag_in_carton=0; $this_gmt_qty_in_polybag=1;$this_SKU=""; $master_upc=""; $case_upc="";
				if($PID!=0){
					$sql = "SELECT total_qty, gmt_qty_in_polybag, polybag_qty_in_blisterbag, blisterbag_in_carton, SKU, master_upc, case_upc
														FROM tblship_packing_detail$from_location WHERE PID='$PID' AND group_number='$this_num' AND size_name='$this_size' AND statusID=1 limit 1";
					// echo "<pre>$sql</pre>";
					$columnsql2 = $this->conn->prepare($sql);
					$columnsql2->execute();
					$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
						$this_col_total_qty = $row["total_qty"];
						$this_gmt_qty_in_polybag = $row["gmt_qty_in_polybag"];
						$this_polybag_qty_in_blisterbag = $row["polybag_qty_in_blisterbag"];
						$this_blisterbag_in_carton = $row["blisterbag_in_carton"];
						$this_SKU = $row["SKU"];
						$master_upc = $row["master_upc"];
						$case_upc = $row["case_upc"];
				}
				
				//--- Check Pack Factor Qty ---//
				//----- Check From Pack Factor Qty -----//
				$html_packfactor = "";
				/*$sql_factor = "SELECT amount, tmode FROM tblpackfactor WHERE orderno='$soID' AND colorID = '$colorID' AND size='$this_size' AND del='0'";
				$result_factor = $this->conn->prepare($sql_factor);
				$result_factor->execute();
				while($row_factor = $result_factor->fetch(PDO::FETCH_ASSOC)){
					$this_tmode = $row_factor["tmode"];
					$this_factor = $row_factor["amount"];
					$this_factor = ($this_factor==""? 0: $this_factor);
					$html_packfactor .= "<input type='hidden' id='pick_list_tmode$k-$r-$c-$this_tmode' value='$this_factor' />";
				}//--- End While ---//*/
				
				
				$sqlasn = "SELECT ifnull(sum(ShipQty * PackValue),0) as sum_asn
						FROM `tblasn_shipment_orderdetail` 
						WHERE PID = '$PID' AND ConsumerPackageCode='$this_SKU' AND del=0 AND ASOHID>0";
				$stmt_asn = $this->conn->prepare($sqlasn);
				$stmt_asn->execute();
				$count_asn = $stmt_asn->rowCount();
				$rowasn = $stmt_asn->fetch(PDO::FETCH_ASSOC);
						$sum_asn   = $rowasn["sum_asn"];
						$asn_min_qty = $sum_asn;
						
				
				// Modified by ckwai on 2018-07-25
				$prepack_qty="";
				$readonly = ($GTN_styleno=="" || $flag_sync==0? "": "readonly");
				$this_factor = $this->funcGetPackFactor($soID, $PID, $pack_method, $this_size, $prepack_qty, $is_standard, $packing_type);
				$html .= "<input type='hidden' id='pick_list_tmode$k-$r-$c-0' value='$this_factor' />";
				$html .= "<input type='hidden' id='pick_list_tmode$k-$r-$c-1' value='$this_factor' />";
				
				$html .= "<th class='topcolortd2 leftcolortd2' style='background-color:#fff'>
								<input type='number' id='total_qty$k-$r-$c' name='total_qty$k-$r-$c' min='0' value='$this_col_total_qty' $formmode_disabled
										class='txt_medium' style='width:65px' 
										onclick='funcSingColSingSizeQty(&#39;$k&#39; , &#39;$r&#39;);funcCheckSingleSizeQtyValidate();'
										onkeyup='funcSingColSingSizeQty(&#39;$k&#39; , &#39;$r&#39;);funcCheckSingleSizeQtyValidate();' 
										/>
								<input type='hidden' name='asn_min$k-$r-$c' id='asn_min$k-$r-$c' value='$asn_min_qty'>
								<input type='hidden' name='count_asn$k-$r-$c' id='count_asn$k-$r-$c' value='$count_asn'>
								$html_packfactor<br/>
								CODES: <input type='text' name='txt_SKU$k-$r-$c' id='txt_SKU$k-$r-$c' class='txt_medium' style='width:120px' placeholder='CODES...' value='$this_SKU' $readonly /><br/>
								Master UPC: <input type='text' name='master_upc$k-$r-$c' id='master_upc$k-$r-$c' class='txt_medium' style='width:120px' placeholder='Master UPC...' value='$master_upc' /><br/>
								Case UPC: <input type='text' name='case_upc$k-$r-$c' id='case_upc$k-$r-$c' class='txt_medium' style='width:120px' placeholder='Case UPC...' value='$case_upc' /><br/>
								
								</th>";
				$html .= "<th class='topcolortd2' style='background-color:#fff'>
								<input type='number' id='gmt_qty$k-$r-$c' name='gmt_qty$k-$r-$c' onclick='funcSingColSingSizeQty(&#39;$k&#39; , &#39;$r&#39;)'
										onkeyup='funcSingColSingSizeQty(&#39;$k&#39; , &#39;$r&#39;)' class='txt_medium' style='width:50px' min='0' 
										value='$this_gmt_qty_in_polybag' $formmode_disabled $readonly />
								<input type='hidden' id='size$k-$r-$c' name='size$k-$r-$c' class='txt_medium' value='$this_size' />
								</th>";
				$html .= "<th class='topcolortd2' style='background-color:#fff'>
								<input type='number' id='pb_qty$k-$r-$c' name='pb_qty$k-$r-$c' onclick='funcSingColSingSizeQty(&#39;$k&#39; , &#39;$r&#39;)'
										onkeyup='funcSingColSingSizeQty(&#39;$k&#39; , &#39;$r&#39;)' class='txt_medium' style='width:50px' min='0'
										value='$this_polybag_qty_in_blisterbag' $formmode_disabled $readonly />
								</th>";
				$html .= "<th class='topcolortd2' style='background-color:#fff'>
								<input type='number' id='bb_qty$k-$r-$c' name='bb_qty$k-$r-$c' onclick='funcSingColSingSizeQty(&#39;$k&#39; , &#39;$r&#39;)'
										onkeyup='funcSingColSingSizeQty(&#39;$k&#39; , &#39;$r&#39;)' class='txt_medium' style='width:50px' min='0'
										value='$this_blisterbag_in_carton' $formmode_disabled $readonly />
								</th>";
				$html .= "<th class='topcolortd2' style='background-color:#fff'>
								<input type='text' id='qty_in_carton$k-$r-$c' name='qty_in_carton$k-$r-$c' class='txt_medium' style='width:50px' min='0' readonly />
								/ <font color='red' id='str_factor_qty$k-$r-$c' >$this_factor</font>
								<input type='hidden' id='factor_qty$k-$r-$c' value='$this_factor' /></th>";
			}//--- End For Loop Size Range ---//
			
			//--- # of ratio pack in one Blister Bag ---//
			$html .= "<th class='topcolortd2 leftcolortd2'>
							<input type='text' id='totalQtyOfRow$k-$r' name='totalQtyOfRow$k-$r' min='0' class='txt_medium' style='width:50px' readonly /></th>";
			}
			else{
				// echo "[$r] $arr_color[$r] << <br/>";
			}
		}//---- End Color Loop ----//
		//total row
			$html .= "<tr>";
			$colspan_num = (count($arr_size_total) * 5);
			$html .= "<th class='topcolortd2' colspan='$colspan_num' style='background-color:#fff'></th>";
			$html .= "<th class='topcolortd2'>".$hdlang["Total"]."</th>";
			$html .= "<th class='topcolortd2 leftcolortd2'>
							<input type='text' id='totalQtyOfList$k' name='totalQtyOfList$k' class='txt_medium' style='width:50px' readonly /></th>";
			$html .= "</tr></table><br/><br/>";
	
	echo $html;
}

public function funcGetPickListMultiColorPrepackByMultiSize($soID, $mid, $this_style, $formmode, $pack_count, $color_type, 
														$arr_color, $pack_method, $PID, $packing_type, $is_multi_gender=0, $order_by_color=0){
	$html = "";
	$k = $pack_count;
	$lang = $this->lang;
	include("../../lang/{$lang}.php");
	$formmode_disabled = ($formmode==3? "disabled":""); 
	$chk_polybag = "";
	$chk_blisterbag = "checked";
	$this_blisterbag_in_carton="0";
	$chk_standard = "";
	$chk_nonstandard = "checked";
	$this_factor = 0;
	
	$filter_query = " AND lcd.PID='$PID'";
	$lbl_lc_assignment = $this->checkLCAssignmentExist($filter_query);
	
	$from_location = ($this->prod_pocount>0 ? $this->from_location:"");
	$css_hide = ($this->from_location=="_prod"? "display:none": "");
	$css_hide = ($this->prod_pocount>0 || $lbl_lc_assignment!=""? "display:none": "$css_hide");

	$btn_carton_calculator = "";
	$str_PID = "";
	$chk_ctn_blister = "";
	$chk_standard_online  = "";
	$chk_last_ctn_by   = "";
	$last_ctn_num_size    = "";
	$ship_remark     = "";
	$is_blisterbag = 0;
	$is_polybag  = 0;
	$this_ratio_qty   = "";
	
	//--- If Packing List Data From Database ---//
	if($PID!=0){
		$columnsql2 = $this->conn->prepare("SELECT * FROM tblship_packing$from_location WHERE PID='$PID'");
		$columnsql2->execute();
		$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
			$is_polybag = $row["is_polybag"];
			$is_blisterbag = $row["is_blisterbag"];
			$is_ctnblister = $row["is_ctnblister"];
			$last_ctn_by_SCSS = $row["last_ctn_by_SCSS"];
			$last_ctn_num_size = $row["last_ctn_num_size"];
			$tmode = $row["tmode"];
			$is_standard = $row["tmode"];
			$chk_polybag = ($is_polybag==1? "checked":"");
			$chk_blisterbag = ($is_blisterbag==1? "checked":"");
			$chk_ctn_blister = ($is_ctnblister==1? "checked":"");
			$chk_standard = ($tmode==0? "checked":"");
			$chk_standard_online = ($tmode==2? "checked":"");
			$chk_nonstandard = ($tmode==1? "checked":"");
			$ship_remark = $row["ship_remark"];	
			$chk_last_ctn_by = ($last_ctn_by_SCSS==1? "checked":"");			
			$btn_carton_calculator = $this->funcLinkToCalculator($soID, $PID, $pack_method, $tmode, $packing_type);
			$str_PID = " <em>PID: <b><u>$PID</u></b></em> &nbsp; ";
	}
	
	$html .= "<hr style='background-color:#bdbdbd;padding:1px' />";
	$html .= "<div>$lbl_lc_assignment <button type='button' style='$css_hide' class='btn btn-danger btn-xs' onclick='funcDeletePickList(&#39;$k&#39;)'>
						<span class='glyphicon glyphicon-trash'></span></button> &nbsp; 
					<input type='hidden' name='packingID$k' id='packingID$k' value='$PID' />
					<input type='hidden' name='is_multi_gender$k' id='is_multi_gender$k' value='$is_multi_gender' />
					<b class='subTitle'>".$hdlang["pick_list"]." $k </b>";//--- Pick List ---//
	$html .= "<span class='glyphicon glyphicon-list-alt btntop editcode' data-toggle='modal' 
							data-target='#methodbox' data-id='$PID' data-backdrop='static' data-keyboard='false' title='".$hdlang["ship_12"]."' 
							style='display: inline-block'></span> &nbsp; "; //--- Packing Method Attachment ---//
	
	$MCRS = ($is_multi_gender==1? $hdlang["MCRS3"]: $hdlang["MCRS2"]);	
	$html .= " $str_PID ".$hdlang["packing_method"].": ".$MCRS." (Carton) - ".$hdlang["normal"]." $btn_carton_calculator"; //-- Packing Method --// //-- Multi Color --// //-- Prepack Multi Size --// 
	
	/*$html .= "<br/><input type='checkbox' id='chk_polybag$k' name='chk_polybag$k' $chk_polybag class='icheckbox_flat-blue' 
							onclick='chkTickedOfPolyBlisterBag(&#39;$k&#39;)'  /> Poly Bag &nbsp; &nbsp; 
					<input type='checkbox' id='chk_blisterbag$k' name='chk_blisterbag$k' $chk_blisterbag class='icheckbox_flat-blue' 
							onclick='chkTickedOfPolyBlisterBag(&#39;$k&#39;)'  /> Blister Bag";
	
	$html .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ";
	$html .= "<input type='radio' name='standard$k' value='1' $chk_nonstandard /> ".$hdlang["non_standard"];
	$html .= "<input type='radio' name='standard$k' value='0' $chk_standard /> ".$hdlang["standard"]." &nbsp; &nbsp; ";*/

	$html .= $this->funcPickListMenuBar($k, $chk_polybag, $chk_blisterbag, $chk_ctn_blister, $formmode_disabled, $chk_nonstandard, $chk_standard, $chk_standard_online, 
									$pack_method, $chk_last_ctn_by, $last_ctn_num_size, $packing_type, $ship_remark, $order_by_color);
	//-------------------------------------//
	//-------- Start Table Display --------//
	//-------------------------------------//
	$html .= "<table class='tb_detail pick_list' id='tb_detail' cellspacing=0' style='width:auto; max-width:100%;'>";
	//--- Row 1 ---//
	$html .= "<tr class='titlebar5'>";
		$html .= "<th class='topcolortd5' rowspan='2' width='150px'>".$hdlang["Color"]."</th>";//-- Color --//
		$html .= "<th class='topcolortd5' rowspan='2' width='150px'>Total Qty of Color</th>";
			$num_column = $this->getSizeNameColumnFromOrder($soID, "0");
			$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
			$arr_size = array();
			$arr_size_total = array();
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				$html .= "<th class='topcolortd5' colspan='2'>$size</th>";
				array_push($arr_size, $size);
				array_push($arr_size_total, 0);
							
			}//--- End For Loop Size Range ---//
			$html .= "<th class='topcolortd5' rowspan='2' id='scss_1st$k'>".(($is_blisterbag)?"# of Product Prepack" :"# of Product Prepack")."</th>";
			$html .= "<th class='topcolortd5' rowspan='2' id='scss_4th$k' width='150px'>".(($is_blisterbag)?"# of Pack <br/>in 1 carton":"")."</th>";
			$html .= "<th class='topcolortd5' rowspan='2' width='150px'>Total Qty of Color</th>";
			$html .= "</tr>";
			
			
			//--- Row 2 ---//				
			$html .= "<tr class='titlebar5'>";
			for($c=0;$c<$num_column;$c++){
				// $html .= "<th class='sub_title' >Total Qty</th>";
				// $html .= "<th class='sub_title' id='scss_3rd$k-$c' ># of Gmt <br/>in 1 Poly Bag</th>";
				$html .= "<th class='sub_title' >Ratio</th>";
				$html .= "<th class='sub_title' id='scss_3rd$k-$c' >".(($is_polybag)?"# of Gmt <br/>in 1 P.Bag":"")."</th>";
			}
			
			$html .= "</tr>";
		
		$valid_row_count = 0;

		$total_qty_in_carton = 0;
		$total_qty_whole_list = 0;

		for($r=0;$r<count($arr_color);$r++){
			$row_display_css = "0";
			$grpID = $r + 1;
			
			$count_grp = 1;
			if($PID!="0"){
				$sqlgrp = "SELECT total_qty
							FROM tblship_packing_detail$from_location 
							WHERE PID='$PID' AND group_number='$grpID' AND statusID=1 limit 1";
				$stmt_grp = $this->conn->prepare($sqlgrp);
				$stmt_grp->execute();
				$count_grp = $stmt_grp->rowCount();
			}
			
			if($count_grp>0){
			//--- Combo Color ---//
			if($color_type==0){
				$arr_grp_color = array();
				$str_all_color = "<div style=&#39;text-align:left&#39;>";
				for($col_num=0;$col_num<count($arr_color[$r]);$col_num++){
					$this_color_garment = $arr_color[$r][$col_num];
					array_push($arr_grp_color, $this_color_garment);
					
					$result_color = $this->getColorAndStyleName($arr_color[$r][$col_num], "1");
					$row_color = $result_color->fetch(PDO::FETCH_ASSOC);
						$str_color = $row_color["color"];
						$str_styling = $row_color["styling"];
						$alias_colorName = $row_color["GTN_colorname"];
						$str_all_color .= "<font size=&#39;1px&#39;><i>$alias_colorName</i></font> &nbsp;$str_color ($str_styling) &nbsp; <br/>";
				}//--- End Load All color in one group ---//
				$str_all_color .= "</div>";
				$str_color_garment = implode(",", $arr_grp_color);
				
				$this_num = $r + 1;
				$display = "<font data-toggle='tooltip2' data-html='true' style='cursor:pointer' id='group_color$k-$r'
									title='$str_all_color' class='tt_large' >Group $this_num </font>
								<input type='hidden' name='group_number$k-$r' id='group_number$k-$r' value='$this_num' />
								<input type='hidden' name='sr_group$k-$r' id='sr_group$k-$r' value='$r' />
								<input type='hidden' name='sr_color_garment$k-$r' id='sr_color_garment$k-$r' value='$str_color_garment' />";//-- Group colorID & gmtID --//
				$row_display_css = ($arr_color[$r][0]==""? "display:none":"");//--- If Group Color not contains color, make it none display ---//
				$display_value = ($arr_color[$r][0]==""? "0":"1");//--- 0 is none display, 1 is display ---//
				$valid_row_count = ($arr_color[$r][0]==""? $valid_row_count : ++$valid_row_count);
			}
			//--- Single Color ---//
			else{
				$arr_temp = explode("-",$arr_color[$r]);
				
				$str_col_gmt = $arr_temp[0];
				$str_checked = $arr_temp[1];
				$result_color = $this->getColorAndStyleName($str_col_gmt, "1");
				$row_color = $result_color->fetch(PDO::FETCH_ASSOC);
					$str_color = $row_color["color"];
					$alias_colorName = $row_color["GTN_colorname"];
					$str_styling = $row_color["styling"];
					$colorID = $row_color["colorID"];
					$garmentID = $row_color["garmentID"];
				
				$this_num = $r + 1;//$colorID."/".$garmentID;
				$display = "<font size='1px'><i>$alias_colorName</i></font> &nbsp; $str_color &nbsp; <br/><font color='blue' data-toggle='tooltip' title='Test' style='cursor:pointer'>$str_styling</font>
												<input type='hidden' name='group_number$k-$r' id='group_number$k-$r' value='$this_num' />
												<input type='hidden' id='sr_color$k-$r' name='sr_color$k-$r' value='$colorID' />
												<input type='hidden' id='sr_garment$k-$r' name='sr_garment$k-$r' value='$garmentID' />
												";
				$row_display_css = ($str_checked=="0"? "display:none":"");//--- If Group Color not contains color, make it none display ---//
				
				$display_value = ($str_checked==""? "0":"1");//--- 0 is none display, 1 is display ---//
				$valid_row_count = ($arr_color[$r][0]==""? $valid_row_count : ++$valid_row_count);
			}
			
			$html .= "<tr id='color_row$k-$r' style='$row_display_css' >"; //-- Single Color (pickListID - $colorID), Combo Color (pickListID - groupID) --//
			$html .= "<th class='topcolortd5' id='color_display$k-$r' style='white-space:nowrap;'>$display
						<input type='hidden' name='display_scms_row$k-$r' id='display_scms_row$k-$r' value='$display_value' /></th>";

			// find the total qty of this color
			$qty_sql = "SELECT ifnull(total_qty,0) as total_qty, SKU FROM tblship_packing_detail$from_location 
						WHERE PID='$PID' AND group_number='$this_num' AND statusID=1 GROUP BY PID,group_number LIMIT 1";
			$qty_res = $this->conn->query($qty_sql);
			$qty_row = $qty_res->fetch(PDO::FETCH_BOTH);
			$this_total_qty = $qty_row['total_qty'];
			$SKU = $qty_row['SKU'];
			if(is_null($this_total_qty) || empty($this_total_qty)){
				$this_total_qty = 0;
			}
			$total_qty_whole_list += $this_total_qty;

			$html .= "<th class='topcolortd5'>
							<input type='number' id='total_qty$k-$r' name='total_qty$k-$r' min='0' 
									value='$this_total_qty' class='txt_medium' style='width:50px' 
									onclick='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)' 
									onkeyup='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)' $formmode_disabled />
							<input type='text' id='txt_SKU$k-$r' name='txt_SKU$k-$r' class='txt_medium' style='width:120px' placeholder='Code...' value='$SKU' /></th>";
						
			$color_total = 0;
			$prepack_qty = 0; // load the qty of ratio multiply by gmt_in_poly
			
			for($c=0;$c<$num_column;$c++){
				$this_size = $arr_size[$c];
				$sizepriceresult = $this->getColorSizeQtyOfShipment($mid, $colorID, $this_size, "1", $this_style, "qty");
				$rowtitle2=$sizepriceresult->fetch(PDO::FETCH_ASSOC);
				$this_qty = $rowtitle2['qty'];
				$this_qty = ($this_qty==""? 0: $this_qty);
				$color_total += $this_qty;
				$arr_size_total[$c] += $this_qty;
				
				//--- If Packing List Data From Database --//
				$this_colsize_total_qty=0; $this_polybag_qty_in_blisterbag=0; $this_gmt_qty_in_polybag=1;
				if($PID!=0){
					$columnsql2 = $this->conn->prepare("SELECT total_qty, gmt_qty_in_polybag, polybag_qty_in_blisterbag, blisterbag_in_carton, ratio_qty 
														FROM tblship_packing_detail$from_location WHERE PID='$PID' AND group_number='$this_num' AND size_name='$this_size' AND statusID=1 limit 1");
					$columnsql2->execute();
					$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
						$this_colsize_total_qty = $row["total_qty"];
						$this_ratio_qty = $row["ratio_qty"];
						$this_gmt_qty_in_polybag = $row["gmt_qty_in_polybag"];
						$this_polybag_qty_in_blisterbag = $row["polybag_qty_in_blisterbag"];
						$this_blisterbag_in_carton = ($row["blisterbag_in_carton"]==""? $this_blisterbag_in_carton: $row["blisterbag_in_carton"]);

					$prepack_qty += $this_ratio_qty*$this_gmt_qty_in_polybag;
					
				}
				
				/*$html .= "<th class='topcolortd5 leftcolortd5' style='background-color:#fff'>
								<input type='number' id='total_qty$k-$r-$c' name='total_qty$k-$r-$c' min='0' value='$this_colsize_total_qty'
										class='txt_medium' style='width:65px' onclick='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)' $formmode_disabled /></th>";*/
				$html .= "<th class='topcolortd5' style='background-color:#fff'>
								<input type='number' id='ratio_qty$k-$r-$c' name='ratio_qty$k-$r-$c' onclick='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)' class='txt_medium' style='width:50px' min='0' 
										value='$this_ratio_qty' $formmode_disabled />
								<input type='hidden' id='size$k-$r-$c' name='size$k-$r-$c' class='txt_medium' value='$this_size' />
								</th>";
				$html .= "<th class='topcolortd5' style='background-color:#fff'>
							<input type='".((!$is_polybag) ? 'hidden' : 'number')."' id='gmt_qty$k-$r-$c' name='gmt_qty$k-$r-$c' onclick='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)' class='txt_medium' style='width:50px' min='0' 
										value='$this_gmt_qty_in_polybag' $formmode_disabled ".((!$is_polybag)?"readonly":'')." />

								
								</th>";
			}//--- End For Loop Size Range ---//
				
			//------ backup input for pb_qty ------//
			/* <input type='number' id='pb_qty$k-$r-$c' name='pb_qty$k-$r-$c' onclick='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)' class='txt_medium' style='width:50px' min='0'
										value='$this_polybag_qty_in_blisterbag' $formmode_disabled />*/
			//---------------------------------//
			$total_qty_in_carton += $prepack_qty * $this_polybag_qty_in_blisterbag;

			$html .= "<th class='topcolortd5' style='background-color:#fff'>
						<input type='number' id='prepack_qty$k-$r' name=''  class='txt_medium' style='width:50px' min='0'
										value='$prepack_qty' readonly /> 

						</th>";

			$html .= "<th class='topcolortd5' style='background-color:#fff'>
						<input type='".((!$is_blisterbag)?'hidden':'number')."' id='pb_qty$k-$r' name='pb_qty$k-$r' 
										onclick='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)' class='txt_medium' style='width:50px' min='0'
										value='$this_polybag_qty_in_blisterbag' $formmode_disabled ".((!$is_blisterbag)?'readonly':'')." disabled />

						</th>";

			$html .= "<th class='topcolortd5' style='background-color:#fff'>
						<input type='number' id='display_total_qty$k-$r' name='display_total_qty$k-$r' class='txt_medium' style='width:50px' min='0'
										value='$this_total_qty' readonly />

						</th>";
			$html .= "</tr>";
			
			}
		}//---- End Color Loop ----//
		//total row
			
			$html .= "<tr>";
			$colspan_num = (count($arr_size_total) * 2) + 5;
			$this_size = ""; $prepack_qty = "";
			$this_factor = $this->funcGetPackFactor($soID, $PID, $pack_method, $this_size, $prepack_qty, $is_standard, $packing_type);
			
			//--- # of Blister Bag in one carton ---//
			$html .= "<th class='topcolortd5' colspan='$colspan_num' style='background-color:#fff; width:100%;'>
						
							<b id='scss_2nd$k'>".(($is_blisterbag==1)?"# Total B.Bag in 1 Carton":"# of Prepack in 1 Carton").":</b> 
										<input type='number' id='bb_qty$k' name='bb_qty$k' onclick='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)' min='0'
												onkeyup='funcMultiColorPrepackByMultiSize(&#39;$k&#39;)' class='txt_medium' style='width:50px'
												value='$this_blisterbag_in_carton' $formmode_disabled readonly />
							&nbsp; / <font color='red' id='str_factor_qty$k' >$this_factor</font> &nbsp; &nbsp;
							<b>Total Qty in 1 Carton:</b> <input type='text' id='qty_in_carton$k' name='qty_in_carton$k' class='txt_medium' style='width:50px' value='$total_qty_in_carton' readonly />
							&nbsp; &nbsp; &nbsp;
							<b>Total Qty of Pick List:</b> <input type='text' id='totalQtyOfList$k' name='totalQtyOfList$k' 
															class='txt_medium' style='width:50px' value='$total_qty_whole_list' readonly />
							
							<input type='hidden' name='size_count$k' id='size_count$k' value='$num_column' />
							<input type='hidden' name='pick_list_method$k' id='pick_list_method$k' value='$pack_method' />
							</th>";
		
			$html .= "</tr></table>";
			$html .= "<br/><br/>";
	
	echo $html;
}

public function funcGetPickListMultiColorPrepackBySingleColorSingleSize($soID, $mid, $this_style, $formmode, $pack_count, $color_type, 
														$arr_color, $pack_method, $PID, $packing_type){
	$html = "";
	$k = $pack_count;
	$lang = $this->lang;
	include("../../lang/{$lang}.php");
	$formmode_disabled = ($formmode==3? "disabled":""); 
	$chk_polybag = "checked";
	$chk_blisterbag = "checked";
	$chk_ctn_blister = "";
	$this_blisterbag_in_carton="0";
	$chk_standard = "";
	$chk_nonstandard = "checked";
	$from_location = ($this->prod_pocount>0 ? $this->from_location:"");
	$css_hide = ($this->from_location=="_prod"? "display:none": "");
	$css_hide = ($this->prod_pocount>0? "display:none": "$css_hide");
	
	//--- If Packing List Data From Database --//
	if($PID!=0){
		$columnsql2 = $this->conn->prepare("SELECT * FROM tblship_packing$from_location WHERE PID='$PID'");
		$columnsql2->execute();
		$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
			$is_polybag = $row["is_polybag"];
			$is_blisterbag = $row["is_blisterbag"];
			$is_ctnblister = $row["is_ctnblister"];
			$tmode = $row["tmode"];
			$chk_polybag = ($is_polybag==1? "checked":"");
			$chk_blisterbag = ($is_blisterbag==1? "checked":"");
			$chk_ctn_blister = ($is_ctnblister==1? "checked":"");
			$chk_standard = ($tmode==0? "checked":"");
			$chk_nonstandard = ($tmode==1? "checked":"");
	}
	
	$html .= "<hr style='background-color:#bdbdbd;padding:1px' />";
	$html .= "<div><button type='button' style='$css_hide' class='btn btn-danger btn-xs' onclick='funcDeletePickList(&#39;$k&#39;)'>
						<span class='glyphicon glyphicon-trash'></span></button> &nbsp; 
					<input type='hidden' name='packingID$k' id='packingID$k' value='$PID' />
					<b class='subTitle'>".$hdlang["pick_list"]." $k </b>";//--- Pick List ---//
	$html .= "<span class='glyphicon glyphicon-list-alt btntop editcode' data-toggle='modal' 
							data-target='#methodbox' data-id='$PID' data-backdrop='static' data-keyboard='false' title='".$hdlang["ship_12"]."' 
							style='display: inline-block'></span> &nbsp; "; //--- Packing Method Attachment ---//
							
	$html .= "".$hdlang["packing_method"].": ".$hdlang["MCRS2"]." - ".$hdlang["packcolorsize"]; //-- Packing Method --// //-- Multi Color --// //-- Prepack by Single Color Single Size --//
	$html .= "<br/><input type='checkbox' id='chk_polybag$k' name='chk_polybag$k' $chk_polybag class='icheckbox_flat-blue' 
							onclick='chkTickedOfPolyBlisterBag(&#39;$k&#39;)' $formmode_disabled /> Poly Bag &nbsp; &nbsp; 
					<input type='checkbox' id='chk_blisterbag$k' name='chk_blisterbag$k' $chk_blisterbag class='icheckbox_flat-blue' 
							onclick='chkTickedOfPolyBlisterBag(&#39;$k&#39;)' $formmode_disabled /> Blister Bag";
	
	$html .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ";
	$html .= "<input type='radio' name='standard$k' value='0' $chk_standard /> ".$hdlang["standard"]." &nbsp; &nbsp; ";
	$html .= "<input type='radio' name='standard$k' value='1' $chk_nonstandard /> ".$hdlang["non_standard"];
	
	//-------------------------------------//
	//-------- Start Table Display --------//
	//-------------------------------------//
	$html .= "<table class='tb_detail pick_list' id='tb_detail' cellspacing=0'>";
	//--- Row 1 ---//
	$html .= "<tr class='titlebar5'>";
		$html .= "<th class='topcolortd5' rowspan='2'>".$hdlang["Color"]."</th>";//-- Color --//
						
			$num_column = $this->getSizeNameColumnFromOrder($soID, "0");
			$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
			$arr_size = array();
			$arr_size_total = array();
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				$html .= "<th class='topcolortd5' colspan='4'>$size</th>";
				array_push($arr_size, $size);
				array_push($arr_size_total, 0);
							
			}//--- End For Loop Size Range ---//
			
			$html .= "</tr>";
			
			
			//--- Row 2 ---//				
			$html .= "<tr class='titlebar5'>";
			for($c=0;$c<$num_column;$c++){
				$html .= "<th class='sub_title' >Total Qty</th>";
				$html .= "<th class='sub_title' id='scss_3rd$k-$c' ># of Gmt <br/>in 1 Poly Bag</th>";
				$html .= "<th class='sub_title' id='scss_4th$k-$c' ># of P.Bag <br/>in 1 B.Bag</th>";
				$html .= "<th class='sub_title' id='scss_2nd$k-$c' ># of B.Bag <br/>in 1 Carton:</th>";
			}
			
			$html .= "</tr>";
		
		$valid_row_count = 0;
		for($r=0;$r<count($arr_color);$r++){
			$row_display_css = "0";
			
			//--- Combo Color ---//
			if($color_type==0){
				$arr_grp_color = array();
				$str_all_color = "<div style=&#39;text-align:left&#39;>";
				for($col_num=0;$col_num<count($arr_color[$r]);$col_num++){
					$this_color_garment = $arr_color[$r][$col_num];
					array_push($arr_grp_color, $this_color_garment);
					
					$result_color = $this->getColorAndStyleName($arr_color[$r][$col_num], "1");
					$row_color = $result_color->fetch(PDO::FETCH_ASSOC);
						$str_color = $row_color["color"];
						$str_styling = $row_color["styling"];
						$alias_colorName = $row_color["GTN_colorname"];
						$str_all_color .= "<font size=&#39;1px&#39;><i>$alias_colorName</i></font> &nbsp; $str_color ($str_styling) &nbsp; <br/>";
				}//--- End Load All color in one group ---//
				$str_all_color .= "</div>";
				$str_color_garment = implode(",", $arr_grp_color);
				
				$this_num = $r + 1;
				$display = "<font data-toggle='tooltip2' data-html='true' style='cursor:pointer' id='group_color$k-$r'
									title='$str_all_color' class='tt_large' >Group $this_num </font>
								<input type='hidden' id='group_number$k-$r' name='group_number$k-$r' value='$this_num' />
								<input type='hidden' name='sr_group$k-$r' id='sr_group$k-$r' value='$r' />
								<input type='hidden' name='sr_color_garment$k-$r' id='sr_color_garment$k-$r' value='$str_color_garment' />";//-- Group colorID & gmtID --//
				$row_display_css = ($arr_color[$r][0]==""? "display:none":"");//--- If Group Color not contains color, make it none display ---//
				$display_value = ($arr_color[$r][0]==""? "0":"1");//--- 0 is none display, 1 is display ---//
				$valid_row_count = ($arr_color[$r][0]==""? $valid_row_count : ++$valid_row_count);
			}
			//--- Single Color ---//
			else{
				$arr_temp = explode("-",$arr_color[$r]);
				$str_col_gmt = $arr_temp[0];
				$str_checked = $arr_temp[1];
				$result_color = $this->getColorAndStyleName($str_col_gmt, "1");
				$row_color = $result_color->fetch(PDO::FETCH_ASSOC);
					$str_color = $row_color["color"];
					$alias_colorName = $row_color["GTN_colorname"];
					$str_styling = $row_color["styling"];
					$colorID = $row_color["colorID"];
					$garmentID = $row_color["garmentID"];
				
				$this_num = $r + 1;//$colorID."/".$garmentID;
				$display = "<font size='1px'><i>$alias_colorName</i></font> &nbsp; $str_color &nbsp; <br/><font color='blue' data-toggle='tooltip' title='Test' style='cursor:pointer'>$str_styling</font>	
												<input type='hidden' id='group_number$k-$r' name='group_number$k-$r' value='$this_num' />
												<input type='hidden' id='sr_color$k-$r' name='sr_color$k-$r' value='$colorID' />
												<input type='hidden' id='sr_garment$k-$r' name='sr_garment$k-$r' value='$garmentID' />
												";
				$row_display_css = ($str_checked=="0"? "display:none":"");//--- If Group Color not contains color, make it none display ---//
				$display_value = ($str_checked==""? "0":"1");//--- 0 is none display, 1 is display ---//
				$valid_row_count = ($arr_color[$r][0]==""? $valid_row_count : ++$valid_row_count);
			}
			
			$html .= "<tr id='color_row$k-$r' style='$row_display_css' >"; //-- Single Color (pickListID - $colorID), Combo Color (pickListID - groupID) --//
			$html .= "<th class='topcolortd5' id='color_display$k-$r' style='white-space:nowrap'>$display
													<input type='hidden' name='display_scms_row$k-$r' id='display_scms_row$k-$r' value='$display_value' /></th>";
						
			$color_total = 0;
			for($c=0;$c<$num_column;$c++){
				$this_size = $arr_size[$c];
				$sizepriceresult = $this->getColorSizeQtyOfShipment($mid, $colorID, $this_size, "1", $this_style, "qty");
				$rowtitle2=$sizepriceresult->fetch(PDO::FETCH_ASSOC);
				$this_qty = $rowtitle2['qty'];
				$this_qty = ($this_qty==""? 0: $this_qty);
				$color_total += $this_qty;
				$arr_size_total[$c] += $this_qty;
				
				//--- If Packing List Data From Database --//
				$this_colsize_total_qty=0; $this_polybag_qty_in_blisterbag=0; $this_gmt_qty_in_polybag=1; $this_blisterbag_in_carton = 1;
				if($PID!=0){
					$columnsql2 = $this->conn->prepare("SELECT total_qty, gmt_qty_in_polybag, polybag_qty_in_blisterbag, blisterbag_in_carton 
														FROM tblship_packing_detail$from_location WHERE PID='$PID' AND group_number='$this_num' AND size_name='$this_size' AND statusID=1 limit 1");
					$columnsql2->execute();
					$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
						$this_colsize_total_qty = $row["total_qty"];
						$this_gmt_qty_in_polybag = $row["gmt_qty_in_polybag"];
						$this_polybag_qty_in_blisterbag = $row["polybag_qty_in_blisterbag"];
						$this_blisterbag_in_carton = ($row["blisterbag_in_carton"]==""? $this_blisterbag_in_carton: $row["blisterbag_in_carton"]);
				}
							
				$html .= "<th class='topcolortd5 leftcolortd5' style='background-color:#fff'>
								<input type='number' id='total_qty$k-$r-$c' name='total_qty$k-$r-$c' min='0' 
										class='txt_medium' style='width:65px' onclick='funcMultiColorPrepackBySingleColorSingleSize(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackBySingleColorSingleSize(&#39;$k&#39;)'
										value='$this_colsize_total_qty' $formmode_disabled /></th>";
				$html .= "<th class='topcolortd5' style='background-color:#fff'>
								<input type='number' id='gmt_qty$k-$r-$c' name='gmt_qty$k-$r-$c' onclick='funcMultiColorPrepackBySingleColorSingleSize(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackBySingleColorSingleSize(&#39;$k&#39;)' class='txt_medium' style='width:50px' min='0' 
										value='$this_gmt_qty_in_polybag' $formmode_disabled />
								<input type='hidden' id='size$k-$r-$c' name='size$k-$r-$c' class='txt_medium' value='$this_size' />
								</th>";
				$html .= "<th class='topcolortd5' style='background-color:#fff'>
								<input type='number' id='pb_qty$k-$r-$c' name='pb_qty$k-$r-$c' onclick='funcMultiColorPrepackBySingleColorSingleSize(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackBySingleColorSingleSize(&#39;$k&#39;)' class='txt_medium' style='width:50px' min='0' 
										value='$this_polybag_qty_in_blisterbag' $formmode_disabled />
								</th>";
				$html .= "<th class='topcolortd5' style='background-color:#fff'>
								<input type='number' id='bb_qty$k-$r-$c' name='bb_qty$k-$r-$c' onclick='funcMultiColorPrepackBySingleColorSingleSize(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackBySingleColorSingleSize(&#39;$k&#39;)' class='txt_medium' style='width:50px' min='0'
										value='$this_blisterbag_in_carton' $formmode_disabled />
								</th>";
			}//--- End For Loop Size Range ---//
			
		}//---- End Color Loop ----//
		//total row
			$html .= "<tr>";
			$colspan_num = (count($arr_size_total) * 4) + 1;
			//--- # of Blister Bag in one carton ---//
			$html .= "<th class='topcolortd5' colspan='$colspan_num' style='background-color:#fff'>
							<b>Total Qty in 1 Carton:</b> <input type='text' id='qty_in_carton$k' name='qty_in_carton$k' class='txt_medium' style='width:50px' readonly />
							&nbsp; &nbsp; &nbsp;
							<b>Total Qty of Pick List:</b> <input type='text' id='totalQtyOfList$k' name='totalQtyOfList$k'
																	class='txt_medium' style='width:50px' readonly />
							
							<input type='hidden' name='size_count$k' id='size_count$k' value='$num_column' />
							<input type='hidden' name='pick_list_method$k' id='pick_list_method$k' value='$pack_method' />
							</th>";
		
			$html .= "</tr></table>";
			$html .= "<br/><br/>";
	
	echo $html;
}

public function funcGetPickListMultiColorPrepackBySingleSize($soID, $mid, $this_style, $formmode, $pack_count, $color_type, 
														$arr_color, $pack_method, $PID, $packing_type){
	$html = "";
	$k = $pack_count;
	$lang = $this->lang;
	include("../../lang/{$lang}.php");
	$formmode_disabled = ($formmode==3? "disabled":"");
	$chk_polybag = "checked";
	$chk_blisterbag = "checked";
	$this_blisterbag_in_carton="0";
	$chk_standard = "";
	$chk_nonstandard = "checked";
	$from_location = ($this->prod_pocount>0 ? $this->from_location:"");
	$css_hide = ($this->from_location=="_prod"? "display:none": "");
	$css_hide = ($this->prod_pocount>0? "display:none": "$css_hide");
	
	//--- If Packing List Data From Database --//
	if($PID!=0){
		$columnsql2 = $this->conn->prepare("SELECT * FROM tblship_packing$from_location WHERE PID='$PID'");
		$columnsql2->execute();
		$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
			$is_polybag = $row["is_polybag"];
			$is_blisterbag = $row["is_blisterbag"];
			$is_ctnblister = $row["is_ctnblister"];
			$tmode = $row["tmode"];
			$chk_polybag = ($is_polybag==1? "checked":"");
			$chk_blisterbag = ($is_blisterbag==1? "checked":"");
			$chk_standard = ($tmode==0? "checked":"");
			$chk_nonstandard = ($tmode==1? "checked":"");
	}
	
	$html .= "<hr style='background-color:#bdbdbd;padding:1px' />";
	$html .= "<div><button type='button' style='$css_hide' class='btn btn-danger btn-xs' onclick='funcDeletePickList(&#39;$k&#39;)'>
						<span class='glyphicon glyphicon-trash'></span></button> &nbsp; 
					<input type='hidden' name='packingID$k' id='packingID$k' value='$PID' />
					<b class='subTitle'>".$hdlang["pick_list"]." $k </b>";//--- Pick List ---//
	$html .= "<span class='glyphicon glyphicon-list-alt btntop editcode' data-toggle='modal' 
							data-target='#methodbox' data-id='$PID' data-backdrop='static' data-keyboard='false' title='".$hdlang["ship_12"]."' 
							style='display: inline-block'></span> &nbsp; "; //--- Packing Method Attachment ---//
							
	$html .= "".$hdlang["packing_method"].": ".$hdlang["MCRS2"]." - ".$hdlang["packbysize"]; //-- Packing Method --// //-- Multi Color --// //-- Prepack by Size --//
	$html .= "<br/><input type='checkbox' id='chk_polybag$k' name='chk_polybag$k' $chk_polybag class='icheckbox_flat-blue' 
							onclick='chkTickedOfPolyBlisterBag(&#39;$k&#39;)' $formmode_disabled /> Poly Bag &nbsp; &nbsp; 
					<input type='checkbox' id='chk_blisterbag$k' name='chk_blisterbag$k' $chk_blisterbag class='icheckbox_flat-blue' 
							onclick='chkTickedOfPolyBlisterBag(&#39;$k&#39;)' $formmode_disabled /> Blister Bag";
	
	$html .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ";
	$html .= "<input type='radio' name='standard$k' value='0' $chk_standard /> ".$hdlang["standard"]." &nbsp; &nbsp; ";
	$html .= "<input type='radio' name='standard$k' value='1' $chk_nonstandard /> ".$hdlang["non_standard"];
	
	//-------------------------------------//
	//-------- Start Table Display --------//
	//-------------------------------------//
	$html .= "<table class='tb_detail pick_list' id='tb_detail' cellspacing=0'>";
	//--- Row 1 ---//
	$html .= "<tr class='titlebar5'>";
		$html .= "<th class='topcolortd5' rowspan='2'>".$hdlang["Color"]."</th>";//-- Color --//
						
			$num_column = $this->getSizeNameColumnFromOrder($soID, "0");
			$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
			$arr_size = array();
			$arr_size_total = array();
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				$html .= "<th class='topcolortd5' colspan='3'>$size</th>";
				array_push($arr_size, $size);
				array_push($arr_size_total, 0);
							
			}//--- End For Loop Size Range ---//
			
			$html .= "</tr>";
			
			
			//--- Row 2 ---//				
			$html .= "<tr class='titlebar5'>";
			for($c=0;$c<$num_column;$c++){
				$html .= "<th class='sub_title' >Total Qty</th>";
				$html .= "<th class='sub_title' id='scss_3rd$k-$c' ># of Gmt <br/>in 1 Poly Bag</th>";
				$html .= "<th class='sub_title' id='scss_4th$k-$c' ># of P.Bag <br/>in 1 B.Bag</th>";
			}
			
			$html .= "</tr>";
		
		$valid_row_count = 0;
		$arr_size_blister = array();
		for($r=0;$r<count($arr_color);$r++){
			$row_display_css = "0";
			
			//--- Combo Color ---//
			if($color_type==0){
				$arr_grp_color = array();
				$str_all_color = "<div style=&#39;text-align:left&#39;>";
				for($col_num=0;$col_num<count($arr_color[$r]);$col_num++){
					$this_color_garment = $arr_color[$r][$col_num];
					array_push($arr_grp_color, $this_color_garment);
					
					$result_color = $this->getColorAndStyleName($arr_color[$r][$col_num], "1");
					$row_color = $result_color->fetch(PDO::FETCH_ASSOC);
						$str_color = $row_color["color"];
						$alias_colorName = $row_color["GTN_colorname"];
						$str_styling = $row_color["styling"];
						$str_all_color .= "<font size=&#39;1px&#39;><i>$alias_colorName</i></font> &nbsp; $str_color ($str_styling) &nbsp; <br/>";
				}//--- End Load All color in one group ---//
				$str_all_color .= "</div>";
				$str_color_garment = implode(",", $arr_grp_color);
				
				$this_num = $r + 1;
				$display = "<font data-toggle='tooltip2' data-html='true' style='cursor:pointer' id='group_color$k-$r'
									title='$str_all_color' class='tt_large' >Group $this_num </font>
								<input type='hidden' id='group_number$k-$r' name='group_number$k-$r' value='$this_num' />
								<input type='hidden' name='sr_group$k-$r' id='sr_group$k-$r' value='$r' />
								<input type='hidden' name='sr_color_garment$k-$r' id='sr_color_garment$k-$r' value='$str_color_garment' />";//-- Group colorID & gmtID --//
				$row_display_css = ($arr_color[$r][0]==""? "display:none":"");//--- If Group Color not contains color, make it none display ---//
				$display_value = ($arr_color[$r][0]==""? "0":"1");//--- 0 is none display, 1 is display ---//
				$valid_row_count = ($arr_color[$r][0]==""? $valid_row_count : ++$valid_row_count);
			}
			//--- Single Color ---//
			else{
				$arr_temp = explode("-",$arr_color[$r]);
				$str_col_gmt = $arr_temp[0];
				$str_checked = $arr_temp[1];
				$result_color = $this->getColorAndStyleName($str_col_gmt, "1");
				$row_color = $result_color->fetch(PDO::FETCH_ASSOC);
					$str_color = $row_color["color"];
					$alias_colorName = $row_color["GTN_colorname"];
					$str_styling = $row_color["styling"];
					$colorID = $row_color["colorID"];
					$garmentID = $row_color["garmentID"];
				
				$this_num = $r + 1;//$colorID."/".$garmentID;
				$display = "<font size='1px'><i>$alias_colorName</i></font> &nbsp; $str_color &nbsp; <br/><font color='blue' data-toggle='tooltip' title='Test' style='cursor:pointer'>$str_styling</font>
												<input type='hidden' id='group_number$k-$r' name='group_number$k-$r' value='$this_num' />
												<input type='hidden' id='sr_color$k-$r' name='sr_color$k-$r' value='$colorID' />
												<input type='hidden' id='sr_garment$k-$r' name='sr_garment$k-$r' value='$garmentID' />
												";
				$row_display_css = ($str_checked=="0"? "display:none":"");//--- If Group Color not contains color, make it none display ---//
				$display_value = ($str_checked==""? "0":"1");//--- 0 is none display, 1 is display ---//
				$valid_row_count = ($arr_color[$r][0]==""? $valid_row_count : ++$valid_row_count);
			}
			
			$html .= "<tr id='color_row$k-$r' style='$row_display_css' >"; //-- Single Color (pickListID - $colorID), Combo Color (pickListID - groupID) --//
			$html .= "<th class='topcolortd5' id='color_display$k-$r' style='white-space:nowrap'>$display
													<input type='hidden' name='display_scms_row$k-$r' id='display_scms_row$k-$r' value='$display_value' /></th>";
						
			$color_total = 0;
			for($c=0;$c<$num_column;$c++){
				$this_size = $arr_size[$c];
				$sizepriceresult = $this->getColorSizeQtyOfShipment($mid, $colorID, $this_size, "1", $this_style, "qty");
				$rowtitle2=$sizepriceresult->fetch(PDO::FETCH_ASSOC);
				$this_qty = $rowtitle2['qty'];
				$this_qty = ($this_qty==""? 0: $this_qty);
				$color_total += $this_qty;
				$arr_size_total[$c] += $this_qty;
				
				//--- If Packing List Data From Database --//
				$this_colsize_total_qty=0; $this_polybag_qty_in_blisterbag=0; $this_gmt_qty_in_polybag=1; $this_blisterbag_in_carton = 1;
				if($PID!=0){
					$columnsql2 = $this->conn->prepare("SELECT total_qty, gmt_qty_in_polybag, polybag_qty_in_blisterbag, blisterbag_in_carton 
														FROM tblship_packing_detail$from_location WHERE PID='$PID' AND group_number='$this_num' AND size_name='$this_size' AND statusID=1 limit 1");
					$columnsql2->execute();
					$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
						$this_colsize_total_qty = $row["total_qty"];
						$this_gmt_qty_in_polybag = $row["gmt_qty_in_polybag"];
						$this_polybag_qty_in_blisterbag = $row["polybag_qty_in_blisterbag"];
						$this_blisterbag_in_carton = ($row["blisterbag_in_carton"]==""? $this_blisterbag_in_carton: $row["blisterbag_in_carton"]);
				}
				$arr_size_blister[$c] = $this_blisterbag_in_carton;
							
				$html .= "<th class='topcolortd5 leftcolortd5' style='background-color:#fff'>
								<input type='number' id='total_qty$k-$r-$c' name='total_qty$k-$r-$c' min='0' 
										class='txt_medium' style='width:65px' onclick='funcMultiColorPrepackBySingleSize(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackBySingleSize(&#39;$k&#39;)' value='$this_colsize_total_qty' $formmode_disabled /></th>";
				$html .= "<th class='topcolortd5' style='background-color:#fff'>
								<input type='number' id='gmt_qty$k-$r-$c' name='gmt_qty$k-$r-$c' onclick='funcMultiColorPrepackBySingleSize(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackBySingleSize(&#39;$k&#39;)' class='txt_medium' style='width:50px' min='0' 
										value='$this_gmt_qty_in_polybag' $formmode_disabled />
								<input type='hidden' id='size$k-$r-$c' name='size$k-$r-$c' class='txt_medium' value='$this_size'  />
								</th>";
				$html .= "<th class='topcolortd5' style='background-color:#fff'>
								<input type='number' id='pb_qty$k-$r-$c' name='pb_qty$k-$r-$c' onclick='funcMultiColorPrepackBySingleSize(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackBySingleSize(&#39;$k&#39;)' class='txt_medium' style='width:50px' min='0' 
										value='$this_polybag_qty_in_blisterbag' $formmode_disabled />
								</th>";
			}//--- End For Loop Size Range ---//
			
		}//---- End Color Loop ----//
		//total row
			$html .= "<tr>";
			$html .= "<th class='topcolortd5' style='background-color:#fff' ></th>";
			for($c=0;$c<$num_column;$c++){
				$this_blisterbag_in_carton = $arr_size_blister["$c"];
				$html .= "<th class='topcolortd5 leftcolortd5' colspan='3' style='background-color:#fff' >
								<b id='scss_2nd$k-$c'># of B.Bag in 1 Carton:</b> 
								<input type='number' id='bb_qty$k-$c' name='bb_qty$k-$c' onclick='funcMultiColorPrepackBySingleSize(&#39;$k&#39;)' min='0'
										onkeyup='funcMultiColorPrepackBySingleSize(&#39;$k&#39;)' class='txt_medium' style='width:50px'
										value='$this_blisterbag_in_carton' $formmode_disabled />
								</th>";
			}
			
		
			$html .= "</tr>";
			$html .= "<tr>";
			$colspan_num = (count($arr_size_total) * 3) + 1;
			//--- # of Blister Bag in one carton ---//
			$html .= "<th class='topcolortd5' colspan='$colspan_num' style='background-color:#fff'>
							<b>Total Qty in 1 Carton:</b> <input type='text' id='qty_in_carton$k' name='qty_in_carton$k' class='txt_medium' style='width:50px' readonly />
							&nbsp; &nbsp; &nbsp;
							<b>Total Qty of Pick List:</b> <input type='text' id='totalQtyOfList$k' name='totalQtyOfList$k'
																	class='txt_medium' style='width:50px' readonly />
							
							<input type='hidden' name='size_count$k' id='size_count$k' value='$num_column' />
							<input type='hidden' name='pick_list_method$k' id='pick_list_method$k' value='$pack_method' />
							</th>";
		
			$html .= "</tr>";
			$html .= "</table><br/><br/>";
	
	echo $html;
}

public function funcGetPickListMultiColorPrepackBySingleColor($soID, $mid, $this_style, $formmode, $pack_count, $color_type, 
														$arr_color, $pack_method, $PID, $packing_type){
	$html = "";
	$k = $pack_count;
	$lang = $this->lang;
	include("../../lang/{$lang}.php"); 
	$formmode_disabled = ($formmode==3? "disabled":"");
	$chk_polybag = "checked";
	$chk_blisterbag = "checked";
	$this_blisterbag_in_carton="0";
	$chk_standard = "";
	$chk_nonstandard = "checked";
	$from_location = ($this->prod_pocount>0 ? $this->from_location:"");
	$css_hide = ($this->from_location=="_prod"? "display:none": "");
	$css_hide = ($this->prod_pocount>0? "display:none": "$css_hide");
	
	//--- If Packing List Data From Database ---//
	if($PID!=0){
		$columnsql2 = $this->conn->prepare("SELECT * FROM tblship_packing$from_location WHERE PID='$PID'");
		$columnsql2->execute();
		$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
			$is_polybag = $row["is_polybag"];
			$is_blisterbag = $row["is_blisterbag"];
			$is_ctnblister = $row["is_ctnblister"];
			$tmode = $row["tmode"];
			$chk_polybag = ($is_polybag==1? "checked":"");
			$chk_blisterbag = ($is_blisterbag==1? "checked":"");
			$chk_standard = ($tmode==0? "checked":"");
			$chk_nonstandard = ($tmode==1? "checked":"");
	}
	
	$html .= "<hr style='background-color:#bdbdbd;padding:1px' />";
	$html .= "<div><button type='button' style='$css_hide' class='btn btn-danger btn-xs' onclick='funcDeletePickList(&#39;$k&#39;)'>
						<span class='glyphicon glyphicon-trash'></span></button> &nbsp; 
					<input type='hidden' name='packingID$k' id='packingID$k' value='$PID' />
					<b class='subTitle'>".$hdlang["pick_list"]." $k </b>";//--- Pick List ---//
	$html .= "<span class='glyphicon glyphicon-list-alt btntop editcode' data-toggle='modal' 
							data-target='#methodbox' data-id='$PID' data-backdrop='static' data-keyboard='false' title='".$hdlang["ship_12"]."' 
							style='display: inline-block'></span> &nbsp; "; //--- Packing Method Attachment ---//
							
	$html .= "".$hdlang["packing_method"].": ".$hdlang["MCRS2"]." - ".$hdlang["packbycolor"]; //-- Packing Method --// //-- Multi Color --// //-- Prepack by Color --// 
	$html .= "<br/><input type='checkbox' id='chk_polybag$k' name='chk_polybag$k' checked class='icheckbox_flat-blue' 
							onclick='chkTickedOfPolyBlisterBag(&#39;$k&#39;)' $formmode_disabled /> Poly Bag &nbsp; &nbsp; 
					<input type='checkbox' id='chk_blisterbag$k' name='chk_blisterbag$k' checked class='icheckbox_flat-blue' 
							onclick='chkTickedOfPolyBlisterBag(&#39;$k&#39;)' $formmode_disabled /> Blister Bag";
	
	$html .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ";
	$html .= "<input type='radio' name='standard$k' value='0' $chk_standard /> ".$hdlang["standard"]." &nbsp; &nbsp; ";
	$html .= "<input type='radio' name='standard$k' value='1' $chk_nonstandard /> ".$hdlang["non_standard"];
	
	//-------------------------------------//
	//-------- Start Table Display --------//
	//-------------------------------------//
	$html .= "<table class='tb_detail pick_list' id='tb_detail' cellspacing=0'>";
	//--- Row 1 ---//
	$html .= "<tr class='titlebar5'>";
		$html .= "<th class='topcolortd5' rowspan='2'>".$hdlang["Color"]."</th>";//-- Color --//
						
			$num_column = $this->getSizeNameColumnFromOrder($soID, "0");
			$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
			$arr_size = array();
			$arr_size_total = array();
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				$html .= "<th class='topcolortd5' colspan='3'>$size</th>";
				array_push($arr_size, $size);
				array_push($arr_size_total, 0);
							
			}//--- End For Loop Size Range ---//
			$html .= "<th class='topcolortd5' rowspan='2' id='scss_2nd$k'># of B.Bag in 1 Carton</th>";
			$html .= "</tr>";
			
			
			//--- Row 2 ---//				
			$html .= "<tr class='titlebar5'>";
			for($c=0;$c<$num_column;$c++){
				$html .= "<th class='sub_title' >Total Qty</th>";
				$html .= "<th class='sub_title' id='scss_3rd$k-$c' ># of Gmt <br/>in 1 Poly Bag</th>";
				$html .= "<th class='sub_title' id='scss_4th$k-$c' ># of P.Bag <br/>in 1 B.Bag</th>";
			}
			
			$html .= "</tr>";
		
		$valid_row_count = 0;
		for($r=0;$r<count($arr_color);$r++){
			$row_display_css = "0";
			
			//--- Combo Color ---//
			if($color_type==0){
				$arr_grp_color = array();
				$str_all_color = "<div style=&#39;text-align:left&#39;>";
				for($col_num=0;$col_num<count($arr_color[$r]);$col_num++){
					$this_color_garment = $arr_color[$r][$col_num];
					array_push($arr_grp_color, $this_color_garment);
					
					$result_color = $this->getColorAndStyleName($arr_color[$r][$col_num], "1");
					$row_color = $result_color->fetch(PDO::FETCH_ASSOC);
						$str_color = $row_color["color"];
						$alias_colorName = $row_color["GTN_colorname"];
						$str_styling = $row_color["styling"];
						$str_all_color .= "<font size=&#39;1px&#39;><i>$alias_colorName</i></font> $nbsp; $str_color ($str_styling) &nbsp; <br/>";
				}//--- End Load All color in one group ---//
				$str_all_color .= "</div>";
				$str_color_garment = implode(",", $arr_grp_color);
				
				$this_num = $r + 1;
				$display = "<font data-toggle='tooltip2' data-html='true' style='cursor:pointer' id='group_color$k-$r'
									title='$str_all_color' class='tt_large' >Group $this_num </font>
								<input type='hidden' name='group_number$k-$r' id='group_number$k-$r' value='$this_num' />
								<input type='hidden' name='sr_group$k-$r' id='sr_group$k-$r' value='$r' />
								<input type='hidden' name='sr_color_garment$k-$r' id='sr_color_garment$k-$r' value='$str_color_garment' />";//-- Group colorID & gmtID --//
				$row_display_css = ($arr_color[$r][0]==""? "display:none":"");//--- If Group Color not contains color, make it none display ---//
				$display_value = ($arr_color[$r][0]==""? "0":"1");//--- 0 is none display, 1 is display ---//
				$valid_row_count = ($arr_color[$r][0]==""? $valid_row_count : ++$valid_row_count);
			}
			//--- Single Color ---//
			else{
				$arr_temp = explode("-",$arr_color[$r]);
				$str_col_gmt = $arr_temp[0];
				$str_checked = $arr_temp[1];
				$result_color = $this->getColorAndStyleName($str_col_gmt, "1");
				$row_color = $result_color->fetch(PDO::FETCH_ASSOC);
					$str_color = $row_color["color"];
					$alias_colorName = $row_color["GTN_colorname"];
					$str_styling = $row_color["styling"];
					$colorID = $row_color["colorID"];
					$garmentID = $row_color["garmentID"];
				
				$this_num = $r + 1;//$colorID."/".$garmentID;
				$display = "<font size='1px'><i>$alias_colorName</i></font> &nbsp; $str_color &nbsp; <br/><font color='blue' data-toggle='tooltip' title='Test' style='cursor:pointer'>$str_styling</font>
												<input type='hidden' id='group_number$k-$r' name='group_number$k-$r' value='$this_num' />
												<input type='hidden' id='sr_color$k-$r' name='sr_color$k-$r' value='$colorID' />
												<input type='hidden' id='sr_garment$k-$r' name='sr_garment$k-$r' value='$garmentID' />
												";
				$row_display_css = ($str_checked=="0"? "display:none":"");//--- If Group Color not contains color, make it none display ---//
				$display_value = ($str_checked==""? "0":"1");//--- 0 is none display, 1 is display ---//
				$valid_row_count = ($arr_color[$r][0]==""? $valid_row_count : ++$valid_row_count);
			}
			
			$html .= "<tr id='color_row$k-$r' style='$row_display_css' >"; //-- Single Color (pickListID - $colorID), Combo Color (pickListID - groupID) --//
			$html .= "<th class='topcolortd5' id='color_display$k-$r' style='white-space:nowrap'>$display
													<input type='hidden' name='display_scms_row$k-$r' id='display_scms_row$k-$r' value='$display_value' /></th>";
						
			$color_total = 0;
			for($c=0;$c<$num_column;$c++){
				$this_size = $arr_size[$c];
				$sizepriceresult = $this->getColorSizeQtyOfShipment($mid, $colorID, $this_size, "1", $this_style, "qty");
				$rowtitle2=$sizepriceresult->fetch(PDO::FETCH_ASSOC);
				$this_qty = $rowtitle2['qty'];
				$this_qty = ($this_qty==""? 0: $this_qty);
				$color_total += $this_qty;
				$arr_size_total[$c] += $this_qty;
				
				//--- If Packing List Data From Database --//
				$this_colsize_total_qty=0; $this_polybag_qty_in_blisterbag=0; $this_gmt_qty_in_polybag=1; $this_blisterbag_in_carton = 1;
				if($PID!=0){
					$columnsql2 = $this->conn->prepare("SELECT total_qty, gmt_qty_in_polybag, polybag_qty_in_blisterbag, blisterbag_in_carton 
														FROM tblship_packing_detail$from_location WHERE PID='$PID' AND group_number='$this_num' AND size_name='$this_size' AND statusID=1 limit 1");
					$columnsql2->execute();
					$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
						$this_colsize_total_qty = $row["total_qty"];
						$this_gmt_qty_in_polybag = $row["gmt_qty_in_polybag"];
						$this_polybag_qty_in_blisterbag = $row["polybag_qty_in_blisterbag"];
						$this_blisterbag_in_carton = ($row["blisterbag_in_carton"]==""? $this_blisterbag_in_carton: $row["blisterbag_in_carton"]);
				}
							
				$html .= "<th class='topcolortd5 leftcolortd5' style='background-color:#fff'>
								<input type='number' id='total_qty$k-$r-$c' name='total_qty$k-$r-$c' min='0' 
										class='txt_medium' style='width:65px' onclick='funcMultiColorPrepackBySingleColor(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackBySingleColor(&#39;$k&#39;)' value='$this_colsize_total_qty' $formmode_disabled /></th>";
				$html .= "<th class='topcolortd5' style='background-color:#fff'>
								<input type='number' id='gmt_qty$k-$r-$c' name='gmt_qty$k-$r-$c' onclick='funcMultiColorPrepackBySingleColor(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackBySingleColor(&#39;$k&#39;)' class='txt_medium' style='width:50px' min='0' 
										value='$this_gmt_qty_in_polybag' $formmode_disabled />
								<input type='hidden' id='size$k-$r-$c' name='size$k-$r-$c' class='txt_medium' value='$this_size' />
								</th>";
				$html .= "<th class='topcolortd5' style='background-color:#fff'>
								<input type='number' id='pb_qty$k-$r-$c' name='pb_qty$k-$r-$c' onclick='funcMultiColorPrepackBySingleColor(&#39;$k&#39;)'
										onkeyup='funcMultiColorPrepackBySingleColor(&#39;$k&#39;)' class='txt_medium' style='width:50px' min='0' 
										value='$this_polybag_qty_in_blisterbag' $formmode_disabled />
								</th>";
			}//--- End For Loop Size Range ---//
			
			$html .= "<th class='topcolortd5'>
									<input type='number' id='bb_qty$k-$r' name='bb_qty$k-$r' onclick='funcMultiColorPrepackBySingleColor(&#39;$k&#39;)' min='0'
												onkeyup='funcMultiColorPrepackBySingleColor(&#39;$k&#39;)' class='txt_medium' style='width:50px' 
												value='$this_blisterbag_in_carton' $formmode_disabled />
									</th>
						</tr>";
		}//---- End Color Loop ----//
		//total row
			
			$html .= "<tr>";
			$colspan_num = (count($arr_size_total) * 3) + 1;
			//--- # of Blister Bag in one carton ---//
			$html .= "<th class='topcolortd5' colspan='$colspan_num' style='background-color:#fff'>
							<b>Total Qty in 1 Carton:</b> <input type='text' id='qty_in_carton$k' name='qty_in_carton$k' class='txt_medium' style='width:50px' readonly />
							&nbsp; &nbsp; &nbsp;
							<b>Total Qty of Pick List:</b> <input type='text' id='totalQtyOfList$k' name='totalQtyOfList$k'
																	class='txt_medium' style='width:50px' readonly />
							
							<input type='hidden' name='size_count$k' id='size_count$k' value='$num_column' />
							<input type='hidden' name='pick_list_method$k' id='pick_list_method$k' value='$pack_method' />
							</th>";
			$html .= "<th class='topcolortd5'></th>";
			$html .= "</tr></table>";
			$html .= "<br/><br/>";
	
	echo $html;
}

//==================================================================//
//================ Packing Carton Display Function =================//
//==================================================================//

public function funcGetCartonMeasurement($orderno, $PID, $pack_method, $size_name, $prepack_qty, $is_standard, 
											$prepack_name, $total_qty, $packing_type, $arr_size_qty, $arr_size=array()){
	$sub_query = ""; $sub_query_info = ""; $measurement="0.000 x 0.000 x 0.000"; $ext_CBM = 0; $pack_factor=0; $ctn_weight=0;
	$str_length=0; $str_width=0; $str_height=0; $ext_length=0; $ext_width=0; $ext_height=0;
	
	sort($arr_size);
	$str_size = implode(",",$arr_size);
	
	switch($pack_method){
		//case 50:
		case 1:$sub_query = " AND ccp.prepack_qty='$prepack_qty' AND ccp.grp_size='$str_size'";break;
		case 2:$sub_query = " AND ccp.size_name='$size_name' ";break;
	}
	if($is_standard==0 || $is_standard==2){
		$sub_query .= " AND cco.standard_name like '%$prepack_name'";
	}
	if($size_name!=""){
		$sub_query_info = " AND size_name='$size_name' ";
	}
	
	if($PID==107655){
		// echo "$str_size / $sub_query<hr/>";
		// print_r($arr_size_qty);
	}
	
	$gmt_pcs_weight = 0;
	if($pack_method<50 && count($arr_size_qty)==0){
		$sql = "SELECT gmt_pcs_weight 
				FROM tblcarton_calculator_sizeinfo 
				WHERE orderno='$orderno' AND is_standard='$is_standard' 
				AND PID='$PID' $sub_query_info AND statusID='1' AND packing_type='$packing_type'";

		$result_pack = $this->conn->prepare($sql);
			$result_pack->execute();
			$num_column = $result_pack->rowCount();
			if($num_column>0 ){ 
				$row_pack=$result_pack->fetch(PDO::FETCH_ASSOC);
				$gmt_pcs_weight = $row_pack["gmt_pcs_weight"];
			}
			else{
				$sql = "SELECT gmt_pcs_weight 
						FROM tblcarton_calculator_sizeinfo 
						WHERE orderno='$orderno' AND is_standard='$is_standard' AND PID='0' 
						$sub_query_info AND statusID='1' AND packing_type='$packing_type'"; 
				$result_pack = $this->conn->prepare($sql);
				$result_pack->execute();
				$row_pack=$result_pack->fetch(PDO::FETCH_ASSOC);
				$gmt_pcs_weight = $row_pack["gmt_pcs_weight"];
			}
	}
	else if(count($arr_size_qty)>0 && $pack_method==2){
		$total_gmt_qty = 0;
		$total_weight = 0;
		foreach($arr_size_qty as $key_size => $str_qty){
			$sql = "SELECT gmt_pcs_weight 
				FROM tblcarton_calculator_sizeinfo 
				WHERE orderno='$orderno' AND is_standard='$is_standard' 
				AND PID='$PID' AND size_name='$key_size'  AND statusID='1' AND packing_type='$packing_type'";
			$result_pack = $this->conn->prepare($sql);
			$result_pack->execute();
			$num_column = $result_pack->rowCount();
			if($num_column>0 ){ 
				$row_pack=$result_pack->fetch(PDO::FETCH_ASSOC);
				$gmt_pcs_weight = $row_pack["gmt_pcs_weight"];
				$this_size_weight = $gmt_pcs_weight * $str_qty;
				$total_gmt_qty += $str_qty;
				$total_weight += $this_size_weight;
			}
			else{
				$sql = "SELECT gmt_pcs_weight 
						FROM tblcarton_calculator_sizeinfo 
						WHERE orderno='$orderno' AND is_standard='$is_standard' AND PID='0' 
						AND size_name='$key_size' AND statusID='1' AND packing_type='$packing_type'"; 
				$result_pack = $this->conn->prepare($sql);
				$result_pack->execute();
				$row_pack=$result_pack->fetch(PDO::FETCH_ASSOC);
				$gmt_pcs_weight = $row_pack["gmt_pcs_weight"];	
				$this_size_weight = $gmt_pcs_weight * $str_qty;
				$total_gmt_qty += $str_qty;
				$total_weight += $this_size_weight;
			}
		}//--- End For Each ---//
		$gmt_pcs_weight = $total_weight / $total_gmt_qty;
	}
	else{ //--- calculate garment weight for Multi Color by ckwai on 201907221608 ---//
		$total_gmt_qty = 0;
		$total_weight = 0;
		$sql = "SELECT spd.ID, sum(spd.ratio_qty) as total_gmt, gmt_qty_in_polybag,
				(SELECT ccs.gmt_pcs_weight FROM tblcarton_calculator_sizeinfo ccs WHERE ccs.orderno = sp.Orderno AND ccs.is_standard = spk.tmode AND ccs.packing_type = spk.packing_type AND ccs.size_name = spd.size_name AND ccs.PID=spd.PID limit 1) as gmt_weight_PID, 
				(SELECT ccs.gmt_pcs_weight FROM tblcarton_calculator_sizeinfo ccs WHERE ccs.orderno = sp.Orderno AND ccs.is_standard = spk.tmode AND ccs.packing_type = spk.packing_type AND ccs.size_name = spd.size_name  limit 1) as gmt_weight,
				spd.size_name 
				FROM `tblship_packing_detail` spd 
				INNER JOIN tblship_packing spk ON spk.PID = spd.PID
				INNER JOIN tblshipmentprice sp ON sp.ID = spk.shipmentpriceID
				WHERE spd.PID = '$PID' AND spd.statusID = 1
				group by spd.size_name";
		// echo "<pre>$sql</pre>";
		$result_pack = $this->conn->prepare($sql);
		$result_pack->execute();
		while($row_pack = $result_pack->fetch(PDO::FETCH_ASSOC)){
				$total_gmt = $row_pack["total_gmt"];
				$gmt_qty_in_polybag = $row_pack["gmt_qty_in_polybag"];
				$gmt_weight_PID = $row_pack["gmt_weight_PID"];
				$gmt_weight = $row_pack["gmt_weight"];
				$gmt_weight = ($gmt_weight_PID!="" && $gmt_weight_PID>0? $gmt_weight_PID: $gmt_weight);
				$total_weight += ($total_gmt * $gmt_qty_in_polybag * $gmt_weight);
				$total_gmt_qty += $total_gmt;
		}
		
		$gmt_pcs_weight = $total_weight / $total_gmt_qty;
	}
	
	if(glb_mainproduct=="BAG"){
		$filter_range = ($is_standard==0 && $total_qty>0? " AND cco.pack_factor>='$total_qty' order by cco.pack_factor asc": "");
	}
	else{
		$filter_range = ($is_standard==0 && $total_qty>0? " AND cco.pack_factor>='$total_qty' order by cco.pack_factor asc": "");
	}
	$sql = "SELECT cco.ext_length, cco.ext_width, cco.ext_height, cco.ext_CBM, cco.pack_factor, cco.ctn_weight, cch.unit,
					cco.length_stack, cco.width_stack, cs.carton_weight as flute_ctn_weight, 
					cco.chk_stack_opt, cco.pack_in_single_stack_opt, cco.ratio_total_qty, ccp.diff_ext_ctn
				FROM `tblcarton_calculator_picklist` ccp
				INNER JOIN tblcarton_calculator_option cco ON cco.CCPID = ccp.CCPID
				INNER JOIN tblcarton_calculator_head cch ON cch.CCHID = ccp.CCHID
				INNER JOIN tblcarton_spec cs ON cs.CSID = cch.flute
				WHERE ccp.PID='$PID' AND cco.selected='1' AND ccp.statusID='1' AND cco.statusID='1' 
				$sub_query AND ccp.packing_method='$pack_method' AND cch.PID='$PID' 
				AND ccp.is_standard='$is_standard' AND cco.pack_factor>0 AND cch.packing_type='$packing_type' $filter_range";
		
		// if(count($arr_size_qty)>0){
			// echo "<pre>$sql</pre>";
		// }
		$result_pack = $this->conn->prepare($sql);
			$result_pack->execute();
			$num_column = $result_pack->rowCount();
		if($num_column>0){ //--- Check whether got calculator Pack Factor for this pick list ---//
			$gmt_pcs_height = 0; $ext_height_last = 0;
			//----- Last Carton According qty to decide height of carton (2018-08-10) -----//
			if($total_qty>0){
				$ht_sub_query = ($pack_method==2? "AND ccsi.size_name='$size_name'":"");
				$sql_ht = "SELECT ccsi.gmt_pcs_height 
							FROM tblcarton_calculator_sizeinfo ccsi
							WHERE ccsi.orderno ='$orderno' $ht_sub_query AND ccsi.PID='$PID' 
							AND ccsi.is_standard='$is_standard' AND statusID='1' AND ccsi.packing_type='$packing_type'
							AND ccsi.gmt_pcs_height>0 limit 1";
				$result_ht = $this->conn->prepare($sql_ht);
				$result_ht->execute();
				while($row_ht = $result_ht->fetch(PDO::FETCH_ASSOC)){
					$gmt_pcs_height = $row_ht["gmt_pcs_height"];
				}
					
				if($pack_method==1){
					$sqlinfo = "SELECT cco.add_height, ccp.diff_ext_ctn
									FROM `tblcarton_calculator_option` cco 
									INNER JOIN tblcarton_calculator_picklist ccp ON ccp.CCPID = cco.CCPID
									INNER JOIN tblcarton_calculator_head cch ON cch.CCHID = ccp.CCHID
									WHERE cch.orderno = '$orderno' AND ccp.packing_method=1 AND ccp.prepack_qty='$prepack_qty' AND cch.PID='$PID'";
					$result_info = $this->conn->prepare($sqlinfo);
					$result_info->execute();
					$row_info = $result_info->fetch(PDO::FETCH_ASSOC);
					$add_height   = $row_info["add_height"];
					$diff_ext_ctn = $row_info["diff_ext_ctn"];
							
					$ext_height_last = ($total_qty * $gmt_pcs_height) + $add_height + $diff_ext_ctn;
				}
						
					// echo "<pre>$sqlinfo / Ext Height: $ext_height</pre>";
			}//-- Total Qty --//
			
		
			$row_pack=$result_pack->fetch(PDO::FETCH_ASSOC);
			$str_unit = $row_pack["unit"];
			$ext_length = $row_pack["ext_length"];
			$ext_width = $row_pack["ext_width"];
			$ext_height = $row_pack["ext_height"];
			$chk_stack_opt = $row_pack["chk_stack_opt"];
			$pack_in_single_stack_opt = $row_pack["pack_in_single_stack_opt"];
			$this_pf = $row_pack["pack_factor"];
			$ratio_total_qty = $row_pack["ratio_total_qty"];
			$diff_ext_ctn = $row_pack["diff_ext_ctn"];
			
			$length_stack = $row_pack["length_stack"];
			$width_stack = $row_pack["width_stack"];
			$stack_divide = floor($length_stack * $width_stack);
			//----- Last Carton According qty to decide height of carton (2018-08-10) -----//
			$ext_height = (($total_qty>0 && $is_standard==1 && $chk_stack_opt==0)? (($total_qty * $gmt_pcs_height / $stack_divide) + $diff_ext_ctn): $ext_height); 
			// $ext_height = (($total_qty>0 && $is_standard==1 && $chk_stack_opt==1)? (($total_qty * $gmt_pcs_height / $stack_divide) + 0.5): $ext_height); 
			
			
			if($PID==77335){
				// echo "<pre>$gmt_pcs_height / ($total_qty x $gmt_pcs_height / $stack_divide) + $diff_ext_ctn<< [$chk_stack_opt / $is_standard] = $ext_height / $ext_height_last</pre>";
			}
			
			if($total_qty>0 && $is_standard==1 && $chk_stack_opt==1){
				$single_stack = $total_qty / $ratio_total_qty;
				$ext_height = $ext_height;//$gmt_pcs_height * $single_stack + $diff_ext_ctn;
			}
			
			if($this->acctid==1){	
				// echo "$PID // $sub_query // $pack_method // $is_standard // $packing_type <br/>";	
				// echo "$sql <br/><br/>";	
				// echo "[$this_pf / $ratio_total_qty] $total_qty x $gmt_pcs_height / $stack_divide <br/><br/>";
			}
			
			//change to 1 decimal (2018-10-05 w)
			if($str_unit=="16"){ //-- CM --//
				$measurement = round($ext_length, 1)." x ".round($ext_width, 1)." x ".round($ext_height, 1)." (cm)";
				$str_length = $ext_length;
				$str_width = $ext_width;
				$str_height = $ext_height;
			}
			else{//-- INCH --//
				$ext_length_inch = $row_pack["ext_length"] * 0.393701;
				$ext_width_inch = $row_pack["ext_width"] * 0.393701;
				$ext_height_inch = $ext_height * 0.393701;
				$measurement = (glb_profile=="iapparelintl"? round($ext_length_inch, 1)." x ".round($ext_width_inch, 1)." x ".round($ext_height_inch, 1)." (inch)":  round($ext_length_inch, 1)." x ".round($ext_width_inch, 1)." x ".round($ext_height_inch, 2)." (inch)");
				$str_length = $ext_length_inch;
				$str_width = $ext_width_inch;
				$str_height = $ext_height_inch;
			}
			$ext_CBM = $row_pack["ext_CBM"];
			$pack_factor = $row_pack["pack_factor"];
			$ctn_weight = $row_pack["ctn_weight"];
			
			if($PID==371){
				// echo "<pre>$ctn_weight</pre>";
			}
			
			//----- Last Carton According qty to decide height of carton (2018-08-10) -----//
				if($total_qty>0){
					$flute_ctn_weight = $row_pack["flute_ctn_weight"];
					$ctn_board_area = ((($ext_length + $ext_width)+2)/100) * (($ext_width + $ext_height)/100);
					$ctn_weight = ($ctn_weight=="" || $ctn_weight==0? round($ctn_board_area * $flute_ctn_weight,3): $ctn_weight);
					$ext_CBM = ($ext_length / 100) * ($ext_width / 100) * ($ext_height / 100);
					$ext_CBM = round($ext_CBM, 3);
				}
		}
		else{ //--- if not, get Default Calculator Pack Factor ---//
			$gmt_pcs_height = 0;
			//----- Last Carton According qty to decide height of carton (2018-08-10) -----//
			if($total_qty>0 && $is_standard==1){
					$ht_sub_query = ($pack_method==2? "AND ccsi.size_name='$size_name'":"");
					$sql_ht = "SELECT ccsi.gmt_pcs_height 
								FROM tblcarton_calculator_sizeinfo ccsi
								WHERE ccsi.orderno ='$orderno' $ht_sub_query AND ccsi.PID='0' 
								AND ccsi.is_standard='$is_standard' AND statusID='1' AND ccsi.packing_type='$packing_type'
								AND ccsi.gmt_pcs_height>0 limit 1";
						$result_ht = $this->conn->prepare($sql_ht);
						$result_ht->execute();
					while($row_ht = $result_ht->fetch(PDO::FETCH_ASSOC)){
							$gmt_pcs_height = $row_ht["gmt_pcs_height"];
					}
			}
			$filter_range .= ($pack_method>=50? " AND ccp.PID='$PID' ": "");
			$sql = "SELECT cco.ext_length, cco.ext_width, cco.ext_height, cco.ext_CBM, cco.pack_factor, cco.ctn_weight, cch.unit, 
							cco.length_stack, cco.width_stack, cs.carton_weight as flute_ctn_weight
				FROM `tblcarton_calculator_picklist` ccp 
				INNER JOIN tblcarton_calculator_option cco ON cco.CCPID = ccp.CCPID
				INNER JOIN tblcarton_calculator_head cch ON cch.CCHID = ccp.CCHID
				INNER JOIN tblcarton_spec cs ON cs.CSID = cch.flute
				WHERE cch.PID='0' AND cco.selected='1' AND ccp.statusID='1' AND cco.statusID='1' 
				$sub_query AND ccp.packing_method='$pack_method' AND cch.orderno='$orderno' 
				AND cch.is_standard='$is_standard' AND cco.pack_factor>0 AND cch.packing_type='$packing_type' $filter_range";
			// echo "<pre>$sql / $filter_range</pre>";
			if($this->acctid==1 && $PID==371 ){	
				// echo "$PID // $sub_query // $pack_method // $is_standard // $packing_type <br/>";	
				// echo "<pre>$sql / $total_qty [$str_size]</pre> <br/>";	
				// print_r($arr_size);
			}
			
			if(count($arr_size_qty)>0){
				// echo "<pre>$sql</pre>";
			}
			
			$result_pack = $this->conn->prepare($sql);
				$result_pack->execute();
				$num_column = $result_pack->rowCount();
			if($num_column>0){
				$row_pack=$result_pack->fetch(PDO::FETCH_ASSOC);
				$str_unit = $row_pack["unit"];
				$ext_length = $row_pack["ext_length"];
				$ext_width = $row_pack["ext_width"];
				$ext_height = $row_pack["ext_height"];
				
				$length_stack = $row_pack["length_stack"];
				$width_stack = $row_pack["width_stack"];
				$stack_divide = floor($length_stack * $width_stack);
				//----- Last Carton According qty to decide height of carton (2018-08-10) -----//
				$ext_height = (($total_qty>0 && $is_standard==1)? (($total_qty * $gmt_pcs_height / $stack_divide) + 0.5): $ext_height); 
				
				if($str_unit=="16"){ //-- CM --//
					$measurement = round($ext_length,1)." x ".round($ext_width,1)." x ".round($ext_height,1)." (cm)";
					$str_length = $ext_length;
					$str_width = $ext_width;
					$str_height = $ext_height;
				}
				else{ //-- INCH --//
					$ext_length_inch = $row_pack["ext_length"] * 0.393701;
					$ext_width_inch = $row_pack["ext_width"] * 0.393701;
					$ext_height_inch = $ext_height * 0.393701;
					$measurement = (glb_profile=="iapparelintl"? round($ext_length_inch, 1)." x ".round($ext_width_inch, 1)." x ".round($ext_height_inch, 1)." (inch)":  round($ext_length_inch, 1)." x ".round($ext_width_inch, 1)." x ".round($ext_height_inch, 2)." (inch)");
					$str_length = $ext_length_inch;
					$str_width = $ext_width_inch;
					$str_height = $ext_height_inch;
				}
				$ext_CBM = $row_pack["ext_CBM"];
				$ctn_weight = $row_pack["ctn_weight"];
				$pack_factor = $row_pack["pack_factor"];
				
				//----- Last Carton According qty to decide height of carton (2018-08-10) -----//
				if($total_qty>0 && $is_standard==1 ){
					$flute_ctn_weight = $row_pack["flute_ctn_weight"];
					$ctn_board_area = ((($ext_length + $ext_width)+2)/100) * (($ext_width + $ext_height)/100);
					$ctn_weight = round($ctn_board_area * $flute_ctn_weight,3);
					$ext_CBM = ($ext_length / 100) * ($ext_width / 100) * ($ext_height / 100);
					$ext_CBM = round($ext_CBM, 3);
				}
				
			}//--- End If num_column > 0 ---//
		}
	//echo "$sql = $pack_factor<br/>";		
	// echo "$measurement = $str_height<br/>";		

	return array($measurement, $ext_CBM, $pack_factor, $ctn_weight, $gmt_pcs_weight, $ext_length, $ext_width, $ext_height, $str_length, $str_width, $str_height);
}

public function funcGetAccWeight($orderno){ // ../form/ctn_index.php
	$grand_acc_weight = 0;
	$sql = "SELECT amt.AMID, (amt.acc_weight) as acc_weight, avg(apd.consum) as consum, bu.Description as b_unit, pu.Description as p_unit 
			FROM tblapurchase ap
			INNER JOIN tblapurchase_detail apd ON apd.APID = ap.APID
			INNER JOIN tblasizecolor ascl ON ascl.ASCID = apd.ASCID
			INNER JOIN tblamaterial amt ON amt.AMID = ascl.AMID
			INNER JOIN tblunit bu ON bu.ID = amt.basic_unit
			INNER JOIN tblunit pu ON pu.ID = amt.price_unit
			WHERE ap.statusID NOT IN (6) AND amt.AtypeID NOT IN (67,43,47)
			AND amt.trimtypeID IN (2) AND ap.orderno='$orderno' AND apd.valid='1'
			group by amt.AMID"; //apd.unitprice>0 AND //67:carton, 43:adhesive tape
	// echo "<pre>$sql</pre>";
	$result_pack = $this->conn->prepare($sql);
	$result_pack->execute();
		$num_column = $result_pack->rowCount();
	if($num_column>0){
		while($row_pack=$result_pack->fetch(PDO::FETCH_ASSOC)){
			$AMID       = $row_pack["AMID"];
			$acc_weight = $row_pack["acc_weight"];
			$consum     = $row_pack["consum"];
			$b_unit     = $row_pack["b_unit"];
			$p_unit     = $row_pack["p_unit"];
			
			$basicUnitPcs = intval(preg_replace('/[^0-9]+/', '', $b_unit), 10);
			$basicUnitPcs = ($basicUnitPcs==0)? 1: $basicUnitPcs;
					
			$priceUnitPcs = intval(preg_replace('/[^0-9]+/', '', $p_unit), 10);
			$priceUnitPcs = ($priceUnitPcs==0)? 1: $priceUnitPcs;
			
			$gmt_consum = round($acc_weight / $priceUnitPcs * $basicUnitPcs * $consum, 6);
			
			// $acc_weight += $row_pack["acc_weight"];
			$grand_acc_weight += $gmt_consum;
			$str_consum = number_format($gmt_consum, 6);
			
			// echo "[$AMID] $str_consum << $acc_weight / $priceUnitPcs x $basicUnitPcs x $consum<br/>";
		}
	}
	
	// echo "<pre>$grand_acc_weight</pre>";
	
	return $grand_acc_weight;
}

public function funcGetGmtWeightForRatioPack($orderno, $is_standard, $PID, $size_name, $packing_type){
	
	$gmt_pcs_weight = 0;
	$sql = "SELECT gmt_pcs_weight 
			FROM tblcarton_calculator_sizeinfo 
			WHERE orderno='$orderno' AND is_standard='$is_standard' AND PID='$PID' 
			AND statusID='1' AND size_name='$size_name' AND packing_type='$packing_type'";
	$result_pack = $this->conn->prepare($sql);
		$result_pack->execute();
		$num_column = $result_pack->rowCount();
		if($num_column>0){ 
			$row_pack=$result_pack->fetch(PDO::FETCH_ASSOC);
			$gmt_pcs_weight = $row_pack["gmt_pcs_weight"];
		}
		else{
			$sql = "SELECT gmt_pcs_weight 
					FROM tblcarton_calculator_sizeinfo 
					WHERE orderno='$orderno' AND is_standard='$is_standard' AND PID='0' 
					AND statusID='1' AND size_name='$size_name' AND packing_type='$packing_type'";
			$result_pack = $this->conn->prepare($sql);
			$result_pack->execute();
			$row_pack=$result_pack->fetch(PDO::FETCH_ASSOC);
			$gmt_pcs_weight = $row_pack["gmt_pcs_weight"];
		}
	return $gmt_pcs_weight;
}

public function update_carton_num_head($start_carton, $end_carton, $shipmentpriceID, $PID, $group_number,
										$qty_in_blisterbag, $this_blisterbag, $total_qty_in_carton, $this_netnet_weight, $this_net_weight,
										$this_gross_weight, $str_measurement, $total_carton_CBM, $is_last, $prepack_name, $ext_length, $ext_width, $ext_height, $str_measurement_last=NULL){
	//echo "|| $start_carton => $end_carton <br/>";
	//for($i=intval($start_carton);$i<=intval($end_carton);$i++){
		//echo "$shipmentpriceID / $PID / $i [$group_number] || <br/>";
		//$ctn_num = $i;
		$ctn_num = $start_carton;
		$temp_grp = 0;
		//$qty_in_blisterbag = $this_qty_in_carton;
		$updatedDate = date("Y-m-d H:i:s");
		$ctn_range = "$start_carton-$end_carton";
						
		//echo " [head] --->> $ctn_range - $shipmentpriceID / $PID / $i [$group_number]  /====>  $this_blisterbag<br/>";
		$this->insertTblcarton_picklist_head($shipmentpriceID, $PID, $ctn_num, $group_number, $temp_grp, 
												$qty_in_blisterbag, $this_blisterbag, $total_qty_in_carton, $this_netnet_weight, 
												$this_net_weight, $this_gross_weight, $str_measurement, $total_carton_CBM, 
												$this->acctid, $updatedDate, $ctn_range, $is_last, $prepack_name, 
												$ext_length, $ext_width, $ext_height, $str_measurement_last);//*/
									
	//}
}

public function update_carton_num_detail($start_carton, $end_carton, $shipmentpriceID, $PID, $size, $group_number, $ratio_qty){
	//for($i=$start_carton;$i<=$end_carton;$i++){
		//$ctn_num = $i;
		$ctn_num = $start_carton;
		//echo " [detail] ---------->> $str_carton_display $shipmentpriceID / $PID / $i [$size / $ratio_qty] <br/>";
		$this->insertTblcarton_picklist_detail($shipmentpriceID, $PID, $ctn_num, $size, $group_number, $ratio_qty);//*/
	//}
}

public function getStrPackingType($packing_type){
	$str = "";
	// switch($packing_type){
		// case 0: $str = "<b><u>Flat Pack</u><b> &nbsp; &nbsp; ";break; 
		// case 1: $str = "<b><u>Hanger Pack</u><b> &nbsp; &nbsp; ";break;
	// }
	
	$sqlpm = "SELECT ID, Description 
							FROM tblpackingmethod 
							WHERE ID='$packing_type'";
	$result = $this->conn->prepare($sqlpm);
	$result->execute();
	$row = $result->fetch(PDO::FETCH_ASSOC);
		$ID = $row["ID"];
		$Description = $row["Description"];
	
	$str = "<b><u>$Description</u><b> &nbsp; &nbsp; ";
	
	return $str;
}

public function updateCartonStartNumberForLinkPO($shipmentpriceID, $orderno, $from_location, $screenID){
	$sql = "SELECT sp.ID, sp.BuyerPO FROM tblshipmentprice sp WHERE sp.linkPOwith='$shipmentpriceID' AND sp.statusID='1'";
	$result = $this->conn->prepare($sql);
	$result->execute();
	$num_column = $result->rowCount();
	$row = $result->fetch(PDO::FETCH_ASSOC);
		$this_buyerPO = $row["BuyerPO"];
		$this_mid = $row["ID"];
		
	$str_display = "";
	if($num_column>0){
		$str_display = "<br/><font color='blue' style='font-size:14px'>Please do update PO 	
								<a href='preview.php?soID=$orderno&&id=$this_mid&&formmode=0&&screen=$screenID'><font color='red'><b>$this_buyerPO</b></font></a> in order to update carton number with link this PO.</font>";
	}
	
	return $str_display;
}

public function getStartCarton($link_shipmentpriceID, $from_location){ //-- New function to get link PO start carton number by ckwai on 201907151514 --//
	$start_carton = 1;
	$sql = "SELECT ctn_range FROM tblcarton_picklist_head$from_location WHERE shipmentpriceID = '$link_shipmentpriceID' order by ctn_num desc";
	$result_pack = $this->conn->prepare($sql);
	$result_pack->execute();
		$row_pack = $result_pack->fetch(PDO::FETCH_ASSOC);
		$ctn_range = $row_pack["ctn_range"];
	if($ctn_range!=""){
		$arr_temp = explode("-",$ctn_range);
		$start_carton = $arr_temp[1] + 1;
	}
	// echo " // start: $start_carton";
	
	return $start_carton;
}

public function getStartCartonByEachSize($link_shipmentpriceID, $this_shipmentpriceID, $this_size){ //-- added by ckwai on 20190723 --//
	$start_carton = 1;
	// $sql = "SELECT cph.ID, cph.shipmentpriceID, cph.PID, cph.group_number, cph.ctn_range, spk.packing_method, spd.size_name, spd.total_qty
	// 		FROM tblcarton_picklist_head cph
	// 		INNER JOIN tblship_packing spk ON spk.PID = cph.PID
	// 		INNER JOIN tblship_packing_detail spd ON spd.PID = spk.PID
	// 		WHERE cph.shipmentpriceID = '$link_shipmentpriceID' AND spd.total_qty>0 AND spd.size_name='$this_size' AND spd.statusID=1";

//modified by SL 30 July 2019... need check with CK again!
$sql = "SELECT cph.shipmentpriceID, cph.PID, cph.group_number, cph.ctn_range, spk.packing_method, spd.size_name, spd.total_qty
			FROM tblcarton_picklist_head cph
			INNER JOIN tblship_packing spk ON spk.PID = cph.PID
			INNER JOIN tblship_packing_detail spd ON spd.PID = spk.PID
			WHERE cph.shipmentpriceID = '$link_shipmentpriceID' AND spd.total_qty>0 AND spd.size_name='$this_size' AND spd.statusID=1";

	$result_pack = $this->conn->prepare($sql);
	$result_pack->execute();
		$row_pack = $result_pack->fetch(PDO::FETCH_ASSOC);
		$ctn_range = $row_pack["ctn_range"];
	if($ctn_range!=""){
		$arr_temp = explode("-",$ctn_range);
		$start_carton = $arr_temp[1] + 1;
	}
	return $start_carton;
}

public function funcCheckUnSetAllAccFabSelectedPO($orderno, $removed_shipmentpriceID){ // shipmentbar/podeletion.php
	$sql = "SELECT ap.APID, ap.shipmentpriceID FROM tblapurchase ap WHERE ap.orderno = '$orderno'";
	$result = $this->conn->prepare($sql);
	$result->execute();
	while($row = $result->fetch(PDO::FETCH_ASSOC)){
		$APID = $row["APID"];
		$this_shipmentpriceID = $row["shipmentpriceID"];
		$ship_arr = explode(",", $this_shipmentpriceID);
		
		if(in_array("$removed_shipmentpriceID", $ship_arr)){
			$key = array_keys($ship_arr, "$removed_shipmentpriceID");
			unset($ship_arr[$key[0]]);
		}
		
		$this_shipmentpriceID = implode(",", $ship_arr);
		//echo "$shipmentpriceID<br/>";
		$update_sql = $this->conn->prepare("UPDATE tblapurchase SET shipmentpriceID = :shipmentpriceID WHERE APID = :APID");
		$update_sql->bindParam(':shipmentpriceID', $this_shipmentpriceID);
		$update_sql->bindParam(':APID', $APID);
		$update_sql->execute();
		
	}//--- End While ---//
	
	$sqlFab = "SELECT mp.MPID, mp.shipmentpriceID FROM tblmpurchase mp WHERE mp.orderno = '$orderno'";
	$result = $this->conn->prepare($sqlFab);
	$result->execute();
	while($row = $result->fetch(PDO::FETCH_ASSOC)){
		$MPID = $row["MPID"];
		$this_shipmentpriceID = $row["shipmentpriceID"];
		$ship_arr = explode(",", $this_shipmentpriceID);
		
		if(in_array("$removed_shipmentpriceID", $ship_arr)){
			$key = array_keys($ship_arr, "$removed_shipmentpriceID");
			unset($ship_arr[$key[0]]);
		}
		
		$this_shipmentpriceID = implode(",", $ship_arr);
		//echo "$shipmentpriceID<br/>";
		$update_sql = $this->conn->prepare("UPDATE tblmpurchase SET shipmentpriceID = :shipmentpriceID WHERE MPID = :MPID");
		$update_sql->bindParam(':shipmentpriceID', $this_shipmentpriceID);
		$update_sql->bindParam(':MPID', $MPID);
		$update_sql->execute();
		
	}//--- End While ---//
	
	return true;
}

public function funcGetPolyBagDetailOfPickList($orderno, $shipmentpriceID, $packing_method, $polybagType){
	$lbl_polybaginfo = "<br/>";
	$sql = "SELECT apd.APDID, apd.ratioMethod, apd.isPolyBagRequired, apd.ASCID, 
					ascl.Standard, c.ColorName as color, astp.Description as sub_type
			FROM `tblapurchase_detail` apd 
			INNER JOIN tblapurchase ap ON ap.APID = apd.APID
			INNER JOIN tblasizecolor ascl ON ascl.ASCID = apd.ASCID
			INNER JOIN tblcolor c ON c.ID = ascl.colorID
			INNER JOIN tblamaterial amt ON amt.AMID = ascl.AMID
			LEFT JOIN tblasubtype astp ON astp.ID = amt.AsubtypeID
			WHERE ap.orderno = '$orderno' AND ap.statusID NOT IN (6) 
			AND ap.byMethod=5 and apd.ratioMethod='$packing_method' 
			AND apd.isPolyBagRequired='$polybagType' AND find_in_set($shipmentpriceID, ap.shipmentpriceID)";
	$result = $this->conn->prepare($sql);
	$result->execute();
	while($row = $result->fetch(PDO::FETCH_ASSOC)){
		$sub_type = $row["sub_type"];
		$Standard = $row["Standard"];
		$color = $row["color"];
		$lbl_polybaginfo .= "<br/><small><font color='blue'><i>$sub_type // $Standard [$color]</i></font></small><br/>";
	}
	
	return $lbl_polybaginfo;
}

public function funcGetAllCartonDisplayFormula($shipmentpriceID, $soID, $from_location, $filter_query){ //ship_saving.php, form/ctn_saving.php
	$mid = $shipmentpriceID; $balpack = 0;
	$this->deleteTblcarton_picklist($shipmentpriceID);
	
	$sqllinkPO = "SELECT sp.linkPOwith, sp.byEachSize, splink.BuyerPO as linkBuyerPO, sp.grouporcolor, sp.gap 
					FROM tblshipmentprice sp 
					LEFT JOIN tblshipmentprice splink ON splink.ID = sp.linkPOwith
					WHERE sp.ID='$shipmentpriceID' "; //AND sp.statusID NOT IN (2)
	$resultlinkPO = $this->conn->prepare($sqllinkPO);
	$resultlinkPO->execute();
	$poinfo = $resultlinkPO->fetch(PDO::FETCH_ASSOC);
		$linkPOwith = $poinfo["linkPOwith"];
		$byEachSize = $poinfo["byEachSize"];
		$linkBuyerPO = $poinfo["linkBuyerPO"];
		$grouporcolor = $poinfo["grouporcolor"];
		$gap = $poinfo["gap"];
	
	$start_carton = ($linkBuyerPO==""? 1: $this->getStartCarton($linkPOwith, $from_location));
	
	$sql = "SELECT spk.PID, spk.packing_method, spk.is_polybag, spk.is_blisterbag, spk.tmode, spk.is_ctnblister, spk.packing_type, spk.ship_remark,
							(SELECT spd.size_name 
							FROM tblship_packing_detail$from_location spd 
							INNER JOIN tblship_packing$from_location spk2 ON spk2.PID = spd.PID
							WHERE spd.PID = spk.PID AND spd.total_qty>0 AND spd.statusID=1 AND spk2.packing_method=2 limit 1) as size_name, spk.order_by_color, spk.is_multi_gender
			FROM tblship_packing$from_location spk WHERE spk.shipmentpriceID='$shipmentpriceID' AND spk.statusID='1' order by spk.PID";
	$result = $this->conn->prepare($sql);
	$result->execute();
	
	while($row = $result->fetch(PDO::FETCH_ASSOC)){
			$PID = $row["PID"];
			$pack_method = $row["packing_method"];
			$is_polybag = $row["is_polybag"];
			$is_blisterbag = $row["is_blisterbag"];
			$is_ctnblister = $row["is_ctnblister"];
			$this_tmode = $row["tmode"];
			$packing_type = $row["packing_type"];
			$ship_remark = $row["ship_remark"];		//remark field (2018-10-25 w)	
			$this_size = $row["size_name"];		// added by ckwai on 20190723							
			$order_by_color = $row["order_by_color"];		// added by ckwai on 20200706							
			$is_multi_gender = $row["is_multi_gender"];		// added by ckwai on 20200706							
			$html = "";
			$is_pdf = false;
			//$is_pdf = ((isset($_GET["saving"])) ? false: true);
						
			$start_carton = ($byEachSize==1 && $pack_method==2? 1: $start_carton);//-- check whether ctn no. start by each size added by ckwai on 20190723 --//
			// echo 'start'.$start_carton.'<br>';
			// echo $linkBuyerPO.'>>'.$this_size.'>>'.$linkPOwith.'>>>'.$mid;

			$start_carton = (($linkBuyerPO!="" && $this_size!="")? $this->getStartCartonByEachSize($linkPOwith, $mid, $this_size): $start_carton);//-- check whether ctn no. start by each size with link PO added by ckwai on 20190723 --//
						
			// echo 'start2'.$start_carton.'<br>';
			// echo "$pack_method <br/>";
						
			switch($pack_method){
				case 1:list($html, $start_carton) = $this->funcPackingCartonDisplayFormula_SingleColorMultiSize($PID, $soID, $mid, 
																$start_carton, $grouporcolor, $is_polybag, $is_blisterbag, $is_ctnblister, 
																$this_tmode, $gap, $is_pdf, $packing_type, $ship_remark);break;
				case 2:list($html, $start_carton) = $this->funcPackingCartonDisplayFormula_SingleColorSingleSize($PID, $soID, $mid, 
																$start_carton, $grouporcolor, $is_polybag, $is_blisterbag, $is_ctnblister, $gap, 
																$balpack, $this_tmode, $is_pdf, $packing_type, $ship_remark, $order_by_color);break;
				case 50:list($html, $start_carton) = $this->funcPackingCartonDisplayFormula_MultiColorRatioPack($PID, $soID, $mid, 
																$start_carton, $grouporcolor, $is_polybag, $is_blisterbag, $is_ctnblister, $gap, 
																$balpack, $this_tmode, $is_pdf, $packing_type, $ship_remark, $is_multi_gender);break;
							
			}//--- End Switch ---//
							
		//echo $html;
	}
	
}

public function funcPackingCartonDisplayFormula_SingleColorMultiSize($PID, $soID, $shipmentpriceID, $start_carton, $color_type, 
													$is_polybag, $is_blisterbag, $is_ctnblister, $tmode, $is_gap, $is_pdf, $packing_type, $ship_remark, $grp_size=""){
	$lang = $this->lang;
	$from_location = $this->from_location;
	
	$packing_method = 1;
	$lbl_polybaginfo = ($is_polybag==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "1"): ""); // polybag
	$lbl_blisterbaginfo = ($is_blisterbag==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "2"): ""); // blisterbag
	$lbl_ctnblisterbaginfo = ($is_ctnblister==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "3"): ""); // ctn blisterbag
	
	$path = "../lang/{$lang}.php";
	$path2 = "../../lang/{$lang}.php";
	$chk = file_exists($path);
	$url = ($chk==1? $path: $path2);
	
	include($url);
	$arr_chk_last = array();
	$html = "";
	$pack_method = 1;
	$grand_total_qty = 0;
	$grand_total_cbm = 0;
	$grand_nnw=0; $grand_nw=0; $grand_gw=0;
	$str_polybag = ($is_polybag==1?"<u><b>Poly Bag Required</b></u> &nbsp; &nbsp; ":"");
	$str_blisterbag = ($is_blisterbag==1?"<u><b>Blister Bag Required</b></u> &nbsp; &nbsp; ":"");
	$str_ctn_blister = ($is_ctnblister==1?"<u><b>Carton Blister Required</b></u> &nbsp; &nbsp; ":"");
	$str_standard = $this->checkStandardName($tmode);
	$btn_carton_calculator = ($is_pdf==false? "": $this->funcLinkToCalculator($soID, $PID, $pack_method, $tmode, $packing_type));

	$str_PID = "&nbsp; <em>PID: <b><u>$PID</u></b></em> &nbsp; &nbsp;  $btn_carton_calculator &nbsp; ";
	
	$columnsql2 = $this->conn->prepare("select spd.*, spk.tmode, sp.balpack,
											(SELECT GROUP_CONCAT(DISTINCT spd2.size_name order by spd2.size_name asc) 
											 FROM tblship_packing_detail$from_location spd2
											 WHERE spd2.PID = spk.PID AND spd2.statusID=1 AND spd2.group_number = spd.group_number AND spd2.ratio_qty>0) as grp_size, spk.is_polybag, spk.is_blisterbag, spk.is_ctnblister  
										from tblship_packing_detail$from_location spd
										inner join tblship_packing$from_location spk ON spk.PID = spd.PID
										inner join tblshipmentprice sp ON sp.ID = spk.shipmentpriceID
										where spd.PID = '$PID' AND spd.statusID='1' 
										group by spd.group_number ORDER BY spd.ID asc");
	$columnsql2->execute();	
	
	$html .= "<br/>
			<b class='subTitle'>".$hdlang["packing_method"].": ".$hdlang["SCRP"]."</b>";
	$html .= "&nbsp;<span class='glyphicon glyphicon-list-alt btntop editcode' data-toggle='modal' 
							data-target='#methodbox' data-id='$PID' data-backdrop='static' data-keyboard='false' title='".$hdlang["ship_12"]."' 
							style='display:inline-block;cursor:pointer'></span>
							&nbsp;<span style='padding:3px;background-color:red;color:#fff;border-radius:5px'><b>".$this->count_attach."</b></span>&nbsp; "; //--- Packing Method Attachment ---//
	$html .= "&nbsp; $str_PID<br/>"; //-- Packing Method --// //-- Single Color (Carton) - Prepack By Single Color Multiple Size --// 
	$html .= "$str_polybag  ";
	$html .= "$str_blisterbag ";
	$html .= "$str_ctn_blister ";
	$html .= " <b><u>$str_standard</u></b> &nbsp; &nbsp; ";
	$html .= $this->getStrPackingType($packing_type);
	//remark field (2018-10-25 w)
	if($ship_remark != ""){
		$html .= "<b>Remark:</b> $ship_remark";		
	}	
	$html .= "<br/>";
	$html .= "<br/>";
	
	$ctn_width = ($this->pdf_display==1? "30px": "70px");
	
	$html .= '<table class="tb_detail pick_list" id="tb_detail" cellspacing="0" border="1" cellpadding="3" >';
	$html .= '<tr class="titlebar">';
		$html .= '<th class="titlecol" rowspan="2" style="width:'.$ctn_width.';min-width:'.$ctn_width.'">'.$hdlang["carton_no"].'</th>';//-- Carton No --//
		$html .= '<th class="titlecol" rowspan="2" style="width:50px;min-width:50px;white-space:nowrap">'.$hdlang["total_carton"].'</th>';//-- Total Carton --//
		$html .= '<th class="titlecol" rowspan="2" style="width:50px;min-width:50px">'.$hdlang["pc_per_pack"].'#</th>';//-- Prepack --//
		$html .= '<th class="titlecol" rowspan="2">'.$hdlang["Color"].'</th>';//-- Color --//
		
		$num_column = $this->getSizeNameColumnFromOrder($soID, "0");
		$wd_size = $num_column * 25;
		$pack_method = 1;
		list($str_1st, $str_2nd, $str_3rd, $str_4th) = $this->chkTickedOfPolyBlisterBag($pack_method, $is_polybag, $is_blisterbag);
		$html .= '<th class="titlecol" colspan="'.$num_column.'" style="width:'.$wd_size.'px;min-width:'.$wd_size.'px;">'.$hdlang["Size"].' </th>';//-- Size --//
		$html .= '<th class="titlecol" style="white-space:nowrap;width:40px;min-width:40px" rowspan="2">'.$str_1st.'</th>';//-- Prepack --//
		$html .= '<th class="titlecol" style="white-space:nowrap;width:40px;min-width:40px" rowspan="2">'.$str_2nd.'</th>';//-- Prepack Per Carton --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Total Qty <br/>in 1 Carton</th>';//-- Total Qty in 1 Carton --//
		$html .= '<th class="titlecol" style="white-space:nowrap;width:30px;min-width:30px;" rowspan="2">'.$hdlang["total_set"].'</th>';//-- Total Qty --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Net Net Weight (total ctn)</th>';//-- Net Net Weight --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Net Weight (total ctn)</th>';//-- Net Weight --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Gross Weight (total ctn)</th>';//-- Gross Weight --//
		$html .= '<th class="titlecol" style="white-space:nowrap;width:55px;min-width:55px;" rowspan="2">Carton Measurement <br/>(L*W*H)</th>';//-- Carton Measurement --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Total CBM</th>';//-- Total CBM --//
	$html .= "</tr>";
	
	//---- Display Size Range of order ----//
	$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
	$html .= "<tr class='titlebar'>";
	for($c=0;$c<$num_column;$c++){
		$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
		$size = $columntitle2['SizeName'];
		
		$html .= "<th class='titlecol' style='width:25px;min-width:25px'>$size</th>";
	}
	$html .= "</tr>";
	
	$temp_last_group_number = -1; 
	while($row=$columnsql2->fetch(PDO::FETCH_ASSOC)){
		$group_number = $row["group_number"];
		$polybag_qty_in_blisterbag = $row["polybag_qty_in_blisterbag"];
		$prepack_qty = $row["polybag_qty_in_blisterbag"];
		$blisterbag_in_carton = $row["blisterbag_in_carton"];
		$total_qty = $row["total_qty"];
		$tmode = $row["tmode"];
		$is_standard = $row["tmode"];
		$balpack = $row["balpack"];
		$grp_size = $row["grp_size"];
		$is_decimal = 0;
		$size_name = "color";
		$SKU = $row["SKU"];
		$master_upc = $row["master_upc"];
		$case_upc = $row["case_upc"];


		
		if($SKU=="" && $from_location!=""){//if SKU empty, auto link from buyer PO on 2021-12-06 by ckwai
			$sqlsku = "SELECT spd.SKU, spd.master_upc, spd.case_upc
						FROM tblship_packing_detail spd
						WHERE spd.PID='$PID' AND spd.statusID=1 AND spd.group_number='$group_number' AND spd.SKU!=''";
			$stmtsku = $this->conn->prepare($sqlsku);
			$stmtsku->execute();	
			$rowsku = $stmtsku->fetch(PDO::FETCH_ASSOC);
			$SKU = $rowsku["SKU"];
			$master_upc = $rowsku["master_upc"];
			$case_upc = $rowsku["case_upc"];
		}
		$display_MU_CU="";
		if($master_upc<>"" || $case_upc<>""){
			$display_MU_CU = "<br>MU:$master_upc<br>CU:$case_upc";
		}
		
		// set the temp_last group number
		if($temp_last_group_number == -1){
			$temp_last_group_number = $group_number;
		}

		// print the last carton if group number(color) change 
		if($temp_last_group_number != $group_number){
			if(count($arr_chk_last)>0){
				list($this_html, $start_carton, $grand_total_qty, $grand_total_cbm) = 
						$this->checkBalanceQtyInCarton_SCRS($PID, $shipmentpriceID, $arr_chk_last, $soID, $start_carton, $color_type, $is_standard, 
																$is_gap, $grand_total_qty, $grand_total_cbm, $is_pdf, $packing_type,
																$grand_nnw, $grand_nw, $grand_gw, $is_polybag, $is_blisterbag, $is_ctnblister);
				$html .= $this_html;
				
				$arr_chk_last = [];
			}

			$temp_last_group_number = $group_number;
		}
		
		if($total_qty>0){
			$total_qty_in_carton = $polybag_qty_in_blisterbag * $blisterbag_in_carton;
			$total_carton = $total_qty / $total_qty_in_carton;
			//$total_carton = ceil($total_carton); //--- Temp ---//
			$is_decimal = ($total_qty%$total_qty_in_carton);
			$able_carton = floor($total_qty / $total_qty_in_carton); //--- 2018-08-14 ---//
			$end_carton = $start_carton + floor($total_carton) - 1;
			$actual_total_carton = $end_carton - $start_carton + 1;
			$str_color_ID = $this->getComboOrSingleColorNameDisplay($shipmentpriceID, $group_number, $color_type, "1");
			$str_carton_display = "$start_carton - $end_carton";
			
			//----- Check From Pack Factor Qty -----//
			$sql_factor = "SELECT amount, kg, amount_2, amount_3, amount_4 as L, amount_5 as W, amount_6 as H, amount_7 as CBM 
							FROM tblpackfactor 
							WHERE orderno='$soID' AND colorID = '$str_color_ID' AND size='$size_name' AND del='0' AND tmode='$tmode'";
			$result_factor = $this->conn->prepare($sql_factor);
			$result_factor->execute();
			$row_factor = $result_factor->fetch(PDO::FETCH_ASSOC);
				//$this_factor = $row_factor["amount"];
				$this_kg = $row_factor["kg"];
				$this_accweight = $row_factor["amount_2"]; //--- Acc Weight ---//
				$this_cartonweight = $row_factor["amount_3"]; //-- Carton Weight ---//
				$this_length = $row_factor["L"];
				$this_width = $row_factor["W"];
				$this_height = $row_factor["H"];
				$this_CBM = $row_factor["CBM"];
				$str_measurement = "$this_length x $this_width x $this_height";
				//$total_carton_CBM = $this_CBM * $actual_total_carton;
				$this_size="";
				$this_factor = $this->funcGetPackFactor($soID, $PID, $pack_method, $this_size, $prepack_qty, $is_standard, $packing_type, $grp_size);
				//$is_gap =($is_standard==0? 1:0);
				$is_gap = (($is_standard==1)? 0:$tmode);
				$is_gap = (($is_standard==0)? 1:$is_gap);
			
			// echo "$this_factor [$PID] / $total_qty_in_carton / $total_qty<< <br/>";
			$str_extra = ($this_factor==0? "<br/><font color='red'>No Pack Factor</font>":"");
			if($actual_total_carton==0 || $is_decimal>0){ //$total_qty_in_carton!=$this_factor || 
				$actual_total_carton = 0;
				$str_carton_display = "Cannot Pack";
			}
			
			//---- Check Whether got balance for last carton by ckwai on 2018-08-14 ----//
			if($able_carton>0 && $is_decimal>0){
				$end_carton = $start_carton + $able_carton - 1;
				$actual_total_carton = $end_carton - $start_carton + 1;
				$str_carton_display = "$start_carton - $end_carton";
				$this_balance_qty = $total_qty - ($actual_total_carton * $total_qty_in_carton);
				$arr_chk_last["$group_number=^$total_qty_in_carton=^$this_factor=^$SKU"] = $this_balance_qty;
			}
			
			$prepack_name = $this->checkCartonName($is_gap, $this_factor, $total_qty_in_carton, $balpack);
			
			$html .= "<tr>";
			$html .= "<td class='topcolortd leftcolortd'>$str_carton_display $str_extra</td>";//[$total_qty / $total_carton]
			$html .= "<td class='topcolortd leftcolortd'>$actual_total_carton <br>$prepack_name $lbl_ctnblisterbaginfo</td>";//--- Total Carton ---//
			$html .= "<td class='topcolortd leftcolortd'>$SKU $display_MU_CU</td>";//--- Prepack# ---//
			$str_color = $this->getComboOrSingleColorNameDisplay($shipmentpriceID, $group_number, $color_type, "0");
			$html .= "<td class='topcolortd leftcolortd'>$str_color</td>";//--- Color ---//
			
			$total_prepack = 0;
			$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
			$this_gmt_weight_total = 0;  $arr_size_qty = array(); $arr_size = array();
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				
				//--- Get Ratio Qty by size ---//
				$sql = "SELECT spd.ratio_qty, spd.gmt_qty_in_polybag  
						FROM tblship_packing_detail$from_location spd 
						WHERE spd.PID='$PID' AND spd.group_number='$group_number' AND size_name='$size' AND statusID='1'";
				$result_ratio = $this->conn->prepare($sql);
				$result_ratio->execute();	
					$row_ratio = $result_ratio->fetch(PDO::FETCH_ASSOC);
					$ratio_qty = $row_ratio["ratio_qty"];
					$gmt_qty_in_polybag = $row_ratio["gmt_qty_in_polybag"];
					
					if($ratio_qty>0){
						$arr_size[] = $size;
					}
					
				//-------- Calculation to get garment weight per carton --------//	
				$this_gmt_weight = $this->funcGetGmtWeightForRatioPack($soID, $is_standard, $PID, $size, $packing_type);
				$this_size_weight = $this_gmt_weight * $ratio_qty;
				$this_gmt_weight_total += $this_size_weight;
				
				$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$ratio_qty $lbl_polybaginfo</td>";
				$total_prepack += $ratio_qty;
				
				//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
				//====> Able Pack Carton, record size detail carton in database <====//
				//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
				if($str_carton_display!="Cannot Pack" && $is_pdf==false){
					$this->update_carton_num_detail($start_carton, $end_carton, $shipmentpriceID, $PID, $size, $group_number, $ratio_qty);
				}
			}//---- End For Loop ----//
			
			$this_total_qty = $total_qty_in_carton * $actual_total_carton;
			$this_total_qty = ($actual_total_carton==0? $total_qty_in_carton: $this_total_qty);
			
			// $this_netnet_weight = $this_kg * $this_total_qty;
			// $this_net_weight = $this_kg + $this_accweight;
			// $this_gross_weight = $this_kg + $this_accweight + ($this_cartonweight * $actual_total_carton);
			
			//Modified by ckwai on 2018-07-25
			$size_name = "";
			list($str_measurement, $ext_CBM, $pack_factor, $ctn_weight, $gmt_pcs_weight, $str_length, $str_width, $str_height) = 
															$this->funcGetCartonMeasurement($soID, $PID, $pack_method, $size_name, 
																							$total_prepack, $is_standard, $prepack_name,0, $packing_type, $arr_size_qty, $arr_size);
	
			//$total_carton_CBM = $ext_CBM * $actual_total_carton;
			//$total_carton_CBM = ($str_length/100) * ($str_width/100) * ($str_height/100) * $actual_total_carton;// modified by ckwai on 202007021138 for rith issue of decimal point one carton CBM
			$single_carton_CBM = round(($str_length/100) * ($str_width/100) * ($str_height/100), 3);
			$total_carton_CBM = round($single_carton_CBM * $actual_total_carton, 3);
			$acc_weight = $this->funcGetAccWeight($soID);
			$ori_acc_weight = $acc_weight;
			
			
			//$this_netnet_weight = $this_total_qty * $gmt_pcs_weight;
			$this_netnet_weight = $this_gmt_weight_total * $blisterbag_in_carton;
			$this_net_weight = $this_netnet_weight + $acc_weight;
			$this_gross_weight = $ctn_weight + $this_net_weight;
			//>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<//
			//====> Able Pack Carton, record in database <====//
			//>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<//
			if($str_carton_display!="Cannot Pack" && $is_pdf==false){
				$qty_in_blisterbag = $total_prepack;
				$this_blisterbag = $blisterbag_in_carton;
				$is_last = 0;
				$this->update_carton_num_head($start_carton, $end_carton, $shipmentpriceID, $PID, $group_number,
										$qty_in_blisterbag, $this_blisterbag, $total_qty_in_carton, $this_netnet_weight, $this_net_weight,
										$this_gross_weight, $str_measurement, $total_carton_CBM, $is_last, $SKU, $str_length, $str_width, $str_height);

			}
			$start_carton += $actual_total_carton;
			$grand_total_qty += $this_total_qty;
			$grand_total_cbm += $total_carton_CBM;
			
			//convert to LBS if is 41 (2018-10-05 w)
			$unit = $this->check_kg_lbs($soID, $PID, $is_standard, $packing_type);
			$wg_unit = $this->wg_unit;
			$str_unit = "KG";
			if(($unit == 41 && $wg_unit==0) || $wg_unit==57){ //inch use lbs and cm use KGS due to Joe Fresh Order needed on 2020-11-17
				$this_netnet_weight = $this_netnet_weight * 2.204622622;
				$this_net_weight = $this_net_weight * 2.204622622;
				$this_gross_weight = $this_gross_weight * 2.204622622;
				$acc_weight = round($acc_weight * 2.204622622,5);
				$str_unit = "LBS";
			}
			$str_acc_weight = number_format($acc_weight, 5);
			//convert to one decimal (2018-10-05 w) //change 3 decimal point request by shipping rithy 20220222/1005
			$this_netnet_weight = round($this_netnet_weight, 3); //$this->to_one_dec($this_netnet_weight);
			$this_net_weight    = round($this_net_weight, 3); //$this->to_one_dec($this_net_weight);
			$this_gross_weight  = round($this_gross_weight, 3); //$this->to_one_dec($this_gross_weight);
			
			$display_nnw = round($this_netnet_weight * $actual_total_carton, 2);
			$display_nw  = round($this_net_weight * $actual_total_carton, 2);
			$display_gw  = round($this_gross_weight * $actual_total_carton, 2);
			
			$grand_nnw += $display_nnw;
			$grand_nw += $display_nw;
			$grand_gw += $display_gw;
			
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_prepack</td>";//--- Prepack ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$blisterbag_in_carton $lbl_blisterbaginfo</td>";//--- Prepack Per Carton ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_qty_in_carton</td>";//--- Carton Qty ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$this_total_qty</td>";//--- Total Qty ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_nnw $str_unit
									<br/><small><i><font color='blue'>$this_netnet_weight $str_unit/ per ctn</font></i></small>
									</td>";//--- Net Net Weight ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff;white-space:nowrap'>$display_nw $str_unit
													<br/><small><i><font color='blue'>NW: $this_net_weight $str_unit/ per ctn</font></i></small>
													<br/><small><i><font color='blue'>Acc Wgt: $str_acc_weight $str_unit/ per ctn</font></i></small>
													</td>";//--- New Weight ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_gw $str_unit
											<br/><small><i><font color='blue'>GW: $this_gross_weight $str_unit/ per ctn</font></i></small>
											<br/><small><i><font color='blue'>Ctn Wgt: $ctn_weight / per ctn</font></i></small>
											</td>";//--- Gross Weight ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$str_measurement</td>";//--- Carton Measurement ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_carton_CBM
								<br/><small><i><font color='blue'>$single_carton_CBM / per ctn</font></i></small>
								</td>";//--- Total CBM ---//
			$html .= "</tr>";
			
			//$html .= "[$group_number] $polybag_qty_in_blisterbag - $blisterbag_in_carton - $total_qty <br/>";
		}
	}//--- End While ---//
	
	if(count($arr_chk_last)>0){
		list($this_html, $start_carton, $grand_total_qty, $grand_total_cbm, $grand_nnw, $grand_nw, $grand_gw) = 
				$this->checkBalanceQtyInCarton_SCRS($PID, $shipmentpriceID, $arr_chk_last, $soID, $start_carton, $color_type, $is_standard, 
														$is_gap, $grand_total_qty, $grand_total_cbm, $is_pdf, $packing_type, $grand_nnw, $grand_nw, $grand_gw,
														$is_polybag, $is_blisterbag, $is_ctnblister);
		$html .= $this_html;
		
		$arr_chk_last = [];//-- Modified by JX 20190103 --//
	}
	
	list($this_html) = $this->funcLastTotalDisplay($grand_total_qty, $soID, $num_column, $grand_total_cbm, $grand_nnw, $grand_nw, $grand_gw, $str_unit);
	$html .= $this_html;
	
	$html .= "</table><br/><br/>";
	return array($html, $start_carton);
}

public function checkBalanceQtyInCarton_SCRS($PID, $shipmentpriceID, $arr_chk_last, $soID, $start_carton, $color_type, $is_standard, 
												$is_gap, $grand_total_qty, $grand_total_cbm, $is_pdf, $packing_type, $grand_nnw=0, $grand_nw=0, $grand_gw=0,
												$is_polybag, $is_blisterbag, $is_ctnblister){
	$packing_method = 1;
	$lbl_polybaginfo = ($is_polybag==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "1"): ""); // polybag
	$lbl_blisterbaginfo = ($is_blisterbag==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "2"): ""); // blisterbag
	$lbl_ctnblisterbaginfo = ($is_ctnblister==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "3"): ""); // ctn blisterbag		
	
	$html = ""; $this_str_pack_name = "";
	$from_location = $this->from_location;
	foreach($arr_chk_last as $key => $balance_qty){
		$arr_temp = explode("=^", $key);
		$group_number = $arr_temp[0];
		$total_qty_in_carton = $arr_temp[1];
		$pack_factor = $arr_temp[2];
		$SKU = $arr_temp[3];
		$end_carton = $start_carton;
		$str_carton_display = "$start_carton - $end_carton";
		$prepack_name = "";
		$str_pack_name = "";
		if($is_gap>0){
			$balpack = 0;
			$this_packing_method = 1;// single color ratio
			$this_size = "";
			list($tt_ctnqty, $tt_qtyincarton, $tt_balqty, $this_str_pack_name) =
					$this->checkBalanceCartonQty($is_gap, $pack_factor, $balance_qty, $balpack, "last", $shipmentpriceID, $is_standard, 
													$PID, $packing_type, $this_packing_method, $this_size);
			$str_pack_name = $this_str_pack_name;
		}
		
		
		$html .= "<tr>";
		$html .= "<td class='topcolortd leftcolortd'>$str_carton_display</td>";//[$total_qty / $total_carton]
		$html .= "<td class='topcolortd leftcolortd'>1 <br/>$this_str_pack_name $lbl_ctnblisterbaginfo</td>";//--- Total Carton ---//
		$html .= "<td class='topcolortd leftcolortd'>$SKU <br> <span class='label label-default label-xs'>Last Carton</span></td>";//--- Prepack# ---//
		$str_color = $this->getComboOrSingleColorNameDisplay($shipmentpriceID, $group_number, $color_type, "0");
		$html .= "<td class='topcolortd leftcolortd'>$str_color</td>";//--- Color ---//
		
		$total_prepack = 0;
		$num_column = $this->getSizeNameColumnFromOrder($soID, "0");
		$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
		$this_gmt_weight_total = 0; $arr_size_qty = array(); $arr_size = array();
		for($c=0;$c<$num_column;$c++){
			$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
			$size = $columntitle2['SizeName'];
			
			//--- Get Ratio Qty by size ---//
			$sql = "SELECT spd.ratio_qty FROM tblship_packing_detail$from_location spd 
					WHERE spd.PID='$PID' AND spd.group_number='$group_number' AND size_name='$size' AND statusID='1'";
			$result_ratio = $this->conn->prepare($sql);
			$result_ratio->execute();	
				$row_ratio = $result_ratio->fetch(PDO::FETCH_ASSOC);
				$ratio_qty = $row_ratio["ratio_qty"];
			
			//-------- Calculation to get garment weight per carton --------//
			$this_gmt_weight = $this->funcGetGmtWeightForRatioPack($soID, $is_standard, $PID, $size, $packing_type);
				$this_size_weight = $this_gmt_weight * $ratio_qty;
				$this_gmt_weight_total += $this_size_weight;
				
			if($ratio_qty>0){
				$arr_size[] = $size;
			}
				
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$ratio_qty $lbl_polybaginfo</td>";
			$total_prepack += $ratio_qty;
			
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
			//====> Able Pack Carton, record size detail carton in database <====//
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
			if($str_carton_display!="Cannot Pack" && $is_pdf==false){
				$this->update_carton_num_detail($start_carton, $end_carton, $shipmentpriceID, $PID, $size, $group_number, $ratio_qty);
			}
		}//--- End For ---//
		$blisterbag_in_carton = $balance_qty / $total_prepack;
		$pack_method = 1;
		$size_name = ""; 
		
		if($PID==77335){
			$str_size = implode(",", $arr_size);
			// echo "Last Carton:  [".$str_size."]/ [$balance_qty] $total_prepack<br/>";
		}
		
		list($str_measurement, $ext_CBM, $pack_factor, $ctn_weight, $gmt_pcs_weight, $str_length, $str_width, $str_height) = 
									$this->funcGetCartonMeasurement($soID, $PID, $pack_method, $size_name, 
																		$total_prepack, $is_standard, $str_pack_name, $balance_qty, $packing_type, $arr_size_qty, $arr_size);
		//$total_carton_CBM = $ext_CBM * 1;
		$total_carton_CBM = ($str_length/100) * ($str_width/100) * ($str_height/100) * 1;// modified by ckwai on 202007021138
		$total_carton_CBM = round($total_carton_CBM, 3);
		$acc_weight = $this->funcGetAccWeight($soID);
		
		//$this_netnet_weight = $this_total_qty * $gmt_pcs_weight;
		$this_netnet_weight = $this_gmt_weight_total * $blisterbag_in_carton;
		$this_net_weight = $this_netnet_weight + $acc_weight;
		$this_gross_weight = $ctn_weight + $this_net_weight;
		
		// $grand_total_cbm += $total_carton_CBM;
		
		$percentage = 1;//$balance_qty / $pack_factor;
		$est_height = ceil($str_height * $percentage);
		$est_CBM    = ($str_length / 100) * ($str_width/100) * ($est_height/100); 
		$est_CBM    = round($est_CBM, 3);
		$grand_total_cbm += $est_CBM;
		
		//convert to LBS if is 41 (2018-10-05 w)
		$unit     = $this->check_kg_lbs($soID, $PID, $is_standard, $packing_type);
		$wg_unit  = $this->wg_unit;
		$str_unit = "KG";
		if($unit == 41){ //inch use lbs and cm use KGS due to Joe Fresh Order needed on 2020-11-17
			
			if($wg_unit==0 || $wg_unit==57){
				$this_netnet_weight = $this_netnet_weight * 2.204622622;
				$this_net_weight = $this_net_weight * 2.204622622;
				$this_gross_weight = $this_gross_weight * 2.204622622;
				$str_unit = "LBS";
			}
			
			$str_length_inch = $str_length * 0.393701;
			$str_width_inch  = $str_width * 0.393701;
			$str_height_inch = $est_height * 0.393701;
			
			$str_measurement = "".round($str_length_inch, 1)." x ".round($str_width_inch, 1)." x ".round($str_height_inch, 1)." (inch)";
		}
		else{
			$str_measurement = "".round($str_length,1)." x ".round($str_width,1)." x ".round($est_height,1)." (cm)";
		}
		
		//>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<//
		//====> Able Pack Carton, record in database <====//
		//>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<//
		if($str_carton_display!="Cannot Pack" && $is_pdf==false){
			$qty_in_blisterbag = $total_prepack;
			$total_qty_in_carton = $balance_qty;
			$this_blisterbag = $blisterbag_in_carton;
			$is_last = 1;//last carton
			$this->update_carton_num_head($start_carton, $end_carton, $shipmentpriceID, $PID, $group_number,
											$qty_in_blisterbag, $this_blisterbag, $total_qty_in_carton, $this_netnet_weight, $this_net_weight,
											$this_gross_weight, $str_measurement, $total_carton_CBM, $is_last, $SKU, $str_length, $str_width, $str_height);
		}
		$grand_total_qty += $balance_qty;
		
		//convert to one decimal (2018-10-05 w) 
		$this_netnet_weight = round($this_netnet_weight, 3); //$this->to_one_dec($this_netnet_weight);
		$this_net_weight    = round($this_net_weight, 3); //$this->to_one_dec($this_net_weight);
		$this_gross_weight  = round($this_gross_weight, 3); //$this->to_one_dec($this_gross_weight);
		
		$actual_total_carton = 1;
		$display_nnw = $this_netnet_weight * $actual_total_carton;
		$display_nw = $this_net_weight * $actual_total_carton;
		$display_gw = $this_gross_weight * $actual_total_carton;
		
		$grand_nnw += $display_nnw;
		$grand_nw += $display_nw;
		$grand_gw += $display_gw;
		
		$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_prepack </td>";//--- Prepack ---//
		$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$blisterbag_in_carton $lbl_blisterbaginfo </td>";//--- Prepack Per Carton ---//
		$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$balance_qty</td>";//--- Carton Qty ---//
		$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$balance_qty</td>";//--- Total Qty ---//
		$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_nnw $str_unit
												<br/><small><i><font color='blue'>$this_netnet_weight $str_unit/ per ctn</font></i></small></td>";//--- Net Net Weight ---//
		$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_nw $str_unit
						<br/><small><i><font color='blue'>$this_net_weight $str_unit/ per ctn</font></i></small></td>";//--- New Weight ---//
		$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_gw $str_unit
						<br/><small><i><font color='blue'>$this_gross_weight $str_unit/ per ctn</font></i></small></td>";//--- Gross Weight ---//
		$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$str_measurement</td>";//--- Carton Measurement ---//
		$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_carton_CBM</td>";//--- Total CBM ---//
		$html .= "</tr>";
		
		$start_carton+=1;
	}
	
	return array($html, $start_carton, $grand_total_qty, $grand_total_cbm, $grand_nnw, $grand_nw, $grand_gw);
}

//---- Not use function ----//
public function backup_funcPackingCartonDisplayFormula_SingleColorMultiSize($PID, $soID, $shipmentpriceID, $start_carton, $color_type, 
																				$is_polybag, $is_blisterbag, $tmode, $is_gap, $is_pdf){
	$lang = $this->lang;
	include("../../lang/{$lang}.php");
	$html = "";
	$pack_method = 1;
	$chk_polybag = ($is_polybag==1?"checked":"");
	$chk_blisterbag = ($is_blisterbag==1?"checked":"");
	$str_standard = $this->checkStandardName($tmode);
	$btn_carton_calculator = $this->funcLinkToCalculator($soID, $PID, $pack_method, $tmode, $packing_type);
	$str_PID = " &nbsp; <em>PID: <b><u>$PID</u></b></em> &nbsp; &nbsp;  $btn_carton_calculator &nbsp; ";
	
	
	$columnsql2 = $this->conn->prepare("select spd.*, spk.tmode, sp.balpack 
										from tblship_packing_detail spd
										inner join tblship_packing spk ON spk.PID = spd.PID
										inner join tblshipmentprice sp ON sp.ID = spk.shipmentpriceID
										where spd.PID = '$PID' AND spd.statusID='1' 
										group by spd.group_number ORDER BY spd.ID asc");
	$columnsql2->execute();	
	
	$html .= "<br/>
			<b class='subTitle'>".$hdlang["packing_method"].": ".$hdlang["SCRP"]."</b> &nbsp; $str_PID<br/>"; //-- Packing Method --// //-- Single Color (Carton) - Prepack By Single Color Multiple Size --// 
	$html .= "<input type='checkbox' $chk_polybag class='icheckbox_flat-blue' disabled />Poly Bag &nbsp; &nbsp; ";
	$html .= "<input type='checkbox' $chk_blisterbag class='icheckbox_flat-blue' disabled />Blister Bag &nbsp; &nbsp; ";
	$html .= " <b><u>$str_standard</u></b><br/>";
	$html .= '<table class="tb_detail pick_list" id="tb_detail" cellspacing="0" border="1" cellpadding="3" >';
	$html .= '<tr class="titlebar">';
		$html .= '<th class="titlecol" rowspan="2" style="width:80px;min-width:80px">'.$hdlang["carton_no"].'</th>';//-- Carton No --//
		$html .= '<th class="titlecol" rowspan="2" style="width:60px;min-width:60px">'.$hdlang["total_carton"].'</th>';//-- Total Carton --//
		$html .= '<th class="titlecol" rowspan="2">'.$hdlang["pc_per_pack"].'#</th>';//-- Prepack --//
		$html .= '<th class="titlecol" rowspan="2">'.$hdlang["Color"].'</th>';//-- Color --//
		
		$num_column = $this->getSizeNameColumnFromOrder($soID, "0");
		$pack_method = 1;
		list($str_1st, $str_2nd, $str_3rd, $str_4th) = $this->chkTickedOfPolyBlisterBag($pack_method, $is_polybag, $is_blisterbag);
		$html .= '<th class="titlecol" colspan="'.$num_column.'">'.$hdlang["Size"].'</th>';//-- Size --//
		$html .= '<th class="titlecol" style="white-space:nowrap;width:100px;min-width:100px" rowspan="2">'.$str_1st.'</th>';//-- Prepack --//
		$html .= '<th class="titlecol" style="white-space:nowrap;width:100px;min-width:100px" rowspan="2">'.$str_2nd.'</th>';//-- Prepack Per Carton --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Total Qty <br/>in 1 Carton</th>';//-- Total Qty in 1 Carton --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Total Qty</th>';//-- Total Qty --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Net Net Weight</th>';//-- Net Net Weight --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Net Weight</th>';//-- Net Weight --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Gross Weight</th>';//-- Gross Weight --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Carton Measurement <br/>(L*W*H)</th>';//-- Carton Measurement --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Total CBM</th>';//-- Total CBM --//
	$html .= "</tr>";
	
	//---- Display Size Range of order ----//
	$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
	$html .= "<tr class='titlebar'>";
	for($c=0;$c<$num_column;$c++){
		$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
		$size = $columntitle2['SizeName'];
		
		$html .= "<th class='titlecol' style='width:70px;min-width:70px;background-color:#fff'>$size</th>";
	}
	$html .= "</tr>";
	
	while($row=$columnsql2->fetch(PDO::FETCH_ASSOC)){
		$group_number = $row["group_number"];
		$polybag_qty_in_blisterbag = $row["polybag_qty_in_blisterbag"];
		$prepack_qty = $row["polybag_qty_in_blisterbag"];
		$blisterbag_in_carton = $row["blisterbag_in_carton"];
		$total_qty = $row["total_qty"];
		$tmode = $row["tmode"];
		$is_standard = $row["tmode"];
		$balpack = $row["balpack"];
		$is_decimal = 0;
		$size_name = "color";
		
		if($total_qty>0){
			$total_qty_in_carton = $polybag_qty_in_blisterbag * $blisterbag_in_carton;
			$total_carton = $total_qty / $total_qty_in_carton;
			//$total_carton = ceil($total_carton); //--- Temp ---//
			$is_decimal = ($total_qty%$total_qty_in_carton);
			$end_carton = $start_carton + floor($total_carton) - 1;
			$actual_total_carton = $end_carton - $start_carton + 1;
			$str_color_ID = $this->getComboOrSingleColorNameDisplay($shipmentpriceID, $group_number, $color_type, "1");
			$str_carton_display = "$start_carton - $end_carton";
			
			//----- Check From Pack Factor Qty -----//
			$sql_factor = "SELECT amount, kg, amount_2, amount_3, amount_4 as L, amount_5 as W, amount_6 as H, amount_7 as CBM 
							FROM tblpackfactor 
							WHERE orderno='$soID' AND colorID = '$str_color_ID' AND size='$size_name' AND del='0' AND tmode='$tmode'";
			$result_factor = $this->conn->prepare($sql_factor);
			$result_factor->execute();
			$row_factor = $result_factor->fetch(PDO::FETCH_ASSOC);
				//$this_factor = $row_factor["amount"];
				$this_kg = $row_factor["kg"];
				$this_accweight = $row_factor["amount_2"]; //--- Acc Weight ---//
				$this_cartonweight = $row_factor["amount_3"]; //-- Carton Weight ---//
				$this_length = $row_factor["L"];
				$this_width = $row_factor["W"];
				$this_height = $row_factor["H"];
				$this_CBM = $row_factor["CBM"];
				$str_measurement = "$this_length x $this_width x $this_height";
				//$total_carton_CBM = $this_CBM * $actual_total_carton;
				$this_size="";
				$this_factor = $this->funcGetPackFactor($soID, $PID, $pack_method, $this_size, $prepack_qty, $is_standard, $packing_type);
				$is_gap = (($is_standard==1)? 0:$tmode);
				$is_gap = (($is_standard==0)? 1:$is_gap);
				
			$str_extra = ($this_factor==0? "<br/><font color='red'>No Pack Factor</font>":"");
			if($actual_total_carton==0 || $is_decimal>0){ //$total_qty_in_carton!=$this_factor || 
				$actual_total_carton = 0;
				$str_carton_display = "Cannot Pack";
			}
			
			$prepack_name = $this->checkCartonName($is_gap, $this_factor, $total_qty_in_carton, $balpack);
			
			$html .= "<tr>";
			$html .= "<td class='topcolortd leftcolortd'>$str_carton_display $str_extra</td>";//[$total_qty / $total_carton]
			$html .= "<td class='topcolortd leftcolortd'>$actual_total_carton</td>";//--- Total Carton ---//
			$html .= "<td class='topcolortd leftcolortd'>$prepack_name</td>";//--- Prepack# ---//
			$str_color = $this->getComboOrSingleColorNameDisplay($shipmentpriceID, $group_number, $color_type, "0");
			$html .= "<td class='topcolortd leftcolortd'>$str_color</td>";//--- Color ---//
			
			$total_prepack = 0;
			$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				
				//--- Get Ratio Qty by size ---//
				$sql = "SELECT spd.ratio_qty FROM tblship_packing_detail spd WHERE spd.PID='$PID' AND spd.group_number='$group_number' AND size_name='$size' AND statusID='1'";
				$result_ratio = $this->conn->prepare($sql);
				$result_ratio->execute();	
					$row_ratio = $result_ratio->fetch(PDO::FETCH_ASSOC);
					$ratio_qty = $row_ratio["ratio_qty"];
				
				$html .= "<td class='topcolortd leftcolortd'>$ratio_qty</td>";
				$total_prepack += $ratio_qty;
				
				//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
				//====> Able Pack Carton, record size detail carton in database <====//
				//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
				if($str_carton_display!="Cannot Pack" && $is_pdf==false){
					$this->update_carton_num_detail($start_carton, $end_carton, $shipmentpriceID, $PID, $size, $group_number, $ratio_qty);
				}
			}//---- End For Loop ----//
			
			$this_total_qty = $total_qty_in_carton * $actual_total_carton;
			$this_total_qty = ($actual_total_carton==0? $total_qty_in_carton: $this_total_qty);
			
			// $this_netnet_weight = $this_kg * $this_total_qty;
			// $this_net_weight = $this_kg + $this_accweight;
			// $this_gross_weight = $this_kg + $this_accweight + ($this_cartonweight * $actual_total_carton);
			
			//Modified by ckwai on 2018-07-25
			$size_name = ""; $arr_size_qty = array();
			list($str_measurement, $ext_CBM, $pack_factor, $ctn_weight, $gmt_pcs_weight, $str_length, $str_width, $str_height) = 
																$this->funcGetCartonMeasurement($soID, $PID, $pack_method, $size_name, 
																						$total_prepack, $is_standard, $prepack_name,0, $packing_type, $arr_size_qty);
	
			$total_carton_CBM = $ext_CBM * $actual_total_carton;
			$acc_weight = $this->funcGetAccWeight($soID);
			
			$this_netnet_weight = $this_total_qty * $gmt_pcs_weight;
			$this_net_weight = $this_netnet_weight + $acc_weight;
			$this_gross_weight = $ctn_weight + $this_net_weight;
			
			//>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<//
			//====> Able Pack Carton, record in database <====//
			//>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<//
			if($str_carton_display!="Cannot Pack" && $is_pdf==false){
				$qty_in_blisterbag = $total_prepack;
				$this_blisterbag = $blisterbag_in_carton;
				$is_last = 0;
				$this->update_carton_num_head($start_carton, $end_carton, $shipmentpriceID, $PID, $group_number,
										$qty_in_blisterbag, $this_blisterbag, $total_qty_in_carton, $this_netnet_weight, $this_net_weight,
										$this_gross_weight, $str_measurement, $total_carton_CBM, $is_last, $prepack_name, $str_length, $str_width, $str_height);
										
			}
			$start_carton += $actual_total_carton;
			
			//convert to LBS if is 41 (2018-10-05 w)
			$unit = $this->check_kg_lbs($soID, $PID, $is_standard, $packing_type);
			$wg_unit  = $this->wg_unit;
			if($unit == 41){ // always use KGS request by Boss AA on 2020-11-13 morning shipping meeting
				// $this_netnet_weight = $this_netnet_weight * 2.204622622;
				// $this_net_weight = $this_net_weight * 2.204622622;
				// $this_gross_weight = $this_gross_weight * 2.204622622;
			}
			//convert to one decimal (2018-10-05 w)
			$this_netnet_weight = $this->to_one_dec($this_netnet_weight);
			$this_net_weight = $this->to_one_dec($this_net_weight);
			$this_gross_weight = $this->to_one_dec($this_gross_weight);
			
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_prepack</td>";//--- Prepack ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$blisterbag_in_carton</td>";//--- Prepack Per Carton ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_qty_in_carton</td>";//--- Carton Qty ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$this_total_qty</td>";//--- Total Qty ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$this_netnet_weight</td>";//--- Net Net Weight ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$this_net_weight</td>";//--- New Weight ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$this_gross_weight</td>";//--- Gross Weight ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$str_measurement</td>";//--- Carton Measurement ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_carton_CBM</td>";//--- Total CBM ---//
			$html .= "</tr>";
			
			//$html .= "[$group_number] $polybag_qty_in_blisterbag - $blisterbag_in_carton - $total_qty <br/>";
		}
	}//--- End While ---//
	
	$html .= "</table><br/><br/>";
	return array($html, $start_carton);
}


public function funcPackingCartonDisplayFormula_SingleColorSingleSize($PID, $soID, $shipmentpriceID, $start_carton, $color_type, 
														$is_polybag, $is_blisterbag, $is_ctnblister, $is_gap, $balpack, $tmode, $is_pdf, $packing_type, $ship_remark, $order_by_color=0){
	$lang = $this->lang;
	$from_location = $this->from_location;
	
	$packing_method = 2;
	$lbl_polybaginfo = ($is_polybag==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "1"): ""); // polybag echo
	$lbl_blisterbaginfo = ($is_blisterbag==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "2"): ""); // blisterbag
	$lbl_ctnblisterbaginfo = ($is_ctnblister==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "3"): ""); // ctn blisterbag	
	
	$path = "../lang/{$lang}.php";
	$path2 = "../../lang/{$lang}.php";
	$chk = file_exists($path);
	$url = ($chk==1? $path: $path2);
	
	include($url);
	$html = "";
	$pack_method = 2;
	$arr_colorsize = array(); $arr_last = array();
	$str_polybag = ($is_polybag==1?"<b><u>Poly Bag Required</u><b> &nbsp; &nbsp; ":"");
	$str_blisterbag = ($is_blisterbag==1?"<b><u>Blister Bag Required</u></b> &nbsp; &nbsp; ":"");
	$str_ctn_blister = ($is_ctnblister==1?"<b><u>Carton Blister Required</u></b> &nbsp; &nbsp; ":"");
	$count = 0; $grand_total_qty = 0; $grand_total_cbm=0; $grand_nnw=0; $grand_nw=0; $grand_gw=0;
	$str_standard = $this->checkStandardName($tmode);
	$btn_carton_calculator = ($is_pdf==false? "": $this->funcLinkToCalculator($soID, $PID, $pack_method, $tmode, $packing_type));
	$str_PID = " &nbsp; <em>PID: <b><u>$PID</u></b></em> &nbsp; &nbsp;  $btn_carton_calculator &nbsp;";
	
	// $sql = "select spd.*, spk.tmode, spk.last_ctn_by_SCSS, spk.last_ctn_num_size 
										// from tblship_packing_detail$from_location spd
										// inner join tblship_packing$from_location spk ON spk.PID = spd.PID
										// INNER JOIN tblcolorsizeqty csq ON csq.SizeName = spd.size_name
										// where spd.PID = '$PID' AND spd.statusID='1' 
										// group by spd.group_number, spd.size_name, spd.ID  
										// ORDER BY spd.group_number, csq.ID asc";
	if($order_by_color==1){//52378
		$columnsql2 = $this->conn->prepare("select spd.*, spk.tmode, spk.last_ctn_by_SCSS, spk.last_ctn_num_size, (SELECT sgc.colorID FROM tblship_group_color sgc WHERE sgc.shipmentpriceID = spk.shipmentpriceID AND sgc.statusID=1 AND sgc.group_number = spd.group_number limit 1) as colorID,
										(SELECT sgc.garmentID FROM tblship_group_color sgc WHERE sgc.shipmentpriceID = spk.shipmentpriceID AND sgc.statusID=1 AND sgc.group_number = spd.group_number limit 1) as garmentID, od.buyerID, b.is_size_seq
										
											from tblship_packing_detail spd
											inner join tblship_packing spk ON spk.PID = spd.PID
											inner join tblshipmentprice sp ON sp.ID = spk.shipmentpriceID
                                            inner join tblorder od ON od.Orderno = sp.Orderno
                                            inner join tblbuyer b ON b.BuyerID = od.buyerID
											INNER JOIN tblcolorsizeqty csq ON csq.SizeName = spd.size_name AND csq.orderno = '$soID'
											where spd.PID = '$PID' AND spd.statusID='1' AND spd.total_qty>0
											group by spd.group_number, spd.size_name, spd.ID  
											ORDER BY colorID, garmentID, csq.ID asc");
	}
	else{
		$columnsql2 = $this->conn->prepare("select spd.*, spk.tmode, spk.last_ctn_by_SCSS, spk.last_ctn_num_size, od.buyerID, b.is_size_seq 
										from tblship_packing_detail$from_location spd
										inner join tblship_packing$from_location spk ON spk.PID = spd.PID
										inner join tblshipmentprice sp ON sp.ID = spk.shipmentpriceID
                                        inner join tblorder od ON od.Orderno = sp.Orderno
                                        inner join tblbuyer b ON b.BuyerID = od.buyerID
										INNER JOIN tblcolorsizeqty csq ON csq.SizeName = spd.size_name AND csq.orderno = '$soID'
										where spd.PID = '$PID' AND spd.statusID='1' AND spd.total_qty>0
										group by spd.group_number, spd.size_name, spd.ID  
										ORDER BY spd.group_number, csq.ID asc");
	}
	$columnsql2->execute();	
	
	$html .= "<br/>
			<b class='subTitle'>".$hdlang["packing_method"].": ".$hdlang["SCSS"]."</b>";
	$html .= "&nbsp;<span class='glyphicon glyphicon-list-alt btntop editcode' data-toggle='modal'
							data-target='#methodbox' data-id='$PID' data-backdrop='static' data-keyboard='false' title='".$hdlang["ship_12"]."' 
							style='display: inline-block;cursor:pointer'></span> 
							&nbsp;<span style='padding:3px;background-color:red;color:#fff;border-radius:5px'><b>".$this->count_attach."</b></span>&nbsp; "; //--- Packing Method Attachment ---//

	$html .= "$str_PID<br/>"; //-- Packing Method --// //-- Single Color (Carton) - Prepack Single Size --// 
	$html .= "$str_polybag  ";
	$html .= "$str_blisterbag ";
	$html .= "$str_ctn_blister ";
	$html .= " <b><u>$str_standard</u></b> &nbsp; &nbsp; ";
	$html .= $this->getStrPackingType($packing_type);
	//remark field (2018-10-25 w)
	if($ship_remark != ""){
		$html .= "<b>Remark:</b> $ship_remark";		
	}
	$html .= "<br/>";
	
	$ctn_width = ($this->pdf_display==1? "30px": "70px");
	
	$html .= '<table class="tb_detail pick_list" id="tb_detail" cellspacing="0" cellpadding="3" border="1">';
	$html .= '<tr class="titlebar2">';
		$html .= '<th class="titlecol" rowspan="2" style="width:'.$ctn_width.';min-width:'.$ctn_width.'">'.$hdlang["carton_no"].'</th>';//-- Carton No --//
		$html .= '<th class="titlecol" rowspan="2" style="width:50px;min-width:50px;white-space:nowrap">'.$hdlang["total_carton"].'</th>';//-- Total Carton --//
		$html .= '<th class="titlecol" rowspan="2" style="width:50px;min-width:50px">'.$hdlang["pc_per_pack"].'#</th>';//-- Prepack --//
		$html .= '<th class="titlecol" rowspan="2">'.$hdlang["Color"].'</th>';//-- Color --//
		
		$num_column = $this->getSizeNameColumnFromOrder($soID, "0");
		$wd_size = $num_column * 25;
		$pack_method = 2;
		list($str_1st, $str_2nd, $str_3rd, $str_4th) = $this->chkTickedOfPolyBlisterBag($pack_method, $is_polybag, $is_blisterbag);
		
		$html .= '<th class="titlecol" colspan="'.$num_column.'" style="width:'.$wd_size.'px;min-width:'.$wd_size.'px;">'.$hdlang["Size"].'</th>';//-- Size --//
		$html .= '<th class="titlecol" style="white-space:nowrap;width:40px;min-width:40px" rowspan="2">'.$str_1st.'</th>';//-- Prepack --//
		$html .= '<th class="titlecol" style="white-space:nowrap;width:40px;min-width:40px" rowspan="2">'.$str_2nd.'</th>';//-- Prepack Per Carton --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Total Qty <br/>in 1 Carton</th>';//-- Total Qty in 1 Carton --//
		$html .= '<th class="titlecol" style="white-space:nowrap;width:30px;min-width:30px" rowspan="2">'.$hdlang["total_set"].'</th>';//-- Total Qty --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Net Net Weight (total ctn)</th>';//-- Net Net Weight --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Net Weight (total ctn)</th>';//-- Net Weight --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Gross Weight (total ctn)</th>';//-- Gross Weight --//
		$html .= '<th class="titlecol" style="white-space:nowrap;width:55px;min-width:55px" rowspan="2">Carton Measurement <br/>(L*W*H)</th>';//-- Carton Measurement --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Total CBM</th>';//-- Total CBM --//
		
	$html .= "</tr>";
	
	//---- Display Size Range of order ----//
	$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
	$html .= "<tr class='titlebar2'>";
	for($c=0;$c<$num_column;$c++){
		$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
		$size = $columntitle2['SizeName'];
		
		$html .= "<th class='titlecol2' style='while-space:nowrap;width:25px;min-width:25px;'>$size</th>";
	}
	$html .= "</tr>";
	
	//---- Load Each Size Row Data ----//
	$cannot_pack_count=0; $is_size_seq = 0;
	$temp_last_group_number = -1;
	if($this->acctid==1){
		//echo "$sql";
	}
	while($row=$columnsql2->fetch(PDO::FETCH_ASSOC)){
		$group_number = $row["group_number"];
		$this_size = $row["size_name"];
		$gmt_qty_in_polybag = $row["gmt_qty_in_polybag"];
		$polybag_qty_in_blisterbag = $row["polybag_qty_in_blisterbag"];
		$blisterbag_in_carton = $row["blisterbag_in_carton"];
		$total_qty = $row["total_qty"];
		$tmode = $row["tmode"];
		$is_standard = $row["tmode"];
		// $tmode = ($this->acctid==1? 0: $tmode);
		// $is_standard = ($this->acctid==1? 0: $is_standard);
		
		$last_ctn_by_SCSS = $row["last_ctn_by_SCSS"];
		$last_ctn_num_size = $row["last_ctn_num_size"];
		$buyerID = $row["buyerID"];
		$is_size_seq = $row["is_size_seq"];
		$is_decimal = 0;
		$SKU = $row["SKU"];
		$master_upc = $row["master_upc"];
		$case_upc = $row["case_upc"];
		
		// echo "$last_ctn_by_SCSS << ";
		
		if($SKU=="" && $from_location!=""){//if SKU empty, auto link from buyer PO on 2021-12-06 by ckwai
			$sqlsku = "SELECT spd.SKU, spd.master_upc, spd.case_upc
						FROM tblship_packing_detail spd
						WHERE spd.PID='$PID' AND spd.statusID=1 AND spd.group_number='$group_number' AND spd.size_name='$this_size' AND spd.SKU!=''";
			$stmtsku = $this->conn->prepare($sqlsku);
			$stmtsku->execute();	
			$rowsku = $stmtsku->fetch(PDO::FETCH_ASSOC);
			$SKU = $rowsku["SKU"];
			$master_upc = $rowsku["master_upc"];
			$case_upc = $rowsku["case_upc"];
		}
		$display_MU_CU="";
		if($master_upc<>"" || $case_upc<>""){
			$display_MU_CU = "<br>MU:$master_upc<br>CU:$case_upc";
		}
		
		$ori_SKU = $SKU;
		if($SKU==""){
			$sqlupc = "SELECT sud.upc_code
						FROM `tblship_upc` su 
						INNER JOIN tblship_upc_detail sud ON sud.SUID = su.SUID
						INNER JOIN tblship_group_color sgc ON sgc.shipmentpriceID = su.shipmentpriceID 
															AND sgc.garmentID = sud.garmentID 
															AND sgc.colorID = sud.colorID
						WHERE su.statusID=1 AND sud.statusID=1 AND sgc.statusID = 1 AND sud.upc_code!='' 
						AND sgc.group_number='$group_number' 
						AND sgc.shipmentpriceID='$shipmentpriceID' AND sud.size_name='$this_size'";
			$stmtupc = $this->conn->prepare($sqlupc);
			$stmtupc->execute();	
			$rowupc = $stmtupc->fetch(PDO::FETCH_ASSOC);
			$SKU = $rowupc["upc_code"];
		}
		
		if($this->acctid==3){
			//echo "$group_number // $this_size <br/>";
		}
		
		// set the temp_last group number
		if($temp_last_group_number == -1){
			$temp_last_group_number = $group_number;
		}

		if($temp_last_group_number != $group_number){
			//echo "".count($arr_colorsize)." <--- [$group_number] <br/>";
			//if($group_number==3){
				//print_r($arr_colorsize);
			//}
			
			if(count($arr_colorsize)>0){
				list($this_html, $start_carton, $grand_total_qty, $grand_total_cbm, $grand_nnw, $grand_nw, $grand_gw) = 
								$this->checkBalanceQtyInCarton($PID, $arr_colorsize, $soID, $shipmentpriceID, $color_type, $num_column, 
														$start_carton, $is_gap, $balpack, $grand_total_qty, $is_standard, $last_ctn_by_SCSS, 
														$last_ctn_num_size, $grand_total_cbm, $is_pdf, $packing_type, 
														$grand_nnw, $grand_nw, $grand_gw, $is_polybag, $is_blisterbag, $is_ctnblister, 
														$order_by_color);
				$html .= $this_html;

				$arr_colorsize = [];
			}


			$temp_last_group_number = $group_number;
		}
		
		if($total_qty>0){
			$total_qty_in_carton = $gmt_qty_in_polybag * $polybag_qty_in_blisterbag * $blisterbag_in_carton;
			if($total_qty_in_carton==0){
				$total_carton=0;
				$is_decimal=0;
			}else{
				$total_carton = $total_qty / $total_qty_in_carton;
				$is_decimal = ($total_qty%$total_qty_in_carton);
				
				//echo "$total_qty % $total_qty_in_carton <  - $this_size // $is_decimal <br/>";
			}
			$end_carton = $start_carton + floor($total_carton) - 1;
			$actual_total_carton = $end_carton - $start_carton + 1;
			$str_color_ID = $this->getComboOrSingleColorNameDisplay($shipmentpriceID, $group_number, $color_type, "1");
			$str_carton_display = "$start_carton - $end_carton";
			
			//----- Check From Pack Factor Qty -----//
			$sql_factor = "SELECT amount, kg, amount_2, amount_3, amount_4 as L, amount_5 as W, amount_6 as H, amount_7 as CBM 
							FROM tblpackfactor WHERE orderno='$soID' AND colorID = '$str_color_ID' AND size='$this_size' AND del='0' AND tmode='$tmode'";
			$result_factor = $this->conn->prepare($sql_factor);
			$result_factor->execute();
			$row_factor = $result_factor->fetch(PDO::FETCH_ASSOC);
				//$this_factor = $row_factor["amount"];
				$this_kg = $row_factor["kg"];
				$this_accweight = $row_factor["amount_2"];
				$this_cartonweight = $row_factor["amount_3"];
				$this_length = $row_factor["L"];
				$this_width = $row_factor["W"];
				$this_height = $row_factor["H"];
				$this_CBM = $row_factor["CBM"];
				$str_measurement = "$this_length x $this_width x $this_height";
				//$total_carton_CBM = $this_CBM * $actual_total_carton;
				
			$prepack_qty="";
			$is_gap = (($is_standard==1)? 0:$tmode);
			$is_gap = (($is_standard==0 && $buyerID=="B12")? 1:$is_gap);
			$this_factor = $this->funcGetPackFactor($soID, $PID, $pack_method, $this_size, $prepack_qty, $is_standard, $packing_type);
			$str_extra = ($this_factor==0? "<br/><font color='red'>No Pack Factor </font>":"");
			$str_extra = ($this_factor>0 && $total_qty_in_carton!=$this_factor? "<br/><font color='red'>Pack factor not match</font>":"$str_extra");

			//---- If (total qty in carton) not equal (pack factor) ----//
			if($total_qty_in_carton!=$this_factor || $actual_total_carton==0){
				$actual_total_carton = 0;
				$str_carton_display = "Cannot Pack";
				$prepack_name = "";
			}
			else if($is_decimal>0){
				$balance_qty = $total_qty - ($total_qty_in_carton * $actual_total_carton);
				$arr_colorsize["$group_number=^$this_size=^$this_factor=^$SKU"] = $balance_qty;
				$prepack_name = $this->checkCartonName($is_gap, $this_factor, $total_qty_in_carton, $balpack);
				//echo "$this_size | $balance_qty <br/>";
				// echo "A: [$SKU] $this_size / $PID | Balance: $balance_qty<< <br/>";
			}
			else{
				$prepack_name = $this->checkCartonName($is_gap, $this_factor, $total_qty_in_carton, $balpack);
			}
			
			// echo "<br/>$this_factor << ";
			//-------- If Qty smaller than pack factor --------//
			if($total_qty<$this_factor){
				$balance_qty = $total_qty;
				$balpack = 0;
				$this_packing_method = 2;
				list($carton_qty, $this_qty_in_carton, $this_balance_qty, $str_pack_name) = 
											$this->checkBalanceCartonQty($is_gap, $this_factor, $balance_qty, $balpack, "first",
																		$shipmentpriceID, $is_standard, $PID, $packing_type, 
																		$this_packing_method, $this_size);
				$prepack_name = $str_pack_name;
				$actual_total_carton = $carton_qty;
				$end_carton = $start_carton + floor($actual_total_carton) - 1;
				$str_carton_display = ($start_carton>$end_carton ? "Cannot Pack": "$start_carton - $end_carton");//-- 2018-08-09 --//
				
				if($this_balance_qty>0){
					$arr_colorsize["$group_number=^$this_size=^$this_factor=^$SKU"] = $this_balance_qty;
				}
				
				// echo "B: $total_qty < $this_factor / $this_size / $PID << <br/>";
				
			}
			//echo "$total_qty % $total_qty_in_carton < $this_factor - $this_size // $is_decimal <br/>";
			$css_highlight = ($str_carton_display=="Cannot Pack"  && $is_pdf==false? "style='color:#bdbdbd;'":"");
			$cannot_pack_count = ($str_carton_display=="Cannot Pack"? ++$cannot_pack_count : $cannot_pack_count);
			
			// echo "$str_carton_display / $start_carton>$end_carton / $actual_total_carton [$carton_qty] << this_factor: $total_qty/$this_factor<br/>";
			
			if($str_carton_display!="Cannot Pack" || $is_pdf==false){
			
				$html .= "<tr $css_highlight>";
				$html .= "<td class='topcolortd leftcolortd'>$str_carton_display $str_extra - </td>";
				$html .= "<td class='topcolortd leftcolortd'>$actual_total_carton <br>$prepack_name $lbl_ctnblisterbaginfo</td>";//--- Total Carton ---//
				$html .= "<td class='topcolortd leftcolortd'>$SKU $display_MU_CU</td>";//--- Prepack# ---// / $group_number / $this_size / $ori_SKU
				$str_color = $this->getComboOrSingleColorNameDisplay($shipmentpriceID, $group_number, $color_type, "0");
				$html .= "<td class='topcolortd leftcolortd'>$str_color [$group_number]</td>";//--- Color ---//
				
				$total_prepack = 0; $this_blisterbag = 0;
				$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
				$this_gmt_qty_in_polybag = 0;
				$this_polybag_qty_in_blisterbag = 0;
				for($c=0;$c<$num_column;$c++){
					$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
					$size = $columntitle2['SizeName'];
					
					//--- Get Ratio Qty by size ---//
					$sql = "SELECT spd.gmt_qty_in_polybag, spd.polybag_qty_in_blisterbag, spd.blisterbag_in_carton 
							FROM tblship_packing_detail$from_location spd WHERE spd.PID='$PID' AND spd.group_number='$group_number' AND size_name='$size' AND statusID='1'";
					$result_ratio = $this->conn->prepare($sql);
					$result_ratio->execute();	
						$row_ratio = $result_ratio->fetch(PDO::FETCH_ASSOC);
						$gmt_qty_in_polybag = $row_ratio["gmt_qty_in_polybag"];
						$polybag_qty_in_blisterbag = $row_ratio["polybag_qty_in_blisterbag"];
						$blisterbag_in_carton = $row_ratio["blisterbag_in_carton"];
						$size_qty_prepack = $gmt_qty_in_polybag * $polybag_qty_in_blisterbag;// * $blisterbag_in_carton;
						$size_qty = $gmt_qty_in_polybag * $polybag_qty_in_blisterbag * $blisterbag_in_carton;
						
					if($size==$this_size){
						$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$size_qty</td>";
						$total_prepack += $size_qty_prepack;
						$this_blisterbag = $blisterbag_in_carton;
						$this_gmt_qty_in_polybag = $gmt_qty_in_polybag;
						$this_polybag_qty_in_blisterbag = $polybag_qty_in_blisterbag;
					}else{
						$size_qty = 0;
						$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'></td>";
					}
					
					//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
					//====> Able Pack Carton, record size detail carton in database <====//
					//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
					if($str_carton_display!="Cannot Pack" && $size_qty>0 && $is_pdf==false){
						$this->update_carton_num_detail($start_carton, $end_carton, $shipmentpriceID, $PID, $size, $group_number, $size_qty);
					}
					
				}//---- End For Loop ----//
				
				$this_total_qty = $total_qty_in_carton * $actual_total_carton;
				$this_total_qty = ($actual_total_carton==0? $total_qty_in_carton: $this_total_qty);
				//$this_total_qty = (($total_qty<$this_factor && $total_qty>0)? $total_qty: $this_total_qty);//-- 2018-08-09 --//
				$this_total_qty = ($str_carton_display=="Cannot Pack" ? 0 : $this_total_qty);
				$total_qty_in_carton = ($str_carton_display=="Cannot Pack"? 0: $total_qty_in_carton);
				
				// $this_netnet_weight = $this_kg * $this_total_qty;
				// $this_net_weight = $this_kg + $this_accweight;
				// $this_gross_weight = $this_kg + $this_accweight + ($this_cartonweight * $actual_total_carton);
				$grand_total_qty += $this_total_qty;
				
				//Modified by ckwai on 2018-07-25
				$this_total_prepack = ""; 
				$arr_size_qty = array();
				$this_total_pack_qty = ($is_standard==0 && $this_factor>0? $this_factor: "0"); 
				list($str_measurement, $ext_CBM, $pack_factor, $ctn_weight, $gmt_pcs_weight,  $str_length, $str_width, $str_height) = 
							$this->funcGetCartonMeasurement($soID, $PID, $pack_method, $this_size, $this_total_prepack, 
																$is_standard, $prepack_name,$this_total_pack_qty, $packing_type, $arr_size_qty);
				//$total_carton_CBM = $ext_CBM * $actual_total_carton;
				//$total_carton_CBM = ($str_length/100) * ($str_width/100) * ($str_height/100) * $actual_total_carton;// modified by ckwai on 202007021138
				$single_carton_CBM = round(($str_length/100) * ($str_width/100) * ($str_height/100), 3);
				$total_carton_CBM = round($single_carton_CBM * $actual_total_carton, 3);
				
				
				$acc_weight = $this->funcGetAccWeight($soID);
				//$this_netnet_weight = $this_total_qty * $gmt_pcs_weight;
				$this_netnet_weight = $total_qty_in_carton * $gmt_pcs_weight;
				$this_net_weight = $this_netnet_weight + $acc_weight;
				$this_gross_weight = $ctn_weight + $this_net_weight;
				$css_acc_wgt = "blue";
				
				if($str_carton_display=="Cannot Pack"){
					$str_measurement = "0 x 0 x 0";
					$this_gross_weight = 0;
					$this_net_weight = 0;
					$this_netnet_weight = 0;
					$css_acc_wgt = "grey";
				}
				
				//>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<//
				//====> Able Pack Carton, record in database <====//
				//>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<//
				if($str_carton_display!="Cannot Pack" && $is_pdf==false){
					$qty_in_blisterbag = $total_prepack;
					$is_last = 0;
					
					$this->update_carton_num_head($start_carton, $end_carton, $shipmentpriceID, $PID, $group_number,
										$qty_in_blisterbag, $this_blisterbag, $total_qty_in_carton, $this_netnet_weight, $this_net_weight,
										$this_gross_weight, $str_measurement, $total_carton_CBM, $is_last, $SKU, $str_length, $str_width, $str_height);
				}
				$start_carton += $actual_total_carton;
				$grand_total_cbm += $total_carton_CBM;
				
				//convert to LBS if is 41 (2018-10-05 w)
				$unit = $this->check_kg_lbs($soID, $PID, $is_standard, $packing_type);
				$wg_unit  = $this->wg_unit;
				$str_unit = "KG";
				if(($unit == 41 && $wg_unit==0) || $wg_unit==57){ //inch use lbs and cm use KGS due to Joe Fresh Order needed on 2020-11-17
					$this_netnet_weight = $this_netnet_weight * 2.204622622;
					$this_net_weight = $this_net_weight * 2.204622622;
					$this_gross_weight = $this_gross_weight * 2.204622622;
					$acc_weight = round($acc_weight * 2.204622622, 5);
					$str_unit = "LBS";
				}
				
				$str_acc_weight = number_format($acc_weight, 5);
				//convert to one decimal (2018-10-05 w)
				$this_netnet_weight = round($this_netnet_weight, 3); //$this->to_one_dec($this_netnet_weight);
				$this_net_weight    = round($this_net_weight, 3); //$this->to_one_dec($this_net_weight);
				$this_gross_weight  = round($this_gross_weight, 3); //$this->to_one_dec($this_gross_weight);
				
				$display_nnw = $this_netnet_weight * $actual_total_carton;
				$display_nw = $this_net_weight * $actual_total_carton;
				$display_gw = $this_gross_weight * $actual_total_carton;
				
				$grand_nnw += $display_nnw;
				$grand_nw += $display_nw;
				$grand_gw += $display_gw;
				
				$total_prepack = ($is_polybag==1 && $is_blisterbag==1? $this_polybag_qty_in_blisterbag: $total_prepack);
				//$total_prepack = ($is_polybag==1 && $is_blisterbag==0? $this_blisterbag: $total_prepack);
				$str_gmtqtyinpoly = ($is_polybag==1 ? "<br/><small><font color='blue'>($this_gmt_qty_in_polybag product in 1 polybag)</font></small>":"");
				
				$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_prepack $str_gmtqtyinpoly $lbl_polybaginfo</td>";//--- Prepack ---//
				$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$this_blisterbag $lbl_blisterbaginfo</td>";//--- Prepack Per Carton ---//
				$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_qty_in_carton</td>";//--- Carton Qty ---//
				$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$this_total_qty</td>";//--- Total Qty ---//
				$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_nnw $str_unit
						<br/><small><i><font color='$css_acc_wgt'>$this_netnet_weight $str_unit/ per ctn</font></i></small>
						</td>";//--- Net Net Weight ---//
				$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_nw $str_unit
						<br/><small><i><font color='$css_acc_wgt'>NW: $this_net_weight $str_unit/ per ctn</font></i></small>
						<br/><small><i><font color='$css_acc_wgt'>Acc Wgt: $str_acc_weight $str_unit/ per ctn</font></i></small>
						</td>";//--- Net Weight ---// 
				$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_gw $str_unit
											<br/><small><i><font color='blue'>$this_gross_weight $str_unit/ per ctn</font></i></small>
											<br/><small><i><font color='blue'>Carton Weight: $ctn_weight</font></i></small></td>";//--- Gross Weight ---//
				$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$str_measurement</td>";//--- Carton Measurement ---//
				$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_carton_CBM
						<br/><small><i><font color='$css_acc_wgt'>$single_carton_CBM / per ctn</font></i></small>
						</td>";//--- Total CBM ---//
				$html .= "</tr>";
				
			}
			
			if($is_size_seq==1 && count($arr_colorsize)>0 && $arr_colorsize["$group_number=^$this_size=^$this_factor=^$SKU"]>0){
				
				$this_html = '';
				list($this_html, $start_carton, $grand_total_qty, $grand_total_cbm, $grand_nnw, $grand_nw, $grand_gw) = 
						$this->checkBalanceQtyInCarton($PID, $arr_colorsize, $soID, $shipmentpriceID, $color_type, $num_column, 
												$start_carton, $is_gap, $balpack, $grand_total_qty, $is_standard, $last_ctn_by_SCSS, 
												$last_ctn_num_size, $grand_total_cbm, $is_pdf, $packing_type, $grand_nnw, $grand_nw, $grand_gw,
												$is_polybag, $is_blisterbag, $is_ctnblister, $order_by_color);
				$html .= $this_html;
				$arr_colorsize = array();
			}
		}//--- End If Total Qty > 0 ---//
	}//--- End While ---//
	
	//echo count($arr_colorsize)." <--- <br/>";
	
	if(count($arr_colorsize)>0 && $is_size_seq!=1){
		//echo "here!!!!";
		//print_r($arr_colorsize);
		//if($PID!=32299){
		list($this_html, $start_carton, $grand_total_qty, $grand_total_cbm, $grand_nnw, $grand_nw, $grand_gw) = 
						$this->checkBalanceQtyInCarton($PID, $arr_colorsize, $soID, $shipmentpriceID, $color_type, $num_column, 
												$start_carton, $is_gap, $balpack, $grand_total_qty, $is_standard, $last_ctn_by_SCSS, 
												$last_ctn_num_size, $grand_total_cbm, $is_pdf, $packing_type, $grand_nnw, $grand_nw, $grand_gw,
												$is_polybag, $is_blisterbag, $is_ctnblister, $order_by_color);
		$html .= $this_html;
		//}
		$arr_colorsize = [];
	}
	
	if($cannot_pack_count>0 && $grand_total_qty==0){
		$total_col = $num_column+13;
		$html .= '<tr><td colspan="'.$total_col.'" style="background-color:#fff;text-align:center">Cannot Pack</td></tr>';
	}
	
	list($this_html) = $this->funcLastTotalDisplay($grand_total_qty, $soID, $num_column, $grand_total_cbm, $grand_nnw, $grand_nw, $grand_gw, $str_unit);
	$html .= $this_html;
	
	$html .= "</table><br/><br/>";
	return array($html, $start_carton);
}

public function funcLastTotalDisplay($grand_total_qty, $soID, $num_column, $grand_total_cbm, $grand_nnw=0, $grand_nw=0, $grand_gw=0, $str_unit=""){
	$html = "";
	$total_col = $num_column+6;
	
	$html .= '<tr >';
	$html .= '<td style="background-color:#e8e8e8">Total</td>';
	$html .= '<td style="background-color:#e8e8e8" colspan="'.$total_col.'" ></td>';
	$html .= '<td style="background-color:#e8e8e8;text-align:center">'.$grand_total_qty.'</td>';
	$html .= '<td style="background-color:#e8e8e8;text-align:center">'.$grand_nnw.' '.$str_unit.'</td>';
	$html .= '<td style="background-color:#e8e8e8;text-align:center">'.$grand_nw.' '.$str_unit.'</td>';
	$html .= '<td style="background-color:#e8e8e8;text-align:center">'.$grand_gw.' '.$str_unit.'</td>';
	$html .= '<td style="background-color:#e8e8e8;"></td>';
	$html .= '<td style="background-color:#e8e8e8;text-align:center">'.$grand_total_cbm.'</td>';
	$html .= "</tr>";
	
	return array($html);
}

public function checkBalanceQtyInCarton($PID, $arr_colorsize, $soID, $shipmentpriceID, $color_type, $num_column, 
									$start_carton, $is_gap, $balpack, $grand_total_qty, $is_standard, $last_ctn_by_SCSS, 
									$last_ctn_num_size, $grand_total_cbm, $is_pdf, $packing_type, $grand_nnw=0, $grand_nw=0, $grand_gw=0,
									$is_polybag, $is_blisterbag, $is_ctnblister, $order_by_color=0){
	$packing_method = 2;
	$lbl_polybaginfo = ($is_polybag==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "1"): ""); // polybag
	$lbl_blisterbaginfo = ($is_blisterbag==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "2"): ""); // blisterbag
	$lbl_ctnblisterbaginfo = ($is_ctnblister==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "3"): ""); // ctn blisterbag
	
	$html = ""; $pack_factor="";
	$from_location = $this->from_location;
	$arr_last = array();
	$arr_chk_last = array();
	foreach ($arr_colorsize as $key => $balance_qty) {
		
		$arr_temp = explode("=^", $key);
		$group_number = $arr_temp[0];
		$this_size = $arr_temp[1];
		$this_pack_factor = $arr_temp[2];
		$SKU = (array_key_exists(3, $arr_temp)? $arr_temp[3]:"");
		$this_packing_method = 2;
	
		$str_color_ID = $this->getComboOrSingleColorNameDisplay($shipmentpriceID, $group_number, $color_type, "1");
		
		
		
		list($carton_qty, $this_qty_in_carton, $this_balance_qty, $str_pack_name) = 
											$this->checkBalanceCartonQty($is_gap, $this_pack_factor, $balance_qty, $balpack,"",
																		$shipmentpriceID, $is_standard, $PID, $packing_type, 
																		$this_packing_method, $this_size);
		
		if($this->acctid==3){
			//echo "$group_number // $this_size // $this_pack_factor // $SKU // [$balance_qty<>$this_balance_qty] $is_gap <br/>";
		}
		
		if($this_balance_qty>0){
			$arr_last["$group_number=^$this_size=^$SKU"] = $this_balance_qty;
			// echo "$group_number == $this_size [$SKU] // $this_balance_qty --> $is_gap / pf:$this_pack_factor / $balance_qty / $this_qty_in_carton<br/>";
			$arr_chk_last["$group_number=^$this_size=^$this_pack_factor"] = $this_balance_qty;
		}
		
		$actual_total_carton = $carton_qty;
		$sql_factor = "SELECT amount, kg, amount_2, amount_3, amount_4 as L, amount_5 as W, amount_6 as H, amount_7 as CBM 
							FROM tblpackfactor WHERE orderno='$soID' AND colorID = '$str_color_ID' AND size='$this_size' AND del='0'";
		$result_factor = $this->conn->prepare($sql_factor);
		$result_factor->execute();
		$row_factor = $result_factor->fetch(PDO::FETCH_ASSOC);
				$this_factor = $row_factor["amount"];
				$this_kg = $row_factor["kg"];
				$this_accweight = $row_factor["amount_2"];
				$this_cartonweight = $row_factor["amount_3"];
				$this_length = $row_factor["L"];
				$this_width = $row_factor["W"];
				$this_height = $row_factor["H"];
				$this_CBM = $row_factor["CBM"];
				$str_measurement = "$this_length x $this_width x $this_height";
				//$total_carton_CBM = $this_CBM * $actual_total_carton;
		
		$end_carton = $start_carton + $carton_qty - 1;
		$str_carton_display = "$start_carton - $end_carton";
		//$start_carton += $carton_qty;
		$total_qty_in_carton = $carton_qty * $this_qty_in_carton;
		if($start_carton<=$end_carton){ 		
			$html .= "<tr>";
			$html .= "<td class='topcolortd leftcolortd'>$str_carton_display</td>";
			$html .= "<td class='topcolortd leftcolortd'>$carton_qty <br>$str_pack_name $lbl_ctnblisterbaginfo</td>";//--- Total Carton ---//
			$html .= "<td class='topcolortd leftcolortd'>$SKU</td>";//--- Prepack# ---//
			$str_color = $this->getComboOrSingleColorNameDisplay($shipmentpriceID, $group_number, $color_type, "0");
			$html .= "<td class='topcolortd leftcolortd'>$str_color</td>";//--- Color ---//
			
			$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				
				//--- Get Ratio Qty by size ---//
					$sql = "SELECT spd.gmt_qty_in_polybag, spd.polybag_qty_in_blisterbag, spd.blisterbag_in_carton,
									spk.is_polybag, spk.is_blisterbag
							FROM tblship_packing_detail$from_location spd
							INNER JOIN tblship_packing$from_location spk ON spk.PID = spd.PID
							WHERE spd.PID='$PID' AND spd.group_number='$group_number' AND spd.size_name='$size' AND spd.statusID='1'";
					$result_ratio = $this->conn->prepare($sql);
					$result_ratio->execute();	
						$row_ratio = $result_ratio->fetch(PDO::FETCH_ASSOC);
						$is_polybag = $row_ratio["is_polybag"];
						$is_blisterbag = $row_ratio["is_blisterbag"];
						$gmt_qty_in_polybag = $row_ratio["gmt_qty_in_polybag"];
						$polybag_qty_in_blisterbag = $row_ratio["polybag_qty_in_blisterbag"];
						$blisterbag_in_carton = $row_ratio["blisterbag_in_carton"];
				
				if($size==$this_size){
					$prepack_qty = ($is_polybag==1 && $is_blisterbag==0? $gmt_qty_in_polybag :$polybag_qty_in_blisterbag);
					
					$size_qty = $this_qty_in_carton;
					$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$this_qty_in_carton</td>";
					$this_blisterbag = $blisterbag_in_carton;
					$this_blisterbag = ($this_pack_factor==$this_blisterbag? $this_qty_in_carton: 1);//$this_qty_in_carton;//$blisterbag_in_carton;
					$this_blisterbag = ($is_polybag==1 && $is_blisterbag==0? $this_qty_in_carton/$prepack_qty :$this_blisterbag);
				}
				else{
					$size_qty = 0;
					$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'></td>";
				}
				
				//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
				//====> Able Pack Carton, record size detail carton in database <====//
				if($str_carton_display!="Cannot Pack" && $size_qty>0 && $is_pdf==false){
					$this->update_carton_num_detail($start_carton, $end_carton, $shipmentpriceID, $PID, $size, $group_number, $size_qty);
				}
			}//--- End For Col Size Range ---//
			
			$this_total_qty = $total_qty_in_carton * $actual_total_carton;
			$this_total_qty = ($actual_total_carton==0? $total_qty_in_carton: $this_total_qty);
			
			
			// $this_netnet_weight = $this_kg * $this_total_qty;
			// $this_net_weight = $this_kg + $this_accweight;
			// $this_gross_weight = $this_kg + $this_accweight + ($this_cartonweight * $actual_total_carton);
			$grand_total_qty += $total_qty_in_carton;
			
			//Modified by ckwai on 2018-07-25
			$total_prepack=""; $pack_method=2; $arr_size_qty = array();
			list($str_measurement, $ext_CBM, $pack_factor, $ctn_weight, $gmt_pcs_weight, $str_length, $str_width, $str_height) = 
											$this->funcGetCartonMeasurement($soID, $PID, $pack_method, $this_size, $total_prepack, 
																					$is_standard, $str_pack_name, 0, $packing_type, $arr_size_qty);
			//$total_carton_CBM = $ext_CBM * $actual_total_carton;
			//$total_carton_CBM = ($str_length/100) * ($str_width/100) * ($str_height/100) * $actual_total_carton;// modified by ckwai on 202007021138
			$single_carton_CBM = round(($str_length/100) * ($str_width/100) * ($str_height/100), 3);
			$total_carton_CBM = round($single_carton_CBM * $actual_total_carton, 3);
			
			
			$acc_weight = $this->funcGetAccWeight($soID);
			//$this_netnet_weight = $this_total_qty * $gmt_pcs_weight;
			$this_netnet_weight = $this_qty_in_carton * $gmt_pcs_weight;
			$this_net_weight = $this_netnet_weight + $acc_weight;
			$this_gross_weight = $ctn_weight + $this_net_weight;
			$pack_factor = ($is_gap>0? $this_pack_factor:$pack_factor); //-- if gap standard carton --//
				
			
				//>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<//
				//====> Able Pack Carton, record in database <====//
				//>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<//
				if($str_carton_display!="Cannot Pack" && $is_pdf==false){
					$qty_in_blisterbag = $prepack_qty;
					$is_last = 0;
					$this->update_carton_num_head($start_carton, $end_carton, $shipmentpriceID, $PID, $group_number,
										$qty_in_blisterbag, $this_blisterbag, $this_qty_in_carton, $this_netnet_weight, $this_net_weight,
										$this_gross_weight, $str_measurement, $ext_CBM, $is_last, $SKU, $str_length, 
										$str_width, $str_height);
				}
			$start_carton += $actual_total_carton;
			$grand_total_cbm += $total_carton_CBM;
			
			//convert to LBS if is 41 (2018-10-05 w)
			$unit = $this->check_kg_lbs($soID, $PID, $is_standard, $packing_type);
			$wg_unit  = $this->wg_unit;
			$str_unit = "KG";
			if(($unit == 41 && $wg_unit==0) || $wg_unit==57){ //inch use lbs and cm use KGS due to Joe Fresh Order needed on 2020-11-17
				$this_netnet_weight = $this_netnet_weight * 2.204622622;
				$this_net_weight = $this_net_weight * 2.204622622;
				$this_gross_weight = $this_gross_weight * 2.204622622;
				$acc_weight = round($acc_weight * 2.204622622, 5);
				$str_unit = "LBS";
			}
			$str_acc_weight = number_format($acc_weight, 5);
			//convert to one decimal (2018-10-05 w) //request by shipping rithy, 202202221036
			$this_netnet_weight = round($this_netnet_weight, 3); //$this->to_one_dec($this_netnet_weight);
			$this_net_weight    = round($this_net_weight, 3); //$this->to_one_dec($this_net_weight);
			$this_gross_weight  = round($this_gross_weight, 3); //$this->to_one_dec($this_gross_weight);
			$this_blisterbag = ($this_blisterbag==0? 1: $this_blisterbag);
			$prepack_qty = $this_qty_in_carton / $this_blisterbag;
			
			$display_nnw = $this_netnet_weight * $actual_total_carton;
			$display_nw = $this_net_weight * $actual_total_carton;
			$display_gw = $this_gross_weight * $actual_total_carton;
			
			$grand_nnw += $display_nnw;
			$grand_nw += $display_nw;
			$grand_gw += $display_gw;
			
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$prepack_qty $lbl_polybaginfo</td>";//--- Prepack ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$this_blisterbag $lbl_blisterbaginfo</td>";//--- Prepack Per Carton ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$this_qty_in_carton</td>";//--- Carton Qty ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_qty_in_carton</td>";//--- Total Qty ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_nnw $str_unit
							<br/><small><i><font color='$css_acc_wgt'>$this_netnet_weight $str_unit/ per 
							</td>";//--- Net Net Weight ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_nw $str_unit 
							<br/><small><i><font color='blue'>NW: $this_net_weight $str_unit/ per ctn</font></i></small>
							<br/><small><i><font color='blue'>Acc Wgt: $str_acc_weight $str_unit /per ctn</font></i><small>
							</td>";//--- Net Weight ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_gw $str_unit
												<br/><small><i><font color='blue'>$this_gross_weight $str_unit/ per ctn</font></i></small></td>";//--- Gross Weight ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$str_measurement</td>";//--- Carton Measurement ---//
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_carton_CBM
							<br/><small><i><font color='$css_acc_wgt'>$single_carton_CBM / per ctn</font></i></small>
							</td>";//--- Total CBM ---//
			$html .= "</tr>";
		}
		
	}//--- End Foreach ---//
	
	//=====================================================//
	//========= Only For Gap Standard Check Again =========//
	//========= Is_Gap: 1, 2 is online ====================//
	$chk=false;
	if($is_gap>0){
		foreach ($arr_chk_last as $key => $balance_qty) {
			
			$arr_temp = explode("=^", $key);
			$this_pack_factor = $arr_temp[2];
			$g8m = ($is_gap==1 ? floor($this_pack_factor * 25 / 100): floor($this_pack_factor * 32 / 100));// g8m or GID3 
			
			if($balance_qty>$g8m){
				//echo "chk===>$balance_qty > $g8m<br/>";
				$chk=true;
			}
		}
	}
	
	if($chk==true){
			//echo "$chk===>$balance_qty > $g8m<br/>";
			list($this_html, $start_carton, $grand_total_qty, $grand_total_cbm, $grand_nnw, $grand_nw, $grand_gw) = 
							$this->checkBalanceQtyInCarton($PID, $arr_chk_last, $soID, $shipmentpriceID, $color_type, $num_column, 
											$start_carton, $is_gap, $balpack, $grand_total_qty, $is_standard, $last_ctn_by_SCSS, 
											$last_ctn_num_size, $grand_total_cbm, $is_pdf, $packing_type, $grand_nnw, $grand_nw, $grand_gw,
											$is_polybag, $is_blisterbag, $is_ctnblister, $order_by_color);
			$html .= $this_html;
	}
	//======================================================//
	//======================================================//
	else if(count($arr_last)>0){		
		if(($pack_factor == "") || ($pack_factor == null)){
			$pack_factor = $this_pack_factor;
		}
//print_r($arr_last);		
		//$pack_factor = ($pack_factor == "" ? $this_pack_factor : $pack_factor);
		//echo "$pack_factor / $this_pack_factor";
		//======================================================//
		//===Special last carton rule for Single Color (Carton) - Single Size Pack + is "Non-Standard", if total balance amount is greater than pack factor, use split the balance into carton according pack factor, until balance is lower than pack factor (2018-10-18 w)================================//
		//======================================================//		
		//echo "$is_standard - $last_ctn_by_SCSS <br/>";
		if(($is_standard == 1) && ($last_ctn_by_SCSS == 0)){
			
			//check how many group
			$total_bals = array();
			foreach ($arr_last as $key => $balance_qty) {		
				$arr_temp = explode("=^", $key);
				$group_number = $arr_temp[0];
				//echo "$group_number<br/>";
				if(!isset($total_bals[$group_number])){
					$total_bals[$group_number] = $balance_qty;
				}else{
					$total_bals[$group_number] += $balance_qty;
				}
			}
			
			//calculate how many carton needed for each group
			foreach ($total_bals as $key_bals => $total_bals_data) {
				//echo "loops $key_bals<br/>";
				
				//generate array by group
				$arr_last_group = array();
				foreach ($arr_last as $key => $balance_qty) {
					$arr_temp = explode("=^", $key);
					$group_number = $arr_temp[0];
						
					if($group_number == $key_bals){
						$arr_last_group[$key] = $balance_qty;
					}
				}
				
				$total_bals_carton = ceil($total_bals_data / $pack_factor);

				//calculate how to build a carton fulfill pack factor (2018-11-09 w)
				// $perfect_bals = $arr_last_group;
				$perfect_last_bals = $arr_last_group;
				// $perfect_bals_count = count($arr_last_group);
				for($tbal=0; $tbal<$total_bals_carton; $tbal++){
					$perfect_bals = $arr_last_group;
					$mini_pack_factor = $pack_factor;
					foreach ($perfect_last_bals as $key => $balance_qty) {
						$arr_temp = explode("=^", $key);
						$group_number = $arr_temp[0];
						if($group_number == $key_bals){
							
							if($balance_qty <= $mini_pack_factor){							
								$split_amount = $balance_qty;
							}else{
								$split_amount = $mini_pack_factor;
							}
							$mini_pack_factor -= $split_amount;
							$perfect_bals[$key] = $split_amount;
							$perfect_last_bals[$key] = $balance_qty - $perfect_bals[$key];
						}
					}
					
					// echo "<pre>";
					// print_r($perfect_last_bals);
					// echo "</pre>";
					
					$pass_arr = ($tbal == $total_bals_carton ? $perfect_last_bals : $perfect_bals);
					list($this_html, $start_carton, $grand_total_qty, $grand_total_cbm, $grand_nnw, $grand_nw, $grand_gw) = 
									$this->funcLastCartonBalance($pass_arr, $start_carton, $is_gap, $num_column, $soID, $color_type, 
													$shipmentpriceID, $grand_total_qty, $pack_factor, $PID, $is_standard, $last_ctn_by_SCSS, 
													$last_ctn_num_size, $grand_total_cbm, $is_pdf, $packing_type, 1, $grand_nnw, $grand_nw, $grand_gw,
													$is_polybag, $is_blisterbag, $is_ctnblister);
					$html .= $this_html;
					
				}

				//============previous split by ratio method, but removed on 2018-11-09 w=============
				//calculate how to build a carton fulfill pack factor
				// $perfect_bals = $arr_last_group;
				// $perfect_last_bals = $arr_last_group;
				// $perfect_bals_count = count($arr_last_group);
				// if($total_bals_carton > 1){
					// foreach ($arr_last_group as $key => $balance_qty) {
						// $arr_temp = explode("=^", $key);
						// $group_number = $arr_temp[0];
												
						// if($group_number == $key_bals){
							// $perfect_bals[$key] = floor($balance_qty / $total_bals_carton);
							// $perfect_last_bals[$key] = $balance_qty - ($perfect_bals[$key] * ($total_bals_carton - 1));
						// }
					// }
					
					// //check is fulfill
					// $perfect_need = $pack_factor - array_sum($perfect_bals);
					// $ansddd = array_sum($perfect_bals);
					// echo "$perfect_need / $ansddd<br/>";
					// //contiunue loop until is fulfill pack factor
					// do{
						// //$aa = 0;
						// //$aa++;
						// //echo "<br/>$aa<br/>";
						// $all_cannot_give = 0;
						// foreach ($arr_last_group as $key => $balance_qty) {
							// $arr_temp = explode("=^", $key);
							// $group_number = $arr_temp[0];
							// //$aa++;
							// //echo "$aa = $key / $perfect_need / $perfect_last_bals[$key] / $total_bals_carton<br/>";
							// //check is suitable to give this size an amount, each time assign / decrease 1pc only
							// if(($perfect_last_bals[$key] - $total_bals_carton + 1 > 0) && ($perfect_need > 0)){
								// //echo "before : ".$perfect_bals[$key]."<br/>";
								// //assign one more to this size
								// $perfect_bals[$key]++;
								
								// //decrease balance quantity for this size (last last carton)
								// $perfect_last_bals[$key] = $perfect_last_bals[$key] - $total_bals_carton + 1;
								
								// //decrease amount still needed to fulfill pack factor by 1
								// $perfect_need--;
								// //echo "after : $perfect_need = ".$perfect_bals[$key]."<br/>";
							// }else{
								// $all_cannot_give++;
							// }
						// }
						
						// //stop the loop if all size cannot give amount anymore
						// if($all_cannot_give == $perfect_bals_count){
							// $perfect_need = 0;
						// }
						// //echo "$all_cannot_give == $perfect_bals_count = $perfect_need<br/>";
					// }while($perfect_need > 0);
				// }
				
				// for($tbal=0; $tbal < $total_bals_carton; $tbal++){			
					// $pass_arr = ($tbal == $total_bals_carton - 1 ? $perfect_last_bals : $perfect_bals);
					// list($this_html, $start_carton, $grand_total_qty, $grand_total_cbm) = 
									// $this->funcLastCartonBalance($pass_arr, $start_carton, $is_gap, $num_column, $soID, $color_type, 
													// $shipmentpriceID, $grand_total_qty, $pack_factor, $PID, $is_standard, $last_ctn_by_SCSS, 
													// $last_ctn_num_size, $grand_total_cbm, $is_pdf, $packing_type, 1);
					// $html .= $this_html;
				// }
			}	
// $total_bal = array_sum($arr_last);										
// print_r($arr_last);										
// echo "<br/>$total_bal, $arr_last, $start_carton, $is_gap, $num_column, $soID, $color_type, 
										// $shipmentpriceID, $grand_total_qty, $pack_factor, $PID, $is_standard, $last_ctn_by_SCSS, 
										// $last_ctn_num_size, $grand_total_cbm, $is_pdf, $packing_type";			
			
		}
		else{
		//==========end: special method===================		
			list($this_html, $start_carton, $grand_total_qty, $grand_total_cbm, $grand_nnw, $grand_nw, $grand_gw) = 
							$this->funcLastCartonBalance($arr_last, $start_carton, $is_gap, $num_column, $soID, $color_type, 
											$shipmentpriceID, $grand_total_qty, $pack_factor, $PID, $is_standard, $last_ctn_by_SCSS, 
											$last_ctn_num_size, $grand_total_cbm, $is_pdf, $packing_type, 0, $grand_nnw, $grand_nw, $grand_gw,
											$is_polybag, $is_blisterbag, $is_ctnblister);

											
			$html .= $this_html;
		}
	}
	
	return array($html, $start_carton, $grand_total_qty, $grand_total_cbm, $grand_nnw, $grand_nw, $grand_gw);
}

//----- Not use Function -----//
public function funcLastCartonBalance_backup($arr_last, $start_carton, $is_gap, $num_column, $soID, $color_type, $shipmentpriceID, $grand_total_qty){
	$html = "";
	$end_carton = $start_carton;
	$str_carton_display = "$start_carton - $end_carton";
	$start_carton+=1;
	
	$html .= "<tr>";
		$html .= "<td class='topcolortd leftcolortd'>$str_carton_display</td>";
		$html .= "<td class='topcolortd leftcolortd'>1</td>";//--- Total Carton ---//
		$html .= "<td class='topcolortd leftcolortd'>MIX</td>";//--- Prepack# ---//
		$html .= "<td class='topcolortd leftcolortd'></td>";//--- Color ---//
		
	$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
		$total_last_qty = 0; $str_color = "";
		for($c=0;$c<$num_column;$c++){
			$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
			$size = $columntitle2['SizeName'];
			$bal_qty = 0;
			foreach ($arr_last as $key => $balance_qty) {
				$arr_temp = explode("=^", $key);
				$group_number = $arr_temp[0];
				$this_size = $arr_temp[1];
				$str_color .= "$group_number - ";
				if($size==$this_size){
					$bal_qty += $balance_qty;
				}
			}//--- End Foreach ---//
			$total_last_qty += $bal_qty;
			$html .= "<td class='topcolortd leftcolortd'>$bal_qty</td>";
		}//--- End For ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_last_qty</td>";//--- Prepack ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>1 </td>";//--- Prepack Per Carton ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_last_qty</td>";//--- Carton Qty ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_last_qty</td>";//--- Total Qty ---// 
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'></td>";//--- Net Net Weight ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'></td>";//--- Net Weight ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'></td>";//--- Gross Weight ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'></td>";//--- Carton Measurement ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'></td>";//--- Total CBM ---//
	$html .= "</tr>";
	
	return array($html, $start_carton, $grand_total_qty);
}

public function funcLastCartonBalance($arr_last, $start_carton, $is_gap, $num_column, $soID, $color_type, $shipmentpriceID, $grand_total_qty, 
										$pack_factor, $PID, $is_standard, $last_ctn_by_SCSS, $last_ctn_num_size, $grand_total_cbm, $is_pdf, $packing_type, $weight_mode, $grand_nnw=0, $grand_nw=0, $grand_gw=0,
										$is_polybag, $is_blisterbag, $is_ctnblister){
	$html = ""; $this_size="";
	$from_location = $this->from_location;
	//-------- Distinct Color --------//
	$arr_color_temp = array();
	$arr_color_SKU  = array();
	$arr_color_last_multi_size = array();
	$total_balance_qty = 0;
	//---- testing variable ----//
	$count_last_num = 0;
	$count_tt = 0;
	$last_grp = 0;
	$pack_factor_chk = 0;
	
	$packing_method = 2;
	$lbl_polybaginfo = ($is_polybag==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "1"): ""); // polybag
	$lbl_blisterbaginfo = ($is_blisterbag==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "2"): ""); // blisterbag
	$lbl_ctnblisterbaginfo = ($is_ctnblister==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "3"): ""); // ctn blisterbag
	
	foreach ($arr_last as $key => $balance_qty) {
		list($temp_group_number, $temp_size, $SKU) = explode("=^", $key);
		
		// echo "[$SKU] $temp_size $balance_qty < ==== $last_ctn_by_SCSS // $last_ctn_num_size // $last_grp==$temp_group_number <br/>";
		
		//---- Check Whether last carton need by Single Color Single Size Pack ---//
			//-- ** If last carton not by Single Color Single Size Pack ** --//
			if($last_ctn_by_SCSS==0){
				if(!in_array("$temp_group_number", $arr_color_temp)){
					array_push($arr_color_temp, "$temp_group_number");
				}
			}
			else if($last_ctn_by_SCSS==1 && $last_ctn_num_size==1){
				array_push($arr_color_temp, "$temp_group_number=^$temp_size=^$balance_qty=^$SKU");
			}
			else if($last_ctn_by_SCSS==1 && $last_ctn_num_size>1){
				
				//---- Testing variable ----//
				if($count_last_num<$last_ctn_num_size && $last_grp==$temp_group_number){
					//*****--- Check Pack Factor by mix fixed number of size by ckwai on 20190107 ---******//
					$pack_factor_chk += $balance_qty;
					
					$extra_bal = false;
					$str_temp_size_start_extra = "";
					//echo "$pack_factor_chk > $pack_factor <br/>";
					if($pack_factor_chk>$pack_factor){
						$bal_pack_qty = $pack_factor_chk - $pack_factor;
						$actual_qty = $balance_qty - $bal_pack_qty;
						$balance_qty = $actual_qty;
						
						$str_temp_size_start_extra = "$temp_group_number=^$temp_size=^$bal_pack_qty=^$SKU";
						$extra_bal = true;
						
						//echo "$str_temp_size_start_extra << ===== ===== =====  <br/>";
						
					}
					$pack_factor_chk = 0;
					//******---END CHECK---******//
					//***************************//
					
					$str_pos = count($arr_color_temp) - 1;
					$str_temp_size_start = $arr_color_temp[$str_pos];
					$str_temp_size_start .= "%%**$temp_group_number=^$temp_size=^$balance_qty=^$SKU";
					$arr_color_temp[$str_pos] = $str_temp_size_start;
					
					//echo "[$count_tt] $temp_group_number - $temp_size - $balance_qty // $PID - ".count($arr_color_temp)." / $str_pos<br/>";
					
					//*****--- Check Pack Factor by mix fixed number of size by ckwai on 20190107 ---******//
					if($extra_bal==true){
						
						array_push($arr_color_temp, "$str_temp_size_start_extra");
						$count_last_num=0;
						$pack_factor_chk += $bal_pack_qty;
						//echo "$bal_pack_qty <===== [$actual_qty] // $pack_factor_chk <br/>";
					}
					//******---END CHECK---******//
					//***************************//
					
				}
				else{
					//$arr_temp_size_start = array();
					$count_tt++;
					$count_last_num=0;
					
					//*****--- Check Pack Factor by mix fixed number of size by ckwai on 20190107 ---******//
					$pack_factor_chk+=$balance_qty;
					
					$extra_bal = false;
					$str_temp_size_start_extra = "";
					
					if($pack_factor_chk>$pack_factor){
						$bal_pack_qty = $pack_factor_chk - $pack_factor;
						$actual_qty = $balance_qty - $bal_pack_qty;
						$balance_qty = $actual_qty;
						
						$str_temp_size_start_extra = "$temp_group_number=^$temp_size=^$bal_pack_qty=^$SKU";
						$extra_bal = true;
						
						//echo "$str_temp_size_start_extra << ===== ===== =====  <br/>";
						$pack_factor_chk = 0;
					}
					//******---END CHECK---******//
					//***************************//
					
					//echo "<br/>$pack_factor push [$count_tt] $temp_group_number - $temp_size - $balance_qty // $PID - ".count($arr_color_temp)."<br/>";
					$str_temp_size_start = "$temp_group_number=^$temp_size=^$balance_qty=^$SKU";
					
					//echo "$pack_factor_chk > $pack_factor <br/>";
					array_push($arr_color_temp, "$str_temp_size_start");
					
					//*****--- Check Pack Factor by mix fixed number of size by ckwai on 20190107 ---******//
					if($extra_bal==true){
						
						array_push($arr_color_temp, "$str_temp_size_start_extra");
						$count_last_num=0;
						$pack_factor_chk += $bal_pack_qty;
						//echo "$bal_pack_qty <===== [$actual_qty] // $pack_factor_chk <br/>";
					}
					//******---END CHECK---******//
					//***************************//
				}
				$last_grp = $temp_group_number;
				$count_last_num++;
				//---- End Testing variable ----//
				
			}
		$total_balance_qty += $balance_qty;
		//echo "$temp_group_number / $total_balance_qty == ";
	}
	//print_r($arr_color_temp);
	for($i=0;$i<count($arr_color_temp);$i++){
		if($last_ctn_by_SCSS==0){
			$this_grp_number = $arr_color_temp[$i];
			$this_str_pack_name = "MIX";
			//---- If Standard Carton For Gap by ckwai on 2018-08-14 ----//
			if($is_gap>0){
				$total_balance_qty = 0;
				foreach ($arr_last as $key => $balance_qty) {
					list($temp_group_number, $temp_size) = explode("=^", $key);
					if($temp_group_number==$this_grp_number){
						$total_balance_qty += $balance_qty;
					}
				}
			}//--- End If Gap ---//
		}
		else if($last_ctn_by_SCSS==1 && $last_ctn_num_size==1){
			list($this_grp_number, $this_size, $this_balance_qty, $this_SKU) = explode("=^",$arr_color_temp[$i]);
			$total_balance_qty = $this_balance_qty;
			$this_str_pack_name = "";
		}
		else if($last_ctn_by_SCSS==1 && $last_ctn_num_size>1){
			$str_temp_size = $arr_color_temp[$i];
			$arr_temp_size = explode("%%**",$str_temp_size);
			list($this_grp_number, $this_size, $this_balance_qty, $this_SKU) = explode("=^", $arr_temp_size[0]);
			//---- If Standard Carton For Gap by ckwai on 2018-08-14 ----//
			if($is_gap>0){
				$total_balance_qty = 0;
				for($tt=0;$tt<count($arr_temp_size);$tt++){
					list($ttt_grp_number, $ttt_size, $ttt_balance_qty, $this_SKU) = explode("=^", $arr_temp_size[$tt]);
					$total_balance_qty += $ttt_balance_qty;
				}
			}
		}
		
		$end_carton = $start_carton;
		$str_carton_display = "$start_carton - $end_carton ";
		$str_carton_display = ($pack_factor==0? "Cannot Pack":"$str_carton_display");
		$str_extra = ($pack_factor==0? "<br/><font color='red'>No Pack Factor</font>":"");
		$css_highlight = ($str_carton_display=="Cannot Pack"? "style='color:#bdbdbd'":"");
		
		$actual_total_carton = 1;
		$str_color_ID = $this->getComboOrSingleColorNameDisplay($shipmentpriceID, $this_grp_number, $color_type, "1");
		$sql_factor = "SELECT amount, kg, amount_2, amount_3, amount_4 as L, amount_5 as W, amount_6 as H, amount_7 as CBM 
							FROM tblpackfactor WHERE orderno='$soID' AND colorID = '$str_color_ID' AND del='0'";
		$result_factor = $this->conn->prepare($sql_factor);
		$result_factor->execute();
		$row_factor = $result_factor->fetch(PDO::FETCH_ASSOC);
				$this_factor = $row_factor["amount"];
				$this_kg = $row_factor["kg"];
				$this_accweight = $row_factor["amount_2"];
				$this_cartonweight = $row_factor["amount_3"];
				$this_length = $row_factor["L"];
				$this_width = $row_factor["W"];
				$this_height = $row_factor["H"];
				$this_CBM = $row_factor["CBM"];
				$str_measurement = "$this_length x $this_width x $this_height";
				$total_carton_CBM = $this_CBM * $actual_total_carton;
				
	
	$str_pack_name = "";
	if($is_gap>0){
		$balpack = 0;
		$this_packing_method = 2;
		list($tt_ctnqty, $tt_qtyincarton, $tt_balqty, $this_str_pack_name) =
				$this->checkBalanceCartonQty($is_gap, $pack_factor, $total_balance_qty, $balpack, "last",
												$shipmentpriceID, $is_standard, $PID, $packing_type, 
												$this_packing_method, $this_size);
		$str_pack_name = $this_str_pack_name;
	}
	// echo "//-----------//<br/>";
	
			$sqlupc = "SELECT sud.upc_code
						FROM `tblship_upc` su 
						INNER JOIN tblship_upc_detail sud ON sud.SUID = su.SUID
						INNER JOIN tblship_group_color sgc ON sgc.shipmentpriceID = su.shipmentpriceID 
															AND sgc.garmentID = sud.garmentID 
															AND sgc.colorID = sud.colorID
						WHERE su.statusID=1 AND sud.statusID=1 AND sgc.statusID = 1 AND sud.upc_code!='' 
						AND sgc.group_number='$this_grp_number' 
						AND sgc.shipmentpriceID='$shipmentpriceID' AND sud.size_name='$this_size'";
			$stmtupc = $this->conn->prepare($sqlupc);
			$stmtupc->execute();	
			$rowupc = $stmtupc->fetch(PDO::FETCH_ASSOC);
			$SKU = $rowupc["upc_code"];
			// $this_SKU = ($SKU!=""? $SKU: $this_SKU); //hide for Destination XL buyer 2024-03-28
	
	$html .= "<tr $css_highlight>";
		$html .= "<td class='topcolortd leftcolortd'>$str_carton_display $str_extra</td>";
		$html .= "<td class='topcolortd leftcolortd'>1 <br/>$this_str_pack_name $lbl_ctnblisterbaginfo</td>";//--- Total Carton ---//
		$html .= "<td class='topcolortd leftcolortd'>$this_SKU <br> <span class='label label-default label-xs'>Last Carton</span> </td>";//--- Prepack# ---//
		$str_color = $this->getComboOrSingleColorNameDisplay($shipmentpriceID, $this_grp_number, $color_type, "0");
		$html .= "<td class='topcolortd leftcolortd'>$str_color</td>";//--- Color ---//
		
	$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
		$total_last_qty = 0; $str_color = ""; $arr_size_qty = array(); $total_gmt_weight = 0;
		for($c=0;$c<$num_column;$c++){
			$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
			$size = $columntitle2['SizeName'];
			$bal_qty = 0;
			//---- Check Whether last carton need by Single Color Single Size Pack ---//
			// echo "$last_ctn_by_SCSS / $last_ctn_num_size <br/>";
			//-- ** If last carton not by Single Color Single Size Pack ** --//
			if($last_ctn_by_SCSS==0){
				foreach ($arr_last as $key => $balance_qty) {
					$arr_temp = explode("=^", $key);
					$group_number = $arr_temp[0];
					$this_size = ($balance_qty>0? $arr_temp[1]: $this_size);
					$str_color .= "[$group_number / $this_size / $balance_qty]<br/>";
					if($size==$this_size && $group_number==$this_grp_number){
						$bal_qty += $balance_qty;
					}
				}//--- End Foreach ---//
			}
			else if($last_ctn_by_SCSS==1 && $last_ctn_num_size==1){
				if($size==$this_size){
					$bal_qty += $this_balance_qty;
				}
			}
			else if($last_ctn_by_SCSS==1 && $last_ctn_num_size>1){
				for($tt=0;$tt<count($arr_temp_size);$tt++){
					list($tt_grp_number, $this_size, $this_balance_qty) = explode("=^",$arr_temp_size[$tt]);
					if($size==$this_size){
						$bal_qty += $this_balance_qty;
					}
				}
			}
			
			$total_last_qty += $bal_qty;
			//only MIX will display 0 (2018-10-22 w)
			if($last_ctn_by_SCSS == 1){
				$display_bal = ($bal_qty > 0 ? $bal_qty : "");
			}else{
				$display_bal = $bal_qty;
			}
			if($display_bal>0){
				//$str_size = "'$size'";
				$arr_size_qty["$size"] = $display_bal; //-- in order to count last carton garment weight --//
			}
			
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff' >$display_bal</td>";
			
			$gmt_weight       = $this->getGarmentWeightBySize($soID, $PID, $size, $is_standard, $packing_type);
			$this_weight      = $gmt_weight * $display_bal;
			$total_gmt_weight += $this_weight;
			
			$size_qty = $bal_qty;
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
			//====> Able Pack Carton, record size detail carton in database <====//
			if($str_carton_display!="Cannot Pack" && $size_qty>0 && $is_pdf==false){
				$this->update_carton_num_detail($start_carton, $end_carton, $shipmentpriceID, $PID, $size, $this_grp_number, $size_qty);
			}
			
		}//--- End For ---//
	
	$total_qty_in_carton = $total_last_qty;
	$this_total_qty = $total_qty_in_carton * $actual_total_carton;
	$this_total_qty = ($actual_total_carton==0? $total_qty_in_carton: $this_total_qty);
			
	// $this_netnet_weight = $this_kg * $this_total_qty;
	// $this_net_weight = $this_kg + $this_accweight;
	// $this_gross_weight = $this_kg + $this_accweight + ($this_cartonweight * $actual_total_carton);
	$grand_total_qty += $total_last_qty;
	
	//if is using special method in last carton, using pack factor instead (2018-10-18 w)
	$grand_total_qty_pass = ($weight_mode == 0 ? $grand_total_qty : $pack_factor);
	$grand_total_qty_pass = ($is_standard==0? $total_last_qty: 0);
	
	//Modified by ckwai on 2018-07-25
	$total_prepack=""; $pack_method=2; 
	// echo "this_size: $this_size [$last_ctn_by_SCSS]  << <br/>";
	list($str_measurement, $ext_CBM, $tt_pack_factor, $ctn_weight, $gmt_pcs_weight, $str_length, $str_width, $str_height) = 
											$this->funcGetCartonMeasurement($soID, $PID, $pack_method, $this_size, $total_prepack, 
																				$is_standard, $str_pack_name, $grand_total_qty_pass, $packing_type, $arr_size_qty);
	//$total_carton_CBM = $ext_CBM * $actual_total_carton;
	$single_carton_CBM = round(($str_length/100) * ($str_width/100) * ($str_height/100), 3);// modified by ckwai on 202007021138
	$total_carton_CBM = round($single_carton_CBM  * $actual_total_carton, 3);
	
	$acc_weight = $this->funcGetAccWeight($soID);
	//$this_netnet_weight = $this_total_qty * $gmt_pcs_weight;
	//if is using special method in last carton, using pack factor instead (2018-10-18 w)
	$this_netnet_weight = $total_gmt_weight;//($weight_mode == 0 ? $total_last_qty * $gmt_pcs_weight : $pack_factor * $gmt_pcs_weight);
	$this_net_weight = $this_netnet_weight + $acc_weight;
	$this_gross_weight = $ctn_weight + $this_net_weight;
	
	if($PID==89513){
		echo "[$weight_mode] = $total_last_qty x $gmt_pcs_weight << <br/>";
	}
	
	//--- Get Ratio Qty by size ---//
	$sql_get_blister = "SELECT max(spd.gmt_qty_in_polybag) as gmt_qty_in_polybag,
						spd.polybag_qty_in_blisterbag, (spd.blisterbag_in_carton),
						spk.is_polybag, spk.is_blisterbag
			FROM tblship_packing_detail$from_location spd
			INNER JOIN tblship_packing$from_location spk ON spk.PID = spd.PID
			WHERE spd.PID='$PID' AND spd.statusID='1' limit 1";
	$result_get_blister = $this->conn->prepare($sql_get_blister);
	$result_get_blister->execute();
		$row_get_blister = $result_get_blister->fetch(PDO::FETCH_ASSOC);
		$is_blisterbag      = $row_get_blister["is_blisterbag"];
		$is_polybag         = $row_get_blister["is_polybag"];
		$gmt_qty_in_polybag = $row_get_blister["gmt_qty_in_polybag"];
		$polybag_qty_in_blisterbag = $row_get_blister["polybag_qty_in_blisterbag"];
		$blisterbag_in_carton      = $row_get_blister["blisterbag_in_carton"];
		$this_gmt_qty_in_polybag   = (($is_blisterbag==0 && $is_polybag==1) ? $gmt_qty_in_polybag : $total_last_qty);
		$this_poly_in_ctn = (($is_blisterbag==0 && $is_polybag==1)? $total_last_qty / $this_gmt_qty_in_polybag : 1);
		
		//--- modified by ckwai on 2023-04-07 for last carton follow blisterbag qty ---//
		$this_gmt_qty_in_polybag = (($is_blisterbag==1 && $polybag_qty_in_blisterbag<$total_last_qty)? $polybag_qty_in_blisterbag : $this_gmt_qty_in_polybag);
		$this_poly_in_ctn =  ($is_blisterbag==1? $total_last_qty / $this_gmt_qty_in_polybag: $this_poly_in_ctn);
		
		$temp_total = "";
		//added by ckwai on 20230511
		$this_decimal = $this_poly_in_ctn;
		if (is_float($this_poly_in_ctn) && $is_blisterbag==0) {
			$this_poly_in_ctn = 1;
			$this_gmt_qty_in_polybag = $total_last_qty;
		}
		else if (is_float($this_poly_in_ctn) && $is_blisterbag==1) {
			$this_poly_in_ctn = floor($this_poly_in_ctn);
			$this_gmt_qty_in_polybag = $total_last_qty;
			
			$temp_total = $this_poly_in_ctn * $this_gmt_qty_in_polybag;
			if($temp_total>$this_gmt_qty_in_polybag){
				$this_poly_in_ctn = 1;
			}
		}
		else if($this_poly_in_ctn=="" && $is_blisterbag==0){
			$this_poly_in_ctn = 1;
			$this_gmt_qty_in_polybag = $total_last_qty;
		}
	
	$percentage = $total_last_qty / $tt_pack_factor;
	$est_height = (glb_mainproduct=="BAG" || $is_standard==0? $str_height:  ceil($str_height * $percentage));
	// $est_height = ( $is_standard==0? $str_height:  ceil($str_height * $percentage));// Lushbax Request estimate last carton by Vicheka, 2024-03-06
	$est_CBM    = ($str_length / 100) * ($str_width/100) * ($est_height/100); 
	$est_CBM    = round($est_CBM, 3);
	$grand_total_cbm += $est_CBM;
	// $grand_total_cbm += $total_carton_CBM;
	
	//convert to LBS if is 41 (2018-10-05 w)
	$unit = $this->check_kg_lbs($soID, $PID, $is_standard, $packing_type);
	if($unit==41){ //inch
		$str_length_inch = $str_length * 0.393701;
		$str_width_inch  = $str_width * 0.393701;
		$str_height_inch = $est_height * 0.393701;
		
		$est_measurement = "".round($str_length_inch, 1)." x ".round($str_width_inch, 1)." x ".round($str_height_inch, 1)." (inch)";
	}
	else{ //cm
		
		$est_measurement = "".round($str_length,1)." x ".round($str_width,1)." x ".round($est_height,1)." (cm)";
	}
	
	//>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<//
	//====> Able Pack Carton, record in database <====//
	//>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<//
	if($str_carton_display!="Cannot Pack" && $is_pdf==false){
		$qty_in_blisterbag = 1;
		$this_blisterbag = $total_last_qty;
		$is_last = 1;
		$this->update_carton_num_head($start_carton, $end_carton, $shipmentpriceID, $PID, $this_grp_number,
										$this_gmt_qty_in_polybag, $this_poly_in_ctn, $total_qty_in_carton, $this_netnet_weight, $this_net_weight,
										$this_gross_weight, $str_measurement, $est_CBM, $is_last, $this_SKU, $str_length, $str_width, $str_height, $est_measurement);
	}
	
	$start_carton+=1;
	
	
	
	$str_unit = "KG";
	$wg_unit  = $this->wg_unit;
	if(($unit == 41 && $wg_unit==0) || $wg_unit==57){ //16:CM / 41:INCH //inch use lbs and cm use KGS due to Joe Fresh Order needed on 2020-11-17
		$this_netnet_weight = $this_netnet_weight * 2.204622622;
		$this_net_weight = $this_net_weight * 2.204622622;
		$this_gross_weight = $this_gross_weight * 2.204622622;
		$acc_weight = round($acc_weight * 2.204622622,2);
		$str_unit = "LBS";
	}
	$str_acc_weight = number_format($acc_weight, 5);
	//convert to one decimal (2018-10-05 w)//request by shipping rithy, 202202221036
	$this_netnet_weight = round($this_netnet_weight, 3); //$this->to_one_dec($this_netnet_weight);
	$this_net_weight    = round($this_net_weight, 3); //$this->to_one_dec($this_net_weight);
	$this_gross_weight  = round($this_gross_weight, 3); //$this->to_one_dec($this_gross_weight);
	
	$display_nnw = $this_netnet_weight * $actual_total_carton;
	$display_nw = $this_net_weight * $actual_total_carton;
	$display_gw = $this_gross_weight * $actual_total_carton;
	
	$grand_nnw += $display_nnw;
	$grand_nw += $display_nw;
	$grand_gw += $display_gw;
	
	// $s_length = ($str_length / 100);
	// $s_width  = ($str_width / 100);
	// $s_height = ($est_height / 100);
	// $est_CBM    = ($str_length / 100) * ($str_width/100) * ($est_height/100);
	
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$this_gmt_qty_in_polybag $lbl_polybaginfo </td>";//--- Prepack ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$this_poly_in_ctn $lbl_blisterbaginfo</td>";//--- Prepack Per Carton ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_last_qty</td>";//--- Carton Qty ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$total_last_qty</td>";//--- Total Qty ---// 
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_nnw $str_unit
						<br/><small><i><font color='blue'>$display_nnw $str_unit/ per ctn</font></i></small>
						</td>";//--- Net Net Weight ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_nw $str_unit
						<br/><small><i><font color='blue'>NW: $this_net_weight $str_unit/ per ctn</font></i></small>
						<br/><small><i><font color='blue'>Acc Wgt: $str_acc_weight $str_unit/ per ctn</font></i></small>
						</td>";//--- Net Weight ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$display_gw $str_unit
									<br/><small><i><font color='blue'>$this_gross_weight $str_unit/ per ctn</font></i></small></td>";//--- Gross Weight ---//
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$est_measurement
									
									</td>";//--- Carton Measurement ---//<br/><small><i><font color='blue'>Ori. Ctn: $str_measurement</font></i></small>
	$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff'>$est_CBM
									<br/><small><i><font color='blue'>$est_CBM/ per ctn</font></i></small>
									</td>";//--- Total CBM ---//
	$html .= "</tr>";//*/
	
	}
	return array($html, $start_carton, $grand_total_qty, $grand_total_cbm, $grand_nnw, $grand_nw, $grand_gw);
}

public function getGarmentWeightBySize($soID, $PID, $size, $is_standard, $packing_type){
	
	// echo "$soID / $PID / $is_standard / $packing_type << <br/>";
	$sql = "SELECT ccs.gmt_pcs_weight
			FROM tblcarton_calculator_sizeinfo ccs 
			WHERE ccs.orderno=:orderno AND ccs.size_name=:size_name 
			AND ccs.is_standard=:is_standard AND ccs.packing_type=:packing_type 
			AND ccs.PID=:PID AND statusID=1";
	$stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":orderno", $soID);
    $stmt->bindParam(":size_name", $size);
    $stmt->bindParam(":is_standard", $is_standard);
    $stmt->bindParam(":packing_type", $packing_type);
    $stmt->bindParam(":PID", $PID);
    $stmt->execute();
	
	$count = $stmt->rowCount();
	$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);
	
	$gmt_pcs_weight = 0;
	if($count==0){
		$sql = "SELECT ccs.gmt_pcs_weight
			FROM tblcarton_calculator_sizeinfo ccs 
			WHERE ccs.orderno=:orderno AND ccs.size_name=:size_name 
			AND ccs.is_standard=:is_standard AND ccs.packing_type=:packing_type 
			AND ccs.PID='0' AND statusID=1";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(":orderno", $soID);
		$stmt->bindParam(":size_name", $size);
		$stmt->bindParam(":is_standard", $is_standard);
		$stmt->bindParam(":packing_type", $packing_type);
		$stmt->execute();
		
		$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);
		$gmt_pcs_weight = (isset($row[0]["gmt_pcs_weight"])? $row[0]["gmt_pcs_weight"]: "0");
	}
	else{
		$gmt_pcs_weight = (isset($row[0]["gmt_pcs_weight"])? $row[0]["gmt_pcs_weight"]: "0");
	}
	
	return $gmt_pcs_weight;
}

public function funcPackingCartonDisplayFormula_MultiColorRatioPack($PID, $soID, $shipmentpriceID, $start_carton, $color_type, 
														$is_polybag, $is_blisterbag, $is_ctnblister, $is_gap, $balpack, $tmode, $is_pdf, $packing_type, $ship_remark, $is_multi_gender=0){
	$lang = $this->lang;
	$from_location = $this->from_location;
	
	$packing_method = 50;
	$lbl_polybaginfo = ($is_polybag==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "1"): ""); // polybag
	$lbl_blisterbaginfo = ($is_blisterbag==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "2"): ""); // blisterbag
	$lbl_ctnblisterbaginfo = ($is_ctnblister==1? $this->funcGetPolyBagDetailOfPickList($soID, $shipmentpriceID, $packing_method, "3"): ""); // ctn blisterbag
	
	$path = "../lang/{$lang}.php";
	$path2 = "../../lang/{$lang}.php";
	$chk = file_exists($path);
	$url = ($chk==1? $path: $path2);
	
	include($url);
	$html = "";
	$pack_method = 50;
	$arr_colorsize = array(); $arr_last = array();
	$str_polybag = ($is_polybag==1?"<b><u>Poly Bag Required</u><b> &nbsp; &nbsp; ":"");
	$str_blisterbag = ($is_blisterbag==1?"<b><u>Blister Bag Required</u></b> &nbsp; &nbsp; ":"");
	$str_ctn_blister = ($is_ctnblister==1?"<b><u>Carton Blister Required</u></b> &nbsp; &nbsp; ":"");
	$count = 0; $grand_total_qty = 0; $grand_total_cbm=0;
	$str_standard = $this->checkStandardName($tmode);
	$btn_carton_calculator = ($is_pdf==false? "==":$this->funcLinkToCalculator($soID, $PID, $pack_method, $tmode, $packing_type));
	$str_PID = " &nbsp; <em>PID: <b><u>$PID</u></b></em> &nbsp; &nbsp;  $btn_carton_calculator &nbsp;";
	$have_leftover = false;
	$prepack_name = "";
	$str_carton_display = "";
	$grand_nnw=0; $grand_nw=0; $grand_gw=0;

	$sql = "SELECT spd.*, spk.tmode, spk.last_ctn_by_SCSS, spk.last_ctn_num_size, spk.shipmentpriceID
			
			, (SELECT SUM(b.ratio_qty*b.gmt_qty_in_polybag*b.polybag_qty_in_blisterbag) 
				FROM tblship_packing_detail$from_location b WHERE b.PID=spd.PID ) as qty_in_carton 
			
			from tblship_packing_detail$from_location spd
			inner join tblship_packing$from_location spk ON spk.PID = spd.PID
			INNER JOIN tblcolorsizeqty csq ON csq.SizeName = spd.size_name
			where spd.PID = '$PID' AND spd.statusID='1' AND spd.total_qty>0
			group by spd.group_number
			ORDER BY spd.group_number, csq.ID asc";// -- , spd.size_name, spd.ID  
	$columnsql2 = $this->conn->prepare($sql);
	
	/*
	$columnsql2 = $this->conn->prepare("SELECT * FROM tblship_packing spk 
										INNER JOIN tblship_packing_detail spd ON spk.PID = spd.PID 
										INNER JOIN tblship_group_color sgc ON sgc.shipmentpriceID = spk.shipmentpriceID 
										WHERE spd.PID='$PID' AND spd.statusID='1' 
										group by spd.group_number, spd.size_name, spd.ID  
										ORDER BY spd.group_number asc");
	*/
	$columnsql2->execute();	
	$this_rowspan = $columnsql2->rowCount();
	// avoid redundant of the rowspan 
	$countRow = 0;

	$MCRS = ($is_multi_gender==1? $hdlang["MCRS3"]: $hdlang["MCRS2"]);//Multi Color (Carton)
	$html .= "<br/>
			<b class='subTitle'>".$hdlang["packing_method"].": $MCRS - Ratio Pack</b>";
	$html .= "&nbsp;<span class='glyphicon glyphicon-list-alt btntop editcode' data-toggle='modal' 
							data-target='#methodbox' data-id='$PID' data-backdrop='static' data-keyboard='false' title='".$hdlang["ship_12"]."' 
							style='display:inline-block;cursor:pointer'></span>
							&nbsp;<span style='padding:3px;background-color:red;color:#fff;border-radius:5px'><b>".$this->count_attach."</b></span>&nbsp; "; //--- Packing Method Attachment ---//			
	$html .= "&nbsp;$str_PID<br/>"; //-- Packing Method --// //-- Multi Color (Carton) - Multi Size --// 
	$html .= "$str_polybag  ";
	$html .= "$str_blisterbag ";
	$html .= "$str_ctn_blister ";
	$html .= " <b><u>$str_standard</u></b> &nbsp; &nbsp; ";
	$html .= $this->getStrPackingType($packing_type);
	//remark field (2018-10-25 w)
	if($ship_remark != ""){
		$html .= "<b>Remark:</b> $ship_remark";		
	}	
	$html .= "<br/>";
	
	$ctn_width = ($this->pdf_display==1? "30px": "70px");
	
	// print table
	$html .= '<table class="tb_detail pick_list" id="tb_detail" cellspacing="0" cellpadding="3" border="1">';

	// --- print thead --- //
	$html .= '<tr class="titlebar5">';
		$html .= '<th class="titlecol" rowspan="2" style="width:'.$ctn_width.';min-width:'.$ctn_width.'">'.$hdlang["carton_no"].'</th>';//-- Carton No --//
		$html .= '<th class="titlecol" rowspan="2" style="width:50px;min-width:50px">'.$hdlang["total_carton"].'</th>';//-- Total Carton --//
		$html .= '<th class="titlecol" rowspan="2" style="width:50px;min-width:50px">'.$hdlang["pc_per_pack"].'#</th>';//-- Prepack --//
		$html .= '<th class="titlecol" rowspan="2">'.$hdlang["Color"].'</th>';//-- Color --//
		
		$num_column = $this->getSizeNameColumnFromOrder($soID, "0");
		$wd_size = $num_column * 25;
		$pack_method = 50;
		list($str_1st, $str_2nd, $str_3rd, $str_4th) = $this->chkTickedOfPolyBlisterBag($pack_method, $is_polybag, $is_blisterbag);
		
		$html .= '<th class="titlecol" colspan="'.$num_column.'" style="width:'.$wd_size.'px;min-width:'.$wd_size.'px;">'.$hdlang["Size"].'</th>';//-- Size --//
		// $html .= '<th class="titlecol2" style="white-space:nowrap;width:80px;min-width:80px" rowspan="2">'.$str_1st.'</th>';//-- Prepack --//
		// $html .= '<th class="titlecol2" style="white-space:nowrap;width:80px;min-width:80px" rowspan="2">'.$str_2nd.'</th>';//-- Prepack Per Carton --//

		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">'.$str_1st.'</th>';

		// $html .= '<th class="titlecol2" style="white-space:nowrap" rowspan="2">Total Qty </br>of Color</th>';

		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">'.$str_2nd.'</th>';//-- ratio pack --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Total Qty <br/>in 1 carton</th>';//-- Total qty in 1 carton --//
		$html .= '<th class="titlecol" style="white-space:nowrap;width:30px;min-width:30px;" rowspan="2">'.$hdlang["total_set"].'</th>';//-- Total Qty --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Net Net Weight (total ctn)</th>';//-- Net Net Weight --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Net Weight (total ctn)</th>';//-- Net Weight --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Gross Weight (total ctn)</th>';//-- Gross Weight --//
		$html .= '<th class="titlecol" style="white-space:nowrap;width:55px;min-width:55px" rowspan="2">Carton Measurement <br/>(L*W*H)</th>';//-- Carton Measurement --//
		$html .= '<th class="titlecol" style="white-space:nowrap" rowspan="2">Total CBM</th>';//-- Total CBM --//
		
	$html .= "</tr>";

	//---- Display Size Range of order ----//
	$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
	$html .= "<tr class='titlebar5'>";
	for($c=0;$c<$num_column;$c++){
		$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
		$size = $columntitle2['SizeName'];
		
		$html .= "<th class='titlecol' style='while-space:nowrap;width:25px;min-width:25px;'>$size</th>";
	}
	$html .= "</tr>";
	// ---- end print thead -----//

	//---- Load Each Size Row Data ----//
	$cannot_pack_count=0;
	while($row=$columnsql2->fetch(PDO::FETCH_ASSOC)){
		$group_number = $row["group_number"];
		$this_size = $row["size_name"];
		$gmt_qty_in_polybag = $row["gmt_qty_in_polybag"];

		$polybag_qty_in_blisterbag = $row["polybag_qty_in_blisterbag"];
		$blisterbag_in_carton = $row["blisterbag_in_carton"];
		$total_qty = $row["total_qty"];
		$SKU = $row["SKU"];
		$master_upc = $row["master_upc"];
		$case_upc = $row["case_upc"];
		$tmode = $row["tmode"];
		$is_standard = $row["tmode"];
		$last_ctn_by_SCSS = $row["last_ctn_by_SCSS"];
		$last_ctn_num_size = $row["last_ctn_num_size"];
		$shipmentpriceID = $row["shipmentpriceID"];
		$master_upc = $row["master_upc"];
		$case_upc = $row["case_upc"];

		$total_qty = $row['total_qty']; // this is the color qty that user key in
		// the `$actual_qty` inside the if(total_qty>0){ } , that one is the qty that can be packed in the carton.

		$qty_in_carton = $row["qty_in_carton"];
		// $qty_in_carton = floatval($qty_in_blister) * floatval($blisterbag_in_carton);
		
		$is_decimal = 0;
		$prepack_qty = 0;

		$display_MU_CU="";
		if($master_upc<>"" || $case_upc<>""){
			$display_MU_CU="<br>MU:$master_upc<br>CU:$case_upc";
		}
		
		
		if($total_qty > 0){
			$this_factor = $this->funcGetPackFactor($soID, $PID, $pack_method, $this_size, $prepack_qty, $is_standard, $packing_type);
			$str_extra = ($this_factor==0? "<br/><font color='red'>No Pack Factor</font>":"");
			$str_extra = ($this_factor!=$qty_in_carton && $this_factor>0? "<br/><font color='red'>Pack Factor Not Match</font> [$this_factor/$qty_in_carton]":"$str_extra");
			$css_highlight = ($str_extra!="" && $is_pdf==false? "style='color:#bdbdbd;'":"");
			$css_highlight = ($str_extra!="" && $is_pdf==true? "style='color:#bdbdbd;'":"$css_highlight");
		
			$html .= "<tr $css_highlight>";
			if($countRow == 0){
				$carton_number = "";
				$carton_qty = "";
				$arr_leftover = array();
				list($carton_number, $carton_qty, $arr_leftover, $start_carton) = $this->calculateCarton_MultiColorRatioPack($PID, $is_polybag, $is_blisterbag, $start_carton);
				if($str_extra!=""){
					$carton_number = "Cannot pack";
					$start_carton = $start_carton - $carton_qty;
				}
				
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" >'.$carton_number.' '.$str_extra.'</td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" 
								style="background-color:#fff;">'.$carton_qty.' '.$lbl_ctnblisterbaginfo.'</td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;">'.$SKU.''.$display_MU_CU.'</td>';
				
			}
			
			//qty that is packed into carton
			$actual_qty = $total_qty-$arr_leftover[$group_number];
			if($arr_leftover[$group_number] > 0 && !$have_leftover ){
				$have_leftover = true;
			}

			$str_color = $this->getComboOrSingleColorNameDisplay($shipmentpriceID, $group_number, $color_type, '0');
			$html .= '<td class="topcolortd leftcolortd" style="background-color:#fff;" >'.$str_color.'</td>';

			$total_prepack = 0;
			$this_blisterbag = 0;
			$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
			$arr_size_qty = array();
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				
				//--- Get Ratio Qty by size ---//
				$sql2 = "SELECT spd.gmt_qty_in_polybag, spd.polybag_qty_in_blisterbag, spd.blisterbag_in_carton, spd.ratio_qty  
						FROM tblship_packing_detail$from_location spd WHERE spd.PID='$PID' AND spd.group_number='$group_number' AND size_name='$size' AND statusID='1'";
				$result_ratio = $this->conn->prepare($sql2);
				$result_ratio->execute();	
					$row_ratio = $result_ratio->fetch(PDO::FETCH_ASSOC);
					$gmt_qty_in_polybag = $row_ratio["gmt_qty_in_polybag"];
					$ratio_qty = $row_ratio["ratio_qty"];
					$polybag_qty_in_blisterbag = $row_ratio["polybag_qty_in_blisterbag"];
					$blisterbag_in_carton = $row_ratio["blisterbag_in_carton"];

					$size_qty_prepack = $gmt_qty_in_polybag * $ratio_qty;// * $blisterbag_in_carton;
					$size_qty = $size_qty_prepack * $polybag_qty_in_blisterbag ;
				
				// if($size==$this_size){
				if(true){
					$html .= '<td class="topcolortd leftcolortd" style="background-color:#fff;">'.$size_qty_prepack.' '.$lbl_polybaginfo.'</td>';
					$total_prepack += $size_qty_prepack;
					$this_blisterbag = $blisterbag_in_carton;
					
					//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
					//====> Able Pack Carton, record size detail carton in database <====//
					//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
					if($carton_number!="Cannot pack" && $size_qty_prepack>0){
						list($str_start, $str_end) = explode("-", $carton_number);
						$str_start = TRIM($str_start);
						$str_end = TRIM($str_end);
						//echo "$str_start - $str_end // $shipmentpriceID // $PID // $size //$group_number // $size_qty_prepack  || $a - $b <br/>";
						$this->update_carton_num_detail($str_start, $str_end, $shipmentpriceID, $PID, $size, $group_number, $size_qty_prepack);
					}
					
				}else{
					$size_qty = 0;
					$html .= '<td class="topcolortd leftcolortd" style="background-color:#fff;"></td>';
				}

				
			}//---- End For Loop ----//
			
			$html .= "<td class='topcolortd leftcolortd'>$total_prepack</td>";
			$html .= "<td class='topcolortd leftcolortd'>$polybag_qty_in_blisterbag $lbl_blisterbaginfo</td>";
			// $html .= "<td class='topcolortd leftcolortd'>".$actual_qty."</td>";

			if($countRow == 0){
				$qty_in_all_carton = floatval($qty_in_carton)*floatval($carton_qty);
				$grand_total_qty += $qty_in_all_carton;
				
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;">'.$qty_in_carton.'</td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;">'.$qty_in_all_carton.'</td>';
				
				//Modified by ckwai on 20181010 1446
				$size_name = ""; 
				list($str_measurement, $ext_CBM, $pack_factor, $ctn_weight, $gmt_pcs_weight, $str_length, $str_width, $str_height) = 
										$this->funcGetCartonMeasurement($soID, $PID, $pack_method, $size_name, 
																		$qty_in_carton, $is_standard, $prepack_name,0, $packing_type, $arr_size_qty);
				if($str_extra!=""){
					$total_carton_CBM = "0.00";
					$str_measurement = "0.00 x 0.00 x 0.00";
					$this_netnet_weight = "0.00";
					$this_net_weight = "0.00";
					$this_gross_weight = "0.00";
					$single_carton_CBM = 0;
				}
				else{
					//$total_carton_CBM = $ext_CBM * $carton_qty;
					//$total_carton_CBM = ($str_length/100) * ($str_width/100) * ($str_height/100) * $carton_qty;// modified by ckwai on 202007021138
					$single_carton_CBM = round(($str_length/100) * ($str_width/100) * ($str_height/100), 3);
					$total_carton_CBM = round($single_carton_CBM * $carton_qty, 3);
					
					$acc_weight = $this->funcGetAccWeight($soID);
					
					$this_netnet_weight = $qty_in_carton * $gmt_pcs_weight;
					$this_net_weight = $this_netnet_weight + $acc_weight;
					$this_gross_weight = $ctn_weight + $this_net_weight;
				}
				
				//>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<//
				//====> Able Pack Carton, record in database <====//
				//>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<//
				if($carton_number!="Cannot pack" && $is_pdf==false){
					list($str_start, $str_end) = explode("-", $carton_number);
					$str_start = TRIM($str_start);
					$str_end = TRIM($str_end);
					$this_grp = 0;
					$total_qty_in_carton = $qty_in_carton;
					$qty_in_blisterbag = $total_prepack;
					$this_blisterbag = $blisterbag_in_carton;
					$is_last = 0;
					//echo "[head] $str_start - $str_end || $shipmentpriceID - $PID - $this_grp - $str_length - $str_width - $str_height <br/>";
					$this->update_carton_num_head($str_start, $str_end, $shipmentpriceID, $PID, $this_grp,
											$qty_in_blisterbag, $this_blisterbag, $total_qty_in_carton, $this_netnet_weight, $this_net_weight,
											$this_gross_weight, $str_measurement, $total_carton_CBM, $is_last, $SKU, 
											$str_length, $str_width, $str_height);

				}
				
				//convert to LBS if is 41 (2018-10-05 w)
				$unit = $this->check_kg_lbs($soID, $PID, $is_standard, $packing_type);
				$wg_unit  = $this->wg_unit;
				$str_unit = "KG";
				if(($unit == 41 && $wg_unit==0) || $wg_unit==57){ //inch use lbs and cm use KGS due to Joe Fresh Order needed on 2020-11-17
					$this_netnet_weight = $this_netnet_weight * 2.204622622;
					$this_net_weight = $this_net_weight * 2.204622622;
					$this_gross_weight = $this_gross_weight * 2.204622622;
					$acc_weight = round($acc_weight * 2.204622622,4);
					$str_unit = "LBS";
				}
				//convert to one decimal (2018-10-05 w)//request by shipping rithy, 202202221036
				$this_netnet_weight = round($this_netnet_weight, 3); //$this->to_one_dec($this_netnet_weight);
				$this_net_weight    = round($this_net_weight, 3); //$this->to_one_dec($this_net_weight);
				$this_gross_weight  = round($this_gross_weight, 3); //$this->to_one_dec($this_gross_weight);
				
				$display_nnw = $this_netnet_weight * $carton_qty;
				$display_nw = $this_net_weight * $carton_qty;
				$display_gw = $this_gross_weight * $carton_qty;
				
				$grand_nnw += $display_nnw;
				$grand_nw += $display_nw;
				$grand_gw += $display_gw;
			
				// net weight, carton measurement, ...
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;"> 
					'.$display_nnw.' '.$str_unit.' 
					<br/><small><i><font color="blue">'.$this_netnet_weight.' '.$str_unit.'/ per ctn</font></i></small>
					</td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;"> 
					'.$display_nw.' '.$str_unit.'
					<br/><small><i><font color="blue">NW: '.$this_net_weight.' '.$str_unit.'/ per ctn</font></i></small>
					<br/><small><i><font color="blue">Acc Wgt: '.$acc_weight.' '.$str_unit.'/ per ctn</font></i></small>
					</td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;"> 
					'.$display_gw.' '.$str_unit.'
					<br/><small><i><font color="blue">'.$this_gross_weight.' '.$str_unit.'/ per ctn</font></i></small>
					</td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;"> '.$str_measurement.' </td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;"> 
							'.$total_carton_CBM.' 
							<br/><small><i><font color="blue">'.$single_carton_CBM.' / per ctn</font></i></small></td>';
				$grand_total_cbm += $total_carton_CBM;
			}
			$html .= "</tr>";
			$countRow++;
		} // ----- end if( max_total_qty > 0 ) -------------//
			
	}//--- End While ---//

	if($have_leftover){
		
		$res2 = $this->conn->query($sql);
		$countRow = 0;
		while($row2 = $res2->fetch(PDO::FETCH_BOTH)){
			// var get from db
			$group_number = $row2["group_number"];
			$this_size = $row2["size_name"];
			
			$temp_carton_qty = $carton_qty;
			++$carton_qty;
			$str_carton_num = $start_carton;
			$carton_number = $carton_qty-$temp_carton_qty;
			$str_carton_display = "$str_carton_num - $str_carton_num";
			$str_notice = "";
			
			if($str_extra!=""){
				$str_carton_display = "Cannot Pack $str_extra";
				$str_notice = "Cannot pack";
			}

			$html .= "<tr $css_highlight>";
			if($countRow == 0){
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;">'.($str_carton_display).'</td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" 
								style="background-color:#fff;">'.($carton_qty-$temp_carton_qty).' '.$lbl_ctnblisterbaginfo.'</td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;"></td>';
			}
			

			$str_color = $this->getComboOrSingleColorNameDisplay($shipmentpriceID, $group_number, $color_type, '0');
			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff;'>$str_color</td>";

			$total_prepack = 0;
			$this_blisterbag = 0;

			$columnresult2 = $this->getSizeNameColumnFromOrder($soID, "1");
			// print the ratio qty for each size
			for($c=0;$c<$num_column;$c++){
				$columntitle2=$columnresult2->fetch(PDO::FETCH_ASSOC);
				$size = $columntitle2['SizeName'];
				
				//--- Get Ratio Qty by size ---//
				$sql = "SELECT spd.gmt_qty_in_polybag, spd.polybag_qty_in_blisterbag, spd.blisterbag_in_carton, spd.ratio_qty  
						FROM tblship_packing_detail$from_location spd WHERE spd.PID='$PID' AND spd.group_number='$group_number' AND size_name='$size' AND statusID='1'";
				$result_ratio = $this->conn->prepare($sql);
				$result_ratio->execute();	
					$row_ratio = $result_ratio->fetch(PDO::FETCH_ASSOC);
					$gmt_qty_in_polybag = $row_ratio["gmt_qty_in_polybag"];
					$ratio_qty = $row_ratio["ratio_qty"];
					$polybag_qty_in_blisterbag = $row_ratio["polybag_qty_in_blisterbag"];
					$blisterbag_in_carton = $row_ratio["blisterbag_in_carton"];

					$size_qty_prepack = $gmt_qty_in_polybag * $ratio_qty;// * $blisterbag_in_carton;
					$size_qty = $size_qty_prepack * $polybag_qty_in_blisterbag ;
				
				
				// if($size==$this_size){
				if(true){
					$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff;'>$size_qty_prepack $lbl_polybaginfo</td>";
					$total_prepack += $size_qty_prepack;
					$this_blisterbag = $blisterbag_in_carton;
					
					//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
					//====> Able Pack Carton, record size detail carton in database <====//
					//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//
					if($str_notice!="Cannot pack" && $size_qty_prepack>0){
						$str_start = $str_carton_num;
						$str_end = $str_carton_num;
						//echo "$str_start - $str_end // $shipmentpriceID // $PID // $size //$group_number // $size_qty_prepack  || $a - $b <br/>";
						$this->update_carton_num_detail($str_start, $str_end, $shipmentpriceID, $PID, $size, $group_number, $size_qty_prepack);
					}
					
				}else{
					$size_qty = 0;
					$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff;'></td>";
				}
				
			}//---- End For Loop ----//


			$html .= "<td class='topcolortd leftcolortd' style='background-color:#fff;'>$total_prepack</td>";
			// $html .= "<td class='topcolortd leftcolortd' style='background-color:#fff;'>".$polybag_qty_in_blisterbag."</td>";
			
			// modified by jx 20181227 1048
			$html .= "<td class='topcolortd leftcolortd' 
							style='background-color:#fff;'>".($arr_leftover[$group_number] / $total_prepack)." $lbl_blisterbaginfo</td>";

			if($countRow == 0){
				$qty_in_last_carton = array_sum($arr_leftover);
				$grand_total_qty += $qty_in_last_carton;
				
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;">'.$qty_in_last_carton.'</td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;">'.$qty_in_last_carton.'</td>';
				
				//Modified by ckwai on 20181010 1446
				$size_name = ""; $arr_size_qty = array();
				list($str_measurement, $ext_CBM, $pack_factor, $ctn_weight, $gmt_pcs_weight, $str_length, $str_width, $str_height) = 
										$this->funcGetCartonMeasurement($soID, $PID, $pack_method, $size_name, 
																		$total_prepack, $is_standard, $prepack_name,0, $packing_type, $arr_size_qty);
				//$total_carton_CBM = $ext_CBM * $carton_number;
				$single_carton_CBM = round(($str_length/100) * ($str_width/100) * ($str_height/100), 3);
				$total_carton_CBM = $single_carton_CBM * $carton_number;// modified by ckwai on 202007021138
				$total_carton_CBM = round($total_carton_CBM, 3);
				$acc_weight = $this->funcGetAccWeight($soID);
				
				$this_netnet_weight = $qty_in_last_carton * $gmt_pcs_weight;
				$this_net_weight = $this_netnet_weight + $acc_weight;
				$this_gross_weight = $ctn_weight + $this_net_weight;
				
				//convert to LBS if is 41 (2018-10-05 w)
				$unit = $this->check_kg_lbs($soID, $PID, $is_standard, $packing_type);
				$wg_unit  = $this->wg_unit;
				$str_unit = "KG";
				if(($unit == 41 && $wg_unit==0) || $wg_unit==57){ //inch use lbs and cm use KGS due to Joe Fresh Order needed on 2020-11-17
					$this_netnet_weight = $this_netnet_weight * 2.204622622;
					$this_net_weight = $this_net_weight * 2.204622622;
					$this_gross_weight = $this_gross_weight * 2.204622622;
					$str_unit = "LBS";
				}
				//convert to one decimal (2018-10-05 w)//request by shipping rithy, 202202221036
				$this_netnet_weight = round($this_netnet_weight, 3); //$this->to_one_dec($this_netnet_weight);
				$this_net_weight    = round($this_net_weight, 3); //$this->to_one_dec($this_net_weight);
				$this_gross_weight  = round($this_gross_weight, 3); //$this->to_one_dec($this_gross_weight);
				
				$display_nnw = $this_netnet_weight * ($carton_qty - $temp_carton_qty);
				$display_nw = $this_net_weight * ($carton_qty - $temp_carton_qty);
				$display_gw = $this_gross_weight * ($carton_qty - $temp_carton_qty);
				
				$grand_nnw += $display_nnw;
				$grand_nw += $display_nw;
				$grand_gw += $display_gw;
				
				//>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<//
				//====> Able Pack Carton, record in database <====//
				//>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<//
				if($str_notice!="Cannot pack" && $is_pdf==false){
					$str_start = TRIM($str_carton_num);
					$str_end = TRIM($str_carton_num);
					$this_grp = 0;
					$total_qty_in_carton = $qty_in_last_carton;
					$qty_in_blisterbag = $total_prepack;
					$this_blisterbag = $blisterbag_in_carton;
					$is_last = 0;
					//echo "[head] $str_start - $str_end || $shipmentpriceID - $PID - $this_grp - $str_length - $str_width - $str_height <br/>";
					$this->update_carton_num_head($str_start, $str_end, $shipmentpriceID, $PID, $this_grp,
											$qty_in_blisterbag, $this_blisterbag, $total_qty_in_carton, $this_netnet_weight, $this_net_weight,
											$this_gross_weight, $str_measurement, $ext_CBM, $is_last, $SKU, 
											$str_length, $str_width, $str_height);

				}
				// else{
					// $total_carton_CBM = "0.00";
					// $str_measurement = "0.00 x 0.00 x 0.00";
					// $this_netnet_weight = "0.00";
					// $this_net_weight = "0.00";
					// $this_gross_weight = "0.00";
				// }
				
				// net weight, carton measurement, ...
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;"> '.$display_nnw.' '.$str_unit.'</td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;"> '.$display_nw.' '.$str_unit.'</td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;"> '.$display_gw.' '.$str_unit.'<br/><small><i><font color="blue">'.$this_gross_weight.' '.$str_unit.'/ per ctn</font></i></small></td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;"> '.$str_measurement.' </td>';
				$html .= '<td class="topcolortd leftcolortd" rowspan="'.$this_rowspan.'" style="background-color:#fff;"> '.$total_carton_CBM.' </td>';
				$grand_total_cbm += $total_carton_CBM;
			}

			$html .= "</tr>";
			$countRow++;
		}
		
	}
	
	list($this_html) = $this->funcLastTotalDisplay($grand_total_qty, $soID, $num_column, $grand_total_cbm, $grand_nnw, $grand_nw, $grand_gw, $str_unit);
	$html .= $this_html;

	$html .= "</table><br/><br/> ";

	return array($html, $start_carton);
}

public function calculateCarton_MultiColorRatioPack($PID, $is_polybag, $is_blisterbag, $start_carton, $mode='0'){
	$from_location = $this->from_location;
	if($mode == '0'){
		$sql = "SELECT spd.group_number , spd.total_qty, spd.blisterbag_in_carton, spd.polybag_qty_in_blisterbag, spd.ratio_qty  
				, SUM(spd.ratio_qty*spd.gmt_qty_in_polybag) as prepack_qty
				-- , SUM(spd.ratio_qty) as color_subtotal_qty 
					FROM tblship_packing_detail$from_location spd 
					WHERE spd.PID='$PID' 
					GROUP BY spd.group_number ";
		$res = $this->conn->query($sql);

		//$start_carton = 1;
		$end_carton = $start_carton;

		$arr_subtotal = array();
		$arr_total = array();
		$arr_differ = array();
		$arr_leftover = array(); // to store how many qty cannot be packed.

		$blisterbag_in_carton = 0;

		while($row = $res->fetch(PDO::FETCH_BOTH)){
			$group_number = $row['group_number'];
			$total_qty = $row['total_qty'];
			$blisterbag_in_carton = $row['blisterbag_in_carton'];
			
			$prepack_qty = $row['prepack_qty'];
			$polybag_qty_in_blisterbag = $row['polybag_qty_in_blisterbag'];

			// the garment qty in carton?
			$this_gmt_qty_in_carton = $prepack_qty * $polybag_qty_in_blisterbag;
			
			if($this_gmt_qty_in_carton>0){
				$arr_total[$group_number] = $total_qty;
				$arr_subtotal[$group_number]= $this_gmt_qty_in_carton;
			}
			
		}//-- End While --//

		// find how much qty of carton used to be pack 
		// find the smallest one
		$min_carton_used = 0;
		
		foreach($arr_total as $i=>$value){ // modified by ckwai on 201907191121
		//for ($i=1; $i <= count($arr_total) ; $i++) {
			if($arr_subtotal[$i] <= 0)
				continue;

			$temp_differ = floatval( $arr_total[$i] ) / floatval( $arr_subtotal[$i] );
			if($min_carton_used == 0 ){
				$min_carton_used = $temp_differ;
			}else{
				if($temp_differ>0 && $temp_differ<$min_carton_used){
					$min_carton_used = $temp_differ;
				}
			}
		}
		
		// calculate how many carton need to used
		$total_ctn_used = floor(intval($min_carton_used)); 
		$end_carton = floor(intval($min_carton_used)) + $start_carton - 1;
		$carton_number = "$start_carton - $end_carton ";
		$start_carton = $end_carton + 1;
		
		for ($i=1; $i <= count($arr_total) ; $i++) { 
			if($arr_subtotal[$i] <= 0)
				continue;

			$temp_leftover = floatval( $arr_total[$i] ) - ( $total_ctn_used * $arr_subtotal[$i] );
			$arr_leftover[$i] = $temp_leftover;
			
		}
		/*echo "<pre>";
		print_r($arr_subtotal);
		echo "</pre>";*/
		return array($carton_number, $total_ctn_used, $arr_leftover, $start_carton);
	}
	
}


public function checkStandardName($tmode){
	$lang = $this->lang;
	
	$path = "../lang/{$lang}.php";
	$path2 = "../../lang/{$lang}.php";
	$chk = file_exists($path);
	$url = ($chk==1? $path: $path2);
	
	include($url);
	$str_standard = "";
	switch($tmode){
		case 0:$str_standard = "Buyer ".$hdlang["standard"];break;
		case 1:$str_standard = $hdlang["non_standard"];break;
		case 2:$str_standard = "Buyer ".$hdlang["standard_online"];break;
	}
	return $str_standard;
}

public function checkCartonName($is_gap, $pack_factor, $qty_in_carton, $balpack){
	$str_pack_name = "";
	
	switch($is_gap){
		//--- Balance ---//
		case 0: break;
		//--- GAP ---//
		case 1: 
				$g8s = floor($pack_factor * 66.67 / 100);
				$g8m = floor($pack_factor * 25 / 100);
				
				if($qty_in_carton==$pack_factor){
					$str_pack_name = "G8";
				}
				else if($qty_in_carton==$g8s){
					$str_pack_name = "G8S";
				}
				else if($qty_in_carton==$g8m){
					$str_pack_name = "G8M";
				}
				
				break;
		//--- GAP Online ---//
		case 2: 
				$GID2 = floor($pack_factor * 72 / 100);
				$GID4 = floor($pack_factor * 44 / 100);
				$GID3 = floor($pack_factor * 32 / 100);
				if($qty_in_carton==$pack_factor){
					$str_pack_name = "GID1";
				}
				else if($qty_in_carton==$GID2){
					$str_pack_name = "GID2";
				}
				else if($qty_in_carton==$GID4){
					$str_pack_name = "GID4";
				}
				else if($qty_in_carton==$GID3){
					$str_pack_name = "GID3";
				}
				break;
	}
	
	if($this->buyerID!="B12"){ //GAP
		$str_pack_name = "";
	}
	
	return $str_pack_name;
}

public function checkBalanceCartonQty($is_gap, $pack_factor, $balance_qty, $balpack, $is_last,
										$shipmentpriceID, $is_standard, $PID, $packing_type, $this_packing_method, $this_size){
	$carton_qty = 0; $this_qty_in_carton = 0;
	switch($is_gap){
		//--- Balance ---//
		case 0: 
				if($balpack>0){
					$carton_qty = floor($balance_qty/$balpack);
				}
				$this_qty_in_carton = $balpack;
				//echo " $balance_qty - ($balpack * $carton_qty) // $balance_qty/$balpack<br/>";
				$this_balance_qty = $balance_qty - ($balpack * $carton_qty);
				$str_pack_name = "";
				break;
		//--- GAP ---//
		case 1: 
				$g8s = $this-> funcGetStandardManual($shipmentpriceID, $is_standard, $PID, $packing_type, $this_packing_method, 
													$this_size, "G8S", $pack_factor);
				$g8m = $this-> funcGetStandardManual($shipmentpriceID, $is_standard, $PID, $packing_type, $this_packing_method, 
													$this_size, "G8M", $pack_factor);
													
				//$g8s = floor($pack_factor * 66.67 / 100);//34
				//$g8m = floor($pack_factor * 25 / 100);//13
				if($balance_qty>$g8s && $is_last=="last"){
					$this_balance_qty = 0;
					$str_pack_name = "G8";
				}
				else if($balance_qty>=$g8s){
					$carton_qty = ($g8s==0? 0: floor($balance_qty/$g8s));
					$this_qty_in_carton = $g8s;
					$this_balance_qty = $balance_qty - ($g8s * $carton_qty);
					$str_pack_name = "G8S";
					if($is_last=="first"){
						$carton_qty =0 ;
						$this_balance_qty = $balance_qty;
					}
				}
				else if($balance_qty>=$g8m && $balance_qty<$g8s){
					$carton_qty = floor($balance_qty/$g8m);
					$this_qty_in_carton = $g8m;
					//echo "$this_balance_qty = $balance_qty - ($g8m * $carton_qty) --> $this_qty_in_carton<br/>";
					$this_balance_qty = $balance_qty - ($g8m * $carton_qty);
					$str_pack_name = "G8M"; // <br/>g8s: $g8s<br/>g8m:$g8m
					if($is_last=="last"){
						$str_pack_name = "G8S";
					}
					else if($is_last=="first"){
						$carton_qty =0 ;
						$this_balance_qty = $balance_qty;
					}
				}
				else{
					// $carton_qty = 1;
					// $this_qty_in_carton = $balance_qty;
					// $this_balance_qty = 0;
					$str_pack_name = "G8M";
					
					$this_balance_qty = $balance_qty;
				}
				break;
		//--- GAP Online ---//
		case 2: 
				$GID2 = $this-> funcGetStandardManual($shipmentpriceID, $is_standard, $PID, $packing_type, $this_packing_method, 
													$this_size, "GID2", $pack_factor);
				$GID4 = $this-> funcGetStandardManual($shipmentpriceID, $is_standard, $PID, $packing_type, $this_packing_method, 
													$this_size, "GID4", $pack_factor);
				$GID3 = $this-> funcGetStandardManual($shipmentpriceID, $is_standard, $PID, $packing_type, $this_packing_method, 
													$this_size, "GID3", $pack_factor);
				// $GID2 = floor($pack_factor * 72 / 100);
				// $GID4 = floor($pack_factor * 44 / 100);
				// $GID3 = floor($pack_factor * 32 / 100);
				$g8m = floor($pack_factor * 25 / 100);
				if($balance_qty>$GID2 && $is_last=="last"){
					$this_balance_qty = 0;
					$str_pack_name = "GID1";
				}
				else if($balance_qty>=$GID2){	
					$carton_qty = floor($balance_qty/$GID2);
					$this_qty_in_carton = $GID2;
					$this_balance_qty = $balance_qty - ($GID2 * $carton_qty);
					$str_pack_name = "GID2";
					
					if($is_last=="first"){
						$carton_qty =0 ;
						$this_balance_qty = $balance_qty;
					}
				}
				else if($balance_qty>=$GID4){
					$carton_qty = floor($balance_qty/$GID4);
					$this_qty_in_carton = $GID4;
					$this_balance_qty = $balance_qty - ($GID4 * $carton_qty);
					$str_pack_name = "GID4";
					if($is_last=="last"){
						$str_pack_name = "GID2";
					}
					
					if($is_last=="first"){
						$carton_qty =0 ;
						$this_balance_qty = $balance_qty;
					}
				}
				else if($balance_qty>=$GID3){
					$carton_qty = floor($balance_qty/$GID3);
					$this_qty_in_carton = $GID3;
					$this_balance_qty = $balance_qty - ($GID3 * $carton_qty);
					$str_pack_name = "GID3";
					
					if($is_last=="first"){
						$carton_qty =0 ;
						$this_balance_qty = $balance_qty;
					}
				}
				else if($balance_qty<$GID3){
					$carton_qty = 0;
					$this_balance_qty = $balance_qty;
					$str_pack_name = "";
					
					if($is_last=="last"){
						$str_pack_name = "GID3";
					}
				}
				else{
					$carton_qty = 1;
					$this_qty_in_carton = $balance_qty;
					$this_balance_qty = 0;
					$str_pack_name = "GID3";
					
					if($is_last=="first"){
						$carton_qty =0 ;
						$this_balance_qty = $balance_qty;
					}
				}
				break;
	}
	
	if($this->buyerID=="B25"){
		$str_pack_name = "";
	}
	
	//pack factor must be even number (2018-11-02 w)
	//if pack factor decrease by 1, add back amount to balance qty (1 * carton qty) (2018-11-06 w)
	if($this_qty_in_carton % 2 > 0){
		$this_qty_in_carton--;
		$this_balance_qty = $this_balance_qty + $carton_qty;
	}
	//echo "$pack_factor, $balance_qty, $balpack | $carton_qty / $this_qty_in_carton / $this_balance_qty<br/>";
	return array($carton_qty, $this_qty_in_carton, $this_balance_qty, $str_pack_name);
}

public function funcGetStandardManual($shipmentpriceID, $is_standard, $PID, $packing_type, $this_packing_method, $this_size, $standard_name, $pack_factor){
	$this_pf = 1;
	$sql = "SELECT cco.pack_factor, cco.standard_name 
			FROM tblcarton_calculator_head cch 
			INNER JOIN tblcarton_calculator_picklist ccpl ON ccpl.CCHID = cch.CCHID
			INNER JOIN tblcarton_calculator_option cco ON cco.CCPID = ccpl.CCPID
			INNER JOIN tblshipmentprice sp ON sp.Orderno = cch.orderno
			WHERE cch.packing_type='$packing_type' AND sp.ID = '$shipmentpriceID' AND cch.pack_factor_method=1 AND ccpl.PID='$PID'
			AND ccpl.packing_method='$this_packing_method' AND ccpl.size_name='$this_size' 
			AND ccpl.is_standard = '$is_standard' AND cco.standard_name ='$standard_name'";
	$columnsql2 = $this->conn->prepare($sql);
	$columnsql2->execute();	
	$count = $columnsql2->rowCount();
	if($count>0){
		$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
			$this_pf = $row["pack_factor"];
	}
	else{
		$sql = "SELECT cco.pack_factor, cco.standard_name 
			FROM tblcarton_calculator_head cch 
			INNER JOIN tblcarton_calculator_picklist ccpl ON ccpl.CCHID = cch.CCHID
			INNER JOIN tblcarton_calculator_option cco ON cco.CCPID = ccpl.CCPID
			INNER JOIN tblshipmentprice sp ON sp.Orderno = cch.orderno
			WHERE cch.packing_type='$packing_type' AND sp.ID = '$shipmentpriceID' AND cch.pack_factor_method=1 AND ccpl.PID='0'
			AND ccpl.packing_method='$this_packing_method' AND ccpl.size_name='$this_size' 
			AND ccpl.is_standard = '$is_standard' AND cco.standard_name ='$standard_name'";
		$columnsql2 = $this->conn->prepare($sql);
		$columnsql2->execute();	
		$count = $columnsql2->rowCount();
		if($count>0){
			$row=$columnsql2->fetch(PDO::FETCH_ASSOC);
				$this_pf = $row["pack_factor"];
				//echo "$this_size ==> $this_pf <br/>";
		}else{
			switch($standard_name){
				case "GID2" : $this_pf =  floor($pack_factor * 72 / 100);break;
				case "GID4" : $this_pf = floor($pack_factor * 44 / 100);break;
				case "GID3" : $this_pf = floor($pack_factor * 32 / 100);break;
				case "G8S" : $this_pf = floor($pack_factor * 66.67  / 100);break;
				case "G8M" : $this_pf = floor($pack_factor * 25 / 100);break;
			}
		}
	}
	
	//echo "$this_pf <====== <br/>";
	
	return $this_pf;	
}

public function getComboOrSingleColorNameDisplay($shipmentpriceID, $group_number, $color_type, $mode){
	$color_name = "";
	//--- Combo Color ---//
	if($color_type==0){
		$is_group = "1";
	}
	//--- Single Color ---//
	else{
		$is_group = "0";
	}
	
	//---- If mode is get color name ----//
	// modified by SL , add in tblcolorsizeqty table to joinn GTN color name
	if($mode==0){
		$sql = "SELECT g.styleNo as styling, c.ColorName as color ,csq.GTN_colorname
					FROM tblship_group_color sgc
					INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
					INNER JOIN tblcolor c ON c.ID = sgc.colorID
					inner join tblcolorsizeqty csq On csq.colorID = sgc.colorID and csq.garmentID = g.garmentID
					WHERE sgc.shipmentpriceID='$shipmentpriceID' AND sgc.group_number='$group_number' 
					AND sgc.statusID='1' AND sgc.is_group='$is_group' 
					group by sgc.colorID, sgc.garmentID
					order by g.garmentID, c.ID";
			$columnsql2 = $this->conn->prepare($sql);
			$columnsql2->execute();	
			while($row=$columnsql2->fetch(PDO::FETCH_ASSOC)){
				$styling = $row["styling"];
				$color = $row["color"];
				$alias_colorName = $row["GTN_colorname"];
				
				$color_name .= "<font size='1px'><i>$alias_colorName</i></font> &nbsp; $color ($styling)<br/>";
			}//----- End While -----//
		
		return $color_name;
	}
	//---- If mode is get color ID
	else{
		$sql = "SELECT g.styleNo as styling, c.ColorName as color, sgc.colorID ,csq.GTN_colorname
					FROM tblship_group_color sgc
					INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
					INNER JOIN tblcolor c ON c.ID = sgc.colorID
					inner join tblcolorsizeqty csq On csq.colorID = sgc.colorID and csq.garmentID = g.garmentID
					WHERE sgc.shipmentpriceID='$shipmentpriceID' AND sgc.group_number='$group_number' 
					AND sgc.statusID='1' AND sgc.is_group='$is_group' 
					group by sgc.colorID
					order by g.garmentID, c.ID limit 1";
			$columnsql2 = $this->conn->prepare($sql);
			$columnsql2->execute();	
			while($row=$columnsql2->fetch(PDO::FETCH_ASSOC)){
				$colorID = $row["colorID"];
			}//----- End While -----//
		
		return $colorID;
	}
}

public function chkTickedOfPolyBlisterBag($pack_method, $is_polybag, $is_blisterbag){
	$str_title = "";
	$scss_1st = "";//$pack_method / $is_polybag / $is_blisterbag
	$scss_2nd = "";
	$scss_3rd = "";
	$scss_4th = "";
	
	if($is_polybag==0 && $is_blisterbag==1){
		switch($pack_method){
			case 1:$scss_1st = "# of Prepack in 1 Blister Bag";
				   $scss_2nd = "# of Blister in 1 Carton";
					break;
			case 2:$scss_1st = "# of Prepack in 1 Blister Bag";
				   $scss_2nd = "# of Blister in 1 Carton";
					break;
			case 50:$scss_1st = "Ratio Qty";
					$scss_2nd = "Ratio Pack";// $scss_2nd = "Total Blister bag <br>in 1 carton";
					break;
		}
	}
	else if($is_polybag==1 && $is_blisterbag==0){
		switch($pack_method){
			case 1:$scss_1st = "# of Prepack";
				   $scss_2nd = "# of Ratio Pack <br/>in 1 Carton";
					break;
			case 2:$scss_1st = "# of gmt <br/>in 1 Polybag";
				   $scss_2nd = "# of Polybag <br/>in 1 Carton";
					break;
			case 50:$scss_1st = "";
					// $scss_2nd = "# of Prepack in 1 Carton";
					break;
		}
	}
	else if($is_polybag==0 && $is_blisterbag==0){
		switch($pack_method){
			case 1:$scss_1st = "# of Prepack";
				   $scss_2nd = "# of Ratio Pack in 1 Carton";
					break;
			case 2:$scss_1st = "# of Prepack";
				   $scss_2nd = "# of Prepack in 1 Carton";
					break;
			case 50:$scss_1st = "Ratio Qty";
				    $scss_2nd = "Ratio Pack";
					break;
		}
	}
	else if($is_polybag==1 && $is_blisterbag==1){
		switch($pack_method){
			case 1:$scss_1st = "# of Prepack in 1 Blister Bag";
				   $scss_2nd = "# of Blister in 1 Carton";
					break;
			case 2:$scss_1st = "# of Polybag <br/>in 1 Blister Bag";
				   $scss_2nd = "# of Blister in 1 Carton";
					break;
			case 50:$scss_1st = "Ratio Pack";break;
		}
	}
	
	return array($scss_1st, $scss_2nd, $scss_3rd, $scss_4th);
}

public function updateMaterialBuyerPO($orderno, $new_shipmentpriceID){
	
	$apsql = $this->conn->prepare("SELECT ap.APID, ap.shipmentpriceID, ac.Description AS acontent, sb.Description AS asubtype 
							FROM tblapurchase AS ap
							LEFT JOIN tblasubtype AS sb ON sb.ID = ap.AsubtypeID
							LEFT JOIN tblamaterial AS am ON am.AMID = ap.AMID
							LEFT JOIN tblacontent AS ac ON ac.ID = am.contentID
							WHERE ap.orderno=:orderno");
	$apsql->bindParam(':orderno', $orderno);
	$apsql->execute();
	while($apcode = $apsql->fetch(PDO::FETCH_ASSOC)){
		$APID = $apcode['APID'];
		$apshipmentpriceID = $apcode['shipmentpriceID'];
		$ship_arr = explode(",", $apshipmentpriceID);
		array_push($ship_arr, "$new_shipmentpriceID");
		
		$this_shipmentpriceID = implode(",", $ship_arr);
		$update_sql = $this->conn->prepare("UPDATE tblapurchase SET shipmentpriceID = :shipmentpriceID WHERE APID = :APID");
		$update_sql->bindParam(':shipmentpriceID', $this_shipmentpriceID);
		$update_sql->bindParam(':APID', $APID);
		$update_sql->execute();
	
	}
	
	$fpsql = $this->conn->prepare("SELECT mp.MPID, mp.shipmentpriceID
								  mmd.FabricContent, ft.Description, mmd.mmcode,
								  mmd.min_weight_gm, mmd.max_weight_gm, mp.allSize,
								  mmd.min_weight_yard, mmd.max_weight_yard,
								  mmd.InternalWidth, mmd.ExternalWidth,mmd.multiplier, mmd.basic_unit, mmd.Measurement,
								  mmd.TopYarn, mmd.BottomYarn, mmd.Shrinkage, mp.positionID, mp.dozPcs, ft.Description,
								  mp.unitprice, mp.currencyID, mp.garmentID
								  
								  FROM tblmpurchase mp
								  inner join tblmm_detail mmd on mp.MMID = mmd.MMID
								  inner join tblfabtype ft on mmd.FabricTypeID = ft.ID
								  INNER JOIN tblposition AS pp ON pp.ID = mp.positionID
								  WHERE mp.orderno = :orderno");
	$fpsql->bindParam(':orderno', $orderno);
	$fpsql->execute();
	while($apcode = $fpsql->fetch(PDO::FETCH_ASSOC)){
		$MPID = $apcode['MPID'];
		$apshipmentpriceID = $apcode['shipmentpriceID'];
		$fship_arr = explode(",", $apshipmentpriceID);
		array_push($fship_arr, "$new_shipmentpriceID");
				
		$this_shipmentpriceID = implode(",", $fship_arr);
		//echo "$shipmentpriceID<br/>";
		
		$update_sql = $this->conn->prepare("UPDATE tblmpurchase SET shipmentpriceID = :shipmentpriceID WHERE MPID = :MPID");
		$update_sql->bindParam(':shipmentpriceID', $this_shipmentpriceID);
		$update_sql->bindParam(':MPID', $MPID);
		$update_sql->execute();
	}
	
}
// ================================= Added by SL 22 June 2018
public function updateMasterPO($masterpo,$acctid){

	$now=date("Y-m-d H:i:s");

    //echo '>>'.$masterpo;
    $master_query="select sp.GTN_buyerpo , GROUP_CONCAT(DISTINCT spd.PID) as PID, 
                        GROUP_CONCAT(DISTINCT spd.group_number) as group_number,
                        GROUP_CONCAT(DISTINCT spd.size_name) as sizeName,
                        GROUP_CONCAT(DISTINCT gc.colorID) as colorID
                    from tblshipmentprice sp 
                    inner join tblship_packing tsp ON tsp.shipmentpriceID = sp.ID
                    inner join tblship_packing_detail spd ON spd.PID = tsp.PID and (spd.total_qty>0 or spd.ratio_qty>0)
                    inner join tblship_group_color gc On gc.shipmentpriceID = sp.ID and gc.group_number = spd.group_number
                    where sp.ID='$masterpo' and sp.statusID=1 and tsp.statusID=1 and spd.statusID=1";     
    // echo $master_query.'<br>';                        
    $result = $this->conn->query($master_query);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    $GTNpo = $row["GTN_buyerpo"];
    $PID = $row["PID"];
    $group_number = $row["group_number"];
    $sizeName = $row["sizeName"];
    $orderno=$row["Orderno"];
    $colorID = $row["colorID"];

    $arr_groupNUmber = explode(',', $group_number);
    $arr_colorID = explode(',', $colorID);
    $arr_sizeName = explode(',', $sizeName);

    print_r($arr_groupNUmber);
    print_r($arr_sizeName);

    //echo 'GTNPO>>>>'.$GTNpo .'>>>'.$PID.'<br>';
    if ($GTNpo==''){ // MasterPO not from GTN, only deduct by None size Qty, Modified by SL 24 Jun 2018
		for($a=0; $a<sizeof($arr_groupNUmber); $a++){
            $loop_query="select sp.ID,spd.group_number, sgc.garmentID, sgc.colorID, 
      						CASE WHEN tsp.packing_method=1 THEN 
								sum(spd.total_qty)/ sum(spd.polybag_qty_in_blisterbag) 
							ELSE 
								sum(spd.total_qty) 
							end total_qty 
						from tblship_packing_detail spd
						inner join tblship_packing tsp ON tsp.PID = spd.PID and tsp.statusID=1
						inner join tblshipmentprice sp ON sp.ID = tsp.shipmentpriceID and sp.statusID=1
						inner join tblship_group_color sgc ON sgc.shipmentpriceID = sp.ID and sgc.group_number = spd.group_number 
						where sp.tmp_GTNMasterpo='$masterpo' and spd.group_number=$arr_groupNUmber[$a] and spd.statusID=1 and sgc.statusID=1
						group by spd.PID,tsp.packing_method,spd.group_number"; 
            //echo $loop_query.'<br>';                            
            $re_1=$this->conn->query($loop_query);
            while($row_1=$re_1->FETCH(PDO::FETCH_ASSOC)){

            	$ID =$row_1["ID"];
            	$colorID = $row_1["colorID"];
            	$garmentID = $row_1["garmentID"];
            	$packing_method=$row_1["packing_method"];
                $ratio_qty1 = $row_1["ratio_qty"];
                $total_qty2 = $row_1["total_qty"];
                
                $ratioQty =($ratio_qty>0)? $ratio_qty1 : $total_qty2;

                //1. update ratio_qty
                $update_query="update tblship_packing_detail 
                                SET     ratio_qty = GREATEST(0, ratio_qty-$ratioQty),
                                        gmt_qty_in_polybag = GREATEST(1,gmt_qty_in_polybag-$total_qty2),
                                        polybag_qty_in_blisterbag = GREATEST(0, polybag_qty_in_blisterbag-$total_qty2),
                                        total_qty = GREATEST(0,total_qty-$total_qty2)
                                where group_number = $arr_groupNUmber[$a] and PID =$PID";
                //echo $update_query.'<br>';
                $query = $this->conn->exec($update_query);           

                $update_query2 = "update tblship_colorsizeqty 
                						SET qty = GREATEST(0,qty-$total_qty)
                					where garmentID=$garmentID and colorID =$colorID and shipmentpriceID=$masterpo";
				$query = $this->conn->exec($update_query2);  
            }
        }    
    }else{ // deduct qty from GTN PO, Added by SL 24 June 2018

    	echo "<br> got Master <br>";

    	for($a=0; $a<sizeof($arr_groupNUmber); $a++){
    		for($b=0;$b<sizeof($arr_sizeName); $b++){
			$loop_query="select sp.ID,spd.group_number, sgc.garmentID, sgc.colorID, 
      						CASE WHEN tsp.packing_method=1 THEN 
								sum(spd.total_qty)/ sum(spd.polybag_qty_in_blisterbag) * spd.ratio_qty
							ELSE 
								sum(spd.total_qty) 
							end total_qty 
						from tblship_packing_detail spd
						inner join tblship_packing tsp ON tsp.PID = spd.PID 
						inner join tblshipmentprice sp ON sp.ID = tsp.shipmentpriceID and sp.statusID=1
						inner join tblship_group_color sgc ON sgc.shipmentpriceID = sp.ID and sgc.group_number = spd.group_number 
						where sp.tmp_GTNMasterpo='$masterpo' 
						and spd.group_number=$arr_groupNUmber[$a] and spd.size_name='$arr_sizeName[$b]'
						and spd.statusID=1 and tsp.statusID=1 and sgc.statusID=1
						group by spd.PID,tsp.packing_method,spd.group_number"; 
            echo '====<br>'.$loop_query.'<br>';                            
            $re_1=$this->conn->query($loop_query);
            while($row_1=$re_1->FETCH(PDO::FETCH_ASSOC)){

            	$ID =$row_1["ID"];
            	$colorID = $row_1["colorID"];
            	$garmentID = $row_1["garmentID"];
            	$packing_method=$row_1["packing_method"];
                $ratio_qty1 = $row_1["ratio_qty"];
                $total_qty = $row_1["total_qty"];
                
                $ratioQty =($ratio_qty>0)? $ratio_qty1 : $total_qty;

                //1. update ratio_qty
                $update_query="update tblship_packing_detail 
                                SET     ratio_qty = GREATEST(0, ratio_qty-$ratioQty),
                                        gmt_qty_in_polybag = GREATEST(1,gmt_qty_in_polybag-$total_qty),
                                        polybag_qty_in_blisterbag = GREATEST(0, polybag_qty_in_blisterbag-$total_qty),
                                        total_qty = GREATEST(0,total_qty-$total_qty)
                                where group_number = $arr_groupNUmber[$a] 
                                and PID =$PID and size_name='$arr_sizeName[$b]'";
                echo '<br> '.$update_query.'<br>';
                $query = $this->conn->exec($update_query);           

                $update_query2 = "update tblship_colorsizeqty 
                						SET qty = GREATEST(0,qty-$total_qty)
                					where garmentID=$garmentID and colorID =$arr_colorID[$a] and size_name='$arr_sizeName[$b]'
                					and shipmentpriceID=$masterpo";
                echo '<br>'.$update_query2.'<br>';					
				$query = $this->conn->exec($update_query2);  
				}
			}// end for size qty deduction
   		 	}// end for color qty deduction
   	}	

    //$update_query3 = "update tblshipmentprice SET tmp_GTNMasterpo='' where tmp_GTNMasterpo=$masterpo";
	//$query = $this->conn->exec($update_query3);

	
   	$sql_shipcolor="select scsq.shipmentpriceID, sum(scsq.qty) as sumQty
						from tblship_colorsizeqty scsq 
					where scsq.statusID=1 and scsq.shipmentpriceID=$masterpo
					group by scsq.shipmentpriceID";
	$re_shipcolor=$conn->query($sql_shipcolor);
	$r_row=$re_shipcolor->FETCH(PDO::FETCH_ASSOC);

	$shipmentpriceID = $r_row["shipmentpriceID"];
	$sumQty = $r_row["sumQty"];

	if ($sumQty=0){
		$update_query4="update tblshipmentprice sp 
						set sp.statusID=2, 
						sp.updatedby=$acctid,
						sp.updateddate='$now'
						where sp.statusID=1 and sp.ID='$masterpo'";
	echo "<br> ======== Got Zero value ========= '<br>'";
	//echo '<br>'.$update_query4;				
	$query4 = $this->conn->exec($update_query4);
	echo "================================ end '<br>'";
	}

	
	//$this->shipmentback($masterpo, $acctid);

return true;
}
//========================================================================================================================//
//============== Back End Update Whole Pack Factor And Blister & Polybag Qty According Carton Calculator =================//
//========================================================================================================================//
public function updateAllPickListByPackFactor($orderno, $shipmentpriceID=""){
	$from_location = $this->from_location;
	$query_shipment = ($shipmentpriceID==""?"": " AND sp.ID='$shipmentpriceID'");
	$sql = "SELECT spk.shipmentpriceID, spk.PID, spk.packing_method, spk.tmode, sp.grouporcolor 
			FROM tblshipmentprice sp 
			INNER JOIN tblship_packing$from_location spk ON spk.shipmentpriceID = sp.ID
			WHERE sp.orderno = '$orderno' AND sp.statusID='1' AND spk.statusID='1' $query_shipment";
	$columnsql2 = $this->conn->prepare($sql);
	$columnsql2->execute();	
	while($row=$columnsql2->fetch(PDO::FETCH_ASSOC)){
		$shipID = $row["shipmentpriceID"];
		$PID = $row["PID"];
		$packing_method = $row["packing_method"];
		$is_standard = $row["tmode"];
		$grouporcolor = $row["grouporcolor"];
		
		switch($packing_method){
			//--- Single Color Ratio Pack ---//
			case 1:$this->updatePickListOfSingleColorRatioPack($orderno, $shipID, $PID, $is_standard, $grouporcolor);break; 
			//--- Single Color Single Pack ---//
			case 2:$this->updatePickListOfSingleColorSinglePack($orderno, $shipID, $PID, $is_standard, $grouporcolor);break;
			//--- Multi Color Ratio Pack ---//
			case 50:$this->updatePickListOfMultiColorRatioPack($orderno, $shipID, $PID, $is_standard, $grouporcolor);break;
		}
	}
}

public function updatePickListOfSingleColorRatioPack($orderno, $shipID, $PID, $is_standard, $grouporcolor){
	$pack_method = 1;
	$from_location = $this->from_location;
	$sql = "SELECT spd.group_number, spd.polybag_qty_in_blisterbag, spk.packing_type, 
		(SELECT GROUP_CONCAT(spd2.size_name,'-',spd2.ratio_qty separator '_')
         FROM tblship_packing_detail$from_location spd2 WHERE spd2.PID = spk.PID AND spd2.statusID=1
         AND spd2.group_number = spd.group_number AND spd2.ratio_qty>0) as size_range,
		 
		 (SELECT GROUP_CONCAT(DISTINCT spd2.size_name order by spd2.size_name asc) 
									 FROM tblship_packing_detail$from_location spd2
									 WHERE spd2.PID = spk.PID AND spd2.statusID=1 AND spd2.group_number = spd.group_number AND spd2.ratio_qty>0) as grp_size
			FROM tblship_packing$from_location spk 
			INNER JOIN tblship_packing_detail$from_location spd ON spd.PID = spk.PID
			WHERE spk.PID = '$PID' AND spd.statusID = '1'
			group by spd.group_number
            order by spd.ID";
	$result = $this->conn->prepare($sql);
	$result->execute();	
	while($row = $result->fetch(PDO::FETCH_ASSOC)){
		$group_number = $row["group_number"];
		$prepack_qty  = $row["polybag_qty_in_blisterbag"];
		$packing_type = $row["packing_type"];	//add packing_type for funcGetPackFactor() used(2018-10-25 w)
		$size_range   = $row["size_range"];
		$grp_size   = $row["grp_size"];
		
		if($prepack_qty>0){
			$size_name = "";
			$pack_factor = $this->funcGetPackFactor($orderno, $PID, $pack_method, $size_name, $prepack_qty, $is_standard, $packing_type, $grp_size);
			
			$blisterbag_in_carton = $pack_factor / $prepack_qty;
			$blisterbag_in_carton = round($blisterbag_in_carton,0);
			$blisterbag_in_carton = ($pack_factor==0? 1: $blisterbag_in_carton);
			
			//echo "$orderno - [$shipID] $PID - $prepack_qty - $blisterbag_in_carton // $pack_factor<br/>";
			$sql_update = "UPDATE tblship_packing_detail$from_location SET blisterbag_in_carton='$blisterbag_in_carton' 
							WHERE PID='$PID' AND group_number='$group_number' AND statusID='1'";
			$query = $this->conn->exec($sql_update); 
		}	
	}//---- End While ----//
}

public function updatePickListOfSingleColorSinglePack($orderno, $shipID, $PID, $is_standard, $grouporcolor){
	$pack_method = 2;
	$from_location = $this->from_location;
	$sql = "SELECT spd.group_number, spd.gmt_qty_in_polybag, spd.polybag_qty_in_blisterbag, spd.blisterbag_in_carton, spd.size_name, spd.total_qty, spk.packing_type,
					spk.is_polybag, spk.is_blisterbag, spk.is_ctnblister
			FROM tblship_packing$from_location spk 
			INNER JOIN tblship_packing_detail$from_location spd ON spd.PID = spk.PID
			WHERE spk.PID = '$PID' AND spd.statusID = '1'
			group by spd.group_number, spd.size_name
            order by spd.ID, spd.size_name";
	$result = $this->conn->prepare($sql);
	$result->execute();	
	while($row = $result->fetch(PDO::FETCH_ASSOC)){
		$group_number = $row["group_number"];
		$size_name = $row["size_name"];
		$total_qty = $row["total_qty"];
		$is_polybag = $row["is_polybag"];
		$is_blisterbag = $row["is_blisterbag"];
		$is_ctnblister = $row["is_ctnblister"];
		$gmt_qty_in_polybag = ($row["gmt_qty_in_polybag"]==0? 1: $row["gmt_qty_in_polybag"]);
		$polybag_qty_in_blisterbag = ($row["polybag_qty_in_blisterbag"]==0? 1: $row["polybag_qty_in_blisterbag"]);
		$this_blisterbag_in_carton = ($row["blisterbag_in_carton"]==0? 1: $row["blisterbag_in_carton"]);
		$prepack_qty = $gmt_qty_in_polybag * $polybag_qty_in_blisterbag;
		$packing_type = $row["packing_type"];	//add packing_type for funcGetPackFactor() used(2018-10-25 w)
		
		
		
		if($prepack_qty>0 && $total_qty>0){
			$pack_factor = $this->funcGetPackFactor($orderno, $PID, $pack_method, $size_name, $prepack_qty, $is_standard, $packing_type);
			$this_qty    = $gmt_qty_in_polybag * $polybag_qty_in_blisterbag * $this_blisterbag_in_carton;
			
			if($this_qty<>$pack_factor){
				//echo "$gmt_qty_in_polybag x $polybag_qty_in_blisterbag x $blisterbag_in_carton = $pack_factor <==== <br/>";
				$blisterbag_in_carton = $pack_factor / 1;//$prepack_qty;
					//echo "$pack_factor/$prepack_qty = $blisterbag_in_carton ==== ";
				$blisterbag_in_carton = round($blisterbag_in_carton,0);
				$blisterbag_in_carton = (($pack_factor==0 || $blisterbag_in_carton==0)? 1: $blisterbag_in_carton);
				if($blisterbag_in_carton>$total_qty){
					$blisterbag_in_carton = $total_qty / $gmt_qty_in_polybag / $polybag_qty_in_blisterbag;
				}
			}
			else{
				$blisterbag_in_carton = $this_blisterbag_in_carton;
			}
			
			//echo "[$group_number - $size_name] $is_polybag / $is_blisterbag = $blisterbag_in_carton [pf:$pack_factor/$prepack_qty]<br/>";
			
			if($pack_factor>0){
				$this_count = 1;
				$filter_update = "blisterbag_in_carton='$blisterbag_in_carton' ";
				if($is_polybag==0 && $is_blisterbag==1){
					if($this_qty<>$pack_factor){
						$filter_update = " blisterbag_in_carton='1', polybag_qty_in_blisterbag='$blisterbag_in_carton'";
					}
				}
				else if($is_polybag==1 && $is_blisterbag==0){
					if($this_qty<>$pack_factor){
						$filter_update = " gmt_qty_in_polybag='1', blisterbag_in_carton='$blisterbag_in_carton'";
					}
				}
				else if($is_polybag==1 && $is_blisterbag==1){
					$chk_PK = $polybag_qty_in_blisterbag * $this_blisterbag_in_carton;
					if($chk_PK==$pack_factor){
						$blisterbag_in_carton = $polybag_qty_in_blisterbag;
						$this_count = $this_blisterbag_in_carton;
					}
					if($this_qty<>$pack_factor){
						$filter_update = " gmt_qty_in_polybag='1', polybag_qty_in_blisterbag='$blisterbag_in_carton', blisterbag_in_carton='$this_count'";
					}
				}
				
				$sql_update = "UPDATE tblship_packing_detail$from_location SET $filter_update 
								WHERE PID='$PID' AND group_number='$group_number' AND size_name='$size_name' AND statusID='1'";
				$query = $this->conn->exec($sql_update); 
			}
			//echo "[$blisterbag_in_carton] $group_number - $size_name <br/>";
		
		}
	}//---- End While ----//
}

public function updatePickListOfMultiColorRatioPack($orderno, $shipID, $PID, $is_standard, $grouporcolor){
	$pack_method = 50;
	$from_location = $this->from_location;
	$sql = "SELECT sum(spd.ratio_qty) as prepack_qty, spk.PID, spk.packing_type
					FROM tblship_packing_detail$from_location spd 
					INNER JOIN tblship_packing$from_location spk ON spk.PID = spd.PID
					INNER JOIN tblshipmentprice sp ON sp.ID = spk.shipmentpriceID
					WHERE sp.Orderno = '$orderno' AND spk.packing_method='50' 
					AND spk.tmode='$is_standard' AND sp.statusID=1 AND spk.statusID=1 AND spd.statusID=1 AND spk.PID='$PID'
					group by spk.PID, spd.group_number"; //AND spd.polybag_qty_in_blisterbag>0 modified by ckwai on 201907221630
	$result = $this->conn->prepare($sql);
	$result->execute();	
	$total_ratio = 0; $count_color = 0;
	while($row = $result->fetch(PDO::FETCH_ASSOC)){
		$prepack_qty = $row["prepack_qty"];
		$total_ratio += $prepack_qty;
		++$count_color;
		$packing_type = $row["packing_type"];	//add packing_type for funcGetPackFactor() used(2018-10-25 w)
	}
	$size_name = "";
	$pack_factor = $this->funcGetPackFactor($orderno, $PID, $pack_method, $size_name, $total_ratio, $is_standard, $packing_type);
		
		$blisterbag_in_carton = $pack_factor / $total_ratio; //echo "$blisterbag_in_carton = $pack_factor / $total_ratio<br/>";
		$blisterbag_in_carton = round($blisterbag_in_carton,0);
		$blisterbag_in_carton = ($pack_factor==0? 1: $blisterbag_in_carton);
		$bb_bag = $blisterbag_in_carton * $count_color;
		
		if($pack_factor>0){
			$sql_update = "UPDATE tblship_packing_detail$from_location SET polybag_qty_in_blisterbag='$blisterbag_in_carton', blisterbag_in_carton='$bb_bag' 
								WHERE PID='$PID'  AND statusID='1'";
			$query = $this->conn->exec($sql_update); 
			
		}
}

public function funcSyncProdPacking($shipmentpriceID){
	$sql_update = "UPDATE tblship_packing_prod spp
					INNER JOIN tblship_packing spk ON spk.PID = spp.PID
					SET spp.packing_type = spk.packing_type
					WHERE spp.shipmentpriceID = '$shipmentpriceID'";
	$query = $this->conn->exec($sql_update); 
}

public function funcSyncEarliestOrderShipmentDate($orderno){ // added by ckwai on 20191015, ship_saving.php
	$sql = "SELECT sp.Shipdate FROM tblshipmentprice sp 
				WHERE sp.Orderno='$orderno' AND sp.statusID IN (1) 
				order by sp.Shipdate asc limit 1";
	$result = $this->conn->prepare($sql);
	$result->execute();
	$count = $result->rowCount();
	if($count>0){
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$Shipdate = $row["Shipdate"];
		
		$sql_update = "UPDATE tblorder SET ShipmentDate='$Shipdate' WHERE Orderno='$orderno'";
		$query = $this->conn->exec($sql_update); 
	}
}

public function checkColorGroupNumber($shipmentpriceID, $garmentID, $colorID){
	$arr_value = array();
	$arr_color = array();
	$sql = "SELECT sgc.group_number,
			(SELECT count(sgc2.group_number) 
             FROM tblship_group_color sgc2
             WHERE sgc2.group_number = sgc.group_number 
             AND sgc2.statusID=1 AND sgc2.shipmentpriceID = sgc.shipmentpriceID) as count_color 
			FROM tblship_group_color sgc 
			WHERE sgc.shipmentpriceID='$shipmentpriceID' 
			AND sgc.colorID='$colorID' AND garmentID='$garmentID' AND statusID=1
			group by sgc.group_number";
	$result = $this->conn->query($sql);
	$rowCPD = $result->fetch(PDO::FETCH_ASSOC);
		$group_number = $rowCPD["group_number"];
		$count_color = $rowCPD["count_color"];
	
	// $sql_grp = "SELECT garmentID, colorID 
				// FROM tblship_group_color 
				// WHERE shipmentpriceID='$shipmentpriceID' AND group_number='$group_number'
				// AND statusID=1";
	// $result = $this->conn->query($sql);
	// while($rowCPD = $result->fetch(PDO::FETCH_ASSOC)){
		// extract($rowCPD);
		
		// array_push($arr_color, "$garmentID**%%$colorID");
	// }
	
	$arr_value["group_number"] = $group_number;
	$arr_value["count_color"]  = $count_color;
	$arr_value["arr_color"]    = $arr_color;
	
	return $arr_value;
}

public function funcGetCTPATDetail($shipmentpriceID, $this_PID,  $filter_query="", $isIAL=""){
	$arr_size = array();
	$filter_PID = ($this_PID==""? "": " AND spk.PID='$this_PID'");
	//-----------------------------------------------------------//
	//------------- Get all pick list of buyer PO ---------------//
	//-----------------------------------------------------------//	
	$sql = "SELECT spk.PID, spk.packing_method, sp.grouporcolor 
			FROM tblship_packing spk 
			LEFT JOIN tblshipmentprice sp ON sp.ID = spk.shipmentpriceID
			WHERE spk.shipmentpriceID='$shipmentpriceID' AND spk.statusID=1 AND sp.statusID=1 $filter_PID";
	// echo "<pre>$sql</pre>";
	$resultSPK = $this->conn->query($sql);
	while($rowSPK = $resultSPK->fetch(PDO::FETCH_ASSOC)){
		extract($rowSPK);
		$arr_PID_method["$PID"] = $packing_method;
		$this_is_group = ($grouporcolor==0? 1: 0);
	
		//-----------------------------------------------------------//
		//-------- Get shipped qty of each picklist to array --------//
		//-----------------------------------------------------------//		
		$sql = "SELECT cpt.PTID, cpt.PID, cpt.ctn_num, cpt.ctn_range, count(cpt.PTID) as count_range,
						cpt.blisterbag_in_carton
				FROM tblcarton_picklist_transit$isIAL cpt
				LEFT JOIN tblship_packing spk ON spk.PID = cpt.PID AND spk.statusID = 1
				WHERE cpt.PID='$PID' AND cpt.shipmentpriceID='$shipmentpriceID' $filter_query
				group by cpt.PID, cpt.ctn_range";
		// echo "<pre>$sql</pre>";
		$result = $this->conn->query($sql);
		while($row = $result->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			list($start, $end) = explode("-",$ctn_range);
			//echo "$ctn_num [$start] / $ctn_range [Method:$packing_method] PID:$PID [$count_range]<br/>";
			
			switch($packing_method){
				//-- Multi Color Ratio Size Pack --//
				case 50:
						$sqlCPD = "SELECT cptd.size_name, cptd.garmentID, cptd.colorID, cptd.qty,
										(SELECT spd.polybag_qty_in_blisterbag
											FROM tblship_packing_detail spd 
											WHERE spd.PID = cptd.PID AND spd.statusID=1 limit 1) as polybag_qty_in_blisterbag
									FROM tblcarton_picklist_transit_detail$isIAL cptd
									WHERE cptd.shipmentpriceID='$shipmentpriceID' 
									AND cptd.PID='$PID' 
									AND cptd.ctn_num='$start'";
						$resultCPD = $this->conn->query($sqlCPD);
						while($rowCPD = $resultCPD->fetch(PDO::FETCH_ASSOC)){
							extract($rowCPD);
							$total_qty = $qty * $count_range * $polybag_qty_in_blisterbag;
							$key = "$size_name**$garmentID**$colorID";
							if(array_key_exists($key, $arr_size)){
								$arr_size["$size_name**$garmentID**$colorID"] += $total_qty;
							}
							else{
								$arr_size["$size_name**$garmentID**$colorID"] = $total_qty;
							}
							
							//echo "=====> [$size_name] $qty x $count_range = $total_qty";
						}
						//echo "<br/>";
						break;
				//-- Single Color Ratio Size Pack --//
				case 1: 
						
						$sqlCPD = "SELECT cptd.size_name, cptd.garmentID, cptd.colorID, cptd.qty
									FROM tblcarton_picklist_transit_detail$isIAL cptd
									WHERE cptd.shipmentpriceID='$shipmentpriceID' 
									AND cptd.PID='$PID' 
									AND cptd.ctn_num='$start'";
						$resultCPD = $this->conn->query($sqlCPD);
						while($rowCPD = $resultCPD->fetch(PDO::FETCH_ASSOC)){
							extract($rowCPD);
							$total_qty = $qty * $count_range * $blisterbag_in_carton ;// / $count_color
							$key = "$size_name**$garmentID**$colorID**$PID";
							if(array_key_exists($key, $arr_size)){
								$arr_size["$size_name**$garmentID**$colorID"] += $total_qty;
							}
							else{
								$arr_size["$size_name**$garmentID**$colorID"] = $total_qty;
							}
							
							//echo "=====> [$size_name] $qty x $count_range = $total_qty [$count_color]";
						}
						//echo "<br/>";
						break;
				//-- Single Color Single Size Pack --//		
				case 2: $sqlCPD = "SELECT cptd.size_name, cptd.garmentID, cptd.colorID, cptd.qty
									FROM tblcarton_picklist_transit_detail$isIAL cptd
									WHERE cptd.shipmentpriceID='$shipmentpriceID' 
									AND cptd.PID='$PID' 
									AND cptd.ctn_num='$start'";
						// echo "<pre>$sqlCPD</pre>";
						$resultCPD = $this->conn->query($sqlCPD);
						while($rowCPD = $resultCPD->fetch(PDO::FETCH_ASSOC)){
							extract($rowCPD);
							$total_qty = $qty * $count_range;
							$key = "$size_name**$garmentID**$colorID";
							if(array_key_exists($key, $arr_size)){
								$arr_size["$size_name**$garmentID**$colorID"] += $total_qty;
							}
							else{
								$arr_size["$size_name**$garmentID**$colorID"] = $total_qty;
							}
							//echo "=====> [$ctn_range][$size_name] $qty x $count_range = $total_qty &nbsp; &nbsp; &nbsp; <br/>";
						}
						break;
			}
			
		}//--- End While Inner (Actual Shiped Qty) ---//
	}
	
	
	return $arr_size;
}

public function funcGetCTPATDetailLastCarton($shipmentpriceID, $filter_query="", $isIAL=""){
	$arr_size = array();
	
	//-----------------------------------------------------------//
	//----- Get Last Carton manual key in garment color qty -----//
	//-----------------------------------------------------------//
	$sql = "SELECT cpt.PTID, cpt.PID, cpt.ctn_num, cpt.ctn_range, cpt.total_qty_in_carton
				FROM tblcarton_picklist_transit$isIAL cpt
				WHERE cpt.PID='0' AND cpt.shipmentpriceID='$shipmentpriceID' $filter_query
				"; // LEFT JOIN tblcarton_picklist_transit_detail cptd ON cptd.PTID = cpt.PTID //group by cpt.PID, cpt.ctn_range, cptd.size_name
	// echo "<pre>$sql</pre>";
	$result = $this->conn->query($sql);
	while($rowCPD = $result->fetch(PDO::FETCH_ASSOC)){
		extract($rowCPD);
		
		$sqlCPTD = "SELECT cptd.size_name, cptd.garmentID, cptd.colorID, cptd.qty 
					FROM tblcarton_picklist_transit_detail$isIAL cptd 
					WHERE cptd.PTID='$PTID' AND cptd.shipmentpriceID='$shipmentpriceID'";
		$resultCPTD = $this->conn->query($sqlCPTD);
		$count_CPTD = $resultCPTD->rowCount();
		while($rowCPTD = $resultCPTD->fetch(PDO::FETCH_ASSOC)){
			extract($rowCPTD);
			
			$key = "$size_name**$garmentID**$colorID";
			$qty = ($count_CPTD==1? $total_qty_in_carton: $qty);
			
			if(array_key_exists($key, $arr_size)){
				$arr_size["$size_name**$garmentID**$colorID"] += $qty;
			}
			else{
				$arr_size["$size_name**$garmentID**$colorID"] = $qty;
			}
			
		}//--- End While CPTD ---//
	}//--- End While CPT ---//
	
	return $arr_size;
}

public function checkLCAssignmentExist($filter_query){
	$sql = "SELECT * 
			FROM tbllc_assignment_head lch
			INNER JOIN tbllc_assignment_info lci ON lci.LCHID = lch.LCHID
			INNER JOIN tbllc_assignment_detail lcd ON lci.LCIID = lcd.LCIID
			INNER JOIN tblship_packing spk ON spk.PID = lcd.PID
			WHERE lch.statusID!=6 AND lci.del=0 AND lcd.del=0 $filter_query";
	$result = $this->conn->query($sql);
	$count = $result->rowCount();
	
	$lbl_lc_assignment = ($count>0? '<label class="label label-warning">LC Assignment</label>': "");
	
	return "$lbl_lc_assignment";
}

public function getGarmentWeightFromCalculator($orderno, $this_tmode, $packing_type){
	$arr_size_weight = array();
	
	$sql = "SELECT ccs.size_name, ccs.gmt_pcs_weight
			FROM tblcarton_calculator_sizeinfo ccs 
			WHERE ccs.orderno='$orderno' AND ccs.is_standard='$this_tmode' AND ccs.packing_type='$packing_type' AND ccs.gmt_pcs_weight>0";
	// echo "<pre>$sql</pre>";
	$result = $this->conn->query($sql);
	while($row = $result->fetch(PDO::FETCH_ASSOC)){
		extract($row);
		
		$arr_size_weight["$size_name"] = $gmt_pcs_weight;
	}
	
	return $arr_size_weight;
}

public function checkBuyerPOWhetherSetOrder($shipmentpriceID){
	$sqlSGC = "SELECT count(sgc.group_number) as countstyle 
						FROM tblship_group_color sgc
						WHERE sgc.shipmentpriceID='$shipmentpriceID' AND sgc.statusID=1 
						group by group_number";
	$resultSGC = $this->conn->query($sqlSGC);
	$rowSGC = $resultSGC->fetch(PDO::FETCH_ASSOC);
		$countstyle = $rowSGC["countstyle"];
		
	return $countstyle;
}

public function checkGroupNumberColor($shipmentpriceID, $colorID, $garmentID){
	$sql = "SELECT group_number 
			FROM tblship_group_color sgc 
			WHERE sgc.shipmentpriceID='$shipmentpriceID' AND sgc.statusID='1' 
			AND sgc.colorID='$colorID' AND sgc.garmentID='$garmentID'";
	$result = $this->conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
		$group_number = $row["group_number"];
	
	return $group_number;
}

public function checkShipmentColorByGroupNumber($shipmentpriceID, $group_number){
	$sql = "SELECT group_concat(distinct c.colorName) as color
			FROM tblship_group_color sgc 
			INNER JOIN tblcolor c ON c.ID = sgc.colorID
			WHERE sgc.shipmentpriceID='$shipmentpriceID' AND sgc.statusID='1' 
			AND sgc.group_number='$group_number'";
	
	$result = $this->conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
		$color = $row["color"];
		
	return $color;
}

public function getAllPackingInfoByBuyerPO($shipmentpriceID, $factoryID="", $query_filter=""){ // buyer_kohls.php, shipment_new_2/ajax_get_shipment.php, shipment_new_2/lc_class.php, test_sps/ajax_custom.php, test_sps/class_function.php
		
		// $sqlfty = "SELECT od.FactoryID 
					// FROM tblorder od 
					// INNER JOIN tblshipmentprice sp ON sp.Orderno = od.Orderno
					// WHERE sp.ID = '$shipmentpriceID'";
		// $result_fty = $this->conn->prepare($sqlfty);
		// $result_fty->execute();
		// $row_fty  = $result_fty->fetch(PDO::FETCH_ASSOC);
			// $factoryID = $row_fty["FactoryID"];
		
		$str_ial = "";//($factoryID!="G00"? "_ial":""); //-- if not IK Factory --//
		
		$this_tmode = "0";
		$this_packing_type = "1";

		$arr_Prepackname = array();
		$arr_FOBPrice = array();
		$arr_row = array();
		$arr_all_size = array();
		$arr_all_color_ctn = array();
		$ctn_qty = 0;
		$grand_nw = 0;
		$grand_nnw = 0;
		$grand_gw = 0;
		$grand_qty = 0;
		$grand_cbm = 0;
		$quotacat ="";
		$fob_price ="";
		$CIHID = 0;
		$weight_unitID = 44; $ctn_unitID = 16;//CM
		
		// $sql = "SELECT bid.fob_price, g.orderno, qtc.Description as quotacat, GROUP_CONCAT(DISTINCT g.styleNo) as styleNo
				// FROM  tblshipmentprice sp 
				// LEFT JOIN tblbuyer_invoice_detail bid ON bid.shipmentpriceID = sp.ID
				// LEFT JOIN tblquotacat qtc ON qtc.ID = bid.quotaID
				// INNER JOIN tblgarment g ON find_in_set(g.garmentID, sp.StyleNo)
				// WHERE bid.shipmentpriceID='$shipmentpriceID' AND bid.del=0";
		// $result = $this->conn->prepare($sql);
		// $result->execute();
		// $row = $result->fetch(PDO::FETCH_ASSOC);
			// $fob_price = $row["fob_price"];
			// $quotacat  = $row["quotacat"];
			// $orderno   = $row["orderno"];
			
		// $sqlmp = "SELECT mmd.FabricContent as fab_order 
				 // FROM tblmpurchase mp
				 // INNER JOIN tblmpurchase_detail mpd ON mpd.MPID = mp.MPID
				 // INNER JOIN tblmpo_detail mpod ON mpod.MPDID = mpd.MPDID
				 // INNER JOIN tblmm_color mmc ON mmc.MMCID = mpd.MMCID
				 // INNER JOIN tblmm_detail mmd ON mmd.MMID = mmc.MMID
				 // WHERE mp.orderno = '$orderno' AND mp.part=1 limit 1";
		// $stmt_mp = $this->conn->prepare($sqlmp);
		// $stmt_mp->execute();
		// $row_mp = $stmt_mp->fetch(PDO::FETCH_ASSOC);
		// $fab_order = $row_mp["fab_order"];
		
		$sql = "SELECT cpt.PTID, cpt.isLocalhost, cpt.factoryID, 
						count(cpt.PTID) as count_ctn, sp.BuyerPO, sp.Orderno, cpt.shipmentpriceID, cpt.PID, cpt.ctn_range,
						cpt.net_weight, cpt.gross_weight, cpt.net_net_weight, sum(cpt.transit_gross_weight) as transit_gross_weight, 
						cpt.ctn_measurement, cpt.ext_length, cpt.ext_width, cpt.ext_height, cpt.prepack_name,
						spk.tmode, spk.packing_type, spk.packing_method, cpt.blisterbag_in_carton, cpt.is_last,
                        (SELECT cco.ctn_weight 
                         FROM tblcarton_calculator_head cch
                        INNER JOIN tblcarton_calculator_picklist ccp ON ccp.CCHID = cch.CCHID
                        INNER JOIN tblcarton_calculator_option cco ON cco.CCPID = ccp.CCPID
                        WHERE cch.orderno = sp.Orderno AND cco.ext_length = cpt.ext_length AND round(cco.ext_width,1) = cpt.ext_width AND round(cco.ext_height,1) = cpt.ext_height limit 1) as ctn_weight
				FROM tblcarton_picklist_transit cpt
				INNER JOIN tblshipmentprice sp ON sp.ID = cpt.shipmentpriceID
				LEFT JOIN tblship_packing spk ON spk.PID = cpt.PID
				WHERE cpt.shipmentpriceID='$shipmentpriceID' $query_filter
				group by cpt.ctn_range, cpt.net_net_weight, cpt.net_weight, cpt.transit_gross_weight, cpt.ctn_measurement, cpt.total_qty_in_carton
				order by cpt.ctn_num asc";//, cpt.PID
		$result_pack = $this->conn->prepare($sql);
		$result_pack->execute();
		$countctn = $result_pack->rowCount();
		
		// echo "$countctn - $shipmentpriceID << <br/>";
		$start = 0;
		while($row_pack = $result_pack->fetch(PDO::FETCH_ASSOC)){
			extract($row_pack);
			$blisterbag_in_carton = ($blisterbag_in_carton==0? 1: $blisterbag_in_carton);
			$blisterbag_in_carton = ($packing_method==1? $blisterbag_in_carton: 1);
			
			$ext_length = round($ext_length, 1);
			$ext_width  = round($ext_width, 1);
			$ext_height = round($ext_height, 1);
			
			$cbm_perctn      = round(($ext_length/100) * ($ext_width/100) * ($ext_height/100),3);
			$cbm_total       = round($cbm_perctn * $count_ctn, 3);
			$ctn_measurement = ($ctn_measurement==""? "$ext_length x $ext_width x $ext_height (CM)": $ctn_measurement);
			
			$start++;
			$ctn_qty += $count_ctn;
			$end = $start + $count_ctn - 1;
			$this_ctn_range = "$start-$end";
			$this_start = $start;
			$this_end_num = $end;
			
			$this_tmode        = ($tmode!=""? $tmode: $this_tmode);
			$this_packing_type = ($packing_type!=""? $packing_type: $this_packing_type);
			$arr_size_weight   = $this->getGarmentWeightFromCalculator($Orderno, $this_tmode, $this_packing_type);
			$acc_weight        = $this->funcGetAccWeight($Orderno);
			$countstyle        = $this->checkBuyerPOWhetherSetOrder($shipmentpriceID);
			
			list($ori_start, $ori_end) = explode("-", $ctn_range);
			
			$html_head = "$ctn_range / count:$count_ctn | $this_ctn_range | method:$packing_method | ";
			$start = $end;
			
			$inner_html = ""; $this_ctn_qty=0; $one_nnw=0; $this_nnw=0; $one_nw=0; $this_nw=0;
			//echo "<br/>";
			$sql_dt = "SELECT cptd.size_name as size_name, cptd.qty, cptd.colorID, cptd.garmentID,
								group_concat(c.colorName) as gmt_color, group_concat(g.styleNo) as gmt_style, 
								(SELECT sgc.group_number FROM tblship_group_color sgc 
								 WHERE sgc.shipmentpriceID=cptd.shipmentpriceID AND sgc.statusID=1
								 AND sgc.colorID = cptd.colorID AND sgc.garmentID = cptd.garmentID limit 1) as group_number,
                                 (SELECT scsq.ID FROM tblship_colorsizeqty scsq 
                                  WHERE scsq.shipmentpriceID = cptd.shipmentpriceID 
                                  AND scsq.size_name = cptd.size_name order by scsq.ID asc limit 1) as sizeID
						FROM tblcarton_picklist_transit_detail cptd
						LEFT JOIN tblcolor c ON c.ID = cptd.colorID
						LEFT JOIN tblgarment g ON g.garmentID = cptd.garmentID
						WHERE cptd.shipmentpriceID='$shipmentpriceID' 
						AND cptd.PTID='$PTID' AND cptd.isLocalhost='$isLocalhost' AND cptd.factoryID='$factoryID'
						
						group by group_number, size_name
						order by group_number, sizeID";//group by cptd.size_name //AND cptd.ctn_num='$ori_start'
			$result_dt = $this->conn->prepare($sql_dt);
			$result_dt->execute();
			$arr_grp_color   = array();
			$arr_size_info   = array();
			$arr_list_detail = array();
			$new_range = ""; $count = 0; $mixID = "";
			while($row_dt  = $result_dt->fetch(PDO::FETCH_ASSOC)){
				$group_number = $row_dt["group_number"];
				$colorID      = $row_dt["colorID"];
				$garmentID    = $row_dt["garmentID"];
				$gmt_color    = $row_dt["gmt_color"];
				$gmt_style    = $row_dt["gmt_style"];
				$size_name    = $row_dt["size_name"];
				$qty          = $row_dt["qty"];
				
				// echo ">>> [$shipmentpriceID] $ori_start [$this_start] - $group_number / $size_name <br/>";
				
				$arr_color = explode(",", $gmt_color);
				$arr_style = explode(",", $gmt_style);
				
				// $this_nnw += (($qty * $arr_size_weight["$size_name"] * $blisterbag_in_carton) * $count_ctn);
				// $one_nnw  += ($qty * $arr_size_weight["$size_name"] * $blisterbag_in_carton);
				// $this_ctn_qty += ($qty * $blisterbag_in_carton);
				
				$this_nnw += (($qty * $arr_size_weight["$size_name"]) * $count_ctn);
				$one_nnw  += ($qty * $arr_size_weight["$size_name"]);
				$this_ctn_qty += ($qty);
				
				$sqlSKU = "SELECT spd.SKU
								FROM tblship_packing_detail spd
								INNER JOIN tblship_packing spk ON spk.PID = spd.PID
								INNER JOIN tblship_group_color sgc ON sgc.group_number = spd.group_number 
																	AND sgc.shipmentpriceID = spk.shipmentpriceID
								WHERE spk.shipmentpriceID='$shipmentpriceID' AND spk.statusID=1 AND spd.statusID=1 AND sgc.statusID=1 AND sgc.colorID='$colorID' AND sgc.garmentID='$garmentID' AND spd.size_name='$size_name' AND spk.PID='$PID' AND spd.SKU!='' limit 1";
				$result_sku = $this->conn->prepare($sqlSKU);
				$result_sku->execute();
				$row_sku = $result_sku->fetch(PDO::FETCH_ASSOC);
					$SKU = $row_sku["SKU"];
					$SKU = ($SKU==""? "-": $SKU);
					//$SKU = ($prepack_name==""? $SKU: $prepack_name);
					$prepack_name = $SKU;
					
				$sqlPOPrice = "SELECT scsq.price as fob_price
								FROM tblship_colorsizeqty scsq 
								WHERE scsq.shipmentpriceID='$shipmentpriceID' AND scsq.statusID=1 
								AND scsq.garmentID='$garmentID' AND scsq.colorID='$colorID' AND scsq.price>0";
				$result_scsq = $this->conn->prepare($sqlPOPrice);
				$result_scsq->execute();
				$row_scsq = $result_scsq->fetch(PDO::FETCH_ASSOC);
					$fob_price = $row_scsq["fob_price"];
					
				
				if (!in_array("$group_number**%%^^$SKU", $arr_grp_color)){
					array_push($arr_grp_color, "$group_number**%%^^$SKU");
				}
				$arr_size_info["$group_number"]["$size_name"] = $qty ;//* $blisterbag_in_carton
				
				if (array_key_exists("$group_number**^^$size_name", $arr_all_size)){
					$arr_all_size["$group_number**^^$size_name"] += ($qty * $count_ctn ); //* $blisterbag_in_carton
				}
				else{
					$arr_all_size["$group_number**^^$size_name"] = $qty * $count_ctn ;//* $blisterbag_in_carton
				}
				
				if($new_range==""){// to store color contains how many carton qty
					if (array_key_exists("$group_number", $arr_all_color_ctn)){
						$arr_all_color_ctn["$group_number"] += $count_ctn;
					}
					else{
						$arr_all_color_ctn["$group_number"] = $count_ctn;
					}
					$new_range = "No";
				}
				
				//------------------------------------//
				//-------- Array Based ON SKU --------//
				//------------------------------------//
				$prepack_qty = ($qty * $count_ctn );//* $blisterbag_in_carton
				if (array_key_exists("$SKU**^^$group_number", $arr_Prepackname)){
					$arr_Prepackname["$SKU**^^$group_number"]["qty"] += $prepack_qty;
					$this_temp = $prepack_qty;
				}
				else{
					$color       = $this->checkShipmentColorByGroupNumber($shipmentpriceID, $group_number);
					$arr_Prepackname["$SKU**^^$group_number"] = array("qty"=>$prepack_qty, "color"=>$color, "fob_price"=>$fob_price,);
				}
				//-------- End Array Based ON SKU --------//
				//----------------------------------------//
				
				//-----------------------------------------//
				//-------- Array Based ON PO PRICE --------//
				//-----------------------------------------//
				if (array_key_exists("F$fob_price", $arr_FOBPrice)){
					$arr_FOBPrice["F$fob_price"]["qty"] += $prepack_qty;
				}
				else{
					$arr_FOBPrice["F$fob_price"] = array("qty"=>$prepack_qty);
				}
				//-------- End Array Based ON PO PRICE --------//
				//---------------------------------------------//
				$count++;
				$gs_qty = $qty ;//* $blisterbag_in_carton
				if($count==1){
					$mixID = "$group_number**%%$size_name**%%$gs_qty";
				}
				else{
					$mixID .= "::^^$group_number**%%$size_name**%%$gs_qty";
				}
				$arr_list_detail[] = array("size_name"=>$size_name, "group_number"=>$group_number, "qty"=>$gs_qty);
				
				$inner_html .= "======> grp:$group_number / c:$colorID / g:$garmentID | [$gmt_color / $gmt_style] $size_name / $qty | SKU:$SKU <br/>";
			}//--- End While Inner ---//
			
			// $one_nw    = round((($this_nnw / $count_ctn) + $acc_weight),3);
			
			$gross_weight = round($transit_gross_weight / $count_ctn, 3);
			$one_nw    = $gross_weight - $ctn_weight;
			$this_nw   = $one_nw * $count_ctn;
			$this_gw   = $transit_gross_weight;// * $count_ctn;
			$total_qty = $count_ctn * $this_ctn_qty;
			
			$one_nnw      = ($one_nnw>$one_nw? $one_nw - 0.1: $one_nnw);
			
			$net_net_weight = $one_nnw;
			$net_weight = $one_nw;
			
			$html_head .= "ctn_qty:$this_ctn_qty - total_qty:$total_qty | nnw:$this_nnw ($one_nw) | nw:$this_nw  | gw:$transit_gross_weight | ctn_measurement:$ctn_measurement | CBM:$cbm_total / $cbm_perctn per<br/> $inner_html";
			
			$arr_info = array("CIHID"=>$CIHID, "PID"=>$PID, "SKU"=>"$SKU",  "prepack_name"=>$prepack_name, 
								"start"=>$this_start, "end_num"=>$this_end_num, "is_last"=>$is_last,  "total_ctn"=>$count_ctn,
								"ctn_range"=>"$this_ctn_range", "count_ctn"=>"$count_ctn", "mixID"=>$mixID, "total_qty_in_carton"=>$this_ctn_qty,  "gmt_style"=>$gmt_style,
								"net_net_weight"=>$net_net_weight, "net_weight"=>$net_weight, "gross_weight"=>$gross_weight,
								"weight_unitID"=>$weight_unitID, "ctn_unitID"=>$ctn_unitID, "total_CBM"=>$cbm_total, "arr_list_detail"=>$arr_list_detail, "blisterbag_in_carton"=>$blisterbag_in_carton,
								"arr_grp_color"=>$arr_grp_color, "arr_size_info"=>$arr_size_info, 
								"this_ctn_qty"=>"$this_ctn_qty", "total_qty"=>"$total_qty", 
								"this_nnw"=>"$this_nnw", "this_nw"=>"$this_nw", "this_gw"=>"$this_gw", "cbm_total"=>"$cbm_total", 
								"ctn_measurement"=>$ctn_measurement, "ext_length"=>$ext_length, "ext_width"=>$ext_width, "ext_height"=>$ext_height);
			$grand_nnw += $this_nnw;
			$grand_nw  += $this_nw;
			$grand_gw  += $this_gw;
			$grand_qty += $total_qty;
			$grand_cbm += $cbm_total;
			
			array_push($arr_row, $arr_info);
			$html = "$html_head ";
		}//--- End While Outer ---//
		
		//echo "$html <br/>";
		$arr_all = array("arr_row"=>$arr_row, "arr_all_size"=>$arr_all_size, "arr_Prepackname"=>$arr_Prepackname, 
						 "ctn_qty"=>$ctn_qty, "arr_all_color_ctn"=>$arr_all_color_ctn, "arr_FOBPrice"=>$arr_FOBPrice,
						 "grand_nnw"=>$grand_nnw, "grand_nw"=>$grand_nw, "grand_gw"=>$grand_gw, "grand_qty"=>$grand_qty,
						 "grand_cbm"=>$grand_cbm);
		return $arr_all;
	}
	
	
public function getAllExfactoryPackingInfoByBuyerPO($shipmentpriceID, $factoryID="", $query_filter=""){ // buyer_kohls.php, shipment_new_2/ajax_get_shipment.php, shipment_new_2/lc_class.php, test_sps/ajax_custom.php, test_sps/class_function.php
		
		// $sqlfty = "SELECT od.FactoryID 
					// FROM tblorder od 
					// INNER JOIN tblshipmentprice sp ON sp.Orderno = od.Orderno
					// WHERE sp.ID = '$shipmentpriceID'";
		// $result_fty = $this->conn->prepare($sqlfty);
		// $result_fty->execute();
		// $row_fty  = $result_fty->fetch(PDO::FETCH_ASSOC);
			// $factoryID = $row_fty["FactoryID"];
		
		$str_ial = "";//($factoryID!="G00"? "_ial":""); //-- if not IK Factory --//
		
		$this_tmode = "0";
		$this_packing_type = "1";

		$arr_Prepackname = array();
		$arr_FOBPrice = array();
		$arr_row = array();
		$arr_all_size = array();
		$arr_all_color_ctn = array();
		$ctn_qty = 0;
		$grand_nw = 0;
		$grand_nnw = 0;
		$grand_gw = 0;
		$grand_qty = 0;
		$grand_cbm = 0;
		$quotacat ="";
		$fob_price ="";
		$CIHID = 0;
		$weight_unitID = 44; $ctn_unitID = 16;//CM
		
		// $sql = "SELECT bid.fob_price, g.orderno, qtc.Description as quotacat, GROUP_CONCAT(DISTINCT g.styleNo) as styleNo
				// FROM  tblshipmentprice sp 
				// LEFT JOIN tblbuyer_invoice_detail bid ON bid.shipmentpriceID = sp.ID
				// LEFT JOIN tblquotacat qtc ON qtc.ID = bid.quotaID
				// INNER JOIN tblgarment g ON find_in_set(g.garmentID, sp.StyleNo)
				// WHERE bid.shipmentpriceID='$shipmentpriceID' AND bid.del=0";
		// $result = $this->conn->prepare($sql);
		// $result->execute();
		// $row = $result->fetch(PDO::FETCH_ASSOC);
			// $fob_price = $row["fob_price"];
			// $quotacat  = $row["quotacat"];
			// $orderno   = $row["orderno"];
			
		// $sqlmp = "SELECT mmd.FabricContent as fab_order 
				 // FROM tblmpurchase mp
				 // INNER JOIN tblmpurchase_detail mpd ON mpd.MPID = mp.MPID
				 // INNER JOIN tblmpo_detail mpod ON mpod.MPDID = mpd.MPDID
				 // INNER JOIN tblmm_color mmc ON mmc.MMCID = mpd.MMCID
				 // INNER JOIN tblmm_detail mmd ON mmd.MMID = mmc.MMID
				 // WHERE mp.orderno = '$orderno' AND mp.part=1 limit 1";
		// $stmt_mp = $this->conn->prepare($sqlmp);
		// $stmt_mp->execute();
		// $row_mp = $stmt_mp->fetch(PDO::FETCH_ASSOC);
		// $fab_order = $row_mp["fab_order"];
		
		$sql = "SELECT cpt.PTID, cpt.isLocalhost, cpt.factoryID, 
						count(cpt.PTID) as count_ctn, sp.BuyerPO, sp.Orderno, cpt.shipmentpriceID, cpt.PID, cpt.ctn_range,
						cpt.net_weight, cpt.gross_weight, cpt.net_net_weight, sum(cpt.transit_gross_weight) as transit_gross_weight, 
						cpt.ctn_measurement, cpt.ext_length, cpt.ext_width, cpt.ext_height, cpt.prepack_name,
						spk.tmode, spk.packing_type, spk.packing_method, cpt.blisterbag_in_carton, cpt.is_last,
                        (SELECT cco.ctn_weight 
                         FROM tblcarton_calculator_head cch
                        INNER JOIN tblcarton_calculator_picklist ccp ON ccp.CCHID = cch.CCHID
                        INNER JOIN tblcarton_calculator_option cco ON cco.CCPID = ccp.CCPID
                        WHERE cch.orderno = sp.Orderno AND cco.ext_length = cpt.ext_length AND round(cco.ext_width,1) = cpt.ext_width AND round(cco.ext_height,1) = cpt.ext_height limit 1) as ctn_weight
				FROM tblcarton_picklist_transit cpt
				INNER JOIN tblci_detail cd ON cd.shipmentpriceID = cpt.shipmentpriceID 
                							AND cd.PTID = cpt.PTID 
                                            AND cd.isLocalhost = cpt.isLocalhost
                                            AND cd.factoryID = cpt.factoryID
                                            AND cd.isreturn = 0
                							AND cd.del=0 
                INNER JOIN tblci_header ch ON ch.CIHID = cd.CIHID AND ch.trf_type=2
				INNER JOIN tblshipmentprice sp ON sp.ID = cpt.shipmentpriceID
				LEFT JOIN tblship_packing spk ON spk.PID = cpt.PID
				WHERE cpt.shipmentpriceID='$shipmentpriceID' $query_filter
				group by cpt.ctn_range, cpt.net_net_weight, cpt.net_weight, cpt.transit_gross_weight, cpt.ctn_measurement, cpt.total_qty_in_carton
				order by cpt.ctn_num asc";//, cpt.PID
		$result_pack = $this->conn->prepare($sql);
		$result_pack->execute();
		$countctn = $result_pack->rowCount();
		
		// echo "$countctn - $shipmentpriceID << <br/>";
		$start = 0;
		while($row_pack = $result_pack->fetch(PDO::FETCH_ASSOC)){
			extract($row_pack);
			$blisterbag_in_carton = ($blisterbag_in_carton==0? 1: $blisterbag_in_carton);
			$blisterbag_in_carton = ($packing_method==1? $blisterbag_in_carton: 1);
			
			$ext_length = round($ext_length, 1);
			$ext_width  = round($ext_width, 1);
			$ext_height = round($ext_height, 1);
			
			$cbm_perctn      = round(($ext_length/100) * ($ext_width/100) * ($ext_height/100),3);
			$cbm_total       = round($cbm_perctn * $count_ctn, 3);
			$ctn_measurement = ($ctn_measurement==""? "$ext_length x $ext_width x $ext_height (CM)": $ctn_measurement);
			
			$start++;
			$ctn_qty += $count_ctn;
			$end = $start + $count_ctn - 1;
			$this_ctn_range = "$start-$end";
			$this_start = $start;
			$this_end_num = $end;
			
			$this_tmode        = ($tmode!=""? $tmode: $this_tmode);
			$this_packing_type = ($packing_type!=""? $packing_type: $this_packing_type);
			$arr_size_weight   = $this->getGarmentWeightFromCalculator($Orderno, $this_tmode, $this_packing_type);
			$acc_weight        = $this->funcGetAccWeight($Orderno);
			$countstyle        = $this->checkBuyerPOWhetherSetOrder($shipmentpriceID);
			
			list($ori_start, $ori_end) = explode("-", $ctn_range);
			
			$html_head = "$ctn_range / count:$count_ctn | $this_ctn_range | method:$packing_method | ";
			$start = $end;
			
			$inner_html = ""; $this_ctn_qty=0; $one_nnw=0; $this_nnw=0; $one_nw=0; $this_nw=0;
			//echo "<br/>";
			$sql_dt = "SELECT cptd.size_name as size_name, cptd.qty, cptd.colorID, cptd.garmentID,
								group_concat(c.colorName) as gmt_color, group_concat(g.styleNo) as gmt_style, 
								(SELECT sgc.group_number FROM tblship_group_color sgc 
								 WHERE sgc.shipmentpriceID=cptd.shipmentpriceID AND sgc.statusID=1
								 AND sgc.colorID = cptd.colorID AND sgc.garmentID = cptd.garmentID limit 1) as group_number,
                                 (SELECT scsq.ID FROM tblship_colorsizeqty scsq 
                                  WHERE scsq.shipmentpriceID = cptd.shipmentpriceID 
                                  AND scsq.size_name = cptd.size_name order by scsq.ID asc limit 1) as sizeID
						FROM tblcarton_picklist_transit_detail cptd
						LEFT JOIN tblcolor c ON c.ID = cptd.colorID
						LEFT JOIN tblgarment g ON g.garmentID = cptd.garmentID
						WHERE cptd.shipmentpriceID='$shipmentpriceID' 
						AND cptd.PTID='$PTID' AND cptd.isLocalhost='$isLocalhost' AND cptd.factoryID='$factoryID'
						
						group by group_number, size_name
						order by group_number, sizeID";//group by cptd.size_name //AND cptd.ctn_num='$ori_start'
			$result_dt = $this->conn->prepare($sql_dt);
			$result_dt->execute();
			$arr_grp_color   = array();
			$arr_size_info   = array();
			$arr_list_detail = array();
			$new_range = ""; $count = 0; $mixID = "";
			while($row_dt  = $result_dt->fetch(PDO::FETCH_ASSOC)){
				$group_number = $row_dt["group_number"];
				$colorID      = $row_dt["colorID"];
				$garmentID    = $row_dt["garmentID"];
				$gmt_color    = $row_dt["gmt_color"];
				$gmt_style    = $row_dt["gmt_style"];
				$size_name    = $row_dt["size_name"];
				$qty          = $row_dt["qty"];
				
				// echo ">>> [$shipmentpriceID] $ori_start [$this_start] - $group_number / $size_name <br/>";
				
				$arr_color = explode(",", $gmt_color);
				$arr_style = explode(",", $gmt_style);
				
				// $this_nnw += (($qty * $arr_size_weight["$size_name"] * $blisterbag_in_carton) * $count_ctn);
				// $one_nnw  += ($qty * $arr_size_weight["$size_name"] * $blisterbag_in_carton);
				// $this_ctn_qty += ($qty * $blisterbag_in_carton);
				
				$this_nnw += (($qty * $arr_size_weight["$size_name"]) * $count_ctn);
				$one_nnw  += ($qty * $arr_size_weight["$size_name"]);
				$this_ctn_qty += ($qty);
				
				$sqlSKU = "SELECT spd.SKU
								FROM tblship_packing_detail spd
								INNER JOIN tblship_packing spk ON spk.PID = spd.PID
								INNER JOIN tblship_group_color sgc ON sgc.group_number = spd.group_number 
																	AND sgc.shipmentpriceID = spk.shipmentpriceID
								WHERE spk.shipmentpriceID='$shipmentpriceID' AND spk.statusID=1 AND spd.statusID=1 AND sgc.statusID=1 AND sgc.colorID='$colorID' AND sgc.garmentID='$garmentID' AND spd.size_name='$size_name' AND spk.PID='$PID' AND spd.SKU!='' limit 1";
				$result_sku = $this->conn->prepare($sqlSKU);
				$result_sku->execute();
				$row_sku = $result_sku->fetch(PDO::FETCH_ASSOC);
					$SKU = $row_sku["SKU"];
					$SKU = ($SKU==""? "-": $SKU);
					//$SKU = ($prepack_name==""? $SKU: $prepack_name);
					$prepack_name = $SKU;
					
				$sqlPOPrice = "SELECT scsq.price as fob_price
								FROM tblship_colorsizeqty scsq 
								WHERE scsq.shipmentpriceID='$shipmentpriceID' AND scsq.statusID=1 
								AND scsq.garmentID='$garmentID' AND scsq.colorID='$colorID' AND scsq.price>0";
				$result_scsq = $this->conn->prepare($sqlPOPrice);
				$result_scsq->execute();
				$row_scsq = $result_scsq->fetch(PDO::FETCH_ASSOC);
					$fob_price = $row_scsq["fob_price"];
					
				
				if (!in_array("$group_number**%%^^$SKU", $arr_grp_color)){
					array_push($arr_grp_color, "$group_number**%%^^$SKU");
				}
				$arr_size_info["$group_number"]["$size_name"] = $qty ;//* $blisterbag_in_carton
				
				if (array_key_exists("$group_number**^^$size_name", $arr_all_size)){
					$arr_all_size["$group_number**^^$size_name"] += ($qty * $count_ctn ); //* $blisterbag_in_carton
				}
				else{
					$arr_all_size["$group_number**^^$size_name"] = $qty * $count_ctn ;//* $blisterbag_in_carton
				}
				
				if($new_range==""){// to store color contains how many carton qty
					if (array_key_exists("$group_number", $arr_all_color_ctn)){
						$arr_all_color_ctn["$group_number"] += $count_ctn;
					}
					else{
						$arr_all_color_ctn["$group_number"] = $count_ctn;
					}
					$new_range = "No";
				}
				
				//------------------------------------//
				//-------- Array Based ON SKU --------//
				//------------------------------------//
				$prepack_qty = ($qty * $count_ctn );//* $blisterbag_in_carton
				if (array_key_exists("$SKU**^^$group_number", $arr_Prepackname)){
					$arr_Prepackname["$SKU**^^$group_number"]["qty"] += $prepack_qty;
					$this_temp = $prepack_qty;
				}
				else{
					$color       = $this->checkShipmentColorByGroupNumber($shipmentpriceID, $group_number);
					$arr_Prepackname["$SKU**^^$group_number"] = array("qty"=>$prepack_qty, "color"=>$color, "fob_price"=>$fob_price,);
				}
				//-------- End Array Based ON SKU --------//
				//----------------------------------------//
				
				//-----------------------------------------//
				//-------- Array Based ON PO PRICE --------//
				//-----------------------------------------//
				if (array_key_exists("F$fob_price", $arr_FOBPrice)){
					$arr_FOBPrice["F$fob_price"]["qty"] += $prepack_qty;
				}
				else{
					$arr_FOBPrice["F$fob_price"] = array("qty"=>$prepack_qty);
				}
				//-------- End Array Based ON PO PRICE --------//
				//---------------------------------------------//
				$count++;
				$gs_qty = $qty ;//* $blisterbag_in_carton
				if($count==1){
					$mixID = "$group_number**%%$size_name**%%$gs_qty";
				}
				else{
					$mixID .= "::^^$group_number**%%$size_name**%%$gs_qty";
				}
				$arr_list_detail[] = array("size_name"=>$size_name, "group_number"=>$group_number, "qty"=>$gs_qty);
				
				$inner_html .= "======> grp:$group_number / c:$colorID / g:$garmentID | [$gmt_color / $gmt_style] $size_name / $qty | SKU:$SKU <br/>";
			}//--- End While Inner ---//
			
			// $one_nw    = round((($this_nnw / $count_ctn) + $acc_weight),3);
			
			$gross_weight = round($transit_gross_weight / $count_ctn, 3);
			$one_nw    = $gross_weight - $ctn_weight;
			$this_nw   = $one_nw * $count_ctn;
			$this_gw   = $transit_gross_weight;// * $count_ctn;
			$total_qty = $count_ctn * $this_ctn_qty;
			
			$one_nnw      = ($one_nnw>$one_nw? $one_nw - 0.1: $one_nnw);
			
			$net_net_weight = $one_nnw;
			$net_weight = $one_nw;
			
			$html_head .= "ctn_qty:$this_ctn_qty - total_qty:$total_qty | nnw:$this_nnw ($one_nw) | nw:$this_nw  | gw:$transit_gross_weight | ctn_measurement:$ctn_measurement | CBM:$cbm_total / $cbm_perctn per<br/> $inner_html";
			
			$arr_info = array("CIHID"=>$CIHID, "PID"=>$PID, "SKU"=>"$SKU",  "prepack_name"=>$prepack_name, 
								"start"=>$this_start, "end_num"=>$this_end_num, "is_last"=>$is_last,  "total_ctn"=>$count_ctn,
								"ctn_range"=>"$this_ctn_range", "count_ctn"=>"$count_ctn", "mixID"=>$mixID, "total_qty_in_carton"=>$this_ctn_qty,  "gmt_style"=>$gmt_style,
								"net_net_weight"=>$net_net_weight, "net_weight"=>$net_weight, "gross_weight"=>$gross_weight,
								"weight_unitID"=>$weight_unitID, "ctn_unitID"=>$ctn_unitID, "total_CBM"=>$cbm_total, "arr_list_detail"=>$arr_list_detail, "blisterbag_in_carton"=>$blisterbag_in_carton,
								"arr_grp_color"=>$arr_grp_color, "arr_size_info"=>$arr_size_info, 
								"this_ctn_qty"=>"$this_ctn_qty", "total_qty"=>"$total_qty", 
								"this_nnw"=>"$this_nnw", "this_nw"=>"$this_nw", "this_gw"=>"$this_gw", "cbm_total"=>"$cbm_total", 
								"ctn_measurement"=>$ctn_measurement, "ext_length"=>$ext_length, "ext_width"=>$ext_width, "ext_height"=>$ext_height);
			$grand_nnw += $this_nnw;
			$grand_nw  += $this_nw;
			$grand_gw  += $this_gw;
			$grand_qty += $total_qty;
			$grand_cbm += $cbm_total;
			
			array_push($arr_row, $arr_info);
			$html = "$html_head ";
		}//--- End While Outer ---//
		
		//echo "$html <br/>";
		$arr_all = array("arr_row"=>$arr_row, "arr_all_size"=>$arr_all_size, "arr_Prepackname"=>$arr_Prepackname, 
						 "ctn_qty"=>$ctn_qty, "arr_all_color_ctn"=>$arr_all_color_ctn, "arr_FOBPrice"=>$arr_FOBPrice,
						 "grand_nnw"=>$grand_nnw, "grand_nw"=>$grand_nw, "grand_gw"=>$grand_gw, "grand_qty"=>$grand_qty,
						 "grand_cbm"=>$grand_cbm);
		return $arr_all;
	}
	
public function getAllCuttingPickListByBuyerPO($shipmentpriceID){//buyer_kohls.php, shipment_new_2/lc_class.php, shipment_new_2/ajax_get_shipment.php, shipment_new_2/buyer_inv.php
		
		$this_tmode = "0";
		$this_packing_type = "1";

		$arr_Prepackname = array();
		$arr_skucolorsize = array();
		$arr_group_number = array();
		$arr_FOBPrice = array();
		$arr_row = array();
		$arr_all_size = array();
		$arr_all_color_ctn = array();
		$arr_all_size_color = array();
		$arr_ctn_measurement = array();
		$ctn_qty = 0;
		$grand_nw = 0;
		$grand_nnw = 0;
		$grand_gw = 0;
		$grand_qty = 0;
		$grand_cbm = 0;
		$quotacat ="";
		$fob_price ="";
		
		//---------------- BUYER INVOICE INFO ------------------//
		$sql = "SELECT sp.BuyerPO, bid.fob_price, g.orderno, qtc.Description as quotacat, GROUP_CONCAT(DISTINCT g.styleNo) as styleNo,
						group_concat(distinct bid.shipping_marking separator ' / ') as shipping_marking
				FROM  tblshipmentprice sp 
				LEFT JOIN tblbuyer_invoice_detail bid ON bid.shipmentpriceID = sp.ID
				LEFT JOIN tblquotacat qtc ON qtc.ID = bid.quotaID
				INNER JOIN tblgarment g ON find_in_set(g.garmentID, sp.StyleNo)
				WHERE bid.shipmentpriceID='$shipmentpriceID' AND bid.del=0";
		$result = $this->conn->prepare($sql);
		$result->execute();
		$row = $result->fetch(PDO::FETCH_ASSOC);
			$fob_price = $row["fob_price"];
			$quotacat  = $row["quotacat"];
			$orderno   = $row["orderno"];
			$BuyerPO   = $row["BuyerPO"];
			$shipping_marking   = $row["shipping_marking"];
		
		
		//----------------------------------------//
		//------------- CARTON HEAD --------------//
		//----------------------------------------//
		$sql = "SELECT cphp.shipmentpriceID as this_spID, cphp.PID, cphp.ctn_num, cphp.ctn_range, cphp.prepack_name, cphp.group_number,
						cphp.blisterbag_in_carton, cphp.total_qty_in_carton, 
						cphp.net_net_weight as nnw, cphp.net_weight as nw, cphp.gross_weight as gw, 
						cphp.ctn_measurement, cphp.ctn_measurement_last, cphp.ext_length, cphp.ext_width, cphp.ext_height, cphp.total_CBM,
						spk.tmode, spk.packing_type, spk.packing_method
				FROM tblcarton_picklist_head_prod cphp
				LEFT JOIN tblship_packing spk ON spk.PID = cphp.PID
				WHERE cphp.shipmentpriceID = '$shipmentpriceID'";
		$result_pack = $this->conn->prepare($sql);
		$result_pack->execute();
		$start = 0;
		while($row_pack = $result_pack->fetch(PDO::FETCH_ASSOC)){
			extract($row_pack);
			$blisterbag_in_carton = ($blisterbag_in_carton==0? 1: $blisterbag_in_carton);
			$blisterbag_in_carton = ($packing_method==1? $blisterbag_in_carton: 1);
			$ctn_measurement = ($ctn_measurement_last==""? $ctn_measurement: $ctn_measurement_last);
			
			list($start_range, $end_range) = explode("-", $ctn_range);
			$count_ctn = $end_range - $start_range + 1;
			$start++;
			$ctn_qty += $count_ctn;
			
			//----------------------------------------//
			//------------ CARTON DETAIL -------------//
			//----------------------------------------//
			$arr_grp_color = array();
			$arr_size_info = array();
			$new_range = "";
			
			$sql_dt = "SELECT cpdp.size_name, cpdp.group_number, cpdp.qty
						FROM tblcarton_picklist_detail_prod cpdp
						WHERE cpdp.shipmentpriceID='$this_spID' 
						AND cpdp.PID='$PID' AND cpdp.ctn_num='$ctn_num'";
			$result_dt = $this->conn->prepare($sql_dt);
			$result_dt->execute();
			while($row_dt  = $result_dt->fetch(PDO::FETCH_ASSOC)){
				$group_number = $row_dt["group_number"];
				$size_name    = $row_dt["size_name"];
				$qty          = $row_dt["qty"];
				
				//---------------- SKU ------------------//
				$sqlSKU = "SELECT spd.SKU
							FROM tblship_packing_detail spd 
							INNER JOIN tblship_packing spk ON spk.PID = spd.PID
							WHERE spk.shipmentpriceID = '$this_spID' AND spd.PID='$PID' 
							AND spd.group_number='$group_number' AND spd.size_name='$size_name' AND spd.statusID=1";
				$result_sku = $this->conn->prepare($sqlSKU);
				$result_sku->execute();
				$row_sku = $result_sku->fetch(PDO::FETCH_ASSOC);
					$SKU = $row_sku["SKU"];
					$SKU = ($SKU==""? "-": $SKU);
					$SKU = ($prepack_name==""? $SKU: $prepack_name);
					
				//------------- FOB PRICE / PO PRICE --------------//
				$sqlFOB = "SELECT scsq.price as fob_price, scsq.garmentID, scsq.colorID, c.colorName as colorName
							FROM tblship_group_color sgc 
							INNER JOIN tblship_colorsizeqty scsq ON scsq.garmentID = sgc.garmentID 
																	AND scsq.colorID = sgc.colorID
																	AND scsq.statusID=1
							INNER JOIN tblcolor c ON c.ID = scsq.colorID
							WHERE sgc.shipmentpriceID = '$this_spID' AND sgc.group_number='$group_number' 
							AND sgc.statusID=1 AND scsq.price>0 limit 1";
				$result_scsq = $this->conn->prepare($sqlFOB);
				$result_scsq->execute();
				$row_scsq = $result_scsq->fetch(PDO::FETCH_ASSOC);
					$fob_price = $row_scsq["fob_price"];
					$garmentID = $row_scsq["garmentID"];
					$colorID   = $row_scsq["colorID"];
					$colorName = $row_scsq["colorName"];
				
				///////////////////////////////////////////////////////////
				////////////--- Array Store Color & SKU ---////////////////
				if (!in_array("$group_number**%%^^$SKU", $arr_grp_color)){
					array_push($arr_grp_color, "$group_number**%%^^$SKU");
				}
				$arr_size_info["$group_number"]["$size_name"] = $qty * $blisterbag_in_carton;
				$arr_all_size_color["g$group_number**^^$colorName"]["$size_name"] += ($qty * $count_ctn * $blisterbag_in_carton);
				
				/////////////////////////////////////////////////////////////
				//////////--- Array Store Color Size Total Qty ---///////////
				$gs_qty = $qty * $count_ctn * $blisterbag_in_carton;
				if (array_key_exists("$group_number**^^$size_name", $arr_all_size)){
					$arr_all_size["$group_number**^^$size_name"] += $gs_qty; 
				}
				else{
					$arr_all_size["$group_number**^^$size_name"] = $gs_qty;
				}
				
				$prepack_qty = ($qty * $count_ctn * $blisterbag_in_carton);
				if (array_key_exists("$SKU**^^$group_number**^^$size_name", $arr_skucolorsize)){
					$arr_skucolorsize["$SKU**^^$group_number**^^$size_name"]["qty"] += $prepack_qty;
				}
				else{
					$color       = $this->checkShipmentColorByGroupNumber($shipmentpriceID, $group_number);
					$arr_skucolorsize["$SKU**^^$group_number**^^$size_name"] = array("qty"=>$prepack_qty, "color"=>$color, "fob_price"=>$fob_price);
				}
				
				//echo "$group_number - $size_name = $gs_qty [".$arr_all_size["$group_number**^^$size_name"]."] <br/>";
				
				/////////////////////////////////////////////////////////////////////
				////////--- To Store Color Contains How Many Carton Qty ---//////////
				if($new_range==""){
					if (array_key_exists("$group_number", $arr_all_color_ctn)){
						$arr_all_color_ctn["$group_number"] += $count_ctn;
					}
					else{
						$arr_all_color_ctn["$group_number"] = $count_ctn;
					}
					$new_range = "No";
				}
				/////// End Store Color Contains How Many Carton Qty //////
				///////////////////////////////////////////////////////////
				
				//////////////////////////////////////////////////////////////
				////////////////--- Array Based On SKU ---////////////////////
				$prepack_qty = ($qty * $count_ctn * $blisterbag_in_carton);
				if (array_key_exists("$SKU**^^$group_number", $arr_Prepackname)){
					$arr_Prepackname["$SKU**^^$group_number"]["qty"] += $prepack_qty;
					$this_temp = $prepack_qty;
				}
				else{
					$color       = $this->checkShipmentColorByGroupNumber($shipmentpriceID, $group_number);
					$arr_Prepackname["$SKU**^^$group_number"] = array("qty"=>$prepack_qty, "color"=>$color, "fob_price"=>$fob_price);
				}
				/////////// End Array Based On SKU //////////
				/////////////////////////////////////////////
				
				if (array_key_exists("G$group_number", $arr_group_number)){
					$arr_group_number["G$group_number"]["qty"] += $prepack_qty;
					
					//echo "A: $group_number - $prepack_qty <br/>";
				}
				else{
					$color       = $this->checkShipmentColorByGroupNumber($shipmentpriceID, $group_number);
					$arr_group_number["G$group_number"] = array("qty"=>$prepack_qty, "color"=>$color, "fob_price"=>$fob_price, 
																"garmentID"=>$garmentID, "colorID"=>$colorID);
																
					//echo "B: $group_number - $prepack_qty <br/>";
				}
				
				//////////////////////////////////////////////////////////////
				//////////////--- Array Based On PO Price ---/////////////////
				if (array_key_exists("F$fob_price", $arr_FOBPrice)){
					$arr_FOBPrice["F$fob_price"]["qty"] += $prepack_qty;
				}
				else{
					$arr_FOBPrice["F$fob_price"] = array("qty"=>$prepack_qty, "color"=>$color);
				}
				//////// End Array Based On PO Price ////////
				/////////////////////////////////////////////
				
				
			}
			//--- END WHILE CARTON DETAIL ---//
			//-------------------------------//
			
			
			$this_nnw = $nnw * $count_ctn;
			$this_nw  = $nw * $count_ctn;
			$this_gw  = $gw * $count_ctn;
			$this_ctn_range = $ctn_range;
			$this_ctn_qty   = $total_qty_in_carton;
			$total_qty      = $total_qty_in_carton * $count_ctn;
			$cbm_total      = $total_CBM;
			
			if (!in_array("$ctn_measurement", $arr_ctn_measurement)){
				array_push($arr_ctn_measurement, "$ctn_measurement");
			}
			
			$arr_info = array("ctn_range"=>"$this_ctn_range", "count_ctn"=>"$count_ctn", "SKU"=>"$SKU", 
								"arr_grp_color"=>$arr_grp_color, "arr_size_info"=>$arr_size_info, 
								"this_ctn_qty"=>"$this_ctn_qty", "total_qty"=>"$total_qty", 
								"this_nnw"=>"$this_nnw", "this_nw"=>"$this_nw", "this_gw"=>"$this_gw", "cbm_total"=>"$cbm_total", 
								"ctn_measurement"=>$ctn_measurement, "ext_length"=>$ext_length, "ext_width"=>$ext_width, "ext_height"=>$ext_height);
			
			$grand_nnw += $this_nnw;
			$grand_nw  += $this_nw;
			$grand_gw  += $this_gw;
			$grand_qty += $total_qty;
			$grand_cbm += $cbm_total;
			
			array_push($arr_row, $arr_info);
			
		}
		//--- END WHILE CARTON HEAD ---//
		//-----------------------------//
		
		$arr_all = array("arr_row"=>$arr_row, "arr_all_size"=>$arr_all_size, "arr_Prepackname"=>$arr_Prepackname,
						 "arr_group_number"=>$arr_group_number, "arr_all_size_color"=>$arr_all_size_color, "arr_skucolorsize"=>$arr_skucolorsize,
						 "ctn_qty"=>$ctn_qty, "arr_all_color_ctn"=>$arr_all_color_ctn, "arr_FOBPrice"=>$arr_FOBPrice,
						 "grand_nnw"=>$grand_nnw, "grand_nw"=>$grand_nw, "grand_gw"=>$grand_gw, "grand_qty"=>$grand_qty,
						 "grand_cbm"=>$grand_cbm, "shipping_marking"=>$shipping_marking, "arr_ctn_measurement"=>$arr_ctn_measurement);
		
		return $arr_all;
	}
	
public function getBuyerInvoiceDescriptionInfo($id, $query_filter=""){ //buyer_joefresh.php, buyer_dxl.php, buyer_hunnybunny.php
	$arr_buyerpo = array();
	$arr_shipID  = array();
	$grand_inv_gw = 0;
	$grand_inv_nw = 0;
	$grand_inv_nnw = 0;
	$grand_inv_ctn = 0;
	$grand_inv_qty = 0;
	$grand_inv_cbm = 0;
	
	$sqlInv = " SELECT bid.shipmentpriceID, sp.Orderno, sp.BuyerPO, bid.fob_price, se.Description as uom, 
					GROUP_CONCAT(DISTINCT g.styleNo) as styleNo, qtc.Description as quotacat, od.FactoryID,
					bid.shipping_marking, gd.Description as gender, ptt.Description as product_type, bid.ht_code
				FROM tblbuyer_invoice_detail bid 
				INNER JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
				INNER JOIN tblorder od ON od.Orderno = sp.Orderno
				INNER JOIN tblset se ON od.Qunit = se.ID
				INNER JOIN tblgarment g ON find_in_set(g.garmentID, sp.StyleNo)
				LEFT JOIN tblgender gd ON gd.ID = g.genderID
				LEFT JOIN tblproducttype ptt ON ptt.ID = g.gmttype
				INNER JOIN tblquotacat qtc ON qtc.ID = bid.quotaID
				WHERE bid.invID='$id' AND bid.del='0' AND bid.group_number>0
				group by bid.shipmentpriceID, bid.shipping_marking";
	$stmt_inv = $this->conn->prepare($sqlInv);
	$stmt_inv->execute();
	while($row_inv = $stmt_inv->fetch(PDO::FETCH_ASSOC)){
		$Orderno         = $row_inv["Orderno"];
		$gender          = $row_inv["gender"];
		$product_type    = $row_inv["product_type"];
		$shipmentpriceID = $row_inv["shipmentpriceID"];
		$fob_price       = $row_inv["fob_price"];
		$BuyerPO         = $row_inv["BuyerPO"];
		$uom             = $row_inv["uom"];
		$styleNo         = $row_inv["styleNo"];
		$quotacat        = $row_inv["quotacat"];
		$ht_code         = $row_inv["ht_code"];
		$FactoryID       = $row_inv["FactoryID"];
		$ship_marking    = $row_inv["shipping_marking"];
		$str_ial = "";//($FactoryID=="F22"?"_ial":"");
		
		$sqlmp = "SELECT mmd.FabricContent as fab_order 
				 FROM tblmpurchase mp
				 INNER JOIN tblmpurchase_detail mpd ON mpd.MPID = mp.MPID
				 INNER JOIN tblmpo_detail mpod ON mpod.MPDID = mpd.MPDID
				 INNER JOIN tblmm_color mmc ON mmc.MMCID = mpd.MMCID
				 INNER JOIN tblmm_detail mmd ON mmd.MMID = mmc.MMID
				 WHERE mp.orderno = '$Orderno' AND mp.part=1 limit 1";
		$stmt_mp = $this->conn->prepare($sqlmp);
		$stmt_mp->execute();
		$row_mp = $stmt_mp->fetch(PDO::FETCH_ASSOC);
		$fab_order = $row_mp["fab_order"];
		$ship_marking = ($ship_marking==""? "$gender $fab_order $product_type": $ship_marking);
		
		//$arr_all   = $this->getAllPackingInfoByBuyerPO($shipmentpriceID, $FactoryID, $query_filter);
		$arr_all   = $this->getAllCuttingPickListByBuyerPO($shipmentpriceID);
		$arr_ctn_measurement  = $arr_all["arr_ctn_measurement"];
		$arr_skucolorsize  = $arr_all["arr_skucolorsize"];
		$arr_info  = $arr_all["arr_Prepackname"];
		$arr_FOB   = $arr_all["arr_FOBPrice"];
		$grand_nnw = $arr_all["grand_nnw"];
		$grand_nw  = $arr_all["grand_nw"];
		$grand_gw  = $arr_all["grand_gw"];
		$grand_qty = $arr_all["grand_qty"];
		$total_ctn = $arr_all["ctn_qty"];
		$grand_cbm = $arr_all["grand_cbm"];
		
		$arr_all_size      = $arr_all["arr_all_size"];
		$arr_group_number  = $arr_all["arr_group_number"];
		//array_push($arr_info, $arr_info_row);
		
		$arr_buyerpo["byBuyerPO"]["$BuyerPO"] = array("shipmentpriceID"=>"$shipmentpriceID","od_FactoryID"=>$FactoryID, 
											"styleNo"=>"$styleNo","total_ctn"=>"$total_ctn", "quotacat"=>"$quotacat", "ht_code"=>$ht_code,
											"fab_order"=>"$ship_marking", "fob_price"=>$fob_price, "ship_marking"=>"$ship_marking",
											"count_row"=>count($arr_info), "arr_info"=>$arr_info,
											"grand_nnw"=>$grand_nnw, "grand_nw"=>$grand_nw, "grand_gw"=>$grand_gw, "uom"=>$uom);
											
		$arr_buyerpo["byFabric"]["$ship_marking"][] = array("arr_FOB"=>$arr_FOB, "arr_all_size"=>$arr_all_size,
															"arr_group_number"=>$arr_group_number, "arr_skucolorsize"=>$arr_skucolorsize,
															"BuyerPO"=>$BuyerPO, "styleNo"=>$styleNo, "quotacat"=>"$quotacat", "ht_code"=>$ht_code,
															"total_ctn"=>$total_ctn, "shipmentpriceID"=>$shipmentpriceID, "Orderno"=>$Orderno,
															"grand_nnw"=>$grand_nnw, "grand_nw"=>$grand_nw, "grand_gw"=>$grand_gw, "grand_cbm"=>$grand_cbm, "uom"=>$uom, "arr_ctn_measurement"=>$arr_ctn_measurement);
		//echo "$gender $fab_order << <br/>";
		if(!in_array($shipmentpriceID, $arr_shipID)){
			array_push($arr_shipID, $shipmentpriceID);
			$grand_inv_gw  += $grand_gw;
			$grand_inv_nw  += $grand_nw;
			$grand_inv_nnw += $grand_nnw;
			$grand_inv_ctn += $total_ctn;
			$grand_inv_cbm += $grand_cbm;
		}
		
		$arr_buyerpo["grand_inv_gw"]  = $grand_inv_gw;
		$arr_buyerpo["grand_inv_nw"]  = $grand_inv_nw;
		$arr_buyerpo["grand_inv_nnw"] = $grand_inv_nnw;
		$arr_buyerpo["grand_inv_ctn"] = $grand_inv_ctn;
		$arr_buyerpo["grand_inv_cbm"] = $grand_inv_cbm;
		
	}//--- End Buyer PO while ---//
	
	return $arr_buyerpo;
}

public function getBuyerInvoiceDescriptionInfoMethod2($id, $query_filter=""){ //buyer_buffalo_tw.php, buyer_buffalo_cn.php
	$arr_buyerpo = array();
	$sqlInv = " SELECT bid.shipmentpriceID, sp.Orderno, sp.BuyerPO, bid.fob_price, se.Description as uom, 
					GROUP_CONCAT(DISTINCT g.styleNo) as styleNo, qtc.Description as quotacat, od.FactoryID,
					group_concat(distinct bid.shipping_marking separator ' / ') as shipping_marking,
					gd.Description as gender, ptt.Description as product_type, bid.ht_code
				FROM tblbuyer_invoice_detail bid 
				INNER JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
				INNER JOIN tblorder od ON od.Orderno = sp.Orderno
				INNER JOIN tblset se ON od.Qunit = se.ID
				INNER JOIN tblgarment g ON find_in_set(g.garmentID, sp.StyleNo)
				LEFT JOIN tblgender gd ON gd.ID = g.genderID
				LEFT JOIN tblproducttype ptt ON ptt.ID = g.gmttype
				INNER JOIN tblquotacat qtc ON qtc.ID = bid.quotaID
				WHERE bid.invID='$id' AND bid.del='0' AND bid.group_number>0
				group by bid.shipmentpriceID";
	$stmt_inv = $this->conn->prepare($sqlInv);
	$stmt_inv->execute();
	while($row_inv = $stmt_inv->fetch(PDO::FETCH_ASSOC)){
		$Orderno         = $row_inv["Orderno"];
		$gender          = $row_inv["gender"];
		$product_type    = $row_inv["product_type"];
		$shipmentpriceID = $row_inv["shipmentpriceID"];
		$fob_price       = $row_inv["fob_price"];
		$BuyerPO         = $row_inv["BuyerPO"];
		$uom             = $row_inv["uom"];
		$styleNo         = $row_inv["styleNo"];
		$quotacat        = $row_inv["quotacat"];
		$ht_code         = $row_inv["ht_code"];
		$FactoryID       = $row_inv["FactoryID"];
		$ship_marking    = $row_inv["shipping_marking"];
		$str_ial = "";//($FactoryID=="F22"?"_ial":"");
		
		$sqlmp = "SELECT mmd.FabricContent as fab_order 
				 FROM tblmpurchase mp
				 INNER JOIN tblmpurchase_detail mpd ON mpd.MPID = mp.MPID
				 INNER JOIN tblmpo_detail mpod ON mpod.MPDID = mpd.MPDID
				 INNER JOIN tblmm_color mmc ON mmc.MMCID = mpd.MMCID
				 INNER JOIN tblmm_detail mmd ON mmd.MMID = mmc.MMID
				 WHERE mp.orderno = '$Orderno' AND mp.part=1 limit 1";
		$stmt_mp = $this->conn->prepare($sqlmp);
		$stmt_mp->execute();
		$row_mp = $stmt_mp->fetch(PDO::FETCH_ASSOC);
		$fab_order = $row_mp["fab_order"];
		$ship_marking = ($ship_marking==""? "$gender $fab_order $product_type": $ship_marking);
		
		//$arr_all   = $this->getAllPackingInfoByBuyerPO($shipmentpriceID, $FactoryID, $query_filter);
		$arr_all   = $this->getAllCuttingPickListByBuyerPO($shipmentpriceID);
		$arr_info  = $arr_all["arr_Prepackname"];
		$arr_FOB   = $arr_all["arr_FOBPrice"];
		$grand_nnw = $arr_all["grand_nnw"];
		$grand_nw  = $arr_all["grand_nw"];
		$grand_gw  = $arr_all["grand_gw"];
		$grand_qty = $arr_all["grand_qty"];
		$total_ctn = $arr_all["ctn_qty"];
		$grand_cbm = $arr_all["grand_cbm"];
		
		$arr_all_size      = $arr_all["arr_all_size"];
		$arr_group_number  = $arr_all["arr_group_number"];
		//array_push($arr_info, $arr_info_row);
		
		$arr_buyerpo["byBuyerPO"]["$BuyerPO"] = array("shipmentpriceID"=>"$shipmentpriceID","od_FactoryID"=>$FactoryID, 
											"styleNo"=>"$styleNo","total_ctn"=>"$total_ctn", "quotacat"=>"$quotacat", "ht_code"=>$ht_code,
											"fab_order"=>"$ship_marking", "fob_price"=>$fob_price, "ship_marking"=>"$ship_marking",
											"count_row"=>count($arr_info), "arr_info"=>$arr_info,
											"grand_nnw"=>$grand_nnw, "grand_nw"=>$grand_nw, "grand_gw"=>$grand_gw, "uom"=>$uom);
											
		$arr_buyerpo["byFabric"]["$ship_marking"][] = array("arr_FOB"=>$arr_FOB, "arr_all_size"=>$arr_all_size,
															"arr_group_number"=>$arr_group_number,
															"BuyerPO"=>$BuyerPO, "styleNo"=>$styleNo, "ht_code"=>$ht_code,
															"total_ctn"=>$total_ctn, "shipmentpriceID"=>$shipmentpriceID,
															"grand_nnw"=>$grand_nnw, "grand_nw"=>$grand_nw, "grand_gw"=>$grand_gw, "grand_cbm"=>$grand_cbm, "grand_qty"=>$grand_qty, "uom"=>$uom);
		//echo "$gender $fab_order << <br/>";
		
	}//--- End Buyer PO while ---//
	
	return $arr_buyerpo;
}

public function getBuyerInvoiceDiscountOtherCharges($id, $tblbuyer_invoice_detail="tblbuyer_invoice_detail"){
	$arr_discount = array();
	
	$sql = "SELECT bid.ID, bid.other_charge, bid.charge_percentage, bid.total_amount 
			FROM $tblbuyer_invoice_detail bid
			WHERE bid.invID = '$id' AND bid.del=0 AND bid.group_number=0 AND bid.charge_percentage<0";
	$stmt = $this->conn->prepare($sql);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		extract($row);
		
		$arr_discount[] = array("ID"=>$ID, "discount_name"=>$other_charge, 
								"percentage"=>$charge_percentage, "discount_amt"=>$total_amount);
	}
	
	return $arr_discount;
}

public function updateBuyerPOIsCtnSticker($shipmentpriceID){
	$query = "UPDATE `tblshipmentprice` sp 
				INNER JOIN tblorder od ON od.Orderno = sp.Orderno
				INNER JOIN tblbuyer b ON b.BuyerID = od.buyerID
				SET sp.is_ctn_sticker = b.is_ctn_sticker
				WHERE sp.ID =:shipmentpriceID ";
	$stmt = $this->conn->prepare($query);
	$stmt->bindParam(':shipmentpriceID', $shipmentpriceID);
	$stmt->execute();
}

public function stocktake_po_ratio($orderno, $date_type, $search_date_to, $totalQty){

	$arr = array();
	$search_date="";
	if($date_type="sp.Shipdate"){
		$search_date = "AND sp.Shipdate<='$search_date_to' ";
		$un_search_date = "AND (sp.Shipdate>'$search_date_to' or sp.isship=0)";
	}else{
		$search_date = "AND date(sa.exfactory)<='$search_date_to' ";
		$un_search_date = "AND (sp.Factorydate>'$search_date_to' or sp.isship=0)";
	}

	$sql_shipped="select sum(sp.aFOBPrice_uom) as aFOBPrice_uom , sum(sp.aShippedQty_uom) as aShippedQty_uom, 
						MIN(sp.Shipdate) as min_shipdate, MIN(DATE(sa.exfactory)) as min_exfactory
					from tblshipmentprice sp 
					inner join tblshippingadvise sa On sa.tblshipmentpriceID = sp.ID
					where sp.statusID=1 and sp.is_warehouse=0
					$search_date
					and sp.Orderno='$orderno'
					and sp.is_warehouse=0;";
	$result = $this->conn->query($sql_shipped);
	$row = $result->fetch(PDO::FETCH_ASSOC);

	$aFOBPrice_uom = $row["aFOBPrice_uom"];
	$aShippedQty_uom = $row["aShippedQty_uom"];
	$min_shipdate = $row["min_shipdate"];
	$min_exfactory = $row["min_exfactory"];

	$shipped_ratio = ($aShippedQty_uom / $totalQty) * 100;

	$u_qty=0 ; $u_fob=0;
	$sql_unshipped = "select sum(scsq.qty ) as qty,sum(scsq.qty * scsq.price) as fob,
							(select count(g2.groupID) from tblgarment g2 where g2.orderno = sp.Orderno and g2.groupID = g.groupID  group by g2.groupID) as g3
					from tblship_colorsizeqty scsq
					inner join tblshipmentprice sp ON sp.ID = scsq.shipmentpriceID
					inner join tblgarment g ON g.garmentID = scsq.garmentID
					where sp.statusID=1 and scsq.statusID=1 
					$un_search_date
					and sp.Orderno='$orderno'
					group by g.orderno, g.groupID;";
	// echo " $sql_unshipped <br>";
	$u_result = $this->conn->query($sql_unshipped);
	while($u_row = $u_result->fetch(PDO::FETCH_ASSOC)){

		$uqty2 = $u_row["qty"];
		$ufob2 = $u_row["fob"];
		$u_g3 = $u_row["g3"];

		$u_qty +=$uqty2 / $u_g3;
		$u_fob +=$ufob2 / $u_g3;
	}

	$ushipped_ratio = ($u_qty / $totalQty) * 100;

	$sql_warehouse = "SELECT ifnull(sum(gw.transfer_qty),0) as w_qty
					FROM tblgarment_warehouse gw 
					where gw.is_wh_trf=0
					and gw.del=0
					and gw.flag_sample=0 
					and gw.Orderno='$orderno';";
	$w_result = $this->conn->query($sql_warehouse);
	$w_row = $w_result->fetch(PDO::FETCH_ASSOC);

	$w_qty = $w_row["w_qty"];


	$arr = array(
			"min_shipdate"=>$min_shipdate,
			"min_exfactory"=>$min_exfactory,
			"shippedqty"=>$aShippedQty_uom,
			"shippedamt"=>$aFOBPrice_uom,
			"shippedratio"=>$shipped_ratio,
			"ushippedqty"=>$u_qty,
			"ushippedamt"=>$u_fob,
			"ushippedratio"=>$ushipped_ratio,
			"warehouseqty"=>$w_qty
	);
	return $arr;
}

}

?>