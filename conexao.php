<?php
/*conectar ao mysql*/ 
$conexao = mysql_connect("localhost", "root", "" )
//$conexao = mysqli_connect("10.1.140.249", "combustivel", "hTcXx0bAAM5hrif6", "combustivel" )
//$conexao = mysqli_connect("localhost", "root", "fenix1199", "scc_8rm" )
or die ("<br><BLINK><font color='red' size='5'>ATEN&Ccedil;&Atilde;O!</BLINK>
<br><center><b><p></font><P> <IMG SRC='./imagens/servidor.png'><p>
O Banco de Dados n&atilde;oo foi inicializado  corretamente!");
/*/Selecionando o banco de dados...*/ 
//$db = mysql_select_db("rio2016",$conexao)
//$db = mysql_select_db("scc_8rm",$conexao)
//$db = mysql_select_db("sca_11RM",$conexao)
//$db = mysql_select_db("scc_3rm",$conexao)
$db = mysql_select_db("sca",$conexao)
//$db = mysql_select_db("scc_3de",$conexao)
//$db = mysql_select_db("sca_9gptlog",$conexao)
or die ("N&atilde;o foi poss&iacute;vel conectar com Banco de Dados!");
//mysqli_set_charset($conexao,"ISO-8859-1");

?>
