<?php
     include "verifica_sessao.php";
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<form name="om" method="POST" action="index.php?nomeArquivo=gravar_om.php" >
   <center><font size=5><p><b>REGISTRO DE EVAPORAÇÃO<p></font> </b> </center>
   <table>
   <tr><td align="right">Reservatório:* </td><td><input type="text" size="10" maxlength="6" name="codom"></td></tr>
   <tr><td align="right">Sigla da OM:* </td><td><input type="text" size="30" maxlength="30" name="sigla"></td></tr>
   <tr><td align="right">Nome da OM:* </td><td><input type="text" size="60" maxlength="50" name="nome_om"></td></tr>
   <input type="hidden" name="operacao" value="1">
   <tr><td></td><td></td></tr>
   <tr><td align="right"><input type="submit" value= "Cadastrar"></td><td><input type="reset" value= "  Limpar  ">&nbsp;<input type="button" value= "Retornar" onclick="history.go(-1)"></td></tr>
   </table>
</form>
</div>
