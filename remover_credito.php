<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
	if(empty($_SESSION['usuario']))
		include "verifica_sessao.php";

	/*/Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	$query = "delete from ".$_POST['tab']." where ".$_POST['ch']."='".$_POST['valor']."' and ".$_POST['ch1']."='".$_POST['valor1']."'";
	$rc = mysql_query($query);
	if($rc)	{
		/*/Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('Removeu '.$_POST['descricao']);
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Crédito removido com sucesso.");
	}
	else
	{ 
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro ao remover o Crédito ".$_POST['descricao'].".");
		/*/Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('Tentou remover '.$_POST['descricao']);
	}
 	echo "<p><center><a href='index.php?nomeArquivo=cons_credito.php#".$_POST['ancora']."'><u><b>CONTINUAR</b></u></a></center>";
 	
?>	


