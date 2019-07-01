<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
	if(empty($_SESSION['usuario']))
		include "verifica_sessao.php";

	/*/Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	
	$query = "delete from ".$_POST['tab']." where ".$_POST['ch']."=".$_POST['valor']." and ".$_POST['ch1']."=".$_POST['valor1'];
	$rc = mysql_query($query);
	if($rc)	{
		/*/Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('REMOVEU O RECEBIMENTO DA '.$_POST['descricao']);
		echo ("<br><br>ATENÇÃO! Recebimento removido com sucesso.");
	}
	else{
		echo ("<br><br>ATENÇÃO! Erro ao remover o Recebimento ".$_POST['descricao'].".");
		/*/Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('TENTOU REMOVER O RECEBIMENTO DA '.$_POST['descricao']);
	}
?>	

<p>
<center><a href="index.php?nomeArquivo=cons_recebimento.php"><u><b>CONTINUAR</b></u></a></center>
