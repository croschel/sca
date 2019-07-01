
<?php
	include "verifica_sessao.php";

	/*Conexão com arquivo para registrar atividade*/
	include "atividade.php";

	/*Testa a operação se operacao for igual a 1 é insersão se não é alteração*/
	if ($_POST['operacao']==1) {
	
		/*Seleciona o proximo número de estoque*/
		$qry="SELECT (ifnull(max(cod_credito),".date(Y)."0) + 1) FROM credito where om_codom='".$_POST['codom']."' and cod_credito like '".date(Y)."%'";
		$cons_cred = mysql_query($qry);
		$cred = mysql_fetch_row($cons_cred);
		$query = "insert credito (cod_credito, om_codom, combustivel_codigo, cod_tp_cota, desc_credito, qtd_destinada, qtd_atual)
		values($cred[0] ,'".$_POST['codom']."','".$_POST['combustivel']."','".$_POST['cota']."','".$_POST['desc_credito']."','".$_POST['qtd_dest']."','".$_POST['qtd_dest']."')";
		$rc = mysql_query($query);

		if($rc)	{
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('Cadastrou o Crédito de '.$_POST['qtd_dest'].' Lts de '.$_POST['combustivel'].' para o(a) '.$row[0]);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Distribuição realizada com sucesso! ");
		}
		else{
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro durante o processamento da distribuição! ");

			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('TENTOU Cadastrar o Crédito de '.$_POST['qtd_dest'].' Lts de '.$_POST['combustivel'].' para o(a) '.$row[0]);
		}
		
	} elseif ($_POST['operacao']==2) {
	
		$query = "update credito set om_codom='".$_POST['codom']."', combustivel_codigo='".$_POST['combustivel']."', 
						cod_tp_cota='".$_POST['cota']."', desc_credito='".$_POST['desc_credito']."', qtd_destinada='".$_POST['qtd_dest']."', qtd_atual='".$_POST['qtd_atual']."'
					where cod_credito='".$_POST['cod_credito']."' and om_codom='".$_POST['codom_ant']."'";
		$rc = mysql_query($query);

		if($rc)	{
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('Alterou o Crédito de '.$_POST['qtd_dest'].' Lts de '.$_POST['combustivel'].' para o(a) '.$row[0]);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Distribuição realizada com sucesso! ");
		}
		else{
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro durante o processamento da distribuição! ");

			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('TENTOU Alterou o Crédito de '.$_POST['qtd_dest'].' Lts de '.$_POST['combustivel'].' para o(a) '.$row[0]);
		}
		
	}

   echo "<p> <center><a href='index.php?nomeArquivo=cons_credito.php'><u><b>VISUALIZAR</b></u></a></center>";

?>

