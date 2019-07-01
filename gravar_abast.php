<?php
	include "verifica_sessao.php";

	/*/Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	//Armazena a qtd abastecida
	$qtd=$_POST['qtd_abast'];
	
	//Se o odometro estiver em branco
	if($_POST['odometro']=='') {
		$_POST['odometro']='0';
	}

	//Se o horimetro estiver em branco
	if($_POST['horimetro']=='') {
		$_POST['horimetro']='0';
	}

	$busca="select cod_seguranca, qtd_autorizada from abastecimento where dt_abastecimento='".$_POST['dt_abast']."' and viatura_eb='".$_POST['eb']."'";
	
	$result = mysql_query($busca);
	$row = mysql_fetch_row($result);

	$dt_abast = date('Y-m-d H:i:s');
	if (($row[0]==$_POST['codigo']) and ($row[1]>=$_POST['qtd_abast'])) {
		$query = "update abastecimento set dt_abastecimento = '$dt_abast', qtd_abastecida='".$_POST['qtd_abast']."', usuario_idt='".$_SESSION['idt']."', od_atual='".$_POST['odometro']."', horimetro='".$_POST['horimetro']."'
				where dt_abastecimento='".$_POST['dt_abast']."' and viatura_eb='".$_POST['eb']."'";

	$rc = mysql_query($query);

	if($rc) {
			/*Consulta a quantidade atual no estoque da OM*/
			$qry="select nr_repasse, om_codom, qtd_atual, dt_repasse 
					from estoque 
					where reservatorio_codigo='".$_POST['reservatorio']."'
					and om_codom='".$_POST['cota']."'
					and cod_tp_cota='".$_POST['cod_tp_cota']."' 
					and qtd_atual>0
					order by dt_repasse";
			$result1 = mysql_query($qry);
			$num_lin=0;
			if(mysql_num_rows($result1)>0) { /* Se existir cotas*/
				while(($_POST['qtd_abast']>0) and (mysql_num_rows($result1)>=$num_lin)) { /*Enquanto não for debitado das cotas todo o valor abastecido e o numermo de cotas for maior ou igual ao contador*/
					$cota = mysql_fetch_array($result1, MYSQL_NUM);

					if ($cota[2]>=$_POST['qtd_abast']){
						/*Atualiza o estoque da OM*/
						$query2 = "update estoque set qtd_atual='".($cota[2]-$_POST['qtd_abast'])."' where nr_repasse='".$cota[0]."' and om_codom='".$_POST['cota']."'";

						/*Registra historico abastecimento */ 
						$query3 = "insert into historico_abastecimento (nr_repasse, om_codom, dt_abastecimento, viatura_eb, qtd_abastecida)
										values('".$cota[0]."', '".$_POST['cota']."', '$dt_abast', '".$_POST['eb']."', '".$_POST['qtd_abast']."')";
						$_POST['qtd_abast']=0;				
					}else{
						/*Atualiza o estoque da OM*/
						$query2 = "update estoque set qtd_atual='0' where nr_repasse='".$cota[0]."' and om_codom='".$_POST['cota']."' ";				

						/*Registra historico abastecimento*/
						$query3 = "insert into historico_abastecimento (nr_repasse, om_codom, dt_abastecimento, viatura_eb, qtd_abastecida)
										values('".$cota[0]."', '".$_POST['cota']."', '$dt_abast', '".$_POST['eb']."', '$cota[2]')";
						$resultado = mysql_query($query3);
						$_POST['qtd_abast']=$_POST['qtd_abast']-$cota[2];				
					}
					$resultado = mysql_query($query2);
					$resultado = mysql_query($query3);
					
					$num_lin++;
				}
			}
			/*Registra atividade executada pelo usuário para auditoria*/
			if($_POST['tipo']=='vtr') {
				registrarAtividade('Abasteceu '.$qtd.' litros na viatura EB '.$_POST['eb'].' do(a) '.$_POST['om']);
				echo "<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Abastecimento cadastrado com sucesso! ";
			} elseif($_POST['tipo']=='cisterna') {
				registrarAtividade('Carregou '.$qtd.' litros na cisterna EB '.$_POST['eb']);
				echo "<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Carregamento de cisterna registrado com sucesso! ";
			}
			echo "<p>";
			echo "<center><a href='index.php?nomeArquivo=abastecimento.php'><u><b>VISUALIZAR</b></u></a></center>";
			
		}
	}
	else {
		/*Registra atividade executada pelo usuário para auditoria*/
		if($_POST['tipo']=='vtr') {
			registrarAtividade('Tentou abastecer da viatura EB '.$_POST['eb'].' do(a) '.$_POST['om'].'. Codigo Segurança incorreto !');
		} elseif($_POST['tipo']=='cisterna') {
			registrarAtividade('Tentou registrar o carregamento da cisterna EB '.$_POST['eb'].'. Codigo Segurança incorreto !');
		}
		echo "<br> <IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>Código de segurança incorreto ! ";

		echo "<p><center><a href='index.php?nomeArquivo=cad_abast.php&dt_abast=".$_POST['dt_abast']."&eb=".$_POST['eb']."&tipo=".$_POST['tipo']."'><u><b>RETORNAR</b></u></a></center>";
		
	}
?>

