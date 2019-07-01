<?php
	include "verifica_sessao.php";

	/*Conexão com arquivo para registrar atividade*/
	include "atividade.php";

	/*Testa a operação se operacao for igual a 1 é insersão se não é alteração*/
	if ($_POST['operacao']==1) {
		$query = "insert into abastecimento (dt_abastecimento, viatura_eb, reservatorio_codigo, cota_om, usuario_autz, dt_autorizacao, 
								qtd_autorizada, qtd_abastecida, motorista, missao, od_atual, local, tp_abastecimento, cod_tp_cota) 
			values('".substr($_POST['dt_abast'],6,4)."-".substr($_POST['dt_abast'],3,2)."-".substr($_POST['dt_abast'],0,2)."', '".$_POST['eb']."', null, null, 
			'".$_SESSION['idt']. "',now(), '".$_POST['qtd_abast']."', '".$_POST['qtd_abast']."', upper('".$_POST['motorista']."'), '".$_POST['missao']."', 
			'".$_POST['odometro']."', '".$_POST['local']."', '".$_POST['tp_abastecimento']."', '".$_POST['cota']."')";

		$rc = mysql_query($query);
		if($rc)	{
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('Registrou o abastecimento fora do sistema da viatura '.$_POST['eb'].' do(a) '.$_SESSION['unidade']);
			
			echo "<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>Abastecimento registrado com sucesso.";
		}
		else{
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('Tentou registrar o abastecimento fora do sistema da viatura '.$_POST['eb'].' do(a) '.$_SESSION['unidade']);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! <br><br>Erro durante o processamento do registro! ");
		}
	}

echo "<p><a href='index.php?nomeArquivo=abast_fora_sistema.php'>VISUALIZAR</b></a></center>";


?>
