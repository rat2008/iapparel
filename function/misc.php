<?php
class misc{

private $conn          = "";
public $clienttimezone = "0";
public $api_key        = "I_WANT_EZI_ACCESS";
public $domainurl      = "http://localhost";
public $apiurl         = "http://localhost/api";

public function __construct($conn=""){
    $this->conn = $conn;
}

public function setConnection($conn){ // shipment_new/shipmentmain/cron_job.php, purchase/mpurchase_class.php 
	$this->conn = $conn;
}

public function getDomainURL(){
	return $this->domainurl;
}

public function getAPIURL(){
	return $this->apiurl;
}

public function funcCallAPI($method, $url, $data){
		$curl = curl_init($url);
		
		$jsonDataEncoded = json_encode($data, JSON_UNESCAPED_UNICODE);
		
		//Tell cURL that we want to send a POST request.
		curl_setopt($curl, CURLOPT_POST, 1);
		
		//Attach our encoded JSON string to the POST fields.
		curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonDataEncoded);
		
		//Set the content type to application/json
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		
		//Execute the request
		$result = curl_exec($curl);
		if($result === false){
			echo curl_errno($ch); 
		}
		else{
			//success
			$result = json_decode($result, true);
			// print_r($result);
		}
		
		return $result;
	}
	
public function funcCallAPIBlob($apiUrl){
	// API endpoint URL
	// $apiUrl = 'http://yourdomain.com/get_blob.php?id=1'; // Replace with your API URL and file ID

	// Initialize cURL session
	$ch = curl_init();

	// Set cURL options
	curl_setopt($ch, CURLOPT_URL, $apiUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);

	// Execute the cURL request
	$response = curl_exec($ch);
	
	if($response === false){
		echo curl_errno($ch); 
	}
	else{
		return $response;	
	}

	/*// Check for cURL errors
	if (curl_errno($ch)) {
		echo "cURL Error: " . curl_error($ch);
		exit;
	}

	// Get the HTTP status code
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	// Close the cURL session
	curl_close($ch);

	// Check if the request was successful (HTTP status code 200)
	if ($httpCode == 200) {
		// Get the content type from the response headers
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

		// Determine the file extension based on the content type
		$fileExtension = '';
		switch ($contentType) {
			case 'image/jpeg':
				$fileExtension = 'jpg';
				break;
			case 'image/png':
				$fileExtension = 'png';
				break;
			case 'application/pdf':
				$fileExtension = 'pdf';
				break;
			// Add more cases for other file types as needed
			default:
				$fileExtension = 'bin'; // Default to binary if type is unknown
				break;
		}

		// Save the BLOB data to a file
		$fileName = 'downloaded_file.' . $fileExtension;
		file_put_contents($fileName, $response);

		echo "File downloaded successfully: $fileName";

		// Alternatively, you can display the file directly in the browser
		// header("Content-Type: $contentType");
		// echo $response;
	} else {
		// Handle API errors
		echo "Failed to retrieve file. HTTP Status Code: $httpCode";
		if ($httpCode == 404) {
			echo " (File not found)";
		} elseif ($httpCode == 400) {
			echo " (Invalid request)";
		}
	}//*/
}

public function funcGetStatus($in_status){
	$in_status=str_replace(",", "','", $in_status);
	
	$sel_status=$this->conn->prepare("SELECT StatusID,StatusName FROM tblstatus WHERE StatusID IN ('$in_status') ORDER BY FIELD(StatusID,'$in_status')");
	$sel_status->execute();

	$row_status=$sel_status->fetchAll(PDO::FETCH_ASSOC);

	return $row_status;
}

public function getUserCompanyGroupID($conn, $lock_locationID){
	$factoryID = str_replace(",","','", $lock_locationID);
	$sql = "SELECT FGID
			FROM tblfactory
			WHERE FactoryID IN ('$factoryID')";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$FGID = $row["FGID"];
		$FGID = ($FGID==""? 0: $FGID);
	
	return $FGID;
}

public function funcMaxID($tbl_name, $columnID){ //-- purchase/mpurchase_class.php, accessory/apurchase_class.php, mpo/prebooking_class.php, acc_saving.php --//
		$sql = "SELECT max($columnID) as maxID FROM $tbl_name";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$maxID = $row["maxID"] + 1;
		
		return $maxID;
}

function formatSizeUnits($bytes, $sizetype=""){
	$value = 0; 
	
	if($sizetype==""){
        if ($bytes >= 1073741824){
			$value = number_format($bytes / 1073741824, 3);
            $bytes = 'GB';
        }
        elseif ($bytes >= 1048576){
			$value = number_format($bytes / 1048576, 3);
            $bytes = 'MB';
        }
        elseif ($bytes >= 1024){
			$value = number_format($bytes / 1024, 3);
            $bytes = 'KB';
        }
        elseif ($bytes > 1){
			$value = $bytes;
            $bytes = 'bytes';
        }
        elseif ($bytes == 1){
			$value = $bytes;
            $bytes = 'byte';
        }
        else{
			$value = 0;
            $bytes = 'bytes';
        }
	}
	else if($sizetype=="GB"){
		$value = number_format($bytes / 1073741824, 4);
        $bytes = 'GB';
	}
	else if($sizetype=="MB"){
		$value = number_format($bytes / 1048576, 4);
        $bytes = 'MB';
		
		// echo "$bytes / 1048576 <br/>";
	}
	else if($sizetype=="KB"){
		$value = number_format($bytes / 1024, 4);
            $bytes = 'KB';
	}
		
	$arr = array("bytes"=>$bytes, "value"=>$value);

    return $arr;
}

public function getMPOHIDMaxID($conn, $isNE="0"){//mv_saving.php
	$date    = date("Y/m/d") ;
	$monthID = substr($date,5,2);
	$yearID  = substr($date,2,2);
	$year    = substr($date,0,4);
	
	if($isNE==1){
        $prefix = glb_smpl_fab_po;
		$increSql = "SELECT MPOHID as maxNum
					FROM tblmpo_header WHERE DATE(createdDate) >=('2017-07-01') AND substr(MPOHID,1,4) = '$prefix' 
					order by createdDate desc, MPOHID desc limit 1"; //max(substring(MPOHID, 12,15)) as maxNum 
		
		$newen = "&newen=true";
	}
	else if($isNE==2){
        $prefix = glb_smpl_mo_po;
		$increSql = "SELECT MPOHID as maxNum
				FROM tblmpo_header WHERE DATE(createdDate) >=('2017-07-01') AND substr(MPOHID,1,4) = '$prefix' 
				order by createdDate desc, MPOHID desc limit 1"; //max(substring(MPOHID, 12,15)) as maxNum 
		
	}
	else{
        $prefix = glb_bulk_fab_po;
		$increSql = "SELECT MPOHID as maxNum
				FROM tblmpo_header WHERE DATE(createdDate) >=('2017-07-01') AND substr(MPOHID,1,4) = '$prefix' 
				order by createdDate desc, MPOHID desc limit 1"; //max(substring(MPOHID, 12,15)) as maxNum 
		
	}
	$resultIncre = $conn->prepare($increSql); 
	$resultIncre->execute(); 
	$number = $resultIncre->fetchColumn();
	
	$resultIncre->execute(); 
	$rowIncre = $resultIncre->fetch(PDO::FETCH_ASSOC);
		$str_mpohid = $rowIncre["maxNum"];
		$arr_mpohid = explode(".", $str_mpohid);
		
		$maxNum = ($str_mpohid==""? "": $arr_mpohid[3]);
		$maxNum = ($maxNum==""? 0: $maxNum);
	
	$length =4;
		
	if($maxNum<0001){
		$maxNum = 0001;
	}else if($maxNum>=9999){
		$maxNum++;
		$length = 5;
	}else{
		$maxNum++;
	}
	
    $num = $this->numberFormat($maxNum, $length);	
	
	$new_MPOHID = "$prefix $yearID.$monthID.$num";	
	
	return $new_MPOHID;
}

public function getMPODIDMaxID($conn){
	$select = "SELECT MAX(CAST((substring(MPODID,2)) AS DECIMAL(6,0))) FROM tblmpo_detail  ";
	$selectMPODID = $conn->prepare($select); 
	$selectMPODID->execute(); 
	$number_of_rows = $selectMPODID->fetchColumn(); 

	$number_of_rows++; 
	$currentID = $number_of_rows;

	$new_MPODID = "M$currentID";

	return $new_MPODID; 	
}

public function getUserID($conn, $acctid){ //warehouse/fab_inv_review.php
	$sql = "SELECT UserID, UserFullName 
				FROM tbluseraccount WHERE AcctID='$acctid'";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$UserID = $row["UserID"];
		$UserFullName = $row["UserFullName"];
		
	return array($UserID, $UserFullName);
}

public function funcConvertSizeToValidID($this_size){
	$this_size  = str_replace(" ", "-", $this_size);
	$this_size  = str_replace("(", "-", $this_size);
	$this_size  = str_replace(")", "-", $this_size);
	$this_size  = str_replace("/", "-", $this_size);
	$this_size  = str_replace("+", "-", $this_size);
	
	return $this_size;
}

public function funcConvertPONOToValidID($this_pohid){
	$this_pohid  = str_replace(" ", "_", $this_pohid);
	$this_pohid  = str_replace(".", "-", $this_pohid); 
	
	return $this_pohid;
}

public function funcDecodeValidIDToPONO($this_pohid){
	$this_pohid  = str_replace("_", " ", $this_pohid);
	$this_pohid  = str_replace("-", ".", $this_pohid); 
	
	return $this_pohid;
}

public function funcConvertSpecialCharWOUpperCase($txt_standard){ //tblbuyer_invoice.php
	$txt_standard = trim($txt_standard);
	//$txt_standard = strtoupper($txt_standard);
	// $txt_standard = str_replace('"','',$txt_standard);
	$txt_standard = htmlspecialchars("$txt_standard", ENT_QUOTES);
	
	//--- Below remove double space ---//
	// $txt_standard = ucfirst($txt_standard);
	// $txt_standard = ucwords(strtolower($txt_standard));
	$txt_standard = preg_replace('/\s+/', ' ', $txt_standard);
	$txt_standard = htmlentities($txt_standard);
	
	return $txt_standard;
}

public function funcConvertSpecialChar($txt_standard){ // finance
	$txt_standard = trim($txt_standard);
	$txt_standard = str_replace('"','',$txt_standard);
	$txt_standard = strtoupper($txt_standard);
	$txt_standard = htmlspecialchars("$txt_standard", ENT_QUOTES);
	
	//--- Below remove double space ---//
	$txt_standard = ucfirst($txt_standard);
	//$txt_standard = ucwords(strtolower($txt_standard));
	$txt_standard = preg_replace('/\s+/', ' ', $txt_standard);
	
	return $txt_standard;
}

public function funcConvertSpecialCharWithDoubleQuote($txt_standard){ // inventory/mmmain/mm_submit.php, accessory/acc_saving_plan.php, invsourcing/, shiment_new/shipmentmain/ship_saving.php
	$txt_standard = trim($txt_standard);
	$txt_standard = strtoupper($txt_standard);
	$txt_standard = htmlspecialchars("$txt_standard", ENT_QUOTES);
	
	//--- Below remove double space ---//
	$txt_standard = ucfirst($txt_standard);
	// $txt_standard = ucwords(strtolower($txt_standard));
	$txt_standard = preg_replace('/\s+/', ' ', $txt_standard);
	$txt_standard = htmlentities($txt_standard);
	
	return $txt_standard;
}

public function funcConvertYdsToMtr($yds_qty){
	$mtr_qty = $yds_qty * 0.9144;
	
	return array($mtr_qty, 0.9144);
}

public function funcDecodeSpecialChar($str){//-- cf/func_logistic.php, func_gpo.php, logistic/lgx_co_summary_pdf.php, iTracking/ajax_custom.php, SOM/excel_trifold.php, Report/lock_ajax_dev.php --//
	$str = html_entity_decode($str);
	$str = str_replace("amp;","", $str);
	$str = str_replace("AMP;","", $str);
	$str = str_replace("&#039;","'", $str);
	$str = str_replace("&QUOT;",'"', $str);
	$str = str_replace("&quot;",'"', $str);
	$str = str_replace("&nbsp;",' ', $str);
	$str = str_replace("&NBSP;",' ', $str);
	
	return $str;
}

function getLocationInfoByIp(){
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = @$_SERVER['REMOTE_ADDR'];
    $result  = array('country'=>'', 'city'=>'');
    if(filter_var($client, FILTER_VALIDATE_IP)){
        $ip = $client;
    }elseif(filter_var($forward, FILTER_VALIDATE_IP)){
        $ip = $forward;
    }else{
        $ip = $remote;
    }
    $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));    
    if($ip_data && $ip_data->geoplugin_countryName != null){
        $result['country'] = $ip_data->geoplugin_countryCode;
        $result['city'] = $ip_data->geoplugin_city;
    }
    return $result;
}

public function getSendEmailForm($brand){ //purchase/mpurchase_load.php, accessory/apurchase_load.php
	$html = '';
	$html .= '<div id="dialog-form" title="Email PO to supplier" style=""> 
				<label for="name"><b>To: </b></label>
				<select name="supplier_email[]" id="supplier_email" class="chosen-select" style="max-width:400px;width:400px;min-width:400px" multiple>
				</select>
				<input type="hidden" id="this_num" value="" />
				<!--<input type="text" name="dialog_to" id="dialog_to" placeholder="Receiver email address...." 
						value="" class="text ui-widget-content ui-corner-all" />-->
				
				<label for="name" style="margin-top:15px"><b>Cc: <small>(*Use ; in order send to multiple email address, example: test@hotmail.com;email@gmail.com)</small></b></label>
				<input type="text" name="dialog_cc" id="dialog_cc" placeholder="Cc email address...." 
					   value="" class="text ui-widget-content ui-corner-all" />
				
				<label for="name"><b>Subject:</b></label>
				<input type="text" value="PO# ['.$brand.']" readonly
						class="subject_text_prefix ui-widget-content ui-corner-all" style="float:left;background-color:#bdbdbd" />
				<input type="text" name="dialog_subject" id="dialog_subject" placeholder="Email subject...."  
					   value="" class="subject_text ui-widget-content ui-corner-all" />
				
				<label for="name"><b>Content:</b></label>
				<textarea name="Content" id="Content" rows="8" style="width:100%;padding:3px" class="text ui-widget-content ui-corner-all" placeholder="Email content...."></textarea><br/>
				
				<!--<label for="name"><b>Attachment File:</b> 
				<a href="#" onClick="window.open(&#39;mpo_listing.php?mpohid=&#39; , &#39;_blank&#39;)" ><small>.pdf</small></a>
				</label> -->
			
		 </div>';
		 
	return $html;
}

public function folder_exist($path){
	if ( !file_exists($path) || !is_dir($path) ){   
		mkdir($path, 0777);
	}
	else{
		// chmod($path, 0777);
	}
}



public function os_info($uagent){
    // the order of this array is important
    global $uagent;
    $oses   = array(
        'Win311' => 'Win16',
        'Win95' => '(Windows 95)|(Win95)|(Windows_95)',
        'WinME' => '(Windows 98)|(Win 9x 4.90)|(Windows ME)',
        'Win98' => '(Windows 98)|(Win98)',
        'Win2000' => '(Windows NT 5.0)|(Windows 2000)',
        'WinXP' => '(Windows NT 5.1)|(Windows XP)',
        'WinServer2003' => '(Windows NT 5.2)',
        'WinVista' => '(Windows NT 6.0)',
        'Win7' => '(Windows NT 6.1)',
        'Win8' => '(Windows NT 6.2)',
        'Win8.1' => '(Windows NT 6.3)',
        'Win10' => '(Windows NT 10.0)',
        'WinNT' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
        'OpenBSD' => 'OpenBSD',
        'SunOS' => 'SunOS',
        'Ubuntu' => 'Ubuntu',
        'Android' => 'Android',
        'Linux' => '(Linux)|(X11)',
        'iPhone' => 'iPhone',
        'iPad' => 'iPad',
        'MacOS' => '(Mac_PowerPC)|(Macintosh)',
        'QNX' => 'QNX',
        'BeOS' => 'BeOS',
        'OS2' => 'OS/2',
        'SearchBot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)'
    );
    $uagent = strtolower($uagent ? $uagent : $_SERVER['HTTP_USER_AGENT']);
    foreach ($oses as $os => $pattern)
        if (preg_match('/' . $pattern . '/i', $uagent))
            return $os;
    return 'Unknown';
}
//echo os_info($uagent);

public function IP(){
	$ip_address=$_SERVER['REMOTE_ADDR'];
	if ($ip_address=='::1')
		$ip_address='localhost';
	return $ip_address;
	}

// public function getCity(){
// 	/*Get user ip address*/
// 	$ip_address=$_SERVER['REMOTE_ADDR'];
// 
// 	/*Get user ip address details with geoplugin.net*/
// 	$geopluginURL='http://www.geoplugin.net/php.gp?ip='.$ip_address;
// 	$addrDetailsArr = unserialize(file_get_contents($geopluginURL)); 
// 
// 	/*Get City name by return array*/
// 	$city = $addrDetailsArr['geoplugin_city']; 
// 	
// 	if(!$city)
// 		$city='Not Define';
// 	
// 	return $city;
// 	}
// 	
// public function getRegion(){
// 	/*Get user ip address*/
// 	$ip_address=$_SERVER['REMOTE_ADDR'];
// 
// 	/*Get user ip address details with geoplugin.net*/
// 	$geopluginURL='http://www.geoplugin.net/php.gp?ip='.$ip_address;
// 	$addrDetailsArr = unserialize(file_get_contents($geopluginURL)); 
// 
// 	/*Get region name by return array*/
// 	$region = $addrDetailsArr['geoplugin_region']; 
// 	
// 	if(!$region)
// 		$region='Not Define';
// 	
// 	return $region;
// 	}
// 
// public function getCountry(){
// 	/*Get user ip address*/
// 	$ip_address=$_SERVER['REMOTE_ADDR'];
// 
// 	/*Get user ip address details with geoplugin.net*/
// 	$geopluginURL='http://www.geoplugin.net/php.gp?ip='.$ip_address;
// 	$addrDetailsArr = unserialize(file_get_contents($geopluginURL)); 
// 
// 	/*Get region name by return array*/
// 	$country = $addrDetailsArr['geoplugin_countryName']; 
// 	
// 	if(!$country)
// 		$country='Not Define';
// 	
// 	return $country;
// 	}
// 
public function TimeNow($clienttimezone=0){
 	//$dt = new DateTime();
	$dt = new DateTime(null, new DateTimeZone('Asia/Kuala_lumpur'));
	
	// echo glb_NE_prefix." << ".glb_order_prefix;
	
	if($clienttimezone!=0){
		return date('Y-m-d H:i:s', strtotime($clienttimezone.' hour'));
	}
	else{
		return $dt->format('Y-m-d H:i:s');
	}
 }
 
public function TimebyTimeZone(){
		$date = new DateTime(null, new DateTimeZone('Asia/Kuala_lumpur'));
		$tz = $date->format('Y-m-d H:i:s');
		return $tz;
	}
	
public function DateNow(){
  		//$dt = new DateTime();
		$dt = new DateTime(null, new DateTimeZone('Asia/Kuala_lumpur'));
 		return $dt->format('Y-m-d');
  	}	
  	
public function TimeOnlyNow(){
		$time = new DateTime(null, new DateTimeZone('Asia/Kuala_lumpur'));
		return $time->format('H:i:sa');
	}
 
public function calcElapsedTime($start,$end)
	{ // calculate elapsed time (in seconds!)
		

		$diff1 = $end-$start;
		$diff = $end-$start;
		$daysDiff = floor($diff/60/60/24);
		$diff -= $daysDiff*60*60*24;
		$hrsDiff = floor($diff/60/60);
		$diff -= $hrsDiff*60*60;
		$minsDiff = floor($diff/60);
		$diff -= $minsDiff*60;
		$secsDiff = $diff;
		
		// echo "[$diff1 = $end - $start]======== $daysDiff >> $hrsDiff >> $minsDiff <br/>";
		
		if ($daysDiff>0)
		{ return $daysDiff.'d '.$hrsDiff.'h '.$minsDiff.'m'.$secsDiff.'s';
			exit;
		}
		else if ($hrsDiff>0)
		{return $hrsDiff.'h '.$minsDiff.'m'.$secsDiff.'s';
			exit;
		}
		else if ($minsDiff>0)
		{return $minsDiff.'m'.$secsDiff.'s';
			exit;
		}
		else if ($secsDiff>=0)
		{return $secsDiff.'s';
			exit;
		}
	}
 
 
 /* browser()
 * Returns browser information in a string
 * Compatible with Chrome, Internet Explorer, Firefox, Safari and Opera
 * This settings are according to the superglobal SERVER
 * @return string Name & Version
 */
public function browser(){
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browsers = array(
                        'Chrome' => array('Google Chrome','Chrome/(.*)\s'),
                        'MSIE' => array('Internet Explorer','MSIE\s([0-9\.]*)'),
                        'Firefox' => array('Firefox', 'Firefox/([0-9\.]*)'),
                        'Safari' => array('Safari', 'Version/([0-9\.]*)'),
                        'Opera' => array('Opera', 'Version/([0-9\.]*)')
                        ); 
                         
    $browser_details = array();
     
        foreach ($browsers as $browser => $browser_info){
            if (preg_match('@'.$browser.'@i', $user_agent)){
                $browser_details['name'] = $browser;//$browser_info[0];
                    preg_match('@'.$browser_info[1].'@i', $user_agent, $version);
                $browser_details['version'] = $version[1];
                $version = strstr( $browser_details['version'], '.',1);
                
                    break;
            } else {
                $browser_details['name'] = 'Unknown';
                $browser_details['version'] = '0';
            }
        }
     
    return $browser_details['name'] .' '.$version;
}
 

// using in user account

public function SelectedCompany($acctid, $conn){
		$sql="SELECT CompanyID FROM tbluseraccount  ".
	  	  	 "where AcctID=".$acctid ;

		$p=$conn->query($sql);
		$rows = $p->fetch(PDO::FETCH_ASSOC);
							
		$tmp=$rows['CompanyID'];	
		$list=explode(',',$tmp);

		return $list;
}

public function SelectedFactory($acctid, $conn){
		$sql="SELECT FactoryID FROM tbluseraccount  ".
	  	  	 "where AcctID=".$acctid ;

		$p=$conn->query($sql);
		$rows = $p->fetch(PDO::FETCH_ASSOC);
							
		$tmp=$rows['FactoryID'];	
		$list=explode(',',$tmp);

		return $list;
}

//==================== Trigger Update Consumption of Fabric ====================//
public function updateFabConsump($conn, $orderno){
	
	$sqlM_detail = "SELECT mp.MPID, mpd.MPDID, mp.allSize, mp.dozPcs, mpd.wastage, mpd.inventoryQty, mpd.colorID,
					(SELECT sum(Qty) FROM tblcolorsizeqty csq WHERE csq.orderno = mp.orderno AND csq.colorID = mpd.colorID GROUP BY csq.orderno) as qty,
					mmd.max_weight_gm as maxGM, mmd.ExternalWidth as external, mmd.multiplier, mmd.basic_unit 
					FROM tblmpurchase_detail mpd
					INNER JOIN tblmpurchase mp ON mpd.MPID = mp.MPID
					INNER JOIN tblmm_color mmc ON mmc.MMCID = mpd.MMCID
					INNER JOIN tblmm_detail mmd ON mmd.MMID = mmc.MMID
					WHERE mp.orderno = '$orderno'";
	$resultM_detail = $conn->query($sql);
	while($rowsM_detail = $resultM_detail->fetch(PDO::FETCH_ASSOC)){
		$mpid = $rowsM_detail["MPID"];
		$mpdid = $rowsM_detail["MPDID"];
		$colorID = $rowsM_detail["colorID"];
		$wastage = $rowsM_detail["wastage"];
		$dozPcs = $rowsM_detail["dozPcs"];
		$basic = $rowsM_detail["basic_unit"];
		$maxGM = $rowsM_detail["maxGM"];
		$external = $rowsM_detail["external"];
		$multiplier = $rowsM_detail["multiplier"];
		
		$consumYDSInner = 0;
		$consumYDS = 0;
		$consumLBS = 0;
		
		$sqlColorSize = "SELECT sum(csq.Qty) as colorSizeQty, cs.qty as sizeQty 
						FROM tblcolorsizeqty csq
						LEFT JOIN tblconsumption cs ON cs.sizeName = csq.sizeName
						WHERE csq.orderno='$orderno' 
						AND csq.colorID='$colorID' AND cs.MPID =  '$mpid' 
						group by csq.SizeName";
		$resultColorSize = $conn->query($sqlColorSize);
		while($rowColorSize=$resultColorSize->fetch(PDO::FETCH_BOTH)){
			$gmtQty = $rowColorSize["colorSizeQty"];
			$consumQty = $rowColorSize["sizeQty"];
			
			$consumYDSInner = ($gmtQty * $consumQty * ($wastage/100 + 1) / $dozPcs);
			
			$consumYDS += $consumYDSInner;
			
		}//---- End While ----//
		
		$oneYDSequalLBS = $maxGM * $external * 0.0232 * $multiplier * 2.2046/1000;
		$invYDS = $inv / $oneYDSequalLBS;
		
		$consumYDS = ($basic!=64 && $basic!=83) ? $consumYDS - $invYDS : $consumYDS - $inv;
		$consumLBS = $consumYDS * (($maxGM * $external * 0.0232 * $multiplier) * 2.2046/1000 );
		
		$ansYDS = $consumYDS;
		$ansLBS = $consumLBS;
		
		if($basic==64 || $basic==83){
				$ansYDS = decimalFormat($consumYDS,0);
				$ansLBS = 0;
		}		
		
		$sqlUpdateM_detail = "UPDATE tblmpurchase_detail SET purchaseQty_yds = '$ansYDS', purchaseQty_lbs = '$ansLBS' WHERE MPDID = '$mpdid'";
		$resultUpdateM_detail = $conn->prepare($sqlUpdateM_detail);
		$resultUpdateM_detail->execute();
	}//---- End While ----//
	
	return true;
}

public function SelectedWorkGroup($acctid, $conn){
		$sql="SELECT WorkGroupID FROM tbluseraccount  ".
	  	  	 "where AcctID=".$acctid ;

		$p=$conn->query($sql);
		$rows = $p->fetch(PDO::FETCH_ASSOC);
							
		$tmp=$rows['WorkGroupID'];	
		$list=explode(',',$tmp);

		return $list;
	}

public function GetDefaultGroup($acctid, $conn){
		$sql="SELECT u.IsDefaultGRP, w.Description FROM `tbluseraccount` u  ".
			 "inner join tblworkgroup w on w.ID = u.IsDefaultGRP ".
	  	  	 "where u.AcctID=".$acctid ;

		$p=$conn->query($sql);
		$rows = $p->fetch(PDO::FETCH_ASSOC);
							

		return $rows;
	}

public function getDepartmentSection($acctid, $conn){
		$sql="SELECT u.AcctID,u.sectionID, s.SectionName, s.DepartmentID, d.DepartmentName FROM `tbluseraccount` u  ".
			 "inner join tblsection s ON s.SectionID = u.sectionID ".
			 "inner join tbldepartment d ON d.DepartmentID = s.DepartmentID ".
	  	  	 "where AcctID=".$acctid ;

		$p=$conn->query($sql);
		$rows = $p->fetch(PDO::FETCH_ASSOC);
							
		return $rows;
	}
	
public function SelectedNetwork($acctid, $conn){
		$sql="SELECT AccessID FROM tbluseraccount  ".
	  	  	 "where AcctID=".$acctid ;

		$p=$conn->query($sql);
		$rows = $p->fetch(PDO::FETCH_ASSOC);
							
		$tmp=$rows['AccessID'];	
		$list=explode(',',$tmp);

		return $list;
	}
	
	
// Create Dynamic HTML table

function createDynamicHTMLTable($sql_query, $link){
    // execute SQL query and get result
    $sql_result=$link->query($sql_query);
    if (($sql_result)||(errorInfo() == 0)) 
    {        
    	$count=$sql_result->RowCount();
    	
    	echo $count;
    
        echo "<DIV>\n";
        echo "<TABLE borderColor=#000000 cellSpacing=0 cellPadding=6 border=2>\n";
        echo "<TBODY>\n";
        if ($sql_result->fetchColumn()>0) 
        { 
            //loop thru the field names to print the correct headers 
            $i = 0; 
            echo "<TR vAlign=top bgColor=#00ffff>\n";
			for ($i = 0; $i < $sql_result->columnCount(); $i++) {
    			$col = $sql_result->getColumnMeta($i);
    			echo "<th>".$col['name']."</th>\n";
            } 
            echo "</TR>\n"; 

            //display The data 
              while ($rows = $sql_result->fetch(PDO::FETCH_ASSOC))
              { 
                  echo "<TR>\n"; 
                 foreach ($rows as $data) 
                 { 
                     echo "<TD align='center'>". $data . "</TD>\n"; 
                      
                 } 
                 echo "</TR>\n"; 
             } 
         } else { 
             echo "<TR>\n<TD colspan='" . ($i+1) . "'>No Results found!</TD></TR>\n"; 
         } 
        echo "</TBODY>\n</TABLE>\n";
        echo "</DIV>\n";
    }
    $sql_result->closeCursor();

}

public function GetPrefixID($screen,$acctid, $conn){
   $sql="SELECT u.IsDefaultGRP, w.Description FROM `tbluseraccount` u  ".
  "inner join tblWorkGroup w on w.ID = u.IsDefaultGRP ".
   "where AcctID=".$acctid ;

   $sql="select count(Prefix) as count from tblprefix where ScreenID=".$screen;
   $result=$conn->query($sql);
   $row =$result->fetch(PDO::FETCH_ASSOC);
   if ($row['count']>1)
   {
 //
    $b="select Prefix from tblprefix ".
    "where ScreenID=".$screen." and GroupID = (select IsDefaultGRP from tbluseraccount where AcctID=".$acctid. ")";
    $b=$conn->query($b);
    $brow = $b->FETCH(PDO::FETCH_ASSOC);
    
    return $brow['Prefix']; 
	
   }
   else{
    $b="select prefix from tblprefix where screenID=".$screen;
    $b=$conn->query($b);
    $brow = $b->FETCH(PDO::FETCH_ASSOC);
    
    return $brow['prefix']; 
   }
  }	
	
public function numberFormat($number, $length){
  $num = str_pad($number,$length,"0",STR_PAD_LEFT);
  return $num;
 }
 
public function decimalFormat($number, $decimal){
	return number_format((float)$number, $decimal, '.', '');
} 

public function getConvertFabricQty($conn, $unitID, $MMCID, $qty){ // warehouse/ajax_custom.php
		$arr_value = array();
		switch($unitID){
			case 44: $qty_KGS = $qty;
					 $qty_LBS = round($this->KGSConvertLBS($qty), 3);
					 $qty_YDS = round($this->LBSConvertYDS($conn, $MMCID, $qty_LBS), 3);
					 $qty_MTR = round($this->YDSConvertMTR($qty_YDS), 3); 
					 $unit    = "KGS"; break;
					 
			case 57: $qty_LBS = $qty;
					 $qty_KGS = round($this->LBSConvertKGS($qty), 3);
					 $qty_YDS = round($this->LBSConvertYDS($conn, $MMCID, $qty_LBS), 3);
					 $qty_MTR = round($this->YDSConvertMTR($qty_YDS), 3); 
					 $unit    = "LBS"; break;
					 
			case 84: $qty_YDS = $qty;
					 $qty_LBS = round($this->YDSConvertLBS($conn, $MMCID, $qty_YDS), 3);
					 $qty_MTR = round($this->YDSConvertMTR($qty_YDS), 3);
					 $qty_KGS = round($this->LBSConvertKGS($qty_LBS), 3); 
					 $unit    = "YDS"; break;
			
			case 60: $qty_MTR = $qty;
					 $qty_YDS = round($this->MTRConvertYDS($qty_MTR), 3);
					 $qty_LBS = round($this->YDSConvertLBS($conn, $MMCID, $qty_YDS), 3);
					 $qty_KGS = round($this->LBSConvertKGS($qty_LBS), 3); 
					 $unit    = "MTR"; break;
			case 64: 
					$qty_LBS = $qty;
					$qty_KGS = $qty;
					$qty_YDS = $qty;
					$qty_MTR = $qty;
					$unit    = "PCS"; break;
		}
		
		$arr_value["qty_KGS"] = $qty_KGS;
		$arr_value["qty_LBS"] = $qty_LBS;
		$arr_value["qty_YDS"] = $qty_YDS;
		$arr_value["qty_MTR"] = $qty_MTR;
		$arr_value["unit"]    = $unit;
		
		return $arr_value;
}

public function ConvertUnitToNumber($unit){
	$basicUnitPcs = intval(preg_replace('/[^0-9]+/', '', $unit), 10);
	$basicUnitPcs = ($basicUnitPcs==0)? 1: $basicUnitPcs;
	
	return $basicUnitPcs;
}

public function getFabricRatioInstoreQty($conn, $MPOHID, $MMCID, $orderno){
	$sql = "SELECT sum(mpod.purchaseQty_lbs) as ia_qty 
			FROM `tblmpo_detail` mpod 
			INNER JOIN tblmpurchase_detail mpd ON mpd.MPDID = mpod.MPDID
			INNER JOIN tblmpurchase mp ON mp.MPID = mpd.MPID
			WHERE mpod.MPOHID = '$MPOHID' AND mp.orderno = '$orderno' AND mpd.MMCID = '$MMCID'";
	$stmt = $conn->query($sql);
	$row  = $stmt->fetch(PDO::FETCH_ASSOC);
		$ia_qty = $row["ia_qty"];
	
	$sqlone = "SELECT sum(mpod.purchaseQty_lbs) as all_qty 
				FROM `tblmpo_detail` mpod 
				INNER JOIN tblmpurchase_detail mpd ON mpd.MPDID = mpod.MPDID
				WHERE mpod.MPOHID = '$MPOHID' AND mpd.MMCID = '$MMCID'";
	$stmtone = $conn->query($sqlone);
	$rowone  = $stmtone->fetch(PDO::FETCH_ASSOC);
		$all_qty = $rowone["all_qty"];
		
	$percentage = ($all_qty==0? 0: $ia_qty / $all_qty);
	
	
	return $percentage;
}

public function getFabricRatioInstoreQtyByInvoice($conn, $MPOHID, $MMCID, $orderno, $AIEID){
	$sql = "SELECT sum(fap.invoice_qty) as ia_qty
			FROM `tblfin_ap_invoice_entry_detail` fap
			INNER JOIN tblfin_ap_invoice_entry aie ON aie.AIEID = fap.AIEID
			WHERE fap.POHID = '$MPOHID' AND fap.orderno='$orderno' AND fap.itemID = '$MMCID' 
			AND del=0 AND aie.statusID NOT IN (6) AND aie.AIEID='$AIEID'";
	$stmt = $conn->query($sql);
	$row  = $stmt->fetch(PDO::FETCH_ASSOC);
		$ia_qty = $row["ia_qty"];
	
	$sqlone = "SELECT sum(fap.invoice_qty) as all_qty
				FROM `tblfin_ap_invoice_entry_detail` fap
				INNER JOIN tblfin_ap_invoice_entry aie ON aie.AIEID = fap.AIEID
				WHERE fap.POHID = '$MPOHID' AND fap.itemID = '$MMCID' AND del=0 AND aie.statusID NOT IN (6)";
	$stmtone = $conn->query($sqlone);
	$rowone  = $stmtone->fetch(PDO::FETCH_ASSOC);
		$all_qty = $rowone["all_qty"];
		
	$percentage = $ia_qty / $all_qty;
	
	
	return $percentage;
}

public function convertAllByUnitID($conn, $this_qty, $unitID, $MMCID, $decimal=3, $prm_maxYD="", $arr_qty=array()){
	$request_kgs   = 0;
	$request_lbs   = 0;
	$request_yds   = 0;
	$request_mtr   = 0;
	$request_sqf   = 0;
	$request_sqm   = 0;
	$request_pcs   = 0;
	$request_sheet = 0;
	
	$lbs   = (isset($arr_qty["lbs"])? $arr_qty["lbs"]: 0);
	$kgs   = (isset($arr_qty["kgs"])? $arr_qty["kgs"]: 0);
	$yds   = (isset($arr_qty["yds"])? $arr_qty["yds"]: 0);
	$mtr   = (isset($arr_qty["mtr"])? $arr_qty["mtr"]: 0);
	$sqf   = (isset($arr_qty["sqf"])? $arr_qty["sqf"]: 0);
	$sqm   = (isset($arr_qty["sqm"])? $arr_qty["sqm"]: 0);
	$pcs   = (isset($arr_qty["pcs"])? $arr_qty["pcs"]: 0);
	$sheet = (isset($arr_qty["sheet"])? $arr_qty["sheet"]: 0);
	
	$unit_qty = 0;
	
	switch($unitID){
			case 57: 
					$request_lbs = $this_qty; 
					$request_kgs = round($this->LBSConvertKGS($this_qty), $decimal);  
					$request_yds = round($this->LBSConvertYDS($conn, $MMCID, $request_lbs, $prm_maxYD), $decimal);  
					$request_mtr = round($this->YDSConvertMTR($request_yds), $decimal);
					$unit_qty    = $lbs;
					break;
			case 44: 
					$request_kgs = $this_qty; 
					$request_lbs = round($this->KGSConvertLBS($this_qty), $decimal);  
					$request_yds = round($this->LBSConvertYDS($conn, $MMCID, $request_lbs, $prm_maxYD), $decimal);  
					$request_mtr = round($this->YDSConvertMTR($request_yds), $decimal);
					$unit_qty    = $kgs;
					break;
			case 84: 
					$request_yds = $this_qty;  
					$request_lbs = round($this->YDSConvertLBS($conn, $MMCID, $this_qty, $prm_maxYD), $decimal);
					$request_kgs = round($this->LBSConvertKGS($request_lbs), $decimal);  
					$request_mtr = round($this->YDSConvertMTR($request_yds), $decimal);
					$unit_qty    = $yds;
					
					if($unit_qty==0){ 
						// $kgs = round($this->LBSConvertKGS($lbs), $decimal);  
						$unit_qty = round($this->LBSConvertYDS($conn, $MMCID, $lbs, $prm_maxYD), $decimal); 
					}
					// echo "YDS: $yds / $unit_qty << <br/>";
					break;
			case 60: 
					$request_mtr = $this_qty;  
					$request_yds = round($this->MTRConvertYDS($request_mtr), $decimal);
					$request_lbs = round($this->YDSConvertLBS($conn, $MMCID, $request_yds, $prm_maxYD), $decimal);
					$request_kgs = round($this->LBSConvertKGS($request_lbs), $decimal); 
					$unit_qty    = $mtr;
					break;
			case 118: 
					$request_sqf = $this_qty;   
					$request_sqm = round($this->SQFConvertSQM($request_sqf), $decimal);   
					$request_kgs = round($this->SQFConvertKGS($conn, $MMCID, $request_sqf, $prm_maxYD), $decimal);  
					$request_lbs = round($this->KGSConvertLBS($request_kgs), $decimal);
					$unit_qty    = $sqf;
					break;
			case 119: 
					$request_sqm = $this_qty;   
					$request_sqf = round($this->SQMConvertSQF($request_sqf), $decimal);   
					$request_kgs = round($this->SQMConvertKGS($conn, $MMCID, $request_sqm, $prm_maxYD), $decimal);  
					$request_lbs = round($this->KGSConvertLBS($request_kgs), $decimal);  
					$unit_qty    = $sqm;
					break;
			case 120: 
					$request_sheet = $this_qty; 
					$request_yds = $this_qty;     
					$request_kgs = round($this->SQMConvertKGS($conn, $MMCID, $request_sheet, $prm_maxYD), $decimal);  
					$request_lbs = round($this->KGSConvertLBS($request_kgs), $decimal);  
					$unit_qty    = $yds;
					break;
			case 64: 
					$request_pcs = $this_qty;   
					$request_yds = $this_qty;   
					$request_kgs = round($this->SQMConvertKGS($conn, $MMCID, $request_sheet, $prm_maxYD), $decimal);  
					$request_lbs = round($this->KGSConvertLBS($request_kgs), $decimal);  
					$unit_qty    = $yds;
					break;
		}
		
	$arr_result = array("request_kgs"=>$request_kgs, "request_lbs"=>$request_lbs, "request_yds"=>$request_yds, "request_mtr"=>$request_mtr, "request_sqf"=>$request_sqf, "request_sqm"=>$request_sqm, "request_pcs"=>$request_pcs, "request_sheet"=>$request_sheet, "unit_qty"=>$unit_qty);
	
	return $arr_result;
	
}

public function KGSConvertLBS($totalKGS){ //material/transfer/wh_save_receiving.php, warehouse/warehouse_class.php
	$totalLBS = $totalKGS * 2.20462262;
	
	return $totalLBS;
}

public function LBSConvertKGS($totalLBS){ //material/transfer/wh_save_receiving.php
	// echo "totalLBS: $totalLBS << <br/>";
	$totalKGS = $totalLBS / 2.20462262;
	
	return $totalKGS;
}
	
public function LBSConvertYDS($conn, $MMCID, $totalLBS, $prm_maxYD=""){ // purchase/mpurchase_class.php, mpo/prebooking_ajax_submit.php, warehouse/gmt_submit.php, material/transfer/wh_save_receiving.php
	$prm_maxYD = (trim($prm_maxYD)==""? 0: $prm_maxYD);
	
	$sqlCheckConvert = "select MMCID, mmd.max_weight_gm, mmd.max_weight_yard, mmd.ExternalWidth, mmd.multiplier 
									from tblmm_color mmc
									inner join tblmm_detail mmd ON mmc.MMID = mmd.MMID
									WHERE MMCID  IN ($MMCID)";
	$resultConvert = $conn->query($sqlCheckConvert);
	$rowConvert = $resultConvert->fetch(PDO::FETCH_ASSOC);
		$maxGM = $rowConvert["max_weight_gm"];
		$maxYD = $rowConvert["max_weight_yard"];
		$extWidth = $rowConvert["ExternalWidth"];
		$multiplier = $rowConvert["multiplier"];
		$multiplier = ($multiplier=="" || $multiplier==0? 1: $multiplier);
		
	$this_maxYD = ($prm_maxYD==0? $maxYD: $prm_maxYD);
	
	// $lbsAns2 = $maxGM * $extWidth * 0.0232 * 2.2046 / 1000;
	// $ydsAns2 = $totalLBS / $lbsAns2;
	
	$lbsAns = ($this_maxYD * $multiplier) * 2.2046 / 1000;  //--------1 yds = ? lbs
	$ydsAns = ($lbsAns==0? 0: $totalLBS / $lbsAns);
	
	// echo "$maxGM x $extWidth x 0.0232 x 2.2046  / 1000 = $lbsAns2 [$ydsAns2]  <br/>";
	// echo "($maxYD x $multiplier) x 2.2046 / 1000 = $lbsAns [$ydsAns] <br/>";
	return number_format((float)$ydsAns, 4, '.', '');
}

public function YDSConvertLBS($conn, $MMCID, $totalYDS, $prm_maxYD=""){ // purchase/mpurchase_class.php, mpo/prebooking_ajax_submit.php
	$prm_maxYD = (trim($prm_maxYD)==""? 0: $prm_maxYD);
	
	$sqlCheckConvert = "select MMCID, mmd.max_weight_gm, mmd.max_weight_yard, mmd.ExternalWidth, mmd.multiplier 
									from tblmm_color mmc
									inner join tblmm_detail mmd ON mmc.MMID = mmd.MMID
									WHERE MMCID IN ($MMCID)";
	$resultConvert = $conn->query($sqlCheckConvert);
	$rowConvert = $resultConvert->fetch(PDO::FETCH_ASSOC);
		$maxGM = $rowConvert["max_weight_gm"];
		$maxYD = $rowConvert["max_weight_yard"];
		$extWidth = $rowConvert["ExternalWidth"];
		$multiplier = $rowConvert["multiplier"];
		$multiplier = ($multiplier=="" || $multiplier==0? 1: $multiplier);
		
	$this_maxYD = ($prm_maxYD==0? $maxYD: $prm_maxYD);
				
	$lbsAns = ($this_maxYD * $multiplier) * 2.2046 / 1000;  //--------1 yds = ? lbs
	$final_lbs = $totalYDS * $lbsAns;
	return number_format((float)$final_lbs, 4, '.', '');
	
	// （62 X 375 / 43.05） X 0.9688 X（1 + 3.89/100）X 0.0022046) = 1.19836
	// consumption = (((BWidth X BWeight) / 43.05) X BLength X (1 + (BConsumptionA / 100)) X 0.0022046); william quotation formula
}

public function YDSConvertMTR($totalYDS){ //warehouse/warehouse_class.php
	$mtr_qty = $totalYDS * 0.9144;
	
	return $mtr_qty;
}

public function MTRConvertYDS($totalMTR){ 
	$yds_qty = $totalMTR / 0.9144;
	
	return $yds_qty;
}

public function SQFConvertSQM($totalsqf){ 
	$totalsqm = $totalsqf * 0.092903;
	
	return $totalsqm;
}

public function SQFConvertKGS($conn, $MMCID, $totalsqf, $prm_maxYD=""){ 
	$prm_maxYD = (trim($prm_maxYD)==""? 0: $prm_maxYD);
	
	$sqlCheckConvert = "select MMCID, mmd.max_weight_gm, mmd.max_weight_yard, mmd.ExternalWidth, mmd.multiplier 
									from tblmm_color mmc
									inner join tblmm_detail mmd ON mmc.MMID = mmd.MMID
									WHERE MMCID IN ($MMCID)";
	$resultConvert = $conn->query($sqlCheckConvert);
	$rowConvert = $resultConvert->fetch(PDO::FETCH_ASSOC);
		$maxGM = $rowConvert["max_weight_gm"];
		$maxYD = $rowConvert["max_weight_yard"];
		$extWidth = $rowConvert["ExternalWidth"];
		$multiplier = $rowConvert["multiplier"];
		
	$this_maxYD = ($prm_maxYD==0? $maxYD: $prm_maxYD);
	
	$totalkgs = round($totalsqf * $this_maxYD / 1000, 3);
	
	return $totalkgs;
}

public function KGSConvertSQF($conn, $MMCID, $totalkgs, $prm_maxYD=""){ 
	$prm_maxYD = (trim($prm_maxYD)==""? 0: $prm_maxYD);
	
	$sqlCheckConvert = "select MMCID, mmd.max_weight_gm, mmd.max_weight_yard, mmd.ExternalWidth, mmd.multiplier 
									from tblmm_color mmc
									inner join tblmm_detail mmd ON mmc.MMID = mmd.MMID
									WHERE MMCID IN ($MMCID)";
	$resultConvert = $conn->query($sqlCheckConvert);
	$rowConvert = $resultConvert->fetch(PDO::FETCH_ASSOC);
		$maxGM = $rowConvert["max_weight_gm"];
		$maxYD = $rowConvert["max_weight_yard"];
		$extWidth = $rowConvert["ExternalWidth"];
		$multiplier = $rowConvert["multiplier"];
		
	$this_maxYD = ($prm_maxYD==0? $maxYD: $prm_maxYD);
	
	$totalsqf = round($totalkgs * 1000 / $this_maxYD , 3);
	
	return $totalsqf;
}

public function SQMConvertSQF($totalsqm){ 
	$totalsqf = $totalsqm * 10.7639;
	
	return $totalsqf;
}

public function SQMConvertKGS($conn, $MMCID, $totalsqm, $prm_maxYD=""){ 
	$prm_maxYD = (trim($prm_maxYD)==""? 0: $prm_maxYD);
	
	$sqlCheckConvert = "select MMCID, mmd.max_weight_gm, mmd.max_weight_yard, mmd.ExternalWidth, mmd.multiplier 
									from tblmm_color mmc
									inner join tblmm_detail mmd ON mmc.MMID = mmd.MMID
									WHERE MMCID IN ($MMCID)";
	$resultConvert = $conn->query($sqlCheckConvert);
	$rowConvert = $resultConvert->fetch(PDO::FETCH_ASSOC);
		$maxGM = $rowConvert["max_weight_gm"];
		$maxYD = $rowConvert["max_weight_yard"];
		$extWidth = $rowConvert["ExternalWidth"];
		$multiplier = $rowConvert["multiplier"];
		
	$this_maxYD = ($prm_maxYD==0? $maxYD: $prm_maxYD);
	
	$totalkgs = round($totalsqm * $this_maxYD / 1000, 3);
	
	return $totalkgs;
}

public function KGSConvertSQM($conn, $MMCID, $totalkgs, $prm_maxYD=""){ 
	$prm_maxYD = (trim($prm_maxYD)==""? 0: $prm_maxYD);
	
	$sqlCheckConvert = "select MMCID, mmd.max_weight_gm, mmd.max_weight_yard, mmd.ExternalWidth, mmd.multiplier 
									from tblmm_color mmc
									inner join tblmm_detail mmd ON mmc.MMID = mmd.MMID
									WHERE MMCID IN ($MMCID)";
	$resultConvert = $conn->query($sqlCheckConvert);
	$rowConvert = $resultConvert->fetch(PDO::FETCH_ASSOC);
		$maxGM = $rowConvert["max_weight_gm"];
		$maxYD = $rowConvert["max_weight_yard"];
		$extWidth = $rowConvert["ExternalWidth"];
		$multiplier = $rowConvert["multiplier"];
		
	$this_maxYD = ($prm_maxYD==0? $maxYD: $prm_maxYD);
	
	$totalsqm = round($totalkgs * 1000 / $this_maxYD , 3);
	
	return $totalsqm;
}

public function convertUnit($unit){
	$unitpcs = intval(preg_replace('/[^0-9]+/', '', $unit), 10);
	$unitpcs = ($unitpcs==0)? 1: $unitpcs;					
									
	return $unitpcs;
}

public function regexUnit($unit){
	$data=$unit;
	$a = substr($data,strpos($data,"(")+1,(strlen($data)-strpos($data,")"))-2);
	$b  = ((implode(preg_split( '/([a-z]+)/i', $a, -1))!=null)?(implode(preg_split( '/([a-z]+)/i', $a, -1))):1);
	return $b;
}

public function packingMethodCalc($orderno, $method, $conn, $shipmentID){
	
	switch($method){
		//---------------------Single Color Ratio Pack----------------------//
		case 1: $sumQty = 0;
				$sqlCountQty = "SELECT smpk.PackingMethod, smp.Orderno, smp.BuyerPO, smpk.PackingRatio, smpk.PackingQty 
							FROM tblshipmentprice smp INNER JOIN
							tblshipmentpacking smpk ON smp.ID = smpk.tblshipmentpriceID
							WHERE smp.Orderno = '$orderno' AND smpk.PackingMethod = '$method' AND smpk.tblshipmentpriceID = '$shipmentID'";
				$resultCountQty = $conn->query($sqlCountQty);
				while($rowCountQty = $resultCountQty->fetch(PDO::FETCH_BOTH)){
					
					$ratio = $rowCountQty["PackingRatio"];
					$qty = $rowCountQty["PackingQty"];
					
					$ratioArray = array();
					$qtyArray = array();
					
					$ratioArray = explode(":",$ratio);
					$qtyArray = explode(":",$qty);
					
					$countRatio = count($ratioArray);
					$countQty = count($qtyArray);
					
					$totalRatio = 0;
					$totalQty = 0;
					
					for($rr=0;$rr<$countRatio;$rr++){
						$totalRatio+=$ratioArray[$rr];
						$totalQty+=$qtyArray[$rr];
					}//---end for loop---//
					
					$singleQty = $totalQty / $totalRatio;
					$sumQty += $singleQty;
					
					//echo "$ratio	|	$qty	|	$countRatio:$countQty	|	$totalRatio:$totalQty	|	$singleQty<br/>";
					
				}//---end while loop---//
				return $sumQty;
				break;
		
		//---------------------Single Color Single Pack----------------------//		
		case 2: $sumQty = 0; $totalCtn = 0;
				$sqlCountQty = "SELECT smpk.PackingMethod, smp.Orderno, smp.BuyerPO, smpk.PackingRatio, smpk.PackingQty, smpk.TotalCtn
							FROM tblshipmentprice smp INNER JOIN
							tblshipmentpacking smpk ON smp.ID = smpk.tblshipmentpriceID
							WHERE smp.Orderno = '$orderno' AND smpk.PackingMethod = '$method' AND smpk.tblshipmentpriceID = '$shipmentID'";
				$resultCountQty = $conn->query($sqlCountQty);
				while($rowCountQty = $resultCountQty->fetch(PDO::FETCH_BOTH)){
					
					$ratio = $rowCountQty["PackingRatio"];
					$qty = $rowCountQty["PackingQty"];
					$totalCtn = $rowCountQty["TotalCtn"];
					
					$ratioArray = array();
					$qtyArray = array();
					
					$ratioArray = explode(":",$ratio);
					$qtyArray = explode(":",$qty);
					
					$countRatio = count($ratioArray);
					$countQty = count($qtyArray);
					
					$totalRatio = 0;
					$totalQty = 0;
					
					for($rr=0;$rr<$countRatio;$rr++){
						if($ratioArray[$rr]!=0){
							$tempSum = $qtyArray[$rr]/$ratioArray[$rr];
							$totalQty+=$tempSum;
						}
					}//---end for loop---//
					
					$sumQty += $totalQty;
					
				}//---end while loop---//
				//return $sumQty;
				return $totalCtn;
				break;
		//---------------------Multi Color Ratio Pack----------------------//
		case 3:$sumQty = 0;
				$sqlCountQty = "SELECT smpk.PackingMethod, smp.Orderno, smp.BuyerPO, smpk.PackingRatio, smpk.PackingQty 
							FROM tblshipmentprice smp INNER JOIN
							tblshipmentpacking smpk ON smp.ID = smpk.tblshipmentpriceID
							WHERE smp.Orderno = '$orderno' AND smpk.PackingMethod = '$method' AND smpk.tblshipmentpriceID = '$shipmentID'";
				$resultCountQty = $conn->query($sqlCountQty);
				while($rowCountQty = $resultCountQty->fetch(PDO::FETCH_BOTH)){
					
					$ratio = $rowCountQty["PackingRatio"];
					$qty = $rowCountQty["PackingQty"];
					
					$ratioArray = array();
					$qtyArray = array();
					
					$ratioArray = explode(":",$ratio);
					$qtyArray = explode(":",$qty);
					
					$countRatio = count($ratioArray);
					$countQty = count($qtyArray);
					
					$totalQty = 0;
					$tempSum = 0;
					
					for($rr=0;$rr<$countRatio;$rr++){
						if($ratioArray[$rr]!=0){
							$tempSum = $qtyArray[$rr]/$ratioArray[$rr];
						}
					}//---end for loop---//
					
					$sumQty += $tempSum;
					
				}//---end while loop---//
				return $sumQty;
				break;
	
	
	}//---end switch---//


}//*/

public function existUser($acctid, $conn){
	$sql="";
	$sql = "select ID from tbllogonhistory 
			where AcctID=:acctid and LogoutTime is null and LoginStatus=1 
			order by ID DeSC limit 1";
	$result = $conn->prepare($sql);
	$result->bindParam(":acctid", $acctid);
	$result->execute();
	// $result= $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$count = $result->rowCount();
	($count>0) ? $ID = $row['ID'] : $ID=0;

	
	if ($count>0)
	{
		$exist = "True:$ID";
	}else{
		$exist = "False:$ID";
	}
		return $exist;
}

public function MaxOnlineUser($conn){
	// $sql="";
	// $sql = "select count(*) from tbllogonhistory where LogoutTime is null and LoginStatus=1";
	// $result=$conn->query($sql);
	// $count=$result->rowCount();
	
	// if ($count>100)
	// {
		// $Max = "True";
	// }else{
		$Max = "False";
	// }
		return $Max;
}

public function FailedLogin($acctid, $conn){
	$sql="";

	// $sql_1 = "select u.updatedDate, max(l.LogonTime) as logontime 
	// 		from tbluseraccount u 
	// 		inner join tbllogonhistory l ON l.AcctID = u.AcctID
	// 		where u.AcctID=2 and u.StatusID=1 and l.LoginStatus=1;";
	// $result_1 = $conn->query($sql_1);
	// $row_1 = $result_1->fetch(PDO::FETCH_ASSOC);
	// $acctid_updatedDate = $row_1["updatedDate"];


	$sql = "SELECT * FROM tbllogonhistory WHERE AcctID=$acctid and LogonTime > DATE_SUB(now(), INTERVAL 3 MINUTE)";
	$result=$conn->query($sql);
	$count=$result->rowCount();
	
	if ($count>=4)
	{
		$failed = "True";
	}else{
		$failed = "False";
	}
		return $failed;
}

public function updateLastActiveTime($lastID,$conn){
	$sql="";
	$dt = new DateTime(null, new DateTimeZone('Asia/Kuala_lumpur'));
	//$dt = new DateTime();
	$now= $dt->format('Y-m-d H:i:s');
	
	$sql = "Update tbllogonhistory set LastActiveTime='$now' where ID=$lastID";
	$result=$conn->prepare($sql);
	$result->execute();
}

public function validUser($lastID,$conn){
	$sql="";	
	$sql = "select * from tbllogonhistory where ID='$lastID' and LogoutTime is not Null";
	$result=$conn->query($sql);
	$count=$result->rowCount();
	
	if ($count>0) // killed
	{
		$killed = "True";
	}else{
		$killed = "False";
	}
		return $killed;
}

public function func_logoffOneItem($conn,$acctID,$screen,$item){
	$dt = new DateTime();
	$now= $dt->format('Y-m-d H:i:s');
	// echo "<br/>";
	// echo "<br/>";
	// echo "<br/>";
	// echo "<br/>";
	// echo "<br/>";
	// echo "<br/>";
	// echo "<br/>";
	// echo "<br/>";
	
	$sql = "UPDATE tblLog lg
			INNER JOIN tbllogonhistory lgh ON lgh.ID = lg.LoginID
			SET lg.isActive=0, lg.TimeOut='$now'
			WHERE lg.TimeOut is null AND lgh.AcctID='$acctID' AND lg.item!='$item' AND screenNo='$screen'";
	$result=$conn->prepare($sql);
	$result->execute();
	$conn=null;
}

public function func_logoffOneItemOnly($conn,$acctID,$screen,$item){
	$dt = new DateTime();
	$now= $dt->format('Y-m-d H:i:s');
	// echo "<br/>";
	// echo "<br/>";
	// echo "<br/>";
	// echo "<br/>";
	// echo "<br/>";
	// echo "<br/>";
	// echo "<br/>";
	// echo "<br/>";
	
	$sql = "UPDATE tblLog lg
			INNER JOIN tbllogonhistory lgh ON lgh.ID = lg.LoginID
			SET lg.isActive=0, lg.TimeOut='$now'
			WHERE lg.TimeOut is null AND lgh.AcctID='$acctID' AND lg.item='$item' AND screenNo='$screen'";
	$result=$conn->prepare($sql);
	$result->execute();
}

public function noEdited($lastID,$acctID,$screenNo,$item,$conn){
	//tblLog: LoginID(lastID),screenNo,item,isActive,TimeIn
	//LoginID=$lastID and 

	$sql = "";	
	$dt  = new DateTime();
	$now = $dt->format('Y-m-d H:i:s');
	$sql = "select log.*, his.AcctID, a.UserFullName, his.AcctID, concat(his.AcctID,',',a.UserFullName,' (',log.TimeIn,')') as x 
			from tblLog log
			inner join tbllogonhistory his ON his.ID = log.LoginID
			inner join tbluseraccount a On a.AcctID = his.AcctID
			where screenNo=$screenNo and TimeOut is null and log.LoginID<>$lastID
			and isActive=1 and item='$item' and Date(TimeIn)=CURDATE()";
	
	// echo "<pre>$sql</pre>";
	$result = $conn->query($sql);
	$row    = $result->fetch(PDO::FETCH_ASSOC);
	$count  = $result->rowCount();
	
	$AcctID       = $row['AcctID'];
	$UserFullName = $row['UserFullName'];
	$TimeIn       = $row['TimeIn'];
	$TimeIn       = date("Y-m-d H:i:s", strtotime($TimeIn.' '.$this->clienttimezone.' hours'));
	
	($count>0) ? $name = "$AcctID,$UserFullName ($TimeIn)" : $name='';

	if (($count>0) && ($item<>"New Order")) // no edit
	{
		$edit = "True,$name";
	}
	else{
		$edit = "False";
		
		$sql2 = "select log.*, his.AcctID, a.UserFullName, his.AcctID, concat(his.AcctID,',',a.UserFullName,' (',log.TimeIn,')') as x 
			from tblLog log
			inner join tbllogonhistory his ON his.ID = log.LoginID
			inner join tbluseraccount a On a.AcctID = his.AcctID
			where screenNo='$screenNo' and TimeOut is null and log.LoginID='$lastID'
			and isActive=1 and item='$item' and Date(TimeIn)=CURDATE()";
		$result2 = $conn->query($sql2);
		$count2  = $result2->rowCount();

		if($count2==0){
			$sql = "Insert into tblLog (LoginID, screenNo,item,isActive,TimeIn)  
					values($lastID,$screenNo,'$item',1,'$now')";
			$result=$conn->prepare($sql);
			$result->execute();
		}
	}
    // $edit = "False";
		return $edit;
}

public function updateLog($lastID,$screenNo,$item,$conn){

	//tblLog: LoginID(lastID),screenNo,item,isActive,DateTime
	$sql="";
	$dt = new DateTime();
	$now= $dt->format('Y-m-d H:i:s');
	
	$sql = "Update tblLog set isActive=0 , TimeOut ='$now' where LoginID=$lastID and screenNo=$screenNo 
			and item='$item' and Date(TimeIn)=CURDATE() and TimeOut is Null";

	$result=$conn->prepare($sql);
	$result->execute();
}

public function updateLogIn($lastID,$screenNo,$item,$conn){

	//tblLog: LoginID(lastID),screenNo,item,isActive,DateTime
	$sql="";
	$dt = new DateTime();
	$now= $dt->format('Y-m-d H:i:s');
	
	$sql = "Update tblLog set isActive=0 , TimeOut ='$now' where LoginID=$lastID and screenNo in ($screenNo) 
			and TimeOut is Null";

	$result=$conn->prepare($sql);
	$result->execute();
	$conn=null;
}

public function updateAllLog($lastID,$conn){

	//tblLog: LoginID(lastID),screenNo,item,isActive,TimeIn, TimeOut
	$sql="";
	$dt = new DateTime();
	$now= $dt->format('Y-m-d H:i:s');
	
	$sql = "Update tblLog set isActive=0, TimeOut ='$now' where LoginID=$lastID and TimeOut is Null";
	$result=$conn->prepare($sql);
	$result->execute();
	$conn=null;
}

public function updateAllLogByAcctID($acctid, $conn){
	$dt = new DateTime();
	$now= $dt->format('Y-m-d H:i:s');

	$sql = "UPDATE tblLog lg
			INNER JOIN tbllogonhistory lgh ON lgh.ID = lg.LoginID
			SET lg.isActive=0, lg.TimeOut='$now'
			WHERE lg.TimeOut is null AND lgh.AcctID='$acctid'";
	$result=$conn->prepare($sql);
	$result->execute();
	$conn=null;
}

public function screenUser($acctid,$conn){

	$sql="";	
	$sql = "select group_concat(ScreenID) as arr from ctrltrustee where AcctID=$acctid";
	$result=$conn->query($sql);
	$row =$result->fetch(PDO::FETCH_ASSOC);
	//$count=$result->rowCount();
	
	$t=$row['arr'];
	$arr=explode(',',$t);
	
	return $arr;
}

public function chkInstore($po, $POHID,$conn){
	$sql="";	
	if ($po==1){
		$sql = "select count(*) as count from tblmpo_detail where MPDID in (select POID from tblmr_detail WHERE MPOHID='$POHID') 
				and MPOHID='$POHID'";
	}else{			
		$sql = "select count(*) as count from tblapo_detail where APOHID in (select APOHID from tblacc_receive) 
				and APOHID='$POHID'";
	}
	$result=$conn->query($sql);
	$row =$result->fetch(PDO::FETCH_ASSOC);

	$count=$row['count'];

	return $count;
}

public function transferOrder($orderno, $QDID, $conn,$submitrange, $acctid, $detail){ //transfer to order	
	$copybom = new copy_function();

	//find garmentID (2018-02-06 w)
	$garq = $conn->prepare("SELECT garmentID FROM tblgarment WHERE QDID = :QDID LIMIT 1");
	$garq->bindParam(':QDID', $QDID);
	$garq->execute();
	$grow = $garq->fetch(PDO::FETCH_ASSOC);
	$garmentID = $grow["garmentID"];	
	
	//$submitrange = 1 - single order, =2 is multiple order
		
	$strmp = "SELECT MPID FROM tblmpurchase ORDER BY MPID DESC LIMIT 1";
	$resultmp = $conn->query($strmp);
	$resultfmp= $resultmp->fetch(PDO::FETCH_ASSOC);
	$MPID = $resultfmp["MPID"];
	
	//data for testing purpose
	$createdBy = $acctid;
	$updatedBy = $acctid;
	$Status = 0;
	$updatedDate = date("Y-m-d H:i:s");

	//fabric
	//fabric type - tblfabtype
	$fabricq = $conn->prepare('SELECT d.ArrFabric, d.ArrFID, d.ArrFType, d.ArrYY, d.ArrConsumption, d.ArrPla, d.ArrFabCost, d.ArrCIF, d.Qty, h.CurrencyID, h.Qunit, h.QHID, d.refID FROM tblq_detail AS d INNER JOIN tblq_header AS h ON h.QHID = d.QHID WHERE d.QDID = :QDID');
	$fabricq->bindParam(':QDID', $QDID);
	$fabricq->execute();	

	$rowf= $fabricq->fetch(PDO::FETCH_ASSOC);
	$ArrFabricf = $rowf["ArrFabric"];
	$ArrFTypef = $rowf["ArrFType"];
	$ArrYYf = $rowf["ArrYY"];
	$ArrCIF = $rowf["ArrCIF"];
	$Qtyf = $rowf["Qty"];
	$refID = $rowf["refID"];
	$ArrFID = $rowf["ArrFID"];
	
	$arrf1 = explode(':', $ArrFabricf);
	$arrf2 = explode(':', $ArrFTypef);
	$arrf3 = explode(':', $ArrYYf);
	$arrf4 = explode(':',$ArrCIF);
	$arrf5 = explode(':',$ArrFID);
	
	$CurrencyID = $rowf["CurrencyID"];
	$QHID = $rowf["QHID"];
	$Qunit = $rowf["Qunit"];
	
	$totalYY = 0;
	
	$numfabric = count($arrf1);
	
	//update tblnewenquiry status = 8 (2017-09-08 w)
	$nequery = $conn->prepare("UPDATE tblnewenquiry SET statusID = '8' WHERE Orderno = :refID");
	$nequery->bindParam(':refID', $refID);
	$nequery->execute();
	
	for($i=0; $i < $numfabric; $i++){
		//bom (2017-09-08 w)
		$fid = $arrf5[$i];
		$fid = explode("-", $fid);
		$fid0 = $fid[0];
		$fid1 = $fid[1];
		$fid2 = $fid[2];
		
		//$fYY = $arrf3[$i];
		//$totalYY += $fYY;
		$totalYY = $arrf3[$i];
		$unitprice = ($arrf4[$i]=="null" || $arrf4[$i]=="" ? 0.00 : $arrf4[$i] ); 

		if ($arrf2[$i] == "empty"){
			$farr = $arrf1[$i];	
			if(strpos($farr, '^^') !== false) {
				$fdata = explode('^^', $farr);
			}else{
				$fdata = explode('-', $farr);
			}			
			$ftop = $fdata[0];
			$fbottom = $fdata[1];
			$fspender = $fdata[2];
			$ftype = $fdata[3];
			$fdescription = $fdata[4];
			$fint = $fdata[7];
			$fext = $fdata[5];
			$fmin = $fdata[8];
			$fmax = $fdata[6];
			$mmcode = $fdata[9];
			
			//Fabric type start
			//check fabric type exist or not
			$ftypeq = $conn->prepare("SELECT ID FROM tblfabtype WHERE Description = :des AND flag_new = '1' ORDER BY flag_new DESC LIMIT 1");
			$ftypeq->bindParam(':des', $fdescription);
			$ftypeq->execute();
			$numf_result = $ftypeq->rowCount();
										
			//if not
			$detailID = 0;	//2017-07-27 w (avoid empty fabric type)
			if($fdescription != ""){
			if(($numf_result == 0)){
				$strf = "SELECT count(ID) as num FROM tblfabtype";
				$resultf = $conn->query($strf);
				$resultfabric= $resultf->fetch(PDO::FETCH_ASSOC);
				$fID = $resultfabric["num"];
				$fID = $fID + 1;
				
				$fstatus = 1;
				
				//Insert query
				$finsert = $conn->prepare('INSERT INTO tblfabtype (ID, Description, StatusID) VALUES (:ID, :Description, :statusID)');
				$finsert->bindParam(':ID', $fID);
				$finsert->bindParam(':Description', $fdescription);
				$finsert->bindParam(':statusID', $fstatus);
				// $finsert->execute();
			}else{
				//if exist, cancel insert action
				$ftyperesult= $ftypeq->fetch(PDO::FETCH_ASSOC);
				$fID = $ftyperesult['ID'];
			}
			}else{
				$fID = "empty";
			}
			//fabric type end
			

			
			//tblmm_detail start
			//check mmdetail exist or not
			if($fID != "empty"){
			$mmdetailq = $conn->prepare('SELECT MMID FROM tblmm_detail WHERE
			FabricContent = :des AND
			FabricTypeID = :ID AND
			mmcode = :mmcode AND
			min_weight_gm = :min_weight_gm AND 
			max_weight_gm = :max_weight_gm AND 
			InternalWidth = :InternalWidth AND 
			ExternalWidth = :ExternalWidth AND 
			TopYarn = :TopYarn AND 
			BottomYarn = :BottomYarn AND 
			spandex = :spandex AND 
			currencyID = :currencyID 
			LIMIT 1');
			$mmdetailq->bindParam(':des', $ftype);
			$mmdetailq->bindParam(':ID', $fID);
			//$mmdetailq->bindParam(':MMTID', $topID);
			$mmdetailq->bindParam(':mmcode', $mmcode);
			$mmdetailq->bindParam(':min_weight_gm', $fmin);
			$mmdetailq->bindParam(':max_weight_gm', $fmax);
			$mmdetailq->bindParam(':InternalWidth', $fint);
			$mmdetailq->bindParam(':ExternalWidth', $fext);
			$mmdetailq->bindParam(':TopYarn', $ftop);
			$mmdetailq->bindParam(':BottomYarn', $fbottom);
			$mmdetailq->bindParam(':spandex', $fspender);
			$mmdetailq->bindParam(':currencyID', $CurrencyID);
			$mmdetailq->execute();
			$numd_result = $mmdetailq->rowCount();
			
			//if not
			if($numd_result == 0){
				$strdetail = "SELECT MAX(CAST(MMID AS SIGNED)) as num FROM tblmm_detail";
				$resultdetail = $conn->query($strdetail);
				$resultd= $resultdetail->fetch(PDO::FETCH_ASSOC);
				$detailIDs = $resultd["num"];
				$detailID = $detailIDs + 1;
				
				$statusID = 1;
				$Shrinkage = "W/L 8%";
				$multiplier = "1";
				$basic_unit = "57";
				$price_unitID = "57";
				$MOQ = "5";
				$MPQ = "30";
				
				$yardmin = $fmin * $fext * $multiplier * 0.0232;
				$yardmax = $fmax * $fext * $multiplier * 0.0232;
								
				//Insert query
				$detailinsert = $conn->prepare('INSERT INTO tblmm_detail (MMID, FabricContent, FabricTypeID, statusID, mmcode, min_weight_gm, max_weight_gm, min_weight_yard, max_weight_yard, InternalWidth, ExternalWidth, TopYarn, BottomYarn, spandex, Shrinkage, multiplier, basic_unit, price_unitID, currencyID, MOQ, MPQ, acctID_created, createdDate) VALUES (:MMID, :FabricContent, :FabricTypeID, :statusID, :mmcode, :min_weight_gm, :max_weight_gm, :min_weight_yard, :max_weight_yard, :InternalWidth, :ExternalWidth, :TopYarn, :BottomYarn, :spandex, :Shrinkage, :multiplier, :basic_unit, :price_unitID, :currencyID, :MOQ, :MPQ, :acctID_created, :createdDate)');
				$detailinsert->bindParam(':MMID', $detailID);	
				$detailinsert->bindParam(':FabricContent', $ftype);								
				$detailinsert->bindParam(':FabricTypeID', $fID);												
				//$detailinsert->bindParam(':MMTID', $topID);								
				$detailinsert->bindParam(':statusID', $statusID);
				$detailinsert->bindParam(':mmcode', $mmcode);
				$detailinsert->bindParam(':min_weight_gm', $fmin);
				$detailinsert->bindParam(':max_weight_gm', $fmax);
				$detailinsert->bindParam(':min_weight_yard', $yardmin);
				$detailinsert->bindParam(':max_weight_yard', $yardmax);
				$detailinsert->bindParam(':InternalWidth', $fint);
				$detailinsert->bindParam(':ExternalWidth', $fext);
				$detailinsert->bindParam(':TopYarn', $ftop);
				$detailinsert->bindParam(':BottomYarn', $fbottom);
				$detailinsert->bindParam(':spandex', $fspender);
				$detailinsert->bindParam(':Shrinkage', $Shrinkage);
				$detailinsert->bindParam(':multiplier', $multiplier);
				$detailinsert->bindParam(':basic_unit', $basic_unit);
				$detailinsert->bindParam(':price_unitID', $price_unitID);				
				$detailinsert->bindParam(':currencyID', $CurrencyID);
				$detailinsert->bindParam(':MOQ', $MOQ);				
				$detailinsert->bindParam(':MPQ', $MPQ);
				$detailinsert->bindParam(':acctID_created', $createdBy);
				$detailinsert->bindParam(':createdDate', $updatedDate);
				// $detailinsert->execute();
			}else{
				//if exist, cancel insert action
				$detailresult= $mmdetailq->fetch(PDO::FETCH_ASSOC);
				$detailID = $detailresult['MMID'];
			}
			}
			//tblmm_detail end
			
			//tblmpurchase start
			$isLocked = 0;
			$statusID = 4;
			
			if($Qunit == 1){
				$dozPcs = 1;
			}else{
				$dozPcs = 12;
			}
			
			$allSize = $totalYY;
			
			
			
			//update tblbomheader	(2017-07-20 w)
			$bomh = $conn->prepare("SELECT bomID FROM tblbomheader WHERE bomID = :bomID LIMIT 1");
			$bomh->bindParam(':bomID', $orderno);
			$bomh->execute();
			$bomhno = $bomh->rowCount();		
			
			if($bomhno != 1){
				$bomIDs = $orderno.".".date("ym");
				$bstatus = 4;
				$bomq = $conn->prepare("INSERT INTO tblbomheader (bomID, rbomID, statusID, createdby, createddate, lastupdateby, lastupdateddate) VALUES (:bomID, :rbomID, :statusID, :createdBy, :createdDate, :updateBy, :updateDate)");
				$bomq->bindParam(':bomID', $orderno);
				$bomq->bindParam(':rbomID', $bomIDs);
				$bomq->bindParam(':statusID', $bstatus);				
				$bomq->bindParam(':createdBy', $updatedBy);
				$bomq->bindParam(':createdDate', $updatedDate);
				$bomq->bindParam(':updateBy', $updatedBy);
				$bomq->bindParam(':updateDate', $updatedDate);
				$bomq->execute();
			}
		
			//select from existing BOM mode (2017-09-08 w)
			if($fid0 != "0"){
				//echo "$orderno / $fid0 / $fid1 / $fid2<br/>";
				$datetime = $this->TimeNow();
				//tblbomfabric
				if($fid0 == "1"){
					$copybom->copy_partial($orderno, $conn, $acctid, $fid1, $fid2, "", $datetime);
				//tblbombinding	
				}else{
					$copybom->copy_partial($orderno, $conn, $acctid, "", "", $fid1, $datetime);
				//tblbomflatyoko
				}
				
				//now transfer again, only for fabric from bom (2017-09-08 w)
				//======(NOT transfer to tblmpurchase (2017-07-18 w))======
				//-------- Validate if not fabric details by ckwai (14-11-2016) *quotation possible not fill fabric to convert to Potential --------//
				if($fID!="empty"){
					$MPID = $MPID + 1;
					$mpurchaseinsert = $conn->prepare('INSERT INTO tblmpurchase (MPID, orderno, MMID, mmcode, prodQty, dozPcs, allSize, currencyID, unitprice,createdBy, createdDate, updatedBy, updatedDate, isLocked, statusID) VALUES (:MPID, :orderno, :MMID, :mmcode, :prodQty, :dozPcs, :allSize, :currencyID, :unitprice, :createdBy, :createdDate, :updatedBy, :updatedDate, :isLocked, :statusID)');
					$mpurchaseinsert->bindParam(':MPID', $MPID);								
					$mpurchaseinsert->bindParam(':orderno', $orderno);								
					$mpurchaseinsert->bindParam(':MMID', $detailID);
					$mpurchaseinsert->bindParam(':mmcode', $mmcode);
					$mpurchaseinsert->bindParam(':prodQty', $Qtyf);	
					$mpurchaseinsert->bindParam(':dozPcs', $dozPcs);								
					$mpurchaseinsert->bindParam(':allSize', $allSize);	
					$mpurchaseinsert->bindParam(':currencyID', $CurrencyID);	
					$mpurchaseinsert->bindparam(':unitprice',$unitprice);
					$mpurchaseinsert->bindParam(':createdBy', $createdBy);								
					$mpurchaseinsert->bindParam(':createdDate', $updatedDate);	
					$mpurchaseinsert->bindParam(':updatedBy', $updatedBy);								
					$mpurchaseinsert->bindParam(':updatedDate', $updatedDate);	
					$mpurchaseinsert->bindParam(':isLocked', $isLocked);								
					$mpurchaseinsert->bindParam(':statusID', $statusID);	
					//$mpurchaseinsert->execute();
				}

			//old mode: key in fabric
			}else{
		
				//get latest bom ID
				$strmp = "SELECT bomfabricID FROM tblbomfabric ORDER BY bomfabricID DESC LIMIT 1";
				$resultmp = $conn->query($strmp);
				$resultfmp= $resultmp->fetch(PDO::FETCH_ASSOC);
				$bomfabricID = $resultfmp["bomfabricID"];
				
				$bomfabricID++;
				$subID = 0;
				$bbstatus = "4";
				$bisDefault = "1";
				//insert into tblbomfabric
				$bomfabric = $conn->prepare('INSERT INTO tblbomfabric (bomfabricID, subID, bomID, fabricSpec, statusID, createdBy, createdDate, updateBy, updateDate, isDefault) VALUES (:bomfabricID, :subID, :bomID, :fabricSpec, :statusID, :createdBy, :createdDate, :updateBy, :updateDate, :isDefault)');
				$bomfabric->bindParam(':bomfabricID', $bomfabricID);								
				$bomfabric->bindParam(':subID', $subID);								
				$bomfabric->bindParam(':bomID', $orderno);
				$bomfabric->bindParam(':fabricSpec', $detailID);
				$bomfabric->bindParam(':createdBy', $updatedBy);								
				$bomfabric->bindParam(':createdDate', $updatedDate);	
				$bomfabric->bindParam(':updateBy', $updatedBy);								
				$bomfabric->bindParam(':updateDate', $updatedDate);	
				$bomfabric->bindParam(':statusID', $bbstatus);	
				$bomfabric->bindParam(':isDefault', $bisDefault);	
				//$bomfabric->execute(); //hide by ckwai on 202001031140
			}
	
	


			//tblmpurchase end
		}else if($arrf2[$i] != 0) {

			$detailID=$arrf2[$i];
			
			//tblmpurchase start
			$isLocked = 0;
			$statusID = 4;
			
			if($Qunit == 1){
				$dozPcs = 1;
			}else{
				$dozPcs = 12;
			}
			
			$allSize = $totalYY;
			
			
			$MPID = $MPID + 1;
			$mpurchaseinsert = $conn->prepare('INSERT INTO tblmpurchase (MPID, orderno, MMID, prodQty, dozPcs, allSize, currencyID, unitprice,createdBy, createdDate, updatedBy, updatedDate, isLocked, statusID) VALUES (:MPID, :orderno, :MMID, :prodQty, :dozPcs, :allSize, :currencyID, :unitprice, :createdBy, :createdDate, :updatedBy, :updatedDate, :isLocked, :statusID)');
			$mpurchaseinsert->bindParam(':MPID', $MPID);								
			$mpurchaseinsert->bindParam(':orderno', $orderno);								
			$mpurchaseinsert->bindParam(':MMID', $detailID);								
			$mpurchaseinsert->bindParam(':prodQty', $Qtyf);	
			$mpurchaseinsert->bindParam(':dozPcs', $dozPcs);								
			$mpurchaseinsert->bindParam(':allSize', $allSize);	
			$mpurchaseinsert->bindParam(':currencyID', $CurrencyID);	
			$mpurchaseinsert->bindparam(':unitprice',$unitprice);
			$mpurchaseinsert->bindParam(':createdBy', $createdBy);								
			$mpurchaseinsert->bindParam(':createdDate', $updatedDate);	
			$mpurchaseinsert->bindParam(':updatedBy', $updatedBy);								
			$mpurchaseinsert->bindParam(':updatedDate', $updatedDate);	
			$mpurchaseinsert->bindParam(':isLocked', $isLocked);								
			$mpurchaseinsert->bindParam(':statusID', $statusID);	
			//$mpurchaseinsert->execute();
		}

		
	}
	
	//new apurchase format (2019-08-14 w)
	echo "$refID / $orderno<br/>";
	$hquery = $conn->prepare("SELECT * FROM tblapurchase WHERE orderno = '$refID' AND statusID NOT IN (6) ORDER BY APID ASC");
	$hquery->execute();
	
	while($hrow = $hquery->fetch(PDO::FETCH_ASSOC)){		
		$strap = "SELECT APID FROM tblapurchase ORDER BY APID DESC LIMIT 1";
		$resultap = $conn->query($strap);
		$resultfap= $resultap->fetch(PDO::FETCH_ASSOC);
		$APID = $resultfap["APID"];
		$APID = $APID + 1;
		
		$tna_item = $hrow["tna_item"];
		$tna_quality = $hrow["tna_quality"];
		$tna_color_ap = $hrow["tna_color_ap"];
		$request_bom = 0;//$hrow["request_bom"];
		$request_thread = $hrow["request_thread"];		
		$bomaccID = 0;//$hrow["bomaccID"];
		$positionID = $hrow["positionID"];
		$part = $hrow["part"];
		$AMID = $hrow["AMID"];
		$AsubtypeID = $hrow["AsubtypeID"];
		$accCode = $hrow["accCode"];
		//$orderno = $hrow["orderno"];
		$byMethod = $hrow["byMethod"];
		$dozPcs = $hrow["dozPcs"];
		$unitID = $hrow["unitID"];
		$allQty = $hrow["allQty"];
		$garmentID = $hrow["garmentID"];
		$shipmentpriceID = $hrow["shipmentpriceID"];
		$ASCID = $hrow["ASCID"];
		$statusID = $hrow["statusID"];
		$createdBy = $hrow["createdBy"];
		$createdDate = $hrow["createdDate"];
		// $updatedBy = $hrow["updatedBy"];
		// $updatedDate = $hrow["updatedDate"];
		$currencyID = $hrow["currencyID"];
		$unitprice = $hrow["unitprice"];
		$confirmed_bom_date = $hrow["confirmed_bom_date"];	
		
		$accCode = str_replace('"','', $accCode);

		$apurchaseinsert = $conn->prepare("INSERT INTO tblapurchase (APID, tna_item, tna_quality, tna_color_ap, request_bom, request_thread, bomaccID, positionID, part, AMID, AsubtypeID, accCode, orderno, byMethod, dozPcs, unitID, allQty, garmentID, shipmentpriceID,  ASCID, statusID, createdBy, createdDate, updatedBy, updatedDate, currencyID, unitprice, confirmed_bom_date) VALUES ('$APID', '$tna_item', '$tna_quality', '$tna_color_ap', '$request_bom', '$request_thread', '$bomaccID', '$positionID', '$part', '$AMID', '$AsubtypeID', '$accCode', '$orderno', '$byMethod', '$dozPcs', '$unitID', '$allQty', '$garmentID', '$shipmentpriceID', '$ASCID', '$statusID', '$acctid', now(), '$updatedBy', '$updatedDate', '$currencyID', '$unitprice', '$confirmed_bom_date')");				$apurchaseinsert->execute();
	}
	
	
	//tblapurchase start (old format removed 2019-08-14 w)
	//select acc array from header
	//$aheadquery = "SELECT ArrAcc, CurrencyID FROM tblq_header WHERE QHID = '$QHID' LIMIT 1";
	
	
	// //check duplicate
	// $apurchasecheck = $conn->prepare('SELECT APID FROM tblapurchase WHERE orderno = :orderno LIMIT 1');
	// $apurchasecheck->bindParam(':orderno', $orderno);								
	// $apurchasecheck->execute();
	
	// $apchecker= $apurchasecheck->fetch(PDO::FETCH_ASSOC);
	// $aAPID = $apchecker["APID"];
	// if($aAPID == ""){
	// if ($submitrange == 1){ //single order
		// //$QDIDdata = implode(",", $detail);
		// $QDIDdata = implode("','",$detail);
		// //echo "$QHID $QDID $QDIDdata<br/>";
		// $aheadquery="select count(q.QDID) as Detail, h.CurrencyID,
					// GROUP_CONCAT(q.arrAccCost SEPARATOR ';')as arrAccCost, h.ArrAcc, h.ArrAccType
					// from tblq_header h
					// inner join tblq_detail q On h.QHID = q.QHID 
					// where q.QHID ='$QHID' AND q.QDID IN ('".$QDIDdata."') LIMIT 1";
 	// }else{ //multiple order
 			// $aheadquery="select count(q.QDID) as Detail, h.CurrencyID, 
 						// GROUP_CONCAT(q.arrAccCost SEPARATOR ';')as arrAccCost, h.ArrAcc, h.ArrAccType
						// from tblq_header h
						// inner join tblq_detail q On h.QHID = q.QHID 
						// where q.QDID ='$QDID' LIMIT 1";		
	// }
	
	// $resulahead = $conn->query($aheadquery);
	// $aheadrow = $resulahead->fetch(PDO::FETCH_ASSOC);
	// $ArrAcch = $aheadrow["ArrAcc"];
	// $a2 = $aheadrow["arrAccCost"]; // Cost
	// $ArrAccType = $aheadrow["ArrAccType"]; // Acc Type array (2018-12-17 w)

	// //$detail=$aheadrow["Detail"];
	// $currency = $aheadrow["CurrencyID"];
	// $detail = count($detail);
	
	// $accheadarr = explode(':', $ArrAcch);	
	// $ArrAccType = explode(':', $ArrAccType);						
	// $numacc = count($accheadarr);
	
	// $aCode = array_map (
  			// function ($_) {return explode (',', $_);},
  			// explode (':', $ArrAcch)
			// );
		
	// // 	Array ( [0] => Array ( [0] => 1 [1] => 2CM ) 
	// // 		[1] => Array ( [0] => 3 [1] => 1.5IN ) 
	// // 		[2] => Array ( [0] => 1 [1] => ABC123 ) ) 
		
		// $arrCost = array_map (
	  			// function ($_) {return explode (':', $_);},
	  			// explode (';', $a2)
				// );
		
	// // 	print_r($arrCost);

		// for($i=0; $i < $numacc; $i++){
			// $AMIDarr = $accheadarr[$i];
			// $AMIDdata = explode(',', $AMIDarr);
			// //$AMID = $AMIDdata[0];

			// $amtype = $ArrAccType[$i];	//0: asubtype, 1: amaterial
			// $accCode = $AMIDdata[1];
			
			// //if is asubtype
			// if($amtype == 0){
				// $AMID = null;
				// $asubtype = $AMIDdata[0];
			// }else{
				// //amaterial
				// $AMID = $AMIDdata[0];
				// $findsub = "SELECT AsubtypeID FROM tblamaterial WHERE AMID = '$AMID' LIMIT 1";
				// $findsub = $conn->query($findsub);
				// $resultsub = $findsub->fetch(PDO::FETCH_ASSOC);
				// $asubtype = $resultsub["AsubtypeID"];
			// }
			
			
			
			// if($asubtype != 0){
			
				// $strap = "SELECT APID FROM tblapurchase ORDER BY APID DESC LIMIT 1";
				// $resultap = $conn->query($strap);
				// $resultfap= $resultap->fetch(PDO::FETCH_ASSOC);
				// $APID = $resultfap["APID"];
				// $APID = $APID + 1;
			
				// $bunitquery = "SELECT basic_unit FROM tblamaterial  WHERE AsubtypeID = '$asubtype' LIMIT 1";
				// $resultbunit = $conn->query($bunitquery);
				// $rowbunit= $resultbunit->fetch(PDO::FETCH_ASSOC);
				// $unitID = $rowbunit["basic_unit"];						
			
			// //avoid amaterial error 2017-08-08 w
			// if(($unitID == "") || ($unitID == null)){
				// $unitID = 64;
			// }
			
				// $byMethod = 0;
				// $allQty = 1;
				// $statusID = 4;
			
				// if($Qunit == 1){
					// $dozPcs = 1;
				// }else{
					// $dozPcs = 12;
				// }
				
				// $a=0.00;
				// for ($k=0; $k<$detail; $k++){
					// echo "$k = $a + ".$arrCost[$k][$i]."<br/>";
					// $a=$a+$arrCost[$k][$i];
				// }
				// $unitprice=round(($a/$detail),3);
				
				// //$accCode = $aCode[$i][1];
				// //$zero=null;
				// // echo "$APID - $unitprice - $AMID - $asubtype - $accCode | $a/$detail<br/>";
				
				// if($unitprice > 0){
					// //insert query
					// $apurchaseinsert = $conn->prepare('INSERT INTO tblapurchase (APID, AMID, accCode , orderno, byMethod, dozPcs, unitID, allQty, garmentID, currencyID, unitprice, statusID, createdBy, createdDate, updatedBy, updatedDate, AsubtypeID) VALUES (:APID, :AMID, :accCode, :orderno, :byMethod, :dozPcs, :unitID, :allQty, :garmentID, :currencyID, :unitprice,  :statusID,:createdBy, :createdDate, :updatedBy, :updatedDate, :AsubtypeID)');
					// $apurchaseinsert->bindParam(':APID', $APID);								
					// $apurchaseinsert->bindParam(':AMID', $AMID);	
					// $apurchaseinsert->bindParam(':accCode', $accCode);							
					// $apurchaseinsert->bindParam(':orderno', $orderno);								
					// $apurchaseinsert->bindParam(':byMethod', $byMethod);
					// $apurchaseinsert->bindParam(':dozPcs', $dozPcs);								
					// $apurchaseinsert->bindParam(':unitID', $unitID);
					// $apurchaseinsert->bindParam(':allQty', $allQty);
					// $apurchaseinsert->bindParam(':garmentID', $garmentID);			
					// $apurchaseinsert->bindParam(':currencyID', $currency);
					// $apurchaseinsert->bindParam(':unitprice',$unitprice);							
					// $apurchaseinsert->bindParam(':statusID', $statusID);
					// $apurchaseinsert->bindParam(':createdBy', $createdBy);								
					// $apurchaseinsert->bindParam(':createdDate', $updatedDate);
					// $apurchaseinsert->bindParam(':updatedBy', $updatedBy);								
					// $apurchaseinsert->bindParam(':updatedDate', $updatedDate);						
					// $apurchaseinsert->bindParam(':AsubtypeID', $asubtype);											
					// $apurchaseinsert->execute();
				// }
			// }

		// }
		// }
		// //tblapurchase end (old format removed 2019-08-14 w)
}

//for quotation accessories
public function accprice($subtype, $conn, $mode){
	
	if($mode == "q"){
		$accquery = $conn->prepare("select distinct ast.ID AS AsubtypeID, apo.PODate , apo.APOHID AS orderno, ast.Description as type, ac.Description as content, apod.unitprice, s.SuppName_ENG AS supname, ub.Description AS ubd, up.Description AS upd
					from tblapo_header apo
					LEFT join tblsupplier AS s ON s.SupplierID = apo.SupplierID
					LEFT join tblapo_detail apod On apod.APOHID = apo.APOHID
					LEFT join tblapurchase_detail apd On apd.APDID = apod.APDID
					LEFT join tblapurchase ap On ap.APID = apd.APID
					LEFT join tblamaterial am ON am.AMID = ap.AMID
					LEFT join tblacontent ac On ac.ID = am.contentID
					LEFT join tblasubtype ast On ast.ID = ap.AsubtypeID
					LEFT JOIN tblunit AS ub ON ub.ID = am.basic_unit
					LEFT JOIN tblunit AS up ON up.ID = am.price_unit
					WHERE ast.ID = :subtype
					GROUP BY supname
					order by apo.PODate DESC
					LIMIT 10");
		$accquery->bindParam(':subtype', $subtype);
		$accquery->execute();
		
		$acccount = $accquery->rowCount();
		
		
		if($acccount > 0){
			$accarr = array();
			for($a=0; $a<$acccount; $a++){
				$accrow = $accquery->fetch(PDO::FETCH_ASSOC);
				
				$AsubtypeID = $accrow["AsubtypeID"];
				$PODate = $accrow["PODate"];
				$orderno = $accrow["orderno"];
				$type = $accrow["type"];
				$content = $accrow["content"];
				$unitprice = $accrow["unitprice"];
				$supname = $accrow["supname"];
				$ubd = $accrow["ubd"];
				$upd = $accrow["upd"];	
				
				$accdata = "$PODate|$orderno|$type|$content|$unitprice|$AsubtypeID|$supname|$ubd|$upd";
				
				array_push($accarr, "$accdata");
			}
			
			$result = implode(",,,", $accarr);
			return "$result";
		}else{
			return "0";
		}
	}else{
		return "0";
	}
}
//end for quotation accessories


//for quotation fabric
public function fabprice($content, $fabricdes, $conn, $mode){

	if($mode == "q"){
		$accquery = $conn->prepare("select distinct mpo.PODate, mp.orderno, mm.FabricContent as content, ft.Description as type, mpod.unitprice,GROUP_CONCAT(DISTINCT c.ColorName) as ColorName
									from tblmpo_header mpo
									inner join tblmpo_detail mpod On mpod.MPOHID = mpo.MPOHID
									inner join tblmpurchase_detail mpd On mpd.MPDID = mpod.MPDID
									inner join tblmpurchase mp On mp.MPID = mpd.MPID
									inner join tblmm_detail mm ON mm.MMID = mp.MMID
									inner join tblfabtype ft On ft.ID = mm.FabricTypeID
									inner join tblmm_color mmc ON mmc.MMCID = mpd.MMCID
                                    inner join tblcolor c ON c.ID = mmc.colorID
									where mpod.unitprice>0 and mm.FabricContent like ? AND
									ft.Description LIKE ?
									group by mpo.PODate, mp.orderno, mm.FabricContent, ft.Description , mpod.unitprice
									order by mpo.PODate DESC limit 5");
		$accquery->bindParam(1, $content);
		$accquery->bindParam(2, $fabricdes);
		$accquery->execute();
		
		$acccount = $accquery->rowCount();
		
		
		if($acccount > 0){
			$accarr = array();
			for($a=0; $a<$acccount; $a++){
				$accrow = $accquery->fetch(PDO::FETCH_ASSOC);
				
				$PODate = $accrow["PODate"];
				$orderno = $accrow["orderno"];
				$type = $accrow["type"];
				$content = $accrow["content"];
				$unitprice = $accrow["unitprice"];
				$ColorName = $accrow["ColorName"];
				
				$accdata = "$PODate|$orderno|$type|$content <br>($ColorName)|$unitprice";
				
				array_push($accarr, "$accdata");
			}
			
			$result = implode(";;;", $accarr);
			return "$result";
		}else{
			return "0";
		}
	}else{
		return "0";
	}
}
//end for quotation fabric


// public function updateCostmargin($orderno,$conn){


// // try{
// // $conn->beginTransaction();

// $cost="select c.ArrPEWCost, c.TransInPCT, t1.cRate as crate1, t1.Rate as rate1,
// 			c.TransOutPCT,t2.cRate as crate2, t2.Rate as rate2, c.FinanceCostPCT, c.cmCostPC, c.GMTest, c.Quota, 
// 			c.RDCPCT, c.BPCostPCT, c.PDCostPCT, c.ADCostPCT, c.expfcost, c.AgentCommPCT, 
// 			c.fobpc, o.Qunit,
// 			(select count(styleno) from tblgarment where orderno=o.orderno) as stylecount
// 		from tblcosting c 
// 		inner join tblorder o ON o.orderno =c.orderno
// 		left outer join tblTransportCost t1 ON t1.TCID=c.TransInRateID
//         left outer join tblTransportCost t2 ON t2.TCID=c.TransOutRateID
// 		where c.orderno='$orderno'";
// $re=$conn->query($cost);
// $row=$re->FETCH(PDO::FETCH_BOTH);
// $oqty=$row['Qunit'];
// $stylecount=$row["stylecount"];
// $arrPEWCost=$row["ArrPEWCost"];
// $crate1=$row["crate1"];
// $rate1=$row["rate1"];
// $TransInPCT=$row["TransInPCT"];
// $crate2=$row["crate2"];
// $rate2=$row["rate2"];
// $TransOutPCT=$row["TransOutPCT"];
// $FinanceCostPCT=$row["FinanceCostPCT"];
// $cmCostPC=$row["cmCostPC"];
// $GMTest=$row["GMTest"];
// $Quota=$row["Quota"];
// $RDCPCT=$row["RDCPCT"];
// $BPCostPCT=$row["BPCostPCT"];
// $PDCostPCT=$row["PDCostPCT"];
// $ADCostPCT=$row["ADCostPCT"];
// $expfcost=$row["expfcost"];
// $AgentCommPCT=$row["AgentCommPCT"];
// $fobpc=$row["fobpc"];


// if ($oqty==2){
// 	$gmt2=$stylecount;
// }else{
// 	$gmt2=1;
// }		

// $PEW=explode(':',$arrPEWCost);
// $emb = round($PEW[0],3);// Embroidery
// $print = round($PEW[1],3);//Printing
// $wash = round(($PEW[2]),3); //washing


// //================= Qty information 

// 	$qtysql="select sum(qty) as qty from tblcolorsizeqty csq
// 			where csq.orderno='$orderno'";
// 	$re_sql=$conn->query($qtysql);
// 	$qrow=$re_sql->FETCH(PDO::FETCH_BOTH);
// 	$qQty=$qrow["qty"];

// 		$Qty=round(($qQty/$gmt2),0);

// //=================================

// //================= If FOB price is 0
//   $totalAmt=0;
//   $Qship=0;

//  if ($fobpc==0){
//   $pack="select shipprice, packresult from tblshipmentprice where orderno='$orderno'";
//   $re_pack=$conn->query($pack);
//   while($prow=$re_pack->FETCH(PDO::FETCH_ASSOC)){
//   		$arrPO = $prow["packresult"];
// 		$arrPrice = $prow["shipprice"];
	
// 		$shipQ = array_map (
// 	  	function ($_) {return explode (':', $_);},
// 	  			explode ('-', $arrPO)
// 		);
	
	
// 		$priceQ = array_map (
// 	  	function ($_) {return explode (':', $_);},
// 	  			explode ('-', $arrPrice)
// 		);

// 		for($i=0; $i<sizeof($shipQ); $i++){
// 			for($j=0;$j<sizeof($shipQ[$i]); $j++){
// 				$Qship +=$shipQ[$i][$j];
// 				$totalAmt +=$shipQ[$i][$j] * $priceQ[$i][$j];
// 			}
// 		}			
// 		}
	
	
// 	if ($Qty<>0){
// 		$fobpc=round(($totalAmt/$Qship),2);
// 	}else{
// 		$fobpc=0;
// 	}	

// }

// //==============================================================

// //1. Retrieve Fabric Material Cost
// // Fabric Cost
// $fabCost=$this->FabricCost($orderno, $Qty, $conn);

// //==============================================================

// //2. Retrieve Acc Material Cost
// // Acc Cost
// $accCost=$this->AccCost($orderno,$oqty,$Qty,$conn);

// //==================================

// //3. PEW Cost
// // Emb Cost

// // EMB
// 	$upemb=0;
//    	$embCost="select sum(apod.TotalPrice)as total	
// 			from tblapo_detail apod
// 			inner join tblapo_header apo ON apo.APOHID = apod.APOHID
// 			inner join tblapurchase_detail apd ON apd.APDID = apod.APDID
// 			inner join tblapurchase ap ON ap.APID = apd.APID
// 			where ap.orderno ='$orderno' and apo.statusID<>6 and apo.type=12
// 			group by ap.orderno";
//    	$re_emb=$conn->query($embCost);
//    	$rowemb=$re_emb->FETCH(PDO::FETCH_ASSOC);
//     $eup=$rowemb["total"];

//     if ($Qty>0){
//     	$upemb=$eup/$Qty;
//     }

//     if ($upemb==0){
//     	$embprice=$emb;
//     }else{
//     	$embprice=round($upemb,3);
//     }	

//     $upprt=0;
//    	$prtCost="select sum(apod.TotalPrice)as total	
// 			from tblapo_detail apod
// 			inner join tblapo_header apo ON apo.APOHID = apod.APOHID
// 			inner join tblapurchase_detail apd ON apd.APDID = apod.APDID
// 			inner join tblapurchase ap ON ap.APID = apd.APID
// 			where ap.orderno ='$orderno' and apo.statusID<>6 and apo.type=11
// 			group by ap.orderno";
//    	$re_prt=$conn->query($prtCost);
//    	$rowprt=$re_prt->FETCH(PDO::FETCH_ASSOC);
//     $pup=$rowprt["total"];

//     if ($Qty>0){
//     	$upprt=$pup/$Qty;
//     }

//     if ($upprt==0){
//     	$prtprice=$print;
//     }else{
//     	$prtprice=round($upprt,3);
//     }	


//     $upwash=0;
//     $wup=0;
//    	$washCost="select sum(apod.TotalPrice)as total	
// 			from tblapo_detail apod
// 			inner join tblapo_header apo ON apo.APOHID = apod.APOHID
// 			inner join tblapurchase_detail apd ON apd.APDID = apod.APDID
// 			inner join tblapurchase ap ON ap.APID = apd.APID
// 			where ap.orderno ='$orderno' and apo.statusID<>6 and apo.type in (13,22)
// 			group by ap.orderno";
//    	$re_wash=$conn->query($washCost);
//    	$rowwash=$re_wash->FETCH(PDO::FETCH_ASSOC);
//     $apocost=$rowwash["total"];


//     $mpowash="select sum(mpod.TotalPrice)as total	
// 			from tblmpo_detail mpod
// 			inner join tblmpo_header mpo ON mpo.MPOHID = mpod.MPOHID
// 			inner join tblmpurchase_detail mpd ON mpd.MPDID=mpod.MPDID
// 			inner join tblmpurchase mp ON mp.MPID=mpd.MPID
// 			where mp.orderno ='$orderno'  and mpo.statusID<>6 and mpo.type in (13,22)
// 			group by mp.orderno";
//     $re_mpowash=$conn->query($mpowash);
//    	$rowmpowash=$re_mpowash->FETCH(PDO::FETCH_ASSOC);
//     $mpocost=$rowmpowash["total"];


//     if ($Qty>0){
//     	$wup= $apocost+$mpocost;
//     	$upwash=$wup/$Qty;
//     }

//     if ($upwash==0){
//     	$washprice=$wash;
//     }else{
//     	$washprice=round($upwash,3);
//     }	

// //=================================

// //4. Retrieve from Costing 
// //(Import Finance Cost, Transport IN, cmCost, TransportOut, GarmentTest, Quota TPL Cost, 
// // Design Developement, Business Development, Product Developemnt, Administration , Export Finance, Agent Commm)
// // FOB price 


// $materialCost=$fabCost + $accCost + $embprice+ $prtprice + $washprice;
// $finCost = round(($materialCost * ($FinanceCostPCT/100)),4);

// $PEWCost=$embprice.':'.$prtprice.':'.$washprice;

// //Calculate transportIN Cost
// $transInCost= round($TransInPCT * ($crate1 * $rate1),4);
// $transOutCost= round($TransOutPCT * ($crate2 * $rate2),4);

// $billMtr=$materialCost+$finCost+$transInCost;

// $cmtCost= $materialCost+$finCost+$transInCost + $cmCostPC +$transOutCost +$GMTest+$Quota;

// $dpcost=round($fobpc * ($RDCPCT/100),4);
// $bpcost=round($fobpc * ($BPCostPCT/100),4);
// $pdcost=round($fobpc * ($PDCostPCT/100),4);
// $adcost=round($fobpc * ($ADCostPCT/100),4);

// $efcost=round($fobpc * ($expfcost/100),4);
// $accost=round($fobpc * ($AgentCommPCT/100),4);


// $gmtflatcost=$materialCost+$finCost+$transInCost + $cmCostPC +$transOutCost +$GMTest+ $Quota 
// 			+ $dpcost + $bpcost + $pdcost + $adcost;

// $marginpc = round($fobpc -($materialCost+$finCost+$transInCost + $cmCostPC +$transOutCost +$GMTest+ $Quota 
// 			+ $dpcost + $bpcost + $pdcost + $adcost+ $efcost + $accost),2);

// if ($fobpc==0){
// 	$margin=0;
// }else{	
// 	$margin=round((($marginpc/$fobpc) *100),2);
// }
// $margindz=round(($marginpc*12),2);


// // echo "<br>";
// // echo "Fabric>>".$fabCost.">> Acc".$accCost." PEW ".$embprice.">>".$prtprice.">>".$washprice."<br>";
// // echo "material>>>".$materialCost.">>>".$billMtr."<br>";
// // echo "CM>>".$cmCostPC."<br>";
// // echo "CMT>>".$cmtCost."<br>";
// // echo "gflat>>".$gmtflatcost."<br>";
// // echo "marginpc>>".$marginpc."<br>";

// $by=1;
// $now=date('Y-m-d H:i:s');

// //5. update COst
// // ( ArrPEWCost, billMtr, cmtCost, gmtflastCost, Margin, MarginPC, MarginDz)

// $updatequery = $conn->prepare('update tblcosting SET 
// ArrPEWCost = :ArrPEWCost,
// MarginFOBPCT = :MarginFOBPCT,
// cmtcost = :cmtcost,
// gmtflatcost = :gmtflatcost,
// cmCostPC =:cmCostPC,
// margin = :margin,
// marginpc = :marginpc,
// margindz = :margindz,
// billMtr = :billmtr,
// updateddate = :updateddate,
// updatedby = :updatedby
// WHERE orderno = :orderno');

// $updatequery->bindParam(':ArrPEWCost', $PEWCost);	//11-1-2016
// $updatequery->bindParam(':MarginFOBPCT', $margin);
// $updatequery->bindParam(':cmtcost', $cmtCost);	//4/2/2016
// $updatequery->bindParam(':gmtflatcost', $gmtflatcost);
// $updatequery->bindParam(':cmCostPC', $cmCostPC);
// $updatequery->bindParam(':margin', $margin);
// $updatequery->bindParam(':marginpc', $marginpc);
// $updatequery->bindParam(':margindz', $margindz);
// $updatequery->bindParam(':billmtr',$billMtr);
// $updatequery->bindParam(':updateddate',$now);
// $updatequery->bindParam(':updatedby',$by);
// $updatequery->bindParam(':orderno', $orderno);
// $updatequery->execute();
// 	//$conn->commit();

// 	// $conn->commit();
// // }catch(PDOException $e){
// 		// $conn->rollBack();
// 		// throw $e;
// 		// echo $e;
// // }

// }

//====================== added by SL 24 May 2017 call to display in costing
// modofied by SL on 23 sept 2017

// public function FabricCost($orderno, $Qty, $conn){

// 	$firstSoc="select newcol ,mm_type, ac_type from tblfirstsoc soc
// 			 	where soc.orderno='$orderno'";
//   	$cfirstSoc = $conn->query($firstSoc);
//   	$rowfirstSoc=$cfirstSoc->FETCH(PDO::FETCH_ASSOC);
// 	$mm_type1=$rowfirstSoc["mm_type"];
// 	$newcol=$rowfirstSoc["newcol"];
// 	$ac_type=$rowfirstSoc["ac_type"];
  
//   	$rowc =$cfirstSoc->rowCount();
  	
//   $subtotal=0;
//   $subtotal2=0;
//   if (($rowc==1) && ($newcol==1)){

// 		$mtype0=str_replace(";;;", ':', $mm_type1);

// 		$mtype1 = array_map (
//   		function ($_) {return explode (',', $_);},
//   			explode (':', $mtype0)
// 		);
		
// 		for ($m1=0; $m1<sizeof($mtype1); $m1++){
// 			if ($mtype1[$m1][0]<>''){
// 				$wastage = $mtype1[$m1][6];
// 				$consumption = $mtype1[$m1][7];
// 				$fabunit =$mtype1[$m1][8];
// 				$uprice=$mtype1[$m1][9];
				
// 				if ($fabunit=='YDS'){
// 					$consumption=$YY;	
// 				}
	
// 				$fab_fCost_pc=round((($consumption *(1+$wastage/100)) * $uprice),4);
			
// 				$fab_fCost_dz=round($fab_fCost_pc*12,3);

		
// 		$subtotal2+=$fab_fCost_pc;
		
// 		}//end if		
// 	}// end for
// }	
	
// 	// //1. Retrieve Fabric Material Cost
// 	// // Fabric Cost
// 	//   	$count="select count(distinct mpo.MPOHID) as count
// 	// 	from tblmpo_header mpo
// 	// 	inner join tblmpo_detail mpod ON mpod.MPOHID = mpo.MPOHID
// 	// 	inner join tblmpurchase_detail mpd ON mpd.MPDID = mpod.MPDID
// 	// 	inner join tblmpurchase mp ON mp.MPID = mpd.MPID
// 	// 	where mpo.statusID<>6 and mp.orderno='".$orderno."'";
// 	//
// 	//   	$cresult = $conn->query($count);
// 	//   	$r=$cresult->FETCH(PDO::FETCH_ASSOC);
// 	//   	$count=$r['count'];
// 	//   	$n=0;
// 	//
// 	//   	$fabCost=0;
// 	//   	$total=0;
// 	//
// 	//   	if ($count=="0"){ // MPO not yet approved
// 	//
// 	//   		//echo "MPO not yet approved <br>";
// 	// 		$mpo="select
// 	// 						   sum(mpd.purchaseQty_yds * mpd.unitprice * mpd.c_rate) as yds,
// 	// 	  					   sum(mpd.purchaseQty_lbs * mpd.unitprice * mpd.c_rate) as lbs,
// 	//      					   sum((mpd.purchaseQty_lbs /2.2046) * mpd.unitprice * mpd.c_rate) as kg,
// 	//      					   sum(mpd.purchaseQty_yds  * mpd.unitprice * mpd.c_rate) as pc,
// 	// 						   mp.sets,mpd.punit
// 	//
// 	// 					from tblmpurchase_detail mpd
// 	// 					inner join tblmpurchase mp ON mp.MPID = mpd.MPID
// 	// 					where mp.orderno='$orderno'
// 	// 					group by mp.orderno, mpd.punit";
// 	// 		$mresult = $conn->query($mpo);
// 	// 		while($row = $mresult->fetch(PDO::FETCH_ASSOC)){
// 	// 			$yds = $row['yds'];
// 	// 			$lbs = $row['lbs'];
// 	// 			$kg = $row['kg'];
// 	// 			$pc = $row['pc'];
// 	// 			$punit = $row['punit'];
// 	//
// 	// 			if (($punit==63) || ($punit==64)){ //PC / PCS
// 	// 				$fCost = ($pc/$Qty);//$consumption * $uprice;
// 	// 			}else if ($punit == 84){ //YDS
// 	// 				$fCost =($yds/$Qty);
// 	// 			}else if ($punit == 44){ //KG
// 	// 				$fCost =($kg/$Qty);
// 	// 			}else if ($punit==57){ // LBS
// 	// 				$fCost =($lbs/$Qty);
// 	// 			}
// 	//
// 	// 		$fabCost +=$fCost;
// 	//   		}
// 	//
// 	//   	}else{ // MPO has been approved
// 	//
// 	//   	  	// echo "MPO has been approved <br>";
// 	//
// 	//
// 	// 		$mpo="select
// 	// 					sum(mpd.purchaseQty_yds * mpd.unitprice * mpd.c_rate) as yds,
// 	// 	  				sum(mpd.purchaseQty_lbs * mpd.unitprice * mpd.c_rate) as lbs,
// 	//      				sum((mpd.purchaseQty_lbs /2.2046) * mpd.unitprice * mpd.c_rate) as kg,
// 	//      				sum(mpd.purchaseQty_yds  * mpd.unitprice * mpd.c_rate) as pc,
// 	// 					mp.sets,mpd.punit
// 	//
// 	// 				from tblmpurchase_detail mpd
// 	//                     left outer join tblmpo_detail mpod ON mpod.MPDID = mpd.MPDID
// 	// 				inner join tblmpurchase mp ON mp.MPID = mpd.MPID
// 	// 				where mp.orderno='IA8510' and mpod.MPOHID is NULL
// 	// 				group by mp.orderno, mpd.punit";
// 	// 		$mresult = $conn->query($mpo);
// 	// 		while($row = $mresult->fetch(PDO::FETCH_ASSOC)){
// 	// 			$yds = $row['yds'];
// 	// 			$lbs = $row['lbs'];
// 	// 			$kg = $row['kg'];
// 	// 			$pc = $row['pc'];
// 	// 			$punit = $row['punit'];
// 	//
// 	// 			if (($punit==63) || ($punit==64)){ //PC / PCS
// 	// 				$fCost = ($pc/$Qty);//$consumption * $uprice;
// 	// 			}else if ($punit == 84){ //YDS
// 	// 				$fCost =($yds/$Qty);
// 	// 			}else if ($punit == 44){ //KG
// 	// 				$fCost =($kg/$Qty);
// 	// 			}else if ($punit==57){ // LBS
// 	// 				$fCost =($lbs/$Qty);
// 	// 			}
// 	//
// 	// 		$fabCost +=$fCost;
// 	// 		} //price //--- End While ---//
// 	//
// 	//
// 	// 		$mpo="select sum(mpod.TotalPrice * mpo.c_rate) as total
// 	// 			from tblmpo_detail mpod
// 	// 			inner join tblmpo_header mpo ON mpo.MPOHID = mpod.MPOHID
// 	// 			inner join tblmpurchase_detail mpd ON mpd.MPDID = mpod.MPDID
// 	// 			inner join tblmpurchase mp ON mp.MPID = mpd.MPID
// 	// 			where mp.orderno='$orderno' and mpo.statusID<>6 and mpo.Type=0
// 	// 			group by mp.orderno";
// 	// 			$mresult = $conn->query($mpo);
// 	// 			$row = $mresult->fetch(PDO::FETCH_ASSOC);
// 	//
// 	// 			$price=$row['total'];
// 	//
// 	// 			$total =($price / $Qty);
// 	// 			$fabCost +=round($total,4);
// 	//   }
// 	// //echo "fabCost>>>".$fabCost."<br>";


// 		$mpo="select GROUP_CONCAT(mpd.MPDID) as MPDID,
// 				mpd.unitprice,mm.FabricContent,ft.Description as fabrictype,
// 				concat(mm.min_weight_gm,'-',mm.max_weight_gm) as weight,
// 				Concat(mm.TopYarn,'/',mm.BottomYarn,'-',mm.spandex) as YarnCount,
// 				mm.ExternalWidth, mm.max_weight_gm,
// 				concat(mm.InternalWidth,'-', mm.ExternalWidth) as width,
// 				sum(distinct mp.allSize) as yy, 
// 				mpd.wastage, 
// 				mpd.punit, u.Description as unit,
// 				mpd.currencyID, c.rate, c.CurrencyCode,mp.dozPcs, mm.MMID

// 			from tblmpurchase_detail mpd 
// 			left outer join tblmpo_detail mpod ON mpod.MPDID = mpd.MPDID
// 			inner join tblmpurchase mp ON mp.MPID = mpd.MPID
// 			inner join tblcurrency c ON c.ID=mpd.currencyID
// 			left outer join tblmm_detail mm ON mm.MMID = mp.MMID
// 			inner join tblfabtype ft ON ft.ID=mm.FabricTypeID
// 			left outer join tblunit u ON u.ID=mpd.punit
// 			where mp.orderno='$orderno' and mpd.MMCID<>0 and mpod.MPOHID is NULL
// 			Group by mp.orderno, mp.MMID, mpd.unitprice";
// 		$mresult = $conn->query($mpo);
// 		while($row = $mresult->fetch(PDO::FETCH_ASSOC)){
// 			$mmid = $row['MMID'];
// 			$MPDID = $row['MPDID'];
// 			$unitprice = $row['unitprice'];
// 			$FabricContent = $row['FabricContent'];
// 			$FabType = $row['fabrictype'];
// 			$weight = $row['weight'];
// 			$YarnCount = $row['YarnCount'];
// 			$ExternalWidth = $row['ExternalWidth'];
// 			$max_weight_gm = $row['max_weight_gm'];
// 			$width = $row['width'];
// 			$yy2 = $row['yy'];
// 			$wastage = $row['wastage'];
// 			$punit = $row['punit'];
// 			$fabunit = $row['unit'];
// 			$dozpc = $row['dozPcs'];
// 			$rate=$row['rate'];		
// 			$unitprice = (trim($unitprice)==""? 0: $unitprice); //--- Added by ckwai on 2017-08-28 ---//
			
// 			$price2="select sum(mpd.purchaseQty_yds * mpd.unitprice * mpd.c_rate) as yds,
// 						sum(mpd.purchaseQty_lbs * mpd.unitprice * mpd.c_rate) as lbs,
// 						sum((mpd.purchaseQty_lbs /2.2046) * mpd.unitprice * mpd.c_rate) as kg,
// 						sum(mpd.purchaseQty_yds  * mpd.unitprice * mpd.c_rate) as pc,
// 						mp.sets, mp.shipmentpriceID, mpd.MPID,
// 						(select (count(DISTINCT garmentID)) from tblgarment where orderno='$orderno') as garmentID
                       

// 					from tblmpurchase_detail mpd
// 					inner join tblmpurchase mp ON mp.MPID = mpd.MPID
// 					where mp.orderno='$orderno' and mpd.MPDID in ($MPDID) and mpd.unitprice=$unitprice 
// 					group by mp.orderno,mp.MMID, mpd.unitprice";
// 			$presult = $conn->query($price2);
// 			while($prow = $presult->fetch(PDO::FETCH_ASSOC)){
// 				$yds=$prow["yds"];
// 				$lbs =$prow["lbs"];
// 				$kg = $prow["kg"];
// 				$pc=$prow["pc"];
// 				$sets=$prow["sets"];
// 				$garmentID=$prow["garmentID"];
// 				$shipmentpriceID = $prow["shipmentpriceID"];
// 				$shipmentpriceID =(trim($shipmentpriceID)=="" ? "0": $shipmentpriceID); //--- Added by ckwai on 2017-08-28 ---//
// 				$MPID = $prow["MPID"];
				
// 				// // added by SL 26 Aug 2017
// 				// $shipmentpricesql="select sum(poAmount) as poAmount from tblshipmentprice where ID in ($shipmentpriceID)";
// 				// //echo $shipmentpricesql." | $MPID<br/>";
// 				// $rshipmentprice=$conn->query($shipmentpricesql);
// 				// $shiprow = $rshipmentprice->fetch(PDO::FETCH_ASSOC);
// 				// $shipmentQty = $shiprow["poAmount"];
// 				// $shipmentQty = (trim($shipmentQty)=="" ? "0": $shipmentQty); //--- Added by ckwai on 2017-08-28 ---//
// 				// //echo $shipmentQty."<==============>";
// 				//
// 				// if ($oqty==2){ // set Qty
// 				// 	$poQty = $shipmentQty/$stylecount;
// 				// }else{
// 				// 	$poQty = $shipmentQty;
// 				// }
// 				//
// 				// $overrate = $poQty / $Qty;
				
// 				$yy = $yy2/$dozpc;
			
// 				$unitprice = $unitprice*$rate;
			
// 				$comp2 = ($ExternalWidth * $max_weight_gm * 0.0232 * 2.2046)/1000;
// 				$consumption = ($comp2 * $yy);

					
// 			if (($punit==63) || ($punit==64)){ //PC / PCS
// 				$consumption=1;
// 				$fCost = $pc/$Qty;//$consumption * $uprice;
// 				//$fCost = $pc * $rate;
// 				//$fCost = $fCost,4);
// 				$in=$YY/6;
// 			}else if ($punit == 84){ //YDS
// 				$consumption=($yy *(1+($wastage/100)));
// 				//$fCost =$totakyds/$topProdqty;//$consumption * $uprice;
// 				$fCost =($yds /$Qty);
// 				//$fCost = round($fCost,4);
// 				$in=$yy/6;
// 			}else if ($punit == 44){ //KG
// 				$consumption = ($consumption / 2.2046); 
// 				//$fCost = $totalkg/$topProdqty;//$consumption * $uprice;
// 				$fCost =$kg /$Qty;
// 				//$fCost = round($fCost,4);		
// 				$in=$consumption1;	
// 			}else if ($punit==57){ // LBS
// 				//$fCost = $total/$topProdqty;//$consumption * $uprice;
// 				$fCost =$lbs  /$Qty;
// 				//$fCost=round($fCost,4);
// 				$in=$consumption/2.2046;
// 			}

// 			$fabCost +=$fCost;
			
// 			}//--- End While Inner ---//
		
// 		} //price //--- End While ---//	
	
// 		$mpo="select 
// 	   				sum(mpod.TotalPrice * mpo.c_rate) as TotalPrice,mpod.unitprice,
// 	 				mpod.purchaseUnitID,mp.MMID,mpo.currencyID,mpo.c_rate,c.CurrencyCode,
        
//         			mm.FabricContent,ft.Description as fabrictype,
// 					mm.ExternalWidth,mm.max_weight_gm,
// 					concat(mm.min_weight_gm,'-',mm.max_weight_gm) as weight,
// 					Concat(mm.TopYarn,'/',mm.BottomYarn,'-',mm.spandex) as YarnCount,
// 					concat(mm.InternalWidth,'-', mm.ExternalWidth) as width,
//         			sum(DISTINCT mp.allSize) as yy, 
//         			GROUP_CONCAT(distinct mpd.wastage ORDER BY mpd.wastage DESC SEPARATOR'<br>') as wastage,
//         			u.Description as unit, mp.shipmentpriceID

// 			from tblmpo_detail mpod 
// 			inner join tblmpo_header mpo ON mpo.MPOHID=mpod.MPOHID
// 			left outer join tblcurrency c ON c.ID=mpo.currencyID
// 			inner join tblmpurchase_detail mpd ON mpd.MPDID = mpod.MPDID
// 			inner join tblmpurchase mp ON mp.MPID = mpd.MPID
// 			inner join tblorder o ON o.Orderno = mp.orderno
// 			left outer join tblmm_detail mm ON mm.MMID = mp.MMID
// 			inner join tblfabtype ft ON ft.ID=mm.FabricTypeID
// 			left outer join tblunit u ON u.ID=mpod.purchaseUnitID
// 			where mp.orderno='$orderno' and mpo.statusID<>6 and mpo.Type=0 
// 			and mpod.MPOHID <> 'NULL'
// 			group by mp.orderno, mp.MMID, mpod.unitprice";
			
// 			$mresult = $conn->query($mpo);
// 			while($row = $mresult->fetch(PDO::FETCH_ASSOC)){
// 				$price=$row['unitprice'];
// 				$totalprice=$row['TotalPrice'];
// 				$FabricContent = $row['FabricContent'];
// 				$FabType = $row['fabrictype'];
// 				$ExternalWidth=$row['ExternalWidth'];
// 				$max_weight_gm=$row['max_weight_gm'];
// 				$weight = $row['weight'];
// 				$YarnCount = $row['YarnCount'];
// 				$width = $row['width'];
// 				$yy = $row['yy'];
// 				$wastage = $row['wastage'];
// 				$punit=$row['purchaseUnitID'];
// 				$currencyID=$row['currencyID'];
// 				$rate=$row['c_rate'];
// 				$currencyCode=$row['CurrencyCode'];
// 				$fabunit=$row['unit'];
// 				$shipmentpriceID = $row["shipmentpriceID"];

// 				// // added by SL 26 Aug 2017
// 				// $shipmentpricesql="select sum(poAmount) as poAmount from tblshipmentprice where ID in ($shipmentpriceID)";
// 				// $rshipmentprice=$conn->query($shipmentpricesql);
// 				// $shiprow = $rshipmentprice->fetch(PDO::FETCH_ASSOC);
// 				// $shipmentQty = $shiprow["poAmount"];
// 				//
// 				// if ($oqty==2){ // set Qty
// 				// 	$poQty = $shipmentQty/$stylecount;
// 				// }else{
// 				// 	$poQty = $shipmentQty;
// 				// }
// 				//
// 				// $overrate = $poQty / $Qty;
				
// 				$price = round(($price*$rate),4);
// 				$totalprice =round(($totalprice),4);
					
// 				$comp2 = ($ExternalWidth * $max_weight_gm * 0.0232 * 2.2046)/1000;
// 				$consumption = $comp2 * $yy;
					
// 				$fCost2 = ($totalprice /$Qty);
// 				$fCost = round($fCost2,4);
					
// 				$fabCost += $fCost;	
				
// 				}//end price while
// 				//}//while	


// 	if ($fabCost==0){
// 		$fabCost = $subtotal2;
// 	}

// return $fabCost;
// }

// public function AccCost($orderno,$Qunit,$Qty,$conn){
// 	$firstSoc="select newcol, ac_type, ac_info from tblfirstsoc soc
// 			 	where soc.orderno='$orderno'";
//   	$cfirstSoc = $conn->query($firstSoc);
//   	$rowfirstSoc=$cfirstSoc->FETCH(PDO::FETCH_ASSOC);
// 	$newcol=$rowfirstSoc["newcol"];
// 	$ac_type1=$rowfirstSoc["ac_type"];
//   	$ac_info1=$rowfirstSoc["ac_info"];
 
// 	$acccost2=0;
// 	$accCost3=0;
// 	$gmt2=1; // added by SL 30 Sept 2017
// 	if (($newcol==1)){

// 		//$Styleno1=explode(';',$Styleno);

// 		// for material purpose
// 	  	if ($Qunit==2){
// 			$unit2='Set';
// 			$gmt2=1;
// 		}else{
// 			$unit2='PC';
// 			$gmt2=sizeOf($Styleno1);
// 		}

// 		$actype0=str_replace(";;;", ':', $ac_type1);

// 		$atype2 = array_map (
// 	  	function ($_) {return explode (',', $_);},
// 	  		explode (':', $actype0)
// 		);

// 		$atype1= array_unique($atype2, SORT_REGULAR);

// 		for ($a1=0; $a1<sizeof($atype1); $a1++){
// 			$acost = $atype1[$a1][5];

// 	  		$acccost2 +=$acost;
// 	  	}
// 	  }
// 	  	$accCost3 =round($acccost2,4);

// 	//2. Retrieve Acc Material Cost
// 	// Acc Cost

// 	$acost=0;

// 			$acc2="select ast.Description as subType, apd.c_rate, ap.AsubtypeID,
// 					sum(apd.unitprice * apd.purchaseQty * apd.c_rate) as total,
// 					sum(apd.purchaseQty) as pQty,

// 					(sum(apd.unitprice * apd.purchaseQty * apd.c_rate) / sum(apd.purchaseQty)) as unitprice,
// 	        		sum(distinct apd.consum) * (1+ sum(DISTINCT apd.wastage)/100) as consum,
// 	                u2.Description as priceUnit,
// 	                u3.Description as basicUnit,
				
// 					(select GROUP_CONCAT(DISTINCT ap2.shipmentpriceID)
// 						from tblapurchase ap2
// 						where ap2.orderno='$orderno' and ap2.AsubtypeID=ap.AsubtypeID) as shipmentpriceID,
					
// 					(select GROUP_CONCAT(DISTINCT ap3.garmentID)
// 						from tblapurchase ap3
// 					 	where ap3.orderno='$orderno' and ap3.AsubtypeID=ap.AsubtypeID) as garmentno,
				
// 					case o.Qunit
// 					when 1 then 
// 					1
// 					ELSE
// 					LENGTH(ap.garmentID)+1 - LENGTH(REPLACE(ap.garmentID, ',', ''))
// 					end as garmentID

// 				from tblapurchase ap
// 				inner join tblapurchase_detail apd ON apd.APID = ap.APID
// 	            left outer join tblapo_detail apod ON apod.APDID = apd.APDID
// 				inner join tblorder o ON o.Orderno = ap.orderno
// 				inner join tblasubtype ast ON ast.ID = ap.AsubtypeID
// 	            inner join tblamaterial am ON am.AMID = ap.AMID
// 	            inner join tblunit u2 ON u2.ID = am.price_unit
// 	            inner join tblunit u3 ON u3.ID = am.basic_unit
// 				where ap.orderno='$orderno' and apd.purchaseQty>0 and apod.APOHID is NULL
// 				group by ap.AsubtypeID,am.basic_unit,am.price_unit
// 				order by ap.AsubtypeID";
// 				$aresult2 = $conn->query($acc2);
// 				while($arow2 = $aresult2->fetch(PDO::FETCH_ASSOC)){
// 						$AsubtypeID = $arow2["AsubtypeID"];
// 						$Type = $arow2["subType"];			
// 						$unitprice = $arow2["unitprice"];
// 						$garmentID =$arow2["garmentID"];
// 						$consum = $arow2["consum"];
// 						$rate=$arow2["c_rate"];
// 						$totalQtyp=round($arow2["total"],5);
// 						$pQty = $arow2["pQty"];
// 						$priceUnit=$arow2["priceUnit"];
// 						$aunit = $arow2["basicUnit"];
// 						$shipmentpriceID  =$arow2["shipmentpriceID"];
// 						$garmentno = $arow2["garmentno"];
					
// 						$basicUnitPcs = intval(preg_replace('/[^0-9]+/', '', $aunit), 10);
// 						$basicUnitPcs = ($basicUnitPcs==0)? 1: $basicUnitPcs;
		
// 						$priceUnitPcs = intval(preg_replace('/[^0-9]+/', '', $priceUnit), 10);
// 						$priceUnitPcs = ($priceUnitPcs==0)? 1: $priceUnitPcs;
		
// 						$consum3 = $consum * $basicUnitPcs / $priceUnitPcs;
// 						$consum3 = round($consum3,5);

					
// 						//Get Full color qty from style#
// 						//----- Modify by ckwai on 2017-09-27 -----//
// 						$shipmentpriceID = ltrim($shipmentpriceID,",");
// 						$garmentno = ltrim($garmentno,","); // added by SL 30 sept 2017
// 						$subquery = ($shipmentpriceID==""? "": "AND sp.tblshipmentpriceID IN ($shipmentpriceID)");
					
// 						$sql3="select sum(sp.TotalCtn) as TotalCtn from tblshipmentpacking sp
// 								where 1=1 $subquery and sp.garmentID in ($garmentno)";
// 				   		$asql3 = $conn->query($sql3);
// 				   		$arow3 = $asql3->fetch(PDO::FETCH_ASSOC);
					
// 						$TotalCtn = $arow3["TotalCtn"];
// 						$overrate = $TotalCtn / $Qty;
					
// 						$acost=	$unitprice * $consum3 * $garmentID * $overrate;
// 						$accCost +=$acost;
// 			}

// 			$subSQL="select sum(apod.TotalPrice * apo.c_rate) as TotalPrice
// 					from tblapurchase ap
// 					inner join tblapurchase_detail apd on apd.APID = ap.APID 
// 					inner join tblapo_detail apod ON apod.APDID = apd.APDID
// 					inner join tblapo_header apo ON apo.APOHID = apod.APOHID
// 					where ap.orderno='$orderno' and apo.statusID<>6 and apo.Type in (1,2)
// 					group by ap.orderno";
// 		  	$subr = $conn->query($subSQL);
// 	  		while($subrow = $subr->FETCH(PDO::FETCH_ASSOC)){
// 				$TotalPrice = $subrow["TotalPrice"];
			
// 				$acost=round(($TotalPrice/$Qty),5);
// 		  		$accCost +=$acost;
	  		
// 			}
// 	$accCost =round($accCost,5); 

// 	if ($accCost==0){
// 		$accCost=$accCost3;
// 	}	
	
// return $accCost;
// }


// public function PEWCost($orderno, $cost_pew, $Qty, $conn){

// //3. PEW Cost

// $qtysql="select sum(qty) as qty from tblcolorsizeqty csq
// 		where csq.orderno='$orderno'";
// $re_sql=$conn->query($qtysql);
// $qrow=$re_sql->FETCH(PDO::FETCH_BOTH);
// //$qQty=$qrow["qty"];
	
// //$Qty=round(($qQty/$stylecount),0);



// $PEW=explode(':',$cost_pew);
// $emb = round($PEW[0],3);// Embroidery
// $print = round($PEW[1],3);//Printing
// $wash = round(($PEW[2]),3); //washing


// // EMB
// 	$upemb=0;
//    	$embCost="select sum(apod.TotalPrice)as total	
// 			from tblapo_detail apod
// 			inner join tblapo_header apo ON apo.APOHID = apod.APOHID
// 			inner join tblapurchase_detail apd ON apd.APDID = apod.APDID
// 			inner join tblapurchase ap ON ap.APID = apd.APID
// 			where ap.orderno ='$orderno' and apo.statusID<>6 and apo.type=12
// 			group by ap.orderno";
//    	$re_emb=$conn->query($embCost);
//    	$rowemb=$re_emb->FETCH(PDO::FETCH_ASSOC);
//     $eup=$rowemb["total"];
    
//     if ($Qty>0){
//     	$upemb=$eup/$Qty;
//     }
    
//     if ($upemb==0){
//     	$embprice=$emb;
//     }else{
//     	$embprice=round($upemb,3);
//     }	

//     $upprt=0;
//    	$prtCost="select sum(apod.TotalPrice)as total	
// 			from tblapo_detail apod
// 			inner join tblapo_header apo ON apo.APOHID = apod.APOHID
// 			inner join tblapurchase_detail apd ON apd.APDID = apod.APDID
// 			inner join tblapurchase ap ON ap.APID = apd.APID
// 			where ap.orderno ='$orderno' and apo.statusID<>6 and apo.type=11
// 			group by ap.orderno";
//    	$re_prt=$conn->query($prtCost);
//    	$rowprt=$re_prt->FETCH(PDO::FETCH_ASSOC);
//     $pup=$rowprt["total"];
    
//     if ($Qty>0){
//     	$upprt=$pup/$Qty;
//     }
    
//     if ($upprt==0){
//     	$prtprice=$print;
//     }else{
//     	$prtprice=round($upprt,3);
//     }	


//     $upwash=0;
//     $wup=0;
//    	$washCost="select sum(apod.TotalPrice)as total	
// 			from tblapo_detail apod
// 			inner join tblapo_header apo ON apo.APOHID = apod.APOHID
// 			inner join tblapurchase_detail apd ON apd.APDID = apod.APDID
// 			inner join tblapurchase ap ON ap.APID = apd.APID
// 			where ap.orderno ='$orderno' and apo.statusID<>6 and apo.type in (13,22)
// 			group by ap.orderno";
//    	$re_wash=$conn->query($washCost);
//    	$rowwash=$re_wash->FETCH(PDO::FETCH_ASSOC);
//     $apocost=$rowwash["total"];
    
    
//     $mpowash="select sum(mpod.TotalPrice)as total	
// 			from tblmpo_detail mpod
// 			inner join tblmpo_header mpo ON mpo.MPOHID = mpod.MPOHID
// 			inner join tblmpurchase_detail mpd ON mpd.MPDID=mpod.MPDID
// 			inner join tblmpurchase mp ON mp.MPID=mpd.MPID
// 			where mp.orderno ='$orderno'  and mpo.statusID<>6 and mpo.type in (13,22)
// 			group by mp.orderno";
//     $re_mpowash=$conn->query($mpowash);
//    	$rowmpowash=$re_mpowash->FETCH(PDO::FETCH_ASSOC);
//     $mpocost=$rowmpowash["total"];


//     if ($Qty>0){
//     	$wup= $apocost+$mpocost;
//     	$upwash=$wup/$Qty;
//     }
    
//     if ($upwash==0){
//     	$washprice=$wash;
//     }else{
//     	$washprice=round($upwash,3);
//     }	


// 	$pewCost=$embprice.':'.$prtprice.':'.$washprice;


// 	return $pewCost;

// }

//-------- BOM check update to tblmpurchase, tblmpurchase_detail and tblmm_color by ckwai on 2017-06-02 --------//
public function funcCheckFabPurchase($conn, $part, $orderno, $bomfabID, $bomsubID, $new_MMID, $new_wastage, $new_yy){
	
		//-------- Load from tblmpurchase --------//
		$sqlChkMP = $conn->prepare("SELECT MPID FROM tblmpurchase WHERE bomfabricID=:bomfabID AND subID=:bomsubID AND orderno=:orderno AND part=:part");
		$sqlChkMP->bindParam(":orderno", $orderno);
		$sqlChkMP->bindParam(":bomfabID", $bomfabID);
		$sqlChkMP->bindParam(":bomsubID", $bomsubID);
		$sqlChkMP->bindParam(":part", $part);
		$sqlChkMP->execute();
	
		while($row = $sqlChkMP->fetch(PDO::FETCH_ASSOC)){
			$MPID = $row["MPID"];
			
			//-------- Load from tblmpurchase_detail --------//
			$sqlChkMPD = $conn->prepare("SELECT MPDID, MMCID, MMSID FROM tblmpurchase_detail WHERE MPID=:MPID");
			$sqlChkMPD->bindParam(":MPID", $MPID);
			$sqlChkMPD->execute();
			while($rowMPD = $sqlChkMPD->fetch(PDO::FETCH_ASSOC)){
				$MPDID = $rowMPD["MPDID"];
				$MMCID = $rowMPD["MMCID"];
				$MMSID = $rowMPD["MMSID"];
				
				//-------- Load from tblmm_color --------//
				$sqlChkMMC = $conn->prepare("SELECT colorID, pcode, img FROM tblmm_color WHERE MMCID=:MMCID");
				$sqlChkMMC->bindParam(":MMCID", $MMCID);
				$sqlChkMMC->execute();
				while($rowMMC = $sqlChkMMC->fetch(PDO::FETCH_ASSOC)){
					$color = $rowMMC["colorID"];
					$pcode = $rowMMC["pcode"];
					$img   = $rowMMC["img"];
					$new_MMCID = $this->checkMMC($conn, $color, $new_MMID, $pcode, $img);
					
					$supplierID = ""; $price = ""; $basic_unit = ""; $price_unit = ""; $currencyID = "";
					$MOQ = ""; $MPQ = ""; $new_MMSID = NULL;
					
					if($MMSID!=""){ // modified check MMSID also by ckwai on 201912191807
						$sql = "SELECT supplierID, price, basic_unit, price_unit, currencyID, MOQ, MPQ
														FROM tblmm_supplier WHERE MMSID='$MMSID'";
						$sqlChkMMS = $conn->prepare($sql);
						$sqlChkMMS->execute();
						while($rowMMS = $sqlChkMMS->fetch(PDO::FETCH_ASSOC)){
							$supplierID = $rowMMS["supplierID"];
							$price = $rowMMS["price"];
							$basic_unit = $rowMMS["basic_unit"];
							$price_unit = $rowMMS["price_unit"];
							$currencyID = $rowMMS["currencyID"];
							$MOQ = $rowMMS["MOQ"];
							$MPQ = $rowMMS["MPQ"];
							
						}
					
						$sqlID = "SELECT max(MMSID) as lastID FROM tblmm_supplier";
						$resultID = $conn->query($sqlID);
							$rowID = $resultID->fetch(PDO::FETCH_ASSOC);
							$new_MMSID = $rowID["lastID"];
												
						$new_MMSID = ($new_MMSID == null? 1: ++$new_MMSID);//----Get latest MMSID----//
						
						$statusID = 1;
						$sqlInsertMMS = "INSERT INTO tblmm_supplier
										(MMSID, MMCID, supplierID, price, statusID, basic_unit, price_unit, currencyID)
								VALUES (:MMSID, :MMCID, :supplierID, :price, :statusID, :basic_unit, :price_unit, :currencyID)";
						$stmt_supplier = $conn->prepare($sqlInsertMMS);
						$stmt_supplier->bindParam(':MMSID', $new_MMSID);
						$stmt_supplier->bindParam(':MMCID', $new_MMCID);
						$stmt_supplier->bindParam(':supplierID', $supplierID);
						$stmt_supplier->bindParam(':price', $price);
						$stmt_supplier->bindParam(':statusID', $statusID);
						$stmt_supplier->bindParam(':basic_unit', $basic_unit);
						$stmt_supplier->bindParam(':price_unit', $price_unit);
						$stmt_supplier->bindParam(':currencyID', $currencyID);
						$stmt_supplier->execute();
					}
					//--- Update MMCID of fabric ---//
					$chk = $this->updateTblMpurchase_detail($conn, $MPDID, $new_MMCID, $new_wastage, $part, $new_MMSID); 
					
				}//-------- End while tblmm_color --------//
			}//-------- End while tblmpurchase_detail --------//
			
			$chk = $this->updateTblconsumption($conn, $MPID, $new_yy, $part);
			$chk = $this->updateTblMpurchase($conn, $MPID, $new_yy, $new_MMID, $part);
			
		}//-------- End while tblmpurchase --------//
		
		//$chk = $this->triggerFabConsumption($conn, $orderno); //off by ckwai on 201910151525 in order use mpurchase_class function
	
	return true;
}

//-------- Check MMCID whether exist in system by ckwai on 2017-06-02 --------//
public function checkMMC($conn, $fabColor, $mmid, $pcode, $img=null){
	
	if($fabColor!=0){
		$sqlCheckMMC = "SELECT * FROM tblmm_color WHERE MMID=:MMID AND colorID=:colorID AND pcode=:pcode";
		$sqlChk = $conn->prepare($sqlCheckMMC);
		$sqlChk->bindParam(":MMID", $mmid);
		$sqlChk->bindParam(":colorID", $fabColor);
		$sqlChk->bindParam(":pcode", $pcode);
		$sqlChk->execute();
		
		$countCheckMMCcountCheckMMC = $sqlChk->rowCount();
		$rowColorSQL = $sqlChk->fetch(PDO::FETCH_BOTH);
					
		if($countCheckMMCcountCheckMMC==0){ //----check whether exist color in tblmm_color----//
						
			$sqlID = "SELECT max(MMCID) as lastID FROM tblmm_color";
			$resultID = $conn->query($sqlID);
				$rowID = $resultID->fetch(PDO::FETCH_ASSOC);
				$MMCID = $rowID["lastID"];
									
				$MMCID = ($MMCID == null? 1: ++$MMCID);//----Get latest MMCID----//
				
			$qty = 0;			
			$sqlInsertMMC = "INSERT INTO tblmm_color(MMCID, MMID, colorID, pcode, Qty_YDS, Qty_LBS, img)
								VALUES (:MMCID, :MMID, :colorID, :pcode, :Qty_YDS, :Qty_LBS, :img)";
			$stmt_color = $conn->prepare($sqlInsertMMC);
			$stmt_color->bindParam(':MMCID', $MMCID);
			$stmt_color->bindParam(':MMID', $mmid);
			$stmt_color->bindParam(':colorID', $fabColor);
			$stmt_color->bindParam(':pcode', $pcode);
			$stmt_color->bindParam(':Qty_YDS', $qty);
			$stmt_color->bindParam(':Qty_LBS', $qty);
			$stmt_color->bindParam(':img', $img);
			$stmt_color->execute();
							
		}else{
			$MMCID = $rowColorSQL["MMCID"];
		}						
	}else{
		$MMCID = "0";
	}
	return $MMCID;
}

public function updateTblMpurchase_detail($conn, $MPDID, $MMCID, $wastage, $part, $MMSID){
	
	$sqlMPurchase_detail = "UPDATE tblmpurchase_detail SET MMCID=:MMCID, wastage=:wastage, MMSID=:MMSID WHERE MPDID=:MPDID";
			$stmt = $conn->prepare($sqlMPurchase_detail);
			$stmt->bindParam(':MPDID', $MPDID);
			$stmt->bindParam(':MMCID', $MMCID);
			$stmt->bindParam(':wastage', $wastage);
			$stmt->bindParam(':MMSID', $MMSID);
			//$stmt->bindParam(':unitprice', $unitprice);
			//$stmt->bindParam(':inventoryQty', $inventoryQty);
			//$stmt->bindParam(':StatusID', $StatusID);
			$stmt->execute();
			
	return true;
}

public function updateTblconsumption($conn, $MPID, $qty, $part){
	
	if($part==1 || $part==2){
	$sqlTblConsumption = "UPDATE tblconsumption  SET qty=:qty WHERE MPID=:MPID";
		$stmt = $conn->prepare($sqlTblConsumption);
		$stmt->bindParam(':MPID', $MPID);
		$stmt->bindParam(':qty', $qty);
		$stmt->execute();
	}
		
	return true;
}

public function updateTblMpurchase($conn, $MPID, $allSize, $MMID, $part){
	
	if($part==1 || $part==2){
		// $sql = "UPDATE tblmpurchase SET allSize='$allSize', MMID='$MMID' WHERE MPID='$MPID'";
		// echo "<pre>$sql</pre>";
		
		$sqlUpdateMpurchase = "UPDATE tblmpurchase SET allSize=:allSize, MMID=:MMID WHERE MPID=:MPID";
			$stmt = $conn->prepare($sqlUpdateMpurchase);
			$stmt->bindParam(':allSize', $allSize);		
			$stmt->bindParam(':MMID', $MMID);		
			$stmt->bindParam(':MPID', $MPID);		
			$stmt->execute();
	}else if($part==3){
		$sqlUpdateMpurchase = "UPDATE tblmpurchase SET MMID=:MMID WHERE MPID=:MPID";
			$stmt = $conn->prepare($sqlUpdateMpurchase);	
			$stmt->bindParam(':MMID', $MMID);		
			$stmt->bindParam(':MPID', $MPID);		
			$stmt->execute();
	}
	return true;
}

public function triggerFabConsumption($conn, $orderno){

	$sqlID = "SELECT mp.MPID, mp.dozPcs, mp.sets,
					mmd.min_weight_gm, mmd.max_weight_gm,
					mmd.min_weight_yard, mmd.max_weight_yard,
					mmd.InternalWidth, mmd.ExternalWidth,mmd.multiplier, mmd.basic_unit,
					mp.allSize, mp.garmentID 
				FROM tblmpurchase mp 
				inner join tblmm_detail mmd on mp.MMID = mmd.MMID
				inner join tblfabtype ft on mmd.FabricTypeID = ft.ID
				WHERE mp.orderno='$orderno'";
	$resultID = $conn->query($sqlID);
	while($rowID = $resultID->fetch(PDO::FETCH_ASSOC)){
		$MPID = $rowID["MPID"];
		$dozPcs = $rowID["dozPcs"];
		$allSize = $rowID["allSize"];
		$garmentID = $rowID["garmentID"];
		$chkSets = $rowID["sets"];
		
		$maxGM = $rowID["max_weight_gm"];
		$extWidth = $rowID["ExternalWidth"];
		$multiplier = $rowID["multiplier"];
		$basic = $rowID["basic_unit"];
		
		//-------- Count selected garment by ckwai on 2017-05-22 --------//
		$arr_gmt = explode(",",$garmentID);
		$num_gmt = count($arr_gmt); 
		
		$sub_query_qty = "";
		if($garmentID!=0 && $garmentID!=""){
			$sub_query_qty = " AND garmentID IN ($garmentID)";
		}
		
		$sqlGarmentColor = "SELECT  c.ID as colorID, mpd.inventoryQty, mpd.unitprice, mpd.MPDID, mpd.MMCID,
									(select sum(Qty) FROM tblcolorsizeqty WHERE colorID=mpd.colorID AND orderno='$orderno' $sub_query_qty) as qty	, 
									mpd.wastage, mpd.StatusID, mpd.purchaseQty_yds as qtyYDS, mpd.purchaseQty_lbs as qtyLBS, mpd.punit				
									FROM tblmpurchase_detail mpd
									INNER JOIN tblcolor c ON c.ID = mpd.colorID
									INNER JOIN tblmm_color mmc ON mmc.MMCID = mpd.MMCID
									INNER JOIN tblcolor cc ON cc.ID = mmc.colorID
									WHERE mpd.MPID = '$MPID' AND mpd.MMCID != 0";
		$resultGarmentColor=$conn->query($sqlGarmentColor);
		while($rowColor=$resultGarmentColor->fetch(PDO::FETCH_BOTH)){
			$MPDID = $rowColor["MPDID"];
			$MMCID = $rowColor["MMCID"];
			$colorID = $rowColor["colorID"];
			$garmentQty = $rowColor["qty"];
			$inventoryQty = $rowColor["inventoryQty"];
			$unitprice = $rowColor["unitprice"];
			$wastage = $rowColor["wastage"];
			$statusID = $rowColor["StatusID"];
			$qtyYDS = $rowColor["qtyYDS"];
			$qtyLBS = $rowColor["qtyLBS"];
			$punit = $rowColor["punit"];
			
			//========== Tblconsumption ==========//
			$consumYDSInner = 0;
			$consumYDS = 0;
			$sqlColorSize = "select sum(csq.Qty) as colorSizeQty, cs.qty as sizeQty 
							FROM tblcolorsizeqty csq
							LEFT JOIN tblconsumption cs ON cs.sizeName = csq.sizeName
							WHERE csq.orderno='$orderno' 
							AND csq.colorID='$colorID' AND cs.MPID =  '$MPID' $sub_query_qty
							group by csq.SizeName";
			$resultColorSize = $conn->query($sqlColorSize);
			while($rowColorSize=$resultColorSize->fetch(PDO::FETCH_BOTH)){
					$gmtQty = $rowColorSize["colorSizeQty"]; //---garment qty---//
					$sizeQty = $rowColorSize["sizeQty"]; //---consumption qty---//
					$gmtQty = ($garmentID==""? 0: $gmtQty); // added by ckwai 2016-10-07
					
					//-------- Check whether Sets Order by ckwai on 2017-05-22 --------//
					if($chkSets==1){
						$gmtQty = $gmtQty / $num_gmt;
						$gmtQty = round($gmtQty);
					}
					
					$consumYDSInner = $gmtQty * $sizeQty * ($wastage/100 + 1) / $dozPcs;
					$consumYDS += $consumYDSInner;
											
			}//------ End While Loop ------//
			
			$oneYDSequalLBS = $maxGM * $extWidth * 0.0232 * $multiplier * 2.2046/1000;
			$invYDS = $inventoryQty / $oneYDSequalLBS; 
			
			$consumYDS = ($basic!=64 && $basic!=83 && $maxGM>0) ? $consumYDS - $invYDS : $consumYDS - $inventoryQty;
			$consumLBS = $consumYDS * (($maxGM * $extWidth * 0.0232 * $multiplier) * 2.2046/1000 );
			
			if($basic==64 || $basic==83){
				$ansYDS = round($consumYDS, 0); 
				$ansLBS = 0;
			}else{
				$ansYDS = round($consumYDS,4);
				$ansLBS = round($consumLBS,4);
			}
			
			//echo "$ansYDS - $ansLBS <br/>";
			$this->updateTblmpurchase_detail_qty($conn, $MPDID, $ansYDS, $ansLBS);
			
		}//------ End While Loop ------//
	}//-------- End While --------//
	return true;
}

public function updateTblmpurchase_detail_qty($conn, $MPDID, $ansYDS, $ansLBS){
		$sqlUpdateTblmpurchase_detail = "UPDATE tblmpurchase_detail SET purchaseQty_yds=:purchaseQty_yds, purchaseQty_lbs=:purchaseQty_lbs WHERE MPDID=:MPDID";
		$stmt = $conn->prepare($sqlUpdateTblmpurchase_detail);
			$stmt->bindParam(':MPDID', $MPDID);
			$stmt->bindParam(':purchaseQty_yds', $ansYDS);
			$stmt->bindParam(':purchaseQty_lbs', $ansLBS);
			$stmt->execute();
		
	return true;	
}

//----------------------------------------------------------------------------------------------------------------//
//-------- BOM check update to tblapurchase, tblapurchase_detail and tblasizecolor by ckwai on 2017-06-19 --------//
//----------------------------------------------------------------------------------------------------------------//
public function funcCheckAccPurchase($conn, $part, $orderno, $bomaccID, $new_AMID, $new_wastage, $new_yy){
	//-------- Load from new tblamaterial --------//
	$sqlChkAMT = $conn->prepare("SELECT acc_code, basic_unit, AsubtypeID FROM tblamaterial WHERE AMID=:new_AMID");
	$sqlChkAMT->bindParam(":new_AMID",$new_AMID);
	$sqlChkAMT->execute();
	$rowAMT = $sqlChkAMT->fetch(PDO::FETCH_ASSOC);
		$new_acc_code = $rowAMT["acc_code"];
		$new_basic_unit = $rowAMT["basic_unit"];
		$new_AsubtypeID = $rowAMT["AsubtypeID"];

	//-------- Load from tblmpurchase --------//
		$sqlChkMP = $conn->prepare("SELECT APID, ASCID FROM tblapurchase WHERE bomaccID=:bomaccID AND orderno=:orderno AND part=:part");
		$sqlChkMP->bindParam(":orderno", $orderno);
		$sqlChkMP->bindParam(":bomaccID", $bomaccID);
		$sqlChkMP->bindParam(":part", $part);
		$sqlChkMP->execute();
		
		while($row = $sqlChkMP->fetch(PDO::FETCH_ASSOC)){
			$APID = $row["APID"];
			$ap_ASCID = $row["ASCID"];
			$ap_new_ASCID = NULL;
			
			//-------- Load from tblasizecolor --------//
			$sqlChkASC = $conn->prepare("SELECT colorID, Standard FROM tblasizecolor WHERE ASCID=:ASCID");
			$sqlChkASC->bindParam(":ASCID", $ap_ASCID);
			$sqlChkASC->execute();
			while($rowASC = $sqlChkASC->fetch(PDO::FETCH_ASSOC)){
				$ap_colorID = $rowASC["colorID"];
				$ap_Standard = $rowASC["Standard"];
				
				$ap_new_ASCID = $this->funcCheckStandard($conn, $ap_Standard, $ap_colorID, $new_AMID);
			}
			
			
			//-------- Load from tblapurchase_detail --------//
			$sqlChkMPD = $conn->prepare("SELECT APDID, ASCID FROM tblapurchase_detail WHERE APID=:APID");
			$sqlChkMPD->bindParam(":APID", $APID);
			$sqlChkMPD->execute();
			while($rowMPD = $sqlChkMPD->fetch(PDO::FETCH_ASSOC)){
				$APDID = $rowMPD["APDID"];
				$ASCID = $rowMPD["ASCID"];
				
				//-------- Load from tblasizecolor --------//
				$sqlChkMMC = $conn->prepare("SELECT colorID, Standard FROM tblasizecolor WHERE ASCID=:ASCID");
				$sqlChkMMC->bindParam(":ASCID", $ASCID);
				$sqlChkMMC->execute();
				while($rowMMC = $sqlChkMMC->fetch(PDO::FETCH_ASSOC)){
					$colorID = $rowMMC["colorID"];
					$standard = $rowMMC["Standard"];
					$new_ASCID = $this->funcCheckStandard($conn, $standard, $colorID, $new_AMID);
					
					//--- Update ASCID of fabric ---//
					$chk = $this->updateTblapurchase_detail($conn, $APDID, $new_ASCID, $new_yy); 
					
				}//-------- End while tblmm_color --------//
			}//-------- End while tblmpurchase_detail --------//

			//$chk = $this->updateTblMpurchase($conn, $APID, $new_yy, $new_AMID, $part);
			$chk = $this->updateTblapurchase($conn, $APID, $new_AMID, $new_AsubtypeID, $new_basic_unit, $new_yy, $new_acc_code, $ap_new_ASCID);
			
		}//-------- End while tblmpurchase --------//
		
	$chk = $this->triggerCalAccConsumption($conn, $orderno);
}

public function funcCheckStandard($conn, $txt_standard, $selectColor, $amid){
	
	$txt_standard = trim($txt_standard);
	
	//---Check whether exist standard in tblasizecolor---//
	$checkStandard = "SELECT ASCID FROM tblasizecolor WHERE 
						TRIM(Standard)='$txt_standard' AND colorID = '$selectColor' AND AMID='$amid' ORDER BY ASCID ASC";
	$resultStandard = $conn->query($checkStandard);
	$countStandard = $resultStandard->RowCount();
		$rowStandard = $resultStandard->fetch(PDO::FETCH_BOTH);
	
	//--- If database doesn't exist asizecolor name ---//
	if($countStandard==0 && $txt_standard != "" && $amid!=0){
		$sqlID = "SELECT max(ASCID) as lastID FROM tblasizecolor";
		$resultID = $conn->query($sqlID);
		$rowID = $resultID->fetch(PDO::FETCH_ASSOC);
		$maxASCID = $rowID["lastID"] + 1;
		
		$txt_standard = strtoupper($txt_standard);
		$txt_standard = htmlspecialchars("$txt_standard", ENT_QUOTES);
		
		//---Insert new standard name into tblasizecolor---//
		$sqlInsertColor = "INSERT INTO tblasizecolor (ASCID, AMID, Standard, colorID)
							VALUES('$maxASCID','$amid','$txt_standard','$selectColor')";
		$insertColor = $conn->exec($sqlInsertColor);
		$ASCID = $maxASCID;
										
	}else if($txt_standard != "" && $amid!=0){
		$ASCID = $rowStandard["ASCID"];
		
	}else{
		$ASCID = 0;
		
	}//---End If---//
	
	return $ASCID;
}

public function updateTblapurchase_detail($conn, $APDID, $ASCID, $consum){
	$sqlUpdateTblapurchase_detail = "UPDATE tblapurchase_detail SET ASCID=:ASCID, consum=:consum WHERE APDID=:APDID";

	$stmt = $conn->prepare($sqlUpdateTblapurchase_detail);
		$stmt->bindParam(':APDID', $APDID);
		$stmt->bindParam(':ASCID', $ASCID);
		$stmt->bindParam(':consum', $consum);
		$stmt->execute();
		
	return true;	
}

public function updateTblapurchase($conn, $APID, $AMID, $AsubtypeID, $unitID, $allQty, $accCode, $ASCID){
	$sqlUpdateApurchase = "UPDATE tblapurchase SET AMID=:AMID, AsubtypeID=:AsubtypeID, unitID=:unitID, 
								allQty=:allQty, accCode=:accCode, ASCID=:ASCID WHERE APID=:APID";
	$stmt = $conn->prepare($sqlUpdateApurchase);
		$stmt->bindParam(':APID', $APID);
		$stmt->bindParam(':AMID', $AMID);
		$stmt->bindParam(':AsubtypeID', $AsubtypeID);
		//$stmt->bindParam(':byMethod', $byMethod);
		//$stmt->bindParam(':dozPcs', $dozPcs);
		$stmt->bindParam(':unitID', $unitID);
		$stmt->bindParam(':allQty', $allQty);
		$stmt->bindParam(':accCode', $accCode);
		//$stmt->bindParam(':garmentID', $garmentID);
		//$stmt->bindParam(':shipmentpriceID', $shipmentpriceID);
		$stmt->bindParam(':ASCID', $ASCID);
		//$stmt->bindParam(':statusID', $statusID);
		//$stmt->bindParam(':updatedBy', $updatedBy);
		//$stmt->bindParam(':updatedDate', $updatedDate);	
		$stmt->execute();
		
	return true;
}

//-------- Function Update Purchase Qty Consumption in tblapurchase_detail --------//
public function triggerCalAccConsumption($conn, $orderno){
	//echo "====>$shipmentID<br/>";
	$sqlID = "SELECT ap.dozPcs, apd.APDID, ap.byMethod, ap.garmentID, ap.shipmentpriceID, ap.orderno, ap.APID,
			apd.colorID, apd.size, apd.consum,
			apd.wastage, apd.inventory, apd.ratioMethod
			FROM tblapurchase ap
			INNER JOIN tblapurchase_detail apd ON ap.APID = apd.APID
			WHERE ap.orderno = '$orderno'";
	$resultID = $conn->query($sqlID);
	while($rowID = $resultID->fetch(PDO::FETCH_ASSOC)){
		$myOrderno = $rowID["orderno"];
		$myAPID = $rowID["APID"];
		$myAPDID = $rowID["APDID"];
		$dozPcs = $rowID["dozPcs"];
		$by = $rowID["byMethod"];
		$garmentID = $rowID["garmentID"];
		$strPO = $rowID["shipmentpriceID"];
		$colorID = $rowID["colorID"];
		$sizeName = $rowID["size"];
		$consum = $rowID["consum"];
		
		$wastage = $rowID["wastage"];
		$inventory = $rowID["inventory"];
		$ratioMethod = $rowID["ratioMethod"];
		
		$purchaseQty = $this->funcUpdateConsumption($conn, $dozPcs, $myAPDID, $by, $orderno, $garmentID, $strPO, $colorID, $sizeName, $consum, $wastage, $inventory, $ratioMethod);
		//echo "-->$myOrderno - $myAPID - $purchaseQty<br />";
		
		$chk = $this->updateTblapurchase_detail_qty($conn, $myAPDID, $purchaseQty);
	}//------ End While ------//
}

public function funcUpdateConsumption($conn, $dozPcs, $myAPDID, $by, $orderno, $garmentID, $strPO, $colorID, $sizeName, $consum, $wastage, $inventory, $ratioMethod){
	$total_qty = 0;
	$unit = 1; // default unit value
	
	//============== Get Details From tblapurchase_detail (2016-09-06) ==============//
		// $sqlChkAPD = "SELECT wastage, inventory, consum, ratioMethod FROM tblapurchase_detail WHERE APDID = '$myAPDID'";
		// $resultChkAPD = $conn->query($sqlChkAPD);
			// $rowChkAPD = $resultChkAPD->fetch(PDO::FETCH_ASSOC);
			// $wastage = $rowChkAPD["wastage"];
			// $inventory = $rowChkAPD["inventory"];
			////////$consum = $rowChkAPD["consum"];
			// $ratioMethod = $rowChkAPD["ratioMethod"];
		$colorID = ($by==5? $ratioMethod: $colorID); //---- If accessories matching item is by Ratio Pack ----//
	
	$qty = $this->calQtyByMethod($conn, $by, $orderno, $garmentID, $strPO, $colorID, $sizeName);
	
	if($qty>0){			
		$total_qty = ($qty * $consum * (($wastage/100)+ 1) / $dozPcs / $unit) - $inventory;
	}
	//echo "$myAPDID - $total_qty<br/>";
	
	return $total_qty;
}

public function calQtyByMethod($conn, $byMethod, $orderno, $garmentID, $shipmentpriceID, $colorID, $size){
	$subquery = ""; $subqueryShip = ""; $totalQty = 0; $poQty = 0; $str = "";
	$arr_gmtID = explode(",", $garmentID);
	if($garmentID!=0){
		$subquery .= "AND garmentID IN (".$garmentID.")";
		for($ii=0;$ii<count($arr_gmtID);$ii++){
			//$subquery .= "AND garmentID = '".$arr_gmtID[$ii]."'";
			if($ii==0){
				$subqueryShip .= "AND FIND_IN_SET(".$arr_gmtID[$ii].", StyleNo)"; 
			}else{
				$subqueryShip .= "OR FIND_IN_SET(".$arr_gmtID[$ii].", StyleNo)"; 
			}
		}
		//$subqueryShip = "AND StyleNo like '%$garmentID,%'"; 
	}
	
	$chk_shipmentID = substr("$shipmentpriceID", -1);
	$aftersub_shipmentID =  substr("$shipmentpriceID", 0, -1);
	$shipmentpriceID = ($chk_shipmentID==","? $aftersub_shipmentID : $shipmentpriceID);
	$subqueryShipment = ($shipmentpriceID=="") ? "" : "AND ID IN ($shipmentpriceID)";
	//echo "$subqueryShipment <br/>";
	
	//========= By Method: Order =========//
	if($byMethod == 0){
		$sqlColorSize = "SELECT sum(Qty) as totalQty FROM tblcolorsizeqty WHERE orderno = '$orderno' $subquery";
		$resultColorSize = $conn->query($sqlColorSize);
		$rowCS = $resultColorSize->fetch(PDO::FETCH_ASSOC);
			$totalQty = $rowCS["totalQty"];
			
		//====================== Get Arrangement of Color and garment =================================//
		$sqlArrangeColor = "select DISTINCT g.garmentID, g.styleNo, c.colorID, co.colorName 
							from tblcolorsizeqty AS c 
							INNER JOIN tblcolor AS co ON c.colorID = co.ID 
							INNER JOIN tblgarment AS g ON g.garmentID = c.garmentID
							where c.orderno = '$orderno' ORDER BY g.garmentID, c.colorID ASC";
		$resultArrangeColor = $conn->query($sqlArrangeColor);
		$rowCount = 0;
		$str_gmtCL="";
		while($rowArrangeColor = $resultArrangeColor->fetch(PDO::FETCH_ASSOC)){
			$gmtID = $rowArrangeColor["garmentID"];
			$cID = $rowArrangeColor["colorID"];
			
			if($rowCount==0){
				$str_gmtCL = "$cID:$gmtID";
			}else{
				$str_gmtCL = $str_gmtCL."-$cID:$gmtID";
			}
			$rowCount++;
		}
			
		$sqlShip = "SELECT packresult as poAmount, groupColor as gp_color FROM tblshipmentprice WHERE Orderno = '$orderno' $subqueryShipment $subqueryShip";
		$resultShip = $conn->query($sqlShip);
		$countShip = $resultShip->rowCount();
		while($rowShip = $resultShip->fetch(PDO::FETCH_ASSOC)){
			$poAmount = $rowShip["poAmount"];
			//$gp_color = $rowShip["gp_color"];
			$gp_color = $str_gmtCL;
			
			$groupColorArr = explode('-', $gp_color);
			$arrColor = explode("-",$poAmount);
			
			for($i=0;$i<count($arrColor);$i++){
				//$str_gpColor = substr($groupColorArr[$i], 1, -1);
				$str_gpColor = $groupColorArr[$i];
				$gminiArr = explode(':', $str_gpColor);
				$gp_colorID = $gminiArr[0];
				$gp_gmtID = $gminiArr[1];
			
				//if(($gp_gmtID==$garmentID || $garmentID==0)){
				if((in_array("$gp_gmtID", $arr_gmtID) || $garmentID==0)){
					$arrSize = explode(":",$arrColor[$i]);
					for($c=0;$c<count($arrSize);$c++){
						$poQty += $arrSize[$c];
						
					}//------ End For Loop (Size) ------//
				}
				
			}//------ End For Loop (Color) ------//
		}//------ End While Loop ------//
		//$totalQty = $totalQty - $poQty;
		$totalQty = $poQty;
		//$totalQty = ($countShip==0)? 0 : $totalQty;
	}
	
	//========= By Method: Color =========//
	else if($byMethod == 1){
		$sqlColorSize = "SELECT sum(Qty) as totalQty FROM tblcolorsizeqty WHERE orderno = '$orderno' AND colorID = '$colorID' $subquery";
		$resultColorSize = $conn->query($sqlColorSize);
		$rowCS = $resultColorSize->fetch(PDO::FETCH_ASSOC);
			$totalQty = $rowCS["totalQty"];
			$totalQty = ($totalQty == "")? 0: $totalQty;
		
		//====================== Get Arrangement of Color and garment =================================//
		$sqlArrangeColor = "select DISTINCT g.garmentID, g.styleNo, c.colorID, co.colorName 
							from tblcolorsizeqty AS c 
							INNER JOIN tblcolor AS co ON c.colorID = co.ID 
							INNER JOIN tblgarment AS g ON g.garmentID = c.garmentID
							where c.orderno = '$orderno' ORDER BY g.garmentID, c.colorID ASC";
		$resultArrangeColor = $conn->query($sqlArrangeColor);
		$rowCount = 0;
		$str_gmtCL="";
		while($rowArrangeColor = $resultArrangeColor->fetch(PDO::FETCH_ASSOC)){
			$gmtID = $rowArrangeColor["garmentID"];
			$cID = $rowArrangeColor["colorID"];
			
			if($rowCount==0){
				$str_gmtCL = "$cID:$gmtID";
			}else{
				$str_gmtCL = $str_gmtCL."-$cID:$gmtID";
			}
			
			$rowCount++;
		}
		
		$sqlShip = "SELECT packresult as poAmount, groupColor as gp_color  FROM tblshipmentprice WHERE Orderno = '$orderno' $subqueryShipment $subqueryShip";
		//echo "$sqlShip <br/>";
		$resultShip = $conn->query($sqlShip);
		$countShip = $resultShip->rowCount();
		while($rowShip = $resultShip->fetch(PDO::FETCH_ASSOC)){
			$poAmount = $rowShip["poAmount"];
			//$gp_color = $rowShip["gp_color"];
			$gp_color = $str_gmtCL;
			
			$groupColorArr = explode('-', $gp_color);	
			$arrColor = explode("-",$poAmount);
			
			$nnn = count($groupColorArr);
			//echo "$nnn -<br/>";
			
			for($i=0;$i<count($arrColor);$i++){
				
				//$str_gpColor = substr($groupColorArr[$i], 1, -1);
				$str_gpColor = $groupColorArr[$i];
				$gminiArr = explode(':', $str_gpColor);
				$gp_colorID = $gminiArr[0];
				$gp_gmtID = $gminiArr[1];
				
				//if($gp_colorID==$colorID && ($gp_gmtID==$garmentID || $garmentID==0)){
				if($gp_colorID==$colorID && (in_array("$gp_gmtID", $arr_gmtID) || $garmentID==0)){
				$arrSize = explode(":",$arrColor[$i]);				
					for($c=0;$c<count($arrSize);$c++){
						$poQty += $arrSize[$c];
						
					}//------ End For Loop (Size) ------//
				}
			}//------ End For Loop (Color) ------//
		}//------ End While Loop ------//
		
		//$totalQty = ($totalQty==0)? 0 : $totalQty - $poQty;
		$totalQty = ($totalQty==0)? 0 : $poQty;
		//$totalQty = ($countShip==0)? 0 : $totalQty;
		//$totalQty = $poQty;	
	}
	
	//========= By Method: Size/Color =========//
	else if($byMethod == 2){
		$sqlColorSize = "SELECT sum(Qty) as totalQty FROM tblcolorsizeqty WHERE orderno = '$orderno' AND colorID = '$colorID' AND SizeName = '$size' $subquery";
		$resultColorSize = $conn->query($sqlColorSize);
		$rowCS = $resultColorSize->fetch(PDO::FETCH_ASSOC);
			$totalQty = $rowCS["totalQty"];
			$totalQty = ($totalQty == "")? 0: $totalQty;
			//$totalQty = $size;
		
		//--------- Search SizeName Position ---------//
		$szNum = 0;
		$currentSZpos = 0;
		$sqlLoadCS = "SELECT SizeName FROM tblcolorsizeqty WHERE orderno = '$orderno' group by SizeName order by ID ASC";
		$resultLoadCS = $conn->query($sqlLoadCS);
		while($rowLoadCS = $resultLoadCS->fetch(PDO::FETCH_ASSOC)){
			$sz = $rowLoadCS["SizeName"];
			if($sz==$size){ $currentSZpos=$szNum; }
			$szNum++;
		}
		
		//--------- Search ColorID position ---------//
		$clNum = 0;
		$currentCLpos = 0;
		$sqlLoadCL = "SELECT colorID FROM tblcolorsizeqty WHERE orderno = '$orderno' group by colorID order by ID ASC";
		$resultLoadCL = $conn->query($sqlLoadCL);
		while($rowLoadCL = $resultLoadCL->fetch(PDO::FETCH_ASSOC)){
			$cl = $rowLoadCL["colorID"];
			if($cl==$colorID){ $currentCLpos=$clNum; }
			$clNum++;
		}
		
		//====================== Get Arrangement of Color and garment =================================//
		$sqlArrangeColor = "select DISTINCT g.garmentID, g.styleNo, c.colorID, co.colorName 
							from tblcolorsizeqty AS c 
							INNER JOIN tblcolor AS co ON c.colorID = co.ID 
							INNER JOIN tblgarment AS g ON g.garmentID = c.garmentID
							where c.orderno = '$orderno' ORDER BY g.garmentID, c.colorID ASC";
		$resultArrangeColor = $conn->query($sqlArrangeColor);
		$rowCount = 0;
		$str_gmtCL="";
		while($rowArrangeColor = $resultArrangeColor->fetch(PDO::FETCH_ASSOC)){
			$gmtID = $rowArrangeColor["garmentID"];
			$cID = $rowArrangeColor["colorID"];
			
			if($rowCount==0){
				$str_gmtCL = "$cID:$gmtID";
			}else{
				$str_gmtCL = $str_gmtCL."-$cID:$gmtID";
			}
			$rowCount++;
		}
		
		$sqlShip = "SELECT packresult as poAmount, groupColor as gp_color FROM tblshipmentprice WHERE Orderno = '$orderno' $subqueryShipment $subqueryShip";
		$resultShip = $conn->query($sqlShip);
		$countShip = $resultShip->rowCount();
		while($rowShip = $resultShip->fetch(PDO::FETCH_ASSOC)){
			$poAmount = $rowShip["poAmount"];
			//$gp_color = $rowShip["gp_color"];
			$gp_color = $str_gmtCL;
			
			$groupColorArr = explode('-', $gp_color);	
			// for($arr_gp=0;$arr_gp<count($groupColorArr);$arr_gp++){
				// $str_gpColor = substr($groupColorArr[$arr_gp], 1, -1);
				// echo "$str_gpColor<br/>";
			// }

			//$gminiArr = explode(':', $groupColorArr[$r-1]);	
			
			$arrColor = explode("-",$poAmount);
			$groupColorArr = explode('-', $gp_color);
			for($i=0;$i<count($arrColor);$i++){
				//$str_gpColor = substr($groupColorArr[$i], 1, -1);
				$str_gpColor = $groupColorArr[$i];
				$gminiArr = explode(':', $str_gpColor);
				$gp_colorID = $gminiArr[0];
				$gp_gmtID = $gminiArr[1];
			
				//if($gp_colorID==$colorID && ($gp_gmtID==$garmentID || $garmentID==0)){
				if($gp_colorID==$colorID && (in_array("$gp_gmtID", $arr_gmtID) || $garmentID==0)){
			
				//if($i==$currentCLpos){
					$arrSize = explode(":",$arrColor[$i]);
					//$str = $str."-".$arrColor[$i];
					for($c=0;$c<count($arrSize);$c++){
						if($c==$currentSZpos){
							$poQty += $arrSize[$c];
						}
						//$str = $str."=".$arrSize[$c];	
					}//------ End For Loop (Size) ------//
				}
				
			}//------ End For Loop (Color) ------//
		}//------ End While Loop ------//
		
		$totalQty = ($totalQty==0)? 0 : $poQty;
		//$totalQty = $str_gpColor;
		//$totalQty = ($countShip==0)? 0 : $totalQty;
	}
	
	//========= By Method: Size Only =========//
	else if($byMethod == 3){
		$sqlColorSize = "SELECT sum(Qty) as totalQty FROM tblcolorsizeqty WHERE orderno = '$orderno' AND SizeName = '$size' $subquery";
		$resultColorSize = $conn->query($sqlColorSize);
		$rowCS = $resultColorSize->fetch(PDO::FETCH_ASSOC);
			$totalQty = $rowCS["totalQty"];
			$totalQty = ($totalQty == "")? 0: $totalQty;
			//$totalQty = $size;
		
		//--------- Search SizeName Position ---------//
		$szNum = 0;
		$currentSZpos = 0;
		$sqlLoadCS = "SELECT SizeName FROM tblcolorsizeqty WHERE orderno = '$orderno' group by SizeName order by ID ASC";
		$resultLoadCS = $conn->query($sqlLoadCS);
		while($rowLoadCS = $resultLoadCS->fetch(PDO::FETCH_ASSOC)){
			$sz = $rowLoadCS["SizeName"];
			if($sz==$size){ $currentSZpos=$szNum; }
			$szNum++;
		}
		
		//--------- Search ColorID position ---------//
		$clNum = 0;
		$currentCLpos = 0;
		$sqlLoadCL = "SELECT colorID FROM tblcolorsizeqty WHERE orderno = '$orderno' group by colorID order by ID ASC";
		$resultLoadCL = $conn->query($sqlLoadCL);
		while($rowLoadCL = $resultLoadCL->fetch(PDO::FETCH_ASSOC)){
			$cl = $rowLoadCL["colorID"];
			if($cl==$colorID){ $currentCLpos=$clNum; }
			$clNum++;
		}
		
		//====================== Get Arrangement of Color and garment =================================//
		$sqlArrangeColor = "select DISTINCT g.garmentID, g.styleNo, c.colorID, co.colorName 
							from tblcolorsizeqty AS c 
							INNER JOIN tblcolor AS co ON c.colorID = co.ID 
							INNER JOIN tblgarment AS g ON g.garmentID = c.garmentID
							where c.orderno = '$orderno' ORDER BY g.garmentID, c.colorID ASC";
		$resultArrangeColor = $conn->query($sqlArrangeColor);
		$rowCount = 0;
		$str_gmtCL="";
		while($rowArrangeColor = $resultArrangeColor->fetch(PDO::FETCH_ASSOC)){
			$gmtID = $rowArrangeColor["garmentID"];
			$cID = $rowArrangeColor["colorID"];
			
			if($rowCount==0){
				$str_gmtCL = "$cID:$gmtID";
			}else{
				$str_gmtCL = $str_gmtCL."-$cID:$gmtID";
			}
			$rowCount++;
		}
		
		$sqlShip = "SELECT packresult as poAmount,  groupColor as gp_color FROM tblshipmentprice WHERE Orderno = '$orderno' $subqueryShipment $subqueryShip";
		$resultShip = $conn->query($sqlShip);
		$countShip = $resultShip->rowCount();
		while($rowShip = $resultShip->fetch(PDO::FETCH_ASSOC)){
			$poAmount = $rowShip["poAmount"];
			//$gp_color = $rowShip["gp_color"];
			$gp_color = $str_gmtCL;
			
			$groupColorArr = explode('-', $gp_color);	
			$arrColor = explode("-",$poAmount);
			
			for($i=0;$i<count($arrColor);$i++){
				//$str_gpColor = substr($groupColorArr[$i], 1, -1);
				$str_gpColor = $groupColorArr[$i];
				$gminiArr = explode(':', $str_gpColor);
				$gp_colorID = $gminiArr[0];
				$gp_gmtID = $gminiArr[1];
				
				//if(($gp_gmtID==$garmentID || $garmentID==0)){
				if((in_array("$gp_gmtID", $arr_gmtID) || $garmentID==0)){
				//if($i==$currentCLpos){
					$arrSize = explode(":",$arrColor[$i]);
					//$str = $str."-".$arrColor[$i];
					for($c=0;$c<count($arrSize);$c++){
						if($c==$currentSZpos){
							$poQty += $arrSize[$c];
						}
						//$str = $str."=".$arrSize[$c];	
					}//------ End For Loop (Size) ------//
				}
				
			}//------ End For Loop (Color) ------//
		}//------ End While Loop ------//
		
		//$totalQty = ($totalQty==0)? 0 : $totalQty - $poQty;
		$totalQty = ($countShip==0)? 0 : $poQty;
	}
	
	//========= By Method: Ratio =========//
	else if($byMethod == 5){
		$packMethod = $colorID;
		$subqueryShipment = ($shipmentpriceID=="") ? "" : "AND sp.ID IN ($shipmentpriceID)";
		
		if($packMethod == 1 || $packMethod == 3){
			$sqlShip = "SELECT sum(spk.TotalCtn) as totalPack
						FROM tblshipmentprice sp 
						INNER JOIN tblshipmentpacking spk ON sp.ID = spk.tblshipmentpriceID
						WHERE sp.Orderno = '$orderno' AND spk.packingMethod = '$packMethod' $subqueryShipment $subqueryShip
						GROUP BY spk.tblshipmentpriceID";
			$resultShip = $conn->query($sqlShip);
			$rowShip = $resultShip->fetch(PDO::FETCH_ASSOC);
				$totalPack = $rowShip["totalPack"];	
				$totalPack = ($totalPack == "")? 0: $totalPack;
			
		}else{
			$sqlShip = "SELECT spk.PackingRatio as totalPack
						FROM tblshipmentprice sp 
						INNER JOIN tblshipmentpacking spk ON sp.ID = spk.tblshipmentpriceID
						WHERE sp.Orderno = '$orderno' AND spk.packingMethod = '$packMethod' 
						$subqueryShipment $subqueryShip";
			$resultShip = $conn->query($sqlShip);
			$totalTP = 0;
			while($rowShip = $resultShip->fetch(PDO::FETCH_ASSOC)){
				$tp = $rowShip["totalPack"];
				$arrTP = explode(":",$tp);
				
				for($r=0;$r<count($arrTP);$r++){
					$arrSingle = explode("-",$arrTP[$r]);
					$totalTP += $arrSingle[1];
				}
			}//---- End While ----//
			$totalPack = $totalTP;
		}
		$totalQty = $totalPack;
	}
	
	//========= By Method: Pick List Order =========//
	else if($byMethod == 6){
		$totalPack = 0;
		$subqueryShipment = ($shipmentpriceID=="") ? "" : "AND sp.ID IN ($shipmentpriceID)";
		for($packMethod=1;$packMethod<=3;$packMethod++){
			
			if($packMethod == 1 || $packMethod == 3){
				$sqlShip = "SELECT sum(spk.TotalCtn) as totalPack
							FROM tblshipmentprice sp 
							INNER JOIN tblshipmentpacking spk ON sp.ID = spk.tblshipmentpriceID
							WHERE sp.Orderno = '$orderno' AND spk.packingMethod = '$packMethod' $subqueryShipment $subqueryShip
							GROUP BY spk.tblshipmentpriceID";
				$resultShip = $conn->query($sqlShip);
				$rowShip = $resultShip->fetch(PDO::FETCH_ASSOC);
					$totalPackShip = $rowShip["totalPack"];
					$totalPackShip = ($totalPackShip == "")? 0: $totalPackShip;
					
				$totalPack += $totalPackShip;
				
			}else{
				$sqlShip = "SELECT spk.PackingRatio as totalPack
							FROM tblshipmentprice sp 
							INNER JOIN tblshipmentpacking spk ON sp.ID = spk.tblshipmentpriceID
							WHERE sp.Orderno = '$orderno' AND spk.packingMethod = '$packMethod' 
							$subqueryShipment $subqueryShip";
				$resultShip = $conn->query($sqlShip);
				$totalTP = 0;
				while($rowShip = $resultShip->fetch(PDO::FETCH_ASSOC)){
					$tp = $rowShip["totalPack"];
					$arrTP = explode(":",$tp);
					
					for($r=0;$r<count($arrTP);$r++){
						$arrSingle = explode("-",$arrTP[$r]);
						$totalTP += $arrSingle[1];
					}
				}//---- End While ----//
				$totalPack += $totalTP;
			}
			
		}//--- End For Loop ---//
	
		$totalQty = $totalPack;
		
	}//------ End Pick List Order ------//
	
	//*/
	return $totalQty;
}

public function updateTblapurchase_detail_qty($conn, $APDID, $purchaseQty){
	
		$sqlUpdateTblapurchase_detail = "UPDATE tblapurchase_detail SET purchaseQty=:purchaseQty WHERE APDID=:APDID";
		$stmt = $conn->prepare($sqlUpdateTblapurchase_detail);
			$stmt->bindParam(':APDID', $APDID);
			$stmt->bindParam(':purchaseQty', $purchaseQty);
			$stmt->execute();
		
	return true;	
}


//added by SL 20 July 2018
public function get_NEIA($conn, $orderno){
	$arr_o = array();

	//echo $orderno;

	if (substr($orderno,0,2)==glb_order_prefix){ // IA
		$order_sql="select (vw.refID) as refID 
					from vw_getRefID vw where orderno='$orderno'
					group by vw.orderno";
		$result = $conn->query($order_sql);//GROUP_CONCAT DISTINCT
		$count = $result->rowCount();
		$row=$result->fetch(PDO::FETCH_ASSOC);

		if($count>0){
			$refID = $row["refID"];
			$arr_o = explode(',', $refID);
		}
		array_push($arr_o, $orderno);

	}else{ // NE
		$order_sql="select orderno from vw_getRefID vw where refID='$orderno' and orderno is not null";
		$result=$conn->query($order_sql);
		$row=$result->fetch(PDO::FETCH_ASSOC);
		$count=$result->rowCount();

		array_push($arr_o,$orderno);

		if ($count>0){
			$ia_order = $row["orderno"];
			array_push($arr_o,$ia_order);
		 }	
	}

	array_unique($arr_o);
	//return $orderno;
	return $arr_o;	
}

// added by SL 12 Oct 2018
public function get_auth($section,$auth,$conn){


	$auth0 = str_replace(',,', ',', $auth);
	$auth2 = ltrim($auth0,",");
	$auth3 = rtrim($auth2,",");
	

	if ($auth3==''){
		$auth=0;
	}else{
		if (substr($auth3,-1)==','){
			$auth=$auth3.'0';
		}
	}

	// if ($section<>''){
		// $mruser="select GROUP_CONCAT(u.UserFullName order by u.UserFullName SEPARATOR '<br>') as Auth 
					// from tbluseraccount u where u.acctID in ($auth) AND sectionID = '$section'";
	// }else{
		$mruser="select GROUP_CONCAT(u.UserFullName order by u.UserFullName SEPARATOR '<br>') as Auth 
					from tbluseraccount u where u.acctID in ($auth)";
	// }
	$mr_result=$conn->query($mruser);
	$mrow=$mr_result->FETCH(PDO::FETCH_BOTH);
	$display_username=$mrow["Auth"];

	return $display_username;	
}


// added by SL 09 Jan 2020 , update shipment date in tblorder
public function update_minShipdate($conn, $orderno){

	$sql="select min(sp.Shipdate) as ship_date from tblshipmentprice sp where sp.orderno='$orderno' and sp.Shipdate<>'0000-00-00' and sp.statusID=1";
	// echo $sql;
	$result=$conn->query($sql);
	$row=$result->fetch(PDO::FETCH_ASSOC);
	$min_shipdate =$row["ship_date"];

	$insert="update tblorder set SOCDate='$min_shipdate', ShipmentDate='$min_shipdate' where orderno='$orderno'";
	// echo "<br> $insert <br>";
	$re_insert=$conn->prepare($insert);
	$re_insert->execute();

	return true;
}

// get order unit measurement 
public function get_orderUOM($orderno,$conn){
	$sql="select s.Description as UOM
		from tblorder o
		inner join tblset s ON s.ID = o.Qunit
		where o.Orderno='$orderno';";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);

	$uom = $row["UOM"];

	return $uom;

}



//LB0092, NE0218_E
public function update_leather_firstsoc($conn, $orderno, $QDID_arr){// quotation leather retransfer to firstsoc (2024-05-03 ckwai) 
	
	// SELECT f.orderno, f.leather_type, qd.ArrLtr1, qd.ArrLtrWidth1, qd.ArrLtrWidth2, qd.ArrLtrWeight1, qd.ArrLtrWeight2, qd.ArrLtrYY, qd.ArrLtrConsumption1, qd.ArrLtrConsumption2, qd.ArrLtrLCost1, qd.ArrLtrLCost2, qd.ArrLtrCost, qd.ArrLtrMMID, qd.totalLtrTotal
// FROM `tblfirstsoc` f
// INNER JOIN tblgarment g oN g.orderno = f.orderno
// INNER jOIN tblq_detail qd ON qd.QDID = g.QDID
// WHERE f.leather_type!='' AND f.orderno = 'LB0099'
	
	$ltr_type = array(); $leather_cost = 0;
	 foreach($QDID_arr as $QDID){
		 $query_q = $conn->prepare("SELECT ne.Orderno, h.CurrencyID, qd.ArrLtr1, qd.ArrLtr2, qd.ArrLtr3, qd.ArrLtr4, qd.ArrLtr5, qd.ArrLtr6, qd.ArrLtrWidth1, qd.ArrLtrWidth2, qd.ArrLtrWeight1, qd.ArrLtrWeight2, qd.ArrLtrYY, qd.ArrLtrConsumption1, qd.ArrLtrConsumption2, qd.ArrLtrLCost1, qd.ArrLtrLCost2, qd.ArrLtrCost, qd.ArrLtrMMID, qd.totalLtrTotal
                                    FROM tblq_detail AS qd
                                    INNER JOIN tblq_header AS h ON qd.QHID = h.QHID
									INNER JOIN tblnewenquiry ne ON ne.QDID = qd.QDID
                                    WHERE qd.QDID = :QDID");
        $query_q->bindParam(':QDID', $QDID);
        $query_q->execute();
 
        $row_q = $query_q->fetch(PDO::FETCH_ASSOC);
		$ArrLtr1       = explode("^^", $row_q["ArrLtr1"]);
		$ArrLtr2       = explode("^^", $row_q["ArrLtr2"]);
		$ArrLtr3       = explode("^^", $row_q["ArrLtr3"]);
		$ArrLtr4       = explode("^^", $row_q["ArrLtr4"]);
		$ArrLtr5       = explode("^^", $row_q["ArrLtr5"]);
		$ArrLtr6       = explode("^^", $row_q["ArrLtr6"]);
		$ArrLtrWidth1  = explode("^^", $row_q["ArrLtrWidth1"]);
		$ArrLtrWidth2  = explode("^^", $row_q["ArrLtrWidth2"]);
		$ArrLtrWeight1 = explode("^^", $row_q["ArrLtrWeight1"]);
		$ArrLtrWeight2 = explode("^^", $row_q["ArrLtrWeight2"]);
		$ArrLtrYY      = explode("^^", $row_q["ArrLtrYY"]);
		$ArrLtrConsumption1 = explode("^^", $row_q["ArrLtrConsumption1"]);
		$ArrLtrConsumption2 = explode("^^", $row_q["ArrLtrConsumption2"]);
		$ArrLtrLCost1       = explode("^^",$row_q["ArrLtrLCost1"]);
		$ArrLtrLCost2       = explode("^^",$row_q["ArrLtrLCost2"]);
		$ArrLtrCost         = $row_q["ArrLtrCost"];
		$ArrLtrMMID         = $row_q["ArrLtrMMID"];
		$totalLtrTotal      = $row_q["totalLtrTotal"];
		$Orderno            = $row_q["Orderno"];
		
		// echo "Orderno: $Orderno / ".print_r($ArrLtrLCost1)." / ".print_r($ArrLtrLCost2)."<< <br/>";
		
		for($i=0;$i<count($ArrLtr4);$i++){
			$leathercontent  = $ArrLtr4[$i];
			$leathertype     = $ArrLtr1[$i];
			$leathermmcode   = $ArrLtr3[$i];
			$leatherskintype = $ArrLtr2[$i];
			$leatherskinsize = $ArrLtr5[$i];
			$leatherdes      = $ArrLtr6[$i];
			$ltrwidthA       = $ArrLtrWidth1[$i];
			$ltrwidthB       = $ArrLtrWidth2[$i];
			$ltrweightA      = $ArrLtrWeight1[$i];
			$ltrweightB      = $ArrLtrWeight2[$i];
			$ltryy           = $ArrLtrYY[$i];
			$ltrconsumptionA = $ArrLtrConsumption1[$i];
			$ltrconsumptionB = $ArrLtrConsumption2[$i];
			$ltrlcostB       = $ArrLtrLCost2[$i];
			$ltrlcostA       = $ArrLtrLCost1[$i];
			
			$ltrtype_now = "{$leathercontent}!!{$leathertype}!!{$leatherskintype} {$leatherskinsize}!!{$ltrwidthA}-{$ltrwidthB}!!{$ltrweightA}-{$ltrweightB}!!{$ltryy}!!{$ltrconsumptionA}!!{$ltrconsumptionB}!!{$ltrlcostB}!!{$ltrlcostA}!!{$leathermmcode}!!{$leatherdes}";
			
			// echo "<pre>[$QDID / $Orderno] $ltrtype_now</pre>";
			array_push($ltr_type, $ltrtype_now);
		}
		
		$leather_cost += $totalLtrTotal;
		
	 }//-- End foreach --//
	 
	$leather_type = implode("^^", $ltr_type);
	$query_soc = $conn->prepare("UPDATE tblfirstsoc 
                                SET 
								leatherCost=:leatherCost,
								leather_type =:leather_type 
                                WHERE
                                orderno = :orderno
                                LIMIT 1
                                ");
    $query_soc->bindParam(':leatherCost', $leather_cost);
    $query_soc->bindParam(':leather_type', $leather_type);
    $query_soc->bindParam(':orderno', $orderno);
    $query_soc->execute();   
}

public function update_fabric_firstsoc($conn, $orderno, $QDID_arr){// quotation fabric retransfer to firstsoc (2020-06-03 w)
    
    $big_mm_type = array();
    $big_mmidarr = array();
    $big_mmremarks = array();  
    $big_cspcustarr = array();
    $qdid_count = 0;
    $tmp_fCost=0;
	
	$arr_ne = array();
	$sqlGmt = "SELECT ne.Orderno as ne_order
				FROM tblgarment g
				INNER JOIN tblq_detail qd ON qd.QDID = g.QDID
				INNER JOIN tblnewenquiry ne ON ne.QDID = qd.QDID
				WHERE g.orderno = '$orderno'
				group by ne.ori_orderno";
	$stmt_gmt = $conn->prepare($sqlGmt);
    $stmt_gmt->execute();
	while($row_gmt = $stmt_gmt->fetch(PDO::FETCH_ASSOC)){
		extract($row_gmt);
		
		array_push($arr_ne, $ne_order);
	}

	$gmt = sizeof($arr_ne);
	$str_ne = implode(";", $arr_ne);
	
    foreach($QDID_arr as $QDID){
        //Fabric data
        $mm_type   = array();
        $mmidarr   = array();
        $mmremarks = array();
		
        // select from tblq_detail
        $query_q = $conn->prepare("SELECT h.CurrencyID, d.ArrFabric, d.ArrFID, d.ArrFType, d.ArrYY, d.ArrConsumption, d.ArrPla, d.ArrFabCost, d.ArrCIF, d.ArrLBSYDS, ne.Orderno, d.ArrPEWCost, d.ArrFabwetResult, d.cspcustarr
                                    FROM tblq_detail AS d
                                    INNER JOIN tblq_header AS h ON d.QHID = h.QHID
									INNER JOIN tblnewenquiry ne ON ne.QDID = d.QDID
                                    WHERE d.QDID = :QDID");
        $query_q->bindParam(':QDID', $QDID);
        $query_q->execute();
 
        $row_q = $query_q->fetch(PDO::FETCH_ASSOC);
        
        $ne_orderno = $row_q["Orderno"];
        $CurrencyID = $row_q["CurrencyID"];
        $ArrPEWCost = $row_q["ArrPEWCost"];
        $ArrFabric = explode(":", $row_q["ArrFabric"]);
        $ArrFID = explode(":", $row_q["ArrFID"]);
        $ArrFType = explode(":", $row_q["ArrFType"]);
        $ArrYY = explode(":", $row_q["ArrYY"]);
        $ArrConsumption = explode(":", $row_q["ArrConsumption"]);
        $ArrPla = explode(":", $row_q["ArrPla"]);
        $ArrFabCost = explode(":", $row_q["ArrFabCost"]);
        $ArrCIF = explode(":", $row_q["ArrCIF"]);
        $ArrLBSYDS = explode(":", $row_q["ArrLBSYDS"]);
        $ArrFabwetResult = explode(":", $row_q["ArrFabwetResult"]);	// wet cost (2023-08-02 w)
		$wet_cal = array_sum($ArrFabwetResult);
		
		// add wet cost into PEW "W" (2023-08-02 w)
		$ArrPEWCost = explode(":", $ArrPEWCost);
		$ArrPEWCost[2] += $wet_cal;
		$ArrPEWCost = implode(":", $ArrPEWCost);
		
		$cspcustarr = $row_q["cspcustarr"];
                
        $bodycounter = count($ArrFabric);

        for($f=0;$f<$bodycounter;$f++){
            //$minifabric = "$yarn1^^$yarn2^^$yarn3^^$content^^$fabricdes^^$BWidth^^$BWeight^^$BWidthInt^^$BWeightMin^^$mmcode^^$fabricremark";
            $minifabric = explode("^^", $ArrFabric[$f]);
            
            $yarn1 = strtoupper($minifabric[0]);
            $yarn2 = strtoupper($minifabric[1]);
            $yarn3 = strtoupper($minifabric[2]);
            $FabricContent = strtoupper($minifabric[3]);
            $FabType = strtoupper($minifabric[4]);
            $ExternalWidth = strtoupper($minifabric[5]);
            $max_weight_gm = strtoupper($minifabric[6]);
            $InternalWidth = strtoupper($minifabric[7]);
            $min_weight_gm = strtoupper($minifabric[8]);
            $qmmcode = strtoupper($minifabric[9]);
            $fabricremark = strtoupper($minifabric[10]);
			
			$YarnCount = "$yarn1 $yarn2";
                      
            $YY = $ArrYY[$f];
            $wastage = $ArrConsumption[$f];
            $consumption = $ArrPla[$f];
            $price = $ArrCIF[$f];
            $fabunit = $ArrLBSYDS[$f];
            $fabCost = $ArrFabCost[$f];
			
			// echo "[$QDID] / $FabType | fab cost: $fabCost [$fabunit] << <br/>";
                	
            switch($fabunit){
                case 0: $fabunit = "LBS";  break;
                case 1: $fabunit = "YDS";  break;
                case 2: $fabunit = "PC";  break;
                case 60: $fabunit = "M";  break;
                default: $fabunit = "LBS";  break;
            }

            $tmp_fCost +=$fabCost;
            
			// if(trim($FabricContent)!="" && trim($FabType)!="" && $price>0 && $consumption>0 && $YY>0){
				$Arrtype = "{$FabricContent},{$FabType},{$YarnCount},{$InternalWidth}-{$ExternalWidth},{$min_weight_gm}-{$max_weight_gm},{$YY},{$wastage},{$consumption},{$fabunit},{$price},{$fabCost},{$gmt},{$qmmcode}";
			// }
			// echo "<pre> $ne_orderno [$qmmcode] / $fabCost / YY: $YY / consumption: $consumption [$QDID]</pre><br/>";			
			
            
            $mmid = "";
			$ftop = $yarn1;
			$fbottom = $yarn2;
			$fspender = $yarn3;
			$ftype = $FabricContent;
			$fdescription = $FabType;
			$fint = $InternalWidth;
			$fext = $ExternalWidth;
			$fmin = $min_weight_gm;
			$fmax = $max_weight_gm;
			$mmcode = $qmmcode;
//echo "$mmid / $ftop / $fbottom / $fspender / $ftype / $fdescription / $fint / $fext / $fmin / $fmax / $mmcode<br/>";
			$ftypeq = $conn->prepare('SELECT ID FROM tblfabtype WHERE Description = :des LIMIT 1');
			$ftypeq->bindParam(':des', $fdescription);
			$ftypeq->execute();
			$numf_result = $ftypeq->rowCount();
										
			//if not
			if($fdescription != ""){
			if(($numf_result == 0)){
				$strf = "SELECT count(ID) as num FROM tblfabtype";
				$resultf = $conn->query($strf);
				$resultfabric= $resultf->fetch(PDO::FETCH_ASSOC);
				$fID = $resultfabric["num"];
				$fID = $fID + 1;
				
				$fstatus = 1;
				
				//Insert query
				$finsert = $conn->prepare('INSERT INTO tblfabtype (ID, Description, StatusID) VALUES (:ID, :Description, :statusID)');
				$finsert->bindParam(':ID', $fID);
				$finsert->bindParam(':Description', $fdescription);
				$finsert->bindParam(':statusID', $fstatus);
				//$finsert->execute();
			}else{
				//if exist, cancel insert action
				$ftyperesult= $ftypeq->fetch(PDO::FETCH_ASSOC);
				$fID = $ftyperesult['ID'];
			}
			}else{
				$fID = "empty";
			}
			//fabric type end
			
			//tblmm_detail start
			//check mmdetail exist or not
			if($fID != "empty"){
				$mmdetailq = $conn->prepare('SELECT MMID FROM tblmm_detail WHERE
				FabricContent = :des AND
				FabricTypeID = :ID AND
				mmcode = :mmcode AND
				min_weight_gm = :min_weight_gm AND 
				max_weight_gm = :max_weight_gm AND 
				InternalWidth = :InternalWidth AND 
				ExternalWidth = :ExternalWidth AND 
				TopYarn = :TopYarn AND 
				BottomYarn = :BottomYarn AND 
				spandex = :spandex AND 
				currencyID = :currencyID 
				LIMIT 1');
				$mmdetailq->bindParam(':des', $ftype);
				$mmdetailq->bindParam(':ID', $fID);
				//$mmdetailq->bindParam(':MMTID', $topID);
				$mmdetailq->bindParam(':mmcode', $mmcode);
				$mmdetailq->bindParam(':min_weight_gm', $fmin);
				$mmdetailq->bindParam(':max_weight_gm', $fmax);
				$mmdetailq->bindParam(':InternalWidth', $fint);
				$mmdetailq->bindParam(':ExternalWidth', $fext);
				$mmdetailq->bindParam(':TopYarn', $ftop);
				$mmdetailq->bindParam(':BottomYarn', $fbottom);
				$mmdetailq->bindParam(':spandex', $fspender);
				$mmdetailq->bindParam(':currencyID', $CurrencyID);
				$mmdetailq->execute();
				$numd_result = $mmdetailq->rowCount();
				
				//if not
				if($numd_result == 0){
					$strdetail = "SELECT MAX(CAST(MMID AS SIGNED)) as num FROM tblmm_detail";
					$resultdetail = $conn->query($strdetail);
					$resultd= $resultdetail->fetch(PDO::FETCH_ASSOC);
					$detailIDs = $resultd["num"];
					$detailID = $detailIDs + 1;
					
					$statusID = 1;
					$Shrinkage = "W/L 8%";
					$multiplier = "1";
					$basic_unit = "57";
					$price_unitID = "57";
					$MOQ = "5";
					$MPQ = "30";
					
					$yardmin = $fmin * $fext * $multiplier * 0.0232;	//use ext only (2017-05-18 w)
					$yardmax = $fmax * $fext * $multiplier * 0.0232;
									
					//Insert query
					$detailinsert = $conn->prepare('INSERT INTO tblmm_detail (MMID, FabricContent, FabricTypeID, statusID, mmcode, min_weight_gm, max_weight_gm, min_weight_yard, max_weight_yard, InternalWidth, ExternalWidth, TopYarn, BottomYarn, spandex, Shrinkage, multiplier, basic_unit, price_unitID, currencyID, MOQ, MPQ) VALUES (:MMID, :FabricContent, :FabricTypeID, :statusID, :mmcode, :min_weight_gm, :max_weight_gm, :min_weight_yard, :max_weight_yard, :InternalWidth, :ExternalWidth, :TopYarn, :BottomYarn, :spandex, :Shrinkage, :multiplier, :basic_unit, :price_unitID, :currencyID, :MOQ, :MPQ)');
					$detailinsert->bindParam(':MMID', $detailID);	
					$detailinsert->bindParam(':FabricContent', $ftype);								
					$detailinsert->bindParam(':FabricTypeID', $fID);												
					//$detailinsert->bindParam(':MMTID', $topID);								
					$detailinsert->bindParam(':statusID', $statusID);
					$detailinsert->bindParam(':mmcode', $mmcode);
					$detailinsert->bindParam(':min_weight_gm', $fmin);
					$detailinsert->bindParam(':max_weight_gm', $fmax);
					$detailinsert->bindParam(':min_weight_yard', $yardmin);
					$detailinsert->bindParam(':max_weight_yard', $yardmax);
					$detailinsert->bindParam(':InternalWidth', $fint);
					$detailinsert->bindParam(':ExternalWidth', $fext);
					$detailinsert->bindParam(':TopYarn', $ftop);
					$detailinsert->bindParam(':BottomYarn', $fbottom);
					$detailinsert->bindParam(':spandex', $fspender);
					$detailinsert->bindParam(':Shrinkage', $Shrinkage);
					$detailinsert->bindParam(':multiplier', $multiplier);
					$detailinsert->bindParam(':basic_unit', $basic_unit);
					$detailinsert->bindParam(':price_unitID', $price_unitID);				
					$detailinsert->bindParam(':currencyID', $CurrencyID);
					$detailinsert->bindParam(':MOQ', $MOQ);				
					$detailinsert->bindParam(':MPQ', $MPQ);
					//$detailinsert->execute();
					$mmid = $detailID;
				}else{
					//if exist, cancel insert action
					$detailresult= $mmdetailq->fetch(PDO::FETCH_ASSOC);
					$mmid = $detailresult['MMID'];
				}
				
			}
			//tblmm_detail end
            
			array_push($mm_type, $Arrtype);
			array_push($mmremarks, $fabricremark);
			array_push($mmidarr, $mmid);
			array_push($big_cspcustarr, $cspcustarr);
		}
		
		$mmremarks = implode(":", $mmremarks);
		$mm_type = implode(":", $mm_type);
		$mmidarr = implode(":", $mmidarr);
        // echo "$mm_type<br/>";
        // echo "$mmremarks<br/>";
        // echo "$mmidarr<br/>";

        array_push($big_mm_type, $mm_type);
        array_push($big_mmremarks, $mmremarks);
        if($qdid_count == 0){
            array_push($big_mmidarr, $mmidarr);
            $qdid_count++;
        }        
    }
    
    $big_mmremarks = implode(";;;", $big_mmremarks);
    $big_mm_type = implode(";;;", $big_mm_type);
    $big_mmidarr = implode(";;;", $big_mmidarr);
    $big_cspcustarr = implode(",", $big_cspcustarr);
	$cspcustarr  = explode(",", $big_cspcustarr);					
	$cspcustarr = array_sum($cspcustarr) / count($cspcustarr);	
	// echo "$big_mm_type << <br/>";
    
    // update tblfirstsoc
    $query_soc = $conn->prepare("UPDATE tblfirstsoc 
                                SET 
								styleno ='$str_ne',
								ArrPEWCost ='$ArrPEWCost',
                                remarks = :big_mmremarks,
                                mmid = :big_mmidarr,
                                mm_type = :big_mm_type,
                                fabCost =$tmp_fCost,
								cspcustarr=:cspcustarr
                                WHERE
                                orderno = :orderno
                                LIMIT 1
                                ");
    $query_soc->bindParam(':big_mmremarks', $big_mmremarks);
    $query_soc->bindParam(':big_mm_type', $big_mm_type);				
    $query_soc->bindParam(':big_mmidarr', $big_mmidarr);
    $query_soc->bindParam(':cspcustarr', $cspcustarr);
    $query_soc->bindParam(':orderno', $orderno);
    $query_soc->execute();   
	
	$sqlUpdate = "UPDATE tblcosting 
                   SET ArrPEWCost='$ArrPEWCost' 
				   WHERE orderno = '$orderno'";
	$query_soc = $conn->prepare($sqlUpdate);
	$query_soc->execute();
    
}

public function update_acc_firstsoc($conn, $orderno, $QDID_arr){// quotation acc retransfer to firstsoc (2020-08-10 ckwai)
	$str_acc = ""; $count_acc = 1; $tmp_accCost=0; $grand_amt = 0;
	foreach($QDID_arr as $QDID){
		// echo "$QDID << <br/>";
		
		$sql = "SELECT ap.APID, ap.AsubtypeID, ap.AMID, ap.dozPcs, ap.allQty, ap.uprice, ap.unitprice, astp.Description as subtype, atype.Description as acc_type, ne.QDID, ap.orderno as neorderno
			FROM tblapurchase ap
			INNER JOIN tblasubtype astp ON astp.ID = ap.AsubtypeID
			inner join tblatype atype ON atype.ID = astp.AtypeID
			INNER JOIN tblnewenquiry ne ON ne.Orderno = ap.orderno
			WHERE ne.QDID = '$QDID' AND ap.statusID NOT IN (6) AND ap.unitprice>0 AND ne.QDID!='0'";
		// echo "<pre>$sql</pre>";
		$result = $conn->query($sql);
		
		while($row = $result->fetch(PDO::FETCH_BOTH)){
			$neorderno = $row["neorderno"];
			$AsubtypeID = $row["AsubtypeID"];
			$AMID = $row["AMID"];
			$dozPcs = $row["dozPcs"];
			$allQty = $row["allQty"];
			$unitprice = $row["unitprice"];
			$uprice = $row["uprice"];
			$subtype = $row["subtype"];
			$acc_type = $row["acc_type"];
			$this_acc = ($AMID==0? "0-$AsubtypeID":"1-$AMID");
			$accCost = $unitprice;//round($allQty * $unitprice,6);
			$tmp_accCost +=$accCost;

			if($count_acc==1){
				$str_acc .= "$this_acc,$this_acc,$dozPcs,$allQty,$uprice,$accCost";
			}
			else{
				$str_acc .= ":$this_acc,$this_acc,$dozPcs,$allQty,$uprice,$accCost";
			}
			// echo "[$neorderno] $this_acc,$this_acc,$dozPcs,$allQty,$uprice,$unitprice [$acc_type /// $subtype]<br/>";
			$grand_amt += $unitprice;
			
			++$count_acc;
			
			// echo "[$neorderno] [$unitprice] $accCost / $tmp_accCost << <br/>";
		}
	}
	
	// echo "tmp_accCost: $tmp_accCost [$orderno]";
	
	$sqlUpdate = "UPDATE tblfirstsoc SET accCost =$tmp_accCost, ac_type='$str_acc' WHERE orderno = '$orderno'"; 
	$updateresult = $conn->exec($sqlUpdate);
	
	// echo "grand_amt: $grand_amt << <br/>";
	
}

function funcToQuo($conn, $connlog, $bomq, $THID=0, $acctid=0){
	//get all NE like keyword, remove select gmt.QDID by ckwai on 202001311255
	$sql = "SELECT ne.Orderno, ne.QDID, qd.QHID 
								FROM tblnewenquiry AS ne
								INNER JOIN tblq_detail AS qd ON qd.QDID = ne.QDID
								LEFT JOIN tblgarment gmt ON gmt.QDID = qd.QDID
								WHERE ne.Orderno LIKE '$bomq%'  AND ne.isbommatch = '1' 
								-- AND gmt.QDID is NULL AND ne.ne_hidden='0'
								group by ne.Orderno"; // modified add inner join tblgarment in order avoid update transferred quotation by ckwai on 201910311653
	// echo "<pre>$sql</pre>";
	$allquery = $conn->prepare($sql);
	$allquery->execute();
	$allno = $allquery->rowCount();

	while($allrow = $allquery->fetch(PDO::FETCH_ASSOC)){
		$QHID = $allrow["QHID"];
		$QDID = $allrow["QDID"];
		$neorderno = $allrow["Orderno"];
		
		// echo "<hr/>$neorderno << <br/>";
		
		//Fabric's data array
		$ArrFType = array();
		$ArrYY = array();
		$ArrConsumption = array();
		$ArrPla = array();
		$ArrCIF = array();
		$ArrFabCost = array();
		$ArrFID = array();
		$ArrLBSYDS = array();
		$ArrFabric = array();
		
		//Leather's data array
		$ArrLtr1 = array(); //leather type
		$ArrLtr2 = array(); //Skin Type
		$ArrLtr3 = array(); //MM Code
		$ArrLtr4 = array(); //Content
		$ArrLtr5 = array(); //Skin Size
		$ArrLtr6 = array(); //Material Description
		$ArrLtrMMID = array(); // MMID
		$ArrLtrWidth1 = array(); //Int Width
		$ArrLtrWidth2 = array(); //Ext Width
		$ArrLtrWeight1 = array(); //Min Thickness
		$ArrLtrWeight2 = array(); //Max Thickness
		$ArrLtrYY = array(); //YY before wastage always SQF
		$ArrLtrConsumption1 = array(); //wastage
		$ArrLtrConsumption2 = array(); //YY after wastage defend on unit
		$ArrLtrLCost1 = array(); //unit price depend on unit
		$ArrLtrLCost2 = array(); //unit measurement
		$ArrLtrCost = array(); //total amount = unit price x YY After wastage 
		$totalLtrTotal = 0;// grand total amount all leather
		$totalLeather = 0;
		
		$filter_THID = ($THID!=0? " AND h.THID='$THID' ": "");
		//--- Modified by ckwai on 202001311246 for ascending body first, after interlining [7519], then binding and flat yoko
		$query_bom = "		SELECT DISTINCT d.bomfabricID, d.subID, d.bom_type, bf.markerName, mmd.MMID
							FROM tblorderbom_detail AS d
							INNER JOIN tblorderbom_header AS h ON h.obHID = d.obHID
                            INNER JOIN tblbomfabric bf ON bf.bomfabricID = d.bomfabricID AND bf.subID = d.subID
                            LEFT JOIN tblmm_detail mmd ON mmd.MMID = bf.fabricSpec
							WHERE h.orderno = '$neorderno' AND d.bom_type=1 AND mmd.FabricTypeID NOT IN (7519) AND d.del=0 
							$filter_THID 
							
							UNION ALL
							
							SELECT DISTINCT d.bomfabricID, d.subID, d.bom_type, bf.markerName, mmd.MMID
							FROM tblorderbom_detail AS d
							INNER JOIN tblorderbom_header AS h ON h.obHID = d.obHID
                            INNER JOIN tblbomfabric bf ON bf.bomfabricID = d.bomfabricID AND bf.subID = d.subID
                            LEFT JOIN tblmm_detail mmd ON mmd.MMID = bf.fabricSpec
							WHERE h.orderno = '$neorderno' AND d.bom_type=1 AND mmd.FabricTypeID IN (7519) AND d.del=0
							$filter_THID 
							
							UNION ALL
							
							SELECT DISTINCT d.bomfabricID, d.subID, d.bom_type, 'z' as markerName, '' as MMID
							FROM tblorderbom_detail AS d
							INNER JOIN tblorderbom_header AS h ON h.obHID = d.obHID
							WHERE h.orderno = '$neorderno' AND d.bom_type NOT IN (1,4) AND d.del=0
							$filter_THID 
							
							UNION ALL 
                            
                            SELECT DISTINCT d.bomfabricID, d.subID, d.bom_type, bl.marker_name as markerName, mmd.MMID
                            FROM tblorderbom_detail d 
                            INNER JOIN tblorderbom_header h ON h.obHID = d.obHID
                            INNER JOIN tblbom_leather bl ON bl.ID = d.bomfabricID
                            LEFT JOIN tblmm_detail mmd ON mmd.MMID = bl.MMID
                            WHERE h.orderno='$neorderno' AND d.bom_type=4 AND bl.del=0 $filter_THID 
							order by markerName asc, bom_type";
		// echo "<pre>$query_bom</pre>";
		$nequery = $conn->prepare($query_bom);
		// $nequery->bindParam(':orderno', $neorderno);
		$nequery->execute();
		$neno = $nequery->rowCount();
		$num = 0;
		while($nerow = $nequery->fetch(PDO::FETCH_ASSOC)){
			$num++;
			$bomfabricID = $nerow["bomfabricID"];
			$subID       = $nerow["subID"];
			$bom_type    = $nerow["bom_type"];
			$MMID        = $nerow["MMID"];

			switch($bom_type){
				case 1:
					$query = "SELECT 'Body' AS tblname, '1' AS ID0, bf.bomfabricID AS ID1, bf.subID AS ID2, mm.FabricContent, f.Description AS FabricTypeID,
							mm.mmcode, bf.bomID, mm.min_weight_gm, mm.max_weight_gm, mm.InternalWidth, mm.ExternalWidth, mm.TopYarn, mm.BottomYarn, mm.spandex, bf.totalSLengthDozen AS YY, bf.losspercent AS com,
							bf.description as remarks, po.Description as position, bf.price, bf.unit, 
							(select GROUP_CONCAT(pt.description) from tblproducttype pt where find_in_set(pt.ID,bf.prodtype)) AS prodtype, '' as skintype, mm.multiplier, mm.aft_min_gm, mm.aft_max_gm, mm.MMID
					FROM tblbomfabric AS bf 
					INNER JOIN tblmm_detail AS mm ON bf.fabricSpec = mm.MMID
					INNER JOIN tblfabtype AS f ON f.ID = mm.FabricTypeID
					LEFT OUTER JOIN tblposition po ON po.ID = bf.positionID
					WHERE bf.statusID = '7' AND bf.bomfabricID = :bomfabricID AND bf.subID = :subID 
					LIMIT 1"; // AND bf.isDefault = '1'
				break;					
				
				case 2:	
					$query = "SELECT 'Binding' AS tblname, '2' AS ID0, bb.bombindID AS ID1, '0' AS ID2, mm.FabricContent, f.Description AS FabricTypeID, mm.mmcode, bb.bomID,
							mm.min_weight_gm, mm.max_weight_gm, mm.InternalWidth, mm.ExternalWidth, mm.TopYarn, mm.BottomYarn, mm.spandex, bb.consumption AS YY, bb.losspercent AS com, 
							bb.partName as remarks, p.Description as position, bb.price, bb.unit, '' AS prodtype, '' as skintype, mm.multiplier, mm.aft_min_gm, mm.aft_max_gm, mm.MMID
					FROM tblbombinding AS bb 
					INNER JOIN tblmm_detail AS mm ON bb.MMID = mm.MMID
					INNER JOIN tblfabtype AS f ON f.ID = mm.FabricTypeID
					LEFT JOIN tblposition p ON p.ID = bb.position
					WHERE bb.statusID = '7' AND bb.isDefault = '1' AND bb.bombindID = :bomfabricID LIMIT 1";
				break;

				case 3:
					$query = "SELECT 'Flat Knit' AS tblname, '3' AS ID0, bfys.ID as ID1, bfy.bomflatyokoID as ID2, CONCAT(mmd.FabricContent, ' ', bfy.sizeheight) AS FabricContent, ft.Description as FabricTypeID, mmd.mmcode, bfy.bomID,
							mmd.min_weight_gm, mmd.max_weight_gm, mmd.InternalWidth, mmd.ExternalWidth, mmd.TopYarn, mmd.BottomYarn, mmd.spandex, bfy.gmtNo as YY, bfy.losspercent as com,
							bfy.remark as remarks, file.Description as position, bfys.price, bfys.unit, '' AS prodtype, '' as skintype, mmd.multiplier, mmd.aft_min_gm, mmd.aft_max_gm, mmd.MMID
					FROM tblbomflatyoko bfy
					LEFT JOIN tblbomfysize_detail bfys ON bfys.flatID = bfy.bomflatyokoID
					LEFT JOIN tblmm_detail mmd ON mmd.MMID = bfys.MMID
					LEFT JOIN tblflatknitfile AS file ON file.ID = bfy.flatknitID
					LEFT JOIN tblfabtype ft ON mmd.FabricTypeID = ft.ID
					WHERE bfy.statusID = '7' AND bfy.isDefault = '1' AND bfy.bomflatyokoID =:bomfabricID LIMIT 1";
				break;
				
				case 4: 
					$query = "SELECT 'Body' AS tblname, '4' AS ID0, bl.ID AS ID1, '0' AS ID2, mm.FabricContent, f.Description AS FabricTypeID,
							mm.mmcode, bl.detail_orderno as bomID, mm.min_weight_gm, mm.max_weight_gm, mm.InternalWidth, mm.ExternalWidth, mm.TopYarn, mm.BottomYarn, mm.spandex, bl.consumption_before AS YY, bl.wastage AS com,
							bl.remark as remarks, po.Description as position, '' as price, u.Description as unit, 
							'' AS prodtype, mmsk.description as skintype, mm.multiplier, mm.aft_min_gm, mm.aft_max_gm, mm.MMID, bl.consumption_after, bl.unitprice
					FROM tblbom_leather AS bl 
					INNER JOIN tblmm_detail AS mm ON bl.MMID = mm.MMID
					INNER JOIN tblfabtype AS f ON f.ID = mm.FabricTypeID
					LEFT OUTER JOIN tblposition po ON po.ID = bl.positionID
                    LEFT JOIN tblunit u ON u.ID = bl.unitID
                    LEFT JOIN tblmm_skin mmsk ON mmsk.SKID = mm.SKID
					WHERE bl.statusID = '7' AND bl.is_hidden = '0' AND bl.ID =:bomfabricID
					LIMIT 1";
					break;
				
				default:	$query = "";	break;
			}

			
			if($query != ""){
				// echo "<br/><pre>[$num] $query / $neorderno / $bomfabricID-$subID</pre><br/>";
				$query = $conn->prepare($query);
				//$query->bindParam(':bomq', $bomq);
				$query->bindParam(':bomfabricID', $bomfabricID);		
				if($bom_type == 1){
					$query->bindParam(':subID', $subID);			
				}
				$query->execute();
				
				$bomno = $query->rowCount();
				
				while($row = $query->fetch(PDO::FETCH_ASSOC)){
					$ID0 = $row["ID0"];
					$ID1 = $row["ID1"];
					$ID2 = $row["ID2"];
					
					$MMID = $row["MMID"];
					$tblname = $row["tblname"];
					$FabricContent = $row["FabricContent"];
					$FabricTypeID = $row["FabricTypeID"];
					$skintype = $row["skintype"];
					$multiplier = $row["multiplier"]; //or skin size
					$mmcode = $row["mmcode"];
					$min_weight_gm = $row["min_weight_gm"];
					$max_weight_gm = $row["max_weight_gm"];
					$InternalWidth = $row["InternalWidth"];
					$ExternalWidth = $row["ExternalWidth"];
					$aft_min_gm = $row["aft_min_gm"]; // Min Thickness
					$aft_max_gm = $row["aft_max_gm"]; // Max Thickness
					$TopYarn = $row["TopYarn"]; // or Material Description
					$BottomYarn = $row["BottomYarn"];
					$spandex = $row["spandex"];
					$consumption_after = (isset($row["consumption_after"])? $row["consumption_after"]:0);
					$unitprice = (isset($row["unitprice"])? $row["unitprice"]:0);
					$yy = $row["YY"];
					$yy = round($yy,4);
					$remarks = $row["remarks"];                    
					$losspercent = $row["com"];
					$position = $row["position"];
					$bomID = $row["bomID"];
					$prodtype = $row["prodtype"];
					$price = $row["price"];
					$price = ($price == "" ? 0 : $price);
					$unit = $row["unit"];
					//$unit = ($unit == "LBS" ? 0 : 1);			
                    
                    // new unit condition to avoid empty (2020-03-24 w)
                    switch($unit){
                        case "LBS": $unit = 0;  break;
                        case "YDS": $unit = 1;  break;
                        case "PC": $unit = 2;  break;
                        case "M": $unit = 60;  break;
						case "SQF": $unit = 118; break;
						case "SQM": $unit = 119; break;
                        default: $unit = 0;  break;
                    }

                    
					// if($ID0 == 1){
						// $data = "F";
						// $url = "{$ID1}-{$ID2}";
					// }else{
						// $data = "B";
						// $url = "{$ID1}";
					// }
					
					//table for debug
					// echo "<table border='1'><tr>
					// <td></td>
					// <td>$bomID</td>
					// <td>$tblname</td>
					// <td>$remarks</td>
					// <td>$prodtype</td>
					// <td>$position</td>
					// <td>$mmcode</td>
					// <td>$FabricContent<br/>".$hdlang["Top"].": $TopYarn<br/>".$hdlang["Btm"].": $BottomYarn<br/>".$hdlang["spandex"].": $spandex</td>
					// <td>$FabricTypeID</td>
					// <td>$InternalWidth</td>
					// <td>$ExternalWidth</td>
					// <td>$min_weight_gm</td>
					// <td>$max_weight_gm</td>
					// <td>$yy</td>
					// <td>$losspercent</td>										
					// </tr></table>";
					
					//data for quotation
					$yarn1 = $TopYarn;
                    $yarn1 = str_replace(":", " ", $yarn1);
					$yarn2 = $BottomYarn;
                    $yarn2 = str_replace(":", " ", $yarn2);
					$yarn3 = $spandex;
                    $yarn3 = str_replace(":", " ", $yarn3);
					$mmcode = $mmcode;
                    $mmcode = str_replace(":", " ", $mmcode);
                    
					$fabricremark = preg_replace( "/\r|\n/", "", $remarks);
					$fabricremark = str_replace(":", " ", $fabricremark);
					
					$content = str_replace(":", " ", $FabricContent);
					$fabricdes = str_replace(":", " ", $FabricTypeID);
					$BWidth = str_replace(":", " ", $ExternalWidth);
					$BWeight = str_replace(":", " ", $max_weight_gm);

					$BWidthInt = $InternalWidth;
					$BWeightMin = $min_weight_gm;
					
					$minifabric = "$yarn1^^$yarn2^^$yarn3^^$content^^$fabricdes^^$BWidth^^$BWeight^^$BWidthInt^^$BWeightMin^^$mmcode^^$fabricremark";
					
					echo "<br/> // [$num][$bom_type] $bomfabricID-$subID | $QDID / $neorderno // MM Code: $mmcode / $fabricdes<br/>";
									
					$BLength = $yy;
					$BConsumption = $losspercent;
					// $BConsumptionB = numvalidation($_POST["BConsumption$f$colbfield"]);
					// $BCIF = numvalidation($_POST["BCIF$f$afield"]);
					// //BCIF1lbsyds_5
					// $LBSYDS = $_POST["BCIF{$f}lbsyds{$afield}"];

					// $fabCost = numvalidation($_POST["fabCost$f$afield"]);
					// $fid = $_POST["fid$f$afield"];
					//if($fid == ""){
					$fid = "{$ID0}-{$ID1}-{$ID2}";
					//}		

					// echo "$bom_type << <br/>";
					
					if($bom_type!=4){
						array_push($ArrFType, "empty");
						array_push($ArrYY, $BLength);
						array_push($ArrConsumption, $BConsumption);
						array_push($ArrPla, 0);
						array_push($ArrCIF, $price);
						array_push($ArrFabric, $minifabric);	
						array_push($ArrFabCost, 0);
						array_push($ArrFID, $fid);
						array_push($ArrLBSYDS, $unit);	
						// echo "===>>>>>>>>>>>> $minifabric << <br/>";
					}	
					else{// if leather
						if($unit==119){//if SQM convert to SQF
							$yy = round($this->SQMConvertSQF($yy), 4);
						}
						
						$total_amt = $consumption_after * $unitprice;
					
						array_push($ArrLtr1, "$FabricTypeID"); //leather type
						array_push($ArrLtr2, "$skintype"); //Skin Type
						array_push($ArrLtr3, "$mmcode"); //MM Code
						array_push($ArrLtr4, "$FabricContent"); //Content
						array_push($ArrLtr5, "$multiplier"); //Skin Size
						array_push($ArrLtr6, "$TopYarn"); //Material Description
						array_push($ArrLtrMMID, "$MMID"); // MMID
						array_push($ArrLtrWidth1, "$InternalWidth"); //Int Width
						array_push($ArrLtrWidth2, "$ExternalWidth"); //Ext Width
						array_push($ArrLtrWeight1, "$aft_min_gm"); //Min Thickness
						array_push($ArrLtrWeight2, "$aft_max_gm"); //Max Thickness
						array_push($ArrLtrYY, "$yy"); //YY before wastage always SQF
						array_push($ArrLtrConsumption1, "$losspercent"); //wastage
						array_push($ArrLtrConsumption2, "$consumption_after"); //YY after wastage defend on unit
						array_push($ArrLtrLCost1, "$unitprice"); //unit price depend on unit
						array_push($ArrLtrLCost2, "$unit"); //unit measurement
						array_push($ArrLtrCost, "$total_amt"); //total amount = unit price x YY After wastage 
						$totalLtrTotal += $total_amt;// grand total amount all leather
						$totalLeather++;
					}
				}//-- End While --//			
			}//-- End if --//
		}
		
		// echo "<br/>[$QDID] $neorderno >> ArrFabric: ".count($ArrFabric)." <br/>";
		// echo ">> ArrFabCost: ".count($ArrFabCost)." <br/>";
		
		//update tblq_detail
		$ArrFType = implode(":", $ArrFType);
		$ArrYY = implode(":", $ArrYY);
		$ArrConsumption = implode(":", $ArrConsumption);
		$ArrPla = implode(":", $ArrPla);
		$ArrCIF = implode(":", $ArrCIF);
		$ArrFabric = implode(":", $ArrFabric);
		$ArrFabCost = implode(":", $ArrFabCost);
		$ArrFID = implode(":", $ArrFID);
		$ArrLBSYDS = implode(":", $ArrLBSYDS);
		
		if($acctid==1){
			echo "<pre>[$QDID] $ArrFabric / </pre>";
		}
		
		//update leather 
		$ArrLtr1 = implode("^^", $ArrLtr1);
		$ArrLtr2 = implode("^^", $ArrLtr2);
		$ArrLtr3 = implode("^^", $ArrLtr3);
		$ArrLtr4 = implode("^^", $ArrLtr4);
		$ArrLtr5 = implode("^^", $ArrLtr5);
		$ArrLtr6 = implode("^^", $ArrLtr6);
		$ArrLtrMMID = implode("^^", $ArrLtrMMID);
		$ArrLtrWidth1 = implode("^^", $ArrLtrWidth1);
		$ArrLtrWidth2 = implode("^^", $ArrLtrWidth2);
		$ArrLtrWeight1 = implode("^^", $ArrLtrWeight1);
		$ArrLtrWeight2 = implode("^^", $ArrLtrWeight2);
		$ArrLtrYY = implode("^^", $ArrLtrYY);
		$ArrLtrConsumption1 = implode("^^", $ArrLtrConsumption1);
		$ArrLtrConsumption2 = implode("^^", $ArrLtrConsumption2);
		$ArrLtrLCost1 = implode("^^", $ArrLtrLCost1);
		$ArrLtrLCost2 = implode("^^", $ArrLtrLCost2);
		$ArrLtrCost = implode("^^", $ArrLtrCost);
		
		// echo "[$QDID] $ArrFID <=== <br/><br/>";
				
		$detailquery = $conn->prepare("UPDATE tblq_detail SET 
							ArrFabric = :ArrFabric,
							ArrFID = :ArrFID,
							ArrFType = :ArrFType, 
							ArrYY = :ArrYY, 
							ArrConsumption = :ArrConsumption, 
							ArrPla = :ArrPla,
							ArrFabCost = :ArrFabCost,
							ArrCIF = :ArrCIF, 
							ArrLBSYDS = :ArrLBSYDS,
							ArrLtr1 = :ArrLtr1,
							ArrLtr2 = :ArrLtr2,
							ArrLtr3 = :ArrLtr3, 
							ArrLtr4 = :ArrLtr4, 
							ArrLtr5 = :ArrLtr5, 
							ArrLtr6 = :ArrLtr6,
							ArrLtrMMID = :ArrLtrMMID,
							ArrLtrWidth1 = :ArrLtrWidth1, 
							ArrLtrWidth2 = :ArrLtrWidth2,
							ArrLtrWeight1 = :ArrLtrWeight1,
							ArrLtrWeight2 = :ArrLtrWeight2,
							ArrLtrYY = :ArrLtrYY,
							ArrLtrConsumption1 = :ArrLtrConsumption1,
							ArrLtrConsumption2 = :ArrLtrConsumption2,
							ArrLtrLCost1 = :ArrLtrLCost1,
							ArrLtrLCost2 = :ArrLtrLCost2,
							ArrLtrCost = :ArrLtrCost,
							totalLtrTotal = :totalLtrTotal
							WHERE QDID = :QDID");
		$detailquery->bindParam(':ArrFabric', $ArrFabric);
		$detailquery->bindParam(':ArrFID', $ArrFID);
		$detailquery->bindParam(':ArrFType', $ArrFType);
		$detailquery->bindParam(':ArrYY', $ArrYY);
		$detailquery->bindParam(':ArrConsumption', $ArrConsumption);
		$detailquery->bindParam(':ArrPla', $ArrPla);
		$detailquery->bindParam(':ArrFabCost', $ArrFabCost);
		$detailquery->bindParam(':ArrCIF', $ArrCIF);
		$detailquery->bindParam(':ArrLBSYDS', $ArrLBSYDS);	
		
		// leather update info to quotation
		$detailquery->bindParam(':ArrLtr1', $ArrLtr1);
		$detailquery->bindParam(':ArrLtr2', $ArrLtr2);
		$detailquery->bindParam(':ArrLtr3', $ArrLtr3);
		$detailquery->bindParam(':ArrLtr4', $ArrLtr4);
		$detailquery->bindParam(':ArrLtr5', $ArrLtr5);
		$detailquery->bindParam(':ArrLtr6', $ArrLtr6);
		$detailquery->bindParam(':ArrLtrMMID', $ArrLtrMMID);
		$detailquery->bindParam(':ArrLtrWidth1', $ArrLtrWidth1);
		$detailquery->bindParam(':ArrLtrWidth2', $ArrLtrWidth2);
		$detailquery->bindParam(':ArrLtrWeight1', $ArrLtrWeight1);
		$detailquery->bindParam(':ArrLtrWeight2', $ArrLtrWeight2);
		$detailquery->bindParam(':ArrLtrYY', $ArrLtrYY);
		$detailquery->bindParam(':ArrLtrConsumption1', $ArrLtrConsumption1);
		$detailquery->bindParam(':ArrLtrConsumption2', $ArrLtrConsumption2);
		$detailquery->bindParam(':ArrLtrLCost1', $ArrLtrLCost1);
		$detailquery->bindParam(':ArrLtrLCost2', $ArrLtrLCost2);
		$detailquery->bindParam(':ArrLtrCost', $ArrLtrCost);
		$detailquery->bindParam(':totalLtrTotal', $totalLtrTotal);
		
		$detailquery->bindParam(':QDID', $QDID);							
		$detailquery->execute();
		
        
        //--------- for fabric log (2020-05-15 w) ----------------
        //insert to fabric log
        $quomode = 1;
        $fabricquery = $connlog->prepare('INSERT INTO tblq_fabric (QDID, QHID, ArrFabric, ArrFType, ArrYY, ArrConsumption, ArrPla, ArrFabCost, ArrCIF, mode) VALUES (:QDID, :QHID, :ArrFabric, :ArrFType, :ArrYY, :ArrConsumption, :ArrPla, :ArrFabCost, :ArrCIF, :mode)');
        $fabricquery->bindParam(':QDID', $QDID);
        $fabricquery->bindParam(':QHID', $QHID);
        $fabricquery->bindParam(':ArrFabric', $ArrFabric);
        $fabricquery->bindParam(':ArrFType', $ArrFType);
        $fabricquery->bindParam(':ArrYY', $ArrYY);
        $fabricquery->bindParam(':ArrConsumption', $ArrConsumption);
        $fabricquery->bindParam(':ArrPla', $ArrPla);
        $fabricquery->bindParam(':ArrFabCost', $ArrFabCost);
        $fabricquery->bindParam(':ArrCIF', $ArrCIF);
        $fabricquery->bindParam(':mode', $quomode);		
        $fabricquery->execute();
        
        
        
		//update QHID total fabric number
		//$neno = ($neno < 0 ? 0 : $neno);
		$neno = 0;
		$hquery = $conn->prepare("SELECT ArrFID FROM tblq_detail WHERE QHID = :QHID");
		$hquery->bindParam(':QHID', $QHID);
		$hquery->execute();
		
		while($hrow = $hquery->fetch(PDO::FETCH_ASSOC)){
			$ArrFID = $hrow["ArrFID"];
			$ArrFID = explode(":", $ArrFID);
			$fidno = count($ArrFID);
			if($fidno > $neno){
				$neno = $fidno;
			}
		}
		
		$totalLeather = ($totalLeather==0? 1: $totalLeather);
		//echo "UPDATE tblq_header SET totalFabric = '$neno' WHERE QHID = '$QHID' AND totalFabric < '$neno' LIMIT 1";
		$qhquery = $conn->prepare("UPDATE tblq_header SET totalFabric = :bomno, totalLeather=:totalLeather WHERE QHID = :QHID LIMIT 1");
		$qhquery->bindParam(':bomno', $neno);
		$qhquery->bindParam(':totalLeather', $totalLeather);
		$qhquery->bindParam(':QHID', $QHID);
		//$qhquery->bindParam(':bomnos', $neno);
		$qhquery->execute();
	}
		
	return true;
}

public static function removeXSS($val){
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed 
        // this prevents some character re-spacing such as <java\0script> 
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs 
        $val = strip_tags($val);	//remove html tag
        $val = substr(stripslashes(json_encode($val, JSON_UNESCAPED_UNICODE)), 1, -1);
        $val = htmlspecialchars($val);
        $val = preg_replace('/([\x00-\x08|\x0b-\x0c|\x0e-\x19])/', '', $val); 

        // straight replacements, the user should never need these since they're normal characters 
        // this prevents like <IMG SRC=@avascript:alert('XSS')> 
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
        $search .= '1234567890!@#$%^&*(),';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

            // @ @ search for the hex values
            $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
            // @ @ 0{0,7} matches '0' zero to seven times 
            $val = preg_replace('/(�{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
        }

        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);

        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '('; 
                        $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                        $pattern .= '|'; 
                        $pattern .= '|(�{0,8}([9|10|13]);)';
                        $pattern .= ')*';
                        }
                        $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i'; 
                $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag 
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags 
                if ($val_before == $val) { 
                    // no replacements were made, so exit the loop 
                    $found = false; 
                } 
            } 
        } 
        return $val; 
}





public function funcGetGeneralTable($tblname="tblpaymentterm", $this_id, $this_classname, $this_onchange="", $selected_ID, $filter_query, $confirmed_disabled=""){ //mpo/prebooking_index.php
		$html = '<select name="'.$this_id.'" id="'.$this_id.'" class="'.$this_classname.'" 
							onchange="" '.$confirmed_disabled.'>';
		$stmt = $this->conn->prepare("SELECT ID, Description 
										FROM $tblname 
										WHERE 1=1 $filter_query");
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			
			$selected = ($selected_ID==$ID? "selected": "");
			$html .= "<option value='$ID' $selected >$Description</option>";
		}
		
		$html .= '</select>';
		return $html;
	}

public function ym_to_wording($yymm){
		//format = 2023-01
		$split=explode("-", $yymm);
		$yy=$split[0];
		$mm=$split[1];

		$str_mm="";
		switch ($mm) {
			case '01':
				$str_mm="Jan";
				break;
			case '02':
				$str_mm="Feb";
				break;
			case '03':
				$str_mm="Mar";
				break;
			case '04':
				$str_mm="Apr";
				break;
			case '05':
				$str_mm="May";
				break;
			case '06':
				$str_mm="Jun";
				break;
			case '07':
				$str_mm="Jul";
				break;
			case '08':
				$str_mm="Aug";
				break;
			case '09':
				$str_mm="Sep";
				break;
			case '10':
				$str_mm="Oct";
				break;
			case '11':
				$str_mm="Nov";
				break;
			case '12':
				$str_mm="Dec";
				break;
		}

		$str=$str_mm." ".$yy;

		return $str;
}

public function json_viewer($data, $statusCode = 200){
	    http_response_code($statusCode);
	    header('Content-Type: application/json');
	    echo json_encode($data);
}

public function getIAMinMaxMaterialInstoreDate($orderno){
	$query = " SELECT concat(ifnull(min(date(mrd.MRDate)),min(mpo.ETA)),':::',max(mpo.ETA)) as mrDate
				  from tblmpo_header mpo 
				  inner join tblmpo_detail mpod ON mpod.MPOHID = mpo.MPOHID
				  inner join tblmpurchase_detail mpd ON mpd.MPDID = mpod.MPDID
				  inner join tblmpurchase mp ON mp.MPID = mpd.MPID
				  left join tblmr_detail mrd ON mrd.MPOHID = mpo.MPOHID and mrd.flag_ext=0
				  where mpo.Type in (7,23,25,26,27) and mpo.statusID<>6 
				and mp.orderno=:orderno 
				group by mp.orderno";
	$stmt = $this->conn->prepare($query);
	$stmt->bindParam(":orderno", $orderno);
    $stmt->execute();	
	
	$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);
	$mrDate = (isset($row[0]["mrDate"])? $row[0]["mrDate"]: ":::");
	
	list($minETA, $maxETA) = explode(":::", $mrDate);
	
	$queryupdate = "";
	
}

public function getIAMinMaxAccInstoreDate($orderno){
	$querysew = " SELECT concat(ifnull(min(date(acc.ARDate)), MIN(apo.ETA)),':::',max(apo.ETA)) as mrDate
				from tblapo_header apo 
				inner join tblapo_detail apod ON apod.APOHID = apo.APOHID
				inner join tblapurchase_detail apd ON apd.APDID =apod.APDID
				inner join tblapurchase ap ON ap.APID = apd.APID
				left join tblacc_receive acc ON acc.APOHID = apod.APOHID and acc.valid=1 and acc.statusID=9 and acc.flag_receive=1 and acc.flag_ext=0
				where apo.Type=1 and apo.statusID<>6 and ap.statusID<>6
				and ap.orderno=:orderno 
				group by ap.orderno";
	$stmt = $this->conn->prepare($querysew);
	$stmt->bindParam(":orderno", $orderno);
    $stmt->execute();
	
	$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);
	$mrDate = (isset($row[0]["mrDate"])? $row[0]["mrDate"]: ":::");
	
	list($minsewETA, $maxsewETA) = explode(":::", $mrDate);
				
	$querypack = " SELECT concat(ifnull(min(date(acc.ARDate)), MIN(apo.ETA)),':::',max(apo.ETA)) as mrDate
				from tblapo_header apo 
				inner join tblapo_detail apod ON apod.APOHID = apo.APOHID
				inner join tblapurchase_detail apd ON apd.APDID =apod.APDID
				inner join tblapurchase ap ON ap.APID = apd.APID
				left join tblacc_receive acc ON acc.APOHID = apod.APOHID and acc.valid=1 and acc.statusID=9 and acc.flag_receive=1 and acc.flag_ext=0
				where apo.Type=2 and apo.statusID<>6 and ap.statusID<>6
				and ap.orderno=:orderno 
				group by ap.orderno";
	$stmt = $this->conn->prepare($querypack);
	$stmt->bindParam(":orderno", $orderno);
    $stmt->execute();
	
	$row   = $stmt->fetchALL(PDO::FETCH_ASSOC);
	$mrDate = (isset($row[0]["mrDate"])? $row[0]["mrDate"]: ":::");
	
	list($minpackETA, $maxpackETA) = explode(":::", $mrDate);
	
	$queryupdate = "";
}

} // end of c
?>