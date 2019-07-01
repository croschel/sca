<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
	//Conexão ao Banco de Dados
	include "conexao.php";
	//Conexão com arquivo para registrar atividade
	include "atividade.php";
	$query = "delete from ".$_POST['tab']." where ".$_POST['ch']."='".$_POST['valor']."'";

	$rc = mysql_query($query);
	if($rc)	{
		//Registra atividade executada pelo usuário para auditoria
		registrarAtividade('Removeu '.$_POST['descricao']);
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Usuário removido com sucesso! ");
	}
	else{
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>Erro ao tentar remover ".$_POST['descricao']."! ");
		if(mysql_errno()==1451) {
			echo "<p> Usuário já possui atividade registrada no sistema e não poderá ser excluído !";
		}
		//Registra atividade executada pelo usuário para auditoria
		registrarAtividade('Tentou remover '.$_POST['descricao']);
	}

echo "<p>";
echo "<center><a href='index.php?nomeArquivo=cons_usuario.php&listar=".$_POST['ancora']."#".$_POST['ancora']."'><u><b>CONTINUAR</b></u></a></center>";

?>