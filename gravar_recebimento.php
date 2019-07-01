<?php
	include "verifica_sessao.php";
	/*/Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	$_POST['valor']= str_replace(',','.',$_POST['valor']);
	$posto = explode("|",$_POST['reservatorio']);
	$_POST['reservatorio'] = $posto[0];
	/*/Testa a operação se operacao for igual a 1 é insersão se não é alteração*/
	if ($_POST['operacao']==1) {
      $dt_nota = substr($_POST['dt_nota'],6,4)."-".substr($_POST['dt_nota'],3,2)."-".substr($_POST['dt_nota'],0,2);
		$query = "insert into recebimento (nr_nota_fiscal, combustivel_codigo, dt_rec, 
						qtd_rec, nr_pedido, nr_remessa, 
						reservatorio_codigo, oc, valor, contrato)
		values('".$_POST['nota_fiscal']."','".$_POST['combustivel']."','".substr($_POST['dt_nota'],6,4)."-".substr($_POST['dt_nota'],3,2)."-".substr($_POST['dt_nota'],0,2)."',
					'".$_POST['qtd_rec']."','".$_POST['nr_pedido']."','".$_POST['nr_remessa']."',
					'".$_POST['reservatorio']."','".$_POST['oc']."','".$_POST['valor']."','".$_POST['contrato']."')";
		$rc = mysql_query($query);
		if($rc)	{
			/*Insere o combustivel na cota do OC*/	
			$qry="SELECT ifnull(max(nr_repasse),".date(Y)."1) FROM estoque where nr_repasse like '".date(Y)."%'";
			$res = mysql_query($qry);
			$nr = mysql_fetch_row($res);
			$nr_repasse=date(Y).(substr($nr[0],4)+1);		
			$query2 = "insert  into estoque (nr_repasse, dt_repasse, om_codom, nr_nota_fiscal, combustivel_codigo, reservatorio_codigo, cod_tp_cota, nr_remessa, qtd_destinada, qtd_atual, obs)
				values($nr_repasse,'".substr($_POST['dt_nota'],6,4)."-".substr($_POST['dt_nota'],3,2)."-".substr($_POST['dt_nota'],0,2)." ".date('H:i')."','".$_POST['oc']."','".$_POST['nota_fiscal']."',
				'".$_POST['combustivel']."','".$_POST['reservatorio']."',1,'".$_POST['nr_remessa']."','".$_POST['qtd_rec']."','".$_POST['qtd_rec']."','Recebimento da Nota Fiscal nº ".$_POST['nota_fiscal']."')";
			$rc = mysql_query($query2);
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('CADASTROU A NOTA FISCAL Nº '.$_POST['nota_fiscal'].' de '.$_POST['dt_nota']);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Recebimento cadastrado com sucesso! ");
		}
		else{
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro durante o processamento do cadastro! ");
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('TENTOU ALTERAR A NOTA FISCAL Nº '.$_POST['nota_fiscal'].' de '.$_POST['dt_nota']);
		}
	}
	elseif ($_POST['operacao']==2){
      $dt_rec = substr($_POST['dt_rec'],6,4)."-".substr($_POST['dt_rec'],3,2)."-".substr($_POST['dt_rec'],0,2);
		$query = "update recebimento set dt_rec='".$dt_rec."', qtd_rec='".$_POST['qtd_rec'] ."', nr_pedido='".$_POST['nr_pedido'] ."', 
						nr_remessa='".$_POST['nr_remessa'] ."', oc='".$_POST['oc'] ."', valor='".$_POST['valor'] ."', contrato='".$_POST['contrato'] ."' 
						where nr_nota_fiscal='".$_POST['nr_nota_fiscal']."' and combustivel_codigo='".$_POST['combustivel']."'";

		$rc = mysql_query($query);
		if($rc)	{
			
			
			if ($_POST['oc_ant'] != $_POST['oc']){
				$query3 = "update estoque set om_codom='".$_POST['oc']."' where recebimento_combustivel_codigo='".$_POST['cod_comb']."' and recebimento_nr_nota_fiscal='".$_POST['nr_nota']."' and om_codom='".$_POST['oc_ant']."'";

				if ($_POST['qtd_rec'] > $_POST['qtd_anterior']){
					/*Atualiza a cota do OC*/
					$query2 = "update cotas set qtd_destinada=".($_POST['qtd_rec']).", qtd_atual=".($_POST['qtd_disp']+($_POST['qtd_rec']-$_POST['qtd_anterior']))." where recebimento_combustivel_codigo='".$_POST['cod_comb']."' and recebimento_nr_nota_fiscal='".$_POST['nr_nota']."' and om_codom='".$_POST['oc']."'";
					$rc = mysql_query($query2);
				}
				elseif ($_POST['qtd_rec'] < $_POST['qtd_anterior']){
					/*Atualiza a cota do OC*/
					$query2 = "update cotas set qtd_destinada=".($_POST['qtd_anterior']-$_POST['qtd_rec']).", qtd_atual=".($_POST['qtd_disp']-($_POST['qtd_anterior']-$_POST['qtd_rec']))." where recebimento_combustivel_codigo='".$_POST['cod_comb']."' and recebimento_nr_nota_fiscal='".$_POST['nr_nota']."' and om_codom='".$_POST['oc']."'";
					$rc = mysql_query($query2);
				}
				
			} else {
				if ($_POST['qtd_rec'] > $_POST['qtd_anterior']){
					/*Atualiza a cota do OC*/
					$query2 = "update estoque set qtd_destinada=".($_POST['qtd_destinada']+($_POST['qtd_rec']-$_POST['qtd_anterior'])).", qtd_atual='".($_POST['qtd_disp']+($_POST['qtd_rec']-$_POST['qtd_anterior']))."' where combustivel_codigo='".$_POST['cod_comb']."' and nr_nota_fiscal='".$_POST['nr_nota']."' and om_codom='".$_POST['oc']."' and cod_tp_cota=0";

					$rc = mysql_query($query2);
				}
				elseif ($_POST['qtd_rec'] < $_POST['qtd_anterior']){
	
					/*Atualiza a cota do OC*/
					$query2 = "update estoque set qtd_destinada='".($_POST['qtd_destinada']-($_POST['qtd_anterior']-$_POST['qtd_rec']))."', qtd_atual='".($_POST['qtd_disp']-($_POST['qtd_anterior']-$_POST['qtd_rec']))."' where combustivel_codigo='".$_POST['cod_comb']."' and nr_nota_fiscal='".$_POST['nr_nota']."' and om_codom='".$_POST['oc']."' and cod_tp_cota=0";

					$rc = mysql_query($query2);
				}
			}
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('ALTEROU A NOTA FISCAL Nº '.$_POST['nr_nota_fiscal'].' de '.$_POST['dt_rec']);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Recebimento alterado com sucesso! ");
		}
		else{
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro durante a alteração da Viatura! ");
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('TENTOU ALTERAR A NOTA FISCAL Nº '.$_POST['nr_nota_fiscal'].' de '.$_POST['dt_rec']);
		}
 	}
 	echo "<p>";
	echo "<center><a href='index.php?nomeArquivo=cons_recebimento.php'><u><b>VISUALIZAR</b></u></a></center>";

?>

