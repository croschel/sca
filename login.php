<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
	echo "<br><br>";
//	echo "<br><p><br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><p><br><font color='red'> <h3> O Sistema de Controle de Combustíveis estará indisponível no dia 25 Mar 2015 das 08:00 hs às 11:45 hs.<br>Motivo manutenção no servidor.</font> ";

	echo "<p><font color='#000000'><font size=4><center><i>Login do Usuário</font><p> ";
	echo "<IMG SRC='./imagens/icon_login.gif'>";
	echo "<table border='0' >";
	echo "<form name='login' method='POST' action='index.php?nomeArquivo=principal.php'>";
	echo "<tr><td>&nbsp;</td></tr>";
	echo "<tr><td align='right'>Identidade&nbsp;</td><td><input type='text' name='idt' size='20'></td></tr>";
	echo "<tr><td align='right'>Senha&nbsp;</td><td><input type='password' name='senha' size='20'></td></tr>";
	echo "<tr><td colspan='2' align=center><br><input type='submit' name='b1' value='  Enviar  '>&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<input type='reset' name='b2' value='Cancelar'> </i></center></font></td></tr>";
	echo "</form>";
	echo "</table>";
?>