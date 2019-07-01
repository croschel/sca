<?php
   include "verifica_sessao.php";
	include "conexao.php";

	echo "<head>";
		echo "<META http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />";
	echo "</head>";

	echo "<form name='viatura' method='POST' action='index.php?nomeArquivo=gravar_credito.php' >";
	echo "<center><t1><br>Cadastro de Crédito de Combustível </t1> <p> </center>";
	echo "<table>";
	echo "<tr><td align='right'>OM Atendida: </td><td><select name='codom'>";
						echo "<option value='' selected DISABLED>:-: ESCOLHA A OM :-:</option>";
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
							echo "<option value='$row[1]' >$row[2]</option>";
						}
						echo "</select></td></tr>";
	echo "<tr><td align='right'>Combustível: </td><td><select name='combustivel'>";
						echo "<option value='' selected DISABLED>:-: ESCOLHA O COMBUSTÍVEL :-:</option>";
						/*Seleciona o tipo de combustivel*/
						$result = mysql_query("select * from combustivel order by codigo ");
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$row[0]' >$row[1]</option>";
						}
						echo "</select></td></tr>";
	echo "<tr><td align='right'>Tipo de Cota: </td><td><select name='cota'>";
						echo "<option value='' selected DISABLED>:-: ESCOLHA A COTA :-:</option>";
						/*Seleciona o tipo de combustivel*/
						$result = mysql_query("select * from tipo_cota order by cod_tp_cota ");
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$row[0]' > $row[1]</option>";
						}
						echo "</select></td></tr>";
	echo "<tr><td align='right'>Motivo: </td><td><input type='text' size='40' maxlength='100' name='desc_credito'></td></tr>";
	echo "<tr><td align='right'>Quantidade Destinada: </td><td><input type='number' min='1' size='11' name='qtd_dest'></td></tr>";
	echo "<input type='hidden' name='operacao' value='1'>";
	echo "<tr><td></td><td></td></tr>";
	echo "<tr><td align='right'><input type='submit' value= 'Cadastrar'></td><td><input type='reset' value= '  Limpar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
   echo "</table>";
echo "</form>";
?>
