<?php
	include "verifica_sessao.php";
	//Conexão ao Banco de Dados
	include "conexao.php";
	//Conexão com arquivo para registrar atividade
	include "atividade.php";
	//Converte data do abastecimento
	$data_autz = substr($_POST['dt_abast'],6,4)."-".substr($_POST['dt_abast'],3,2)."-".substr($_POST['dt_abast'],0,2);
	//Testa a operação se operacao for igual a 1 é insersão se não é alteração
	$query = "insert into abastecimento (dt_abastecimento, viatura_eb, disponibilidade_reservatorio_codigo, disponibilidade_om_codom, usuario_idt, usuario_autz, dt_autorizacao, qtd_autorizada, qtd_abastecida, motorista, od_atual, missao) 
		values(now(), '".$_POST['ebv']."', '".$_POST['reservatorio']. "', '".$_POST['codom']. "', '".$_SESSION['idt']. "', '".$_POST['mil_autz']. "', '".$data_autz."', '".$_POST['qtd_autz']."', '".$_POST['qtd_abast']."', upper('".$_POST['motorista']."'), '".$_POST['odometro']."', '".$_POST['missao']."')";
	$rc = mysql_query($query);
	if($rc)	{
		//Atualiza a quantidade disponível
		$busca="select qtd_atual from disponibilidade
		where om_codom='".$_POST['codom']."'and reservatorio_codigo='".$_POST['reservatorio']."'";
		$result = mysql_query($busca,$conexao);
		$qtd_atual = mysql_fetch_row($result);
		$query1 = "update disponibilidade set qtd_atual='".($qtd_atual[0]-$_POST['qtd_abast'])."'
			where om_codom='".$_POST['codom']."' and reservatorio_codigo='".$_POST['reservatorio']."'";
		$resultado = mysql_query($query1);
		//Registra atividade executada pelo usuário para auditoria
		registrarAtividade("Registrou o abastecimento da viatura ".$_POST['ebv'].", na cota do(a) ".$_POST['sigla']);
		echo ("<br>ATENÇÃO! Abastecimento registrado com sucesso! ");
	}
	else{
		//Registra atividade executada pelo usuário para auditoria
		registrarAtividade("Tentou autorizar o abastecimento da viatura ".$_POST['ebv'].", na cota do(a) '".$_POST['sigla']."'.");
		echo ("<br>ATENÇÃO! Erro durante o processamento do registro! ");
	}

?>
<p>
<a href="index.php?nomeArquivo=cota_outra_om.php">CONTINUAR</b></a></center>
