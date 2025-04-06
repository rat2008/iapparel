<?php
include("mpdf/mpdf.php");
include("lock.php");


$pdf = new \mPDF('C','','6'); 

$pdf->SetCreator("Apparel Ezi");

$pdf->SetAutoPageBreak(TRUE);
// $pdf->SetFont('arial', '', 10, '', false); //heavy
// $pdf->SetFont('dejavusans', '', 14, '', true);
$pdf->SetFont('droidsansfallback', '', 8);
$pdf->autoLangToFont = true;
$pdf->autoScriptToLang = true;

// add a page
$pdf->AddPage('P', '', '', '', '', 8, 8, 5, 5); 

$pdf->SetTitle('Sample MPDF');

$html = "TESTING MPDF";

$pdf->writeHTML($html);
// $pdf->Write(2, $txt_tnc, '', 0, '', false, 0, false, false, 0);

$pdf->autoScriptToLang = true;
$pdf->autoLangToFont = true;

$pdf->Output('Sample.pdf','I');

?>