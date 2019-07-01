<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";

?>

<Script Language="JavaScript">
function getStates(what) {
   if (what.selectedIndex != '') {
      var ebv = what.value;
      document.location=('index.php?nomeArquivo=abast_fora_sistema.php&ebv=' + ebv);
   }
}

$(document).ready(function () {
	$("input[name='qtd_abast']").css({width:'60px',})
})

</Script>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<?php  
	echo "<font size='5'><b> <br>Abastecimento Realizado Fora do Sistema<hr></font>";
	echo "<table width='100%'>";
	echo "<tr><td>";
	echo "<table border='0' width='60%' align='left' valign='top'>";
	echo "<tr align='center' bgcolor='$cor1'><td COLSPAN='5'><b><I>NOVO REGISTRO</I></td><td COLSPAN='3' bgcolor='$cor_fundo'></td></tr>";
	echo "</table>";
	echo "</td></tr>";
	
	
	echo "<tr><td>";
	echo "<table border='0' width='70%' align='left' valign='top'>";
	echo "<form name='frm_autz' method='POST' action='index.php?nomeArquivo=gravar_abast_fora_sistema.php' >";
	echo "<tr><td align='right'>EB:&nbsp;</td><td ><select name='eb' onChange='getStates(this);' required >";
				echo "<option value='' selected DISABLED>:-: ESCOLHA A VIATURA :-: </option>";
						/*Seleciona a relação de viaturas*/
						$result = mysql_query("select v.eb, c.nome, v.combustivel_codigo, v.marca, v.modelo, v.cap_tanque 
									from viatura v, combustivel c
									where c.codigo=v.combustivel_codigo
									and v.om_codom='".$_SESSION['posto']."' 
									order by v.eb");
						while ($vtr = mysql_fetch_array($result, MYSQL_NUM)) {
							$eb = $vtr[0];
							echo "<option value='$eb' "; if ($_GET['ebv']==$eb){ echo "SELECTED";$tp_comb = $vtr[2]; $viatura=$vtr[3]." - ".$vtr[4]; $capac=$vtr[5]; $tpcomb=$vtr[1];} echo " > $eb </option> ";
						}
						echo " </select></td></tr>";
	echo "<tr><td align='right'>Viatura:&nbsp;</td><td><font color='blue'><i>$viatura</td></tr>";
	echo "<tr><td align='right'>Tipo de Combustível:&nbsp;</td><td><font color='blue'><i>$tpcomb</td></tr>";
	echo "<tr><td align='right'>Capacidade do Tanque:&nbsp;</td><td><font color='blue'><i>$capac Litros</td></tr>";

	echo "<tr><td align='right'>Data Abastecimento:&nbsp;</td><td><input type='text' size='10' maxlength='10' name='dt_abast' id='dt_ini' value='$dt_inicio' required></td></tr>";
	echo "<tr><td align='right' width='19%'>Quantidade:&nbsp;</td><td width='18%'><input type='number' name='qtd_abast' min='1' max='$capac' required> Lts</td></tr>";
	echo "<tr><td align='right'>Odometro Atual:&nbsp;</td><td><input type='text' size='6' maxlength='50' name='odometro' required><br></td></tr>";
	echo "<tr><td align='right'>Motorista:&nbsp;</td><td><input type='text' size='30' maxlength='50' name='motorista' required><br></td></tr>";
	echo "<tr><td align='right'>Local:&nbsp;</td><td><input type='text' size='60' maxlength='50' name='local' required><br></td></tr>";
	echo "<tr><td align='right'>Tipo de Missão: </td><td><select name='cota' required>";
						echo "<option value='' selected DISABLED>:-: ESCOLHA A COTA :-:</option>";
						/*Seleciona o tipo de combustivel*/
						$result = mysql_query("select * from tipo_cota where cod_tp_cota >'0'order by cod_tp_cota ");
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$row[0]' > $row[1]</option>";
						}
						echo "</select></td></tr>";	
	echo "<tr><td align='right'>Missão:&nbsp;</td><td><input type='text' size='60' maxlength='50' name='missao' required><br></td></tr>";
	echo "<input type='hidden' name='tp_abastecimento' value='Abastecimento Fora do Sistema'>";
	echo "<input type='hidden' name='operacao' value='1'>";
	echo "<tr><td COLSPAN='2'>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td><input type='submit' value= 'Cadastrar'><input type='reset' value= '  Limpar  '></td></tr>";
	echo "</form>";
	echo "</table>";
	echo "</td></tr>";

	echo "<tr align='center' bgcolor='$cor_fundo'><td COLSPAN='8'><hr></td></tr>";
	
	echo "<tr><td>";
	echo "<table border='0' width='100%' align='left' valign='top'>";
	echo "<tr align='center' bgcolor='$cor1'><td COLSPAN='9'><b><I>ABASTECIMENTOS REGISTRADOS NOS ÚLTIMOS 30 DIAS</I></td></tr>";
	echo "<tr align='center' bgcolor='$cor2'>
		<td><b>Viatura</td><td><b>EB</td><td><b>Od Atual</td><td><b>Qtd</td><td><b>Motorista</td><td><b>Data Abast</td><td><b>Local</td><td><b>Missão</td><td><b>Remover</td>
	</tr>";
   
	/*Seleciona a OM e a qtd de gasolina disponível*/
	$query="SELECT a.viatura_eb, a.qtd_abastecida, a.dt_abastecimento, v.marca, v.modelo, a.dt_abastecimento, c.codigo, a.motorista, o.sigla, a.local, a.od_atual, a.missao
					FROM combustivel c, viatura v, abastecimento a, om o
					WHERE c.codigo = v.combustivel_codigo
						and o.codom=v.om_codom
						and v.eb=a.viatura_eb
						and a.cota_om is null
						and a.dt_abastecimento >= '".date('Y-m-d', mktime(0, 0, 0, date(m), date(d)-30, date(Y)))."'
						and o.codom='".$_SESSION['codom']."'
					ORDER BY a.dt_abastecimento desc";
	$resultado = mysql_query($query);				
	while ($row = mysql_fetch_array($resultado, MYSQL_NUM)) {
		$data = substr($row[2],8,2)."/".substr($row[2],5,2)."/".substr($row[2],0,4);
		echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><td align='left'>$row[4] - $row[3]</td><td>$row[0]</td><td>$row[10]</td><td>$row[1] Lts</td>
		<td align='left'>$row[7]</td><td>$data</td><td>$row[9]</td>	<td>$row[11]</td>
		<td><a href='index.php?nomeArquivo=remover_registro.php&&tab=abastecimento&ch=viatura_eb&valor=$row[0]&&ch1=dt_abastecimento&&valor1=$row[5]&valor2=3&registro=o abastecimento da Viatura $row[3] - EB $row[0] &arquivo=remover_autz.php' title='Remover este registro'>Remover</td></tr>";
		for ($x=0; $x<$i; $x++) {
			if (($comb[$x]==$row[2]) && ($reservatorio[$x]==$row[8]))
				$qtd[$x] = $qtd[$x] - $row[1];
		}
	}
	echo "</table>";
	echo "</td></tr>";
?>
