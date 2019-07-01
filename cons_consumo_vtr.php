<?php
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
$ano_ant = date("Y")-1;
$periodo = date("d")."/".date("m")."/".$ano_ant." � ".(date("d")+1)."/".date("m")."/".date("Y");

if($_GET['od_atual']!='') {
	$_GET['horimetro'] = str_replace(',', '.', $_GET['horimetro']);
	$query="update abastecimento set od_atual='".$_GET['od_atual']."', horimetro='".$_GET['horimetro']."' where viatura_eb='".$_GET['viatura_eb']."' and dt_abastecimento='".$_GET['dt_abast']."'";
	$result = mysql_query($query);
	$_GET['dt_abast']='';
}
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<?php
	echo "<table border='0' width='100%' align='left' valign='top'>";
	echo "<tr align='center' bgcolor='$cor_fundo'><td COLSPAN='10'>&nbsp;</td></tr>";
	echo "<tr align='center' bgcolor='$cor_fundo'><td COLSPAN='10'><b><t1><I>Consumo de Combustível</I></t1></td></tr>";
	echo "<tr align='center' bgcolor='$cof_fundo'><td COLSPAN='10'>&nbsp;</td></tr>";

	$resultado = mysql_query("SELECT *
				FROM viatura
				where eb='".$_GET['viatura_eb']."'");

	while ($vtr = mysql_fetch_array($resultado, MYSQL_NUM)) {
		echo "<tr bgcolor='$cor1' align='left'><td COLSPAN='10'><b>&nbsp;Viatura:<font color='$cor_submenu'> <i>$vtr[3] $vtr[4]</i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font> EB:<font color='$cor_submenu'> <i>$vtr[0]</font></td></tr>
		<tr bgcolor='$cor1' align='left'><td COLSPAN='10'><b>&nbsp;Período de Consumo:</b><font color='$cor_submenu'> <i> $periodo   </font></td></tr>
		<tr align='center' bgcolor='$cor2'>
			<td><b>Data</td>
			<td><b>Hora</td>
			<td><b>Qtd Abast</td>
			<td><b>Odômetro</td>
			<td><b>Horário</td>
			<td><b>Motorista</td>
			<td><b>Autorizado Por</td>
			<td><b>Missão</td>
			<td><b>Abastecedor</td>
			<td><b>Local</td>

		</tr>";

		$total=0;

		$qry1 = "SELECT a.dt_abastecimento, a.qtd_abastecida, a.od_atual, a.motorista, substr(u.post_grad,2), u.nome_guerra, a.usuario_idt, a.missao, a.reservatorio_codigo, a.local, ifnull(a.horimetro,0)
					FROM abastecimento a, usuario u
					WHERE u.idt = a.usuario_autz
						and a.viatura_eb='".$_GET['viatura_eb']."'
						and a.dt_abastecimento between '".$ano_ant."-".date("m")."-".date("d")."' and '".date("Y")."-".date("m")."-".date("d")." 23:59'
						and qtd_abastecida is not null
					order by dt_abastecimento";
		$cont=0;
		$resultado1 = mysql_query($qry1);
		while ($abast = mysql_fetch_array($resultado1, MYSQL_NUM)) {
			$data_a=substr($abast[0],8,2)."/".substr($abast[0],5,2)."/".substr($abast[0],0,4);
			$hora=substr($abast[0],11,5);
			$qry_abastecedor="select concat(substr(post_grad,2),' ', nome_guerra) from usuario where idt='$abast[6]'";
			$res=mysql_query($qry_abastecedor);
			$abastecedor=mysql_fetch_row($res);
			if($abastecedor[0]=='') {
				$abastecedor[0]='Abastecedor Externo';
			}
			
			/*Seeleciona a OM do reservatorio*/
 			$busca="select o.sigla from om o, reservatorio r where o.codom=r.om_codom and r.codigo='".$abast[8]."'";
			$resultado = mysql_query($busca);
			$local = mysql_fetch_row($resultado);
			if($local[0]=='') {
				$local[0]=$abast[9];
			}
			if(($_SESSION['perfil']=='FISCAL DA OM') and($_GET['viatura_eb']==$vtr[0]) and ($_GET['dt_abast']==$abast[0])) {
				echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><td>$data_a</td><td>$hora </td><td>$abast[1]</td>";
				echo "<form name='frm_autz' method='GET' action='index.php?nomeArquivo=cons_consumo_vtr.php' >";
				echo "<input type='hidden' name='nomeArquivo' value='cons_consumo_vtr.php'>";
				echo "<input type='hidden' name='viatura_eb' value='".$_GET['viatura_eb']."'>";
				echo "<input type='hidden' name='dt_abast' value='".$_GET['dt_abast']."'>";
				echo "<td>";
				if($_GET['c']=='o') {
					echo "<input type='text' size='6' name='od_atual' value='$abast[2]'><br>
						<input type='hidden' name='horimetro' value='$abast[10]'>
					<input type='submit' value= 'Gravar'>&nbsp;<input type='button' value= 'Cancelar' onclick='history.go(-1)'>";
				} else {
					echo "$abast[2]";
				}
				echo "</td>";
				echo "<td>";
				if($_GET['c']=='h') {
					echo "<input type='text' size='6' name='horimetro' value='$abast[10]'><br>
					<input type='hidden' name='od_atual' value='$abast[2]'>
					<input type='submit' value= 'Gravar'>&nbsp;<input type='button' value= 'Cancelar' onclick='history.go(-1)'>";
				} else {
					echo "$abast[10]";
				}	
				echo "</td>";
				echo "<td>$abast[3]</td><td>$abast[4] $abast[5]</td><td>$abast[7]</td><td>$abastecedor[0] $abastecedor[1]</td><td>$local[0]</td></tr>";
				echo "</form>";
			} else {
				echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><td>$data_a</td><td>$hora </td><td>$abast[1]</td>
				<td>$abast[2] ";
				if ($_SESSION['perfil']=='FISCAL DA OM') {				
					echo "<a href='index.php?nomeArquivo=cons_consumo_vtr.php&viatura_eb=$vtr[0]&dt_abast=$abast[0]&c=o'><img src='./imagens/editar.png' title='Alterar Odometro' width='15' height='15' border='0'></a>";
				}
				echo "</td>
				<td>".number_format($abast[10], 1, ',', '.')."";
				if ($_SESSION['perfil']=='FISCAL DA OM') {				
					echo "<a href='index.php?nomeArquivo=cons_consumo_vtr.php&viatura_eb=$vtr[0]&dt_abast=$abast[0]&c=h'><img src='./imagens/editar.png' title='Alterar Odometro' width='15' height='15' border='0'></a>";
				}
				echo "</td><td>$abast[3]</td><td>$abast[4] $abast[5]</td><td>$abast[7]</td><td>$abastecedor[0]</td><td>$local[0]</td></tr>";
			}
			if($cont==0) {
				$od_ini=$abast[2];
			}
			$cont++;
			$km_perc=$abast[2];
			$total=$total+$abast[1];
			$ult_abast=$abast[1];
		}
		$km_perc=$km_perc-$od_ini;
		$cons_medi = number_format($km_perc/($total-$ult_abast),2, ',', ' ');
		echo "<tr bgcolor='$cor2'><td COLSPAN='10'>&nbsp;<i><b>Quilometros Percorridos:</b><font color='$cor_submenu'> <i> $km_perc Km </font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Total Abastecido:</b><font color='$cor_submenu'> <i> $total Lts</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Consumo Médio:</b><font color='$cor_submenu'> <i> $cons_medi Km/Lt</td></tr>";
    		echo "<tr ><td COLSPAN='10'>&nbsp;<i><font size=2><b>OBS:</b> Os últimos <b>$ult_abast litros</b> abastecidos não foram computados no cálculo do consumo médio.</td></tr>";
    		echo "<tr><td align='center' COLSPAN='10'><br><b><a href='index.php?nomeArquivo=cons_consumo.php&oc=".$_GET['oc']."&codom=".$_GET['codom']."' style='text-decoration:none'><b>RETORNAR</b></a></td></tr>";
	}
	echo "</table>";
?>
	

