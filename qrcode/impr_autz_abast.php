<?php
include ('../fpdf/fpdf.php');
$pdf = new FPDF('P','cm','A4');

$pdf -> SetMargins(0.8, 0.8, 0.8);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(8.5, 1,strtoupper($_GET['tp_abastecimento']), 1, 0, 'C');
$pdf->Cell(2, 1,'|', 0, 0, 'C');
$pdf->Cell(0, 1,strtoupper($_GET['tp_abastecimento']), 1, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(8.5, 0.5,'Cota de Combustível do(a): '.$_GET['sigla'], 1, 0, 'L');
$pdf->Cell(2, 0.5,'', 0, 0, 'C');
$pdf->Cell(0, 0.5,'Cota de Combustível do(a): '.$_GET['sigla'], 1, 1, 'L');
$pdf->Cell(8.5, 0.5,'Autorizado por: '.$_GET['usuario'], 1, 0, 'L');
$pdf->Cell(2, 0.5,'', 0, 0, 'C');
$pdf->Cell(0, 0.5,'Autorizado por: '.$_GET['usuario'], 1, 1, 'L');
$pdf->Cell(8.5, 0.5,'Data Autorização: '.$_GET['data'], 1, 0, 'L');
$pdf->Cell(2, 0.5,'', 0, 0, 'C');
$pdf->Cell(0, 0.5,'Data Autorização: '.$_GET['data'], 1, 1, 'L');
$pdf->Cell(8.5, 0.5,'OM da Viatura: '.$_GET['om'], 1, 0, 'L');
$pdf->Cell(2, 0.5,'', 0, 0, 'C');
$pdf->Cell(0, 0.5,'OM da Viatura: '.$_GET['om'], 1, 1, 'L');
$pdf->Cell(8.5, 0.5,'EB da Viatura: '.$_GET['eb'], 1, 0, 'L');
$pdf->Cell(2, 0.5,'', 0, 0, 'C');
$pdf->Cell(0, 0.5,'EB da Viatura: '.$_GET['eb'], 1, 1, 'L');
if($_GET['tp_abastecimento']=='Carregamento de Cisterna') {
	$pdf->Cell(8.5, 0.5,'Responsável: '.$_GET['motorista'], 1, 0, 'L');
	$pdf->Cell(2, 0.5,'', 0, 0, 'C');
	$pdf->Cell(0, 0.5,'Responsável: '.$_GET['motorista'], 1, 1, 'L');
} else {
	$pdf->Cell(8.5, 0.5,'Odômetro Atual: '.$_GET['odometro'], 1, 0, 'L');
	$pdf->Cell(2, 0.5,'', 0, 0, 'C');
	$pdf->Cell(0, 0.5,'Odômetro Atual: '.$_GET['odometro'], 1, 1, 'L');
	$pdf->Cell(8.5, 0.5,'Motorista: '.$_GET['motorista'], 1, 0, 'L');
	$pdf->Cell(2, 0.5,'', 0, 0, 'C');
	$pdf->Cell(0, 0.5,'Motorista: '.$_GET['motorista'], 1, 1, 'L');
}
$pdf->Cell(8.5, 0.5,'Rerservatório: Nº '.$_GET['posto'], 1, 0, 'L');
$pdf->Cell(2, 0.5,'', 0, 0, 'C');
$pdf->Cell(0, 0.5,'Rerservatório: Nº '.$_GET['posto'], 1, 1, 'L');
$pdf->Cell(8.5, 0.5,'Quantidade Autorizada: '.$_GET['qtd'].' Lts', 1, 0, 'L');
$pdf->Cell(2, 0.5,'', 0, 0, 'C');
$pdf->Cell(0, 0.5,'Quantidade Autorizada: '.$_GET['qtd'].' Lts', 1, 1, 'L');
$pdf->Cell(8.5, 0.5,'Código de Segurança '.$_GET['cod'], 1, 0, 'L');
$pdf->Cell(2, 0.5,'', 0, 0, 'C');
$pdf->Cell(0, 0.5,'Código de Segurança: '.$_GET['cod'], 1, 1, 'L');

//set it to writable location, a place for temp generated PNG files
$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
   
//html PNG location prefix
$PNG_WEB_DIR = 'temp/';

include "qrlib.php";  
   
if (!file_exists($PNG_TEMP_DIR))
 	mkdir($PNG_TEMP_DIR);

$filename = $PNG_TEMP_DIR.'test.png';

$errorCorrectionLevel = 'L';

//gera o codigo de barra do abastecimento	
$fig = "eb=".$_GET['eb']."&dt=".$_GET['dt']."&res=".$_GET['res']."&cota=".$_GET['cota']."&tpcota=".$_GET['tp_cota']."&qtd_autz=".$_GET['qtd'];

QRcode::png($fig , $filename, $errorCorrectionLevel, 4, 2);    

if($_GET['tp_abastecimento']=='Carregamento de Cisterna') {
	$pdf->Image($PNG_WEB_DIR.basename($filename), 3.5,6.3,3);
	$pdf->Image($PNG_WEB_DIR.basename($filename), 14.2,6.3,3);
} else {
	$pdf->Image($PNG_WEB_DIR.basename($filename), 3.5,6.8,3);
	$pdf->Image($PNG_WEB_DIR.basename($filename), 14.2,6.8,3);
}

$pdf->Cell(8.5, 3,'', 1, 0, 'L');
$pdf->Cell(2, 3,'', 0, 0, 'C');
$pdf->Cell(0, 3,' ', 1, 1, 'L');

$pdf->Cell(8.5, 0.8,'Odômetro Atual: ____________________ Km', 1, 0, 'L');
$pdf->Cell(2, 0.8,'', 0, 0, 'C');
$pdf->Cell(0, 0.8,'Odômetro Atual: ____________________ Km', 1, 1, 'L');

$pdf->Cell(8.5, 0.8,'Quantidade Abastecida: _________________ Lts', 1, 0, 'L');
$pdf->Cell(2, 0.8,'', 0, 0, 'C');
$pdf->Cell(0, 0.8,'Quantidade Abastecida: _________________ Lts', 1, 1, 'L');
$pdf->Cell(8.5, 0.8,'Rúbrica do Motorista: ___________________________', 1, 0, 'L');
$pdf->Cell(2, 0.8,'', 0, 0, 'C');
$pdf->Cell(0, 0.8,'Rúbrica do Abastecedor: _______________________', 1, 1, 'L');
$pdf->MultiCell(8.5, 0.4,'ATENÇÃO: Esta via deverá ser preenchida com a quantidade abastecida, rubricada pelo motorista e entregue ao abastecedor para ser arquivada.', 1, 'J');
$pdf->SetXY(9.3,12.2);
$pdf->MultiCell(2, 1.2,'|', 0, 'C');
if($_GET['tp_abastecimento']=='Carregamento de Cisterna') {
	$pdf->SetXY(11.3,11.7);
} else {
	$pdf->SetXY(11.3,12.2);
}	
$pdf->MultiCell(0, 0.4,'ATENÇÃO: Esta via deverá ser preenchida com a quantidade abastecida, rubricada pelo abastecedor e entregue ao motorista para ser devolvida ao Fiscal da OM.', 1, 'J');

$pdf->Output();

?>


