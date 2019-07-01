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
	$query = "delete from ".$_POST['tab']." where ".$_POST['ch']."='".$_POST['valor']."'";

	$rc = mysql_query($query);
	if($rc){
		//Registra atividade executada pelo usuário para auditoria
		registrarAtividade('Removeu '.$_POST['descricao']);
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Viatura removida com sucesso.");
	}
	else{
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br> Erro ao remover ".$_POST['descricao'].".");
		if(mysql_errno()==1451) {
			echo "<p> A viatura já possui abastecimentos realizados e não poderá ser excluída !";
			$_POST['codom']=$user[6];
		}
		//Registra atividade executada pelo usuário para auditoria
		registrarAtividade('Tentou remover '.$_POST['descricao']);
	}

echo "<p>";
echo "<center><a href='index.php?nomeArquivo=cons_viatura.php&listar=".$_POST['valor1']."#".$_POST['ancora']."'><u><b>CONTINUAR</b></u></a></center>";
?>	

