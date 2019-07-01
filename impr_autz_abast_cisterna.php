<html>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
</head>
<?php
echo "<br>";
echo "<hr>";
echo "<center><b>Informações sobre Abastecimento de Cisterna</b>";
echo "<hr><br>";
	echo "<table border='1' width='400'>";
	echo "<tr><td>Autorizado Por: </td><td><b><i>&nbsp;&nbsp;".$_GET['usuario_autz']."</td></tr>";
	echo "<tr><td>Data Autorização: </td><td><b><i>&nbsp;&nbsp;".$_GET['data']."</td></tr>";
	echo "<tr><td width='50%'>EB da Viatura: </td><td width='50%'><b><i>&nbsp;&nbsp;".$_GET['eb']."</td></tr>";
	echo "<tr><td>Responsável: </td><td><b><i>&nbsp;&nbsp;".$_GET['responsavel']."</td></tr>";
	echo "<tr><td>Reservatório: </td><td><b><i>&nbsp;&nbsp;Nº ".$_GET['posto']."</td></tr>";
	echo "<tr><td>Quantidade: </td><td><b><i>&nbsp;&nbsp;".$_GET['qtd']." Lts</td></tr>";
	echo "<tr><td>Código de Segurança: </td><td><b><i><font color='red'> &nbsp;&nbsp;".strtoupper($_GET['cod'])."</td></tr>";
	echo "</table>";
	echo "<br><br>";
  echo "<a href='#' onclick='javaScript:window.print()'><img border='0' src='./imagens/printer.gif' width='30' height='30'></a>&nbsp;&nbsp;&nbsp;&nbsp;";
  echo "<a href='#' onclick='javaScript:window.close()'><img border='0' src='./imagens/fechar.jpeg' width='50' height='20'></a>";
echo "</center>";
?>
</html>

