<?php
     include "verifica_sessao.php";
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<form name="viatura" method="POST" action="index.php?nomeArquivo=gravar_reservatorio.php" >
	<center><font size=5><p><b>RECEBIMENTO DE COMBUSTÍVEIS<p></font> </b> </center>
	<table>
	<tr>
	<td width="12%" align="right">Nº Nota Fiscal: </td>
	<td width="10%" align="left"><input type="text" size="10" maxlength="15" name="nota_fiscal"></td>
	<td width="5%" align="right">Data: </td>
	<td width="20%" align="left"><input type="text" size="10" maxlength="15" name="nota_fiscal"><font size="2">dd/mm/aaaa</font></td>
	<td width="5%" align="right">Combustível: </td>
	</td><td><select name="combustivel">
		<option value="" selected>SELECIONE</option>
		<?php
		//Conexão ao Banco de Dados
		include "conexao.php";
		//Seleciona a relação de alunos
		$result = mysql_query("select * from combustivel order by nome desc");
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo "<option value=\"".$row[0]."\" >".$row[1];
		}
		?> </select></td>
	<td width="12%" align="right">Qdt Recebida: </td>
	<td width="15%" align="left"><input type="text" size="6" maxlength="6" name="nota_fiscal"><font size="2">Litros</font></td>
	</tr>
	</table>
	<br><br>
	<table>
	<caption>DISTRIBUIÇÃO POR OM</caption>
	<tr ALIGN=center><td COLSPAN=6></td> <td></td></tr>

	<tr><td width="15%"></td><td align="right" width="10%"><input type="submit" value= "Cadastrar"></td>
	<td width="15%" align="center"><input type="reset" value= "  Limpar  "></td><td width="10%"><input type="button" value= "Retornar" onclick="history.go(-1)"></td>
	<td width="15%"></td><td width="35%"></td></tr>	
   	</table>
</form>
</div>
