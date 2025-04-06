<?php 
	$pdf->SetTitle("AEO ORIGIN");
	$pdf->SetFont('dejavusans', '', 14, '', true);
	
	$tblbuyer_invoice_detail = ($isBuyerPayment==1? "tblbuyer_invoice_payment_detail":"tblbuyer_invoice_detail");
	$tblbuyer_invoice        = ($isBuyerPayment==1? "tblbuyer_invoice_payment":"tblbuyer_invoice");
	$tblbuyer_invoice_hts    = ($isBuyerPayment==1? "tblbuyer_invoice_payment_hts":"tblbuyer_invoice_hts");
	
	$sqldd = "SELECT bid.shipping_marking, sum(bid.qty) as qty, bid.fob_price,
		(SELECT count(sgc.group_number) FROM tblship_group_color sgc 
         WHERE sgc.shipmentpriceID = bid.shipmentpriceID AND sgc.group_number = bid.group_number AND sgc.statusID=1) as count_sgc
			FROM $tblbuyer_invoice bip 
			INNER JOIN $tblbuyer_invoice_detail bid ON bid.invID = bip.ID
			INNER JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
			WHERE bid.invID='$invID' AND bid.del=0 AND bid.group_number>0";
	$stmtdd = $conn->prepare($sqldd);
	$stmtdd->execute();
	$rowdd = $stmtdd->fetch(PDO::FETCH_ASSOC);
	$qty = $rowdd["qty"];
	$count_sgc = $rowdd["count_sgc"];
	$unit      = ($count_sgc==1? "PCS":"SETS");
	
	$pdf->AddPage('P', 'A4');
	
	$html ='<!DOCTYPE html>
			<html lang="en">
			<head>';
	$html .= '<meta charset="UTF-8">
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
				
			';
	$html .= '<style>
			body {
				font-family: droidsansfallback, sans-serif;
			}
			.font_times{
			 font-family:"Times New Roman", Times, serif;
			}
			.font_xs{
				font-size:11px;
			}
			.font_s{
				font-size:12px;
			}
			.font_m{
				font-size:13px;
			}
			td.all_border{
				border: 1px solid #000;
			}
			td.top_border{
				border-top: 1px solid #000;
			}
			td.left_border{
				border-left: 1px solid #000;
			}
			td.right_border{
				border-right: 1px solid #000;
			}
			td.bottom_border{
				border-bottom: 1px solid #000;
			}';
	$html .= '</style>';
	$html .= '</head>';
	$html .= '<body>';
	$html .= '<table border="0" class="" cellspacing="3">
			<tr>
				<th class=" font_m" align="center">
					<b>'.$letterhead_name.'</b>
				</th>
			</tr>

			<tr>
				<td class=" font_m" align="center">
					'.$letterhead_address.'
				</td>
			</tr>

			<tr>
				<td class=" font_m" align="center">
					TEL: '.$letterhead_tel.' &nbsp;&nbsp;&nbsp; FAX: '.$letterhead_fax.'
					<br>
				</td>
			</tr>
			</table>';
	$html .= '<table border="0" class="" >
			<tr>
				<th class=" font_m" align="center">
					<u>CERTIFICATE OF ORIGIN</u>
					<br>
				</th>
			</tr>
			</table>';
	$date = date_create("$invoice_date");
	$inv_date = date_format($date,"d-M-Y");
	$html .= '<table border="0" class="" >
			<tr>
				<td class=" font_m" width="15%">INVOICE NO:</td>
				<td class=" font_m">'.$byr_invoice_no.'</td>
				</tr>
			<tr>
				<td class=" font_m">INVOICE DATE:</td>
				<td class=" font_m">'.$inv_date.'</td>
				</tr>
			</table>';
	
	$html .= '&nbsp;<br/><table border="0" class="" >
			<tr>
				<td class=" font_m" width="15%">TOTAL QTY:</td>
				<td class=" font_m">'.$qty.' '.$unit.'</td>
				</tr>
			</table>';
			
	$html .= '&nbsp;<br/><table border="0" class="" >
			<tr>
				<td class=" font_m" width="15%">DESCRIPTION:</td>
				<td class=" font_m">';
			
			$html .= '<table>';
			$sqldd = "SELECT sp.GTN_buyerpo as buyerpo, bid.shipping_marking,
					(SELECT CONCAT(bih.ht_code,'**',g.styleNo) 
					 FROM $tblbuyer_invoice_hts bih 
					 INNER JOIN tblgarment g ON g.garmentID = bih.garmentID
					 WHERE bih.shipmentpriceID = bid.shipmentpriceID limit 1) as info
							FROM $tblbuyer_invoice bip 
							INNER JOIN $tblbuyer_invoice_detail bid ON bid.invID = bip.ID
							INNER JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
							WHERE bid.invID='$invID' AND bid.del=0 AND bid.group_number>0
							group by bid.shipmentpriceID";
			$stmtdd = $conn->prepare($sqldd);
			$stmtdd->execute();
			while($rowdd = $stmtdd->fetch(PDO::FETCH_ASSOC)){
				extract($rowdd);
				
				list($ht_code, $style) = explode("**", $info);
				$html .= '<tr>
							<td colspan="2">'.$shipping_marking.'</td></tr>';
				$html .= '<tr>
							<td>PO Number</td>
							<td>Style No.</td>
							</tr>';
				$html .= '<tr>
							<td>'.$buyerpo.'</td>
							<td>'.$style.'</td>
							</tr>';
				$html .= '<tr>
							<td colspan="2">HTS: '.$ht_code.'</td></tr>';
				
			}
			$html .= '</table>';
				
				
			$html .= '</td>
				</tr>
			</table>';
			
	$html .= '<br/>';
	$html .= '<br/>';
	$html .= '<hr/>';
	$html .= '<p align="center" class="font_m">This is Certify that the above mention goods were made by CAMBODIA Origin.</p>';
	
	
	$html .= '</body>';
	$html .= '</html>';
	
?>