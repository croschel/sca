<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<Script Language="JavaScript">
function getRepasse() {
      document.location=('index.php?nomeArquivo=repasse.php');
}
</script>

<?php    
   

	echo "<table border='0' bgcolor='$cor_fundo' align='left' valign='top'>";
	echo "<tr align='center'><td COLSPAN='8'>&nbsp;</td></tr>";
	echo "<tr align='center'><td COLSPAN='8'><t1>Disponibilidade de Combustíveis para Repasses</td></tr>";
	echo "<tr align='center'><td COLSPAN='8'>&nbsp;</td></tr>";
	echo "<tr bgcolor='$cor1' align='center'>
				<td bgcolor='$cor_fundo' rowspan='2'>&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td rowspan='2'><coluna>&nbsp;OM&nbsp;</td>
				<td rowspan='2'><coluna>&nbsp;Reservatório&nbsp;</td>
				<td rowspan='2'><coluna>&nbsp;Combustível&nbsp;</td>
				<td colspan='3'><coluna>&nbsp;Disponibilidade&nbsp;</td>
				<td rowspan='2'><coluna>&nbsp;Opção&nbsp;</td>
			</tr>";
	echo "<tr bgcolor='$cor1' align='center'><td><coluna>&nbsp;Reserva Estratégica&nbsp;</td><td><coluna>&nbsp;Abastecimento Trânsito&nbsp;</td><td><coluna>&nbsp;Comando Brigada&nbsp;</td></tr>";

	$qry_res ="SELECT om.codom, om.sigla, r.codigo, c.nome, truncate(sum(e.qtd_atual),0), e.combustivel_codigo, om.oc
						FROM om, estoque e, reservatorio r, combustivel c
						WHERE om.codom=r.om_codom
							and r.codigo = e.reservatorio_codigo
							and c.codigo=e.combustivel_codigo 
							and e.om_codom='".$_SESSION['codom']."'
						group by om.codom, om.sigla, r.codigo, c.nome, e.combustivel_codigo, om.oc
						order by om.sigla, c.nome";
	$res_res=mysql_query($qry_res);
	while ($res = mysql_fetch_array($res_res, MYSQL_NUM)) {
		//Quantidade disponível para transito
		$qry_trans = "select sum(qtd_atual) from estoque where reservatorio_codigo='$res[2]' and om_codom='$res[0]' and cod_tp_cota='7'";
		$res_trans=mysql_query($qry_trans);
		$trans = mysql_fetch_row($res_trans);
		//Quantidade disponível do Cmdo Bda
		$qry_oc = "select sum(qtd_atual) from estoque where reservatorio_codigo='$res[2]' and om_codom='$res[6]'";
		$res_oc=mysql_query($qry_oc);
		$oc = mysql_fetch_row($res_oc);
		if($_GET['res']==$res[2]) {
			echo "<tr><td colspan='7'>&nbsp;</td></tr>";
			echo "<tr bgcolor='$cor2'><td bgcolor='$cor_fundo'></td><td colspan='7'><coluna>Repasse de Combustível no Posto do(a) ".$_GET['posto']." </td></tr>";
			echo "<tr bgcolor='$cor2'><td bgcolor='$cor_fundo'></td><td align='center'><coluna>&nbsp;$res[1]&nbsp;</td>
					<td align='center'><coluna>Nº&nbsp;$res[2]</td>
					<td align='center'><coluna>$res[3]</td>
					<td align='right'><coluna>&nbsp;".number_format($res[4],0,',','.')."&nbsp;Lt&nbsp;</td>
					<td align='right'><coluna>&nbsp;".number_format($trans[0],0,',','.')."&nbsp;Lt&nbsp;</td>
					<td align='right'><coluna>&nbsp;".number_format($oc[0],0,',','.')."&nbsp;Lt&nbsp;</td>
					<td>&nbsp;</td> ";
			echo "</tr>";
			echo "<tr bgcolor='$cor3'><td bgcolor='$cor_fundo'></td><td colspan='7'>";
				echo "<table width='100%' border='0'>";
				echo "<form name='repasse' method='POST' action='index.php?nomeArquivo=gravar_repasse.php' >";
				echo "<tr><td align='right'>";
							echo "<coluna>OM Destino: </td><td><select name='codom'>"; 
							$qry_repasse = "select codom, sigla from om where codom in ((select oc from om where codom='".$_GET['codom']."') , (select om_codom from reservatorio where codigo='".$_GET['res']."')) ";
							$res_repasse=mysql_query($qry_repasse);
							while ($repasse = mysql_fetch_array($res_repasse, MYSQL_NUM)) {
								echo "<option value='"; if(substr($repasse[1],0,4)=='CMDO') { echo "0$repasse[0]"; } else { echo "7$repasse[0]"; } echo "' >$repasse[1]</option>";
							}
							echo "</select>";
						echo "</td>";
						echo "<td align='right'>";
							echo "<coluna>Qtd: </td><td><input type='number' size='6' max='".$_GET['qtd_atual']."' min='0' name='qtd_repassada' >";
						echo "</td>";
						echo "<td align='center'><input type='submit' value= '  Gravar  '></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align='right'>";
							echo "<coluna>Nº Remessa: </td><td><input type='text' size='20' name='remessa' >";
						echo "</td>";
						echo "<td align='right'>";
							echo "<coluna>Obs: </td><td><input type='text' size='30' name='obs' >";
						echo "</td>";
						echo "<td align='center'><input type='button' value= 'Cancelar' onclick='getRepasse()'</td>";
						echo "</tr>";
						echo "<input type='hidden' name='reservatorio' value='$res[2]'>";
						echo "<input type='hidden' name='combustivel' value='$res[3]'>";
						echo "<input type='hidden' name='oc' value='$res[6]'>";
						echo "</form>";
				echo "</table>";
			echo "</td></tr>";
			echo "<tr bgcolor='$cor2'><td bgcolor='$cor_fundo'></td><td colspan='7'>&nbsp;</td></tr>";
			echo "<tr><td colspan='7'>&nbsp;</td></tr>";
		} else {
			echo "<tr bgcolor='$cor3' align='right' class='marcar_linha'><td bgcolor='$cor_fundo'></td><td align='center'><lin>&nbsp;$res[1]&nbsp;</td>
						<td align='center'><lin>Nº&nbsp;$res[2]</td>
						<td align='center'><lin>$res[3]</td>
						<td><lin>&nbsp;".number_format($res[4],0,',','.')."&nbsp;Lt&nbsp;</td>
						<td><lin>&nbsp;".number_format($trans[0],0,',','.')."&nbsp;Lt&nbsp;</td>
						<td><lin>&nbsp;".number_format($oc[0],0,',','.')."&nbsp;Lt&nbsp;</td>
						<td align='center'>&nbsp; ";
						if($res[4]>0) {
							echo "<a href='index.php?nomeArquivo=repasse.php&codom=$res[0]&res=$res[2]&qtd_atual=$res[4]&posto=$res[1]' style='text-decoration:none'>Transferir</a> &nbsp;";
						}else {
							echo "-";
						}
				echo "</td>";
			echo "</tr>";
		}
		
	}
	echo "<tr bgcolor='$cor2' align='left'><td bgcolor='$cor_fundo'></td><td colspan='7'>&nbsp;</td></tr>";
	echo "</table>";
?>
