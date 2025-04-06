<?php
//error reporting
// ini_set('display_errors', 1); 
// ini_set('display_startup_errors', 1); 
// error_reporting(E_ALL);
//db connection

ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 90000);
ini_set('post_max_size','1024M');
ini_set('upload_max_filesize','1024M');

include('../../lock.php');
// include('../../model/_linkmodel.php');
// include('../../includes/config.inc');
include('../../shipment_new/shipmentmain/shipmentmainClass.php');
include('../../shipment_new/shipmentmain/packingformquery.php');
include_once("../../function/misc.php");
// include_once("../../class/fabInstoreClass.php");
// include("../../finance/finance_class.php");
include_once("lc_class.php");

$handle_class = new shipmentmainClass();// by ckwai on 2018-06-07
$handle_class->setConnection($conn);
$handle_class->setlanguage($lang);

$handle_misc = new misc($conn);
	
// $handle_fabInstore = new fabInstoreClass($conn, $lang, $acctid);
// $handle_fabInstore->setHandleMisc($handle_misc);

$handle_lc = new lcClass();
$handle_lc->setConnection($conn);
$handle_lc->setHandleShipment($handle_class);

// $handle_finance = new finance_class($conn,$lang);

// $split_total = explode(".", $grand_amt);
// $dollar = strtoupper($handle_finance->convert_number($split_total[0]));
// $cent   = strtoupper($handle_finance->convert_number($split_total[1]));
// $str_words = strtoupper($cur_description." ".$dollar." AND ".$cent." CENTS ONLY.");

// ob_start();
if(empty($_GET["id"]) || !isset($_GET['id'])){
	header("Location: buyer_inv_list.php");
}
$invID = $_GET["id"];
if(isset($_GET["displayBuyer"])){
	$displayBuyer = $_GET["displayBuyer"];
}

$excel=0;
if(isset($_GET['excel'])){
	$excel=$_GET['excel'];

}

function numberTowords($num)
{ 

	$ones = array(
		0 =>"ZERO", 
		1 => "ONE", 
		2 => "TWO", 
		3 => "THREE", 
		4 => "FOUR", 
		5 => "FIVE", 
		6 => "SIX", 
		7 => "SEVEN", 
		8 => "EIGHT", 
		9 => "NINE", 
		10 => "TEN", 
		11 => "ELEVEN", 
		12 => "TWELVE", 
		13 => "THIRTEEN", 
		14 => "FOURTEEN", 
		15 => "FIFTEEN", 
		16 => "SIXTEEN", 
		17 => "SEVENTEEN", 
		18 => "EIGHTEEN", 
		19 => "NINETEEN"
	); 
	$tens = array( 
		0 => "ZERO",
		1 => "TEN",
		2 => "TWENTY",
		3 => "THIRTY", 
		4 => "FORTY", 
		5 => "FIFTY", 
		6 => "SIXTY", 
		7 => "SEVENTY", 
		8 => "EIGHTY", 
		9 => "NINETY" 
	); 
	$hundreds = array( 
		"HUNDRED", 
		"THOUSAND", 
		"MILLION", 
		"BILLION", 
		"TRILLION", 
		"QUARDRILLION" 
	); /*limit t quadrillion */

	$num = number_format($num,2,".",","); 
	$num_arr = explode(".",$num); 
	$wholenum = $num_arr[0]; 
	$decnum = $num_arr[1]; 
	$whole_arr = array_reverse(explode(",",$wholenum)); 
	krsort($whole_arr,1); 
	$rettxt = ""; 
	foreach($whole_arr as $key => $i){
		
		while(substr($i,0,1)=="0")
			$i=substr($i,1,5);

		if($i < 20){ 
			/* echo "getting:".$i; */
			$rettxt .= $ones[$i]; 
		}elseif($i < 100){ 
			if(substr($i,0,1)!="0")  
				$rettxt .= $tens[substr($i,0,1)]; 
			if(substr($i,1,1)!="0") 
				$rettxt .= " ".$ones[substr($i,1,1)]; 
		}else{ 
			if(substr($i,0,1)!="0") 
				$rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 
			if(substr($i,1,1)!="0")
				$rettxt .= " ".$tens[substr($i,1,1)]; 
			if(substr($i,2,1)!="0")
				$rettxt .= " ".$ones[substr($i,2,1)]; 
		} 

		if($key > 0){ 
			$rettxt .= " ".$hundreds[$key]." "; 
		}
	} 

	if($decnum > 0){
		$rettxt .= " and "; 

		if($decnum < 20){ 
			$rettxt .= $ones[$decnum]; 
		}elseif($decnum < 100){ 
			$rettxt .= $tens[substr($decnum,0,1)]; 
			$rettxt .= " ".((substr($decnum,1,1) == 0 || substr($decnum,1,1) == "0") ? "" : $ones[substr($decnum,1,1)]); 
		} 
		$rettxt .= "CENTS "; 
	} 
	return $rettxt;
 
}

// Include the main TCPDF library (search for installation path).
require_once('../../tcpdf/tcpdf.php');

class B255DOC extends TCPDF {

	// Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
		
        // Set font
        $this->SetFont('helvetica', '', 8);
        // Page number [left],[top/bottom]
        $this->Cell(20, 10, 'PAGE '.$this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		
		$image_file = 'buyer_template/Canada.jpg';//path, [left/right], [top/bottom], [img size]
        $this->Image($image_file, 185, 285, 18, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
}

class B10DOC extends TCPDF {

	// Custom method to set the HTML header content
    public function setCustomHeaderHTML($html) {
        $this->customHeaderHTML = $html;
    }

    // Page header
    public function Header() {
        // Get the current page number
        $page = $this->getPage();
		$numpage=$this->getAliasNbPages();
		$print_datetime=date("Y/m/d H:i:s");

        // Set the header content based on the page number
        if ($page >= 2) {
            // Use the custom HTML for pages after the second page
            $this->SetY(10);
            $html_header='
            	<table cellpadding="3">
            		<tr>
						<td colspan="2" class="center-align" style="font-size:20px;"><b>Packing List Detail</b></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="3" style="text-align:right;font-size:10px;">
							Page '.$page.' / '.$numpage.' <br>
							'.$print_datetime.'
						</td>
					</tr>
            	</table>
            ';
            $this->writeHTMLCell(0, 10, '', '', $html_header, 0, 1, 0, true, 'C', true);
        } else {
            // Default header for first and second pages
            $this->SetY(10);
            $this->SetFont('helvetica', 'B', 12);
            $this->Cell(0, 10, '', 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }
    }

}
$displayBuyer = (isset($_GET["displayBuyer"])? $_GET["displayBuyer"]:"");

if($displayBuyer=="B255"){
// create new PDF document
$pdf = new B255DOC(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
}else if($displayBuyer=="B10"){
// create new PDF document
$pdf = new B10DOC(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
}
else{
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
}

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Site');
$pdf->SetTitle('Buyer Invoice in PDF');
$pdf->SetSubject("Buyer Invoice");
$pdf->SetKeywords('TCPDF, PDF, Buyer Invoice');

$pdf->setPrintHeader(false);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
// $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// IMPORTANT: disable font subsetting to allow users editing the document
$pdf->setFontSubsetting(false);

// set font
//$pdf->SetFont('cid0cs', '', 6, '', false);
// $pdf->SetFont('arialuni', '', 6, '', false); //heavy


//set margin
$pdf->SetMargins(10, 10, 10, true);


// add a page
// $pdf->AddPage('P', 'A4');


// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$html = <<<EOD
<style>
	.bold-text {
		font-weight: bold;
	}

	.center-align{
		text-align: center;
		
	}

	p.p-format {
		white-space: pre-wrap; 	
	}

	table td{
		font-size: 10px; 
	}

	table th{
		font-size: 12px; 
	}

	.table-bordered th, 
	.table-bordered td {
		border: 1px solid black;
	}
	.tb_joefresh th{
		border: 1px solid black;
	}
	
	td.all_border{
		border: 1px solid black;
	}
	td.top_border{
		border-top: 1px solid black;
	}
	td.left_border{
		border-left: 1px solid black;
	}
	td.right_border{
		border-right: 1px solid black;
	}
	td.bottom_border{
		border-bottom: 1px solid black;
	}

	.font-red {
		color: red;
	}

	.font-blue {
		color: blue;
	}

	.full-border {
		border: 1px solid black;
	}
	.border_btm, .border-b {
		border-bottom: 1px solid black;
	}
	.border_top, .border-t {
		border-top: 1px solid black;
	}
	.border_right, .border-r {
		border-right: 1px solid black;
	}
	.border_left, .border-l {
		border-left: 1px solid black;
	}
	.border-rl, .border-lr {
		border-left: 1px solid black;
		border-right: 1px solid black;
	}
	.border_left_bold {
		border-left: 3px solid black;
	}


	.dashedborder_btm {
		border-bottom: 1px dashed black;
	}
	.dashedborder_top {
		border-top: 1px dashed black;
	}
	.dashedborder_right {
		border-right: 1px dashed black;
	}
	.dashedborder_left {
		border-left: 1px dashed black;
	}

	
</style>
EOD;

$html_css = $html;

$isBuyerPayment = (isset($_GET["isBuyerPayment"])? $_GET["isBuyerPayment"]: 0);

if($isBuyerPayment==0){
	$tblbuyer_invoice = "tblbuyer_invoice";
	$tblbuyer_invoice_detail = "tblbuyer_invoice_detail";
}
else{
	$tblbuyer_invoice = "tblbuyer_invoice_payment";
	$tblbuyer_invoice_detail = "tblbuyer_invoice_payment_detail";
}

$sql = "
		SELECT inv.*, sm.Description as shipmode, lp.Description as portLoading, lp2.Description as portReceive,
			ifnull(tt.Description,'-') as tradeterm, ifnull(pt.Description,'-') as paymentterm, pt.Day as payterm_day,
			DATE(inv.exfactory) as exfactorydate, '' as lc_number, '' as lc_bank, '' as LCHID, '' as lc_type,
			'' as lc_date, cur.CurrencyCode, cur.Description as cur_description, 
			cp.CompanyName_ENG as ownership, cp.Address as owneraddress, cp.Tel as ownertel, cp.Fax as ownerfax, cp.taxID,
			ftym.FactoryName_ENG as manufacturer, ftym.Address as manuaddress, ftym.Tel as manutel, ftym.Fax as manufax,
			cty.Description as manucountry, csn.Name as csn_name, csn.Address as csn_address, od.FactoryID, csn.ConsigneeID, '' as buyerdestination, '' as destport_countryID, '' as portDischarges,
			group_concat(distinct sp.Orderno) as orderno, 
			crmb.bank_account_no, crmb.beneficiary_name, crmb.bank_name, crmb.bank_address, crmb.swift_code,
			ct.description as country, ct.countryCode, group_concat(distinct g.styleNo) as style_no, 
			ss.Description as season, bd.Description as brand, '' as bill_to, '' as bill_address,
			group_concat(distinct sp.ID) as grp_shipmentpriceID, '' as poissuer, '' as poissuer_address,
			group_concat(distinct sp.GTN_buyerpo separator ', ') as allBuyerPO, 
			group_concat(distinct sp.BuyerPO separator ', ') as allBuyerPO_notori, 
			fty_shipper.FactoryName_ENG as shipper, fty_shipper.Tel as shipper_tel, fty_shipper.Fax as shipper_fax, fty_shipper.Address as shipaddr, bnp.NotifyName, bnp.NotifyAddress, bnp.tel as notifyTel, bnp.fax as notifyFax, bnp.email as notifyEmail, '' as buyerdest_country, '' as byrdt_countrycode, cts.description as transitPort, fty_shipper.MID,
			uat.UserFullName as exporter_sign, uat.SignatureID, uat.AcctID as userID, uat.Tel as exporter_tel, uat.Fax as exporter_fax,
			uat.position as exporter_pos,csn.EIN
		FROM $tblbuyer_invoice inv 
		LEFT JOIN tblshipmode sm ON sm.ID = inv.shipmodeID 
		LEFT JOIN tblloadingport lp ON lp.ID = inv.portLoadingID 
		LEFT JOIN tblloadingport lp2 ON lp2.ID = inv.porID 
		LEFT JOIN tbltradeterm tt ON tt.ID = inv.tradeTermID 
		LEFT JOIN tblpaymentterm pt ON pt.ID = inv.paymentTermID 
		LEFT JOIN $tblbuyer_invoice_detail invd ON invd.invID = inv.ID AND invd.del = 0 AND invd.group_number > 0
		
		LEFT JOIN tblshipmentprice sp ON invd.shipmentpriceID = sp.ID
		LEFT JOIN tblgarment g ON find_in_set(g.garmentID, sp.StyleNo)
		LEFT JOIN tblorder od ON od.Orderno = sp.Orderno
		LEFT JOIN tblseason ss ON ss.ID = od.seasonID
		LEFT JOIN tblbrand bd ON bd.ID = od.brandID
		LEFT JOIN tblcurrency cur ON cur.ID = od.currencyID
		LEFT JOIN tblcompanyprofile cp ON cp.ID = inv.issue_from
        LEFT JOIN tblfactory ftym ON ftym.FactoryID = od.manufacturer
        LEFT JOIN tblfactory fty_shipper ON fty_shipper.FactoryID = inv.shipper
        LEFT JOIN tblcountry cty ON cty.ID = ftym.countryID
		LEFT JOIN tblconsignee csn ON find_in_set(csn.ConsigneeID, inv.ConsigneeID)   
		LEFT JOIN tblcrm_bank crmb ON crmb.BID = inv.BID
		LEFT JOIN tblcountry ct ON ct.ID = crmb.countryID 
		LEFT JOIN tblcountry cts ON cts.ID = inv.transitPortID	
		LEFT JOIN tblbuyer_notify_party bnp ON bnp.notifyID = inv.notifyID
		LEFT JOIN tbluseraccount uat ON uat.AcctID = inv.export_by
		WHERE inv.ID='$invID' 
	";

// echo "<pre>$sql</pre>";
$invsql = $conn->prepare($sql);
$invsql->execute();
$invrow = $invsql->fetch(PDO::FETCH_ASSOC);

$exporter_sign = $invrow["exporter_sign"];
$exporter_tel  = $invrow["exporter_tel"];
$exporter_fax  = $invrow["exporter_fax"];
$exporter_pos  = $invrow["exporter_pos"];
$SignatureID   = $invrow["SignatureID"];
$userID        = $invrow["userID"];

$BuyerID       = $invrow["BuyerID"];
$ConsigneeID   = $invrow["ConsigneeID"];
$byr_invoice_no = $invrow["byr_invoice_no"];
$invoice_no    = $invrow["invoice_no"];
$invoice_date  = $invrow["invoice_date"];
$portLoadingID = $invrow["portLoadingID"];
$remarks       = $invrow["remarks"];
$lc_number     = $invrow["lc_number"];
$lc_bank       = $invrow["lc_bank"];
$lc_date       = $invrow["lc_date"];
$lc_type       = $invrow["lc_type"];
$LCHID         = $invrow["LCHID"];

$shipper        = $invrow["shipper"];
$shipper_addr   = $invrow["shipaddr"];
$shipper_tel    = $invrow["shipper_tel"];
$shipper_fax    = $invrow["shipper_fax"];
$shipper_MID    = $invrow["MID"];

$notify_party   = $invrow["NotifyName"];
$notify_address = $invrow["NotifyAddress"];
$notifyTel      = $invrow["notifyTel"];
$notifyFax      = $invrow["notifyFax"];
$notifyEmail    = $invrow["notifyEmail"];

$ship_to       = $invrow["ship_to"];
$ship_address  = $invrow["ship_address"];
$shipmode      = $invrow["shipmode"];
$shippeddate   = $invrow["shippeddate"];
$ETA           = $invrow["ETA"];
$container_no  = $invrow["container_no"];
$seal_no       = $invrow["seal_no"];
$carrier       = $invrow["carrier"];

$grp_spID      = $invrow["grp_shipmentpriceID"];
$style_no      = $invrow["style_no"];
$season        = $invrow["season"];
$brand         = $invrow["brand"];
$allBuyerPO_notori = $invrow["allBuyerPO_notori"];
$allBuyerPO    = $invrow["allBuyerPO"];
$allBuyerPO    = ($allBuyerPO==""? $allBuyerPO_notori: $allBuyerPO);
$portLoading   = $invrow["portLoading"];
$portReceive   = $invrow["portReceive"];
$tradeterm     = $invrow["tradeterm"];
$paymentterm   = $invrow["paymentterm"];
$payterm_day   = $invrow["payterm_day"];

$buyerdest      = $invrow["buyerdestination"];
$byrdt_country  = $invrow["buyerdest_country"];
$byrdt_ctrcode  = $invrow["byrdt_countrycode"];
$portDischarges = $invrow["portDischarges"];
$transitPort    = $invrow["transitPort"];
$vesselname     = $invrow["vesselname"];
$ein_no			= $invrow["EIN"];

$exfactorydate = $invrow["exfactorydate"];
$taxID         = $invrow["taxID"];
$ownership     = $invrow["ownership"];
$owneraddress  = $invrow["owneraddress"];
$ownertel      = $invrow["ownertel"];
$ownerfax      = $invrow["ownerfax"];
$manufacturer  = $invrow["manufacturer"];
$manuaddress   = $invrow["manuaddress"];
$manutel       = $invrow["manutel"];
$manufax       = $invrow["manufax"];
$manucountry   = $invrow["manucountry"];
$csn_name      = $invrow["csn_name"];
$csn_address   = $invrow["csn_address"];
$od_FactoryID  = $invrow["FactoryID"]; 
$orderno       = $invrow["orderno"];

$bill_to       = $invrow["bill_to"];
$bill_address  = $invrow["bill_address"];
$poissuer          = $invrow["poissuer"];
$poissuer_address  = $invrow["poissuer_address"];
$po_date           = $invrow['poissue_date'];

$bank_account_no  = $invrow["bank_account_no"];
$beneficiary_name = $invrow["beneficiary_name"];
$bank_name        = $invrow["bank_name"];
$bank_address     = $invrow["bank_address"];
$swift_code       = $invrow["swift_code"];
$bank_country     = $invrow["country"];
$destport_countryID   = $invrow["destport_countryID"];
$CurrencyCode    = $invrow["CurrencyCode"];
$cur_description = $invrow["cur_description"];

$sqlCharge = "SELECT sum(total_amount) as other_charge 
				FROM $tblbuyer_invoice_detail 
				WHERE invID='$invID' AND del=0 AND group_number=0";
$stmt = $conn->prepare($sqlCharge);
$stmt->execute();
$chargerow  = $stmt->fetch(PDO::FETCH_ASSOC);
$other_charge = $chargerow["other_charge"];

if($lc_type>0){
	$sqllc = "SELECT lci.lc_date
				FROM tbllc_assignment_info lci 
				WHERE lci.LCHID='$LCHID' AND lci.lc_type='0' AND lci.del='0'";
	$stmt_lc = $conn->prepare($sqllc);
	$stmt_lc->execute();
	$row_lc  = $stmt_lc->fetch(PDO::FETCH_ASSOC);
	$lc_date = $row_lc["lc_date"];
}


//======================================= Consignee detail  =================================//
$sql = "SELECT con.*, c.Description as countryName, bd.Description as brand 
							FROM tblconsignee con 
							LEFT JOIN tblcountry c ON c.ID=con.countryID 
							LEFT JOIN tblbrand bd ON bd.ID = con.brandID
							WHERE con.ConsigneeID='$ConsigneeID' ";
// echo "<pre>$sql</pre>";
$consql = $conn->prepare($sql);
$consql->execute();
$conrow = $consql->fetch(PDO::FETCH_ASSOC);
$brand       = $conrow["brand"];
$conName     = $conrow["Name"]."";
$conAddrOnly = $conrow["Address"];
$conEIN      = $conrow["EIN"];
$contel      = $conrow["tel"];
$confax      = $conrow["fax"];
$conemail    = $conrow["email"];
$concontact  = $conrow["contactperson"];
$conAddress  = str_replace("Tel", "<br>Tel", $conAddrOnly);
$countryName = $conrow["countryName"];
$countryID   = $conrow["countryID"];

//======================================= buyer detail  =================================//
$buyersql = $conn->prepare("SELECT b.* FROM tblbuyer b WHERE b.BuyerID='$BuyerID' ");
$buyersql->execute();
$buyerrow  = $buyersql->fetch(PDO::FETCH_ASSOC);
$BuyerName = $buyerrow["BuyerName_Eng"];
$BuyerAddr = $buyerrow["Address"];


$letterhead_name    = ($isBuyerPayment==1? "$ownership":"$shipper");
$letterhead_address = ($isBuyerPayment==1? "$owneraddress":"$shipper_addr");
$letterhead_tel     = ($isBuyerPayment==1? "$ownertel":"$shipper_tel");
$letterhead_fax     = ($isBuyerPayment==1? "$ownerfax":"$shipper_fax");
$letterhead_title   = ($isBuyerPayment==1? "Buyer Payment Invoice":"Commercial Invoice");
$letterhead_title   = "Commercial Invoice";


$handle_lc->letterhead_name    = $letterhead_name;
$handle_lc->letterhead_address = $letterhead_address;
$handle_lc->letterhead_tel     = $letterhead_tel;
$handle_lc->letterhead_fax     = $letterhead_fax;
$handle_lc->letterhead_title   = $letterhead_title;
$handle_lc->isBuyerPayment     = $isBuyerPayment;

// $html .= '<br pagebreak="true">';

$title = ""; $filename = "";
$file_prefix = ($isBuyerPayment==0? "CI":"BI");
$filename    = $file_prefix."_".$invoice_no.".pdf";

if(isset($_GET["displayBuyer"])){
	switch($displayBuyer){
		case "B28": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4');include("buyer_template/buyer_oliver.php"); break;
		case "B49": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4');include("buyer_template/buyer_decathelon.php"); break;
		case "B13": $pdf->SetFont('arialuni', '', 6, '', false);
					switch($destport_countryID){
						case 205: $pdf->AddPage('P', 'A4');include("buyer_template/buyer_joefresh_usa.php"); break;
						default: $pdf->AddPage('P', 'A4');include("buyer_template/buyer_joefresh.php"); break;
					}
					break; 
		case "B81": $pdf->AddPage('P', 'A4');include("buyer_template/buyer_shortstry.php"); 
					$pdf->SetFont('arialuni', '', 6, '', false); break;//san sophanny
		case "B26": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4');include("buyer_template/buyer_roots.php"); break; 
		case "B37": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4');include("buyer_template/buyer_noble.php"); break;//include("buyer_template/buyer_sherpa.php"); break; 
		case "B47": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4');include("buyer_template/buyer_itochu.php"); break; 
		case "B64": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4');include("buyer_template/buyer_kohls.php"); 
					break; 
		case "B53": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4');include("buyer_template/buyer_puma.php"); break; 
		case "B55": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4');$title=" External Buyer"; include("buyer_template/buyer_hunnybunny.php"); break; 
		case "B56": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4');$title=" Hunny Bunny"; include("buyer_template/buyer_hunnybunny.php"); break; 
		case "B59": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4');include("buyer_template/buyer_dxl.php"); break; 
		case "B63": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4');
				switch($destport_countryID){
					case "119": $title=" MEXICO"; include("buyer_template/buyer_buffalo_tw.php"); break;//MEXICO
					case "89":  $title=" JAPAN"; include("buyer_template/buyer_buffalo_tw.php"); break;//JAPAN
					case "181": $title=" TAIWAN"; include("buyer_template/buyer_buffalo_tw.php"); break; //TAIWAN
					case "205": $title=" USA"; include("buyer_template/buyer_buffalo_cn.php"); break; //USA
					case "33":  $title=" CANADA"; include("buyer_template/buyer_buffalo_cn.php"); break; //CANADA
				}break;
		case "B36": //$pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('P', 'A4'); include("buyer_template/buyer_aeo.php"); break; 
		case "B35": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_walmart.php"); break;
		case "B66": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_disney.php"); break;
		case "B68": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_caregiver.php"); break;
		case "CCI": //$pdf->SetFont('arialuni', '', 6, '', false); 
					$filename = "CCI_".$invoice_no.".pdf";
					$pdf->AddPage('P', 'A4'); include("buyer_template/cci_template.php"); break; 
		case "BIC2":$pdf->SetFont('times', '', 10, '', false); $filename = "Beneficiary_".$invoice_no.".pdf";
					$pdf->AddPage('P', 'A4'); include("buyer_inv_certificate2.php"); break;
		case "BIC": $pdf->SetFont('times', '', 10, '', false); $filename = "Beneficiary2_".$invoice_no.".pdf";
					$pdf->AddPage('P', 'A4'); include("buyer_inv_certificate.php"); break;
		case "MCO": $html = ""; $pdf->SetFont('arialuni', '', 6, '', false); $filename = "MCO_".$invoice_no.".pdf";
					$pdf->AddPage('P', 'A4'); include("buyer_template/buyer_multi_country.php");break;
		case "BCC": $html = ""; //$pdf->SetFont('arialuni', '', 6, '', false); 
					$filename = "BCC_".$invoice_no.".pdf";
					$pdf->AddPage('P', 'A4'); include("buyer_template/buyer_cert_conformity.php");break;
		case "B255": //$pdf->SetFont('arialuni', '', 6, '', false); 
					$filename = "B255_".$invoice_no.".pdf"; $pdf->AddPage('P', 'A4');
					include("buyer_template/buyer_B255.php");break;
		case "B255A": //$pdf->SetFont('arialuni', '', 6, '', false); 
					$filename = "B255_".$invoice_no.".pdf"; $pdf->AddPage('P', 'A4');
					include("buyer_template/buyer_B2552.php");break;
		case "B36C": $filename = "CONTRACT_".$invoice_no.".pdf"; //$pdf->AddPage('P', 'A4');
					include("buyer_template/buyer_B36C.php");break;
		case "B36O": $filename = "CERT_ORIGIN_".$invoice_no.".pdf"; //$pdf->AddPage('P', 'A4');
					include("buyer_template/buyer_B36_origin.php");break;
		case "B36D": $filename = "DECLARE_".$invoice_no.".pdf"; //$pdf->AddPage('P', 'A4');
					include("buyer_template/buyer_B36_declare.php");break;
		case "B10": 
				if($excel==1){
					echo "<script>window.location='buyer_template/buyer_lacoste_excel.php?id=".$invID."&displayBuyer=".$displayBuyer."&isBuyerPayment=".$isBuyerPayment."'</script>";
				}else{
					$pdf->SetFont('', '', 6, '', false);
					$pdf->setPrintHeader(true);
					$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

					// set margins
					$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, true);
					$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
					$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

					$pdf->AddPage('P', 'A4');include("buyer_template/buyer_lacoste.php"); 
				}
				break;
		case "B08": //$pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('P', 'A4');include("buyer_template/buyer_brahmin.php"); 
					break;
		default: $pdf->SetFont('arialuni', '', 6, '', false);
				 $pdf->AddPage('P', 'A4');include("buyer_template/buyer_noble.php"); break; 
	}
}
else{
	switch($BuyerID){
		case "B28": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_oliver.php"); break;
		case "B49": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_decathelon.php"); break;
		case "B13": $pdf->SetFont('arialuni', '', 6, '', false);
					switch($destport_countryID){
						case 205: $pdf->AddPage('L', 'A4');include("buyer_template/buyer_joefresh_usa.php"); break;
						default: $pdf->AddPage('L', 'A4');include("buyer_template/buyer_joefresh.php"); break;
					}
					break; 
		case "B81": $pdf->AddPage('P', 'A4');include("buyer_template/buyer_shortstry.php"); break;
		case "B26": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_roots.php"); break; 
		case "B37": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_noble.php"); break;//include("buyer_template/buyer_sherpa.php"); break; 
		case "B47": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_itochu.php"); break; 
		case "B64": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_kohls.php"); break; 
		case "B53": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_puma.php"); break; 
		case "B55": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); $title=" External Buyer"; include("buyer_template/buyer_hunnybunny.php"); break; 
		case "B56": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); $title=" Hunny Bunny"; include("buyer_template/buyer_hunnybunny.php"); break; 
		case "B59": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_dxl.php"); break; 
		case "B63": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4');
					switch($destport_countryID){
						case "119": $title=" MEXICO"; include("buyer_template/buyer_buffalo_tw.php"); break; //MEXICO
						case "89":  $title=" JAPAN";  include("buyer_template/buyer_buffalo_tw.php"); break;  //JAPAN
						case "181": $title=" TAIWAN"; include("buyer_template/buyer_buffalo_tw.php"); break; //TAIWAN
						case "205": $title=" USA"; include("buyer_template/buyer_buffalo_cn.php"); break; //USA
						case "33":  $title=" CANADA"; include("buyer_template/buyer_buffalo_cn.php"); break; //CANADA
					}break;
		case "B36": //$pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('P', 'A4'); include("buyer_template/buyer_aeo.php"); break; 
		case "B35": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_walmart.php"); break; 
		case "B66": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_disney.php"); break;
		case "B68": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/buyer_caregiver.php"); break;
		case "CCI": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('L', 'A4'); include("buyer_template/cci_template.php"); break; 
		case "BIC2":$pdf->SetFont('times', '', 10, '', false); 
					$pdf->AddPage('P', 'A4'); include("buyer_inv_certificate2.php"); break;
		case "BIC": $pdf->SetFont('times', '', 10, '', false);
					$pdf->AddPage('P', 'A4'); include("buyer_inv_certificate.php"); break;
		case "MCO": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('P', 'A4'); include("buyer_template/buyer_multi_country.php"); break;
		case "BCC": $html = ""; //$pdf->SetFont('arialuni', '', 6, '', false); 
					$filename = "BCC_".$invoice_no.".pdf";
					$pdf->AddPage('P', 'A4'); include("buyer_template/buyer_cert_conformity.php");break;
		case "B08": $pdf->SetFont('arialuni', '', 6, '', false);
					$pdf->AddPage('P', 'A4');include("buyer_template/buyer_brahmin.php"); 
					break;
		case "B10": 
				if($excel==1){
					echo "<script>window.location='buyer_template/buyer_lacoste_excel.php?id=".$invID."&displayBuyer=".$displayBuyer."&isBuyerPayment=".$isBuyerPayment."'</script>";
				}else{
					$pdf->SetFont('', '', 6, '', false);
					$pdf->setPrintHeader(true);
					$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

					// set margins
					$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, true);
					$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
					$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

					$pdf->AddPage('P', 'A4');include("buyer_template/buyer_lacoste.php"); 
				}
				break;
		default: $pdf->SetFont('arialuni', '', 6, '', false);
				 $pdf->AddPage('P', 'A4');include("buyer_template/buyer_noble.php"); break; 
	}
}



$pdf->writeHTML($html, true, 0, true, 0);


$html = "";

if($BuyerID=="B13" && $destport_countryID==205 && $displayBuyer=="B13"){
	// $pdf->SetFont('times', '', 10, '', false);//times
	// $pdf->AddPage('P', 'A4'); include("buyer_inv_certificate2.php"); 
	// $pdf->writeHTML($html, true, 0, true, 0);
	
	// $html = "";
	// $pdf->SetFont('times', '', 10, '', false);
	// $pdf->AddPage('P', 'A4'); include("buyer_inv_certificate.php"); 
	// $pdf->writeHTML($html, true, 0, true, 0);
	
	// $html = "";
	// $pdf->SetFont('arialuni', '', 6, '', false);
	// $pdf->AddPage('P', 'A4'); include("buyer_template/buyer_multi_country.php"); 
}


$datetoday = date("Y/m/d h:i:sa");

if($BuyerID!="B36"){
$html .= <<<EOD
	<!--<footer><small>Printed Date: $datetoday</small></footer>-->
EOD;
}

/*
<table border="0">
		<tr>
			<th style="width: 80%;">SHIPPING MARKS</th>
			<th style="width: 20%;" align="center">Signature</th>
		</tr>
		<tr>
			<td style="height: 50px; "></td>
			<td style="height: 50px; border-bottom: 1px solid black;"></td>
		</tr>
	</table>
	<br>
	<br>
*/

// echo "$html";
$pdf->writeHTML($html, true, 0, true, 0);

// reset pointer to the last page
// $pdf->lastPage();
// if($acctid!=1){
// ---------------------------------------------------------
//Close and output PDF document
if($acctid!=0){
	$pdf->Output(''.$filename, 'I'); //D
}
// }

//============================================================+
// END OF FILE
//============================================================+
$conn = null;

// I: send the file inline to the browser. The PDF viewer is used if available.
// D: send to the browser and force a file download with the name given by name.
// F: save to a local file with the name given by name (may include a path).
// S: return the document as a string.
?>