<html>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
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
	echo "<tr><td>Quantidade Autorizada: </td><td><b><i>&nbsp;&nbsp;".$_GET['qtd']." Lts</td></tr>";
	echo "<tr><td>Código de Segurança: </td><td><b><i><font color='red'> &nbsp;&nbsp;".strtoupper($_GET['cod'])."</td></tr>";
	
	//set it to writable location, a place for temp generated PNG files
	$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    
   //html PNG location prefix
   $PNG_WEB_DIR = 'temp/';

   include "qrlib.php";  
   
   if (!file_exists($PNG_TEMP_DIR))
   	mkdir($PNG_TEMP_DIR);
 
	$filename = $PNG_TEMP_DIR.'test.png';

   $errorCorrectionLevel = 'L';

	/*/gera o codigo de barra do abastecimento*/	
	$fig = "eb=".$_GET['eb']."&dt=".$_GET['dt']."&res=".$_GET['res']."&cota=".$_GET['cota']."&tpcota=".$_GET['tp_cota']."&qtd_autz=".$_GET['qtd'];

	QRcode::png($fig , $filename, $errorCorrectionLevel, 4, 2);    

	echo "<tr><td colspan='2' align='center'><img src=".$PNG_WEB_DIR.basename($filename)." /></td></tr>";
	echo "<tr><td>Quantidade Abastecida: </td><td><b><i>&nbsp;&nbsp;___________ Lts</td></tr>";
	echo "<tr><td>Rúbrica Abastecedor: </td><td><b><i>&nbsp;&nbsp;____________________</td></tr>";
	echo "</table>";

	echo "<br><br>";
  echo "<a href='#' onclick='javaScript:window.print()'><img border='0' src='../imagens/printer.gif' width='30' height='30'></a>&nbsp;&nbsp;&nbsp;&nbsp;";
  echo "<a href='#' onclick='javaScript:window.close()'><img border='0' src='../imagens/fechar.jpeg' width='50' height='20'></a>";
echo "</center>";
?>
</html>

