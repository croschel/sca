<?php
   include "verifica_sessao.php";
	include "conexao.php";

	$query="SELECT codigo FROM reservatorio
			order by CONCAT(REPEAT('0', 20 - LENGTH(codigo)), codigo) desc";
	$result = mysql_query($query);
	$num = mysql_fetch_row($result);
	$num[0]=$num[0]+1;
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<?php
	echo "<form name='viatura' method='POST' action='index.php?nomeArquivo=gravar_reservatorio.php' >";
	echo "<center><t1><br>Cadastro de Reservatório </t1> <p> </center>";
	echo "<table>";
	echo "<tr><td align='right'>Nº do Reservatório: </td><td><input type='text' size='10' maxlength='2' name='cod_reservatorio' readonly='true' value='$num[0]'></td></tr>";
	echo "<tr><td align='right'>Local do Reservatório: </td><td><select name='codom'>";
						echo "<option value='' selected></option>";
							/*Seleciona a relação de om*/
						if($_SESSION['perfil']=='ADMINISTRADOR') {
							$result = mysql_query("select * from om where tipo<>'Não Integrante' and (oc='".$_SESSION['codom']."' or codom='".$_SESSION['codom']."') order by sigla");
						} else {
							$result = mysql_query("select * from om where tipo<>'Não Integrante' order by sigla");
						}
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$row[0]' >$row[1]</option>";
						}
						echo "</select></td></tr>";
	echo "<tr><td align='right'>Tipo de Combustível: </td><td><select name='combustivel'>";
						echo "<option value='' selected></option>";
						/*Seleciona o tipo de combustivel*/
						$result = mysql_query("select * from combustivel order by codigo ");
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$row[0]' >$row[1]</option>";
						}
						echo "</select></td></tr>";
	echo "<tr><td align='right'>Capacidade: </td><td><input type='text' size='10' maxlength='10' name='capacidade'></td></tr>";
	echo "<tr><td align='right'>Taxa Mensal de Evaporação: </td><td><input type='text' size='5' maxlength='10' name='taxa'> %</td></tr>";
	echo "<tr><td align='right'>Situação: </td><td><select name='situacao'>";
					echo "<option value='Ativo'> Ativo</option>";
					echo "<option value='Inativo'> Desativado</option>";
					echo "</select></td></tr>";
	echo "<input type='hidden' name='operacao' value='1'>";
	echo "<tr><td></td><td></td></tr>";
	echo "<tr><td align='right'><input type='submit' value= 'Cadastrar'></td><td><input type='reset' value= '  Limpar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
   echo "</table>";
echo "</form>";
?>
