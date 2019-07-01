<?php
	include "verifica_sessao.php";
	/*/Conexão ao Banco de Dados*/
	include "conexao.php";
	/*/Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	$query="select idt from usuario where idt='".$_SESSION['idt']."' and senha='".crypt($_POST['senha_atual'],substr(md5($_POST['senha_atual']), 0, 2))."'";
	$result = mysql_query($query,$conexao);
	$row = mysql_fetch_row($result);
	if (($row[0] != '')&&($_POST['senha']==$_POST['confirma_senha'])){
		$query1 = "update usuario set senha='".crypt($_POST['senha'],substr(md5($_POST['senha']), 0, 2))."' where idt='".$_SESSION['idt']."'";
		$rc = mysql_query($query1);
		if($rc) {
			/*/Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('ALTEROU A SUA SENHA.');
			echo ("<br>ATENÇÃO! Senha alterada com sucesso! ");
		} else {
			/*/Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('TENTOU ALTERAR A SENHA.');
			echo "<script>alert('ATENÇÃO! Senha atual não confere ou senha diferente da confirmação !');</script>";
			echo "<meta http-equiv=REFRESH Content='0;URL=index.php?nomeArquivo=alterar_senha.php'>";	
		}	
	}
	else{
		/*/Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('TENTOU ALTERAR A SENHA.');
		
		echo "<script>alert('ATENÇÃO! Senha atual não confere ou senha diferente da confirmação !');</script>";
		echo "<meta http-equiv=REFRESH Content='0;URL=index.php?nomeArquivo=alterar_senha.php'>";
		
	}
echo "<p>";
echo "<center><a href='index.php?nomeArquivo=principal.php'><u><b>CONTINUAR</b></u></a></center>";
?>
