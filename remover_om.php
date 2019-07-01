<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
	if(empty($_SESSION['usuario']))
		include "verifica_sessao.php";

	/*/Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	$query = "delete from ".$_POST['tab']." where ".$_POST['ch']."='".$_POST['valor']."'";
	$rc = mysql_query($query);
	if($rc)	{
		/*/Remove a disponibilidade*/
		$query = "delete from disponibilidade where om_codom=".$_POST['valor'];
		$resultado = mysql_query($query);
		/*/Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('Removeu '.$_POST['descricao']);
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Organização Militar removida com sucesso.");
	}
	else
	{ 
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro ao remover a Unidade ".$_POST['descricao'].".");
		/*/Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('Tentou remover '.$_POST['descricao']);
	}
 	echo "<p><center><a href='index.php?nomeArquivo=cons_om.php'><u><b>CONTINUAR</b></u></a></center>";
 	
?>	


