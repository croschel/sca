<?php
   include "verifica_sessao.php";
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<?php
	//Conexão ao Banco de Dados
	include "conexao.php";
	//Registra a leitura da mensagem
	if($_GET['arq']=='1') {
		$query2 = "update destinatario set arquivada=now()
				where data_hora='".$_GET['dt_hr']."' and destinatario_idt='".$_SESSION['idt']."' ";
	} elseif($_GET['arq']=='0') {
		$query2 = "update destinatario set arquivada=null
				where data_hora='".$_GET['dt_hr']."' and destinatario_idt='".$_SESSION['idt']."' ";
	}
//echo $query2;
	$rc = mysql_query($query2);

	echo "<p><center><meta http-equiv=REFRESH Content='0;URL=index.php?nomeArquivo=cons_msg.php'>";

?>



