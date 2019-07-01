<?php
	//Conexão ao Banco de Dados
	include "conexao.php";
	//Conexão com arquivo para registrar atividade
	include "atividade.php";

	$query="select c.nome from combustivel c, reservatorio r where c.codigo=r.combustivel_codigo and r.codigo='".$_POST['reservatorio']."'";
	$result = mysql_query($query,$conexao);
	$combustivel = mysql_fetch_row($result);
	$query="select sigla from om where codom='".$_POST['om_ret']."'";
	$result = mysql_query($query,$conexao);
	$om_retirada = mysql_fetch_row($result);
	$query="select sigla from om where codom='".$_POST['om_atd']."'";
	$result = mysql_query($query,$conexao);
	$om_atendida = mysql_fetch_row($result);

	//Seleciona a quantidade atual da OM atendida
	$query="select qtd_atual from disponibilidade where om_codom='".$_POST['om_atd']."' and reservatorio_codigo=".$_POST['reservatorio'];
	$result = mysql_query($query,$conexao);
	$qtd_atual = mysql_fetch_row($result);
	//Testa se existe combustivel para a OM no Reservatório
	//Se não existir insere um registro e com a quantidade
	if (empty($qtd_atual)){
		$soma_qtd_destino = "insert into disponibilidade(om_codom,reservatorio_codigo,qtd_atual) 
			values('".$_POST['om_atd']."','".$_POST['reservatorio']."','".$_POST['qtd_atd']."')";
	}
	//Se existir aumenta a quantidade de combustível da OM
	else{
		$qtd_atual = $qtd_atual[0] + $_POST['qtd_atd'];
		$soma_qtd_destino = "update disponibilidade set qtd_atual = ".$qtd_atual." where om_codom='".$_POST['om_atd']."' and reservatorio_codigo='".$_POST['reservatorio']."'";
	}
	$rc = mysql('scc',$soma_qtd_destino);

	//Seleciona a OM que será abatido a quantidade de combustível
	$query="select qtd_atual from disponibilidade where om_codom='".$_POST['om_ret']."' and reservatorio_codigo=".$_POST['reservatorio'];
	$result = mysql_query($query,$conexao);
	$qtd_atual = mysql_fetch_row($result);
	$qtd_atual = $qtd_atual[0] - $_POST['qtd_atd'];
	//DIminui a quantidade da OM que perdeu combustivel
	$retira_qtd_anterior = "update disponibilidade set qtd_atual = ".$qtd_atual." where om_codom='".$_POST['om_ret']."' and reservatorio_codigo='".$_POST['reservatorio']."'";
	$rc = mysql('scc',$retira_qtd_anterior);


	if($rc)	{
		//Registra atividade executada pelo usuário para auditoria
		registrarAtividade('REDISTRIBUIU '.$_POST['qtd_atd'].' litros de '.$combustivel[0].' do(a) '.$om_retirada[0].' para o(a) '.$om_atendida[0].' no  reservatório nº '.$_POST['reservatorio']);
		echo ("<br>ATENÇÃO!  Redistribuição realizada com sucesso ! ");
	}
	else{
		echo ("<br>ATENÇÃO! Erro durante o processamento da redistribuição! ");
		//Registra atividade executada pelo usuário para auditoria
		registrarAtividade('TENTOU REDISTRIBUIR '.$_POST['qtd_atd'].' litros de '.$combustivel.' do(a) '.$om_retirada.' para o(a) '.$om_atendida.' no  reservatório nº '.$_POST['reservatorio']);
	}

?>
<p>
<center><a href="index.php?nomeArquivo=cad_redistribuicao.php&cod_om=<?echo $_POST['om_ret'];?>"><u><b>VISUALIZAR</b></u></a></center>

