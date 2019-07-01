<?php
	/*Conexão com arquivo para registrar atividade*/
	include "atividade.php";

	$reserv = explode(",", $_POST['reservatorio']);
	$qtd_retirada = $_POST['qtd_retirada'];
	$qtd_atual = $reserv[1];
	
	$percentual = round(($qtd_retirada * 100 / $qtd_atual),3);
	$dt_sang=substr($_POST['dt_sangria'],6,4)."-".substr($_POST['dt_sangria'],3,2)."-".substr($_POST['dt_sangria'],0,2);

	
	$query="select om_codom, qtd_atual, nr_repasse from estoque where reservatorio_codigo='$reserv[0]' and qtd_atual>0";

	$result = mysql_query($query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$insere = "insert into sangria(om_codom, nr_repasse, data_sangria, qtd_retirada, usuario_idt)
			values ('$row[0]', '$row[2]', '".$dt_sang."',".round(($row[1] * $percentual  / 100),3).", '".$_SESSION['idt']."')";

		$rc = mysql_query($insere); 

		if($rc)	{
			$atualiza = "update estoque set qtd_atual = ".($row[1]-round(($row[1] * $percentual  / 100),3))." where nr_repasse='$row[2]' and om_codom='$row[0]' ";
			$rc = mysql_query($atualiza);
		}
		
	}

	if($rc)	{
		/*Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('REGISTROU A SANGRIA DE '.$_POST['qtd_retirada'].' litros do reservatório nº '.$reserv[0]);
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Sangria registrada com sucesso ! ");
	}	else{
		echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇãO! Erro durante o processamento do registro! ");
		/*Registra atividade executada pelo usuário para auditoria*/
		registrarAtividade('TENTOU A SANGRIA DE '.$_POST['qtd_retirada'].' litros do reservatório nº '.$reserv[0]);
	}

	
	echo "<p><center><a href='index.php?nomeArquivo=cons_sangria.php'><u><b>VISUALIZAR</b></u></a></center>";

?>

