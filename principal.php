<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>
<?php

	include "configuracao.php";
	include "conexao.php";
	/*Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	$query="select u.idt,u.post_grad,u.nome_guerra,o.sigla,u.perfil,o.codom,u.gerente,o.oc, o.subordinacao, o.nome 
		from usuario u, om o 
		where o.codom=u.om_codom 
		and u.situacao = 'PRONTO NA OM'
		and u.idt='".addslashes($_POST['idt'])."' 
		and senha='".crypt($_POST['senha'],substr(md5($_POST['senha']), 0, 2))."'";

	$result = mysql_query($query);
	$row = mysql_fetch_row($result);

	if ($_SESSION['idt']==''){
		if ($row[0]!='') {
			/*/Registra Sessão do Usuário*/
			$_SESSION['usuario']=$_POST['idt'];
			$_SESSION['idt']=$row[0];
			$_SESSION['post_grad']=$row[1];
			$_SESSION['nome_guerra']=$row[2];
			$_SESSION['unidade']=$row[3];
			$_SESSION['om']=$row[9];
			$_SESSION['perfil']=$row[4];
			$_SESSION['codom']=$row[5];
			$_SESSION['posto']=$row[5];
			$_SESSION['gerente']=$row[6];
			$_SESSION['oc']=$row[7];
			$_SESSION['om_enq']=$row[8];
			$num_rows = mysql_num_rows($result);
			mysql_free_result($result);
			echo "<meta HTTP-EQUIV = 'Refresh' CONTENT = '0; URL=index.php?nomeArquivo=principal.php'>";
			
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('USUÁRIO ACESSOU O SISTEMA');

		}
		else {
			
			echo "<script>alert('ATENÇÃO! Usuário ou senha inválidos !');</script>";
			echo "<meta http-equiv=REFRESH Content='0;URL=index.php'>";
			
		}
	}

echo "<br><br>";
echo "<br><br><br><IMG SRC='./imagens/$logo_oc' width='$w_logo' height='$h_logo'><p>";


echo "<table border='0' width='100%' align='center' >";
	echo "<tr height='60'>";
		echo "<td COLSPAN=4 align=center><b><font size=3>Bem Vindo ao Sistema de Controle de Abastecimento da $oc_geral.</b><p></td>";
	echo "</tr>";
echo "</table>";
?>

