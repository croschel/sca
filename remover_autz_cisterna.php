<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
	if(empty($_SESSION['usuario']))
		include "verifica_sessao.php";
	/*Conexão ao Banco de Dados*/	
	include "conexao.php";
	/*Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	$query = "delete from ".$_POST['tab']." where ".$_POST['ch']."='".$_POST['valor']."' and ".$_POST['ch1']."='".$_POST['valor1']."'";

	$resultado = mysql_query($query);
	if (mysql_affected_rows()!= 0){
		/*Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('Removeu '.$_POST['descricao']);
		echo ("<br><br>ATENÇÃO! <br><br>Autorização de carregamento de cisterna removida com sucesso.");
	}
	else{
		echo ("<br><br>ATENÇÃO! <br><br>Erro ao remover ".$_POST['descricao'].".");
		/*Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('Tentou remover '.$_POST['descricao']);
	}

	echo "<p><center><a href='index.php?nomeArquivo=abast_cisterna.php'><u><b>CONTINUAR</b></u></a></center>";

?>	


