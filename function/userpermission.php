<?php

//  $view = $_GET['view'];
//  $username = $_GET['UserId'];
//  $screenID = $_GET['screen'];
// 
// $uid=("SELECT * FROM tbluseraccount where UserID='$username'");
// $user=$conn->query($uid);
// $rowsUser = $user->fetch(PDO::FETCH_BOTH);
// $acct_id = $rowsUser['AcctID'];

class userpermission
{
/*
$userid = $_GET['UserId'];
$screenID = $_GET['screen'];

$uid=("SELECT AcctID FROM tbluseraccount where UserID=".$acctid."");
$userID=$conn->query($uid);
foreach ($userID as $rowUserid)
{
$acctid_s=$rowUserid['AcctID'];
}*/

// public function arrPermission($acct_id, $screenID, $conn){
// $action="enabled";
// 
// $sql=("SELECT * FROM ctrltrustee ".
// 	  "where AcctID='$acct_id' and ScreenID='$screenID'");
// 
// $p=$conn->query($sql);
// $rows = $p->fetch(PDO::FETCH_BOTH);
// 							
// $list=$rows['PermissionID'];	
// 
// echo "<br>";
// $arr=explode(',',$list);
// 			
// foreach ($arr as $value) {
// 	
// 	switch ($value)
// 	{
//     	case 1: //read
//     		//echo "<td><button type='button' ".$action. ">Read</button></td> ";
//     		break;
//     	case 2: //Create
// 		    if ($view="accountMaintenance")
// 			{
// 			} 
// 			else
// 			{
//     		echo "<a href='Setup.php?view=accountMaintenance&screen=".$screenID."'><span>New Account</span></a>";
// 			}
//     		break;
//     	case 3: //update
//     		echo "<input type='submit' name='submit' class='button' value='Save'/> ";
//     		break;
//     	case 4: //Delete
//     		echo "<button type='button' ".$action. ">Delete</button>";
//     		break;
//     	case 5: //Approved
//     		echo "<button type='button' ".$action. ">Approved</button>";
//     		break;
//     	case 6: //Print
//     		echo " <button type='button' ".$action. ">Print</button>";
//     		break;
// 		case 7: //Print
//     		echo " <button type='button' ".$action. ">Export</button>";
//     		break;
// 		case 8: //Print
//     		echo " <button type='button' ".$action. ">Upload</button>";
//     		break;
// 
// 	}
// 	
// }
// }
// 
// }


// capture user screen Permission

public function arrPermission($acct_id, $screenID, $conn){

	$sql="SELECT * FROM ctrltrustee ".
	  	  "where AcctID='".$acct_id."' and ScreenID='".$screenID."'";
	$p=$conn->query($sql);
	$rows = $p->fetch(PDO::FETCH_ASSOC);
							
	$tmp=$rows['PermissionID'];	
	$list=explode(',',$tmp);

	return $list;
}

public function arrscreen($acct_id, $conn){

	$sql="SELECT group_concat(ScreenID) as screenID FROM ctrltrustee where AcctID='$acct_id' AND TRIM(PermissionID)!='' order by ScreenID";

	$p=$conn->query($sql);
	$rows = $p->fetch(PDO::FETCH_ASSOC);
							
	$tmp=$rows['screenID'];	
	$list=explode(',',$tmp);

	return $list;
}


public function arrGenPermission($acct_id, $ctrlID, $conn){

	$sql="SELECT * FROM ctrltrusteegen ".
	  	  "where AcctID='".$acct_id."' and ctrlID='". $ctrlID."'";

	$p=$conn->query($sql);
	$rows = $p->fetch(PDO::FETCH_ASSOC);
							
	$tmp=$rows['permissionID'];	
	$list=explode(',',$tmp);

	return $list;
}

public function arrMenu($acct_id, $conn){

	$sql="SELECT group_concat(distinct s.MenuID) as MenuID 
 			FROM ctrltrustee c
 			inner join tblscreenmenu s ON s.ID = c.ScreenID 
	  	  	where AcctID='".$acct_id."'";

	$p=$conn->query($sql);
	$rows = $p->fetch(PDO::FETCH_ASSOC);
							
	$tmp=$rows['MenuID'];	
	$list=explode(',',$tmp);

	return $list;
}


// public function arrZeroMenu($acct_id, $conn){
// 
// 	$sql="SELECT group_concat(c.ScreenID) as screenID 
// 			FROM ctrltrustee c
// 			inner join tblscreenmenu s ON s.ID = c.ScreenID 
// 			where AcctID=$acct_id  and MenuID=0 sorder by ScreenID";
// 
// 	$p=$conn->query($sql);
// 	$rows = $p->fetch(PDO::FETCH_ASSOC);
// 							
// 	$tmp=$rows['screenID'];	
// 	$list=explode(',',$tmp);
// 
// 	return $list;
// }


}
?>