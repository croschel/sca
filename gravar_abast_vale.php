<?php
	include "verifica_sessao.php";

	/*Conexao com arquivo para registrar atividade*/
	include "atividade.php";
	
	/*Converte data do abastecimento*/
	$data_autz = substr($_POST['dt_abast'],6,4)."-".substr($_POST['dt_abast'],3,2)."-".substr($_POST['dt_abast'],0,2);

	/*Extrai cod_reservatorio e cod_tp_cota*/
	$reserv = explode(",", $_POST['reservatorio']);

	$query = "insert into abastecimento (dt_abastecimento, viatura_eb, reservatorio_codigo, usuario_idt, usuario_autz, dt_autorizacao, qtd_autorizada, qtd_abastecida, motorista, od_atual, missao, cod_tp_cota, cota_om, tp_abastecimento) 
		values(now(), '".$_POST['ebv']."', '".$reserv[0]. "', '".$_SESSION['idt']. "', '".$_POST['mil_autz']. "', '".$data_autz."', '".$_POST['qtd_autz']."', '".$_POST['qtd_comb']."', upper('".$_POST['motorista']."'), '".$_POST['odometro']."', '".$_POST['missao']."', '".$reserv[1]. "', '".$reserv[2]. "', 'Abastecimento com Vale')";

	$rc = mysql_query($query);
	if($rc)	{
		/*Atualiza a quantidade atual no estoque da OM*/
		$qry="select nr_repasse, om_codom, qtd_atual, dt_repasse 
				from estoque 
				where reservatorio_codigo='".$reserv[0]."'
					and om_codom='".$reserv[2]."'
					and cod_tp_cota='".$reserv[1]."' 
					and qtd_atual>0
				order by dt_repasse";
		$result1 = mysql_query($qry);
		$num_lin=0;
		if(mysql_num_rows($result1)>0) { /* Se existir cotas*/
			while(($_POST['qtd_comb']>0) and (mysql_num_rows($result1)>=$num_lin)) { /*Enquanto não for debitado das cotas todo o valor abastecido e o numermo de cotas for maior ou igual ao contador*/
				$cota = mysql_fetch_array($result1, MYSQL_NUM);

				if ($cota[2]>=$_POST['qtd_comb']){
					/*Atualiza o estoque da OM*/
					$query2 = "update estoque set qtd_atual='".($cota[2]-$_POST['qtd_comb'])."' where nr_repasse='".$cota[0]."' and om_codom='$reserv[2]'";
					$resultado = mysql_query($query2);
					/*Registra historico abastecimento */ 
					$query3 = "insert into historico_abastecimento (nr_repasse, om_codom, dt_abastecimento, viatura_eb, qtd_abastecida)
									values('".$cota[0]."', '$reserv[2]', now(), '".$_POST['ebv']."', '".$_POST['qtd_comb']."')";
					$resultado = mysql_query($query3);
					$_POST['qtd_comb']=0;				
				}else{
					/*Atualiza o estoque da OM*/
					$query2 = "update estoque set qtd_atual='0' where nr_repasse='".$cota[0]."' and om_codom='$reserv[2]' ";				
					$resultado = mysql_query($query2);
					/*Registra historico abastecimento*/
					$query3 = "insert into historico_abastecimento (nr_repasse, om_codom, dt_abastecimento, viatura_eb, qtd_abastecida)
									values('".$cota[0]."', '$reserv[2]', now(), '".$_POST['ebv']."', '$cota[2]')";
					$resultado = mysql_query($query3);
					$_POST['qtd_comb']=$_POST['qtd_comb']-$cota[2];				
				}
				$resultado = mysql_query($query2);
					
				$num_lin++;
			}
		}
		/*/Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('Abasteceu a viatura '.$_POST['ebv'].', mediante vale.');
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Abastecimento realizado com sucesso! ");

	} else{
		/*/Registra atividade executada pelo usuario para auditoria*/
		registrarAtividade('Tentou registrar o abastecimento da viatura '.$_POST['eb'].' mediante vale.');
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro durante o processamento do registro! ");
	}

	echo "<p><a href='index.php?nomeArquivo=abast_vale.php'>CONTINUAR</b></a></center>";

?>
