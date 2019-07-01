<?php
	include "verifica_sessao.php";
	/*Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	
	//Verifica se � gerente ou n�o
	if($_POST['gerente']!='SIM') {
		$_POST['gerente'] = 'NÃO';
	}
	
	/*Testa a operação se operacao for igual a 1 é insersão se não é alteração*/
	if ($_POST['operacao']==1) {
		$query = "insert into usuario (idt,post_grad,nome,nome_guerra,om_codom,perfil,situacao,gerente,login,senha) 
		values('".$_POST['idt']."','".$_POST['post_grad']."',upper(\"".$_POST['nome_usuario']."\"),upper(\"".$_POST['nome_guerra']."\"),'".substr($_POST['codom'],1)."','".$_POST['perfil']."','".$_POST['situacao']."','".$_POST['gerente']."','".$_POST['idt']."','".crypt($_POST['senha'],substr(md5($_POST['senha']), 0, 2))."')";
		$rc = mysql_query($query);
		if($rc)	{
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('CADASTROU USUÁRIO '.$_POST['nome_usuario']);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Usuário cadastrado com sucesso! ");
		}
		else{
//			echo mysql_error().mysql_errno();
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇãO! Erro durante o processamento do cadastro! <p>");
			if(mysql_errno()==1062) {
				$qryuser = "select om.sigla, u.post_grad, u.nome_guerra, u.perfil, u.situacao, u.idt, om.codom 
									from om, usuario u
									where om.codom=u.om_codom 
										and u.idt='".$_POST['idt']."'";
				$resuser = mysql_query($qryuser);
				$user = mysql_fetch_row($resuser);
				echo "<table border='0'>";
				echo "<tr><td align='right' width='100'>Usuário: </td><td width='400'>".substr($user[1],1)." $user[2] já está cadastrado(a) no Sistema.</td></tr>";
				echo "<tr><td align='right'>OM: </td><td>$user[0]</td></tr>";
				echo "<tr><td align='right'>Idt: </td><td>$user[5]</td></tr>";
				echo "<tr><td align='right'>Função: </td><td>$user[3]</td></tr>";
				echo "<tr><td align='right'>Situação: </td><td>$user[4]</td></tr>";
				echo "</table>";
				$_POST['codom']=$user[6];
			} else {
				echo mysql_error()."<p> Se o erro persistir entre em contato com o Administrador.";
			}
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('TENTOU CADASTRAR USUÁRIO '.$_POST['nome_usuario']);
		}
	}
	else{
		if ($_POST['senha_ant']==$_POST['senha']) {
			$query = "update usuario set idt='".$_POST['idt']."', post_grad='".$_POST['post_grad']."', nome=upper(\"".$_POST['nome_usuario']."\"), nome_guerra=upper(\"".$_POST['nome_guerra']."\"), om_codom='".substr($_POST['codom'],1)."', perfil='".$_POST['perfil']."', situacao='".$_POST['situacao']."', gerente='".$_POST['gerente']."' where idt='".$_POST['idt_ant']."'";
		}
		 else {
			$query = "update usuario set idt='".$_POST['idt']."', post_grad='".$_POST['post_grad']."', nome=upper(\"".$_POST['nome_usuario']."\"), nome_guerra=upper(\"".$_POST['nome_guerra']."\"), om_codom='".substr($_POST['codom'],1)."', perfil='".$_POST['perfil']."', situacao='".$_POST['situacao']."', gerente='".$_POST['gerente']."', senha='".crypt($_POST['senha'],substr(md5($_POST['senha']), 0, 2))."' where idt='".$_POST['idt_ant']."'";
		}
		$resultado = mysql_query($query);
		if (mysql_affected_rows()!= 0){
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('ALTEROU O CADASTRO DO USUÁRIO '.$_POST['nome_usuario']);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Usuário alterado com sucesso! ");
		}
		else{
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro durante a alteração do usuário! ");
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('TENTOU ALTERAR O CADASTRO DO USUÁRIO '.$_POST['nome_usuario']);
		}
   }
	echo "<p>";
	echo "<center><a href='index.php?nomeArquivo=cons_usuario.php&listar=".$_POST['codom']."#".$_POST['codom']."'><u><b>VISUALIZAR</b></u></a></center>";
?>

