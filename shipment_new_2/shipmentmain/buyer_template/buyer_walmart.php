<?php 
$pdf->SetFont('cid0cs', '', 6, '', false);
// ============== pdf header ===================//
$html .= <<<EOD
<table border="0" style="border:1px solid black;">
	<tr>
		<th class="bold-text center-align">
			<h1>$ownership</h1>
		</th>
	</tr>

	<tr>
		<td class="center-align">
			$owneraddress
		</td>
	</tr>

	<tr>
		<td class="center-align">
			TEL : $ownertel &nbsp; & &nbsp; FAX : $ownerfax
			<br/>
		</td>
	</tr>

</table>

<br>
<br>

EOD;

$html .= <<<EOD
		<table border="1" class="table-bordered" cellpadding="5">
			<tr>
				<td class="center-align" colspan="2" style="font-size: 12px;"><b>Commercial Invoice</b></td>
			</tr>
			<tr>
				<td style="width:50%;">
					<table>
						<tr>
							<td style="width:20%"><b>TO: </b></td>
							<td>$conName</td>
						</tr>
						<tr>
							<td style="width:20%"><b>ADDRESS: </b></td>
							<td>$conAddress</td>
						</tr>
					</table>
				</td>
				<td style="width:50%;">
					<table>
						<tr>
							<td style="width:20%"><b>INVOICE NO.: </b></td>
							<td>$invoice_no</td>
						</tr>
						<tr>
							<td style="width:20%"><b>INVOICE DATE: </b></td>
							<td>$invoice_date</td>
						</tr>
						<tr>
							<td style="width:20%"><b>PAYMENT TERMS: </b></td>
							<td style="width:80%">$paymentterm</td>
						</tr>
						<tr>
							<td style="width:20%"><b>LC #: </b></td>
							<td>$lc_number</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<table border="1" cellpadding="5">
			<tr style="text-align:center;">
				<td rowspan="4"><b>PO NUMBER</b></td>
				<td rowspan="4"><b>STYLE NO</b></td>
				<td rowspan="4"><b>LABEL CODE</b></td>
				<td rowspan="4"><b>COLOR CODE</b></td>
				<td rowspan="4"><b>COLOR NAME</b></td>
				<td rowspan="4"><b>CUSTOMER NAME</b></td>
				<td rowspan="4"><b>BRAND</b></td>
				<td rowspan="4"><b>DESCRIPTION</b></td>
				<td rowspan="4"><b>COMPOSITION / MATERIAL</b></td>
				<td rowspan="4"><b>HTS #</b></td>
				<td rowspan="2" colspan="2"><b>QUANTITY</b></td>
				<td><b>FOB</b></td>
				<td><b></b></td>
			</tr>
			<tr>
				<td><b>UNIT PRICE</b></td>
				<td><b>AMOUNT</b></td>
			</tr>
			<tr>
				<td><b>CTNS</b></td>
				<td><b>PCS</b></td>
				<td><b>US$/PC</b></td>
				<td><b>US$</b></td>
			</tr>
			<tr>
				<td><b></b></td>
				<td><b></b></td>
				<td><b></b></td>
				<td><b></b></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="10" style="text-align:right;"><b>TOTAL:</b></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="13" style="text-align:right;"><b>INVOICE TOTAL:</b></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="14"></td>
			</tr>
			<tr>
				<td colspan="2">SAY TOTAL U.S. DOLLAR</td>
				<td colspan="12"></td>
			</tr>
			<tr>
				<td colspan="14">
					<table>
						<tr>
							<td><b>BENEFICIARY'S NAME:</b></td>
							<td style="width:30%;"></td>
							<td><b>SHIPMENT FROM:</b></td>
							<td></td>
							<td><b>TO:</b></td>
							<td></td>
						</tr>
						<tr>
							<td><b>ADDRESS:</b></td>
							<td></td>
							<td><b>ETD:</b></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><b>BANK NAME:</b></td>
							<td></td>
							<td><b>ETA:</b></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><b>BANK ADDRESS:</b></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><b>COUNTRY:</b></td>
							<td></td>
							<td><b>VESSEL/VOYAGE:</b></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><b>BENEFICIARIES ACCOUNT:</b></td>
							<td></td>
							<td><b>CONTAINER NO:</b></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><b>SWIFT NO.:</b></td>
							<td></td>
							<td><b>FORWARDER BOOKING CONFIRM NO.:</b></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
EOD;





?>