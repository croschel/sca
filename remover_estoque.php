<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
	if(empty($_SESSION['usuario']))
		include "verifica_sessao.php";
	/*Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	/*Seleciona a quantidade distribuida na cota do OC*/
	$query="SELECT qtd_destinada, qtd_atual, nr_repasse, om_codom FROM estoque 
		WHERE combustivel_codigo='".$_POST['valor3']."' and nr_nota_fiscal='".$_POST['valor2']."' and om_codom='".$_SESSION['codom']."' ";
	$result = mysql_query($query);
	$qtd_disp = mysql_fetch_row($result);
	
	//Atualiza o estoque da OM
	$query = "update ".$_POST['tab']." set qtd_destinada=".round(($_POST['qtd']-$_POST['qtd_atual']),3).", qtd_atual=0 
					where ".$_POST['ch']."='".$_POST['valor']."' and ".$_POST['ch1']."='".$_POST['valor1']."'";

	$rc = mysql_query($query);
	if($rc)	{
		//Atualiza a cota do OC
echo		"<br>".$qrya = "update estoque set qtd_destinada=".($_POST['qtd_atual']+$qtd_disp[0]).", qtd_atual=".($_POST['qtd_atual']+$qtd_disp[1])." 
							where nr_repasse='".$qtd_disp[2]."' and om_codom='".$_SESSION['codom']."'";
		$rc = mysql_query($qrya);

		/*Registra historico repasse da OM*/ 
		$qrya = "insert into historico_repasse (dt_registro, nr_repasse, om_codom, registro, quantidade, om_destino)
						values(now(), '$qtd_disp[2]', '$qtd_disp[3]', 'Cancelamento de Repasse', '".$_POST['qtd_atual']."', '".$_POST['valor1']."')";
		$resultado = mysql_query($qrya);

		/*Registra historico repasse do OC*/ 
echo		"<br>".$qrya = "insert into historico_repasse (dt_registro, nr_repasse, om_codom, registro, quantidade, om_destino)
						values(now(), '".$_POST['valor']."', '".$_POST['valor1']."', 'Cancelamento de Repasse', '".$_POST['qtd_atual']."', '".$_POST['valor1']."')";
		$resultado = mysql_query($qrya);

		
		//Identifica o código do crédito da OM
		$query="SELECT d.cod_credito, d.nr_repasse, d.om_codom, d.qtd_repassada, c.qtd_atual, c.qtd_destinada 
							FROM destino_credito d, credito c 
							WHERE c.cod_credito=d.cod_credito 
								and c.om_codom=d.om_codom
								and d.nr_repasse='".$_POST['valor']."' 
								and d.om_codom='".$_POST['valor1']."' ";
		$result = mysql_query($query);
		$dest_cred = mysql_fetch_row($result);
		
		//Se o sistema aceita abastecimento com saldo negativo
		if($abast_sem_saldo=='s') {
			//Atualiza o destino do crédito da OM
echo			"<br>".$qrya = "update destino_credito set qtd_repassada=".($dest_cred[3] - $_POST['qtd_atual'])." 
							where nr_repasse='".$_POST['valor']."' and nr_repasse='".$dest_cred[1]."' and om_codom='".$_POST['valor1']."' ";
			$rc = mysql_query($qrya);
	
			//Atualiza o destino do crédito da OM
echo			"<br>".$qrya = "update credito set qtd_atual=".($dest_cred[4] + $_POST['qtd_atual'])." 
							where cod_credito='".$dest_cred[0]."' and om_codom='".$_POST['valor1']."' ";
			$rc = mysql_query($qrya);
		}
		
		/*Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('Removeu '.$_POST['descricao']);
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Distribuição de cota removida com sucesso.");
	}
	else{
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro ao remover ".$_POST['descricao'].".");
		/*Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('Tentou remover '.$_POST['descricao']);
	}

	echo "<p>";
	echo "<center><a href='index.php?nomeArquivo=gerenciar_cotas.php&nota_fiscal=".$_POST['valor2']."&combustivel=".$_POST['valor3']."'><u><b>CONTINUAR</b></u></a></center>";
	
?>	

