
<?php
	include "verifica_sessao.php";

	/*Conexão com arquivo para registrar atividade*/
	include "atividade.php";

	/*Testa a operação se operacao for igual a 1 é insersão se não é alteração*/
	if ($_POST['operacao']==1) {
		
		if($_POST['qtd_atd']>$_POST['qtd_atual']) {
			$_POST['qtd_atd']=$_POST['qtd_atual'];
		}
		
	
		/*Seleciona o proximo número de estoque*/
		$qry="SELECT ifnull(max(nr_repasse),".date(Y)."0) FROM estoque where om_codom='".$_POST['codom']."' and nr_repasse like '".date(Y)."%'";
		$res = mysql_query($qry);
		$nr = mysql_fetch_row($res);
		$nr_repasse=date(Y).(substr($nr[0],4)+1);
		$qtd_atend=$_POST['qtd_atd'];
		$qtd_rec=$_POST['qtd_atd'];
		$_POST['qtd_atd'] = str_replace(',', '.', $_POST['qtd_atd']);
		$query = "insert estoque (nr_repasse, om_codom, dt_repasse, combustivel_codigo, nr_nota_fiscal, reservatorio_codigo, qtd_destinada, nr_remessa, cod_tp_cota, qtd_atual, obs)
		values($nr_repasse ,'".$_POST['codom']."', now(),'".$_POST['cod_comb']."','".$_POST['nr_nota']."','".$_POST['reservatorio']."','".$_POST['qtd_atd']."','".$_POST['nr_remessa']."','".$_POST['tp_cota']."','".$_POST['qtd_atd']."','".$_POST['obs']."')";
		$rc = mysql_query($query);
		if($rc)	{
			
			/*Consulta o estoque do OC*/
			$qry="select nr_repasse, om_codom, qtd_destinada, qtd_atual, dt_repasse, nr_nota_fiscal, combustivel_codigo
				from estoque 
				where reservatorio_codigo='".$_POST['reservatorio']."'
					and om_codom='".$_SESSION['codom']."'
					and nr_nota_fiscal='".$_POST['nr_nota']."'
					and qtd_atual>0
				order by dt_repasse";
			$result1 = mysql_query($qry);
			$num_lin=0;
			$qtd_atd = $_POST['qtd_atd'];		
			while(($_POST['qtd_atd']>0) and (mysql_num_rows($result1)>=$num_lin)) { /*Enquanto não for debitado das cotas todo o valor repassado e o numermo de cotas for maior ou igual ao contador*/
				$cota = mysql_fetch_array($result1, MYSQL_NUM);
				
				if ($cota[2]>=$_POST['qtd_atd']){
					//Qtd para o registro do historico
					$qtd_hist = $_POST['qtd_atd'];
					/*Diminui a cota do OC*/
 					$query2 = "update estoque set qtd_destinada=".($cota[2]-$_POST['qtd_atd']).", qtd_atual=".($cota[3]-$_POST['qtd_atd'])." where nr_repasse='".$cota[0]."' and om_codom='".$_SESSION['codom']."' and nr_nota_fiscal='".$_POST['nr_nota']."'";
					$rc = mysql_query($query2);
					$_POST['qtd_atd'] = 0;
					
				} else {
					/*Diminui a cota do OC*/
 					$query2 = "update estoque set qtd_destinada=".($cota[2]-$cota[3]).", qtd_atual=0 where nr_repasse='".$cota[0]."' and om_codom='".$_SESSION['codom']."' and nr_nota_fiscal='".$_POST['nr_nota']."'";
					$rc = mysql_query($query2);				
					//Qtd para o registro do historico
					$qtd_hist = $cota[3];
					$_POST['qtd_atd']=$_POST['qtd_atd']-$cota[3];		

				}				
				
				//Registra historico repasse na OM que pagou o combustivel 
				$query3 = "insert into historico_repasse (dt_registro, nr_repasse, om_codom, registro, quantidade, om_destino)
							values(now(), '$cota[0]', '$cota[1]', 'Repasse', '$qtd_hist', '".$_POST['codom']."')";
				$resultado = mysql_query($query3);
				
				//Registra historico repasse na OM destino 
				$query3 = "insert into historico_repasse (dt_registro, nr_repasse, om_codom, registro, quantidade, om_destino)
							values(now(), '$nr_repasse', '".$_POST['codom']."', 'Recebimento', '$qtd_hist', '$cota[1]')";
				$resultado = mysql_query($query3);

				//Se for seleciona para registrar em um crédito
				if($_POST['credito']) {				
					//Registra destino do crédito da OM receptora do combustível 
					$query4 = "insert into destino_credito (cod_credito, nr_repasse, om_codom, dt_repasse, qtd_repassada)
								values( '".$_POST['credito']."', '$nr_repasse', '".$_POST['codom']."', now(), '".$qtd_atd."')";
					$resultado = mysql_query($query4);
					if($resultado) {
						//Diminui o crídito atual da OM receptora do combustível
						$query5 = "update credito set qtd_atual=".($_POST['qtd_max']-$qtd_atd)." where cod_credito='".$_POST['credito']."'  and om_codom='".$_POST['codom']."' ";
						$rc = mysql_query($query5);
					}
				}
				
				//Se o sistema aceita abastecimento com saldo negativo
				if($abast_c_saldo_neg=='s') {
					$qry = "select a.dt_abastecimento, a.viatura_eb, a.qtd_abastecida - ifnull(h.qtd_abastecida ,0)
							from abastecimento a LEFT JOIN 
									(SELECT dt_abastecimento, viatura_eb, sum(qtd_abastecida) qtd_abastecida 
										FROM `historico_abastecimento` 
										GROUP BY dt_abastecimento, viatura_eb
									) h on (a.dt_abastecimento=h.dt_abastecimento and a.viatura_eb=h.viatura_eb)
							where a.qtd_abastecida > ifnull(h.qtd_abastecida ,0)
								and a.cota_om='".$_POST['codom']."'
								and a.cod_tp_cota='".$_POST['tp_cota']."' 
								and a.reservatorio_codigo='".$_POST['reservatorio']."'
							order by a.dt_abastecimento ";
					$result2 = mysql_query($qry);
					$num_lin=1;
					$qtd_rec;
					mysql_num_rows($result2);
					while(($qtd_rec > 0) and (mysql_num_rows($result2)>=$num_lin)) {
						$abast = mysql_fetch_array($result2, MYSQL_NUM);
						if($qtd_rec < $abast[2]) {
							$abast[2] = $qtd_rec ;
						}

						$query1 = "insert into historico_abastecimento (nr_repasse, om_codom, dt_abastecimento, viatura_eb, qtd_abastecida)
								values('$nr_repasse', '".$_POST['codom']."', '$abast[0]', '$abast[1]', '$abast[2]')";
							$resultado = mysql_query($query1);
	
							$qtd_rec = $qtd_rec-$abast[2];

						$query2 = "update estoque set qtd_atual='$qtd_rec'
								where nr_repasse='$nr_repasse' and om_codom='".$_POST['codom']."' ";
							$resultado = mysql_query($query2);

						$num_lin++;
						
					}
				}

			}
			
			/*Seeleciona a sigla da OM atendida*/
			$busca="select sigla from om
				where codom='".$_POST['codom']."'";
			$result = mysql_query($busca,$conexao);
			$row = mysql_fetch_row($result);

	
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('DISTRIBUIU '.$qtd_atend.' Lts de '.$_POST['combustivel'].' da NF nº '.$_POST['nr_nota'].' para o(a) '.$row[0]);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Distribuição realizada com sucesso! ");
		}
		else{
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro durante o processamento da distribuição! ");

			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('TENTOU DISTRIBUIR '.$_POST['qtd_atd'].' Lts de '.$_POST['combustivel'].' da NF nº '.$_POST['nr_nota'].' para o(a) '.$row[0]);
		}
	}

   echo "<p> <center><a href='index.php?nomeArquivo=gerenciar_cotas.php&combustivel=".$_POST['cod_comb']."&nota_fiscal=".$_POST['nr_nota']."'><u><b>VISUALIZAR</b></u></a></center>";

?>

