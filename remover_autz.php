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
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! <br><br>Registro removido com sucesso.");
	}
	else{
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! <br><br>Erro ao remover ".$_POST['descricao'].".");
		/*Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('Tentou remover '.$_POST['descricao']);
	}

	if($_POST['valor2']=='1') {
		echo "<p><center><a href='index.php?nomeArquivo=autz_abast.php'><u><b>CONTINUAR</b></u></a></center>";
	}	elseif($_POST['valor2']=='2') {
		echo "<p><center><a href='index.php?nomeArquivo=abast_vtr_outra_om.php'><u><b>CONTINUAR</b></u></a></center>";
	} elseif($_POST['valor2']=='3') {
		echo "<p><center><a href='index.php?nomeArquivo=abast_fora_sistema.php'><u><b>CONTINUAR</b></u></a></center>";
	} elseif($_POST['valor2']=='4') {
		echo "<p><center><a href='index.php?nomeArquivo=abast_vtr_transito.php'><u><b>CONTINUAR</b></u></a></center>";
	} elseif($_POST['valor2']=='5') {
		echo "<p><center><a href='index.php?nomeArquivo=autz_carr_cisterna.php'><u><b>CONTINUAR</b></u></a></center>";
	} 
?>	


