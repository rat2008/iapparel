<?php 
	$pdf->SetTitle("AEO CONTRACT");
	$pdf->SetFont('dejavusans', '', 14, '', true);
	
	$tblbuyer_invoice_detail = ($isBuyerPayment==1? "tblbuyer_invoice_payment_detail":"tblbuyer_invoice_detail");
	$tblbuyer_invoice        = ($isBuyerPayment==1? "tblbuyer_invoice_payment":"tblbuyer_invoice");
	
	$sql = "SELECT sp.GTN_buyerpo as buyer_po, sp.ID, bip.invoice_date, bip.shippeddate
			FROM $tblbuyer_invoice bip 
			INNER JOIN $tblbuyer_invoice_detail bid ON bid.invID = bip.ID
			INNER JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
			WHERE bid.invID='$invID' AND bid.del=0 AND bid.group_number>0 
			group by bid.shipmentpriceID";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	$spID = $row["ID"];
	$buyer_po = $row["buyer_po"];
	$invoice_date = $row["invoice_date"];
	
	$date = date_create("$invoice_date");
	$inv_date = date_format($date,"d-M-y");

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
	
	$html .= '<table border="0" class="" >
	<tr>
		<th class="center-align" align="center">
			<b class="font_times">CONTRACT</b><br/>合同
		</th>
	</tr>
	<tr>
		<td class="center-align" width="85%" align="right" class="font_xs" >合同号 <font class="font_times ">Contract No</font>: </td>
		<td class="center-align font_xs"> '.$buyer_po.'</td>
	</tr>
	<tr>
		<td class="center-align" align="right" class="font_xs" >签字日期 <font class="font_times ">Signing Date</font>: </td>
		<td class="center-align font_xs"> '.$inv_date.'</td>
	</tr>
	</table>';
	
	$html .= '<table border="0" cellpadding="0" cellspacing="0">';
	$html .= '<tr>';
	$html .= '<td class="center-align" class="font_xs" width="12%" align="left"> <font class="font_times ">The Seller:</font> </td>';
	$html .= '<td class="center-align" class="font_xs" colspan="2" ><font class="font_times ">'.$ownership.'</font> </td>';
	// $html .= '<td class="center-align" class="font_xs" > </td>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<td class="center-align" class="font_xs" align="left"> <font class="font_times ">Address:</font> </td>';
	$html .= '<td class="center-align" class="font_xs" width="38%"> <font class="font_times ">'.$owneraddress.'</font><br/>
														<font class="font_times ">Tel: '.$ownertel.'</font> &nbsp; &nbsp; 
														<font class="font_times ">&nbsp; &nbsp; Fax: '.$ownerfax.'</font> 
														</td>';
	$html .= '<td class="center-align" class="font_xs" width="50%"> </td>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<td colspan="3" class="font_xs">&nbsp;</td>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<td class="center-align" class="font_xs" width="12%" align="left"> <font class="font_times ">The Buyer:</font> </td>';
	$html .= '<td class="center-align" class="font_xs" colspan="2" ><font class="font_times ">'.$conName.'</font> </td>';
	// $html .= '<td class="center-align" class="font_xs" > </td>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<td class="center-align" class="font_xs" align="left"> <font class="font_times ">Address:</font> </td>';
	$html .= '<td class="center-align" class="font_xs"><font class="font_times ">'.$conAddrOnly.'</font><br/>
														<font class="font_times ">Tel: '.$contel.'</font> &nbsp; &nbsp; 
														<font class="font_times ">&nbsp; &nbsp; Fax: '.$confax.'</font> 
														</td>';
	$html .= '<td class="center-align" class="font_xs"> </td>';
	$html .= '</tr>';
	$html .= '</table>';
	$html .= '&nbsp;<br/>';
	$html .= '<table>
				<tr>
					<td class="font_xs"><font class="font_times">1. This Contract is made by and between the Buyer and the Seller,. And whereby the Buyer agree to buy and the Seller agree to sell the under-mentioned commodity according to the terms and conditions stipulated below. (According to the practical price of invoice)</font><br/>
					本合同由买方和卖方签订，同时根据下面规定的条款，买方同意购买并且卖方同意销售如下商品（以发票的实际金额为准）
					</td>
					</tr>
					</table>';
	$html .= '&nbsp;<br/>';
	$html .= '<table cellpadding="2">';
	$html .= '<tr>';
	$html .= '<td class="font_xs font_times all_border" align="center" width="5%">ITEM</td>';
	$html .= '<td class="font_xs font_times all_border" align="center" width="35%">Commodity/Specification</td>';
	$html .= '<td class="font_xs font_times all_border" align="center" width="15%">Unit of Measure</td>';
	$html .= '<td class="font_xs font_times all_border" align="center" width="15%">Quantity of Unit</td>';
	$html .= '<td class="font_xs font_times all_border" align="center" width="15%">Unit Price</td>';
	$html .= '<td class="font_xs font_times all_border" align="center" width="15%">Amount</td>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<td class="font_xs all_border" align="center">序号</td>';
	$html .= '<td class="font_xs all_border" align="center">商 品/规 格</td>';
	$html .= '<td class="font_xs all_border" align="center">单 位</td>';
	$html .= '<td class="font_xs all_border" align="center">数 量</td>';
	$html .= '<td class="font_xs all_border" align="center">单 价</td>';
	$html .= '<td class="font_xs all_border" align="center">总金额</td>';
	$html .= '</tr>';
	
	$sqldd = "SELECT bid.shipping_marking, sum(bid.qty) as qty, bid.fob_price,
		(SELECT count(sgc.group_number) FROM tblship_group_color sgc 
         WHERE sgc.shipmentpriceID = bid.shipmentpriceID AND sgc.group_number = bid.group_number AND sgc.statusID=1) as count_sgc
			FROM $tblbuyer_invoice bip 
			INNER JOIN $tblbuyer_invoice_detail bid ON bid.invID = bip.ID
			INNER JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
			WHERE bid.invID='$invID' AND bid.del=0 AND bid.group_number>0 AND bid.shipmentpriceID='$spID'
			group by bid.shipmentpriceID";
	$stmtdd = $conn->prepare($sqldd);
	$stmtdd->execute();
	$num = 0;
	$grand_amt = 0;
	while($rowdd = $stmtdd->fetch(PDO::FETCH_ASSOC)){
		$shipping_marking = $rowdd["shipping_marking"];
		$qty       = $rowdd["qty"];
		$fob_price = $rowdd["fob_price"];
		$count_sgc = $rowdd["count_sgc"];
		$unit      = ($count_sgc==1? "PCS":"SETS");
		$amt       = $qty * $fob_price;
		$num++;
		
		$grand_amt += $amt;
		$str_total   = number_format($amt, 2);
		
		$html .= '<tr>';
		$html .= '<td class="font_xs font_times all_border" align="center">'.$num.'</td>';
		$html .= '<td class="font_xs font_times all_border" align="left">'.$shipping_marking.'</td>';
		$html .= '<td class="font_xs font_times all_border" align="center">'.$unit.'</td>';
		$html .= '<td class="font_xs font_times all_border" align="center">'.$qty.' '.$unit.'</td>';
		$html .= '<td class="font_xs font_times all_border" align="center">'.$fob_price.'</td>';
		$html .= '<td class="font_xs font_times all_border" align="center">'.$str_total.'</td>';
		$html .= '</tr>';
	}
	
	$html .= '<tr>';
	$html .= '<td class="font_xs font_times all_border" align="center"></td>';
	$html .= '<td class="font_xs font_times all_border" align="left">&nbsp;</td>';
	$html .= '<td class="font_xs font_times all_border" align="center"></td>';
	$html .= '<td class="font_xs font_times all_border" align="center"></td>';
	$html .= '<td class="font_xs font_times all_border" align="center"></td>';
	$html .= '<td class="font_xs font_times all_border" align="center"></td>';
	$html .= '</tr>';
	
	$sqlless = "SELECT bid.total_amount
				FROM $tblbuyer_invoice bip 
				INNER JOIN $tblbuyer_invoice_detail bid ON bid.invID = bip.ID
				INNER JOIN tblshipmentprice sp ON sp.ID = bid.shipmentpriceID
				WHERE bid.invID='$invID' AND bid.del=0 AND bid.group_number=0 AND bid.total_amount<0 AND bid.shipmentpriceID='$spID'
				group by bid.shipmentpriceID";
	$stmtless = $conn->prepare($sqlless);
	$stmtless->execute();
	$rowless = $stmtless->fetch(PDO::FETCH_ASSOC);
		$less_amount = $rowless["total_amount"];
		
	$grand_total = $grand_amt + $less_amount;
	$str_total   = number_format($grand_total, 2);
	
	$html .= '<tr>';
	$html .= '<td class="font_xs font_times all_border" align="center"></td>';
	$html .= '<td class="font_xs font_times all_border" align="left">Total Cost of Merchandise Less Payment Discount</td>';
	$html .= '<td class="font_xs font_times all_border" align="center"></td>';
	$html .= '<td class="font_xs font_times all_border" align="center"></td>';
	$html .= '<td class="font_xs font_times all_border" align="center"></td>';
	$html .= '<td class="font_xs font_times all_border" align="center">'.$less_amount.'</td>';
	$html .= '</tr>';
	
	$html .= '<tr>';
	$html .= '<td class="font_xs font_times all_border" align="center"></td>';
	$html .= '<td class="font_xs font_times all_border" align="left"></td>';
	$html .= '<td class="font_xs font_times all_border" align="center"></td>';
	$html .= '<td class="font_xs font_times all_border" align="center"></td>';
	$html .= '<td class="font_xs  all_border" align="center">总价<br/><font class="font_times">Total Amount</font></td>';
	$html .= '<td class="font_xs font_times all_border" align="center">&nbsp;<br/>'.$str_total.'</td>';
	$html .= '</tr>';
	$html .= '</table>';
	
	$html .= '<table>';
	$html .= '<tr><td class="font_xs" width="100%">&nbsp;</td></tr>';
	$html .= '<tr>';
	$html .= '<td class="font_xs"><font class="font_times">2. COUNTRY OF ORIGIN AND MANUFACTURER:</font>
					<br/>原产地和制造商： <font class="font_times">**Country of origin and manufacturer name** </font></td>';
	$html .= '</tr>';
	$html .= '<tr><td class="font_xs">&nbsp;</td></tr>';
	$html .= '<tr>';
	$html .= '<td class="font_xs"><font class="font_times">3. PACKING:<br/>
To be packed under the buyer’s vendor manual requirement. The Seller shall be liable for any damage of the commodity and expenses incurred on account of improper packing and for any rust damage attributable to inadequate or improper protective measures taken by the sellers in regard to the packing.</font><br/>包装：根据买方供货商手册要求，由于采用不适当或不妥当的包装而引起的生锈、损坏，其责任应由卖方承担。</td>';
	$html .= '</tr>';
	$html .= '<tr><td class="font_xs">&nbsp;</td></tr>';
	$html .= '<tr><td class="font_xs"><font class="font_times">4. SHIPPING MARK:<br/>
The Seller shall mark on each package with faceless paint the package number, gross weight, net wet, measurement and the shipping mark.
</font><br/>唛头：卖方应在每件包装箱上用不褪色的油漆刷上箱号、毛重、净重、尺码等字样。</td></tr>';
	
	$date = date_create("$shippeddate");
	$ship_date = date_format($date,"d-M-y");
	$html .= '<tr><td class="font_xs">&nbsp;</td></tr>';
	$html .= '<tr><td class="font_xs"><font class="font_times">5. TIME OF TRANSPORTATION: '.$ship_date.'</font>
	<br/>装货时间： <font class="font_times">'.$ship_date.'</font></td></tr>';
	
	$html .= '<tr><td class="font_xs">&nbsp;</td></tr>';
	$html .= '<tr><td class="font_xs"><font class="font_times">6. PLACE OF TRANSPORTATION: '.$portLoading.'</font>
	<br/>起运地点： <font class="font_times">'.$portLoading.'</font></td></tr>';
	
	$html .= '<tr><td class="font_xs">&nbsp;</td></tr>';
	$html .= '<tr><td class="font_xs"><font class="font_times">7. PLACE OF DESTINATION: '.$buyerdest.'</font>
	<br/>目的地： <font class="font_times">'.$buyerdest.'</font></td></tr>';
	
	$html .= '<tr><td class="font_xs">&nbsp;</td></tr>';
	$html .= '<tr><td class="font_xs"><font class="font_times">8. PAYMENT:<br/>By　T/T and/or L/C. The payment shall be effected after receipt the contract goods and the document stipulated in Clause 10.   </font>
	<br/>支付：以电汇及／或信用證方式支付。付款将在收到合同货物后执行。</td></tr>';
	
	$html .= '<tr><td class="font_xs">&nbsp;</td></tr>';
	$html .= '<tr><td class="font_xs"><font class="font_times">9. DATE OF SHIPPING: '.$ship_date.'<br/>The date of Bill of Lading shall be regarded as the actual date of shipment. </font>
	<br/>装运日期：提单上的日期将被视为装运日期。</td></tr>';
	
	$html .= '<tr><td class="font_xs">&nbsp;</td></tr>';
	$html .= '<tr><td class="font_xs"><font class="font_times">10. FORCE MAJEURE:<br/>The seller shall not be held responsible for the delay in shipment or non-delivery of the goods due to Force Majeure , such as war , serious fire , flood , typhoon and earthquake , or other events agreed upon between both parties , which might occur during the process of manufacturing or in the course of loading or transit . The Seller shall advise the Buyer by cable/telex immediately of the occurrence mentioned above and within fourteen days thereafter, shall send by airmail to the Buyer for their acceptance a certificate of the accident issued by the Competent Government Authorities, where the accident occurs as evidence thereof. Under such circumstances the Seller, however, are still under the obligation to take all necessary measures to hasten the delivery of the goods .  </font>
	<br/>不可抗力：由于严重的火灾、水灾、台风、地震以及双方同意的其它不可抗力事故，致使卖方交货延迟或不能交货时，卖方可不负责任，但发生上述事故时，卖方应立即以电传或电报通知买方，并于事故发生后14天内将事故发生地主管当局出具的事故证明书用航空寄交买方,依不可抗力事件之轻重,部分或全部免除合同责任。</td></tr>';
	
	$html .= '<tr><td class="font_xs">&nbsp;</td></tr>';
	$html .= '<tr><td class="font_xs"><font class="font_times">11. ARBITRATION:<br/>All disputes in connection with this Contract or the execution there of shall be settled friendly through negotiations. In case no settlement can be reached, the case may then be submitted for arbitration to China International Economic and Trade Arbitration Commission in accordance with the Rules of Arbitration promulgated by the said Arbitration Commission. The Arbitration shall take place in Beijing and the decision of the Arbitration Commission shall be final and binding upon both parties; neither party shall seek recourse to a law court or other authorities to appeal for revision of the decision. Arbitration fee shall be borne by the losing party. This contract shall be subject to Laws of the People\'s Republic of China. </font>
	<br/>仲裁：一切因执行本合同或与本合同有关的争议，应由各方友好协商解决，如经协商不能解决时，应提交中国国际经济贸易仲裁委员会根据该会仲裁规则进行仲裁，仲裁地点在北京，仲裁裁决是终局的，对各方都有约束力，仲裁费用由败诉方承担。本合同适用中华人民共和国法律管辖。</td></tr>';
	
	$html .= '<tr><td class="font_xs">&nbsp;</td></tr>';
	$html .= '<tr><td class="font_xs"><font class="font_times">12. EFFECTIVENESS OF THE CONTRACT:<br/>This Contract shall come into force immediately after signature by representative of each party and upon approval by the relevant authority of each party. </font>
	<br/>合同的生效：本合同在各方盖章后立即生效。</td></tr>';
	
	$html .= '<tr><td class="font_xs">&nbsp;</td></tr>';
	$html .= '<tr><td class="font_xs"><font class="font_times">13. SPECIAL PROVISIONS:<br/>This contract is made out in English and Chinese, both version being equally authentic. In case of discrepancy between the English and Chinese version, the Chinese version shall prevail. The original Contracts are in three copies; each part keeps one of three original copies after signature. </font>
	<br/>本合同采用中文、英文书写，具有同等法律效力，中英文解释发生争议，以中文版本为准。合同正本三份，三方各持一份。</td></tr>';
	
	$html .= '</table>';
	$html .= '&nbsp;<br/>';
	
	$html .= '<table>';
	$html .= '<tr>
				<td width="20%" class="font_xs font_times">For The Buyer</td>
				<td width="60%"></td>
				<td width="20%" class="font_xs font_times" align="right">For The Seller</td>
				</tr>';
	$html .= '<tr>
				<td class="font_xs">&nbsp;</td>
				<td ><br/><br/><br/><br/></td>
				<td class="font_xs" align="right">&nbsp;</td>
				</tr>';
	$html .= '<tr>
				<td class="font_xs top_border font_times">Authorized Signature</td>
				<td ></td>
				<td class="font_xs top_border font_times" align="right">Authorized Signature</td>
				</tr>';
	$html .= '</table>';
	
	
	$html .= '</body>';
	$html .= '</html>';//4124.7 //4295
	
$pdf->writeHTML($html, true, 0, true, 0);
$html = '';

}
	
?>