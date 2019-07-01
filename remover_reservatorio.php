<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
	if(empty($_SESSION['usuario']))
		include "verifica_sessao.php";
	//Conexão ao Banco de Dados	
	include "conexao.php";
	//Conexão com arquivo para registrar atividade
	include "atividade.php";
	$query = "delete from ".$_POST['tab']." where ".$_POST['ch']."=\"".$_POST['valor']."\"";
	$rc = mysql_query($query);
	if($rc)	{
		//Registra atividade executada pelo usuário para auditoria
		registrarAtividade('Removeu '.$_POST['descricao']);
		echo ("<br><br>ATENÇÃO! Reservatório removido com sucesso.");
	}
	else{
		echo ("<br><br>ATENÇÃO! Erro ao remover o Reservatório ".$_POST['descricao'].".");
		//Registra atividade executada pelo usuário para auditoria
		registrarAtividade('Tentou remover '.$_POST['descricao']);
	}
?>	

<p>
<center><a href="index.php?nomeArquivo=cons_reservatorio.php"><u><b>CONTINUAR</b></u></a></center>
