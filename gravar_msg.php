<?php
	include "verifica_sessao.php";
	//Conexão ao Banco de Dados
	include "conexao.php";
	//Conexao com arquivo para registrar atividade
//	include "atividade.php";
	$dt = date('Y-m-d H:i:s');
//	echo $dt;
	//Insere a nova mensagem
	$query = "insert into mensagem (data_hora, remetente_idt, assunto, texto) 
					values('$dt', '".$_SESSION['idt']."',\"".$_POST['assunto']."\",\"".$_POST['texto']."\")";
//		echo $query."<br>";
	$rc = mysql_query($query);
//$rc=1;
	if($rc)	{
		$destinos = explode(",", substr($_POST['idts'], 0, -1));
 		foreach($destinos as $valor){
 			//Insere a nova mensagem
			$query1 = "insert into destinatario (data_hora, remetente_idt, destinatario_idt) 
							values('$dt', '".$_SESSION['idt']."','$valor')";
//		echo $query1."<br>";
			$rc = mysql_query($query1);
		}



		//Registra atividade executada pelo usuário para auditoria
//		registrarAtividade('Enviou a me '.$_POST['sigla']);
		echo ("<br><br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br> Mensagem Enviada com Sucesso !");
	}
	else{
		//Registra atividade executada pelo usuário para auditoria
//		registrarAtividade('Tentou enviar a OM '.$_POST['sigla']);
		echo ("<br><br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br> Erro ao enviar a mensagem !");
	}
	echo "<p><center><a href='index.php?nomeArquivo=cons_msg.php'><u><b>CONTINUAR</b></u></a></center>";
?>

