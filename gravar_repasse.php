
<?php
	include "verifica_sessao.php";

	/*Conexão com arquivo para registrar atividade*/
	include "atividade.php";

	/*Consulta o estoque da 3ª DE*/
	$qry="select nr_repasse, om_codom, qtd_atual, dt_repasse, nr_nota_fiscal, combustivel_codigo, qtd_destinada
			from estoque 
			where reservatorio_codigo='".$_POST['reservatorio']."'
				and om_codom='".$_SESSION['codom']."'
				and qtd_atual>0
			order by dt_repasse";
	$result1 = mysql_query($qry);
	$num_lin=0;
	$qtd_repassada = $_POST['qtd_repassada'];
	
	while(($_POST['qtd_repassada']>0) and (mysql_num_rows($result1)>=$num_lin)) { /*Enquanto não for debitado das cotas todo o valor repassado e o numermo de cotas for maior ou igual ao contador*/
		$cota = mysql_fetch_array($result1, MYSQL_NUM);

		/*Seleciona o proximo número de estoque*/
		$qry="SELECT ifnull(max(nr_repasse),".date(Y)."0) FROM estoque where om_codom='".substr($_POST['codom'],1)."' and nr_repasse like '".date(Y)."%'";
		$res = mysql_query($qry);
		$nr = mysql_fetch_row($res);
		$nr_repasse=date(Y).(substr($nr[0],4)+1);

		if ($cota[2]>=$_POST['qtd_repassada']){
			/*Registra o repasse para a OM*/
			$query = "insert estoque (nr_repasse, om_codom, dt_repasse, combustivel_codigo, nr_nota_fiscal, reservatorio_codigo, qtd_destinada, nr_remessa, cod_tp_cota, qtd_atual, obs)
				values('$nr_repasse','".substr($_POST['codom'],1)."', now(),'".$cota[5]."','".$cota[4]."','".$_POST['reservatorio']."','".$_POST['qtd_repassada']."','".$_POST['remessa']."','".substr($_POST['codom'],0,1)."','".$_POST['qtd_repassada']."','".$_POST['obs']."')";
			$rc = mysql_query($query);

			/*Atualiza o estoque da OM*/
			$query2 = "update estoque set qtd_destinada='".($cota[6]-$_POST['qtd_repassada'])."', qtd_atual='".($cota[2]-$_POST['qtd_repassada'])."' where nr_repasse='".$cota[0]."' and om_codom='".$_SESSION['codom']."'";
			$resultado = mysql_query($query2);

			/*Registra historico repasse da om que transferiu o combustivel*/ 
			$query3 = "insert into historico_repasse (dt_registro, nr_repasse, om_codom, registro, quantidade, om_destino)
						values(now(), '".$cota[0]."', '".$_SESSION['codom']."', 'Repasse', '".$_POST['qtd_repassada']."', '".substr($_POST['codom'],1)."')";
			$resultado = mysql_query($query3);

			/*Registra historico repasse da om que recebeu o combustivel*/ 
			$query3 = "insert into historico_repasse (dt_registro, nr_repasse, om_codom, registro, quantidade, om_destino)
						values(now(), '$nr_repasse', '".substr($_POST['codom'],1)."', 'Repasse', '".$_POST['qtd_repassada']."', '".substr($_POST['codom'],1)."')";
			$resultado = mysql_query($query3);

			$_POST['qtd_repassada']=0;	
			
			$nota = $nota.$cota[4];		
		}else{
			/*Registra o repasse para a OM*/
			$query = "insert estoque (nr_repasse, om_codom, dt_repasse, combustivel_codigo, nr_nota_fiscal, reservatorio_codigo, qtd_destinada, nr_remessa, cod_tp_cota, qtd_atual, obs)
				values('$nr_repasse' ,'".substr($_POST['codom'],1)."', now(),'".$cota[5]."','".$cota[4]."','".$_POST['reservatorio']."','".$cota[2]."','".$_POST['remessa']."','".substr($_POST['codom'],0,1)."','".$cota[2]."','".$_POST['obs']."')";
			$rc = mysql_query($query);

			/*Atualiza o estoque da OM*/
			$query2 = "update estoque set qtd_destinada='".($cota[6]-$cota[2])."', qtd_atual='0' where nr_repasse='".$cota[0]."' and om_codom='".$_SESSION['codom']."'";
			$resultado = mysql_query($query2);

			/*Registra historico repasse */ 
			$query3 = "insert into historico_repasse (dt_registro, nr_repasse, om_codom, registro, quantidade, om_destino)
				values(now(), '".$cota[0]."', '".$_SESSION['codom']."', 'Repasse', '".$cota[2]."', '".substr($_POST['codom'],1)."')";
			$resultado = mysql_query($query3);

			/*Registra historico repasse da om que recebeu o combustivel*/ 
			$query3 = "insert into historico_repasse (dt_registro, nr_repasse, om_codom, registro, quantidade, om_destino)
						values(now(), '$nr_repasse', '".substr($_POST['codom'],1)."', 'Repasse', '".$_POST['qtd_repassada']."', '".substr($_POST['codom'],1)."')";
			$resultado = mysql_query($query3);

			$_POST['qtd_repassada']=$_POST['qtd_repassada']-$cota[2];	
						
			$nota = $nota.$cota[4].", ";		
		}
				
		$num_lin++;
echo $query3;
	}

	if(substr($_POST['codom'],0,1)=='0') {
		$tp_cota="Administrativo";
	}elseif(substr($_POST['codom'],0,1)=='7') {
		$tp_cota="Trânsito";
	}		

	/*Seleciona a sigla da OM Receptora*/
	$qry_sigla="SELECT sigla FROM om where codom='".substr($_POST['codom'],1)."'";
	$res_sigla = mysql_query($qry_sigla);
	$sigla = mysql_fetch_row($res_sigla);
	
	/*Registra atividade executada pelo usuário para auditoria*/
	registrarAtividade('REPASSOU '.$qtd_repassada.' Lts de '.$_POST['combustivel'].' ('.$tp_cota.') da(s) NF nº '.$nota.' para o(a) '.$sigla[0]);
	echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Distribuição realizada com sucesso! ");

   echo "<p><center><a href='index.php?nomeArquivo=repasse.php'><u><b>VISUALIZAR</b></u></a></center>";

?>

