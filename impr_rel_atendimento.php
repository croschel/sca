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

$pdf->Cell(0, 0.5,'Relatório Periódico de Abastecimentos', 0, 1, 'C');
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
$pdf->Cell(0, 0.01,'', 1, 1, 'C');
$pdf->Cell(5, 1.5, 'OM Abastecedora', 0, 0, 'C');
$pdf->Cell(5, 1.5, 'OM Atendida', 0, 0, 'C');
$pdf->Cell(7.8, 0.5, 'óleo Diesel', 0, 0, 'C');
$pdf->Cell(7.8, 0.5, 'Gasolina', 0, 0, 'C');
$pdf->Cell(0, 1, 'Total', 0, 0, 'C');
$pdf->SetXY(26.5,7.2);
$pdf->Cell(0, 1, '(R$)', 0, 0, 'C');
$pdf->SetXY(11,7.2);
$pdf->Cell(2.5, 1, 'Nota Fiscal', 0, 0, 'C');
$pdf->Cell(1.8, 0.5, 'Consumo', 0, 0, 'C');
$pdf->Cell(1.5, 0.5, 'Valor', 0, 0, 'C');
$pdf->Cell(2, 0.5, 'Subtotal', 0, 0, 'C');
$pdf->Cell(2.5, 1, 'Nota Fiscal', 0, 0, 'C');
$pdf->Cell(1.8, 0.5, 'Consumo', 0, 0, 'C');
$pdf->Cell(1.5, 0.5, 'Valor', 0, 0, 'C');
$pdf->Cell(2, 0.5, 'Subtotal', 0, 1, 'C');
$pdf->SetXY(13.5,7.7);
$pdf->Cell(1.8, 0.5, '(litros)', 0, 0, 'C');
$pdf->Cell(1.5, 0.5, 'Unitário', 0, 0, 'C');
$pdf->Cell(2, 0.5, '(R$)', 0, 0, 'C');
$pdf->Cell(2.5, 0.5, '', 0, 0, 'C');
$pdf->Cell(1.8, 0.5, '(litros)', 0, 0, 'C');
$pdf->Cell(1.5, 0.5, 'Unitário', 0, 0, 'C');
$pdf->Cell(2, 0.5, '(R$)', 0, 1, 'C');
$pdf->Cell(0, 0.01,'', 1, 1, 'C');
$pg=1;
$lin=8.2;

$pdf->Line(1, 6.7, 1, 18.6);
$pdf->Line(6, 6.7, 6, 18.6);
$pdf->Line(11, 6.7, 11, 18.6);

$pdf->Line(11, 7.2, 26.6, 7.2);
$pdf->Line(13.5, 7.2, 13.5, 18.6);
$pdf->Line(15.3, 7.2, 15.3, 18.6);
$pdf->Line(16.8, 7.2, 16.8, 18.6);
	
$pdf->Line(18.8, 6.7, 18.8, 18.6);
$pdf->Line(21.2, 7.2, 21.2, 18.6);
$pdf->Line(23.1, 7.2, 23.1, 18.6);
$pdf->Line(24.6, 7.2, 24.6, 18.6);

$pdf->Line(26.6, 6.7, 26.6, 18.6);
$pdf->Line(28.7, 6.7, 28.7, 18.6);
$pdf->Line(1, 18.6, 28.7, 18.6);

/*Conexãoo ao Banco de Dados*/
include('conexao.php');

$query = "SELECT distinct r.om_codom, a.cota_om 
						FROM abastecimento a, historico_abastecimento h, reservatorio r
						WHERE r.codigo=a.reservatorio_codigo
							and a.dt_abastecimento=h.dt_abastecimento
							and a.viatura_eb=h.viatura_eb
							and h.qtd_abastecida > 0
							and h.dt_abastecimento between  '".$_GET['dt_ini']." 00:00' and '".$_GET['dt_fim']." 23:59' 
							and a.reservatorio_codigo in(select codigo from reservatorio where om_codom in ('".$_GET['codom']."')) ";	

	if(($_GET['cota']!='Todas') and ($_GET['cota']!='')) {
		$query=$query."AND a.cota_om='".$_GET['cota']."' ";
	}
	$query=$query."order by 1,2 ";
							

$result = mysql_query($query,$conexao);

$pdf->SetFont('Arial', '', 8);

/*EXIBE OS REGISTROS*/

while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	/*Seleciona a sigla da OM do reservatorio*/
	$busca1="select o.sigla from om o, reservatorio r where o.codom='".$row[0]."'";
	$result1 = mysql_query($busca1,$conexao);
	$local = mysql_fetch_row($result1);
		
	/*Seeleciona a OM que saiu o combustivel*/
	$busca2="select sigla from om where om.codom='".$row[1]."'";
	$result2 = mysql_query($busca2,$conexao);
	$abast = mysql_fetch_row($result2);
	$pdf->SetXY(1,$lin);
	$pdf->Cell(5, 0.5,$local[0], 0, 0, 'C');
	$pdf->Cell(5, 0.5,$abast[0], 0, 0, 'C');
	$qryod = "SELECT r.nr_nota_fiscal, r.reservatorio_codigo, sum(h.qtd_abastecida), r.valor, sum(h.qtd_abastecida)*r.valor ,e.combustivel_codigo
						FROM recebimento r, estoque e, abastecimento a, historico_abastecimento h
						WHERE r.nr_nota_fiscal=e.nr_nota_fiscal
							and r.combustivel_codigo=e.combustivel_codigo
							and e.nr_repasse=h.nr_repasse
							and e.om_codom=h.om_codom
							and a.dt_abastecimento=h.dt_abastecimento
							and a.viatura_eb=h.viatura_eb
							and h.qtd_abastecida > 0
							and h.dt_abastecimento between  '".$_GET['dt_ini']." 00:00' and '".$_GET['dt_fim']." 23:59'  
							and e.om_codom = $row[1] 
							and e.combustivel_codigo = '2' 
							and r.reservatorio_codigo in( select codigo from reservatorio where om_codom='$row[0]')
						group by r.nr_nota_fiscal, r.reservatorio_codigo, e.om_codom ";

	$resod = mysql_query($qryod,$conexao);
	$i=1;
	if(mysql_num_rows($resod)>0) {
  		while ($od = mysql_fetch_array($resod, MYSQL_NUM)) {
			if($lin>18.5) {
				$pdf->AddPage();
				$pg++;
				$lin=2;
			}
			$pdf->SetXY(11,$lin);
			$pdf->Cell(2.4, 0.5,$od[0], 0, 0, 'C');
			$pdf->Cell(1.8, 0.5,number_format($od[2], 0, ',', '.').' ', 0, 0, 'R');
			$pdf->Cell(1.5, 0.5,number_format($od[3], 2, ',', '.').' ', 0, 0, 'R');
			$pdf->Cell(2, 0.5,number_format($od[4], 2, ',', '.').' ', 0, 0, 'R');

			$lin = $lin + 0.5;
			
			$lin_od = $lin;
			$i++;
			$die=$die+$od[4];
		} 
	} else {
		$pdf->SetXY(11,$lin);
		$pdf->Cell(2.4, 0.5,'-  ', 0, 0, 'C');
		$pdf->Cell(1.8, 0.5,'-  ', 0, 0, 'R');
		$pdf->Cell(1.5, 0.5,'-  ', 0, 0, 'R');
		$pdf->Cell(2, 0.5,'-  ', 0, 0, 'R');
	}
	$i--;
	$lin=$lin - ($i * 0.5);
	$i=1;
	$qryg = "SELECT r.nr_nota_fiscal, r.reservatorio_codigo, sum(h.qtd_abastecida), r.valor, sum(h.qtd_abastecida)*r.valor ,e.combustivel_codigo
						FROM recebimento r, estoque e, abastecimento a, historico_abastecimento h
						WHERE r.nr_nota_fiscal=e.nr_nota_fiscal
							and r.combustivel_codigo=e.combustivel_codigo
							and e.nr_repasse=h.nr_repasse
							and e.om_codom=h.om_codom
							and a.dt_abastecimento=h.dt_abastecimento
							and a.viatura_eb=h.viatura_eb
							and h.qtd_abastecida > 0
							and h.dt_abastecimento between  '".$_GET['dt_ini']." 00:00' and '".$_GET['dt_fim']." 23:59'  
							and e.om_codom = $row[1] 
							and e.combustivel_codigo = '1' 
							and r.reservatorio_codigo in( select codigo from reservatorio where om_codom='$row[0]')
						group by r.nr_nota_fiscal, r.reservatorio_codigo, e.om_codom ";

	$resg = mysql_query($qryg,$conexao);
	if(mysql_num_rows($resg)>0) {
  		while ($g = mysql_fetch_array($resg, MYSQL_NUM)) {
			if($lin>18.5) {
				$pdf->AddPage();
				$pg++;
				$lin=2;
			}
			$pdf->SetXY(18.8,$lin);
			$pdf->Cell(2.5, 0.5,$g[0].' ', 0, 0, 'C');
			$pdf->Cell(1.8, 0.5,number_format($g[2], 0, ',', '.').' ', 0, 0, 'R');
			$pdf->Cell(1.5, 0.5,number_format($g[3], 2, ',', '.').' ', 0, 0, 'R');
			$pdf->Cell(2, 0.5,number_format($g[4], 2, ',', '.').' ', 0, 0, 'R');
			$lin=$lin + 0.5;
			$lin_g = $lin;
			$i++;
			$gas=$gas+$g[4];
		} 
	} else {
		$pdf->SetXY(18.8,$lin);
		$pdf->Cell(2.4, 0.5,'-  ', 0, 0, 'C');
		$pdf->Cell(1.8, 0.5,'-  ', 0, 0, 'R');
		$pdf->Cell(1.5, 0.5,'-  ', 0, 0, 'R');
		$pdf->Cell(2, 0.5,'-  ', 0, 0, 'R');
	}
	$i--;
	$lin=$lin - ($i * 0.5);

	$pdf->SetXY(22.8,$lin);
	$pdf->Cell(0, 0.5,number_format(($gas + $die), 2, ',', '.').' ', 0, 0, 'R');
	$gas = $die = 0;
	if($lin_od >= $lin_g) {
		$pdf->SetXY(1,$lin_od);
		$lin = $lin_od;
	} else {
		$pdf->SetXY(1,$lin_g);
		$lin = $lin_g;
	} 
	$pdf->Cell(0, 0.01,'', 1, 1, 'C');
}

if($pg>1) {
	$pdf->Line(1, 1, 28.7, 1);
	$pdf->Line(1, 1, 1, $lin);
	$pdf->Line(6, 1, 6, $lin);
	$pdf->Line(11, 1, 11, $lin);

	$pdf->Line(13.5, 1, 13.5, $lin);
	$pdf->Line(15.3, 1, 15.3, $lin);
	$pdf->Line(16.8, 1, 16.8, $lin);

	$pdf->Line(18.8, 1, 18.8, $lin);
	$pdf->Line(21.2, 1, 21.2, $lin);
	$pdf->Line(23.1, 1, 23.1, $lin);
	$pdf->Line(24.6, 1, 24.6, $lin);

	$pdf->Line(26.6, 1, 26.6, $lin);
	$pdf->Line(28.7, 1, 28.7, $lin);
}else{
	$pdf->Line(1, 6.7, 1, $lin);
	$pdf->Line(6, 6.7, 6, $lin);
	$pdf->Line(11, 6.7, 11, $lin);

	$pdf->Line(11, 7.2, 26.6, 7.2);
	$pdf->Line(13.5, 7.2, 13.5, $lin);
	$pdf->Line(15.3, 7.2, 15.3, $lin);
	$pdf->Line(16.8, 7.2, 16.8, $lin);

	$pdf->Line(18.8, 6.7, 18.8, $lin);
	$pdf->Line(21.2, 7.2, 21.2, $lin);
	$pdf->Line(23.1, 7.2, 23.1, $lin);
	$pdf->Line(24.6, 7.2, 24.6, $lin);

	$pdf->Line(26.6, 6.7, 26.6, $lin);
	$pdf->Line(28.7, 6.7, 28.7, $lin);
} 

$pdf->SetXY(1,$lin);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 0.4, 'Data e Hora de Emissão: '.date('d/m/Y H:i').' hs.',0,1,'L');

$pdf->Output();
?>




