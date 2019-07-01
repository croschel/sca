<?php
include ('./fpdf/fpdf.php');
$pdf = new FPDF('L','cm','A4');

$pdf -> SetMargins(1, 1, 1);
$pdf->AddPage();

$pdf->SetFont('Arial', '', 8);
$pdf->Image("./imagens/brasao.png", 13.8,2,2);
$pdf->SetXY(1,4);
$pdf->Cell(0, 0.3,'Ministério da Defesa', 0, 1, 'C');
$pdf->Cell(0, 0.3,'Exército Brasileiro', 0, 1, 'C');
$pdf->Cell(0, 0.3,$_GET['cma'], 0, 1, 'C');
$pdf->Cell(0, 0.3,$_GET['oc'], 0, 1, 'C');
$pdf->Cell(0, 0.3,$_GET['oc'], 0, 1, 'C');
$pdf->Cell(0, 0.5,'', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 1,'Sistema de Controle de Combustíveis', 0, 1, 'C');

$pdf->SetFont('Arial', 'BI', 10);
$pdf->Cell(2.2, 0.5, 'Posto do(a): ',0,0,'L');
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 0.5, $_GET['sigla'],0,1,'L');
$pdf->SetFont('Arial', 'BI', 10);
$pdf->Cell(1.9, 0.5,'Perído de: ', 0, 0, 'L');
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 0.5,substr($_GET['dt_ini'],8,2).'/'.substr($_GET['dt_ini'],5,2).'/'.substr($_GET['dt_ini'],0,4).' até '.substr($_GET['dt_fim'],8,2).'/'.substr($_GET['dt_fim'],5,2).'/'.substr($_GET['dt_fim'],0,4), 0, 1, 'L');

/*Cabeçalho das Colunas*/
$pdf->Cell(5, 0.5, 'OM', 1, 0, 'C');
$pdf->Cell(2, 0.5, 'EB Viatura', 1, 0,'C');
$pdf->Cell(1.5, 0.5, 'Data', 1, 0, 'C');
$pdf->Cell(1.5, 0.5, 'Comb', 1, 0, 'C');
$pdf->Cell(5, 0.5, 'Local', 1, 0, 'C');
$pdf->Cell(1, 0.5, 'Qtd', 1, 0, 'C');
$pdf->Cell(5, 0.5, 'Cota', 1, 0, 'C');
$pdf->Cell(5, 0.5, 'Missão', 1, 0, 'C');
$pdf->Cell(0, 0.5, 'Código', 1, 1, 'C');

/*Conexãoo ao Banco de Dados*/
include('conexao.php');

$query1 = "SELECT o.sigla, a.viatura_eb, a.dt_abastecimento, c.nome, a.disponibilidade_reservatorio_codigo, a.qtd_autorizada, a.missao, a.disponibilidade_om_codom, a.cod_seguranca
				FROM abastecimento a, om o, combustivel c, viatura v
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND v.eb = a.viatura_eb
				AND a.dt_abastecimento between '".$_GET['dt_ini']." 00:00' and '".$_GET['dt_fim']." 23:59'
				AND (o.codom='".$_GET['codom']."' or a.disponibilidade_reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_GET['codom']."'))
				AND a.qtd_abastecida is null 
				union
				SELECT o.sigla, concat(a.viatura_eb,' - Cisterna'), a.dt_autorizacao, c.nome, a.disponibilidade_reservatorio_codigo, a.qtd_autorizada, a.missao, a.disponibilidade_om_codom, a.cod_seguranca 
				FROM combustivel c, reservatorio r, om o, disponibilidade d, cisterna a
				WHERE c.codigo = r.combustivel_codigo
				and r.codigo=d.reservatorio_codigo
				and o.codom=d.om_codom
				and d.om_codom=a.disponibilidade_om_codom
				and d.reservatorio_codigo=a.disponibilidade_reservatorio_codigo
				AND a.dt_autorizacao between '".$_GET['dt_ini']." 00:00' and '".$_GET['dt_fim']." 23:59'
				and (o.codom = '".$_GET['codom']."' or a.disponibilidade_reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_GET['codom']."'))
				and a.qtd_carregada is null
				order by '".$_GET['ordem']."'";
							

$result2 = mysql_query($query1);

$pdf->SetFont('Arial', 'BI', 8);

/*EXIBE OS REGISTROS*/

while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
	/*Seeleciona a OM que saira o combustivel*/
	$busca="select sigla from om where codom='$row2[7]'";
	$resultado = mysql_query($busca,$conexao);
	$cota = mysql_fetch_row($resultado);
	/*Seeleciona a OM do reservatorio*/
	$busca="select o.sigla from om o, reservatorio r where o.codom=r.om_codom and r.codigo='$row2[4]'";
	$resultado = mysql_query($busca,$conexao);
	$local = mysql_fetch_row($resultado);
	$pdf->Cell(5, 0.5,$row2[0], 1, 0, 'C');
	$pdf->Cell(2, 0.5,$row2[1], 1, 0, 'C');
	$pdf->Cell(1.5, 0.5,substr($row2[2],8,2).'/'.substr($row2[2],5,2).'/'.substr($row2[2],0,4), 1, 0, 'C');
	$pdf->Cell(1.5, 0.5,$row2[3], 1, 0, 'C');
	$pdf->Cell(5, 0.5,$local[0], 1, 0, 'C');
	$pdf->Cell(1, 0.5,$row2[5], 1, 0, 'C');
	$pdf->Cell(5, 0.5,$cota[0], 1, 0, 'C');
	$pdf->Cell(5, 0.5,$row2[6], 1, 0, 'C');
	$pdf->Cell(0, 0.5,$row2[8], 1, 1, 'C');
}
$pdf->SetFont('Arial', 'BI', 8);



/*
$somat=array();
$pdf->Cell(4.3, 0.6, 'Somatório Total:', 1, 0, 'R');
$pdf->Cell(0, 0.6, $soma.' Km', 1, 1, 'L');
$soma = 0;

$pdf->SetFont('Arial', 'BI', 8);

$pdf->Cell(0, 0.5, "Total: ".$i." Viaturas", 0, 1, 'L');

$pdf->SetFont('Arial', 'I', 6);
$pdf->Cell(0, 0.3, 'Data e Hora de Emissão: '.date('d/m/Y H:i').' hs.',0,1,'C');
*/
$pdf->Output();
?>




