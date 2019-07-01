<?php
	include "verifica_sessao.php";

	/*/Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	/*/Testa a operação se operacao for igual a 1 é insersão se não é alteração*/
		if ($_POST['oc']=='') {
			$_POST['oc'] = 'null';
		} else {
			$_POST['oc'] = "'".$_POST['oc']."'";
		} 
		if ($_POST['subordinacao']=='') {
			$_POST['subordinacao'] = 'null';
		} else {
			$_POST['subordinacao'] = "'".$_POST['subordinacao']."'";
		} 

		if ($_POST['tipo']=='Não Integrante') {
			$r = 'null';
		} else {
			$r = "'".$rm."'";
		} 

	if ($_POST['operacao']==1) {
		$query = "insert into om (codom,sigla,nome,tipo,oc,subordinacao,rm) 
				values('".$_POST['codom']."',upper('".$_POST['sigla']."'),upper('".$_POST['nome_om']."'),'".$_POST['tipo']."',".$_POST['oc'].",".$_POST['subordinacao'].",".$r.")";

		$rc = mysql_query($query);
		if($rc)	{
			/*/Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('Cadastrou a OM '.$_POST['sigla']);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Organização Militar cadastrada com sucesso! ");
		}
		else{
			/*/Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('Tentou cadastrar a OM '.$_POST['sigla']);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro durante o processamento do cadastro! ");
		}
	}
	else {
		$query = "update om set codom='".$_POST['codom']."', sigla=upper('".$_POST['sigla']."'), nome=upper('".$_POST['nome_om']."'), tipo='".$_POST['tipo']."', oc=".$_POST['oc'].", subordinacao=".$_POST['subordinacao'].",
						rm=".$r." 
						where codom='".$_POST['codom_ant']."'";

		$resultado = mysql_query($query);
		if (mysql_affected_rows()!= 0){
			/*/Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('Alterou ou cadastro da OM '.$_POST['sigla']);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Organização Militar alterada com sucesso! ");
		}
		else{
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇãO! Erro durante a alteração da OM! ");
			/*/Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('Usuário tentou alterar o cadastro da OM '.$_POST['sigla']);
		}
	}
	echo "<p><center><a href='index.php?nomeArquivo=cons_om.php'><u><b>VISUALIZAR</b></u></a></center>";

?> 


