<html>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
echo "<br>";
echo "<hr>";
echo "<center><b>Informações sobre o Abastecimento</b>";
echo "<hr><br>";
	echo "<table border='1' width='400'>";
	echo "<tr><td colspan='2' align='center'><b><i>".strtoupper($_GET['tp_abastecimento'])."</td></tr>";
	echo "<tr><td>OM da Viatura: </td><td><b><i>&nbsp;&nbsp;".$_GET['om']."</td></tr>";
	echo "<tr><td>Data Autorização: </td><td><b><i>&nbsp;&nbsp;".$_GET['data']."</td></tr>";
	echo "<tr><td width='50%'>EB da Viatura: </td><td width='50%'><b><i>&nbsp;&nbsp;".$_GET['eb']."</td></tr>";
	if($_GET['tp_abastecimento']=='Carregamento de Cisterna') {
		echo "<tr><td>Responsável: </td><td><b><i>&nbsp;&nbsp;".$_GET['motorista']."</td></tr>";
	} else {
		echo "<tr><td>Odometro Atual: </td><td><b><i>&nbsp;&nbsp;".$_GET['odometro']."</td></tr>";
		echo "<tr><td>Motorista: </td><td><b><i>&nbsp;&nbsp;".$_GET['motorista']."</td></tr>";
	}
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

