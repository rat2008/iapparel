<?php
include('../../../lock.php');
include('../../../shipment_new/shipmentmain/shipmentmainClass.php');
include_once("../lc_class.php");
include("../../../finance/finance_class.php");

$handle_class = new shipmentmainClass();// by ckwai on 2018-06-07
$handle_class->setConnection($conn);
$handle_class->setlanguage($lang);
		
$handle_lc = new lcClass();
$handle_lc->setConnection($conn);
$handle_lc->setHandleShipment($handle_class);
$handle_lc->isBuyerPayment = 1;

$handle_finance = new finance_class($conn,$lang);

//============================================================+
// File name   : example_003.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 003 for TCPDF class
//               Custom Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Custom Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */
$invID = $_GET["id"];
 $sql = "SELECT inv.*, sm.Description as shipmode, lp.Description as portLoading, ifnull(tt.Description,'-') as tradeterm, 
				ifnull(pt.Description,'-') as paymentterm, pt.Day as payterm_day,
				DATE(inv.exfactory) as exfactorydate, lch.lc_number, lch.lc_bank, lch.LCHID, lci.lc_type,
				lci.lc_date, cur.CurrencyCode, cur.Description as cur_description, 
				cp.CompanyName_ENG as ownership, cp.Address as owneraddress, cp.Tel as ownertel, cp.Fax as ownerfax,
				ftym.FactoryName_ENG as manufacturer, ftym.Address as manuaddress, ftym.Tel as manutel, ftym.Fax as manufax,
				cty.Description as manucountry, csn.Name as csn_name, csn.Address as csn_address, od.FactoryID, csn.ConsigneeID, bdt.Description as buyerdestination, dp.countryID as destport_countryID, dp.Description as portDischarges,
				group_concat(distinct sp.Orderno) as orderno, 
				crmb.bank_account_no, crmb.beneficiary_name, crmb.bank_name, crmb.bank_address, crmb.swift_code,
				ct.description as country, ct.countryCode, group_concat(distinct g.styleNo) as style_no, 
				ss.Description as season, bd.Description as brand, pyr.Description as bill_to, pyr.address as bill_address,
				group_concat(distinct sp.ID) as grp_shipmentpriceID, pis.Description as poissuer, pis.address as poissuer_address,
				group_concat(distinct sp.GTN_buyerpo separator ', ') as allBuyerPO, fty_shipper.FactoryName_ENG as shipper, fty_shipper.Tel as shipper_tel, fty_shipper.Fax as shipper_fax, fty_shipper.Address as shipaddr, bnp.NotifyName, bnp.NotifyAddress, bnp.tel as notifyTel, bnp.fax as notifyFax, bnp.email as notifyEmail, cbd.description as buyerdest_country, cbd.countryCode as byrdt_countrycode, cts.description as transitPort,
				uat.UserFullName as exporter_sign, uat.SignatureID, uat.AcctID as userID, uat.Tel as exporter_tel, uat.Fax as exporter_fax,
				uat.position as exporter_pos
			FROM tblbuyer_invoice_payment inv 
			LEFT JOIN tblshipmode sm ON sm.ID = inv.shipmodeID 
			LEFT JOIN tblloadingport lp ON lp.ID = inv.portLoadingID 
			LEFT JOIN tbltradeterm tt ON tt.ID = inv.tradeTermID 
			LEFT JOIN tblpaymentterm pt ON pt.ID = inv.paymentTermID 
			LEFT JOIN tblbuyer_invoice_payment_detail invd ON invd.invID = inv.ID AND invd.del = 0 AND invd.group_number > 0
			LEFT JOIN tbllc_assignment_detail lcd ON lcd.shipmentpriceID = invd.shipmentpriceID AND lcd.del=0 AND invd.del=0
			LEFT JOIN tbllc_assignment_info lci ON lci.LCIID = lcd.LCIID AND lci.del=0 
			LEFT JOIN tbllc_assignment_head lch ON lch.LCHID = lci.LCHID
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
			LEFT JOIN tblpayer pyr ON pyr.ID = inv.built_to
			LEFT JOIN tblpoissuer pis ON pis.ID = inv.poissuer
			LEFT JOIN tblbuyerdestination bdt ON bdt.ID = inv.BuyerDestID
			LEFT JOIN tbldestinationport dp ON dp.ID = inv.PortDestID
			LEFT JOIN tblcrm_bank crmb ON crmb.BID = inv.BID
			LEFT JOIN tblcountry ct ON ct.ID = crmb.countryID
			LEFT JOIN tblcountry cbd ON cbd.ID = bdt.countryID
			LEFT JOIN tblcountry cts ON cts.ID = inv.transitPortID	
			LEFT JOIN tblbuyer_notify_party bnp ON bnp.notifyID = inv.notifyID
			LEFT JOIN tbluseraccount uat ON uat.AcctID = inv.export_by
			WHERE inv.ID='$invID' 
		";
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

$grp_spID      = $invrow["grp_shipmentpriceID"];
$style_no      = $invrow["style_no"];
$season        = $invrow["season"];
$brand         = $invrow["brand"];
$allBuyerPO    = $invrow["allBuyerPO"];
$portLoading   = $invrow["portLoading"];
$tradeterm     = $invrow["tradeterm"];
$paymentterm   = $invrow["paymentterm"];
$payterm_day   = $invrow["payterm_day"];

$buyerdest      = $invrow["buyerdestination"];
$byrdt_country  = $invrow["buyerdest_country"];
$byrdt_ctrcode  = $invrow["byrdt_countrycode"];
$portDischarges = $invrow["portDischarges"];
$transitPort    = $invrow["transitPort"];
$vesselname     = $invrow["vesselname"];

$exfactorydate = $invrow["exfactorydate"];
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

$exporter_sign = strtoupper($exporter_sign);
$exporter_pos  = strtoupper($exporter_pos);
$exporter_fax  = (trim($exporter_fax)==""? "":"FAX: $exporter_fax");
// Include the main TCPDF library (search for installation path).
// require_once('tcpdf_include.php');
require_once('../../../tcpdf/tcpdf.php');

$d = date_create($shippeddate);
$shipdate = date_format($d, "d-M-Y");

$_SESSION["invID255"] = $invID;
$_SESSION["exportersign255"] = $exporter_sign;
$_SESSION["exporterpos255"] = $exporter_pos;
$_SESSION["exportertel255"] = $exporter_tel;
$_SESSION["exporterfax255"] = $exporter_fax;
$_SESSION["shipdate255"] = $shipdate;
$_SESSION["invoice_no255"] = $invoice_no;

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	public $conn;
	public $invID;
	public $shipper = "123";
	public $shipper_addr;
	public $shipper_tel;
	public $conName;
	public $conAddrOnly;
	public $contel;
	public $confax;
	public $portLoading;
	public $transitPort;
	public $buyerdest;
	public $shipdate;
    //Page header
    public function Header() {
		include('../../../lock.php');
		// include('../../../shipment_new/shipmentmain/shipmentmainClass.php');
		// include_once("../lc_class.php");
		
		$handle_class = new shipmentmainClass();// by ckwai on 2018-06-07
		$handle_class->setConnection($conn);
		$handle_class->setlanguage($lang);
		
		$handle_lc = new lcClass();
		$handle_lc->setConnection($conn);
		$handle_lc->setHandleShipment($handle_class);
		$handle_lc->isBuyerPayment = 1;
		
		$invID = $_SESSION["invID255"];
		$sql = "SELECT inv.*, bi.invoice_date as ci_invoice_date, sm.Description as shipmode, lp.Description as portLoading, ifnull(tt.Description,'-') as tradeterm, 
				ifnull(pt.Description,'-') as paymentterm, pt.Day as payterm_day,
				DATE(inv.exfactory) as exfactorydate, lch.lc_number, lch.lc_bank, lch.LCHID, lci.lc_type,
				lci.lc_date, cur.CurrencyCode, cur.Description as cur_description, 
				cp.CompanyName_ENG as ownership, cp.Address as owneraddress, cp.Tel as ownertel, cp.Fax as ownerfax,
				ftym.FactoryName_ENG as manufacturer, ftym.Address as manuaddress, ftym.Tel as manutel, ftym.Fax as manufax,
				cty.Description as manucountry, csn.Name as csn_name, csn.Address as csn_address, od.FactoryID, csn.ConsigneeID, bdt.Description as buyerdestination, dp.countryID as destport_countryID, dp.Description as portDischarges,
				group_concat(distinct sp.Orderno) as orderno, 
				crmb.bank_account_no, crmb.beneficiary_name, crmb.bank_name, crmb.bank_address, crmb.swift_code,
				ct.description as country, ct.countryCode, group_concat(distinct g.styleNo) as style_no, 
				ss.Description as season, bd.Description as brand, pyr.Description as bill_to, pyr.address as bill_address,
				group_concat(distinct sp.ID) as grp_shipmentpriceID, pis.Description as poissuer, pis.address as poissuer_address,
				group_concat(distinct sp.GTN_buyerpo separator ', ') as allBuyerPO, fty_shipper.FactoryName_ENG as shipper, fty_shipper.Tel as shipper_tel, fty_shipper.Fax as shipper_fax, fty_shipper.Address as shipaddr, bnp.NotifyName, bnp.NotifyAddress, bnp.tel as notifyTel, bnp.fax as notifyFax, bnp.email as notifyEmail, cbd.description as buyerdest_country, cbd.countryCode as byrdt_countrycode, cts.description as transitPort,
				uat.UserFullName as exporter_sign, uat.SignatureID, uat.AcctID as userID, uat.Tel as exporter_tel, uat.Fax as exporter_fax,
				uat.position as exporter_pos
			FROM tblbuyer_invoice_payment inv 
			LEFT JOIN tblbuyer_invoice bi ON bi.ID = inv.ID
			LEFT JOIN tblshipmode sm ON sm.ID = inv.shipmodeID 
			LEFT JOIN tblloadingport lp ON lp.ID = inv.portLoadingID 
			LEFT JOIN tbltradeterm tt ON tt.ID = inv.tradeTermID 
			LEFT JOIN tblpaymentterm pt ON pt.ID = inv.paymentTermID 
			LEFT JOIN tblbuyer_invoice_payment_detail invd ON invd.invID = inv.ID AND invd.del = 0 AND invd.group_number > 0
			LEFT JOIN tbllc_assignment_detail lcd ON lcd.shipmentpriceID = invd.shipmentpriceID AND lcd.del=0 AND invd.del=0
			LEFT JOIN tbllc_assignment_info lci ON lci.LCIID = lcd.LCIID AND lci.del=0 
			LEFT JOIN tbllc_assignment_head lch ON lch.LCHID = lci.LCHID
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
			LEFT JOIN tblpayer pyr ON pyr.ID = inv.built_to
			LEFT JOIN tblpoissuer pis ON pis.ID = inv.poissuer
			LEFT JOIN tblbuyerdestination bdt ON bdt.ID = inv.BuyerDestID
			LEFT JOIN tbldestinationport dp ON dp.ID = inv.PortDestID
			LEFT JOIN tblcrm_bank crmb ON crmb.BID = inv.BID
			LEFT JOIN tblcountry ct ON ct.ID = crmb.countryID
			LEFT JOIN tblcountry cbd ON cbd.ID = bdt.countryID
			LEFT JOIN tblcountry cts ON cts.ID = inv.transitPortID	
			LEFT JOIN tblbuyer_notify_party bnp ON bnp.notifyID = inv.notifyID
			LEFT JOIN tbluseraccount uat ON uat.AcctID = inv.export_by
			WHERE inv.ID='$invID' 
		";
		$invsql = $conn->prepare($sql);
		$invsql->execute();
		$invrow = $invsql->fetch(PDO::FETCH_ASSOC);
		$invoice_no     = $invrow["invoice_no"];
		$shipper        = $invrow["shipper"];
		$shipper_addr   = $invrow["shipaddr"];
		$shipper_tel    = $invrow["shipper_tel"];
		$shipper_fax    = $invrow["shipper_fax"];
		$ConsigneeID    = $invrow["ConsigneeID"];
		$portLoading    = $invrow["portLoading"];
		$transitPort    = $invrow["transitPort"];
		$buyerdest      = $invrow["buyerdestination"];
		$buyerID        = $invrow["BuyerID"];
		
		$consql = $conn->prepare("SELECT con.*, c.Description as countryName, bd.Description as brand 
							FROM tblconsignee con 
							LEFT JOIN tblcountry c ON c.ID=con.countryID 
							LEFT JOIN tblbrand bd ON bd.ID = con.brandID
							WHERE con.ConsigneeID='$ConsigneeID' ");
		$consql->execute();
		$conrow = $consql->fetch(PDO::FETCH_ASSOC);
		$conName     = $conrow["Name"]."";
		$conAddrOnly = $conrow["Address"];
		$conEIN      = $conrow["EIN"];
		$contel      = $conrow["tel"];
		$confax      = $conrow["fax"];
		$conemail    = $conrow["email"];
		$shipmode    = $invrow["shipmode"];
		// $shippeddate = $invrow["ci_invoice_date"];
		$shippeddate = $invrow["shippeddate"];// MAO request link from payment ETD 2021-08-18 
		
		$d = date_create($shippeddate);
		$shipdate = strtoupper(date_format($d, "d-M-Y"));
        // Logo
        // $image_file = K_PATH_IMAGES.'logo_example.jpg';
        // $image_file = 'canada_flag.png';//path, [left/right], [top/bottom], [img size]
        // $this->Image($image_file, 10, 8, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        // $this->SetFont('helvetica', 'B', 12);
        // Title
        // $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		
		$html = '<style>
				.font_size{
					font-size:10px;
					font-family:Arial;
				}
				.font_size_9{
					font-size:9px;
					font-family:Arial;
				}
				.font_size_8{
					font-size:8px;
					font-family:Arial;
				}
				.font_size_7{
					font-size:7px;
					font-family:Arial;
				}
				.font_times{
					font-family: "Times New Roman", Times, serif;
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
				.font_courier_new{
					font-family: Courier New, monospace;
					font-size:9px;
				}
				</style>';
		$html .= '<table cellpadding="1" class="font_size_9">
					<tr>
						<td rowspan="2" width="8%">
							<img src="canada_flag.png" height="40" />
						</td>
						<td width="1%"></td>
						<td width="16%">Canada Customs<br/>and Revenue Agency</td>
						<td width="60%">Agence des douanes<br/>et du revenu du Canada</td>
						<td width="15%"><img src="icon001.jpg" height="40" /></td>
					</tr>
				</table>
				
				<br/>
				<br/>

				<table  class="font_size">
					<tr>
						<td align="center">
							CERTIFICATE OF ORIGIN</td>
						</tr>
					<tr>
						<td align="center">Textile and Apparel Goods Originating in a Least Developed Country</td>
						</tr>
					<tr>
						<td align="center">(Instructions attached)</td>
						</tr>
						</table>';
						
		$html .= '<div class="font_size_8">Please print or type</div>
				<table cellpadding="2" class="font_size_8">
				<tr>
					<td width="50%" class="border_left border_right border_top">1. Exporter\'s business name, address and country</td>
					<td width="50%" class="border_left border_right border_top">2. Business name and address of importer in Canada</td>
					</tr>
				<tr>
					<td class="border_left border_right border_btm">
							<div style="padding:5px;" class="font_courier_new">'.$shipper.'<br/>'.$shipper_addr.'<br/>TEL: '.$shipper_tel.' &nbsp; &nbsp; FAX: '.$shipper_fax.'</div></td>
					<td class="border_left border_right border_btm">
							<div style="padding:5px" class="font_courier_new">'.$conName.'<br/>'.$conAddrOnly.'<br/>TEL: '.$contel.' &nbsp; &nbsp; FAX: '.$confax.'</div></td>
					</tr>
				<tr>
					<td colspan="2" class="border_left border_right">3. Means of transport and route (if known)
							<br/>
							<div class="font_courier_new">
							BY '.$shipmode.' FROM '.$portLoading.' VIA '.$transitPort.' <br/>TO '.$buyerdest.'
							<br/>
							<br/>
							SHIPPED ON: '.$shipdate.'
							</div>
							<br/>
						</td>
					</tr>
					</table>';
		
		$prefix_E = "E";//($buyerID=="B82" && glb_profile=="iapparelintl"? "I": "E");//request by Rithy 20240924 //second change 20241009 by Rithy
		$html .= '<table cellpadding="1" class="font_size_8">
					<tr>
						<td class="border_left border_right border_btm border_top" width="14%"> 4. Markings and number of packages</td>
						<td class="border_right border_btm border_top" colspan="4" width="58%"> 5. Description of goods As Apparel</td>
						<td class="border_right border_btm border_top" width="14%"> 6. Preference crit erion</td>
						<td class="border_right border_btm border_top" width="14%"> 7. Number and date of invoices</td>
						</tr>
					<tr>
						<td class="border_left border_right" style="height:493px">
							<br/>
							<br/>
							<div class="font_courier_new">&nbsp;AS PER <br/>&nbsp;COMMERCIAL <br/>&nbsp;INVOICE</div>
						</td>
						<td class="border_right" colspan="4">
							</td>
						<td class="border_right" align="center">
							<br/>
							<br/>
							<br/>
							<br/>
							<br/>
							<br/>
							<div class="font_courier_new">" '.$prefix_E.' "</div>
							</td>
						<td class="border_right">
							<br/>
							<br/>
							<br/>
							<br/>
							<table cellpadding="2">
							<tr><td >
								INVOICE:<br/>
								<b>'.$invoice_no.'</b><br/>
								DATE:<br/>
								'.$shipdate.'</td>
							</tr></table>
						
						</td>
						</tr>
						</table>';
		$arr_array   = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
		$arr_buyerpo = $arr_array["byBuyerPO"];				
		
		$this->writeHTML($html);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        // $this->SetY(-15);
        $this->SetY(-73.5);
		
		$html = '<style>
				.font_size{
					font-size:10px;
					font-family:Arial;
				}
				.font_size_9{
					font-size:9px;
					font-family:Arial;
				}
				.font_size_8{
					font-size:8px;
					font-family:Arial;
				}
				.font_size_7{
					font-size:7px;
					font-family:Arial;
				}
				.font_times{
					font-family: "Times New Roman", Times, serif;
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
				.font_courier_new{
					font-family: Courier New, monospace;
					font-size:9px;
				}
				</style>';
		
		$exporter_sign = $_SESSION["exportersign255"];
		$exporter_pos  = $_SESSION["exporterpos255"] ;
		$exporter_tel  = $_SESSION["exportertel255"] ;
		$exporter_fax  = $_SESSION["exporterfax255"] ;
		$shipdate      = $_SESSION["shipdate255"] ;
		
		$html .= '
			
			<table cellpadding="2" class="font_size_8">
				<tr>
					<td colspan="2" class="top_border"></td>
					</tr>
				<tr>
				<td colspan="2" class="all_border">8. "As the exporter, I hereby declare that the above details and statements are correct, namely:<br/>
					<br/>
			<table>
			<tr>
				<td width="5%"></td>
				<td width="20%">1. all the goods were produced in </td>
				<td class="bottom_border" align="center"><div class="font_courier_new">KINGDOM OF CAMBODIA</div></td>
				<td>; and</td>
				</tr>
			<tr>
				<td></td>
				<td></td>
				<td  align="center" class="font_size_7">(name of country)</td>
				<td></td>
				</tr>
				</table>
			<br/>
			<table>
			<tr>
				<td width="5%"></td>
				<td width="95%">2. the goods comply with the requirements specified for those goods in the <i>General Preferential Tariff and Least Developed Country Tariff Rules of Origin Regulations.</i>"</td>
				</tr>
				</table>
			</td>
		</tr>
	<tr>
		<td class="left_border right_border">Name:</td>
		<td class="left_border right_border">Position in the company:</td>
		</tr>
	<tr>
		<td class="left_border right_border bottom_border" align="center"><div class="font_courier_new">'.$exporter_sign.'</div></td>
		<td class="left_border right_border bottom_border" align="center"><div class="font_courier_new">'.$exporter_pos.'</div></td>
		</tr>
	<tr>
		<td class="left_border right_border">Telephone Number:</td>
		<td class="left_border right_border">Facsimile Number:</td>
		</tr>
	<tr>
		<td class="left_border right_border bottom_border" align="center"><div class="font_courier_new">'.$exporter_tel.'</div></td>
		<td class="left_border right_border bottom_border" align="center"><div class="font_courier_new">'.$exporter_fax.'</div></td>
		</tr>
	<tr>
		<td class="all_border" colspan="2" style="padding:5px" align="center">
			<table cellpadding="3" align="center">
			<tr>
				<td width="5%"></td>
				<td class="bottom_border" width="45%">&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;</td>
				<td class="bottom_border" width="45%">&nbsp; <br/> <div class="font_courier_new">'.$shipdate.'</div></td>
				<td width="5%"></td>
				</tr>
			<tr>
				<td ></td>
				<td align="center">Signature</td>
				<td align="center">Date (yyyy/mm/dd)</td>
				<td ></td>
				</tr>
				</table>
		</td>
		</tr>
		</table>';
		$this->writeHTML($html);
		
        // Set font
        $this->SetFont('helvetica', '', 8);
        // Page number [left],[top/bottom]
        $this->Cell(20, 10, 'PAGE '.$this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');//.'/'.$this->getAliasNbPages()
		
		$image_file = 'Canada.jpg';//path, [left/right], [top/bottom], [img size]
        $this->Image($image_file, 185, 280, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
	
	public function setConnection($conn){
		$this->conn = $conn;
	}
	
	public function setValue($shipper){
		$this->shipper = $shipper;
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setConnection($conn);
$pdf->setValue("12346");

$pdf->shipper = $shipper;
$pdf->shipper_addr = $shipper_addr;
$pdf->shipper_tel = $shipper_tel;
$pdf->conName = $conName;
$pdf->conAddrOnly = $conAddrOnly;
$pdf->contel = $contel;
$pdf->confax = $confax;
$pdf->portLoading = $portLoading;
$pdf->transitPort = $transitPort;
$pdf->buyerdest = $buyerdest;
$pdf->shipdate = $shipdate;

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('B255');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
// ,Top, , 
$pdf->SetMargins(10, 84.5, 10, true); 
// $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetHeaderMargin(10);
// $pdf->SetFooterMargin(150);
// $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
// $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetAutoPageBreak(TRUE, 72);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
// $pdf->SetFont('times', 'BI', 12);

// add a page
$pdf->AddPage();

$html = '<style>
	.font_size{
		font-size:10px;
	}
	.font_size_info{
		font-size:8px;
	}
	.font_size_8{
		font-size:8px;
		font-family:Arial;
	}
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
		/*font-size: 10px;*/ 
	}

	table th{
		/*font-size: 10px;*/
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
	.font_times{
		font-family: "Times New Roman", Times, serif;
	}

	
</style>';
$html_css = $html;
$arr_array   = $handle_lc->getBuyerInvoicePDFInvoice($invID, $query_filter);
$arr_buyerpo = $arr_array["byBuyerPO"];		
$count_row   = 1;
$grand_ctn   = 0;
$all_qty     = 0;
$all_qty_set = 0;
$fab_country = "";

$count_color  = 0;
$count_po     = 0;
$count_tb_row = 0;

foreach($arr_buyerpo as $key => $arr_info){
		$shipmentpriceID  = $arr_info["shipmentpriceID"];
		$arr_info_row  = $arr_info["arr_info"];
		$total_ctn     = $arr_info["total_ctn"];
		$grand_qty     = $arr_info["grand_qty"];
		$uom           = $arr_info["uom"];
		$fab_country   = $arr_info["fab_country"];
		$count_color   = count($arr_info["arr_info"]);
		$setID         = $arr_info["setID"];
		
		$grand_ctn += $total_ctn;
		$count_row += 3;
		foreach($arr_info_row as $prepack_key => $arr_value){
			$count_row++;
		}
		
		// echo "shipmentpriceID: $key / $grand_qty << <br/>";
		
		if($setID==1){ //PCS
			$all_qty += $grand_qty;
		}
		else{
			$all_qty_set += $grand_qty;
		}
	}
// $totalHeight = $pdf->getPageHeight($page) / $pdf->getScaleFactor();

	
$html .= <<<EOD
	<table class="font_size_8" style="height:100%;min-height:100%">
	<tr>
		<td rowspan="$count_row" class=" " width="14%;"  style="height:11px">
			<br/>
			<br/>
			
			</td>
		<td colspan="4" class=" " width="58%"> <b>$grand_ctn CTNS</b></td>
		<td rowspan="$count_row" align="center" class=" " width="14%">
				</td>
		<td rowspan="$count_row" class=" " width="14%">
			
		</td>
		</tr>
EOD;

$count_tb_row++;

$arr_po = array();
foreach($arr_buyerpo as $key => $arr_info){
	$shipmentpriceID  = $arr_info["shipmentpriceID"];
	$arr_info_row  = $arr_info["arr_info"];
	$ship_marking  = $arr_info["ship_marking"];
	$styleNo       = $arr_info["styleNo"];
	$quotacat      = $arr_info["quotacat"];
	
	// print_r($arr_info_row);
	
	$ship_marking = strtolower($ship_marking);
	$ship_marking = html_entity_decode($ship_marking);
	$ship_marking = strtoupper($ship_marking);
	
	$count_po++;
	$count_tb_row++;

	$arrbuyerpo = explode("%%", $key);
	$key = $arrbuyerpo[0];

	$arrstyleno = explode("_", $styleNo);
	$styleNo = $arrstyleno[0];

	$this_po    = trim("$key");
	$this_style = "$styleNo";
	
	
	//added by ckwai on 2024-10-14 avoid duplicate same PO# display for case BINV24001926
	if(!in_array($this_po, $arr_po)){
		$arr_po[] = $this_po;
	}
	else{
		continue;
	}
	
	// echo "PO: $this_po << <br/>";

$html.= <<<EOD
	<tr>
		<td colspan="4" class=" " style="height:11px" > $ship_marking</td>
		<!--<td class="left_border right_border" ></td>-->
		</tr>
	<tr>
		<td class="" width="14%" style="height:11px"> P.O NUMBER</td>
		<td width="15%">STYLE #</td>
		<td width="15%">NG ITEM#</td>
		<td class="" width="14%">COLOUR</td>
		<!-- <td class="left_border right_border"></td>-->
		</tr>
	
EOD;

$arr_color = array();

foreach($arr_info_row as $prepack_key => $arr_value){
	list($prepack_name, $group_number) = explode("**^^", $prepack_key);
	$colorOnly     = $arr_value["colorOnly"];
	$total_ctn_qty = $arr_value["qty"];
	$fob_price     = $arr_value["fob_price"];
	$garmentID     = $arr_value["garmentID"];
	$this_po       = (trim($this_po)!=""? "$this_po": "");
	$this_style    = (trim($this_style)!=""? "$this_style": "");
	
	//added by ckwai on 2024-10-14 avoid duplicate same color display for case BINV24001926
	//added by ckwai on 2024-11-07 prepack_name with color due to missing prepack_name# display for BINV24001926
	$str = trim($colorOnly)."_".trim($prepack_name);
	if(!in_array($str, $arr_color)){
		$arr_color[] = $str;
	}
	else{
		continue;
	}
	
	$sqlqc = "SELECT qc.Description as this_quotacat, bih.ht_code
                     FROM tblbuyer_invoice_payment_hts bih
                     LEFT JOIN tblquotacat qc ON qc.ID = bih.quotaID
                     WHERE bih.invID='$invID' AND bih.shipmentpriceID='$shipmentpriceID'  AND qc.Description !=''
					 order by ht_code desc";
	$stmtqc = $conn->query($sqlqc);
	$rowqc = $stmtqc->fetch(PDO::FETCH_ASSOC);
	$quotacat = $rowqc["this_quotacat"];
	$ht_code  = $rowqc["ht_code"];
	
	$count_color++;

$html .= <<<EOD
	<tr>
		<td class="" style="height:11px"> $this_po</td>
		<td>$this_style</td>
		<td>$prepack_name</td>
		<td class="">$colorOnly</td>
		<!--<td class="left_border right_border"></td>-->
		</tr>
EOD;

$count_tb_row++;

$this_po    = "";
$this_style = "";
	
	
}//--- end foreach po color level ---//

$lbl_hts = ($BuyerID=="B82" && glb_profile=="iapparelintl"? "HTS: $ht_code <br/>": "");

$html .= <<<EOD
	<tr>
		<td colspan="2" class="" style="height:11px">$lbl_hts CATEGORY NO: $quotacat</td>
		<td></td>
		<td class=""></td>
		<!--<td class="left_border right_border"></td>-->
		</tr>
EOD;

$count_tb_row++;
}//--- End foreach buyer po ---//

$word_qty = strtoupper($handle_finance->convert_number($all_qty)." PCS ONLY");
$str_qty  = number_format($all_qty);

// echo "all_qty: $all_qty << <br/>";

$word_qty_set = strtoupper($handle_finance->convert_number($all_qty_set)." SETS ONLY");
$str_qty_set  = number_format($all_qty_set);

$str_pcs  = '';
$str_set  = '';
$word_pcs = '';
$word_set = '';
if($all_qty>0){ // PCS
	$str_pcs  = ($all_qty_set>0? "<b> QTY: $str_qty PCS</b><br/>": "<b> QTY: $str_qty PCS</b>");
	$word_pcs = ($all_qty_set>0? "<b>TOTAL: $word_qty</b><br/>":"<b>TOTAL: $word_qty</b>");
}
if($all_qty_set>0){ // PCS
	$str_set  = "<b> QTY: $str_qty_set SETS</b>";
	$word_set = "&nbsp;<b>TOTAL: $word_qty_set</b>";
}

$html .= <<<EOD
	<tr>
		<td class="  " style="height:11px"></td>
		<td colspan="4" class="  ">
				</td>
		<td class="  "></td>
		<td class="  "></td>
		</tr>
	<tr>
		<td class="  " style="height:11px"></td>
		<td colspan="4" class="  ">
				$str_pcs $str_set
				</td>
		<td class="  "></td>
		<td class="  "></td>
		</tr>
	<!--<tr>
		<td class="  " style="height:11px"></td>
		<td colspan="4" class="  ">
				</td>
		<td class="  "></td>
		<td class="  "></td>
		</tr>-->
	<tr>
		<td class="  " style="height:11px"></td>
		<td colspan="4" class="  ">
				&nbsp;Fabric Origin: $fab_country
				</td>
		<td class="  "></td>
		<td class="  "></td>
		</tr>
	<tr>
		<td class="  " style="height:11px"></td>
		<td colspan="4" class="  ">
				&nbsp;Yarn Origin: $fab_country
				</td>
		<td class="  "></td>
		<td class="  "></td>
		</tr>
	<!--<tr>
		<td class="  " style="height:11px"></td>
		<td colspan="4" class="  ">
				
				</td>
		<td class="  "></td>
		<td class="  "></td>
		</tr>-->
	<tr>
		<td class="  " style="height:11px"></td>
		<td colspan="4" class="  ">
				$word_pcs $word_set
				</td>
		<td class="  "></td>
		<td class="  "></td>
		</tr>
EOD;
$count_tb_row += 6;
$balance_row = 0;
if($count_tb_row<42){
	$balance_row = 43 - $count_tb_row;
}
else{
	$num = floor($count_tb_row / 42);
	$balance_row = $count_tb_row - ($num * 42);
	$balance_row = 42 - $balance_row;
}


	// for($i=1;$i<=$balance_row;$i++){
	// $html .= <<<EOD
	// <tr>
		// <td class="left_border right_border "></td>
		// <td colspan="4" class=" right_border "></td>
		// <td class=" right_border "></td>
		// <td class=" right_border "></td>
		// </tr>
// EOD;
	// }


$html .= <<<EOD
	</table>
	
EOD;

//border_btm

$pdf->writeHTML($html, true, 0, true, 0);

$idealHeight = $pdf->getPageHeight();

// set some text to print
$txt = <<<EOD
TCPDF Example 003

Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
EOD;

// print a block of text using Write()
// $pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);

// ---------------------------------------------------------

//Close and output PDF document
if($acctid!=0){
$pdf->Output('B255_'.$invoice_no.'.pdf', 'I');
}

//============================================================+
// END OF FILE
//============================================================+