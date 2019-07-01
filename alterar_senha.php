<?php
     include "verifica_sessao.php";
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
</head>
<br><br><center><b>ALTERAÇÃO DE SENHA</b> </center>
<form method="POST" action="index.php?nomeArquivo=gravar_senha.php">
	<input type="hidden" name="idt" value="<?=$row[0]?>"><br>
	<table>
		<tr><td align="right">Digite a Senha Atual: </td><td><input type="password" size="20" maxlength="15" name="senha_atual"></td></tr>
		<tr><td align="right">Digite a Nova Senha: </td><td><input type="password" size="20" maxlength="15" name="senha"></td></tr>
		<tr><td align="right">Confirme a Nova Senha: </td><td><input type="password" size="20" maxlength="15" name="confirma_senha"></td></tr>
		<tr><td></td><td></td></tr>
		<tr><td></td><td></td></tr>
		<tr><td align="right"><input type="submit" value= "Gravar"></td><td><input type="reset" value= "  Cancelar  ">&nbsp;<input type="button" value= "Retornar" onclick="history.go(-1)"></td></tr>
	</table>
</form>


