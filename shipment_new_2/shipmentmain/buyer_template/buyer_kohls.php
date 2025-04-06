<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// include("../../mpdf/mpdf.php");
// include("../../lock.php");

$id = $_GET['id'];

$html='';
$today_date      = date("m/d/Y");
// $pdf = new \mPDF('C','','6'); 

// ob_start();

// $pdf->SetCreator(PDF_CREATOR);
// $pdf->SetTitle("Kohls");
$html.='
		<table style="width:100%;font-size:10px;">
			<tr>
				<td>'.$today_date.'</td>
				<td>Kohls: Invoice</td>
			</tr>
		</table>
	';
// $pdf->SetHTMLHeader($html);

// $footer='
// 	<table style="width:100%;font-size:10px;">
// 		<tr>
// 			<td>https://commerce.spscommerce.com/fulfillment/transactions/document/1175631920/</td>
// 			<td style="text-align:right;">{PAGENO} / {nbpg}</td>
// 		</tr>
// 	</table>
// ';
// $pdf->SetHTMLFooter($footer);
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE);

// add a page
$pdf->AddPage('', // L - landscape, P - portrait 
        '', '', '', '',
        10, // margin_left
        10, // margin right
       15, // margin top
       10, // margin bottom
        5, // margin header
        5); // margin footer


//$html="";

$html.='<body style="font-family: arial;font-size:11px;">';

// echo "invoice_no: $invoice_no << ";
//header
$html.='<div style="font-size:12px;">';
	$html.='
		<table style="width:100%;">
			<tr>
				<td style="font-size:16px;">
					<b><span style="font-size:21px;font-family: monospace;">I</span>NVOICE</b><br>
					<b><span style="font-size:21px;font-family: monospace;">K</span>OHLS</b>
					<br>
					<table>
						<tr>
							<td>Invoice #:</td>
							<td>'.$invoice_no.'</td>
						</tr>
						<tr>
							<td>Order Number:</td>
							<td>'.$allBuyerPO.'</td>
						</tr>
						<tr>
							<td>Customer Order #:</td>
							<td></td>
						</tr>
						<tr>
							<td>Release #:</td>
							<td></td>
						</tr>
						<tr>
							<td>Currency:</td>
							<td>'.$CurrencyCode.'</td>
						</tr>
					</table>
				</td>
				<td>
					<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
						<tr>
							<td>
								Invoice Date: <br>
								'.$invoice_date.'
							</td>
							<td>
								PO Date: <br>
								'.$po_date.'
							</td>
						</tr>
						<tr>
							<td>
								Ship Date: <br>
								'.$shippeddate.'
							</td>
							<td>
								
							</td>
						</tr>
						<tr>
							<td>
								Vendor #: <br> &nbsp;
							</td>
							<td>
								Department #: <br> &nbsp;
							</td>
						</tr>
					</table>
				</td>
			<tr>
		</table>

		<table style="width:100%;">
			<tr>
				<td style="width:30%" valign="top">
					Ship To: <br>
					<b>Location ID:</b> 00899<br>
					'.$ship_to.'<br/>
					'.$ship_address.'
				</td>
				<td style="width:30%" valign="top">
					Bill To: <br>
					<b>Location ID:</b> 00899<br>
					'.$bill_to.'<br/>
					'.$bill_address.'
				</td>
				<td style="width:30%" valign="top">
					Remit To: <br>
					<b>Location ID:</b> 001174499 <br>
					'.$ownership.'<br/>
					'.$owneraddress.'
				</td>
			</tr>
		</table>
		<br>
		<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
			<tr>
				<td colspan="7">
					Freight Terms: <br> &nbsp;
				</td>
				<td>
					Freight Terms: <br>
					'.$tradeterm.'
				</td>
			</tr>
			<tr>
				<td>
					*Terms Type: <br>
					&nbsp;
				</td>
				<td>
					*Terms Basis: <br>
					&nbsp;
				</td>
				<td>
					*Terms Disc %: <br> &nbsp;
				</td>
				<td>
					*Disc Due Date: <br> &nbsp;
				</td>
				<td>
					*Net Due Date: <br> &nbsp;
				</td>
				<td>
					Net Days: <br>
					'.$payterm_day.'
				</td>
				<td>
					*Disc Amt: <br> &nbsp;
				</td>
				<td>
					*Description: <br> &nbsp;
				</td>
			</tr>
		</table>
	';
$html.='</div><br>';

//details
$html.='
	<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
		<tr style="font-size:15px;">
			<td>LINE</td>
			<!--td>SKU</td-->
			<td>VENDOR PN</td>
			<td>UPC/GTIN</td>
			<td>DESCRIPTION</td>
			<td>UNIT COST</td>
			<td>QTY INVOICED</td>
			<td>UOM</td>
			<td>GROSS PRICE</td>
		</tr>';
$filter = ($acctid==1? " ":""); $arr_sp = array();
$sqlInv = "SELECT bid.shipmentpriceID, sp.BuyerPO, bid.fob_price, se.Description as uom, bid.BICID
			FROM $tblbuyer_invoice_detail bid 
			INNER JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
			INNER JOIN tblorder od ON od.Orderno = sp.Orderno
			INNER JOIN tblset se ON od.Qunit = se.ID
			WHERE bid.invID='$id' AND bid.del='0' AND bid.qty>0 $filter
			group by bid.shipmentpriceID, bid.BICID";
$stmt_inv = $conn->prepare($sqlInv);
$stmt_inv->execute();
$total_ctn = 0;
while($row_inv = $stmt_inv->fetch(PDO::FETCH_ASSOC)){
	$shipmentpriceID = $row_inv["shipmentpriceID"];
	$BuyerPO         = $row_inv["BuyerPO"];
	$BICID           = $row_inv["BICID"];
	$fob_price       = $row_inv["fob_price"];
	$uom             = $row_inv["uom"];
	
	//$filter_query = " AND cpt.shiped=1";
	//$arr_all = $handle_class->getAllPackingInfoByBuyerPO($shipmentpriceID, $od_FactoryID, $filter_query);
	// $arr_all   = $handle_class->getAllCuttingPickListByBuyerPO($shipmentpriceID);
	// $arr_row      = $arr_all["arr_row"];
	// $arr_all_size = $arr_all["arr_all_size"];
	// $ctn_qty      = $arr_all["ctn_qty"];
	$handle_lc->BICID = $BICID;
	$arr_all = $handle_lc->getBuyerInvoicePackingListDataFromCartonInv($shipmentpriceID, $id);
	$arr_row      = $arr_all["arr_list"];
	$ctn_qty      = $arr_all["ctn_qty"];
	$arr_all_size = $arr_all["arr_all_size"];
	
	if(!in_array($shipmentpriceID, $arr_sp)){
		$arr_sp[] = $shipmentpriceID;
	}
	
	$total_ctn += $ctn_qty;
	
	$line = 0; $grand_qty = 0; $total_gross = 0;
	foreach ($arr_all_size as $key => $po_qty) {
		$line++;
		
		list($group_number, $size_name) = explode("**^^", $key);
		
		$sqlSGC = "SELECT group_concat(distinct c.colorName) as gmt_color, g.styleNo, bid.fob_price, 
							group_concat(distinct bih.shipping_marking) as shipping_marking
		
					FROM tblship_group_color sgc 
					INNER JOIN tblcolor c ON c.ID = sgc.colorID
					INNER JOIN tblgarment g ON g.garmentID = sgc.garmentID
																
					LEFT JOIN $tblbuyer_invoice_detail bid ON bid.shipmentpriceID = sgc.shipmentpriceID 
															AND bid.group_number = sgc.group_number
															AND bid.del=0
					
					LEFT JOIN tblbuyer_invoice_payment_hts bih ON bih.BICID = bid.BICID 
																AND bih.shipmentpriceID = bid.shipmentpriceID 
																AND bih.garmentID = sgc.garmentID 
																AND bih.invID = bid.invID
					WHERE sgc.shipmentpriceID='$shipmentpriceID' AND sgc.group_number='$group_number' AND sgc.statusID=1 AND bid.BICID='$BICID'";
		$stmt_sgc = $conn->prepare($sqlSGC); //bid.shipping_marking,
		$stmt_sgc->execute();
		$row_sgc = $stmt_sgc->fetch(PDO::FETCH_ASSOC);
			$gmt_color = $row_sgc["gmt_color"];
			$styleNo   = $row_sgc["styleNo"];
			$fob_price = $row_sgc["fob_price"];
			$shipping_marking = $row_sgc["shipping_marking"];
		
		$gross = round($po_qty * $fob_price, 5);
		$grand_qty   += $po_qty;
		$total_gross += $gross;
		
		$sqlSPD = "SELECT cih.prepack_name
					FROM tblcarton_inv_head cih 
					INNER JOIN tblcarton_inv_detail cid ON cid.CIHID = cih.CIHID
					WHERE cih.del=0 AND cid.del=0 AND cid.shipmentpriceID='$shipmentpriceID' 
					AND cid.group_number='$group_number' AND cih.prepack_name!='' AND cid.size_name='$size_name'";
		$stmt_spd = $conn->prepare($sqlSPD);
		$stmt_spd->execute();
		$row_spd = $stmt_spd->fetch(PDO::FETCH_ASSOC);
			$UPC = $row_spd["prepack_name"];
		
		$this_gross = number_format($gross,2);
		
		// echo "$key -> $po_qty / $UPC /  $fob_price << <br/>";
		
		$html.='
			<tr style="font-size:15px;">
				<td>'.$line.'</td>
				<td>'.$styleNo.'</td>
				<td>'.$UPC.'</td>
				<td>
					Product Description:  '.$shipping_marking.'<br>
					Buyers Color Description:  '.$gmt_color.' <br>
					Buyers Item Size Description: '.$size_name.'
				</td>
				<td>'.$fob_price.'</td>
				<td>'.$po_qty.'</td>
				<td>'.$uom.'</td>
				<td>'.$this_gross.'</td>
			</tr>';
	}
	
}


$total_gross = number_format($total_gross, 2);
$html.='<tr>
			<td colspan="7" align="right">
				Merchandise Total
			</td>
			<td>'.$total_gross.'</td>
		</tr>
	</table>
	<br>

	ALLOWANCE, CHARGES AND TAX INFORMATION: <br><br>
	<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
		<tr>
			<td>TYPE</td>
			<td>SERVICE TYPE</td>
			<td>PERCENT</td>
			<td>RATE</td>
			<td>QTY</td>
			<td>UOM</td>
			<td>DESCRIPTION</td>
			<td>AMOUNT</td>
		</tr>';

$sql = "SELECT other_charge,charge_percentage,total_amount 
							FROM $tblbuyer_invoice_detail binv 
							WHERE binv.other_charge<>'' AND binv.del='0' AND binv.invID='$id'  AND binv.group_number=0 ";
// echo "<pre>$sql</pre>";
$sel_charge=$conn->prepare($sql);
$sel_charge->execute();

while($row_charge=$sel_charge->fetch(PDO::FETCH_ASSOC)){
	$description=$row_charge['other_charge'];
	$percentage=$row_charge['charge_percentage'];
	$charge_amt=$row_charge['total_amount'];

	if($charge_amt>0){
		$html.='
			<tr>
				<td></td>
				<td></td>
				<td>'.$percentage.'</td>
				<td></td>
				<td></td>
				<td></td>
				<td>'.$description.'</td>
				<td>'.$charge_amt.'</td>
			</tr>
		';
	}
}

$str_sp = implode(",", $arr_sp); $total_ctn = 0;
// for($sp=0;$sp<count($arr_sp);$sp++){ // solving for case BINV21000203 rithy 2021-08-19
	
	// $this_sp = $arr_sp[$sp];
	$arr_sp_ctn = array();
	$sqlctn = "SELECT sum(cih.total_ctn) as this_ctn, shipmentpriceID, count(DISTINCT cih.BICID) as count_bic,
		'' as ship_ctn
				FROM `tblcarton_inv_payment_head` cih 
				WHERE  cih.del=0 AND cih.invID='$id'
				group by shipmentpriceID
				-- group by cih.start,  cih.PID, cih.prepack_name, cih.SKU";//cih.shipmentpriceID = '$this_sp' AND
	// echo "<pre>$sqlctn</pre>";
	$stmt_ctn = $conn->prepare($sqlctn);
	$stmt_ctn->execute();
	while($row_ctn = $stmt_ctn->fetch(PDO::FETCH_ASSOC)){
		extract($row_ctn);
		
		if($this_ctn>$ship_ctn && $count_bic>1){
			$this_ctn = ceil($this_ctn / $count_bic);
		}
		
		$total_ctn += $this_ctn;
	}
// }

$html.='
	</table>
	<br>
	<span style="font-size:10px;">Notes/Comments/Special Instruction: </span>
	<br><br>
	<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
		<tr>
			<td>
				Total Qty: '.$grand_qty.' '.$uom.'<br>
				'.$total_ctn.' Carton
			</td>
			<td style="text-align:right;width:70%;">
				Invoice Total
			</td>
			<td>
				'.$total_gross.'
			</td>
		</tr>
	</table>
';

$pdf->autoScriptToLang = true;
$pdf->autoLangToFont = true;


$pdf->writeHTML($html);

//= =============================== packing list ====================================//


$sql = "SELECT invd.shipmentpriceID, invd.BICID
		FROM tblbuyer_invoice_payment_detail invd 
		INNER JOIN tblbuyer_invoice_payment_category cg ON cg.BICID = invd.BICID
		WHERE invd.invID = '$id' AND invd.del='0' AND invd.group_number>0
		group by invd.shipmentpriceID, invd.BICID
		order by cg.options, invd.ID asc";
// echo "<pre>sql: $sql</pre>";
$stmt = $conn->prepare($sql);
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	$shipmentpriceID = $row["shipmentpriceID"];
	$BICID = $row["BICID"];

$pdf->AddPage('L', 'A4');
$html='';
// $pdf->SetHTMLHeader($html);

$handle_lc->BICID = $BICID;
// $this_html = $handle_lc->getBuyerInvoicePackingList($id, $shipmentpriceID);
// $html .= $this_html;

// $pdf->writeHTML($html);

}

// $html ='</body>';
// $pdf->writeHTML($html);



// ob_end_clean();
// if($acctid!=1){
	// $pdf->Output('BuyerInvoice_'.$invoice_no.'.pdf','I');
// }
?>