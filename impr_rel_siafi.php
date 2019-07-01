<?php
include ('./fpdf/fpdf.php');
$pdf = new FPDF('L','cm','A4');

$pdf -> SetMargins(1, 1, 1);
$pdf->AddPage();

$pdf->SetFont('Arial', '', 8);
$pdf->Image("./imagens/brasao.png", 13.8,1,2);
$pdf->SetXY(1,3);
$pdf->Cell(0, 0.3,'Ministério da Defesa', 0, 1, 'C');
$pdf->Cell(0, 0.3,'Exército Brasileiro', 0, 1, 'C');
$pdf->Cell(0, 0.3,$_GET['cma'], 0, 1, 'C');
$pdf->SetFont('Arial', 'U', 8);
$pdf->Cell(0, 0.3,'Sistema de Controle de Combustíveis do(a) '.$_GET['oc'], 0, 1, 'C');
$pdf->Cell(0, 0.5,'', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(0, 0.5,'Relatório para Evolução Patrimonial - SIAFI e SisCoFis', 0, 1, 'C');
$pdf->Cell(0, 0.5,'', 0, 1, 'C');

$pdf->SetFont('Arial', 'BI', 10);
$pdf->Cell(5.3, 0.5, 'Posto de Abastecimento do(a): ',0,0,'L');

$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 0.5, $_GET['sigla'],0,1,'L');
$pdf->SetFont('Arial', 'BI', 10);
$pdf->Cell(1.8, 0.5,'Período de: ', 0, 0, 'L');
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 0.5,substr($_GET['dt_ini'],8,2).'/'.substr($_GET['dt_ini'],5,2).'/'.substr($_GET['dt_ini'],0,4).' até '.substr($_GET['dt_fim'],8,2).'/'.substr($_GET['dt_fim'],5,2).'/'.substr($_GET['dt_fim'],0,4), 0, 1, 'L');

/*Cabeçalho das Colunas*/
$pdf->SetFont('Arial', 'BI', 10);
$pdf->Cell(5, 0.5, 'OM Abastecedora', 1, 0, 'C');
$pdf->Cell(5, 0.5, 'OM Atendida', 1, 0, 'C');
$pdf->Cell(0, 0.5, 'Eveolução que deverá ser executada', 1, 1, 'C');

/*Conexãoo ao Banco de Dados*/
include('conexao.php');

$query = "SELECT r.nr_nota_fiscal, r.dt_rec, r.reservatorio_codigo, c.nome, e.om_codom, a.cota_om, sum(h.qtd_abastecida), sum(h.qtd_abastecida)*r.valor 
						FROM combustivel c, recebimento r, estoque e, abastecimento a, historico_abastecimento h
						WHERE c.codigo=r.combustivel_codigo
							and r.nr_nota_fiscal=e.nr_nota_fiscal
							and r.combustivel_codigo=e.combustivel_codigo
							and e.nr_repasse=h.nr_repasse
							and e.om_codom=h.om_codom
							and a.dt_abastecimento=h.dt_abastecimento
							and a.viatura_eb=h.viatura_eb
							and h.dt_abastecimento between  '".$_GET['dt_ini']." 00:00' and '".$_GET['dt_fim']." 23:59' 
							and a.reservatorio_codigo in(select codigo from reservatorio where om_codom in ('".$_GET['codom']."')) ";	

	if(($_GET['cota']!='Todas') and ($_GET['cota']!='')) {
		$query=$query."AND a.cota_om='".$_GET['cota']."' ";
	}
	$query=$query."group by r.nr_nota_fiscal, r.dt_rec, r.reservatorio_codigo, e.om_codom order by 1";
							

$result = mysql_query($query,$conexao);

$pdf->SetFont('Arial', '', 8);

/*EXIBE OS REGISTROS*/

while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	$ano=substr($row[1],0,4);
	$mes=substr($row[1],5,2);
	$dia=substr($row[1],8,2);
	$dt_nota = $dia."/".$mes."/".$ano;
	/*Seleciona a sigla da OM do reservatorio*/
	$busca1="select o.sigla from om o, reservatorio r where o.codom=r.om_codom and r.codigo='".$row[2]."'";
	$result1 = mysql_query($busca1,$conexao);
	$local = mysql_fetch_row($result1);
		
	/*Seeleciona a OM que saiu o combustivel*/
	$busca2="select sigla from om where om.codom='".$row[5]."'";
	$result2 = mysql_query($busca2,$conexao);
	$abast = mysql_fetch_row($result2);
	
	$pdf->Cell(5, 1,$local[0], 1, 0, 'C');
	$pdf->Cell(5, 1,$abast[0], 1, 0, 'C');
	if($local[0]==$abast[0]) {
		$texto= "Exclusão patrimonial no valor de R$ ".number_format($row[7], 2, ',', '.')." referente ao abstecimento de $row[6] litros de $row[3] recebidos com a Nota Fiscal nº $row[0] de $dt_nota.";	
	} else {
		$texto= "Transferência patrimonial no valor de R$ ".number_format($row[7], 2, ',', '.')." do(a) $local[0] para o(a) $abast[0], referente ao abstecimento de $row[6] litros de $row[3] recebidos com a Nota Fiscal nº $row[0] de $dt_nota.";
	}
	$pdf->MultiCell(0, 0.5,$texto, 1, 'J');
}

$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 0.4, 'Data e Hora de Emissão: '.date('d/m/Y H:i').' hs.',0,1,'L');

$pdf->Output();
?>




