
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
	echo "<br><br><p><font size='20pt' color='red'>ATENÇÃO!</font><p><img src='./imagens/bloqueio.png' width='200' height='200' border='0'>";
	echo "<p> <font color='red'>Acesso indevido registrado a partir do endereço IP ".getenv("REMOTE_ADDR").".</font>"; 

	echo "<p><a href='index.php?nomeArquivo=login.php'><IMG SRC='./imagens/inicio.png' width='60' height='30' border='0'></a>";
	
?>