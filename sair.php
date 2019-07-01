<?php
//  session_start();
  session_destroy();
	//Conexão ao Banco de Dados
	include "conexao.php";
	//Conexão com arquivo para registrar atividade
	include "atividade.php";
	//Registra atividade executada pelo usuário para auditoria
	registrarAtividade('USUÁRIO SAIU DO SISTEMA');
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
  <script>alert("ATENÇÃO! Logoff executado com sucesso !");</script>
  <meta http-equiv=REFRESH Content="0;URL=index.php?nomeArquivo=login.php">
