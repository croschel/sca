<?php
	include "verifica_sessao.php";
	/*Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	/*Testa a operação se operacao for igual a 1 é insersão se não é alteração*/
	if ($_POST['operacao']==1) {
		$query = "insert into reservatorio (codigo, om_codom, combustivel_codigo, capacidade, situacao, evaporacao)
		values('".$_POST['cod_reservatorio']."','".$_POST['codom']."','".$_POST['combustivel']."','".$_POST['capacidade']."','".$_POST['situacao']."','".(str_replace(',', '.', $_POST['taxa']))."')";
		$rc = mysql_query($query);
		if($rc)	{
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('CADASTROU RESERVATÓRIO '.$_POST['cod_reservatorio']);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Reservatório cadastrado com sucesso! ");
		}
		else{
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇãO! Erro durante o processamento do cadastro! ");
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('TENTOU CADASTRAR RESERVATÓRIO '.$_POST['cod_reservatorio']);
		}
	}
	else{
		$query = "update reservatorio set om_codom='".$_POST['codom']."', combustivel_codigo='".$_POST['combustivel']."', capacidade='".$_POST['capacidade']."', situacao='".$_POST['situacao']."', evaporacao='".(str_replace(',', '.', $_POST['taxa']))."' where codigo='".$_POST['cod_reservatorio']."'";
		$rc = mysql_query($query);
		if($rc)	{
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('ALTEROU O RESERVATòRIO Nº '.$_POST['cod_reservatorio']);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Reservatório alterado com sucesso! ");
		}
		else{
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro durante a alteração do Reservatório! ");
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('TENTOU ALTERAR O RESERVATÓRIO '.$_POST['cod_reservatorio']);
		}
    	}
    echo "<p>";
    echo "<center><a href='index.php?nomeArquivo=cons_reservatorio.php'><u><b>VISUALIZAR</b></u></a></center>";

?>


