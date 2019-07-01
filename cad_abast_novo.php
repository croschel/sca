<?php
/*		$query="select a.viatura_eb, a.dt_abastecimento, v.marca, v.modelo, v.combustivel_codigo, v.om_codom, o.sigla, c.nome, u.post_grad, u.nome_guerra, 
					a.qtd_autorizada, a.motorista, c.codigo, a.disponibilidade_reservatorio_codigo, a.missao, a.disponibilidade_om_codom, a.od_atual
				from viatura v, om o, combustivel c, abastecimento a, usuario u
				where o.codom=v.om_codom 
					and c.codigo=v.combustivel_codigo 
					and u.idt=a.usuario_autz 
					and v.eb=a.viatura_eb
					and a.dt_abastecimento='".$_GET['dt_abast']."' 
					and a.viatura_eb='".$_GET['eb']."'";*/
	include "verifica_sessao.php";

	/*Seeleciona todos os alunos*/
	if($_GET['tipo']=='vtr') {
		$query="select o.sigla, a.motorista, a.viatura_eb, concat(v.marca, ' - ', v.modelo), c.nome, a.reservatorio_codigo, a.dt_autorizacao, a.missao, a.qtd_autorizada, 
					a.od_atual, v.om_codom, v.combustivel_codigo, concat(u.post_grad, u.nome_guerra), c.codigo, a.cod_tp_cota, a.cota_om
				from viatura v, om o, combustivel c, abastecimento a, usuario u
				where o.codom=v.om_codom 
					and c.codigo=v.combustivel_codigo 
					and u.idt=a.usuario_autz 
					and v.eb=a.viatura_eb
					and a.dt_abastecimento='".$_GET['dt_abast']."' 
					and a.viatura_eb='".$_GET['eb']."'";
	} elseif($_GET['tipo']=='cisterna') {
		$query="select o.sigla, a.responsavel, a.viatura_eb, ifnull(a.qtd_atual,'Carregamento de Cisterna'), c.nome, a.disponibilidade_reservatorio_codigo, a.dt_autorizacao, a.missao, a.qtd_autorizada, 
					c.codigo, d.om_codom, a.disponibilidade_om_codom, 
					c.codigo
				FROM combustivel c, reservatorio r, om o, disponibilidade d, cisterna a
				WHERE c.codigo = r.combustivel_codigo
					and r.codigo=d.reservatorio_codigo
					and o.codom=d.om_codom
					and d.om_codom=a.disponibilidade_om_codom
					and d.reservatorio_codigo=a.disponibilidade_reservatorio_codigo
					and a.dt_autorizacao='".$_GET['dt_abast']."' 
					and a.viatura_eb='".$_GET['eb']."'";
	}

 	$result = mysql_query($query,$conexao);
	$row = mysql_fetch_row($result);
	$ano=substr($row[6],0,4);
	$mes=substr($row[6],5,2);
	$dia=substr($row[6],8,2);
	$hora=substr($row[6],11,5);
	$dt_autz = $dia.'/'.$mes.'/'.$ano.' '.$hora;
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<?php
	echo "<font size='5'><br><br><center><b>Registro de Abastecimento</b> </center><p>";
	echo "<form method='POST' action='index.php?nomeArquivo=gravar_abast.php' name='abastecimento'>";
	echo "<table border='0' width='60%' align='center' valign='top'>";
	echo "<tr><td align='right'>Organização Militar: </td><td><b><I>$row[0]</td></tr>";
	echo "<tr><td align='right'>Motorista da Viatura: </td><td><b><I>$row[1]</td></tr>";
	echo "<tr><td align='right'>EB da Viatura: </td><td><b><I>$row[2]</td></tr>";
	echo "<tr><td align='right'>Viatura: </td><td><b><I>$row[3]</td></tr>";
	echo "<tr><td align='right'>Combustível: </td><td><b><I>$row[4]</td></tr>";
	echo "<tr><td align='right'>Reservatório: </td><td><b><I>Nº $row[5]</td></tr>";
	echo "<tr><td align='right'>Data/Hora Autorização: </td><td><b><I>$dt_autz</td></tr>";
	echo "<tr><td align='right'>Missão: </td><td><b><I>$row[7]</td></tr>";
	echo "<tr><td align='right'>Quantidade Autorizada: </td><td><b><I>$row[8] Lts</td></tr>";
	echo "<tr><td align='right'>Data/Hora Abastecimento: </td><td><input type='text' id='dt_ini' size='10' maxlength='10' name='dt_abastec'></td></tr>";
	echo "<tr><td align='right'>Quantidade Abastecida: </td><td><input type='number' max='$row[8]' name='qtd_abast' ><i> Lts</td></tr>";
	if($_GET['tipo']=='vtr') {
		echo "<tr><td align='right'>Odometro Atual: </td><td><input type='text' size='8' maxlength='8' name='odometro' value='$row[9]'></td></tr>";
	}
	echo "<tr><td align='right'>Código Abastecimento: </td><td><input type='text' size='8' maxlength='5' name='codigo' value=''></td>";
	echo "<input type='hidden' name='eb' value='$row[2]'>";
	echo "<input type='hidden' name='dt_abast' value='$row[6]'>";
	echo "<input type='hidden' name='codom' value='$row[10]'>";
	echo "<input type='hidden' name='reservatorio' value='$row[5]'>";
	echo "<input type='hidden' name='tipo' value='".$_GET['tipo']."'>";
	echo "<input type='hidden' name='cod_tp_cota' value='$row[14]'>";
	echo "<input type='hidden' name='cota' value='$row[15]'>";
	echo "<input type='hidden' name='om' value='$row[0]'>";
	echo "<tr><td align='right'><input type='submit' value= 'Gravar'></td><td><input type='reset' value= '  Cancelar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
   echo "</table>";
	echo "</form>";
	
?>

