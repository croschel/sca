<script>
function atualiza_qtd_atual(nova_qtd,qtd_dest,qtd_atual) {
	if (nova_qtd < (qtd_dest - qtd_atual)) {
		document.credito.qtd_dest.value= qtd_dest;
		document.credito.qtd_atual.value= qtd_atual;
		alert('ATENÇÃO! Valor da quantidade destinada menor que a quantidade já consumida');
	} else {
		if (nova_qtd >= qtd_dest) {
			document.credito.qtd_atual.value= nova_qtd - qtd_dest + qtd_atual;	
		} else	if (nova_qtd < qtd_dest) {
			document.credito.qtd_atual.value= qtd_atual - (qtd_dest - nova_qtd) ;	
		}
	}	

}

</script>

<?php
   include "verifica_sessao.php";

	/*Conexão ao Banco de Dados*/
	include "conexao.php";

	/*Seeleciona a OM*/
	$query="select om.sigla, c.* from om, credito c where om.codom=c.om_codom and c.cod_credito='".$_GET['cod_credito']."' and c.om_codom='".$_GET['codom']."'";
	$result = mysql_query($query,$conexao);
	$credito = mysql_fetch_row($result);
	echo "<head>";
		echo "<META http-equiv='Content-Type' content='text/html; charset=utf-8' />";
	echo "</head>";

	echo "<form name='credito' method='POST' action='index.php?nomeArquivo=gravar_credito.php' >";
	echo "<center><t1><br>Cadastro de Crédito de Combustível </t1> <p> </center>";
	echo "<table>";
	echo "<input type='hidden' size='40' value='$credito[1]' name='cod_credito' >";
	echo "<input type='hidden' size='40' value='$credito[2]' name='codom_ant' >";
	echo "<tr><td align='right'>OM Atendida: </td><td><select name='codom'>";
							/*Seleciona a relação de om*/
						if($_SESSION['perfil']=='ADMINISTRADOR *') {
							$qry_om = "select cmdo.sigla, om.codom, om.sigla
											from om cmdo, om 
											where om.subordinacao=cmdo.codom
												and om.tipo<>'Não Integrante'
											order by abs(cmdo.sigla), cmdo.sigla, abs(om.sigla), om.sigla";
							$result = mysql_query($qry_om);
						} 
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							if($cmdo!=$row[0]) {
 								echo "<optgroup label='$row[2]'>";
  								$cmdo=$row[0];
  							}
							echo "<option value='$row[1]' "; if($row[1]==$credito[2]) { echo "selected";} echo ">$row[2]</option>";
						}
						echo "</select></td></tr>";
	echo "<tr><td align='right'>Combustível: </td><td><select name='combustivel'>";
						echo "<option value='' selected DISABLED>:-: ESCOLHA O COMBUSTÍVEL :-:</option>";
						/*Seleciona o tipo de combustivel*/
						$result = mysql_query("select * from combustivel order by codigo ");
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$row[0]' "; if($row[0]==$credito[3]) { echo "selected";} echo ">$row[1]</option>";
						}
						echo "</select></td></tr>";
	echo "<tr><td align='right'>Tipo de Cota: </td><td><select name='cota'>";
						echo "<option value='' selected DISABLED>:-: ESCOLHA A COTA :-:</option>";
						/*Seleciona o tipo de combustivel*/
						$result = mysql_query("select * from tipo_cota order by cod_tp_cota ");
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$row[0]' "; if($row[0]==$credito[4]) { echo "selected";} echo "> $row[1]</option>";
						}
						echo "</select></td></tr>";
	echo "<tr><td align='right'>Motivo: </td><td><input type='text' size='40' maxlength='100' value='$credito[5]' name='desc_credito'></td></tr>";
	echo "<tr><td align='right'>Quantidade Destinada: </td><td><input type='number' min='1' size='11' value='$credito[6]' name='qtd_dest' onChange='atualiza_qtd_atual(this.value,$credito[6],$credito[7])'></td></tr>";
	echo "<tr><td align='right'>Quantidade Atual: </td><td><input type='number' size='11' value='$credito[7]' name='qtd_atual' readonly></td></tr>";
	echo "<input type='hidden' name='operacao' value='2'>";
	echo "<tr><td></td><td></td></tr>";
	echo "<tr><td align='right'><input type='submit' value= 'Cadastrar'></td><td><input type='reset' value= '  Limpar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
   echo "</table>";
echo "</form>";
?>
