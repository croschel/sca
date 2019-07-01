<?php
	include "verifica_sessao.php";
	/*/Seeleciona todos os reservatorios*/
	$query="SELECT * 
		FROM reservatorio 
		WHERE codigo=".$_GET['codigo'];
	$result = mysql_query($query,$conexao);
	$row = mysql_fetch_row($result);

echo "<head>";
	echo "<META http-equiv='Content-Type' content='text/html; charset=utf-8' />";
echo "</head>";
echo "<br><t1>Alteração de Reservatório</t1> </center><p>";
echo "<form method='POST' action='index.php?nomeArquivo=gravar_reservatorio.php' name='viatura'>";
	echo "<table>";
	echo "<tr><td align='right'>Nº do Reservatório:* </td><td><input type='text' size='10' maxlength='2' name='cod_reservatorio' readonly='true' value='$row[0]'></td></tr>";
	echo "<tr><td align='right'>Local do Reservatório:* </td><td><select name='codom'>";
						/*Seleciona a relacao de om*/
						$result = mysql_query("select * from om order by sigla");
  						while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$om[0]' ";	if ($om[0]==$row[1])	{echo "selected";}	echo ">$om[1]</option>";
						}
					echo "</select></td></tr>";
	echo "<tr><td align='right'>Tipo de Combustível:* </td><td><select name='combustivel'>";
						/*Seleciona tipo de combustivel*/
    						$result = mysql_query("select * from combustivel order by nome desc");
  						while ($combustivel = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$combustivel[0]' ";	if ($combustivel[0]==$row[2])	{echo "selected";	}	echo "> $combustivel[1]</option>";
						}
					echo "</select></td></tr>";
	echo "<tr><td align='right'>Capacidade do Reservatório:* </td><td><input type='text' size='10' maxlength='10' name='capacidade' value='$row[3]'>Litros</td></tr>";
	echo "<tr><td align='right'>Taxa Mensal de Evaporação: </td><td><input type='text' size='5' maxlength='10' name='taxa' value='".number_format($row[5], 1, ',', '.')."'> %</td></tr>";
	echo "<tr><td align='right'>Situação:* </td><td><select name='situacao'>";
					echo "<option value='Ativo' ";	if ($row[4]=='Ativo')	{echo "selected";	}	echo "> Ativo</option>";
					echo "<option value='Inativo' ";	if ($row[4]=='Inativo')	{echo "selected";	}	echo "> Desativado</option>";
					echo "</select></td></tr>";
   echo "<input type='hidden' name='operacao' value='2'>";
   echo "<tr><td></td><td></td></tr>";
   echo "<tr><td align='right'><input type='submit' value= 'Gravar'></td><td><input type='reset' value= '  Cancelar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
   echo "</table>";

echo "</form>";
?>

