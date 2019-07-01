<?php
	include "verifica_sessao.php";

	/*/Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	if($_POST['senha'] != crypt($_POST['senha1'],substr(md5($_POST['senha1']), 0, 2))) {
		echo "<script>alert('ATENÇÃO! Senha do Abastecedor Incorreta !');</script>";
		echo "<meta http-equiv=REFRESH Content='0;URL=index.php?nomeArquivo=retorno_cisterna.php&eb=".$_POST['eb']."&dt_abast=".$_POST['dtabast']."'>";
	}
	$query = "update abastecimento set qtd_autorizada='".$_POST['qtd_abast']."', qtd_abastecida='".$_POST['qtd_abast']."', usuario_autz='".$_SESSION['idt']."', usuario_idt='".$_POST['idt']."' 
				where dt_abastecimento='".$_POST['dtabast']."' and viatura_eb='".$_POST['eb']."'";

	$rc = mysql_query($query);

	$qtd = $_POST['qtd_devol'];

	if($rc) {
			/*Atualiza a quantidade atual no estoque da OM*/
			"<br>".$qry="select e.nr_repasse, e.om_codom, h.qtd_abastecida, h.dt_abastecimento, h.viatura_eb, e.qtd_atual, e.* 
					from estoque e, historico_abastecimento h
					where e.nr_repasse=h.nr_repasse
						and e.om_codom=h.om_codom
						and h.dt_abastecimento='".$_POST['dtabast']."'
						and h.viatura_eb='".$_POST['eb']."'
					order by e.nr_repasse desc";
			$result1 = mysql_query($qry);
			$num_lin=0;
			if(mysql_num_rows($result1)>0) { /* Se existir cotas*/
				while(($_POST['qtd_devol']>0) and (mysql_num_rows($result1)>=$num_lin)) { /*Enquanto não for debitado das cotas todo o valor abastecido e o numermo de cotas for maior ou igual ao contador*/
					$cota = mysql_fetch_array($result1, MYSQL_NUM);
					

					if ($_POST['qtd_devol'] >= $cota[2]){
						//Atualiza o estoque da OM
						$query2 = "update estoque set qtd_atual='".($cota[5] + $cota[2])."' where nr_repasse='".$cota[0]."' and om_codom='".$cota[1]."'";

						/*Registra historico abastecimento*/
						$query3 = "update historico_abastecimento set qtd_abastecida='0'
										where nr_repasse='".$cota[0]."' and om_codom='".$cota[1]."' and dt_abastecimento='".$_POST['dtabast']."' and viatura_eb='".$_POST['eb']."'";
						$_POST['qtd_devol']=$_POST['qtd_devol']-$cota[2];				
					}else{
						//Atualiza o estoque da OM
						$query2 = "update estoque set qtd_atual='".($cota[5] + $_POST['qtd_devol'])."' where nr_repasse='".$cota[0]."' and om_codom='".$cota[1]."'";

						//Atualiza historico abastecimento 
						$query3 = "update historico_abastecimento set qtd_abastecida='".($cota[2] - $_POST['qtd_devol'])."'
										where nr_repasse='".$cota[0]."' and om_codom='".$cota[1]."' and dt_abastecimento='".$_POST['dtabast']."' and viatura_eb='".$_POST['eb']."'";
						$_POST['qtd_devol']=0;				
					}
					$resultado = mysql_query($query2);
					$resultado = mysql_query($query3);
					
					$num_lin++;
				}
			}
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('Registrou a devolução de '.$qtd.' litros da cisterna EB '.$_POST['eb'].' carregada em '.substr($_POST['dtabast'],8,2).'/'.substr($_POST['dtabast'],5,2).'/'.substr($_POST['dtabast'],0,4));
			echo "<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Retorno de cisterna registrado com sucesso! ";
			echo "<p>";
			echo "<center><a href='index.php?nomeArquivo=retorno_cisterna.php'><u><b>VISUALIZAR</b></u></a></center>";
			
		}

?>

